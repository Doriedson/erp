let billstopayChart = null;
let billstopayChartPending = null;

async function BillstopayFormEdit(container, button, action) {

	data = {
		action: action,
		id_contasapagar: button.data("id_contasapagar"),
	}

	return await FormEdit(container, button, data, "bills_to_pay.php");
}

async function BillstopayFormCancel(container, field, action) {

	var form = field.closest('form');

	var id_contasapagar = form.data('id_contasapagar');

	data = {
		action: action,
		id_contasapagar: id_contasapagar,
	}

	return await FormCancel(container, form, field, data, "bills_to_pay.php");
}

async function BillstopayFormSave(container, form, field, action) {

	let id_contasapagar = form.data('id_contasapagar');

	let data = {
		action: action,
		id_contasapagar: id_contasapagar,
		value: field.val(),
	}

	let response = await FormSave(container, form, field, data, "bills_to_pay.php");

	if (response != null) {

		$('.billstopay_' + id_contasapagar).replaceWith(response['bill']);
	}

	return response;
}

function BillstopayUpdateChart() {

	let total_pago = 0;
	let sector_pago = [];
	let total_apagar = 0;
	let sector_apagar = [];

	$(".w-billstopay").each(function() {

		if (parseFloat($(this).data('valorpago')) > 0) {

			if (sector_pago[$(this).data('contasapagarsetor')]) {

				sector_pago[$(this).data('contasapagarsetor')] += parseFloat($(this).data('valorpago'));

			} else {

				sector_pago[$(this).data('contasapagarsetor')] = parseFloat($(this).data('valorpago'));
			}

			total_pago += parseFloat($(this).data('valorpago'));

		} else {

			if (sector_apagar[$(this).data('contasapagarsetor')]) {

				sector_apagar[$(this).data('contasapagarsetor')] += parseFloat($(this).data('valor'));

			} else {

				sector_apagar[$(this).data('contasapagarsetor')] = parseFloat($(this).data('valor'));
			}

			total_apagar += parseFloat($(this).data('valor'));
		}


	});

	if (total_pago == 0) {

		$('.billstopay_chart_container').addClass('hidden');

	} else {

		$('.billstopay_chart_container').removeClass('hidden');
	}

	billstopayChart.chart.data.labels = Object.keys(sector_pago);
	billstopayChart.chart.data.datasets[0].data = Object.values(sector_pago);
	billstopayChart.chart.data.total = total_pago.toLocaleString("pt-BR", {minimumFractionDigits: 2});

	billstopayChart.chart.update();

	if (total_apagar == 0) {

		$('.billstopay_pendingchart_container').addClass('hidden');

	} else {

		$('.billstopay_pendingchart_container').removeClass('hidden');
	}

	billstopayChartPending.chart.data.labels = Object.keys(sector_apagar);
	billstopayChartPending.chart.data.datasets[0].data = Object.values(sector_apagar);
	billstopayChartPending.chart.data.total = total_apagar.toLocaleString("pt-BR", {minimumFractionDigits: 2});

	billstopayChartPending.chart.update();
}

/**
 * Enables/Disables second date field for search.
 */
$(document).on("click", "#frm_billstopay_search #intervalo", function() {

	$("#frm_billstopay_search #datafim").prop("disabled", !this.checked);

	if($("#frm_billstopay_search #datafim").prop("disabled") == false) {

		$("#frm_billstopay_search #datafim").focus();
	}

});

/**
 * Enables/Disables setor for search.
 */
$(document).on("click", "#frm_billstopay_search #chk_setor", function() {

	$("#frm_billstopay_search #setor").prop("disabled", !this.checked);

});

/**
 * Enables/Disables description field for search.
 */
$(document).on("click", "#frm_billstopay_search #chk_descricao", function() {

	$("#frm_billstopay_search #descricao").prop( "disabled", !this.checked);

	if($("#frm_billstopay_search #descricao").prop( "disabled") == false) {

		$("#frm_billstopay_search #descricao").focus();
	}

});

/**
  * Defines datafim as minimum from dataini.
  */
$(document).on("change", "#frm_billstopay_search #dataini", function() {

	$(this).closest('form').find("#datafim").prop({min: this.value});

});

/**
  * Event submit to save the new bill
  */
