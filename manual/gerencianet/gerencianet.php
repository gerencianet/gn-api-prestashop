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

include_once dirname(__FILE__) . '/lib/GerencianetIntegration.php';
include_once dirname(__FILE__) . '/lib/dbGerencianetPrestaShop.php';
include_once dirname(__FILE__) . '/lib/util.php';

if (!defined('_PS_VERSION_'))
	exit;

class Gerencianet extends PaymentModule
{
	protected $_html = '';
	protected $_postErrors = array();

	public $details;
	public $owner;
	public $address;
	public $extra_mail_vars;
	public $payee_code;
	public function __construct()
	{
		$this->name = 'gerencianet';
		$this->tab = 'payments_gateways';
		$this->version = '0.1.0';
		$this->author = 'Gerencianet';
		$this->controllers = array('payment', 'validation');
		$this->is_eu_compatible = 1;

		$this->currencies = true;
		$this->currencies_mode = 'checkbox';

		$config = Configuration::getMultiple(array('BANK_WIRE_DETAILS', 'BANK_WIRE_OWNER', 'BANK_WIRE_ADDRESS'));
		if (!empty($config['BANK_WIRE_OWNER']))
			$this->owner = $config['BANK_WIRE_OWNER'];
		if (!empty($config['BANK_WIRE_DETAILS']))
			$this->details = $config['BANK_WIRE_DETAILS'];
		if (!empty($config['BANK_WIRE_ADDRESS']))
			$this->address = $config['BANK_WIRE_ADDRESS'];

		$this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->l('Gerencianet');
		$this->description = $this->l('Accept payments for your products via Gerencianet payment Gateway.');
		$this->confirmUninstall = $this->l('Are you sure about removing these details?');

		/*$this->extra_mail_vars = array(
			'{gerencianet_owner}' => 'teste1',//Configuration::get('BANK_WIRE_OWNER'),
			'{gerencianet_details}' => 'teste2',//nl2br(Configuration::get('BANK_WIRE_DETAILS')),
			'{gerencianet_address}' => 'teste3',//nl2br(Configuration::get('BANK_WIRE_ADDRESS'))
		);*/
	}

	public function install()
	{
		if (!parent::install() || !$this->registerHook('payment') || ! $this->registerHook('displayPaymentEU') || !$this->registerHook('paymentReturn')) {
			return false;
		}

		if (! $this->generateGerencianetOrderStatus()) {
            return false;
        }

        if (! $this->createTables()) {
            return false;
        }

        $this->setGerencianetDefaultValues();

		return true;
	}

	public function uninstall()
	{
		if (!Configuration::deleteByName('GERENCIANET_SANDBOX')
		|| !Configuration::deleteByName('GERENCIANET_PAYMENT_OPTION_BILLET')
		|| !Configuration::deleteByName('GERENCIANET_PAYMENT_OPTION_CARD')
		|| !Configuration::deleteByName('GERENCIANET_BILLET_DAYS_TO_EXPIRE')
		|| !Configuration::deleteByName('GERENCIANET_DISCOUNT_BILLET_VALUE')
		|| !Configuration::deleteByName('GERENCIANET_PAYMENT_NOTIFICATION_UPDATE')
		|| !Configuration::deleteByName('GERENCIANET_PAYMENT_NOTIFICATION_UPDATE_NOTIFY')
		|| !Configuration::deleteByName('GERENCIANET_STATUS')
		|| !Configuration::deleteByName('GERENCIANET_CLIENT_ID_PROD')
		|| !Configuration::deleteByName('GERENCIANET_CLIENT_SECRET_PROD')
		|| !Configuration::deleteByName('GERENCIANET_CLIENT_ID_DEV')
		|| !Configuration::deleteByName('GERENCIANET_CLIENT_SECRET_DEV')
		|| !Configuration::deleteByName('GERENCIANET_PAYEE_CODE')
		|| !Configuration::deleteByName('GERENCIANET_DEBUG')
		|| !parent::uninstall())
			return false;

		return true;
	}

