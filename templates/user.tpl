<!-- BEGIN BLOCK_PAGE -->

<!-- BEGIN EXTRA_BLOCK_COLLABORATOR_PASS -->
<div class="flex flex-dc gap-10">

    <div class="box-header gap-10">
        <i class="icon fa-solid fa-user"></i>
        <span class="entity_{id_entidade}_nome">{nome}</span>
    </div>

    <form method="post" id="frm_collaborator_pass" class='flex flex-dc gap-10'>
        <div>
            <label class="caption">Senha atual</label>
            <div class="addon">
                <input
                class="fill"
                type="password"
                id="old_pass"
                maxlength="4"
                pattern="[0-9]+"
                autofocus
                required
                placeholder="">
            </div>
        </div>

        <div>
            <label class="caption">Nova senha</label>
            <div class="addon">
                <input
                class="fill"
                type="password"
                id="new_pass"
                maxlength="4"
                pattern="[0-9]+"
                required
                placeholder="">
            </div>
        </div>

        <div>
            <label class="caption">Confirma nova senha</label>
            <div class="addon">
                <input
                class="fill"
                type="password"
                id="new_pass_confirm"
                maxlength="4"
                pattern="[0-9]+"
                required
                placeholder="">
            </div>
        </div>

        <div class="flex flex-ai-fe padding-t10">
            <button type="submit" class="button-blue fill" title="Alterar senha do colaborador.">Alterar senha</button>
        </div>
    </form>
</div>
<!-- END EXTRA_BLOCK_COLLABORATOR_PASS -->

<div class="box-container box-header flex flex-jc-sb gap-10">

    <div class="flex gap-10">
        <i class="icon fa-solid fa-user"></i>
        <span class="entity_{id_entidade}_nome">{nome}</span>
    </div>

    <button type="button" class="bt_collaborator_pass button-blue" title="Alterar senha">
        <i class="icon fa-solid fa-key"></i>
    </button>
</div>

<div class="box-container window flex flex-dc gap-10">
    <div class="box-header gap-10">
        <i class="icon fa-solid fa-wallet"></i>
        Minha Carteira
    </div>

	<div class="flex flex-dc gap-10">{wallets}</div>
</div>
<!-- END BLOCK_PAGE -->