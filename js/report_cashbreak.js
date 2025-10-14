/**
 * Enables/Disables second date field for search.
 */
 $(document).on("click", "#frm_report_cashbreak #intervalo", function() {

	$("#frm_report_cashbreak #datafim").prop( "disabled", !this.checked);
});

/**
  * Defines datafim as minimum from dataini.
  */
$(document).on("change", "#frm_report_cashbreak #dataini", function() {

	$("#frm_report_cashbreak #datafim").prop({min: this.value});
});

/**
  * Search report stock in from date / date interval.
  */
$(document).on("submit", "#frm_report_cashbreak", async function(event) {

  	event.preventDefault();

	var form = $(this);

	var datafim = $(this.datafim).prop('disabled');

	FormDisable(form);

  	var data = { 
		action: 'report_cashbreak_search', 
		dataini: this.dataini.value,
		intervalo: this.intervalo.checked,
		datafim: this.datafim.value,
	}

	var response = await Post("report_cashbreak.php", data);

	if (response != null) {

		$(".w-report-cashbreak-container").html(response['data']);
		$('.w-reportcashbreak-total').html(response['total']);
	}

	FormEnable(form);
	$(this.datafim).prop('disabled', datafim);
});