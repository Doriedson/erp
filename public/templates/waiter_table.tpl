<!-- BEGIN BLOCK_PAGE -->
<div class="box-container flex flex-dc gap-10">

    <div class="w_waitertable_header box-header gap-10">
        <i class="icon fa-solid fa-chair"></i>
        <span>Lista de Mesas</span>
    </div>

    <div class="flex gap-10">

        <!-- <form method="post" id="frm_table_search" class="flex gap-10"> -->

            <div class="flex-1">
                <label class="caption">Buscar mesa</label>

                <div class="autocomplete-dropdown flex flex-dc gap-10">
                    <input
                        type="text"
                        id="table_search"
                        class="uppercase table_search smart_search smart-search fill"
                        data-screen="waiter_table"
                        maxlength="40"
                        required
                        placeholder=""
                        autocomplete="off"
                        autofocus>

                    <!-- BEGIN BLOCK_TABLE_AUTOCOMPLETE_SEARCH -->
                    <!-- <ul class="dropdown-list"> -->

                        <!-- BEGIN BLOCK_ITEM_SEARCH_NONE -->
                        <!-- <li class="padding-10"></li> -->
                        <!-- END BLOCK_ITEM_SEARCH_NONE -->

                        <!-- BEGIN EXTRA_BLOCK_ITEM_SEARCH_NOTFOUND -->
                        <!-- <li class="padding-10">Nenhuma mesa encontrada.</li> -->
                        <!-- END EXTRA_BLOCK_ITEM_SEARCH_NOTFOUND -->

                        <!-- BEGIN EXTRA_BLOCK_ITEM_SEARCH -->
                        <!-- <li class="dropdown-item" data-descricao="{mesa}" data-sku="{id_mesa}"> -->
                            <!-- <div class="flex-responsive gap-10"> -->
                                <!-- <div class="flex-13"> -->
                                    <!-- <div class="addon"> -->
                                        <!-- <span class="field fill">{mesa}</span> -->
                                    <!-- </div> -->
                                <!-- </div> -->
                            <!-- </div> -->
                        <!-- </li> -->
                        <!-- END EXTRA_BLOCK_ITEM_SEARCH -->
                    <!-- </ul> -->
                    <!-- END BLOCK_TABLE_AUTOCOMPLETE_SEARCH -->

                    <div class="w-waitertable-container padding-t10 flex-table gap-10">

                        {extra_block_table}

                        <!-- BEGIN EXTRA_BLOCK_TABLE_NOTFOUND -->
                        <div class="table_not_found window fill">
                            <div class="font-size-12" style="padding: 40px 10px;">
                                Nenhuma mesa localizada.
                            </div>
                        </div>
                        <!-- END EXTRA_BLOCK_TABLE_NOTFOUND -->

                        <!-- BEGIN EXTRA_BLOCK_TABLE_PAYMENT -->
                        <div class="waitertable_table flex-4-col ticket-border gap-10 font-size-12 border-red" data-id_mesa="{id_mesa}" data-versao="{versao}" data-mesa="{mesa}">

                            <div class="flex flex-dc flex-jc-sb gap-10 flex-1">
                                <div>{mesa}</div>
                                <div class="grid">
                                    <span class="one-line color-gray font-size-09"><span class="fa-solid fa-user font-size-12"></span>&nbsp;&nbsp;{cliente}</span>
                                    <span class="one-line color-gray font-size-09"><span class="fa-solid fa-user-pen font-size-12"></span>&nbsp;&nbsp;{garcom}</span>
                                    <span class="one-line font-size-09 padding-t10">Mesa em pagamento</span>
                                    <span class="one-line font-size-09">{data_formatted}</span>
                                </div>
                            </div>
                            <div class="flex flex-dc gap-10">
                                <div class="flex flex-ai-center">
                                    {button_select}
                                    <!-- BEGIN EXTRA_BLOCK_BUTTON_SELECT -->
                                    <button type="button" class="waitertable_bt_select button-float button-icon button-blue fa-solid fa-utensils" title="Atender mesa"></button>
                                    <!-- END EXTRA_BLOCK_BUTTON_SELECT -->
                                    <!-- BEGIN EXTRA_BLOCK_BUTTON_ADD -->
                                    <button type="button" class="selfservice_table_bt_select button-float button-icon button-blue fa-solid fa-plus" title="Adicionar a mesa"></button>
                                    <!-- END EXTRA_BLOCK_BUTTON_ADD -->
                                </div>
                            </div>
                        </div>
                        <!-- END EXTRA_BLOCK_TABLE_PAYMENT -->

                        <!-- BEGIN EXTRA_BLOCK_TABLE_BUSY -->
                        <div class="waitertable_table flex-4-col ticket-border gap-10 font-size-12 border-orange" data-id_mesa="{id_mesa}" data-versao="{versao}" data-mesa="{mesa}">

                            <div class="flex flex-dc flex-jc-sb gap-10 flex-1">
                                <div>{mesa}</div>
                                <div class="grid">
                                    <span class="one-line color-gray font-size-09"><span class="fa-solid fa-user font-size-12"></span>&nbsp;&nbsp;{cliente}</span>
                                    <span class="one-line color-gray font-size-09"><span class="fa-solid fa-user-pen font-size-12"></span>&nbsp;&nbsp;{garcom}</span>
                                    <span class="one-line font-size-09 padding-t10">Mesa em atendimento</span>
                                    <span class="one-line font-size-09">{data_formatted}</span>
                                </div>
                            </div>
                            <div class="flex flex-dc gap-10">

                                <div class="flex flex-ai-center">
                                    {button_select}
                                    <!-- BEGIN EXTRA_BLOCK_BUTTON_SELECT -->
                                    <button type="button" class="waitertable_bt_select button-float button-icon button-blue fa-solid fa-utensils" title="Atender mesa"></button>
                                    <!-- END EXTRA_BLOCK_BUTTON_SELECT -->
                                    <!-- BEGIN EXTRA_BLOCK_BUTTON_TRANSFER -->
                                    <button type="button" class="waitertable_bt_transf button-float button-icon button-blue fa-solid fa-turn-down" title="Transferir para esta mesa" data-id_mesa="{id_mesa}" data-versao="{versao}"></button>
                                    <!-- END EXTRA_BLOCK_BUTTON_TRANSFER -->
                                    <!-- BEGIN EXTRA_BLOCK_BUTTON_ADD -->
                                    <button type="button" class="selfservice_table_bt_select button-float button-icon button-blue fa-solid fa-plus" title="Adicionar a mesa"></button>
                                    <!-- END EXTRA_BLOCK_BUTTON_ADD -->
                                </div>

                                <div class="flex flex-ai-center">
                                    {button_view}
                                    <!-- BEGIN EXTRA_BLOCK_BUTTON_VIEW -->

                                    <div class="menu-inter">
                                        <button class="menu-inter-button fa-solid fa-ellipsis-vertical button-float button-blue"></button>

                                        <ul>
                                            <li class="waitertable_bt_view flex flex-ai-center gap-10 color-blue">
                                                <i class="icon fa-solid fa-receipt"></i>
                                                <span>Ver mesa</span>
                                            </li>

                                            <li class="waitertable_bt_tabletransf flex flex-ai-center gap-10 color-blue" data-id_mesa="{id_mesa}">
                                                <i class="icon fa-solid fa-left-right"></i>
                                                <span>Transferir mesa</span>
                                            </li>
                                        </ul>
                                    </div>
                                    <!-- END EXTRA_BLOCK_BUTTON_VIEW -->
                                </div>
                            </div>
                        </div>
                        <!-- END EXTRA_BLOCK_TABLE_BUSY -->

                        <!-- BEGIN EXTRA_BLOCK_TABLE_FREE -->
                        <div class="waitertable_table flex-4-col ticket-border gap-10 font-size-12 border-green" data-id_mesa="{id_mesa}" data-mesa="{mesa}">

                            <div class="flex flex-dc flex-jc-sb gap-10 flex-1">
                                <div>{mesa}</div>
                                <div class="grid">
                                    <span class="one-line font-size-09">Mesa livre</span>
                                </div>
                            </div>
                            <div class="flex flex-dc gap-10">
                                <div class="flex flex-ai-center">
                                    {button_select}
                                    <!-- BEGIN EXTRA_BLOCK_BUTTON_SELECT -->
                                    <button type="button" class="waitertable_bt_select button-float button-icon button-blue fa-solid fa-utensils" title="Atender mesa"></button>
                                    <!-- END EXTRA_BLOCK_BUTTON_SELECT -->
                                    <!-- BEGIN EXTRA_BLOCK_BUTTON_ADD -->
                                    <button type="button" class="selfservice_table_bt_select button-float button-icon button-blue fa-solid fa-plus" title="Adicionar a mesa"></button>
                                    <!-- END EXTRA_BLOCK_BUTTON_ADD -->
                                </div>
                            </div>
                        </div>
                        <!-- END EXTRA_BLOCK_TABLE_FREE -->
                    </div>
                </div>
            </div>
        <!-- </form> -->
    </div>
</div>
<!-- END BLOCK_PAGE -->