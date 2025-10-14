<!-- BEGIN BLOCK_PAGE -->
<div class="w-waiterorder-container box-container flex flex-dc gap-10">

    <div class="w_waitertable_header box-header">{mesa_desc} - Revisão de Pedido</div>

    <div class="waiterorder_entidade_container flex flex-dc fill font-size-12 gap-10">

        <div class="w_waiterorder_entidade">

            {extra_block_waiterorder_entity}

            <!-- BEGIN EXTRA_BLOCK_WAITERORDER_ENTITY_NONE -->
            <label class="caption">Cliente</label>
            <div class="addon flex gap-10">
                <span class="field flex-1">Varejo</span>
                <button type="button" class="waiterorder_bt_entity button-icon button-blue" data-window="{window}">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </div>
            <!-- END EXTRA_BLOCK_WAITERORDER_ENTITY_NONE -->

            <!-- BEGIN EXTRA_BLOCK_WAITERORDER_ENTITY -->
            <label class="caption">Cliente</label>
            <div class="addon">
                {extra_block_entity_button_status}
                <!-- {block_entity_nome} -->
                <span class="flex-1 entity_{id_entidade}_nome">{nome}</span>
                <button type="button" class="waiterorder_bt_entity button-icon button-blue" data-window="{window}">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </div>
            <!-- END EXTRA_BLOCK_WAITERORDER_ENTITY -->

            <!-- BEGIN EXTRA_BLOCK_WAITERORDER_ENTITY_SEARCH -->
            <label class="caption">Cliente</label>
            <form method="post" id="frm_waiterorder_entity" class="fill flex gap-10" data-window="{window}">
                <div class='autocomplete-dropdown flex-1'>
                    <input
                        type="text"
                        class="uppercase entity_search smart_search smart-search fill"
                        autofocus
                        placeholder="Código / Nome / Telefone"
                        data-source="popup"
                        required
                        autocomplete="off"
                        maxlength="100">
                    {block_entity_autocomplete_search}
                </div>

                <div class="menu-inter">
                    <button type="button" class="menu-inter-button fa-solid fa-ellipsis-vertical button-blue"></button>

                    <ul>

                        <li class="entity_bt_new flex flex-ai-center gap-10 color-blue" data-window="{window}">
                            <i class="icon fa-solid fa-person-circle-plus"></i>
                            <span>Cadastrar Cliente</span>
                        </li>

                        <li class="waiterorder_bt_entity_del flex flex-ai-center gap-10 color-blue">
                            <i class="icon fa-solid fa-user-slash"></i>
                            <!-- <i class="icon fa-solid fa-trash-can"></i> -->
                            <span>Venda Varejo</span>
                        </li>

                        <li class="waiterorder_bt_entity_cancel flex flex-ai-center gap-10 color-blue">
                            <i class="icon fa-solid fa-rotate-left"></i>
                            <span>Cancelar Seleção</span>
                        </li>
                    </ul>
                </div>
            </form>
            <!-- END EXTRA_BLOCK_WAITERORDER_ENTITY_SEARCH -->
        </div>
    </div>


    <div class="flex flex-dc">

        <div class="section-header">
            Itens
        </div>

        <div class="table tbody flex flex-dc">
            {extra_block_product}
        </div>
    </div>

    <div class="flex flex-dc gap-10">

        <div class="section-header">
            Total
        </div>

        <div class="addon font-size-15">
            <span>R$</span>
            <span class="waiterorder-total">0,00</span>
        </div>
    </div>

</div>

<div class="footer-popup flex-jc-fe">
	<div class="popup-menu">
		<div class="flex gap-10">
            <div class="flex flex-ai-center gap-10">
                <button type="button" class="waitertable_bt_order button-float button-green flex gap-5" title="Confirmar pedido">
                    <i class="icon fa-solid fa-check"></i>
                    <span>Confirmar pedido</span>
                </button>
			</div>
            <button class="popup-main-button fa-solid fa-ellipsis-vertical button-pink"></button>
        </div>

		<ul>
			<li class="waitertable_bt_sector flex flex-ai-center gap-10 color-blue" data-id_mesa="{id_mesa}" title="Selecionar setor">
                <i class="icon fa-solid fa-cubes"></i>
				<span>Lista de Produtos</span>
			</li>

            <li class="waitertable_bt_table flex flex-ai-center gap-10 color-blue" title="Selecionar mesa">
                <i class="icon fa-solid fa-chair"></i>
				<span>Lista de Mesas</span>
			</li>
		</ul>
	</div>
</div>
<!-- END BLOCK_PAGE -->