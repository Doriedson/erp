<!-- BEGIN BLOCK_PAGE -->

<!-- BEGIN EXTRA_BLOCK_POPUP_PURCHASEORDER_NEW -->
<form method="post" id="frm_purchase_order">

	<div class="flex flex-dc gap-10">

		<div>
			<label class="caption">Data</label>
			<div>
				<input
				type="date"
				id="data"
				class="fill"
				value="{date}"
				required>
			</div>
		</div>

		<div class="autocomplete-dropdown">
			<label class="caption">Fornecedor</label>
			<div>
				<input
					type="text"
					id="provider_search"
					class="provider_search smart_search smart-search fill"
					data-focus_next="#obs"
					data-source="popup"
					autofocus
					placeholder=""
					required
					autocomplete="off"
					maxlength="100">
				{block_provider_autocomplete_search}
			</div>
		</div>

		<div>
			<label class="caption">Observação</label>
			<div>
				<input
					type="text"
					id="obs"
					class="fill"
					placeholder=""
					maxlength="255"
					autocomplete="off">
			</div>
		</div>

		<div class="flex gap-10">
			<div class="flex-1">
				<label class="caption">Lista</label>
				<div>
					<select id="lista" class="fill" >
						<option value="0" selected>Nenhuma lista</option value="0">
						{compra_lista}
					</select>
				</div>
			</div>

			<div class="flex flex-ai-fe">
				<button type="submit" class="button-blue fill">Cadastrar</button>
			</div>
		</div>
	</div>
</form>
<!-- END EXTRA_BLOCK_POPUP_PURCHASEORDER_NEW -->

<div class="box-container flex flex-dc gap-10">

	<div class="box-header gap-10">
		<i class="icon fa-solid fa-file-invoice-dollar"></i>
		<span>Compra / Ordem de Compra</span>
	</div>

	<div class="flex-responsive gap-10 flex-jc-sb">

		<form method="post" id="frm_purchase_order_search">

			<div class="flex-responsive gap-10">

				<div class="flex gap-10">

					<div class="flex-2">
						<label class="caption flex flex-ai-center gap-5">
							<i class="fa-solid fa-magnifying-glass"></i>
							Status
						</label>
						<div class="addon">
							<select id="status" class="fill" autofocus>
								<option value="0">Todos</option>
								{compra_status}
								<!-- BEGIN EXTRA_BLOCK_PURCHASE_STATUS -->
								<option value="{id_comprastatus}">{comprastatus}</option>
								<!-- END EXTRA_BLOCK_PURCHASE_STATUS -->
							</select>
						</div>
					</div>

					<div class="flex-2">
						<label class="caption">Data</label>
						<div class="addon">
							<input type='date' id="dataini" class="fill" value='{date}' required>
						</div>
					</div>
				</div>

				<div class="flex gap-10">
					<div class="flex-2">
						<label class="caption">até</label>
						<div class="addon">
							<span class="flex">
								<input type="checkbox" id='intervalo'>
							</span>
							<input type='date' id="datafim" class="fill" min='{date}' value='{date}' required disabled>
						</div>
					</div>

					<div class="flex flex-ai-fe flex-2">
						<button type="submit" class="button-blue fill" title="Procura ordem de compra por data.">Procurar</button>
					</div>
				</div>
			</div>
		</form>

		<div class="flex flex-ai-fe gap-10 desktop">
			<button type="button" class="purchaseorder_bt_show_new button-blue" title="Cadastrar nova ordem de compras">Nova Ordem</button>
			<button type="button" class="purchase_order_bt_list button-blue" title="Listar ordens em aberto">Em Aberto</button>
		</div>
	</div>
</div>

<div class="box-container setor-2 flex flex-jc-sb gap-10">

	<div>
		Ordens
	</div>

	<div class="flex flex-jc-fe fill">
		<span>Total R$ <span class="w-purchaseorder-total">{purchaseorder_total_formatted}</span></span>
	</div>

</div>

