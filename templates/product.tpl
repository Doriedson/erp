<!-- BEGIN BLOCK_PAGE -->
<div>
	<div class="box-container flex flex-dc gap-10">

		<div class="flex gap-10">
			<div class="box-header gap-10 flex-1">
				<i class="font-size-20 fa-solid fa-boxes-stacked"></i>
				<span>Produto / Cadastro & Consulta</span>
			</div>

			<div class="flex flex-ai-fe gap-10 non-mobile">
				<button type="button" class="bt_load button-blue button-icon" data-page="digital_menu_config" title="Cardápio Digital">
					<i class="fa-solid fa-table-list"></i>
				</button>

				<button type="button" class="bt_load button-blue button-icon" data-page="price_tag" title="Impressão de etiquetas de preço">
					<i class="fa-solid fa-tags"></i>
				</button>

				<button type="button" class="productsector_bt_show_new button-blue button-icon" title="Cadastrar novo setor">
					<i class="fa-solid fa-folder-plus"></i>
				</button>

				<button type="button" class="bt_product_new button-blue button-icon" data-id_produtosetor="0" title="Cadastrar novo produto">
					<i class="fa-solid fa-square-plus"></i>
				</button>
			</div>

			<div class="flex flex-ai-fe gap-10 mobile">

				<div class="menu-inter">
					<button class="menu-inter-button fa-solid button-blue fa-ellipsis-vertical"></button>

					<ul style="display: none;">
						<li class="bt_load color-blue flex flex-ai-center gap-10" data-page="digital_menu_config" title="Cardápio Digital">
							<i class="icon fa-solid fa-table-list"></i>
							<span>Cardápio Digital</span>
						</li>

						<li class="bt_load color-blue flex flex-ai-center gap-10" data-page="price_tag" title="Impressão de etiquetas de preço">
							<i class="icon fa-solid fa-tags"></i>
							<span>Etiquetas de Preço</span>
						</li>

						<li class="productsector_bt_show_new color-blue flex flex-ai-center gap-10" title="Cadastrar novo setor">
							<i class="icon fa-solid fa-folder-plus"></i>
							<span>Adicionar Setor</span>
						</li>

						<li class="bt_product_new color-blue flex flex-ai-center gap-10" data-id_produtosetor="0" title="Cadastrar novo produto">
							<i class="icon fa-solid fa-square-plus"></i>
							<span>Adicionar Produto</span>
						</li>
					</ul>
				</div>
			</div>
		</div>

		<div class="flex-responsive flex-jc-sb gap-10">

			<div class="flex gap-10">

				<div class="fill">
					<label class="caption flex flex-ai-center gap-5">
						Produto [Código ou Descrição]
					</label>

					<div class="autocomplete-dropdown">
						<input
							type="text"
							id="product_search"
							class="uppercase product_search smart-search fill flex-4"
							data-source="product"
							data-sort="sector"
							title="Consulta de produto"
							maxlength="40"
							required
							placeholder=""
							autocomplete="off"
							autofocus>

						<!-- BEGIN BLOCK_PRODUCT_AUTOCOMPLETE_SEARCH -->
						<ul class="dropdown-list">

							<!-- BEGIN EXTRA_BLOCK_ITEM_SEARCH_NOTFOUND -->
							<li class="padding-10">Nenhum produto encontrado.</li>
							<!-- END EXTRA_BLOCK_ITEM_SEARCH_NOTFOUND -->

							<!-- BEGIN EXTRA_BLOCK_ITEM_SEARCH -->
							<li class="dropdown-item" data-descricao="{produto}" data-sku="{id_produto}">
								<div class="flex-responsive gap-10">
									<div class="flex-10">
										<label class="caption">{produtotipo}</label>
										<div class="addon">
											<span class="{class_status}">{id_produto}</span>
											<span class="field fill">{produto}</span>
										</div>
									</div>

									<div class='flex gap-10 flex-6'>
										<div class="flex-3">
											<label class="caption">Preço</label>
											<div class="addon">
												<span class="fill one-line">R$ {preco_final_formatted} <span class="font-size-075">/{produtounidade}</span></span>
											</div>
										</div>

										<div class="flex-3">
											<label class="caption">Estoque</label>
											<div class="addon">
												<span class="fill one-line">{estoque_formatted} <span class="font-size-075">{produtounidade}</span></span>
											</div>
										</div>
									</div>
								</div>
							</li>
							<!-- END EXTRA_BLOCK_ITEM_SEARCH -->
						</ul>
						<!-- END BLOCK_PRODUCT_AUTOCOMPLETE_SEARCH -->
					</div>
				</div>
			</div>

			<!-- <div class="flex flex-ai-fe flex-jc-fe">
				<button type="button" class="productsector_bt_show_new button-blue" title="Cadastrar novo setor">Adicionar setor</button>
			</div> -->
		</div>
	</div>
</div>

<!-- BEGIN EXTRA_BLOCK_PRODUCT_COMPLEMENT_SELECTION -->
<div class="flex flex-dc gap-10">

	<div>
		<label class="produto-tipo caption">
			Produto
		</label>
		<div class="addon">
			{extra_block_product_button_status}
			{block_product_produto}
		</div>
	</div>

	<div class="section-header">
		Grupos de Complementos disponíveis
	</div>

	{extra_block_product_complement_item}

	<!-- BEGIN EXTRA_BLOCK_PRODUCT_COMPLEMENT_ITEM -->
	<div class="produtct_complementgroup_container">

			<div class="window box-container flex-1 flex flex-dc gap-10">
			    <div class="box-header-title flex flex-jc-sb gap-10">
					<div class="flex-1">
						<button class="product_bt_complementgroup_descricao button-field textleft fill" data-id_complementogrupo="{id_complementogrupo}" title="Editar descrição do grupo de complementos">
							{descricao}
						</button>
					</div>
					<button type="button" class="product_bt_complementgroup_select button-icon button-blue fa-solid fa-file-import" title="Selecionar grupo de complementos" data-id_complementogrupo="{id_complementogrupo}" data-id_produto="{id_produto}"></button>
					<button type="button" class="product_bt_complementgroup_expand button-icon button-blue fa-solid fa-chevron-down" title="Expandir" data-id_complementogrupo="{id_complementogrupo}" data-id_produto="{id_produto}"></button>
				</div>

				<div class="expandable flex flex-dc gap-10" style="display: none;"></div>
			</div>
	</div>
	<!-- END EXTRA_BLOCK_PRODUCT_COMPLEMENT_ITEM -->

</div>
<!-- END EXTRA_BLOCK_PRODUCT_COMPLEMENT_SELECTION -->

<!-- BEGIN EXTRA_BLOCK_POPUP_PRODUCT_ESTOQUE_ADD -->
<div class="w-productstock flex flex-dc gap-10 fill" data-id_produto="{id_produto}">

	<div>
		<label class="produto-tipo caption">
			{produtotipo}
		</label>
		<div class="addon">
			{extra_block_product_button_status}
			{block_product_produto}
		</div>
	</div>

	<div>
		<form method="post" id="frm_product_estoque" class="fill" data-screen="add" data-id_produto="{id_produto}">
			<div class="flex flex-dc gap-10">

				<!-- <div class="section-header">Insira a quantidade para adicionar no estoque</div> -->

				<div class="flex gap-10 fill">

					<div class="flex-1">
						<label class="produto-tipo caption">
							Estoque atual
						</label>
						<div class="addon container">
							<span class="field uppercase">{estoque_formatted} <span class="font-size-075">{produtounidade}</span></span>
						</div>
					</div>

					<div class="flex-1">
						<label class="caption">Adicionar Qtd</label>
						<input
							type="number"
							id="estoque"
							class="fill"
							step='0.001'
							min='0'
							max='999999.999'
							required
							autofocus>
					</div>
				</div>

				<div class="flex gap-10">
					<div class="flex-1">
						<label class="caption">Observação / Justificativa</label>
						<div class="addon">
							<input
							type="text"
							id="obs"
							class="fill"
							maxlength="255"
							placeholder=""
							autocomplete="off">
						</div>
					</div>

					<div class="flex flex-ai-fe">
						<button type="submit" class="button-green flex-1" title="Adicionar estoque ao produto">Adicionar</button>
					</div>

				</div>
			</div>
		</form>
	</div>
</div>
<!-- END EXTRA_BLOCK_POPUP_PRODUCT_ESTOQUE_ADD -->

