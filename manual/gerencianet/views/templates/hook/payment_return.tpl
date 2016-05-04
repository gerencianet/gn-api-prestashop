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

{if $status == 'ok'}

<link rel="stylesheet" type="text/css" href="{$this_path}assets/css/checkout.css" />
<p class="alert alert-success">{l s='Seu Pedido em %s está completo.' sprintf=$shop_name mod='gerencianet'}</p>
<p>
    {if !isset($reference)}
        {l s='Seu pedido #%d foi realizado com sucesso e enviamos um e-mail contendo todas informações sobre o pedido para você.' sprintf=$id_order mod='gerencianet'}
    {else}
        {l s='Seu pedido %s foi realizado com sucesso e enviamos um e-mail contendo todas informações sobre o pedido para você.' sprintf=$reference mod='gerencianet'}
    {/if}
    <br /><br /><strong>{l s='O seu pedido será enviado assim que nós recebermos o pagamento.' mod='gerencianet'}</strong>
    <br /><br />{l s='Se você tiver perguntas, comentários ou dúvidas, por favor, entre em contato com a nossa ' mod='gerencianet'} <a href="{$link->getPageLink('contact', true)|escape:'html'}">{l s='equipe de ajuda ao cliente' mod='gerencianet'}</a>.

</p>
<div>
        <div class="gn-success-payment">
            <div class="gn-row gn-box-emission">
                <div class="pull-left gn-left-space-2">
                    <img src="{$this_path}assets/images/gerencianet-configurations.png" alt="Gerencianet" title="Gerencianet" />
                </div>
                <div class="pull-left gn-title-emission">
                    {if $charge_type == 'billet'}
                        Boleto emitido através da Gerencianet
                    {else}
                        Seu pedido foi realizado com sucesso e seu pagamento está sendo processado. Aguarde até receber a confirmação do pagamento por e-mail.
                    {/if}
                </div>
                <div class="clear"></div>
            </div>

            <div class="gn-success-payment-inside-box">
                <div class="gn-row">
                    <div class="gn-col-1">
                      <div class="gn-icon-emission-success">
                          <span class="icon-check-circle-o"></span>
                      </div>
                    </div>

                    <div class="gn-col-10 gn-success-payment-billet-comments">
                        {if $charge_type == 'billet'}
                            O Boleto Bancário foi gerado com sucesso. Efetue o pagamento em qualquer banco conveniado, lotéricas, correios ou bankline. Fique atento à data de vencimento do boleto.
                        {else}
                            A cobrança em seu cartão está sendo processada. Assim que houver a confirmação, enviaremos um e-mail para o endereço <b>{$email}</b>, informado em seu cadastro. Caso não receba o produto ou serviço adquirido, você tem o prazo de <b>14 dias a partir da data de confirmação</b> do pagamento para abrir uma contestação.
                            Informe-se em <a href="http://www.gerencianet.com.br/contestacao" target="_blank">www.gerencianet.com.br/contestacao</a>.
                        {/if}
                        
                        <p>
                            Número da cobrança: <b>{$charge_id}</b>
                        </p>
                    </div>
                </div>

                {if $charge_type == 'billet'}
                <div class="gn-align-center">
                    <button id="showBillet" class="button btn btn-default standard-checkout button-medium" name="showBillet" onclick="window.open('{$billet}', '_blank');">
                        <div class="gn-success-payment-button-icon pull-left"><i class="icon-download"></i></div> 
                        <div class="pull-left gn-button-with-icon">Visualizar Boleto</div>
                        <div class="clear"></div>
                    </button>
                </div>
                {/if}
            </div>
          </div>
      </div>

{else}
	<p class="warning">
		{l s='We noticed a problem with your order. If you think this is an error, feel free to contact our' mod='gerencianet'} 
		<a href="{$link->getPageLink('contact', true)|escape:'html'}">{l s='expert customer support team' mod='gerencianet'}</a>.
	</p>
{/if}
