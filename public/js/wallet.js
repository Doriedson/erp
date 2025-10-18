let walletDespesaChart = null;
let walletReceitaChart = null;

class Wallet {

	// static POPUP_SIZE_SMALL = 0;
	// static POPUP_SIZE_LARGE = 1;
	// static POPUP_BUTTONFIX = true;

	// static window = null;

	// static handles = [];

	static filter = {
		// datelock: false,
		datestart: null,
		dateend_sel: false,
		dateend: null,
		sector_sel: false,
		sector: null,
		cashtype_sel: false,
		cashtype: null
	}
}

async function WalletDespesaFormEdit(container, button, action) {

	data = {
		action: action,
		id_walletdespesa: button.data("id_walletdespesa"),
	}

	return await FormEdit(container, button, data, "wallet.php");
}

async function WalletDespesaFormCancel(container, field, action) {

	var form = field.closest('form');

	var id_walletdespesa = form.data('id_walletdespesa');

	data = {
		action: action,
		id_walletdespesa: id_walletdespesa,
	}

	return await FormCancel(container, form, field, data, "wallet.php");
}

async function WalletDespesaFormSave(container, form, field, action) {

	let id_wallet = form.data('id_wallet');

	let data = {
		action: action,
		id_walletdespesa: form.data('id_walletdespesa'),
		value: field.val(),
	}

	let response = await FormSave(container, form, field, data, "wallet.php");

	if (action != "walletdespesa_walletdespesa_save") {

		WalletFilter(id_wallet);
	}

	return response;
}

async function WalletReceitaFormEdit(container, button, action) {

	data = {
		action: action,
		id_walletreceita: button.data("id_walletreceita"),
	}

	return await FormEdit(container, button, data, "wallet.php");
}

async function WalletReceitaFormCancel(container, field, action) {

	var form = field.closest('form');

	var id_walletreceita = form.data('id_walletreceita');

	data = {
		action: action,
		id_walletreceita: id_walletreceita,
	}

	return await FormCancel(container, form, field, data, "wallet.php");
}

async function WalletReceitaFormSave(container, form, field, action) {

	let id_wallet = form.data('id_wallet');

	let data = {
		action: action,
		id_walletreceita: form.data('id_walletreceita'),
		value: field.val(),
	}

	let response = await FormSave(container, form, field, data, "wallet.php");

	if (action != "walletreceita_walletreceita_save") {

		WalletFilter(id_wallet);
	}

	return response;
}

/**
 * Enables/Disables second date field for search.
 */
 $(document).on("click", "#frm_report_wallet .check_intervalo", function() {

	$("#frm_report_wallet .select_datafim").prop( "disabled", !this.checked);

	Wallet.filter.dateend_sel = this.checked;
	Wallet.filter.dateend = $("#frm_report_wallet .select_datafim").val();
});

/**
 * Enables/Disables sector for search.
 */
$(document).on("click", "#frm_report_wallet #setor", function() {

	$("#frm_report_wallet .select_id_walletsector").prop( "disabled", !this.checked);

	Wallet.filter.sector_sel = this.checked;
	Wallet.filter.sector = $("#frm_report_wallet #id_walletsector").val();
});

/**
 * Defines sector value.
 */
$(document).on("change", "#frm_report_wallet #id_walletsector", function() {

	Wallet.filter.sector = this.value;
});

/**
 * Enables/Disables cashtype for search.
 */
 $(document).on("click", "#frm_report_wallet #especie", function() {

	$("#frm_report_wallet .select_id_walletcashtype").prop( "disabled", !this.checked);

	Wallet.filter.cashtype_sel = this.checked;
	Wallet.filter.cashtype = $("#frm_report_wallet #id_walletcashtype").val();
});

/**
 * Defines cashtype value.
 */
$(document).on("change", "#frm_report_wallet #id_walletcashtype", function() {

	Wallet.filter.cashtype = this.value;
});

/**
  * Defines datafim as minimum from dataini.
  */
