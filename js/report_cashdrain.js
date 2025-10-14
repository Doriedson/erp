function CashdrainCalcTotal() {


	// let cashdrain = $('.reportcashdrain_table');

	let cashdrain_total = 0;
	let cashadd_total = 0;

	$(".cashdrain_tr").each(

		function(index, data) {

			cashdrain_total += parseFloat($(data).data('valor'));
		}
	);

	$('.cashdrain_total').html(cashdrain_total.toLocaleString("pt-BR", {minimumFractionDigits: 2}));

	$(".cashadd_tr").each(

		function(index, data) {

			cashadd_total += parseFloat($(data).data('valor'));
		}
	);

	$('.cashadd_total').html(cashadd_total.toLocaleString("pt-BR", {minimumFractionDigits: 2}));
}

async function CashdrainFormEdit(container, button, action) {

	data = {
		action: action,
		id_caixasangria: button.data("id_caixasangria"),
	}

	return await FormEdit(container, button, data, "report_cashdrain.php");
}

async function CashdrainFormCancel(container, field, action) {

	var form = field.closest('form');

	var id_caixasangria = form.data('id_caixasangria');

	data = {
		action: action,
		id_caixasangria: id_caixasangria,
	}

	return await FormCancel(container, form, field, data, "report_cashdrain.php");
}

async function CashdrainFormSave(container, form, field, action) {

	var data = {
		action: action,
		id_caixasangria: form.data('id_caixasangria'),
		value: field.val(),
	}

	return await FormSave(container, form, field, data, "report_cashdrain.php");
}

async function CashaddFormEdit(container, button, action) {

	data = {
		action: action,
		id_caixareforco: button.data("id_caixareforco"),
	}

	return await FormEdit(container, button, data, "report_cashdrain.php");
}

async function CashaddFormCancel(container, field, action) {

	var form = field.closest('form');

	var id_caixareforco = form.data('id_caixareforco');

	data = {
		action: action,
		id_caixareforco: id_caixareforco,
	}

	return await FormCancel(container, form, field, data, "report_cashdrain.php");
}

async function CashaddFormSave(container, form, field, action) {

	var data = {
		action: action,
		id_caixareforco: form.data('id_caixareforco'),
		value: field.val(),
	}

	return await FormSave(container, form, field, data, "report_cashdrain.php");
}

/**
 * Enables/Disables second date field for search.
 */
 $(document).on("click", "#frm_report_cashdrain #intervalo", function() {

	$("#frm_report_cashdrain #datafim").prop( "disabled", !this.checked);
});

/**
  * Defines datafim as minimum from dataini.
  */
$(document).on("change", "#frm_report_cashdrain #dataini", function() {

	$("#frm_report_cashdrain #datafim").prop({min: this.value});
});

/**
  * Search report stock in from date / date interval.
  */
$(document).on("submit", "#frm_report_cashdrain", async function(event) {

  	event.preventDefault();

	let form = $(this);

	// let datafim = $(this.datafim).prop('disabled');

	FormDisable(form);

  	let data = {
		action: 'report_cashdrain_search',
		dataini: this.dataini.value,
		intervalo: this.intervalo.checked,
		datafim: this.datafim.value,
	}

	$(".reportcashdrain_notfound").addClass('hidden');
	$(".reportcashdrain_container").html("");

	let response = await Post("report_cashdrain.php", data);

	if (response != null) {

		$(".reportcashdrain_container").html(response);
	}

	FormEnable(form);
});

/**
  * Opens "obs" edition
  */
 $(document).on("click", ".cashdrain_bt_obs", async function() {

	CashdrainFormEdit($(this), $(this), "cashdrain_obs_edit");
});

/**
  * Cancels "obs" edition.
  */
$(document).on("focusout", "#frm_cashdrain_obs #obs", async function() {

	CashdrainFormCancel($(this).closest('form'), $(this), "cashdrain_obs_cancel");
});

/**
  * Saves "obs" edition.
  */
 $(document).on("submit", "#frm_cashdrain_obs", async function(event) {

	event.preventDefault();

	let form = $(this);

	let id_caixasangria = form.data("id_caixasangria");

	FormDisable(form);

	let response = await CashdrainFormSave(form, form, $(this.obs), "cashdrain_obs_save");

	if (response != null) {

		$(".cashdrain_tr_" + id_caixasangria).replaceWith(response["cashdrain"]);
	}

});

/**
  * Opens "especie" edition
  */
 $(document).on("click", ".cashdrain_bt_especie", async function() {

	CashdrainFormEdit($(this), $(this), "cashdrain_especie_edit");
});

/**
  * Cancels "especie" edition.
  */
$(document).on("focusout", "#frm_cashdrain_especie #id_especie", async function() {

	CashdrainFormCancel($(this).closest('form'), $(this), "cashdrain_especie_cancel");
});

/**
  * Saves "especie" edition.
  */
 $(document).on("change", "#frm_cashdrain_especie", async function(event) {

	event.preventDefault();

	let form = $(this);

	let id_caixasangria = form.data("id_caixasangria");

	FormDisable(form);

	let response = await CashdrainFormSave(form, form, $(this.id_especie), "cashdrain_especie_save");

	if (response != null) {

		$(".cashdrain_tr_" + id_caixasangria).replaceWith(response["cashdrain"]);
	}
});

