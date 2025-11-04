<!-- BEGIN BLOCK_PAGE -->

<!-- BEGIN EXTRA_BLOCK_SALEORDER_REVERSE -->
<div class="flex gap-10">
	<div class="flex flex-dc flex-1">
		<label class="caption">{salelegend} #{id_venda}</label>
		<div class="addon">
			<span>{data_formatted}</span>
		</div>
	</div>

	<div class="flex-1">
		<label class="caption">Total</label>
		<div class="addon color-blue">
			<span>R$ {total_formatted}</span>
		</div>
	</div>
</div>

<div class="margin-t20">
	<span class="setor-2">Confirma cancelamento da venda?</span>
</div>
<!-- END EXTRA_BLOCK_SALEORDER_REVERSE -->

<!-- BEGIN EXTRA_BLOCK_ADDRESS_SELECTION -->
<div class="w_saleorder_address w-entityaddress flex flex-dc" data-id_venda="{id_venda}" data-id_entidade="{id_entidade}">

	<div class="table tbody flex flex-dc">

		<div class="flex flex-jc-fe tr gap-10 padding-t10">
			<button class="sale_order_bt_address_delete button-green" title='Cliente retira na loja'>
				Cliente retira
			</button>
		</div>

		{tplentity_extra_block_address}
	</div>

	<div class="flex flex-jc-fe gap-10 padding-t10">
		<button class="bt_entity_address_new button-blue" data-id_entidade="{id_entidade}" data-page="sale_order" title="Cadastrar novo endereço.">
			Adicionar Endereço
		</button>
	</div>
</div>
<!-- END EXTRA_BLOCK_ADDRESS_SELECTION -->

<!-- BEGIN EXTRA_BLOCK_STATUSHISTORY -->
<div>
	<div class="flex gap-10">
		<div class="flex-1">
			<label class="caption">Cupom</label>
			<div class="addon">
				<span>{id_venda}</span>
			</div>

		</div>

		<div class="flex-1">
			<label class="caption">Data</label>
			<div class="addon">
				<span>{data_formatted}</span>
			</div>

		</div>
	</div>

	<h1></h1>

	<div class="table tbody">
		{extra_block_statushistory_list}

		<!-- BEGIN EXTRA_BLOCK_STATUSHISTORY_NOTFOUND -->
		<div class="tr">
			Histórico de status não encontrado.
		</div>
		<!-- END EXTRA_BLOCK_STATUSHISTORY_NOTFOUND -->

		<!-- BEGIN EXTRA_BLOCK_STATUSHISTORY_LIST -->
		<div class="tr">
			<div>
				<label class="caption">{data_formatted} - {vendastatus}</label>
				<label class="caption"><i class="fa-solid fa-user padding-h5"></i> {colaborador}</label>
				<label class="caption">{obs}</label>
				<!-- <div class="addon">
					<span>{colaborador}</span>
				</div> -->
			</div>
		</div>
		<!-- END EXTRA_BLOCK_STATUSHISTORY_LIST -->
	</div>
</div>
<!-- END EXTRA_BLOCK_STATUSHISTORY -->

<!-- BEGIN EXTRA_BLOCK_POPUP_SALE_COMPLEMENT -->
<div class="w_saleorder_complement flex flex-dc gap-10" data-id_venda="{id_venda}" data-id_produto="{id_produto}" data-qtd="{qtd}" data-obs="{obs}">

	<div class="flex-6">
		<label class="caption">Produto</label>
		<div class="addon">
			<span class="{class_status}">{id_produto}</span>
			<span class="textleft">{produto}</span>
		</div>
	</div>

	<!-- <div class="section-header">
		Grupos de Complementos que compõem esse produto
	</div> -->

	<!-- <div class="complementgroup_not_found complementgroup_not_found_{id_produto} table tbody {hidden}">
		<div class="" style="padding: 20px 10px;">
			Não há grupos de complementos para este produto.
		</div>
	</div> -->

	<div class="w_complementgroup_table flex flex-dc gap-10">

		{extra_block_complement_tr}

		<!-- BEGIN EXTRA_BLOCK_COMPLEMENT_TR -->

		<div class="produtct_complementgroup_container flex flex-dc gap-10" data-id_complementogrupo="{id_complementogrupo}" data-min="{qtd_min}" data-max="{qtd_max}">

			<div class="section-header">
				{descricao}
			</div>

			<div class="flex-1 flex flex-dc gap-10">

				<div class="flex flex-dc gap-30">

					{extra_block_complement_tr_msg}

					<!-- BEGIN EXTRA_BLOCK_COMPLEMENT_TR_MSG1 -->
					<div>Escolha até {qtd_max} item(s). [Opcional]</div>
					<!-- END EXTRA_BLOCK_COMPLEMENT_TR_MSG1 -->

					<!-- BEGIN EXTRA_BLOCK_COMPLEMENT_TR_MSG2 -->
					<div>Escolha {qtd_min} item(s)</div>
					<!-- END EXTRA_BLOCK_COMPLEMENT_TR_MSG2 -->

					<!-- BEGIN EXTRA_BLOCK_COMPLEMENT_TR_MSG3 -->
					<div>Escolha entre {qtd_min} a {qtd_max} item(s)</div>
					<!-- END EXTRA_BLOCK_COMPLEMENT_TR_MSG3 -->

					<!-- <div class="product_complement_not_found product_complement_not_found_{id_complementogrupo} table tbody {hidden}">
						<div class="" style="padding: 20px 10px;">
							Não há itens para este grupo.
						</div>
					</div> -->

					<!-- <div class="product_complement_table product_complement_table_{id_complementogrupo}"> -->

						{extra_block_complementgroup_product}

						<!-- BEGIN EXTRA_BLOCK_COMPLEMENTGROUP_PRODUCT -->

						<div class="complementproduct flex gap-10" data-id_produto="{id_produto}">

							<div class="flex flex-ai-center">
								<input type="checkbox" value="{id_produto}">
							</div>

							<div class="flex flex-ai-center non-mobile">
								<img class="img_product" style="min-width: 80px; min-height: 80px;" src="pic/{imagem}" loading="lazy">
							</div>

							<div class="flex-1 flex-dc gap-10">

								<div class="flex-1 flex gap-10">
									<div class="flex flex-ai-center mobile">
										<img class="img_product" style="min-width: 80px; min-height: 80px;" src="pic/{imagem}" loading="lazy">
									</div>

									<div class="flex flex-dc flex-jc-center gap-5 flex-1">
										<div class="">
											{produto}
										</div>
										<div class="font-size-075">
											<span>R$ {preco_complemento_formatted} <span class="">/{produtounidade}</span></span>
										</div>
									</div>
								</div>

								<div class="">
									<label class="caption">Observação</label>
									<div class="addon">
										<input type="text" id="obs" class="fill" maxlength="255" title="Observação para o produto." placeholder="">
									</div>
								</div>
							</div>

							<!-- <div class="flex flex-dc gap-5 flex-1 non-mobile">
								<div class="">
									{produto}
								</div>
								<div class="font-size-075">
									<span>R$ {preco_complemento_formatted} <span class="">/{produtounidade}</span></span>
								</div>
								<div class="">
									<label class="caption">Observação</label>
									<div class="addon">
										<input type="text" id="obs" class="fill" maxlength="255" title="Observação para o produto." placeholder="">
									</div>
								</div>
							</div> -->
						</div>
						<!-- END EXTRA_BLOCK_COMPLEMENTGROUP_PRODUCT -->
					<!-- </div> -->
				</div>
			</div>
		</div>
		<!-- END EXTRA_BLOCK_COMPLEMENT_TR -->
	</div>

	<div class="flex flex-jc-fe padding-t10">
		<button type="button" class="bt_sale_complement button-blue">Confirmar</button>
	</div>