$(document).on("change", "#frm_report_wallet .select_dataini", function() {

	$("#frm_report_wallet .select_datafim").prop({min: this.value});

	Wallet.filter.datestart = this.value;
});

/**
  * Defines datafim value.
  */
$(document).on("change", "#frm_report_wallet .select_datafim", function() {

	Wallet.filter.dateend = this.value;
});

/**
 * Removes filter 01.
 */
 $(document).on("click", ".wallet_bt_filter01", function() {

	let button = $(this);

	let id_wallet = button.data("id_wallet");

	Wallet.filter.dateend_sel = false;

	button.remove();

	WalletFilter(id_wallet);

});

/**
 * Removes filter 02.
 */
 $(document).on("click", ".wallet_bt_filter02", function() {

	let button = $(this);

	let id_wallet = button.data("id_wallet");

	Wallet.filter.sector_sel = false;

	button.remove();

	WalletFilter(id_wallet);

});

/**
 * Removes filter 03.
 */
 $(document).on("click", ".wallet_bt_filter03", function() {

	let button = $(this);

	let id_wallet = button.data("id_wallet");

	Wallet.filter.cashtype_sel = false;

	button.remove();

	WalletFilter(id_wallet);

});

async function WalletFilter(id_wallet) {

	let data = {
		action: 'wallet_filter',
		id_wallet: id_wallet,
		dataini: Wallet.filter.datestart,
		intervalo: Wallet.filter.dateend_sel,
		datafim: Wallet.filter.dateend,
		especie: Wallet.filter.cashtype_sel,
		setor: Wallet.filter.sector_sel,
		id_walletcashtype: Wallet.filter.cashtype,
		id_walletsector: Wallet.filter.sector
	}

	$(".walletfilter_description").html("");
	$(".walletdespesa_container").html(imgLoading);
	$(".walletreceita_container").html(imgLoading);

	let response = await Post("wallet.php", data);

	if (response != null) {

		$(".walletdespesa_container").html(response['extra_block_wallet_expense_container']);

		$(".walletdespesafutura_container").html(response['extra_block_wallet_futureexpense_container']);

		$(".walletfilter_description").html(response['walletfilter_description']);

		$(".walletreceita_container").html(response['extra_block_wallet_receita_container']);

		$(".wallet_resume_container").html(response["extra_block_wallet_resume"]);

		WalletUpdateChart();
		$('.w-expense-filter-popup').addClass("hidden");
	}

	return response;
}

function WalletUpdateChart() {

	if ($('.walletdespesa').length == 0) {

		$('.wallet_expense_chart_container').addClass('hidden');

	} else {

		$('.wallet_expense_chart_container').removeClass('hidden');

		let total = 0;
		let sector = [];

		$(".walletdespesa").each(function() {

			if (sector[$(this).data('walletsector')]) {

				sector[$(this).data('walletsector')] += parseFloat($(this).data('valor'));

			} else {

				sector[$(this).data('walletsector')] = parseFloat($(this).data('valor'));
			}

			total += parseFloat($(this).data('valor'));
		});

		walletDespesaChart.chart.data.labels = Object.keys(sector);
		walletDespesaChart.chart.data.datasets[0].data = Object.values(sector);
		walletDespesaChart.chart.data.total = total.toLocaleString("pt-BR", {minimumFractionDigits: 2});

		walletDespesaChart.chart.update();
	}

	if ($('.walletreceita').length == 0) {

		$('.wallet_receita_chart_container').addClass('hidden');

	} else {

		$('.wallet_receita_chart_container').removeClass('hidden');

		let total = 0;
		let sector = [];

		$(".walletreceita").each(function() {

			// if (sector[$(this).data('walletsector')]) {

			// 	sector[$(this).data('walletsector')] += parseFloat($(this).data('valor'));

			// } else {

			// 	sector[$(this).data('walletsector')] = parseFloat($(this).data('valor'));
			// }

			total += parseFloat($(this).data('valor'));
		});

		walletReceitaChart.chart.data.labels = ["Receita"];
		walletReceitaChart.chart.data.datasets[0].data = [total];
		walletReceitaChart.chart.data.total = total.toLocaleString("pt-BR", {minimumFractionDigits: 2});

		walletReceitaChart.chart.update();
	}
}

