<!-- BEGIN BLOCK_PAGE -->

<!-- BEGIN EXTRA_BLOCK_POPUP_BILLSTOPAY_FILTER -->
<form method="post" id="frm_billstopay_search">

	<div class="flex flex-dc gap-10">

		<div>
			<label class="caption">Por</label>
			<div class="addon">
				<select id="procura" class="fill">
					<option value="0">Data de Lançamento</option>
					<option value="1">Data de Pagamento</option>
					<option value="2" selected>Data de Vencimento</option>
				</select>
			</div>
		</div>

		<div class="flex gap-10">
			<div class="flex-1">
				<label class="caption">de</label>
				<div class="addon">
					<input
						type='date'
						id="dataini"
						class="fill"
						value='{data}'
						title="Data de vencimento."
						required>
				</div>
			</div>

			<div class="flex-1">
				<label class="caption">até</label>
				<div class="addon">
					<span class="flex">
						<input type="checkbox" id='intervalo' title="Ativa busca de data de vencimento por intervalo.">
					</span>
					<input
						type='date'
						id="datafim"
						class="fill"
						min='{data}'
						value='{data}'
						title="Data de vencimento."
						disabled
						required>
				</div>
			</div>
		</div>

		<div class="">
			<label class="caption">Setor</label>
			<div class="addon">
				<span class="flex">
					<input type="checkbox" id='chk_setor' title="Ativa busca por setor">
				</span>
				<select
					id='setor'
					class="fill uppercase"
					title="Categoria da conta."
					required
					disabled>{setor_lista}</select>
			</div>
		</div>

		<div>
			<label class="caption">Descrição</label>
			<div class="addon">
				<span class="flex">
					<input type="checkbox" id='chk_descricao' title="Ativa busca de contas por descrição.">
				</span>
				<input
					type="text"
					id="descricao"
					class="fill"
					maxlength="50"
					required
					placeholder=""
					autocomplete="off"
					title="Busca por descrição da conta."
					disabled>
			</div>
		</div>

		<div class="flex flex-jc-center">
			<button type="submit" class="button-blue margin-t10" title="Filtro para Contas a Pagar">Aplicar Filtro</button>
		</div>
	</div>
</form>
<!-- END EXTRA_BLOCK_POPUP_BILLSTOPAY_FILTER -->

