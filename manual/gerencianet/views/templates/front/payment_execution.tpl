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
<!-- v0.1.2 -->
<style type="text/css" media="all"> 
	div#center_column{ width: {$width_center_column|escape}; }
	div#left_column{ display: none; }
</style>
<link rel="stylesheet" type="text/css" href="{$this_path_bw}assets/css/checkout.css" />


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
  {if !$card_option && $billet_option}
      {literal}
      justBillet();
      {/literal}
  {/if}
  {if $card_option && !$billet_option}
      {literal}
      justCard();
      {/literal}
  {/if}
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

{capture name=path}
	<a href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html':'UTF-8'}" title="{l s='Go back to the Checkout' mod='gerencianet'}">{l s='Checkout' mod='gerencianet'}</a><span class="navigation-pipe">{$navigationPipe}</span>{l s='Bank-wire payment' mod='gerencianet'}
{/capture}

<h2>{l s='Finalizar Pagamento' mod='gerencianet'}</h2>

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

{if $nbProducts <= 0}
	<p class="warning">{l s='Your shopping cart is empty.' mod='gerencianet'}</p>
{else}

<p>
	<img src="{$this_path_bw}assets/images/gerencianet-configurations.png" alt="{l s='Boleto ou Cartão de Crédito' mod='gerencianet'}" style="float:left; margin: 0px 10px 5px 0px;" />
	<h3>{l s='Este pagamento será processado pela Gerencianet' mod='gerencianet'}</h3>

</p>

{if $sandbox}
<div class="gn-warning" id="wc-gerencianet-messages-sandbox">
    O modo sandbox está ativo. Sua cobrança não será efetivada.
</div>
{/if}

<div class="gn-alert{if $order_total_card>=500 || $order_total_billet>500} gn-hide {/if}" id="wc-gerencianet-messages">
    {if $card_option && $order_total_card<500 && $billet_option && $order_total_billet<500}
        O valor mínimo para pagar através da gerencianet é de R$5,00
    {/if}
</div>

