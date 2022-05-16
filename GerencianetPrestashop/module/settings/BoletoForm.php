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

use Contrib\Bundle\CoverallsV1Bundle\Config\Configurator;

require_once GN_ROOT_URL . '/module/settings/AbstractForms.php';


class BoletoForm extends AbstractForms
{

    public function __construct()
    {
        parent::__construct('icon-barcode');
        $this->submit = 'submitBoletoForm';
        $this->values = $this->getFormValues();
        $this->form = $this->generateForm();
        $this->process = $this->verifyPostProcess();
    }

    /**
     * Generate inputs form
     *
     * @return void
     */
    public function generateForm()
    {
        $title = $this->module->l('Configuração do boleto', 'BoletoForm');
        $fields = [
            [
                'col' => 4,
                'type' => 'text',
                'label' => $this->module->l('Número de dias'),
                'prefix' => '<i class="icon-calendar"></i>',
                'desc' => $this->module->l('Dias para vencimento do boleto após emissão.'),
                'name' => 'GERENCIANET_VENCIMENTO_DIAS_BOLETO',
            ],
            [
                'col' => 8,
                'type' => 'switch',
                'label' => $this->module->l('Cancelar Boletos não pagos?'),
                'desc' => $this->module->l('Quando habilitado, cancela todos os Boletos que não foram pagos. Impedindo que o cliente pague o Boleto após a data de vencimento.'),
                'name' => 'GERENCIANET_CANCELAR_BOLETOS_AUTO',
                'values' => [
                    [
                        'id' => 'active',
                        'value' => true,
                        'label' => $this->module->l('Habilitado')
                    ],
                    [
                        'id' => 'disabled',
                        'value' => false,
                        'label' => $this->module->l('Desabilitado')
                    ]
                ]
            ],
            [
                'col' => 8,
                'type' => 'switch',
                'label' => $this->module->l('Ativar desconto?'),
                'name' => 'GERENCIANET_DESCONTO_BOLETO_ACTIVE',
                'values' => [
                    [
                        'id' => 'boleto_active',
                        'value' => true,
                        'label' => $this->module->l('Habilitado')
                    ],
                    [
                        'id' => 'boleto_disabled',
                        'value' => false,
                        'label' => $this->module->l('Desativado')
                    ]
                ]
            ], [
                'col' => 4,
                'type' => 'text',
                'label' => $this->module->l('Percentual de desconto do boleto'),
                'prefix' => '<i class="icon-money"></i>',
                'name' => 'GERENCIANET_DESCONTO_BOLETO_VALOR',
                'hint' => 'Desconto para pagamento por boleto.',
                'desc' => $this->module->l('Percentual de desconto para pagamento por boleto.'),
                'placeholder' => '0.00',
            ], [
                'col' => 4,
                'type' => 'text',
                'label' => $this->module->l('Percentual de multa'),
                'prefix' => '<i class="icon-money"></i>',
                'name' => 'GERENCIANET_MULTA_BOLETO_PERCENTUAL',
                'hint' => 'Percentual de multa para pagamento por boleto.',
                'desc' => $this->module->l('Percentual de multa para pagamento por boleto.'),
                'placeholder' => '0.00',
            ], [
                'col' => 4,
                'type' => 'text',
                'label' => $this->module->l('Percentual de juros'),
                'prefix' => '<i class="icon-money"></i>',
                'name' => 'GERENCIANET_JUROS_BOLETO_PERCENTUAL',
                'hint' => 'Percentual de juros para pagamento por boleto.',
                'desc' => $this->module->l('Percentual de juros para pagamento por boleto.'),
                'placeholder' => '0.00',
            ],
            [
                'col' => 8,
                'type' => 'switch',
                'label' => $this->module->l('Enviar boleto por e-mail?'),
                'desc' => $this->module->l('Quando habilitado, envia o boleto por e-mail ao cliente.'),
                'name' => 'GERENCIANET_ENVIAR_BOLETO_EMAIL',
                'values' => [
                    [
                        'id' => 'active_boleto_email',
                        'value' => true,
                        'label' => $this->module->l('Habilitado')
                    ],
                    [
                        'id' => 'disabled_boleto_email',
                        'value' => false,
                        'label' => $this->module->l('Desabilitado')
                    ]
                ]
            ]
        ];

        return $this->buildForm($title, $fields);
    }