<div class="w-purchaseorder-container flex flex-dc gap-10">

	<div class="purchaseorder_not_found box-container window {purchase_notfound}">
		<div class="font-size-12 textcenter" style="padding: 80px 10px;">
			Nenhuma Ordem de Compra encontrada.
		</div>
	</div>

	<div class="purchaseorder_table flex flex-dc gap-10">

		{extra_block_purchase}

		<!-- BEGIN EXTRA_BLOCK_PURCHASE_ABERTO -->
		<div class="w-purchaseorder window flex flex-dc gap-10 box-container" data-id_compra='{id_compra}' data-total="{total}">

			<div class="flex-responsive gap-10">

				<div class="flex gap-10 flex-6">
					<div class="flex-3">
						<label class="caption">OC #{id_compra}</label>
						<div class="border-ticket border-green flex flex-jc-center">
							<span class="color-blue">Aberto</span>
						</div>
					</div>

					<div class="flex-3">
						<label class="caption">Data</label>
						<div class="addon">
							<!-- BEGIN BLOCK_DATA -->
							<button class="purchase_order_bt_data button-field textleft nowrap fill" title="Alterar data">
								{data_formatted}

							</button>
							<!-- END BLOCK_DATA -->
							<!-- BEGIN EXTRA_BLOCK_FORM_DATA -->
							<form method="post" id="frm_purchase_order_date" class="fill" data-id_compra="{id_compra}">
								<input
									type="date"
									id="data"
									class="fill"
									value="{data}"
									required
									autofocus
								>
							</form>
							<!-- END EXTRA_BLOCK_FORM_DATA -->
						</div>
					</div>

					<!-- <div class="flex gap-10 flex-2">
						<div class="flex-1">
							<label class="caption">Status</label>
							<div class="addon">
								<span class="field fill flex-jc-center color-green font-size-15">Aberto</span>
							</div>
						</div>
					</div> -->
				</div>

				<div class="flex-6">
					<label class="caption">Fornecedor</label>
					<div class="addon">
						<!-- BEGIN BLOCK_PROVIDER -->
						<button class="purchase_order_bt_provider button-field textleft fill" title="Escolher fornecedor">
							{razaosocial}

						</button>
						<!-- END BLOCK_PROVIDER -->
						<!-- BEGIN EXTRA_BLOCK_FORM_PROVIDER -->
						<form method="post" id="frm_purchase_order_provider" class="fill flex" data-id_compra="{id_compra}">
							<div class="autocomplete-dropdown flex-1">
								<input
									type="text"
									id="razaosocial"
									class="provider_search smart_search smart-search fill"
									data-source="popup"
									placeholder="Fornecedor"
									required
									maxlength="100"
									value="{razaosocial}"
									autocomplete="off"
									autofocus>
									{block_provider_autocomplete_search}
							</div>
							<button type="button" class="purchaseorder_bt_provider_cancel button-blue" title="Cancelar edição">Cancelar</button>
						</form>
						<!-- END EXTRA_BLOCK_FORM_PROVIDER -->
					</div>
				</div>

				<div class="flex gap-10 flex-6">
					<div class="flex-3">
						<label class="caption color-blue">Total</label>
						<div class="addon color-blue font-size-12">
							<span>R$ <span class="purchaseorder-total">{total_formatted}</span></span>
						</div>
					</div>

					{extra_block_purchaseorder_obs}

					<!-- BEGIN EXTRA_BLOCK_PURCHASEORDER_OBS -->
					<div class="w_purchaseorder_obs flex flex-ai-fe">

						<div class="tooltip pos-rel">

							<button type="button" class="bt_comment button-icon button-blue fa-solid fa-comment-dots" title="Observação da Ordem de Compra"></button>

							<span class="tooltiptext">{obs}</span>

							<div class="float-form hidden">

								<div>
									<button class="bt_commentclose button-icon button-gray fa-solid fa-xmark pos-abs" style="top: -15px; right: -15px;" title="Fechar"></button>
								</div>

								<form method="post" class="frm_purchase_order_note flex gap-10 fill" data-id_compra='{id_compra}'>
									<div>
										<label class="caption">Observação da OC</label>
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
					<!-- END EXTRA_BLOCK_PURCHASEORDER_OBS -->

					<!-- BEGIN EXTRA_BLOCK_PURCHASEORDER_OBS_EMPTY -->
					<div class="w_purchaseorder_obs flex flex-ai-fe pos-rel">

						<button type="button" class="bt_comment button-icon button-blue fa-regular fa-comment" title="Observação da Ordem de Compra"></button>

						<div class="float-form hidden">

							<div>
								<button class="bt_commentclose button-icon button-gray fa-solid fa-xmark pos-abs" style="top: -15px; right: -15px;" title="Fechar"></button>
							</div>

							<form method="post" class="frm_purchase_order_note flex gap-10 fill" data-id_compra='{id_compra}'>
								<div>
									<label class="caption">Observação da OC</label>
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
					<!-- END EXTRA_BLOCK_PURCHASEORDER_OBS_EMPTY -->

					<div class="flex flex-ai-fe flex-jc-right gap-10">
						<!-- <div class="flex ai-center flex-jc-right gap-10"> -->
							<div class="menu-inter">
								<button class="menu-inter-button fa-solid fa-ellipsis-vertical button-blue"></button>

								<ul>

									<li class="bt_purchase_order_close flex flex-ai-center gap-10 color-blue" data-id_compra="{id_compra}" title="Fechar ordem de compra e lançar estoque">
										<i class="icon fa-solid fa-clipboard-check"></i>
										<span>Finalizar ordem</span>
									</li>

									<li class="bt_purchase_order_delete flex flex-ai-center gap-10 color-red" data-id_compra="{id_compra}" title="Cancelar ordem de compra">
										<i class="icon fa-solid fa-file-excel"></i>
										<span>Cancelar ordem</span>
									</li>

									<li class="bt_purchaseorder_estimate flex flex-ai-center gap-10 color-blue" data-id_compra="{id_compra}" title="Calcular valor baseado na última compra">
										<i class="icon fa-solid fa-file-invoice-dollar"></i>
										<span>Estimar valor</span>
									</li>


									<li class="purchase_order_bt_print flex flex-ai-center gap-10 color-blue" data-id_compra="{id_compra}" title="Imprimir para conferência">
										<i class="icon fa-solid fa-print"></i>
										<span>Imprimir</span>
									</li>

									<li class="purchase_order_bt_whatsapp flex flex-ai-center gap-10 color-green" data-id_compra="{id_compra}" title="Envia a Ordem de Compra através do WhtasApp">
										<i class="icon fa-brands fa-whatsapp"></i>
										<span>WhatsApp</span>
									</li>

									<li class="purchase_order_bt_copy flex flex-ai-center gap-10 color-blue" data-id_compra="{id_compra}" title="Copia o pedido para área de transferência">
										<i class="icon fa-solid fa-copy"></i>
										<span>Copia e Cola</span>
									</li>
								</ul>
							</div>

							<div>
								<button class="purchase_bt_expand button-icon button-blue fa-solid fa-chevron-down"></button>
							</div>
						<!-- </div> -->
					</div>
				</div>
			</div>

			<div class="expandable" style="display: none;">

				<!-- BEGIN EXTRA_BLOCK_PURCHASE_ABERTO_CONTAINER -->
				<div class="card-body flex flex-dc gap-10">
					<div class="section-header">
						Itens
					</div>

					<div class="table flex flex-dc gap-10">

						<div class="tbody flex flex-dc">

							{extra_block_purchase_aberto_container_item}

							<!-- BEGIN EXTRA_BLOCK_PURCHASE_CONTAINER_ITEM_NONE -->
							<div class="w-purchaseorder-item-notfound padding-v10">
								Não há itens.
							</div>
							<!-- END EXTRA_BLOCK_PURCHASE_CONTAINER_ITEM_NONE -->

							<!-- BEGIN EXTRA_BLOCK_PURCHASE_ABERTO_CONTAINER_ITEM -->
							<div class="w-purchaseorder-item tr flex flex-dc gap-10" data-produto="{produto}" data-id_compraitem="{id_compraitem}" data-vol='{vol}' data-custo='{custo}' data-custo_history="{custohistoryun}">

								<div class="flex-responsive gap-10">

									<div class="flex-13">
										<label class="caption">{produtotipo}</label>
										<div class="addon">
											{extra_block_product_button_status}
											{block_product_produto}
											{block_product_menu}
										</div>
									</div>

									<div class="flex gap-10 flex-5">
										<div class="flex-3">
											<label class="caption">Estoque</label>
											<div class="addon menu-inter">
												<span class="estoque_{id_produto} fill">{estoque_formatted} <span class="font-size-075">{produtounidade}</span></span>

												<ul>
													<li class="product_bt_estoque flex flex-ai-center gap-10 color-green" data-id_produto="{id_produto}" data-screen="add" title="Adicionar produtos ao estoque">
														<i class="icon fa-solid fa-square-plus"></i>
														<span>Adicionar estoque</span>
													</li>

													<li class="product_bt_estoque flex flex-ai-center gap-10 color-red" data-id_produto="{id_produto}" data-screen="del" title="Reduzir estoque de produtos ">
														<i class="icon fa-solid fa-square-minus"></i>
														<span>Reduzir estoque</span>
													</li>

													<li class="product_bt_estoque flex flex-ai-center gap-10 color-blue" data-id_produto="{id_produto}" data-screen="update" title="Atualizar estoque de produtos">
														<i class="icon fa-solid fa-equals"></i>
														<span>Atualizar estoque</span>
													</li>

													<li class="product_bt_estoque flex flex-ai-center gap-10 color-blue" data-id_produto="{id_produto}" data-screen="transf" title="Transferir estoque primário para secundário">
														<i class="icon fa-solid fa-left-right"></i>
														<span>Transferir estoque</span>
													</li>
												</ul>

												<button class="menu-inter-button fa-solid fa-ellipsis-vertical button-blue"></button>
											</div>
										</div>

										{extra_block_purchaseorderitem_obs}

										<!-- BEGIN EXTRA_BLOCK_PURCHASEORDERITEM_OBS -->
										<div class="w_purchaseorderitem_obs flex flex-ai-fe">

											<div class="tooltip pos-rel">

												<button type="button" class="bt_comment button-icon button-blue fa-solid fa-comment-dots" title="Observação do Item"></button>

												<span class="tooltiptext">{obs}</span>

												<div class="float-form hidden">

													<div>
														<button class="bt_commentclose button-icon button-gray fa-solid fa-xmark pos-abs" style="top: -15px; right: -15px;" title="Fechar"></button>
													</div>

													<form method="post" class="frm_purchase_order_item_obs flex gap-10 fill" data-id_compraitem='{id_compraitem}'>
														<div>
															<label class="caption">Observação do Item</label>
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
										<!-- END EXTRA_BLOCK_PURCHASEORDERITEM_OBS -->

										<!-- BEGIN EXTRA_BLOCK_PURCHASEORDERITEM_OBS_EMPTY -->
										<div class="w_purchaseorderitem_obs flex flex-ai-fe pos-rel">

											<button type="button" class="bt_comment button-icon button-blue fa-regular fa-comment" title="Observação do Item"></button>

											<div class="float-form hidden">

												<div>
													<button class="bt_commentclose button-icon button-gray fa-solid fa-xmark pos-abs" style="top: -15px; right: -15px;" title="Fechar"></button>
												</div>

												<form method="post" class="frm_purchase_order_item_obs flex gap-10 fill" data-id_compraitem='{id_compraitem}'>
													<div>
														<label class="caption">Observação do Item</label>
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
										<!-- END EXTRA_BLOCK_PURCHASEORDERITEM_OBS_EMPTY -->
									</div>
								</div>

								<div class="flex-responsive gap-10 flex-18">

									<div class="flex flex-6 gap-10">
										<div class="flex-3">
											<label class="caption">Volume</label>
											<div class="addon">
												<button class="purchase_order_item_bt_vol button-field textleft nowrap" title="Editar volume de entrada do produto">
													{vol_formatted}
												</button>
											</div>
										</div>

										<div class="flex-3">
											<label class="caption">Qtd / Vol</label>
											<div class="addon">
												<button class="purchase_order_item_bt_qtdvol button-field textleft" title="Editar Custo de entrada do produto">
													{qtdvol_formatted} <span class="font-size-075">{produtounidade}</span>
												</button>
											</div>
										</div>
									</div>

									<div class="flex flex-6 gap-10">
										<div class="flex-3">
											<label class="caption">Custo / Vol</label>

											<div class="addon">
												<button class="purchase_order_item_bt_custo button-field textleft nowrap" title="Editar Custo de entrada do produto">
													R$ {custo_formatted}
												</button>

												{custo_arrow}
											</div>
										</div>

										<div class="flex-3">
											<label class="caption">Custo Atual</label>
											<div class="addon">
												<div class="flex flex-dc flex-jc-center flex-ai-fs addon font-size-09">
													<span>R$ {custo_unidade_formatted} <span class="font-size-075">/{produtounidade}</span></span>
													<span class="color-red one-line {custo_ajustado_visible}">R$ {custo_unidade_ajustado_formatted} <span class="font-size-075">/{produtounidade}</span> <i class="fa-solid fa-circle-info" title="Custo ajustado com margem de perda de {margem_perda_formatted}%"></i></span>
												</div>
												{custo_arrow}

												<!-- BEGIN EXTRA_BLOCK_PURCHASE_ITEM_CUSTO_ARROW_EQUAL -->
												<div class='pseudo-button color-blue' title='Custo igual a última compra'>
													<i class="icon fa-solid fa-left-right"></i>
												</div>
												<!-- END EXTRA_BLOCK_PURCHASE_ITEM_CUSTO_ARROW_EQUAL -->
												<!-- BEGIN EXTRA_BLOCK_PURCHASE_ITEM_CUSTO_ARROW_DOWN -->
												<div class='pseudo-button color-green' title='Custo menor do que a última compra'>
													<i class="icon fa-solid fa-down-long"></i>
												</div>
												<!-- END EXTRA_BLOCK_PURCHASE_ITEM_CUSTO_ARROW_DOWN -->
												<!-- BEGIN EXTRA_BLOCK_PURCHASE_ITEM_CUSTO_ARROW_UP -->
												<div class='pseudo-button color-red' title='Custo maior do que a última compra'>
													<i class="icon fa-solid fa-up-long"></i>
												</div>
												<!-- END EXTRA_BLOCK_PURCHASE_ITEM_CUSTO_ARROW_UP -->
											</div>
										</div>
									</div>

									<div class="flex gap-10 flex-6">
										<div class="flex-3">
											<label class="caption">Custo Anterior</label>
											<div class="flex flex-dc flex-jc-center addon font-size-09">
												<div>
													<span >R$</span>
													<span class="field">{custohistory_formatted}</span>
													<span class="font-size-075">/Vol</span>
												</div>
												<div>
													<span>R$ {custohistoryun_formatted} <span class="font-size-075">/{produtounidade}</span></span>
												</div>
											</div>
										</div>

										<!-- <div class="flex  gap-10"></div> -->
										<div class="flex flex-3 flex-jc-fe flex-ai-fe gap-10">

											<button type="button" class="bt_delete_purchase_order_item flex flex-ai-center gap-10 button-red" data-id_compraitem="{id_compraitem}" title="Excluir produto da ordem de compra">
												<i class="icon fa-solid fa-trash-can"></i>
											</button>

											<button type="button" class="product_bt_validade button-blue flex flex-ai-center gap-10" data-id_produto="{id_produto}" title="Cadastro de validade do produto">
													<i class="icon fa-solid fa-calendar-days"></i>
											</button>

											<button type="button" class="bt_purchaseorder_history flex flex-ai-center gap-10 button-blue" data-id_produto="{id_produto}" title="Histórico de venda do produto">
												<i class="icon fa-solid fa-chart-column"></i>
											</button>
											<!-- <div class="menu-inter">
												<button class="menu-inter-button fa-solid fa-ellipsis-vertical button-blue"></button>

												<ul>
													<li class="bt_delete_purchase_order_item flex flex-ai-center gap-10 color-red" data-id_compraitem="{id_compraitem}" title="Excluir produto da ordem de compra">
														<i class="icon fa-solid fa-trash-can"></i>
														<span>Remover produto</span>
													</li>

													<li class="product_bt_validade flex flex-ai-center gap-10 color-blue" data-id_produto="{id_produto}" title="Cadastro de validade do produto">
														<i class="icon fa-solid fa-calendar-days"></i>
														<span>Controle de Validade</span>
													</li>

													<li class="bt_purchaseorder_history flex flex-ai-center gap-10 color-blue" data-id_produto="{id_produto}" data-produto="{produto}"  title="Histórico de venda do produto">
														<i class="icon fa-solid fa-chart-column"></i>
														<span>Histórico de Venda</span>
													</li>
												</ul>
											</div> -->
										</div>
									</div>
								</div>
							</div>
							<!-- END EXTRA_BLOCK_PURCHASE_ABERTO_CONTAINER_ITEM -->
						</div>

						<!-- <div class="section-header">
							Adicionar Produto
						</div> -->

						{extra_block_product_form}
						<!-- BEGIN EXTRA_BLOCK_PRODUCT_FORM -->
						<form method="post" id="frm_purchase_order_item" class="flex-responsive" data-id_compra="{id_compra}">

							<div class="flex gap-10">

								<div class="fill">
									<label class="caption">Produto [Código ou Descrição]</label>

									<div class="autocomplete-dropdown">
										<input
											type="text"
											id="product_search"
											class="uppercase product_search smart_search smart-search fill"
											data-source="popup"
											maxlength="50"
											required
											placeholder=""
											autocomplete="off"
											autofocus>

										{block_product_autocomplete_search}
									</div>
								</div>

								<div class='flex flex-ai-fe gap-10'>
									<button type="submit" title="Adicionar produto à ordem de compra." class="button-blue fill">Adicionar</button>

									<button type="button" class="bt_product_new button-blue button-icon" data-window="purchase_order" data-id_compra="{id_compra}" data-id_produtosetor="0" title="Cadastrar novo produto">
										<i class="fa-solid fa-square-plus"></i>
									</button>
								</div>


							</div>
						</form>
						<!-- END EXTRA_BLOCK_PRODUCT_FORM -->
					</div>
				</div>
				<!-- END EXTRA_BLOCK_PURCHASE_ABERTO_CONTAINER -->
			</div>
		</div>
		<!-- END EXTRA_BLOCK_PURCHASE_ABERTO -->

		<!-- BEGIN EXTRA_BLOCK_PURCHASE_FECHADO -->
		<div class="w-purchaseorder window flex flex-dc gap-10 box-container" data-id_compra='{id_compra}' data-total="{total}">

			<div class="flex-responsive gap-10">

				<div class="flex gap-10 flex-6">
					<div class="flex-3">
						<label class="caption">OC #{id_compra}</label>
						<div class="border-ticket border-blue flex flex-jc-center">
							<span class="color-blue">Fechado</span>
						</div>
					</div>

					<div class="flex-3">
						<label class="caption">Data</label>
						<div class="addon">
							<span class="field fill">{data_formatted}</span>
						</div>
					</div>
				</div>

				<div class="flex-6">
					<label class="caption">Fornecedor</label>
					<div class="addon">
						<span class="field">{razaosocial}</span>
					</div>
				</div>

				<div class="flex gap-10 flex-6">
					<div class="flex-3">
						<label class="caption color-blue">Total</label>
						<div class="addon color-blue font-size-12">
							<span>R$ <span class="purchaseorder-total">{total_formatted}</span></span>
						</div>
					</div>

					{extra_block_purchaseorder_obs}

					<div class="flex flex-ai-fe flex-jc-right gap-10">
						<!-- <div class="flex ai-center flex-jc-right gap-10"> -->
							<div class="menu-inter">
								<button class="menu-inter-button fa-solid fa-ellipsis-vertical button-blue"></button>

								<ul>
									<li class="purchase_order_bt_print flex flex-ai-center gap-10 color-blue" data-id_compra="{id_compra}" title="Imprimir para conferência">
										<i class="icon fa-solid fa-print"></i>
										<span>Imprimir</span>
									</li>

									<li class="purchase_order_bt_whatsapp flex flex-ai-center gap-10 color-green" data-id_compra="{id_compra}" title="Envia a Ordem de Compra através do WhtasApp">
										<i class="icon fa-brands fa-whatsapp"></i>
										<span>WhatsApp</span>
									</li>

									<li class="purchase_order_bt_copy flex flex-ai-center gap-10 color-blue" data-id_compra="{id_compra}" title="Copia o pedido para área de transferência">
										<i class="icon fa-solid fa-copy"></i>
										<span>Copia e Cola</span>
									</li>
								</ul>
							</div>

							<div>
								<button class="purchase_bt_expand button-icon button-blue fa-solid fa-chevron-down"></button>
							</div>
						<!-- </div> -->
					</div>
				</div>
			</div>

			<div class="expandable" style="display: none;">
				<!-- BEGIN EXTRA_BLOCK_PURCHASE_FECHADO_CONTAINER -->
				<div class="card-body flex flex-dc gap-10">

					<div class="section-header">
						Itens
					</div>

					<div class="table flex flex-dc gap-10">

						<div class="tbody flex flex-dc">

							{extra_block_purchase_fechado_container_item}
							<!-- BEGIN EXTRA_BLOCK_PURCHASE_FECHADO_CONTAINER_ITEM -->
							<div class="w-purchaseorder-item tr flex flex-dc gap-10" data-produto="{produto}" data-id_compraitem="{id_compraitem}" data-id_produto="{id_produto}" data-custo_unidade="{custo_unidade}" data-vol='{vol}' data-custo='{custo}'>

								<div class="flex-responsive flex-jc-sb gap-10">

									<div class="flex-14">
										<label class="caption">{produtotipo}</label>
										<div class="addon">
											{extra_block_product_button_status}
											{block_product_produto}
											{block_product_menu}
										</div>
									</div>

									<div class="flex gap-10 flex-4">

										<div class="flex-3">
											<label class="caption">Estoque</label>
											<div class="addon menu-inter">
												<span class="estoque_{id_produto} field fill">{estoque_formatted} <span class="font-size-075">{produtounidade}</span></span>

												<ul>
													<li class="product_bt_estoque flex flex-ai-center gap-10 color-green" data-id_produto="{id_produto}" data-screen="add" title="Adicionar produtos ao estoque">
														<i class="icon fa-solid fa-square-plus"></i>
														<span>Adicionar estoque</span>
													</li>

													<li class="product_bt_estoque flex flex-ai-center gap-10 color-red" data-id_produto="{id_produto}" data-screen="del" title="Reduzir estoque de produtos ">
														<i class="icon fa-solid fa-square-minus"></i>
														<span>Reduzir estoque</span>
													</li>

													<li class="product_bt_estoque flex flex-ai-center gap-10 color-blue" data-id_produto="{id_produto}" data-screen="update" title="Atualizar estoque de produtos">
														<i class="icon fa-solid fa-equals"></i>
														<span>Atualizar estoque</span>
													</li>

													<li class="product_bt_estoque flex flex-ai-center gap-10 color-blue" data-id_produto="{id_produto}" data-screen="transf" title="Transferir estoque primário para secundário">
														<i class="icon fa-solid fa-left-right"></i>
														<span>Transferir estoque</span>
													</li>
												</ul>

												<button class="menu-inter-button fa-solid fa-ellipsis-vertical button-blue"></button>
											</div>
										</div>

										{extra_block_purchaseorderitem_obs}
									</div>

								</div>

								<div class="flex-responsive gap-10">

									<div class="flex gap-10 flex-6">
										<div class="flex-2">
											<label class="caption">Volume</label>
											<div class="addon">
												<!-- BEGIN BLOCK_ITEM_VOL -->
												<button class="purchase_order_item_bt_vol button-field textleft nowrap fill" title="Editar volume de entrada do produto">
													{vol_formatted}
												</button>
												<!-- END BLOCK_ITEM_VOL -->
												<!-- BEGIN EXTRA_BLOCK_FORM_ITEM_VOL -->
												<form method="post" id="frm_purchase_order_item_vol" class="fill" data-id_compraitem="{id_compraitem}" data-id_produto="{id_produto}">
													<input
														type="number"
														id="vol"
														class="fill"
														placeholder="0,000"
														min="0"
														max="999999.999"
														step="0.001"
														value="{vol}"
														autofocus
														required>
												</form>
												<!-- END EXTRA_BLOCK_FORM_ITEM_VOL -->
											</div>
										</div>

										<div class="flex-2">
											<label class="caption">Qtd / Vol</label>
											<div class="flex flex-jc-sb flex-ai-center">
												<div class="addon">
													<!-- BEGIN BLOCK_ITEM_QTDVOL -->
													<button class="purchase_order_item_bt_qtdvol button-field textleft nowrap fill" title="Editar Custo de entrada do produto">
														{qtdvol_formatted} <span class="font-size-075">{produtounidade}</span>
													</button>
													<!-- END BLOCK_ITEM_QTDVOL -->
													<!-- BEGIN EXTRA_BLOCK_FORM_ITEM_QTDVOL -->
													<form method="post" id="frm_purchase_order_item_qtdvol" class="flex flex-ai-center fill" data-id_compraitem="{id_compraitem}" data-id_produto="{id_produto}">
														<input
															type="number"
															id="qtdvol"
															class="fill"
															placeholder="0,000"
															min="0"
															max="999999.999"
															step="0.001"
															value="{qtdvol}"
															autofocus
															required>

														<span class="padding-h5 font-size-075">{produtounidade}</span>
													</form>
													<!-- END EXTRA_BLOCK_FORM_ITEM_QTDVOL -->

												</div>
											</div>
										</div>
									</div>

									<div class="flex gap-10 flex-6">
										<div class="flex-3">
											<label class="caption">Custo / Vol</label>
											<div class="addon">
												<!-- BEGIN BLOCK_ITEM_CUSTO -->
												<button class="purchase_order_item_bt_custo button-field textleft nowrap fill" title="Editar Custo de entrada do produto">
													R$ {custo_formatted}

												</button>
												<!-- END BLOCK_ITEM_CUSTO -->
												<!-- BEGIN EXTRA_BLOCK_FORM_ITEM_CUSTO -->
												<form method="post" class="flex flex-ai-center fill" id="frm_purchase_order_item_custo" data-id_compraitem="{id_compraitem}" data-id_produto="{id_produto}">
													<span class="padding-h5">R$</span>
													<input
													type="number"
													id="custo"
													class="fill"
													placeholder="0,00"
													min="0"
													max="999999.99"
													step="0.01"
													value="{custo}"
													autofocus
													required>
												</form>
												<!-- END EXTRA_BLOCK_FORM_ITEM_CUSTO -->
											</div>
										</div>

										<div class="flex-3">
											<label class="caption">Custo</label>
											<div class="flex flex-dc flex-jc-center flex-ai-fs addon font-size-09">
												<span>R$ {custo_unidade_formatted} <span class="font-size-075">/{produtounidade}</span></span>
												<span class="color-red one-line {custo_ajustado_visible}">R$ {custo_unidade_ajustado_formatted} <span class="font-size-075">/{produtounidade}</span> <i class="fa-solid fa-circle-info" title="Custo ajustado com margem de perda de {margem_perda_formatted}%"></i></span>
											</div>
										</div>
									</div>

									<div class="flex gap-10 flex-8">

										{block_group_preco}

										<div class="flex flex-ai-fe">

											<div class="tooltip pos-rel">

												<button type="button" class="button-icon button-blue fa-solid fa-hand-holding-dollar" title="Sugestões de Preços"></button>

												<span class="tooltiptext">

													<div class="flex flex-dc gap-10 padding-h10">

														<label class="caption one-line">Sugestão de Preço</label>

														<div class="flex flex-jc-sb flex-ai-fe gap-10">
															<span class="one-line">R$ {custo0} <span class="font-size-075">/{produtounidade}</span></span>
															<span class="font-size-09">({margem_lucro_formatted}%)</span>
														</div>

														<!-- <span>R$ {custo1} (30,00%)</span>
														<span>R$ {custo2} (40,00%)</span>
														<span>R$ {custo3} (50,00%)</span>
														<span>R$ {custo4} (60,00%)</span> -->
													</div>
												</span>

											</div>
										</div>
									</div>
								</div>

								{extra_block_purchase_fechado_container_composition}
							</div>
							<!-- END EXTRA_BLOCK_PURCHASE_FECHADO_CONTAINER_ITEM -->

							<!-- BEGIN EXTRA_BLOCK_PURCHASE_FECHADO_CONTAINER_COMPOSITION -->
							<div class="w-purchaseorder-item  flex flex-dc gap-10" data-produto="{produto}" data-id_compraitem="{id_compraitem}" data-id_produto="{id_produto}" data-custo_unidade="{custo_unidade}" data-vol='0' data-custo='0'>

								<div class="flex-responsive flex-jc-sb gap-10">

									<div class="flex-9">
										<label class="caption">Composição de {composicao}</label>
										<div class="flex flex-ai-center gap-10">
											<i class="icon fa-solid fa-code-merge"></i>
											<div class="addon">
												{extra_block_product_button_status}
												{block_product_produto}
												{block_product_menu}
											</div>
										</div>
									</div>

									<div class="flex-3">
										<label class="caption">Custo</label>
										<div class="flex flex-dc flex-jc-center flex-ai-fs addon font-size-09">
											<span>R$ {custo_unidade_formatted} <span class="font-size-075">/{produtounidade}</span></span>
											<span class="color-red one-line {custo_ajustado_visible}">R$ {custo_unidade_ajustado_formatted} <span class="font-size-075">/{produtounidade}</span> <i class="fa-solid fa-circle-info" title="Custo ajustado com margem de perda"></i></span>
										</div>
									</div>

									<div class="flex gap-10 flex-8">

										{block_group_preco}

										<div class="flex flex-ai-fe">

											<div class="tooltip pos-rel">

												<button type="button" class="button-icon button-blue fa-solid fa-hand-holding-dollar" title="Sugestões de Preços"></button>

												<span class="tooltiptext">

													<div class="flex flex-dc gap-10 padding-h10">

														<label class="caption one-line">Sugestão de Preço</label>

														<div class="flex flex-jc-sb flex-ai-fe gap-10">
															<span class="one-line">R$ {custo0} <span class="font-size-075">/{produtounidade}</span></span>
															<span class="font-size-09">({margem_lucro_formatted}%)</span>
														</div>

														<!-- <span>30% - R$ {custo1}</span>
														<span>40% - R$ {custo2}</span>
														<span>50% - R$ {custo3}</span>
														<span>60% - R$ {custo4}</span> -->
													</div>
												</span>

											</div>
										</div>
									</div>
								</div>
							</div>
							<!-- END EXTRA_BLOCK_PURCHASE_FECHADO_CONTAINER_COMPOSITION -->
						</div>
					</div>
				</div>
				<!-- END EXTRA_BLOCK_PURCHASE_FECHADO_CONTAINER -->
			</div>
		</div>
		<!-- END EXTRA_BLOCK_PURCHASE_FECHADO -->

		<!-- BEGIN EXTRA_BLOCK_PURCHASE_CANCELADO -->
		<div class="w-purchaseorder window flex flex-dc gap-10 card-container" data-id_compra='{id_compra}' data-total="{total}">

			<div class="flex-responsive gap-10">

				<div class="flex gap-10 flex-6">
					<div class="flex-3">
						<label class="caption">OC #{id_compra}</label>
						<div class="border-ticket border-red flex flex-jc-center">
							<span class="color-blue">Cancelado</span>
						</div>
					</div>

					<div class="flex-3">
						<label class="caption">Data</label>
						<div class="addon">
							<span class="field">{data_formatted}</span>
						</div>
					</div>
				</div>

				<div class="flex-6">
					<label class="caption">Fornecedor</label>
					<div class="addon">
						<span class="field">{razaosocial}</span>
					</div>
				</div>

				<div class="flex gap-10 flex-6">
					<div class="flex-3">
						<label class="caption color-blue">Total</label>
						<div class="addon color-blue font-size-12 reversed">
							<span>R$ <span class="purchaseorder-total">{total_formatted}</span></span>

						</div>
					</div>

					{extra_block_purchaseorder_obs}

					<div class="flex flex-jc-fe flex-ai-fe">
						<div class="flex ai-center flex-jc-right gap-10">
							<button class="purchase_bt_expand button-icon button-blue fa-solid fa-chevron-down"></button>
						</div>
					</div>
				</div>
			</div>

			<div class="expandable" style="display: none;">

				<!-- BEGIN EXTRA_BLOCK_PURCHASE_CANCELADO_CONTAINER -->
				<div class="card-body flex flex-dc gap-10">

					<div class="section-header">
						Itens
					</div>

					<div class="table tbody">

						{extra_block_purchase_cancelado_container_item}
						<!-- BEGIN EXTRA_BLOCK_PURCHASE_CANCELADO_CONTAINER_ITEM -->
						<div class="tr flex flex-dc gap-10" data-id_compraitem="{id_compraitem}" data-custo_unidade_history="{custohistoryun}">

							<div class="flex-responsive flex-jc-sb gap-10">

								<div class="flex-8">
									<label class="caption">{produtotipo}</label>
									<div class="addon">
										{extra_block_product_button_status}
										{block_product_produto}
										{block_product_menu}
									</div>
								</div>

								<div class="flex flex-6 gap-10">
									<div class="flex-3">
										<label class="caption">Volume</label>
										<div class="addon">
											<span>R$ {vol_formatted}</span>
										</div>
									</div>

									<div class="flex-3">
										<label class="caption">Qtd / Vol</label>
										<div class="addon">
											<span>{qtdvol_formatted} <span class="font-size-075">{produtounidade}</span></span>
										</div>
									</div>
								</div>

								<div class="flex gap-10 flex-4">
									<div class="flex-3">
										<label class="caption">Custo / Vol</label>

										<div class="addon">
											<span >R$</span>
											<span class="field">{custo_formatted}</span>
										</div>
									</div>

									<div class="flex flex-ai-fe">
										{extra_block_purchaseorderitem_obs}
									</div>
								</div>
							</div>
						</div>
						<!-- END EXTRA_BLOCK_PURCHASE_CANCELADO_CONTAINER_ITEM -->
					</div>
				</div>
				<!-- END EXTRA_BLOCK_PURCHASE_CANCELADO_CONTAINER -->
			</div>
		</div>
		<!-- END EXTRA_BLOCK_PURCHASE_CANCELADO -->
	</div>
</div>

<div class="footer-popup flex-jc-fe not-desktop">
	<div class="popup-menu">
		<button class="popup-main-button fa-solid fa-ellipsis-vertical button-pink"></button>

		<ul>
			<li class="purchaseorder_bt_show_new flex flex-ai-center gap-10 color-blue" title="Cadastrar nova ordem de compras">
				<i class="icon fa-solid fa-square-plus"></i>
				<span>Nova ordem</span>
			</li>

			<li class="purchase_order_bt_list flex flex-ai-center gap-10 color-blue" title="Listar ordens em aberto">
				<i class="icon fa-solid fa-file-lines"></i>
				<span>Ordens em Aberto</span>
			</li>

			<!-- <li class="purchaseorder_bt_show_search flex flex-ai-center gap-10 mobile" title="Conulta de ordens de compras">
				<span class="icon color-blue"></span>
				<span>Consulta</span>
			</li> -->
		</ul>
	</div>
</div>
<!-- END BLOCK_PAGE -->