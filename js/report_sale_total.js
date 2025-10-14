async function ReportPDVFormEdit(container, button, action) {

	data = {
		action: action,
		id_caixa: button.data("id_caixa"),
		change_type: button.data("change_type"),
	}

	return await FormEdit(container, button, data, "report_sale_total.php");
}

async function ReportPDVFormCancel(container, field, action) {

	var form = field.closest('form');

	var id_caixa = form.data('id_caixa');

	data = {
		action: action,
		id_caixa: id_caixa,
	}

	return await FormCancel(container, form, field, data, "report_sale_total.php");
}

async function ReportPDVFormSave(container, form, field, action) {

	let id_caixa = form.data('id_caixa');

	let data = {
		action: action,
		id_caixa: id_caixa,
		value: field.val(),
	}

	let response = await FormSave(container, form, field, data, "report_sale_total.php");

	if (response != null) {

		$('.w_reportpdv_' + id_caixa).replaceWith(response['report']);
	}

	return response;
}

/**
 * Enables/Disables second date field for search.
 */
 $(document).on("click", "#frm_report_sale_total #intervalo", function() {

	$("#frm_report_sale_total #datafim").prop( "disabled", !this.checked);
	$("#frm_report_sale_total #pdv").prop( "disabled", this.checked).closest("span:first").addClass('disabled');

	if (this.checked) {

		$("#frm_report_sale_total #pdv").prop("checked", false);
	}
});

/**
  * Defines datafim as minimum from dataini.
  */
$(document).on("change", "#frm_report_sale_total #dataini", function() {

	$("#frm_report_sale_total #datafim").prop({min: this.value});
});

/**
  * Search report total sales from date / date interval.
  */
$(document).on("submit", "#frm_report_sale_total", async function(event) {

  	event.preventDefault();

	let form = $(this);

	FormDisable(form);

  	let data = {
		action: 'report_sale_total_search',
		dataini: this.dataini.value,
		intervalo: this.intervalo.checked,
		datafim: this.datafim.value,
		pdv: this.pdv.checked,
	}

	$(".w_pdvreport_none").addClass("hidden");
	$(".w_pdvreport_notfound").addClass("hidden");
	$(".w_pdvreport_container").html("<div class='box-container fill' style='padding: 40px 10px;'>" + imgLoading + "</div>");

	let response = await Post("report_sale_total.php", data);

	if (response != null) {

		$(".w_pdvreport_container").html(response);

	} else {

		$(".w_pdvreport_notfound").removeClass("hidden");
		$(".w_pdvreport_container").html("");
	}

	FormEnable(form);
});


/**
 * Opens cashchange from trocofim
 */
 $(document).on("click", ".bt_trocofim_detalhado", async function() {

	let button = $(this);

	let id_caixa = button.data('id_caixa');

	// let nome = button.closest('.card-container').find('.nome_' + id_caixa).html();

	Disable(button);

	let data = {
		action: 'report_sale_total_gettrocofim',
		id_caixa: id_caixa
	}

	let response = await Post("report_sale_total.php", data);

	if (response != null) {

		Modal.Show(Modal.POPUP_SIZE_SMALL, "Fundo de Caixa - Fechamento", response, null);
	}

	Enable(button);
});

/**
 * Opens trocoini edition.
 */
$(document).on("click", ".bt_trocoini", async function() {

	ReportPDVFormEdit($(this), $(this), "reportpdv_trocoini_edit");
});

/**
 * Cancels trocoini edition.
 */
$(document).on("focusout", "#frm_trocoini #trocoini", async function() {

	ReportPDVFormCancel($(this).closest('form'), $(this), "reportpdv_trocoini_cancel");
});

/**
  * Saves trocoini edition.
  */
$(document).on("submit", "#frm_trocoini", async function(event) {

	event.preventDefault();

	ReportPDVFormSave($(this), $(this), $(this.trocoini), "reportpdv_trocoini_save");
});

/**
 * Opens trocofim edition.
 */
$(document).on("click", ".bt_trocofim", async function() {

	ReportPDVFormEdit($(this), $(this), "reportpdv_trocofim_edit");
});

/**
 * Cancels trocofim edition.
 */
$(document).on("focusout", "#frm_trocofim #trocofim", async function() {

	ReportPDVFormCancel($(this).closest('form'), $(this), "reportpdv_trocofim_cancel");
});

