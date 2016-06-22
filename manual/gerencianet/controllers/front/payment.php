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

/**
 * @since 1.5.0
 */



class GerencianetPaymentModuleFrontController extends ModuleFrontController
{
	public $ssl = true;
	public $display_column_left = true;
	public $gnIntegration;

	/**
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		parent::initContent();

		$cart = $this->context->cart;
		$customer_fields = $this->context->customer->getFields();

		if (!$this->module->checkCurrency($cart))
			Tools::redirect('index.php?controller=order');

		$actual_year = intval(date("Y")); 
        $last_year = $actual_year + 15;
        for ($i = $actual_year; $i <= $last_year; $i++) {
            $select_insert .= '<option value="'.$i.'"> '.$i.' </option>';
        }

        $this->context->controller->addJS($this->module->getPathUri().'/assets/js/checkout.js');
        $this->context->controller->addJS($this->module->getPathUri().'/assets/js/jquery.mask.min.js');

		$address_invoice = new Address((Integer)$cart->id_address_invoice);

		if (isset($customer_fields['birthday'])) {
			$birthdate_formatted = explode("-",$customer_fields['birthday']);
			$birthdate = $birthdate_formatted[2]."/".$birthdate_formatted[1]."/".$birthdate_formatted[0];
			if (strlen($birthdate)!=10) {
				$birthdate = "";
			}
		} else {
			$birthdate = "";
		}

		if (isset($customer_fields['document'])) {
			$cpf = $customer_fields['document'];
		} else if (isset($customer_fields['cpf'])) {
			$cpf = $customer_fields['cpf'];
		} else {
			$cpf = "";
		}

    	$billet_discount = floatval(preg_replace( '/[^0-9.]/', '', str_replace(",",".",Configuration::get('GERENCIANET_DISCOUNT_BILLET_VALUE'))));
    	$billet_discount_formatted = str_replace(".",",",$billet_discount);

    	$total_order_gn_formatted = GerencianetIntegration::priceFormat($cart->getOrderTotal(true));
    	$total_order_pay_with_billet_gn_formatted = (int) ($total_order_gn_formatted * (1-($billet_discount/100)));
    	$total_discount = $total_order_gn_formatted - $total_billet_discount_gn_formatted;

		$this->gnIntegration = $this->configGerencianetIntegration();

    	$max_installments = $this->gnIntegration->max_installments($total_order_gn_formatted);

    	if (isset($address_invoice->id_state)) {
    		$billing_state = State::getNameById($address_invoice->id_state);
    	} else {
			$billing_state = "";
    	}

    	if (isset($address_invoice->address3)) {
    		$billing_complement = $address_invoice->address3;
    	} else {
			$billing_complement = "";
    	}

    	if (isset($address_invoice->address4)) {
    		$billing_number = $address_invoice->address4;
    	} else {
			$billing_number = "";
    	}

    	if( isset($_SERVER['HTTPS'] ) ) {
    		$base_url_dir = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->module->name.'/';
    	} else {
    		$base_url_dir = Tools::getShopDomain(true, true).__PS_BASE_URI__.'modules/'.$this->module->name.'/';
    	}

    	if (Configuration::get('PS_SSL_ENABLED') || (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) != 'off')) {
    		$base_url_dir = str_replace("http://","https://",$base_url_dir);
    	}

		$this->context->smarty->assign(array(
			'base_url_dir' => $base_url_dir,
			'nbProducts' => $cart->nbProducts(),
			'cust_currency' => $cart->id_currency,
			'currencies' => $this->module->getCurrency((int)$cart->id_currency),
			'total' => $cart->getOrderTotal(true, Cart::BOTH),
			'this_path' => $this->module->getPathUri(),
			'this_path_bw' => $this->module->getPathUri(),
			'width_center_column' => '100%',
			'payee_code' => Configuration::get('GERENCIANET_PAYEE_CODE'),
			'sandbox' => Configuration::get('GERENCIANET_SANDBOX'),
			'billet_option' => Configuration::get('GERENCIANET_PAYMENT_OPTION_BILLET'),
			'card_option' => Configuration::get('GERENCIANET_PAYMENT_OPTION_CARD'),
			'order_total_card' => $total_order_gn_formatted,
			'order_total_billet' => $total_order_pay_with_billet_gn_formatted,
			'discount' => $billet_discount,
			'totalValueFormatted' => $total_order_gn_formatted,
			'discountFormatted' => $billet_discount_formatted,
			'order_with_billet_discount' => GerencianetIntegration::formatCurrencyBRL($total_order_pay_with_billet_gn_formatted),
			'order_id' => "-",
			'billing_cnpj' => "",
			'billing_name' => $customer_fields['firstname'] . " " . $customer_fields['lastname'],
			'billing_company' => "",
			'billing_email' => $customer_fields['email'],
			'billing_cpf' => $cpf,
			'billing_phone' => $address_invoice->phone,
			'billing_birthdate' => $birthdate,
			'max_installments' => $max_installments,
			'billing_address_1' => $address_invoice->address1,
			'billing_number' => $billing_number,
			'billing_neighborhood' => $address_invoice->address2,
			'billing_complement' => $billing_complement,
			'billing_city' => $address_invoice->city,
			'billing_postcode' => $address_invoice->postcode,
			'billing_state' => $billing_state,
			'select_insert' => $select_insert,
			'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->module->name.'/'
		));

		$this->setTemplate('payment_execution.tpl');

	}

	private function configGerencianetIntegration() {
		return new GerencianetIntegration(Configuration::get('GERENCIANET_CLIENT_ID_PROD'),Configuration::get('GERENCIANET_CLIENT_SECRET_PROD'),Configuration::get('GERENCIANET_CLIENT_ID_DEV'),Configuration::get('GERENCIANET_CLIENT_SECRET_DEV'),Configuration::get('GERENCIANET_SANDBOX'),Configuration::get('GERENCIANET_PAYEE_CODE'));
	}

}
