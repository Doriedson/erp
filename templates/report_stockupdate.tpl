<!-- BEGIN BLOCK_PAGE -->
<div class="box-container flex flex-dc gap-10">

    <div class="box-header gap-10">
        <i class="icon fa-solid fa-file-invoice"></i>
        <span>Relatório / Ajuste de Estoque</span>
    </div>

    <div class="flex-responsive gap-10 flex-jc-sb">

        <form method="post" id="frm_report_stockupdate" class="flex-responsive">

            <div class="flex-responsive gap-10">

                <div class="">
                    <label class="caption flex flex-ai-center gap-5">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        Estoque
                    </label>
                    <div class="addon">
                        <select id="stock_type" class="fill" autofocus="">
                            <option value="0">Primário</option>
                            <option value="1">Secundário</option>
                        </select>
                    </div>
                </div>

                <div class="flex gap-10">
                    <div class="flex-1">
                        <label class="caption">Data</label>
                        <div class="addon">
                            <input type='date' id="dataini" class="fill" value='{data}' title="Data ou data inicial." required>
                        </div>
                    </div>

                    <div class="flex-1">
                        <label class="caption">até</label>
                        <div class="addon">
                            <span class="flex">
                                <input type="checkbox" id='intervalo' title="Ativa busca de data de vencimento por intervalo.">
                            </span>

                            <input type='date' id="datafim" class="fill" min='{data}' value='{data}' title="Data final." required disabled>
                        </div>
                    </div>
                </div>

                <div class="flex gap-10">
                    <div class="flex-1">
                        <label class="caption">Produto [Código ou Descrição]</label>
                        <div class="addon">
                            <span class="flex">
                                <input type="checkbox" id='produto' title="Adiciona produto na busca">
                            </span>

                            <div class="fill">
                                <div class="autocomplete-dropdown">
                                    <input
                                        type="text"
                                        id="product_search"
                                        class="uppercase product_search smart_search smart-search fill flex-4"
                                        data-source="popup"
                                        maxlength="40"
                                        required
                                        placeholder=""
                                        autocomplete="off"
                                        data-focus_next="#bt_submit"
                                        disabled
                                        autofocus>

                                    {block_product_autocomplete_search}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-ai-fe flex-jc-center">
                        <button id="bt_submit" type="submit" class="button-blue" title="Procurar vendas totalizadas">Procurar</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="report_stockupdate_container box-container flex flex-dc gap-10">

	<div class="report_stockupdate_notfound box container fill">

		<div class="font-size-12 textcenter" style="padding: 80px 10px;">
			Use o campo procura para localizar produtos com ajuste de estoque.
		</div>
	</div>

    <div class="table_stockupdate flex flex-dc gap-10">

        <!-- BEGIN EXTRA_BLOCK_STOCKUPDATE_HEADER -->
        <div class="box-header">
            <div class="flex-responsive flex-ai-center flex-jc-sb gap-10 flex-1">
                <div>
                    <span>{header}</span>
                </div>

                <div class="textright">
                    <label class="caption">Total Geral</label>
                    <div>
                        <span class="{color}">R$ {total_formatted}</span>
                    </div>
                </div>
            </div>
        </div>
        <!-- END EXTRA_BLOCK_STOCKUPDATE_HEADER -->

        <!-- BEGIN EXTRA_BLOCK_STOCKUPDATE_CONTENT -->
        <div class="window">

            <div class="flex gap-10">
                <div class="section-header flex-1">
                    <div class="flex-1 flex-responsive gap-10">
                        <div class="flex flex-ai-center flex-12">
                            <span>{produto}</span>
                        </div>

                        <div class="flex gap-10 flex-6 textright">

                            {extra_block_stockupdate_total}

                            <!-- BEGIN EXTRA_BLOCK_STOCKUPDATE_TOTALPLUS -->
                            <div class="flex-1">
                                <label class="caption">Quantidade</label>
                                <div>
                                    <span class="field color-green">{saldo_formatted} <span class="font-size-075">{produtounidade}</span></span>
                                </div>
                            </div>

                            <div class="flex-1">
                                <label class="caption">Total</label>
                                <div>
                                    <span class="field color-green">R$ {total_formatted}</span>
                                </div>
                            </div>
                            <!-- END EXTRA_BLOCK_STOCKUPDATE_TOTALPLUS -->

                            <!-- BEGIN EXTRA_BLOCK_STOCKUPDATE_TOTALMINUS -->
                            <div class="flex-1">
                                <label class="caption">Quantidade</label>
                                <div>
                                    <span class="field color-red">{saldo_formatted} <span class="font-size-075">{produtounidade}</span></span>
                                </div>
                            </div>

                            <div class="flex-1">
                                <label class="caption">Total</label>
                                <div>
                                    <span class="field color-red">R$ {total_formatted}</span>
                                </div>
                            </div>
                            <!-- END EXTRA_BLOCK_STOCKUPDATE_TOTALMINUS -->

                            <!-- BEGIN EXTRA_BLOCK_STOCKUPDATE_TOTALZERO -->
                            <div class="flex-1">
                                <label class="caption">Quantidade</label>
                                <div>
                                    <span class="field color-blue">{saldo_formatted} <span class="font-size-075">{produtounidade}</span></span>
                                </div>
                            </div>

                            <div class="flex-1">
                                <label class="caption">Total</label>
                                <div>
                                    <span class="field color-blue">R$ {total_formatted}</span>
                                </div>
                            </div>
                            <!-- END EXTRA_BLOCK_STOCKUPDATE_TOTALZERO -->
                        </div>
                    </div>
                </div>

                <div class="flex flex-ai-fe">
                    <button class="button-icon button-blue fa-solid bt_expand fa-chevron-down"></button>
                </div>
            </div>

            <div class="window flex flex-dc gap-10">

                <div class="flex flex-dc table tbody expandable" style="display: none;">
                    {extra_block_stockupdate_tr}
                    <!-- BEGIN EXTRA_BLOCK_STOCKUPDATE_TR_ADD -->
                    <div class="tr flex-1 flex-responsive gap-10">
                        <div class="flex-11">
                            <label class="caption">{data_formatted}</label>
                            <div>
                                <span>{colaborador}</span><br>
                                <span>{obs}</span>
                            </div>
                        </div>

                        <div class="flex flex-jc-sb gap-10 flex-7">
                            <div class="flex flex-ai-fe flex-1 flex-jc-right">
                                <div class="textright">
                                    <label class="caption">Quantidade</label>
                                    <div class="">
                                        <span class="field color-green">{qtd_formatted} <span class="font-size-075">{produtounidade}</span></span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-ai-fe flex-1 flex-jc-right">
                                <div class="textright">
                                    <label class="caption">Custo</label>
                                    <div class="">
                                        <span class="field">R$ {custoun_formatted} <span class="font-size-075">/{produtounidade}</span></span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-ai-fe flex-1 flex-jc-right">
                                <div class="textright">
                                    <label class="caption">Subtotal</label>
                                    <div class="">
                                        <span class="field color-green">R$ {subtotal_formatted}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END EXTRA_BLOCK_STOCKUPDATE_TR_ADD -->
                    <!-- BEGIN EXTRA_BLOCK_STOCKUPDATE_TR_DEL -->
                    <div class="tr flex-1 flex-responsive gap-10">
                        <div class="flex-11">
                            <label class="caption">{data_formatted}</label>
                            <div>
                                <span>{colaborador}</span><br>
                                <span>{obs}</span>
                            </div>
                        </div>

                        <div class="flex flex-jc-sb gap-10 flex-7">
                            <div class="flex flex-ai-fe flex-1 flex-jc-right">
                                <div class="textright">
                                    <label class="caption">Quantidade</label>
                                    <div class="">
                                        <span class="field color-red">- {qtd_formatted} <span class="font-size-075">{produtounidade}</span></span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-ai-fe flex-1 flex-jc-right">
                                <div class="textright">
                                    <label class="caption">Custo</label>
                                    <div class="">
                                        <span class="field">R$ {custoun_formatted} <span class="font-size-075">/{produtounidade}</span></span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-ai-fe flex-1 flex-jc-right">
                                <div class="textright">
                                    <label class="caption">Subtotal</label>
                                    <div class="">
                                        <span class="field color-red">- R$ {subtotal_formatted}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END EXTRA_BLOCK_STOCKUPDATE_TR_DEL -->
                </div>
            </div>
        </div>
        <!-- END EXTRA_BLOCK_STOCKUPDATE_CONTENT -->
    </div>
</div>
<!-- END BLOCK_PAGE -->