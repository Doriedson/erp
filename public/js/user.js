/**
  * Shows change collaborator pass.
  */
$(document).on("click", ".bt_collaborator_pass", async function() {

    let button = $(this);

    Disable(button);

    data = {
        action: 'collaborator_show_password',
    }

    response = await Post("user.php", data);

    if (response != null) {

        Modal.Show(Modal.POPUP_SIZE_SMALL, "Alteração de Senha", response, null, false, "<i class='icon fa-solid fa-key'></i>");
	}

    Enable(button);
});

/**
  * change collaborator pass.
  */
$(document).on("submit", "#frm_collaborator_pass", async function(event) {

    event.preventDefault();

    let form = $(this);

    FormDisable(form);

    let old_pass = this.old_pass.value;
    let new_pass = this.new_pass.value;
    let new_pass_confirm = this.new_pass_confirm.value;

    if (new_pass != new_pass_confirm) {

        Message.Show("Nova senha não confere!",Message.MSG_ERROR);

        FormEnable(form);
        this.new_pass.focus();

        return;
    }

    data = {
        action: 'collaborator_password',
        old_pass: old_pass,
        new_pass: new_pass,
        new_pass_confirm: new_pass_confirm,
    }

    response = await Post("user.php", data);

    if (response != null) {

        // this.old_pass.value = "";
        // this.new_pass.value = "";
        // this.new_pass_confirm.value = "";
        Modal.Close(form.closest(".popup"));

	} else {

        FormEnable(form);
        this.old_pass.focus();
    }
});