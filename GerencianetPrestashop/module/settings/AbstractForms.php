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



class AbstractForms
{
    public $module;
    public $submit;
    public $values;
    public $form;
    public $process;
    private $icon;
    protected $validate;


    public function __construct($icon = 'icon-cogs')
    {
        $this->icon = $icon;
        $this->module = Module::getInstanceByName('GerencianetPrestashop');
    }

    /**
     * Build Config Form
     *
     * @return void
     */
    public function buildForm($title, $fields)
    {
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $title,
                    'icon' => $this->icon,
                ),
                'class' => 'credentials',
                'input' => $fields,
                'submit' => array(
                    'title' => $this->module->l('Save', 'AbstractSettings')
                ),
            ),
        );
    }

    /**
     * Verify form submit
     *
     * @return void
     */
    public function verifyPostProcess()
    {
        if (((bool) Tools::isSubmit($this->submit)) == true) {
            return $this->postFormProcess();
        }
    }

    /**
     * Save form data
     *
     * @return void
     */
    public function postFormProcess()
    {

        foreach (array_keys($this->values) as $key) {
            $value = htmlentities(strip_tags(Tools::getValue($key)), ENT_QUOTES, 'UTF-8');

            if (!$this->validateInput($key, $value) || $key == 'GERENCIANET_CERTIFICADO_PIX_SALVO') {
                continue;
            }

            $this->values[$key] = $value;
            Configuration::updateValue($key, $value);
        }
    }



    /**
     * Validate input for submit
     *
     * @param mixed $input
     * @return void
     */
    public function validateInput($input, $value)
    {
        if ($this->validate != null && array_key_exists($input, $this->validate)) {
            switch ($this->validate[$input]) {
                case "expiration_preference":
                    if ($value != '' && !is_numeric($value)) {

                        return false;
                    }
                    break;

                case "public_key":
                    if ($value == '') {

                        return false;
                    }
                    break;

                case "access_token":

                    break;

                case "percentage":
                    if ($value != '' && is_numeric($value) && $value > 99 || $value != '' && !is_numeric($value)) {

                        return false;
                    }
                    break;

                case "payment_due":
                    return false;

                    break;

                default:
                    return true;
            }
        }

        return true;
    }
}
