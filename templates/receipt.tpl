<!-- BEGIN BLOCK_PAGE -->

<!-- BEGIN EXTRA_BLOCK_POPUP_NEWRECEIPT -->
<form method="post" id='frm_receipt' class="flex flex-dc gap-10">

	<div class="flex gap-10">

		<div class="flex-1">
			<label class="caption flex flex-ai-center gap-5">
				<i class="fa-solid fa-magnifying-glass"></i>
				Código / Nome / Telefone
			</label>

			<div class="addon">

				<div class="autocomplete-dropdown flex-1">
					<input
						type="text"
						class="uppercase entity_search smart_search smart-search fill"
						data-focus_next="#data"
						data-source="popup"
						maxlength="40"
						value="{nome}"
						sku="{id_entidade}"
						placeholder=""
						autocomplete="off"
						autofocus
						required>

					{block_entity_autocomplete_search}
				</div>
			</div>
		</div>
	</div>

	<div class="flex gap-10">
		<div class="flex-2">
			<label class="caption">Data</label>
			<input type="date" id="frm_receipt_data" class="fill" value="{date}" required>
		</div>

		<div class="flex-2">
			<label class="caption">Valor</label>
			<div class="addon">
				<span>R$</span>
				<input
					type='number'
					class="fill"
					id="frm_receipt_valor"
					placeholder='0,00'
					min="0.01"
					max="10000.00"
					step="0.01"
					required>
			</div>
		</div>
	</div>

	<div class="flex gap-10">
		<div class="fill">
			<label class="caption">Motivo</label>
			<input
				type="text"
				id="frm_receipt_motivo"
				class="fill"
				placeholder=""
				autocomplete="off"
				maxlength="255"
				required>
		</div>

		<div class="flex flex-ai-fe">
			<button type="submit" class="button-blue fill" title="Adicionar recibo">Adicionar</button>
		</div>
	</div>
</form>
<!-- END EXTRA_BLOCK_POPUP_NEWRECEIPT -->

<div class="box-header box-container gap-10">
	<i class="icon fa-solid fa-sack-dollar"></i>
	<span>Financeiro / Emissão de Recibos</span>
</div>

<div class="box-container flex flex-dc gap-10">
	<div class="w-receipt-container flex flex-dc gap-10">

		<div class="box-header">Recibos</div>

		<div class="table tbody flex flex-dc">

			{extra_block_receipt}

			<!-- BEGIN EXTRA_BLOCK_RECEIPT_NONE -->
			<div class="receipt_not_found" style="padding: 40px 10px;">

				<div class="font-size-12 textcenter">
					Nenhum recibo cadastrado.
				</div>
			</div>
			<!-- END EXTRA_BLOCK_RECEIPT_NONE -->

			<!-- BEGIN EXTRA_BLOCK_RECEIPT -->
			<div class="w-receipt window tr flex flex-dc gap-10" data-id_entidade='{id_entidade}'>

				<div class="flex-responsive gap-10">
					<div class="flex-6">
						<label class="caption">Cliente / Colaborador</label>
						<div class="addon">
							{extra_block_entity_button_status}
							<span class="entity_{id_entidade}_nome fill">{nome}</span>
						</div>
					</div>

					<div class="flex gap-10 flex-4">
						<div class="flex-2">
							<label class="caption">Data</label>
							<div class="addon">
								<span class="field fill">{data_formatted}</span>
							</div>
						</div>

						<div class="flex-2">
							<label class="caption">Valor</label>
							<div class="addon">
								<span>R$</span>
								<span class="field fill">{valor_formatted}</span>
							</div>
						</div>
					</div>

					<div class="flex gap-10 flex-7">
						<div class="flex-5">
							<label class="caption">Motivo</label>
							<div class="addon">
								<span class="field fill">{motivo}</span>
							</div>
						</div>

						<div class="flex flex-ai-fe">
							<button class='receipt_bt_delete button-icon button-red fa-solid fa-trash-can' data-id_recibo='{id_recibo}' title="Remover o recibo"></button>
						</div>
					</div>
				</div>
			</div>
			<!-- END EXTRA_BLOCK_RECEIPT -->
		</div>

	</div>

	<div class="padding-t10 flex flex-jc-fe gap-10">

		<button type="button" class="receipt_bt_print button-blue {receipt_bt_print_visibility} flex flex-ai-center gap-5 color-blue" title="Gera página de impressão dos recibos">
			<i class="icon fa-solid fa-print"></i>
			<span>Imprimir</span>
		</button>

		<button type="button" class="receipt_bt_clear button-red {receipt_bt_clear_visibility} flex flex-ai-center gap-5" title="Remove todos os recibos">
			<li class="icon fa-solid fa-trash-can"></li>
			<span>Limpar</span>
		</button>

		<button type="button" class="receipt_bt_new button-blue">Novo Recibo</button>
	</div>
</div>

<!-- <div class="footer-popup flex-jc-fe">
	<div class="popup-menu">
		<button class="popup-main-button fa-solid fa-ellipsis-vertical button-pink"></button>

		<ul>
			<li class="entity_bt_new flex flex-ai-center gap-10" title="Cadastrar novo cliente">

				<span>Novo cliente</span>
			</li>

			<li class="receipt_bt_print flex flex-ai-center gap-10 {receipt_bt_print_visibility}" title="Gera página de impressão dos recibos">

				<span>Imprimir</span>
			</li>

			<li class="receipt_bt_clear flex flex-ai-center gap-10 color-red {receipt_bt_clear_visibility}" title="Remover todos os recibos">
				<i class="icon fa-solid fa-trash-can"></i>
				<span>Remover recibos</span>
			</li>
		</ul>
	</div>
</div> -->
<!-- END BLOCK_PAGE -->