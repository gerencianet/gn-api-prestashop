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

class GerencianetUtil
{

    private static $order_status = array(
        'NEW' => 'Nova Cobrança',
        'WAITING' => 'Aguardando pagamento',
        'PAID' => 'Pagamento Confirmado',
        'UNPAID' => 'Não Pago',
        'REFUNDED' => 'Pagamento devolvido',
        'CONTESTED' => 'Pagamento em processo de contestação',
        'CANCELLED' => 'Cancelada'
    );

    private static $array_st_cms = array(
        0 => 'Nova Cobrança',
        1 => 'Aguardando pagamento',
        2 => 'Pagamento Confirmado',
        3 => 'Não Pago',
        4 => 'Pagamento devolvido',
        5 => 'Pagamento em processo de contestação',
        6 => 'Cancelada'
    );

    private static $order_status_gerencianet = array(
        'NEW' => array(
            'name' => 'Nova Cobrança',
            'send_email' => false,
            'color' => '#0B8FBB',
            'template' => '',
            'hidden' => true,
            'delivery' => false,
            'logable' => false,
            'invoice' => false,
            'unremovable' => false,
            'shipped' => false,
            'paid' => false
        ),
        'WAITING' => array(
            'name' => 'Aguardando pagamento',
            'send_email' => true,
            'color' => '#054DA4',
            'template' => 'gerencianet_waiting_payment',
            'hidden' => false,
            'delivery' => false,
            'logable' => false,
            'invoice' => false,
            'unremovable' => false,
            'shipped' => false,
            'paid' => false
        ),
        'PAID' => array(
            'name' => 'Pagamento Confirmado',
            'send_email' => true,
            'color' => '#469616',
            'template' => 'payment',
            'hidden' => false,
            'delivery' => false,
            'logable' => true,
            'invoice' => true,
            'unremovable' => false,
            'shipped' => false,
            'paid' => true
        ),
        'UNPAID' => array(
            'name' => 'Não Pago',
            'send_email' => true,
            'color' => '#D71B2D',
            'template' => 'order_canceled',
            'hidden' => false,
            'delivery' => false,
            'logable' => false,
            'invoice' => false,
            'unremovable' => false,
            'shipped' => false,
            'paid' => false
        ),
        'REFUNDED' => array(
            'name' => 'Pagamento devolvido',
            'send_email' => true,
            'color' => '#AA921B',
            'template' => 'refund',
            'hidden' => false,
            'delivery' => false,
            'logable' => false,
            'invoice' => false,
            'unremovable' => false,
            'shipped' => false,
            'paid' => false
        ),
        'CONTESTED' => array(
            'name' => 'Pagamento em processo de contestação',
            'send_email' => false,
            'color' => '#D2CB02',
            'template' => '',
            'hidden' => true,
            'delivery' => false,
            'logable' => true,
            'invoice' => true,
            'unremovable' => false,
            'shipped' => false,
            'paid' => true
        ),
        'CANCELLED' => array(
            'name' => 'Cancelada',
            'send_email' => true,
            'color' => '#D71B2D',
            'template' => 'order_canceled',
            'hidden' => false,
            'delivery' => false,
            'logable' => false,
            'invoice' => false,
            'unremovable' => false,
            'shipped' => false,
            'paid' => false
        )
    );

    public static function getStatusCMS($id_status)
    {
        return self::$array_st_cms[$id_status];
    }

    public static function getOrderStatus()
    {
        return self::$order_status;
    }

    public static function getCustomOrderStatusGerencianet()
    {
        return self::$order_status_gerencianet;
    }
    
    private static function getBaseDefaultUrl()
    {
        return _PS_BASE_URL_ . __PS_BASE_URI__;
    }
    
    
    public static function createAddOrderHistory($idOrder, $status)
    {
        $order_history = new OrderHistory();
        $order_history->id_order = $idOrder;
        $order_history->changeIdOrderState($status, $idOrder);
        $order_history->addWithemail();
    
        return true;
    }
}
