async function ShipmentFormEdit(container, button, action) {

	data = {
		action: action,
	}

	return await FormEdit(container, button, data, "config_shipment.php");
}

async function ShipmentFormCancel(container, field, action) {

	var form = field.closest('form');

	data = {
		action: action,
	}

	return await FormCancel(container, form, field, data, "config_shipment.php");
}

async function ShipmentFormSave(container, form, field, action) {

	var data = {
		action: action,
		value: field.val(),
	}

	return await FormSave(container, form, field, data, "config_shipment.php");
}

/**
  * Opens "deliveryminimo_valor" to edition.
  */
 $(document).on("click", ".shipment_bt_deliveryminimo_valor", async function() {

	ShipmentFormEdit($(this), $(this), 'shipment_deliveryminimo_valor_edit');
});

/**
  * Cancels "deliveryminimo_valor" edition.
  */
 $(document).on("focusout", "#frm_shipment_deliveryminimo_valor #frm_shipment_deliveryminimo_valor_field", async function() {

	ShipmentFormCancel($(this).closest('form'), $(this), 'shipment_deliveryminimo_valor_cancel');
});

/**
  * Saves "deliveryminimo_valor" edition.
  */
 $(document).on("submit", "#frm_shipment_deliveryminimo_valor", async function(event) {

	event.preventDefault();

	FormDisable($(this));

	ShipmentFormSave($(this), $(this), $(this.frm_shipment_deliveryminimo_valor_field), 'shipment_deliveryminimo_valor_save');
});

/**
  * Opens "frete gratis valor" edition.
  */
 $(document).on("click", ".shipment_bt_fretegratis_valor", async function() {

	ShipmentFormEdit($(this), $(this), 'shipment_fretegratis_valor_edit');
});

/**
  * Cancels "frete gratis valor" edition.
  */
 $(document).on("focusout", "#frm_shipment_fretegratis_valor #frm_shipment_fretegratis_valor_field", async function() {

	ShipmentFormCancel($(this).closest('form'), $(this), 'shipment_fretegratis_valor_cancel');
});

/**
  * Saves "frete gratis valor" edition.
  */
 $(document).on("submit", "#frm_shipment_fretegratis_valor", async function(event) {

	event.preventDefault();

	FormDisable($(this));

	ShipmentFormSave($(this), $(this), $(this.frm_shipment_fretegratis_valor_field), 'shipment_fretegratis_valor_save');
});

/**
 * Shows new freightCEP popup.
 */
$(document).on("click", ".freightcep_bt_new", async function() {

	let data = {
		action: "freightcep_show_new"
	}

	let response = await Post("config_shipment.php", data);

	if (response != null) {

		Modal.Show(Modal.POPUP_SIZE_SMALL, "Nova Área de Entrega", response, null, Modal.POPUP_BUTTONFIX);
	}

	// MenuClose();
});

/**
 * Shows CEP list without rules.
 */
$(document).on("click", ".freightcep_bt_list", async function() {

	let data = {
		action: "freightcep_list_norules"
	}

	let response = await Post("config_shipment.php", data);

	if (response != null) {

		Modal.Show(Modal.POPUP_SIZE_SMALL, "CEPs sem regra", response, null);
	}

	// MenuClose();
});

/**
  * Shows frete valor manager
  */
$(document).on("click", ".freightvalue_bt_manager", async function() {

	let button = $(this);

	Disable(button);

	let data = {
		action: 'freightvalue_manager',
	}

	let response = await Post("config_shipment.php", data);

	if (response != null) {

		Modal.Show(Modal.POPUP_SIZE_SMALL, "Taxa de Entrega", response, null, false, "<i class='icon fa-solid fa-pencil'></i>");
	}

	Enable(button);
	MenuClose();
});

/**
  * Adds "frete valor".
  */
$(document).on("submit", "#frm_freightvalue", async function(event) {

	event.preventDefault();

	let form = $(this);

	FormDisable(form);

	let data = {
		action: "freightvalue_new",
		descricao: this.descricao.value,
		valor: this.valor.value
	}

	let response = await Post("config_shipment.php", data);

	if (response != null) {

		let content = $(response["extra_block_freightvalue"]);

		$(".freightvalue_notfound").addClass("hidden");
		$(".freightvalue_container").append(content);
		$(".id_fretevalor").replaceWith(response["freight_cep_list"]);

		ContainerFocus(content);

		FormEnable(form);

		this.descricao.value = "";
		this.valor.value = "";
		this.descricao.focus();

	} else {

		FormEnable(form);
	}
});

