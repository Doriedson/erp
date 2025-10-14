<!-- BEGIN BLOCK_PAGE -->

<!-- BEGIN EXTRA_BLOCK_FREIGHTCEP_NEW -->
<form method="post" id="frm_freightcep">

	<div class="flex flex-dc gap-10">


        <div class="">
            <label class="caption">Descrição</label>
            <div class="addon">
                <input
                type='text'
                id="field_freightcep_descricao"
                class="fill"
                maxlength='255'
                placeholder=''
                title="Descrição da faixa de CEP"
                required
                autofocus>
            </div>
        </div>

        <div class="">

            <label class="caption">Faixa de CEP (somente números)</label>
            <div class="addon">
                <input
                    type="text"
                    id="field_freightcep_cep_de"
                    class="fill"
                    pattern="\d{8}"
                    maxlength="8"
                    size="8"
                    placeholder="00000-000"
                    value="{cep_de}"
                    required>

                <span>até</span>

                <input
                    type="text"
                    id="field_freightcep_cep_ate"
                    class="fill"
                    pattern="\d{8}"
                    maxlength="8"
                    size="8"
                    placeholder="00000-000"
                    value="{cep_ate}"
                    required>

                <button type="button" class="freightvalue_bt_cepsearch button-blue" title="Buscar CEP para descrição">
                    <i class="icon fa-solid fa-search"></i>
                </button>
            </div>
		</div>

		<div class="flex gap-10">
			<div class="fill">
				<label class="caption">Valor</label>
				<div class="addon">
                    <!-- BEGIN BLOCK_FREIGHTVALUE_SELECT -->
					<select id='field_freightcep_id_fretevalor' class="id_fretevalor fill" title="Valor do frete" required>
                        {extra_block_freightvalue_option}
                        <!-- BEGIN EXTRA_BLOCK_FREIGHTVALUE_OPTION -->
                        <option value="{id_fretevalor}" {selected}>R$ {valor_formatted} [{descricao}]</option>
                        <!-- END EXTRA_BLOCK_FREIGHTVALUE_OPTION -->
                    </select>
                    <!-- END BLOCK_FREIGHTVALUE_SELECT -->
				</div>
			</div>

            <div class="flex flex-ai-fe">
                <button type="button" class="freightvalue_bt_manager button-blue" title="Gerenciar valores">
                    <i class="icon fa-solid fa-pencil"></i>
                </button>
            </div>
		</div>

        <div class="flex flex-ai-fe fill">
            <div class="fill margin-t10">
                <button type="submit" class="button-blue fill" title="Adicionar frete para CEP">Adicionar</button>
            </div>
        </div>
	</div>
</form>
<!-- END EXTRA_BLOCK_FREIGHTCEP_NEW -->