/**
  * Registers new 'despesa'
  */
$(document).on("submit", "#frm_walletdespesa_new", async function(event) {

	event.preventDefault();

	let form = $(this);
	let id_wallet = form.data('id_wallet');
	let popup = form.closest(".popup");

	FormDisable(form);

	let data = {
		action: "walletdespesa_new",
		id_wallet: id_wallet,
		data: this.data.value,
		id_walletsector: this.frm_walletdespesanew_walletsector.value,
		valor: this.valor.value,
		id_walletcashtype: this.frm_walletdespesanew_walletcashtype.value,
		descricao: this.descricao.value,
		pago: this.pago.checked,
		parcelado: this.walletdespesanew_parcelado.checked,
		parcelas: this.frm_walletdespesanew_parcelado.value
	};

	let response = await Post("wallet.php", data);

	if (response != null) {

		this.valor.value = "";
		this.descricao.value = "";

		// let content = $(response['data']);

		let container = $('.walletdespesa_container');

		if (container.length > 0) {

			// $('.walletdespesa_notfound').addClass('hidden');

			// container.find('.table').append(content);

			// WalletUpdateChart();
			WalletFilter(id_wallet);

		} else {

			$(".wallet_" + id_wallet + "_saldo").html(response['saldo']);
		}

		FormEnable(form);

		if (popup.find('.popup_fixwindow').hasClass('fa-thumbtack')) {

			Modal.Close(popup);

		} else {

			AutoFocus(form);
		}

		// ContainerFocus(content, true);

	} else {

		FormEnable(form);
		AutoFocus(form);
	}
});

/**
  * Registers new 'receita'
  */
$(document).on("submit", "#frm_walletreceita_new", async function(event) {

	event.preventDefault();

	let form = $(this);
	let id_wallet = form.data('id_wallet');

	let popup = form.closest(".popup");

	FormDisable(form);

	let data = {
		action: "walletreceita_new",
		id_wallet: id_wallet,
		data: this.data.value,
		valor: this.valor.value,
		descricao: this.descricao.value,
	};

	let response = await Post("wallet.php", data);

	if (response != null) {

		this.valor.value = "";
		this.descricao.value = "";

		// let content = $(response['data']);

		let container = $('.walletreceita_container');

		if (container.length > 0) {

			// $('.walletreceita_notfound').addClass('hidden');

			// container.find('.table').append(content);

			// WalletUpdateChart();
			WalletFilter(id_wallet);
		}

		// $(".wallet_" + id_wallet + "_saldo").html(response['saldo']);

		FormEnable(form);

		if (popup.find('.popup_fixwindow').hasClass('fa-thumbtack')) {

			Modal.Close(popup);

		} else {

			AutoFocus(form);
		}

		// ContainerFocus(content, true);

	} else {

		FormEnable(form);
		AutoFocus(form);
	}
});

/**
  * Event click to deletes expense
  */
$(document).on("click", ".walletdespesa_bt_del", async function() {

	let button = $(this);

	Disable(button);

	let container = button.closest(".walletdespesa");

	let id_wallet = button.data('id_wallet');
	let id_walletdespesa = button.data('id_walletdespesa');
	let despesa = container.data('walletdespesa');

	let yes = async function() {

		let data = {
			action: "walletdespesa_delete",
			id_walletdespesa: id_walletdespesa,
		};

		response = await Post("wallet.php", data);

		if (response != null) {

			WalletFilter(id_wallet);
			// $(".wallet_" + id_wallet + "_saldo").html(response);

			// ContainerRemove(container, async function() {

			// 	WalletUpdateChart();

			// 	if ($(".walletdespesa").length == 0) {

			// 		$('.walletdespesa_notfound').removeClass('hidden');
			// 	}
			// });

		} else {

			button.removeClass("disabled");

			MenuClose();
		}

		return true;
	}

	let no = async function() {

			button.removeClass("disabled");

			MenuClose();
	}

	MessageBox.Show("Apagar despesa: " + despesa + "?", yes, no);
});

