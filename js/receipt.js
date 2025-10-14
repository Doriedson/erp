/**
  * Adds receipt
  */
$(document).on("submit", "#frm_receipt", async function(event) {

	event.preventDefault();

	if ($('.w-receipt-container').length == 0) {

		await LoadPage("receipt.php");
	}

	let form = $(this);

	let popup = form.closest('.popup');

	Modal.CloseAround(popup);

	let container = $('.w-receipt-container').find('.tbody');

	let field = $(this).find(".entity_search");

	let entidade = field.data("sku");

	if (entidade) {

		field.val(field.data('descricao'));

	} else {

		entidade = field.val();
	}

	FormDisable(form);

	let data = {
		action: 'receipt_add',
		data: this.frm_receipt_data.value,
		nome: entidade,
		valor: this.frm_receipt_valor.value,
		motivo: this.frm_receipt_motivo.value,
	}

	response = await Post("receipt.php", data);

	FormEnable(form);

	if (response != null) {

		$('.receipt_not_found').remove();

		let content = $(response);

		container.append(content);

		ContainerFocus(content, true);

		$('.receipt_bt_clear').removeClass('hidden');
		$('.receipt_bt_print').removeClass('hidden');

		if (popup.find('.popup_fixwindow').hasClass('fa-thumbtack')) {

			Modal.Close(popup);

		} else {

			field.val("");
			this.frm_receipt_valor.value = "";
			this.frm_receipt_motivo.value = "";

			AutoFocus(form);
		}
	}
});

/**
  * Deletes receipt
  */
$(document).on("click", ".receipt_bt_delete", async function() {

	let button = $(this);

	let container = button.closest('.w-receipt');

	let	data = {
		action: 'receipt_delete',
		id_recibo: button.data('id_recibo'),
	}

	let yes = async function() {

		let response = await Post("receipt.php", data);

		if (response != null) {

			if (response.length) {

				ContainerRemove(container, function() {
					$('.w-receipt-container').find('.tbody').html(response);
				});

				$('.receipt_bt_clear').addClass('hidden');
				$('.receipt_bt_print').addClass('hidden');

			} else {

				ContainerRemove(container);
			}
		}

		return true;
	}

	let no = async function() {

	}

	MessageBox.Show("Remover recibo?", yes, no);
});

/**
  * Deletes all receipts
  */
$(document).on("click", ".receipt_bt_clear", async function() {

	// let button = $(this);

	let	data = {
		action: 'receipt_delete_all',
	}

	response = await Post("receipt.php", data);

	if (response != null) {

		// var container = $('.w-receipt');

		// ContainerRemove(container, function() {
			$('.w-receipt-container').find('.tbody').html(response);

			$('.receipt_bt_clear').addClass('hidden');
			$('.receipt_bt_print').addClass('hidden');
		// });
	}

	MenuClose();
});

/**
  * Prints receipt list.
  */
$(document).on("click", ".receipt_bt_print", async function() {

	let button = $(this);

	let	data = {
		action: 'receipt_print',
	}

	response = await Post("receipt.php", data);

	if (button.closest('.popup-menu').length) {

		MenuClose();
	}

	if (response != null) {

		Printer.Print(response);
	}
});

/**
  * Shows new receipt popup
  */
$(document).on("click", ".receipt_bt_new", async function() {

	let button = $(this);

	let id_entidade = button.data('id_entidade');

	let nome = button.data('nome');

	if (typeof id_entidade === 'undefined') {

		id_entidade = "";
		nome = "";
	}

	Disable(button);

	let data = {
		action: "receipt_popup",
		id_entidade: id_entidade,
		nome: nome
	}

	response = await Post("receipt.php", data);

	if (response != null) {

		Modal.Show(Modal.POPUP_SIZE_SMALL, "Emitir Novo Recibo", response, null, Modal.POPUP_BUTTONFIX);
	}

	Enable(button);
});