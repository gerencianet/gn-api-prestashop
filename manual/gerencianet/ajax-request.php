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

include_once dirname(__FILE__).'/../../config/config.inc.php';
include_once dirname(__FILE__).'/../../init.php';
include_once dirname(__FILE__).'/gerencianet.php';
include_once dirname(__FILE__).'/lib/dbGerencianetPrestaShop.php';

$action = Tools::getValue('action', NULL) ;

switch ($action) {

    case 'get_installments':

		$gnIntegration = configGnIntegration();

		$post_brand = Tools::getValue('brand', NULL);
		if ($post_brand=="") {
			$post_brand = $_GET['brand'];
		}

		$cart = Context::getContext()->cart;
		$totalOrder = $cart->getOrderTotal(true, Cart::BOTH);
		$total = GerencianetIntegration::priceFormat($totalOrder);
		$brand = $post_brand;
		$gnApiResult = $gnIntegration->get_installments($total,$brand);

		$resultCheck = array();
		$resultCheck = json_decode($gnApiResult, true);

		if (Configuration::get('GERENCIANET_DEBUG')=="1") {
			GerencianetLog('GERENCIANET :: gerencianet_get_installments Request' );
		}

		if (isset($resultCheck["code"])) {
			if ($resultCheck["code"]==200) {		
				if (Configuration::get('GERENCIANET_DEBUG')=="1") {
					GerencianetLog('GERENCIANET :: gerencianet_get_installments Request : SUCCESS' );
				}
			} else {
				if (Configuration::get('GERENCIANET_DEBUG')=="1") {
					GerencianetLog('GERENCIANET :: gerencianet_get_installments Request : ERROR' );
				}
			}
		} else {
			if (Configuration::get('GERENCIANET_DEBUG')=="1") {
				GerencianetLog('GERENCIANET :: gerencianet_get_installments Request : ERROR' );
			}
		}
		echo $gnApiResult;
		break;

	case 'create_charge':

		$gnIntegration = configGnIntegration();

		$post_order_id = Tools::getValue('order_id', NULL);

		$cart = Context::getContext()->cart;
		$products = $cart->getProducts();
		$items = array();
		foreach ($products as $key=>$product)
		{

			$items [] = array (
				'name' => $product['name'],
				'value' => (int) GerencianetIntegration::priceFormat($product['price_wt']),
				'amount' => (int) $product['quantity']
				);
			$preco = $product['price_wt'];

		}

		$shipping_cost = (Float)$cart->getOrderTotal(true, Cart::ONLY_SHIPPING) + (Float)$cart->getOrderTotal(true, Cart::ONLY_WRAPPING);
		if ($shipping_cost > 0)
		{
			$shipping = array (
					array (
				        'name' => 'Serviço de entrega utilizado pela Loja',
				        'value' => (int) GerencianetIntegration::priceFormat($shipping_cost)
				    )
				);
		} else {
			$shipping=null;
		}

		$notificationURL = Context::getContext()->link->getModuleLink('gerencianet', 'notification', array(), Configuration::get('PS_SSL_ENABLED'), null, null, false).'?checkout=custom&';

		$gnApiResult =  $gnIntegration->create_charge($post_order_id,$items,$shipping,$notificationURL);

		$resultCheck = array();
		$resultCheck = json_decode($gnApiResult, true);

		if (isset($resultCheck["code"])) {
			if ($resultCheck["code"]==200) {		
				if (Configuration::get('GERENCIANET_DEBUG')=="1") {
					GerencianetLog('GERENCIANET :: gerencianet_create_charge Request : SUCCESS' );
				}
			} else {
				if (Configuration::get('GERENCIANET_DEBUG')=="1") {
					GerencianetLog('GERENCIANET :: gerencianet_create_charge Request : ERROR : ' . $resultCheck["code"] );
				}
			}
		} else {
			if (Configuration::get('GERENCIANET_DEBUG')=="1") {
				GerencianetLog('GERENCIANET :: gerencianet_create_charge Request : ERROR : Ajax Request Fail' );
			}
		}
		echo $gnApiResult;

		break;

	case 'pay_billet':

		if (Configuration::get('GERENCIANET_BILLET_DAYS_TO_EXPIRE')) {
			$billetExpireDays = intval(Configuration::get('GERENCIANET_BILLET_DAYS_TO_EXPIRE'));
		} else {
			$billetExpireDays = "5";
		}

	    $expirationDate = date("Y-m-d", mktime (0, 0, 0, date("m")  , date("d")+intval($billetExpireDays), date("Y")));

	    $post_order_id = Tools::getValue('order_id', NULL);
	    $post_pay_billet_with_cnpj = Tools::getValue('pay_billet_with_cnpj', NULL);
	    $post_corporate_name = Tools::getValue('corporate_name', NULL);
	    $post_cnpj = Tools::getValue('cnpj', NULL);
	    $post_name = Tools::getValue('name', NULL);
	    $post_cpf = Tools::getValue('cpf', NULL);
	    $post_phone_number = Tools::getValue('phone_number', NULL);
	    $post_charge_id = Tools::getValue('charge_id', NULL);

	    if ($post_pay_billet_with_cnpj && $post_corporate_name && $post_cnpj) {
			$juridical_data = array (
			  'corporate_name' => $post_corporate_name,
			  'cnpj' => $post_cnpj
			);

			$customer = array (
			    'name' => $post_name,
			    'cpf' => $post_cpf,
			    'phone_number' => $post_phone_number,
				'juridical_person' => $juridical_data
			);
		} else {
			$customer = array (
			    'name' => $post_name,
			    'cpf' => $post_cpf,
			    'phone_number' => $post_phone_number
			);
		}

		$cart = Context::getContext()->cart;

		$discountFromCart = intval((Float)$cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS)*100);

		$billet_discount = floatval(preg_replace( '/[^0-9.]/', '', str_replace(",",".",Configuration::get('GERENCIANET_DISCOUNT_BILLET_VALUE'))));
    	$billet_discount_formatted = str_replace(".",",",$billet_discount);

    	$total_order_gn_formatted = GerencianetIntegration::priceFormat($cart->getOrderTotal(true));
    	$total_order_pay_with_billet_gn_formatted = (int) ($total_order_gn_formatted * (1-($billet_discount/100)));
    	$total_discount = (int)($total_order_gn_formatted-$total_order_pay_with_billet_gn_formatted);

		$discountBillet = $total_discount;
		$discountTotalValue = $total_discount + $discountFromCart;

		if ($discountTotalValue>0) {
			$discount = array (
				'type' => 'currency',
				'value' => $discountTotalValue
			);
		} else {
			$discount=null;
		}
		
		$gnIntegration = configGnIntegration();
		$gnApiResult = $gnIntegration->pay_billet($post_charge_id,$expirationDate,$customer,$discount);

		$resultCheck = array();
		$resultCheck = json_decode($gnApiResult, true);

		if (isset($resultCheck["code"])) {
			if ($resultCheck["code"]==200) {		
				dbGerencianetPrestaShop::saveGnCharge($post_charge_id, '', 'billet', floatval($total_discount/100), $resultCheck['data']['link']);

				if (Configuration::get('GERENCIANET_DEBUG')=="1") {
					GerencianetLog('GERENCIANET :: gerencianet_pay_billet Request : SUCCESS ' );
				}

		    } else {
				if (Configuration::get('GERENCIANET_DEBUG')=="1") {
					GerencianetLog('GERENCIANET :: gerencianet_pay_billet Request : ERROR : ' . $resultCheck["code"] );
				}
		    }
	    } else {
			if (Configuration::get('GERENCIANET_DEBUG')=="1") {
				GerencianetLog('GERENCIANET :: gerencianet_pay_billet Request : ERROR : Ajax request fail' );
			}
	    }

	    echo $gnApiResult;
		break;

	case 'pay_card':

		$post_order_id = Tools::getValue('order_id', NULL);
		if (Tools::getValue('pay_card_with_cnpj')) {
		    $post_pay_card_with_cnpj = Tools::getValue('pay_card_with_cnpj', NULL);
		}
		if (Tools::getValue('corporate_name')) {
		    $post_corporate_name = Tools::getValue('corporate_name', NULL);
		}
		if (Tools::getValue('cnpj')) {
			$post_cnpj = Tools::getValue('cnpj', NULL);
		}
	    
	    $post_name = Tools::getValue('name', NULL);
	    $post_cpf = Tools::getValue('cpf', NULL);
	    $post_phone_number = Tools::getValue('phone_number', NULL);
	    $post_email = Tools::getValue('email', NULL);
	    $post_birth = Tools::getValue('birth', NULL);
	    $post_street = Tools::getValue('street', NULL);
	    $post_number = Tools::getValue('number', NULL);
	    $post_neighborhood = Tools::getValue('neighborhood', NULL);
	    $post_zipcode = preg_replace( '/[^0-9]/', '', Tools::getValue('zipcode', NULL));
	    $post_city = Tools::getValue('city', NULL);
	    $post_state = Tools::getValue('state', NULL);
	    $post_complement = Tools::getValue('complement', NULL);
	    $post_payment_token = Tools::getValue('payment_token', NULL);
	    $post_installments = Tools::getValue('installments', NULL);
	    $post_charge_id = Tools::getValue('charge_id', NULL);

	    if (isset($post_pay_card_with_cnpj) && isset($post_corporate_name) && isset($post_cnpj)) {
			$juridical_data = array (
			  'corporate_name' => $post_corporate_name,
			  'cnpj' => $post_cnpj
			);

			$customer = array (
			    'name' => $post_name,
			    'cpf' => $post_cpf,
			    'phone_number' => $post_phone_number,
				'juridical_person' => $juridical_data,
			    'email' => $post_email,
			    'birth' => $post_birth
			);
		} else {
			$customer = array (
			    'name' => $post_name,
			    'cpf' => $post_cpf,
			    'phone_number' => $post_phone_number,
			    'email' => $post_email,
			    'birth' => $post_birth
			);
		}

		$billingAddress = array (
		    'street' => $post_street,
		    'number' => $post_number,
		    'neighborhood' => $post_neighborhood,
		    'zipcode' => $post_zipcode,
		    'city' => $post_city,
		    'state' => $post_state,
		    'complement' => $post_complement
		);

		$cart = Context::getContext()->cart;

		$discountTotalValue = intval(((Float)$cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS))*100);

		if ($discountTotalValue>0) {
			$discount = array (
				'type' => 'currency',
				'value' => $discountTotalValue
			);
		} else {
			$discount=null;
		}
		
		$gnIntegration = configGnIntegration();
		$gnApiResult = $gnIntegration->pay_card((int)$post_charge_id,$post_payment_token,(int)$post_installments,$billingAddress,$customer,$discount);

		$resultCheck = array();
		$resultCheck = json_decode($gnApiResult, true);

		if (isset($resultCheck["code"])) {
			if ($resultCheck["code"]==200) {	
				dbGerencianetPrestaShop::saveGnCharge($post_charge_id, '', 'card', 0.00, $resultCheck['data']['installments'] . 'x de R$' . number_format(intval($resultCheck['data']['installment_value'])/100, 2, ',', '.') );

				if (Configuration::get('GERENCIANET_DEBUG')=="1") {
					GerencianetLog('GERENCIANET :: gerencianet_pay_card Request : SUCCESS ' );
				}
			} else {
				if (Configuration::get('GERENCIANET_DEBUG')=="1") {
					GerencianetLog('GERENCIANET :: gerencianet_pay_card Request : ERROR : ' . $resultCheck["code"] );
				}
			}
		} else {
			if (Configuration::get('GERENCIANET_DEBUG')=="1") {
				GerencianetLog('GERENCIANET :: gerencianet_pay_card Request : ERROR : Ajax Request Fail' );
			}
		}
		echo $gnApiResult;

		break;
}

function configGnIntegration() {
	return new GerencianetIntegration(Configuration::get('GERENCIANET_CLIENT_ID_PROD'),Configuration::get('GERENCIANET_CLIENT_SECRET_PROD'),Configuration::get('GERENCIANET_CLIENT_ID_DEV'),Configuration::get('GERENCIANET_CLIENT_SECRET_DEV'),Configuration::get('GERENCIANET_SANDBOX'),Configuration::get('GERENCIANET_PAYEE_CODE'));
}

function GerencianetLog($msg){
    PrestaShopLogger::addLog('DEBUG :: '.$msg, 0 , null);	
}