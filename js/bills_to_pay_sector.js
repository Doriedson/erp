/**
 * Adds sector
 */
$(document).on("submit", "#frm_billstopaysector", async function(event) {

	event.preventDefault();

	let form = $(this);

	let container = $(".w-billstopaysector-container");

	FormDisable(form);

	data = {
		action: 'billstopaysector_setor_new',
		value: this.contasapagarsetor.value
	}

	let response = await Post("bills_to_pay_sector.php", data);

	if (response != null) {

		$('.billstopaysector_not_found').remove();

		let content = $(response);

		container.append(content);

		this.contasapagarsetor.value = "";

		ContainerFocus(content, true);

		Modal.Close(form.closest('.popup'));
	}

	FormEnable(form);
	AutoFocus(form);
});

/**
  * Opens 'contasapagarsetor' edition
  */
 $(document).on("click", ".billstopaysector_bt_contasapagarsetor", async function() {

	var button = $(this);

	var id_contasapagarsetor = $(this).closest(".w-billstopaysector").data("id_contasapagarsetor");

	Disable(button);

	data = {
		action: 'billstopaysector_contasapagarsetor_edit',
		id_contasapagarsetor: id_contasapagarsetor,
	}

	response = await Post("bills_to_pay_sector.php", data);

	if (response != null) {

		var content = $(response);

		button.replaceWith(content);

		AutoFocus(content);

	} else {

		Enable(button);
	}
});

/**
  * Cancels 'contasapagarsetor' edition.
  */
 $(document).on("focusout", "#frm_billstopaysector_contasapagarsetor #contasapagarsetor", async function() {

	//Prevents focusout on save
 	if ($(this).prop('disabled')) {
 		return;
 	}

	var form = $(this).closest('form');

	var id_contasapagarsetor = $(this).closest('.w-billstopaysector').data('id_contasapagarsetor');

	FormDisable(form);

	data = {
		action: 'billstopaysector_contasapagarsetor_cancel',
		id_contasapagarsetor: id_contasapagarsetor,
	}

	response = await Post("bills_to_pay_sector.php", data);

	if (response != null) {

		form.replaceWith(response);

	} else {

		FormEnable(form);
		AutoFocus(form);
	}
});

/**
  * Saves 'contasapagarsetor' edition.
  */
$(document).on("submit", "#frm_billstopaysector_contasapagarsetor", async function(event) {

	event.preventDefault();


	var form = $(this).closest('form');

	var id_contasapagarsetor = $(this).closest('.w-billstopaysector').data('id_contasapagarsetor');
	var contasapagarsetor = this.contasapagarsetor.value;

	FormDisable(form);

	data = {
		action: 'billstopaysector_contasapagar_save',
		id_contasapagarsetor: id_contasapagarsetor,
		contasapagarsetor: contasapagarsetor,
	}

	response = await Post("bills_to_pay_sector.php", data);

	if (response != null) {

		form.replaceWith(response);

	} else {

		FormEnable(form);
		AutoFocus(form);
	}
});

/**
 * Deletes sector
 */
 $(document).on("click", ".billstopaysector_bt_delete", async function() {

	var button = $(this);

	var container = button.closest(".w-billstopaysector");

	var id_contasapagarsetor = container.data("id_contasapagarsetor");

	button.addClass('laoding');

	data = {
		action: 'billstopaysector_setor_delete',
		id_contasapagarsetor: id_contasapagarsetor,
	}

	response = await Post("bills_to_pay_sector.php", data);

	if (response != null) {

		if (response.length) {

			ContainerRemove(container, function() {
				$('.w-billstopaysector-container').html(response);
			});

		} else {

			ContainerRemove(container);
		}

	} else {

        Enable(button);
    }
});

/**
  * Shows new billstopaysector popup
  */
 $(document).on("click", ".billstopaysector_bt_show_new", async function() {

	// $('.w-billstopaysector-new-popup').removeClass("hidden");

	// AutoFocus($('.w-billstopaysector-new-popup'));
	let data = {
		action: "billstopay_popup_newsector"
	}

	let response = await Post("bills_to_pay_sector.php", data);

	if (response != null) {

		Modal.Show(Modal.POPUP_SIZE_SMALL, "Novo Setor", response, null);
	}

	MenuClose();
});