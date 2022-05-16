<?php
/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/*



if (!defined('_PS_VERSION_'))
	exit;*/

/* SSL Management */
if (isset($_SERVER['HTTPS'])) {
    $useSSL = true;
}


include_once dirname(__FILE__) . '/../../config/config.inc.php';
include_once dirname(__FILE__) . '/../../init.php';
include_once dirname(__FILE__) . '/GerencianetPrestashop.php';
include_once dirname(__FILE__) . '/lib/GerencianetIntegration.php';
include_once dirname(__FILE__) . '/lib/dbGerencianetPrestaShop.php';
include_once dirname(__FILE__) . '/lib/payments/Pix.php';





$action = Tools::getValue('action', NULL);

switch ($action) {

    case 'create_charge':

        $gnIntegration = configGnIntegration();

        $post_order_id = Tools::getValue('order_id', NULL);

        $cart = Context::getContext()->cart;
        $products = $cart->getProducts();
        $items = array();

        foreach ($products as $key => $product) {


            $items[] = array(
                'name' => $product['name'],
                'value' => (int) GerencianetIntegration::priceFormat($product['price_wt']) + "dasda",
                'amount' => (int) $product['quantity']
            );

            $preco = $product['price_wt'];
        }

        $shipping_cost = (float)$cart->getOrderTotal(true, Cart::ONLY_SHIPPING) + (float)$cart->getOrderTotal(true, Cart::ONLY_WRAPPING);
        if ($shipping_cost > 0) {
            $shipping = array(
                array(
                    'name' => 'Serviço de entrega utilizado pela Loja',
                    'value' => (int) GerencianetIntegration::priceFormat($shipping_cost)
                )
            );
        } else {
            $shipping = null;
        }

        $notificationURL = Context::getContext()->link->getModuleLink('GerencianetPrestashop', 'notification', array(), Configuration::get('PS_SSL_ENABLED'), null, null, false) . '?checkout=custom&';


        $gnApiResult =  $gnIntegration->create_charge($post_order_id, $items, $shipping, $notificationURL);



        $resultCheck = array();
        $resultCheck = json_decode($gnApiResult, true);

        if (isset($resultCheck["code"])) {
            if ($resultCheck["code"] == 200) {
                if (Configuration::get('GERENCIANET_DEBUG') == "1") {
                    GerencianetLog('GERENCIANET :: gerencianet_create_charge Request : SUCCESS');
                }
            } else {
                if (Configuration::get('GERENCIANET_DEBUG') == "1") {
                    GerencianetLog('GERENCIANET :: gerencianet_create_charge Request : ERROR : ' . $resultCheck["code"]);
                }
            }
        } else {
            if (Configuration::get('GERENCIANET_DEBUG') == "1") {
                GerencianetLog('GERENCIANET :: gerencianet_create_charge Request : ERROR : Ajax Request Fail');
            }
        }
        echo $gnApiResult;

        break;
    case 'pay_billet':


        if (Configuration::get('GERENCIANET_VENCIMENTO_DIAS_BOLETO')) {
            $billetExpireDays = intval(Configuration::get('GERENCIANET_VENCIMENTO_DIAS_BOLETO'));
        } else {
            $billetExpireDays = "5";
        }

        $expirationDate = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") + intval($billetExpireDays), date("Y")));

        $post_order_id = Tools::getValue('order_id', NULL);
        $post_pay_billet_with_cnpj = Tools::getValue('pay_billet_with_cnpj', NULL);
        $post_corporate_name = Tools::getValue('corporate_name', NULL);
        $post_cnpj = Tools::getValue('cnpj', NULL);
        $post_name = Tools::getValue('name', NULL);
        $post_cpf = Tools::getValue('cpf', NULL);
        $post_charge_id = Tools::getValue('charge_id', NULL);



        if ($post_pay_billet_with_cnpj == "1") {
            $juridical_data = array(
                'corporate_name' => $post_corporate_name,
                'cnpj' => $post_cnpj

            );

            $customer = array(
                'juridical_person' => $juridical_data
            );
        } else {
            $customer = array(
                'name' => $post_name,
                'cpf' => $post_cpf,
            );
        }


        $cart = Context::getContext()->cart;

        $discountFromCart = intval((float)$cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS) * 100);

        if (Configuration::get('GERENCIANET_DESCONTO_BOLETO_ACTIVE')) {
            $billet_discount = floatval(preg_replace('/[^0-9.]/', '', str_replace(",", ".", Configuration::get('GERENCIANET_DESCONTO_BOLETO_VALOR'))));
        } else {
            $billet_discount = 0;
        }

        $billet_discount_formatted = str_replace(".", ",", $billet_discount);

        $total_order_gn_formatted = GerencianetIntegration::priceFormat($cart->getOrderTotal(true));
        $total_order_pay_with_billet_gn_formatted = (int) ($total_order_gn_formatted * (1 - ($billet_discount / 100)));
        $total_discount = (int)($total_order_gn_formatted - $total_order_pay_with_billet_gn_formatted);

        $discountBillet = $total_discount;
        $discountTotalValue = $total_discount + $discountFromCart;


        if ($discountTotalValue > 0) {
            $discount = array(
                'type' => 'currency',
                'value' => $discountTotalValue
            );
        } else {
            $discount = null;
        }



        $configurations = [
            'fine' => (int)GerencianetIntegration::priceFormat(Configuration::get('GERENCIANET_MULTA_BOLETO_PERCENTUAL')),
            'interest' => (int)GerencianetIntegration::priceFormat(Configuration::get('GERENCIANET_JUROS_BOLETO_PERCENTUAL'))
        ];


        $client = new Customer($cart->id_customer);
        if ((bool)Configuration::get('GERENCIANET_ENVIAR_BOLETO_EMAIL')) {
            $customer['email'] = $client->email;
        }

        $gnIntegration = configGnIntegration();
        $gnApiResult = $gnIntegration->pay_billet($post_charge_id, $expirationDate, $customer, $discount, $configurations);



        $resultCheck = array();
        $resultCheck = json_decode($gnApiResult, true);



        if (isset($resultCheck["code"])) {
            if ($resultCheck["code"] == 200) {
                dbGerencianetPrestaShop::saveGnCharge($post_charge_id, $post_order_id, 'billet', floatval($total_discount / 100), $resultCheck['data']['pdf']['charge']);

                if (Configuration::get('GERENCIANET_DEBUG') == "1") {
                    GerencianetLog('GERENCIANET :: gerencianet_pay_billet Request : SUCCESS ');
                }
            } else {
                if (Configuration::get('GERENCIANET_DEBUG') == "1") {
                    GerencianetLog('GERENCIANET :: gerencianet_pay_billet Request : ERROR : ' . $resultCheck["code"]);
                }
            }
        } else {
            var_dump($resultCheck['message']);
            die();
            if (Configuration::get('GERENCIANET_DEBUG') == "1") {
                GerencianetLog('GERENCIANET :: gerencianet_pay_billet Request : ERROR : Ajax request fail');
            }
        }

        echo $gnApiResult;
        break;
    case 'pay_card':
        $cart = Context::getContext()->cart;
        $address_order = new Address($cart->id_address_delivery);


        $post_order_id = Tools::getValue('order_id', NULL);
        if (Tools::getValue('pay_card_with_cnpj')) {
            $post_pay_card_with_cnpj = Tools::getValue('pay_card_with_cnpj', NULL);
        }
        if (Tools::getValue('cnpj')) {
            $post_cnpj = Tools::getValue('cnpj', NULL);
        }

        $post_cpf = Tools::getValue('cpf', NULL);
        $client = new Customer($cart->id_customer);


        $post_payment_token = Tools::getValue('payment_token', NULL);
        $post_installments = Tools::getValue('installments', NULL);
        $post_charge_id = Tools::getValue('charge_id', NULL);
        $post_order_id = Tools::getValue('order_id', NULL);


        if ($post_pay_card_with_cnpj == "1") {
            $juridical_data = array(
                'corporate_name' => $address_order->company,
                'cnpj' => $post_cnpj
            );


            $customer = array(
                'name' => $address_order->firstname . ' ' . $address_order->lastname,
                'phone_number' => $address_order->phone,
                'juridical_person' => $juridical_data,
                'email' => $client->email,
                'birth' => $client->birthday,
            );
        } else {
            $customer = array(
                'name' => $address_order->firstname . ' ' . $address_order->lastname,
                'cpf' => $post_cpf,
                'email' => $client->email,
                'birth' => $client->birthday,
                'phone_number' => $address_order->phone

            );
        }

        $billingAddress = array(
            'street' => Tools::getValue('rua', NULL),
            'number' => Tools::getValue('numero', NULL),
            'neighborhood' => Tools::getValue('bairro', NULL),
            'zipcode' => str_replace('-', '', Tools::getValue('cep', NULL)),
            'city' => Tools::getValue('cidade', NULL),
            'state' => Tools::getValue('estado', NULL),
        );

        $discountTotalValue = intval(((float)$cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS)) * 100);

        if ($discountTotalValue > 0) {
            $discount = array(
                'type' => 'currency',
                'value' => $discountTotalValue
            );
        } else {
            $discount = null;
        }

        $gnIntegration = configGnIntegration();
        $gnApiResult = $gnIntegration->pay_card((int)$post_charge_id, $post_payment_token, (int)$post_installments, $billingAddress, $customer, $discount);

        $resultCheck = array();
        $resultCheck = json_decode($gnApiResult, true);

        if (isset($resultCheck["code"])) {
            if ($resultCheck["code"] == 200) {
                dbGerencianetPrestaShop::saveGnCharge($post_charge_id, $post_order_id, 'card', 0.00, $resultCheck['data']['installments'] . 'x de R$' . number_format(intval($resultCheck['data']['installment_value']) / 100, 2, ',', '.'));

                if (Configuration::get('GERENCIANET_DEBUG') == "1") {
                    GerencianetLog('GERENCIANET :: gerencianet_pay_card Request : SUCCESS ');
                }
            } else {
                if (Configuration::get('GERENCIANET_DEBUG') == "1") {
                    GerencianetLog('GERENCIANET :: gerencianet_pay_card Request : ERROR : ' . $resultCheck["code"]);
                }
            }
        } else {
            if (Configuration::get('GERENCIANET_DEBUG') == "1") {
                GerencianetLog('GERENCIANET :: gerencianet_pay_card Request : ERROR : Ajax Request Fail');
            }
        }
        echo $gnApiResult;

        break;
    case 'pay_pix':

        $cart = Context::getContext()->cart;
        $products = $cart->getProducts();
        $infoAdicionais = array();


        $address_order = new Address($cart->id_address_delivery);
        $post_order_id = Tools::getValue('order_id', NULL);

        if (Tools::getValue('pay_card_with_cnpj')) {
            $post_pay_card_with_cnpj = Tools::getValue('pay_card_with_cnpj', NULL);
        }
        if (Tools::getValue('cnpj')) {
            $post_cnpj = Tools::getValue('cnpj', NULL);
        }

        $post_cpf = Tools::getValue('cpf', NULL);

        if ($post_pay_card_with_cnpj == "1") {
            $devedor = [
                "cnpj" => $post_cnpj,
                "nome" => $address_order->company
            ];
        } else {
            $devedor = [
                "cpf" => $post_cpf,
                "nome" => $address_order->firstname . ' ' . $address_order->lastname
            ];
        }


        $i = 1;
        foreach ($products as $key => $product) {

            $infoAdicionais[] = array(
                'nome' => 'Produto ' . $i,
                'valor' => $product['name'] . ': R$' . (int) GerencianetIntegration::priceFormat($product['price_wt']) / 100 . ' x ' . $product['cart_quantity']
            );
            $i++;
        }

        $discountFromCart = intval((float)$cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS) * 100);

        if (Configuration::get('GERENCIANET_DESCONTO_PIX_ACTIVE')) {
            $billet_discount = floatval(preg_replace('/[^0-9.]/', '', str_replace(",", ".", Configuration::get('GERENCIANET_DESCONTO_PIX_VALOR'))));
        } else {
            $billet_discount = 0;
        }

        $billet_discount_formatted = str_replace(".", ",", $billet_discount);

        $total_order_gn_formatted = GerencianetIntegration::priceFormat($cart->getOrderTotal(true));
        $total_order_pay_with_billet_gn_formatted = (int) ($total_order_gn_formatted * (1 - ($billet_discount / 100)));
        $total_discount = (int)($total_order_gn_formatted - $total_order_pay_with_billet_gn_formatted);

        $discountBillet = $total_discount;
        $discountTotalValue = $total_discount + $discountFromCart;



        $body = [
            "calendario" => [
                "expiracao" => 3600
            ],
            "devedor" => $devedor,
            "valor" => [
                "original" => number_format(
                    strval($cart->getOrderTotal(true) - ($discountTotalValue / 100)),
                    2,
                    '.',
                    ''
                ),
            ],
            "chave" => Configuration::get('GERENCIANET_CHAVE_PIX'),
            "infoAdicionais" => $infoAdicionais
        ];

        $gnIntegration = configGnIntegration();

        $gnApiResult = $gnIntegration->pay_pix(Pix::get_gn_api_credentials(credentilsPix()), $body);

        $resultCheck = array();
        $resultCheck = json_decode($gnApiResult, true);


        if (isset($resultCheck["txid"])) {

            $qrCode = $gnIntegration->generate_qrcode(Pix::get_gn_api_credentials(credentilsPix()), $resultCheck['loc']['id']);
            $resultCheckQrCode = array();
            $resultCheckQrCode = json_decode($qrCode, true);

            dbGerencianetPrestaShop::saveGnCharge($resultCheck["txid"], $post_order_id, 'pix', $discountTotalValue / 100, $resultCheckQrCode['qrcode']);
        } else {
            if (Configuration::get('GERENCIANET_DEBUG') == "1") {
                GerencianetLog('GERENCIANET :: gerencianet_pay_pix Request : ERROR : Ajax Request Fail');
            }
        }

        echo $gnApiResult;
        break;
    default:
        throw new Exception('Invalid action');
        break;
}



