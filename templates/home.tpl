<!-- BEGIN BLOCK_PAGE -->

<div class="box-container flex flex-dc gap-10 non-desktop">
	<div class="box-header flex flex-ai-center gap-10">
		<i class="icon fa-solid fa-sliders"></i>
		Acesso Rápido
	</div>

	<div class="flex-table gap-10">

		<button type="button" class="bt_load flex-8-col button-gray flex flex-dc gap-10 flex-ai-center flex-jc-center padding-v10" data-page="product">
			<i class="fa-solid fa-boxes-stacked font-size-25"></i>
			<span class="">Produto</span>
		</button>

		<button type="button" class="bt_load flex-8-col button-gray flex flex-dc gap-10 flex-ai-center flex-jc-center padding-v10" data-page="entity">
			<i class="fa-solid fa-users font-size-25"></i>
			<span class="">Cliente</span>
		</button>

		<button type="button" class="bt_load flex-8-col button-gray flex flex-dc gap-10 flex-ai-center flex-jc-center padding-v10" data-page="sale_order">
			<i class="fa-solid fa-cart-shopping font-size-25"></i>
			<span class="">Delivery</span>
		</button>

		<div class="box-container button  flex-8-col hidden">
			<button type="button" class="bt_module" data-module="updatelog">
				<i class="fa-solid fa-user-pen font-size-25"></i>
				<span class="">Update Log</span>
			</button>
		</div>
	</div>
</div>

<div class="box-container flex flex-dc gap-10">
	<div class="box-header flex flex-ai-center gap-10">
		<i class="icon fa-solid fa-calendar-days"></i>
		Controle de Validade dos Produtos
	</div>

	<div class="flex-responsive gap-10">

		<div class="flex-6 flex gap-10">
			<div class="flex-3">
				<label class="caption">Vencem em</label>
				<div class="addon">
					{extra_block_expiratedays}
				</div>
			</div>

			<div class="flex-3">
				<label class="caption">Vencidos</label>
				<div class="addon">
					<i class="icon color-red fa-solid fa-triangle-exclamation pos-abs"></i>
					<span class="productexpdate_expirated font-size-15 fill textcenter">{expirated}</span>
				</div>
			</div>
		</div>

		<div class="flex-6 flex gap-10">
			<div class="flex-3">
				<label class="caption">
					Vencimento Próximo
				</label>
				<div class="addon">
					<i class="icon color-yellow fa-solid fa-triangle-exclamation pos-abs"></i>
					<span class="productexpdate_toexpirate font-size-15 fill textcenter">{toexpirate}</span>
				</div>
			</div>

			<div class="flex flex-ai-fe flex-3">
				<button type="button" class="productexpdate_bt_list button-blue fill">Lista de Produtos</button>
			</div>
		</div>

		<div class="flex-6"></div>
	</div>
</div>

