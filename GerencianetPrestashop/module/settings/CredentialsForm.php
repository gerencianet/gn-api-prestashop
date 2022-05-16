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
require_once GN_ROOT_URL . '/module/settings/AbstractForms.php';


class CredentialsForm extends AbstractForms
{

    public function __construct()
    {
        parent::__construct();

        $this->submit = 'submitCredentials';
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

        $title = $this->module->l('Credenciais', 'CredentialsForm');

        $fields = [
            [
                'col' => 4,
                'type' => 'text',
                'label' => $this->module->l('Client_ID Produção'),
                'required' => true,
                'name' => 'GERENCIANET_CLIENT_ID_PRODUCAO',
                'prefix' => '<i class="icon-briefcase"></i>',
                'hint' => 'Por favor, forneça o Cliente_Id de produção da sua conta; será necessário para fazer pagamento.'
            ],
            [
                'col' => 4,
                'type' => 'text',
                'label' => $this->module->l('Client_Secret Produção'),
                'required' => true,
                'name' => 'GERENCIANET_CLIENT_SECRET_PRODUCAO',
                'prefix' => '<i class="icon-unlock"></i>',
                'hint' => 'Por favor, forneça o Client_Secret de produção da sua conta. Será necessário para fazer pagamentos.'
            ],
            [
                'col' => 4,
                'type' => 'text',
                'label' => $this->module->l('Client_ID Homologação'),
                'required' => true,
                'name' => 'GERENCIANET_CLIENT_ID_HOMOLOGACAO',
                'prefix' => '<i class="icon-gamepad"></i>',
                'hint' => 'Por favor, forneça o Client_ID de desenvolvimento da sua conta. Será necessário para fazer testes de pagamento.'
            ],
            [
                'col' => 4,
                'type' => 'text',
                'label' => $this->module->l('Client_Secret Homologação'),
                'required' => true,
                'name' => 'GERENCIANET_CLIENT_SECRET_HOMOLOGACAO',
                'prefix' => '<i class="icon-unlock"></i>',
                'hint' => 'Por favor, forneça o Client_Secret de desenvolvimento da sua conta. Será necessário para fazer testes de pagamento.'
            ],
            [
                'col' => 4,
                'type' => 'text',
                'label' => $this->module->l('Código Identificador da Conta'),
                'required' => true,
                'name' => 'GERENCIANET_ID_CONTA',
                'prefix' => '<i class="icon-puzzle-piece"></i>',
                'hint' => 'Por favor, digite o código identificador de sua conta. Será necessário para completar o pagamento.'
            ],
            [
                'col' => 4,
                'type' => 'switch',
                'label' => $this->module->l('Ambiente de emissão'),
                'name' => 'GERENCIANET_PRODUCAO_SANDBOX',
                'is_bool' => true,
                'values' => [
                    [
                        'id' => 'homologation',
                        'value' => true,
                        'label' => $this->module->l('Homogolação')
                    ],
                    [
                        'id' => 'production',
                        'value' => false,
                        'label' => $this->module->l('Produção')
                    ]
                ]
            ], [
                'col' => 4,
                'type' => 'checkbox',
                'label' => $this->module->l('Formas de pagamentos:'),
                'name' => 'GERENCIANET_FORMAS_PAGAMENTOS',
                'required' => true,

            ],
            [
                'col' => 4,
                'type' => 'switch',
                'name' => 'GERENCIANET_FORMAS_PAGAMENTOS_BOLETO',
                'is_bool' => true,
                'label' => $this->module->l('Boleto'),
                'values' => [
                    [
                        'id' => 'active',
                        'value' => true,
                        'label' => $this->module->l('Ativado')
                    ],
                    [
                        'id' => 'disabled',
                        'value' => false,
                        'label' => $this->module->l('Desativado')
                    ]
                ]
            ],
            [
                'col' => 4,
                'type' => 'switch',
                'name' => 'GERENCIANET_FORMAS_PAGAMENTOS_CARTAO',
                'is_bool' => true,
                'label' => $this->module->l('Cartão de credito'),
                'values' => [
                    [
                        'id' => 'active',
                        'value' => true,
                        'label' => $this->module->l('Ativado')
                    ],
                    [
                        'id' => 'disabled',
                        'value' => false,
                        'label' => $this->module->l('Desativado')
                    ]
                ]
            ],
            [
                'col' => 4,
                'type' => 'switch',
                'name' => 'GERENCIANET_FORMAS_PAGAMENTOS_PIX',
                'is_bool' => true,
                'label' => $this->module->l('Pix'),
                'desc' => $this->module->l('Ao clicar em salvar, será possivel configurar as formas de pagamento selecionadas.'),
                'values' => [
                    [
                        'id' => 'active',
                        'value' => true,
                        'label' => $this->module->l('Ativado')
                    ],
                    [
                        'id' => 'disabled',
                        'value' => false,
                        'label' => $this->module->l('Desativado')
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
            'GERENCIANET_CLIENT_ID_PRODUCAO' => 'client_id_producao',
            'GERENCIANET_CLIENT_SECRET_PRODUCAO' => 'client_secret_producao',
            'GERENCIANET_CLIENT_ID_HOMOLOGACAO' => 'client_id_homologacao',
            'GERENCIANET_CLIENT_SECRET_HOMOLOGACAO' => 'client_secret_homologacao',
            'GERENCIANET_ID_CONTA' => 'identificador_conta',
            'GERENCIANET_PRODUCAO_SANDBOX' => 'producao_sanbox',
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
            'GERENCIANET_CLIENT_ID_PRODUCAO' => Configuration::get('GERENCIANET_CLIENT_ID_PRODUCAO'),
            'GERENCIANET_CLIENT_SECRET_PRODUCAO' => Configuration::get('GERENCIANET_CLIENT_SECRET_PRODUCAO'),
            'GERENCIANET_CLIENT_ID_HOMOLOGACAO' => Configuration::get('GERENCIANET_CLIENT_ID_HOMOLOGACAO'),
            'GERENCIANET_CLIENT_SECRET_HOMOLOGACAO' => Configuration::get('GERENCIANET_CLIENT_SECRET_HOMOLOGACAO'),
            'GERENCIANET_ID_CONTA' => Configuration::get('GERENCIANET_ID_CONTA'),
            'GERENCIANET_PRODUCAO_SANDBOX' => (bool)Configuration::get('GERENCIANET_PRODUCAO_SANDBOX'),
            'GERENCIANET_ACTIVE' => (bool)Configuration::get('GERENCIANET_ACTIVE'),
            'GERENCIANET_FORMAS_PAGAMENTOS_BOLETO' => Configuration::get('GERENCIANET_FORMAS_PAGAMENTOS_BOLETO'),
            'GERENCIANET_FORMAS_PAGAMENTOS_CARTAO' => Configuration::get('GERENCIANET_FORMAS_PAGAMENTOS_CARTAO'),
            'GERENCIANET_FORMAS_PAGAMENTOS_PIX' => Configuration::get('GERENCIANET_FORMAS_PAGAMENTOS_PIX'),
        );
    }

    public static function getCredentials()
    {
        return array(
            'client_id_production' => Configuration::get('GERENCIANET_CLIENT_ID_PRODUCAO'),
            'client_secret_production' => Configuration::get('GERENCIANET_CLIENT_SECRET_PRODUCAO'),
            'client_id_homologation' => Configuration::get('GERENCIANET_CLIENT_ID_HOMOLOGACAO'),
            'client_secret_homologation' => Configuration::get('GERENCIANET_CLIENT_SECRET_HOMOLOGACAO'),
            'id' => Configuration::get('GERENCIANET_ID_CONTA'),
            'sandbox' => (bool)Configuration::get('GERENCIANET_PRODUCAO_SANDBOX'),
            'active' => (bool)Configuration::get('GERENCIANET_ACTIVE'),
        );
    }

    public function getFormValuesPayments()
    {
        return array(
            'GERENCIANET_FORMAS_PAGAMENTOS_BOLETO' => Configuration::get('GERENCIANET_FORMAS_PAGAMENTOS_BOLETO'),
            'GERENCIANET_FORMAS_PAGAMENTOS_CARTAO' => Configuration::get('GERENCIANET_FORMAS_PAGAMENTOS_CARTAO'),
            'GERENCIANET_FORMAS_PAGAMENTOS_PIX' => Configuration::get('GERENCIANET_FORMAS_PAGAMENTOS_PIX'),
        );
    }

    public function validate()
    {
        $inputs = array(
            'Client ID Produção' => Configuration::get('GERENCIANET_CLIENT_ID_PRODUCAO'),
            'Client Secret Produção' => Configuration::get('GERENCIANET_CLIENT_SECRET_PRODUCAO'),
            'Client ID Homologação' => Configuration::get('GERENCIANET_CLIENT_ID_HOMOLOGACAO'),
            'Client Secret Homologação' => Configuration::get('GERENCIANET_CLIENT_SECRET_HOMOLOGACAO'),
            'Indentificador da conta' => Configuration::get('GERENCIANET_ID_CONTA')
        );
        foreach ($inputs as $key => $value) {
            if ($value == '') {
                return array('key' => $key);
            }
        }

        $inputs = $this->getFormValuesPayments();
        $sum = 0;
        foreach ($inputs as $key => $value) {
            $sum += (int)$value;
        }
        if ($sum == 0) {
            return array('msg' => 'É necessario selecionar ao menos uma forma de pagamento');
        }

        return false;
    }
}
