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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{if $checkout_type=="default"}

<p class="payment_module">

  <a href="{$link->getModuleLink('gerencianet', 'payment')|escape:'html'}" title="{l s='Pagar com Boleto Bancário ou Cartão de Crédito' mod='gerencianet'}" style="background-image: url({$this_path_bw}assets/images/gerencianet-configurations.png); background-repeat: no-repeat; background-position: 15px 28px; padding-left: 180px;">
    
    {l s='Pagar com Boleto Bancário ou Cartão de Crédito' mod='gerencianet'}&nbsp;<span>{l s='(Seu pagamento será confirmado em até 1 dia útil)' mod='gerencianet'}</span>
  </a>
</p>
{else}


<script type='text/javascript'>
{literal}

  var s=document.createElement('script');
  s.type='text/javascript';
  var v=parseInt(Math.random()*1000000);
  s.src='https://{/literal}{if $sandbox}sandbox{else}api{/if}{literal}.gerencianet.com.br/v1/cdn/{/literal}{$payee_code}{literal}/'+v;
  s.async=false;
  s.id='{/literal}{$payee_code}{literal}';
  if(!document.getElementById('{/literal}{$payee_code}{literal}')){
    document.getElementsByTagName('head')[0].appendChild(s);
  };
  $gn={
    validForm:true,
    processed:false,
    done:{},
    ready:function(fn){
      $gn.done=fn;
    }
  };

  var getPaymentToken;
  $gn.ready(function(checkout) {
      getPaymentToken = checkout.getPaymentToken;
  });

  var home_url = "{/literal}{$base_url_dir}{literal}";
  var payCnpj = false;
  var showCnpjFields = true;

  {/literal}  
  {if $billing_persontype==2}
      {literal}
      payCnpj = true; 
      {/literal}
  {/if}
  {if $billing_cnpj && $billing_company}
      {literal}
      showCnpjFields = false; 
      {/literal}
  {/if}
</script>


{if $billet_option & $order_total_billet>=500}


<p class="payment_module">

<div class="gn-osc-payment_module">

