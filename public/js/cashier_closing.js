/**
  * Activate/Deactivate cashierclosing options.
  */
$(document).on("click", ".cashierclosing", async function() {

    let button = $(this);

    Disable(button);

    let data = {
        action: "cashierclosing",
        field: $(this).data('field'),
        value: this.checked
    }

    response = await Post("cashier_closing.php", data);

    Enable(button);

    if (response != null) {

        button.prop("checked", response);
    }
});

/**
  * Opens cashierclosing options to edit.
  */
$(document).on("click", ".bt_cashierclosing_product", async function() {

    let button = $(this);

    Disable(button);

    let data = {
        action: "cashierclosing_product_edit",
    }

    response = await Post("cashier_closing.php", data);

    if (response != null) {

        let content = $(response);

        button.replaceWith(content);

        AutoFocus(content);

    } else {

        Enable(button);
    }
});

/**
  * Cancels cashierclosing options edition
  */
$(document).on("focusout", "#frm_cashierclosing_product #frm_cashierclosing_product_option", async function() {

    let field = $(this);

	//Prevents focusout on save
 	if (field.prop('disabled')) {
 		return;
 	}

	Disable(field);

	let container = field.closest('form');

	data = {
		action: 'cashierclosing_product_cancel',
	}

	let response = await Post("cashier_closing.php", data);

	if (response != null) {

		container.replaceWith(response);

	} else {

		Enable(field);
	}

});

/**
  * Saves Entity nome edition
  */
$(document).on("change", "#frm_cashierclosing_product", async function(event) {

	event.preventDefault();

	let form = $(this);

	let value = this.frm_cashierclosing_product_option.value;

    FormDisable(form);

	let data = {
		action: "cashierclosing_product_save",
		value: value
	}

	let response = await Post("cashier_closing.php", data);

	if (response != null) {

		form.replaceWith(response);

	} else {

		FormEnable(form);
	}
});