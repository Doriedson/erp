<!-- BEGIN BLOCK_PAGE -->
<div class="w-purchaselist-new-popup">
	<div class="box-container flex flex-dc gap-10">

		<div class="box-header gap-10">
			<i class="icon fa-solid fa-file-invoice-dollar"></i>
			<span>Compra / Lista de Compra</span>
		</div>

		<div class="flex-responsive">
			<form method="post" id="frm_purchaselist" class="flex gap-10">

				<div class="fill">
					<label class="caption">Descrição</label>
					<div class="addon fill">
						<input
							type="text"
							id="descricao"
							maxlength="100"
							required
							placeholder=""
							autocomplete="off"
							class="smart-search fill"
							autofocus>
					</div>
				</div>

				<div class="flex flex-ai-fe">
					<button type="submit" class="button-blue fill">Criar Lista</button>
				</div>
			</form>
		</div>
	</div>
</div>

<div class="setor-2">
	Listas de Compra
</div>

<div class="w-purchaselist-container flex flex-dc gap-10">

	{extra_block_purchaselist}

	<!-- BEGIN EXTRA_BLOCK_PURCHASELIST_NONE -->
	<div class="purchaselist_not_found {hidden} box-container window">

		<div class="font-size-12 textcenter" style="padding: 80px 10px;">
			Não há lista de compras cadastradas.
		</div>
	</div>
	<!-- END EXTRA_BLOCK_PURCHASELIST_NONE -->

	<!-- BEGIN EXTRA_BLOCK_PURCHASELIST -->
	<div class="w-purchaselist window flex flex-dc gap-10 box-container" data-id_compralista='{id_compralista}'>

		<div class="flex gap-10">
			<div class="flex-1">
				<label class="caption">Descrição</label>
				<div class="addon">
					<!-- BEGIN BLOCK_DESCRICAO -->
					<button class="purchaselist_bt_descricao button-field textleft fill" title="Editar descrição da lista de compra">
						{descricao}

					</button>
					<!-- END BLOCK_DESCRICAO -->
					<!-- BEGIN EXTRA_BLOCK_DESCRICAO_FORM -->
					<form method="post" id="frm_purchaselist_descricao" class="fill">
						<input
							type="text"
							id="descricao"
							class="fill"
							placeholder=""
							required
							maxlength="50"
							value="{descricao}"
							autocomplete="off"
							autofocus>
					</form>
					<!-- END EXTRA_BLOCK_DESCRICAO_FORM -->
				</div>
			</div>

			<div class="flex flex-ai-fe">
				<button class="purchaselist_bt_delete button-icon button-red fa-solid fa-trash-can" title="Remover lista de compra"></button>
			</div>

			<div class="flex flex-ai-fe gap-10">
				<button class="purchaselist_bt_expand button-icon button-blue fa-solid fa-chevron-down"></button>
			</div>
		</div>

		<div class="expandable" style="display: none;">
			<!-- BEGIN EXTRA_BLOCK_PURCHASELIST_TABLE -->
			<div class="flex flex-dc gap-10">
				<div class="section-header">
					Itens
				</div>

				<div class="table tbody flex flex-dc">

					{extra_block_purchaselist_item}

					<!-- BEGIN EXTRA_BLOCK_PURCHASELIST_ITEM_NONE -->
					<div class="w-purchaselist-notfound">
						<div class="font-size-12" style="padding: 20px 10px;">
							Nehum item na lista.
						</div>
					</div>
					<!-- END EXTRA_BLOCK_PURCHASELIST_ITEM_NONE -->

					<!-- BEGIN EXTRA_BLOCK_PURCHASELIST_ITEM -->
					<div class="w-purchaselist-item tr flex gap-10" data-id_compralistaitem="{id_compralistaitem}">

						<div class="flex-1">
							<label class="caption">Produto</label>
							<div class="addon">
								{extra_block_product_button_status}
								<span class="uppercase">{produto}</span>
							</div>
						</div>

						<div class="flex flex-ai-fe">
							<button class="purchaselist_bt_itemdelete button-icon button-red fa-solid fa-trash-can" title="Remove o produto da lista de compra"></button>
						</div>
					</div>
					<!-- END EXTRA_BLOCK_PURCHASELIST_ITEM -->
				</div>

				<div class="section-header">
					Adicionar Produto
				</div>

				<div class="flex-responsive">

					<form method='post' id="frm_purchaselist_item" data-id_compralista="{id_compralista}">

						<div class="flex gap-10">
							<div class="fill">
								<label class="caption">Produto</label>
								<div class="addon">
									<div class="fill autocomplete-dropdown">
										<input
											type="text"
											id="product_search"
											class="uppercase product_search smart_search smart-search fill"
											data-source="popup"
											maxlength="50"
											required
											placeholder=""
											autocomplete="off"
											title="Nome ou código do produto."
											autofocus>

										{block_product_autocomplete_search}
									</div>
								</div>
							</div>

							<div class="flex flex-ai-fe">
								<button type='submit' class="button-blue fill" title="Adiciona o produto ao pedido.">Adicionar</button>
							</div>
						</div>
					</form>
				</div>
			</div>
			<!-- END EXTRA_BLOCK_PURCHASELIST_TABLE -->
		</div>
	</div>
	<!-- END EXTRA_BLOCK_PURCHASELIST -->
</div>

<!-- <div class="footer">
	<div class="footer-container gap-10">
	    <button class="purchaselist_bt_show_new button-blue" title="Criar nova lista de compras">Nova lista</button>
	</div>
</div> -->

<!-- <div class="footer-popup flex-jc-fe">
	<div class="popup-menu">
		<button class="popup-main-button fa-solid fa-ellipsis-vertical button-pink"></button>

		<ul>

			<li class="flex flex-ai-center gap-10">
				<label>Nova lista</label>
				<button class="purchaselist_bt_show_new button-blue" title="Criar nova lista de compras"></button>
			</li>
		</ul>
	</div>
</div> -->
<!-- END BLOCK_PAGE -->