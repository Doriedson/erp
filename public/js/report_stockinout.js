/**
 * Enables/Disables second date field for search.
 */
 $(document).on("click", "#frm_report_stockinout #intervalo", function() {

	$("#frm_report_stockinout #datafim").prop( "disabled", !this.checked);
});

/**
  * Defines datafim as minimum from dataini.
  */
$(document).on("change", "#frm_report_stockinout #dataini", function() {

	$("#frm_report_stockinout #datafim").prop({min: this.value});
});

/**
  * Search report stock in from date / date interval.
  */
$(document).on("submit", "#frm_report_stockinout", async function(event) {

  	event.preventDefault();

	var form = $(this);

	var datafim = $(this.datafim).prop('disabled');

	FormDisable(form);

  	var data = {
		action: 'report_stockinout_search',
		dataini: this.dataini.value,
		intervalo: this.intervalo.checked,
		datafim: this.datafim.value,
	}

	var response = await Post("report_stockinout.php", data);

	if (response != null) {

		$(".report_stockinout_container").html(response['data']);
		$(".w-reportstockinout-compra").html(response['totalcompra_formatted']);
		$(".w_reportstockinout_venda").html(response['totalvenda_formatted']);
		$(".w_reportstockinout_venda_percent").html(response['totalvenda_percent_formatted']);
		$(".w-reportstockinout-lucro").html(response['totallucro_formatted']);
	}

	FormEnable(form);

	$(this.datafim).prop('disabled', datafim);
});