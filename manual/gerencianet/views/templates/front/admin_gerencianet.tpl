{*
* 2007-2011 PrestaShop
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
*  @version  Release: $Revision: 6594 $
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<link type="text/css" rel="stylesheet" href="{$module_dir|escape:'none'}assets/css/admin.css" />
<script>
if(!window.jQuery)
{
   var script = document.createElement('script');
   script.type = "text/javascript";
   script.src = "{$module_dir|escape:'none'}assets/js/jquery.min.js";
   document.getElementsByTagName('head')[0].appendChild(script);
}
</script>
<script type="text/javascript" charset="utf8" src="{$module_dir|escape:'none'}assets/js/jquery.mask.min.js"></script>

<div class="panel-body">
        <div class="row gn-admin-title-row">
          <div class="pull-left">
            <a target="_BLANK" href="https://www.gerencianet.com.br"><img src="{$module_dir|escape:'none'}assets/images/gerencianet-configurations.png" alt="Gerencianet" title="Gerencianet" /></a> 
          </div>
          <div class="pull-left gn-admin-title">
            <b>Módulo de Integração Gerencianet</b>
          </div>
        </div>
        <form action="{$action_post|escape:'none'}" method="POST" enctype="multipart/form-data" id="form-gn-std-uk" class="form-horizontal">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-welcome" data-toggle="tab">Bem-vindo</a></li>
            <li><a href="#tab-general" data-toggle="tab">Configurações Gerais</a></li>
            <li><a href="#tab-keys" data-toggle="tab">Credenciais</a></li>
          </ul>

          <div class="tab-content">
              <div class="tab-pane active" id="tab-welcome">
              {if $gerencianet_active == "no"}
              <div class="gn-warning">
                Seu módulo ainda não está ativo. Realize as <b>Configurações Gerais</b> e insira suas <b>Credenciais</b> para começar a receber os pagamentos.
              </div>
              {/if}

              Bem vindo ao Módulo de Pagamentos da Gerencianet para PrestaShop.<br>
              Com esse módulo, você poderá receber pagamentos com Boleto Bancário e Cartão de Crédito de forma fácil e rápida.<br>
              Se você ainda não possui uma conta Gerencianet, clique no link abaixo para abrir uma conta agora. É grátis.

              <div class="gn-buttons">
                <a href="http://www.gerencianet.com.br" target="_blank" class="btn btn-primary gn-admin-btn">Abrir Conta Gerencianet</a>
              </div>

              Caso você tenha alguma dúvida ou sugestão, entre em contato conosco.

              </div>
            <div class="tab-pane" id="tab-general">
              <div class="form-group">
                <label class="col-sm-3 control-label">Modo: </span></label>
                <div class="col-sm-9">
                  <div class="pull-left gn-admin-item-form">
                    <input type="radio" name="gerencianet_sandbox" value="1" {if $gerencianet_sandbox} checked {/if}> 
                  </div>
                  <div class="pull-left gn-admin-item-left">
                    Desenvolvimento (Testes)
                  </div>
                  <div class="pull-left gn-admin-item-form">
                    <input type="radio" name="gerencianet_sandbox" value="0" {if !$gerencianet_sandbox} checked {/if}> 
                  </div>
                  <div class="pull-left gn-admin-item-right">
                    Produção (Pra valer!)
                  </div>
                </div>
              </div>

              <div class="gn-divisor"></div>

              <div class="form-group">
              <label class="col-sm-3 control-label" for="entry-payment-options">Opções de Pagamento: </label>
                <div class="col-sm-9">
                  <div class="pull-left gn-admin-item-form">
                    <input type="checkbox" name="gerencianet_payment_option_billet" value="1" {if $gerencianet_payment_option_billet} checked {/if}>
                  </div>
                  <div class="pull-left gn-admin-item-left">
                    Boleto Bancário
                  </div>
                  <div class="pull-left gn-admin-item-form">
                    <input type="checkbox" name="gerencianet_payment_option_card" value="1" {if $gerencianet_payment_option_card} checked {/if}>
                  </div>
                  <div class="pull-left gn-admin-item-right">
                    Cartão de Crédito
                  </div>
                </div>
              </div>

              <div class="gn-divisor"></div>

              <div class="form-group">
              <div class="col-sm-3">
              <label class="control-label pull-right" for="gerencianet_checkout_type">Pagamento em um passo:<br>
              (esta funcionalidade pode não ser compatível com todas as lojas)</label>
              </div>
                <div class="col-sm-9">
                  <div class="pull-left gn-admin-item-form">
                    <input type="checkbox" name="gerencianet_checkout_type" value="1" {if $gerencianet_checkout_type=="1"} checked {/if}>
                  </div>
                  <div class="pull-left gn-admin-item-left">
                    Habilitar pagamento em um passo
                  </div>
                </div>
              </div>

              <div class="form-group gn-admin-detail-config">
                <div class="col-sm-3 control-label"></div>
                <div class="col-sm-9 gn-admin-detail-background">
                  <div class="form-group gn-admin-detail-content">
                    <label class="control-label" for="gerencianet_billet_days_to_expire">Dias para vencimento do Boleto: </label>
                    <div>
                      <input type="text" name="gerencianet_billet_days_to_expire" value="{$gerencianet_billet_days_to_expire}" id="gerencianet_billet_days_to_expire" class="form-control billet_expiration"/>
                    </div>
                  </div>
                  <div class="form-group gn-admin-detail-content">
                    <label class="control-label" for="gerencianet_discount_billet_value">Desconto para pagamento no boleto(%):</label>
                    <div>
                      <input type="text" name="gerencianet_discount_billet_value" value="{$gerencianet_discount_billet_value}" id="gerencianet_discount_billet_value" class="form-control percent"/>
                    </div>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-3 control-label" for="gerencianet_payment_notification_update">Atualizar status dos pedidos PrestaShop automaticamente: </label>
                <div class="col-sm-9">
                  <div class="pull-left gn-admin-item-form">
                    <input type="radio" name="gerencianet_payment_notification_update" value="1" {if $gerencianet_payment_notification_update} checked {/if}> 
                  </div>
                  <div class="pull-left gn-admin-item-left">
                    Sim
                  </div>
                  <div class="pull-left gn-admin-item-form">
                    <input type="radio" name="gerencianet_payment_notification_update" value="0" {if !$gerencianet_payment_notification_update} checked {/if}> 
                  </div>
                  <div class="pull-left gn-admin-item-right">
                    Não
                  </div>
                </div>
              </div>

              <div class="form-group gn-admin-detail-config">
                <div class="col-sm-3 control-label"></div>
                <div class="col-sm-9 gn-admin-detail-background">
                  <div class="form-group gn-admin-detail-content">
                    <label class="control-label" for="gn_entry_payment_notification_update_notify">Ao atualizar o status do pedido, deseja enviar e-mail automático da sua loja para notificar o cliente?</label>
                    <div>
                      <div class="col-sm-10 gn-admin.item-notification">
                        <div class="pull-left gn-admin-item-form">
                          <input type="radio" name="gerencianet_payment_notification_update_notify" value="1" {if $gerencianet_payment_notification_update_notify} checked {/if}> 
                        </div>
                        <div class="pull-left gn-admin-item-left">
                          Sim
                        </div>
                        <div class="pull-left gn-admin-item-form">
                          <input type="radio" name="gerencianet_payment_notification_update_notify" value="0" {if !$gerencianet_payment_notification_update_notify} checked {/if}> 
                        </div>
                        <div class="pull-left gn-admin-item-right">
                          Não
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-3 control-label" for="gerencianet_status">Status:</label>
                <div class="col-sm-9">
                  <div class="pull-left gn-admin-item-form">
                    <input type="radio" name="gerencianet_status" value="1" {if $gerencianet_status} checked {/if}> 
                  </div>
                  <div class="pull-left gn-admin-item-left">
                    Ativo
                  </div>
                  <div class="pull-left gn-admin-item-form">
                    <input type="radio" name="gerencianet_status" value="0" {if !$gerencianet_status} checked {/if}> 
                  </div>
                  <div class="pull-left gn-admin-item-right">
                    Inativo (Não aparecerá como opção de pagamento para o cliente)
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label class="col-sm-3 control-label" for="gerencianet_debug">Debug:</label>
                <div class="col-sm-9">
                  <div class="pull-left gn-admin-item-form">
                    <input type="radio" name="gerencianet_debug" value="1" {if $gerencianet_debug} checked {/if}> 
                  </div>
                  <div class="pull-left gn-admin-item-left">
                    Sim
                  </div>
                  <div class="pull-left gn-admin-item-form">
                    <input type="radio" name="gerencianet_debug" value="0" {if !$gerencianet_debug} checked {/if}> 
                  </div>
                  <div class="pull-left gn-admin-item-right">
                    Não
                  </div>
                </div>
              </div>
            </div>
              
            <div class="tab-pane" id="tab-keys">
              O par de chaves Client ID e Client Secret contém informações sigilosas que identificam sua aplicação e permitem a realização do pagamento. Para obter suas chaves você precisa entrar em sua conta Gerencianet, entrar no menu API e criar uma Nova Aplicação. Você terá acesso a dois pares de chaves: um de Desenvolvimento e outro de Produção.
              
              <div class="form-group required">
                <div class="gn-admin-keys-title">
                  <span><b>PRODUÇÃO</b></span> &nbsp; <a onclick="showGnTutorial('keysProduction');" class="gn-admin-cursor-pointer">Onde eu encontro as chaves de produção?</a>
                </div>
                <label class="col-sm-3 control-label" for="entry-client-id-production">Client ID</label>
                <div class="col-sm-9">
                  <input type="text" name="gerencianet_client_id_production" value="{$gerencianet_client_id_production}" id="gerencianet_client_id_production" class="form-control"/>
                </div>
              </div>

              <div class="form-group required" >
                <label class="col-sm-3 control-label" for="entry-client-secret-production">Client Secret</label>
                <div class="col-sm-9">
                  <input type="text" name="gerencianet_client_secret_production" value="{$gerencianet_client_secret_production}" id="gerencianet_client_secret_production" class="form-control"/>
                </div>
              </div>

              <div class="form-group required">
                <div class="gn-admin-keys-title">
                  <span><b>DESENVOLVIMENTO</b></span> &nbsp; <a onclick="showGnTutorial('keysDevelopment');" class="gn-admin-cursor-pointer">Onde eu encontro as chaves de desenvolvimento?</a>
                </div>
                <label class="col-sm-3 control-label" for="entry-client-id-development">Client ID</label>
                <div class="col-sm-9">
                  <input type="text" name="gerencianet_client_id_development" value="{$gerencianet_client_id_development}" id="gerencianet_client_id_development" class="form-control"/>
                </div>
              </div>

              <div class="form-group required">
                <label class="col-sm-3 control-label" for="entry-client-secret-development">Client Secret</label>
                <div class="col-sm-9">
                  <input type="text" name="gerencianet_client_secret_development" value="{$gerencianet_client_secret_development}" id="gerencianet_client_secret_development" class="form-control"/>
                </div>
              </div>

              <div class="gn-admin-keys-title">
                O identificador da conta é uma chave única utilizada para identificar uma determinada conta. Você pode encontrá-lo no link API, acima do menu de navegação lateral.
              </div>

              <div class="form-group required">
                <div class="col-sm-3 control-label">
                  <label for="entry-gerencianet-payee-code">Identificador da Conta</label><br>
                  <a onclick="showGnTutorial('payeeCode');" class="gn-admin-cursor-pointer">Onde encontrar?</a>
                </div>
                <div class="col-sm-9">
                  <input type="text" name="gerencianet_payee_code" id="gerencianet_payee_code" value="{$gerencianet_payee_code}" class="form-control"/>
                </div>
              </div>
            </div>
          </div>
        <div id="divSalvar">
            <input type="submit" class="btn btn-success gn-admin-btn" name='btnSubmit' value="Salvar alterações" />
        </div>

        </form>
      </div>

<div id="tutorialGnBox" class="gn-admin-tutorial-box">
  <div class="gn-admin-tutorial-row">
    <div class="gn-admin-tutorial-line">
      <div class="pull-right gn-admin-tutorial-close">
        Fechar <b>X</b>
      </div>
    </div>
    <img id="imgTutorial" src="{$module_dir|escape:'none'}assets/images/gerencianet-exemplo-chaves-desenvolvimento.png" />
  </div>
</div>

<script type="text/javascript">
{literal}
  $(document).ready(function() {
    $("#tutorialGnBox").click(function() {
      $("#tutorialGnBox").fadeOut();
    });

    $(".percent").mask("##0,00%", {reverse: true, onKeyPress: function(percentage){
        if (percentage.length>6) {
          $(".percent").val("99,99%");
        }
      }});
    $(".billet_expiration").mask("999");
  });

  function showGnTutorial(tutorial) {
    switch(tutorial) {
      case "keysDevelopment": $("#imgTutorial").attr("src","{/literal}{$module_dir|escape:'none'}{literal}assets/images/gerencianet-exemplo-chaves-desenvolvimento.png"); break;
      case "keysProduction": $("#imgTutorial").attr("src","{/literal}{$module_dir|escape:'none'}{literal}assets/images/gerencianet-exemplo-chaves-producao.png"); break;
      case "payeeCode": $("#imgTutorial").attr("src","{/literal}{$module_dir|escape:'none'}{literal}assets/images/gerencianet-exemplo-identificador-conta.png"); break;
    }
    $("#tutorialGnBox").fadeIn();
  }
{/literal}
</script>
