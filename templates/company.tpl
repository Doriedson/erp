<!-- BEGIN BLOCK_PAGE -->
<div class="flex flex-dc gap-10">

    <div>
        <label class="caption">Nome fantasia</label>
        <div class="addon">
            <!-- BEGIN BLOCK_COMPANY_EMPRESA -->
            <button class="company_bt_empresa button-field textleft fill" title="Alterar nome da empresa">
                {empresa}
            </button>
            <!-- END BLOCK_COMPANY_EMPRESA -->

            <!-- BEGIN EXTRA_BLOCK_COMPANY_EMPRESA_FORM -->
            <form method="post" id="frm_company_empresa" class="flex fill">
                <input
                    type='text'
                    id='empresa'
                    class="fill"
                    placeholder=''
                    value='{empresa}'
                    maxlength='40'
                    required
                    autofocus>
            </form>
            <!-- END EXTRA_BLOCK_COMPANY_EMPRESA_FORM -->
        </div>
    </div>

    <div>
        <label class="caption">CNPJ</label>
        <div class="addon">
            <!-- BEGIN BLOCK_COMPANY_CNPJ -->
            <button class="company_bt_cnpj button-field textleft fill" title="Alterar CNPJ da empresa">
                {cnpj_formatted}
            </button>
            <!-- END BLOCK_COMPANY_CNPJ -->

            <!-- BEGIN EXTRA_BLOCK_COMPANY_CNPJ_FORM -->
            <form method="post" id="frm_company_cnpj" class="flex fill">
                <div class="addon">
                    <input
                        type="text"
                        id="cnpj"
                        class="fill"
                        pattern="\d{14}"
                        value="{cnpj}"
                        autofocus>
                </div>
            </form>
            <!-- END EXTRA_BLOCK_COMPANY_CNPJ_FORM -->
        </div>
    </div>

    <div>
        <label class="caption">IE</label>
        <div class="addon">
            <!-- BEGIN BLOCK_COMPANY_IE -->
            <button class="company_bt_ie button-field textleft fill" title="Alterar Inscrição Estadual">
                {ie_formatted}
            </button>
            <!-- END BLOCK_COMPANY_IE -->

            <!-- BEGIN EXTRA_BLOCK_COMPANY_IE_FORM -->
            <form method="post" id="frm_company_ie" class="flex fill">
                <div class="addon">
                    <input
                        type="text"
                        id="ie"
                        class="fill"
                        pattern="\d{9}"
                        value="{ie}"
                        autofocus>
                </div>
            </form>
            <!-- END EXTRA_BLOCK_COMPANY_IE_FORM -->
        </div>
    </div>

    <div class="flex-3">
        <label class="caption">Telefone</label>
        <div class="addon">
            <!-- BEGIN BLOCK_COMPANY_TELEFONE -->
            <button type="button" class="company_bt_telefone button-field textleft fill" alt="Alterar o número de telefone">
                {telefone_formatted}
            </button>
            <!-- END BLOCK_COMPANY_TELEFONE -->

            <!-- BEGIN EXTRA_BLOCK_COMPANY_TELEFONE_FORM -->
            <form id="frm_company_telefone" method="post" class="flex fill">
                <input
                    type="tel"
                    id="telefone"
                    class="fill"
                    pattern="[0-9]{8,13}"
                    maxlength="13"
                    size="13"
                    value="{telefone}"
                    autofocus>
            </form>
            <!-- END EXTRA_BLOCK_COMPANY_TELEFONE_FORM -->
        </div>
    </div>

    <div class="flex-3">
        <label class="caption">Celular</label>
        <div class="addon">
            <!-- BEGIN BLOCK_COMPANY_CELULAR -->
            <button type="button" class="company_bt_celular button-field textleft fill" alt="Alterar o número de celular">
                {celular_formatted}
            </button>
            <!-- END BLOCK_COMPANY_CELULAR -->

            <!-- BEGIN EXTRA_BLOCK_COMPANY_CELULAR_FORM -->
            <form id="frm_company_celular" method="post" class="flex fill">
                <input
                    type="tel"
                    id="celular"
                    class="fill"
                    pattern="[0-9]{8,13}"
                    maxlength="13"
                    size="13"
                    value="{celular}"
                    autofocus>
            </form>
            <!-- END EXTRA_BLOCK_COMPANY_CELULAR_FORM -->
        </div>
    </div>

    <div class="flex-2">
        <label class="caption">CEP</label>
        <div class="addon">
            <!-- BEGIN BLOCK_COMPANY_CEP -->
            <button type="button" class="company_bt_cep button-field textleft fill" alt="Alterar o CEP">
                {cep_formatted}
            </button>
            <!-- END BLOCK_COMPANY_CEP -->

            <!-- BEGIN EXTRA_BLOCK_COMPANY_CEP_FORM -->
            <form id="frm_company_cep" method="post" class="flex fill">
                <input
                    type="text"
                    id="cep"
                    class="fill"
                    pattern="\d{8}"
                    maxlength="8"
                    size="8"
                    value="{cep}"
                    autofocus>
            </form>
            <!-- END EXTRA_BLOCK_COMPANY_CEP_FORM -->
        </div>
    </div>

    <div>
        <label class="caption">Rua</label>
        <div class="addon">
            <!-- BEGIN BLOCK_COMPANY_RUA -->
            <button class="company_bt_rua button-field textleft fill uppercase" title="Alterar nome da rua">
                {rua}
            </button>
            <!-- END BLOCK_COMPANY_RUA -->

            <!-- BEGIN EXTRA_BLOCK_COMPANY_RUA_FORM -->
            <form method="post" id="frm_company_rua" class="flex fill">
                <input
                    type='text'
                    id='rua'
                    class="fill uppercase"
                    placeholder=''
                    value='{rua}'
                    maxlength='40'
                    required
                    autofocus>
            </form>
            <!-- END EXTRA_BLOCK_COMPANY_RUA_FORM -->
        </div>
    </div>

    <div>
        <label class="caption">Bairro</label>
        <div class="addon">
            <!-- BEGIN BLOCK_COMPANY_BAIRRO -->
            <button class="company_bt_bairro button-field textleft fill uppercase" title="Alterar nome da bairro">
                {bairro}
            </button>
            <!-- END BLOCK_COMPANY_BAIRRO -->

            <!-- BEGIN EXTRA_BLOCK_COMPANY_BAIRRO_FORM -->
            <form method="post" id="frm_company_bairro" class="flex fill">
                <input
                    type='text'
                    id='bairro'
                    class="fill uppercase"
                    placeholder=''
                    value='{bairro}'
                    maxlength='40'
                    required
                    autofocus>
            </form>
            <!-- END EXTRA_BLOCK_COMPANY_BAIRRO_FORM -->
        </div>
    </div>

    <div>
        <label class="caption">Cidade</label>
        <div class="addon">
            <!-- BEGIN BLOCK_COMPANY_CIDADE -->
            <button class="company_bt_cidade button-field textleft fill uppercase" title="Alterar nome da cidade">
                {cidade}
            </button>
            <!-- END BLOCK_COMPANY_CIDADE -->

            <!-- BEGIN EXTRA_BLOCK_COMPANY_CIDADE_FORM -->
            <form method="post" id="frm_company_cidade" class="flex fill">
                <input
                    type='text'
                    id='cidade'
                    class="fill uppercase"
                    placeholder=''
                    value='{cidade}'
                    maxlength='37'
                    required
                    autofocus>
            </form>
            <!-- END EXTRA_BLOCK_COMPANY_CIDADE_FORM -->
        </div>
    </div>

    <div class="flex-1">
        <label class="caption">UF</label>
        <div class="addon">
            <!-- BEGIN BLOCK_COMPANY_UF -->
            <button type="button" class="company_bt_uf button-field textleft fill" alt="Alterar a UF">
                {uf}
            </button>
            <!-- END BLOCK_COMPANY_UF -->
            <!-- BEGIN EXTRA_BLOCK_COMPANY_UF_FORM -->
            <form id="frm_company_uf" method="post" class="flex fill">
                <select id="uf" class="fill" autofocus>{uf}</select>
            </form>
            <!-- END EXTRA_BLOCK_COMPANY_UF_FORM -->
        </div>
    </div>

    <div>
        <label class="caption">Cupom Rodapé Linha 1</label>
        <div class="addon">
            <!-- BEGIN BLOCK_COMPANY_CUPOMLINHA1 -->
            <button class="company_bt_cupomlinha1 button-field textleft fill uppercase" title="Alterar linha 1 do rodapé do cupom">
                {cupomlinha1}
            </button>
            <!-- END BLOCK_COMPANY_CUPOMLINHA1 -->

            <!-- BEGIN EXTRA_BLOCK_COMPANY_CUPOMLINHA1_FORM -->
            <form method="post" id="frm_company_cupomlinha1" class="flex fill">
                <input
                    type='text'
                    id='cupomlinha1'
                    class="fill uppercase"
                    placeholder=''
                    value='{cupomlinha1}'
                    maxlength='40'
                    required
                    autofocus>
            </form>
            <!-- END EXTRA_BLOCK_COMPANY_CUPOMLINHA1_FORM -->
        </div>
    </div>

    <div>
        <label class="caption">Cupom Rodapé Linha 2</label>
        <div class="addon">
            <!-- BEGIN BLOCK_COMPANY_CUPOMLINHA2 -->
            <button class="company_bt_cupomlinha2 button-field textleft fill uppercase" title="Alterar linha 2 do rodapé do cupom">
                {cupomlinha2}
            </button>
            <!-- END BLOCK_COMPANY_CUPOMLINHA2 -->

            <!-- BEGIN EXTRA_BLOCK_COMPANY_CUPOMLINHA2_FORM -->
            <form method="post" id="frm_company_cupomlinha2" class="flex fill">
                <input
                    type='text'
                    id='cupomlinha2'
                    class="fill uppercase"
                    placeholder=''
                    value='{cupomlinha2}'
                    maxlength='40'
                    required
                    autofocus>
            </form>
            <!-- END EXTRA_BLOCK_COMPANY_CUPOMLINHA2_FORM -->
        </div>
    </div>
</div>
<!-- END BLOCK_PAGE -->