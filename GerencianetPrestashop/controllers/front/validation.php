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

include_once dirname(__FILE__) . '/../../lib/dbGerencianetPrestaShop.php';
include_once dirname(__FILE__) . '/../../lib/GerencianetIntegration.php';

class GerencianetPrestashopValidationModuleFrontController extends ModuleFrontController
{
    /**
     * This class should be use by your Instant Payment
     * Notification system to validate the order remotely
     */
    public function postProcess()
    {
        $cart = $this->context->cart;

        $charge_type = dbGerencianetPrestaShop::getChargeTypeByIdCharge(Tools::getValue('gn_charge_id'));

        $charge_id = Tools::getValue('gn_charge_id');


        $discount_value = dbGerencianetPrestaShop::getDiscountValueByIdCharge($charge_id);


        if ($charge_type == "billet") {
            $billet_discount = floatval(preg_replace('/[^0-9.]/', '', str_replace(",", ".", Configuration::get('GERENCIANET_DESCONTO_BOLETO_VALOR'))));
            $billet_discount_formatted = str_replace(".", ",", $billet_discount);

            if (floatval($discount_value) > 0) {
                $addBilletDiscountCartRule = dbGerencianetPrestaShop::createBilletTempDiscount($cart->id_customer, $discount_value, $billet_discount_formatted);
                $this->context->cart->addCartRule($addBilletDiscountCartRule->id);
            }
        } else if ($charge_type == 'pix') {
            $pix_discount = floatval(preg_replace('/[^0-9.]/', '', str_replace(",", ".", Configuration::get('GERENCIANET_DESCONTO_PIX_VALOR'))));
            $pix_discount_formatted = str_replace(".", ",", $pix_discount);


            if (floatval($discount_value) > 0) {
                $addPixDiscountCartRule = dbGerencianetPrestaShop::createBilletTempDiscount($cart->id_customer, $discount_value, $pix_discount_formatted);
                $this->context->cart->addCartRule($addPixDiscountCartRule->id);
            }
        }

        if ($cart->id_customer == 0 || $cart->id_address_delivery == 0 || $cart->id_address_invoice == 0 || !$this->module->active)
            Tools::redirect('index.php?controller=order&step=1');

        $authorized = false;
        foreach (Module::getPaymentModules() as $module)
            if ($module['name'] == 'GerencianetPrestashop') {
                $authorized = true;
                break;
            }
        if (!$authorized)
            die($this->module->l('This payment method is not available.', 'validation'));

        $customer = new Customer($cart->id_customer);
        if (!Validate::isLoadedObject($customer))
            Tools::redirect('index.php?controller=order&step=1');


        if ($charge_type == "billet") {
            $payment_title = "Boleto Bancário - Gerencianet";
            $payment_title_full = "Boleto Bancário Gerencianet ";
            $payment_billet_comment = dbGerencianetPrestaShop::getChargeDataByIdCharge(Tools::getValue('gn_charge_id'));
            $payment_card_comment = "";
            $payment_comment_title = "Link do Boleto: ";
            $payment_description = "A confirmação do pagamento será realizada no dia útil seguinte ao pagamento.";
            $billet_link_text = "Visualizar Boleto";
        } else if ($charge_type == "pix") {
            $payment_title = "Pix - Gerencianet";
            $payment_title_full = "Pix - Gerencianet";
            $payment_card_comment = dbGerencianetPrestaShop::getChargeDataByIdCharge(Tools::getValue('gn_charge_id'));
            $payment_comment_title = "Copia e cola:";
            $payment_description = "A confirmação do pagamento será realizada quando o pagamento for confirmado.";
            $billet_link_text = "";
        } else {
            $payment_title = "Cartão de Crédito - Gerencianet";
            $payment_title_full = "Cartão de Crédito - Gerencianet";
            $payment_billet_comment = "";
            $payment_card_comment = dbGerencianetPrestaShop::getChargeDataByIdCharge(Tools::getValue('gn_charge_id'));
            $payment_comment_title = "Opção de parcelamento: ";
            $payment_description = "O pagamento está sendo processado e a confirmação ocorrerá em até 48 horas.";
            $billet_link_text = "";
        }

        $currency = $this->context->currency;
        $total = (float)$cart->getOrderTotal(true, Cart::BOTH);
        $mailVars = array(
            '{gerencianet_payment_type}' => $payment_title,
            '{gerencianet_data_title}' => $payment_comment_title,
            '{gerencianet_data_billet}' => $payment_billet_comment,
            '{gerencianet_data_card}' => $payment_card_comment,
            '{gerencianet_data_comment}' => $payment_description,
            '{gerencianet_data_billet_link_text}' => $billet_link_text
        );

        $this->module->validateOrder(
            (int)$cart->id,
            1,
            $total,
            $payment_title,
            null,
            [],
            (int)$currency->id,
            false,
            $customer->secure_key
        );

        if ($charge_type != 'pix') {
            if ($charge_type == "billet") {
                if (floatval($discount_value) > 0) {
                    $addBilletDiscountCartRule->delete();
                }
            }

            $notificationURL = Context::getContext()->link->getModuleLink('GerencianetPrestashop', 'notification', array(), Configuration::get('PS_SSL_ENABLED'), null, null, false) . '?checkout=custom&';


            dbGerencianetPrestaShop::setIdOrderByGnChargeId($this->module->currentOrder, Tools::getValue('gn_charge_id'));
        } else {
            if (floatval($discount_value) > 0) {
                $addPixDiscountCartRule->delete();
            }
        }
        dbGerencianetPrestaShop::updatePaymentTitle($this->module->currentOrder, $payment_title_full);

        Tools::redirect('index.php?controller=order-confirmation&id_cart=' . $cart->id . '&id_module=' . $this->module->id . '&id_order=' . $this->module->currentOrder . '&key=' . $customer->secure_key . '&charge_id=' . Tools::getValue('gn_charge_id') . '&charge_type=' . $charge_type);
    }
}
