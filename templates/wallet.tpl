<!-- BEGIN BLOCK_PAGE -->

<!-- BEGIN EXTRA_BLOCK_POPUP_WALLET_FILTER -->
<form method="post" id="frm_report_wallet" data-id_wallet="{id_wallet}" class="fill flex flex-dc gap-10">

	<div>
		<label class="caption">Mês</label>
		<div class="addon">
			<input
				type='month'
				class="select_dataini fill"
				value='{datestart}'
				title="Mês ou mês inicial"
				required
				autofocus>
		</div>
	</div>

	<div>
		<label class="caption">até</label>
		<div class="addon">
			<span class="flex">
				<input type="checkbox" class='check_intervalo' {dateend_sel} title="Ativa busca de data de vencimento por intervalo">
			</span>
			<input
				type='month'
				class="select_datafim fill"
				min='{datestart}'
				value='{dateend}'
				title="Mês final"
				required
				{select_datafim}>
		</div>
	</div>

	<div>
		<label class="caption">Setor</label>
		<div class="addon">
			<span class="flex">
				<input type="checkbox" id='setor' title="Ativa filtro por setor" {sector_sel}>
			</span>
			<select id="id_walletsector" class="select_id_walletsector fill" {select_id_walletsector}>
				{extra_block_walletdespesa_sector_option}
			</select>
		</div>
	</div>

	<div>
		<label class="caption">Espécie</label>
		<div class="addon">
			<span class="flex">
				<input type="checkbox" id='especie' title="Ativa filtro por espécie" {cashtype_sel}>
			</span>
			<select id="id_walletcashtype" class="select_id_walletcashtype fill" {select_id_walletcashtype}>
				{extra_block_walletdespesa_cashtype_option}
			</select>
		</div>
	</div>

	<div class="flex flex-ai-fe flex-jc-center gap-10 padding-t10">
		<button type="submit" class="button-blue" title="Procurar vendas totalizadas">Aplicar</button>
	</div>
</form>
<!-- END EXTRA_BLOCK_POPUP_WALLET_FILTER -->

<!-- BEGIN EXTRA_BLOCK_POPUP_WALLETDESPESA_NEW -->
<form method="post" id="frm_walletdespesa_new" data-id_wallet="{id_wallet}">

	<div class="flex flex-dc gap-10">

		<div class="box-header gap-10">
			<i class="icon fa-solid fa-wallet"></i>
			{wallet}
		</div>

		<div class="flex-1">
			<label class="caption">Setor</label>
			<div class="flex gap-10">
				<select id="frm_walletdespesanew_walletsector" class="select_id_walletsector fill" required>
					{extra_block_walletdespesa_sector_option}
					<!-- BEGIN EXTRA_BLOCK_WALLETDESPESA_SECTOR_OPTION -->
					<option value="{id_walletsector}" {selected}>{walletsector}</option>
					<!-- END EXTRA_BLOCK_WALLETDESPESA_SECTOR_OPTION -->
				</select>

				<div class="flex flex-jc-fe flex-ai-fe">
					<div class="menu-inter">
						<button type="button" class="menu-inter-button fa-solid fa-ellipsis-vertical button-blue"></button>

						<ul>
							<li class="walletsector_bt_show_new flex flex-ai-center gap-10 color-blue" data-id_wallet="{id_wallet}" title="Cadastrar novo setor">
								<i class="icon fa-solid fa-square-plus"></i>
								<span>Cadastrar Setor</span>
							</li>

							<li class="walletsector_bt_manager flex flex-ai-center gap-10 color-blue" data-id_wallet="{id_wallet}" title="Gerenciar setores">
								<i class="icon fa-solid fa-gear"></i>
								<span>Gerenciar Setores</span>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>

		<div class=" flex gap-10 flex-3">

			<div class="flex-1">
				<label class="caption">Parcelado</label>
				<div class="addon gap-10">
					<span class="field flex flex-jc-center">
						<input type="checkbox" id='walletdespesanew_parcelado' class="fill" title="Ativar valor parcelado.">
					</span>

					<select id="frm_walletdespesanew_parcelado" class="fill" disabled required>
						{walletdespesanew_parcelado}
						<!-- BEGIN EXTRA_BLOCK_WALLETDESPESA_PARCELADO_OPTION -->
						<option value="{valor}">{valor_desc}</option>
						<!-- END EXTRA_BLOCK_WALLETDESPESA_PARCELADO_OPTION -->
					</select>
				</div>
			</div>

			<div class="flex-2">
				<label class="caption">Espécie</label>
				<div class="flex gap-10">
					<select id="frm_walletdespesanew_walletcashtype" class="select_id_walletcashtype fill" required>
						{extra_block_walletdespesa_cashtype_option}
						<!-- BEGIN EXTRA_BLOCK_WALLETDESPESA_CASHTYPE_OPTION -->
						<option value="{id_walletcashtype}" {selected}>{walletcashtype}</option>
						<!-- END EXTRA_BLOCK_WALLETDESPESA_CASHTYPE_OPTION -->
					</select>

					<div class="flex flex-jc-fe flex-ai-fe">
						<div class="menu-inter">
							<button type="button" class="menu-inter-button fa-solid fa-ellipsis-vertical button-blue"></button>

							<ul>
								<li class="walletcashtype_bt_show_new flex flex-ai-center gap-10 color-blue" data-id_wallet="{id_wallet}" title="Cadastrar nova espécie">
									<i class="icon fa-solid fa-square-plus"></i>
									<span>Cadastrar Espécie</span>
								</li>

								<li class="walletcashtype_bt_manager flex flex-ai-center gap-10 color-blue" data-id_wallet="{id_wallet}" title="Gerenciar espécies">
									<i class="icon fa-solid fa-gear"></i>
									<span>Gerenciar Espécies</span>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="flex-2 flex gap-10">
			<div class="flex-1">
				<label class="caption">Vencimento</label>
				<div>
					<input
					type="date"
					id="data"
					class="fill"
					value="{data}"
					required>
				</div>
			</div>

			<div class="flex-1">
				<label class="caption">Valor total</label>
				<div class="addon">
					<span>R$</span>
					<input
						type="number"
						id="valor"
						step="0.01"
						min="0.01"
						class="fill"
						placeholder=""
						autocomplete="off"
						autofocus
						required>
				</div>
			</div>

			<div>
				<label class="caption">Pago</label>
				<div class="addon">
					<span class="field flex flex-jc-center">
						<input type="checkbox" id='pago' class="fill" title="Lançar pagamento da conta com a mesma data de vencimento e valor.">
					</span>
				</div>
			</div>
		</div>

		<div class="flex flex-11 gap-10">

			<div class="flex-6">
				<label class="caption">Descrição</label>
				<div>
					<input
						type="text"
						id="descricao"
						class="fill"
						placeholder=""
						maxlength="255"
						autocomplete="off"
						required>
				</div>
			</div>

			<div class="flex flex-ai-fe">
				<button type="submit" class="button-blue fill" title="Registrar despesa">Registrar</button>
			</div>
		</div>
	</div>
