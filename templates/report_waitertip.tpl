<!-- BEGIN BLOCK_PAGE -->
<div class="box-container flex flex-dc gap-10">

	<div class="box-header gap-10">
		<i class="icon fa-solid fa-file-invoice"></i>
		<span>Relatório / Taxa Garçom</span>
	</div>

	<div class="flex-responsive gap-10 flex-jc-sb">

		<form method="post" id="frm_report_waitertip" class="fill">

			<div class="flex-responsive gap-10">

				<div class="flex gap-10">

					<div class="flex-1">
						<label class="caption flex flex-ai-center gap-5">
							<i class="fa-solid fa-magnifying-glass"></i>
							Data
						</label>
						<div class="addon">
							<input type='date' id="dataini" class="fill" value='{data}' title="Data ou data inicial." required>
						</div>
					</div>
				</div>

				<div class="flex gap-10">
					<div class="fill">
						<label class="caption">até</label>
						<div class="addon">
							<span class="flex">
								<input type="checkbox" id='intervalo' title="Ativa busca de data de vencimento por intervalo.">
							</span>

							<input type='date' id="datafim" class="fill" min='{data}' value='{data}' title="Data final." required disabled>
						</div>
					</div>

					<div class="flex flex-ai-fe">
						<button type="submit" class="button-blue" title="Procurar produtos de entrada de estoque">Procurar</button>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>

<div class="flex box-container flex-dc gap-10">

	<div class="report_waitertip_none window fill">

		<div class="font-size-12 textcenter" style="padding: 80px 10px;">
			Selecione uma data no campo de Procura.
		</div>
	</div>

    <div class="report_waitertip_container flex flex-dc gap-10">
        <!-- BEGIN EXTRA_BLOCK_WAITERTIP_CONTENT -->
        <div class="window flex flex-dc gap-10">
            <div class="box-header flex flex-jc-sb gap-10">
                {header}
                <div>
                    <span>Total R$ {total_formatted}</span>
                </div>
            </div>

            {extra_block_waitertip_waiter}

            <!-- BEGIN EXTRA_BLOCK_WAITERTIP_NOTFOUND -->
            <div class="font-size-12 textcenter" style="padding: 80px 10px;">
                Nenhum relatório encontrado.
            </div>
            <!-- END EXTRA_BLOCK_WAITERTIP_NOTFOUND -->

            <!-- BEGIN EXTRA_BLOCK_WAITERTIP_WAITER -->
            <div class="window flex flex-dc gap-10">
                <div class="flex gap-10">
                    <div class="section-header flex-1 flex flex-jc-sb gap-10">

                        <span>{colaborador}</span>
                        <span>R$ {subtotal_formatted}</span>
                    </div>

                    <div class="flex flex-ai-fe">
                        <button type="button" class="bt_expand button-icon button-blue fa-solid fa-chevron-down"></button>
                    </div>
                    <!-- </div> -->

                </div>

                <div class="expandable" style="display: none;">
                    <div class="window_content flex flex-dc gap-10">

                        <!-- <div class="section-header">
                            Itens
                        </div> -->

                        <div class="table tbody flex flex-dc">

                            {extra_block_waitertip_tip}

                            <!-- BEGIN EXTRA_BLOCK_WAITERTIP_TIP -->
                            <div class="tr flex-responsive gap-10">
                                <div class="flex gap-10 flex-6">
                                    <div class="flex-3">
                                        <label class="caption">{salelegend} # {id_venda}</label>

                                        {extra_block_saleorder_show_ticket}
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

                                <div class="flex-3">
                                    <label class="caption">Valor / Serviço</label>
                                    <div class="addon">
                                        <span>R$ {valor_servico_formatted}</span>
                                    </div>
                                </div>

                                <div class="flex flex-ai-fe">
                                    <div class="menu-inter">
                                        <button class="menu-inter-button fa-solid fa-ellipsis-vertical button-blue"></button>

                                        <ul>
                                            <li class="saleorder_bt_show flex flex-ai-center gap-10 color-blue" data-id_venda="{id_venda}">
                                                <i class="icon fa-solid fa-file-lines"></i>
                                                <span>Visualizar pedido</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="flex-8"></div>
                            </div>
                            <!-- END EXTRA_BLOCK_WAITERTIP_TIP -->
                        </div>
                    </div>
                </div>
            </div>
            <!-- END EXTRA_BLOCK_WAITERTIP_WAITER -->
        </div>
        <!-- END EXTRA_BLOCK_WAITERTIP_CONTENT -->
    </div>
</div>
<!-- END BLOCK_PAGE -->