    /**
     * Save form data
     *
     * @return void
     */
    public function postFormProcess()
    {
        $this->validate = ([
            'GERENCIANET_CANCELAR_BOLETOS_AUTO' => 'cancelar_boletos_auto',
            'GERENCIANET_DESCONTO_BOLETO_ACTIVE' => 'desconto_boleto_active',
            'GERENCIANET_DESCONTO_BOLETO_VALOR' => 'desconto_boleto_valor',
            'GERENCIANET_VENCIMENTO_DIAS_BOLETO' => 'dias_vencimento_boleto',
            'GERENCIANET_ENVIAR_BOLETO_EMAIL' => 'enviar_boleto_email',
            'GERENCIANET_JUROS_BOLETO_PERCENTUAL' => 'juros_boleto_percentual',
            'GERENCIANET_MULTA_BOLETO_PERCENTUAL' => 'multa_boleto_percentual'

        ]);

        parent::postFormProcess();
    }


    /**
     * Set values for the form inputs
     *
     * @return array
     */
    public function getFormValues()
    {
        return array(
            'GERENCIANET_CANCELAR_BOLETOS_AUTO' => Configuration::get('GERENCIANET_CANCELAR_BOLETOS_AUTO'),
            'GERENCIANET_DESCONTO_BOLETO_VALOR' => Configuration::get('GERENCIANET_DESCONTO_BOLETO_VALOR'),
            'GERENCIANET_DESCONTO_BOLETO_ACTIVE' => (bool)Configuration::get('GERENCIANET_DESCONTO_BOLETO_ACTIVE'),
            'GERENCIANET_VENCIMENTO_DIAS_BOLETO' => Configuration::get('GERENCIANET_VENCIMENTO_DIAS_BOLETO'),
            'GERENCIANET_ENVIAR_BOLETO_EMAIL' => Configuration::get('GERENCIANET_ENVIAR_BOLETO_EMAIL'),
            'GERENCIANET_JUROS_BOLETO_PERCENTUAL' => Configuration::get('GERENCIANET_JUROS_BOLETO_PERCENTUAL'),
            'GERENCIANET_MULTA_BOLETO_PERCENTUAL' => Configuration::get('GERENCIANET_MULTA_BOLETO_PERCENTUAL')
        );
    }





    public function validate()
    {

        $num = Configuration::get('GERENCIANET_VENCIMENTO_DIAS_BOLETO');

        if (!is_numeric($num) || strlen($num) == 0 || $num < 1) {
            return array('msg' => 'O número de dias de vencimento do boleto deve ser um número positivo.');
        }

        $desconto_ative = (bool)Configuration::get('GERENCIANET_DESCONTO_BOLETO_ACTIVE');

        if ($desconto_ative) {
            $desconto_value = Configuration::get('GERENCIANET_DESCONTO_BOLETO_VALOR');

            if (!is_numeric($desconto_value) || strlen($desconto_value) == 0) {
                return array('msg' => 'O valor do desconto do boleto deve ser um número');
            }

            if ($desconto_value >= 100) {
                return array('msg' => 'O valor do desconto do boleto não pode ser maior que 99%  ');
            } else if ($desconto_value < 1) {
                return array('msg' => 'O valor do desconto do boleto não pode ser menor que 1%  ');
            }
        }


        if (is_numeric(Configuration::get('GERENCIANET_MULTA_BOLETO_PERCENTUAL'))) {
            if (Configuration::get('GERENCIANET_MULTA_BOLETO_PERCENTUAL') > 10) {
                return array('msg' => 'O valor da multa do boleto não pode ser maior que 10%  ');
            } else if (Configuration::get('GERENCIANET_MULTA_BOLETO_PERCENTUAL') < 0) {
                return array('msg' => 'O valor da multa do boleto não pode ser menor que 0%  ');
            }
        } else {
            if (Configuration::get('GERENCIANET_MULTA_BOLETO_PERCENTUAL') != '')
                return array('msg' => 'É necessario informar um valor para a multa do boleto');
        }

        if (is_numeric(Configuration::get('GERENCIANET_JUROS_BOLETO_PERCENTUAL'))) {
            if (Configuration::get('GERENCIANET_JUROS_BOLETO_PERCENTUAL') > 33) {
                return array('msg' => 'O valor do juros do boleto não pode ser maior que 0.33%  ');
            } else if (Configuration::get('GERENCIANET_JUROS_BOLETO_PERCENTUAL') < 0) {
                return array('msg' => 'O valor do juros do boleto não pode ser menor que 0%  ');
            }
        } else {

            if (Configuration::get('GERENCIANET_JUROS_BOLETO_PERCENTUAL') != '')
                return array('msg' => 'É necessario informar um valor para o juros do boleto');
        }
    }
}