<!-- BEGIN EXTRA_BLOCK_BILLSTOPAY_POPUP_EDITION -->
<div class="flex flex-dc gap-10">

	<div class="flex-responsive gap-10">
		<div class="flex-1">
			<label class="caption">Lançamento</label>
			<div class="addon">
				<span class="field disabled fill">{datacad_formatted}</span>
			</div>
		</div>

		<div class="flex-1">
			<label class="caption">Status</label>
			{block_billstopay_status}
		</div>
	</div>

	<div>
		<label class="caption">Descrição</label>
		<!-- BEGIN BLOCK_BILLSTOPAY_DESCRICAO -->
		<div class="addon container">
			<button class="billstopay_bt_descricao button-field textleft fill" data-id_contasapagar="{id_contasapagar}" title="Alterar descrição da conta">
				{descricao}
			</button>
		</div>
		<!-- END BLOCK_BILLSTOPAY_DESCRICAO -->
		<!-- BEGIN EXTRA_BLOCK_BILLSTOPAY_DESCRICAO_FORM -->
		<form method="post" id="frm_billstopay_descricao" class="fill" data-id_contasapagar="{id_contasapagar}">
			<div class="addon">
				<input
				type='text'
				id="descricao"
				class="fill"
				maxlength='100'
				placeholder=''
				value="{descricao}"
				title="Descrição para identificação da conta."
				required
				autofocus>
			</div>
		</form>
		<!-- END EXTRA_BLOCK_BILLSTOPAY_DESCRICAO_FORM -->
	</div>

	<div>
		<label class="caption">Setor</label>
		<!-- BEGIN BLOCK_BILLSTOPAY_SETOR -->
		<div class="addon container">
			<button class="billstopay_bt_setor button-field uppercase textleft fill" data-id_contasapagar="{id_contasapagar}" title="Alterar setor da conta">
				{contasapagarsetor}
			</button>
		</div>
		<!-- END BLOCK_BILLSTOPAY_SETOR -->
		<!-- BEGIN EXTRA_BLOCK_BILLSTOPAY_SETOR_FORM -->
		<form method="post" id="frm_billstopay_setor" class="fill" data-id_contasapagar="{id_contasapagar}">
			<div class="addon">
				<select
					id='setor'
					class="fill uppercase"
					title="Categoria da conta."
					autofocus>{setor_lista}</select>
			</div>
		</form>
		<!-- END EXTRA_BLOCK_BILLSTOPAY_SETOR_FORM -->
	</div>

	<div class="flex-responsive gap-10">
		<div class="flex-1">
			<label class="caption">Vencimento</label>
			<!-- BEGIN BLOCK_BILLSTOPAY_VENCIMENTO -->
			<div class="addon container">
				<button class="billstopay_bt_vencimento button-field textleft fill" data-id_contasapagar="{id_contasapagar}" title="Alterar data de vencimento">
					{vencimento_formatted}
				</button>
			</div>
			<!-- END BLOCK_BILLSTOPAY_VENCIMENTO -->
			<!-- BEGIN EXTRA_BLOCK_BILLSTOPAY_VENCIMENTO_FORM -->
			<form method="post" id="frm_billstopay_vencimento" class="fill" data-id_contasapagar="{id_contasapagar}">
				<div class="addon">
				<input
					type="date"
					id="vencimento"
					class="fill"
					value="{vencimento}"
					required
					autofocus>
				</div>
			</form>
			<!-- END EXTRA_BLOCK_BILLSTOPAY_VENCIMENTO_FORM -->
		</div>

		<div class="flex-1">
			<label class="caption">Valor a pagar</label>

			<!-- BEGIN BLOCK_BILLSTOPAY_VALOR -->
			<div class="addon">
				<button class="billstopay_bt_valor button-field textleft fill" data-id_contasapagar="{id_contasapagar}" title="Alterar valor da conta">
					R$ {valor_formatted}
				</button>
			</div>
			<!-- END BLOCK_BILLSTOPAY_VALOR -->
			<!-- BEGIN EXTRA_BLOCK_BILLSTOPAY_VALOR_FORM -->
			<form method="post" id="frm_billstopay_valor" class="fill" data-id_contasapagar="{id_contasapagar}">
				<div class="addon">
					<span>R$</span>
					<input
						type='number'
						id="valor"
						class="fill"
						placeholder=''
						value={valor}
						min="0.01"
						max="999999.99"
						step="0.01"
						title="Valor da conta."
						required
						autofocus>
				</div>
			</form>
			<!-- END EXTRA_BLOCK_BILLSTOPAY_VALOR_FORM -->
		</div>
	</div>

	{extra_block_billstopay_payment}

	<!-- BEGIN EXTRA_BLOCK_BILLSTOPAY_PAYMENT_DONE -->
	<div class="flex gap-10 flex-4">

		<div class="flex-2">
			<label class="caption">Data pago</label>

			{extra_block_billstopay_datapago}
			<!-- BEGIN EXTRA_BLOCK_BILLSTOPAY_DATAPAGO_NULL -->
			<div class="addon">
				<span class="field disabled fill">a pagar</span>
			</div>
			<!-- END EXTRA_BLOCK_BILLSTOPAY_DATAPAGO_NULL -->
			<!-- BEGIN EXTRA_BLOCK_BILLSTOPAY_DATAPAGO -->
			<div class="addon container">
				<button class="bills_bt_datapago button-field textleft fill" data-id_contasapagar="{id_contasapagar}" title="Alterar data de pagamento da conta">
					{datapago_formatted}
				</button>
			</div>
			<!-- END EXTRA_BLOCK_BILLSTOPAY_DATAPAGO -->
			<!-- BEGIN EXTRA_BLOCK_BILLSTOPAY_DATAPAGO_FORM -->
			<form method="post" id="frm_billstopay_datapago" class="fill" data-id_contasapagar="{id_contasapagar}">
				<div class="addon">
					<input
						type="date"
						id="datapago"
						class="fill"
						value="{datapago}"
						autofocus
						required>
				</div>
			</form>
			<!-- END EXTRA_BLOCK_BILLSTOPAY_DATAPAGO_FORM -->
		</div>

		<div class="flex-2">
			<label class="caption">Valor pago</label>

			{extra_block_billstopay_valorpago}
			<!-- BEGIN EXTRA_BLOCK_BILLSTOPAY_VALORPAGO_NULL -->
			<div class="addon">
				<span class="field disabled fill">a pagar</span>
			</div>
			<!-- END EXTRA_BLOCK_BILLSTOPAY_VALORPAGO_NULL -->

			<!-- BEGIN EXTRA_BLOCK_BILLSTOPAY_VALORPAGO -->
			<div class="addon container">
				<button class="billstopay_bt_valorpago button-field textleft fill" data-id_contasapagar="{id_contasapagar}" title="Alterar valor pago da conta">
					R$ {valorpago_formatted}
				</button>
			</div>
			<!-- END EXTRA_BLOCK_BILLSTOPAY_VALORPAGO -->

			<!-- BEGIN EXTRA_BLOCK_BILLSTOPAY_VALORPAGO_FORM -->
			<form method="post" id="frm_billstopay_valorpago" data-id_contasapagar="{id_contasapagar}">
				<div class="addon">
					<span>R$</span>
					<input
						type='number'
						id="valorpago"
						class="fill"
						placeholder='0,00'
						value={valorpago}
						min="0.01"
						max="999999.99"
						step="0.01"
						title="Valor pago da conta"
						autofocus
						required>
				</div>
			</form>
			<!-- END EXTRA_BLOCK_BILLSTOPAY_VALORPAGO_FORM -->
		</div>
	</div>
	<!-- END EXTRA_BLOCK_BILLSTOPAY_PAYMENT_DONE -->
