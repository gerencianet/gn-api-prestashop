<?php

/**
 * 2007-2022 PrestaShop
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
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2022 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */


define('GN_ROOT_URL', dirname(__FILE__));

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once GN_ROOT_URL . '/lib/GerencianetIntegration.php';
require_once GN_ROOT_URL . '/lib/dbGerencianetPrestaShop.php';
require_once GN_ROOT_URL . '/lib/payments/Pix.php';



class GerencianetPrestashop extends PaymentModule
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'GerencianetPrestashop';
        $this->tab = 'payments_gateways';
        $this->version = '1.0.0';
        $this->author = 'Gerencianet';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Gerencianet');
        $this->description = $this->l('Receba pagamentos com Boleto Bancário ou Cartão de Crédito ou Pix');

        $this->confirmUninstall = $this->l('Realmente desejar desintalar o modulo?');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        if (extension_loaded('curl') == false) {
            $this->_errors[] = $this->l('You have to enable the cURL extension on your server to install this module');
            return false;
        }

        if (!$this->createTables()) {
            return false;
        }

        Configuration::updateValue('GERENCIANETPRESTASHOP_LIVE_MODE', false);

        include(dirname(__FILE__) . '/sql/install.php');

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('payment') &&
            $this->registerHook('paymentReturn') &&
            $this->registerHook('paymentOptions');
    }



    public function uninstall()
    {
        Configuration::deleteByName('GERENCIANETPRESTASHOP_LIVE_MODE');

        include(dirname(__FILE__) . '/sql/uninstall.php');

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        //add css to configuration page
        $this->context->controller->addCSS($this->_path . 'views/css/back' . $this->assets_ext_min . '.css');


        $this->context->smarty->assign('module_dir', $this->_path);

        $this->loadSettings();
        $output = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/configure.tpl');
        $credentials = new CredentialsForm();
        $boletoForm = new BoletoForm();
        $pixForm = new PixForm();

        $output .= $this->validateForm($credentials, $boletoForm, $pixForm);

        $output .= $this->renderForm($credentials->submit, $credentials->values, $credentials->form);

        if (Configuration::get('GERENCIANET_FORMAS_PAGAMENTOS_BOLETO')) {
            $output .= $this->renderForm($boletoForm->submit, $boletoForm->values, $boletoForm->form);
        }

        if (Configuration::get('GERENCIANET_FORMAS_PAGAMENTOS_PIX')) {
            $output .= $this->renderForm($pixForm->submit, $pixForm->values, $pixForm->form);
        }


        return $output;
    }

    /**
     * Load settings
     *
     * @return void
     */
    public function loadSettings()
    {
        include_once GN_ROOT_URL . '/module/settings/CredentialsForm.php';
        include_once GN_ROOT_URL . '/module/settings/BoletoForm.php';
        include_once GN_ROOT_URL . '/module/settings/PixForm.php';
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm($submit, $values, $form)
    {

        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = $submit;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $values, /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($form));
    }

    public function validateForm($credentials, $boletoForm, $pixForm)
    {
        #success - warning - danger
        $class = 'alert ';
        $message = '';

        $validateCredentials = $credentials->validate();
        $validateBoletoForm = $boletoForm->validate();
        $validatePixForm = $pixForm->validate();
        if (is_array($validateCredentials)) {
            $class .= 'alert-danger ';
            if ($validateCredentials['key'] == NULL) {
                $message .= $validateCredentials['msg'] . '! <strong>Plugin desativado!</strong><br/>';
            } else {
                $message .= 'Campo <strong>' . $validateCredentials['key'] . '</strong> invalido! <strong>Plugin desativado!</strong><br/>';
            }


            Configuration::updateValue('GERENCIANET_ACTIVE', false);
        } else if (Configuration::get('GERENCIANET_FORMAS_PAGAMENTOS_BOLETO') && is_array($validateBoletoForm)) {
            $class .= 'alert-danger ';
            $message .= $validateBoletoForm['msg'] . '! <strong>Plugin desativado!</strong><br/>';
            Configuration::updateValue('GERENCIANET_ACTIVE', false);
        } else if (Configuration::get('GERENCIANET_FORMAS_PAGAMENTOS_PIX') && is_array($validatePixForm)) {
            $class .= 'alert-danger ';
            $message .= $validatePixForm['msg'] . '! <strong>Plugin desativado!</strong><br/>';
            Configuration::updateValue('GERENCIANET_ACTIVE', false);
        } else {
            $class  .= 'alert-success';
            $message .= 'Plugin ativado!';
            Configuration::updateValue('GERENCIANET_ACTIVE', true);
        }

        return '<div class="' . $class . '" role="alert"> <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>' . $message . '</div>';
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

        if (!Db::getInstance()->Execute($sql)) {
            return false;
        }
        return true;
    }

    public function validateNotification($notification_token)
    {
        if (Tools::getValue('notification')) {


            $gnIntegration = new GerencianetIntegration(
                Configuration::get('GERENCIANET_CLIENT_ID_PRODUCAO'),
                Configuration::get('GERENCIANET_CLIENT_SECRET_PRODUCAO'),
                Configuration::get('GERENCIANET_CLIENT_ID_HOMOLOGACAO'),
                Configuration::get('GERENCIANET_CLIENT_SECRET_HOMOLOGACAO'),
                (bool)Configuration::get('GERENCIANET_PRODUCAO_SANDBOX'),
                Configuration::get('GERENCIANET_ID_CONTA')
            );

            $notification = json_decode($gnIntegration->notificationCheck($notification_token));


            if ($notification->code == 200) {

                if (Configuration::get('GERENCIANET_DEBUG')) {
                    $this->GerencianetLog('GERENCIANET :: notification Request : SUCCESS ');
                }

                foreach ($notification->data as $notification_data) {
                    $orderIdFromNotification = $notification_data->custom_id;
                    $orderStatusFromNotification = $notification_data->status->current;
                }

                $order = new Order($orderIdFromNotification);


                switch ($orderStatusFromNotification) {
                    case 'waiting':
                        $this->updateOrderHistory((int)$orderIdFromNotification, 1);
                        break;
                    case 'paid':
                        $this->updateOrderHistory((int)$orderIdFromNotification, 2);
                        break;
                    case 'unpaid':
                        $this->updateOrderHistory((int)$orderIdFromNotification, 8);
                        $gnIntegration->cancel_charge($notification->data[0]->identifiers->charge_id);
                        break;
                    case 'refunded':
                        $this->updateOrderHistory((int)$orderIdFromNotification, 7);
                        break;
                    case 'contested':
                        $this->updateOrderHistory((int)$orderIdFromNotification, 8);
                        break;
                    case 'canceled':
                        $this->updateOrderHistory((int)$orderIdFromNotification, 6);
                        $gnIntegration->cancel_charge($notification->data[0]->identifiers->charge_id);
                        break;
                    default:

                        break;
                }
            } else {
                if (Configuration::get('GERENCIANET_DEBUG')) {
                    $this->GerencianetLog('GERENCIANET :: notification Request : FAIL ');
                }
            }
        }

        exit();
    }

    public function validateNotificationPix($txid)
    {
        if ($txid) {
            $orderIdFromNotification = dbGerencianetPrestaShop::getIdOrderByIdChargePix($txid);
            $this->updateOrderHistory($orderIdFromNotification, 2);
        }

        exit();
    }

    private function updateOrderHistory($id_order, $status)
    {
        $objOrder = new Order($id_order);

        $history = new OrderHistory();

        $history->id_order = (int)$objOrder->id;

        $history->changeIdOrderState((int)$status, (int)($objOrder->id), true);

        $history->save();



        dbGerencianetPrestaShop::setStateByIdOrder($id_order, $status);
    }


    /**
     * Add the CSS & JavaScript files you want to be loaded in the BO.
     */
    public function hookBackOfficeHeader()
    {

        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addJS($this->_path . 'views/js/back.js');
            $this->context->controller->addCSS($this->_path . 'views/css/back.css');
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {

        $this->context->controller->addCSS($this->_path . 'views/css/payment_form.css');
        $this->context->controller->addCSS($this->_path . 'views/css/front.css');
    }


    /**
     * This method is used to render the payment button,
     * Take care if the button should be displayed or not.
     */
    public function hookPayment($params)
    {
        $currency_id = $params['cart']->id_currency;
        $currency = new Currency((int)$currency_id);

        if (in_array($currency->iso_code, $this->limited_currencies) == false)
            return false;

        $this->smarty->assign('module_dir', $this->_path);

        return $this->display(__FILE__, 'views/templates/hook/payment.tpl');
    }

    /**
     * This hook is used to display the order confirmation page.
     */
    public function hookPaymentReturn($params)
    {

        if ($this->active == false)
            return;

        $order = $params['order'];

        /*
        
        */

        $charge_id = Tools::getValue('charge_id');

        if ($order->getCurrentOrderState()->id != Configuration::get('PS_OS_ERROR'))
            $this->smarty->assign('status', 'ok');


        $charge_type = dbGerencianetPrestaShop::getChargeTypeByIdCharge($charge_id);
        $charge_data = dbGerencianetPrestaShop::getChargeDataByIdCharge($charge_id);



        if ($charge_type == 'pix') {

            $gnIntegration = new GerencianetIntegration(
                Configuration::get('GERENCIANET_CLIENT_ID_PRODUCAO'),
                Configuration::get('GERENCIANET_CLIENT_SECRET_PRODUCAO'),
                Configuration::get('GERENCIANET_CLIENT_ID_HOMOLOGACAO'),
                Configuration::get('GERENCIANET_CLIENT_SECRET_HOMOLOGACAO'),
                (bool)Configuration::get('GERENCIANET_PRODUCAO_SANDBOX'),
                Configuration::get('GERENCIANET_ID_CONTA')
            );

            $qrCode = json_decode($gnIntegration->generate_qrcode_txid(Pix::get_gn_api_credentials([
                'client_id_production' =>   Configuration::get('GERENCIANET_CLIENT_ID_PRODUCAO'),
                'client_secret_production' =>   Configuration::get('GERENCIANET_CLIENT_SECRET_PRODUCAO'),
                'client_id_development' => Configuration::get('GERENCIANET_CLIENT_ID_HOMOLOGACAO'),
                'client_secret_development' => Configuration::get('GERENCIANET_CLIENT_SECRET_HOMOLOGACAO'),
                'sandbox' =>   (bool)Configuration::get('GERENCIANET_PRODUCAO_SANDBOX')
            ]), Tools::getValue('charge_id')));
        }else if($charge_type == 'billet'){
            
                $urlConst = "https://download.gerencianet.com.br/v1/";
                $urlSplit = explode("https://download.gerencianet.com.br/", $charge_data);

                
                $urlLink = explode(".pdf", $urlSplit[1]);


                $urlConst .= $urlLink[0];
                $charge_data = $urlConst; 

        }


        $this->smarty->assign([
            'id_order' => $charge_id,
            'reference' => $order->reference,
            'charge_type' => $charge_type,
            'charge_data' => $charge_data,
            'qrcode' => $qrCode->imagemQrcode,
        ]);

        return $this->display(__FILE__, 'views/templates/hook/confirmation.tpl');
    }

    /**
     * Return payment options available for PS 1.7+
     *
     * @param array Hook parameters
     *
     * @return array|null
     */
    public function hookPaymentOptions($params)
    {

        if (!$this->active) {
            return;
        }
        if (!$this->checkCurrency($params['cart'])) {
            return;
        }

        if (!(bool)Configuration::get('GERENCIANET_ACTIVE')) {
            return;
        }

        $option = new \PrestaShop\PrestaShop\Core\Payment\PaymentOption();
        $option->setCallToActionText($this->l('Pague com a Gerencianet'))
            ->setForm($this->generateForm($params));


        return [
            $option
        ];
    }

    protected function generateForm($params)
    {

        $total = $params['cart']->getOrderTotal(true, Cart::BOTH);
        $currency = new Currency((int)$params['cart']->id_currency);


        $customer = new Customer($params["cart"]->id_customer);
        $name = $customer->firstname . ' ' . $customer->lastname;

        if (isset($_SERVER['HTTPS'])) {
            $base_url_dir = Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__ . 'modules/' . $this->name . '/';
        } else {
            $base_url_dir = Tools::getShopDomain(true, true) . __PS_BASE_URI__ . 'modules/' . $this->name . '/';
        }

        if (Configuration::get('PS_SSL_ENABLED') || (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) != 'off')) {
            $base_url_dir = str_replace("http://", "https://", $base_url_dir);
        }

        $idOrder = dbGerencianetPrestaShop::getIdOrderLast();
        $idOrder++;


        $adress = json_encode(new Address((int)$params['cart']->id_address_invoice));


        $this->context->smarty->assign([
            'action' => $this->context->link->getModuleLink($this->name, 'validation', array(), true),
            'base_url_dir' => $base_url_dir,
            'boleto' => Configuration::get('GERENCIANET_FORMAS_PAGAMENTOS_BOLETO'),
            'cartao' => Configuration::get('GERENCIANET_FORMAS_PAGAMENTOS_CARTAO'),
            'pix' => Configuration::get('GERENCIANET_FORMAS_PAGAMENTOS_PIX'),
            'customer_firstname' => $name,
            'id_conta' => Configuration::get('GERENCIANET_ID_CONTA'),
            'sandbox' => (bool) Configuration::get('GERENCIANET_PRODUCAO_SANDBOX'),
            'order_id' => $idOrder,

            'endereco_entrega' => $adress,

            'module_dir' => $this->_path,

            'desconto_boleto' => Configuration::get('GERENCIANET_DESCONTO_BOLETO_ACTIVE'),
            'percentual_desconto_boleto' => Configuration::get('GERENCIANET_DESCONTO_BOLETO_VALOR'),

            'desconto_pix' => Configuration::get('GERENCIANET_DESCONTO_PIX_ACTIVE'),
            'percentual_desconto_pix' => Configuration::get('GERENCIANET_DESCONTO_PIX_VALOR'),

            'value' =>  $total,
        ]);


        return $this->context->smarty->fetch(GN_ROOT_URL . '/views/templates/front/payment_form.tpl');
    }



    public function checkCurrency($cart)
    {
        $currency_order = new Currency($cart->id_currency);
        $currencies_module = $this->getCurrency($cart->id_currency);
        if (is_array($currencies_module)) {
            foreach ($currencies_module as $currency_module) {
                if ($currency_order->id == $currency_module['id_currency']) {
                    return true;
                }
            }
        }
        return false;
    }
}