<div id="gerencianet-container">
    
    <div style="margin: 0px;" id="gn-billet-payment-option-selector">
        <div class="gn-osc-logo">
        <img src="{$this_path_bw}assets/images/gerencianet-configurations.png" />
        </div>
        <div id="gn-billet-payment-option" class="gn-osc-payment-option gn-osc-payment-option-unselected">
            <div>
                <div id="billet-radio-button" class="gn-osc-left">
                    <input type="radio" name="paymentMethodRadio" id="paymentMethodBilletRadio" class="gn-osc-radio" value="billet" checked="false" />
                </div>
                <div class="gn-osc-left gn-osc-icon-gerencianet">
                    <span class="gn-icon-icones-personalizados_boleto"></span>
                </div>
                <div class="gn-osc-left gn-osc-payment-option-gerencianet">
                    <strong>{l s='Pagar com Boleto Bancário' mod='gerencianet'}</strong>
                    {if $discount>0}
                        <span style="font-size: 14px; line-height: 15px;"><br>+{$discountFormatted}% de desconto</span>
                    {/if}
                </div>
                <div class="gn-osc-left gn-osc-payment-option-sizer"></div>
                <div class="clear"></div>
            </div>
        </div>
        <div class="clear"></div>
    </div>
    <div id="collapse-payment-billet" class="gn-osc-background gn-hide" >
      {if $sandbox}
      <div class="gn-warning" id="wc-gerencianet-messages-sandbox">
          O modo sandbox está ativo. Sua cobrança não será efetivada.
      </div>
      {/if}
      <div class="gn-alert gn-osc-warning-payment gerencianet-messages gn-hide"></div>

      <div class="gn-panel-body">
         <form class="form-horizontal" id="billet-form" name="billet-form" action="{$link->getModuleLink('gerencianet', 'validation', [], true)|escape:'html'}" method="post">
         <input name="gn_charge_id" id="gn_charge_id_billet" type="hidden" value=""/>
          <div class="gn-osc-row gn-osc-pay-comments">
              <p class="gn-left-space-2"><strong>{l s='Optando pelo pagamento por Boleto, a confirmação será realizada no dia útil seguinte ao pagamento.' mod='gerencianet'}</strong></p>
          </div>
          <div class="gn-form">
            <div id="billet-data">
                <div style="background-color: #F3F3F3; border: 1px solid #F3F3F3;">
              <div class="gn-osc-row">
                <div class="gn-col-12 gn-cnpj-row">
                <input type="checkbox" name="pay_billet_with_cnpj" id="pay_billet_with_cnpj" value="1" /> {l s='Pagar com dados de Pessoa Jurídica' mod='gerencianet'}
                </div>
              </div>

              <div id="pay_cnpj" class="required gn-osc-row">
                <div class="gn-col-2 gn-label">
                  <label for="gn_billet_cnpj" class="gn-right-padding-1">{l s='CNPJ:' mod='gerencianet'}</label>
                </div>
                <div class="gn-col-10">
                  
                  <div>
                    <div class="gn-col-3 required">
                      <input type="text" name="gn_billet_cnpj" id="gn_billet_cnpj" class="form-control cnpj-mask" value="" />
                    </div>
                    <div class="gn-col-8">
                      <div class="required">
                        <div class="gn-col-4 gn-label">
                          <label class=" gn-col-12 gn-right-padding-1" for="gn_billet_corporate_name">{l s='Razão Social: ' mod='gerencianet'}</label>
                        </div>
                        <div class="gn-col-8">
                          <input type="text" name="gn_billet_corporate_name" id="gn_billet_corporate_name" class="form-control" value="" />
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              </div>

              <div id="gn_name_row" class="required gn-osc-row gn-billet-field" >
                <div class="gn-col-2 gn-label">
                  <label for="gn_billet_full_name" class="gn-right-padding-1">{l s='Nome: ' mod='gerencianet'}</label>
                </div>
                <div class="gn-col-10">
                  <input type="text" name="gn_billet_full_name" id="gn_billet_full_name" value="{$billing_name}" class="form-control" />
                </div>
              </div>


              <div id="gn_email_row" class=" required gn-osc-row gn-billet-field" >
                <div class="gn-col-2 gn-label">
                  <label class="gn-col-12 gn-right-padding-1" for="gn_billet_email">{l s='Email: ' mod='gerencianet'}</label>
                </div>
                <div class="gn-col-10">
                  <input type="text" name="gn_billet_email" value="{$billing_email}" id="gn_billet_email" class="form-control" />
                </div>
              </div>

              <div id="gn_cpf_phone_row" class="required gn-osc-row gn-billet-field" >
                <div class="gn-col-2 gn-label">
                  <label for="gn_billet_cpf" class="gn-right-padding-1">{l s='CPF: ' mod='gerencianet'}</label>
                </div>
                <div class="gn-col-10">
                  
                  <div>
                    <div class="gn-col-3 required">
                      <input type="text" name="gn_billet_cpf" id="gn_billet_cpf" value="{$billing_cpf}" class="form-control cpf-mask" />
                    </div>
                    <div class="gn-col-8">
                      <div class=" required">
                        <div class="gn-col-4 gn-label">
                        <label class="gn-col-12 gn-right-padding-1" for="gn_billet_phone_number" >{l s='Telefone: ' mod='gerencianet'}</label>
                        </div>
                        <div class="gn-col-4">
                          <input type="text" name="gn_billet_phone_number" id="gn_billet_phone_number" value="{$billing_phone}" class="form-control phone-mask" />
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

            </div>
            </div>
            </form>
        </div>

        <div class="gn-osc-row" style="padding: 5px 20px;">
            {if $discount>0 }
            <div class="gn-osc-row gn-osc-subtotal" style="margin-bottom: 0px;">
                <div style="float: left;">
                    <strong>DESCONTO DE {$discountFormatted}% NO BOLETO:</strong>
                </div>
                <div style="float: right;">
                    <strong>-{$discount_value_formatted}</strong>
                </div>
            </div>
            {/if}
            <div class="gn-osc-row gn-osc-subtotal">
                <div style="float: left;">
                    <strong>TOTAL:</strong>
                </div>
                <div style="float: right;">
                    <strong>{$order_with_billet_discount}</strong>
                </div>
            </div>
            <div class="gn-osc-row">
                <div style="float: right;">
                    <button id="gn-pay-billet-button" class="gn-osc-button">Pagar com Boleto Bancário</button>
                </div>
                <div class="pull-right gn-loading-request">
                    <div class="gn-loading-request-row">
                      <div class="pull-left gn-loading-request-text">
                        Autorizando, aguarde...
                      </div>
                      <div class="pull-left gn-icons">
                        <div class="spin gn-loading-request-spin-box icon-gerencianet"><div class="gn-icon-spinner6 gn-loading-request-spin-icon"></div></div>
                      </div>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
        </div>
      </div>


    </div>

  </div>
