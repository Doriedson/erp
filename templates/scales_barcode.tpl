<!-- BEGIN BLOCK_PAGE -->
<div class="flex flex-dc gap-10">

    <div class="addon">
        <span class="flex">
            <input type="checkbox" class="check_scalesbarcode" {scalesbarcode}>
        </span>
        <span>Ler código de barras gerado por balança.</span>
    </div>

    <div class="flex-1">
        <label class="caption">Dígitos iniciais que identificam um código de barras gerado pela balança.</label>

        <!-- BEGIN BLOCK_STARTNUMBER -->
        <div class="addon container">
            <button class="bt_scalesbarcode_startnumber button-field nowrap textleft" title="">
                {scalesbarcode_startnumber}

            </button>
        </div>
        <!-- END BLOCK_STARTNUMBER -->
        <!-- BEGIN EXTRA_BLOCK_FORM_STARTNUMBER -->
        <form method="post" id="frm_scalesbarcode_startnumber" class="">
            <div class="addon container">
                <input
                    type="number"
                    id="scalesbarcode_startnumber"
                    class=""
                    step='1'
                    min='0'
                    max='9'
                    maxlength="1"
                    required
                    value='{scalesbarcode_startnumber}'
                    autofocus>
            </div>
        </form>
        <!-- END EXTRA_BLOCK_FORM_STARTNUMBER -->
    </div>

    <div class="flex-1">
        <label class="caption">Tamanho total do código de barras gerado pela balança.</label>

        <!-- BEGIN BLOCK_SIZECODE -->
        <div class="addon container">
            <button class="bt_scalesbarcode_sizecode button-field nowrap textleft" title="">
                {scalesbarcode_sizecode}

            </button>
            <span>dígitos</span>
        </div>
        <!-- END BLOCK_SIZECODE -->
        <!-- BEGIN EXTRA_BLOCK_FORM_SIZECODE -->
        <form method="post" id="frm_scalesbarcode_sizecode" class="">
            <div class="addon container">
                <input
                    type="number"
                    id="scalesbarcode_sizecode"
                    class=""
                    step='1'
                    min='1'
                    max='25'
                    maxlength="2"
                    required
                    value='{scalesbarcode_sizecode}'
                    autofocus>
                <span>dígitos</span>
            </div>
        </form>
        <!-- END EXTRA_BLOCK_FORM_SIZECODE -->
    </div>

    <div class="flex-1">
        <label class="caption">Como identificar o <b>código do produto</b>.</label>

        <div class="addon container">
            <span>inicia no dígito</span>
            <!-- BEGIN BLOCK_PRODUCTSTARTPOSITION -->
            <button class="bt_scalesbarcode_productstartposition button-field nowrap textleft" title="">
                {scalesbarcode_productstartposition}

            </button>
            <!-- END BLOCK_PRODUCTSTARTPOSITION -->
            <span>e vai até o dígito</span>
            <!-- BEGIN BLOCK_PRODUCTENDPOSITION -->
            <button class="bt_scalesbarcode_productendposition button-field nowrap textleft" title="">
                {scalesbarcode_productendposition}

            </button>
            <!-- END BLOCK_PRODUCTENDPOSITION -->
        </div>

        <!-- BEGIN EXTRA_BLOCK_FORM_PRODUCTSTARTPOSITION -->
        <form method="post" id="frm_scalesbarcode_productstartposition" class="">
            <div class="addon container">
                <input
                    type="number"
                    id="scalesbarcode_productstartposition"
                    class=""
                    step='1'
                    min='0'
                    max='99'
                    maxlength="2"
                    required
                    value='{scalesbarcode_productstartposition}'
                    autofocus>
            </div>
        </form>
        <!-- END EXTRA_BLOCK_FORM_PRODUCTSTARTPOSITION -->
        <!-- BEGIN EXTRA_BLOCK_FORM_PRODUCTENDPOSITION -->
        <form method="post" id="frm_scalesbarcode_productendposition" class="">
            <div class="addon container">
                <input
                    type="number"
                    id="scalesbarcode_productendposition"
                    class=""
                    step='1'
                    min='0'
                    max='99'
                    maxlength="2"
                    required
                    value='{scalesbarcode_productendposition}'
                    autofocus>
            </div>
        </form>
        <!-- END EXTRA_BLOCK_FORM_PRODUCTENDPOSITION -->
    </div>

    <div class="flex-1">
        <label class="caption">Como identificar o <b>peso ou valor</b> do produto.</label>

        <div class="addon container">
            <span>inicia no dígito</span>
            <!-- BEGIN BLOCK_WEIGHTSTARTPOSITION -->
            <button class="bt_scalesbarcode_weightstartposition button-field nowrap textleft" title="">
                {scalesbarcode_weightstartposition}

            </button>
            <!-- END BLOCK_WEIGHTSTARTPOSITION -->
            <span>e vai até o dígito</span>
            <!-- BEGIN BLOCK_WEIGHTENDPOSITION -->
            <button class="bt_scalesbarcode_weightendposition button-field nowrap textleft" title="">
                {scalesbarcode_weightendposition}

            </button>
            <!-- END BLOCK_WEIGHTENDPOSITION -->
        </div>

        <!-- BEGIN EXTRA_BLOCK_FORM_WEIGHTSTARTPOSITION -->
        <form method="post" id="frm_scalesbarcode_weightstartposition" class="">
            <div class="addon container">
                <input
                    type="number"
                    id="scalesbarcode_weightstartposition"
                    class=""
                    step='1'
                    min='0'
                    max='99'
                    maxlength="2"
                    required
                    value='{scalesbarcode_weightstartposition}'
                    autofocus>
            </div>
        </form>
        <!-- END EXTRA_BLOCK_FORM_WEIGHTSTARTPOSITION -->
        <!-- BEGIN EXTRA_BLOCK_FORM_WEIGHTENDPOSITION -->
        <form method="post" id="frm_scalesbarcode_weightendposition" class="">
            <div class="addon container">
                <input
                    type="number"
                    id="scalesbarcode_weightendposition"
                    class=""
                    step='1'
                    min='0'
                    max='99'
                    maxlength="2"
                    required
                    value='{scalesbarcode_weightendposition}'
                    autofocus>
            </div>
        </form>
        <!-- END EXTRA_BLOCK_FORM_WEIGHTENDPOSITION -->
    </div>

    <div class="flex-1">
        <label class="caption">O código de barras contém o <b>peso</b> ou o <b>valor</b> do produto.</label>

        <div class="addon container">
            <!-- BEGIN BLOCK_WEIGHTORPRICE -->
            <button class="bt_scalesbarcode_weightorprice button-field nowrap textleft" title="">
                {scalesbarcode_weightorprice_desc}

            </button>
            <!-- END BLOCK_WEIGHTORPRICE -->
            <span>com</span>
            <!-- BEGIN BLOCK_WEIGHTDECIMALS -->
            <button class="bt_scalesbarcode_weightdecimals button-field nowrap textleft" title="">
                {scalesbarcode_weightdecimals}

            </button>
            <!-- END BLOCK_WEIGHTDECIMALS -->
            <span>dígitos de casas decimais</span>
        </div>

        <!-- BEGIN EXTRA_BLOCK_FORM_WEIGHTORPRICE -->
        <form method="post" id="frm_scalesbarcode_weightorprice" class="">
            <div class="addon">
                <select id="scalesbarcode_weightorprice" class="" autofocus>
                    {setor_option}
                </select>
            </div>
        </form>
        <!-- END EXTRA_BLOCK_FORM_WEIGHTORPRICE -->
        <!-- BEGIN EXTRA_BLOCK_FORM_WEIGHTDECIMALS -->
        <form method="post" id="frm_scalesbarcode_weightdecimals" class="">
            <div class="addon container">
                <input
                    type="number"
                    id="scalesbarcode_weightdecimals"
                    class=""
                    step='1'
                    min='0'
                    max='99'
                    maxlength="2"
                    required
                    value='{scalesbarcode_weightdecimals}'
                    autofocus>
            </div>
        </form>
        <!-- END EXTRA_BLOCK_FORM_WEIGHTDECIMALS -->
    </div>
</div>
<!-- END BLOCK_PAGE -->