	protected function setGerencianetDefaultValues()
	{
		Configuration::updateValue('GERENCIANET_SANDBOX', "1");
		Configuration::updateValue('GERENCIANET_PAYMENT_OPTION_BILLET', "1");
		Configuration::updateValue('GERENCIANET_PAYMENT_OPTION_CARD', "1");
		Configuration::updateValue('GERENCIANET_BILLET_DAYS_TO_EXPIRE', "5");
		Configuration::updateValue('GERENCIANET_DISCOUNT_BILLET_VALUE', "0");
		Configuration::updateValue('GERENCIANET_PAYMENT_NOTIFICATION_UPDATE', "1");
		Configuration::updateValue('GERENCIANET_PAYMENT_NOTIFICATION_UPDATE_NOTIFY', "1");
		Configuration::updateValue('GERENCIANET_STATUS', "0");
		Configuration::updateValue('GERENCIANET_DEBUG', "0");		
	}

	private function generateGerencianetOrderStatus()
    {
        $orders_added = true;
        $name_state = null;
        $image = _PS_ROOT_DIR_ . '/modules/gerencianet/logo.gif';
        
        foreach (Util::getCustomOrderStatusGerencianet() as $key => $statusGerencianet) {

            $order_state = new OrderState();
            $order_state->module_name = 'gerencianet';
            $order_state->send_email = $statusGerencianet['send_email'];
            $order_state->color = $statusGerencianet['color'];
            $order_state->hidden = $statusGerencianet['hidden'];
            $order_state->delivery = $statusGerencianet['delivery'];
            $order_state->logable = $statusGerencianet['logable'];
            $order_state->invoice = $statusGerencianet['invoice'];
            
            if (version_compare(_PS_VERSION_, '1.5', '>')) {
                $order_state->unremovable = $statusGerencianet['unremovable'];
                $order_state->shipped = $statusGerencianet['shipped'];
                $order_state->paid = $statusGerencianet['paid'];
            }
            
            $order_state->name = array();
            $order_state->template = array();
            $continue = false;
            
            foreach (Language::getLanguages(false) as $language) {
                
                $list_states = $this->findOrderStates($language['id_lang']);
                
                $continue = $this->checkIfOrderStatusExists(
                    $language['id_lang'],
                    $statusGerencianet['name'],
                    $list_states
                );
                
                if ($continue) {
                    $order_state->name[(int) $language['id_lang']] = $statusGerencianet['name'];
                    $order_state->template[$language['id_lang']] = $statusGerencianet['template'];
                }
                
                if ($key == 'WAITING') {
                    $this->copyMailTo($statusGerencianet['template'], $language['iso_code'], 'html');
                    $this->copyMailTo($statusGerencianet['template'], $language['iso_code'], 'txt');
                }
                
            }
            
            if ($continue) {
                
                if ($order_state->add()) {
                    
                    $file = _PS_ROOT_DIR_ . '/img/os/' . (int) $order_state->id . '.gif';
                    copy($image, $file);
                    
                }
            }
            
            Configuration::updateValue('GERENCIANET_CHARGE_CODE_'.$key, $this->returnIdOrderByStatusGerencianet($statusGerencianet['name']));
            
        }
        
        return $orders_added;
    }

    private function copyMailTo($name, $lang, $ext)
    {
        
        $template = _PS_MAIL_DIR_.$lang.'/'.$name.'.'.$ext;
        
        if (! file_exists($template)) {
            
            $templateToCopy = _PS_ROOT_DIR_ . '/modules/gerencianet/mails/' . $name .'.'. $ext;
            copy($templateToCopy, $template);
            
        }
    }
    

