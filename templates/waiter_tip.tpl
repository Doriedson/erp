<!-- BEGIN BLOCK_PAGE -->
<div class="flex flex-dc gap-10">

    <div class="">
        <span class="caption">Taxa de serviço</span>
        <!-- BEGIN BLOCK_TAXA -->
        <div class="addon container">
            <button class="waitertip_bt_taxaservico button-field" title="Alterar taxa de serviço">
                {taxa_servico_formatted} %
            </button>
        </div>
        <!-- END BLOCK_TAXA -->
        <!-- BEGIN EXTRA_BLOCK_TAXA_FORM -->
        <form method="post" id="frm_waitertip_taxaservico" class="fill">
            <div class="addon">
                <input
                    type="number"
                    id="taxa_servico"
                    step='0.01'
                    min='0'
                    max='100'
                    required
                    value='{taxa_servico}'
                    autofocus>
                <span>%</span>
            </div>
        </form>
        <!-- END EXTRA_BLOCK_TAXA_FORM -->
    </div>
</div>
<!-- END BLOCK_PAGE -->