<!-- BEGIN EXTRA_BLOCK_FREIGHTVALUE_POPUP -->
<div class="flex flex-dc gap-10">

    <div class="box-header">Taxas</div>

    <div class="freightvalue_notfound window {notfound}" style="padding: 40px 10px;">
        Nenhuma taxa de entrega cadastrada.
    </div>

	<div class="freightvalue_container flex flex-dc table tbody">

		{extra_block_freightvalue}
		<!-- BEGIN EXTRA_BLOCK_FREIGHTVALUE -->
		<div class="freightvalue_tr window tr flex flex-dc gap-10">

			<div class="flex gap-10">

				<div class="flex-5">

                    <label class="caption">Descrição</label>

                    <div class="addon">

						<!-- BEGIN BLOCK_FREIGHTVALUE_DESCRICAO -->
						<button class="freightvalue_bt_descricao button-field textleft fill" data-id_fretevalor="{id_fretevalor}" title="Alterar descrição da faixa de CEP">
							{descricao}
						</button>
						<!-- END BLOCK_FREIGHTVALUE_DESCRICAO -->

						<!-- BEGIN EXTRA_BLOCK_FREIGHTVALUE_DESCRICAO_FORM -->
						<form method="post" id="frm_freightvalue_descricao" class="fill" data-id_fretevalor='{id_fretevalor}'>
							<input
								type='text'
								id='field_freightvalue_descricao'
								class="fill"
								placeholder=''
								value='{descricao}'
								maxlength='255'
								required
								autofocus>
						</form>
						<!-- END EXTRA_BLOCK_FREIGHTVALUE_DESCRICAO_FORM -->
					</div>
				</div>

                <div class="flex-2">

                    <label class="caption">Valor</label>

					<div class="addon">

						<!-- BEGIN BLOCK_FREIGHTVALUE_VALOR -->
						<button class="freightvalue_bt_valor button-field textleft one-line fill" data-id_fretevalor="{id_fretevalor}" title="Alterar descrição da faixa de CEP">
							R$ {valor_formatted}
						</button>
						<!-- END BLOCK_FREIGHTVALUE_VALOR -->

						<!-- BEGIN EXTRA_BLOCK_FREIGHTVALUE_VALOR_FORM -->
						<form method="post" id="frm_freightvalue_valor" class="flex flex-ai-center gap-10 fill" data-id_fretevalor='{id_fretevalor}'>

                            <div class="addon">
                                <span>R$</span>
                                <input
                                    type="number"
                                    id="field_freightvalue_valor"
                                    class="fill"
                                    step='0.01'
                                    min='0'
                                    max='999999.99'
                                    required
                                    placeholder='0,00'
                                    value="{valor}"
                                    autofocus>
                            </div>
						</form>
						<!-- END EXTRA_BLOCK_FREIGHTVALUE_VALOR_FORM -->
					</div>
				</div>

				<div class="flex flex-ai-fe">
					<button class='freightvalue_bt_del button-icon button-red fa-solid fa-trash-can' data-id_fretevalor='{id_fretevalor}' title="Remover Valor"></button>
				</div>
			</div>
		</div>
		<!-- END EXTRA_BLOCK_FREIGHTVALUE -->
	</div>

	<div class="">
        <form method="post" id="frm_freightvalue" class="flex flex-dc gap-10">

        <div>
            <label class="caption">Descrição</label>
            <div class="addon">
                <input
                    type='text'
                    id='descricao'
                    class="fill"
                    placeholder=''
                    value=''
                    maxlength='255'
                    required
                    autofocus>
            </div>
        </div>

        <div class="flex gap-10">
            <div class="flex-1">
                <label class="caption">Valor</label>
                <div class="addon">
                    <span>R$</span>
                    <input
                        type="number"
                        id="valor"
                        class="fill"
                        step='0.01'
                        min='0'
                        max='999999.99'
                        required
                        placeholder='0,00'
                        autofocus>
                </div>
            </div>

            <div class="flex flex-ai-fe">
                <button type="submit" class="button-blue" title="Cadastrar nova faixa de CEP">Cadastrar</button>
            </div>
        </div>
    </form>
	</div>
</div>
<!-- END EXTRA_BLOCK_FREIGHTVALUE_POPUP -->