</form>
<!-- END EXTRA_BLOCK_POPUP_WALLETDESPESA_NEW -->

<!-- BEGIN EXTRA_BLOCK_WALLETDESPESA_PAYMENT -->
<div class="flex flex-dc gap-10 fill">

	<div class="flex-3">
		<label class="caption">{walletsector}</label>
		<div class="addon container">
			<span class="field">{walletdespesa} <span class="font-size-09 color-gray">{obs_despesa}</span></span>
		</div>
	</div>

	<div class="flex gap-10 flex-4">
		<div class="flex-2">
			<label class="caption">Vencimento</label>
			<div class="addon container">
				<span class="field">{data_formatted}</span>
			</div>
		</div>

		<div class="flex-2">
			<label class="caption">Valor a pagar</label>
			<div class="addon container">
				<span class="field">R$ {valor_formatted}</span>
			</div>
		</div>
	</div>

	<div class="">
		<form method="post" id="frm_walletdespesa_payment" class="flex flex-dc gap-10" data-id_wallet="{id_wallet}" data-id_walletdespesa="{id_walletdespesa}">
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
<!-- END EXTRA_BLOCK_WALLETDESPESA_PAYMENT -->

<!-- BEGIN EXTRA_BLOCK_POPUP_WALLETRECEITA_NEW -->
<form method="post" id="frm_walletreceita_new" data-id_wallet="{id_wallet}">

	<div class="flex flex-dc gap-10">

		<div class="box-header gap-10">
			<i class="icon fa-solid fa-wallet"></i>
			{wallet}
		</div>

		<div class="flex-1">
			<label class="caption">Data</label>
			<div>
				<input
				type="date"
				id="data"
				class="fill"
				value="{data}"
				required>
			</div>
		</div>

		<div class="flex-2">
			<label class="caption">Valor</label>
			<div class="addon">
				<span>R$</span>
				<input
					type="number"
					id="valor"
					step="0.01"
					min="0.01"
					class="fill"
					placeholder=""
					autocomplete="off"
					autofocus
					required>
			</div>
		</div>

		<div class="flex flex-11 gap-10">

			<div class="flex-6">
				<label class="caption">Descrição</label>
				<div>
					<input
						type="text"
						id="descricao"
						class="fill"
						placeholder=""
						maxlength="255"
						autocomplete="off"
						required>
				</div>
			</div>

			<div class="flex flex-ai-fe">
				<button type="submit" class="button-blue fill" title="Registrar receita">Registrar</button>
			</div>
		</div>
	</div>
</form>
<!-- END EXTRA_BLOCK_POPUP_WALLETRECEITA_NEW -->

<!-- BEGIN EXTRA_BLOCK_POPUP_WALLETDESPESA_SECTOR -->
<div class="flex flex-dc">

	<div class="box-header">Setor</div>

	<div class="walletsector_notfound window {walletsector_notfound}" style="padding: 40px 10px;">
		Nenhum setor cadastrado.
	</div>

	<div class="walletsector_container flex flex-dc table tbody">

		{extra_block_walletdespesa_sector}

		<!-- BEGIN EXTRA_BLOCK_WALLETDESPESA_SECTOR -->
		<div class="w-walletsector window tr flex flex-dc gap-10" data-id_walletsector='{id_walletsector}'>

			<div class="flex gap-10">

				<div class="flex-15">
					<!-- <label class="caption">Descrição</label> -->
					<div class="addon">
						<!-- BEGIN BLOCK_SECTOR -->
						<button class="walletsector_bt_walletsector button-field textleft fill" data-id_walletsector="{id_walletsector}" title="Alterar descrição do setor">
							{walletsector}

						</button>
						<!-- END BLOCK_SECTOR -->

						<!-- BEGIN EXTRA_BLOCK_SECTOR_FORM -->
						<form method="post" id="frm_walletsector_walletsector" class="fill" data-id_walletsector='{id_walletsector}'>
							<input
								type='text'
								id='walletsector'
								class="fill"
								placeholder=''
								value='{walletsector}'
								maxlength='50'
								required
								autofocus>
						</form>
						<!-- END EXTRA_BLOCK_SECTOR_FORM -->
					</div>
				</div>

				<div class="flex flex-ai-fe">
					<button class='walletsector_bt_del button-icon button-red fa-solid fa-trash-can' title="Remover setor"></button>
				</div>
			</div>
		</div>
		<!-- END EXTRA_BLOCK_WALLETDESPESA_SECTOR -->
	</div>

	<div class="flex flex-jc-fe padding-t10">
		<button type="button" class="walletsector_bt_show_new button-blue" data-id_wallet="{id_wallet}">Adicionar setor</button>
	</div>
