<!-- BEGIN BLOCK_PAGE -->

<!-- BEGIN EXTRA_BLOCK_POPUP_CASHDRAIN -->
<div class="flex flex-dc gap-10">

	<div class="flex-2">
		<label class="caption">Data</label>
		<div class="addon">
			<span class="field">{data_formatted}</span>
		</div>
	</div>

	<div class="flex-8">
		<label class="caption">
			<span class="entity_{id_entidade}_nome">{nome}</span>
		</label>
		<div class="addon">
			<!-- BEGIN BLOCK_OBS -->
			<button class="cashdrain_bt_obs button-field textleft fill" title="Editar observação da sangria" data-id_caixasangria="{id_caixasangria}">
				{obs}

			</button>
			<!-- END BLOCK_OBS -->
			<!-- BEGIN EXTRA_BLOCK_FORM_OBS -->
			<form method="post" id="frm_cashdrain_obs" data-id_caixasangria="{id_caixasangria}" class="fill">
				<input
					class="fill"
					type='text'
					id='obs'
					required
					placeholder=''
					value='{obs}'
					maxlength='255'
					autofocus>
			</form>
			<!-- END EXTRA_BLOCK_FORM_OBS -->
		</div>
	</div>

	<div class="flex gap-10 flex-4">
		<div class="flex-2">
			<label class="caption">Espécie</label>
			<div class="addon">
				<!-- BEGIN BLOCK_ESPECIE -->
				<button class="cashdrain_bt_especie button-field textleft" title="Editar espécie da sangria" data-id_caixasangria="{id_caixasangria}">
					{especie}

				</button>
				<!-- END BLOCK_ESPECIE -->
				<!-- BEGIN EXTRA_BLOCK_FORM_ESPECIE -->
				<form method="post" id="frm_cashdrain_especie" class="fill" data-id_caixasangria="{id_caixasangria}">
					<select id="id_especie" class="fill" autofocus>
						{extra_block_option}
						<!-- BEGIN EXTRA_BLOCK_OPTION -->
						<option value="{id_especie}" {selected}>{especie}</option>
						<!-- END EXTRA_BLOCK_OPTION -->
					</select>
				</form>
				<!-- END EXTRA_BLOCK_FORM_ESPECIE -->
			</div>
		</div>

		<div class="flex-2">
			<label class="caption">Valor</label>
			<div class="addon">
				<span>R$</span>
				<!-- BEGIN BLOCK_VALOR -->
				<button class="cashdrain_bt_valor button-field textleft"  data-valor="{valor}" title="Editar valor da sangria" data-id_caixasangria="{id_caixasangria}">
					{valor_formatted}

				</button>
				<!-- END BLOCK_VALOR -->
				<!-- BEGIN EXTRA_BLOCK_FORM_VALOR -->
				<form method="post" id="frm_cashdrain_valor" class="fill" data-id_caixasangria="{id_caixasangria}">
					<input
						type="number"
						id="valor"
						class="fill"
						step='0.01'
						min='0'
						max='999999.99'
						required
						value='{valor}'
						autofocus>
				</form>
				<!-- END EXTRA_BLOCK_FORM_VALOR -->
			</div>
		</div>
	</div>
</div>
<!-- END EXTRA_BLOCK_POPUP_CASHDRAIN -->

