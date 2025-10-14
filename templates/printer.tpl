<!-- BEGIN BLOCK_PAGE -->

<!-- BEGIN EXTRA_BLOCK_POPUP_PRINTER_IMPRESSORA_NEW -->
<form method="post" id="frm_printer">
    <div class="flex flex-dc gap-10">
        <div class="fill">
            <label class="caption">Descrição</label>
            <div>
                <input
                    type="text"
                    id="descricao"
                    maxlength="50"
                    placeholder=""
                    autocomplete="off"
                    class="smart-search fill"
                    required
                    autofocus>
            </div>
        </div>

        <!-- BEGIN BLOCK_PRINTER_OPTIONS -->
        <div class=" flex-1">
            <div>
                <label class="caption">Impressora local (servidor)</label>

                <div class="flex gap-10">
                    <input type="radio" name="printer_option" {printer_local_checked} value="printer_local">

                    <select id="printer_local_desc" class="fill" {printer_local_disabled} autofocus>
                        {extra_block_printers}
                        <!-- BEGIN EXTRA_BLOCK_PRINTERS -->
                        <option value="{impressora}" {selected}>{impressora}</option>
                        <!-- END EXTRA_BLOCK_PRINTERS -->
                    </select>
                </div>
            </div>
        </div>

        <div class="flex-1">
            <label class="caption">
                Impressora compartilhada (smb://[user:pass@]IP/printer)
            </label>

            <div class="flex gap-10">
                <input type="radio" name="printer_option" {printer_share_checked} value="printer_share">

                <input
                type="text"
                id="printer_share_desc"
                maxlength="255"
                placeholder=""
                value="{printer_share_desc}"
                {printer_share_disabled}
                autocomplete="off"
                class="fill">
            </div>
        </div>

        <div class="flex-1">
            <label class="caption">
                Impressora de rede (IP[:porta])
            </label>

            <div class="flex gap-10">
                <input type="radio" name="printer_option" {printer_ip_checked} value="printer_ip">

                <input
                type="text"
                id="printer_ip_desc"
                maxlength="255"
                placeholder=""
                value="{printer_ip_desc}"
                {printer_ip_disabled}
                autocomplete="off"
                class="fill">
            </div>
        </div>
        <!-- END BLOCK_PRINTER_OPTIONS -->

        <div class="flex flex-ai-fe margin-t10">
            <button type="submit" class="button-blue fill">Cadastrar</button>
        </div>
    </div>
</form>
<!-- END EXTRA_BLOCK_POPUP_PRINTER_IMPRESSORA_NEW -->

<!-- BEGIN EXTRA_BLOCK_POPUP_PRINTER_IMPRESSORA_EDIT -->
<form method="post" id="frm_printer_impressora" class="flex-responsive flex-dc gap-10 fill" data-id_impressora='{id_impressora}'>
    <div class="flex-1">
        <label class="caption">Descrição</label>
        <div class="addon">
            <span class="field">{descricao}</span>
        </div>
    </div>

    {block_printer_options}

    <div class="flex flex-ai-fe padding-t10">
        <button type="submit" class="button-blue fill" title="Salva impressora configurada">Salvar</button>
    </div>
</form>
<!-- END EXTRA_BLOCK_POPUP_PRINTER_IMPRESSORA_EDIT -->

<div class="flex flex-dc table tbody">

    <div class="w-printer-none window {hidden}">

		<div class="font-size-12 textcenter" style="padding: 80px 10px;">
			Nenhuma impressora cadastrada.
		</div>
	</div>

    <div class="w-printer-table flex flex-dc gap-10">
        {extra_block_printer}

        <!-- BEGIN EXTRA_BLOCK_PRINTER -->
        <div class="w_printer w_printer_{id_impressora} window tr flex flex-dc gap-10" data-id_impressora='{id_impressora}'>

            <div class="flex-responsive gap-10">

                <div class="flex-3">
                    <label class="caption">Descrição</label>
                    <div class="addon">
                        <!-- BEGIN BLOCK_DESCRICAO -->
                        <button class="printer_descricao button-field textleft fill" data-id_impressora="{id_impressora}" title="Alterar descrição da impressora">
                            {descricao}
                        </button>
                        <!-- END BLOCK_DESCRICAO -->
                        <!-- BEGIN EXTRA_BLOCK_DESCRICAO_FORM -->
                        <form method="post" id="frm_printer_descricao" class="fill" data-id_impressora='{id_impressora}'>
                            <input
                                type='text'
                                id='descricao'
                                class="fill"
                                placeholder=''
                                value='{descricao}'
                                maxlength='50'
                                required
                                autofocus>
                        </form>
                        <!-- END EXTRA_BLOCK_DESCRICAO_FORM -->
                    </div>
                </div>

                <div class="flex flex-4 gap-10">

                    <div class="group-printer fill">
                        <!-- BEGIN BLOCK_IMPRESSORA -->
                        <div>
                            <label class="caption">Impressora</label>
                            <div class="addon">
                                <button class="printer_impressora button-field textleft fill" data-id_impressora="{id_impressora}" title="Alterar descrição da impressora">
                                    {impressora}
                                </button>
                            </div>
                        </div>
                        <!-- END BLOCK_IMPRESSORA -->
                    </div>
                </div>

                <div class="flex flex-5 gap-10">
                    <div class="flex-1">
                        <label class="caption">Cópias</label>

                        <div class="addon">
                            <!-- BEGIN BLOCK_COPIES -->
                            <button class="printer_bt_copies button-field textleft one-line" data-id_impressora="{id_impressora}" title="Número de cópias na impressão">
                                {copies}
                            </button>
                            <!-- END BLOCK_COPIES -->
                            <!-- BEGIN EXTRA_BLOCK_COPIES_FORM -->
                            <form method="post" id="frm_printer_copies" class="fill" data-id_impressora='{id_impressora}'>
                                <select id="printer_copies" required class="fill" autofocus>
                                    <option value="1" {selected_1}>1</option>
                                    <option value="2" {selected_2}>2</option>
                                </select>
                            </form>
                            <!-- END EXTRA_BLOCK_COPIES_FORM -->
                        </div>
                    </div>

                    <div class="flex-2">
                        <label class="toggle">
                            <label class="caption">Fonte Grande</label>

                            <div class="addon-transp">
                                <input {bigfont} class="frm_printer_bigfont hidden" data-id_impressora="{id_impressora}" type="checkbox">
                                <span></span>
                                <div class="true"></div>
                                <div class="false"></div>
                            </div>
                        </label>
                    </div>

                    <div class="flex-2">
                        <label class="caption">Largura</label>
                        <!-- BEGIN BLOCK_COLUNAS -->
                        <div class="addon container">
                            <button class="printer_bt_colunas button-field textleft one-line" data-id_impressora="{id_impressora}" title="Largura do papel em colunas">
                                {colunas} colunas
                            </button>
                        </div>
                        <!-- END BLOCK_COLUNAS -->
                        <!-- BEGIN EXTRA_BLOCK_COLUNAS_FORM -->
                        <form method="post" id="frm_printer_colunas" class="fill" data-id_impressora="{id_impressora}">
                            <div class="addon container">
                                <input
                                    type="number"
                                    id="colunas"
                                    class="fill"
                                    step='1'
                                    min='20'
                                    max='80'
                                    maxlength="2"
                                    required
                                    value='{colunas}'
                                    autofocus>
                                <span>colunas</span>
                            </div>
                        </form>
                        <!-- END EXTRA_BLOCK_COLUNAS_FORM -->
                    </div>
                </div>

                <div class="flex flex-4 gap-10">
                    <div class="flex-5">
                        <label class="caption">Avanço</label>
                        <!-- BEGIN BLOCK_LINEFEED -->
                        <div class="addon container">
                            <button class="printer_bt_linefeed button-field textleft one-line" data-id_impressora="{id_impressora}" title="Avanço do papel no final da impressão">
                                {linefeed} linhas
                            </button>
                        </div>
                        <!-- END BLOCK_LINEFEED -->
                        <!-- BEGIN EXTRA_BLOCK_LINEFEED_FORM -->
                        <form method="post" id="frm_printer_linefeed" class="fill" data-id_impressora="{id_impressora}">
                            <div class="addon container">
                                <input
                                    type="number"
                                    id="linefeed"
                                    class="fill"
                                    step='1'
                                    min='0'
                                    max='10'
                                    maxlength="1"
                                    required
                                    value='{linefeed}'
                                    autofocus>
                                <span>linhas</span>
                            </div>
                        </form>
                        <!-- END EXTRA_BLOCK_LINEFEED_FORM -->
                    </div>

                    <div class="">
                        <label class="toggle">
                            <label class="caption">Guilhotina</label>
                            <div class="addon-transp">
                                <input {guilhotina} class="printer_bt_status hidden" data-id_impressora="{id_impressora}" type="checkbox">
                                <span></span>
                                <div class="true"></div>
                                <div class="false"></div>
                            </div>
                        </label>
                    </div>

                    <div class="flex flex-jc-fe flex-ai-fe">
                        <div class="menu-inter">
                            <button class="menu-inter-button fa-solid button-blue fa-ellipsis-vertical"></button>

                            <ul style="display: none;">
                                <li class="printer_bt_print flex flex-ai-center gap-10 color-blue" title="Teste de Impressão">
                                    <i class="icon fa-solid fa-print"></i>
                                    <span>Teste de Impressão</span>
                                </li>

                                <li class="printer_bt_del flex flex-ai-center gap-10 color-red" title="Remover Impressora">
                                    <i class="icon fa-solid fa-trash-can"></i>
                                    <span>Remover Impressora</span>
                                </li>
                            </ul>
                        </div>

                    </div>

                    <!-- <div class="flex flex-ai-fe gap-10">
                        <button class='printer_bt_print button-icon button-blue' title="Teste de impressão">
                            <i class="fa-solid fa-print"></i>
                        </button>
                        <button class='printer_bt_del button-icon button-red fa-solid fa-trash-can' title="Remover impressora"></button>
                    </div> -->
                </div>
            </div>
        </div>
        <!-- END EXTRA_BLOCK_PRINTER -->
    </div>

    <div class="padding-t10 flex flex-jc-fe">
        <button type="button" class="printer_bt_show_new button-blue" title="Adicionar nova impressora">Adicionar impressora</button>
    </div>
</div>

<!-- <div class="footer">
	<div class="footer-container gap-10">
	    <button class="printer_bt_show_new button-blue" title="Adicionar nova impressora">Adicionar Impressora</button>
	</div>
</div> -->

<!-- <div class="footer-popup flex-jc-fe">
	<div class="popup-menu">
		<button class="popup-main-button fa-solid fa-ellipsis-vertical button-pink"></button>

		<ul>

			<li class="flex flex-ai-center gap-10">
				<label>Adicionar Impressora</label>
				<button type="button" class="printer_bt_show_new button-blue " title="Adicionar nova impressora"></button>
			</li>
		</ul>
	</div>
</div> -->
<!-- END BLOCK_PAGE -->