function configGnIntegration()
{

    return new GerencianetIntegration(
        Configuration::get('GERENCIANET_CLIENT_ID_PRODUCAO'),
        Configuration::get('GERENCIANET_CLIENT_SECRET_PRODUCAO'),
        Configuration::get('GERENCIANET_CLIENT_ID_HOMOLOGACAO'),
        Configuration::get('GERENCIANET_CLIENT_SECRET_HOMOLOGACAO'),
        (bool)Configuration::get('GERENCIANET_PRODUCAO_SANDBOX'),
        Configuration::get('GERENCIANET_ID_CONTA')
    );
}

function credentilsPix()
{
    return
        [
            'client_id_production' =>   Configuration::get('GERENCIANET_CLIENT_ID_PRODUCAO'),
            'client_secret_production' =>   Configuration::get('GERENCIANET_CLIENT_SECRET_PRODUCAO'),
            'client_id_development' => Configuration::get('GERENCIANET_CLIENT_ID_HOMOLOGACAO'),
            'client_secret_development' => Configuration::get('GERENCIANET_CLIENT_SECRET_HOMOLOGACAO'),
            'sandbox' =>   (bool)Configuration::get('GERENCIANET_PRODUCAO_SANDBOX')
        ];
}


function GerencianetLog($msg)
{
    PrestaShopLogger::addLog('DEBUG :: ' . $msg, 0, null);
}

exit;
