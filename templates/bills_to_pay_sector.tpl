<!-- BEGIN BLOCK_PAGE -->
<!-- <div>
	<p class="setor-1 no-margin">Contas a pagar / <span class="setor-2 no-padding">Setor</span></p>
</div> -->

<!-- BEGIN EXTRA_BLOCK_POPUP_BILLSTOPAY_NEWSECTOR -->
<form method="post" id="frm_billstopaysector" class="flex-responsive">
	<div class="flex gap-10">
		<div class="fill">
			<label class="caption">Descrição</label>
			<div class="addon">
				<input 
					type="text" 
					id="contasapagarsetor" 
					class="smart-search fill uppercase"
					maxlength="50" 
					required 
					placeholder="" 
					title="Novo setor para contas a pagar."
					autocomplete="off" 
					autofocus>
			</div>
		</div>

		<div class="flex flex-ai-fe">
			<button type="submit" class="button-blue fill" id="submit">Adicionar</button>
		</div>
	</div>
</form>
<!-- END EXTRA_BLOCK_POPUP_BILLSTOPAY_NEWSECTOR -->

<div class="box-header box-container gap-10">
	<i class="icon fa-solid fa-sack-dollar"></i>
	<span>Financeiro / Contas a Pagar / Setor</span>
</div>

<div class="box-container flex flex-dc gap-10">
	<div class="box-header">
		Setores
	</div>

	<div class="w-billstopaysector-container flex flex-dc table tbody">

		{extra_block_billstopay}

		<!-- BEGIN EXTRA_BLOCK_BILLSTOPAY_NONE -->
		<div class="billstopaysector_not_found window">

			<div class="font-size-12 textcenter" style="padding: 80px 10px;">
				Não há setor de cadastrado para contas a pagar.
			</div>
		</div>
		<!-- END EXTRA_BLOCK_BILLSTOPAY_NONE -->

		<!-- BEGIN EXTRA_BLOCK_BILLSTOPAY -->
		<div class="w-billstopaysector tr window flex-responsive" data-id_contasapagarsetor="{id_contasapagarsetor}">

			<div class="flex gap-10 flex-6">
				
				<div class="flex-5">
					<label class="caption">Descrição</label>
					<div class="addon">
						<!-- BEGIN BLOCK_CONTASAPAGARSETOR -->
						<button class="billstopaysector_bt_contasapagarsetor button-field textleft fill" title="Editar descrição da lista de compra">
							{contasapagarsetor}
						</button>
						<!-- END BLOCK_CONTASAPAGARSETOR -->
						<!-- BEGIN EXTRA_BLOCK_CONTASAPAGARSETOR_FORM -->
						<form method="post" id="frm_billstopaysector_contasapagarsetor" class="fill">
							<input 
								type="text" 
								id="contasapagarsetor" 
								class="fill"
								placeholder="" 
								required 
								maxlength="50" 
								value="{contasapagarsetor}" 
								autocomplete="off"
								autofocus>
						</form>	
						<!-- END EXTRA_BLOCK_CONTASAPAGARSETOR_FORM -->
					</div>
				</div>	
				
				<div class="flex flex-ai-fe gap-10">
					<button class="billstopaysector_bt_delete button-icon button-red fa-solid fa-trash-can" title="Remove o setor"></button>
				</div>
			</div>

			<div class="flex-10"></div>
		</div>
		<!-- END EXTRA_BLOCK_BILLSTOPAY -->
	</div>

	<div class="billstopaysector_bt_show_new padding-t10 flex flex-jc-fe">
		<button type="button" class="button-blue">Novo setor</button>
	</div>
</div>
<!-- END BLOCK_PAGE -->