<!-- BEGIN EXTRA_BLOCK_FREIGHTCEP_NORULES -->
<div class="flex flex-dc gap-10">

    <div class="box-header">
        Lista de CEPs
    </div>

    {extra_block_freightcep_norules_tr}

    <!-- BEGIN EXTRA_BLOCK_FREIGHTCEP_NORULES_TR -->
    <div class="freightcep_norules_container flex gap-10">
        <div class="addon">
            <span class="flex-1">{cep_formatted}</span>
        </div>

        <!-- <div class="flex flex-jc-fe flex-ai-fe">
            <div class="menu-inter">
                <ul style="display: none;">
                    <li class="freightcep_bt_cep_norule flex flex-ai-center gap-10 color-blue" data-cep="{cep}" title="Cadastrar CEP">
                        <i class="icon fa-solid fa-square-plus"></i>
                        <span>Adicionar CEP</span>
                    </li>

                    <li class="freightcep_bt_cep_address flex flex-ai-center gap-10 color-blue" data-cep="{cep}" title="Visualizar endereços cadastrados">
                        <i class="icon fa-solid fa-file-lines"></i>
                        <span>Ver Endereços</span>
                    </li>
                </ul>

                <button class="menu-inter-button fa-solid button-blue fa-ellipsis-vertical"></button>
            </div>
        </div> -->

        <div class="flex flex-ai-fe gap-10">
            <button type="button" class="freightcep_bt_cep_address button-blue" data-cep="{cep}" title="Visualizar endereços cadastrados">
                <i class="icon fa-solid fa-file-lines"></i>
            </button>

            <button type="button" class="freightcep_bt_cep_norule button-blue" data-cep="{cep}" title="Cadastrar CEP">
                <i class="icon fa-solid fa-square-plus"></i>
            </button>
        </div>

    </div>
    <!-- END EXTRA_BLOCK_FREIGHTCEP_NORULES_TR -->
</div>
<!-- END EXTRA_BLOCK_FREIGHTCEP_NORULES -->

<!-- BEGIN EXTRA_BLOCK_FREIGHTCEP_ADDRESS_SHEET -->
<div class="flex flex-dc gap-10">
    <div class="section-header">
        Endereço
    </div>

    <div class="table tbody flex flex-dc gap-10">

        {extra_block_address}
    </div>
</div>
<!-- END EXTRA_BLOCK_FREIGHTCEP_ADDRESS_SHEET -->

