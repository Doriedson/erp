<!-- BEGIN BLOCK_PAGE -->
<div class="flex flex-dc gap-10">

    <div class="addon">
        <label class="toggle">
            <div class="addon-transp padding-h5">
                <input class="cashierclosing hidden" data-field="produtos" {produtos} type="checkbox">
                <span></span>
                <i></i>
            </div>
        </label>
        <span>Estorno de produtos (venda balcão)</span>
    </div>

    <div class="addon">
        <label class="toggle">
            <div class="addon-transp padding-h5">
                <input class="cashierclosing hidden" data-field="vendas" {vendas} type="checkbox">
                <span></span>
                <i></i>
            </div>
        </label>
        <span>Estorno de vendas</span>
    </div>

    <div class="box-container flex-responsive flex-ai-fe gap-10">
        <div class="fill">
            <div class="addon">
                <label class="toggle">
                    <div class="addon-transp padding-h5">
                        <input class="cashierclosing hidden" data-field="produtosvendidos" {produtosvendidos} type="checkbox">
                        <span></span>
                        <i></i>
                    </div>
                </label>
                <span>Lista de produtos vendidos</span>
            </div>
        </div>

        <div class="fill">
            <label class="caption">Intervalo de tempo</label>

            <div class="addon">
                <!-- BEGIN BLOCK_CASHIERCLOSING_PRODUCT -->
                <button type="button" class="bt_cashierclosing_product button-field">{frm_cashierclosing_product_option}</button>
                <!-- END BLOCK_CASHIERCLOSING_PRODUCT -->

                <!-- BEGIN EXTRA_BLOCK_CASHIERCLOSING_PRODUCT_FORM -->
                <form method="post" id="frm_cashierclosing_product">
                    <select id="frm_cashierclosing_product_option" autofocus>
                        <option value="1" {frm_cashierclosing_product_option_1}>Abertura do caixa até fechamento</option>
                        <option value="0" {frm_cashierclosing_product_option_0}>0h da abertura do caixa até fechamento</option>
                    </select>
                </form>
                <!-- END EXTRA_BLOCK_CASHIERCLOSING_PRODUCT_FORM -->
            </div>
        </div>
    </div>

    <div class="addon">
        <label class="toggle">
            <div class="addon-transp padding-h5">
                <input class="cashierclosing hidden" data-field="pedidopago" {pedidopago} type="checkbox">
                <span></span>
                <i></i>
            </div>
        </label>
        <span>Pedidos / Delivery pagos</span>
    </div>

    <div class="addon">
        <label class="toggle">
            <div class="addon-transp padding-h5">
                <input class="cashierclosing hidden" data-field="reprint" {reprint} type="checkbox">
                <span></span>
                <i></i>
            </div>
        </label>
        <span>Reimpressão de pedidos / vendas</span>
    </div>

    <div class="addon">
        <label class="toggle">
            <div class="addon-transp padding-h5">
                <input class="cashierclosing hidden" data-field="taxagarcom" {taxagarcom} type="checkbox">
                <span></span>
                <i></i>
            </div>
        </label>
        <span>Taxa de serviço do garçon</span>
    </div>

    <div class="addon">
        <label class="toggle">
            <div class="addon-transp padding-h5">
                <input class="cashierclosing hidden" data-field="vendaprazo" {vendaprazo} type="checkbox">
                <span></span>
                <i></i>
            </div>
        </label>
        <span>Vendas a prazo</span>
    </div>

    <div class="addon">
        <label class="toggle">
            <div class="addon-transp padding-h5">
                <input class="cashierclosing hidden" data-field="vendaprazopaga" {vendaprazopaga} type="checkbox">
                <span></span>
                <i></i>
            </div>
        </label>
        <span>Vendas a prazo pagas</span>
    </div>

    <div class="addon">
        <label class="toggle">
            <div class="addon-transp padding-h5">
                <input class="cashierclosing hidden" data-field="mesas" {mesas} type="checkbox">
                <span></span>
                <i></i>
            </div>
        </label>
        <span>Controle de Mesas (Mesas pagas e em aberto)</span>
    </div>
</div>
<!-- END BLOCK_PAGE -->