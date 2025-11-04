<!-- BEGIN BLOCK_PAGE -->

<!-- BEGIN EXTRA_BLOCK_POPUP_WALLET_SHARING -->
<div class="flex flex-dc">

    <div class="box-header gap-10">
		<i class="icon fa-solid fa-wallet"></i>
		{wallet}
	</div>
    <!-- <div>
        <label class="caption">Carteira</label>
        <div class="addon">
            <span class="walletsharing_description">{carteira}</span>
        </div>
    </div> -->

    <!-- <div class="section-header">
        Compartilhar com
    </div> -->

    <div class="w_walletsharing_table table tbody flex flex-dc">

        {extra_block_walletsharing_list}

        <!-- BEGIN EXTRA_BLOCK_WALLETSHARING_LIST_NONE -->
        <div class="" style="padding: 40px 10px;">
            Nenhum usuário encontrado.
        </div>
        <!-- END EXTRA_BLOCK_WALLETSHARING_LIST_NONE -->

        <!-- BEGIN EXTRA_BLOCK_WALLETSHARING_LIST -->
        <div class="tr flex gap-10">

            <div class="flex flex-ai-center">
                <input type="checkbox" class="walletsharing_share" data-id_wallet="{id_wallet}" data-id_entidade="{id_entidade}" {shared}>
            </div>

            <div class="flex-1 flex flex-ai-center">
                <span class="entity_{id_entidade}_nome">{nome}</span>
            </div>

        </div>
        <!-- END EXTRA_BLOCK_WALLETSHARING_LIST -->
    </div>
</div>
<!-- END EXTRA_BLOCK_POPUP_WALLET_SHARING -->

<div class="box-container box-header no-margin gap-10">
	<i class="icon fa-solid fa-wallet"></i>
	Minha Carteira
</div>

<div class="box-container flex flex-dc gap-10">

	<div class="box-header">
		Carteiras
	</div>

	<!-- BEGIN BLOCK_WALLETS_CONTAINER -->
    <div class="wallet_not_found window {wallet_notfound}">

        <div class="font-size-12 textcenter" style="padding: 80px 10px;">
            Nenhuma carteira cadastrada
        </div>
    </div>

	<div class="wallets_table table tbody flex flex-wrap gap-10">

        {extra_block_wallet}

		<!-- BEGIN EXTRA_BLOCK_WALLET -->
        <div class="w_wallet flex-4-col ticket-border flex-dc gap-10 border-blue" data-id_wallet="{id_wallet}">

			<div class="flex gap-10 flex-6">
                <div class="flex-1">
                    <label class="caption">Descrição</label>
                    <div class="addon">
                        <!-- BEGIN BLOCK_DESCRIPTION -->
                        <button class="wallet_bt_description button-field textleft fill" data-id_wallet="{id_wallet}" title="Editar descrição da carteira">
                            {wallet}

                        </button>
                        <!-- END BLOCK_DESCRIPTION -->

                        <!-- BEGIN EXTRA_BLOCK_FORM_DESCRIPTION -->
                        <form method="post" id="frm_wallet_description" class="fill" data-id_wallet="{id_wallet}">

                            <input
                                type="text"
                                id="description"
                                class="fill"
                                maxlength="50"
                                required
                                value="{wallet}"
                                autofocus>
                        </form>
                        <!-- END EXTRA_BLOCK_FORM_DESCRIPTION -->
                    </div>
                </div>
            </div>

			<div class="flex gap-10 flex-3">
                <div class="flex-1">
                    <label class="caption">Saldo</label>
                    <div class="addon wallet_{id_wallet}_saldo font-size-12">
                        {wallet_saldototal}
                    </div>
                </div>

                <div class="flex flex-ai-fe">
                    <div class="menu-inter">
                        <button class="menu-inter-button fa-solid fa-ellipsis-vertical button-blue"></button>

                        <ul>
                            <li class="wallet_bt_open flex flex-ai-center gap-10 color-blue" data-id_wallet="{id_wallet}" title='Abrir carteira'>
                                <i class="icon fa-solid fa-wallet"></i>
                                <span>Abrir Carteira</span>
                            </li>

                            <li class="walletdespesa_bt_new flex flex-ai-center gap-10 color-blue" data-id_wallet="{id_wallet}" title="Registrar despesa">
                                <i class="icon fa-solid fa-money-check-dollar"></i>
                                <span>Registrar despesa</span>
                            </li>

                            <li class="walletreceita_bt_new flex flex-ai-center gap-10 color-green-dark" data-id_wallet="{id_wallet}" title="Registrar receita">
                                <i class="icon fa-solid fa-hand-holding-dollar"></i>
                                <span>Registrar receita</span>
                            </li>

                            <li class="wallet_bt_share flex flex-ai-center gap-10 color-blue" data-id_wallet="{id_wallet}" title='Compartilhar carteira'>
                                <i class="icon fa-solid fa-share-nodes"></i>
                                <span>Compartilhar Carteira</span>
                            </li>

                            <li class="wallet_bt_del flex flex-ai-center gap-10 color-red" data-id_wallet="{id_wallet}" title='Remover carteira'>
                                <i class="icon fa-solid fa-trash-can"></i>
                                <span>Apagar Carteira</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
		<!-- END EXTRA_BLOCK_WALLET -->

        <!-- BEGIN EXTRA_BLOCK_WALLETSHARE -->
        <div class="w_wallet flex-4-col ticket-border flex-dc gap-10 border-gray" data-id_wallet="{id_wallet}">
		<!-- <div class="w_wallet tr flex-responsive gap-10 window" data-id_wallet="{id_wallet}" data-value="{value}"> -->

            <div class="flex gap-10 flex-6">
                <div class="flex-1">
                    <div class="flex flex-ai-center gap-10 color-blue" title="Carteira compartilhada">
                        <!-- <div class="icon-share" ></div> -->
                        <i class="fa-solid fa-share-nodes"></i>
                        <label class="caption">
                            <span class="entity_{id_entidade}_nome">{nome}</span>
                        </label>
                    </div>

                    <div class="addon padding-h5">
                        <div>
                            <span>{wallet}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex gap-10 flex-3">
                <div class="flex-1">
                    <label class="caption">Saldo</label>
                    <div class="addon wallet_{id_wallet}_saldo font-size-12">
                        {wallet_saldototal}
                    </div>
                </div>

                <div class="flex flex-ai-fe">
                    <div class="menu-inter">
                        <button class="menu-inter-button fa-solid fa-ellipsis-vertical button-blue"></button>

                        <ul>
                            <li class="wallet_bt_open flex flex-ai-center gap-10 color-blue" data-id_wallet="{id_wallet}" title='Abrir carteira'>
                                <i class="icon fa-solid fa-wallet"></i>
                                <span>Abrir Carteira</span>
                            </li>

                            <li class="walletsharing_bt_del flex flex-ai-center gap-10 color-red" data-id_wallet="{id_wallet}" title='Remover carteira da minha lista'>
                                <i class="icon fa-solid fa-share-nodes"></i>
                                <span>Remover Carteira</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
		</div>
		<!-- END EXTRA_BLOCK_WALLETSHARE -->
	</div>

    <div class="section-footer padding-t10 no-margin flex flex-jc-fe">
        <button type="button" class="wallet_bt_new button-blue">Criar Carteira</button>
    </div>
	<!-- END BLOCK_WALLETS_CONTAINER -->
</div>
<!-- END BLOCK_PAGE -->