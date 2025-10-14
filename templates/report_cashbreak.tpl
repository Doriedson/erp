<!-- BEGIN BLOCK_PAGE -->
<div class="box-container flex flex-dc gap-10">

	<div class="box-header gap-10">
		<i class="icon fa-solid fa-file-invoice"></i>
		<span>Relatório / Quebra de Caixa</span>
	</div>

	<div class="flex-responsive gap-10 flex-jc-sb">

		<form method="post" id="frm_report_cashbreak" class="fill">

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
						<button type="submit" class="button-blue" title="Procurar sangrias de caixa por data">Procurar</button>
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

	<div class="color-blue font-size-12 textright">
		<span>Total&nbsp;R$</span>
		<span class="w-reportcashbreak-total">0,00</span>
	</div>
</div> -->

<div class="w-report-cashbreak-container box-container flex flex-dc gap-10">

	<div class="report_cashbreak_not_found window fill">

		<div class="font-size-12 textcenter" style="padding: 80px 10px;">
			Selecione uma data no campo de Procura.
		</div>
	</div>

	<!-- BEGIN EXTRA_BLOCK_REPORT_CASHBREAK_NOT_FOUND -->
	<div class="report_cashbreak_not_found window fill">

		<div class="font-size-12 textcenter" style="padding: 80px 10px;">
			Nenhum relatório encontrado para data informada.
		</div>
	</div>
	<!-- END EXTRA_BLOCK_REPORT_CASHBREAK_NOT_FOUND -->

	<!-- BEGIN EXTRA_BLOCK_CONTENT -->
	<div class="w-report-cashbreak window flex flex-dc gap-10">

		<div class="padding-b10 border-bottom flex flex-jc-sb flex-ai-fe gap-10">
			<div class="setor-2">
				{header}
			</div>

			<div class="">
				<label class="caption">Saldo Total</label>
				<div class="addon textright color-blue">
					<span>R$ <span class="w-reportcashbreak-total">0,00</span></span>
				</div>
			</div>
		</div>

		{extra_block_cashbreak_collaborator}

		<!-- BEGIN EXTRA_BLOCK_CASHBREAK_COLLABORATOR -->
		<div class="window flex flex-dc gap-10">

			<div class="flex-responsive gap-10 border-bottom padding-b10">

				<div class="flex-6">
					<label class="caption">Colaborador</label>
					<div class="addon">
						{extra_block_entity_button_status}
						<span class="entity_{id_entidade}_nome">{nome}</span>
					</div>
				</div>

				<div class="flex gap-10 flex-6">
					<div class="flex-3 color-red">
						<label class="caption">Faltou</label>
						<div class="addon">
							<span>R$</span>
							<span class="field">{quebraP}</span>
						</div>
					</div>

					<div class="flex-3 color-green">
						<label class="caption">Sobrou</label>
						<div class="addon">
							<span>R$</span>
							<span class="field">{quebraN}</span>
						</div>
					</div>
				</div>

				<div class="flex gap-10 flex-4">

					{extra_block_cashbreak_total}
					<!-- BEGIN EXTRA_BLOCK_CASHBREAK_TOTAL_P -->
					<div class="flex-3 color-red">
						<label class="caption">Saldo</label>
						<div class="addon font-size-12">
							<span>R$</span>
							<span class="field">{quebra_formatted}</span>
						</div>
					</div>
					<!-- END EXTRA_BLOCK_CASHBREAK_TOTAL_P -->

					<!-- BEGIN EXTRA_BLOCK_CASHBREAK_TOTAL_N -->
					<div class="flex-3 color-green">
						<label class="caption">Saldo</label>
						<div class="addon font-size-12">
							<span>R$</span>
							<span class="field">{quebra_formatted}</span>
						</div>
					</div>
					<!-- END EXTRA_BLOCK_CASHBREAK_TOTAL_N -->

					<div class="flex flex-jc-fe flex-ai-fe">
						<button class="bt_expand button-icon button-blue fa-solid fa-chevron-down">&nbsp;</button>
					</div>
				</div>
			</div>

			<div class="expandable" style="display: none;">
				<div class="flex flex-dc gap-10">

					<div class="section-header">
						Histórico
					</div>

					<div class="flex flex-dc table tbody">
						{extra_block_cashbreak_entry}
						<!-- BEGIN EXTRA_BLOCK_CASHBREAK_ENTRY -->
						<div class="tr flex-responsive gap-10">

							<div class="flex gap-10 flex-4">
								<div class="flex-2">
									<label class="caption">Data</label>
									<div class="addon">
										<span class="field">{data_formatted}</span>
									</div>
								</div>

								<div class="flex-2">
									<label class="caption">Total de vendas</label>
									<div class="addon">
										<span>R$</span>
										<span class="field">{total_formatted}</span>
									</div>
								</div>
							</div>

							<div class="flex gap-10 flex-2">
								{extra_block_cashbreak_value}
								<!-- BEGIN EXTRA_BLOCK_CASHBREAK_POSITIVE -->
								<div class="flex-2 color-red">
									<label class="caption">Faltou</label>
									<div class="addon">
										<span>R$</span>
										<span class="field">{quebra_formatted}</span>
									</div>
								</div>
								<!-- END EXTRA_BLOCK_CASHBREAK_POSITIVE -->

								<!-- BEGIN EXTRA_BLOCK_CASHBREAK_NEGATIVE -->
								<div class="flex-2 color-green">
									<label class="caption">Sobrou</label>
									<div class="addon">
										<span>R$</span>
										<span class="field">{quebra_formatted}</span>
									</div>
								</div>
								<!-- END EXTRA_BLOCK_CASHBREAK_NEGATIVE -->
							</div>

							<div class="flex-10"></div>
						</div>
						<!-- END EXTRA_BLOCK_CASHBREAK_ENTRY -->
					</div>
				</div>
			</div>
		</div>
		<!-- END EXTRA_BLOCK_CASHBREAK_COLLABORATOR -->
	</div>
	<!-- END EXTRA_BLOCK_CONTENT -->
</div>
<!-- END BLOCK_PAGE -->