<!-- BEGIN BLOCK_PAGE -->

<!-- BEGIN EXTRA_BLOCK_POPUP_REPORTSALE_CASHCHANGE -->
<div class="flex flex-dc gap-10">
	<div class="nome_{id_caixa}">
		<label class="caption">Operador(a)</label>
		<div class="addon">
			<span class="entity_{id_entidade}_nome">{nome}</span>
		</div>
	</div>

	<div class="section-header no-margin">Fundo de Caixa</div>

	<div class="w_reportsale_cashchange flex flex-dc gap-10">

		{extra_block_troco}

		<!-- BEGIN EXTRA_BLOCK_TROCO -->
		<div class="addon flex-jc-sb">
			<span>Moeda 0,01</span>
			<!-- BEGIN BLOCK_MOEDA_1 -->
			<button class="bt_moeda_1 button-field textright" data-id_caixa="{id_caixa}" title="Alterar troco">
				R$ {moeda_1_formatted}

			</button>
			<!-- END BLOCK_MOEDA_1 -->
			<!-- BEGIN EXTRA_BLOCK_MOEDA_1_FORM -->
			<form method="post" id="frm_moeda_1" class="" data-id_caixa="{id_caixa}">
				R$ <input
					type="number"
					id="moeda_1"
					class=""
					step='0.01'
					min="0"
					max="999999.99"
					required
					value='{moeda_1}'
					autofocus>
			</form>
			<!-- END EXTRA_BLOCK_MOEDA_1_FORM -->
		</div>

		<div class="addon flex-jc-sb">
			<span>Moeda 0,05</span>
			<!-- BEGIN BLOCK_MOEDA_5 -->
			<button class="bt_moeda_5 button-field textright " data-id_caixa="{id_caixa}" title="Alterar troco">
				R$ {moeda_5_formatted}

			</button>
			<!-- END BLOCK_MOEDA_5 -->
			<!-- BEGIN EXTRA_BLOCK_MOEDA_5_FORM -->
			<form method="post" id="frm_moeda_5" class="" data-id_caixa="{id_caixa}">
				R$ <input
					type="number"
					id="moeda_5"
					class=""
					step='0.05'
					min="0"
					max="999999.99"
					required
					value='{moeda_5}'
					autofocus>
			</form>
			<!-- END EXTRA_BLOCK_MOEDA_5_FORM -->
		</div>

		<div class="addon flex-jc-sb">
			<span>Moeda 0,10</span>
			<!-- BEGIN BLOCK_MOEDA_10 -->
			<button class="bt_moeda_10 button-field textright " data-id_caixa="{id_caixa}" title="Alterar troco">
				R$ {moeda_10_formatted}

			</button>
			<!-- END BLOCK_MOEDA_10 -->
			<!-- BEGIN EXTRA_BLOCK_MOEDA_10_FORM -->
			<form method="post" id="frm_moeda_10" class="" data-id_caixa="{id_caixa}">
				R$ <input
					type="number"
					id="moeda_10"
					class=""
					step='0.10'
					min="0"
					max="999999.99"
					required
					value='{moeda_10}'
					autofocus>
			</form>
			<!-- END EXTRA_BLOCK_MOEDA_10_FORM -->
		</div>

		<div class="addon flex-jc-sb">
			<span>Moeda 0,25</span>
			<!-- BEGIN BLOCK_MOEDA_25 -->
			<button class="bt_moeda_25 button-field textright " data-id_caixa="{id_caixa}" title="Alterar troco">
				R$ {moeda_25_formatted}

			</button>
			<!-- END BLOCK_MOEDA_25 -->
			<!-- BEGIN EXTRA_BLOCK_MOEDA_25_FORM -->
			<form method="post" id="frm_moeda_25" class="" data-id_caixa="{id_caixa}">
				R$ <input
					type="number"
					id="moeda_25"
					class=""
					step='0.25'
					min="0"
					max="999999.99"
					required
					value='{moeda_25}'
					autofocus>
			</form>
			<!-- END EXTRA_BLOCK_MOEDA_25_FORM -->
		</div>

		<div class="addon flex-jc-sb">
			<span>Moeda 0,50</span>
			<!-- BEGIN BLOCK_MOEDA_50 -->
			<button class="bt_moeda_50 button-field textright " data-id_caixa="{id_caixa}" title="Alterar troco">
				R$ {moeda_50_formatted}

			</button>
			<!-- END BLOCK_MOEDA_50 -->
			<!-- BEGIN EXTRA_BLOCK_MOEDA_50_FORM -->
			<form method="post" id="frm_moeda_50" class="" data-id_caixa="{id_caixa}">
				R$ <input
					type="number"
					id="moeda_50"
					class=""
					step='0.50'
					min="0"
					max="999999.99"
					required
					value='{moeda_50}'
					autofocus>
			</form>
			<!-- END EXTRA_BLOCK_MOEDA_50_FORM -->
		</div>

		<div class="addon flex-jc-sb">
			<span>Moeda 1,00</span>
			<!-- BEGIN BLOCK_CEDULA_1 -->
			<button class="bt_cedula_1 button-field textright " data-id_caixa="{id_caixa}" title="Alterar troco">
				R$ {cedula_1_formatted}

			</button>
			<!-- END BLOCK_CEDULA_1 -->
			<!-- BEGIN EXTRA_BLOCK_CEDULA_1_FORM -->
			<form method="post" id="frm_cedula_1" class="" data-id_caixa="{id_caixa}">
				R$ <input
					type="number"
					id="cedula_1"
					class=""
					step='1'
					min="0"
					max="999999.99"
					required
					value='{cedula_1}'
					autofocus>
			</form>
			<!-- END EXTRA_BLOCK_CEDULA_1_FORM -->
		</div>

		<div class="addon flex-jc-sb">
			<span>Cédula 2,00</span>
			<!-- BEGIN BLOCK_CEDULA_2 -->
			<button class="bt_cedula_2 button-field textright " data-id_caixa="{id_caixa}" title="Alterar troco">
				R$ {cedula_2_formatted}

			</button>
			<!-- END BLOCK_CEDULA_2 -->
			<!-- BEGIN EXTRA_BLOCK_CEDULA_2_FORM -->
			<form method="post" id="frm_cedula_2" class="" data-id_caixa="{id_caixa}">
				R$ <input
					type="number"
					id="cedula_2"
					class=""
					step='2'
					min="0"
					max="999999.99"
					required
					value='{cedula_2}'
					autofocus>
			</form>
			<!-- END EXTRA_BLOCK_CEDULA_2_FORM -->
		</div>

		<div class="addon flex-jc-sb">
			<span>Cédula 5,00</span>
			<!-- BEGIN BLOCK_CEDULA_5 -->
			<button class="bt_cedula_5 button-field textright " data-id_caixa="{id_caixa}" title="Alterar troco">
				R$ {cedula_5_formatted}

			</button>
			<!-- END BLOCK_CEDULA_5 -->
			<!-- BEGIN EXTRA_BLOCK_CEDULA_5_FORM -->
			<form method="post" id="frm_cedula_5" class="" data-id_caixa="{id_caixa}">
				R$ <input
					type="number"
					id="cedula_5"
					class=""
					step='5'
					min="0"
					max="999999.99"
					required
					value='{cedula_5}'
					autofocus>
			</form>
			<!-- END EXTRA_BLOCK_CEDULA_5_FORM -->
		</div>

		<div class="addon flex-jc-sb">
			<span>Cédula 10,00</span>
			<!-- BEGIN BLOCK_CEDULA_10 -->
			<button class="bt_cedula_10 button-field textright " data-id_caixa="{id_caixa}" title="Alterar troco">
				R$ {cedula_10_formatted}

			</button>
			<!-- END BLOCK_CEDULA_10 -->
			<!-- BEGIN EXTRA_BLOCK_CEDULA_10_FORM -->
			<form method="post" id="frm_cedula_10" class="" data-id_caixa="{id_caixa}">
				R$ <input
					type="number"
					id="cedula_10"
					class=""
					step='10'
					min="0"
					max="999999.99"
					required
					value='{cedula_10}'
					autofocus>
			</form>
			<!-- END EXTRA_BLOCK_CEDULA_10_FORM -->
		</div>

		<div class="addon flex-jc-sb">
			<span>Cédula 20,00</span>
			<!-- BEGIN BLOCK_CEDULA_20 -->
			<button class="bt_cedula_20 button-field textright " data-id_caixa="{id_caixa}" title="Alterar troco">
				R$ {cedula_20_formatted}

			</button>
			<!-- END BLOCK_CEDULA_20 -->
			<!-- BEGIN EXTRA_BLOCK_CEDULA_20_FORM -->
			<form method="post" id="frm_cedula_20" class="" data-id_caixa="{id_caixa}">
				R$ <input
					type="number"
					id="cedula_20"
					class=""
					step='20'
					min="0"
					max="999999.99"
					required
					value='{cedula_20}'
					autofocus>
			</form>
			<!-- END EXTRA_BLOCK_CEDULA_20_FORM -->
		</div>

		<div class="addon flex-jc-sb">
			<span>Cédula 50,00</span>
			<!-- BEGIN BLOCK_CEDULA_50 -->
			<button class="bt_cedula_50 button-field textright " data-id_caixa="{id_caixa}" title="Alterar troco">
				R$ {cedula_50_formatted}

			</button>
			<!-- END BLOCK_CEDULA_50 -->
			<!-- BEGIN EXTRA_BLOCK_CEDULA_50_FORM -->
			<form method="post" id="frm_cedula_50" class="" data-id_caixa="{id_caixa}">
				R$ <input
					type="number"
					id="cedula_50"
					class=""
					step='50'
					min="0"
					max="999999.99"
					required
					value='{cedula_50}'
					autofocus>
			</form>
			<!-- END EXTRA_BLOCK_CEDULA_50_FORM -->
		</div>

		<div class="addon flex-jc-sb">
			<span>Cédula 100,00</span>
			<!-- BEGIN BLOCK_CEDULA_100 -->
			<button class="bt_cedula_100 button-field textright " data-id_caixa="{id_caixa}" title="Alterar troco">
				R$ {cedula_100_formatted}

			</button>
			<!-- END BLOCK_CEDULA_100 -->
			<!-- BEGIN EXTRA_BLOCK_CEDULA_100_FORM -->
			<form method="post" id="frm_cedula_100" class="" data-id_caixa="{id_caixa}">
				R$ <input
					type="number"
					id="cedula_100"
					class=""
					step='100'
					min="0"
					max="999999.99"
					required
					value='{cedula_100}'
					autofocus>
			</form>
			<!-- END EXTRA_BLOCK_CEDULA_100_FORM -->
		</div>

		<div class="addon flex-jc-sb">
			<span>Cédula 200,00</span>
			<!-- BEGIN BLOCK_CEDULA_200 -->
			<button class="bt_cedula_200 button-field textright " data-id_caixa="{id_caixa}" title="Alterar troco">
				R$ {cedula_200_formatted}

			</button>
			<!-- END BLOCK_CEDULA_200 -->
			<!-- BEGIN EXTRA_BLOCK_CEDULA_200_FORM -->
			<form method="post" id="frm_cedula_200" class="" data-id_caixa="{id_caixa}">
				R$ <input
					type="number"
					id="cedula_200"
					class=""
					step='200'
					min="0"
					max="999999.99"
					required
					value='{cedula_200}'
					autofocus>
			</form>
			<!-- END EXTRA_BLOCK_CEDULA_200_FORM -->
		</div>

		<div class="addon flex-jc-sb color-blue">
			<span>Total</span>
			<span class="trocofim_{id_caixa} field textright disabled">R$ {troco_total_formatted}</span>
		</div>
		<!-- END EXTRA_BLOCK_TROCO -->
	</div>