<!-- BEGIN EXTRA_BLOCK_POPUP_PRODUCT_ESTOQUE_DEL -->
<div class="w-productstock flex flex-dc gap-10 fill" data-id_produto="{id_produto}">

	<div>
		<label class="produto-tipo caption">
			{produtotipo}
		</label>
		<div class="addon">
			{extra_block_product_button_status}
			{block_product_produto}
		</div>
	</div>

	<div>
		<form method="post" id="frm_product_estoque" class="fill" data-screen="del" data-id_produto="{id_produto}">
			<div class="flex flex-dc gap-10">

				<!-- <div class="section-header">Insira a quantidade para remover do estoque</div> -->

				<div class="flex gap-10 fill">

					<div class="flex-1">
						<label class="produto-tipo caption">
							Estoque atual
						</label>
						<div class="addon container">
							<span class="field uppercase">{estoque_formatted} <span class="font-size-075">{produtounidade}</span></span>
						</div>
					</div>

					<div class="flex-1">
						<label class="caption">Reduzir Qtd</label>
						<input
							type="number"
							id="estoque"
							class="fill"
							step='0.001'
							min='0'
							max='999999.999'
							required
							autofocus>
					</div>
				</div>

				<div class="flex gap-10">
					<div class="flex-1">
						<label class="caption">Observação / Justificativa</label>
						<div class="addon">
							<input
							type="text"
							id="obs"
							class="fill"
							maxlength="255"
							placeholder=""
							autocomplete="off">
						</div>
					</div>

					<div class="flex flex-ai-fe">
						<button type="submit" class="button-red flex-1" title="Reduzir estoque do produto">Reduzir</button>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
<!-- END EXTRA_BLOCK_POPUP_PRODUCT_ESTOQUE_DEL -->

<!-- BEGIN EXTRA_BLOCK_POPUP_PRODUCT_ESTOQUE_UPDATE -->
<div class="w-productstock flex flex-dc gap-10 fill" data-id_produto="{id_produto}">

	<div>
		<label class="produto-tipo caption">
			{produtotipo}
		</label>
		<div class="addon">
			{extra_block_product_button_status}
			{block_product_produto}
		</div>
	</div>

	<div>
		<form method="post" id="frm_product_estoque" class="fill" data-screen="update" data-id_produto="{id_produto}">
			<div class="flex flex-dc gap-10">

				<!-- <div class="section-header">Insira a quantidade para atualizar o estoque</div> -->

				<div class="flex gap-10 fill">

					<div class="flex-1">
						<label class="produto-tipo caption">
							Estoque atual
						</label>
						<div class="addon container">
							<span class="field uppercase">{estoque_formatted} <span class="font-size-075">{produtounidade}</span></span>
						</div>
					</div>

					<div class="flex-1">
						<label class="caption">Atualizar Qtd</label>
						<input
							type="number"
							id="estoque"
							class="fill"
							step='0.001'
							min='0'
							max='999999.999'
							required
							autofocus>
					</div>
				</div>

				<div class="flex gap-10">
					<div class="flex-1">
						<label class="caption">Observação / Justificativa</label>
						<div class="addon">
							<input
							type="text"
							id="obs"
							class="fill"
							maxlength="255"
							placeholder=""
							autocomplete="off">
						</div>
					</div>

					<div class="flex flex-ai-fe">
						<button type="submit" class="button-blue flex-1" title="Atualiza estoque do produto">Atualizar</button>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
<!-- END EXTRA_BLOCK_POPUP_PRODUCT_ESTOQUE_UPDATE -->

<!-- BEGIN EXTRA_BLOCK_POPUP_PRODUCT_ESTOQUE_TRANSF -->
<div class="w-productstock flex flex-dc gap-10 fill" data-id_produto="{id_produto}">

	<div>
		<label class="produto-tipo caption">
			{produtotipo}
		</label>
		<div class="addon">
			{extra_block_product_button_status}
			{block_product_produto}
		</div>
	</div>

	<div>
		<form method="post" id="frm_product_estoque" class="fill" data-screen="transf" data-id_produto="{id_produto}">
			<div class="flex flex-dc gap-10">

				<!-- <div class="section-header">Insira a quantidade para transferir do estoque primário para o secundário</div> -->

				<div class="flex gap-10 fill">

					<div class="flex-1">
						<label class="produto-tipo caption">
							Est. Primário
						</label>
						<div class="addon container">
							<span class="field uppercase">{estoque_formatted} <span class="font-size-075">{produtounidade}</span></span>
						</div>
					</div>

					<div class="flex flex-ai-fe">
						<div class="pseudo-button">
							<i class="icon color-blue fa-solid fa-arrow-right"></i>
						</div>
					</div>

					<div class="flex-1">
						<label class="produto-tipo caption">
							Est. Secundário
						</label>
						<div class="addon container">
							<span class="field uppercase">{estoque_secundario_formatted} <span class="font-size-075">{produtounidade}</span></span>
						</div>
					</div>
				</div>

				<div class="flex gap-10 fill">
					<div class="flex-1">
						<label class="caption">Quantidade</label>
						<input
							type="number"
							id="estoque"
							class="fill"
							step='0.001'
							min='0'
							max='999999.999'
							required
							autofocus>
					</div>

					<div class="flex flex-ai-fe">
						<button type="submit" class="button-blue flex-1" title="Transferir estoque primário para estoque secundário">Transferir</button>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
<!-- END EXTRA_BLOCK_POPUP_PRODUCT_ESTOQUE_TRANSF -->

<!-- BEGIN EXTRA_BLOCK_FORM_ESTOQUE_SECUNDARIO_ADD -->
<div class="w-productstocksec flex flex-dc gap-10 fill" data-id_produto="{id_produto}">

	<div>
		<label class="produto-tipo caption">
			Produto
		</label>
		<div class="addon container">
			<span class="field uppercase">{produto}</span>
		</div>
	</div>

	<div>
		<form method="post" id="frm_product_estoque_secundario" class="fill flex flex-dc gap-10" data-screen="add" data-id_produto="{id_produto}">

			<div class="flex gap-10 fill">

				<div class="flex-1">
					<label class="produto-tipo caption">
						Estoque atual
					</label>
					<div class="addon container">
						<span class="field uppercase">{estoque_formatted} <span class="font-size-075">{produtounidade}</span></span>
					</div>
				</div>

				<div class="flex-1">
					<label class="caption">Adicionar Qtd</label>
					<input
						type="number"
						id="estoque"
						class="fill"
						step='0.001'
						min='0'
						max='999999.999'
						required
						autofocus>
				</div>
			</div>

			<div class="flex gap-10">
				<div class="flex-1">
					<label class="caption">Observação / Justificativa</label>
					<div class="addon">
						<input
						type="text"
						id="obs"
						class="fill"
						maxlength="255"
						placeholder=""
						autocomplete="off">
					</div>
				</div>

				<div class="flex flex-ai-fe">
					<button type="submit" class="button-green flex-1" title="Adicionar estoque ao produto">Adicionar</button>
				</div>
			</div>
		</form>
	</div>
</div>
<!-- END EXTRA_BLOCK_FORM_ESTOQUE_SECUNDARIO_ADD -->

<!-- BEGIN EXTRA_BLOCK_FORM_ESTOQUE_SECUNDARIO_DEL -->
<div class="w-productstocksec flex flex-dc gap-10 fill" data-id_produto="{id_produto}">

	<div>
		<label class="produto-tipo caption">
			Produto
		</label>
		<div class="addon container">
			<span class="field uppercase">{produto}</span>
		</div>
	</div>

	<div>
		<form method="post" id="frm_product_estoque_secundario" class="fill" data-screen="del" data-id_produto="{id_produto}">
			<div class="flex flex-dc gap-10">

				<div class="flex gap-10 fill">

					<div class="flex-1">
						<label class="produto-tipo caption">
							Estoque atual
						</label>
						<div class="addon container">
							<span class="field uppercase">{estoque_formatted} <span class="font-size-075">{produtounidade}</span></span>
						</div>
					</div>

					<div class="flex-1">
						<label class="caption">Reduzir Qtd</label>
						<input
							type="number"
							id="estoque"
							class="fill"
							step='0.001'
							min='0'
							max='999999.999'
							required
							autofocus>
					</div>
				</div>

				<div class="flex gap-10">
					<div class="flex-1">
						<label class="caption">Observação / Justificativa</label>
						<div class="addon">
							<input
							type="text"
							id="obs"
							class="fill"
							maxlength="255"
							placeholder=""
							autocomplete="off">
						</div>
					</div>

					<div class="flex flex-ai-fe">
						<button type="submit" class="button-red flex-1" title="Reduzir estoque do produto">Reduzir</button>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
<!-- END EXTRA_BLOCK_FORM_ESTOQUE_SECUNDARIO_DEL -->