<!-- BEGIN EXTRA_BLOCK_POPUP_CASHADD -->
<div class="flex gap-10">

	<div class="flex-1">
		<label class="caption">{data_formatted}</label>
		<div class="addon">
			<span class="">{nome}</span>
		</div>
	</div>

	<div class="">
		<label class="caption">{especie}</label>
		<div class="addon">
			<!-- BEGIN BLOCK_CASHADD_VALOR -->
			<button class="cashadd_bt_valor button-field textright"  data-id_caixareforco="{id_caixareforco}" title="Editar valor do reforço">
				R$ {valor_formatted}
			</button>
			<!-- END BLOCK_CASHADD_VALOR -->
			<!-- BEGIN EXTRA_BLOCK_FORM_CASHADD_VALOR -->
			<form method="post" id="frm_cashadd_valor" class="flex flex-ai-center gap-10 fill" data-id_caixareforco="{id_caixareforco}">
				R$ <input
					type="number"
					id="valor"
					class="fill"
					step='0.01'
					min='0'
					max='999999.99'
					required
					value='{valor}'
					autofocus>
			</form>
			<!-- END EXTRA_BLOCK_FORM_CASHADD_VALOR -->
		</div>
	</div>
</div>
<!-- END EXTRA_BLOCK_POPUP_CASHADD -->

<div class="box-container flex flex-dc gap-10">

	<div class="box-header gap-10">
		<i class="icon fa-solid fa-file-invoice"></i>
		<span>Relatório / Sangria e Reforço de Caixa</span>
	</div>

	<div class="flex-responsive gap-10 flex-jc-sb">

		<form method="post" id="frm_report_cashdrain" class="fill">

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