/**
  * Event click to deletes receita
  */
$(document).on("click", ".walletreceita_bt_del", async function() {

	let button = $(this);

	Disable(button);

	let container = button.closest(".walletreceita");

	let id_wallet = button.data('id_wallet');
	let id_walletreceita = button.data('id_walletreceita');
	let receita = container.data('walletreceita');

	let yes = async function() {

		let data = {
			action: "walletreceita_delete",
			id_walletreceita: id_walletreceita,
		};

		response = await Post("wallet.php", data);

		if (response != null) {

			// $(".wallet_" + id_wallet + "_saldo").html(response);

			// ContainerRemove(container, async function() {

			// 	WalletUpdateChart();

			// 	if ($(".walletreceita").length == 0) {

			// 		$('.walletreceita_notfound').removeClass('hidden');
			// 	}
			// });

			WalletFilter(id_wallet);

		} else {

			button.removeClass("disabled");

			MenuClose();
		}

		return true;
	}

	let no = async function() {

			button.removeClass("disabled");

			MenuClose();
	}

	MessageBox.Show("Apagar receita: " + receita + "?", yes, no);
});

/**
  * Opens "data" edition
  */
$(document).on("click", ".walletdespesa_bt_data", function() {

	WalletDespesaFormEdit($(this), $(this), 'walletdespesa_data_edit');
});

/**
  * Cancels "data" edition.
  */
 $(document).on("focusout", "#frm_walletdespesa_data #data", function() {

	WalletDespesaFormCancel($(this).closest('form'), $(this), 'walletdespesa_data_cancel');
});

/**
  * Saves "data" edition.
  */
$(document).on("change", "#frm_walletdespesa_data", async function(event) {

	event.preventDefault();

	let form = $(this);

	FormDisable(form);

	let response = await WalletDespesaFormSave(form, form, $(this.data), 'walletdespesa_data_save');

	// if (response != null) {

	// 	$('.walletdespesa_' + form.data('id_walletdespesa')).replaceWith(response['walletdespesa']);
	// }
});

/**
  * Opens "datapago" edition
  */
$(document).on("click", ".walletdespesa_bt_datapago", function() {

	WalletDespesaFormEdit($(this), $(this), 'walletdespesa_datapago_edit');
});

/**
  * Cancels "datapago" edition.
  */
 $(document).on("focusout", "#frm_walletdespesa_datapago #datapago", function() {

	WalletDespesaFormCancel($(this).closest('form'), $(this), 'walletdespesa_datapago_cancel');
});

/**
  * Saves "datapago" edition.
  */
$(document).on("change", "#frm_walletdespesa_datapago", async function(event) {

	event.preventDefault();

	let form = $(this);

	FormDisable(form);

	// let response =
	await WalletDespesaFormSave(form, form, $(this.datapago), 'walletdespesa_datapago_save');

	// if (response != null) {

	// 	$('.walletdespesa_' + form.datapago('id_walletdespesa')).replaceWith(response['walletdespesa']);
	// }
});

/**
  * Opens "sector" edition
  */
$(document).on("click", ".walletdespesa_bt_walletsector", function() {

	WalletDespesaFormEdit($(this), $(this), 'walletdespesa_sector_edit');
});

/**
  * Cancels "sector" edition.
  */
 $(document).on("focusout", "#frm_walletdespesa_walletsector #id_walletsector", function() {

	WalletDespesaFormCancel($(this).closest('form'), $(this), 'walletdespesa_sector_cancel');
});

/**
  * Saves "sector" edition.
  */
$(document).on("change", "#frm_walletdespesa_walletsector", async function(event) {

	event.preventDefault();

	let form = $(this);

	FormDisable(form);

	let response = await WalletDespesaFormSave(form, form, $(this.id_walletsector), 'walletdespesa_sector_save');

	// if (response != null) {

	// 	$('.walletdespesa_' + form.data('id_walletdespesa')).replaceWith(response['walletdespesa']);
	// }

	// WalletUpdateChart();
});