/**
  * Deletes frete valor
  */
$(document).on("click", ".freightvalue_bt_del", async function() {

	let button = $(this);

	Disable(button);

	let data = {
		action: 'freightvalue_del',
		id_fretevalor: button.data("id_fretevalor")
	}

	let response = await Post("config_shipment.php", data);

	if (response != null) {

		let container = button.closest(".tr");

		ContainerRemove(container, function() {

			if ($(".freightvalue_tr").length == 0) {

				$(".freightvalue_notfound").removeClass("hidden");
			}
		});

		$(".id_fretevalor").replaceWith(response);

	} else {

		Enable(button);
	}
});

/**
  * Open Freight Value Description to edit
  */
$(document).on("click", ".freightvalue_bt_descricao", async function() {

	let button = $(this);

	Disable(button);

	let data = {
		action: 'freightvalue_descricao_edit',
		id_fretevalor: button.data("id_fretevalor")
	}

	let response = await Post("config_shipment.php", data);

	if (response != null) {

		let content = $(response);

		button.replaceWith(content);

		AutoFocus(content);

	} else {

		Enable(button);
	}
});

/**
  * Cancels Freight Value Description edition
  */
$(document).on("focusout", "#frm_freightvalue_descricao #field_freightvalue_descricao", async function() {

	//Prevents focusout on save
 	if ($(this).prop('disabled')) {
 		return;
 	}

	let form = $(this).closest("form");

	FormDisable(form);

	let data = {
		action: 'freightvalue_descricao_cancel',
		id_fretevalor: form.data("id_fretevalor")
	}

	let response = await Post("config_shipment.php", data);

	if (response != null) {

		let content = $(response);

		form.replaceWith(content);

	} else {

		FormEnable(form);
	}
});

/**
  * Saves Freight Value Description edition
  */
$(document).on("submit", "#frm_freightvalue_descricao", async function(event) {

	event.preventDefault();

	let form = $(this)

	FormDisable(form);

	let data = {
		action: 'freightvalue_descricao_save',
		id_fretevalor: form.data("id_fretevalor"),
		descricao: this.field_freightvalue_descricao.value
	}

	let response = await Post("config_shipment.php", data);

	if (response != null) {

		let content = $(response["data"]);

		form.replaceWith(content);

		$(".id_fretevalor").replaceWith(response["freight_cep_list"]);

	} else {

		FormEnable(form);
	}
});

/**
  * Open Freight Value Value to edit
  */
$(document).on("click", ".freightvalue_bt_valor", async function() {

	let button = $(this);

	Disable(button);

	let data = {
		action: 'freightvalue_valor_edit',
		id_fretevalor: button.data("id_fretevalor")
	}

	let response = await Post("config_shipment.php", data);

	if (response != null) {

		let content = $(response);

		button.replaceWith(content);

		AutoFocus(content);

	} else {

		Enable(button);
	}
});

/**
  * Cancels Freight Value Value edition
  */
$(document).on("focusout", "#frm_freightvalue_valor #field_freightvalue_valor", async function() {

	//Prevents focusout on save
 	if ($(this).prop('disabled')) {
 		return;
 	}

	let form = $(this).closest("form");

	FormDisable(form);

	let data = {
		action: 'freightvalue_valor_cancel',
		id_fretevalor: form.data("id_fretevalor")
	}

	let response = await Post("config_shipment.php", data);

	if (response != null) {

		let content = $(response);

		form.replaceWith(content);

	} else {

		FormEnable(form);
	}
});

/**
  * Saves Freight Value Value edition
  */