</div>
<!-- END EXTRA_BLOCK_POPUP_SALE_COMPLEMENT -->

<div class="box-container">

	<div class="flex flex-jc-sb gap-10 padding-b10">
		<div class="box-header no-margin flex-1 gap-10">
			<i class="icon fa-solid fa-cart-shopping"></i>
			<span>Delivery / Pedido</span>
		</div>

		<div class="flex flex-ai-fe flex-jc-fe gap-10">
			<button type="button" class="entity_bt_cepsearchfreight button-icon button-blue" title="Consulta valor de frete">
				<i class="fa-solid fa-magnifying-glass-location"></i>
			</button>

			<button type="button" class="entity_bt_new button-icon button-blue" title="Cadastrar novo cliente">
				<i class="fa-solid fa-person-circle-plus"></i>
			</button>
		</div>
	</div>

	<div>

		<form method="post" id="frm_sale_order" class="flex-responsive gap-10">

			<div class="flex gap-10">

				<div class="fill">
					<label class="caption flex flex-ai-center gap-5">
						<i class="fa-solid fa-magnifying-glass"></i>
						Código / Nome / Telefone
					</label>

					<div class='autocomplete-dropdown flex-1'>
						<input
							type="text"
							class="uppercase entity_search smart_search smart-search fill"
							data-source="popup"
							data-focus_next="entity_search_submit"
							autofocus
							placeholder=""
							required
							autocomplete="off"
							maxlength="100">
						{block_entity_autocomplete_search}
					</div>
				</div>
			</div>

			<div class="flex flex-ai-fe">
				<button id="entity_search_submit" type="submit" class="button-blue fill" title="Abrir pedido">Abrir Pedido</button>
			</div>
		</form>

		<!-- <div class="flex flex-ai-fe flex-jc-fe desktop">
			<button type="button" class="entity_bt_new button-blue" title="Cadastrar novo cliente">Novo cliente</button>
		</div> -->
	</div>
</div>

<!-- BEGIN EXTRA_BLOCK_POPUP_SALEORDER_PAYMENT -->
<div class="w_saleorder_payment_container flex flex-dc gap-10">

	<div class="box-header">
		Forma de Pagamento
	</div>

	{extra_block_popup_saleorder_payment}

	<!-- BEGIN EXTRA_BLOCK_TOTAL -->
	<!-- <div class="box-header">
		Total do pedido
	</div> -->

	<div class="flex flex-5 gap-10">
		<div class="addon flex-4 color-blue flex flex-jc-sb gap-10 font-size-12 one-line">
			<span class="flex-2">Total do Pedido</span>
			<span class="flex-2 textright">R$ {total_formatted}</span>
		</div>
		<div class="pseudo-button"></div>
	</div>
	<!-- END EXTRA_BLOCK_TOTAL -->

	<!-- BEGIN EXTRA_BLOCK_SALEORDER_PAYMENT -->
	<div class="flex flex-5 gap-10">
		<div class="addon flex-4 flex-jc-sb gap-10">
			<span class="flex-2">{especie}</span>
			<span class="flex-2 textright">R$ {valorrecebido_formatted}</span>
		</div>

		<div class="flex flex-ai-fe pseudo-button">
			{extra_block_saleorder_bt_payment_del}
			<!-- BEGIN EXTRA_BLOCK_SALEORDER_BT_PAYMENT_DEL -->
			<button type="button" class="saleorder_bt_payment_del button-icon button-red fa-solid fa-sack-xmark" data-id_venda="{id_venda}" data-id_vendapay="{id_vendapay}" data-id_entidade="{id_entidade}" title="Remover pagamento"></button>
			<!-- END EXTRA_BLOCK_SALEORDER_BT_PAYMENT_DEL -->
		</div>
	</div>
	<!-- END EXTRA_BLOCK_SALEORDER_PAYMENT -->

	<!-- BEGIN EXTRA_BLOCK_SALEORDER_PAYMENT_SUBTOTAL -->
	<div class="flex flex-5 gap-10 color-blue">
		<div class="addon flex-jc-sb gap-10 flex-4">
			<span class="flex-2">Falta</span>
			<span class="flex-2 textright">R$ {subtotal_formatted}</span>
		</div>

		<div class="pseudo-button"></div>
	</div>
	<!-- END EXTRA_BLOCK_SALEORDER_PAYMENT_SUBTOTAL -->

	<!-- BEGIN EXTRA_BLOCK_SALEORDER_PAYMENT_TROCO -->
	<div class="flex flex-5 gap-10 color-blue">
		<div class="addon flex-jc-sb gap-10 flex-4">
			<span class="flex-2">Troco</span>
			<span class="flex-1 textright">R$ {troco_formatted}</span>
		</div>

		<div class="pseudo-button"></div>
	</div>
	<!-- END EXTRA_BLOCK_SALEORDER_PAYMENT_TROCO -->

	<!-- BEGIN EXTRA_BLOCK_SALEORDER_PAYMENT_CLOSE -->
	<div class="flex flex-dc gap-10 color-blue">
		<div class="tr margin-t10"></div>

		<div class="flex flex-jc-center gap-10">
			{extra_block_saleorder_payment_close_button}
			<!-- BEGIN EXTRA_BLOCK_SALEORDER_PAYMENT_CLOSE_BUTTON -->
			<button type="button" class="saleorder_bt_close button-blue flex flex-ai-center gap-5" data-id_venda="{id_venda}" data-print="false" title="Fechar pedido">
				<i class="icon fa-solid fa-clipboard-check"></i>
				<span>Fechar pedido</span>
			</button>

			<button type="button" class="saleorder_bt_close button-blue flex flex-ai-center gap-5" data-id_venda="{id_venda}" data-print="true" title="Fechar pedido e imprimir">
				<i class="icon fa-solid fa-print"></i>
				<span>Fechar e imprimir</span>
			</button>
			<!-- END EXTRA_BLOCK_SALEORDER_PAYMENT_CLOSE_BUTTON -->
		</div>
	</div>

	<!-- END EXTRA_BLOCK_SALEORDER_PAYMENT_CLOSE -->

	<!-- BEGIN EXTRA_BLOCK_SALEORDER_PAYMENT_FORM -->
	<div class="flex-responsive">
		<form method="post" id="frm_saleorder_payment" class="flex flex-5 gap-10" data-id_venda="{id_venda}" data-id_entidade="{id_entidade}">

			<div class="flex-2">
				<!-- <label class="caption">Espécie</label> -->
				<div class="addon">
					<select id="id_especie" class="fill">
						{extra_block_especie}
						<!-- BEGIN EXTRA_BLOCK_ESPECIE -->
						<option value="{id_especie}">{especie}</option>
						<!-- END EXTRA_BLOCK_ESPECIE -->
					</select>
				</div>
			</div>

			<div class="flex-1">
				<!-- <label class="caption">Valor</label> -->
				<div class="addon">
					<span>R$</span>
					<input type="number" id="valor" class="fill textright" step="0.01" min="0.01" max="1000000" required="" placeholder="0,00" title="Valor de cobrança do pedido" autofocus>
				</div>
			</div>

			<div class="flex flex-ai-fe">
				<button type="submit" class="button-icon button-blue fa-solid fa-sack-dollar" title="Adicionar pagamento"></button>
			</div>
		</form>
	</div>

	<div class="flex flex-5 gap-10 color-blue">
		<div class="addon flex-jc-sb gap-10 flex-4">
			<span class="flex-2">Falta</span>
			<span class="flex-2 textright">R$ {subtotal_formatted}</span>
		</div>

		<div class="pseudo-button"></div>
	</div>

	<div class="tr margin-t10 no-padding"></div>

	<div>
		{block_entity_credit}
	</div>
	<!-- END EXTRA_BLOCK_SALEORDER_PAYMENT_FORM -->