</div>

<div>
	<label class="caption">Observação</label>
	<!-- BEGIN BLOCK_BILLSTOPAY_OBS -->
	<div class="addon container">
		<button class="billstopay_bt_obs button-field textleft fill" data-id_contasapagar="{id_contasapagar}" title="Alterar observação da conta">
			{obs}
		</button>
	</div>
	<!-- END BLOCK_BILLSTOPAY_OBS -->

	<!-- BEGIN EXTRA_BLOCK_BILLSTOPAY_OBS_FORM -->
	<form method="post" id="frm_billstopay_obs" class="fill" data-id_contasapagar="{id_contasapagar}">
		<div class="addon">
			<input
				type='text'
				id="obs"
				class="fill"
				maxlength='255'
				value="{obs}"
				title="Observação para conta."
				placeholder=''
				autofocus>
		</div>
	</form>
	<!-- END EXTRA_BLOCK_BILLSTOPAY_OBS_FORM -->
</div>
<!-- END EXTRA_BLOCK_BILLSTOPAY_POPUP_EDITION -->

<!-- BEGIN EXTRA_BLOCK_BILLSTOPAY_POPUP_NEW -->
<form method="post" id="frm_billstopay">

	<div class="flex flex-dc gap-10">

		<div class="flex gap-10">
			<div class="flex-2">
				<label class="caption">Vencimento</label>
				<div class="addon">
					<input type='date' id="vencimento" class="fill" title="Vencimento da conta." value='{data}' required autofocus>
				</div>
			</div>

			<div class="flex-2">
				<label class="caption">Valor a pagar</label>
				<div class="addon">
					<span>R$ </span>
					<input
					type='number'
					class="fill"
					id="valor"
					placeholder='0,00'
					min="0.01"
					max="999999.99"
					step="0.01"
					title="Valor a pagar da conta."
					required>
				</div>
			</div>
		</div>

		<div class="flex gap-10">
			<div>
				<label class="caption">Pago</label>
				<div class="addon">
					<span class="field flex flex-jc-center">
						<input type="checkbox" id='pago' class="fill" title="Lançar pagamento da conta com a mesma data de vencimento e valor.">
					</span>
				</div>
			</div>

			<div class="fill">
				<label class="caption">Descrição</label>
				<div class="addon">
					<input
					type='text'
					id="descricao"
					class="fill"
					maxlength='100'
					placeholder=''
					title="Descrição para identificação da conta."
					required>
				</div>
			</div>
		</div>

		<div class="flex gap-10">
			<div class="fill">
				<label class="caption">Setor</label>
				<div class="addon">
					<select id='setor' class="fill uppercase" title="Categoria da conta.">{setor}</select>
				</div>
			</div>

			<div class="flex gap-10">
				<div class="flex flex-ai-fe fill">
					<div class="fill">
						<button type="submit" class="button-blue fill" title="Registrar conta">Registrar</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>
<!-- END EXTRA_BLOCK_BILLSTOPAY_POPUP_NEW -->

