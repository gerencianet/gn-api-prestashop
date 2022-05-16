{*
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
* @author PrestaShop SA <contact@prestashop.com>
    * @copyright 2007-2015 PrestaShop SA
    * @license http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
    * International Registered Trademark & Property of PrestaShop SA
    *}

    {if $sandbox}
    <div class="shadow-sm p-3 mb-3 bg-body rounded border-top border-2 border-danger">
        Modo Sandbox está ativo. Os pagamentos não serão validos.
    </div>
    {/if}

    <div id="valueTotal" class="d-none">{$value}</div>
    <div id="order_id" class="d-none">{$order_id}</div>

    </br>


    {$boletoActive = false}
    <form action="{$link->getModuleLink('GerencianetPrestashop', 'validation', [], true)|escape:'html'}" method="post"
        id="payment-form-gn">

        <input type="text" class="d-none" name="gn_charge_id" id="gn_charge_id">
        <input type="text" class="d-none" name="payment_token" id="payment-token">

        <ul class="nav nav-pills nav-fill mb-3 shadow-lg " id="pills-tab" role="tablist">
            {if $value > 5}

            {if $desconto_boleto}
            {$totalBoleto = $value - ($value * $percentual_desconto_boleto / 100)}
            {else}
            {if $boleto}
            <li class="nav-item" role="presentation" name="boleto">
                <button class="nav-link " id="pills-boleto-tab" data-bs-toggle="pill" data-bs-target="#pills-boleto"
                    type="button" role="tab" aria-controls="pills-boleto" aria-selected="true"
                    onclick="changeModePayment('boleto')">Boleto</button>
            </li>
            {$boletoActive = true}
            {/if}
            {/if}


            {if $totalBoleto > 5}
            {if $boleto}
            <li class="nav-item" role="presentation">
                <button class="nav-link " id="pills-boleto-tab" data-bs-toggle="pill" data-bs-target="#pills-boleto"
                    type="button" role="tab" aria-controls="pills-boleto" aria-selected="true"
                    onclick="changeModePayment('boleto')">Boleto</button>
            </li>
            {$boletoActive = true}
            {/if}
            {/if}


            {if $cartao}
            <li class="nav-item" role="presentation" name="cartao">
                <button class="nav-link" id="pills-cartao-tab" data-bs-toggle="pill" data-bs-target="#pills-cartao"
                    type="button" role="tab" aria-controls="pills-cartao" aria-selected="true"
                    onclick="changeModePayment('cartao')">Cartão</button>
            </li>
            {/if}


            {else}

            {/if}
            {if $pix}
            <li class="nav-item" role="presentation" name="pix">
                <button class="nav-link" id="pills-pix-tab" data-bs-toggle="pill" data-bs-target="#pills-pix"
                    type="button" role="tab" aria-controls="pills-pix" aria-selected="true"
                    onclick="changeModePayment('pix')">Pix</button>
            </li>
            {/if}
        </ul>

        <div class="tab-content" id="pills-tabContent">
            {if $boleto}
            {if $boletoActive}
            <div class="tab-pane " id="pills-boleto" role="tabpanel" aria-labelledby="pills-boleto-tab">
                <p>Optando por pagar através de boleto bancário, a confirmação será feita no próximo dia útil após o
                    pagamento.</p>

                <label for="inputCPF_CNPJBOLETO" class="form-label">CPF/CNPJ</label>
                <input required type="text" class="form-control" id="inputCPF_CNPJBOLETO"
                    aria-describedby="cpfCnpjHelp">
                <small id="cpfCnpjHelp" class="form-text text-muted">CPF/CNPJ é necessario para finalização da
                    compra!</small>
                <br />

                {if $desconto_boleto}

                <div class=" d-flex justify-content-between">
                    <div>
                        <strong>Desconto:</strong>
                    </div>
                    <div>
                        <strong>{$percentual_desconto_boleto}%</strong>
                    </div>
                </div>
                {/if}
                <div class=" d-flex justify-content-between">
                    <div>
                        <strong>TOTAL:</strong>
                    </div>
                    <div>
                        {if $desconto_boleto}
                        <strong>R${number_format($totalBoleto, 2, ',', ' ')}</strong>
                        {else}
                        <strong>R${number_format($value, 2, ',', ' ')}</strong>
                        {/if}
                    </div>
                </div>
            </div>
            {/if}
            {/if}

            {if $cartao}
            <div class="tab-pane " id="pills-cartao" role="tabpanel" aria-labelledby="pills-cartao-tab">
                <div class="container d-flex justify-content-center">
                    <div class="card-gn">
                        <div class="card_principal card_front">
                            <div class="row mt-2">
                                <div class="col-8"></div>
                                <div class="col-4"><img src="{$module_dir|escape:'html':'UTF-8'}views/img/other.svg"
                                        class="brandCard" height="45" alt="" srcset="">
                                </div>
                            </div>
                            <div class="row mt-4 d-flex justify-content-end align-items-center">
                                <div class="col-1 ml-4">
                                    <img class="chip" src="{$module_dir|escape:'html':'UTF-8'}views/img/chip.svg"
                                        height="45">
                                </div>
                                <div class="col-9 ml-2">
                                    <p class="card_number">**** **** **** 6258</p>
                                </div>
                            </div>
                            <div class="row ml-3 mt-3">
                                <div class="col-7">
                                    <span class="card_label">Nome do Titular</span>
                                    <p class="card_info card_name">{$customer_firstname}</p>
                                </div>
                                <div class="col-5">
                                    <span class="card_label">Expiração</span>
                                    <p class="card_info card_date">MM/YY</p>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="card_principal card_back">
                            <div class="card_black_line"></div>
                            <div class="card_back_content mt-3">
                                <div class="card_secret " data-conteudo="{$customer_firstname}">
                                    <p class="card_secret_last">123</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row  mt-2">
                    <div class="form-group">
                        <h6>Seleciona a Bandeira do Cartão</h6>

                        <div class="d-flex">
                            <label class="p-2 bg-light">
                                <input type="radio" name="test" value="visa" id="visaBrand">
                                <img src="{$module_dir|escape:'html':'UTF-8'}views/img/visa.svg" height="25" alt=""
                                    srcset="">
                            </label>
                            <label class="p-2 bg-light">
                                <input type="radio" name="test" value="mastercard" id="mastercardBrand">
                                <img src="{$module_dir|escape:'html':'UTF-8'}views/img/mastercard.svg" height="25"
                                    alt="" srcset="">
                            </label>
                            <label class="p-2 bg-light">
                                <input type="radio" name="test" value="amex" id="amexBrand">
                                <img src="{$module_dir|escape:'html':'UTF-8'}views/img/amex.svg" height="25" alt=""
                                    srcset="">
                            </label>
                            <label class="p-2 bg-light">
                                <input type="radio" name="test" value="elo" id="eloBrand">
                                <img src="{$module_dir|escape:'html':'UTF-8'}views/img/elo.svg" height="25" alt=""
                                    srcset="">
                            </label>
                            <label class="p-2 bg-light">
                                <input type="radio" name="test" value="hipercard" id="hipercardBrand">
                                <img src="{$module_dir|escape:'html':'UTF-8'}views/img/hipercard.svg" height="25" alt=""
                                    srcset="">
                            </label>
                        </div>
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="">
                        <label for="inputNumberCardGn" class="form-label">Numero do cartão</label>
                        <input required type="text" class="form-control" id="inputNumberCardGn"
                            aria-describedby="numberHelp">
                        <small id="numberHelp" class="form-text text-muted">Não compartilhe seu cartão</small>
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="">
                        <label for="inputNameCardGn" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="inputNameCardGn"
                            placeholder="{$customer_firstname}">
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-md-6">
                        <label for="inputDateCardGn" class="form-label">Data de expiração</label>
                        <input type="text" class="form-control " id="inputDateCardGn" placeholder="01/2022">
                    </div>
                    <div class="col-md-6">
                        <label for="inputCVVCardGn" class="form-label">Codigo de segurança</label>
                        <input type="text" class="form-control" id="inputCVVCardGn">
                    </div>
                </div>

                <div class="GNinstallments">
                    <span class="dropdown-el">
                        <input class="GN-inputInstallments" type="radio" name="installments" value="Installments"
                            checked="checked" id="calculando-parcelas"><label class="GN-labelInstallments"
                            for="calculando-parcelas">Favor inserir os dados do cartão para calcular o valor das
                            parcelas</label>
                    </span>
                </div>

                <label for="inputCPF_CNPJCartao" class="form-label">CPF/CNPJ</label>
                <input required type="text" class="form-control" id="inputCPF_CNPJCartao"
                    aria-describedby="cpfCnpjHelp">
                <small id="cpfCnpjHelp" class="form-text text-muted">CPF/CNPJ é necessario para finalização da
                    compra!</small>
                <hr class="mt-2" />
                <div class="mt-2">
                    <h6>Endereço de Cobrança</h6>
                    <div class="d-flex align-items-baseline">
                        <input type="checkbox" name="checkEnderenco" id="checkEnderenco">
                        <label for="checkEnderenco" class="ml-1">
                            <span> Utilizar dados já cadastrados nas configurações de conta do Prestashop</span>
                        </label>
                    </div>
                    <div class="">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="cepCobrança" class="form-label">CEP</label>
                                <input type="text" name="cepCobrança" id="cepCobrança" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label for="logradouroCep" class="form-label">Logradouro</label>
                                <input type="text" name="logradouroCep" id="logradouroCep" class="form-control"
                                    required>
                            </div>
                            <div class="col-md-2">
                                <label for="complementoCobrança" class="form-label">Complemento</label>
                                <input type="text" name="complementoCobrança" id="complementoCobrança"
                                    class="form-control" required>
                            </div>
                            <div class="col-md-2">
                                <label for="numberCobrança" class="form-label">Número</label>
                                <input type="text" name="numberCobrança" id="numberCobrança" class="form-control"
                                    required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <label for="bairroCobrança" class="form-label">Bairro</label>
                                <input type="text" name="bairroCobrança" id="bairroCobrança" class="form-control"
                                    required>
                            </div>

                            <div class="col-md-4">
                                <label for="cidadeCobrança" class="form-label">Cidade</label>
                                <input type="text" name="cidadeCobrança" id="cidadeCobrança" class="form-control"
                                    required>
                            </div>
                            <div class="col-md-4">
                                <label for="estadoCobrança" class="form-label">Estado</label>
                                <select class="form-select" aria-label="Default select example" id="estadoCobrança">
                                    <option value=" " selected>Selecione</option>
                                    <option value="AC">Acre</option>
                                    <option value="AL">Alagoas</option>
                                    <option value="AP">Amapá</option>
                                    <option value="AM">Amazonas</option>
                                    <option value="BA">Bahia</option>
                                    <option value="CE">Ceará</option>
                                    <option value="DF">Distrito Federal</option>
                                    <option value="ES">Espírito Santo</option>
                                    <option value="GO">Goiás</option>
                                    <option value="MA">Maranhão</option>
                                    <option value="MT">Mato Grosso</option>
                                    <option value="MS">Mato Grosso do Sul</option>
                                    <option value="MG">Minas Gerais</option>
                                    <option value="PA">Pará</option>
                                    <option value="PB">Paraíba</option>
                                    <option value="PR">Paraná</option>
                                    <option value="PE">Pernambuco</option>
                                    <option value="PI">Piauí</option>
                                    <option value="RJ">Rio de Janeiro</option>
                                    <option value="RN">Rio Grande do Norte</option>
                                    <option value="RS">Rio Grande do Sul</option>
                                    <option value="RO">Rondônia</option>
                                    <option value="RR">Roraima</option>
                                    <option value="SC">Santa Catarina</option>
                                    <option value="SP">São Paulo</option>
                                    <option value="SE">Sergipe</option>
                                    <option value="TO">Tocantins</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="d-flex justify-content-between mt-1">
                    <div>
                        <strong>TOTAL:</strong>
                    </div>
                    <div>
                        <strong> <span class="totalCartao"> --,-- </span></strong>
                    </div>
                </div>
            </div>
            {/if}
            {if $pix}
            <div class="tab-pane " id="pills-pix" role="tabpanel" aria-labelledby="pills-pix-tab">
                <p>Optando por pagar via Pix, o pagamento é processado e a confirmação ocorrerá em alguns segundos.</p>

                <label for="inputCPF_CNPJPix" class="form-label">CPF/CNPJ</label>
                <input required type="text" class="form-control" id="inputCPF_CNPJPix" aria-describedby="cpfCnpjHelp">
                <small id="cpfCnpjHelp" class="form-text text-muted">CPF/CNPJ é necessario para finalização da
                    compra!</small>


                {if $desconto_pix}

                <div class=" d-flex justify-content-between">
                    <div>
                        <strong>Desconto:</strong>
                    </div>
                    <div>
                        <strong>{$percentual_desconto_pix}%</strong>
                    </div>
                </div>
                {/if}
                <div class=" d-flex justify-content-between">
                    <div>
                        <strong>TOTAL:</strong>
                    </div>
                    <div>
                        {if $desconto_pix}
                        {$totalPix = $value - ($value * $percentual_desconto_pix / 100)}
                        <strong>R${number_format($totalPix, 2, ',', ' ')}</strong>
                        {else}
                        <strong>R${number_format($value, 2, ',', ' ')}</strong>
                        {/if}
                    </div>
                </div>
            </div>

            {/if}
        </div>


        <input type="text" class="d-none" name="payment_mode" id="payment-mode">
    </form>

    {$url = $link->getModuleLink('GerencianetPrestashop', '', [], true)|escape:'html'}
    {$url = str_replace("index.php?fc=module&module=GerencianetPrestashop&controller=default","modules/",$url)}

    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript" src="{url|escape:'html'}modules/GerencianetPrestashop/views/js/front.js"
        defer></script>
    <script type="text/javascript" src="{url|escape:'html'}modules/GerencianetPrestashop/views/js/bootstrap5.js"
        defer></script>
    <script type="text/javascript" src="{url|escape:'html'}modules/GerencianetPrestashop/views/js/jquerymaskedinput.js"
        defer></script>
    <script type="text/javascript" src="{url|escape:'html'}modules/GerencianetPrestashop/views/js/payment_form.js"
        defer></script>

    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js" defer></script>


    {if $sandbox}
    <script type='text/javascript' defer>

        var s = document.createElement('script');
        var v = parseInt(Math.random() * 1000000);
        s.src = 'https://sandbox.gerencianet.com.br/v1/cdn/{$id_conta}/' + v;
        s.async = false;
        s.id = '{$id_conta}';
        if (!document.getElementById('{$id_conta}')) {
            document.getElementsByTagName('head')[ 0 ].appendChild(s);
        }
        $gn = {
            validForm: true, processed: false, done: {}, ready: function (fn) {
                $gn.done = fn;
            }
        }
        var home_url = "{$base_url_dir}";
        var name_client = "{$customer_firstname}";
        var id_charge = 0;

        var address = JSON.parse("{$endereco_entrega}".replace(/&quot;/g, '"'));
    </script>
    {else}
    <script type='text/javascript' defer>

        var s = document.createElement('script');
        s.type = 'text/javascript';
        var v = parseInt(Math.random() * 1000000);
        s.src = 'https://api.gerencianet.com.br/v1/cdn/{$id_conta}/' + v;
        s.async = false;
        s.id = '{$id_conta}';
        if (!document.getElementById('{$id_conta}')) {
            document.getElementsByTagName('head')[ 0 ].appendChild(s);
        };
        $gn = {
            validForm: true, processed: false, done: {}, ready: function (fn) {
                $gn.done = fn;
            }
        };
        var home_url = "{$base_url_dir}";
        var name_client = "{$customer_firstname}";
        var id_charge = 0;

        var address = JSON.parse("{$endereco_entrega}".replace(/&quot;/g, '"'));
    </script>
    {/if}


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">