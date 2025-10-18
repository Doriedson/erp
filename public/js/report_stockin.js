/**
 * Enables/Disables second date field for search.
 */
 $(document).on("click", "#frm_report_stockin #intervalo", function() {

	$("#frm_report_stockin #datafim").prop( "disabled", !this.checked);
});

/**
  * Defines datafim as minimum from dataini.
  */
$(document).on("change", "#frm_report_stockin #dataini", function() {

	$("#frm_report_stockin #datafim").prop({min: this.value});
});

/**
  * Search report stock in from date / date interval.
  */
$(document).on("submit", "#frm_report_stockin", async function(event) {

  	event.preventDefault();

	var form = $(this);

	var datafim = $(this.datafim).prop('disabled');
	
	FormDisable(form);

  	var data = { 
		action: 'report_stockin_search', 
		dataini: this.dataini.value,
		intervalo: this.intervalo.checked,
		datafim: this.datafim.value,
	}

	var response = await Post("report_stockin.php", data);

	if (response != null) {

		$(".report_stockin_container").html(response['data']);
		$('.w-reportstockin-total').html(response['total_formatted']);
	}

	FormEnable(form);

	$(this.datafim).prop('disabled', datafim);
});