/**
  * Opens "cashtype" edition
  */
$(document).on("click", ".walletdespesa_bt_walletcashtype", function() {

	WalletDespesaFormEdit($(this), $(this), 'walletdespesa_cashtype_edit');
});

/**
  * Cancels "cashtype" edition.
  */
$(document).on("focusout", "#frm_walletdespesa_walletcashtype #id_walletcashtype", function() {

	WalletDespesaFormCancel($(this).closest('form'), $(this), 'walletdespesa_cashtype_cancel');
});

/**
  * Saves "cashtype" edition.
  */
 $(document).on("change", "#frm_walletdespesa_walletcashtype", async function(event) {

	event.preventDefault();

	let form = $(this);

	FormDisable(form);

	let response = await WalletDespesaFormSave(form, form, $(this.id_walletcashtype), 'walletdespesa_cashtype_save');

	// if (response != null) {

	// 	$('.walletdespesa_' + form.data('id_walletdespesa')).replaceWith(response['walletdespesa']);
	// }
});

/**
  * Opens "valor" edition
  */
 $(document).on("click", ".walletdespesa_bt_valor", function() {

	WalletDespesaFormEdit($(this), $(this), 'walletdespesa_valor_edit');
});

/**
  * Cancels "valor" edition.
  */
 $(document).on("focusout", "#frm_walletdespesa_valor #valor", function() {

	WalletDespesaFormCancel($(this).closest('form'), $(this), 'walletdespesa_valor_cancel');
});

/**
  * Saves "valor" edition.
  */
$(document).on("submit", "#frm_walletdespesa_valor", async function(event) {

	event.preventDefault();

	let form = $(this);

	let id_wallet = form.data("id_wallet");

	FormDisable(form);

	let response = await WalletDespesaFormSave(form, form, $(this.valor), 'walletdespesa_valor_save');

	// if (response != null) {

	// 	$('.walletdespesa_' + form.data('id_walletdespesa')).replaceWith(response['walletdespesa']);
	// 	$(".wallet_" + id_wallet + "_saldo").html(response['saldo']);
	// }

	// WalletUpdateChart();
});

/**
  * Opens "valorpago" edition
  */
 $(document).on("click", ".walletdespesa_bt_valorpago", function() {

	WalletDespesaFormEdit($(this), $(this), 'walletdespesa_valorpago_edit');
});

/**
  * Cancels "valorpago" edition.
  */
 $(document).on("focusout", "#frm_walletdespesa_valorpago #valorpago", function() {

	WalletDespesaFormCancel($(this).closest('form'), $(this), 'walletdespesa_valorpago_cancel');
});

/**
  * Saves "valorpago" edition.
  */
$(document).on("submit", "#frm_walletdespesa_valorpago", async function(event) {

	event.preventDefault();

	let form = $(this);

	// let id_wallet = form.data("id_wallet");

	FormDisable(form);

	// let response =
	await WalletDespesaFormSave(form, form, $(this.valorpago), 'walletdespesa_valorpago_save');

	// if (response != null) {

	// 	$('.walletdespesa_' + form.data('id_walletdespesa')).replaceWith(response['walletdespesa']);
	// 	$(".wallet_" + id_wallet + "_saldo").html(response['saldo']);
	// }

	// WalletUpdateChart();
});

/**
  * Opens "walletdespesa" edition
  */
$(document).on("click", ".walletdespesa_bt_walletdespesa", function() {

	WalletDespesaFormEdit($(this), $(this), 'walletdespesa_walletdespesa_edit');
});

/**
  * Cancels "walletdespesa" edition.
  */
$(document).on("focusout", "#frm_walletdespesa_walletdespesa #walletdespesa", function() {

	WalletDespesaFormCancel($(this).closest('form'), $(this), 'walletdespesa_walletdespesa_cancel');
});

/**
  * Saves "walletdespesa" edition.
  */