<!-- BEGIN EXTRA_BLOCK_FORM_ESTOQUE_SECUNDARIO_UPDATE -->
<div class="w-productstocksec flex flex-dc gap-10 fill" data-id_produto="{id_produto}">

	<div>
		<label class="produto-tipo caption">
			Produto
		</label>
		<div class="addon container">
			<span class="field uppercase">{produto}</span>
		</div>
	</div>

	<div>
		<form method="post" id="frm_product_estoque_secundario" class="fill flex flex-dc gap-10" data-screen="update" data-id_produto="{id_produto}">

			<div class="flex gap-10 fill">

				<div class="flex-1">
					<label class="produto-tipo caption">
						Estoque atual
					</label>
					<div class="addon container">
						<span class="field uppercase">{estoque_formatted} <span class="font-size-075">{produtounidade}</span></span>
					</div>
				</div>

				<div class="flex-1">
					<label class="caption">Atualizar Qtd</label>
					<input
						type="number"
						id="estoque"
						class="fill"
						step='0.001'
						min='0'
						max='999999.999'
						required
						autofocus>
				</div>
			</div>

			<div class="flex gap-10">
				<div class="flex-1">
					<label class="caption">Observação / Justificativa</label>
					<div class="addon">
						<input
						type="text"
						id="obs"
						class="fill"
						maxlength="255"
						placeholder=""
						autocomplete="off">
					</div>
				</div>

				<div class="flex flex-ai-fe">
					<button type="submit" class="button-blue flex-1" title="Atualiza estoque do produto">Atualizar</button>
				</div>
			</div>
		</form>
	</div>
</div>
<!-- END EXTRA_BLOCK_FORM_ESTOQUE_SECUNDARIO_UPDATE -->

<!-- BEGIN EXTRA_BLOCK_FORM_ESTOQUE_SECUNDARIO_TRANSF -->
<div class="w-productstocksec flex flex-dc gap-10 fill" data-id_produto="{id_produto}">

	<div>
		<label class="produto-tipo caption">
			Produto
		</label>
		<div class="addon container">
			<span class="field uppercase">{produto}</span>
		</div>
	</div>

	<div>
		<form method="post" id="frm_product_estoque_secundario" class="fill" data-screen="transf" data-id_produto="{id_produto}">
			<div class="flex flex-dc gap-10">

				<div class="flex gap-10 fill">

					<div class="flex-1">
						<label class="produto-tipo caption">
							Est. Primário
						</label>
						<div class="addon container">
							<span class="field uppercase">{estoque_formatted} <span class="font-size-075">{produtounidade}</span></span>
						</div>
					</div>

					<div class="flex flex-ai-fe">
						<div class="pseudo-button">
							<i class="icon color-blue fa-solid fa-arrow-left"></i>
						</div>
					</div>

					<div class="flex-1">
						<label class="produto-tipo caption">
							Est. Secundário
						</label>
						<div class="addon container">
							<span class="field uppercase">{estoque_secundario_formatted} <span class="font-size-075">{produtounidade}</span></span>
						</div>
					</div>
				</div>

				<div class="flex gap-10 fill">
					<div class="flex-1">
						<label class="caption">Quantidade</label>
						<input
							type="number"
							id="estoque"
							class="fill"
							step='0.001'
							min='0'
							max='999999.999'
							required
							autofocus>
					</div>

					<div class="flex flex-ai-fe">
						<button type="submit" class="button-blue flex-1" title="Transferir estoque primário para estoque secundário">Transferir</button>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
<!-- END EXTRA_BLOCK_FORM_ESTOQUE_SECUNDARIO_TRANSF -->

<!-- BEGIN EXTRA_BLOCK_POPUP_PRODUCTSECTOR -->
<div class="flex-responsive">
	<form method="post" id="frm_productsector" class="flex gap-10">
		<div class="fill">
			<label class="caption">Descrição do Setor</label>
			<div>
				<input
					type="text"
					id="sector"
					maxlength="50"
					required
					placeholder=""
					autocomplete="off"
					class="smart-search fill"
					autofocus>
			</div>
		</div>

		<div class="flex flex-ai-fe">
			<button type="submit" class="button-blue fill" title="Cadastra novo setor">Cadastrar</button>
		</div>
	</form>
</div>
<!-- END EXTRA_BLOCK_POPUP_PRODUCTSECTOR -->

<!-- BEGIN EXTRA_BLOCK_POPUP_PRODUCTIMAGE -->
<div class="flex flex-dc gap-10">
	<div>
		<label class="produto-tipo caption">
			{produtotipo}
		</label>
		<div class="addon">
			{extra_block_product_button_status}
			{block_product_produto}
		</div>
	</div>

	<div class="flex flex-jc-center">
		<div class="flex flex-jc-center flex-ai-center margin-v10 pos-rel" style="width: 160; height: 120px;">
			<img id="image_view" class="img_selection" src="pic/{imagem}">

			<div class="pos-abs" style="right: -10px; bottom: -10px;">
				<button type="button" class="bt_uploadfile button-blue" data-target="produto" title="Escolher imagem do computador">
					<i class="icon fa-solid fa-arrow-up-from-bracket"></i>
				</button>
			</div>
		</div>
	</div>

	<div class="section-header">
		Selecione uma imagem
	</div>
	<div>
		<form method="post" id="frm_product_image" class="fill" data-id_produto="{id_produto}">

			<div class="flex flex-dc gap-10">
				<select class="image_select" id="imagem" class="fill" size="5" required autofocus>
					{imagem_lista}
				</select>

				<button class="button-blue flex-1" type="submit">Salvar</button>
			</div>
		</form>
	</div>
</div>
<!-- END EXTRA_BLOCK_POPUP_PRODUCTIMAGE -->

<!-- BEGIN EXTRA_BLOCK_POPUP_COMPLEMENT_DEL -->
<div class="flex flex-dc gap-10">
	<div class="section-header">Confirma remoção do grupo de complementos:</div>
	<div class="addon">
		<span>{descricao}</span>
	</div>
	<div class="section-header">do produto:</div>
		<!-- <div>
		<label class="produto-tipo caption">
			{produtotipo}
		</label> -->

		<div class="addon container">
			{extra_block_product_button_status}
			<button class="product_bt_produto product_{id_produto}_produto button-field textleft fill" data-id_produto="{id_produto}" title="Editar nome do produto">
				{produto}
			</button>
		</div>
	<!-- </div> -->

	<div class="flex gap-10 bg-yellow padding-5 margin-v10">
		<div class="flex flex-ai-center flex-jc-center"><i class="icon color-yellow fa-solid fa-triangle-exclamation"></i></div>
		<span><b>Atenção:</b> Esta ação irá somente desvincular o Grupo de Complementos do Produto selecionado. O Grupo de Complementos não será apagado.</span>
	</div>

</div>
<!-- END EXTRA_BLOCK_POPUP_COMPLEMENT_DEL -->

<!-- BEGIN EXTRA_BLOCK_COMPLEMENT_ITEM_ADD -->
<form method="post" id="frm_product_complement_item" class="flex-responsive" data-id_complementogrupo="{id_complementogrupo}">

	<div class="flex gap-10">

		<div class="fill">
			<label class="caption">Produto [Código ou Descrição]</label>

			<div class="autocomplete-dropdown">
				<input
					type="text"
					id=""
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

		<div class='flex flex-ai-fe'>
			<button type="submit" title="Adicionar produto ao grupo de complementos" class="button-blue fill">Adicionar</button>
		</div>
	</div>
</form>
<!-- END EXTRA_BLOCK_COMPLEMENT_ITEM_ADD -->

