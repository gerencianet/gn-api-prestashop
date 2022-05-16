{*
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
* @author PrestaShop SA <contact@prestashop.com>
    * @copyright 2007-2022 PrestaShop SA
    * @license http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
    * International Registered Trademark & Property of PrestaShop SA
    *}


    <div class="border border-2">

        <div class="row px-4 pt-2">
            <div class="col-md-4">
                <img src="{$module_dir|escape:'html':'UTF-8'}views/img/gerencianet-configurations.png">
            </div>
            <div class="col-md-8">
                <span> <b> Cobrança emitida pela Gerencianet</b></span>
            </div>
        </div>
        <div class="border border-1 border-success m-4">
            <div class="row m-2">
                <div class="col-md-2">
                    <img class="ml-4" src="{$module_dir|escape:'html':'UTF-8'}views/img/check-circle.svg" height="60">
                </div>
                <div class="col-md-9">
                    <span>A cobrança foi gerado com sucesso. Fique ligado na data de processamento da
                        cobrança!</span>
                    <p>Identificador da cobrança: <b>{$id_order}</b></p>
                </div>
            </div>

            {if $charge_type == 'pix'}
            <div class="col-md-4">
                <img src="{$qrcode}">

            </div>
            <div class="col-md-6 d-flex align-items-center ">
                <button class="btn  w-100" id="copia_cola"> <img
                        src="{$module_dir|escape:'html':'UTF-8'}views/img/content-copy.svg" height="45" class="icon">
                    Copie o código do qrcode</button>
                <p class="mt-1">Utilize o botão acima para copiar o codigo do qrcode e colar no seu banco ou
                    abra o seu aplicativo e escaneie a imagem ao lado com a câmera. </p>
            </div>



            {/if}
            {if $charge_type == 'billet'}
            <div class="col-md-4">

            </div>
            <div class="col-md-4">
                <button class="btn w-50">
                    <a href="{$charge_data}" target="_blank" class="colorA">
                        <img class="icon" src="{$module_dir|escape:'html':'UTF-8'}views/img/download.svg" height="45">
                        Acessar boleto responsivo
                    </a></button>
            </div>



            <br />
            <br />
            <br />
            <br />
            <br />

            <div class="row mt-4">

                <div class="col-md-12">
                    <iframe src="{$charge_data}" frameborder="0" width="100%" height="600px"
                        style="overflow: auto; position: absolute;"></iframe>
                </div>
            </div>
            <div style="margin-top: 600px;">

            </div>


            {/if}
            {if $charge_type == 'card'}
            <div class="col-md-8 d-flex align-items-center ">
                <span><b>O pagamento está sendo processado e a confirmação ocorrerá em até 48 horas.</b></span>
            </div>
            {/if}
        </div>
    </div>
    </div>

    {if $charge_type == 'pix'}
    <script type="text/javascript">
        document.getElementById('copia_cola').addEventListener("click", async () => {
            await navigator.clipboard.writeText('{$charge_data}');
            Swal.fire({
                title: 'Copia e cola copiado com sucesso!',
                icon: 'success',
                confirmButtonText: 'OK',
            });
        }, false);



    </script>
    {/if}

    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>

    <style type="text/css">
        .btn {
            background-color: white !important;
            border-color: #F37021 !important;
        }

        .btn:focus+.btn,
        .btn:focus {
            color: #000;
            background-color: #F37021;
            border-color: #283048;
            box-shadow: 0 0 0 0.25rem rgb(11 172 204 / 50%);
        }

        .colorA {
            color: #000 !important;
        }
    </style>