function BillsToReceiveTotalCalc() {

	let total;

	$(".w_billstoreceive:has(.expandable:visible)").each(

		function(index, billstoreceive) {

			total = 0;

			$(".w_saleorder", billstoreceive).each(

				function(index, data) {

					total += parseFloat($(data).data("total"));
				}
			);

			$(".billstoreceive_total", billstoreceive).html(total.toLocaleString("pt-BR", {minimumFractionDigits: 2} ));

			$(billstoreceive).data('total', total);
		}
	);

	total = 0;

	$(".w_billstoreceive").each(

		function(index, data) {

			total += parseFloat($(data).data("total"));
		}
	);

	$(".billstoreceive_totalgeral").html(total.toLocaleString("pt-BR", {minimumFractionDigits: 2} ));
}

/**
 * Opens sale order on credit list from client
 */
 $(document).on("click", ".billsreceive_bt_expand", async function() {

	let button = $(this);

	let id_entidade = button.data("id_entidade");

    let expandable = button.closest('.w_billstoreceive').find(".expandable:first");

	Disable(button);

	// expandable.html(imgLoading);

	// expandable.removeClass("hidden");

	let data = {
		action: 'billsreceive_forwardsale',
		id_entidade: id_entidade,
	}

	let response = await Post("bills_to_receive.php", data);

	if (response != null) {

		expandable.find(".table").html(response);
		// expandable.html(response);

		button.removeClass("billsreceive_bt_expand bt_expand fa-chevron-down");
		button.addClass("bt_collapse fa-chevron-up");

	} else {
		expandable.find(".table").html("");
		// expandable.html("");
	}

	expandable.slideDown("fast");
    Enable(button);
});