<!-- BEGIN BLOCK_PAGE -->
<div class="box-container flex flex-dc gap-10">

	<div class="box-header gap-10">
		<i class="icon fa-solid fa-file-invoice"></i>
		<span>Relatório / Contas Pagas</span>
	</div>

	<div class="flex-responsive gap-10 flex-jc-sb">

		<form method="post" id="frm_report_billspay" class="fill">

			<div class="flex-responsive gap-10">

				<div class="flex gap-10">
					<div class="flex-1">
						<label class="caption flex flex-ai-center gap-5">
							<i class="fa-solid fa-magnifying-glass"></i>
							Por
						</label>
						<div class="addon">
							<select id="procura" class="fill">
								<option value="0">Cadastro</option>
								<option value="1">Pago</option>
								<option value="2" selected>Vencimento</option>
							</select>
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

<div class="w-report-billspay-container box-container flex flex-dc gap-10">

	<div class="report_billspay_not_found window fill">

		<div class="font-size-12 textcenter" style="padding: 80px 10px;">
			Selecione uma data no campo de Procura.
		</div>
	</div>

	<!-- BEGIN EXTRA_BLOCK_REPORT_BILLSPAY_NOT_FOUND -->
	<div class="report_billspay_not_found window fill">

		<div class="font-size-12 textcenter" style="padding: 80px 10px;">
			Nenhum relatório encontrado para data informada.
		</div>
	</div>
	<!-- END EXTRA_BLOCK_REPORT_BILLSPAY_NOT_FOUND -->

	<!-- BEGIN EXTRA_BLOCK_CONTENT -->
	<div class="box-header flex flex-jc-sb gap-10">
		<div>{header}</div>
		<div>Total R$ <span class="reportbillspay_total">0,00</span></div>
	</div>

	{extra_block_setor}
	<!-- BEGIN EXTRA_BLOCK_SETOR -->
	<div class="window flex flex-dc gap-10">

		<div class="flex gap-10">
			<div class="section-header flex-1 flex flex-ai-center flex-jc-sb">

				<span>{contasapagarsetor}</span>
				<span>R$ <span class="reportbillspay_{id_contasapagarsetor}">{subtotal_formatted}</span></span>
			</div>

			<div class="flex flex-ai-fe">
				<button class="bt_expand button-icon button-blue fa-solid fa-chevron-down"></button>
			</div>
		</div>

		<div class="expandable" style="display: none;">

			<div class="table tbody">
				{extra_block_tr}
			</div>
		</div>
	</div>
	<!-- END EXTRA_BLOCK_SETOR -->
	<!-- END EXTRA_BLOCK_CONTENT -->
</div>
<!-- END BLOCK_PAGE -->