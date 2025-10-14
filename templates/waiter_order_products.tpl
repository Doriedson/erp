<!-- BEGIN BLOCK_PAGE -->

<div class="w-waiterorder-container w-waiterorder-tableclose box-container flex flex-dc gap-10">

    <div class="w_waitertable_header box-header">{mesa_desc}</div>

    <div class="waiterorder_entidade_container flex flex-dc fill font-size-12 gap-10">

        <div class="w_waiterorder_entidade">
            {extra_block_waiterorder_entity}
        </div>

    </div>

    <div class="flex flex-dc">

        <div class="section-header">
            Itens
        </div>

        <div class="table tbody flex flex-dc">
            {extra_block_product}

            <!-- BEGIN EXTRA_BLOCK_PRODUCT_NONE -->
            <div class="">
                <div class="font-size-12" style="padding: 20px 10px;">
                    Nenhum item na mesa.
                </div>
            </div>
            <!-- END EXTRA_BLOCK_PRODUCT_NONE -->

            <!-- BEGIN EXTRA_BLOCK_PRODUCT -->
            <div class="waiterproduct_produto window tr flex flex-dc fill font-size-12 gap-10" data-id_vendaitem="{id_vendaitem}" data-versao="{versao}">

                <div class="flex gap-10">
                    <div class="flex flex-ai-center flex-1">
                        <span class="padding-h5 color-gray-darkest">{produto}</span>
                    </div>

                    <button class="waiterproduct_bt_reverse button-icon button-red fa-solid fa-trash-can" title="Estornar produto"></button>
                </div>

                <div class="flex gap-10">
                    <div class="flex flex-dc flex-1 gap-10 flex-jc-fe">
                        <span class="fill padding-h5 color-gray font-size-09">{obs}</span>


                        <div class="flex flex-jc-sb">
                            <div class="flex flex-ai-fe gap-10 font-size-09">
                                <span class="padding-h5">{qtd_formatted} <span class="font-size-075"> {produtounidade}</span></span>
                                <span>X</span>
                                <span class="padding-h5">R$ {preco_formatted} <span class="font-size-075">/{produtounidade}</span></span>
                                <span>=</span>
                            </div>

                            <span class="padding-h5 font-size-12">R$ {subtotal_formatted}</span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END EXTRA_BLOCK_PRODUCT -->
        </div>
    </div>

    <div class="flex flex-dc gap-10">

        <!-- <div class="section-header">
            Fechamento
        </div> -->

        <div class="addon font-size-12">
            <span class="flex-1 color-blue">+ Itens</span>
            <span class="flex-1 textright color-blue">R$ {subtotal_formatted}</span>
        </div>

        <div class="addon font-size-12">
            <span class="flex-1 color-blue">- Descontos</span>
            <span class="flex-1 textright color-blue">R$ {desconto_formatted}</span>
        </div>

        <div class="addon font-size-12">
            <span class="flex-1 color-blue">+ Serviços</span>
            <span class="flex-1 textright color-blue">R$ {servico_formatted}</span>
        </div>

        <div class="addon font-size-12">
            <span class="flex-1 color-blue">= Total</span>
            <span class="flex-1 textright color-blue">R$ {total_formatted}</span>
        </div>
    </div>
</div>

<div class="footer-popup flex-jc-fe">
	<div class="popup-menu">
        <div class="flex gap-10">
            <div class="flex flex-ai-center gap-10">
                <button type="button" class="waitertable_bt_tableclose button-float button-green" title="Fecha a mesa para pagamento">
                    <i class="icon fa-solid fa-hand-holding-dollar"></i>
                    <span>Fechar / pagamento</span>
                </button>
            </div>
            <button class="popup-main-button fa-solid fa-ellipsis-vertical button-pink"></button>
        </div>

		<ul>
            <li class="waitertable_bt_sector flex flex-ai-center gap-10 color-blue" data-id_mesa="{id_mesa}" title="Selecionar setor">
                <i class="icon fa-solid fa-cubes"></i>
				<span>Lista de produtos</span>
			</li>

            <li class="waitertable_bt_tabletransf flex flex-ai-center gap-10 color-blue" title="Abrir trasnferência de mesa" data-id_mesa="{id_mesa}">
                <i class="icon fa-solid fa-left-right"></i>
				<span>Transferir mesa</span>
			</li>

            <li class="waitertable_bt_table flex flex-ai-center gap-10 color-blue" title="Selecionar mesa">
                <i class="icon fa-solid fa-chair"></i>
				<span>Lista de mesas</span>
			</li>

            <li class="waitertable_bt_selfservice flex flex-ai-center gap-10 color-blue" title="Abrir Self-Service">
                <i class="icon fa-solid fa-bell-concierge"></i>
				<span>Self-Service</span>
			</li>
		</ul>
	</div>
</div>
<!-- END BLOCK_PAGE -->