<div class="panel-group" id="accordion">
{if $billet_option & $order_total_billet>=500}
<div class="panel panel-default" id="billet-option" style="border: 1px solid #CCC; margin-bottom: 20px;">
    <div id="background-billet" name="background-billet" class="gn-accordion-option-background">
        <div class="gn-row-left panel-heading panel-gerencianet gn-icons">
            <div id="billet-radio-button" class="gn-left">
                <input type="radio" name="paymentMethodBilletRadio" id="paymentMethodBilletRadio" value="0" />
            </div>
            <div class="gn-left icon-gerencianet">
                <span class="gnicon-icones-personalizados_boleto"></span>
            </div>
            <div class="gn-left payment-option-gerencianet">
                Pagar com Boleto Bancário
            </div>
            <div class="clear"></div>
        </div>
        <div class="gn-row-right">
            <div>
                <div class="gn-left gn-price-payment-info">
                    {if $discount>0}
                    <center><span class="payment-old-price-gerencianet">{displayPrice price=$total}</span><br><span class="payment-discount-gerencianet"><b>Desconto de {$discountFormatted}%</b></span></center>
                    {/if}
                </div>
                <div class="gn-right gn-price-payment-selected total-gerencianet">
                    {$order_with_billet_discount}
                </div>
                <div class="clear"></div>
            </div>
        </div>
    </div>
    <div id="collapse-payment-billet"  class="panel-collapse gn-hide" style="border-top: 1px solid #CCC; background-color: #FFF;" >
    <div class="panel-body">

    	<form class="form-horizontal" id="billet-form" name="billet-form" action="{$link->getModuleLink('gerencianet', 'validation', [], true)|escape:'html'}" method="post">
    	<input name="gn_charge_id" id="gn_charge_id_billet" type="hidden" value=""/>
        <div class="gn-row ">
            <p class="gn-left-space-2"><strong>Optando pelo pagamento por Boleto, a confirmação será realizada no dia útil seguinte ao pagamento.</strong></p>
        </div>

  <div class="gn-form">
  <div id="billet-data">
 
    <div class="gn-row">
      <div class="gn-col-12 gn-cnpj-row">
      <input type="checkbox" name="pay_billet_with_cnpj" id="pay_billet_with_cnpj" value="1" /> Pagar como pessoa jurídica
      </div>
    </div>

    <div id="pay_cnpj" class="required gn-row gn-hide">
      <div class="gn-col-1 gn-label">
        <label for="input-payment-billet-cnpj">CNPJ: </label>
      </div>
      <div class="gn-col-11">
        
        <div>
          <div class="gn-col-3 required">
            <input type="text" name="cnpj" id="cnpj" class="form-control cnpj-mask" value="{$billing_cnpj}" />
          </div>
          <div class="gn-col-8">
            <div class="required">
              <label class="gn-col-4 gn-label" for="input-payment-corporate-name">Razão Social: </label>
              <div class="gn-col-8">
                <input type="text" name="corporate_name" id="corporate_name" class="form-control" value="{$billing_company}" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="required gn-row {if $billing_name} gn-hide  {/if}" >
      <div class="gn-col-1 gn-label">
        <label for="input-payment-billet-name">Nome: </label>
      </div>
      <div class="gn-col-11">
        <input type="text" name="first_name" id="first_name" value="{$billing_name}" class="form-control" />
      </div>
    </div>


    <div class=" required {if $billing_email} gn-hide {/if}" >
      <label class="gn-col-2 gn-label" for="input-payment-billet-email">E-mail: </label>
      <div class="gn-col-10">
        <input type="text" name="input-payment-billet-email" value="{$billing_email}" id="input-payment-billet-email" class="form-control" />
      </div>
    </div>

    <div class="required gn-row {if $billing_cpf && $billing_phone } gn-hide {/if}" >
      <div class="gn-col-1">
        <label class="gn-label" for="input-payment-billet-cpf">CPF: </label>
      </div>
      <div class="gn-col-11">
        
        <div>
          <div class="gn-col-3 required">
            <input type="text" name="cpf" id="cpf" value="{$billing_cpf}" class="form-control cpf-mask" />
          </div>
          <div class="gn-col-8">
            <div class=" required">
              <label class="gn-col-4 gn-label" for="input-payment-billet-phone">Telefone: </label>
              <div class="gn-col-4">
                <input type="text" name="phone_number" id="phone_number" value="{$billing_phone}" class="form-control phone-mask" />
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
  </div>
</div>


{/if}

{if $card_option & $order_total_card >= 500 }
<div id="card-option" style="border: 1px solid #CCC; margin-top: 0px; margin-bottom: 30px;">
    <div id="background-card" name="background-card" class="gn-accordion-option-background">
        <div class="gn-row-left panel-heading panel-gerencianet gn-icons">
            <div id="card-radio-button" class="gn-left">
                <input type="radio" name="paymentMethodCardRadio" id="paymentMethodCardRadio" value="0" />
            </div>
            <div class="gn-left icon-gerencianet">
                <span class="gnicon-credit-card2"></span>
            </div>
            <div class="gn-left payment-option-gerencianet">
                Pagar com Cartão de Crédito
            </div>
            <div class="clear"></div>
        </div>
        <div class="gn-row-right">
            <div>
                <div class="gn-left gn-price-payment-info">
                    <center><span class="payment-installments-gerencianet">Pague em até</span><br><span class="payment-discount-gerencianet">{$max_installments}</b></span></center>
                </div>
                <div class="gn-right gn-price-payment-selected total-gerencianet">
                    {displayPrice price=$total}
                </div>
                <div class="clear"></div>
            </div>
        </div>
    </div>
    <div id="collapse-payment-card"  class="panel-collapse gn-hide" style="border-top: 1px solid #CCC; background-color: #FFF;">
    <div class="panel-body">

