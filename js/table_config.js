/**
  * Adds tables
  */
$(document).on("submit", "#frm_tableconfig", async function(event) {

	event.preventDefault();

	let form = $(this);

	let popup = form.closest(".popup");

	FormDisable(form);

	let data = {
		action: "add",
		number_of_tables: this.number_of_tables.value,
		id_start: this.id_start.value,
	}

	let response = await Post("table_config.php", data);

	if(response) {

		$('.tableconfig_notfound').addClass('hidden');

		$(".tableconfig_table").html(response);

		this.number_of_tables.value = "";

		Modal.Close(popup);
	}

	FormEnable(form);
});

/**
  * Removes table
  */
 $(document).on("click", ".table_bt_del", async function() {

	var button = $(this);

	var container = button.closest('.w-tableconfig');

	Disable(button);

	var data = {
		action: "table_del",
		id_mesa: button.closest('.w-tableconfig').data('id_mesa'),
	}

	response = await Post("table_config.php", data);

	if(response) {

		container.remove();

		if($(".w-tableconfig").length == 0) {

			$(".tableconfig_notfound").removeClass('hidden');
		}

	} else {

		Enable(button);
	}
});

/**
  * Opens "mesa" edition
  */
 $(document).on("click", ".table_bt_mesa", async function() {

	var button = $(this);

	Disable(button);

	var data = {
		action: "table_mesa_edit",
		id_mesa: button.closest('.w-tableconfig').data('id_mesa'),
	}

	response = await Post("table_config.php", data);

	if(response) {

		var content = $(response);

		button.replaceWith(content);

		AutoFocus(content);

	} else {

		Enable(button);
	}
});

/**
  * Cancels "mesa" edition
  */
 $(document).on("focusout", "#frm_table_mesa #mesa", async function() {

	var field = $(this);

	//Prevents focusout on save
	if (field.prop('disabled')) {

		return;
	}

	var form = field.closest('form');

	var id_mesa = form.data('id_mesa');

	data = {
		action: "table_mesa_cancel",
		id_mesa: id_mesa,
	}

	field.addClass('loading');

	response = await Post("table_config.php", data);

	if (response != null) {

		form.replaceWith(response);

	} else {

		field.removeClass('loading');
	}
});

/**
  * Saves "mesa" edition.
  */
 $(document).on("submit", "#frm_table_mesa", async function(event) {

	event.preventDefault();

	var form = $(this);

	FormDisable(form);

	var data = {
		action: "table_mesa_save",
		id_mesa: form.data('id_mesa'),
		value: this.mesa.value,
	}

	response = await Post("table_config.php", data);

	if (response != null) {

		form.replaceWith(response);

	} else {

		FormEnable(form);
		AutoFocus(form);
	}
});

/**
  * Shows new tableconfig popup
  */
 $(document).on("click", ".tableconfig_bt_show_new", async function() {

	let button = $(this);

	let data = {
		action: "tableconfig_popup_new"
	}

	Disable(button);

	let response = await Post("table_config.php", data);

	if (response != null) {

		Modal.Show(Modal.POPUP_SIZE_SMALL, "Cadastro de Mesas", response, null);
	}

	Enable(button);
});