<!-- BEGIN EXTRA_BLOCK_POPUP_COMPLEMENT -->
<div class="w_product_complement flex flex-dc gap-10" data-id_produto="{id_produto}">
	<div>
		<label class="produto-tipo caption">
			Produto
		</label>
		<div class="addon">
			{extra_block_product_button_status}
			{block_product_produto}
		</div>
	</div>

	<div class="section-header">
		Grupos de Complementos que compõem esse produto
	</div>

	<div class="complementgroup_not_found complementgroup_not_found_{id_produto} table tbody {hidden}">
		<div class="" style="padding: 20px 10px;">
			Não há grupos de complementos para este produto.
		</div>
	</div>

	<div class="w_complementgroup_table flex flex-dc gap-10">

		{extra_block_complement_tr}

		<!-- BEGIN EXTRA_BLOCK_COMPLEMENT_TR -->

		<div class="produtct_complementgroup_container">

			<div class="window box-container flex-1 flex flex-dc gap-10">
			    <div class="box-header-title flex flex-jc-sb gap-10">
					<div class="flex-1">
						<!-- BEGIN BLOCK_COMPLEMENTGROUP_DESCRICAO -->
						<button class="product_bt_complementgroup_descricao button-field textleft fill" data-id_complementogrupo="{id_complementogrupo}" title="Editar descrição do grupo de complementos">
							{descricao}
						</button>
						<!-- END BLOCK_COMPLEMENTGROUP_DESCRICAO -->
						<!-- BEGIN EXTRA_BLOCK_COMPLEMENTGROUP_DESCRICAO_FORM -->
						<form method="post" id="frm_product_complementgroup_descricao" class="flex fill" data-id_complementogrupo="{id_complementogrupo}">
							<input
								type='text'
								id='frm_product_complementgroup_descricao_field'
								class="fill"
								required
								placeholder='Descrição do Grupo de Complementos'
								value='{descricao}'
								maxlength='40'
								required
								autofocus>
						</form>
						<!-- END EXTRA_BLOCK_COMPLEMENTGROUP_DESCRICAO_FORM -->
					</div>
					<button type="button" class="product_bt_complementgroup_del button-icon button-red fa-solid fa-file-export" title="Remover grupo de complementos" data-id_complementogrupo="{id_complementogrupo}" data-id_produto="{id_produto}"></button>
					<button type="button" class="bt_collapse button-icon button-blue fa-solid fa-chevron-up"></button>
				</div>

				<div class="expandable flex flex-dc gap-10">

					<!-- BEGIN BLOCK_COMPLEMENTGROUP_EXPANDABLE -->
					<div class="section-header">Quantidade</div>

					<div>Indique quantos item podem ser selecionados</div>

					<div class="complementgroup_minmax_container flex gap-10">
						<!-- BEGIN BLOCK_COMPLEMENTGROUP_QTDMINMAX -->
						<div>
							<label class="caption">Mínimo</label>
							<div class="addon">
								{extra_block_complementgroup_qtdmin_min}
								<!-- BEGIN EXTRA_BLOCK_COMPLEMENTGROUP_QTDMIN_MIN_DISABLED -->
								<div class="pseudo-button button-icon">
									<i class="fa-solid fa-circle-minus color-gray"></i>
								</div>
								<!-- END EXTRA_BLOCK_COMPLEMENTGROUP_QTDMIN_MIN_DISABLED -->
								<!-- BEGIN EXTRA_BLOCK_COMPLEMENTGROUP_QTDMIN_MIN -->
								<button type="button" class="product_bt_complementgroup_min_del button-icon button-transparent-noborder" data-id_complementogrupo="{id_complementogrupo}" title="Reduzir quantidade mínima">
									<i class="fa-solid fa-circle-minus color-red"></i>
								</button>
								<!-- END EXTRA_BLOCK_COMPLEMENTGROUP_QTDMIN_MIN -->
								<div class="pseudo-button">
									<span>{qtd_min}</span>
								</div>
								{extra_block_complementgroup_qtdmin_max}
								<!-- BEGIN EXTRA_BLOCK_COMPLEMENTGROUP_QTDMIN_MAX_DISABLED -->
								<div class="pseudo-button button-icon">
									<i class="fa-solid fa-circle-plus color-gray"></i>
								</div>
								<!-- END EXTRA_BLOCK_COMPLEMENTGROUP_QTDMIN_MAX_DISABLED -->
								<!-- BEGIN EXTRA_BLOCK_COMPLEMENTGROUP_QTDMIN_MAX -->
								<button type="button" class="product_bt_complementgroup_min_add button-icon button-transparent-noborder" data-id_complementogrupo="{id_complementogrupo}" title="Aumentar quantidade mínima">
									<i class="fa-solid fa-circle-plus color-green"></i>
								</button>
								<!-- END EXTRA_BLOCK_COMPLEMENTGROUP_QTDMIN_MAX -->
							</div>
						</div>

						<div>
							<label class="caption">Máximo</label>
							<div class="addon">
								{extra_block_complementgroup_qtdmax_min}
								<!-- BEGIN EXTRA_BLOCK_COMPLEMENTGROUP_QTDMAX_MIN_DISABLED -->
								<div class="pseudo-button button-icon">
									<i class="fa-solid fa-circle-minus color-gray"></i>
								</div>
								<!-- END EXTRA_BLOCK_COMPLEMENTGROUP_QTDMAX_MIN_DISABLED -->
								<!-- BEGIN EXTRA_BLOCK_COMPLEMENTGROUP_QTDMAX_MIN -->
								<button type="button" class="product_bt_complementgroup_max_del button-icon button-transparent-noborder" data-id_complementogrupo="{id_complementogrupo}" title="Reduzir quantidade máxima">
									<i class="fa-solid fa-circle-minus color-red"></i>
								</button>
								<!-- END EXTRA_BLOCK_COMPLEMENTGROUP_QTDMAX_MIN -->
								<div class="pseudo-button">
									<span>{qtd_max}</span>
								</div>
								<button type="button" class="product_bt_complementgroup_max_add button-icon button-transparent-noborder" data-id_complementogrupo="{id_complementogrupo}" title="Aumentar quantidade máxima">
									<i class="fa-solid fa-circle-plus color-green"></i>
								</button>
							</div>
						</div>
						<!-- END BLOCK_COMPLEMENTGROUP_QTDMINMAX -->
					</div>

					<div class="section-header">Itens</div>

					<div class="product_complement_not_found product_complement_not_found_{id_complementogrupo} table tbody {hidden}">
						<div class="" style="padding: 20px 10px;">
							Não há itens para este grupo.
						</div>
					</div>

					<div class="product_complement_table table tbody product_complement_table_{id_complementogrupo}">

						{extra_block_complementgroup_product}

						<!-- BEGIN EXTRA_BLOCK_COMPLEMENTGROUP_PRODUCT -->
						<div class="flex-responsive gap-10 tr">
							<div class="flex-13">
								<label class="produto-tipo caption">
									Produto
								</label>
								<div class="addon">
									{extra_block_product_button_status}
									{block_product_produto}
								</div>
							</div>
							<div class="flex gap-10 flex-5">
								<div class="flex-4">
									<label class="caption">Preço do complemento</label>
									<!-- BEGIN BLOCK_COMPLEMENTGROUP_PRODUCT_PRECO -->
									<div class="addon container">
										<button class="product_bt_complementgroup_preco button-field textleft one-line" data-id_produtocomplemento="{id_produtocomplemento}" title="Alterar preço do produto">
											R$ {preco_complemento_formatted} <span class="font-size-075">/{produtounidade}</span>
										</button>
									</div>
									<!-- END BLOCK_COMPLEMENTGROUP_PRODUCT_PRECO -->
									<!-- BEGIN EXTRA_BLOCK_COMPLEMENTGROUP_PRODUCT_PRECO_FORM -->
									<form method="post" id="frm_product_complementgroup_preco" class="fill" data-id_produtocomplemento="{id_produtocomplemento}">
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
											placeholder='{preco_complemento_formatted}'
											autofocus>
											<span class="font-size-075">/{produtounidade}</span>
										</div>
									</form>
									<!-- END EXTRA_BLOCK_COMPLEMENTGROUP_PRODUCT_PRECO_FORM -->
								</div>

								<div class="flex flex-ai-fe">
									<button type="button" class="product_bt_complementgroup_product_del button-icon button-red fa-solid fa-trash-can" title="Remover complemento do grupo" data-id_complementogrupo="{id_complementogrupo}" data-id_produtocomplemento="{id_produtocomplemento}"></button>
								</div>
							</div>
						</div>
						<!-- END EXTRA_BLOCK_COMPLEMENTGROUP_PRODUCT -->
					</div>

					<div class="flex flex-jc-fe">
						<button type="button" class="product_bt_complement_new button-blue" data-id_complementogrupo="{id_complementogrupo}">Adicionar Complemento</button>
					</div>
					<!-- END BLOCK_COMPLEMENTGROUP_EXPANDABLE -->
				</div>
			</div>


		</div>

		<!-- <div class="tr flex-responsive gap-10" data-id_produto="{id_produto}">

			<div class="flex-5">
				<label class="caption">Produto</label>
				<div class="addon">
					{extra_block_product_button_status}
					<span class="field fill">{produto}</span>
				</div>
			</div>

			<div class="flex flex-3 gap-10">
				<div class="flex-2">
					<label class="caption">Quantidade</label>

					<div class="addon container">
						<button class="composition_bt_qtd button-field textleft one-line" title="Alterar quantidade do produto">
							{qtd} <span class="font-size-075">{produtounidade}</span>
						</button>
					</div>

					<form method="post" id="frm_composition_qtd" data-id_produto="{id_produto}" class="fill">
						<div class="addon">

							<input
								type="number"
								id="qtd"
								class="fill"
								step='0.001'
								min='0.001'
								max='999999.999'
								required
								placeholder='{qtd}'
								autofocus>
							<span class="font-size-075">{produtounidade}</span>
						</div>
					</form>

				</div>

				<div class="flex flex-ai-fe">
					<button type="button" class="product_bt_composition_delete button-icon button-red fa-solid fa-trash-can" title="Remover item"></button>
				</div>
			</div>
		</div> -->
		<!-- END EXTRA_BLOCK_COMPLEMENT_TR -->
	</div>

	<!-- <div class="section-header">
		Adicionar Produto
	</div> -->

	<div class="flex-responsive gap-10 tr">
		<button type="button" class="product_bt_complementgroup_new button-blue" data-id_produto="{id_produto}" title="Criar novo grupo de complementos">Adiciona Novo Grupo</button>
		<button type="button" class="product_bt_complementgroup_selectshow button-blue" data-id_produto="{id_produto}" title="Selecionar um grupo de complementos existente">Adicionar Grupo Existente</button>
	</div>
	<!-- </div> -->