</div>
<!-- END EXTRA_BLOCK_POPUP_REPORTSALE_CASHCHANGE -->

<!-- BEGIN EXTRA_BLOCK_REPORTSALE_CUPOM -->
<div class="cupom">
	{cupom}
</div>
<!-- END EXTRA_BLOCK_REPORTSALE_CUPOM -->

<div class="box-container flex flex-dc gap-10">

	<div class="box-header gap-10">
		<i class="icon fa-solid fa-file-invoice"></i>
		<span>Relatório / PDV</span>
	</div>

	<div class="flex-responsive gap-10 flex-jc-sb">

		<form method="post" id="frm_report_sale_total" class="fill">

			<div class="flex-responsive gap-10">

				<div class="flex gap-10">

					<div class="flex-1">
						<label class="caption flex flex-ai-center gap-5">
							<i class="fa-solid fa-magnifying-glass"></i>
							Data / Abertura
						</label>
						<div class="addon">
							<input
								type='date'
								id="dataini"
								class="fill"
								value='{data}'
								title="Data ou data inicial"
								required>
						</div>
					</div>
				</div>

				<div>
					<label class="caption">até</label>
					<div class="addon">
						<span class="flex">
							<input type="checkbox" id='intervalo' title="Ativa busca de data de vencimento por intervalo">
						</span>
						<input
							type='date'
							id="datafim"
							class="fill"
							min='{data}'
							value='{data}'
							title="Data final"
							required
							disabled>
					</div>
				</div>

				<div class="flex flex-ai-fe gap-10">
					<div class="flex-1">
						<label class="caption">por</label>
						<div class="addon">
							<span class="flex">
								<input type="checkbox" value='1' id="pdv">
							</span>
							<span class="field fill one-line">PDV</span>
						</div>
					</div>
					<div class="flex-1">
						<button type="submit" class="button-blue fill" title="Procurar vendas totalizadas">Procurar</button>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>