</p>

{/if}
    

{if $card_option & $order_total_card >= 500 }

<p class="payment_module">

<div class="gn-osc-payment_module">

<div id="gerencianet-container">

    <div style="margin: 0px;" id="gn-card-payment-option-selector">
        <div class="gn-osc-logo">
        <img src="{$this_path_bw}assets/images/gerencianet-configurations.png" />
        </div>
        
        <div id="gn-card-payment-option" class="gn-osc-payment-option gn-osc-payment-option-unselected">
            <div>
                <div id="card-radio-button" class="gn-osc-left">
                    <input type="radio" name="paymentMethodRadio" id="paymentMethodCardRadio" class="gn-osc-radio" value="card" />
                </div>
                <div class="gn-osc-left gn-osc-icon-gerencianet">
                    <span class="gn-icon-credit-card2"></span>
                </div>
                <div class="gn-osc-left gn-osc-payment-option-gerencianet">
                    <strong>{l s='Pagar com Cartão de Crédito' mod='gerencianet'}</strong>
                    <span style="font-size: 14px; line-height: 15px;"><br>em até {$max_installments}</span>
                </div>
                <div class="gn-osc-left gn-osc-payment-option-sizer"></div>
                <div class="clear"></div>
            </div>
        </div>
        <div class="clear"></div>
    </div>

      <div id="collapse-payment-card"  class="panel-collapse gn-hide gn-osc-background" >
        {if $sandbox}
        <div class="gn-warning" id="wc-gerencianet-messages-sandbox">
            O modo sandbox está ativo. Sua cobrança não será efetivada.
        </div>
        {/if}
        <div class="gn-alert gn-osc-warning-payment gerencianet-messages gn-hide"></div>
        <div class="gn-panel-body">
            <form class="form-horizontal" id="card-form" name="card-form" action="{$link->getModuleLink('gerencianet', 'validation', [], true)|escape:'html'}" method="post">
             <input name="gn_charge_id" id="gn_charge_id_card" type="hidden" value=""/>
                <div class="gn-osc-row gn-osc-pay-comments">
                   <p class="gn-left-space-2"><strong>{l s='Optando pelo pagamento com cartão de crédito, o pagamento é processado e a confirmação ocorrerá em até 48 horas.' mod='gerencianet'}</strong></p>
                </div>
                <div class="gn-form">
                <div id="card-data" >
                        <div style="background-color: #F3F3F3; border: 1px solid #F3F3F3;">
                        <div class="gn-osc-row">
                          <div class="gn-col-12 gn-cnpj-row">
                          <input type="checkbox" name="pay_card_with_cnpj" id="pay_card_with_cnpj" value="1" /> {l s='Pagar com dados de Pessoa Jurídica' mod='gerencianet'}
                          </div>
                        </div>

                        <div id="pay_cnpj_card" class=" required gn-osc-row" >
                          <div class="gn-col-2 gn-label">
                          <label class="gn-right-padding-1" for="gn_card_cnpj">{l s='CNPJ: ' mod='gerencianet'}</label>
                          </div>
                          <div class="gn-col-10">
                            
                            <div>
                              <div class="gn-col-3 required">
                                <input type="text" name="gn_card_cnpj" id="gn_card_cnpj" class="form-control cnpj-mask" value="{$billing_cnpj}" />
                              </div>
                              <div class="gn-col-8">
                                <div class=" required gn-left-space-2">
                                  <div class="gn-col-4 gn-label">
                                    <label class="gn-col-12 gn-right-padding-1" for="gn_card_corporate_name">{l s='Razão Social: ' mod='gerencianet'}</label>
                                  </div>
                                  <div class="gn-col-8">
                                    <input type="text" name="gn_card_corporate_name" id="gn_card_corporate_name" class="form-control" value="{$billing_company}" />
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        </div>

                        <div id="gn_card_name_row" class="required gn-osc-row gn-card-field" >
                          <div class="gn-col-2 gn-label">
                            <label class="gn-col-12 gn-right-padding-1" for="gn_card_full_name">{l s='Nome: ' mod='gerencianet'}</label>
                          </div>
                          <div class="gn-col-10">
                            <input type="text" name="gn_card_full_name" id="gn_card_full_name" value="{$billing_name}" class="form-control" />
                          </div>
                        </div>

                        <div id="gn_card_cpf_phone_row" class="required gn-osc-row gn-card-field" >
                        
                            <div class="gn-col-2 gn-label">
                                <label for="gn_card_cpf" class="gn-right-padding-1" >{l s='CPF: ' mod='gerencianet'}</label>
                            </div>
                            <div class="gn-col-4">
                                <input type="text" name="gn_card_cpf" id="gn_card_cpf" value="{$billing_cpf}" class="form-control cpf-mask gn-minimum-size-field" />
                            </div>
                            <div class="gn-col-6">
                              <div class="gn-col-4 gn-label">
                                  <label class="gn-left-space-2 gn-right-padding-1" for="gn_card_phone_number">{l s='Telefone: ' mod='gerencianet'}</label>
                              </div>
                              <div class="gn-col-8">
                                  <input type="text" name="gn_card_phone_number" value="{$billing_phone}" id="gn_card_phone_number" class="form-control phone-mask gn-minimum-size-field" />
                              </div>
                              
                            </div>
                        </div>

                        <div id="gn_card_birth_row" class=" required gn-osc-row gn-card-field" >
                          <div class="gn-col-3 gn-label-birth">
                              <label class="gn-right-padding-1" for="gn_card_birth">{l s='Data de Nascimento: ' mod='gerencianet'}</label>
                          </div>
                          <div class="gn-col-3">
                              <input type="text" name="gn_card_birth" id="gn_card_birth" value="{$billing_birthdate}" class="form-control birth-mask" />
                          </div>
                        </div>

                        <div id="gn_card_email_row" class=" required gn-card-field" >
                          <div class="gn-col-2">
                            <label class="gn-col-12 gn-label gn-right-padding-1" for="gn_card_email">{l s='Email: ' mod='gerencianet'}</label>
                          </div>
                          <div class="gn-col-10">
                            <input type="text" name="gn_card_email" value="{$billing_email}" id="gn_card_email" class="form-control" />
                          </div>
                        </div>

                    <div id="billing-adress" class="gn-section">
                        <div class="gn-osc-row gn-card-field">
                            <p>
                            <strong>{l s='ENDEREÇO DE COBRANÇA' mod='gerencianet'}</strong>
                            </p>
                        </div>

                        <div id="gn_card_street_number_row" class="required gn-osc-row gn-card-field" >
                            <div class="gn-col-2">
                                <label class="gn-col-12 gn-label gn-right-padding-1" for="gn_card_street">{l s='Endereço: ' mod='gerencianet'}</label>
                            </div>
                            
                            <div class="gn-col-10">
                                <div class="gn-col-6 required">
                                    <input type="text" name="gn_card_street" id="gn_card_street" value="{$billing_address_1}" class="form-control" />
                                </div>
                                <div class="gn-col-6">
                                    <div class=" required gn-left-space-2">
                                        <div class="gn-col-5">
                                            <label class="gn-col-12 gn-label gn-right-padding-1" for="gn_card_street_number">{l s='Número: ' mod='gerencianet'}</label>
                                        </div>
                                        <div class="gn-col-7">
                                            <input type="text" name="gn_card_street_number" id="gn_card_street_number" value="{$billing_number}" class="form-control" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="gn_card_neighborhood_row" class="gn-osc-row gn-card-field">
                            <div class="gn-col-2 required">
                                <label class="gn-col-12 gn-label required gn-right-padding-1" for="gn_card_neighborhood">{l s='Bairro: ' mod='gerencianet'}</label>
                            </div>
                    
                            <div class="gn-col-3">
                                
                                <input type="text" name="gn_card_neighborhood" id="gn_card_neighborhood" value="{$billing_neighborhood}" class="form-control" />
                            </div>
                            <div class="gn-col-7">
                                <div class=" gn-left-space-2">
                                  <div class="gn-col-5">
                                  <label class="gn-col-12 gn-label gn-right-padding-1" for="gn_card_complement">{l s='Complemento: ' mod='gerencianet'}</label>
                                  </div>
                                  <div class="gn-col-7">
                                    <input type="text" name="gn_card_complement" id="gn_card_complement" value="{$billing_complement}" class="form-control" maxlength="54" />
                                  </div>
                                </div>
                            </div>
                        </div>

                        <div id="gn_card_city_zipcode_row" class="required billing-address-data gn-card-field gn-osc-row" >
                            <div class="gn-col-2">
                                <label class="gn-col-12 gn-label gn-right-padding-1" for="gn_card_zipcode">{l s='CEP: ' mod='gerencianet'}</label>
                            </div>
                            <div class="gn-col-10">
                                <div class="gn-col-4 required">
                                    <input type="text" name="gn_card_zipcode" id="gn_card_zipcode" value="{$billing_postcode}" class="form-control" />
                                </div>
                                <div class="gn-col-8">
                                    <div class=" required gn-left-space-2">
                                      <div class="gn-col-4">
                                          <label class="gn-col-12 gn-label gn-right-padding-1" for="gn_card_city">{l s='Cidade: ' mod='gerencianet'}</label>
                                      </div>
                                      <div class="gn-col-6">
                                        <input type="text" name="gn_card_city" id="gn_card_city" value="{$billing_city}" class="form-control" />
                                      </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="gn_card_state_row" class="required billing-address-data gn-card-field gn-osc-row" >
                          <div class="gn-col-2">
                            <label class="gn-col-12 gn-label gn-right-padding-1" for="gn_card_state">{l s='Estado: ' mod='gerencianet'}</label>
                          </div>
                          <div class="gn-col-10">
                            <select name="gn_card_state" id="gn_card_state" class="form-control gn-form-select">
                              <option value=""></option> 
                              <option value="AC" {if $billing_state=="AC" || $billing_state=="Acre"} selected {/if}>Acre</option> 
                              <option value="AL" {if $billing_state=="AL" || $billing_state=="Alagoas"} selected {/if}>Alagoas</option> 
                              <option value="AP" {if $billing_state=="AP" || $billing_state=="Amapá"} selected {/if}>Amapá</option> 
                              <option value="AM" {if $billing_state=="AM" || $billing_state=="Amazonas"} selected {/if}>Amazonas</option> 
                              <option value="BA" {if $billing_state=="BA" || $billing_state=="Bahia"} selected {/if}>Bahia</option> 
                              <option value="CE" {if $billing_state=="CE" || $billing_state=="Ceará"} selected {/if}>Ceará</option> 
                              <option value="DF" {if $billing_state=="DF" || $billing_state=="Distrito Federal"} selected {/if}>Distrito Federal</option> 
                              <option value="ES" {if $billing_state=="ES" || $billing_state=="Espírito Santo"} selected {/if}>Espírito Santo</option> 
                              <option value="GO" {if $billing_state=="GO" || $billing_state=="Goiás"} selected {/if}>Goiás</option> 
                              <option value="MA" {if $billing_state=="MA" || $billing_state=="Maranhão"} selected {/if}>Maranhão</option> 
                              <option value="MT" {if $billing_state=="MT" || $billing_state=="Mato Grosso"} selected {/if}>Mato Grosso</option> 
                              <option value="MS" {if $billing_state=="MS" || $billing_state=="Mato Grosso do Sul"} selected {/if}>Mato Grosso do Sul</option> 
                              <option value="MG" {if $billing_state=="MG" || $billing_state=="Minas Gerais"} selected {/if}>Minas Gerais</option> 
                              <option value="PA" {if $billing_state=="PA" || $billing_state=="Pará"} selected {/if}>Pará</option> 
                              <option value="PB" {if $billing_state=="PB" || $billing_state=="Paraíba"} selected {/if}>Paraíba</option> 
                              <option value="PR" {if $billing_state=="PR" || $billing_state=="Paraná"} selected {/if}>Paraná</option> 
                              <option value="PE" {if $billing_state=="PE" || $billing_state=="Pernambuco"} selected {/if}>Pernambuco</option> 
                              <option value="PI" {if $billing_state=="PI" || $billing_state=="Piauí"} selected {/if}>Piauí</option> 
                              <option value="RJ" {if $billing_state=="RJ" || $billing_state=="Rio de Janeiro"} selected {/if}>Rio de Janeiro</option> 
                              <option value="RN" {if $billing_state=="RN" || $billing_state=="Rio Grande do Norte"} selected {/if}>Rio Grande do Norte</option> 
                              <option value="RS" {if $billing_state=="RS" || $billing_state=="Rio Grande do Sul"} selected {/if}>Rio Grande do Sul</option> 
                              <option value="RO" {if $billing_state=="RO" || $billing_state=="Rondônia"} selected {/if}>Rondônia</option> 
                              <option value="RR" {if $billing_state=="RR" || $billing_state=="Roraima"} selected {/if}>Roraima</option> 
                              <option value="SC" {if $billing_state=="SC" || $billing_state=="Santa Catarina"} selected {/if}>Santa Catarina</option> 
                              <option value="SP" {if $billing_state=="SP" || $billing_state=="São Paulo"} selected {/if}>São Paulo</option> 
                              <option value="SE" {if $billing_state=="SE" || $billing_state=="Sergipe"} selected {/if}>Sergipe</option> 
                              <option value="TO" {if $billing_state=="TO" || $billing_state=="Tocantins"} selected {/if}>Tocantins</option> 
                            </select>
                          </div>
                        </div>
                    </div>
                    <div class="clear"></div>

                    <div class="gn-section" style="background-color: #F0F0F0; padding: 10px;">
                        <div class="required gn-osc-row">
                            <div>
                            <label class="" for="gn_card_brand">{l s='Selecione a bandeira do cartão' mod='gerencianet'}</label>
                            </div>
                            <div>
                                <div class="gn-card-brand-selector">
                                    <input id="none" type="radio" name="gn_card_brand" id="gn_card_brand" value="" checked class="gn-hide" />
                                    <div class="pull-left gn-card-brand-content">
                                        <input id="visa" type="radio" name="gn_card_brand" id="gn_card_brand" value="visa" class="gn-hide" />
                                        <label class="gn-card-brand gn-visa" for="visa" id="brand-visa" name="brand-visa"></label>
                                    </div>
                                    <div class="pull-left gn-card-brand-content">
                                        <input id="mastercard" type="radio" name="gn_card_brand" id="gn_card_brand" value="mastercard" class="gn-hide" />
                                        <label class="gn-card-brand gn-mastercard" for="mastercard" id="brand-mastercard" name="brand-mastercard"></label>
                                    </div>
                                    <div class="pull-left gn-card-brand-content">
                                        <input id="amex" type="radio" name="gn_card_brand" id="gn_card_brand" value="amex" class="gn-hide" />
                                        <label class="gn-card-brand gn-amex" for="amex" id="brand-amex" name="brand-amex"></label>
                                    </div>
                                    <div class="pull-left gn-card-brand-content">
                                        <input id="diners" type="radio" name="gn_card_brand" id="gn_card_brand" value="diners" class="gn-hide" />
                                        <label class="gn-card-brand gn-diners" for="diners" id="brand-diners" name="brand-diners"></label>
                                    </div>
                                    <div class="pull-left gn-card-brand-content">
                                        <input id="discover" type="radio" name="gn_card_brand" id="gn_card_brand" value="discover" class="gn-hide" />
                                        <label class="gn-card-brand gn-discover" for="discover" id="brand-discover" name="brand-discover"></label>
                                    </div>
                                    <div class="pull-left gn-card-brand-content">
                                        <input id="jcb" type="radio" name="gn_card_brand" id="gn_card_brand" value="jcb" class="gn-hide" />
                                        <label class="gn-card-brand gn-jcb" for="jcb" id="brand-jcb" name="brand-jcb"></label>
                                    </div>
                                    <div class="pull-left gn-card-brand-content">
                                        <input id="elo" type="radio" name="gn_card_brand" id="gn_card_brand" value="elo" class="gn-hide" />
                                        <label class="gn-card-brand gn-elo" for="elo" id="brand-elo" name="brand-elo"></label>
                                    </div>
                                    <div class="pull-left gn-card-brand-content">
                                        <input id="aura" type="radio" name="gn_card_brand" id="gn_card_brand" value="aura" class="gn-hide" />
                                        <label class="gn-card-brand gn-aura" for="aura"  id="brand-aura" name="brand-aura"></label>
                                    </div>
                                    <div class="clear"></div>
                                </div>
                            </div>
                        </div>

                        <div class="gn-osc-row required">
                                <div class="gn-col-6">
                                    <div>
                                        {l s='Número do cartão: ' mod='gerencianet'}
                                    </div>
                                    <div>
                                        <div class="gn-card-number-input-row" style="margin-right: 20px;">
                                            <input type="text" name="gn_card_number_card" id="gn_card_number_card" value="" class="form-control gn-input-card-number" />
                                        </div>
                                        <div class="clear"></div>
                                    </div>
                                </div>
                                
                                <div class="gn-col-6">
                                    <div>
                                        {l s='Código de Segurança: ' mod='gerencianet'}
                                    </div>
                                    <div>
                                        <div class="pull-left gn-cvv-row">
                                            <input type="text" name="gn_card_cvv" id="gn_card_cvv" value="" class="form-control gn-cvv-input" />
                                        </div>
                                        <div class="pull-left">
                                            <div class="gn-cvv-info">
                                                <div class="pull-left gn-icon-card-input">
                                                </div>
                                                <div class="pull-left" style="width:110px;">
                                                    {l s='São os três últimos dígitos no verso do cartão.' mod='gerencianet'}
                                                </div>
                                                <div class="clear"></div>
                                            </div>
                                        </div>
                                        <div class="clear"></div>
                                    </div>
                                </div>
                                <div class="clear"></div>
                                <input type="hidden" name="gn_card_payment_token" id="gn_card_payment_token" value="" />
                        </div>

                        <div class="gn-osc-row">
                            <div class="gn-col-12" sytle="overflow: auto;">
                                <div>   
                                    Validade:
                                </div>
                                <div class="gn-card-expiration-row">
                                    <div class="pull-left">
                                      <select class="form-control gn-card-expiration-select" name="gn_card_expiration_month" id="gn_card_expiration_month" >
                                          <option value=""> MM </option>
                                          <option value="01"> 01 </option>
                                          <option value="02"> 02 </option>
                                          <option value="03"> 03 </option>
                                          <option value="04"> 04 </option>
                                          <option value="05"> 05 </option>
                                          <option value="06"> 06 </option>
                                          <option value="07"> 07 </option>
                                          <option value="08"> 08 </option>
                                          <option value="09"> 09 </option>
                                          <option value="10"> 10 </option>
                                          <option value="11"> 11 </option>
                                          <option value="12"> 12 </option>
                                      </select>
                                    </div>
                                    <div class="gn-card-expiration-divisor pull-left">
                                        /
                                    </div>
                                    <div class="pull-left">
                                      <select class="form-control gn-card-expiration-select" name="gn_card_expiration_year" id="gn_card_expiration_year" >
                                          <option value=""> YYYY </option>
                                          {$list_years}
                                      </select>
                                    </div>
                                    <div></div>
                                    <div class="clear"></div>
                                </div>
                            </div>
                        </div>

                        <div class="gn-osc-row required">
                            <div class="gn-col-12">
                                <label class="" for="gn_card_installments">{l s='Quantidade de parcelas: ' mod='gerencianet'}</label>
                            </div>
                            <div class="gn-col-12" id="gn_card_installments_row">
                                <select name="gn_card_installments" id="gn_card_installments" class="form-control gn-form-select">
                                    <option value="">{l s='Selecione a bandeira do cartão' mod='gerencianet'}</option> 
                                </select>
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>
              </div>

            </div>
            <div class="gn-osc-row" style="padding: 5px 20px;">
                <div class="gn-osc-row" style="border: 1px solid #DEDEDE; margin: 0px; padding:5px;">
                    <div style="float: left;">
                        <strong>TOTAL:</strong>
                    </div>
                    <div style="float: right;">
                        <strong>{$order_total_card_formatted}</strong>
                    </div>
                </div>
            </div>
            <div class="gn-osc-row">
                <div style="float: right;">
                    <button id="gn-pay-card-button" class="gn-osc-button">Pagar com Cartão de Crédito</button>
                </div>
                <div class="pull-right gn-loading-request">
                    <div class="gn-loading-request-row">
                      <div class="pull-left gn-loading-request-text">
                        Autorizando, aguarde...
                      </div>
                      <div class="pull-left gn-icons">
                        <div class="spin gn-loading-request-spin-box icon-gerencianet"><div class="gn-icon-spinner6 gn-loading-request-spin-icon"></div></div>
                      </div>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
            </form>
        </div>
        

      </div>
     

</div>

  </div>
</p>
 {/if}

<link rel="stylesheet" type="text/css" media="screen" href="{$this_path_bw}assets/css/checkout-osc.css" />

<script src="{$this_path_bw}assets/js/jquery.maskedinput.js"></script>
<script src="{$this_path_bw}assets/js/checkout-osc.js"></script>

{/if}