</div>
<!-- END EXTRA_BLOCK_POPUP_WALLETDESPESA_SECTOR -->

<!-- BEGIN EXTRA_BLOCK_POPUP_WALLETDESPESA_SECTOR_NEW -->
<form method="post" id="frm_walletsector" data-id_wallet="{id_wallet}" data-source="{source}" class="flex gap-10">
	<div class="fill">
		<label class="caption">Setor</label>
		<div>
			<input
				type="text"
				id="walletsector"
				maxlength="50"
				required
				placeholder=""
				autocomplete="off"
				class="smart-search fill"
				autofocus>
		</div>
	</div>

	<div class="flex flex-ai-fe">
		<button type="submit" class="button-blue fill">Cadastrar</button>
	</div>
</form>
<!-- END EXTRA_BLOCK_POPUP_WALLETDESPESA_SECTOR_NEW -->

<!-- BEGIN EXTRA_BLOCK_POPUP_WALLETDESPESA_CASHTYPE -->
<div class="flex flex-dc gap-10">

	<div class="box-header">Espécie</div>

	<div class="walletcashtype_notfound window {notfound}" style="padding: 40px 10px;">
		Nenhum espécie cadastrado.
	</div>

	<div class="walletcashtype_container flex flex-dc table tbody">

		{extra_block_walletdespesa_cashtype}
		<!-- BEGIN EXTRA_BLOCK_WALLETDESPESA_CASHTYPE -->
		<div class="w-walletcashtype window tr flex flex-dc gap-10" data-id_walletcashtype='{id_walletcashtype}'>

			<div class="flex gap-10">

				<div class="flex-15">
					<!-- <label class="caption">Descrição</label> -->
					<div class="addon">
						<!-- BEGIN BLOCK_CASHTYPE -->
						<button class="walletcashtype_bt_walletcashtype button-field textleft fill" data-id_walletcashtype="{id_walletcashtype}" title="Alterar descrição do espécie">
							{walletcashtype}

						</button>
						<!-- END BLOCK_CASHTYPE -->

						<!-- BEGIN EXTRA_BLOCK_CASHTYPE_FORM -->
						<form method="post" id="frm_walletcashtype_walletcashtype" class="fill" data-id_walletcashtype='{id_walletcashtype}'>
							<input
								type='text'
								id='walletcashtype'
								class="fill"
								placeholder=''
								value='{walletcashtype}'
								maxlength='50'
								required
								autofocus>
						</form>
						<!-- END EXTRA_BLOCK_CASHTYPE_FORM -->
					</div>
				</div>

				<div class="flex flex-ai-fe">
					<button class='walletcashtype_bt_del button-icon button-red fa-solid fa-trash-can' title="Remover espécie"></button>
				</div>
			</div>
		</div>
		<!-- END EXTRA_BLOCK_WALLETDESPESA_CASHTYPE -->
	</div>

	<div class="flex flex-jc-fe">
		<button type="button" class="walletcashtype_bt_show_new button-blue" data-id_wallet="{id_wallet}" title="Cadastrar nova espécie">Cadastrar</button>
	</div>
</div>
<!-- END EXTRA_BLOCK_POPUP_WALLETDESPESA_CASHTYPE -->

<!-- BEGIN EXTRA_BLOCK_POPUP_WALLETDESPESA_CASHTYPE_NEW -->
<form method="post" id="frm_walletcashtype" data-id_wallet="{id_wallet}" class="flex gap-10">
	<div class="fill">
		<label class="caption">Nova Espécie</label>
		<div>
			<input
				type="text"
				id="walletcashtype"
				maxlength="50"
				required
				placeholder=""
				autocomplete="off"
				class="smart-search fill"
				autofocus>
		</div>
	</div>

	<div class="flex flex-ai-fe">
		<button type="submit" class="button-blue fill">Cadastrar</button>
	</div>
</form>
<!-- END EXTRA_BLOCK_POPUP_WALLETDESPESA_CASHTYPE_NEW -->

