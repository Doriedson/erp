<!-- BEGIN BLOCK_PAGE -->

<!-- BEGIN EXTRA_BLOCK_POPUP_PRICETAG_PRINT -->
<div class="flex-responsive gap-10">
	<div class="flex gap-10">
		<div class="fill">
			<label class="caption">Produtos</label>
			<div class="addon">
				<select id="pricetag_option" class="fill">
					<option value="ALL">Todos</option>
					<option value="SALEOFF">Promoção</option>
					<option value="PRICE">Não promoção</option>
					<option value="ACTIVE">Ativos</option>
					<option value="INACTIVE">Inativos</option>
				</select>
			</div>
		</div>

		<div class="fill">
			<label class="caption">Modelo</label>
			<div class="addon">
				<select id="pricetag_model" class="fill">
					<option value="TAG">Etiqueta</option>
					<option value="SALEOFF">Promoção</option>
				</select>
			</div>
		</div>
	</div>
	<div class="flex flex-ai-fe flex-jc-fe">
		<button type="button" class="pricetag_bt_print button-blue">Imprimir</button>
	</div>
</div>
<!-- END EXTRA_BLOCK_POPUP_PRICETAG_PRINT -->

<div class="w-pricetag-container box-container flex flex-dc gap-10">

	<div class="flex gap-10">
		<div class="box-header gap-10 flex-1">
			<i class="icon fa-solid fa-boxes-stacked"></i>
			<span>Produto / Etiquetas</span>
		</div>
		<div class="flex flex-ai-fe">
			<button type="button" class="bt_load button-blue button-icon" data-page="product" title="Cadastro & Consulta de produtos">
				<i class="fa-solid fa-boxes-stacked"></i>
			</button>
		</div>
	</div>

	<div class='table tbody flex flex-dc'>

		{extra_block_pricetag}

		<!-- BEGIN EXTRA_BLOCK_PRICETAG_NONE -->
		<div class="pricetag_not_found padding-v20">
			Nenhum produto na lista.
		</div>
		<!-- END EXTRA_BLOCK_PRICETAG_NONE -->

		<!-- BEGIN EXTRA_BLOCK_PRICETAG -->
		<div class="w-pricetag window tr flex flex-dc gap-10" data-id_etiqueta='{id_etiqueta}'>

			<div class="flex-responsive gap-10">

				<div class="flex-11">
					<label class="caption">Produto</label>
					<div class="addon">
						{extra_block_product_button_status}
						<span class="uppercase">{produto}</span>
					</div>
				</div>

				<div class="flex gap-10 flex-7">
					{product_block_group_preco}

					<div class="flex flex-ai-fe flex-jc-fe">
						<button class='pricetag_bt_del button-icon button-red fa-solid fa-trash-can' title="Remover etiqueta"></button>
					</div>
				</div>
			</div>
		</div>
		<!-- END EXTRA_BLOCK_PRICETAG -->
	</div>

	<!-- <div class="section-header">
		Adicionar etiqueta
	</div> -->

	<div class="flex-responsive flex-jc-sb gap-10">
		<form method="post" class="frm_pricetag flex-responsive">
			<div class="flex gap-10">
				<div class="fill">
					<label class="caption">Produto [Código ou Descrição]</label>
					<div class="autocomplete-dropdown">
						<input
						type="text"
						class="uppercase product_search smart_search smart-search fill"
						data-source="popup"
						maxlength="50"
						required
						placeholder=""
						autocomplete="off"
						title="Digite o código ou o nome do produto"
						autofocus>

						{block_product_autocomplete_search}
					</div>
				</div>

				<div class="flex flex-ai-fe">
					<button type="submit" class="button-blue fill">Adicionar</button>
				</div>
			</div>
		</form>

		<div class="flex flex-ai-fe gap-10">
			<button type="button" class="flex-1 button-red pricetag_bt_clear flex flex-ai-center flex-jc-center gap-10 {pricetag_bt_clear_visibility}">
				<i class="icon fa-solid fa-trash-can"></i>
				<span>Limpar</span>
			</button>

			<button type="button" class="flex-1 button-blue pricetag_bt_show_print flex flex-ai-center flex-jc-center gap-10">
				<i class="icon fa-solid fa-print"></i>
				<span>Imprimir</span>
			</button>

		</div>
	</div>
</div>

<!-- <div class="footer-popup flex-jc-fe">
	<div class="popup-menu">
		<button class="popup-main-button fa-solid fa-ellipsis-vertical button-pink"></button>

		<ul>
			<li class="pricetag_bt_clear flex flex-ai-center gap-10 color-red {pricetag_bt_clear_visibility}">
				<i class="icon fa-solid fa-trash-can"></i>
				<span>Remover etiquetas</span>
			</li>

			<li class="pricetag_bt_show_print flex flex-ai-center gap-10 color-blue">
				<i class="icon fa-solid fa-print"></i>
				<span>Impressão</span>
			</li>
		</ul>
	</div>
</div> -->
<!-- END BLOCK_PAGE -->