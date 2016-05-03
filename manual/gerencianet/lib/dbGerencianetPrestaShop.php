<?php
/**
 * 2007-2013 PrestaShop
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
 *  @copyright 2007-2014 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

include_once dirname(__FILE__).'/../../../../config/config.inc.php';
include_once dirname(__FILE__).'/../../../../init.php';

class dbGerencianetPrestaShop
{
    public function saveGnCharge($charge, $order, $type, $discount_value, $charge_data)
    {
        $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'gerencianet_charge` (`id_charge`, `id_order`, `charge_type`, `discount_value`, `charge_data`)
                VALUES (\'' . pSQL($charge) . '\', \'' . (int) $order . '\', \'' . pSQL($type) . '\', \'' . pSQL($discount_value) . '\', \'' . pSQL($charge_data) . '\')';

        if (! Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($sql)) {
            die(Tools::displayError('Error when saving gerencianet charge in database'));
        }
    }

    public function returnGerencianetChargeData($order)
    {
        
        $sql = "SELECT `charge_data` FROM `" . _DB_PREFIX_ . "gerencianet_charge` WHERE `id_order` = $order";

        $gn_order = Db::getInstance()->getRow($sql);
        
        return $gn_order['charge_data'];
    }

    public function getGnChargeIdByIdOrder($id_order)
    {
        
        $sql = "SELECT `id_charge` FROM `" . _DB_PREFIX_ . "gerencianet_charge` WHERE `id_order` = $id_order";

        $gn_order = Db::getInstance()->getRow($sql);
        
        return $gn_order['id_charge'];
    }

    public function getChargeDataByIdCharge($id_charge)
    {
        
        $sql = "SELECT `charge_data` FROM `" . _DB_PREFIX_ . "gerencianet_charge` WHERE `id_charge` = $id_charge";

        $gn_order = Db::getInstance()->getRow($sql);
        
        return $gn_order['charge_data'];
    }

    public function getChargeTypeByIdCharge($id_charge)
    {
        
        $sql = "SELECT `charge_type` FROM `" . _DB_PREFIX_ . "gerencianet_charge` WHERE `id_charge` = $id_charge";

        $gn_order = Db::getInstance()->getRow($sql);
        
        return $gn_order['charge_type'];
    }

    public function getDiscountValueByIdCharge($id_charge)
    {
        
        $sql = "SELECT `discount_value` FROM `" . _DB_PREFIX_ . "gerencianet_charge` WHERE `id_charge` = $id_charge";

        $gn_order = Db::getInstance()->getRow($sql);
        
        return $gn_order['discount_value'];
    }

    public function setIdOrderByGnChargeId($id_order,$id_charge)
    {
        
        $sql = 'UPDATE `' . _DB_PREFIX_ . 'gerencianet_charge` SET `id_order` = \'' . pSQL($id_order). '\' WHERE `id_charge` = \'' . pSQL($id_charge) . '\';';

        if (! Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($sql)) {
            die(Tools::displayError('Error when updating gerencianet status in database'));
        }
        
    }

    public function createBilletTempDiscount($id_customer_charge, $discount, $percent) {
        $cart_rule = new CartRule();
        $languages = Language::getLanguages();
        foreach ($languages as $key => $language)
            {$array[$language['id_lang']]= "Desconto de " . $percent . "% no Boleto";}

        $cart_rule->name=$array;
        $cart_rule->description = "Desconto por pagamento com Boleto Bancário";
        $cart_rule->id_customer = $id_customer_charge;
        $cart_rule->active = 1;
        $cart_rule->date_from = date('Y-m-d 00:00:00');
        $cart_rule->date_to = date('Y-m-d h:i:s', mktime(0, 0, 0, date("m"), date("d")+1, date("Y")));
        $cart_rule->minimum_amount = '5';
        $cart_rule->minimum_amount_currency = 1;
        $cart_rule->quantity = 1;
        $cart_rule->quantity_per_user = 1;
        $cart_rule->reduction_tax = 1;
        $cart_rule->reduction_amount = floatval($discount);
        $cart_rule->add();

        return $cart_rule;
    }

    public function deleteBilletTempDiscount($cart_rule) {
        $cart_rule->delete();

    }

}