<!-- BEGIN EXTRA_BLOCK_BILLSTOPAY_POPUP_PAYMENT -->
<div class="w_billstopay_payment flex flex-dc gap-10 fill">

	<div class="flex-3">
		<label class="caption">Descrição</label>
		<div class="addon container">
			<span class="field">{descricao}</span>
		</div>
	</div>

	<div class="flex gap-10 flex-4">
		<div class="flex-2">
			<label class="caption">Valor a pagar</label>
			<div class="addon container">
				<span class="field">R$ {valor_formatted}</span>
			</div>
		</div>

		<div class="flex-2">
			<label class="caption">Vencimento</label>
			<div class="addon container">
				<span class="field">{vencimento_formatted}</span>
			</div>
		</div>
	</div>

	<div class="">
		<form method="post" id="frm_billstopay_payment" class="flex flex-dc gap-10" data-id_contasapagar="{id_contasapagar}">
			<div class="flex gap-10">
				<div class="flex-1">
					<label class="caption">Data do pagamento</label>
					<div class="addon">
						<input
							type='date'
							id="datapago"
							class="fill"
							title="Data de pagamento da conta"
							value='{data}'
							required>
					</div>
				</div>

				<div class="flex gap-10 flex-1">
					<div class="flex-1">
						<label class="caption">Valor pago</label>
						<div class="addon">
							<span>R$</span>
							<input
								type='number'
								id="valorpago"
								class="fill"
								value="{valor}"
								placeholder='0,00'
								min="0.01"
								max="999999.99"
								step="0.01"
								title="Valor pago da conta"
								autofocus
								required>
						</div>
					</div>
				</div>
			</div>

			<div class="flex flex-ai-fe flex-jc-center padding-t10">
				<button type="submit" class="button-blue" title="Registrar pagamento da conta">Registrar Pagamento</button>
			</div>
		</form>
	</div>
</div>
<!-- END EXTRA_BLOCK_BILLSTOPAY_POPUP_PAYMENT -->

<div class="box-header box-container flex flex-jc-sb gap-10">

	<div class="flex gap-10">
		<i class="icon fa-solid fa-sack-dollar"></i>
		<span>Financeiro / Contas a Pagar</span>
	</div>

	<div class="flex gap-10">

		<div class="flex gap-10 non-mobile">
			<button type="button" class="button-icon billstopay_bt_filter flex flex-ai-center gap-10 button-blue" title="Filtrar contas a pagar">
				<i class="icon fa-solid fa-filter-circle-dollar"></i>
			</button>

			<button type="button" class="button-icon billstopay_bt_topay flex flex-ai-center gap-10 button-blue" title="Listar contas a pagar em aberto">
				<i class="icon fa-solid fa-file-invoice-dollar"></i>
			</button>

			<button type="button" class="button-icon billstopay_bt_show_new flex flex-ai-center gap-10 button-blue" title="Cadastrar nova conta">
				<i class="icon fa-solid fa-square-plus"></i>
			</button>
		</div>

		<div class="menu-inter flex flex-ai-center mobile">
			<button class="menu-inter-button fa-solid fa-ellipsis-vertical button-blue"></button>

			<ul>
				<li class="billstopay_bt_filter flex flex-ai-center gap-10 color-blue" title="Filtrar contas a pagar">
					<i class="icon fa-solid fa-filter-circle-dollar"></i>
					<span>Filtro</span>
				</li>

				<li class="billstopay_bt_topay flex flex-ai-center gap-10 color-blue" title="Listar contas a pagar em aberto">
					<i class="icon fa-solid fa-file-invoice-dollar"></i>
					<span>Pagamentos Pendentes</span>
				</li>

				<li class="billstopay_bt_show_new flex flex-ai-center gap-10 color-blue" title="Cadastrar nova conta">
					<i class="icon fa-solid fa-square-plus"></i>
					<span>Registrar Conta</span>
				</li>
			</ul>
		</div>
	</div>
</div>

<div class="flex-responsive gap-10">
	<div class="box-container billstopay_chart_container flex-3-col">
		<div class="window-header setor-2">
			Contas Pagas
		</div>

		<canvas id="billstopay_chart"></canvas>
	</div>

	<div class="box-container billstopay_pendingchart_container flex-3-col">
		<div class="window-header setor-2">
			Contas a Pagar
		</div>

		<canvas id="billstopay_pendingchart"></canvas>
	</div>
</div>

