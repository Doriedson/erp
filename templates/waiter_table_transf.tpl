<!-- BEGIN BLOCK_PAGE -->
<div class="w_waitertabletransf_container flex flex-dc gap-10" data-id_mesa="{id_mesa}" data-versao="{versao}">

    <div class="box-container flex flex-dc gap-10">

        <div class="w_waitertable_header box-header">Transferência de Mesa</div>

        <div>
            <label class="caption">Mesa selecionada</label>
            <div class="addon">
                <span>{mesa}</span>
            </div>
        </div>

        <div class="section-header">
            Transferir para Mesa
        </div>

        <div class="w-waitertable-search flex gap-10">
            <div class="flex-1">
                <label class="caption">Buscar mesa</label>

                <div class="autocomplete-dropdown flex flex-dc gap-10">
                    <input
                        type="text"
                        id="table_search"
                        class="uppercase table_search smart_search smart-search fill"
                        data-screen="waiter_tabletransf"
                        maxlength="40"
                        required
                        placeholder=""
                        autocomplete="off"
                        autofocus>

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
</div>

<div class="footer-popup flex-jc-fe">
	<div class="popup-menu">
        <div class="flex gap-10">
            <button class="popup-main-button fa-solid fa-ellipsis-vertical button-pink"></button>
        </div>

		<ul>
            <li class="waitertable_bt_selfservice flex flex-ai-center gap-10 color-blue" title="Abrir Self-Service">
                <i class="icon fa-solid fa-bell-concierge"></i>
				<span>Self-Service</span>
			</li>

            <li class="waitertable_bt_table flex flex-ai-center gap-10 color-blue" title="Selecionar mesa">
                <i class="icon fa-solid fa-chair"></i>
				<span>Lista de mesas</span>
			</li>
		</ul>
	</div>
</div>
<!-- END BLOCK_PAGE -->