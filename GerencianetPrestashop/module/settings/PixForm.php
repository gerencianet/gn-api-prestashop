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
require_once GN_ROOT_URL . '/lib/payments/Pix.php';


class PixForm extends AbstractForms
{

    protected static $falha_certificado = false;

    public function __construct()
    {
        parent::__construct('icon-qrcode');
        $this->submit = 'submitPixForm';
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
        $title = $this->module->l('Configuração do pix', 'PixForm');
        $fields = [
            [
                'col' => 4,
                'type' => 'text',
                'label' => $this->module->l('Chave Pix'),
                'prefix' => '<i class="icon-key"></i>',
                'name' => 'GERENCIANET_CHAVE_PIX',
                'hint' => 'Para chave telefone: +5599999999999',
            ], [
                'type' => 'file',
                'label' => $this->module->l('Certificado Pix'),
                'name' => 'GERENCIANET_CERTIFICADO_PIX',
                'hint' => 'Por favor, envie seu certificado.',
                'desc' => $this->module->l('Por favor, envie seu certificado.', 'PixForm')
            ],
            [
                'col' => 8,
                'type' => 'switch',
                'label' => $this->module->l('Ativar desconto?'),
                'name' => 'GERENCIANET_DESCONTO_PIX_ACTIVE',
                'values' => [
                    [
                        'id' => 'pix_desconto_active_on',
                        'value' => true,
                        'label' => $this->module->l('Habilitado')
                    ],
                    [
                        'id' => 'pix_desconto_active_off',
                        'value' => false,
                        'label' => $this->module->l('Desativado')
                    ]
                ],
            ], [
                'col' => 4,
                'type' => 'text',
                'label' => $this->module->l('Percentual de desconto do Pix'),
                'prefix' => '<i class="icon-money"></i>',
                'name' => 'GERENCIANET_DESCONTO_PIX_VALOR',
                'hint' => 'Desconto para pagamento por PIX.',
                'desc' => $this->module->l('Percentual de desconto para pagamento por PIX.'),
                'placeholder' => '0.00',

            ], [
                'col' => 4,
                'type' => 'text',
                'label' => $this->module->l('Tempo de vencimento em horas'),
                'desc' => $this->module->l('Horas para o vencimento do pix após a emissão.'),
                'prefix' => '<i class="icon-bell"></i>',
                'name' => 'GERENCIANET_VENCIMENTO_HORAS_PIX',
                'hint' => 'Tempo de vencimento em horas',
                'placeholder' => '0',
            ],
            [
                'col' => 4,
                'type' => 'switch',
                'label' => $this->module->l('Validar mTLS'),
                'name' => 'GERENCIANET_VALIDAR_MTLS',
                'hint' => 'Entenda os riscos de não configurar o mTLS acessando o link https://gnetbr.com/rke4baDVyd',
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



        $this->save_pix_cert_dir();
        parent::postFormProcess();
        if (!Configuration::get('GERENCIANET_PRODUCAO_SANDBOX')) {
            Pix::updateWebhook(Configuration::get('GERENCIANET_CHAVE_PIX'));
        }
    }

    public function save_pix_cert_dir()
    {
        $pix_cert_file = $_FILES['GERENCIANET_CERTIFICADO_PIX'];

        if (empty($pix_cert_file)) {
            return;
        }

        $dir = '../modules/GerencianetPrestashop/lib/certs/cert.pem';

        $extensao = strtolower(end(explode('.', $pix_cert_file['name'])));

        if ($extensao == 'p12') {
            if (!$cert_file_p12 = file_get_contents($pix_cert_file['tmp_name'])) { // Pega o conteúdo do arquivo .p12
                echo '<div class="error"><p><strong> Falha ao ler arquivo o .p12! </strong></div>';
                return;
            }
            if (!openssl_pkcs12_read($cert_file_p12, $cert_info_pem, "")) { // Converte o conteúdo para .pem
                echo '<div class="error"><p><strong> Falha ao converter o arquivo .p12! </strong></div>';
                return;
            }
            $file_read = "subject=/CN=271207/C=BR\n";
            $file_read .= "issuer=/C=BR/ST=Minas Gerais/O=Gerencianet Pagamentos do Brasil Ltda/OU=Infraestrutura/CN=api-pix.gerencianet.com.br/emailAddress=infra@gerencianet.com.br\n";
            $file_read .= $cert_info_pem['cert'];
            $file_read .= "Key Attributes: <No Attributes>\n";
            $file_read .= $cert_info_pem['pkey'];

            $fp = fopen($dir, "wb");

            if ($fp) {
                fwrite($fp, $file_read);
                fclose($fp);
            }
            PixForm::$falha_certificado = false;
        } else if ($extensao == 'pem') {
            move_uploaded_file($pix_cert_file['tmp_name'], $dir);
            PixForm::$falha_certificado = false;
        }
    }


    /**
     * Set values for the form inputs
     *
     * @return array
     */
    public function getFormValues()
    {

        $values = [
            'GERENCIANET_CHAVE_PIX' => Configuration::get('GERENCIANET_CHAVE_PIX'),
            'GERENCIANET_CERTIFICADO_PIX' => Configuration::get('GERENCIANET_CERTIFICADO_PIX'),
            'GERENCIANET_DESCONTO_PIX_ACTIVE' => Configuration::get('GERENCIANET_DESCONTO_PIX_ACTIVE'),
            'GERENCIANET_DESCONTO_PIX_VALOR' => Configuration::get('GERENCIANET_DESCONTO_PIX_VALOR'),
            'GERENCIANET_VENCIMENTO_HORAS_PIX' => Configuration::get('GERENCIANET_VENCIMENTO_HORAS_PIX'),
            'GERENCIANET_VALIDAR_MTLS' => Configuration::get('GERENCIANET_VALIDAR_MTLS'),
        ];

        return $values;
    }

    public function validate()
    {
        $chave_pix = Configuration::get('GERENCIANET_CHAVE_PIX');

        $patternCPF = '/^[0-9]{11}$/';
        $patternCNPJ = '/^[0-9]{14}$/';
        $patternPHONE = '/^\+[1-9][0-9]\d{1,14}$/';
        $patternEMAIL = '/^[a-z0-9.]+@[a-z0-9]+\.[a-z]+\.([a-z]+)?$/i';
        $patterEVP = "/[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}/";

        if (
            !preg_match($patternCPF, $chave_pix) &&
            !preg_match($patternCNPJ, $chave_pix) &&
            !preg_match($patternPHONE, $chave_pix) &&
            !preg_match($patternEMAIL, $chave_pix) &&
            !preg_match($patterEVP, $chave_pix)
        ) {
            return array('msg' => 'Chave PIX inválida');
        }

        $desconto_ative = (bool)Configuration::get('GERENCIANET_DESCONTO_PIX_ACTIVE');

        if ($desconto_ative) {
            $desconto_valor = Configuration::get('GERENCIANET_DESCONTO_PIX_VALOR');

            if (!is_numeric($desconto_valor) || strlen($desconto_valor) == 0) {
                return array('msg' => 'O valor do desconto do pix deve ser um número');
            }

            if ($desconto_valor > 99.99) {
                return array('msg' => 'O valor do desconto do pix não pode ser maior que 99%  ');
            } else if ($desconto_valor < 1) {
                return array('msg' => 'O valor do desconto do pix não pode ser menor que 1%  ');
            }
        }

        $vencimento_horas = Configuration::get('GERENCIANET_VENCIMENTO_HORAS_PIX');

        if (!is_numeric($vencimento_horas) || strlen($vencimento_horas) == 0) {
            return array('msg' => 'O vencimento do pix deve ser um número');
        }

        if ($vencimento_horas < 1) {
            return array('msg' => 'O vencimento do pix não pode ser menor que 1 hora');
        }

        if (!Configuration::get('GERENCIANET_PRODUCAO_SANDBOX')) {
            $update = Pix::updateWebhook($chave_pix);
            if ($update->code == 400) {
                return array('msg' => 'Falha de cadastro no webhook PIX: ' . $update->message);
            }
        }
    }
}