<!-- <div class="setor-2">
	Relatório
</div> -->

<div class="">

	<div class="w_pdvreport_none window box-container fill">

		<div class="font-size-12 textcenter" style="padding: 80px 10px;">
			Selecione uma data no campo de Procura.
		</div>
	</div>

	<div class="w_pdvreport_notfound window box-container fill hidden">

		<div class="font-size-12 textcenter" style="padding: 80px 10px;">
			Nenhum relatório encontrado para data especificada.
		</div>
	</div>

	<div class="w_pdvreport_container flex-responsive gap-10">
		<!-- BEGIN EXTRA_BLOCK_REPORT -->
		<div class="w_pdvreport w_reportpdv_{id_caixa} card-container flex flex-dc gap-10" style="min-width: 250px;">

			{extra_block_title}

			<!-- BEGIN EXTRA_BLOCK_TITLE -->
			<div class="window-header">
				{dataini_formatted}
			</div>
			<!-- END EXTRA_BLOCK_TITLE -->

			<!-- BEGIN EXTRA_BLOCK_TITLE_INTERVAL -->
			<div class="window-header">
				De {dataini_formatted} até {datafim_formatted}
			</div>
			<!-- END EXTRA_BLOCK_TITLE_INTERVAL -->

			<!-- BEGIN EXTRA_BLOCK_TITLE_PDV -->
			<div class="window-header">
				<div>
					Abertura {dataini_formatted}<br>
					Fechamento {datafim_formatted}
				</div>
			</div>
			<!-- <div class="nome_{id_caixa} color-gray font-size-09">{nome}</div> -->
			<div class="nome_{id_caixa}">
				<label class="caption">Operador(a)</label>
				<div class="addon">
					<span class="entity_{id_entidade}_nome">{nome}</span>
				</div>
			</div>
			<!-- END EXTRA_BLOCK_TITLE_PDV -->

			<div class="section-header padding-b5 flex-ai-fe no-margin">
				<span class="flex-1">Resumo</span>

				{extra_block_pdvreport_obs}
				<!-- BEGIN EXTRA_BLOCK_PDVREPORT_OBS -->
				<div class="w_pdvreport_obs flex flex-ai-fe">
					<div class="{tooltip} pos-rel">

						<button type="button" class="bt_pdvreport_closeview button-icon button-blue fa-solid fa-receipt {bt_pdvreport_closeview}" title="Exibir cupom de fechamento" data-id_caixa="{id_caixa}"></button>
						<button type="button" class="bt_comment button-icon button-blue {icon_tooltip}" title="Observação do PDV"></button>

						<span class="tooltiptext font-size-10">{obs}</span>

						<div class="float-form hidden">

							<div>
								<button class="bt_commentclose button-icon button-gray fa-solid fa-xmark pos-abs" style="top: -15px; right: -15px;" title="Fechar"></button>
							</div>

							<form method="post" class="frm_pdvreport_obs flex gap-10 fill" data-id_caixa='{id_caixa}'>
								<div>
									<label class="caption">Observação do PDV</label>
									<input
										type="text"
										class="fill field_obs"
										maxlength="255"
										value='{obs}'
										autocomplete="off"
										list="autocompleteOff"
										autofocus>
								</div>

								<div class="flex gap-10 flex-ai-fe">
									<button type="submit" class="flex flex-ai-center button-green" title="Salvar observação">
										<i class="icon fa-solid fa-check"></i>
									</button>
								</div>
							</form>
						</div>
					</div>
				</div>
				<!-- END EXTRA_BLOCK_PDVREPORT_OBS -->
			</div>

			<div class="flex flex-dc gap-10">

				{extra_block_especie}
				<!-- BEGIN EXTRA_BLOCK_ESPECIE -->
				<div>
					<div class="addon flex-jc-sb">
						<span class="caption">{especie}</span>
						<!-- <span class=" disabled">R$</span> -->
						<span class="field disabled">R$ {valor_formatted}</span>
					</div>
				</div>
				<!-- END EXTRA_BLOCK_ESPECIE -->

				<!-- BEGIN EXTRA_BLOCK_TOTAL -->
				<div>
					<div class="addon flex-jc-sb color-blue">
						<span >Total</span>
						<span class="field disabled">R$ {total_formatted}</span>
					</div>
				</div>
				<!-- END EXTRA_BLOCK_TOTAL -->

				<!-- BEGIN EXTRA_BLOCK_CASHBREAKPOSITIVE -->
				<div>
					<div class="addon flex-jc-sb color-green">
						<span>Sobrou</span>
						<span class="field disabled">R$ {quebra_formatted}</span>
					</div>
				</div>
				<!-- END EXTRA_BLOCK_CASHBREAKPOSITIVE -->

				<!-- BEGIN EXTRA_BLOCK_CASHBREAKNEGATIVE -->
				<div>
					<div class="addon flex-jc-sb color-red">
						<span>Faltou</span>
						<span class="field disabled">R$ {quebra_formatted}</span>
					</div>
				</div>

				<!-- END EXTRA_BLOCK_CASHBREAKNEGATIVE -->

				{extra_block_troco_container}
				<!-- BEGIN EXTRA_BLOCK_TROCO_CONTAINER -->
				<div class="section-header no-margin">
					Fundo de Caixa
				</div>

				<div class="window flex flex-dc gap-10">

					<div class="card-body">
						<!-- <label class="caption">Abertura</label> -->
						<div class="addon flex-jc-sb">
							<span>Abertura</span>
							<!-- BEGIN BLOCK_TROCOINI -->
							<button class="bt_trocoini button-field textleft" data-id_caixa="{id_caixa}" title="Alterar fundo de caixa inicial">
								R$ {trocoini_formatted}

							</button>
							<!-- END BLOCK_TROCOINI -->
							<!-- BEGIN EXTRA_BLOCK_TROCOINI_FORM -->
							<form method="post" id="frm_trocoini" class="" data-id_caixa="{id_caixa}">
								<div class="addon">
									<span>R$</span>
									<input
										type="number"
										id="trocoini"
										class="textright"
										step='0.01'
										min="0"
										max="999999.99"
										required
										value='{trocoini}'
										autofocus>
								</div>
							</form>
							<!-- END EXTRA_BLOCK_TROCOINI_FORM -->
						</div>
					</div>

					<div class="flex gap-10">
						<div class="addon gap-10 flex-jc-sb padding-l5">
							<div class="flex flex-ai-center gap-10">
								<span>Fechamento</span>
								<div>
									<button class="bt_trocofim_detalhado button-icon button-blue fa-solid fa-cash-register" data-id_caixa="{id_caixa}" title="Fundo de caixa detalhado"></button>
								</div>
							</div>
							<div class="flex">
								{extra_block_trocofim}
								<!-- BEGIN EXTRA_BLOCK_TROCOFIM_PDVABERTO -->
									<span>PDV aberto</span>
								<!-- END EXTRA_BLOCK_TROCOFIM_PDVABERTO -->
								<!-- BEGIN EXTRA_BLOCK_TROCOFIM -->
								<button class="trocofim_{id_caixa} bt_trocofim button-field textleft" data-id_caixa="{id_caixa}" title="Alterar fundo de caixa final">
									R$ {trocofim_formatted}

								</button>
								<!-- END EXTRA_BLOCK_TROCOFIM -->
								<!-- BEGIN EXTRA_BLOCK_TROCOFIM_FORM -->
								<form method="post" id="frm_trocofim" class="" data-id_caixa="{id_caixa}">
									R$ <input
										type="number"
										id="trocofim"
										class="textright"
										step='0.01'
										min="0"
										max="999999.99"
										required
										value='{trocofim}'
										autofocus>
								</form>
								<!-- END EXTRA_BLOCK_TROCOFIM_FORM -->
							</div>
						</div>
					</div>
				</div>
				<!-- END EXTRA_BLOCK_TROCO_CONTAINER -->
			</div>
		</div>
		<!-- END EXTRA_BLOCK_REPORT -->
	</div>
</div>
<!-- END BLOCK_PAGE -->