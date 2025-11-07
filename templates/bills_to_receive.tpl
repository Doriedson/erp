<!-- BEGIN BLOCK_PAGE -->
<!-- <div class="flex-responsive gap-10">
    <div class="flex-8">
        <p class="setor-1 no-margin">Venda / <span class="setor-2 no-padding">Vendas a prazo</span></p>
    </div>
</div> -->
<div class="box-container flex flex-dc gap-10">

    <div class="box-header gap-10">
        <i class="icon fa-solid fa-cart-shopping"></i>
        <span>Vendas / Vendas a Prazo</span>
    </div>

    <div class="flex-responsive gap-10">

        <div class="flex color-blue flex-jc-fe font-size-15 fill">
            <span>Total R$ <span class="billstoreceive_totalgeral">{total_formatted}</span></span>
        </div>
    </div>
</div>

<div class="w_billstoreceive_container flex flex-dc gap-10">

    {extra_block_billstoreceive}

	<!-- BEGIN EXTRA_BLOCK_BILLSTORECEIVE_NONE -->
	<div class="w_billstoreceive_not_found box-container window">

		<div class="font-size-12 textcenter" style="padding: 80px 10px;">
			Não há vendas a prazo ;-)
		</div>
	</div>
	<!-- END EXTRA_BLOCK_BILLSTORECEIVE_NONE -->

    <!-- BEGIN EXTRA_BLOCK_BILLSTORECEIVE -->
    <div class="w_billstoreceive window box-container flex flex-dc gap-10" data-total="{total}">

        <div class="flex-responsive gap-10">
            <div class="flex-10">
                <label class="caption">Cliente</label>
                <div class="addon">
                    {extra_block_entity_button_status}
                    <span class="entity_{id_entidade}_nome fill">{nome}</span>
                </div>
            </div>

            <div class="flex gap-10 flex-6">
                <div class="flex-3">
                    {block_entity_credit}
                </div>

                <div class="flex-3">
                    <label class="caption color-blue">Total a Prazo</label>
                    <div class="addon color-blue">
                        <span>R$ <span class="billstoreceive_total">{total_formatted}</span></span>
                    </div>
                </div>

                <div class="flex flex-ai-fe flex-jc-fe">
                    <button class="billsreceive_bt_expand button-icon button-blue fa-solid fa-chevron-down" data-id_entidade="{id_entidade}"></button>
                </div>
            </div>
        </div>

        <div class="expandable" style="display: none;">
            <div class="flex flex-dc gap-10">
                <div class="section-header">Pedidos</div>

                <div class="table tbody flex flex-dc">
                    <!-- BEGIN EXTRA_BLOCK_FORWARD_SALE -->
                    <div class="w_saleorder w_saleorder_{id_venda} tr window flex flex-dc gap-10" data-id_venda="{id_venda}" data-versao="{versao}" data-total="{total}">
                        <div class="flex-responsive gap-10">

                            <div class="flex gap-10 flex-6">
                                <div class="flex gap-10 flex-3">
                                    <div class="flex-1">
                                        <label class="caption">{salelegend} # {id_venda}</label>

                                        {extra_block_saleorder_show_ticket}
                                    </div>
                                </div>

                                <div class="flex gap-10 flex-3">
                                    <div class="flex-1">
                                        <label class="caption">Data</label>
                                        <div class="addon flex-jc-center">
                                            <span>{data_formatted}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex gap-10 flex-4">
                                <div class="flex-2">
                                    <label class="caption">Subtotal</label>
                                    <div class="addon">
                                        <span>R$ {subtotal_formatted}</span>
                                    </div>
                                </div>

                                <div class="flex-2">
                                    <label class="caption">Desconto</label>
                                    <div class="addon">
                                        <span>R$ {desconto_formatted}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex gap-10 flex-4">
                                <div class="flex-2">
                                    <label class="caption">Serviço</label>
                                    <div class="addon">
                                        <span>R$ {valor_servico_formatted}</span>
                                    </div>
                                </div>

                                <div class="flex-2">
                                    <label class="caption">Frete</label>
                                    <div class="addon">
                                        <span>R$ {frete_formatted}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex gap-10 flex-4">
                                <div class="flex-3">
                                    <label class="caption color-blue">Total</label>
                                    <div class="addon color-blue">
                                        <span>R$ {total_formatted}</span>
                                    </div>
                                </div>

                                {extra_block_saleorder_menu}
                            </div>
                        </div>

                        <div class="flex flex-dc gap-10 expandable" style="display: none;">
                        </div>
                    </div>
                    <!-- END EXTRA_BLOCK_FORWARD_SALE -->
                </div>
            </div>
        </div>
    </div>
    <!-- END EXTRA_BLOCK_BILLSTORECEIVE -->
</div>
<!-- END BLOCK_PAGE -->