/**
  * Saves trocofim edition.
  */
 $(document).on("submit", "#frm_trocofim", async function(event) {

	event.preventDefault();

	ReportPDVFormSave($(this), $(this), $(this.trocofim), "reportpdv_trocofim_save");
});

/**
 * Opens moeda_1 edition.
 */
 $(document).on("click", ".bt_moeda_1", async function() {

	ReportPDVFormEdit($(this), $(this), "reportpdv_moeda_1_edit");
});

/**
 * Cancels moeda_1 edition.
 */
 $(document).on("focusout", "#frm_moeda_1 #moeda_1", async function() {

	ReportPDVFormCancel($(this).closest('form'), $(this), "reportpdv_moeda_1_cancel");
});

/**
  * Saves moeda_1 edition.
  */
 $(document).on("submit", "#frm_moeda_1", async function(event) {

	event.preventDefault();

	response = await ReportPDVFormSave($(this), $(this), $(this.moeda_1), "reportpdv_moeda_1_save");

	if (response != null) {

		$('.trocofim_' + $(this).data('id_caixa')).html(response['trocofim_formatted']);
	}
});

/**
 * Opens moeda_5 edition.
 */
 $(document).on("click", ".bt_moeda_5", async function() {

	ReportPDVFormEdit($(this), $(this), "reportpdv_moeda_5_edit");
});

/**
 * Cancels moeda_5 edition.
 */
 $(document).on("focusout", "#frm_moeda_5 #moeda_5", async function() {

	ReportPDVFormCancel($(this).closest('form'), $(this), "reportpdv_moeda_5_cancel");
});

/**
  * Saves moeda_5 edition.
  */
 $(document).on("submit", "#frm_moeda_5", async function(event) {

	event.preventDefault();

	response = await ReportPDVFormSave($(this), $(this), $(this.moeda_5), "reportpdv_moeda_5_save");

	if (response != null) {

		$('.trocofim_' + $(this).data('id_caixa')).html(response['trocofim_formatted']);
	}
});

/**
 * Opens moeda_10 edition.
 */
 $(document).on("click", ".bt_moeda_10", async function() {

	ReportPDVFormEdit($(this), $(this), "reportpdv_moeda_10_edit");
});

/**
 * Cancels moeda_10 edition.
 */
 $(document).on("focusout", "#frm_moeda_10 #moeda_10", async function() {

	ReportPDVFormCancel($(this).closest('form'), $(this), "reportpdv_moeda_10_cancel");
});

/**
  * Saves moeda_10 edition.
  */
 $(document).on("submit", "#frm_moeda_10", async function(event) {

	event.preventDefault();

	response = await ReportPDVFormSave($(this), $(this), $(this.moeda_10), "reportpdv_moeda_10_save");

	if (response != null) {

		$('.trocofim_' + $(this).data('id_caixa')).html(response['trocofim_formatted']);
	}
});

/**
 * Opens moeda_25 edition.
 */
 $(document).on("click", ".bt_moeda_25", async function() {

	ReportPDVFormEdit($(this), $(this), "reportpdv_moeda_25_edit");
});

/**
 * Cancels moeda_25 edition.
 */
 $(document).on("focusout", "#frm_moeda_25 #moeda_25", async function() {

	ReportPDVFormCancel($(this).closest('form'), $(this), "reportpdv_moeda_25_cancel");
});

/**
  * Saves moeda_25 edition.
  */
 $(document).on("submit", "#frm_moeda_25", async function(event) {

	event.preventDefault();

	response = await ReportPDVFormSave($(this), $(this), $(this.moeda_25), "reportpdv_moeda_25_save");

	if (response != null) {

		$('.trocofim_' + $(this).data('id_caixa')).html(response['trocofim_formatted']);
	}
});

/**
 * Opens moeda_50 edition.
 */
 $(document).on("click", ".bt_moeda_50", async function() {

	ReportPDVFormEdit($(this), $(this), "reportpdv_moeda_50_edit");
});

/**
 * Cancels moeda_50 edition.
 */
 $(document).on("focusout", "#frm_moeda_50 #moeda_50", async function() {

	ReportPDVFormCancel($(this).closest('form'), $(this), "reportpdv_moeda_50_cancel");
});

