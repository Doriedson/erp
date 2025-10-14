<!-- BEGIN BLOCK_PAGE -->
<div class="box-container flex flex-dc gap-10">

	<div class="box-header gap-10">
		<i class="icon fa-solid fa-file-invoice"></i>
		<span>Relatório / Entrada & Saída de Estoque</span>
	</div>

	<div class="flex-responsive gap-10 flex-jc-sb">

		<form method="post" id="frm_report_stockinout" class="fill">

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

							<input type='date' id="datafim" class="fill" min='{data}' value='{data}' title="Data final" required disabled>
						</div>
					</div>

					<div class="flex flex-ai-fe">
						<button type="submit" class="button-blue" title="Procurar relatório de entrada e saída de estoque">Procurar</button>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>

<!-- <div class="flex-responsive flex-jc-sb gap-10">
	<div class="setor-2">
		Relatório
	</div>


</div> -->

<div class="report_stockinout_container box-container flex flex-dc gap-10">

	<div class="report_stockinout_not_found window fill">

		<div class="font-size-12 textcenter" style="padding: 80px 10px;">
			Selecione uma data no campo de Procura.
		</div>
	</div>

	<!-- BEGIN EXTRA_BLOCK_REPORTSTOCKINOUT_NOTFOUND -->
	<div class="report_stockinout_not_found window fill">

		<div class="font-size-12 textcenter" style="padding: 80px 10px;">
			Nenhum relatório encontrado para a data informada.
		</div>
	</div>
	<!-- END EXTRA_BLOCK_REPORTSTOCKINOUT_NOTFOUND -->

	<!-- BEGIN EXTRA_BLOCK_CONTENT -->
	<div class="window flex flex-dc gap-10">
		<div class="border-bottom flex-responsive flex-jc-sb flex-ai-fe padding-b10 gap-10">
			<div class="setor-2 flex-1">{header}</div>
			<div class="flex flex-1 fill gap-10">
				<div class="flex-1">
					<label class="caption">Compra total</label>
					<div class="addon textright color-blue">
						<span>R$ <span class="w-reportstockinout-compra">0,00</span></span>
					</div>
				</div>

				<div class="flex-1">
					<label class="caption">Venda total (<span class="w_reportstockinout_venda_percent"></span>)</label>
					<div class="addon color-blue textright">
						<span>R$ <span class="w_reportstockinout_venda">0,00</span></span><br>
					</div>
				</div>

				<div class="flex-1">
					<label class="caption">Lucro total</label>
					<div class="addon textright color-blue">
						<span>R$ <span class="w-reportstockinout-lucro">0,00</span></span>
					</div>
				</div>
			</div>
		</div>

		{extra_block_setor_grupo}
		<!-- BEGIN EXTRA_BLOCK_SETOR_GRUPO -->
		<div class="window flex flex-dc gap-10">
			<div class="flex-responsive flex-ai-fe border-bottom padding-b10 gap-10">

				<div class="flex-8 setor-2 fill">
					<!-- <label class="caption">Setor</label>
					<div class="addon"> -->
						<span class="">{produtosetor}</span>
					<!-- </div> -->
				</div>

				<div class="flex flex-6 fill gap-10">
					<div class="flex-3">
						<label class="caption">Compra setor</label>
						<div class="addon color-blue font-size-12">
							<span>R$ {subtotalcusto_formatted}</span>
						</div>
					</div>

					<div class="flex-3">
						<label class="caption">Venda setor</label>
						<div class="addon color-blue font-size-12">
							<span>R$ {subtotalvenda_formatted}</span>
						</div>
					</div>
				</div>

				<div class="flex gap-10 flex-4 fill">
					<div class="flex-3">
						<label class="caption">Lucro setor</label>
						<div class="addon color-blue font-size-12">
							<span>R$ {subtotal_formatted}</span>
						</div>
					</div>

					<div class="flex flex-ai-fe">
						<button class="bt_expand button-icon button-blue fa-solid fa-chevron-down"></button>
					</div>
				</div>
			</div>

			<div class="expandable" style="display: none;">
				<div class="window_content table tbody flex flex-dc">

					<!-- <div class="section-header">
						Itens
					</div> -->

					<div class="table tbody flex flex-dc">
						{extra_block_produto}
						<!-- BEGIN EXTRA_BLOCK_PRODUTO -->
						<div class="tr flex flex-dc gap-10">

							<div>
								<div class="flex-4">
									<label class="caption">Produto</label>
									<div class="addon">
										{extra_block_product_button_status}
										<span class=" uppercase fill">{produto}</span>
									</div>
								</div>
							</div>

							<div class="flex-responsive gap-10">

								<div class="flex flex-dc gap-10 flex-1">
									<div>
										<div class="addon flex-jc-sb padding-h10">
											<label>Entrada</label>
											<span>{compra_qtd_formatted} <span class="font-size-075">{produtounidade}</span></span>

										</div>
									</div>

									<div>
										<div class="addon flex-jc-sb padding-h10">
											<label>Saída</label>
											<span><span class="font-size-075">({venda_qtd_percent}%)</span> {venda_qtd_formatted} <span class="font-size-075">{produtounidade}</span></span>
										</div>
									</div>
								</div>

								<div class="flex flex-dc gap-10 flex-1">
									<div>
										<div class="addon flex-jc-sb padding-h10">
											<label>Compra</label>
											<span>R$ {custo_un_formatted} <span class="font-size-075">/{produtounidade}</span></span>
										</div>
									</div>

									<div>
										<div class="addon flex-jc-sb padding-h10">
											<label>Venda</label>
											<span>R$ {venda_un_formatted} <span class="font-size-075">/{produtounidade}</span></span>
										</div>
									</div>

									<div>
										<div class="addon flex-jc-sb padding-h10">
											<label>Lucro</label>
											<span><span class="font-size-075">({lucro_un_percent}%)</span> R$ {lucro_un_formatted} <span class="font-size-075">/{produtounidade}</span></span>
										</div>
									</div>
								</div>

								<div class="flex flex-1 flex-dc gap-10">
									<div>
										<div class="addon flex-jc-sb padding-h10">
											<label>Compra total</label>
											<span>R$ {compra_subtotal_formatted}</span>
										</div>
									</div>

									<div>
										<div class="addon flex-jc-sb padding-h10">
											<label>Venda total</label>
											<span><span class="font-size-075">({venda_percent}%)</span> R$ {venda_subtotal_formatted}</span>
										</div>
									</div>

									<div>
										<div class="addon flex-jc-sb padding-h10">
											<label>Lucro total</label>
											<span><span class="font-size-075">({lucro_percent}%)</span> R$ {lucro_total_formatted}</span>
										</div>
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