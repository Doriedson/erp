async function PrinterFormEdit(container, button, action) {

	data = {
		action: action,
		id_impressora: button.data("id_impressora"),
	}

	return await FormEdit(container, button, data, "printer.php");
}

async function PrinterFormCancel(container, field, action) {

	var form = field.closest('form');

	var id_impressora = form.data('id_impressora');

	data = {
		action: action,
		id_impressora: id_impressora,
	}

	return await FormCancel(container, form, field, data, "printer.php");
}

async function PrinterFormSave(container, form, field, action) {

	var data = {
		action: action,
		id_impressora: form.data('id_impressora'),
		value: field.val(),
	}

	return await FormSave(container, form, field, data, "printer.php");
}

/**
  * Add printer
  */
$(document).on("submit", "#frm_printer", async function(event) {

	event.preventDefault();

	let form = $(this);

	let popup = form.closest(".popup");

	let container = $('.w-printer-table');

	FormDisable(form);

	let data = {
		action: 'printer_add',
		descricao: this.descricao.value,
		printer_local_desc: this.printer_local_desc.value,
		printer_share_desc: this.printer_share_desc.value,
		printer_ip_desc: this.printer_ip_desc.value,
		printer_option: $("input[name=printer_option]:checked").val()
	}

	response = await Post("printer.php", data);

	if (response != null) {

        $('.w-printer-none').addClass('hidden');

		let content = $(response);
		container.append(content);

		ContainerFocus(content, true);

		this.descricao.value = "";

		Modal.Close(popup);
	}

	FormEnable(form);
});

/**
  * Opens "descricao" edition
  */
 $(document).on("click", ".printer_descricao", async function() {

	PrinterFormEdit($(this), $(this), 'printer_descricao_edit');
});

/**
  * Cancel "descricao" edition.
  */
 $(document).on("focusout", "#frm_printer_descricao #descricao", async function() {

	PrinterFormCancel($(this).closest('form'), $(this), 'printer_descricao_cancel');
});

/**
  * Save "descricao" edition.
  */
$(document).on("submit", "#frm_printer_descricao", async function(event) {

	event.preventDefault();

	FormDisable($(this));

	PrinterFormSave($(this), $(this), $(this.descricao), 'printer_descricao_save');
});

/**
  * Opens "impressora" edition
  */
$(document).on("click", ".printer_impressora", async function() {

	let button = $(this);

	Disable(button);

	let data = {
		action: 'printer_impressora_edit',
		id_impressora: button.data("id_impressora"),
	}

	let response = await Post('printer.php', data);

	if (response != null) {

		Modal.Show(Modal.POPUP_SIZE_SMALL, "Impressora", response, null);
	}

	Enable(button);
});

/**
  * Saves "impressora" edition.
  */
$(document).on("submit", "#frm_printer_impressora", async function(event) {

	event.preventDefault();

	let form = $(this);
	let id_impressora = form.data('id_impressora');

	let popup = form.closest(".popup");

	FormDisable(form);

	let data = {
		action: 'printer_impressora_save',
		id_impressora: id_impressora,
		printer_local_desc: $("#printer_local_desc").val(),
		printer_share_desc: $("#printer_share_desc").val(),
		printer_ip_desc: $("#printer_ip_desc").val(),
		printer_option: $("input[name=printer_option]:checked").val()
	}

	response = await Post("printer.php", data);

	if (response != null) {

		$('.w_printer_' + id_impressora).replaceWith(response);
		Modal.Close(popup);

	} else {

		FormEnable(form);
	}
});

/**
  * Toggle active and not active printer cutter
  */
 $(document).on("click", ".printer_bt_status", async function() {

	let button = $(this);

	let id_impressora = button.data('id_impressora');

	Disable(button);

	let data = {
		action: "printer_change_status",
		id_impressora: id_impressora,
	}

	response = await Post("printer.php", data);

	Enable(button);

	if (response != null) {

		button.prop("checked", response);
	}
});

/**
  * Deletes printer
  */
 $(document).on("click", ".printer_bt_del", async function() {

	let button = $(this);

	let container = button.closest('.w_printer');

	Disable(button);

	let data = {
		action: 'printer_delete',
		id_impressora: container.data('id_impressora')
	}

	let yes = async function() {

		response = await Post("printer.php", data);

		if (response != null) {

			ContainerRemove(container, function() {

				if ($('.w_printer').length == 0) {

					$('.w-printer-none').removeClass('hidden');
				}
			});

		} else {

			Enable(button);
		}

		return true;
	}

	let no = async function() {

		Enable(button);
	}

	MessageBox.Show("Confirma a remoção da impressora?", yes, no);

	MenuClose();
});