/**
  * Saves moeda_50 edition.
  */
 $(document).on("submit", "#frm_moeda_50", async function(event) {

	event.preventDefault();

	response = await ReportPDVFormSave($(this), $(this), $(this.moeda_50), "reportpdv_moeda_50_save");

	if (response != null) {

		$('.trocofim_' + $(this).data('id_caixa')).html(response['trocofim_formatted']);
	}
});

/**
 * Opens cedula_1 edition.
 */
 $(document).on("click", ".bt_cedula_1", async function() {

	ReportPDVFormEdit($(this), $(this), "reportpdv_cedula_1_edit");
});

/**
 * Cancels cedula_1 edition.
 */
 $(document).on("focusout", "#frm_cedula_1 #cedula_1", async function() {

	ReportPDVFormCancel($(this).closest('form'), $(this), "reportpdv_cedula_1_cancel");
});

/**
  * Saves cedula_1 edition.
  */
 $(document).on("submit", "#frm_cedula_1", async function(event) {

	event.preventDefault();

	response = await ReportPDVFormSave($(this), $(this), $(this.cedula_1), "reportpdv_cedula_1_save");

	if (response != null) {

		$('.trocofim_' + $(this).data('id_caixa')).html(response['trocofim_formatted']);
	}
});

/**
 * Opens cedula_2 edition.
 */
 $(document).on("click", ".bt_cedula_2", async function() {

	ReportPDVFormEdit($(this), $(this), "reportpdv_cedula_2_edit");
});

/**
 * Cancels cedula_2 edition.
 */
 $(document).on("focusout", "#frm_cedula_2 #cedula_2", async function() {

	ReportPDVFormCancel($(this).closest('form'), $(this), "reportpdv_cedula_2_cancel");
});

/**
  * Saves cedula_2 edition.
  */
 $(document).on("submit", "#frm_cedula_2", async function(event) {

	event.preventDefault();

	response = await ReportPDVFormSave($(this), $(this), $(this.cedula_2), "reportpdv_cedula_2_save");

	if (response != null) {

		$('.trocofim_' + $(this).data('id_caixa')).html(response['trocofim_formatted']);
	}
});

/**
 * Opens cedula_5 edition.
 */
 $(document).on("click", ".bt_cedula_5", async function() {

	ReportPDVFormEdit($(this), $(this), "reportpdv_cedula_5_edit");
});

/**
 * Cancels cedula_5 edition.
 */
 $(document).on("focusout", "#frm_cedula_5 #cedula_5", async function() {

	ReportPDVFormCancel($(this).closest('form'), $(this), "reportpdv_cedula_5_cancel");
});

/**
  * Saves cedula_5 edition.
  */
 $(document).on("submit", "#frm_cedula_5", async function(event) {

	event.preventDefault();

	response = await ReportPDVFormSave($(this), $(this), $(this.cedula_5), "reportpdv_cedula_5_save");

	if (response != null) {

		$('.trocofim_' + $(this).data('id_caixa')).html(response['trocofim_formatted']);
	}
});

/**
 * Opens cedula_10 edition.
 */
 $(document).on("click", ".bt_cedula_10", async function() {

	ReportPDVFormEdit($(this), $(this), "reportpdv_cedula_10_edit");
});

/**
 * Cancels cedula_10 edition.
 */
 $(document).on("focusout", "#frm_cedula_10 #cedula_10", async function() {

	ReportPDVFormCancel($(this).closest('form'), $(this), "reportpdv_cedula_10_cancel");
});

/**
  * Saves cedula_10 edition.
  */
 $(document).on("submit", "#frm_cedula_10", async function(event) {

	event.preventDefault();

	response = await ReportPDVFormSave($(this), $(this), $(this.cedula_10), "reportpdv_cedula_10_save");

	if (response != null) {

		$('.trocofim_' + $(this).data('id_caixa')).html(response['trocofim_formatted']);
	}
});

/**
 * Opens cedula_20 edition.
 */
 $(document).on("click", ".bt_cedula_20", async function() {

	ReportPDVFormEdit($(this), $(this), "reportpdv_cedula_20_edit");
});

/**
 * Cancels cedula_20 edition.
 */
 $(document).on("focusout", "#frm_cedula_20 #cedula_20", async function() {

	ReportPDVFormCancel($(this).closest('form'), $(this), "reportpdv_cedula_20_cancel");
});

