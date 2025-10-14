<!-- BEGIN BLOCK_PAGE -->
<div class="box-header gap-10 box-container">
	<i class="icon fa-solid fa-gear"></i>
    <span>Configuração / Integrações</span>
</div>

<div class="flex flex-dc gap-10 box-container">
    
    <div class="flex-responsive gap-10">
        <div class="box-header flex-1">Delivery Direto</div>

        <label class="toggle">
        
            <div class="addon-transp">
                <input {ativo} class="bt_deliverydireto_ativo hidden" type="checkbox">
                <span></span>
                <div class="true">Ativo</div>
                <div class="false">Inativo</div>
            </div>
        </label>
    </div>

    <div class="flex flex-dc gap-10">

        <div class="">
            <label class="caption">X-DeliveryDireto-ID</label>
            <div class="addon">
                <!-- BEGIN BLOCK_STOREID -->
                <button class="bt_deliverydireto_storeid button-field textleft fill" title="Identificador da loja no Delivery Direto">
                    {store_id}
                </button>
                <!-- END BLOCK_STOREID -->
                <!-- BEGIN EXTRA_BLOCK_STOREID_FORM -->
                <form method="post" id="frm_deliverydireto_storeid" class="fill">
                    <input 
                        type='text' 
                        id='storeid' 
                        class="fill"
                        placeholder='' 
                        value='{store_id}'
                        maxlength='255'
                        autofocus>
                </form>
                <!-- END EXTRA_BLOCK_STOREID_FORM -->
            </div>
        </div>

        <div class="">
            <label class="caption">Usuário</label>
            <div class="addon">
                <!-- BEGIN BLOCK_USUARIO -->
                <button class="bt_deliverydireto_usuario button-field textleft fill" title="Usuário para acessar a loja no Delivery Direto">
                    {usuario}
                </button>
                <!-- END BLOCK_USUARIO -->
                <!-- BEGIN EXTRA_BLOCK_USUARIO_FORM -->
                <form method="post" id="frm_deliverydireto_usuario" class="fill">
                    <input 
                        type='text' 
                        id='usuario' 
                        class="fill"
                        placeholder='' 
                        value='{usuario}'
                        maxlength='255'
                        autofocus>
                </form>
                <!-- END EXTRA_BLOCK_USUARIO_FORM -->
            </div>
        </div>

        <div class="">
            <label class="caption">Senha</label>
            <div class="addon">
                <!-- BEGIN BLOCK_SENHA -->
                <button class="bt_deliverydireto_senha button-field textleft fill" title="Senha para acessar a loja do Delivery Direto">
                    {senha}
                </button>
                <!-- END BLOCK_SENHA -->
                <!-- BEGIN EXTRA_BLOCK_SENHA_FORM -->
                <form method="post" id="frm_deliverydireto_senha" class="fill">
                    <input 
                        type='text' 
                        id='senha' 
                        class="fill"
                        placeholder='' 
                        value=''
                        maxlength='255'
                        autofocus>
                </form>
                <!-- END EXTRA_BLOCK_SENHA_FORM -->
            </div>
        </div>
    </div>
</div>

<div class="flex flex-jc-fe gap-10 padding-t10">
    <button type="button" class="bt_dd_test1 button-blue">DD get address fee</button>
    <button type="button" class="bt_dd_test2 button-blue">DD Calculate fee</button>
    <button type="button" class="bt_dd_test3 button-blue">DD get Orders</button>
</div>

<!-- END BLOCK_PAGE -->