$(document).on("submit", "#frm_walletdespesa_walletdespesa", async function(event) {

	event.preventDefault();

	let form = $(this);

	FormDisable(form);

	let response = await WalletDespesaFormSave(form, form, $(this.walletdespesa), 'walletdespesa_walletdespesa_save');

	if (response != null) {

		$('.walletdespesa_' + form.data('id_walletdespesa')).replaceWith(response['walletdespesa']);
	}
});

/**
  * Opens "data" edition
  */
$(document).on("click", ".walletreceita_bt_data", function() {

	WalletReceitaFormEdit($(this), $(this), 'walletreceita_data_edit');
});

/**
  * Cancels "data" edition.
  */
 $(document).on("focusout", "#frm_walletreceita_data #data", function() {

	WalletReceitaFormCancel($(this).closest('form'), $(this), 'walletreceita_data_cancel');
});

/**
  * Saves "data" edition.
  */
$(document).on("change", "#frm_walletreceita_data", async function(event) {

	event.preventDefault();

	let form = $(this);

	FormDisable(form);

	let response = await WalletReceitaFormSave(form, form, $(this.data), 'walletreceita_data_save');

	// if (response != null) {

	// 	$('.walletreceita_' + form.data('id_walletreceita')).replaceWith(response['walletreceita']);
	// }
});

/**
  * Opens "valor" edition
  */
 $(document).on("click", ".walletreceita_bt_valor", function() {

	WalletReceitaFormEdit($(this), $(this), 'walletreceita_valor_edit');
});

/**
  * Cancels "valor" edition.
  */
$(document).on("focusout", "#frm_walletreceita_valor #valor", function() {

	WalletReceitaFormCancel($(this).closest('form'), $(this), 'walletreceita_valor_cancel');
});

/**
  * Saves "valor" edition.
  */
$(document).on("submit", "#frm_walletreceita_valor", async function(event) {

	event.preventDefault();

	let form = $(this);

	// let id_wallet = form.data("id_wallet");

	FormDisable(form);

	// let response =
	await WalletReceitaFormSave(form, form, $(this.valor), 'walletreceita_valor_save');

	// if (response != null) {

	// 	$('.walletreceita_' + form.data('id_walletreceita')).replaceWith(response['walletreceita']);
	// 	$(".wallet_" + id_wallet + "_saldo").html(response['saldo']);
	// }

	// WalletUpdateChart();
});

/**
  * Opens "walletreceita" edition
  */
$(document).on("click", ".walletreceita_bt_walletreceita", function() {

	WalletReceitaFormEdit($(this), $(this), 'walletreceita_walletreceita_edit');
});

/**
  * Cancels "walletreceita" edition.
  */
$(document).on("focusout", "#frm_walletreceita_walletreceita #walletreceita", function() {

	WalletReceitaFormCancel($(this).closest('form'), $(this), 'walletreceita_walletreceita_cancel');
});

/**
  * Saves "walletreceita" edition.
  */
$(document).on("submit", "#frm_walletreceita_walletreceita", async function(event) {

	event.preventDefault();

	let form = $(this);

	FormDisable(form);

	let response = await WalletReceitaFormSave(form, form, $(this.walletreceita), 'walletreceita_walletreceita_save');

	if (response != null) {

		$('.walletreceita_' + form.data('id_walletreceita')).replaceWith(response['walletreceita']);
	}
});

/**
  * Shows walletdespesa edition popup
  */
$(document).on("click", ".walletdespesa_bt_edit", async function() {

	let button = $(this);

	Disable(button);

	let id_walletdespesa = button.data('id_walletdespesa');

	let data = {
		action: 'walletdespesa_edition',
		id_walletdespesa: id_walletdespesa
	}

	let response = await Post("wallet.php", data);

	if (response != null) {

		Modal.Show(Modal.POPUP_SIZE_SMALL, "Editar Despesa", response, null, false, "<i class='icon fa-solid fa-file-pen'></i>");
		// $('.walletdespesa_edit_container').html(response);
		// $('.w_walletdespesa_edit_popup').removeClass('hidden');
	}

	Enable(button);
	MenuClose();
});

/**
  * Shows walletreceita edition popup
  */