<!-- BEGIN EXTRA_BLOCK_WALLETDESPESA_EDIT -->
<div class="flex flex-dc gap-10">

	<div class="box-header gap-10">
		<i class="icon fa-solid fa-wallet"></i>
		{wallet}
	</div>

	<div>
		<label class="caption">Setor</label>
		<div class="addon container">
			<!-- BEGIN BLOCK_WALLETDESPESA_WALLETSECTOR -->
			<button class="walletdespesa_bt_walletsector button-field textleft fill" data-id_walletdespesa="{id_walletdespesa}" title="Editar setor da despesa">
				{walletsector}

			</button>
			<!-- END BLOCK_WALLETDESPESA_WALLETSECTOR -->

			<!-- BEGIN EXTRA_BLOCK_WALLETDESPESA_FORM_WALLETSECTOR -->
			<form method="post" id="frm_walletdespesa_walletsector" class="flex-1" data-id_walletdespesa="{id_walletdespesa}" data-id_wallet="{id_wallet}">

				<select class="fill" id="id_walletsector" required autofocus>{extra_block_walletdespesa_sector_option}</select>
			</form>
			<!-- END EXTRA_BLOCK_WALLETDESPESA_FORM_WALLETSECTOR -->
		</div>
	</div>

	<div>
		<label class="caption">Descrição</label>
		<div class="addon">
			<!-- BEGIN BLOCK_WALLETDESPESA_WALLETDESPESA -->
			<button class="walletdespesa_bt_walletdespesa button-field textleft fill" data-id_walletdespesa="{id_walletdespesa}" data-valor="{valor}" title="Editar descrição da despesa">
				{walletdespesa} <span class="font-size-09 color-gray">{obs_despesa}</span>
			</button>
			<!-- END BLOCK_WALLETDESPESA_WALLETDESPESA -->

			<!-- BEGIN EXTRA_BLOCK_WALLETDESPESA_FORM_WALLETDESPESA -->
			<form method="post" id="frm_walletdespesa_walletdespesa" class="fill" data-id_walletdespesa="{id_walletdespesa}" data-id_wallet="{id_wallet}">

				<input
					type="text"
					id="walletdespesa"
					class="fill"
					maxlength="50"
					required
					value="{walletdespesa}"
					autofocus>
			</form>
			<!-- END EXTRA_BLOCK_WALLETDESPESA_FORM_WALLETDESPESA -->
		</div>
	</div>

	<div>
		<label class="caption">Espécie</label>
		<div class="addon container">
			<!-- BEGIN BLOCK_WALLETDESPESA_WALLETCASHTYPE -->
			<button class="walletdespesa_bt_walletcashtype button-field textleft fill" data-id_walletdespesa="{id_walletdespesa}" title="Editar espécie da despesa">
				{walletcashtype}

			</button>
			<!-- END BLOCK_WALLETDESPESA_WALLETCASHTYPE -->

			<!-- BEGIN EXTRA_BLOCK_WALLETDESPESA_FORM_WALLETCASHTYPE -->
			<form method="post" id="frm_walletdespesa_walletcashtype" class="fill" data-id_walletdespesa="{id_walletdespesa}" data-id_wallet="{id_wallet}">

				<select id="id_walletcashtype" class="select_id_walletcashtype fill" required autofocus>{extra_block_walletdespesa_cashtype_option}</select>
			</form>
			<!-- END EXTRA_BLOCK_WALLETDESPESA_FORM_WALLETCASHTYPE -->
		</div>
	</div>

	<div class="flex gap-10">
		<div class="flex-1">
			<label class="caption">Vencimento</label>
			<div class="addon">
				<!-- BEGIN BLOCK_WALLETDESPESA_DATA -->
				<button class="walletdespesa_bt_data button-field textleft fill" data-id_walletdespesa="{id_walletdespesa}" title="Editar data de vencimento da despesa">
					{data_formatted}

				</button>
				<!-- END BLOCK_WALLETDESPESA_DATA -->

				<!-- BEGIN EXTRA_BLOCK_WALLETDESPESA_FORM_DATA -->
				<form method="post" id="frm_walletdespesa_data" class="fill" data-id_walletdespesa="{id_walletdespesa}" data-id_wallet="{id_wallet}">

					<input type="date" id="data" class="fill" required autofocus value="{data}">
				</form>
				<!-- END EXTRA_BLOCK_WALLETDESPESA_FORM_DATA -->
			</div>
		</div>

		<div class="flex-1">
			<label class="caption color-blue">Valor</label>
			<div class="addon color-blue">
				<span>R$</span>
				<!-- BEGIN BLOCK_WALLETDESPESA_VALOR -->
				<button class="walletdespesa_bt_valor button-field textleft fill color-blue" data-id_walletdespesa="{id_walletdespesa}" data-valor="{valor}" title="Editar valor da despesa">
					{valor_formatted}

				</button>
				<!-- END BLOCK_WALLETDESPESA_VALOR -->

				<!-- BEGIN EXTRA_BLOCK_WALLETDESPESA_FORM_VALOR -->
				<form method="post" id="frm_walletdespesa_valor" class="flex-1" data-id_wallet="{id_wallet}" data-id_walletdespesa="{id_walletdespesa}">

					<input
						type="number"
						id="valor"
						class="fill"
						min="0.01"
						max="999999.99"
						step="0.01"
						required
						value="{valor}"
						autofocus>
				</form>
				<!-- END EXTRA_BLOCK_WALLETDESPESA_FORM_VALOR -->
			</div>
		</div>
	</div>

	{extra_block_walletdespesa_payment_edit}
	<!-- BEGIN EXTRA_BLOCK_WALLETDESPESA_PAYMENT_EDIT -->
	<div class="flex gap-10">
		<div class="flex-1">
			<label class="caption">Pagamento</label>
			<div class="addon">
				<!-- BEGIN BLOCK_WALLETDESPESA_DATAPAGO -->
				<button class="walletdespesa_bt_datapago button-field textleft fill" data-id_walletdespesa="{id_walletdespesa}" title="Editar data de vencimento da despesa">
					{datapago_formatted}

				</button>
				<!-- END BLOCK_WALLETDESPESA_DATAPAGO -->

				<!-- BEGIN EXTRA_BLOCK_WALLETDESPESA_FORM_DATAPAGO -->
				<form method="post" id="frm_walletdespesa_datapago" class="fill" data-id_walletdespesa="{id_walletdespesa}" data-id_wallet="{id_wallet}">

					<input type="date" id="datapago" class="fill" required autofocus value="{datapago}">
				</form>
				<!-- END EXTRA_BLOCK_WALLETDESPESA_FORM_DATAPAGO -->
			</div>
		</div>

		<div class="flex-1">
			<label class="caption color-blue">Valor pago</label>
			<div class="addon color-blue">
				<span>R$</span>
				<!-- BEGIN BLOCK_WALLETDESPESA_VALORPAGO -->
				<button class="walletdespesa_bt_valorpago button-field textleft fill color-blue" data-id_walletdespesa="{id_walletdespesa}" data-valor="{valor}" title="Editar valor da despesa">
					{valorpago_formatted}
				</button>
				<!-- END BLOCK_WALLETDESPESA_VALORPAGO -->

				<!-- BEGIN EXTRA_BLOCK_WALLETDESPESA_FORM_VALORPAGO -->
				<form method="post" id="frm_walletdespesa_valorpago" class="flex-1" data-id_wallet="{id_wallet}" data-id_walletdespesa="{id_walletdespesa}">

					<input
						type="number"
						id="valorpago"
						class="fill"
						min="0.01"
						max="999999.99"
						step="0.01"
						required
						value="{valorpago}"
						autofocus>
				</form>
				<!-- END EXTRA_BLOCK_WALLETDESPESA_FORM_VALORPAGO -->
			</div>
		</div>
	</div>
	<!-- END EXTRA_BLOCK_WALLETDESPESA_PAYMENT_EDIT -->

	<div>
		<label class="caption">Cadastrado por</label>
		<div class="addon">
			<span class="entity_{id_entidade}_nome">{nome}</span>
		</div>
	</div>
