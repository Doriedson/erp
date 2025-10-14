<!-- BEGIN BLOCK_PAGE -->
<div class="w-selfservice-container box-container flex flex-dc gap-10">

    <div class="w_waitertable_header box-header gap-10">
        <i class="icon fa-solid fa-bell-concierge"></i>
        <span>Self-Service</span>
    </div>

    <!-- BEGIN BLOCK_PRODUCT -->
    <div class="w-selfservice-product flex gap-10">
        <form method="post" id="frm_selfservice_product" class="fill">

            <label class="caption flex flex-ai-center gap-5">
                <i class="fa-solid fa-magnifying-glass"></i>
                Produto (código ou descrição)
            </label>

            <div class="autocomplete-dropdown">
                <input
                    type="text"
                    id="product_search"
                    class="uppercase product_search smart_search smart-search fill"
                    data-source="popup"
                    data-sort="active"
                    maxlength="40"
                    required
                    placeholder=""
                    autocomplete="off"
                    autofocus>

                {block_product_autocomplete_search}
            </div>
        </form>
    </div>
    <!-- END BLOCK_PRODUCT -->

    <!-- <div class="section-header">
        Quantidade
    </div> -->

    <div class="w-selfservice-qty flex flex-dc gap-10 hidden">
        <!-- Selecione um produto acima. -->
    </div>

    <!-- BEGIN EXTRA_BLOCK_QTY -->
    <div class="w-selfservice-qty flex flex-dc gap-10" data-id_produto="{id_produto}" data-preco="{preco_final}" data-produto="{produto}" data-produtounidade="{produtounidade}">
        <div class="flex-responsive gap-10">
            <div class="flex-6 flex gap-10">
                <div class="flex-1">
                    <label class="caption">Produto</label>
                    <div class="addon">
                        <span class="{class_status}">{id_produto}</span>
                        <span class="field uppercase fill">{produto}</span>
                    </div>
                </div>

                <div class="flex flex-ai-fe">
                    <button type="button" class="selfservice_bt_productsearch button-icon button-blue fa-solid fa-magnifying-glass" title="Procurar produto"></button>
                </div>
            </div>

            <div class="flex-6"></div>
        </div>

        <div class="flex-responsive gap-10">
            <div class="flex gap-10 flex-6">

                <div class="flex-2">
                    <label class="caption">Preço</label>
                    <div class="addon">
                        <span class="field">R$ {preco_final_formatted} <span class="font-size-075">/{produtounidade}</span></span>

                    </div>
                </div>

                <div class="flex-2">
                    <label class="caption">Quantidade</label>
                    <div class="addon">
                        <input
                            type="number"
                            id="qtd"
                            class="selfservice_qtd fill textright"
                            step="0.001"
                            min="0.001"
                            max="999999.999"
                            required=""
                            title="Quantidade do produto."
                            placeholder="0,000"
                            autofocus>
                        <span class="font-size-075">{produtounidade}</span>
                    </div>
                </div>


                <div class="flex-2">
                    <label class="caption color-blue">Total</label>
                    <div class="addon color-blue flex-jc-fe">
                        <span class="field">R$ <span id="selfservice_total">0,00</span></span>
                    </div>
                </div>
            </div>

            <div class="flex-6"></div>
        </div>
    </div>
    <!-- END EXTRA_BLOCK_QTY -->

    <!-- <div class="section-header">
        Mesa
    </div> -->

    <div class="w-waitertable-search flex gap-10">
        <div class="flex-1">
            <label class="caption flex flex-ai-center gap-5">
                <i class="fa-solid fa-magnifying-glass"></i>
                Mesa (número ou descrição)
            </label>

            <div class="autocomplete-dropdown flex flex-dc gap-10">
                <input
                    type="text"
                    id="table_search"
                    class="uppercase table_search smart_search smart-search fill"
                    data-screen="selfservice"
                    maxlength="40"
                    required
                    placeholder=""
                    autocomplete="off"
                    >

                <div class="padding-t10 w-waitertable-container flex-table gap-10">

                    {extra_block_table}

                    <!-- BEGIN EXTRA_BLOCK_TABLE -->
                    <div class="waitertable_table flex-4-col ticket-border gap-10 flex-ai-center font-size-12 pos-rel {status}" data-id_mesa="{id_mesa}" data-mesa="{mesa}">

                        <div class="flex flex-ai-center">
                            {button_view}
                        </div>
                        <div class="flex flex-dc flex-ai-center flex-jc-center gap-10 flex-1">
                            <div>{mesa}</div>
                            <div class="grid">
                                <span class="one-line textcenter font-size-09">{cliente}</span>
                                <span class="one-line textcenter color-gray font-size-09">{garcom}</span>
                            </div>
                        </div>
                        <div class="flex flex-ai-center">
                            {button_add}
                        </div>
                    </div>

                    <!-- END EXTRA_BLOCK_TABLE -->
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END BLOCK_PAGE -->