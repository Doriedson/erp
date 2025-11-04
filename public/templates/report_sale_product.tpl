<!-- BEGIN BLOCK_PAGE -->
<div class="box-container flex flex-dc gap-10">

	<div class="box-header gap-10">
		<i class="icon fa-solid fa-file-invoice"></i>
		<span>Relatório / Produtos Vendidos</span>
	</div>

	<div class="flex-responsive gap-10 flex-jc-sb">

		<form method="post" id="frm_report_sale_product" class="fill">

			<div class="flex-responsive gap-10">

				<div class="flex gap-10">

					<div class="flex-1">
						<label class="caption flex flex-ai-center gap-5">
							<i class="fa-solid fa-magnifying-glass"></i>
							Data
						</label>
						<div class="addon">
							<input type='date' id="dataini" class="fill" value='{data}' title="Data ou data inicial." required>
						</div>
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

<!-- <div class="flex flex-jc-sb gap-10">
	<div class="setor-2">
		Relatório
	</div>

	<div class="color-blue font-size-15">
		<span>Total&nbsp;R$</span>
		<span class="w-reportsale-total">0,00</span>
	</div>
</div> -->

<div class="report_sale_product_container box-container flex flex-dc gap-10">

	<div class="reportsale_not_found window fill">

		<div class="font-size-12 textcenter" style="padding: 80px 10px;">
			Selecione uma data no campo de Procura.
		</div>
	</div>

	<!-- BEGIN EXTRA_BLOCK_REPORTSALE_NOTFOUND -->
	<div class="reportsale_not_found window fill">

		<div class="font-size-12 textcenter" style="padding: 80px 10px;">
			Nenhum relatório encontrado para a data informada.
		</div>
	</div>
	<!-- END EXTRA_BLOCK_REPORTSALE_NOTFOUND -->

	<!-- BEGIN EXTRA_BLOCK_CONTAINER -->
	<div class="box-header flex flex-jc-sb gap-10">

		{extra_block_table_header}
		<!-- BEGIN EXTRA_BLOCK_TABLE_HEADER -->
		<span>Produtos vendidos em {dataini_formatted}</span>
		<!-- END EXTRA_BLOCK_TABLE_HEADER -->
		<!-- BEGIN EXTRA_BLOCK_TABLE_HEADER_INTERVAL -->
		<span>Produtos vendidos entre {dataini_formatted} e {datafim_formatted}</span>
		<!-- END EXTRA_BLOCK_TABLE_HEADER_INTERVAL -->

		<div>
			<span>Total R$ <span class="w-reportsale-total">0,00</span></span>
		</div>
	</div>

	{extra_block_setor_grupo}
	<!-- BEGIN EXTRA_BLOCK_SETOR_GRUPO -->
	<div class="window flex flex-dc gap-10">
		<div class="flex gap-10">

			<div class="section-header flex-1 flex flex-jc-sb gap-10">
				<span>{produtosetor}</span>
				<span>R$ {subtotal_formatted}</span>
			</div>

			<div class="flex flex-ai-fe">
				<button class="bt_expand button-icon button-blue fa-solid fa-chevron-down"></button>
			</div>
		</div>

		<div class="expandable" style="display: none;">
			<div class="window_content flex flex-dc gap-10">

				<!-- <div class="section-header">
					Itens
				</div> -->

				<div class="table tbody flex flex-dc">
					{extra_block_produto}
					<!-- BEGIN EXTRA_BLOCK_PRODUTO -->
					<div class="tr flex-responsive gap-10">
						<div class="flex-10">
							<label class="caption">Produto</label>
							<div class="addon">
								{extra_block_product_button_status}
								<span class="field uppercase fill">{produto}</span>
							</div>
						</div>

						<div class="flex gap-10 flex-6">
							<div class="flex-2">
								<label class="caption">Qtd</label>
								<div class="addon">
									<span>{qtd_formatted} <span class="font-size-075">{produtounidade}</span></span>
								</div>
							</div>

							<div class="flex-2">
								<label class="caption">Preço médio</label>
								<div class="addon">
									<span>R$ {valor_medio_formatted} <span class="font-size-075">/{produtounidade}</span></span>
								</div>
							</div>

							<div class="flex-2">
								<label class="caption">Total</label>
								<div class="addon">
									<span>R$ {subtotal_formatted}</span>
								</div>
							</div>
						</div>
					</div>
					<!-- END EXTRA_BLOCK_PRODUTO -->
				</div>
			</div>
		</div>
	</div>
	<!-- END EXTRA_BLOCK_SETOR_GRUPO -->
	<!-- END EXTRA_BLOCK_CONTAINER -->
</div>
<!-- END BLOCK_PAGE -->