<div class="billstopay_container card-container flex flex-dc gap-10">

	<div class="billstopay_header box-header">
		Pagamentos Pendentes
	</div>

	<div class="billstopay_none window {billstopay_none}">

		<div class="font-size-12 textcenter" style="padding: 80px 10px;">
			Não há contas com pagamento pendende ;-)
		</div>
	</div>

	<div class="billstopay_not_found window hidden">

		<div class="font-size-12 textcenter" style="padding: 80px 10px;">
			Nenhuma conta encontrada na pesquisa.
		</div>
	</div>

	<div class="billstopay_table table tbody flex flex-dc">
		{extra_block_billstopay}

		<!-- BEGIN EXTRA_BLOCK_BILLSTOPAY_PENDING -->
		<div class="w-billstopay billstopay_{id_contasapagar} window tr flex-responsive gap-20" data-id_contasapagarsetor="{id_contasapagarsetor}" data-id_contasapagar='{id_contasapagar}' data-contasapagarsetor="{contasapagarsetor}" data-valor="{valor}" data-valorpago="{valorpago}">

			<div class="flex flex-dc flex-1">
				<label class="caption">{contasapagarsetor}</label>
				<div>{descricao}</div>
				<div>{obs}</div>
			</div>

			<div class="flex gap-20 flex-jc-sb">
				<div>
					<label class="caption">Vencimento</label>
					<div>{vencimento_formatted}</div>
				</div>

				<!-- BEGIN BLOCK_BILLSTOPAY_STATUS_PENDING -->
				<div class="w_billstopay_status_{id_contasapagar} flex flex-ai-center color-blue gap-5" style="min-width: 110px;">
					<i class="icon fa-solid fa-clock"></i>
					<span>A pagar</span>
				</div>
				<!-- END BLOCK_BILLSTOPAY_STATUS_PENDING -->
			</div>

			<div class="flex gap-20">
				<div class="flex flex-dc flex-1" style="min-width: 110px;">
					<label class="caption">Valor a pagar</label>
					<div class="font-size-12 color-blue">R$ {valor_formatted}</div>
				</div>

				<div class="menu-inter flex flex-ai-center">
					<button class="menu-inter-button fa-solid fa-ellipsis-vertical button-blue"></button>

					<ul>
						<li class="bills_to_pay_bt_edit flex flex-ai-center gap-10 color-blue" title="Editar Conta" data-id_contasapagar="{id_contasapagar}">
							<i class="icon fa-solid fa-file-pen"></i>
							<span>Editar Conta</span>
						</li>

						<li class="bills_to_pay_bt_delete flex flex-ai-center gap-10 color-red" title="Remove conta" data-id_contasapagar="{id_contasapagar}" data-id_contasapagarsetor="{id_contasapagarsetor}">
							<i class="icon fa-solid fa-trash-can"></i>
							<span>Remover Conta</span>
						</li>

						<li class="billstopay_bt_pay flex flex-ai-center gap-10 color-green" title="Remove despesa" data-id_contasapagar="{id_contasapagar}">
							<i class="icon fa-solid fa-file-invoice-dollar"></i>
							<span>Pagar Conta</span>
						</li>
					</ul>
				</div>
			</div>
		</div>
		<!-- END EXTRA_BLOCK_BILLSTOPAY_PENDING -->

		<!-- BEGIN EXTRA_BLOCK_BILLSTOPAY_PAID -->
		<div class="w-billstopay billstopay_{id_contasapagar} window tr flex-responsive gap-20" data-id_contasapagarsetor="{id_contasapagarsetor}" data-id_contasapagar='{id_contasapagar}' data-contasapagarsetor="{contasapagarsetor}" data-valor="{valor}" data-valorpago="{valorpago}">

			<div class="flex flex-dc flex-1">
				<label class="caption">{contasapagarsetor}</label>
				<div>{descricao}</div>
				<div>{obs}</div>
			</div>

			<div class="flex gap-20 flex-jc-sb">
				<div>
					<label class="caption">Vencimento</label>
					<div>{vencimento_formatted}</div>
				</div>

				<!-- BEGIN BLOCK_BILLSTOPAY_STATUS_PAID -->
				<div class="w_billstopay_status_{id_contasapagar} flex flex-ai-center color-green gap-5" style="min-width: 110px;">
					<i class="icon fa-solid fa-circle-check"></i>
					<span>Pago</span>
				</div>
				<!-- END BLOCK_BILLSTOPAY_STATUS_PAID -->
			</div>

			<div class="flex gap-20">
				<div class="flex flex-dc flex-1" style="min-width: 110px;">
					<label class="caption">Valor Pago</label>
					<div class="font-size-12 color-blue">R$ {valorpago_formatted}</div>
				</div>

				<div class="menu-inter flex flex-ai-center">
					<button class="menu-inter-button fa-solid fa-ellipsis-vertical button-blue"></button>

					<ul>
						<li class="bills_to_pay_bt_edit flex flex-ai-center gap-10 color-blue" title="Editar conta" data-id_contasapagar="{id_contasapagar}">
							<i class="icon fa-solid fa-file-pen"></i>
							<span>Editar Conta</span>
						</li>

						<li class="bills_to_pay_bt_delete flex flex-ai-center gap-10 color-red" title="Remover conta" data-id_contasapagar="{id_contasapagar}" data-id_contasapagarsetor="{id_contasapagarsetor}">
							<i class="icon fa-solid fa-trash-can"></i>
							<span>Remover Conta</span>
						</li>
					</ul>
				</div>
			</div>
		</div>
		<!-- END EXTRA_BLOCK_BILLSTOPAY_PAID -->

		<!-- BEGIN EXTRA_BLOCK_BILLSTOPAY_OVERDUE -->
		<div class="w-billstopay billstopay_{id_contasapagar} window tr flex-responsive gap-20" data-id_contasapagarsetor="{id_contasapagarsetor}" data-id_contasapagar='{id_contasapagar}' data-contasapagarsetor="{contasapagarsetor}" data-valor="{valor}" data-valorpago="{valorpago}">

			<div class="flex flex-dc flex-1">
				<label class="caption">{contasapagarsetor}</label>
				<div>{descricao}</div>
				<div>{obs}</div>
			</div>

			<div class="flex gap-20 flex-jc-sb">
				<div>
					<label class="caption">Vencimento</label>
					<div>{vencimento_formatted}</div>
				</div>

				<!-- BEGIN BLOCK_BILLSTOPAY_STATUS_OVERDUE -->
				<div class="w_billstopay_status_{id_contasapagar} flex flex-ai-center color-red gap-5" style="min-width: 110px;">
					<i class="icon fa-solid fa-stopwatch"></i>
					<div class="pseudo-button">Vencida</div>
				</div>
				<!-- END BLOCK_BILLSTOPAY_STATUS_OVERDUE -->
			</div>

			<div class="flex gap-20">
				<div class="flex flex-dc flex-1" style="min-width: 110px;">
					<label class="caption">Valor a pagar</label>
					<div class="font-size-12 color-blue">R$ {valor_formatted}</div>
				</div>

				<div class="menu-inter flex flex-ai-center">
					<button class="menu-inter-button fa-solid fa-ellipsis-vertical button-blue"></button>

					<ul>
						<li class="bills_to_pay_bt_edit flex flex-ai-center gap-10 color-blue" title="Editar conta" data-id_contasapagar="{id_contasapagar}">
							<i class="icon fa-solid fa-file-pen"></i>
							<span>Editar Conta</span>
						</li>

						<li class="bills_to_pay_bt_delete flex flex-ai-center gap-10 color-red" title="Remove despesa" data-id_contasapagar="{id_contasapagar}" data-id_contasapagarsetor="{id_contasapagarsetor}">
							<i class="icon fa-solid fa-trash-can"></i>
							<span>Remover Conta</span>
						</li>

						<li class="billstopay_bt_pay flex flex-ai-center gap-10 color-green" title="Remove despesa" data-id_contasapagar="{id_contasapagar}">
							<i class="icon fa-solid fa-file-invoice-dollar"></i>
							<span>Pagar Conta</span>
						</li>
					</ul>
				</div>
			</div>
		</div>
		<!-- END EXTRA_BLOCK_BILLSTOPAY_OVERDUE -->
	</div>
</div>

<div class="footer-popup flex-jc-fe mobile">
	<div class="popup-menu">
		<button class="popup-main-button fa-solid fa-ellipsis-vertical button-pink"></button>

		<ul>
			<li class="billstopay_bt_filter flex flex-ai-center gap-10 color-blue" title="Filtrar contas a pagar">
				<i class="icon fa-solid fa-filter-circle-dollar"></i>
				<span>Filtro</span>
			</li>

			<li class="billstopay_bt_topay flex flex-ai-center gap-10 color-blue" title="Listar contas a pagar em aberto">
				<i class="icon fa-solid fa-file-invoice-dollar"></i>
				<span>Pagamentos Pendentes</span>
			</li>

			<li class="billstopay_bt_show_new flex flex-ai-center gap-10 color-blue" title="Cadastrar nova conta">
				<i class="icon fa-solid fa-square-plus"></i>
				<span>Registrar Conta</span>
			</li>
		</ul>
	</div>
</div>
<!-- END BLOCK_PAGE -->