</div>
<!-- END EXTRA_BLOCK_WALLETDESPESA_EDIT -->

<!-- BEGIN EXTRA_BLOCK_WALLETRECEITA_EDIT -->
<div class="flex flex-dc gap-10">

	<div class="box-header gap-10">
		<i class="icon fa-solid fa-wallet"></i>
		{wallet}
	</div>

	<div>
		<label class="caption">Data</label>
		<div class="addon">
			<!-- BEGIN BLOCK_WALLETRECEITA_DATA -->
			<button class="walletreceita_bt_data button-field textleft fill" data-id_walletreceita="{id_walletreceita}" title="Editar data da receita">
				{data_formatted}

			</button>
			<!-- END BLOCK_WALLETRECEITA_DATA -->

			<!-- BEGIN EXTRA_BLOCK_WALLETRECEITA_FORM_DATA -->
			<form method="post" id="frm_walletreceita_data" class="fill" data-id_walletreceita="{id_walletreceita}" data-id_wallet="{id_wallet}">

				<input type="date" id="data" class="fill" required autofocus value="{data}">
			</form>
			<!-- END EXTRA_BLOCK_WALLETRECEITA_FORM_DATA -->
		</div>
	</div>

	<div>
		<label class="caption">Descrição</label>
		<div class="addon">
			<!-- BEGIN BLOCK_WALLETRECEITA_WALLETRECEITA -->
			<button class="walletreceita_bt_walletreceita button-field textleft fill" data-id_walletreceita="{id_walletreceita}" data-valor="{valor}" title="Editar descrição da receita">
				{walletreceita}

			</button>
			<!-- END BLOCK_WALLETRECEITA_WALLETRECEITA -->

			<!-- BEGIN EXTRA_BLOCK_WALLETRECEITA_FORM_WALLETRECEITA -->
			<form method="post" id="frm_walletreceita_walletreceita" class="fill" data-id_walletreceita="{id_walletreceita}" data-id_wallet="{id_wallet}">

				<input
					type="text"
					id="walletreceita"
					class="fill"
					maxlength="50"
					required
					value="{walletreceita}"
					autofocus>
			</form>
			<!-- END EXTRA_BLOCK_WALLETRECEITA_FORM_WALLETRECEITA -->
		</div>
	</div>

	<div>
		<label class="caption color-blue">Valor</label>
		<div class="addon color-blue">
			<span>R$</span>
			<!-- BEGIN BLOCK_WALLETRECEITA_VALOR -->
			<button class="walletreceita_bt_valor button-field textleft fill color-blue" data-id_walletreceita="{id_walletreceita}" data-valor="{valor}" title="Editar valor da receita">
				{valor_formatted}

			</button>
			<!-- END BLOCK_WALLETRECEITA_VALOR -->

			<!-- BEGIN EXTRA_BLOCK_WALLETRECEITA_FORM_VALOR -->
			<form method="post" id="frm_walletreceita_valor" class="flex-1" data-id_wallet="{id_wallet}" data-id_walletreceita="{id_walletreceita}">

				<input
					type="number"
					id="valor"
					class="fill"
					min="0.01"
					max="999999.99"
					step="0.01"
					required
					value="{valor}"
					autofocus>
			</form>
			<!-- END EXTRA_BLOCK_WALLETRECEITA_FORM_VALOR -->
		</div>
	</div>

	<div>
		<label class="caption">Cadastrado por</label>
		<div class="addon">
			<span class="entity_{id_entidade}_nome">{nome}</span>
		</div>
	</div>
</div>
<!-- END EXTRA_BLOCK_WALLETRECEITA_EDIT -->

