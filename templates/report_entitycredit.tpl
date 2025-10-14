<!-- BEGIN BLOCK_PAGE -->
<div class="box-container flex flex-dc gap-10">

	<div class="box-header gap-10">
		<i class="icon fa-solid fa-file-invoice"></i>
		<span>Relatório / Crédito do Cliente</span>
	</div>

	<div class="flex-responsive gap-10 flex-jc-sb">

		<form method="post" id="frm_report_entitycredit" class="fill">

			<div class="flex-responsive gap-10">

				<div class="flex gap-10">

					<div class="flex-1">
						<label class="caption flex flex-ai-center gap-5">
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

	<div class="report_entitycredit_none window fill">

		<div class="font-size-12 textcenter" style="padding: 80px 10px;">
			Selecione uma data no campo de Procura.
		</div>
	</div>

    <div class="report_entitycredit_container flex flex-dc gap-10">
        <!-- BEGIN EXTRA_BLOCK_ENTITYCREDIT_CONTENT -->
        <div class="window flex flex-dc gap-10">
            <div class="box-header flex flex-jc-sb gap-10">
                {header}
            </div>

            {extra_block_entitycredit}

            <!-- BEGIN EXTRA_BLOCK_ENTITYCREDIT_NOTFOUND -->
            <div class="font-size-12 textcenter" style="padding: 80px 10px;">
                Nenhum relatório encontrado.
            </div>
            <!-- END EXTRA_BLOCK_ENTITYCREDIT_NOTFOUND -->

            <!-- BEGIN EXTRA_BLOCK_ENTITYCREDIT -->
            <div class="window flex flex-dc gap-10">
                <div class="flex gap-10">
                    <div class="section-header flex-1 flex-responsive flex-ai-fe gap-10 padding-b5">

                        <div class="fill flex flex-jc-sb gap-10 flex-10">
                            <span>{nome}</span>
                        </div>

                        <div class="flex fill gap-10 flex-8">
                            <div class="flex-1">
                                <label class="caption color-blue textcenter">Crédito total</label>
                                <div class="color-green textcenter">R$ {totalc_formatted}</div>
                            </div>

                            <div class="flex-1">
                                <label class="caption color-blue textcenter">Débito total</label>
                                <div class="color-red textcenter">R$ {totald_formatted}</div>
                            </div>

                            <div class="flex flex-ai-fe gap-10">
                                <div class="flex ai-center flex-jc-right gap-10">
                                    <div class="menu-inter">
                                        <button class="menu-inter-button fa-solid fa-ellipsis-vertical button-blue"></button>

                                        <ul>
                                            <li class="entity_bt_show flex flex-ai-center gap-10 color-blue" data-id_entidade="{id_entidade}" title="Visualizar dados do cliente">
                                                <i class="icon fa-solid fa-file-lines"></i>
                                                <span>Dados do Cliente</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <button type="button" class="bt_expand button-icon button-blue fa-solid fa-chevron-down"></button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="expandable" style="display: none;">
                    <div class="window_content flex flex-dc gap-10">

                        <!-- <div class="section-header">
                            Itens
                        </div> -->

                        <div class="table tbody flex flex-dc">

                            {extra_block_entitycredit_data}

                            <!-- BEGIN EXTRA_BLOCK_ENTITYCREDIT_CREDIT -->
                            <div class="tr flex gap-10">
                                <div class="flex gap-10 fill">
                                    <div class="flex-3">
                                        <label class="caption">{data_formatted} <i class="fa-solid fa-user padding-h5"></i> {colaborador}</label>
                                        <div>{obs_credit}</div>
                                    </div>
                                </div>

                                <div class="">
                                    <label class="caption">Crédito</label>
                                    <div class="color-green">
                                        <span>R$ {valor_formatted}</span>
                                    </div>
                                </div>
                            </div>
                            <!-- END EXTRA_BLOCK_ENTITYCREDIT_CREDIT -->

                            <!-- BEGIN EXTRA_BLOCK_ENTITYCREDIT_DEBIT -->
                            <div class="tr flex gap-10">
                                <div class="flex gap-10 fill">
                                    <div class="flex-3">
                                        <label class="caption">{data_formatted} <i class="fa-solid fa-user padding-h5"></i> {colaborador}</label>
                                        <div>{obs_credit}</div>
                                    </div>
                                </div>

                                <div class="">
                                    <label class="caption">Débito</label>
                                    <div class="color-red">
                                        <span>R$ {valor_formatted}</span>
                                    </div>
                                </div>
                            </div>
                            <!-- END EXTRA_BLOCK_ENTITYCREDIT_DEBIT -->
                        </div>
                    </div>
                </div>
            </div>
            <!-- END EXTRA_BLOCK_ENTITYCREDIT -->
        </div>
        <!-- END EXTRA_BLOCK_ENTITYCREDIT_CONTENT -->
    </div>
</div>
<!-- END BLOCK_PAGE -->