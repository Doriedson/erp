/**
  * Validates logged user on page load.
  */
$(window).on("load", async function() {

	Authenticator.bootstrap();

	/* AutoUpdate to Vendas/Delivery/Pedidos Screen */
	setInterval(async function(){

		let saleorder_container = $(".w_saleorder_container");

		if (saleorder_container.length > 0) {

			let window = saleorder_container.data('window');

			let data = {
				action: "saleorder_update_screen",
				window: window
			}

			let response = await Post("sale_order.php", data);

			if (response != null) {

				SaleOrderHUD(response['total_andamento'], response['total_efetuado'], response['total_producao'], response['total_entrega']);

				switch (window) {

					case "saleorder_andamento":
					case "saleorder_efetuado":
					case "saleorder_producao":
					case "saleorder_entrega":

						$(".w_saleorder_container .tbody").html(response['extra_block_orders']);

						if ($('.w_saleorder').length > 0) {

							$('.saleorder_notfound').addClass('hidden');

						} else {

							$('.saleorder_notfound').removeClass('hidden');
						}

						break;
				}
			}
		}
	}, 30000);
});