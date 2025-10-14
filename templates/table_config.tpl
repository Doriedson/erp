<!-- BEGIN BLOCK_PAGE -->

<!-- BEGIN EXTRA_BLOCK_POPUP_TABLECONFIG_NEW -->
<form method="post" id="frm_tableconfig" class="flex gap-10">
	<div class="flex-1">
		<label class="caption">Quantidade</label>
		<div>
			<input
				type="number"
				id="number_of_tables"
				class="fill"
				step='1'
				min='1'
				max='9999'
				maxlength="4"
				size="8"
				required
				placeholder=""
				autocomplete="off"
				autofocus>
		</div>
	</div>

	<div class="flex-1">
		<label class="caption">Iniciar ID em</label>
		<div>
			<input
				type="number"
				id="id_start"
				class="fill"
				step='1'
				min='0'
				max='9999'
				maxlength="4"
				required
				value='1'
				placeholder=""
				autocomplete="off"
				>
		</div>
	</div>

	<div class="flex flex-ai-fe">
		<button type="submit" class="button-blue fill">Adicionar</button>
	</div>
</form>
<!-- END EXTRA_BLOCK_POPUP_TABLECONFIG_NEW -->

<div class="flex flex-dc gap-10">

	<div class="tableconfig_notfound window {tableconfig_notfound} fill">
		<div class="font-size-12 textcenter" style="padding: 80px 10px;">
			Nenhuma mesa cadastrada.
		</div>
	</div>

	<div class="tableconfig_table flex flex-wrap gap-10">

		{extra_block_table}

		<!-- BEGIN EXTRA_BLOCK_TABLE -->
		<div class="w-tableconfig flex-4-col ticket-border gap-10 font-size-12 {status}" data-id_mesa="{id_mesa}" data-mesa="{mesa}">

			<div class="flex flex-dc flex-ai-center flex-jc-center gap-10 flex-1">
				<div class="fill">
					<label class="caption">Descrição</label>
					<div class="addon">
						<!-- BEGIN BLOCK_TABLE_MESA -->
						<button class="table_bt_mesa button-field textleft fill" title="Edita a descrição da mesa">
							{mesa}

						</button>
						<!-- END BLOCK_TABLE_MESA -->

						<!-- BEGIN EXTRA_BLOCK_TABLE_FORM_MESA -->
						<form method="post" id="frm_table_mesa" class="fill" data-id_mesa="{id_mesa}">
							<input
								type='text'
								id='mesa'
								class="fill"
								required
								placeholder=''
								value='{mesa}'
								maxlength='50'
								autofocus>
						</form>
						<!-- END EXTRA_BLOCK_TABLE_FORM_MESA -->
					</div>
				</div>
			</div>
			<div class="flex flex-ai-fe">
				<button class="table_bt_del button-icon button-red fa-solid fa-trash-can" title="Remove a mesa"></button>
			</div>
		</div>
		<!-- END EXTRA_BLOCK_TABLE -->
	</div>

	<div class="section-footer padding-t10 flex flex-jc-fe">
		<button type="button" class="tableconfig_bt_show_new button-blue">Adicionar mesa</button>
	</div>
</div>
<!-- END BLOCK_PAGE -->