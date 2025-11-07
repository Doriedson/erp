<!-- BEGIN BLOCK_PAGE -->

<!-- BEGIN EXTRA_BLOCK_POPUP_WAITERSECTOR_PESO -->
<div class="flex flex-dc gap-10">
    <div>
        <label>Produto</label>
        <div class="addon">
            <span>{produto}</span>
        </div>
    </div>

    <form method="post" id="frm_product_qty" class="flex gap-10" data-id_produto="{id_produto}">
        <div class="fill">
            <label class="caption">Peso</label>
            <div class="addon flex fill">
                <input
                    type="number"
                    id="qty"
                    class="fill"
                    step='0.001'
                    min='0'
                    max='999999.999'
                    placeholder="0,000"
                    required
                    autofocus>
                <span class="font-size-075">{produtounidade}</span>
            </div>

        </div>

        <div class="flex flex-ai-fe">
            <button type="submit" class="button-blue fill">Confirmar</button>
        </div>
    </form>
</div>
<!-- END EXTRA_BLOCK_POPUP_WAITERSECTOR_PESO -->

<div class="w-waitersector-container flex flex-dc gap-10">

    <div class="card-container">

        <div class="w_waitertable_header box-header">{mesa_desc} - Seleção de Produtos</div>

        <div class="flex gap-10">
            <div class="flex-1">

                <label class="caption">Código ou Descrição</label>

                <div class="autocomplete-dropdown">
                    <input
                        type="text"
                        id="product_search"
                        class="uppercase product_search smart-search fill flex-4"
                        data-source="waiter"
                        data-sort="sector"
                        title="Consulta de produto"
                        maxlength="40"
                        required
                        placeholder=""
                        autocomplete="off"
                        autofocus>
                </div>
            </div>

            <div class="flex flex-ai-fe">

                <button class='waiterproduct_bt_clear button-icon button-blue' title="Limpar campo de busca">
                    <i class="fa-solid fa-eraser"></i>
                </button>
            </div>

        </div>
    </div>

    <div class="waitersector_notfound window fill {waitersector_notfound}">
        <div class="font-size-12" style="padding: 40px 10px;">
            Nenhum produto encontrado.
        </div>
    </div>

    <div class="w_waitersector_table flex flex-dc gap-10">

        {extra_block_sector}

        <!-- BEGIN EXTRA_BLOCK_SECTOR -->
        <div class="w_waiter_product_sector card-container window flex flex-dc font-size-12" data-id_produtosetor="{id_produtosetor}">

            <div class="flex gap-10 flex-1">
                <div class="section-header flex flex-ai-center textleft flex-1">{produtosetor}</div>
                <div class="flex flex-ai-fe">
                    {sector_bt_expand}
                    <!-- BEGIN EXTRA_BLOCK_SECTOR_BT_EXPAND -->
                    <button class="waitersector_bt_expand button-icon button-blue fa-solid fa-chevron-down"></button>
                    <!-- END EXTRA_BLOCK_SECTOR_BT_EXPAND -->
                    <!-- BEGIN EXTRA_BLOCK_SECTOR_BT_COLLAPSE -->
                    <button class="bt_collapse button-icon button-blue fa-solid fa-chevron-up"></button>
                    <!-- END EXTRA_BLOCK_SECTOR_BT_COLLAPSE -->
                </div>
            </div>

            <div class="expandable" style="display: none;">

                <div class="product_not_found hidden">
                    <div class="font-size-12" style="padding: 20px 10px;">
                        Setor sem produto.
                    </div>
                </div>

                <div class="product_table flex flex-dc table tbody">

                    {extra_block_product}

                    <!-- BEGIN EXTRA_BLOCK_WAITERSECTOR_PRODUCT_NONE -->
                    <div class="product_not_found fill">
                        <div class="font-size-12" style="padding: 40px 10px;">
                            Não há produtos na lista.
                        </div>
                    </div>
                    <!-- END EXTRA_BLOCK_WAITERSECTOR_PRODUCT_NONE -->

                    <!-- BEGIN EXTRA_BLOCK_WAITERSECTOR_PRODUCT -->
                    <div class="waiterproduct_produto waiterproduct_produto_{id_produto} window tr flex flex-dc fill font-size-12 gap-10" data-produto="{produto}" data-id_produto="{id_produto}" data-preco="{preco_final}">

                        <div class="flex gap-10">
                            <div class="flex flex-ai-center flex-1">
                                <span class="padding-h5 color-gray-darkest">{produto}</span>
                            </div>

                            <div class="waiterproduct_obs flex flex-ai-fe pos-rel">

                                <button type="button" class="bt_comment button-icon button-blue fa-regular fa-comment" disabled title="Observação do Produto"></button>

                                <div class="float-form hidden">

                                    <div>
                                        <button class="bt_commentclose button-icon button-gray fa-solid fa-xmark pos-abs" style="top: -15px; right: -15px;" title="Fechar"></button>
                                    </div>

                                    <div class="flex gap-10">
                                        <div>
                                            <label class="caption">Observação do Produto</label>
                                            <input type="text" class="waiterproduct_bt_obs waiterproduct_{id_produto}_obs fill" maxlength="255" value="" autocomplete="off" list="autocompleteOff" autofocus="">
                                        </div>

                                        <div class="flex gap-10 flex-ai-fe">
                                            <button type="button" class="waiterproduct_bt_obs_confirma flex flex-ai-center button-green" title="Salvar Observação">
                                                <i class="icon fa-solid fa-check"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-10">
                            <div class="flex flex-dc flex-1 gap-10 flex-jc-fe">
                                <span class="waiterproduct_{id_produto}_obs fill padding-h5 color-red font-size-09"></span>
                                <span class="one-line padding-h5">R$ {preco_final_formatted} <span class="font-size-075">/{produtounidade}</span></span>
                            </div>
                            <div class='flex flex-ai-fe gap-10'>
                                <!-- <button class='bt_expand_focus button-icon button-blue fa-solid fa-file-pen' data-icon="keep" title="Observação"></button> -->
                                {extra_block_product_un}
                                <!-- BEGIN EXTRA_BLOCK_WAITERSECTOR_PRODUCT_UN -->
                                <button class='waiterproduct_bt_del button-icon button-red fa-solid fa-minus'></button>
                                <div class="button-icon">
                                    <span class="waiterproduct_{id_produto}_qtd">0</span><span class="font-size-075"> {produtounidade}</span>
                                </div>

                                <button class='waiterproduct_bt_add button-icon button-green fa-solid fa-plus'></button>
                                <!-- END EXTRA_BLOCK_WAITERSECTOR_PRODUCT_UN -->
                                <!-- BEGIN EXTRA_BLOCK_WAITERSECTOR_PRODUCT_KG -->
                                <div class="button-icon">
                                    <span class="waiterproduct_{id_produto}_qtd">0</span><span class="font-size-075"> {produtounidade}</span>
                                </div>
                                <button class='waiterproduct_bt_show_qty button-icon button-blue fa-solid fa-scale-balanced' data-id_produto="{id_produto}"></button>
                                <!-- END EXTRA_BLOCK_WAITERSECTOR_PRODUCT_KG -->
                            </div>
                        </div>
                    </div>
                    <!-- END EXTRA_BLOCK_WAITERSECTOR_PRODUCT -->
                </div>
            </div>
        </div>
        <!-- END EXTRA_BLOCK_SECTOR -->
    </div>

</div>

<div class="footer-popup flex-jc-fe">
	<div class="popup-menu">
        <div class="flex gap-10">
            <div class="flex flex-ai-center gap-10">
                <button type="button" class="waitertable_bt_revision button-float button-blue" title="Revisar pedido">
                    <i class="icon fa-solid fa-list-check"></i>
                    <span>Revisar pedido</span>
                </button>
			</div>
            <button class="popup-main-button fa-solid fa-ellipsis-vertical button-pink"></button>
        </div>

		<ul>

            <li class="waitertable_bt_table flex flex-ai-center gap-10 color-blue" title="Selecionar mesa">
                <i class="icon fa-solid fa-chair"></i>
				<span>Lista de mesas</span>
			</li>

    		<li class="waitertable_bt_payment flex flex-ai-center gap-10 color-blue" title="Ver mesa">
                <i class="icon fa-solid fa-receipt"></i>
				<span>Ver mesa</span>
			</li>
		</ul>
	</div>
</div>
<!-- END BLOCK_PAGE -->