<div class="box-container box-header flex flex-jc-sb gap-10">
	<div class="flex gap-10">
		<i class="icon fa-solid fa-wallet"></i>
		{wallet}
	</div>

	<div class="flex gap-10 non-mobile">
		<button class="wallets_bt_show button-icon button-blue" title="Exibir Carteiras">
			<i class="fa-solid fa-wallet"></i>
		</button>

		<button class="expense_bt_filter button-icon button-blue" title="Aplicar Filtro" data-id_wallet="{id_wallet}">
			<i class="fa-solid fa-filter-circle-dollar"></i>
		</button>

		<button class="walletreceita_bt_new button-icon button-green" data-id_wallet="{id_wallet}" title="Registrar receita">
			<i class="fa-solid fa-hand-holding-dollar"></i>
		</button>

		<button class="walletdespesa_bt_new button-icon button-blue" data-id_wallet="{id_wallet}" title="Registrar despesa">
			<i class="icon fa-solid fa-square-plus"></i>
		</button>
	</div>
</div>

<div class="walletfilter_description section-header flex flex-wrap gap-10">
	{walletfilter_description}
	<!-- BEGIN EXTRA_BLOCK_WALLET_FILTER_01 -->
	<button type="button" class="wallet_bt_filter01 filter button-blue font-size-09 flex flex-ai-center flex-jc-sb gap-10" data-id_wallet="{id_wallet}" title="Remover filtro">
		<span>{filter_01}</span>
		<i class="fa-solid fa-xmark"></i>
	</button>
	<!-- END EXTRA_BLOCK_WALLET_FILTER_01 -->
	<!-- BEGIN EXTRA_BLOCK_WALLET_FILTER_02 -->
	<button type="button" class="wallet_bt_filter02 filter button-blue font-size-09 flex flex-ai-center flex-jc-sb gap-10" data-id_wallet="{id_wallet}" title="Remover filtro">
		<span>{filter_02}</span>
		<i class="fa-solid fa-xmark"></i>
	</button>
	<!-- END EXTRA_BLOCK_WALLET_FILTER_02 -->
	<!-- BEGIN EXTRA_BLOCK_WALLET_FILTER_03 -->
	<button type="button" class="wallet_bt_filter03 filter button-blue font-size-09 flex flex-ai-center flex-jc-sb gap-10" data-id_wallet="{id_wallet}" title="Remover filtro">
		<span>{filter_03}</span>
		<i class="fa-solid fa-xmark"></i>
	</button>
	<!-- END EXTRA_BLOCK_WALLET_FILTER_03 -->
</div>

<div class="flex flex-wrap gap-10">
	<div class="wallet_resume_container box-container flex-3-col">
		{extra_block_wallet_resume}

		<!-- BEGIN EXTRA_BLOCK_WALLET_RESUME -->
		<div class="flex flex-dc flex-jc-sb gap-10 fill-height">
			<div class="flex flex-dc gap-10">
				<div class="box-header margin-b10">
					Resumo
				</div>

				<div class="flex flex-jc-sb gap-10">
					<div>Receitas</div>
					<div class="font-size-12">
						<span class="color-green">R$ <span>{receita_formatted}</span></span>
					</div>
				</div>

				<div class="flex flex-jc-sb gap-10">
					<div>Despesas</div>
					<div class="font-size-12">
						<span class="color-red">R$ <span>{despesa_formatted}</span></span>
					</div>
				</div>

				<div class="flex flex-jc-sb gap-10">
					<div>Saldo</div>
					<div class="wallet_{id_wallet}_saldo font-size-12">
						{wallet_saldo}
						<!-- BEGIN EXTRA_BLOCK_WALLET_POSITIVESALDO -->
						<span class="color-green">R$ {saldo_formatted}</span>
						<!-- END EXTRA_BLOCK_WALLET_POSITIVESALDO -->
						<!-- BEGIN EXTRA_BLOCK_WALLET_NEGATIVESALDO -->
						<span class="color-red">R$ {saldo_formatted}</span>
						<!-- END EXTRA_BLOCK_WALLET_NEGATIVESALDO -->
					</div>
				</div>
			</div>

			<div class="section-footer flex flex-dc gap-10">
				<div class="flex flex-jc-sb gap-10">
					<div class="color-blue">Saldo da Carteira</div>
					<div class="wallet_{id_wallet}_saldototal font-size-12">
						{wallet_saldototal}
						<!-- BEGIN EXTRA_BLOCK_WALLET_POSITIVESALDOTOTAL -->
						<span class="color-green">R$ {saldototal_formatted}</span>
						<!-- END EXTRA_BLOCK_WALLET_POSITIVESALDOTOTAL -->
						<!-- BEGIN EXTRA_BLOCK_WALLET_NEGATIVESALDOTOTAL -->
						<span class="color-red">R$ {saldototal_formatted}</span>
						<!-- END EXTRA_BLOCK_WALLET_NEGATIVESALDOTOTAL -->
					</div>
				</div>

				<div class="flex flex-jc-sb gap-10">
					<div>Despesas Futuras</div>
					<div class="font-size-12">
						<span class="color-red">R$ <span>{despesafutura_formatted}</span></span>
					</div>
				</div>
			</div>
		</div>
		<!-- END EXTRA_BLOCK_WALLET_RESUME -->
	</div>

	<div class="wallet_expense_chart_container box-container flex-3-col">
		<div class="box-header flex flex-jc-sb gap-10">
			<div>Despesas</div>
		</div>

		<canvas id="wallet_expense_chart"></canvas>
	</div>

	<div class="wallet_receita_chart_container box-container flex-3-col">
		<div class="box-header">
			Receitas
		</div>

		<canvas id="wallet_receita_chart"></canvas>
	</div>
