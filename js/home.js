async function CPFormEdit(container, button, action) {

	data = {
		action: action
	}

	return await FormEdit(container, button, data, "home.php");
}

async function CPFormCancel(container, field, action) {

	var form = field.closest('form');

	data = {
		action: action
	}

	return await FormCancel(container, form, field, data, "home.php");
}

async function CPFormSave(container, form, field, action) {

	let data = {
		action: action,
		value: field.val(),
	}

	return await FormSave(container, form, field, data, "home.php");
}

$(document).on("click",".bt_load", function(event) {

    let button = $(this);

    Disable(button);

    LoadPage(button.data("page") + ".php");

    Enable(button);
});

$(document).on("click",".bt_module", async function(event) {

    let button = $(this);

    let module = button.data("module");

    Disable(button);

    switch(module) {

        case "waiter":

            window.open("./garcom");

            break;

        case "digitalmenu":

            window.open("./cardapiodigital");

            break;

        case "updatelog":

            let data = {
                action: "updatelog"
            }

            let response = await Post("home.php", data);

            $(".body-container").html(response.join("<br>"));

    }

    Enable(button);
});

/**
  * Opens "product_expirate_days" edition
  */
 $(document).on("click", ".product_bt_expiratedays", async function() {

	CPFormEdit($(this), $(this), 'cp_expiratedays_edit');
});

/**
  * Cancel "product_expirate_days" edition.
  */
 $(document).on("focusout", "#frm_product_expiratedays #product_expirate_days", async function() {

	CPFormCancel($(this).closest('form'), $(this), 'cp_expiratedays_cancel');
});

/**
  * Save "product_expirate_days" edition.
  */
$(document).on("submit", "#frm_product_expiratedays", async function(event) {

	event.preventDefault();

	let form = $(this);

	let table = $('.cp_expdate_table');

	FormDisable(form);

	await CPFormSave(form, form, $(this.product_expirate_days), 'cp_expiratedays_save');

    let data = {
        action: "cp_expiratedays_update"
    }

    $('.productexpdate_bt_print').addClass('hidden');

	if (table.length > 0) {

		table.html(imgLoading);

		$(".cp_expdate_notfound").addClass('hidden');
	}

	$(".productexpdate_expirated").html(imgLoading);
	$(".productexpdate_toexpirate").html(imgLoading);

	let response = await Post('home.php', data);

	if (response != null) {

		if (table.length > 0) {

			table.html(response["data"]);

			if (response["expirated"] == 0 && response["toexpirate"] == 0) {

				$('.cp_expdate_notfound').removeClass('hidden');

			} else {

				$('.productexpdate_bt_print').removeClass('hidden');
			}
		}

		$(".productexpdate_expirated").html(response["expirated"]);
		$(".productexpdate_toexpirate").html(response["toexpirate"]);
		$(".product_bt_expiratedays").replaceWith(response["extra_block_expiratedays"]);
	}
});

/**
  * Event button to print product to expirate list
  */
 $(document).on("click", ".productexpdate_bt_print", async function() {

	var button = $(this);

    Disable(button);

	let data = {
		action: 'cp_expiratedays_print',
	}

	await Post("home.php", data);

    Enable(button);
});

/**
  * Event to show expirate list
  */
$(document).on("click", ".productexpdate_bt_list", async function() {

	let button = $(this);

    Disable(button);

	let data = {
		action: 'productexpdate_popup_list',
	}

	let response = await Post("home.php", data);

	if (response != null) {

		Modal.Show(Modal.POPUP_SIZE_LARGE, "Controle de Validade dos Produtos", response, null);
	}

    Enable(button);
});