<!-- BEGIN BLOCK_PAGE -->
<div class="box-container flex gap-10">
    <div class="box-header gap-10 flex-1">
        <i class="icon fa-solid fa-boxes-stacked"></i>
        <span>Cardápio Digital</span>
    </div>
    <div class="flex flex-ai-fe">
        <button type="button" class="bt_load button-blue button-icon" data-page="product" title="Cadastro & Consulta de produtos">
            <i class="fa-solid fa-boxes-stacked"></i>
        </button>
    </div>
</div>

<div class="box-container flex flex-ai-center flex-dc">
    <div class="flex fill pos-rel">
        <img id="img_digitalmenuheader_view" class="fill" src="./assets/digitalmenu_header.png?t={timestamp}">

        <div class="pos-abs" style="right: 10px; bottom: 10px;">

            <button type="button" class="bt_uploadfile button-gray flex gap-5 flex-ai-center" data-target="digitalmenu-header">
                <i class="icon fa-solid fa-arrow-up-from-bracket"></i>
                <span>Escolher imagem</span>
            </button>
        </div>
    </div>

	<div class="flex flex-ai-center flex-jc-center" style="margin-top: -30px; z-index: 0;">

		<div class="flex flex-ai-center flex-jc-center pos-rel" style="
			width: 135px;
			height: 135px;
			min-width: 135px;
			min-height: 135px;
			border-radius:50%;
			background-color: white;
			box-shadow: 0px 5px 5px 0px gray;">
			<img id="img_digitalmenulogo_view" src="./assets/digitalmenu_logo.png?t={timestamp}">

			<div class="pos-abs" style="right: -10px; bottom: -10px;">

            <button type="button" class="bt_uploadfile button-gray" data-target="digitalmenu-logo">
                <i class="icon fa-solid fa-arrow-up-from-bracket"></i>
                <!-- <span>Escolher imagem</span> -->
            </button>
        </div>
		</div>
	</div>

    <div class="font-size-14 textcenter color-gray-darkest padding-h10 padding-v20">{empresa}</div>

    <div class="fill flex flex-jc-fe">
        <button type="button" class="bt_module button-blue">Ver Cardápio</button>
    </div>
</div>

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

			<div class="box-header padding-b10 gap-10 flex-ai-fe">

                <button type="button" class="productsector_produtosetor button-field textleft font-size-14 color-blue" data-id_produtosetor="{id_produtosetor}" title="Alterar descrição do setor">
                    {produtosetor}
                </button>

                <div class="flex-1"></div>

                <label class="toggle" title="Exibir setor no cardápio digital">
                    <label class="caption flex flex-jc-center">
                        <div class="true">Visível</div>
                        <div class="false">Oculto</div>
                    </label>

                    <div class="addon-transp">
                        <input {cardapio_setor} class="bt_digitalmenusector hidden" type="checkbox" data-id_produtosetor="{id_produtosetor}">
                        <span></span>
                    </div>
                </label>

				<button class="productsector_digitalmenu_bt_expand button-icon button-blue fa-solid fa-chevron-down"></button>
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

                                <div class="flex gap-10">
                                    <div class="flex-1">
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
                                </div>

                                <div class="flex-responsive gap-10">
                                    <div class="flex gap-10 flex-8">
                                        <div class="flex-1">
                                            <label class="caption">Descrição</label>
                                            <div class="addon container">
                                                <button class="product_bt_obs button-field textleft fill" data-id_produto="{id_produto}" title="Editar descrição do produto">
                                                    {obs}
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex gap-10 flex-6">
                                        {block_group_preco}

                                        <div class="flex flex-ai-fe">

                                            <label class="toggle" title="Exibir produto no cardápio digital">

                                                <label class="caption flex flex-jc-center">
                                                    <div class="true">Visível</div>
                                                    <div class="false">Oculto</div>
                                                </label>

                                                <div class="addon-transp">
                                                    <input {cardapio_produto} class="bt_digitalmenuproduct hidden" type="checkbox" data-id_produto="{id_produto}">
                                                    <span></span>
                                                </div>
                                            </label>

                                        </div>
                                    </div>
                                </div>
							</div>
						</div>
					</div>
					<!-- END EXTRA_BLOCK_PRODUCT -->
				</div>

			</div>
		</div>
		<!-- END EXTRA_BLOCK_PRODUCT_SECTOR -->
	</div>
</div>
<!-- END BLOCK_PAGE -->