</div>

<div class="walletdespesa_container box-container flex flex-dc window">

	{extra_block_wallet_expense_container}

	<!-- BEGIN EXTRA_BLOCK_WALLET_EXPENSE_CONTAINER -->
	<div class="flex gap-10">
		<div class="box-header flex-1 flex flex-jc-sb gap-10">
			<div>Despesas</div>
			<div class="font-size-12">
				<span class="color-red">R$ <span>{despesa_formatted}</span></span>
			</div>
		</div>

		<button class="button-icon button-blue fa-solid bt_collapse fa-chevron-up"></button>
	</div>

	<div class="walletdespesa_table table tbody flex flex-dc expandable">

		{extra_block_expense}

		<!-- BEGIN EXTRA_BLOCK_EXPENSE_NONE -->
		<div class="font-size-12 textcenter" style="padding: 40px 10px;">
			Nenhuma despesa encontrada ;-)
		</div>
		<!-- END EXTRA_BLOCK_EXPENSE_NONE -->

		<!-- BEGIN EXTRA_BLOCK_EXPENSE -->
		<div class="walletdespesa walletdespesa_{id_walletdespesa} tr flex flex-dc gap-10 window" data-walletdespesa="{walletdespesa}" data-walletsector="{walletsector}" data-valor="{valor}">

			<div class="w-expense-shrink">
				<div class="flex gap-10">

					<div class="flex flex-dc flex-1">
						<label class="caption">{walletsector} ({datapago_formatted})</label>
						<div>{walletdespesa} <span class="font-size-09 color-gray">{obs_despesa}</span></div>
					</div>

					<div class="flex flex-dc">
						<label class="caption textright">{walletcashtype}</label>
						<div class="textright font-size-12 color-blue">R$ {valorpago_formatted}</div>
					</div>

					<div class="flex flex-ai-fe">
						<div class="menu-inter">
							<button class="menu-inter-button fa-solid fa-ellipsis-vertical button-blue"></button>

							<ul>

								<li class="walletdespesa_bt_edit flex flex-ai-center gap-10 color-blue" data-id_walletdespesa="{id_walletdespesa}" title='Editar despesa'>
									<i class="icon fa-solid fa-file-pen"></i>
									<span>Editar Despesa</span>
								</li>

								<li class="walletdespesa_bt_del flex flex-ai-center gap-10 color-red" data-id_wallet="{id_wallet}" data-id_walletdespesa="{id_walletdespesa}" title='Remove despesa'>
									<i class="icon fa-solid fa-trash-can"></i>
									<span>Remover Despesa</span>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- END EXTRA_BLOCK_EXPENSE -->

		<!-- BEGIN EXTRA_BLOCK_EXPENSE_INFO -->
		<div class="tr flex flex-dc gap-10">
			<div class="w-expense-shrink">
				<div class="flex gap-10">

					<div class="flex flex-dc flex-1">
						<label class="caption">{walletsector} ({data_formatted})</label>
						<div>{walletdespesa} <span class="font-size-09 color-gray">{obs_despesa}</span></div>
					</div>

					<div class="flex flex-dc">
						<label class="caption textright">{walletcashtype}</label>
						<div class="textright font-size-12 color-blue">R$ {valor_formatted}</div>
					</div>
				</div>
			</div>
		</div>
		<!-- END EXTRA_BLOCK_EXPENSE_INFO -->

	</div>
	<!-- END EXTRA_BLOCK_WALLET_EXPENSE_CONTAINER -->
</div>

<div class="walletreceita_container box-container flex flex-dc window">

	{extra_block_wallet_receita_container}

	<!-- BEGIN EXTRA_BLOCK_WALLET_RECEITA_CONTAINER -->
	<div class="flex gap-10">
		<div class="box-header flex-1 flex flex-jc-sb gap-10">
			<div>Receitas</div>
			<div>
				<span class="color-green">R$ <span>{receita_formatted}</span></span>
			</div>
		</div>

		<button class="button-icon button-blue fa-solid bt_collapse fa-chevron-up"></button>
	</div>

	<div class="walletreceita_table table tbody flex flex-dc expandable">
		{extra_block_receita}

		<!-- BEGIN EXTRA_BLOCK_RECEITA_NONE -->
		<div class="font-size-12 textcenter" style="padding: 40px 10px;">
			Nenhuma receita encontrada.
		</div>
		<!-- END EXTRA_BLOCK_RECEITA_NONE -->

		<!-- BEGIN EXTRA_BLOCK_RECEITA -->
		<div class="walletreceita walletreceita_{id_walletreceita} tr flex flex-dc gap-10 window" data-walletreceita="{walletreceita}" data-valor="{valor}">

			<div class="w-expense-shrink">
				<div class="flex gap-10">

					<div class="flex flex-dc flex-1">
						<label class="caption">({data_formatted})</label>
						<div>{walletreceita}</div>
					</div>

					<div class="flex flex-dc">
						<label class="caption textright">Valor</label>
						<div class="textright font-size-12 color-blue">R$ {valor_formatted}</div>
					</div>

					<div class="flex flex-ai-fe">
						<div class="menu-inter">
							<button class="menu-inter-button fa-solid fa-ellipsis-vertical button-blue"></button>

							<ul>

								<li class="walletreceita_bt_edit flex flex-ai-center gap-10 color-blue" data-id_walletreceita="{id_walletreceita}" title='Editar receita'>
									<i class="icon fa-solid fa-file-pen"></i>
									<span>Editar receita</span>
								</li>

								<li class="walletreceita_bt_del flex flex-ai-center gap-10 color-red" data-id_wallet="{id_wallet}" data-id_walletreceita="{id_walletreceita}" title='Remove receita'>
									<i class="icon fa-solid fa-trash-can"></i>
									<span>Apagar receita</span>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- END EXTRA_BLOCK_RECEITA -->

		<!-- BEGIN EXTRA_BLOCK_RECEITA_INFO -->
		<div class="tr flex flex-dc gap-10 window">

			<div class="w-expense-shrink">
				<div class="flex gap-10">

					<div class="flex flex-dc flex-1">
						<label class="caption">({data_formatted})</label>
						<div>{walletreceita}</div>
					</div>

					<div class="flex flex-dc">
						<label class="caption textright">Valor</label>
						<div class="textright font-size-12 color-blue">R$ {valor_formatted}</div>
					</div>
				</div>
			</div>
		</div>
		<!-- END EXTRA_BLOCK_RECEITA_INFO -->
	</div>
	<!-- END EXTRA_BLOCK_WALLET_RECEITA_CONTAINER -->