<form class="form-horizontal" id="payment-card-form" action="{$link->getModuleLink('gerencianet', 'validation', [], true)|escape:'html'}" method="post">
<input type="hidden" name="currency_payement" value="{$currencies.0.id_currency}" />
<input name="gn_charge_id" id="gn_charge_id_card" type="hidden" value=""/>
    <div class="gn-row">
	   <p class="gn-left-space-2"><strong>Optando pelo pagamento com cartão de crédito, o pagamento é processado e a confirmação ocorrerá em até 48 horas.</strong></p>
    </div>

	<div class="gn-form">
    <div id="card-data" >
        <div class="gn-initial-section">

            <div class="gn-row">
              <div class="gn-col-12 gn-cnpj-row">
              <input type="checkbox" name="pay_card_with_cnpj" id="pay_card_with_cnpj" value="1" />  Pagar como pessoa jurídica
              </div>
            </div>

            <div id="pay_cnpj_card" class=" required gn-row gn-hide" >
              <label class="gn-col-2 gn-label" for="input-payment-card-cnpj">CNPJ: </label>
              <div class="gn-col-10">
                
                <div>
                  <div class="gn-col-3 required">
                    <input type="text" name="cnpj_card" id="cnpj_card" class="form-control cnpj-mask" value="{$billing_cnpj}" />
                  </div>
                  <div class="gn-col-9">
                    <div class=" required gn-left-space-2">
                      <label class="gn-col-4 gn-label" for="input-payment-corporate-name">Razão Social</label>
                      <div class="gn-col-8">
                        <input type="text" name="corporate_name_card" id="corporate_name_card" class="form-control" value="{$billing_company}" />
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class=" required gn-row {if $billing_name} gn-hide {/if}" >
              <div class="gn-col-2 gn-label">
                <label for="input-payment-card-name">Nome: </label>
              </div>
              <div class="gn-col-10">
                <input type="text" name="input-payment-card-name" id="input-payment-card-name" value="{$billing_name}" class="form-control" />
              </div>
            </div>

            <div class=" required gn-row {if $billing_cpf && $billing_phone && $billing_birthdate} gn-hide {/if}" >
            
                <div class="gn-col-2 gn-label">
                    <label for="input-payment-card-cpf">CPF: </label>
                </div>
                <div class="gn-col-2">
                    <input type="text" name="input-payment-card-cpf" id="input-payment-card-cpf" value="{$billing_cpf}" class="form-control cpf-mask gn-minimum-size-field" />
                </div>
                <div class="gn-col-8">
                <div class="gn-col-2 gn-label">
                    <label class=" gn-left-space-2" for="input-payment-card-phone">Telefone: </label>
                </div>
                <div class="gn-col-3">
                    <input type="text" name="input-payment-card-phone" value="{$billing_phone}" id="input-payment-card-phone" class="form-control phone-mask gn-minimum-size-field" />
                </div>
                
                <div class="gn-col-4 gn-label">
                    <label class=" gn-left-space-2" for="input-payment-card-birth">Data de Nascimento:</label>
                </div>
                <div class="gn-col-3">
                    <input type="text" name="input-payment-card-birth" id="input-payment-card-birth" value="{$billing_birthdate}" class="form-control birth-mask" />
                </div>
                </div>
            </div>

            <div class=" required {if $billing_email} gn-hide {/if}" >
              <label class="gn-col-2 gn-label" for="input-payment-card-email">E-mail: </label>
              <div class="gn-col-10">
                <input type="text" name="input-payment-card-email" value="{$billing_email}" id="input-payment-card-email" class="form-control" />
              </div>
            </div>
        </div>

        <div id="billing-adress" class="gn-section">
            <div class="gn-row {if $billing_address_1 && $billing_number && $billing_neighborhood && $billing_city && $billing_postcode && $billing_state} gn-hide {/if }">
                <p>
                <strong>Dados da Cobrança</strong>
                </p>
            </div>

            <div class="required gn-row {if $billing_address_1 && $billing_number} gn-hide {/if}" >
                <label class="gn-col-2 gn-label" for="input-payment-card-street">Endereço: </label>
                
                <div class="gn-col-10">
                    <div class="gn-col-6 required">
                        <input type="text" name="input-payment-card-address-street" id="input-payment-card-street" value="{$billing_address_1}" class="form-control" />
                    </div>
                    <div class="gn-col-6">
                        <div class=" required gn-left-space-2">
                            <label class="gn-col-5 gn-label" for="input-payment-card-address-number">Número: </label>
                            <div class="gn-col-7">
                                <input type="text" name="input-payment-card-address-number" id="input-payment-card-address-number" value="{$billing_number}" class="form-control" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="gn-row {if $billing_neighborhood} gn-hide {/if}">
                <div class="gn-col-2 required">
                    <label class="gn-col-12 gn-label required" for="input-payment-card-neighborhood">Bairro: </label>
                </div>
        
                <div class="gn-col-3">
                    
                    <input type="text" name="input-payment-card-neighborhood" id="input-payment-card-neighborhood" value="{$billing_neighborhood}" class="form-control" />
                </div>
                <div class="gn-col-7">
                    <div class=" gn-left-space-2">
                      <label class="gn-col-5 gn-label" for="input-payment-card-complement">Complemento: </label>
                      <div class="gn-col-7">
                        <input type="text" name="input-payment-card-complement" id="input-payment-card-complement" value="{$billing_address_2}" class="form-control" />
                      </div>
                    </div>
                </div>
            </div>

            <div class="required billing-address-data gn-row {if $billing_city && $billing_postcode} gn-hide {/if}" >
                <div class="gn-col-2">
                    <label class="gn-col-12 gn-label" for="input-payment-card-zipcode">CEP: </label>
                </div>
                <div class="gn-col-10">
                    <div class="gn-col-4 required">
                        
                        <input type="text" name="input-payment-card-zipcode" id="input-payment-card-zipcode" value="{$billing_postcode}" class="form-control" />
                    </div>
                    <div class="gn-col-8">
                        <div class=" required gn-left-space-2">
                          <label class="gn-col-4 gn-label" for="input-payment-card-city">Cidade: </label>
                          <div class="gn-col-6">
                            <input type="text" name="input-payment-card-city" id="input-payment-card-city" value="{$billing_city}" class="form-control" />
                          </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class=" required billing-address-data gn-row {if $billing_state} gn-hide {/if}" >
              <label class="gn-col-2 gn-label" for="input-payment-card-state">Estado: </label>
              <div class="gn-col-10">
                <select name="input-payment-card-state" id="input-payment-card-state" class="form-control gn-form-select">
                  <option value="">Selecione o estado</option> 
                  <option value="AC" {if $billing_state == "AC" || $billing_state == "Acre"} selected {/if}>Acre</option> 
                  <option value="AL" {if $billing_state == "AL" || $billing_state == "Alagoas"} selected {/if}>Alagoas</option> 
                  <option value="AP" {if $billing_state == "AP" || $billing_state == "Amapá"} selected {/if}>Amapá</option> 
                  <option value="AM" {if $billing_state == "AM" || $billing_state == "Amazonas"} selected {/if}>Amazonas</option> 
                  <option value="BA" {if $billing_state == "BA" || $billing_state == "Bahia"} selected {/if}>Bahia</option> 
                  <option value="CE" {if $billing_state == "CE" || $billing_state == "Ceará"} selected {/if}>Ceará</option> 
                  <option value="DF" {if $billing_state == "DF" || $billing_state == "Distrito Federal"} selected {/if}>Distrito Federal</option> 
                  <option value="ES" {if $billing_state == "ES" || $billing_state == "Espírito Santo"} selected {/if}>Espírito Santo</option> 
                  <option value="GO" {if $billing_state == "GO" || $billing_state == "Goiás"} selected {/if}>Goiás</option> 
                  <option value="MA" {if $billing_state == "MA" || $billing_state == "Maranhão"} selected {/if}>Maranhão</option> 
                  <option value="MT" {if $billing_state == "MT" || $billing_state == "Mato Grosso"} selected {/if}>Mato Grosso</option> 
                  <option value="MS" {if $billing_state == "MS" || $billing_state == "Mato Grosso do Sul"} selected {/if}>Mato Grosso do Sul</option> 
                  <option value="MG" {if $billing_state == "MG" || $billing_state == "Minas Gerais"} selected {/if}>Minas Gerais</option> 
                  <option value="PA" {if $billing_state == "PA" || $billing_state == "Pará"} selected {/if}>Pará</option> 
                  <option value="PB" {if $billing_state == "PB" || $billing_state == "Paraíba"} selected {/if}>Paraíba</option> 
                  <option value="PR" {if $billing_state == "PR" || $billing_state == "Paraná"} selected {/if}>Paraná</option> 
                  <option value="PE" {if $billing_state == "PE" || $billing_state == "Pernambuco"} selected {/if}>Pernambuco</option> 
                  <option value="PI" {if $billing_state == "PI" || $billing_state == "Piauí"} selected {/if}>Piauí</option> 
                  <option value="RJ" {if $billing_state == "RJ" || $billing_state == "Rio de Janeiro"} selected {/if}>Rio de Janeiro</option> 
                  <option value="RN" {if $billing_state == "RN" || $billing_state == "Rio Grande do Norte"} selected {/if}>Rio Grande do Norte</option> 
                  <option value="RS" {if $billing_state == "RS" || $billing_state == "Rio Grande do Sul"} selected {/if}>Rio Grande do Sul</option> 
                  <option value="RO" {if $billing_state == "RO" || $billing_state == "Rondônia"} selected {/if}>Rondônia</option> 
                  <option value="RR" {if $billing_state == "RR" || $billing_state == "Roraima"} selected {/if}>Roraima</option> 
                  <option value="SC" {if $billing_state == "SC" || $billing_state == "Santa Catarina"} selected {/if}>Santa Catarina</option> 
                  <option value="SP" {if $billing_state == "SP" || $billing_state == "São Paulo"} selected {/if}>São Paulo</option> 
                  <option value="SE" {if $billing_state == "SE" || $billing_state == "Sergipe"} selected {/if}>Sergipe</option> 
                  <option value="TO" {if $billing_state == "TO" || $billing_state == "Tocantins"} selected {/if}>Tocantins</option> 
                </select>
              </div>
            </div>
        </div>
        <div class="clear"></div>

        <div class="gn-section">
            <p><strong>Dados do Cartão</strong></p>

            <div class="required gn-row">
                <div>
                <label class="" for="input-payment-card-brand">Selecione a Bandeira do Cartão</label>
                </div>
                <div>
                    <div class="gn-card-brand-selector">
                        <input id="none" type="radio" name="input-payment-card-brand" id="input-payment-card-brand" value="" checked class="gn-hide" />
                        <div class="pull-left gn-card-brand-content">
                            <input id="visa" type="radio" name="input-payment-card-brand" id="input-payment-card-brand" value="visa" class="gn-hide" />
                            <label class="gn-card-brand gn-visa" for="visa" id="brand-visa" name="brand-visa"></label>
                        </div>
                        <div class="pull-left gn-card-brand-content">
                            <input id="mastercard" type="radio" name="input-payment-card-brand" id="input-payment-card-brand" value="mastercard" class="gn-hide" />
                            <label class="gn-card-brand gn-mastercard" for="mastercard" id="brand-mastercard" name="brand-mastercard"></label>
                        </div>
                        <div class="pull-left gn-card-brand-content">
                            <input id="amex" type="radio" name="input-payment-card-brand" id="input-payment-card-brand" value="amex" class="gn-hide" />
                            <label class="gn-card-brand gn-amex" for="amex" id="brand-amex" name="brand-amex"></label>
                        </div>
                        <div class="pull-left gn-card-brand-content">
                            <input id="diners" type="radio" name="input-payment-card-brand" id="input-payment-card-brand" value="diners" class="gn-hide" />
                            <label class="gn-card-brand gn-diners" for="diners" id="brand-diners" name="brand-diners"></label>
                        </div>
                        <div class="pull-left gn-card-brand-content">
                            <input id="elo" type="radio" name="input-payment-card-brand" id="input-payment-card-brand" value="elo" class="gn-hide" />
                            <label class="gn-card-brand gn-elo" for="elo" id="brand-elo" name="brand-elo"></label>
                        </div>
                        <div class="pull-left gn-card-brand-content">
                            <input id="hipercard" type="radio" name="input-payment-card-brand" id="input-payment-card-brand" value="hipercard" class="gn-hide" />
                            <label class="gn-card-brand gn-hipercard" for="hipercard" id="brand-hipercard" name="brand-hipercard"></label>
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>
            </div>

            <div class="gn-row required">
                    <div class="gn-col-5">
                        <div>
                            Digite o número do cartão: 
                        </div>
                        <div>
                            <div class="gn-card-number-input-row">
                                <input type="text" name="input-payment-card-number" id="input-payment-card-number" value="" class="form-control gn-input-card-number" />
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>
                    <div class="gn-col-3" sytle="overflow: auto;">
                        <div>   
                            Validade:
                        </div>
                        <div class="gn-card-expiration-row">
                            <div class="pull-left">
                              <select class="form-control gn-card-expiration-select" name="input-payment-card-expiration-month" id="input-payment-card-expiration-month" >
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
                              <select class="form-control gn-card-expiration-select" name="input-payment-card-expiration-year" id="input-payment-card-expiration-year" >
                                  <option value=""> YYYY </option>
                                  {$select_insert}
                              </select>
                            </div>
                            <div></div>
                            <div class="clear"></div>
                        </div>
                    </div>
                    <div class="gn-col-4">
                        <div>
                            Código de Segurança
                        </div>
                        <div>
                            <div class="pull-left gn-cvv-row">
                                <input type="text" name="input-payment-card-cvv" id="input-payment-card-cvv" value="" class="form-control gn-cvv-input" />
                            </div>
                            <div class="pull-left">
                                <div class="gn-cvv-info">
                                    <div class="pull-left gn-icon-card-input">
                                    </div>
                                    <div class="pull-left">
                                        São os três últimos<br>dígitos no verso do cartão.
                                    </div>
                                    <div class="clear"></div>
                                </div>
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>
                    <div class="clear"></div>
            </div>

            <div class="gn-row required">
                <div class="gn-col-12">
                    <label class="" for="input-payment-card-installments">Quantidade de Parcelas</label>
                </div>
                <div class="gn-col-12" id="select-card-installments">
                    <select name="input-payment-card-installments" id="input-payment-card-installments" class="form-control gn-form-select">
                        <option value="">Selecione</option> 
                    </select>
                </div>
                <div class="clear"></div>
            </div>
        </div>
  </div>