$(document).on("submit", "#frm_billstopay", async function(event) {

	event.preventDefault();

	let form = $(this);

	FormDisable(form);

	let container = $('.billstopay_table');

	let data = {
		action: 'billstopay_new',
		vencimento: this.vencimento.value,
		setor: this.setor.value,
		descricao: this.descricao.value,
		valor: this.valor.value,
		pago: this.pago.checked,
	}

	let response = await Post("bills_to_pay.php", data);

	if (response != null) {

		$('.billstopay_none').addClass('hidden');

		let content = $(response);
		container.append(content);

		if ($(".w-billstopay").length == 0) {

			$('.billstopay_not_found').removeClass('hidden');

		} else {

			$('.billstopay_not_found').addClass('hidden');
		}

		BillstopayUpdateChart();

		FormEnable(form);

		this.descricao.value = "";
		this.valor.value = "";
		this.pago.checked = false;
		this.descricao.scrollIntoView();
		this.descricao.focus();

		let popup = form.closest('.popup');

		if (popup.find('.popup_fixwindow').hasClass('fa-thumbtack')) {

			Modal.Close(popup);

		} else {

			AutoFocus(form);
		}

		ContainerFocus(content, true);

	} else {

		FormEnable(form);
	}

	MenuClose();
});

/**
  * Searchs bills to pay.
  */
$(document).on("submit", "#frm_billstopay_search", async function(event) {

	event.preventDefault();

	let container = $('.billstopay_table');
	let form = $(this);

	FormDisable(form);

	let data = {
		action: 'billstopay_search',
		procura: this.procura.value,
		dataini: this.dataini.value,
		intervalo: this.intervalo.checked,
		datafim: this.datafim.value,
		chk_setor: this.chk_setor.checked,
		setor: this.setor.value,
		chk_descricao: this.chk_descricao.checked,
		descricao: this.descricao.value,
	}

	let search_desc = ["Lançamento", "Pagamento", "Vencimento"];

	let caption = search_desc[this.procura.value] + ", ";

	let dataini_obj = new Date(this.dataini.value + " 00:00");
	dataini_obj.toLocaleString('pt-br');

	let day = dataini_obj.getDate();

	if (day < 10) { day = '0' + day}

	let month = dataini_obj.getMonth() + 1;

	if (month < 10) { month = '0' + month}

	let datainiFormatada = day + "/" + month + "/" + dataini_obj.getFullYear();

	caption += datainiFormatada;

	if(this.intervalo.checked == true) {

		let datafim_obj = new Date(this.datafim.value + " 00:00");
		datafim_obj.toLocaleString('pt-br');

		let day = datafim_obj.getDate();

		if (day < 10) { day = '0' + day}

		let month = datafim_obj.getMonth() + 1;

		if (month < 10) { month = '0' + month}

		let datafimFormatada = day + "/" + month + "/" + datafim_obj.getFullYear();

		caption += " até " + datafimFormatada;
	}

	if (this.chk_setor.checked) {

		caption += " [" + this.setor.options[this.setor.selectedIndex].text + "]";
	}

	if (this.chk_descricao.checked) {

		caption += " [" + this.descricao.value + "]";
	}

	container.html(imgLoading);

	let response = await Post("bills_to_pay.php", data);

	$('.billstopay_header').html(caption);
	$('.billstopay_none').addClass('hidden');

	if (response != null) {

		container.html(response);

		Modal.Close(form.closest('.popup'));

	} else {

		container.html("");
	}

	if ($(".w-billstopay").length == 0) {

		$('.billstopay_not_found').removeClass('hidden');

	} else {

		$('.billstopay_not_found').addClass('hidden');
	}

	FormEnable(form);

	BillstopayUpdateChart();
});

/**
  * Shows billstopay filter popup
  */
$(document).on("click", ".billstopay_bt_filter", async function() {

	let data = {
		action: "billstopay_popup_filter"
	}

	let response = await Post("bills_to_pay.php", data);

	if (response != null) {

		Modal.Show(Modal.POPUP_SIZE_SMALL, "Contas a Pagar - Filtro", response, null);
	}
	// $('.w-billstopay-filter').removeClass("hidden");

	// AutoFocus($('.w-billstopay-filter'));

	MenuClose();
});

/**
  * Lists bills to pay.
  */
$(document).on("click", ".billstopay_bt_topay", async function() {

	let button = $(this);

	Disable(button);

	let container = $('.billstopay_table');

	let data = {
		action: 'billstopay_topay',
	}

	let response = await Post("bills_to_pay.php", data);

	$('.billstopay_header').html("Pagamentos Pendentes");

	if (response != null) {

		if (response.length == 0) {

			container.html("");

			$('.billstopay_none').removeClass('hidden');
			$('.billstopay_not_found').addClass('hidden');

		} else {

			container.html(response);

			$('.billstopay_none').addClass('hidden');
			$('.billstopay_not_found').addClass('hidden');
		}

		BillstopayUpdateChart();
	}

	MenuClose();
	Enable(button);
});

