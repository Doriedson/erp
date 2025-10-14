/**
 * Enables/Disables second date field for search.
 */
 $(document).on("click", "#frm_report_salecard #intervalo", function() {

	$("#frm_report_salecard #datafim").prop( "disabled", !this.checked);
});

/**
  * Defines datafim as minimum from dataini.
  */
$(document).on("change", "#frm_report_salecard #dataini", function() {

	$("#frm_report_salecard #datafim").prop({min: this.value});
});

/**
  * Search report stock in from date / date interval.
  */
$(document).on("submit", "#frm_report_salecard", async function(event) {

  	event.preventDefault();

  	var submit = $(this).find(':input[type=submit]');
  
	submit.prop('disabled', true);

  	$(".report_salecard_container").html("");

    var data = { 
        action: 'report_salecard_search', 
        dataini: this.dataini.value,
        intervalo: this.intervalo.checked,
        datafim: this.datafim.value,
	}

	var response = await Post("report_salecard.php", data);

	if (response != null) {

		$(".report_salecard_container").html(response);

	} else {
		
		$(".report_salecard_container").html("");
	}

	submit.prop('disabled', false);
});