</div>
</form>

    </div>
  </div>
</div>
{/if}

</div>

<div class="checkout-footer">
    <div class="pull-left">
    	
            <a href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html'}" class="button-exclusive btn btn-default"><i class="icon-chevron-left"></i>{l s='Outros métodos de pagamento' mod='gerencianet'}</a>

    </div>
    <div class="pull-right">
        <div id="price-billet" name="price-billet" class="gn-hide">
            <p>
                <button type="submit" class="button btn btn-default button-medium" id="gn-pay-billet-button">
                <span>{l s='Pagar com Boleto Bancário | ' mod='gerencianet'}{$order_with_billet_discount}
                <i class="icon-chevron-right right"></i></span>
                </button>
            </p>
        </div>
        <div id="price-card" name="price-card" class="gn-hide">
            <p>
                <button type="submit" class="button btn btn-default button-medium" id="gn-pay-card-button">
                <span>{l s='Pagar com Cartão de Crédito | ' mod='gerencianet'}{displayPrice price=$total}
                  <i class="icon-chevron-right right"></i></span>
                </button>
            </p>
        </div>
        <div id="price-no-payment-selected" name="price-no-payment-selected">
            <p>
                <button class="button exclusive_large" id="gn-pay-no-selected" disabled="">Selecione um dos meios acima</button>
            </p>
        </div>
    </div>

    <div class="pull-right gn-loading-request">
          <div class="gn-loading-request-row">
            <div class="pull-left gn-loading-request-text">
              Carregando, aguarde...
            </div>
            <div class="pull-left gn-icons">
              <div class="spin gn-loading-request-spin-box icon-gerencianet"><div class="gnicon-spinner6 gn-loading-request-spin-icon"></div></div>
            </div>
          </div>
      </div>
    <div class="clear"></div>
</div>


{/if}