/**
  * Saves cedula_20 edition.
  */
 $(document).on("submit", "#frm_cedula_20", async function(event) {

	event.preventDefault();

	response = await ReportPDVFormSave($(this), $(this), $(this.cedula_20), "reportpdv_cedula_20_save");

	if (response != null) {

		$('.trocofim_' + $(this).data('id_caixa')).html(response['trocofim_formatted']);
	}
});

/**
 * Opens cedula_50 edition.
 */
 $(document).on("click", ".bt_cedula_50", async function() {

	ReportPDVFormEdit($(this), $(this), "reportpdv_cedula_50_edit");
});

/**
 * Cancels cedula_50 edition.
 */
 $(document).on("focusout", "#frm_cedula_50 #cedula_50", async function() {

	ReportPDVFormCancel($(this).closest('form'), $(this), "reportpdv_cedula_50_cancel");
});

/**
  * Saves cedula_50 edition.
  */
 $(document).on("submit", "#frm_cedula_50", async function(event) {

	event.preventDefault();

	response = await ReportPDVFormSave($(this), $(this), $(this.cedula_50), "reportpdv_cedula_50_save");

	if (response != null) {

		$('.trocofim_' + $(this).data('id_caixa')).html(response['trocofim_formatted']);
	}
});

/**
 * Opens cedula_100 edition.
 */
 $(document).on("click", ".bt_cedula_100", async function() {

	ReportPDVFormEdit($(this), $(this), "reportpdv_cedula_100_edit");
});

/**
 * Cancels cedula_100 edition.
 */
 $(document).on("focusout", "#frm_cedula_100 #cedula_100", async function() {

	ReportPDVFormCancel($(this).closest('form'), $(this), "reportpdv_cedula_100_cancel");
});

/**
  * Saves cedula_100 edition.
  */
 $(document).on("submit", "#frm_cedula_100", async function(event) {

	event.preventDefault();

	response = await ReportPDVFormSave($(this), $(this), $(this.cedula_100), "reportpdv_cedula_100_save");

	if (response != null) {

		$('.trocofim_' + $(this).data('id_caixa')).html(response['trocofim_formatted']);
	}
});

/**
 * Opens cedula_200 edition.
 */
 $(document).on("click", ".bt_cedula_200", async function() {

	ReportPDVFormEdit($(this), $(this), "reportpdv_cedula_200_edit");
});

/**
 * Cancels cedula_200 edition.
 */
 $(document).on("focusout", "#frm_cedula_200 #cedula_200", async function() {

	ReportPDVFormCancel($(this).closest('form'), $(this), "reportpdv_cedula_200_cancel");
});

/**
  * Saves cedula_200 edition.
  */
 $(document).on("submit", "#frm_cedula_200", async function(event) {

	event.preventDefault();

	response = await ReportPDVFormSave($(this), $(this), $(this.cedula_200), "reportpdv_cedula_200_save");

	if (response != null) {

		$('.trocofim_' + $(this).data('id_caixa')).html(response['trocofim_formatted']);
	}
});

/**
  * Saves "obs" to caixa.
  */
$(document).on("submit", ".frm_pdvreport_obs", async function(event) {

	event.preventDefault();

	let form = $(this);

	let container = form.closest('.w_pdvreport_obs');

	let obs = form.find(".field_obs").val();
	let id_caixa = form.data('id_caixa');

	data = {
		action: 'pdvreport_obs_save',
		id_caixa: id_caixa,
		obs: obs,
	}

	FormDisable(form);

	let response = await Post("report_sale_total.php", data);

	if (response != null) {

		container.replaceWith(response);

	} else {

		FormEnable(form);
	}
});

$(document).on("click", ".bt_pdvreport_closeview", async function() {

	let button = $(this);

	Disable(button);

	let id_caixa = button.data("id_caixa");

	let data = {
		action: "pdvreport_closeview",
		id_caixa: id_caixa
	}

	let response = await Post("api.php", data);

	if (response != null) {

		Modal.Show(Modal.POPUP_SIZE_SMALL, "Fechamento de Caixa", response, null, false, "<i class='icon fa-solid fa-receipt'></i>");
	}

	Enable(button);

});