</div>
<!-- END EXTRA_BLOCK_POPUP_SALEORDER_PAYMENT -->

<div class="saleorder_controlpanel table tbody flex flex-wrap gap-10">

	<div class="saleorder_bt_andamento mouseHand flex-4-col-fix ticket-border flex-dc gap-10 border-red" title="Mostrar pedidos em andamento">
		<div class="flex flex-dc gap-10 color-blue font-size-12">
			<div class="flex-1 flex">
				<span class="one-line textcenter">Em Andamento</span>
			</div>
			<div class="w_saleorder_andamento flex-1 flex flex-jc-center">
				{total_andamento}
			</div>
		</div>
	</div>

	<div class="bt_saleorder_efetuado mouseHand flex-4-col-fix ticket-border flex-dc gap-10 border-green" title="Mostrar pedidos confirmados">
		<div class="flex flex-dc gap-10 color-blue font-size-12">
			<div class="flex-1 flex">
				<span class="one-line textcenter">Confirmados</span>
			</div>
			<div class="w_saleorder_efetuado flex-1 flex flex-jc-center">
				{total_efetuado}
			</div>
		</div>
	</div>

	<div class="bt_saleorder_producao mouseHand flex-4-col-fix ticket-border flex-dc gap-10 border-orange"  title="Mostrar pedidos em produção">
		<div class="flex flex-dc gap-10 color-blue font-size-12">
			<div class="flex-1 flex">
				<span class="one-line textcenter">Em Produção</span>
			</div>
			<div class="w_saleorder_producao flex-1 flex flex-jc-center">
				{total_producao}
			</div>
		</div>
	</div>

	<div class="bt_saleorder_entrega mouseHand flex-4-col-fix ticket-border flex-dc gap-10 border-blue"  title="Mostrar pedidos em entrega">
		<div class="flex flex-dc gap-10 color-blue font-size-12">
			<div class="flex-1 flex">
				<span class="one-line textcenter">Em Entrega</span>
			</div>
			<div class="w_saleorder_entrega flex-1 flex flex-jc-center">
				{total_entrega}
			</div>
		</div>
	</div>
</div>

