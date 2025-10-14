<!-- BEGIN BLOCK_PAGE -->

<!-- BEGIN EXTRA_BLOCK_POPUP_REPORTSALEONEPRODUCT -->
<div class="flex flex-dc gap-10">

	<div>
		<label class="produto-tipo caption">
			{produtotipo}
		</label>

		<div class="addon">
			{extra_block_product_button_status}
			{block_product_produto}
		</div>
	</div>

	<div class="flex gap-10">
		<div class="flex-3">
			<label class="caption">
				Filtro
			</label>

			<div class="addon">
				<span class="w_reportsaleoneproduct_filter"></span>
			</div>
		</div>

		<div class="flex-2">
			<label class="caption">
				Total Vendido
			</label>

			<div class="addon">
				<span><span class="w_reportsaleoneproduct_total"></span> <span class="font-size-075">{produtounidade}</span></span>
			</div>
		</div>
	</div>

	<div class="w_reportsaleoneproduct_graph_container gap-10">
	</div>

	<div class="flex flex-ai-fe gap-10">
		<div class="section-header fill">Filtro de data para histórico</div>
	</div>

	<div class="flex-responsive gap-10 flex-jc-sb">

		<form method="post" id="frm_purchaseorder_reportsaleoneproduct" data-id_produto="{id_produto}" class="flex-1">

			<div class="flex flex-dc gap-10">

				<div class="flex gap-10">
					<div class="flex-1">
						<label class="caption">Data</label>
						<div class="addon">
							<input type='date' id='frm_purchaseorder_reportsaleoneproduct_dataini' class="select_dataini fill" value='{datestart}' title="Data ou data inicial." required>
						</div>
					</div>

					<div class="flex-1">
						<label class="caption">até</label>
						<div class="addon">
							<span class="flex">
								<input type="checkbox" id='frm_purchaseorder_reportsaleoneproduct_intervalo' {dateend_sel} title="Ativa busca de data de vencimento por intervalo.">
							</span>

							<input type='date' id='frm_purchaseorder_reportsaleoneproduct_datafim' class="select_datafim fill" min='{datestart}' value='{dateend}' title="Data final." required {dateend_disabled}>
						</div>
					</div>
				</div>

				<div class="flex flex-ai-center flex-jc-center gap-10">
					<div class="flex flex-ai-fe">
						<button type="submit" class="button-blue" title="Aplica filtro de data para histórico de venda do produto">Aplicar Filtro</button>					</div>

					<div class="flex flex-ai-fe">
						{extra_block_button_datelock}
						<!-- BEGIN EXTRA_BLOCK_BUTTON_DATEUNLOCK -->
						<button type="button" class="bt_purchaseorder_history_lock button_lock button-icon button-blue fa-solid fa-lock-open" title="Clique para travar/destravar datas para próximas pesquisas"></button>
						<!-- END EXTRA_BLOCK_BUTTON_DATEUNLOCK -->
						<!-- BEGIN EXTRA_BLOCK_BUTTON_DATELOCK -->
						<button type="button" class="bt_purchaseorder_history_lock button_lock button-icon button-blue fa-solid fa-lock" title="Clique para travar/destravar datas para próximas pesquisas"></button>
						<!-- END EXTRA_BLOCK_BUTTON_DATELOCK -->
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
<!-- END EXTRA_BLOCK_POPUP_REPORTSALEONEPRODUCT -->

<div class="box-container flex flex-dc gap-10">

	<div class="box-header gap-10">
		<i class="icon fa-solid fa-file-invoice"></i>
		<span>Relatório / Histórico de Venda do Produto</span>
	</div>

	<div class="flex-responsive gap-10 flex-jc-sb">

		<form method="post" id="frm_report_sale_one_product" class="flex-responsive">

			<div class="flex-responsive gap-10">

				<div class="flex gap-10">

					<div class="flex-1">
						<label class="caption flex flex-ai-center gap-10">
							<i class="fa-solid fa-magnifying-glass"></i>
							Produto [Código ou Descrição]
						</label>

						<div class="autocomplete-dropdown">
							<input
								type="text"
								id="product_search"
								class="uppercase product_search smart_search smart-search fill flex-4"
								data-source="popup"
								maxlength="40"
								required
								placeholder=""
								autocomplete="off"
								data-focus_next="#dataini"
								autofocus>

							{block_product_autocomplete_search}
						</div>
					</div>
				</div>

				<div>
					<label class="caption">Data</label>
					<div class="addon">
						<input type='date' id="dataini" class="fill" value='{data}' title="Data ou data inicial." required>
					</div>
				</div>

				<div class="flex gap-10">
					<div class="fill">
						<label class="caption">até</label>
						<div class="addon">
							<span class="flex">
								<input type="checkbox" id='intervalo' title="Ativa busca de data de vencimento por intervalo.">
							</span>

							<input type='date' id="datafim" class="fill" min='{data}' value='{data}' title="Data final." required disabled>
						</div>
					</div>

					<div class="flex flex-ai-fe">
						<button type="submit" class="button-blue" title="Procurar vendas totalizadas">Procurar</button>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>

<!-- <div class="setor-2 desktop">
	Relatório
</div> -->

<div class="box-container">

	<div class="reportsale_not_found fill">

		<div class="font-size-12 textcenter" style="padding: 80px 10px;">
			Para visualizar o gráfico de vendas de um produto clique no botão Procurar.
		</div>
	</div>

	<div class="reportsaleoneproduct_container flex flex-dc gap-10">

		<!-- BEGIN EXTRA_BLOCK_REPORTSALEONEPRODUCT_CONTAINER -->
		<div class="box-header setor-2 flex flex-jc-sb flex-ai-center gap-10">
			<div class="popup-title fill w_reportsaleoneproduct_filter"></div>
			<!-- <button class="bt_reportsale_oneproduct_close button-icon button-transparent color-white fa-solid fa-xmark" title="Fechar"></button> -->
		</div>

		<div class="flex-responsive gap-10">
			<div class="flex-15">
				<label class="produto-tipo caption">
					{produtotipo}
				</label>

				<div class="addon">
					{extra_block_product_button_status}
					{block_product_produto}
				</div>
			</div>

			<div class="flex-3">
				<label class="caption">
					Total Vendido
				</label>

				<div class="addon">
					<span><span class="w_reportsaleoneproduct_total"></span> <span class="font-size-075">{produtounidade}</span></span>
				</div>
			</div>
		</div>

		<div class="w_reportsaleoneproduct_graph_container flex-table gap-10">
		</div>
		<!-- END EXTRA_BLOCK_REPORTSALEONEPRODUCT_CONTAINER -->
	</div>
</div>
<!-- END BLOCK_PAGE -->