$(document).on("submit", "#frm_freightvalue_valor", async function(event) {

	event.preventDefault();

	let form = $(this)

	let id_fretevalor = form.data("id_fretevalor")

	FormDisable(form);

	let data = {
		action: 'freightvalue_valor_save',
		id_fretevalor: id_fretevalor,
		valor: this.field_freightvalue_valor.value
	}

	let response = await Post("config_shipment.php", data);

	if (response != null) {

		let content = $(response["data"]);

		form.replaceWith(content);

		$(".freightcep_valor_" + id_fretevalor).html(response["valor_formatted"]);
		$(".id_fretevalor").replaceWith(response["freight_cep_list"]);

	} else {

		FormEnable(form);
	}
});

/**
  * Saves New Freight CEP
  */
$(document).on("submit", "#frm_freightcep", async function(event) {

	event.preventDefault();

	let form = $(this)

	let cep_de = parseInt(this.field_freightcep_cep_de.value);
	let cep_ate = parseInt(this.field_freightcep_cep_ate.value);

	let popup = form.closest(".popup");

	if (cep_ate < cep_de) {

		this.field_freightcep_cep_ate.focus();
		Message.Show("Segundo CEP não pode ser menor que o primeiro CEP.", Message.MSG_ERROR);
		return;
	}

	FormDisable(form);

	let data = {
		action: 'freightcep_new',
		descricao: this.field_freightcep_descricao.value,
		cep_de: this.field_freightcep_cep_de.value,
		cep_ate: this.field_freightcep_cep_ate.value,
		id_fretevalor: this.field_freightcep_id_fretevalor.value

	}

	let response = await Post("config_shipment.php", data);

	if (response != null) {

		$(".freightcep_none").addClass("hidden");

		let content = $(response);

		$(".freightcep_container").append(content);

		ContainerFocus(content);

		if (popup.find('.popup_fixwindow').hasClass('fa-thumbtack')) {

			Modal.Close(popup);

		} else {

			FormEnable(form);
			this.field_freightcep_descricao.value = "";
			this.field_freightcep_cep_de.value = "";
			this.field_freightcep_cep_ate.value = "";
			AutoFocus(form);
		}

	} else {

		FormEnable(form);
	}
});

/**
  * Deletes frete valor
  */