<div class="flex flex-dc gap-10">

    <div class="flex flex-dc gap-10">
        <div class="">
            <label class="caption">Pedido mínimo de Delivery</label>

            <div class="flex gap-10">

                <label class="toggle" title="Ativar / Desativar pedido mínimo de delivery">

                    <!-- <label class="caption flex flex-jc-center"> -->
                        <!-- <div class="true">Ativo</div>
                        <div class="false">Inativo</div> -->
                    <!-- </label> -->
                    <div class="addon-transp">
                        <input {deliveryminimo} class="freight_bt_deliveryminimo hidden" type="checkbox">
                        <span></span>
                    </div>
                </label>

                <!-- BEGIN BLOCK_DELIVERYMINIMO_VALOR -->
                <div class="addon deliveryminimo_container">
                    <button class="shipment_bt_deliveryminimo_valor button-field textleft fill" {deliveryminimo_disabled} title="Alterar valor mínimo para para delivery">
                        R$ {deliveryminimo_valor_formatted}
                    </button>
                </div>
                <!-- END BLOCK_DELIVERYMINIMO_VALOR -->
            </div>

            <!-- BEGIN EXTRA_BLOCK_DELIVERYMINIMO_VALOR_FORM -->
            <form method="post" id="frm_shipment_deliveryminimo_valor" class="fill">
                <div class="addon">
                    <span>R$</span>
                    <input
                        type="number"
                        id="frm_shipment_deliveryminimo_valor_field"
                        class="fill"
                        step='0.01'
                        min='0'
                        max='999999.99'
                        required
                        value='{deliveryminimo_valor}'
                        autofocus>
                </div>
            </form>
            <!-- END EXTRA_BLOCK_DELIVERYMINIMO_VALOR_FORM -->
        </div>

        <div class="">
            <label class="caption">Oferecer frete grátis para pedidos acima de</label>

            <div class="flex gap-10">

                <label class="toggle" title="Ativar / Desativar frete grátis">

                    <!-- <label class="caption flex flex-jc-center"> -->
                        <!-- <div class="true">Ativo</div>
                        <div class="false">Inativo</div> -->
                    <!-- </label> -->
                    <div class="addon-transp">
                        <input {fretegratis} class="freight_bt_fretegratis hidden" type="checkbox">
                        <span></span>
                    </div>
                </label>

                <!-- BEGIN BLOCK_FRETEGRATIS_VALOR -->
                <div class="addon fretegratis_container">
                    <button class="shipment_bt_fretegratis_valor button-field textleft fill" {fretegratis_disabled} title="Alterar valor mínimo para frete grátis">
                        R$ {fretegratis_valor_formatted}
                    </button>
                </div>
                <!-- END BLOCK_FRETEGRATIS_VALOR -->
            </div>

            <!-- BEGIN EXTRA_BLOCK_FRETEGRATIS_VALOR_FORM -->
            <form method="post" id="frm_shipment_fretegratis_valor" class="fill">
                <div class="addon">
                    <span>R$</span>
                    <input
                        type="number"
                        id="frm_shipment_fretegratis_valor_field"
                        class="fill"
                        step='0.01'
                        min='0'
                        max='999999.99'
                        required
                        value='{fretegratis_valor}'
                        autofocus>
                </div>
            </form>
            <!-- END EXTRA_BLOCK_FRETEGRATIS_VALOR_FORM -->
        </div>
    </div>

    <div class="flex-responsive gap-10">
        <div class="section-header flex-1">
            Áreas de Entrega
        </div>

        <div class="flex flex-ai-fe flex-jc-fe gap-10">
            <button type="button" class="freightcep_bt_list button-blue" title="Exibir lista de CEPs sem regra">CEPs sem regra</button>
            <button type="button" class="freightcep_bt_new button-blue" title="Cadastrar área de entrega">Adicionar Área</button>
        </div>
    </div>

    <div class="freightcep_none flex gap-10 {freightcep_none}" style="padding: 40px 10px;">
        Nenhuma área de entrega cadastrada.
    </div>

    <div class="freightcep_container table tbody">

        {extra_block_freightcep}

        <!-- BEGIN EXTRA_BLOCK_FREIGHTCEP -->
        <div class="freightcep_tr flex-responsive tr gap-10">

            <div class="flex flex-ai-fe gap-10 flex-8">

                <label class="toggle" title="Ativar / Desativar taxa de frete para faixa de CEP">

                    <label class="caption flex flex-jc-center">
                        <div class="true">Ativo</div>
                        <div class="false">Inativo</div>
                    </label>

                    <div class="addon-transp">
                        <input {ativo} class="freightcep_bt_ativo hidden" type="checkbox" data-id_fretecep="{id_fretecep}">
                        <span></span>
                    </div>
                </label>

                <div class="flex-1">
                    <label class="caption">Descrição</label>
                    <!-- BEGIN BLOCK_FREIGHTCEP_DESCRICAO -->
                    <div class="addon">
                        <button type="button" class="freightcep_bt_descricao button-field textleft" data-id_fretecep="{id_fretecep}" title="Alterar descrição da faixa de CEP">{descricao}</button>
                    </div>
                    <!-- END BLOCK_FREIGHTCEP_DESCRICAO -->

                    <!-- BEGIN EXTRA_BLOCK_FREIGHTCEP_DESCRICAO_FORM -->
                    <form method="post" id="frm_freightcep_descricao" class="flex" data-id_fretecep="{id_fretecep}">
                        <div class="addon">
                            <input
                                type='text'
                                id="frm_freightcep_descricao_field"
                                class="fill"
                                maxlength='255'
                                placeholder=''
                                value="{descricao}"
                                title="Descrição da faixa de CEP"
                                required
                                autofocus>
                        </div>
                    </form>
                    <!-- END EXTRA_BLOCK_FREIGHTCEP_DESCRICAO_FORM -->
                </div>
            </div>

            <div class="flex-6 flex gap-10">
                <div class="flex-3">
                    <label class="caption">Faixa de CEP</label>
                    <div class="addon">
                        <!-- BEGIN BLOCK_FREIGHTCEP_CEP_DE -->
                        <button type="button" class="freightcep_bt_cep_de button-field" data-id_fretecep="{id_fretecep}" title="Alterar CEP da faixa de CEP">{cep_de_formatted}</button>
                        <!-- END BLOCK_FREIGHTCEP_CEP_DE -->
                        <!-- BEGIN EXTRA_BLOCK_FREIGHTCEP_CEP_DE_FORM -->
                        <form method="post" id="frm_freightcep_cep_de" class="flex" data-id_fretecep="{id_fretecep}">
                            <div class="addon">
                                <input
                                    type="text"
                                    id="frm_freightcep_cep_de_field"
                                    class="fill"
                                    pattern="\d{8}"
                                    maxlength="8"
                                    size="8"
                                    placeholder="00000-000"
                                    value="{cep_de}"
                                    required
                                    autofocus>
                            </div>
                        </form>
                        <!-- END EXTRA_BLOCK_FREIGHTCEP_CEP_DE_FORM -->
                        <span>-</span>
                        <!-- BEGIN BLOCK_FREIGHTCEP_CEP_ATE -->
                        <button type="button" class="freightcep_bt_cep_ate button-field" data-id_fretecep="{id_fretecep}" title="Alterar CEP da faixa de CEP">{cep_ate_formatted}</button>
                        <!-- END BLOCK_FREIGHTCEP_CEP_ATE -->
                        <!-- BEGIN EXTRA_BLOCK_FREIGHTCEP_CEP_ATE_FORM -->
                        <form method="post" id="frm_freightcep_cep_ate" class="flex" data-id_fretecep="{id_fretecep}">
                            <div class="addon">
                                <input
                                    type="text"
                                    id="frm_freightcep_cep_ate_field"
                                    class="fill"
                                    pattern="\d{8}"
                                    maxlength="8"
                                    size="8"
                                    placeholder="00000-000"
                                    value="{cep_ate}"
                                    required
                                    autofocus>
                            </div>
                        </form>
                        <!-- END EXTRA_BLOCK_FREIGHTCEP_CEP_ATE_FORM -->
                    </div>
                </div>
            </div>

            <div class="flex-4 flex gap-10">

                <div class="flex-3">
                    <label class="caption">Taxa de entrega</label>
                    <div class="addon">
                        <!-- BEGIN BLOCK_FREIGHTCEP_VALOR -->
                        <button type="button" class="freightcep_bt_valor button-field" data-id_fretecep="{id_fretecep}" title="Alterar taxa de entrega da faixa de CEP">R$ <span class="freightcep_valor_{id_fretevalor}">{valor_formatted}</span></button>
                        <!-- END BLOCK_FREIGHTCEP_VALOR -->

                        <!-- BEGIN EXTRA_BLOCK_FREIGHTCEP_VALOR_FORM -->
                        <form method="post" id="frm_freightcep_valor" data-id_fretecep="{id_fretecep}">
                            <div class="addon">
                                <select id='frm_freightcep_valor_field' class="id_fretevalor fill" title="Valor do frete" required autofocus>
                                    {extra_block_freightvalue_option}
                                </select>
                            </div>
                        </form>
                        <!-- END EXTRA_BLOCK_FREIGHTCEP_VALOR_FORM -->
                    </div>
                </div>

                <div class="flex flex-ai-fe gap-10">

                    <button class="freightcep_bt_del button-icon button-red fa-solid fa-trash-can" data-id_fretecep="{id_fretecep}" title="Remover faixa de CEP"></button>
                </div>
            </div>
        </div>
        <!-- END EXTRA_BLOCK_FREIGHTCEP -->
    </div>

    <div class="flex flex-jc-fe gap-10">
        <button type="button" class="freightcep_bt_list button-blue" title="Exibir lista de CEPs sem regra">CEPs sem regra</button>
        <button type="button" class="freightcep_bt_new button-blue" title="Cadastrar área de entrega">Adicionar Área</button>
    </div>
</div>
<!-- END BLOCK_PAGE -->