/**
  * Event click to delete the bills to pay
  */
 $(document).on("click", ".bills_to_pay_bt_delete", async function() {

	let button = $(this);

	Disable(button);

	let container = button.closest('.w-billstopay');

	let id_contasapagar = button.data("id_contasapagar");
	let id_contasapagarsetor = button.data("id_contasapagarsetor");

	let data = {
		action: 'billstopay_delete',
		id_contasapagar: id_contasapagar,
	}

	let yes = async function() {

		let response = await Post("bills_to_pay.php", data);

		if (response != null) {

			ContainerRemove(container, function() {

				switch(WindowManager.page) {

					case "bills_to_pay.php":

						if ($(".w-billstopay").length == 0) {

							$('.billstopay_none').removeClass('hidden');
							$('.billstopay_not_found').addClass('hidden');

						} else {

							$('.billstopay_none').addClass('hidden');
							$('.billstopay_not_found').addClass('hidden');
						}

						BillstopayUpdateChart();
						break;

					case "report_billspay.php":

						ReportBillsPayHUD();

						if ($(".reportbillspay_" + id_contasapagarsetor).closest(".section-header").find("w-billstopay").length == 0) {

							$(".reportbillspay_" + id_contasapagarsetor).html("0,00");
						}

						break;

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

	MessageBox.Show("Confirma a remoção da conta?", yes, no);
});

/**
  * Event click to edit the bills to pay
  */
$(document).on("click", ".bills_to_pay_bt_edit", async function() {

	let button = $(this);

	Disable(button);

	let id_contasapagar = button.data("id_contasapagar");

	let data = {
		action: 'billstopay_edit',
		id_contasapagar: id_contasapagar,
	}

	let response = await Post("bills_to_pay.php", data);

	if (response != null) {

		Modal.Show(Modal.POPUP_SIZE_SMALL, "Contas a Pagar - Edição", response, null);
	}

	Enable(button);

	MenuClose();
});

/**
 * Event button to open bills to pay form payment
 */
 $(document).on("click", ".billstopay_bt_pay", async function() {

	let button = $(this);

	Disable(button);

	let data = {
		action: 'billstopay_payment_form',
		id_contasapagar: button.data('id_contasapagar')
	}

	let response = await Post("bills_to_pay.php", data);

	if (response != null) {

		Modal.Show(Modal.POPUP_SIZE_SMALL, "Contas a Pagar - Pagamento", response, null);

	}

	MenuClose();

	Enable(button);
});

/**
 * Event to register payment billstopay
 */
$(document).on("submit", "#frm_billstopay_payment", async function(event) {

	event.preventDefault();

	let form = $(this);

	FormDisable(form);

	let data = {
		action: 'billstopay_payment',
		id_contasapagar: form.data('id_contasapagar'),
		datapago: this.datapago.value,
		valorpago: this.valorpago.value,
	}

	let response = await Post("bills_to_pay.php", data);

	if (response != null) {

		$('.billstopay_' + form.data('id_contasapagar')).replaceWith(response);

		BillstopayUpdateChart();

		Modal.Close(form.closest('.popup'));

	} else {

		FormEnable(form);
	}
});

/**
  * Open "datavencimento" edition
  */
 $(document).on("click", ".billstopay_bt_vencimento", async function() {

	BillstopayFormEdit($(this).closest('.container'), $(this), "billstopay_vencimento_edit");
});

/**
  * Cancels "datavencimento" edition
  */
 $(document).on("focusout", "#frm_billstopay_vencimento #vencimento", async function() {

	BillstopayFormCancel($(this).closest('form'), $(this), "billstopay_vencimento_cancel");
});

/**
  * Saves "datavencimento" edition.
  */
 $(document).on("change", "#frm_billstopay_vencimento", async function(event) {

	event.preventDefault();

	let form = $(this);

	FormDisable(form);

	let response = await BillstopayFormSave(form, form, $(this.vencimento), "billstopay_vencimento_save");

	if (response != null) {

		$('.w_billstopay_status_' + form.data('id_contasapagar')).html(response['status']);
	}
});

/**
  * Opens "descricao" edition
  */
 $(document).on("click", ".billstopay_bt_descricao", async function() {

	BillstopayFormEdit($(this).closest('.container'), $(this), "billstopay_descricao_edit");
});

/**
  * Cancels "descricao" edition.
  */
 $(document).on("focusout", "#frm_billstopay_descricao #descricao", async function() {

	BillstopayFormCancel($(this).closest('form'), $(this), 'billstopay_descricao_cancel');
});

/**
  * Saves "descricao" edition.
  */
 $(document).on("submit", "#frm_billstopay_descricao", async function(event) {

	event.preventDefault();

	let form = $(this);

	FormDisable(form);

	BillstopayFormSave($(this), $(this), $(this.descricao), "billstopay_descricao_save");
});

/**
  * Opens "valor" edition
  */
 $(document).on("click", ".billstopay_bt_valor", async function() {

	BillstopayFormEdit($(this).parent(), $(this), "billstopay_valor_edit");
});

/**
  * Cancels "valor" edition.
  */
 $(document).on("focusout", "#frm_billstopay_valor #valor", async function() {

	BillstopayFormCancel($(this).closest('form'), $(this), "billstopay_valor_cancel");
});

/**
  * Saves "valor" edition.
  */
 $(document).on("submit", "#frm_billstopay_valor", async function(event) {

	event.preventDefault();

	FormDisable($(this));

	if (await BillstopayFormSave($(this), $(this), $(this.valor), "billstopay_valor_save")) {

		BillstopayUpdateChart();
	}
});

/**
  * Opens "setor" edition
  */
 $(document).on("click", ".billstopay_bt_setor", async function() {

	BillstopayFormEdit($(this).closest('.container'), $(this), "billstopay_setor_edit");
});

/**
  * Cancels "setor" edition.
  */
 $(document).on("focusout", "#frm_billstopay_setor #setor", async function() {

	BillstopayFormCancel($(this).closest('form'), $(this), "billstopay_setor_cancel");
});

/**
  * Saves "setor" edition.
  */
 $(document).on("change", "#frm_billstopay_setor", async function(event) {

	event.preventDefault();

	let form = $(this);

	FormDisable(form);

	if (await BillstopayFormSave(form, form, $(this.setor), "billstopay_setor_save")) {

		BillstopayUpdateChart();
	}
});

/**
  * Opens "obs" edition
  */
 $(document).on("click", ".billstopay_bt_obs", async function() {

	BillstopayFormEdit($(this).closest('.container'), $(this), "billstopay_obs_edit");
});

/**
  * Cancels "obs" edition.
  */
 $(document).on("focusout", "#frm_billstopay_obs #obs", async function() {

	BillstopayFormCancel($(this).closest('form'), $(this), "billstopay_obs_cancel");
});

/**
  * Saves "obs" edition.
  */
 $(document).on("submit", "#frm_billstopay_obs", async function(event) {

	event.preventDefault();

	FormDisable($(this));

	BillstopayFormSave($(this), $(this), $(this.obs), "billstopay_obs_save");
});

/**
  * Opens "valorpago" edition
  */
 $(document).on("click", ".billstopay_bt_valorpago", async function() {

	BillstopayFormEdit($(this).closest('.container'), $(this), "billstopay_valorpago_edit");
});

/**
  * Cancels "valorpago" edition.
  */
 $(document).on("focusout", "#frm_billstopay_valorpago #valorpago", async function() {

	BillstopayFormCancel($(this).closest('form'), $(this), "billstopay_valorpago_cancel");
});

/**
  * Saves "valor" edition.
  */
 $(document).on("submit", "#frm_billstopay_valorpago", async function(event) {

	event.preventDefault();

	let form = $(this);

	FormDisable(form);

	if (await BillstopayFormSave(form, form, $(this.valorpago), "billstopay_valorpago_save")) {

		switch (WindowManager.page) {

			case "bills_to_pay.php":

				BillstopayUpdateChart();
				break;

			case "report_billspay.php":

				ReportBillsPayHUD();
				break;
		}

	}
});

/**
  * Open "datapago" edition
  */
 $(document).on("click", ".bills_bt_datapago", async function() {

	BillstopayFormEdit($(this).closest('.container'), $(this), "billstopay_datapago_edit");
});

/**
  * Cancels "datapago" edition
  */
 $(document).on("focusout", "#frm_billstopay_datapago #datapago", async function() {

	BillstopayFormCancel($(this).closest('form'), $(this), "billstopay_datapago_cancel");
});

/**
  * Saves "datapago" edition.
  */
 $(document).on("change", "#frm_billstopay_datapago", async function(event) {

	FormDisable($(this));

	await BillstopayFormSave($(this), $(this), $(this.datapago), "billstopay_datapago_save");
});

/**
 * Shows new billstopay popup.
 */
$(document).on("click", ".billstopay_bt_show_new", async function() {

	let data = {
		action: "billstopay_popup_new"
	}

	let response = await Post("bills_to_pay.php", data);

	if (response != null) {

		Modal.Show(Modal.POPUP_SIZE_SMALL, "Contas a Pagar - Nova", response, null, Modal.POPUP_BUTTONFIX);
	}
	// $('.w-billstopay-new-popup').removeClass('hidden');

	// AutoFocus($('.w-billstopay-new-popup'));

	MenuClose();

});