/**
  * Test print
  */
 $(document).on("click", ".printer_bt_print", async function() {

	let button = $(this);

	let container = button.closest('.w_printer');

	Disable(button);

	let data = {
		action: 'printer_print',
		id_impressora: container.data('id_impressora')
	}

	await Post("printer.php", data);

	Enable(button);

	CloseMenu();
});

/**
  * Shows new printer popup
  */
$(document).on("click", ".printer_bt_show_new", async function() {

	let button = $(this);

	Disable(button);

	let data = {
		action: "printer_popup_impressora_new"
	}

	let response = await Post("printer.php", data);

	if (response != null) {

		Modal.Show(Modal.POPUP_SIZE_SMALL, "Adicionar Impressora", response, null);
	}

	Enable(button);
});

/**
  * Open "linefeed" edition
  */
$(document).on("click", ".printer_bt_linefeed", async function() {

	PrinterFormEdit($(this).closest('.container'), $(this), "printer_linefeed_edit");
});

/**
  * Cancels "linefeed" edition
  */
$(document).on("focusout", "#frm_printer_linefeed #linefeed", async function() {

	PrinterFormCancel($(this).closest('form'), $(this), "printer_linefeed_cancel");
});

/**
  * Saves "linefeed" edition.
  */
$(document).on("submit", "#frm_printer_linefeed", async function(event) {

	event.preventDefault();

	FormDisable($(this));

	PrinterFormSave($(this), $(this), $(this.linefeed), "printer_linefeed_save");
});

/**
  * Open "colunas" edition
  */
$(document).on("click", ".printer_bt_colunas", async function() {

	PrinterFormEdit($(this).closest('.container'), $(this), "printer_colunas_edit");
});

/**
  * Cancels "colunas" edition
  */
$(document).on("focusout", "#frm_printer_colunas #colunas", async function() {

	PrinterFormCancel($(this).closest('form'), $(this), "printer_colunas_cancel");
});

/**
  * Saves "colunas" edition.
  */
$(document).on("submit", "#frm_printer_colunas", async function(event) {

	event.preventDefault();

	FormDisable($(this));

	PrinterFormSave($(this), $(this), $(this.colunas), "printer_colunas_save");
});

/**
  * Selects font size
  */
$(document).on("click", ".frm_printer_bigfont", async function() {

	let button = $(this);

	let id_impressora = button.data('id_impressora');

	Disable(button);

	let data = {
		action: "printer_bigfont_toggle",
		id_impressora: id_impressora,
	}

	response = await Post("printer.php", data);

	Enable(button);

	if (response != null) {

		button.prop("checked", response);
	}
});

/**
  * Opens "copies" edition
  */
$(document).on("click", ".printer_bt_copies", async function() {

	let button = $(this);

	let id_impressora = button.data('id_impressora');

	Disable(button);

	let data = {
		action: "printer_copies_edit",
		id_impressora: id_impressora,
	}

	let response = await Post("printer.php", data);

	if (response != null) {

		let content = $(response);

		button.replaceWith(content);

		AutoFocus(content);

	} else {

		Enable(button);
	}
});

/**
  * Cancel "copies" edition.
  */
$(document).on("focusout", "#frm_printer_copies #printer_copies", async function() {

	//Prevents focusout on save
	if ($(this).prop('disabled')) {
		return;
 	}

	let form = $(this).closest("form");

	FormDisable(form);

	let id_impressora = form.data('id_impressora');

	let data = {
		action: "printer_copies_cancel",
		id_impressora: id_impressora,
	}

	let response = await Post("printer.php", data);

	if (response != null) {

		form.replaceWith(response);

	} else {

		FormEnable(form);
	}
});

/**
  * Save "copies" edition.
  */
$(document).on("change", "#frm_printer_copies", async function(event) {

	event.preventDefault();

	let form = $(this);

	FormDisable(form);

	let value = this.printer_copies.value;

	let id_impressora = form.data('id_impressora');

	let data = {
		action: "printer_copies_save",
		id_impressora: id_impressora,
		value: value
	}

	let response = await Post("printer.php", data);

	if (response != null) {

		let content = $(response);

		form.replaceWith(content);

	} else {

		FormEnable(form);
	}
});

$(document).on("change", "input[name='printer_option']", function() {

	let selection = $("input[name=printer_option]:checked").val();

	$("#printer_local_desc").prop("disabled", "disabled");
	$("#printer_share_desc").prop("disabled", "disabled");
	$("#printer_ip_desc").prop("disabled", "disabled");

	switch(selection) {

		case "printer_local":

			$("#printer_local_desc").prop("disabled", "");
			$("#printer_local_desc").focus();

		break;

		case "printer_share":

			$("#printer_share_desc").prop("disabled", "");
			$("#printer_share_desc").focus();

		break;

		case "printer_ip":

			$("#printer_ip_desc").prop("disabled", "");
			$("#printer_ip_desc").focus();

		break;
	}
});