</div>

<div class="walletdespesafutura_container box-container flex flex-dc window">

	{extra_block_wallet_futureexpense_container}

	<!-- BEGIN EXTRA_BLOCK_WALLET_FUTUREEXPENSE_CONTAINER -->
	<div class="flex gap-10">
		<div class="box-header flex-1 flex flex-jc-sb gap-10">
			<div>Despesas Futuras</div>
			<div>
				<span class="color-red">R$ <span>{despesafutura_formatted}</span></span>
			</div>
		</div>

		<button class="button-icon button-blue fa-solid bt_collapse fa-chevron-up"></button>
	</div>

	<div class="walletfutureexpense_table table tbody flex flex-dc expandable">
		{extra_block_futureexpense}

		<!-- BEGIN EXTRA_BLOCK_FUTUREEXPENSE_NONE -->
		<div class="font-size-12 textcenter" style="padding: 40px 10px;">
			Nenhuma despesa futura encontrada ;-)
		</div>
		<!-- END EXTRA_BLOCK_FUTUREEXPENSE_NONE -->

		<!-- BEGIN EXTRA_BLOCK_FUTUREEXPENSE -->
		<div class="walletfutureexpense tr flex flex-dc gap-10">

			<div class="flex gap-10">

				<div class="flex flex-dc flex-1">
					<label class="caption">{walletsector} ({data_formatted})</label>
					<div>{walletdespesa} <span class="font-size-09 color-gray">{obs_despesa}</span></div>
				</div>

				<div class="flex flex-dc">
					<label class="caption textright">Valor</label>
					<div class="textright font-size-12 color-blue">R$ {valor_formatted}</div>
				</div>

				<div class="flex flex-ai-fe">
					<div class="menu-inter">
						<button class="menu-inter-button fa-solid fa-ellipsis-vertical button-blue"></button>

						<ul>

							<li class="walletdespesa_bt_edit flex flex-ai-center gap-10 color-blue" data-id_walletdespesa="{id_walletdespesa}" title='Editar despesa'>
								<i class="icon fa-solid fa-file-pen"></i>
								<span>Editar Despesa</span>
							</li>

							<li class="walletdespesa_bt_pay flex flex-ai-center gap-10 color-green" title="Registrar pagamento" data-id_walletdespesa="{id_walletdespesa}">
								<i class="icon fa-solid fa-file-invoice-dollar"></i>
								<span>Registrar Pagamento</span>
							</li>

							<li class="walletdespesa_bt_del flex flex-ai-center gap-10 color-red" data-id_wallet="{id_wallet}" data-id_walletdespesa="{id_walletdespesa}" title='Remove despesa'>
								<i class="icon fa-solid fa-trash-can"></i>
								<span>Remover Despesa</span>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
		<!-- END EXTRA_BLOCK_FUTUREEXPENSE -->
	</div>
	<!-- END EXTRA_BLOCK_WALLET_FUTUREEXPENSE_CONTAINER -->
</div>

<div class="footer-popup flex-jc-fe">
	<div class="popup-menu">
		<button class="popup-main-button fa-solid fa-ellipsis-vertical button-pink"></button>

		<ul>
			<li class="walletdespesa_bt_new flex flex-ai-center gap-10 color-blue" data-id_wallet="{id_wallet}" title="Registrar despesa">
				<i class="icon fa-solid fa-square-plus"></i>
				<span>Registrar despesa</span>
			</li>

			<li class="walletreceita_bt_new flex flex-ai-center gap-10 color-green-dark" data-id_wallet="{id_wallet}" title="Registrar receita">
				<i class="icon fa-solid fa-hand-holding-dollar"></i>
				<span>Registrar receita</span>
			</li>

			<li class="expense_bt_filter flex flex-ai-center gap-10 color-blue" title="Aplicar Filtro" data-id_wallet="{id_wallet}">
				<i class="icon fa-solid fa-filter-circle-dollar"></i>
				<span>Filtro</span>
			</li>

			<li class="wallets_bt_show flex flex-ai-center gap-10 color-blue" title="Exibir Carteiras">
				<i class="icon fa-solid fa-wallet"></i>
				<span>Trocar Carteira</span>
			</li>
		</ul>
	</div>
</div>
<!-- END BLOCK_PAGE -->