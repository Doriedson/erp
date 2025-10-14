<!-- BEGIN BLOCK_PAGE -->
<div class="box-container flex flex-dc gap-10">

	<div class="box-header gap-10">
		<i class="icon fa-solid fa-file-invoice"></i>
		<span>Relatório / Entrada de Estoque</span>
	</div>

	<div class="flex-responsive gap-10 flex-jc-sb">

		<form method="post" id="frm_report_stockin" class="fill">

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
						<button type="submit" class="button-blue" title="Procurar produtos de entrada de estoque">Procurar</button>
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


</div> -->

<div class="report_stockin_container flex box-container flex-dc gap-10">

	<div class="report_stockin_not_found window fill">

		<div class="font-size-12 textcenter" style="padding: 80px 10px;">
			Selecione uma data no campo de Procura.
		</div>
	</div>

	<!-- BEGIN EXTRA_BLOCK_REPORTSTOCKIN_NOTFOUND -->
	<div class="report_stockin_not_found window fill">

		<div class="font-size-12 textcenter" style="padding: 80px 10px;">
			Nenhum relatório encontrado para a data informada.
		</div>
	</div>
	<!-- END EXTRA_BLOCK_REPORTSTOCKIN_NOTFOUND -->

	<!-- BEGIN EXTRA_BLOCK_CONTENT -->
	<div class="window flex flex-dc gap-10">
		<div class="box-header flex flex-jc-sb gap-10">
			{header}
			<div>
				<span>Total R$ <span class="w-reportstockin-total">0,00</span></span>
			</div>
		</div>

		{extra_block_setor_grupo}
		<!-- BEGIN EXTRA_BLOCK_SETOR_GRUPO -->
		<div class="window flex flex-dc gap-10">
			<div class="flex gap-10">
				<div class="section-header flex-1 flex flex-jc-sb gap-10">

					<span class="field">{produtosetor}</span>
					<span>R$ <span class="field">{subtotal_formatted}</span></span>
				</div>

				<div class="flex flex-ai-fe">
					<button class="bt_expand button-icon button-blue fa-solid fa-chevron-down">&nbsp;</button>
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
										<span class="field">{qtd_formatted} <span class="font-size-075">{produtounidade}</span></span>
									</div>
								</div>

								<div class="flex-2">
									<label class="caption">Custo médio</label>
									<div class="addon">
										<span>R$ {custo_formatted} <span class="font-size-075">/{produtounidade}</span></span>
									</div>
								</div>

								<div class="flex-2">
									<label class="caption">Custo total</label>
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
	</div>
	<!-- END EXTRA_BLOCK_CONTENT -->
</div>
<!-- END BLOCK_PAGE -->