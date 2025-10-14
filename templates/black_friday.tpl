<!-- BEGIN BLOCK_PAGE -->

<!-- BEGIN EXTRA_BLOCK_POPUP_BLACKFRIDAY_NEW -->
<div class="flex flex-dc gap-10">

    <form method="post" id="frm_blackfriday" class="fill">
        <div class="flex flex-dc gap-10">

            <div>
                <label class="caption">Data</label>
                <div class="addon">
                    <input
                        type='date'
                        id="data"
                        class="fill"
                        value='{data}'
                        title="Data ou data inicial"
                        required>
                </div>
            </div>

            <div class="flex flex-ai-fe gap-10">
                <div>
                    <label class="caption">Desconto</label>
                    <div class="addon">
                        <input
                            type="number"
                            id="desconto"
                            class="fill"
                            step='0.01'
                            min='0.01'
                            max='100'
                            maxlength="6"
                            title="Desconto a ser aplicado em todas as compras"
                            required
                            autofocus
                            placeholder=''>
                        <span>%</span>
                    </div>
                </div>

                <div>
                    <label class="caption">Acumulativo</label>
                    <div class="addon">
                        <span class="flex">
                            <input type="checkbox" value="1" id="acumulativo">
                        </span>
                    </div>
                </div>

                <div class="">
                    <button type="submit" class="button-blue" title="Adicionar data da Black Friday">Cadastrar</button>
                </div>
            </div>
        </div>
    </form>

    <div class="pseudo-button border-up">Acumulativo: Define se a regra do BlackFriday acumula com o programa fidelidade.</div>
</div>
<!-- END EXTRA_BLOCK_POPUP_BLACKFRIDAY_NEW -->

<div class="flex flex-dc gap-10">

    <!-- <div class="section-header">
        Datas
    </div> -->

	<div class="blackfriday_notfound window fill {blackfriday_notfound}">

		<div class="font-size-12" style="padding: 40px 10px;">
			Não há data cadastrada para a Black Friday.
		</div>
	</div>

    <div class="blackfriday_table table tbody flex flex-dc">

        {extra_block_blackfriday}

        <!-- BEGIN EXTRA_BLOCK_BLACKFRIDAY -->
        <div class="w-blackfriday tr flex-responsive gap-10">

            <div class="flex gap-10">
                <div class="flex-1">
                    <label class="caption">Data</label>
                    <div class="addon">
                        <span class="field fill">{data_formatted}</span>
                    </div>
                </div>

                <div class="flex-1">
                    <label class="caption">Desconto</label>
                    <div class="addon">
                        <span class="field">{desconto_formatted} %</span>
                    </div>
                </div>
            </div>

            <div class="flex gap-10 flex-ai-fe ">
                <div class="flex-1">
                    <label class="caption">Acumulativo</label>
                    <div class="addon">
                        <span class="field fill">{acumula}</span>
                    </div>
                </div>

                <div class="flex-1">
                    <button class="blackfriday_rule_del button-red fill" data-id_blackfriday="{id_blackfriday}"  title="Remover data da Black Friday">Remover</button>
                </div>
            </div>
        </div>
        <!-- END EXTRA_BLOCK_BLACKFRIDAY -->
    </div>

    <div class="padding-t10 flex flex-jc-fe">
        <button type="button" class="blackfriday_bt_show_new button-blue" title="Cadastrar nova data">Cadastrar data</button>
    </div>
</div>

<!-- <div class="footer-popup flex-jc-fe">
	<div class="popup-menu">
		<button class="popup-main-button fa-solid fa-ellipsis-vertical button-pink"></button>

		<ul>

			<li class="flex flex-ai-center gap-10">
				<label>Nova data</label>
				<button type="button" class="blackfriday_bt_show_new button-blue" title="Cadastrar nova data"></button>
			</li>
		</ul>
	</div>
</div> -->
<!-- END BLOCK_PAGE -->