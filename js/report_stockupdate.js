/**
 * Enables/Disables second date field for search.
 */
 $(document).on("click", "#frm_report_stockupdate #intervalo", function() {

	$("#frm_report_stockupdate #datafim").prop( "disabled", !this.checked);
});

/**
 * Enables/Disables product field for search.
 */
 $(document).on("click", "#frm_report_stockupdate #produto", function() {

	$("#frm_report_stockupdate #product_search").prop( "disabled", !this.checked);
});

/**
  * Defines datafim as minimum from dataini.
  */
$(document).on("change", "#frm_report_stockupdate #dataini", function() {

	$("#frm_report_stockupdate #datafim").prop({min: this.value});
});

/**
  * Search report stock in from date / date interval.
  */
$(document).on("submit", "#frm_report_stockupdate", async function(event) {

  	event.preventDefault();

	let form = $(this);

	let datafim = $(this.datafim).prop('disabled');

	let product_search = $(this.product_search).prop('disabled');

	let field = $(this.product_search);

	let produto = field.data("sku");

	if (produto) {

		field.val(field.data('descricao'));

	} else {

		produto = field.val();
	}

	FormDisable(form);

	let data = { 
		action: 'report_stockupdate_search',
		byproduct: this.produto.checked, 
		produto: produto,
		dataini: this.dataini.value,
		intervalo: this.intervalo.checked,
		datafim: this.datafim.value,
		stocktype: this.stock_type.value
	}

	let response = await Post("report_stockupdate.php", data);

	if (response != null) {

		FormEnable(form);
		$(this.datafim).prop('disabled', datafim);
		$(this.product_search).prop('disabled', product_search);

		if (response.length == 0) {

			$(".table_stockupdate").html("");

			$('.report_stockupdate_notfound').removeClass('hidden');
			
			this.dataini.select();

		} else {

			$('.report_stockupdate_notfound').addClass('hidden');

			let container = $(response);

			$(".table_stockupdate").html(container);

			// this.product_search.value = "";
		}
	} else {

		FormEnable(form);
		$(this.datafim).prop('disabled', datafim);
		$(this.product_search).prop('disabled', product_search);
	}
});