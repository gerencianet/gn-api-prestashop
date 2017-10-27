{*
 * Alterações realizadas por Imotion-Info (https://www.imotion-info.com)
 *
 *}
<br />
<div class="col-lg-6">
	<div class="panel clearfix">
	{if $ps_version < '1.6'}
	<fieldset style="width: 400px; border-color:orange;">
		<legend>
			<img src="{$this_path_ssl}logo.gif" width="16" height="16"/> {l s='Boleto Gerencianet' mod='gerencianet'}
		</legend>
	{else}
		<h3><i class="icon-barcode"></i> {l s='Boleto Gerencianet' mod='gerencianet'}</h3>
	{/if}

        <br />
            <p align="center">
                <a href="{$boletoUrl}" title="{l s='Visualizar Boleto' mod='gerencianet'}" class="button btn btn-primary" target="_blank">{l s='Visualizar Boleto' mod='gerencianet'}</a>
            </p>

        </div>
	{if $ps_version < '1.6'}
	</fieldset>
	{/if}
</div>
