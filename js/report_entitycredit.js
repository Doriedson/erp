/**
 * Enables/Disables second date field for search.
 */
$(document).on("click", "#frm_report_entitycredit #intervalo", function() {

	$("#frm_report_entitycredit #datafim").prop( "disabled", !this.checked);
});

/**
  * Defines datafim as minimum from dataini.
  */
$(document).on("change", "#frm_report_entitycredit #dataini", function() {

	$("#frm_report_entitycredit #datafim").prop({min: this.value});
});

/**
  * Search report stock in from date / date interval.
  */
$(document).on("submit", "#frm_report_entitycredit", async function(event) {

  	event.preventDefault();

	let form = $(this);

	FormDisable(form);

  	let data = {
		action: 'report_entitycredit_search',
		dataini: this.dataini.value,
		intervalo: this.intervalo.checked,
		datafim: this.datafim.value,
	}

    $(".report_entitycredit_none").addClass("hidden");
    $(".report_entitycredit_container").html(imgLoading);

	let response = await Post("report_entitycredit.php", data);

	if (response != null) {

		$(".report_entitycredit_container").html(response['data']);
		// $('.w-reportentitycredit-total').html(response['total_formatted']);
    } else {

		$(".report_entitycredit_container").html("");
		$(".report_entitycredit_none").removeClass("hidden");
	}

	FormEnable(form);
});