<div class="flex flex-dc gap-10">

	<div class="reportcashdrain_notfound window fill box-container {cashdrain_notfound}">

		<div class="font-size-12 textcenter" style="padding: 80px 10px;">
			Utilize os campos acima para realizar pesquisa.
		</div>
	</div>

	<div class="reportcashdrain_container flex flex-dc gap-10">

		<!-- BEGIN EXTRA_BLOCK_CASHDRAIN_CONTENT -->
		<div class="window box-container">

			<div class="setor-2">
				{cashdrain_container_header}
			</div>

			<div class="flex gap-10">
				<div class="section-header flex-1 flex flex-jc-sb gap-10">
					<span>Sangrias</span>
					<span class="color-red">R$ <span class="cashdrain_total">{cashdrain_total_formatted}</span></span>
				</div>

				<div class="flex flex-ai-fe">
						<button type="button" class="button-icon button-blue bt_collapse fa-solid fa-chevron-up"></button>
				</div>
			</div>


			<div class="window flex flex-dc gap-10">

				<div class="reportcashdrain_table flex flex-dc table tbody expandable">

					{extra_block_cashdrain_tr}

					<!-- BEGIN EXTRA_BLOCK_CASHDRAIN_TR_NONE -->
					<div style="padding: 40px 10px;">
						Nenhuma sangria encontrada.
					</div>
					<!-- END EXTRA_BLOCK_CASHDRAIN_TR_NONE -->

					<!-- BEGIN EXTRA_BLOCK_CASHDRAIN_TR -->
					<div class="cashdrain_tr cashdrain_tr_{id_caixasangria} {conferido} tr flex flex-jc-sb gap-10" data-id_caixasangria="{id_caixasangria}" data-valor="{valor}">

						<div class="flex-2">
							<label class="caption">Sangria <span class="color-green {cashdrain_checked}">[conferido]</span><span class="color-red {cashdrain_notchecked}">[não conferido]</span></label>
							<label class="caption">{nome}</label>
							<div>{obs}</div>
						</div>

						<div class="flex-2">
							<label class="caption textright">{data_formatted}</label>
							<label class="caption textright">{especie}</label>
							<div class="textright color-red">R$ {valor_formatted}</div>
						</div>

						<div class="flex flex-ai-fe">
							<div class="menu-inter flex flex-ai-center">
								<button class="menu-inter-button fa-solid fa-ellipsis-vertical button-blue"></button>

								<ul>
									<li class="bt_cashdrain_edit flex flex-ai-center gap-10 color-blue" title="Editar sangria" data-id_caixasangria="{id_caixasangria}">
										<i class="icon fa-solid fa-file-pen"></i>
										<span>Editar Sangria</span>
									</li>

									<!-- <li class="flex flex-ai-center gap-10 color-red" title="Remover sangria" data-id_caixasangria="{id_caixasangria}">
										<i class="icon fa-solid fa-trash-can"></i>
										<span>Remover Sangria</span>
									</li> -->

									<li class="bt_cashdrain_check flex flex-ai-center gap-10 color-green {cashdrain_notchecked}" title="Marcar sangria como conferida" data-id_caixasangria="{id_caixasangria}">
										<i class="icon fa-solid fa-check"></i>
										<span>Marcar Conferido</span>
									</li>
								</ul>
							</div>
						</div>
					</div>
					<!-- END EXTRA_BLOCK_CASHDRAIN_TR -->
				</div>
			</div>
		</div>
		<!-- END EXTRA_BLOCK_CASHDRAIN_CONTENT -->

		<!-- BEGIN EXTRA_BLOCK_CASHADD_CONTENT -->
		<div class="window box-container">

			<div class="setor-2">
				{cashadd_container_header}
			</div>

			<div class="flex gap-10">
				<div class="section-header flex-1 flex flex-jc-sb gap-10">
					<span>Reforços</span>
					<span class="color-green">R$ <span class="cashadd_total">{cashadd_total_formatted}</span></span>
				</div>

				<div class="flex flex-ai-fe">
						<button type="button" class="button-icon button-blue bt_collapse fa-solid fa-chevron-up"></button>
				</div>
			</div>

            <div class="window flex flex-dc gap-10">

                <div class="reportcashadd_table flex flex-dc table tbody expandable">

					{extra_block_cashadd_tr}

					<!-- BEGIN EXTRA_BLOCK_CASHADD_TR_NONE -->
					<div style="padding: 40px 10px;">
						Nenhum reforço encontrado.
					</div>
					<!-- END EXTRA_BLOCK_CASHADD_TR_NONE -->

					<!-- BEGIN EXTRA_BLOCK_CASHADD_TR -->
					<div class="cashadd_tr cashadd_tr_{id_caixareforco} {conferido} tr flex flex-jc-sb gap-10" data-id_caixareforco="{id_caixareforco}" data-valor="{valor}">

						<div class="flex-2">
							<label class="caption">Reforço <span class="color-green {cashadd_checked}">[conferido]</span><span class="color-red {cashadd_notchecked}">[não conferido]</span></label>
							<label class="caption">{nome}</label>
						</div>

						<div class="flex-2">
							<label class="caption textright">{data_formatted}</label>
							<!-- <label class="caption textright">{especie}</label> -->
							<div class="textright color-green">R$ {valor_formatted}</div>
						</div>

						<div class="flex flex-ai-fe">
							<div class="menu-inter flex flex-ai-center">
								<button class="menu-inter-button fa-solid fa-ellipsis-vertical button-blue"></button>

								<ul>
									<li class="bt_cashadd_edit flex flex-ai-center gap-10 color-blue" title="Editar reforço" data-id_caixareforco="{id_caixareforco}">
										<i class="icon fa-solid fa-file-pen"></i>
										<span>Editar Reforço</span>
									</li>

									<!-- <li class="flex flex-ai-center gap-10 color-red" title="Remover sangria" data-id_caixareforco="{id_caixareforco}">
										<i class="icon fa-solid fa-trash-can"></i>
										<span>Remover Sangria</span>
									</li> -->

									<li class="bt_cashadd_check flex flex-ai-center gap-10 color-green {cashadd_notchecked}" title="Marcar reforço como conferido" data-id_caixareforco="{id_caixareforco}">
										<i class="icon fa-solid fa-check"></i>
										<span>Marcar Conferido</span>
									</li>
								</ul>
							</div>
						</div>
					</div>
					<!-- END EXTRA_BLOCK_CASHADD_TR -->
				</div>
			</div>
		</div>
		<!-- END EXTRA_BLOCK_CASHADD_CONTENT -->
	</div>
</div>
<!-- END BLOCK_PAGE -->