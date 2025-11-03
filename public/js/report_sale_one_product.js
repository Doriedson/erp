/**
 * Enables/Disables second date field for search.
 */
 $(document).on("click", "#frm_report_sale_one_product #intervalo", function() {

	$("#frm_report_sale_one_product #datafim").prop( "disabled", !this.checked);
	$("#frm_report_sale_one_product #pdv").prop( "disabled", this.checked);
});

/**
  * Defines datafim as minimum from dataini.
  */
$(document).on("change", "#frm_report_sale_one_product #dataini", function() {

	$("#frm_report_sale_one_product #datafim").prop({min: this.value});
});

/**
  * Search report total sales from date / date interval.
  */
$(document).on("submit", "#frm_report_sale_one_product", async function(event) {

  	event.preventDefault();

	let form = $(this);

	let datafim = $(this.datafim).prop('disabled');

    let field = $(this.product_search);

	let produto = field.data("sku");

	if (produto) {

		field.val(field.data('descricao'));

	} else {

		produto = field.val();
	}

	FormDisable(form);

  	// $(".report_sale_product_container").html("");

  	let data = { 
		action: 'report_sale_one_product_search', 
        produto: produto,
		dataini: this.dataini.value,
		intervalo: this.intervalo.checked,
		datafim: this.datafim.value,
	}

	$(".reportsale_not_found").addClass('hidden');
	let container = $(".reportsaleoneproduct_container");

	container.html(imgLoading);

	let response = await Post("/ui/products/history/search", data);

	if (response != null) {
		
		let datasets = response['chart'];
		let filter = response["filter"];
		let total = response["total"];

		const popupPayload = {
			produto: produto
		};

		response = await Post("/ui/products/history/popup-data", popupPayload);

		if (response != null) {

			let content = $(response);

			content.find(".w_reportsaleoneproduct_filter").html(filter);
			content.find(".w_reportsaleoneproduct_total").html(total);

			let chart_container = content.siblings(".w_reportsaleoneproduct_graph_container");

			container.html(content);

			let myChart = new MyChart();

			let div;

			datasets.forEach(function(dataset) {

				div = document.createElement("div");
				div.classList.add("flex-2-col");
				$(div).html(myChart.getBar(dataset));

				chart_container.append(div);

			});

		}

	} else {

		container.html("");
		$(".reportsale_not_found").removeClass('hidden');
	}

	FormEnable(form);
	$(this.datafim).prop('disabled', datafim);
	this.product_search.value = "";
});

/**
 * Closes chart.
 */
 $(document).on("click", ".bt_reportsale_oneproduct_close", function() {

	$(this).closest('.reportsaleoneproduct_container').remove();

	if ($('.reportsaleoneproduct_container').length == 0) {

		$(".reportsale_not_found").removeClass('hidden');
	}

});