$(document).on("click", ".walletreceita_bt_edit", async function() {

	let button = $(this);

	Disable(button);

	let id_walletreceita = button.data('id_walletreceita');

	let data = {
		action: 'walletreceita_edition',
		id_walletreceita: id_walletreceita
	}

	let response = await Post("wallet.php", data);

	if (response != null) {

		Modal.Show(Modal.POPUP_SIZE_SMALL, "Editar Receita", response, null, false, "<i class='icon fa-solid fa-file-pen'></i>");
	}

	Enable(button);
	MenuClose();
});

/**
  * Shows expense filter popup
  */
 $(document).on("click", ".expense_bt_filter", async function() {

	let button = $(this);

	let id_wallet = button.data("id_wallet");

	Disable(button);

	let data = {
		action: "wallet_expense_filter",
		id_wallet: id_wallet,
		datestart: Wallet.filter.datestart,
		dateend_sel: Wallet.filter.dateend_sel,
		dateend: Wallet.filter.dateend,
		sector_sel: Wallet.filter.sector_sel,
		sector: Wallet.filter.sector,
		cashtype_sel: Wallet.filter.cashtype_sel,
		cashtype: Wallet.filter.cashtype,
	}

	let response = await Post("wallet.php", data);

	if (response != null) {

		Modal.Show(Modal.POPUP_SIZE_SMALL, "Filtro de Despesas", response, null, false, "<i class='icon fa-solid fa-filter-circle-dollar'></i>");
	}

	Enable(button);

	MenuClose();
});

/**
  * Filters wallet by date / date interval.
  */
 $(document).on("submit", "#frm_report_wallet", async function(event) {

	event.preventDefault();

	let form = $(this);
	let id_wallet = form.data("id_wallet");
	// let datafim = form.find('.select_datafim').prop('disabled');
	// let id_walletcashtype = $(this.id_walletcashtype).prop('disabled');
	// let id_walletsector = $(this.id_walletsector).prop('disabled');
	// let pdv = $(this.pdv).prop('disabled');

	FormDisable(form);

	// let data = {
	// 	action: 'wallet_filter',
	// 	id_wallet: form.data('id_wallet'),
	// 	dataini: form.find('.select_dataini')[0].value,
	// 	intervalo: form.find('.check_intervalo')[0].checked,
	// 	datafim: form.find('.select_datafim')[0].value,
	// 	especie: this.especie.checked,
	// 	setor: this.setor.checked,
	// 	id_walletcashtype: form.find('.select_id_walletcashtype')[0].value,
	// 	id_walletsector: form.find('.select_id_walletsector')[0].value,
	// }

	// $('.walletdespesa_notfound').addClass('hidden');
	// $('.walletreceita_notfound').addClass('hidden');
	// $(".walletfilter_description").html("");
	// $(".walletdespesa_table").html(imgLoading);
	// $(".walletreceita_table").html(imgLoading);

	let response = await WalletFilter(id_wallet);
	// await Post("wallet.php", data);

	if (response != null) {

		// if (response['extra_block_expense']) {

		// 	$('.walletdespesa_notfound').addClass('hidden');

		// } else {

		// 	$('.walletdespesa_notfound').removeClass('hidden');
		// }

		// $(".walletdespesa_table").html(response['extra_block_expense']);
		// $(".walletfilter_description").html(response['walletfilter_description']);

		// if (response['extra_block_receita']) {

		// 	$('.walletreceita_notfound').addClass('hidden');

		// } else {

		// 	$('.walletreceita_notfound').removeClass('hidden');
		// }

		// $(".walletreceita_table").html(response['extra_block_receita']);
		// $(".wallet_resume_container").html(response["block_wallet_resume"]);

		// console.log(response["block_wallet_resume"]);

		// WalletUpdateChart();
		// $('.w-expense-filter-popup').addClass("hidden");

		Modal.Close(form.closest(".popup"));
	}

	FormEnable(form);

	// form.find('.select_datafim').prop('disabled', datafim);
	// form.find('.select_id_walletcashtype').prop('disabled', id_walletcashtype);
	// form.find('.select_id_walletsector').prop('disabled', id_walletsector);
});