/**
  * Opens "valor" edition
  */
 $(document).on("click", ".cashdrain_bt_valor", async function() {

	CashdrainFormEdit($(this), $(this), "cashdrain_valor_edit");
});

/**
  * Cancels "valor" edition.
  */
$(document).on("focusout", "#frm_cashdrain_valor #valor", async function() {

	CashdrainFormCancel($(this).closest('form'), $(this), "cashdrain_valor_cancel");
});

/**
  * Saves "valor" edition.
  */
 $(document).on("submit", "#frm_cashdrain_valor", async function(event) {

	event.preventDefault();

	let form = $(this);

	let id_caixasangria = form.data("id_caixasangria");

	FormDisable(form);

	let response = await CashdrainFormSave(form, form, $(this.valor), "cashdrain_valor_save");

	if (response != null) {

		$(".cashdrain_tr_" + id_caixasangria).replaceWith(response["cashdrain"]);
		CashdrainCalcTotal();
	}
});

/**
  * Opens "valor" edition
  */
$(document).on("click", ".cashadd_bt_valor", async function() {

	CashaddFormEdit($(this), $(this), "cashadd_valor_edit");
});

/**
  * Cancels "valor" edition.
  */
$(document).on("focusout", "#frm_cashadd_valor #valor", async function() {

	CashaddFormCancel($(this).closest('form'), $(this), "cashadd_valor_cancel");
});

/**
  * Saves "valor" edition.
  */
 $(document).on("submit", "#frm_cashadd_valor", async function(event) {

	event.preventDefault();

	let form = $(this);

	let id_caixareforco = form.data("id_caixareforco")

	FormDisable(form);

	let response = await CashaddFormSave(form, form, $(this.valor), "cashadd_valor_save");

	if (response != null) {

		$(".cashadd_tr_" + id_caixareforco).replaceWith(response["cashadd"]);
		CashdrainCalcTotal();
	}
});

/**
  * Toggle unchecked cashdrain
  */
$(document).on("click", ".cashdrain_bt_checked", async function() {

	var button = $(this);

	var id_caixasangria = button.closest('.cashdrain_tr').data('id_caixasangria');

	Disable(button);

	data = {
		action: "cashdrain_uncheck",
		id_caixasangria: id_caixasangria,
	}

	let response = await Post("report_cashdrain.php", data);

	if (response != null) {

		button.replaceWith(response);

	} else {

		Enable(button);
	}
});

/**
  * Toggle checked cashdrain
  */
 $(document).on("click", ".cashdrain_bt_unchecked", async function() {

	var button = $(this);

	var id_caixasangria = button.closest('.cashdrain_tr').data('id_caixasangria');

	Disable(button);

	data = {
		action: "cashdrain_check",
		id_caixasangria: id_caixasangria,
	}

	let response = await Post("report_cashdrain.php", data);

	if (response != null) {

		button.replaceWith(response);

	} else {

		Enable(button);
	}
});

/**
  * Opens CashDrain Popup for edition
  */
$(document).on("click", ".bt_cashdrain_edit", async function() {

	let button = $(this);

	let id_caixasangria = button.data('id_caixasangria');

	Disable(button);

	let data = {
		action: "cashdrain_edit",
		id_caixasangria: id_caixasangria,
	}

	let response = await Post("report_cashdrain.php", data);

	if (response != null) {

		Modal.Show(Modal.POPUP_SIZE_SMALL, "Edição de Sangria", response, null);
	}

	Enable(button);
	MenuClose();
});

/**
  * Opens CashAdd Popup for edition
  */
$(document).on("click", ".bt_cashadd_edit", async function() {

	let button = $(this);

	let id_caixareforco = button.data('id_caixareforco');

	Disable(button);

	let data = {
		action: "cashadd_edit",
		id_caixareforco: id_caixareforco,
	}

	let response = await Post("report_cashdrain.php", data);

	if (response != null) {

		Modal.Show(Modal.POPUP_SIZE_SMALL, "Edição de Reforço", response, null);
	}

	Enable(button);
	MenuClose();
});

/**
  * Checks CashDrain
  */
$(document).on("click", ".bt_cashdrain_check", async function() {

	let button = $(this);

	let id_caixasangria = button.data('id_caixasangria');

	Disable(button);

	let data = {
		action: "cashdrain_check",
		id_caixasangria: id_caixasangria,
	}

	let response = await Post("report_cashdrain.php", data);

	if (response != null) {

		$(".cashdrain_tr_" + id_caixasangria).replaceWith(response);

	} else {

		Enable(button);
		MenuClose();
	}
});

/**
  * Checks CashAdd
  */
$(document).on("click", ".bt_cashadd_check", async function() {

	let button = $(this);

	let id_caixareforco = button.data('id_caixareforco');

	Disable(button);

	let data = {
		action: "cashadd_check",
		id_caixareforco: id_caixareforco,
	}

	let response = await Post("report_cashdrain.php", data);

	if (response != null) {

		$(".cashadd_tr_" + id_caixareforco).replaceWith(response);

	} else {

		Enable(button);
		MenuClose();
	}
});