    public function validateNotification($notification_token) {

		if (Tools::getValue('notification')) {

			$gnIntegration = new GerencianetIntegration(Configuration::get('GERENCIANET_CLIENT_ID_PROD'),Configuration::get('GERENCIANET_CLIENT_SECRET_PROD'),Configuration::get('GERENCIANET_CLIENT_ID_DEV'),Configuration::get('GERENCIANET_CLIENT_SECRET_DEV'),Configuration::get('GERENCIANET_SANDBOX'),Configuration::get('GERENCIANET_PAYEE_CODE'));
			$notification = json_decode($gnIntegration->notificationCheck($notification_token));
		    if ($notification->code==200) {

				if (Configuration::get('GERENCIANET_DEBUG')) {
					$this->GerencianetLog( 'GERENCIANET :: notification Request : SUCCESS ' );
				}

		    	foreach ($notification->data as $notification_data) {
			    	$orderIdFromNotification = $notification_data->custom_id;
			    	$orderStatusFromNotification = $notification_data->status->current;
			    }

			    $order = new Order($orderIdFromNotification);
			 
			    if (Configuration::get('GERENCIANET_PAYMENT_NOTIFICATION_UPDATE')) {
					switch($orderStatusFromNotification) {
						case 'paid':
							$this->updateOrderHistory($orderIdFromNotification, Configuration::get('GERENCIANET_CHARGE_CODE_PAID'));
							break;
						case 'unpaid':
							$this->updateOrderHistory($orderIdFromNotification, Configuration::get('GERENCIANET_CHARGE_CODE_UNPAID'));
							break;
						case 'refunded':
							$this->updateOrderHistory($orderIdFromNotification, Configuration::get('GERENCIANET_CHARGE_CODE_REFUNDED'));
							break;
						case 'contested':
							$this->updateOrderHistory($orderIdFromNotification, Configuration::get('GERENCIANET_CHARGE_CODE_CONTESTED'));
							break;
						case 'canceled':
							$this->updateOrderHistory($orderIdFromNotification, Configuration::get('GERENCIANET_CHARGE_CODE_CANCELLED'));
							break;
						default:
							//no action
							break;
					}
				}
				
			} else {
				if (Configuration::get('GERENCIANET_DEBUG')) {
					$this->GerencianetLog( 'GERENCIANET :: notification Request : FAIL ' );
				}
			}
		}
		
		exit();
    }


    private function updateOrderHistory($id_order, $status)
	{
		if (Configuration::get('GERENCIANET_PAYMENT_NOTIFICATION_UPDATE_NOTIFY')) {
			$mail = true;
		} else {
			$mail = false;
		}

		$history = new OrderHistory();
		$history->id_order = (Integer)$id_order;
		$history->changeIdOrderState((Integer)$status, (Integer)$id_order, true);
		if ($mail)
		{
			$extra_vars = array();
			$history->addWithemail(true, $extra_vars);
		}
		
	}

    private function findOrderStates($lang_id)
    {
        $sql = 'SELECT DISTINCT osl.`id_lang`, osl.`name`
            FROM `' . _DB_PREFIX_ . 'order_state` os
            INNER JOIN `' .
             _DB_PREFIX_ . 'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state`)
            WHERE osl.`id_lang` = '."$lang_id".' AND osl.`name` in ("Nova Cobrança","Aguardando pagamento",
            "Pagamento Confirmado", "Não Pago","Pagamento devolvido","Pagamento em processo de contestação","Cancelada") AND os.`id_order_state` <> 6';
        
