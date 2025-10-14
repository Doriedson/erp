/**
 * Enables/Disables second date field for search.
 */
 $(document).on("click", "#frm_report_sale_product #intervalo", function() {

	$("#frm_report_sale_product #datafim").prop( "disabled", !this.checked);
	$("#frm_report_sale_product #pdv").prop( "disabled", this.checked);
});

/**
  * Defines datafim as minimum from dataini.
  */
$(document).on("change", "#frm_report_sale_product #dataini", function() {

	$("#frm_report_sale_product #datafim").prop({min: this.value});
});

/**
  * Search report total sales from date / date interval.
  */
$(document).on("submit", "#frm_report_sale_product", async function(event) {

  	event.preventDefault();

	var form = $(this);

	var datafim = $(this.datafim).prop('disabled');

	FormDisable(form);

  	// $(".report_sale_product_container").html("");

  	var data = { 
		action: 'report_sale_product_search', 
		dataini: this.dataini.value,
		intervalo: this.intervalo.checked,
		datafim: this.datafim.value,
	}

	var response = await Post("report_sale_product.php", data);

	if (response != null) {

		$(".report_sale_product_container").html(response['data']);
		$(".w-reportsale-total").html(response['total_formatted']);
	}

	FormEnable(form);
	$(this.datafim).prop('disabled', datafim);
});