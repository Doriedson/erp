<!-- BEGIN BLOCK_PAGE -->

<!-- BEGIN EXTRA_BLOCK_POPUP_SALECASHTYPE_NEW -->
<form method="post" id="frm_salecashtype" class="flex gap-10">
	<div class="fill">
		<label class="caption">Descrição</label>
		<div>
			<input
				type="text"
				id="especie"
				maxlength="50"
				required
				placeholder=""
				autocomplete="off"
				class="fill"
				autofocus>
		</div>
	</div>

	<div class="flex flex-ai-fe">
		<button type="submit" class="button-blue fill">Cadastrar</button>
	</div>
</form>
<!-- END EXTRA_BLOCK_POPUP_SALECASHTYPE_NEW -->

<div class="flex flex-dc gap-10">

	<div class="salecashtype_table flex flex-dc table tbody">
		{extra_block_salecashtype}

		<!-- BEGIN EXTRA_BLOCK_SALECASHTYPE -->
		<div class="w-salecashtype window tr flex flex-dc gap-10" data-id_especie='{id_especie}'>

			<div class="flex gap-10">

				<div class="flex-1">
					<label class="toggle" title="Visível no PDV">
						<label class="caption">PDV</label>
						<div class="addon-transp">
							<input {ativo} class="salecashtype_bt_ativo hidden" type="checkbox" data-id_especie="{id_especie}">
							<span></span>
						</div>
					</label>
				</div>

				<div class="flex-15">
					<label class="caption">Descrição</label>
					<div class="addon">
						<!-- BEGIN BLOCK_CASHTYPE -->
						<button class="salecashtype_bt_especie button-field textleft fill" data-id_especie="{id_especie}" title="Alterar descrição do setor">
							{especie}

						</button>
						<!-- END BLOCK_CASHTYPE -->

						<!-- BEGIN EXTRA_BLOCK_CASHTYPE_FORM -->
						<form method="post" id="frm_salecashtype_especie" class="flex fill" data-id_especie='{id_especie}'>
							<input
								type='text'
								id='especie'
								class="fill"
								placeholder=''
								value='{especie}'
								maxlength='50'
								required
								autofocus>
						</form>
						<!-- END EXTRA_BLOCK_CASHTYPE_FORM -->
					</div>
				</div>

				<div class="flex flex-ai-fe">
					<button class='salecashtype_bt_del button-icon button-red fa-solid fa-trash-can' title="Remover espécie"></button>
				</div>
			</div>
		</div>
		<!-- END EXTRA_BLOCK_SALECASHTYPE -->
	</div>

	<div class="flex flex-jc-fe padding-t10">
		<button type="button" class="salecashtype_bt_show_new button-blue">Cadastrar espécie</button>
	</div>
</div>
<!-- END BLOCK_PAGE -->