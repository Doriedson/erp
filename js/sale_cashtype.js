async function PaymentKindFormEdit(container, button, action) {

	data = {
		action: action,
		id_especie: button.data("id_especie"),
	}

	return await FormEdit(container, button, data, "sale_cashtype.php");
}

async function PaymentKindFormCancel(container, field, action) {

	var form = field.closest('form');

	var id_especie = form.data('id_especie');

	data = {
		action: action,
		id_especie: id_especie,
	}

	return await FormCancel(container, form, field, data, "sale_cashtype.php");
}

async function PaymentKindFormSave(container, form, field, action) {

	var data = {
		action: action,
		id_especie: form.data('id_especie'),
		especie: field.val(),
	}

    return await FormSave(container, form, field, data, "sale_cashtype.php");
}

/**
  * Add sector
  */
$(document).on("submit", "#frm_salecashtype", async function(event) {

	event.preventDefault();

	let form = $(this);

	let popup = form.closest(".popup");

	let container = $('.salecashtype_table');

    let especie = this.especie.value;

	FormDisable(form);

	data = {
		action: 'salecashtype_add',
		especie: especie
	}

	let response = await Post("sale_cashtype.php", data);

	if (response != null) {

		let content = $(response);
		container.append(content);

		ContainerFocus(content, true);

		this.especie.value = "";

		Modal.Close(popup);
	}

	FormEnable(form);
});

/**
  * Opens "especie" edition
  */
 $(document).on("click", ".salecashtype_bt_especie", async function() {

	PaymentKindFormEdit($(this), $(this), 'salecashtype_edit');
});

/**
  * Cancel especie edition.
  */
 $(document).on("focusout", "#frm_salecashtype_especie #especie", async function() {

	PaymentKindFormCancel($(this).closest('form'), $(this), 'salecashtype_cancel');
});

/**
  * Save especie edition.
  */
$(document).on("submit", "#frm_salecashtype_especie", async function(event) {

	event.preventDefault();

	FormDisable($(this));

	PaymentKindFormSave($(this), $(this), $(this.especie), 'salecashtype_save');
});

/**
  * Deletes cashtype
  */
 $(document).on("click", ".salecashtype_bt_del", async function() {

	let button = $(this);

	let container = button.closest('.w-salecashtype');

	Disable(button);

	let yes = async function() {

		let data = {
			action: 'salecashtype_delete',
			id_especie: container.data('id_especie')
		}

		response = await Post("sale_cashtype.php", data);

		if (response != null) {

			ContainerRemove(container);

		} else {

			Enable(button);
		}

		return true;
	}

	let no = async function () {

		Enable(button);
	}

	MessageBox.Show("Remover espécie?", yes, no);
});

/**
  * Toggle active salecashtype
  */
 $(document).on("click", ".salecashtype_bt_ativo", async function() {

    let button = $(this);

	Disable(button);

    let data = {
        action: 'salecashtype_toggle_active',
        id_especie: button.data('id_especie')
    }

    let response = await Post("sale_cashtype.php", data);

	Enable(button);

    if (response != null) {

        button.prop("checked", response);
	}
});

/**
  * Shows new salecashtype popup
  */
 $(document).on("click", ".salecashtype_bt_show_new", async function() {

	let button = $(this);

	Disable(button);

	let data = {
		action: "salecashtype_popup_new"
	}

	let response = await Post("sale_cashtype.php", data);

    if (response != null) {

		Modal.Show(Modal.POPUP_SIZE_SMALL, "Cadastro de Espécie", response, null);
	}

	Enable(button);
	// $('.w-salecashtype-new-popup').removeClass("hidden");

	// AutoFocus($('.w-salecashtype-new-popup'));
});