        return (Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql));
    }

    private function checkIfOrderStatusExists($id_lang, $status_name, $list_states)
    {
        
        if (Tools::isEmpty($list_states) or empty($list_states) or ! isset($list_states)) {
            return true;
        }
        
        $save = true;
        foreach ($list_states as $state) {
            
            if ($state['id_lang'] == $id_lang && $state['name'] == $status_name) {
                $save = false;
                break;
            }
        }

        return $save;
    }

    private function returnIdOrderByStatusGerencianet($nome_status)
    {
        
        $isDeleted = version_compare(_PS_VERSION_, '1.5', '<') ? '' : 'WHERE deleted = 0';
        
        $sql = 'SELECT distinct os.`id_order_state`
            FROM `' . _DB_PREFIX_ . 'order_state` os
            INNER JOIN `' . _DB_PREFIX_ . 'order_state_lang` osl
            ON (os.`id_order_state` = osl.`id_order_state` AND osl.`name` = \'' .
             pSQL($nome_status) . '\')' . $isDeleted;
        
        $id_order_state = (Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql));
        
        return $id_order_state[0]['id_order_state'];
    }

    private function createTables()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'gerencianet_charge` (
            `id` int(11) unsigned NOT NULL auto_increment,
            `id_charge` varchar(255) NOT NULL,
            `id_order` int(10) unsigned NOT NULL ,
            `charge_type` varchar(6) NOT NULL ,
            `discount_value` decimal(10,2) NOT NULL ,
            `charge_data` varchar(255) NOT NULL ,
            PRIMARY KEY  (`id`)
            ) ENGINE=' . _MYSQL_ENGINE_ .
            ' DEFAULT CHARSET=utf8  auto_increment=1;';
        
        if (! Db::getInstance()->Execute($sql)) {
            return false;
        }
        return true;
    }
    
	protected function _postValidation()
	{
		if (Tools::isSubmit('btnSubmit'))
		{
            $gerencianet_sandbox = Tools::getValue('gerencianet_sandbox');
            $gerencianet_payment_option_billet = Tools::getValue('gerencianet_payment_option_billet');
            $gerencianet_payment_option_card = Tools::getValue('gerencianet_payment_option_card');
            $gerencianet_billet_days_to_expire = Tools::getValue('gerencianet_billet_days_to_expire');
            $gerencianet_discount_billet_value = Tools::getValue('gerencianet_discount_billet_value');
            $gerencianet_payment_notification_update = Tools::getValue('gerencianet_payment_notification_update');
            $gerencianet_payment_notification_update_notify = Tools::getValue('gerencianet_payment_notification_update_notify');
            $gerencianet_status = Tools::getValue('gerencianet_status');
            $gerencianet_client_id_production = Tools::getValue('gerencianet_client_id_production');
            $gerencianet_client_secret_production = Tools::getValue('gerencianet_client_secret_production');
            $gerencianet_client_id_development = Tools::getValue('gerencianet_client_id_development');
            $gerencianet_client_secret_development = Tools::getValue('gerencianet_client_secret_development');
            $gerencianet_payee_code = Tools::getValue('gerencianet_payee_code');
            $gerencianet_debug = Tools::getValue('gerencianet_debug');

            if (!$gerencianet_client_id_production || !$gerencianet_client_secret_production || !$gerencianet_client_id_development || !$gerencianet_client_secret_development) {
                $this->_postErrors[] = $this->l('Módulo inativo: Credenciais inválidas. Digite novamente.');
            } elseif (!$gerencianet_payee_code) {
                $this->_postErrors[] = $this->l('Módulo inativo: Identificador da conta inválido. Digite novamente.');
            }
            
		}
	}

	protected function _postProcess()
	{
		if (Tools::isSubmit('btnSubmit'))
		{
			Configuration::updateValue('GERENCIANET_SANDBOX', Tools::getValue('gerencianet_sandbox'));
			Configuration::updateValue('GERENCIANET_PAYMENT_OPTION_BILLET', Tools::getValue('gerencianet_payment_option_billet'));
			Configuration::updateValue('GERENCIANET_PAYMENT_OPTION_CARD', Tools::getValue('gerencianet_payment_option_card'));
			Configuration::updateValue('GERENCIANET_BILLET_DAYS_TO_EXPIRE', Tools::getValue('gerencianet_billet_days_to_expire'));
			Configuration::updateValue('GERENCIANET_DISCOUNT_BILLET_VALUE', Tools::getValue('gerencianet_discount_billet_value'));
			Configuration::updateValue('GERENCIANET_PAYMENT_NOTIFICATION_UPDATE', Tools::getValue('gerencianet_payment_notification_update'));
			Configuration::updateValue('GERENCIANET_PAYMENT_NOTIFICATION_UPDATE_NOTIFY', Tools::getValue('gerencianet_payment_notification_update_notify'));
			Configuration::updateValue('GERENCIANET_STATUS', Tools::getValue('gerencianet_status'));
			Configuration::updateValue('GERENCIANET_CLIENT_ID_PROD', Tools::getValue('gerencianet_client_id_production'));
			Configuration::updateValue('GERENCIANET_CLIENT_SECRET_PROD', Tools::getValue('gerencianet_client_secret_production'));
			Configuration::updateValue('GERENCIANET_CLIENT_ID_DEV', Tools::getValue('gerencianet_client_id_development'));
			Configuration::updateValue('GERENCIANET_CLIENT_SECRET_DEV', Tools::getValue('gerencianet_client_secret_development'));
			Configuration::updateValue('GERENCIANET_PAYEE_CODE', Tools::getValue('gerencianet_payee_code'));
			Configuration::updateValue('GERENCIANET_DEBUG', Tools::getValue('gerencianet_debug'));
		}
		$this->_html .= $this->displayConfirmation($this->l('Settings updated'));
	}

	protected function _displayGerencianet()
	{
		return $this->display(__FILE__, 'infos.tpl');
	}

	public function getContent()
	{
		if (Tools::isSubmit('btnSubmit'))
		{
			$this->_postValidation();
			if (!count($this->_postErrors))
				$this->_postProcess();
			else
				foreach ($this->_postErrors as $err)
					$this->_html .= $this->displayError($err);
		} else if (true == false) {

		}
		else
			$this->_html .= '<br />';

		$this->_html .= $this->_displayGerencianet();
		$this->_html .= $this->renderForm();

		return $this->_html;
	}

	public function hookPayment($params)
	{
		if (!$this->active)
			return;
		if (!$this->checkCurrency($params['cart']))
			return;

		$this->smarty->assign(array(
			'this_path' => $this->_path,
			'this_path_bw' => $this->_path,
			'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/'
		));
		return $this->display(__FILE__, 'payment.tpl');
	}

	public function hookDisplayPaymentEU($params)
	{
		if (!$this->active)
			return;

		if (!$this->checkCurrency($params['cart']))
			return;

		$payment_options = array(
			'cta_text' => $this->l('Pague com a Gerencianet'),
			'logo' => Media::getMediaPath(_PS_MODULE_DIR_.$this->name.'/gerencianet.jpg'),
			'action' => $this->context->link->getModuleLink($this->name, 'validation', array(), true)
		);

		return $payment_options;
	}

	public function hookPaymentReturn($params)
	{
		if (!$this->active)
			return;
		
		$state = $params['objOrder']->getCurrentState();

		$this->smarty->assign(array(
			'total_to_pay' => Tools::displayPrice($params['total_to_pay'], $params['currencyObj'], false),
			'gerencianetDetails' => Tools::nl2br($this->details),
			'gerencianetAddress' => Tools::nl2br($this->address),
			'gerencianetOwner' => $this->owner,
			'status' => 'ok',
			'id_order' => $params['objOrder']->id,
			'charge_type' => Tools::getValue('charge_type'),
			'charge_id' => Tools::getValue('charge_id'),
			'billet' => dbGerencianetPrestaShop::returnGerencianetChargeData($params['objOrder']->id)
		));
		if (isset($params['objOrder']->reference) && !empty($params['objOrder']->reference))
			$this->smarty->assign('reference', $params['objOrder']->reference);

		return $this->display(__FILE__, 'payment_return.tpl');
	}

	public function checkCurrency($cart)
	{
		$currency_order = new Currency($cart->id_currency);
		$currencies_module = $this->getCurrency($cart->id_currency);

		if (is_array($currencies_module))
			foreach ($currencies_module as $currency_module)
				if ($currency_order->id == $currency_module['id_currency'])
					return true;
		return false;
	}

	public function renderForm()
	{

		if ((Configuration::get('GERENCIANET_PAYMENT_OPTION_BILLET')=="1" || Configuration::get('GERENCIANET_PAYMENT_OPTION_CARD')=="1") && Configuration::get('GERENCIANET_STATUS')=="1" &&  Configuration::get('GERENCIANET_CLIENT_ID_PROD')!="" && Configuration::get('GERENCIANET_CLIENT_SECRET_PROD')!="" && Configuration::get('GERENCIANET_CLIENT_ID_DEV')!="" && Configuration::get('GERENCIANET_CLIENT_SECRET_DEV')!="" && Configuration::get('GERENCIANET_PAYEE_CODE')!="") {
			$active = "yes";
		} else {
			$active = "no";
		}

		$this->context->smarty->assign('module_dir', _PS_MODULE_DIR_ . 'gerencianet/');
		$this->context->smarty->assign('gerencianet_sandbox', Configuration::get('GERENCIANET_SANDBOX'));
		$this->context->smarty->assign('gerencianet_payment_option_billet', Configuration::get('GERENCIANET_PAYMENT_OPTION_BILLET'));
		$this->context->smarty->assign('gerencianet_payment_option_card', Configuration::get('GERENCIANET_PAYMENT_OPTION_CARD'));
		$this->context->smarty->assign('gerencianet_billet_days_to_expire', Configuration::get('GERENCIANET_BILLET_DAYS_TO_EXPIRE'));
		$this->context->smarty->assign('gerencianet_discount_billet_value', Configuration::get('GERENCIANET_DISCOUNT_BILLET_VALUE'));
		$this->context->smarty->assign('gerencianet_payment_notification_update', Configuration::get('GERENCIANET_PAYMENT_NOTIFICATION_UPDATE'));
		$this->context->smarty->assign('gerencianet_payment_notification_update_notify', Configuration::get('GERENCIANET_PAYMENT_NOTIFICATION_UPDATE_NOTIFY'));
		$this->context->smarty->assign('gerencianet_status', Configuration::get('GERENCIANET_STATUS'));
		$this->context->smarty->assign('gerencianet_client_id_production', Configuration::get('GERENCIANET_CLIENT_ID_PROD'));
		$this->context->smarty->assign('gerencianet_client_secret_production', Configuration::get('GERENCIANET_CLIENT_SECRET_PROD'));
		$this->context->smarty->assign('gerencianet_client_id_development', Configuration::get('GERENCIANET_CLIENT_ID_DEV'));
		$this->context->smarty->assign('gerencianet_client_secret_development', Configuration::get('GERENCIANET_CLIENT_SECRET_DEV'));
		$this->context->smarty->assign('gerencianet_payee_code', Configuration::get('GERENCIANET_PAYEE_CODE'));
		$this->context->smarty->assign('gerencianet_debug', Configuration::get('GERENCIANET_DEBUG'));
		$this->context->smarty->assign('gerencianet_active', $active);
		
		return $this->display(__PS_BASE_URI__ . 'modules/gerencianet', 'views/templates/front/admin_gerencianet.tpl');
	}

	public function getConfigFieldsValues()
	{
		return array(
			'GERENCIANET_SANDBOX' => Tools::getValue('GERENCIANET_SANDBOX', Configuration::get('GERENCIANET_SANDBOX')),
			'GERENCIANET_PAYMENT_OPTION_BILLET' => Tools::getValue('GERENCIANET_PAYMENT_OPTION_BILLET', Configuration::get('GERENCIANET_PAYMENT_OPTION_BILLET')),
			'GERENCIANET_PAYMENT_OPTION_CARD' => Tools::getValue('GERENCIANET_PAYMENT_OPTION_CARD', Configuration::get('GERENCIANET_PAYMENT_OPTION_CARD')),
			'GERENCIANET_BILLET_DAYS_TO_EXPIRE' => Tools::getValue('GERENCIANET_BILLET_DAYS_TO_EXPIRE', Configuration::get('GERENCIANET_BILLET_DAYS_TO_EXPIRE')),
			'GERENCIANET_DISCOUNT_BILLET_VALUE' => Tools::getValue('GERENCIANET_DISCOUNT_BILLET_VALUE', Configuration::get('GERENCIANET_DISCOUNT_BILLET_VALUE')),
			'GERENCIANET_PAYMENT_NOTIFICATION_UPDATE' => Tools::getValue('GERENCIANET_PAYMENT_NOTIFICATION_UPDATE', Configuration::get('GERENCIANET_PAYMENT_NOTIFICATION_UPDATE')),
			'GERENCIANET_PAYMENT_NOTIFICATION_UPDATE_NOTIFY' => Tools::getValue('GERENCIANET_PAYMENT_NOTIFICATION_UPDATE_NOTIFY', Configuration::get('GERENCIANET_PAYMENT_NOTIFICATION_UPDATE_NOTIFY')),
			'GERENCIANET_STATUS' => Tools::getValue('GERENCIANET_STATUS', Configuration::get('GERENCIANET_STATUS')),
			'GERENCIANET_CLIENT_ID_PROD' => Tools::getValue('GERENCIANET_CLIENT_ID_PROD', Configuration::get('GERENCIANET_CLIENT_ID_PROD')),
			'GERENCIANET_CLIENT_SECRET_PROD' => Tools::getValue('GERENCIANET_CLIENT_SECRET_PROD', Configuration::get('GERENCIANET_CLIENT_SECRET_PROD')),
			'GERENCIANET_CLIENT_ID_DEV' => Tools::getValue('GERENCIANET_CLIENT_ID_DEV', Configuration::get('GERENCIANET_CLIENT_ID_DEV')),
			'GERENCIANET_CLIENT_SECRET_DEV' => Tools::getValue('GERENCIANET_CLIENT_SECRET_DEV', Configuration::get('GERENCIANET_CLIENT_SECRET_DEV')),
			'GERENCIANET_PAYEE_CODE' => Tools::getValue('GERENCIANET_PAYEE_CODE', Configuration::get('GERENCIANET_PAYEE_CODE')),
			'GERENCIANET_DEBUG' => Tools::getValue('GERENCIANET_DEBUG', Configuration::get('GERENCIANET_DEBUG')),
		);
	}

	private function GerencianetLog($msg){
        PrestaShopLogger::addLog('DEBUG :: '.$msg, 0 , null);	
	}
}