/**
  * Shows sector manager
  */
 $(document).on("click", ".walletsector_bt_manager", async function() {

	let button = $(this);

	Disable(button);

	let data = {
		action: 'load',
		id_wallet: button.data('id_wallet')
	}

	let response = await Post("wallet_sector.php", data);

	if (response != null) {

		Modal.Show(Modal.POPUP_SIZE_SMALL, "Gerenciamento de Setor", response, null, false, "<i class='icon fa-solid fa-gear'></i>");
	}

	Enable(button);
	MenuClose();
});

/**
  * Shows cashtype manager
  */
 $(document).on("click", ".walletcashtype_bt_manager", async function() {

	let button = $(this);

	Disable(button);

	let data = {
		action: 'load',
		id_wallet: button.data('id_wallet')
	}

	let response = await Post("wallet_cashtype.php", data);

	if (response != null) {

		Modal.Show(Modal.POPUP_SIZE_SMALL, "Gerenciamento de Espécie", response, null, false, "<i class='icon fa-solid fa-gear'></i>");
	}

	Enable(button);
	MenuClose();
});

/**
  * Shows wallet window
  */
 $(document).on("click", ".expense_bt_wallet", function() {

	Disable($(this));

	LoadPage("wallet.php");
});

/**
  * Shows new expense popup
  */
$(document).on("click", ".walletdespesa_bt_new", async function() {

	let button = $(this);

	let id_wallet = button.data('id_wallet');

	Disable(button);

	let data = {
		action: 'walletdespesa_shownew',
		id_wallet: id_wallet
	}

	let response = await Post("wallet.php", data);

	if (response != null) {

		Modal.Show(Modal.POPUP_SIZE_SMALL, "Registro de Despesa", response, null, Modal.POPUP_BUTTONFIX, "<i class='icon fa-solid fa-square-plus'></i>");
	}

	Enable(button);
	MenuClose();
});

/**
  * Shows new receita popup
  */
$(document).on("click", ".walletreceita_bt_new", async function() {

	let button = $(this);
	let id_wallet = button.data('id_wallet');

	Disable(button);

	let data = {
		action: 'walletreceita_popup_new',
		id_wallet: id_wallet
	}

	let response = await Post("wallet.php", data);

	if (response != null) {

		Modal.Show(Modal.POPUP_SIZE_SMALL, "Registro de Receita", response, null, Modal.POPUP_BUTTONFIX, "<i class='icon fa-solid fa-hand-holding-dollar'></i>");
	}

	Enable(button);
	MenuClose();
});

/**
  * Shows wallet despesa payment popup
  */
$(document).on("click", ".walletdespesa_bt_pay", async function() {

	let button = $(this);
	let id_walletdespesa = button.data('id_walletdespesa');

	Disable(button);

	let data = {
		action: 'walletdespesa_popup_payment',
		id_walletdespesa: id_walletdespesa
	}

	let response = await Post("wallet.php", data);

	if (response != null) {

		Modal.Show(Modal.POPUP_SIZE_SMALL, "Despesa - Pagamento", response, null, false, "<i class='icon fa-solid fa-file-invoice-dollar'></i>");
	}

	Enable(button);
	MenuClose();
});

/**
 * Event to register payment billstopay
 */
$(document).on("submit", "#frm_walletdespesa_payment", async function(event) {

	event.preventDefault();

	let form = $(this);
	let id_wallet = form.data("id_wallet");

	FormDisable(form);

	let data = {
		action: 'walletdespesa_payment',
		id_wallet: id_wallet,
		id_walletdespesa: form.data('id_walletdespesa'),
		datapago: this.datapago.value,
		valorpago: this.valorpago.value,
	}

	let response = await Post("wallet.php", data);

	if (response != null) {

		WalletFilter(id_wallet);

		$(".wallet_" + id_wallet + "_saldo").html(response['saldo']);

		Modal.Close(form.closest('.popup'));

	} else {

		FormEnable(form);
	}
});