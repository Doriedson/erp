<!-- BEGIN BLOCK_PAGE -->
<div class="flex flex-dc flex-ai-center gap-10">
    
    <div class="popup">
        <div class="popup-container popup-small">
            <div class="window-container">

                <div class="window-header setor-2 flex flex-jc-sb flex-ai-center gap-10">
                    Abertura de Caixa
                    <button class="popup_close button-icon button-transparent color-white fa-solid fa-xmark" title="Fechar"></button>
                </div>

                <form method="post" id="frm_pdv_open" class="flex flex-dc gap-10">

                    <div>
                        <label class="caption">Fundo de Caixa</label>
                        <div class="addon">
                            <span>R$</span>
                            <input 
                                type="number" 
                                id="troco" 
                                class="fill"
                                step='0.01' 
                                min='0' 
                                max='999999.99' 
                                required
                                placeholder="0,00"
                                value='{troco}' 
                                title="Fundo de caixa de abertura">
                        </div>
                    </div>

                    <div class="flex flex-jc-center">
                        <button type="submit" class="button-blue">Abrir Caixa</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

	<div class="fa-solid fa-cash-register" style="font-size: 15rem; line-height: 15rem;"></div>
    <span class="" style="font-size: 4rem; line-height: 4rem;">Caixa Fechado</span>
    <div>
        <button type="button" class="bt_pdv_open button-blue">Abrir Caixa</button>
    </div>
    <span>{id_pdv}</span>
</div>
<!-- END BLOCK_PAGE -->