</div>
<!-- END EXTRA_BLOCK_POPUP_COMPLEMENT -->

<!-- BEGIN EXTRA_BLOCK_POPUP_COMPOSITION -->
<div class="w_product_composition flex flex-dc gap-10" data-id_produto="{id_produto}">
	<div>
		<label class="produto-tipo caption">
			Produto
		</label>
		<div class="addon">
			{extra_block_product_button_status}
			{block_product_produto}
		</div>
	</div>

	<div class="section-header">
		Itens da Composição
	</div>

	<div class="composition_not_found table tbody {hidden}">
		<div class="tr" style="padding: 20px 10px;">
			Nenhum produto para composição.
		</div>
	</div>

	<div class="w_composition_table flex flex-dc table tbody">

		{extra_block_composition_tr}

		<!-- BEGIN EXTRA_BLOCK_COMPOSITION_TR -->
		<div class="w-composition tr flex-responsive gap-10" data-id_produto="{id_produto}">
			<div class="flex-5">
				<label class="caption">Produto</label>
				<div class="addon">
					{extra_block_product_button_status}
					<span class="field fill">{produto}</span>
				</div>
			</div>

			<div class="flex flex-3 gap-10">
				<div class="flex-2">
					<label class="caption">Quantidade</label>
					<!-- BEGIN BLOCK_COMPOSITION_QTD -->
					<div class="addon container">
						<button class="composition_bt_qtd button-field textleft one-line" title="Alterar quantidade do produto">
							{qtd} <span class="font-size-075">{produtounidade}</span>
						</button>
					</div>
					<!-- END BLOCK_COMPOSITION_QTD -->
					<!-- BEGIN EXTRA_BLOCK_COMPOSITION_QTD_FORM -->
					<form method="post" id="frm_composition_qtd" data-id_produto="{id_produto}" class="fill">
						<div class="addon">

							<input
								type="number"
								id="qtd"
								class="fill"
								step='0.001'
								min='0.001'
								max='999999.999'
								required
								placeholder='{qtd}'
								autofocus>
							<span class="font-size-075">{produtounidade}</span>
						</div>
					</form>
					<!-- END EXTRA_BLOCK_COMPOSITION_QTD_FORM -->
				</div>

				<div class="flex flex-ai-fe">
					<button type="button" class="product_bt_composition_delete button-icon button-red fa-solid fa-trash-can" title="Remover item"></button>
				</div>
			</div>
		</div>
		<!-- END EXTRA_BLOCK_COMPOSITION_TR -->
	</div>

	<div>
		<form method="post" id="frm_product_composition" data-id_composicao="{id_produto}" class="fill">
			<div class="flex flex-dc gap-10">

				<div class="flex gap-10">
					<div class="flex-1">
						<label class="caption">Produto</label>
						<div class="autocomplete-dropdown">
							<input
								type="text"
								id="id_produto"
								class="product_search uppercase smart_search smart-search fill"
								data-source="popup"
								data-focus_next="#qtd"
								maxlength="50"
								value=""
								placeholder=""
								autofocus
								required >
							{block_product_autocomplete_search}
						</div>
					</div>
				</div>

				<div class="flex gap-10">
					<div class=" flex-1">
						<label class="caption">Quantidade</label>
						<div class="addon">
							<input
								type="number"
								class="fill textcenter"
								id="qtd"
								step='0.001'
								min='0.001'
								max="999999.999"
								placeholder=""
								required >
						</div>
					</div>

					<div class="flex flex-ai-fe flex-1">
						<button type="submit" class="button-blue fill" title="Adicionar item">Adicionar</button>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
<!-- END EXTRA_BLOCK_POPUP_COMPOSITION -->

<!-- BEGIN EXTRA_BLOCK_POPUP_PRODUCT_PROFITMARGIN -->
<div class="flex flex-dc gap-10" data-id_produto="{id_produto}">
	<div>
		<label class="produto-tipo caption">
			Produto
		</label>
		<div class="addon">
			{extra_block_product_button_status}
			{block_product_produto}
		</div>
	</div>

	<div>
		<label class="caption">
			Margem de Lucro
		</label>

		<!-- BEGIN BLOCK_PRODUCT_PROFITMARGIN -->
		<div class="addon">
			<button class="product_bt_profitmargin_edit button-field textleft fill" data-id_produto="{id_produto}" title="Editar margem de lucro">
				{margem_lucro_formatted} %
			</button>
		</div>
		<!-- END BLOCK_PRODUCT_PROFITMARGIN -->

		<!-- BEGIN EXTRA_BLOCK_PRODUCT_PROFITMARGIN_FORM -->
		<form method="post" id="frm_product_margem_lucro" class="flex fill" data-id_produto="{id_produto}">
			<div class="addon">
				<input
					type="number"
					id="margem_lucro"
					class="fill"
					step='0.01'
					min='0'
					max='999999.99'
					required
					value="{margem_lucro}"
					autofocus>
				<span>%</span>
			</div>
		</form>
		<!-- END EXTRA_BLOCK_PRODUCT_PROFITMARGIN_FORM -->
	</div>

	<div>
		<span>A margem de lucro é usado na sugestão de preços para as Ordens de Compra finalizadas.</span>
	</div>
</div>
<!-- END EXTRA_BLOCK_POPUP_PRODUCT_PROFITMARGIN -->

<!-- BEGIN EXTRA_BLOCK_POPUP_PRODUCT_LOSSMARGIN -->
<div class="flex flex-dc gap-10" data-id_produto="{id_produto}">
	<div>
		<label class="produto-tipo caption">
			Produto
		</label>
		<div class="addon">
			{extra_block_product_button_status}
			{block_product_produto}
		</div>
	</div>

	<div>
		<label class="caption">
			Margem de Perda
		</label>

		<!-- BEGIN BLOCK_PRODUCT_LOSSMARGIN -->
		<div class="addon">
			<button class="product_bt_lossmargin_edit button-field textleft fill" data-id_produto="{id_produto}" title="Editar margem de perda">
				{margem_perda_formatted} %
			</button>
		</div>
		<!-- END BLOCK_PRODUCT_LOSSMARGIN -->

		<!-- BEGIN EXTRA_BLOCK_PRODUCT_LOSSMARGIN_FORM -->
		<form method="post" id="frm_product_margem_perda" class="flex fill" data-id_produto="{id_produto}">
			<div class="addon">
				<input
					type="number"
					id="margem_perda"
					class="fill"
					step='0.01'
					min='0'
					max='99.99'
					required
					value="{margem_perda}"
					autofocus>
				<span>%</span>
			</div>
		</form>
		<!-- END EXTRA_BLOCK_PRODUCT_LOSSMARGIN_FORM -->
	</div>

	<div>
		<span>A margem de perda é usada para estabelecer o preço de custo para cálculo do preço de venda nas Ordens de Compra finalizadas.</span>
	</div>
</div>
<!-- END EXTRA_BLOCK_POPUP_PRODUCT_LOSSMARGIN -->

