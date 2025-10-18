function ReportBillsPayHUD() {

	let total_pago = 0;
	let sector_pago = [];

	$(".w-billstopay").each(function() {

		if (sector_pago[$(this).data('id_contasapagarsetor')]) {

			sector_pago[$(this).data('id_contasapagarsetor')] += parseFloat($(this).data('valorpago'));

		} else {

			sector_pago[$(this).data('id_contasapagarsetor')] = parseFloat($(this).data('valorpago'));
		}

		total_pago += parseFloat($(this).data('valorpago'));
	});

	sector_pago.forEach((element, key) => {

		$(".reportbillspay_" + key).html(element.toLocaleString("pt-BR", {minimumFractionDigits: 2}));
	});

	$(".reportbillspay_total").html(total_pago.toLocaleString("pt-BR", {minimumFractionDigits: 2}));
}

/**
 * Enables/Disables second date field for search.
 */
 $(document).on("click", "#frm_report_billspay #intervalo", function() {

	$("#frm_report_billspay #datafim").prop( "disabled", !this.checked);
});

/**
  * Defines datafim as minimum from dataini.
  */
$(document).on("change", "#frm_report_billspay #dataini", function() {

	$("#frm_report_billspay #datafim").prop({min: this.value});
});

/**
  * Search report stock in from date / date interval.
  */
$(document).on("submit", "#frm_report_billspay", async function(event) {

  	event.preventDefault();

	let form = $(this);

	// let datafim = $(this.datafim).prop('disabled');
	FormDisable(form);

    let data = { 
        action: 'report_billspay_search', 
        procura: this.procura.value,
        dataini: this.dataini.value,
        intervalo: this.intervalo.checked,
        datafim: this.datafim.value,
	}

	let response = await Post("report_billspay.php", data);

	if (response != null) {

		$(".w-report-billspay-container").html(response['data']);
		// $(".w-reportbillspay-total").html(response['total']);
		ReportBillsPayHUD();
	}

	FormEnable(form);
	// $(this.datafim).prop('disabled', datafim);
});