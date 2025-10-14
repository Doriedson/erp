/**
 * Enables/Disables second date field for search.
 */
 $(document).on("click", "#frm_report_waitertip #intervalo", function() {

	$("#frm_report_waitertip #datafim").prop( "disabled", !this.checked);
});

/**
  * Defines datafim as minimum from dataini.
  */
$(document).on("change", "#frm_report_waitertip #dataini", function() {

	$("#frm_report_waitertip #datafim").prop({min: this.value});
});

/**
  * Search report stock in from date / date interval.
  */
$(document).on("submit", "#frm_report_waitertip", async function(event) {

  	event.preventDefault();

	let form = $(this);

	FormDisable(form);

  	let data = { 
		action: 'report_waitertip_search', 
		dataini: this.dataini.value,
		intervalo: this.intervalo.checked,
		datafim: this.datafim.value,
	}

    $(".report_waitertip_none").addClass("hidden");
    $(".report_waitertip_container").html(imgLoading);

	let response = await Post("report_waitertip.php", data);
    
	if (response != null) {

		$(".report_waitertip_container").html(response['data']);
		$('.w-reportwaitertip-total').html(response['total_formatted']);
    }

	FormEnable(form);
});