<!-- BEGIN EXTRA_BLOCK_POPUP_KIT -->
<div class="w_productkit window flex flex-dc gap-10" data-id_produto='{id_produto}'>
	<div>
		<label class="produto-tipo caption">
			Produto
		</label>
		<div class="addon">
			{extra_block_product_button_status}
			{block_product_produto}
		</div>
	</div>

	<div class="box-header gap-10">
		Itens do kit
	</div>

	<div class="kit_not_found table tbody {hidden}">
		<div class="tr" style="padding: 20px 10px;">
			Não há produtos no kit.
		</div>
	</div>

	<div class="w_kit_table table tbody flex flex-dc">

		{extra_block_kit_tr}

		<!-- BEGIN EXTRA_BLOCK_KIT_TR -->
		<div class="w-kit tr flex flex-dc gap-10" data-id_produto="{id_produto}">

			<div>
				<label class="caption">Produto</label>
				<div class="addon">
					{extra_block_product_button_status}
					<span class="field fill">{produto}</span>
				</div>
			</div>

			<div class="flex-responsive gap-10">
				<div class="flex gap-10 flex-6">
					<div class="flex-2">
						<label class="caption">Quantidade</label>
						<!-- BEGIN BLOCK_KIT_QTD -->
						<div class="addon container">
							<button class="kit_bt_qtd button-field textleft one-line" title="Alterar quantidade do produto">
								{qtd_formatted} <span class="font-size-075">{produtounidade}</span>
							</button>
						</div>
						<!-- END BLOCK_KIT_QTD -->
						<!-- BEGIN EXTRA_BLOCK_KIT_QTD_FORM -->
						<form method="post" id="frm_kit_qtd" class="fill">
							<div class="addon">
								<input
									type="number"
									id="qtd"
									class="fill"
									step='0.001'
									min='0'
									max='999999.999'
									required
									placeholder='{qtd_formatted}'
									autofocus>
								<span class="font-size-075">{produtounidade}</span>
							</div>
						</form>
						<!-- END EXTRA_BLOCK_KIT_QTD_FORM -->
					</div>

					<div class="flex-2">
						<label class="caption">Preço no Kit</label>
						<!-- BEGIN BLOCK_KIT_PRECO -->
						<div class="addon container">
							<button class="kit_bt_preco button-field textleft one-line" title="Alterar preço do produto">
								R$ {preco_formatted} <span class="font-size-075">/{produtounidade}</span>
							</button>
						</div>
						<!-- END BLOCK_KIT_PRECO -->
						<!-- BEGIN EXTRA_BLOCK_KIT_PRECO_FORM -->
						<form method="post" id="frm_kit_preco" class="fill">
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
								placeholder='{preco_formatted}'
								autofocus>
								<span></span><span class="font-size-075">/{produtounidade}</span>
							</div>
						</form>
						<!-- END EXTRA_BLOCK_KIT_PRECO_FORM -->
					</div>
				</div>

				<div class="flex gap-10 flex-4">
					<div class="flex-2">
						<label class="caption">Total</label>
						<div class="addon container">
							<span>R$ {item_kit_total_formatted}</span>
						</div>
					</div>

					<div class="flex flex-jc-fe flex-ai-fe">
						<button type="button" class="product_bt_kit_delete button-icon button-red fa-solid fa-trash-can" title="Remover item"></button>
					</div>
				</div>
			</div>
		</div>
		<!-- END EXTRA_BLOCK_KIT_TR -->
	</div>

	<div>
		<form method="post" id="frm_product_kit" data-id_kit="{id_produto}" class="fill">
			<div class="flex flex-dc gap-10">
				<div>
					<label class="caption">Produto [Código ou Descrição]</label>
					<div class="autocomplete-dropdown">
						<input
						type="text"
						id="id_produto"
						class="product_search uppercase smart_search smart-search fill"
						data-source="popup"
						data-focus_next="#qtd"
						maxlength="50"
						value=""
						placeholder=""
						autofocus
						required >
					{block_product_autocomplete_search}
					</div>
				</div>

				<div class="flex gap-10">

					<div class="flex-1">
						<label class="caption">Quantidade</label>
						<div class="addon">
							<input
								type="number"
								id="qtd"
								class="textcenter fill"
								step='0.001'
								min='0.001'
								max="999999.999"
								placeholder=""
								required >
						</div>
					</div>

					<div class="flex flex-ai-fe flex-1">
						<button type="submit" class="button-blue fill" title="Adicionar item">Adicionar</button>
					</div>
				</div>
		</div>
		</form>
	</div>
</div>
<!-- END EXTRA_BLOCK_POPUP_KIT -->

<!-- BEGIN EXTRA_BLOCK_POPUP_CODBAR -->
<div class="w-productbarcode flex flex-dc gap-10 fill" data-id_produto="{id_produto}">

	<div>
		<label class="produto-tipo caption">
			Produto
		</label>
		<div class="addon">
			{extra_block_product_button_status}
			{block_product_produto}
		</div>
	</div>

	<div class="section-header">
		Códigos de barras
	</div>

	<div class="barcode_not_found table tbody {hidden}">
		<div class="tr" style="padding: 20px 10px;">
			Nenhum código cadastrado.
		</div>
	</div>

	<div class="w_barcode_table table tbody flex flex-dc">

		{extra_block_form_codbar_tr}

		<!-- BEGIN EXTRA_BLOCK_FORM_CODBAR_TR -->
		<div class="w-barcode tr flex gap-10" data-codbar="{codbar}">
			<div class="flex-5">
				<label class="caption">{barcode_legend}</label>
				<div class="addon">
					<span class="field disabled fill">{codbar}</span>
				</div>
			</div>

			<div class="flex flex-ai-fe flex-1">
				<button type="button" class="product_bt_barcode_delete button-icon button-red fa-solid fa-trash-can" title="Remover código de barras do produto"></button>
			</div>
		</div>
		<!-- END EXTRA_BLOCK_FORM_CODBAR_TR -->
	</div>

	<!-- <div class="section-header">
		Adicionar código
	</div> -->

	<div>
		<!-- <label class="caption">Adicionar código</label> -->
		<form method="post" id="frm_product_new_barcode" class="fill" data-id_produto="{id_produto}">
			<div class="flex gap-10">
				<div class="fill">
					<label class="caption">Código de barras [EAN-8, EAN-13, UPC-12]</label>
					<div class="addon">
						<input
						type="text"
						id="codbar"
						class="fill"
						pattern="\d{8}|\d{12}|\d{13}"
						maxlength="13"
						placeholder=""
						autocomplete="off"
						autofocus
						required>
					</div>
				</div>

				<div class="flex flex-ai-fe">
					<button type="submit" class="button-blue fill" title="Vincular código de barras ao produto">Adicionar</button>
				</div>
			</div>
		</form>
	</div>
</div>
<!-- END EXTRA_BLOCK_POPUP_CODBAR -->

<!-- BEGIN EXTRA_BLOCK_POPUP_VALIDADE -->
<div class="w-productvalidade flex flex-dc gap-10 fill" data-id_produto="{id_produto}">

	<div>
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

	<div class="section-header">
		Datas
	</div>

	<div class="product_expdate_notfound table tbody {product_expdate_notfound}">
		<div class="tr" style="padding: 20px 10px;">
			Nenhuma validade cadastrada.
		</div>
	</div>

	<div class="product_expdate_table table tbody flex flex-dc">

		{extra_block_product_expdate_tr}

		<!-- BEGIN EXTRA_BLOCK_PRODUCT_EXPDATE_TR -->
		<div class="product_expdate_tr product_expdate_{id_produtovalidade} tr flex gap-10" data-id_produtovalidade="{id_produtovalidade}">
			<div class="flex-3">
				<label class="caption">Validade</label>
				<div class="addon">
					<span class="field disabled fill">{data_formatted}</span>
				</div>
			</div>

			{extra_block_productexpdate_days}
			<!-- <div class="flex-3">
				<label class="caption">Restam</label>
				<div class="addon">
					<span class="field disabled fill">{dias}</span>
				</div>
			</div> -->

			<div class="flex flex-ai-fe flex-1">
				<button type="button" class="product_bt_validade_delete button-icon button-red fa-solid fa-trash-can" data-id_produtovalidade="{id_produtovalidade}" title="Remover data de validade do produto"></button>
			</div>
		</div>
		<!-- END EXTRA_BLOCK_PRODUCT_EXPDATE_TR -->
	</div>

	<!-- <div class="section-header">
		Adicionar Data
	</div> -->

	<div>
		<form method="post" id="frm_product_new_validade" class="fill" data-id_produto="{id_produto}">
			<div class="flex gap-10">
				<div class="fill">
					<label class="caption">Data de Validade</label>
					<div class="addon">
						<input
							type='date'
							id="data"
							class="fill"
							value='{data}'
							title="Data de validade"
							autofocus
							required>
					</div>
				</div>

				<div class="flex flex-ai-fe">
					<button type="submit" class="button-blue fill" title="Vincular código de barras ao produto">Adicionar</button>
				</div>
			</div>
		</form>
	</div>
</div>
<!-- END EXTRA_BLOCK_POPUP_VALIDADE -->

