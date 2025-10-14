<!-- BEGIN BLOCK_PAGE -->
<div class="flex flex-dc gap-10">

    <div class="w-printer-table flex flex-dc gap-10">
        {extra_block_printing}

        <!-- BEGIN EXTRA_BLOCK_PRINTING -->
        <div class="w-printing window flex flex-dc gap-10" data-id_impressao='{id_impressao}'>

            <div class="flex-responsive gap-10">

                <div class="flex flex-15 gap-10">
                    <div class="flex-14">
                        <label class="caption">{descricao}</label>
                        <div class="addon">
                            <!-- BEGIN BLOCK_IMPRESSORA -->
                            <button class="printing_bt_impressora button-field textleft fill" data-id_impressao='{id_impressao}' title="Definir impressora">
                                {printer_desc}
                            </button>
                            <!-- END BLOCK_IMPRESSORA -->
                            <!-- BEGIN EXTRA_BLOCK_IMPRESSORA_FORM -->
                            <form method="post" id="frm_printing_impressora" class="fill" data-id_impressao='{id_impressao}'>
                                <select id="id_impressora" class="fill" required autofocus>
                                    {printing_option}
                                    <!-- BEGIN EXTRA_BLOCK_PRINTER_OPTION -->
                                    <option value="{id_impressora}" {selected}>{descricao}</option>
                                    <!-- END EXTRA_BLOCK_PRINTER_OPTION -->
                                </select>
                            </form>
                            <!-- END EXTRA_BLOCK_IMPRESSORA_FORM -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END EXTRA_BLOCK_PRINTING -->
    </div>
</div>
<!-- END BLOCK_PAGE -->