<!-- BEGIN BLOCK_PAGE -->
<div class="flex flex-dc gap-10 table tbody">

    {extra_block_pdv}

	<!-- BEGIN EXTRA_BLOCK_PDV_NONE -->
	<div class="pdv-not-none tr window fill">

		<div class="font-size-12 textcenter" style="padding: 80px 10px;">
			Não há PDVs cadastrados.
		</div>
	</div>
	<!-- END EXTRA_BLOCK_PDV_NONE -->

	<!-- BEGIN EXTRA_BLOCK_PDV -->
	<div class="w-pdv tr flex flex-dc gap-10" data-id_pdv="{id_pdv}">

        <div class="flex-responsive gap-10">

            <div class="flex gap-10 flex-4">

                <div class="flex-3">
                    <label class="caption">PDV {id_pdv} - Descrição</label>

                    <!-- BEGIN BLOCK_DESCRICAO -->
                    <div class="addon container">
                        <button class="pdv_bt_descricao button-field textleft fill" data-id_pdv="{id_pdv}">
                            {descricao}
                        </button>
                    </div>
                    <!-- END BLOCK_DESCRICAO -->

                    <!-- BEGIN EXTRA_BLOCK_FORM_DESCRICAO -->
                    <form method="post" id="frm_pdv_descricao" class="flex fill" data-id_pdv="{id_pdv}">
                        <div class="addon">
                            <input
                                type='text'
                                id='descricao'
                                class="fill"
                                placeholder=''
                                value='{descricao}'
                                maxlength='50'
                                autofocus>
                        </div>
                    </form>
                    <!-- END EXTRA_BLOCK_FORM_DESCRICAO -->
                </div>

                <div class="">
                    <label class="caption">Troco Inicial</label>
                    <div class="addon">
                        <label class="toggle" title="Ativar / Desativar alteração do troco na abertura do PDV">
                            <div class="addon-transp padding-h5">
                                <input class="pdv_bt_trocoini hidden" data-id_pdv="{id_pdv}" {bt_trocoini} type="checkbox">
                                <span></span>
                                <i></i>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="flex gap-10 flex-5">

                <div class="flex-1">
                    <label class="caption">Balança</label>
                    <div class="addon">
                        <label class="toggle" title="Ativar / Desativar balança">
                            <div class="addon-transp padding-h5">
                                <input class="pdv_bt_balanca hidden" data-id_pdv="{id_pdv}" {bt_balanca} type="checkbox">
                                <span></span>
                                <i></i>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="flex-2">
                    <label class="caption">CharWrite</label>
                    <!-- BEGIN BLOCK_CHARWRITE -->
                    <div class="addon container">
                        <button class="pdv_bt_charwrite button-field textleft fill one-line" data-id_pdv="{id_pdv}" title="Editar caracter para solicitação de leitura da balança">
                            {balanca_charwrite}
                        </button>
                    </div>
                    <!-- END BLOCK_CHARWRITE -->
                    <!-- BEGIN EXTRA_BLOCK_FORM_CHARWRITE -->
                    <form method="post" id="frm_pdv_charwrite" class="fill" data-id_pdv="{id_pdv}">
                        <div class="addon container">
                            <input
                                type="number"
                                id="balanca_charwrite"
                                class="fill"
                                step='1'
                                min='0'
                                max='255'
                                maxlength="1"
                                required
                                value='{balanca_charwrite}'
                                autofocus>
                        </div>
                    </form>
                    <!-- END EXTRA_BLOCK_FORM_CHARWRITE -->
                </div>

                <div class="flex-2">
                    <label class="caption">CharEnd</label>
                    <!-- BEGIN BLOCK_CHAREND -->
                    <div class="addon container">
                        <button class="pdv_bt_charend button-field textleft fill one-line" data-id_pdv="{id_pdv}" title="Editar caracter final para leitura dos dados da balança">
                            {balanca_charend}
                        </button>
                    </div>
                    <!-- END BLOCK_CHAREND -->
                    <!-- BEGIN EXTRA_BLOCK_FORM_CHAREND -->
                    <form method="post" id="frm_pdv_charend" class="fill" data-id_pdv="{id_pdv}">
                        <div class="addon container">
                            <input
                                type="number"
                                id="balanca_charend"
                                class="fill"
                                step='1'
                                min='0'
                                max='255'
                                maxlength="1"
                                required
                                value='{balanca_charend}'
                                autofocus>
                        </div>
                    </form>
                    <!-- END EXTRA_BLOCK_FORM_CHAREND -->
                </div>
            </div>

            <!-- <div class="flex-7"></div> -->
        <!-- </div> -->

            <div class="flex-responsive gap-10 flex-9">
                <div class="flex-4">
                    <label class="caption">Impressora</label>
                    <div class="addon container">

                        <label class="toggle" title="Ativar / Desativar impressão automática de cupom no PDV">
                            <div class="addon-transp padding-h5">
                                <input class="pdv_bt_impressora hidden" data-id_pdv="{id_pdv}" {bt_impressora} type="checkbox">
                                <span></span>
                                <i></i>
                            </div>
                        </label>

                        <!-- BEGIN BLOCK_IMPRESSORA -->
                        <button class="pdv_bt_impressora_path button-field textleft fill" data-id_pdv="{id_pdv}" title="Definir impressora">
                            {printer_desc}
                        </button>
                        <!-- END BLOCK_IMPRESSORA -->

                        <!-- BEGIN EXTRA_BLOCK_IMPRESSORA_FORM -->
                        <form method="post" id="frm_pdv_impressora_path" class="flex fill" data-id_pdv="{id_pdv}">
                            <select id="id_impressora" class="fill" required autofocus>
                                {extra_block_printer_option}
                                <!-- BEGIN EXTRA_BLOCK_PRINTER_OPTION -->
                                <option value="{id_impressora}" {selected}>{descricao}</option>
                                <!-- END EXTRA_BLOCK_PRINTER_OPTION -->
                            </select>
                        </form>
                        <!-- END EXTRA_BLOCK_IMPRESSORA_FORM -->
                    </div>
                </div>

                <div class="flex-4">
                    <label class="caption">Gaveta / modelo</label>
                    <div class="addon">
                        <label class="toggle" title="Ativar / Desativar gaveta">
                            <div class="addon-transp padding-h5">
                                <input class="pdv_bt_gaveta hidden" data-id_pdv="{id_pdv}" {bt_gaveta} type="checkbox">
                                <span></span>
                                <i></i>
                            </div>
                        </label>

                        <!-- BEGIN BLOCK_CASHDRAWERTYPE -->
                        <button class="pdv_bt_cashdrawertype button-field textleft flex-1" data-id_pdv="{id_pdv}" title="Editar modelo da gaveta">
                            {gaveteiro_desc}
                        </button>
                        <!-- END BLOCK_CASHDRAWERTYPE -->
                        <!-- BEGIN EXTRA_BLOCK_CASHDRAWERTYPE_FORM -->
                        <form method="post" id="frm_pdv_cashdrawertype" class="flex fill" data-id_pdv="{id_pdv}">
                            <select id="id_gaveteiro" class="fill" required autofocus>
                                {extra_block_cashdrawer_option}
                                <!-- BEGIN EXTRA_BLOCK_CASHDRAWER_OPTION -->
                                <option value="{id_gaveteiro}" {selected}>{descricao}</option>
                                <!-- END EXTRA_BLOCK_CASHDRAWER_OPTION -->
                            </select>
                        </form>
                        <!-- END EXTRA_BLOCK_CASHDRAWERTYPE_FORM -->
                    </div>
                </div>
            </div>
        </div>
    </div>
	<!-- END EXTRA_BLOCK_PDV -->

    <!-- <div class="footer-popup flex-jc-fe">
        <div class="popup-menu">
            <button class="popup-main-button fa-solid fa-ellipsis-vertical button-pink"></button>

            <ul>
                <li class="flex flex-ai-center gap-10">
                    <label>Ativar PDV neste computador</label>
                    <button type="button" class="bt_pdv_new button-blue icon-cashier" alt="Ativar PDV neste computador"></button>
                </li>
            </ul>
        </div>
    </div> -->
</div>
<!-- END BLOCK_PAGE -->