<div class="w_productsector_container flex flex-dc gap-10">

	<div class="w_productsector_not_found window card-container tr {hidden}">
		<div class="font-size-12 textcenter" style="padding: 80px 10px;">
			Nenhum produto encontrado.
		</div>
	</div>

	<div class="productsector_table flex flex-dc gap-10">

		{extra_block_product_sector}

		<!-- BEGIN EXTRA_BLOCK_PRODUCT_SECTOR -->
		<div class="w_productsector window card-container flex flex-dc gap-10" data-produtosetor="{produtosetor}" data-id_produtosetor="{id_produtosetor}">

			<div class="flex gap-10">

				<div class="box-header flex-1">

					<!-- BEGIN BLOCK_SETOR_EDITION -->
					<button type="button" class="productsector_produtosetor button-field textleft font-size-14 color-blue" data-id_produtosetor="{id_produtosetor}" title="Alterar descrição do setor">
						{produtosetor}
					</button>
					<!-- END BLOCK_SETOR_EDITION -->
					<!-- BEGIN EXTRA_BLOCK_SETOR_EDITION_FORM -->
					<form method="post" id="frm_productsector_produtosetor" class="" data-id_produtosetor='{id_produtosetor}'>
						<input
							type='text'
							id='produtosetor'
							class="font-size-14"
							placeholder=''
							value='{produtosetor}'
							maxlength='50'
							required
							autofocus>
					</form>
					<!-- END EXTRA_BLOCK_SETOR_EDITION_FORM -->
				</div>

				<div class="flex flex-ai-fe gap-10">
					<div>
						<label class="toggle" title="Exibir setor no módulo Garçom">

							<label class="caption flex flex-jc-center">
								<div class="true">Garçom</div>
								<div class="false">Garçom</div>
							</label>

							<div class="addon-transp">
								<input {garcom} class="productsector_bt_garcom hidden" type="checkbox" data-id_produtosetor="{id_produtosetor}">
								<span></span>
							</div>

						</label>
					</div>

					<div class="flex flex-jc-fe flex-ai-fe">
						<div class="menu-inter">
							<button class="menu-inter-button fa-solid button-blue fa-ellipsis-vertical"></button>

							<ul style="display: none;">
								<li class="productsector_bt_del flex flex-ai-center gap-10 color-red" data-id_produtosetor="{id_produtosetor}" data-text="{produtosetor}" title="Remover Setor">
									<i class="icon fa-solid fa-trash-can"></i>
									<span>Remover Setor</span>
								</li>
							</ul>
						</div>

					</div>

					<button class="{bt_expand} button-icon button-blue fa-solid {bt_expand_icon}"></button>
				</div>
			</div>

			<div class="product_container flex flex-dc gap-10 expandable" style="display: none;">

				<div class="product_not_found window hidden">
					<div class="font-size-12" style="padding: 20px 10px;">
						Setor sem produto.
					</div>
				</div>

				<div class="product_table table tbody flex flex-dc">

					{extra_block_product}

					<!-- BEGIN EXTRA_BLOCK_PRODUCT -->
					<div class="w-product w_product_{id_produto} window tr flex flex-dc gap-10" data-produto="{produto}" data-id_produto="{id_produto}">

						<div class="flex-responsive gap-10">

							<div class="flex flex-ai-center flex-jc-center">
								<button class="product_bt_img button-img button-field" data-id_produto="{id_produto}" title="Alterar foto do produto">
									<img class="img_product" src='pic/{imagem}' loading="lazy">
								</button>
							</div>

							<div class="flex flex-dc gap-10 flex-1">
								<div class="flex-responsive gap-10">

									<div class="flex-8">
										<label class="produto-tipo caption">
											<!-- BEGIN BLOCK_TIPO -->
											{produtotipo}
											<!-- END BLOCK_TIPO -->
										</label>

										<div class="addon">
											{extra_block_product_button_status}
											<!-- BEGIN EXTRA_BLOCK_PRODUCT_BUTTON_ATIVO -->
											<button type="button" class="product_bt_status product_{id_produto}_status button-green" data-id_produto="{id_produto}" title="Desativar produto para venda">{id_produto}</button>
											<!-- END EXTRA_BLOCK_PRODUCT_BUTTON_ATIVO -->
											<!-- BEGIN EXTRA_BLOCK_PRODUCT_BUTTON_INATIVO -->
											<button type="button" class="product_bt_status product_{id_produto}_status button-red" data-id_produto="{id_produto}" title="Ativar produto para venda">{id_produto}</button>
											<!-- END EXTRA_BLOCK_PRODUCT_BUTTON_INATIVO -->

											<!-- BEGIN BLOCK_PRODUCT_PRODUTO -->
											<button class="product_bt_produto product_{id_produto}_produto button-field textleft fill" data-id_produto="{id_produto}" title="Editar nome do produto">
												{produto}
											</button>
											<!-- END BLOCK_PRODUCT_PRODUTO -->
										</div>

										<!-- BEGIN EXTRA_BLOCK_FORM_PRODUCT -->
										<form method="post" id="frm_product_produto" class="flex fill" data-id_produto="{id_produto}">
											<input
												type='text'
												id='produto'
												class="fill"
												required
												placeholder='Descrição'
												value='{produto}'
												maxlength='50'
												autofocus>
										</form>
										<!-- END EXTRA_BLOCK_FORM_PRODUCT -->
									</div>

									<div class="flex gap-10 flex-6">
										<div class="flex-3">
											<label class="caption">Setor</label>
											<!-- BEGIN BLOCK_SETOR -->
											<div class="addon container">
												<button class="product_bt_setor button-field textleft" data-id_produto="{id_produto}" data-id_produtosetor="{id_produtosetor}" title="Alterar setor do produto">
													{produtosetor}
												</button>
											</div>
											<!-- END BLOCK_SETOR -->
											<!-- BEGIN EXTRA_BLOCK_SETOR_FORM -->
											<form method="post" id="frm_product_setor" class="fill" data-id_produto="{id_produto}">
												<div class="addon">
													<select id="id_produtosetor" class="fill" autofocus>{setor_option}</select>
												</div>
											</form>
											<!-- END EXTRA_BLOCK_SETOR_FORM -->
										</div>

										<div class="flex-3 fill">
											<label class="caption">Impressão Cozinha</label>
											<!-- BEGIN BLOCK_IMPRESSORA -->
											<div class="addon container">
												<button class="product_bt_impressora button-field textleft fill" data-id_produto="{id_produto}" title="Selecione uma impressora para impressão na cozinha">
													{printer_desc}

												</button>
											</div>
											<!-- END BLOCK_IMPRESSORA -->
											<!-- BEGIN EXTRA_BLOCK_IMPRESSORA_FORM -->
											<form method="post" id="frm_product_impressora" class="fill" data-id_produto="{id_produto}">
												<div class="addon">
													<select id="impressora" class="fill" autofocus>
														{printer_option}
														<!-- BEGIN EXTRA_BLOCK_PRINTER_OPTION -->
														<option value="{id_impressora}" {selected}>{descricao}</option>
														<!-- END EXTRA_BLOCK_PRINTER_OPTION -->
													</select>
												</div>
											</form>
											<!-- END EXTRA_BLOCK_IMPRESSORA_FORM -->
										</div>
									</div>
								</div>

								<div class="flex flex-dc gap-10">

									<div class="flex-responsive gap-10">

										<!-- BEGIN BLOCK_GROUP_PRECO -->
										<div class="group-preco product_{id_produto}_prices flex gap-10 flex-5">
											<div class="flex-2">
												<label class="caption">Preço {preco_percent}</label>
												<!-- BEGIN BLOCK_PRODUCT_PRECO -->
												<div class="addon container">
													<button class="product_bt_preco button-field textleft fill one-line" data-id_produto="{id_produto}" data-id_compraitem="{id_compraitem}" title="Editar preço do produto">
														<span class="{class_saleoff}">R$ {preco_formatted} <span class="font-size-075">/{produtounidade}</span></span>
													</button>
												</div>
												<!-- END BLOCK_PRODUCT_PRECO -->
												<!-- BEGIN EXTRA_BLOCK_PRODUCT_PRECO_FORM -->
												<form method="post" id="frm_product_preco" class="fill" data-id_produto="{id_produto}" data-id_compraitem="{id_compraitem}">
													<div class="addon container">
														<span class="{class_saleoff}">R$</span>
														<input
															type="number"
															id="preco"
															class="fill"
															step='0.01'
															min='0'
															max='999999.99'
															maxlength="6"
															required
															placeholder='{preco_formatted}'
															autofocus>
														<span class="{class_saleoff}"><span class="font-size-075">/{produtounidade}</span></span>
													</div>
												</form>
												<!-- END EXTRA_BLOCK_PRODUCT_PRECO_FORM -->
											</div>

											<div class="flex-2">
												<label class="caption">Promoção {preco_promo_percent}</label>
												<!-- BEGIN BLOCK_PRODUCT_PRECO_PROMO -->
												<div class="addon container">
													<button class="product_bt_preco_promo button-field textleft fill one-line" data-id_produto="{id_produto}" data-id_compraitem="{id_compraitem}" title="Editar preço promocional do produto">
														<span class="{class_saleoff_saleoff}">R$ {preco_promo_formatted} <span class="font-size-075">/{produtounidade}</span></span>
													</button>
												</div>
												<!-- END BLOCK_PRODUCT_PRECO_PROMO -->
												<!-- BEGIN EXTRA_BLOCK_PRODUCT_PRECO_PROMO_FORM -->
												<form method="post" id="frm_product_preco_promo" class="fill" data-id_produto="{id_produto}" data-id_compraitem="{id_compraitem}">
													<div class="addon">
														<span class="{class_saleoff_saleoff}">R$</span>
														<input
														type="number"
														id="preco_promo"
														class="fill"
														step='0.01'
														min='0'
														max='999999.99'
														maxlength="6"
														required
														placeholder='{preco_promo_formatted}'
														autofocus>
														<span class="{class_saleoff_saleoff}"><span class="font-size-075">/{produtounidade}</span></span>
													</div>
												</form>
												<!-- END EXTRA_BLOCK_PRODUCT_PRECO_PROMO_FORM -->
											</div>
										</div>
										<!-- END BLOCK_GROUP_PRECO -->

										<div class="flex gap-10 flex-5">
											<div class="flex-2">
												<label class="caption">Estoque</label>
												<div class="addon menu-inter">
													<!-- BEGIN BLOCK_PRODUCT_STOCK -->
													<span class="estoque_{id_produto} fill one-line">{estoque_formatted} <span class="font-size-075">{produtounidade}</span></span>
													<!-- END BLOCK_PRODUCT_STOCK -->

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

											<div class="flex-2">
												{extra_block_estoque_secundario}

												<!-- BEGIN EXTRA_BLOCK_ESTOQUE_SECUNDARIO -->
												<label class="caption">Est. Secundário</label>
												<div class="addon menu-inter">

													<!-- BEGIN BLOCK_PRODUCT_STOCKSECOND -->
													<span class="estoque_secundario_{id_produto} fill one-line">{estoque_secundario_formatted} <span class="font-size-075">{produtounidade}</span></span>
													<!-- END BLOCK_PRODUCT_STOCKSECOND -->

													<ul>
														<li class="product_bt_estoque_secundario flex flex-ai-center gap-10 color-green" data-id_produto="{id_produto}" data-screen="add" title="Adicionar produtos ao estoque">
															<i class="icon fa-solid fa-square-plus"></i>
															<span>Adicionar estoque</span>
														</li>

														<li class="product_bt_estoque_secundario flex flex-ai-center gap-10 color-red" data-id_produto="{id_produto}" data-screen="del" title="Reduzir estoque de produtos ">
															<i class="icon fa-solid fa-square-minus"></i>
															<span>Reduzir estoque</span>
														</li>

														<li class="product_bt_estoque_secundario flex flex-ai-center gap-10 color-blue" data-id_produto="{id_produto}" data-screen="update" title="Atualizar estoque de produtos">
															<i class="icon fa-solid fa-equals"></i>
															<span>Atualizar estoque</span>
														</li>

														<li class="product_bt_estoque_secundario flex flex-ai-center gap-10 color-blue" data-id_produto="{id_produto}" data-screen="transf" title="Transferir estoque secundário para primário">
															<i class="icon fa-solid fa-left-right"></i>
															<span>Transferir estoque</span>
														</li>
													</ul>

													<button class="menu-inter-button fa-solid fa-ellipsis-vertical button-blue"></button>
												</div>
												<!-- END EXTRA_BLOCK_ESTOQUE_SECUNDARIO -->
											</div>
										</div>

										<div class="flex gap-10 flex-6">
											<div class="">
												<label class="caption">Tipo</label>
												<!-- BEGIN BLOCK_UNIDADE -->
												<div class="addon container">
													<button class="product_bt_unidade button-field textleft" data-id_produto="{id_produto}" title="Alterar unidade do produto">
														{produtounidade}

													</button>
												</div>
												<!-- END BLOCK_UNIDADE -->
												<!-- BEGIN EXTRA_BLOCK_UNIDADE_FORM -->
												<form method="post" id="frm_product_unidade" data-id_produto="{id_produto}">
													<div class="addon">
														<select id="id_produtounidade" class="fill" autofocus>{produtounidade_option}</select>
													</div>
												</form>
												<!-- END EXTRA_BLOCK_UNIDADE_FORM -->
											</div>

											<div class="flex-4">
												<label class="caption">Descrição</label>
												<!-- BEGIN BLOCK_OBS -->
												<div class="addon container">
													<button class="product_bt_obs button-field textleft fill" data-id_produto="{id_produto}" title="Editar descrição do produto">
														{obs}

													</button>
												</div>
												<!-- END BLOCK_OBS -->
												<!-- BEGIN EXTRA_BLOCK_OBS_FORM -->
												<form method="post" id="frm_product_obs" class="fill" data-id_produto="{id_produto}">
													<div class="addon">
														<input
															type='text'
															id='obs'
															class="fill"
															placeholder=''
															value='{obs}'
															maxlength='255'
															autofocus>
													</div>
												</form>
												<!-- END EXTRA_BLOCK_OBS_FORM -->
											</div>

											<!-- BEGIN BLOCK_PRODUCT_MENU -->
											<div class="flex flex-jc-fe flex-ai-fe">
												<div class="menu-inter">
													<ul>
														<li class="product_bt_complement flex flex-ai-center gap-10 color-blue" data-id_produto="{id_produto}" title="Cadastro de complementos para o produto">
															<i class="icon fa-solid fa-list-ul"></i>
															<span>Complemento</span>
														</li>

														<li class="product_bt_composition flex flex-ai-center gap-10 color-blue" data-id_produto="{id_produto}" title="Cadastro de Composição">
															<i class="icon fa-solid fa-code-merge"></i>
															<span>Composição</span>
														</li>

														<li class="product_bt_kit flex flex-ai-center gap-10 color-blue" data-id_produto="{id_produto}" title="Cadastro de kit do produto">
															<i class="icon fa-solid fa-boxes-stacked"></i>
															<span>Kit</span>
														</li>

														<li class="product_bt_barcode flex flex-ai-center gap-10 color-blue" data-id_produto="{id_produto}" title="Cadastro de código de barras do produto">
															<i class="icon fa-solid fa-barcode"></i>
															<span>Código de barras</span>
														</li>

														<li class="product_bt_validade flex flex-ai-center gap-10 color-blue" data-id_produto="{id_produto}" title="Cadastro de validade do produto">
															<i class="icon fa-solid fa-calendar-days"></i>
															<span>Controle de Validade</span>
														</li>

														<li class="product_bt_profitmargin flex flex-ai-center gap-10 color-blue" data-id_produto="{id_produto}" title="Ajustar Margem de Lucro">
															<i class="icon fa-solid fa-solid fa-arrow-trend-up"></i>
															<span>Margem de Lucro</span>
														</li>

														<li class="product_bt_lossmargin flex flex-ai-center gap-10 color-blue" data-id_produto="{id_produto}" title="Ajustar Margem de Perda">
															<i class="icon fa-solid fa-arrow-trend-down"></i>
															<span>Margem de Perda</span>
														</li>

														<li class="bt_purchaseorder_history flex flex-ai-center gap-10 color-blue" data-id_produto="{id_produto}" data-produto="{produto}" title="Histórico de venda do produto">
															<i class="icon fa-solid fa-chart-column"></i>
															<span>Histórico de Venda</span>
														</li>
													</ul>

													<button class="menu-inter-button fa-solid fa-ellipsis-vertical button-blue"></button>
												</div>
												<!-- <button class="bt_expand button-icon button-blue fa-solid fa-chevron-down"></button> -->
											</div>
											<!-- END BLOCK_PRODUCT_MENU -->
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- END EXTRA_BLOCK_PRODUCT -->
				</div>
			</div>

			<div class="flex flex-jc-fe">
				<button type="button" class="bt_product_new button-blue" data-id_produtosetor="{id_produtosetor}">Adicionar produto</button>
			</div>

		</div>
		<!-- END EXTRA_BLOCK_PRODUCT_SECTOR -->
	</div>
</div>

<!-- <div class="footer-popup flex-jc-fe">
	<div class="popup-menu">
		<button class="popup-main-button fa-solid fa-ellipsis-vertical button-pink"></button>

		<ul>

			<li class="flex flex-ai-center gap-10 mobile">
				<label>Consulta</label>
				<button type="button" class=" button-blue " title="Consultar produto"></button>
			</li>

			<li class="flex flex-ai-center gap-10">
				<label>Novo produto</label>
				<button type="button" class="bt_product_new button-blue " title="Cria novo produto. Digite um código no campo de busca para definir o código do novo produto"></button>
			</li>
		</ul>
	</div>
</div> -->
<!-- END BLOCK_PAGE -->