$(document).on("click", ".freightcep_bt_del", async function() {

	let button = $(this);

	Disable(button);

	let yes = async function() {

		let data = {
			action: 'freightcep_del',
			id_fretecep: button.data("id_fretecep")
		}

		let response = await Post("config_shipment.php", data);

		if (response != null) {

			let container = button.closest(".tr");

			ContainerRemove(container, function() {

				if ($(".freightcep_tr").length == 0) {

					$(".freightcep_none").removeClass("hidden");
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

	MessageBox.Show("Confirma a remoção da faixa de CEP?", yes, no);

});

/**
  * Activates/Desactivates frete valor
  */
$(document).on("click", ".freightcep_bt_ativo", async function() {

	let button = $(this);

	Disable(button);

	let data = {
		action: 'freightcep_ativo',
		id_fretecep: button.data("id_fretecep")
	}

	let response = await Post("config_shipment.php", data);

	Enable(button);

	if (response != null) {

		button.prop("checked", response);
	}
});

/**
  * Opens freight cep "descricao" to edition.
  */
$(document).on("click", ".freightcep_bt_descricao", async function() {

	let button = $(this);

	Disable(button);

	let data = {
		action: "fretecep_descricao_edit",
		id_fretecep: button.data("id_fretecep"),
	}

	let response = await Post("config_shipment.php", data);

	if (response != null) {

		let content = $(response);

		button.parent().replaceWith(content);

		AutoFocus(content);

	} else {

		Enable(button);
	}
});

/**
  * Cancels freight cep "descricao" edition.
  */
$(document).on("focusout", "#frm_freightcep_descricao #frm_freightcep_descricao_field", async function() {

	//Prevents focusout on save
 	if ($(this).prop('disabled')) {
 		return;
 	}

	let form = $(this).closest("form");

	FormDisable(form);

	let data = {
		action: "freightcep_descricao_cancel",
		id_fretecep: form.data("id_fretecep"),
	}

	let response = await Post("config_shipment.php", data);

	if (response != null) {

		form.replaceWith(response);

	} else {

		FormEnable(form);
	}
});

/**
  * Saves freight cep "descricao" edition.
  */
$(document).on("submit", "#frm_freightcep_descricao", async function(event) {

	event.preventDefault();

	let form = $(this);

	FormDisable(form);

	let data = {
		action: "freightcep_descricao_save",
		id_fretecep: form.data("id_fretecep"),
		descricao: this.frm_freightcep_descricao_field.value
	}

	let response = await Post("config_shipment.php", data);

	if (response != null) {

		form.replaceWith(response);

	} else {

		FormEnable(form);
	}
});

/**
  * Opens freight cep "cep_de" to edition.
  */
$(document).on("click", ".freightcep_bt_cep_de", async function() {

	let button = $(this);

	Disable(button);

	let data = {
		action: "fretecep_cep_de_edit",
		id_fretecep: button.data("id_fretecep"),
	}

	let response = await Post("config_shipment.php", data);

	if (response != null) {

		let content = $(response);

		button.replaceWith(content);

		AutoFocus(content);

	} else {

		Enable(button);
	}
});

/**
  * Cancels freight cep "cep_de" edition.
  */
$(document).on("focusout", "#frm_freightcep_cep_de #frm_freightcep_cep_de_field", async function() {

	//Prevents focusout on save
 	if ($(this).prop('disabled')) {
 		return;
 	}

	let form = $(this).closest("form");

	FormDisable(form);

	let data = {
		action: "freightcep_cep_de_cancel",
		id_fretecep: form.data("id_fretecep"),
	}

	let response = await Post("config_shipment.php", data);

	if (response != null) {

		form.replaceWith(response);

	} else {

		FormEnable(form);
	}
});

/**
  * Saves freight cep "cep_de" edition.
  */
$(document).on("submit", "#frm_freightcep_cep_de", async function(event) {

	event.preventDefault();

	let form = $(this);

	FormDisable(form);

	let data = {
		action: "freightcep_cep_de_save",
		id_fretecep: form.data("id_fretecep"),
		cep_de: this.frm_freightcep_cep_de_field.value
	}

	let response = await Post("config_shipment.php", data);

	if (response != null) {

		form.replaceWith(response);

	} else {

		FormEnable(form);
		AutoFocus(form);
	}
});

/**
  * Opens freight cep "cep_ate" to edition.
  */
$(document).on("click", ".freightcep_bt_cep_ate", async function() {

	let button = $(this);

	Disable(button);

	let data = {
		action: "fretecep_cep_ate_edit",
		id_fretecep: button.data("id_fretecep"),
	}

	let response = await Post("config_shipment.php", data);

	if (response != null) {

		let content = $(response);

		button.replaceWith(content);

		AutoFocus(content);

	} else {

		Enable(button);
	}
});

/**
  * Cancels freight cep "cep_ate" edition.
  */
$(document).on("focusout", "#frm_freightcep_cep_ate #frm_freightcep_cep_ate_field", async function() {

	//Prevents focusout on save
 	if ($(this).prop('disabled')) {
 		return;
 	}

	let form = $(this).closest("form");

	FormDisable(form);

	let data = {
		action: "freightcep_cep_ate_cancel",
		id_fretecep: form.data("id_fretecep"),
	}

	let response = await Post("config_shipment.php", data);

	if (response != null) {

		form.replaceWith(response);

	} else {

		FormEnable(form);
	}
});

/**
  * Saves freight cep "cep_ate" edition.
  */
$(document).on("submit", "#frm_freightcep_cep_ate", async function(event) {

	event.preventDefault();

	let form = $(this);

	FormDisable(form);

	let data = {
		action: "freightcep_cep_ate_save",
		id_fretecep: form.data("id_fretecep"),
		cep_ate: this.frm_freightcep_cep_ate_field.value
	}

	let response = await Post("config_shipment.php", data);

	if (response != null) {

		form.replaceWith(response);

	} else {

		FormEnable(form);
		AutoFocus(form);
	}
});

/**
  * Opens freight cep "valor" to choose.
  */
$(document).on("click", ".freightcep_bt_valor", async function() {

	let button = $(this);

	Disable(button);

	let data = {
		action: "fretecep_valor_edit",
		id_fretecep: button.data("id_fretecep"),
	}

	let response = await Post("config_shipment.php", data);

	if (response != null) {

		let content = $(response);

		button.replaceWith(content);

		AutoFocus(content);

	} else {

		Enable(button);
	}
});

/**
  * Cancels Freight Value Value edition
  */
$(document).on("focusout", "#frm_freightcep_valor #frm_freightcep_valor_field", async function() {

	//Prevents focusout on save
 	if ($(this).prop('disabled')) {
 		return;
 	}

	let form = $(this).closest("form");

	FormDisable(form);

	let data = {
		action: 'freightcep_valor_cancel',
		id_fretecep: form.data("id_fretecep")
	}

	let response = await Post("config_shipment.php", data);

	if (response != null) {

		let content = $(response);

		form.replaceWith(content);

	} else {

		FormEnable(form);
	}
});

/**
  * Saves freight value value edition.
  */
 $(document).on("change", "#frm_freightcep_valor", async function(event) {

	event.preventDefault();

	let form = $(this);

	FormDisable(form);

	let data = {
		action: 'freightcep_valor_save',
		id_fretecep: form.data("id_fretecep"),
		id_fretevalor: this.frm_freightcep_valor_field.value
	}

	let response = await Post("config_shipment.php", data);

	if (response != null) {

		let content = $(response);

		form.replaceWith(content);

	} else {

		FormEnable(form);
	}
});

/**
  * Activates/Desactivates fretegratis
  */
$(document).on("click", ".freight_bt_fretegratis", async function() {

	let button = $(this);

	Disable(button);

	let data = {
		action: 'freight_fretegratis',
	}

	let response = await Post("config_shipment.php", data);

	Enable(button);

	if (response != null) {

		button.prop("checked", response);

		if (response == "") {

			$(".shipment_bt_fretegratis_valor").prop("disabled", "disabled");

		} else {

			$(".shipment_bt_fretegratis_valor").prop("disabled", "");
		}
	}
});

/**
  * Activates/Desactivates deliveryminimo
  */
$(document).on("click", ".freight_bt_deliveryminimo", async function() {

	let button = $(this);

	Disable(button);

	let data = {
		action: 'freight_deliveryminimo',
	}

	let response = await Post("config_shipment.php", data);

	Enable(button);

	if (response != null) {

		button.prop("checked", response);

		if (response == "") {

			$(".shipment_bt_deliveryminimo_valor").prop("disabled", "disabled");

		} else {

			$(".shipment_bt_deliveryminimo_valor").prop("disabled", "");
		}
	}
});

/**
  * Adds cep with no rules
  */
$(document).on("click", ".freightcep_bt_cep_norule", async function() {

	let button = $(this);

	Disable(button);

	let data = {
		action: 'freightcep_show_new',
		cep: button.data("cep")
	}

	let response = await Post("config_shipment.php", data);

	if (response != null) {

		Modal.Show(Modal.POPUP_SIZE_SMALL, "Nova Área de Entrega", response, null, Modal.POPUP_BUTTONFIX);

		button.closest(".freightcep_norules_container").remove();

	} else {

		Enable(button);
	}
});

/**
  * Shows cep address
  */
$(document).on("click", ".freightcep_bt_cep_address", async function() {

	let button = $(this);

	Disable(button);

	let data = {
		action: 'freightcep_show_address',
		cep: button.data("cep")
	}

	let response = await Post("config_shipment.php", data);

	if (response != null) {

		Modal.Show(Modal.POPUP_SIZE_LARGE, "Endereços Cadastrados", response, null);

		// button.closest(".freightcep_norules_container").remove();

	} //else {

		Enable(button);
		MenuClose(button);
	// }
});

/**
  * CEP search for description
  */
$(document).on("click", ".freightvalue_bt_cepsearch", async function() {

	let button = $(this);

	Disable(button);

	let cep = await CEPSearch($("#field_freightcep_cep_ate").val());

	if (cep != null) {

		if (cep.complemento != "") {

			$("#field_freightcep_descricao").val(cep.localidade + " - " + cep.bairro + " - " + cep.logradouro + " (" + cep.complemento + ")");

		} else {

			$("#field_freightcep_descricao").val(cep.localidade + " - " + cep.bairro + " - " + cep.logradouro);
		}
	}

	Enable(button);
});