<div class="w_saleorder_container card-container flex flex-dc" data-window="saleorder_andamento">

	<div class="box-header flex flex-jc-sb gap-10">
		<div class="saleorder_header">{header}</div>
	</div>

	<div class="saleorder_notfound window {hidden_sale_order_not_found}">

		<div class="font-size-12 textcenter" style="padding: 80px 10px;">
			Nenhum pedido encontrado.
		</div>
	</div>

	<div class="saleorder_loading window">

		<div class="flex flex-jc-center" style="padding: 80px 10px;">

			<i class="fa-solid fa-rotate fa-spin font-size-20"></i>
		</div>
	</div>

	<div class="flex flex-dc table tbody">

		{extra_block_orders}

		<!-- BEGIN EXTRA_BLOCK_SALEORDER_TICKET -->
		<div class="w_saleorder w_saleorder_{id_venda} window tr flex flex-dc gap-10" data-id_venda='{id_venda}' data-versao="{versao}" data-id_entidade="{id_entidade}" data-total='{total}' data-frete='{frete}' data-valor_servico='{valor_servico}'>

			<div class="{ticket_marker} padding-l10 flex-responsive gap-10">

				<div class="flex flex-dc flex-3">
					<label class="caption">{salelegend} #{id_venda}</label>
					<div class="addon">
						<span>{data_formatted}</span>
					</div>
				</div>

				<div class="flex flex-dc flex-9">
					<label class="caption">Cliente</label>
					<div class="addon">
						{extra_block_entity_button_status}
						<span class="entity_{id_entidade}_nome">{nome}</span>
					</div>
				</div>

				<div class="flex gap-10 flex-6">
					<div class="flex-1">
						<label class="caption">Total</label>
						<div class="addon font-size-12 color-blue">
							<span>R$ {total_formatted}</span>
						</div>
					</div>

					{extra_block_saleorder_obs}

					{extra_block_saleorder_menu}

					<!-- BEGIN EXTRA_BLOCK_SALEORDER_MENU -->
					<div class="flex flex-ai-fe flex-jc-right">
						<div class="flex ai-center flex-jc-right gap-10">
							<div class="menu-inter">
								<button class="menu-inter-button fa-solid fa-ellipsis-vertical button-blue"></button>

								<ul>
									{extra_block_saleorder_menulist}

									<!-- BEGIN EXTRA_BLOCK_SALEORDER_MENU_VIEW -->
									<li class="saleorder_bt_show flex flex-ai-center gap-10 color-blue" data-id_venda="{id_venda}">
										<i class="icon fa-solid fa-file-lines"></i>
										<span>Visualizar Pedido</span>
									</li>
									<!-- END EXTRA_BLOCK_SALEORDER_MENU_VIEW -->

									<!-- BEGIN EXTRA_BLOCK_SALEORDER_MENU_CLOSE -->
									<li class="sale_order_bt_payment flex flex-ai-center gap-10 color-blue" data-id_venda="{id_venda}">
										<i class="icon fa-solid fa-clipboard-check"></i>
										<span>Fechar Pedido</span>
									</li>
									<!-- END EXTRA_BLOCK_SALEORDER_MENU_CLOSE -->

									<!-- BEGIN EXTRA_BLOCK_SALEORDER_MENU_PRODUCTION -->
									<li class="saleorder_bt_changestatus flex flex-ai-center gap-10 color-blue" data-id_venda="{id_venda}" data-action="saleorder_producao" title="Colocar pedido em produção">
										<i class="icon fa-solid fa-fire"></i>
										<span>Colocar em Produção</span>
									</li>
									<!-- END EXTRA_BLOCK_SALEORDER_MENU_PRODUCTION -->

									<!-- BEGIN EXTRA_BLOCK_SALEORDER_MENU_DELIVERY -->
									<li class="saleorder_bt_changestatus flex flex-ai-center gap-10 color-blue" data-id_venda="{id_venda}" data-action="saleorder_entrega" title="Enviar pedido para entrega">
										<i class="icon fa-solid fa-motorcycle"></i>
										<span>Enviar para Entrega</span>
									</li>
									<!-- END EXTRA_BLOCK_SALEORDER_MENU_DELIVERY -->

									<!-- BEGIN EXTRA_BLOCK_SALEORDER_MENU_REOPEN -->
									<li class="saleorder_bt_open flex flex-ai-center gap-10 color-blue" data-id_venda="{id_venda}">
										<i class="icon fa-solid fa-file-pen"></i>
										<span>Reabrir Pedido</span>
									</li>
									<!-- END EXTRA_BLOCK_SALEORDER_MENU_REOPEN -->

									<!-- BEGIN EXTRA_BLOCK_SALEORDER_MENU_ENTITY -->
									<li class="entity_bt_show flex flex-ai-center gap-10 color-blue" data-id_entidade="{id_entidade}" title="Visualizar dados do cliente">
										<i class="icon fa-solid fa-file-lines"></i>
										<span>Dados do Cliente</span>
									</li>
									<!-- END EXTRA_BLOCK_SALEORDER_MENU_ENTITY -->

									<!-- BEGIN EXTRA_BLOCK_SALEORDER_MENU_PRINT -->
									<li class="saleorder_bt_print flex flex-ai-center gap-10 color-blue" data-id_venda="{id_venda}">
										<i class="icon fa-solid fa-print"></i>
										<span>Imprimir</span>
									</li>
									<!-- END EXTRA_BLOCK_SALEORDER_MENU_PRINT -->

									<!-- BEGIN EXTRA_BLOCK_SALEORDER_MENU_REVERSE -->
									<li class="saleorder_bt_cancel flex flex-ai-center gap-10 color-red" data-id_venda="{id_venda}">
										<i class="icon fa-solid fa-file-excel"></i>
										<span>Cancelar / Estornar</span>
									</li>
									<!-- END EXTRA_BLOCK_SALEORDER_MENU_REVERSE -->

									<!-- BEGIN EXTRA_BLOCK_SALEORDER_MENU_STATUSHISTORY -->
									<li class="saleorder_bt_statushistory flex flex-ai-center gap-10 color-blue" data-id_venda="{id_venda}" title="Exibir o histórico de mudança de status do pedido">
										<i class="icon fa-solid fa-list"></i>
										<span>Histórico de Status</span>
									</li>
									<!-- END EXTRA_BLOCK_SALEORDER_MENU_STATUSHISTORY -->

									<!-- BEGIN EXTRA_BLOCK_SALEORDER_MENU_PRAZO -->
									<li class="saleorder_bt_prazo flex flex-ai-center gap-10 color-blue" data-id_venda="{id_venda}">
										<i class="icon fa-solid fa-file-invoice-dollar"></i>
										<span>Venda a Prazo</span>
									</li>
									<!-- END EXTRA_BLOCK_SALEORDER_MENU_PRAZO -->

									<!-- BEGIN EXTRA_BLOCK_SALEORDER_MENU_WHATS -->
									<li class="sale_order_bt_whats flex flex-ai-center gap-10 color-green" data-id_venda="{id_venda}" title="Envia pedido pelo WhatsApp">
										<i class="icon fa-brands fa-whatsapp"></i>
										<span>WhatsApp</span>
									</li>
									<!-- END EXTRA_BLOCK_SALEORDER_MENU_WHATS -->

									<!-- BEGIN EXTRA_BLOCK_SALEORDER_MENU_COPYPASTE -->
									<li class="sale_order_bt_copy flex flex-ai-center gap-10 color-blue" data-id_venda="{id_venda}">
										<i class="icon fa-solid fa-copy"></i>
										<span>Copia e Cola</span>
									</li>
									<!-- END EXTRA_BLOCK_SALEORDER_MENU_COPYPASTE -->
								</ul>
							</div>
						</div>
					</div>
					<!-- END EXTRA_BLOCK_SALEORDER_MENU -->

					<div class="flex flex-ai-fe">
						<button type="button" class="saleorder_bt_show button-blue" data-id_venda="{id_venda}" title="Ver pedido">
							<i class="icon fa-solid fa-chevron-down"></i>
						</button>
					</div>
				</div>
			</div>
		</div>
		<!-- END EXTRA_BLOCK_SALEORDER_TICKET -->

		<!-- BEGIN EXTRA_BLOCK_SALEORDER_EDIT -->
		<div class="w_saleorder w_saleorder_{id_venda} flex flex-dc gap-10" data-id_venda='{id_venda}' data-versao='{versao}' data-id_entidade="{id_entidade}" data-total='{total}' data-frete='{frete}' data-valor_servico='{valor_servico}'>

			<div class="flex-responsive gap-10">

				<div class="flex-10">
					<label class="caption">Cliente</label>
					<div class="addon">
						{extra_block_entity_button_status}
						<span class="entity_{id_entidade}_nome">{nome}</span>
					</div>
				</div>

				<div class="flex-3">
					{block_entity_credit}
				</div>

				<div class="flex gap-10 flex-5">

					<div class="flex-1">
						<label class="caption color-blue">Total Pedido</label>
						<div class="addon color-blue font-size-12">
							<span>R$ <span class="sale_total">{total_formatted}</span></span>
						</div>
					</div>

					{extra_block_saleorder_obs}

					<!-- BEGIN EXTRA_BLOCK_SALEORDER_OBS -->
					<div class="w_saleorder_obs flex flex-ai-fe">

						<div class="tooltip pos-rel">

							<button type="button" class="bt_comment button-icon button-blue fa-solid fa-comment-dots" title="Observação do pedido"></button>

							<span class="tooltiptext">{obs}</span>

							<div class="float-form hidden">

								<div>
									<button class="bt_commentclose button-icon button-gray fa-solid fa-xmark pos-abs" style="top: -15px; right: -15px;" title="Fechar"></button>
								</div>

								<form method="post" class="frm_sale_order_obs flex gap-10 fill">
									<div>
										<label class="caption">Observação do Pedido</label>
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
										<!-- <button type="button" class="bt_saleorder_obs_del button-icon button-red fa-solid fa-trash-can" title="Apagar observação" data-id_venda="{id_venda}"></button> -->
									</div>
								</form>
							</div>
						</div>
					</div>
					<!-- END EXTRA_BLOCK_SALEORDER_OBS -->

					<!-- BEGIN EXTRA_BLOCK_SALEORDER_OBS_EMPTY -->
					<div class="w_saleorder_obs flex flex-ai-fe pos-rel">

						<button type="button" class="bt_comment button-icon button-blue fa-regular fa-comment" title="Observação do pedido"></button>

						<div class="float-form hidden">

							<div>
								<button class="bt_commentclose button-icon button-gray fa-solid fa-xmark pos-abs" style="top: -15px; right: -15px;" title="Fechar"></button>
							</div>

							<form method="post" class="frm_sale_order_obs flex gap-10 fill">
								<div>
									<label class="caption">Observação do Pedido</label>
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
					<!-- END EXTRA_BLOCK_SALEORDER_OBS_EMPTY -->

					{extra_block_saleorder_menu}
				</div>
			</div>

			<div class="flex-responsive gap-10">

				<div class="flex gap-10 flex-3">
					<div class="flex gap-10 flex-2">
						<div class="flex-1">
							<label class="caption">{salelegend} #{id_venda}</label>
							{extra_block_saleorder_show_ticket}
						</div>
					</div>

					<div class="flex gap-10 flex-2">
						<div class="flex-1">
							<label class="caption">Data</label>
							<div class="addon flex-jc-center">
								<span>{data_formatted}</span>
							</div>
						</div>
					</div>
				</div>

				<div class="flex flex-dc flex-5">
					<label class="caption">Vendedor</label>
					<div class="addon">
						<span>{colaborador}</span>
					</div>
				</div>
			</div>

			<div class="saleorder-item">
				<div class="card-body flex flex-dc gap-10">

					<div class="section-header">
						Dados da Entrega
					</div>

					<div class="w-entityaddress flex-1">
						<!-- BEGIN BLOCK_ADDRESS -->
						<label class="caption">Endereço de entrega</label>
						<div class="addon">
							<button type="button" class="sale_order_bt_address button-field textleft fill" title="Escolher o endereço de entrega do pedido">
								{extra_block_saleaddress}
								<!-- BEGIN EXTRA_BLOCK_SALEADDRESS -->
								{endereco}

								<!-- END EXTRA_BLOCK_SALEADDRESS -->
								<!-- BEGIN EXTRA_BLOCK_NO_SALEADDRESS -->
								Cliente retira na loja

								<!-- END EXTRA_BLOCK_NO_SALEADDRESS -->
							</button>
						</div>
						<!-- END BLOCK_ADDRESS -->
					</div>

					<div class="section-header">
						Itens
					</div>

					<div class="table flex flex-dc gap-10">
						<div class="tbody flex flex-dc">

							{extra_block_tab_content_tr}
							<!-- BEGIN EXTRA_BLOCK_SALEORDER_ABERTO_ITEMS_NONE -->
							<div class="w-saleorderitem-notfound">
								<div style="padding: 20px 10px;">
									Nenhum item no pedido.
								</div>
							</div>
							<!-- END EXTRA_BLOCK_SALEORDER_ABERTO_ITEMS_NONE -->

							<!-- BEGIN EXTRA_BLOCK_SALEORDER_ABERTO_ITEMS -->
							<div class="w-saleorderitem window tr flex flex-dc gap-10" data-id_vendaitem="{id_vendaitem}" data-subtotal="{subtotal}" data-desconto="{desconto}" data-total="{total}">

								<div class="flex-responsive gap-10">

									<div class="flex-6">
										<label class="caption">Produto</label>
										<div class="addon">
											<span class="{class_status}">{id_produto}</span>
											<span class="textleft">{produto}</span>
										</div>
									</div>

									<div class="flex gap-10 flex-5">

										<div class="flex-2">
											<label class="caption">Quantidade</label>

											<!-- BEGIN BLOCK_ITEM_QTD -->
											<div class="addon">
												<button class="sale_order_item_qtd button-field textleft" title="Editar quantidade do produto">
													{qtd_formatted} <span class="font-size-075">{produtounidade}</span>
												</button>
											</div>
											<!-- END BLOCK_ITEM_QTD -->

											<!-- BEGIN EXTRA_BLOCK_FORM_ITEM_QTD -->
											<form method="post" id="frm_sale_order_item_qtd" class="flex flex-ai-center fill" data-id_vendaitem="{id_vendaitem}">
												<div class="addon">
													<input
														type="number"
														id="qtd"
														class="fill"
														step='0.001'
														min='0.001'
														max='999999.999'
														required
														value='{qtd}'
														title="Quantidade do produto">
													<span class="padding-h5 font-size-075">{produtounidade}</span>
												</div>
											</form>
											<!-- END EXTRA_BLOCK_FORM_ITEM_QTD -->
										</div>

										<div class="flex-2">
											<label class="caption">Preço</label>

											<!-- BEGIN BLOCK_ITEM_PRECO -->
											<div class="addon">
												<button class="sale_order_item_preco button-field textleft one-line" title="Editar preço do produto">
													R$ {preco_formatted} <span class="font-size-075">/{produtounidade}</span>
												</button>
											</div>
											<!-- END BLOCK_ITEM_PRECO -->

											<!-- BEGIN EXTRA_BLOCK_FORM_ITEM_PRECO -->
											<form method="post" id="frm_sale_order_item_preco" class="fill" data-id_vendaitem="{id_vendaitem}">
												<div class="addon">
													<span>R$</span>
													<input
														type="number"
														id="preco"
														class="fill"
														step='0.01'
														min='0'
														max='999999.99'
														required
														value='{preco}'
														title="Preço do produto">
													<span class="font-size-075">/{produtounidade}</span>
												</div>
											</form>
											<!-- END EXTRA_BLOCK_FORM_ITEM_PRECO -->
										</div>
									</div>

									<div class="flex gap-10 flex-7">

										<div class="flex gap-10 flex-5">

											<div class="flex-3">
												<label class="caption">Desconto</label>

												<!-- BEGIN BLOCK_ITEM_DESCONTO -->
												<div class="addon">
													<button class="sale_order_item_desconto button-field textleft" title="Editar desconto do produto">
														R$ {desconto_formatted}
													</button>
												</div>
												<!-- END BLOCK_ITEM_DESCONTO -->

												<!-- BEGIN EXTRA_BLOCK_FORM_ITEM_DESCONTO -->
												<form method="post" id="frm_sale_order_item_desconto" class="fill" data-id_vendaitem="{id_vendaitem}">
													<div class="addon">
														<span>R$</span>
														<input
															type="number"
															id="desconto"
															class="fill"
															step='0.001'
															min='0'
															max='999999.999'
															required
															value='{desconto}'
															title="Desconto do produto"
															autofocus>
													</div>
												</form>
												<!-- END EXTRA_BLOCK_FORM_ITEM_DESCONTO -->
											</div>

											<div class="flex-3">
												<label class="caption color-blue">Total</label>
												<div class="addon color-blue">
													<span>R$ {total_formatted}</span>
												</div>
											</div>
										</div>

										{extra_block_saleorderitem_obs}

										<!-- BEGIN EXTRA_BLOCK_SALEORDERITEM_OBS -->
										<div class="w_saleorderitem_obs flex flex-ai-fe tooltip pos-rel">

											<!-- <div class="tooltip pos-rel"> -->

												<button type="button" class="bt_comment button-icon button-blue fa-solid fa-comment-dots" title="Observação do produto"></button>

												<span class="tooltiptext">{obs}</span>

												<div class="float-form hidden">

													<div>
														<button class="bt_commentclose button-icon button-gray fa-solid fa-xmark pos-abs" style="top: -15px; right: -15px;" title="Fechar"></button>
													</div>

													<form method="post" class="frm_saleorderitem_obs flex gap-10 fill" data-id_vendaitem='{id_vendaitem}'>
														<div>
															<label class="caption">Observação do Produto</label>
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
											<!-- </div> -->
										</div>
										<!-- END EXTRA_BLOCK_SALEORDERITEM_OBS -->

										<!-- BEGIN EXTRA_BLOCK_SALEORDERITEM_OBS_EMPTY -->
										<div class="w_saleorderitem_obs flex flex-ai-fe pos-rel">

											<button type="button" class="bt_comment button-icon button-blue fa-regular fa-comment" title="Observação do produto"></button>

											<div class="float-form hidden">

												<div>
													<button class="bt_commentclose button-icon button-gray fa-solid fa-xmark pos-abs" style="top: -15px; right: -15px;" title="Fechar"></button>
												</div>

												<form method="post" class="frm_saleorderitem_obs flex gap-10 fill" data-id_vendaitem='{id_vendaitem}'>
													<div>
														<label class="caption">Observação do Produto</label>
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
										<!-- END EXTRA_BLOCK_SALEORDERITEM_OBS_EMPTY -->

										<div class="flex flex-ai-fe">
											<button class="sale_order_item_bt_reverse button-icon button-red fa-solid fa-trash-can" title="Estornar produto"></button>
										</div>
									</div>
								</div>
							</div>
							<!-- END EXTRA_BLOCK_SALEORDER_ABERTO_ITEMS -->

							<!-- BEGIN EXTRA_BLOCK_SALEORDER_ABERTO_ITEMS_REVERSED -->
							<div class="w-saleorderitem window tr flex flex-dc gap-10" data-id_vendaitem="{id_vendaitem}" data-subtotal="0" data-desconto="0" data-total="0">

								<div class="flex-responsive gap-10">

									<div class="flex-6">
										<label class="caption">Produto</label>
										<div class="addon reversed">
											<span class="{class_status}">{id_produto}</span>
											<span class="textleft uppercase">{produto}</span>
										</div>
									</div>

									<div class="flex gap-10 flex-5">
										<div class="flex-2">
											<label class="caption">Quantidade</label>

											<div class="addon reversed">
												<span class="">{qtd_formatted} <span class="font-size-075">{produtounidade}</span></span>
											</div>
										</div>

										<div class="flex-2">
											<label class="caption">Preço</label>

											<div class="addon reversed">
												<span>R$ {preco_formatted} <span class="font-size-075">/{produtounidade}</span></span>
											</div>
										</div>
									</div>

									<div class="flex gap-10 flex-7">

										<div class="flex gap-10 flex-5">
											<div class="flex-2">
												<label class="caption">Desconto</label>

												<div class="addon reversed">
													<span>R$ {desconto_formatted}</span>
												</div>
											</div>

											<div class="flex-2">
												<label class="caption">Total</label>

												<div class="addon reversed">
													<span>R$ {total_formatted}</span>
												</div>
											</div>
										</div>

										{extra_block_saleorderitem_obs}

										<div class="flex flex-ai-fe">
											<button class="sale_order_item_bt_restore button-icon button-blue fa-solid fa-trash-can-arrow-up" title="Restaurar produto"></button>
										</div>
									</div>
								</div>
							</div>
							<!-- END EXTRA_BLOCK_SALEORDER_ABERTO_ITEMS_REVERSED -->
						</div>

						<!-- <div class="section-header">
							Adicionar Produto
						</div> -->

						<div class="flex-responsive">

							<form method='post' id="frm_sale_order_item" data-id_venda="{id_venda}">

								<div class="flex-responsive gap-10">
									<div class="flex gap-10">
										<div class="flex-3">
											<label class="caption">Produto [Código ou Descrição]</label>
											<div class="autocomplete-dropdown">
												<input
													type="text"
													id="product_search"
													class="uppercase product_search smart_search smart-search fill"
													data-source="popup"
													data-sort="active"
													data-focus_next="#qtd"
													maxlength="50"
													required
													placeholder=""
													autocomplete="off"
													title="Nome ou código do produto."
													autofocus>

												{block_product_autocomplete_search}
											</div>
										</div>

										<div>
											<label class="caption">Quantidade</label>
											<div class="addon">
												<input
													type="number"
													class="fill"
													id="qtd"
													step='0.001'
													min='0'
													max="999999.999"
													required
													title="Quantidade do produto."
													placeholder=''>
											</div>
										</div>
									</div>

									<div class="flex gap-10">
										<div class="flex-3">
											<label class="caption">Observação</label>
											<div class="addon">
												<input
													type="text"
													id="obs"
													class="fill"
													maxlength="255"
													title="Observação para o produto."
													placeholder="">
											</div>
										</div>

										<div class="flex flex-ai-fe">
											<button type='submit' class="button-blue fill" title="Adiciona o produto ao pedido">Adicionar</button>
										</div>
									</div>

								</div>
							</form>
						</div>

						<div class="section-header">
							Total
						</div>

						<div class="flex-responsive gap-10">

							<div class="flex gap-10 flex-6">
								<div class="flex-1">
									<label class="caption">Subtotal</label>
									<div class="addon">
										<span>R$ <span class="sale_subtotal textcenter">{subtotal_formatted}</span></span>
									</div>
								</div>

								<div class="flex-1 flex gap-10">
									<div class="flex-1">
										<label class="caption">Desconto</label>
										<div class="addon menu-inter">
											<span class="fill">R$ <span class="sale_desconto textcenter">{desconto_formatted}</span></span>

											<button type="button" class="saleorder_bt_discountclear button-icon button-blue" title="Limpar Desconto">
												<i class="fa-solid fa-sack-xmark"></i>
											</button>
										</div>
									</div>
								</div>
							</div>

							<div class="flex gap-10 flex-6">
								<div class="flex-1">
									<label class="caption">Serviço</label>
									<div class="addon">
										<span class="textcenter">R$ {valor_servico_formatted}</span>
									</div>
								</div>

								<div class="flex-1">
									<label class="caption">Frete</label>

									<!-- BEGIN BLOCK_FRETE -->
									<div class="addon">
										<button class="sale_order_bt_frete button-field textleft" data-id_venda='{id_venda}' title="Alterar valor do frete do pedido.">
											R$ <span class="sale_order_frete">{frete_formatted}</span>
										</button>
									</div>
									<!-- END BLOCK_FRETE -->

									<!-- BEGIN EXTRA_BLOCK_FORM_FRETE -->
									<form method="post" id="frm_sale_order_frete">
										<div class="addon">
											<span>R$</span>
											<input
												type="number"
												id="frete"
												class="fill"
												step='0.01'
												min='0'
												max='999999.99'
												title="Valor do Frete"
												value='{frete}'
												autocomplete="off"
												required>
										</div>
									</form>
									<!-- END EXTRA_BLOCK_FORM_FRETE -->
								</div>
							</div>

							<div class="flex gap-10 flex-4">
								<div class="flex-3">
									<label class="caption color-blue">Total</label>
									<div class="addon color-blue">
										<span>R$ <span class="sale_total textcenter">{total_formatted}</span></span>
									</div>
								</div>

								{extra_block_saleorder_menu}
							</div>

							<div class="flex-2"></div>
						</div>
					</div>

					<!-- BEGIN BLOCK_HISTORY_ORDER -->
					<div class="window flex flex-dc gap-10">

						<div class="flex padding-t20 gap-10">
							<div class="section-header no-padding no-margin flex-1">Histórico de Pedidos</div>
							<div class="flex flex-jc-fe flex-ai-fe">
								<button data-id_entidade="{id_entidade}" data-id_venda="{id_venda}" class="history_order_bt_expand button-icon button-blue fa-solid fa-chevron-down"></button>
							</div>
						</div>

						<div class="expandable" style="display: none;">
							<div class="entity_history_container table tbody flex flex-dc">

								<!-- BEGIN EXTRA_BLOCK_HISTORY_ORDER -->
								<div class="w_saleorder w_saleorder_{id_venda} tr window flex flex-dc gap-10" data-id_venda="{id_venda}" data-versao="{versao}" data-total="{total}">
									<div class="flex-responsive gap-10">

										<div class="flex gap-10 flex-6">
											<div class="flex gap-10 flex-3">
												<div class="flex-1">
													<label class="caption">{salelegend} #{id_venda}</label>

													{extra_block_saleorder_show_ticket}
												</div>
											</div>

											<div class="flex gap-10 flex-3">
												<div class="flex-1">
													<label class="caption">Data</label>
													<div class="addon flex-jc-center">
														<span>{data_formatted}</span>
													</div>
												</div>
											</div>
										</div>

										<div class="flex gap-10 flex-4">
											<div class="flex-2">
												<label class="caption">Subtotal</label>
												<div class="addon">
													<span>R$ {subtotal_formatted}</span>
												</div>
											</div>

											<div class="flex-2">
												<label class="caption">Desconto</label>
												<div class="addon">
													<span>R$ {desconto_formatted}</span>
												</div>
											</div>
										</div>

										<div class="flex gap-10 flex-4">
											<div class="flex-2">
												<label class="caption">Serviço</label>
												<div class="addon">
													<span>R$ {valor_servico_formatted}</span>
												</div>
											</div>

											<div class="flex-2">
												<label class="caption">Frete</label>
												<div class="addon">
													<span>R$ {frete_formatted}</span>
												</div>
											</div>
										</div>

										<div class="flex gap-10 flex-4">
											<div class="flex-3">
												<label class="caption color-blue">Total</label>
												<div class="addon color-blue">
													<span>R$ {total_formatted}</span>
												</div>
											</div>

											{extra_block_saleorder_menu}
										</div>
									</div>

									<div class="flex flex-dc gap-10 expandable" style="display: none;">
									</div>
								</div>
								<!-- END EXTRA_BLOCK_HISTORY_ORDER -->

								<!-- BEGIN EXTRA_BLOCK_BUTTON_LOAD_PAGE -->
								<div class="tr flex flex-jc-center">
									<button class="history_order_bt_page button-blue" data-id_entidade="{id_entidade}" data-page="{page}">Carregar Mais</button>
								</div>
								<!-- END EXTRA_BLOCK_BUTTON_LOAD_PAGE -->

							</div>

						</div>
					</div>
					<!-- END BLOCK_HISTORY_ORDER -->
				</div>
			</div>
		</div>
		<!-- END EXTRA_BLOCK_SALEORDER_EDIT -->

		<!-- BEGIN EXTRA_BLOCK_SALEORDER_SHOW -->
		<div class="w_saleorder w_saleorder_{id_venda} flex flex-dc gap-10" data-id_venda='{id_venda}' data-versao="{versao}" data-id_entidade="{id_entidade}" data-total='{total}' data-valor_servico='{valor_servico}' data-frete='{frete}'>

			<div class="flex-responsive gap-10">

				<div class="flex-10">
					<label class="caption">Cliente</label>
					<div class="addon">
						{extra_block_entity_button_status}
						<span class="entity_{id_entidade}_nome">{nome}</span>
					</div>
				</div>

				<div class="flex-3">
					{block_entity_credit}
				</div>

				<div class="flex gap-10 flex-5">

					<div class="flex-1">
						<label class="caption color-blue">Total Pedido</label>
						<div class="addon color-blue font-size-12">
							<span>
								<span class="sale_total">R$ {total_formatted}</span>
							</span>
						</div>
					</div>

					{extra_block_saleorder_obs}

					{extra_block_saleorder_menu}
				</div>
			</div>

			<div class="flex-responsive gap-10">

				<div class="flex gap-10 flex-3">
					<div class="flex gap-10 flex-2">
						<div class="flex-1">
							<label class="caption">{salelegend} #{id_venda}</label>

							{extra_block_saleorder_show_ticket}

							<!-- BEGIN EXTRA_BLOCK_SALEORDER_SHOW_TICKET_ANDAMENTO -->
							<div class="border-ticket border-red flex flex-jc-center">
								<span class="color-blue">Em Andamento</span>
							</div>
							<!-- END EXTRA_BLOCK_SALEORDER_SHOW_TICKET_ANDAMENTO -->

							<!-- BEGIN EXTRA_BLOCK_SALEORDER_SHOW_TICKET_EFETUADO -->
							<div class="border-ticket border-green flex flex-jc-center">
								<span class="color-blue">Confirmado</span>
							</div>
							<!-- END EXTRA_BLOCK_SALEORDER_SHOW_TICKET_EFETUADO -->

							<!-- BEGIN EXTRA_BLOCK_SALEORDER_SHOW_TICKET_PRODUCAO -->
							<div class="border-ticket border-orange flex flex-jc-center">
								<span class="color-blue">Em Produção</span>
							</div>
							<!-- END EXTRA_BLOCK_SALEORDER_SHOW_TICKET_PRODUCAO -->

							<!-- BEGIN EXTRA_BLOCK_SALEORDER_SHOW_TICKET_ENTREGA -->
							<div class="border-ticket border-blue flex flex-jc-center">
								<span class="color-blue">Em Entrega</span>
							</div>
							<!-- END EXTRA_BLOCK_SALEORDER_SHOW_TICKET_ENTREGA -->

							<!-- BEGIN EXTRA_BLOCK_SALEORDER_SHOW_TICKET_PRAZO -->
							<div class="border-ticket border-purple flex flex-jc-center">
								<span class="color-blue">A Prazo</span>
							</div>
							<!-- END EXTRA_BLOCK_SALEORDER_SHOW_TICKET_PRAZO -->

							<!-- BEGIN EXTRA_BLOCK_SALEORDER_SHOW_TICKET_PAGO -->
							<div class="border-ticket border-greendark flex flex-jc-center">
								<span class="color-blue">Pago</span>
							</div>
							<!-- END EXTRA_BLOCK_SALEORDER_SHOW_TICKET_PAGO -->

							<!-- BEGIN EXTRA_BLOCK_SALEORDER_SHOW_TICKET_CANCELADO -->
							<div class="border-ticket border-gray flex flex-jc-center">
								<span class="color-blue">Cancelado</span>
							</div>
							<!-- END EXTRA_BLOCK_SALEORDER_SHOW_TICKET_CANCELADO -->

							<!-- BEGIN EXTRA_BLOCK_SALEORDER_SHOW_TICKET_TRANSFERIDO -->
							<div class="border-ticket border-gray flex flex-jc-center">
								<span class="color-blue">Transferida</span>
							</div>
							<!-- END EXTRA_BLOCK_SALEORDER_SHOW_TICKET_TRANSFERIDO -->
						</div>
					</div>

					<div class="flex gap-10 flex-2">
						<div class="flex-1">
							<label class="caption">Data</label>
							<div class="addon flex-jc-center">
								<span>{data_formatted}</span>
							</div>
						</div>
					</div>
				</div>

				<div class="flex flex-dc flex-5">
					<label class="caption">Vendedor</label>
					<div class="addon">
						<span>{colaborador}</span>
					</div>
				</div>
			</div>

			<div class="saleorder-item">
				<div class="card-body flex flex-dc gap-10">
					<div class="section-header">
						Dados da Entrega
					</div>

					<div class="w-entityaddress flex-1">
						<label class="caption">Endereço de entrega</label>
						<div class="addon">
							<span>{extra_block_saleaddress}</span>
						</div>
					</div>

					<div class="section-header">
						Itens
					</div>

					<div class="table flex flex-dc gap-10">
						<div class="tbody flex flex-dc">

							{extra_block_tab_content_tr}
							<!-- BEGIN EXTRA_BLOCK_SALEORDER_FECHADO_ITEMS -->
							<div class="w-saleorderitem window tr flex flex-dc gap-10" data-id_vendaitem="{id_vendaitem}" data-subtotal="{subtotal}" data-desconto="{desconto}" data-total="{total}">

								<div class="flex-responsive gap-10">

									<div class="flex-7">
										<label class="caption">Produto</label>
										<div class="addon">
											<span class="{class_status}">{id_produto}</span>
											<span class="textleft">{produto}</span>
										</div>
									</div>

									<div class="flex gap-10 flex-5">

										<div class="flex-2">
											<label class="caption">Quantidade</label>

											<div class="addon">
												<span>{qtd_formatted} <span class="font-size-075">{produtounidade}</span></span>
											</div>
										</div>

										<div class="flex-2">
											<label class="caption">Preço</label>

											<div class="addon">
												<span>R$ {preco_formatted} <span class="font-size-075">/{produtounidade}</span></span>
											</div>
										</div>
									</div>

									<div class="flex gap-10 flex-6">

										<div class="flex gap-10 flex-5">

											<div class="flex-3">
												<label class="caption">Desconto</label>

												<div class="addon">
													<span>R$ {desconto_formatted}</span>
												</div>
											</div>

											<div class="flex-3">
												<label class="caption color-blue">Total</label>
												<div class="addon color-blue">
													<span>R$ {total_formatted}</span>
												</div>
											</div>
										</div>

										{extra_block_saleorderitem_obs}
									</div>
								</div>
							</div>
							<!-- END EXTRA_BLOCK_SALEORDER_FECHADO_ITEMS -->

							<!-- BEGIN EXTRA_BLOCK_SALEORDER_FECHADO_ITEMS_REVERSED -->
							<div class="w-saleorderitem window tr flex flex-dc gap-10" data-id_vendaitem="{id_vendaitem}" data-subtotal="0" data-desconto="0" data-total="0">

								<div class="flex-responsive gap-10">

									<div class="flex-7">
										<label class="caption">Produto</label>
										<div class="addon reversed">
											<span class="{class_status}">{id_produto}</span>
											<span class="textleft uppercase">{produto}</span>
										</div>
									</div>

									<div class="flex gap-10 flex-5">
										<div class="flex-2">
											<label class="caption">Quantidade</label>

											<div class="addon reversed">
												<span>{qtd_formatted} <span class="font-size-075">{produtounidade}</span></span>
											</div>
										</div>

										<div class="flex-2">
											<label class="caption">Preço</label>

											<div class="addon reversed">
												<span>R$ {preco_formatted} <span class="font-size-075">/{produtounidade}</span></span>
											</div>
										</div>
									</div>

									<div class="flex gap-10 flex-6">

										<div class="flex gap-10 flex-5">
											<div class="flex-3">
												<label class="caption">Desconto</label>

												<div class="addon reversed">
													<span>R$ {desconto_formatted}</span>
												</div>
											</div>

											<div class="flex-3">
												<label class="caption">Total</label>

												<div class="addon reversed">
													<span>R$ {total_formatted}</span>
												</div>
											</div>
										</div>

										{extra_block_saleorderitem_obs}
									</div>
								</div>
							</div>
							<!-- END EXTRA_BLOCK_SALEORDER_FECHADO_ITEMS_REVERSED -->
						</div>

						<div class="section-header">
							Total
						</div>

						<div class="flex-responsive gap-10">

							<div class="flex gap-10 flex-6">
								<div class="flex-1">
									<label class="caption">Subtotal</label>
									<div class="addon">
										<span>R$ <span class="sale_subtotal textcenter">{subtotal_formatted}</span></span>
									</div>
								</div>

								<div class="flex-1">
									<label class="caption">Desconto</label>
									<div class="addon">
										<span>R$ <span class="sale_desconto textcenter">{desconto_formatted}</span></span>
									</div>
								</div>
							</div>

							<div class="flex gap-10 flex-6">
								<div class="flex-1">
									<label class="caption">Serviço</label>

									<div class="addon">
										<span class="textcenter">R$ {valor_servico_formatted}</span>
									</div>
								</div>

								<div class="flex-1">
									<label class="caption">Frete</label>

									<div class="addon">
										<span class="textcenter">R$ {frete_formatted}</span>
									</div>
								</div>
							</div>

							<div class="flex gap-10 flex-4">
								<div class="flex-3">
									<label class="caption color-blue">Total</label>
									<div class="addon color-blue">
										<span>R$ <span class="sale_total textcenter">{total_formatted}</span></span>
									</div>
								</div>

								{extra_block_saleorder_menu}
							</div>

							<div class="flex-2"></div>
						</div>

						<div class="section-header">
							Forma de pagamento
						</div>

						<div class="flex-responsive gap-10">
							<div class="flex-5">
								{extra_block_saleorder_payment}
							</div>
							<div class="flex-11"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- END EXTRA_BLOCK_SALEORDER_SHOW -->

	</div>
</div>

<!-- <div class="footer-popup flex-jc-fe mobile">
	<div class="popup-menu">
		<button class="popup-main-button fa-solid fa-ellipsis-vertical button-pink"></button>

		<ul>
			<li class="entity_bt_new flex flex-ai-center gap-10" title="Cadastrar novo cliente">

				<span>Novo cliente</span>
			</li>
		</ul>
	</div>
</div> -->
<!-- END BLOCK_PAGE -->