<!-- BEGIN EXTRA_BLOCK_POPUP_CP_EXPDATE -->
<div class="flex flex-dc gap-10">

	<!-- <div class="window-header setor-2">
		Produtos com vencimento próximo
	</div> -->


	<div class="flex-responsive gap-10">
		<div class="box-header flex-1">
			<span>Lista de Validade Abaixo de </span>

			{extra_block_expiratedays}

			<!-- BEGIN EXTRA_BLOCK_EXPIRATEDAYS -->
			<button class="product_bt_expiratedays color-blue font-size-12 button-field">{product_expirate_days} dias</button>
			<!-- END EXTRA_BLOCK_EXPIRATEDAYS -->
			<!-- BEGIN EXTRA_BLOCK_EXPIRATEDAYS_FORM -->
			<form method="post" id="frm_product_expiratedays" class="padding-h5">
				<div class="">
					<input
						type="number"
						id="product_expirate_days"
						step='1'
						min='0'
						max='365'
						maxlength="1"
						required
						value='{product_expirate_days}'
						autofocus>
						<span>dias</span>
				</div>
			</form>
			<!-- END EXTRA_BLOCK_EXPIRATEDAYS_FORM -->
			</div>
	</div>

	<div class="cp_expdate_notfound {cp_expdate_notfound}" style="padding: 40px 10px;">
		Nenhum produto com vencimento próximo <i class="icon fa-regular fa-face-smile-wink"></i>
	</div>

	<div class="cp_expdate_table table tbody flex flex-dc">

		{extra_block_cp_expdate_tr}

		<!-- BEGIN EXTRA_BLOCK_CP_EXPDATE_TR -->
		<div class="cp_expdate_tr cp_expdate_{id_produtovalidade} tr flex gap-10" data-id_produtovalidade="{id_produtovalidade}">
			<div class="flex-responsive gap-10 fill">
				<div class="flex-11">
					<label class="produto-tipo caption">
						{produtotipo}
					</label>

					<div class="addon container">
						{extra_block_product_button_status}
						<button class="product_bt_produto product_{id_produto}_produto button-field textleft fill" data-id_produto="{id_produto}" title="Editar nome do produto">
							{produto}
						</button>
					</div>
				</div>

				<div class="flex gap-10 flex-7">
					<div class="flex-3">
						<label class="caption">Validade</label>
						<div class="addon">
							<span class="field disabled fill">{data_formatted}</span>
						</div>
					</div>

					{extra_block_productexpdate_days}

					<!-- BEGIN EXTRA_BLOCK_PRODUCTEXPDATE_DAYS -->
					<div class="flex-3">
						<label class="caption">Restam</label>
						<div class="addon">
							<span class="field disabled fill">{dias} dias</span>
						</div>
					</div>
					<!-- END EXTRA_BLOCK_PRODUCTEXPDATE_DAYS -->

					<!-- BEGIN EXTRA_BLOCK_PRODUCTEXPDATE_EXPIRATED -->
					<div class="flex-3 flex flex-ai-fe flex-jc-center">
						<div class="flex flex-ai-center color-red gap-5" style="min-width: 110px;">
							<i class="icon fa-solid fa-stopwatch"></i>
							<div class="pseudo-button">Vencido</div>
						</div>
					</div>
					<!-- END EXTRA_BLOCK_PRODUCTEXPDATE_EXPIRATED -->

					<div class="flex flex-jc-fe flex-ai-fe">
						<div class="menu-inter">
							<button class="menu-inter-button fa-solid button-blue fa-ellipsis-vertical"></button>

							<ul style="display: none;">
								<li class="product_bt_validade_delete flex flex-ai-center gap-10 color-red" data-id_produtovalidade="{id_produtovalidade}" title="Remover data de validade do produto">
									<i class="icon fa-solid fa-trash-can"></i>
									<span>Remover Data</span>
								</li>

								<li class="product_bt_validade flex flex-ai-center gap-10 color-blue" data-id_produto="{id_produto}" title="Cadastro de validade do produto">
									<i class="icon fa-solid fa-calendar-days"></i>
									<span>Controle de Validade</span>
								</li>

								<li class="bt_purchaseorder_history flex flex-ai-center gap-10 color-blue" data-id_produto="{id_produto}" data-produto="{produto}" title="Histórico de venda do produto">
									<i class="icon fa-solid fa-chart-column"></i>
									<span>Histórico de Venda</span>
								</li>
							</ul>
						</div>

					</div>
				</div>
			</div>
		</div>
		<!-- END EXTRA_BLOCK_CP_EXPDATE_TR -->
	</div>

	<div class="flex flex-jc-fe gap-10 padding-t10">
		<button type="button" class="productexpdate_bt_print button-blue {productexpdate_bt_print}">Imprimir lista</button>
	</div>
</div>
<!-- END EXTRA_BLOCK_POPUP_CP_EXPDATE -->

<!-- <div class="box-container flex flex-dc gap-10">
	<div class="box-header flex flex-ai-center gap-10">
		<i class="icon fa-solid fa-sliders"></i>
		Módulos
	</div>

	<div class="flex-table gap-10">

		<button type="button" class="bt_module flex-8-col button-blue flex flex-dc gap-10 flex-ai-center padding-v10">
			<i class="fa-solid fa-user-pen font-size-25"></i>
			<span class="">Garçom</span>
		</button>
	</div>
</div> -->
<!-- END BLOCK_PAGE -->