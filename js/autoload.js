/**
  * Validates logged user on page load.
  */
$(window).on("load", async function() {

	ServiceWorkerInit();

	// let sUsrAg = navigator.userAgent;

	let data = {
		action: "load"
	}

	let response = await Post("message.php", data);

	if (response != null) {

		Message.Set(response['message_info'], Message.MSG_INFO);
		Message.Set(response['message_error'], Message.MSG_ERROR);
		Message.Set(response['message_done'], Message.MSG_DONE);
		Message.Set(response['message_alert'], Message.MSG_ALERT);
		Modal.window = $(response['popup']);
		// Authenticator.window = $(response['authenticator']);
		MessageBox.window = $(response['messagebox']);
	}

	// data = {
	// 	action: "popup_load"
	// }

	// response = await Post("backend.php", data);

	// if (response != null) {

	// 	Modal.window = $(response);
	// }

	console.log(Modal.window);
	// if(sUsrAg.indexOf("Chrome") == -1) {
	// 	sBrowser = "Google Chrome"; <<- Compatível
	// } else if (sUsrAg.indexOf("Safari") > -1) {
	// 	sBrowser = "Apple Safari"; <<- Compatível
	// } else if (sUsrAg.indexOf("Opera") > -1) {
	// 	sBrowser = "Opera";
	// } else if (sUsrAg.indexOf("Firefox") > -1) {
	// 	sBrowser = "Mozilla Firefox";
	// } else if (sUsrAg.indexOf("MSIE") > -1) {
	// 	sBrowser = "Microsoft Internet Explorer";
		// Message.Show("Navegador incompatível!<br> Use:<br>Google Chrome<br>Opera Safari", Message.MSG_ERROR);
		// return;
	// }

	setTimeout( async function() {

		let module = $('#body-container').data('module');

		data = {
			action: "auth"
		}

		switch (module) {

			case "backend":

				response = await Post("backend.php", data);

				if (response != null) {

					$(".leftmenu_container").html(response);
					$(".leftmenu_container").removeClass("hidden");
					$(".body-header").removeClass("hidden");
					$("#body-container").html("");

					LoadPage("home.php");

					observerStart.notify("authenticated");

				} else {

					if (localStorage.getItem('token') !== null) {

						Message.Show("Sessão expirou!", Message.MSG_INFO);
					}
				}
			break;

			case "waiter":

				response = await Post("waiter.php", data);

				if (response != null) {

					WaiterInit(response);

				} else {

					if (localStorage.getItem('token')) {

						Message.Show("Sessão expirou!", Message.MSG_INFO);
					}

					// Logout();
				}
			break;

			case "pdv":

				response = await Post("pdv.php", data);

				if (response != null) {

					PdvInit(response);

				} else {

					if (localStorage.getItem('token')) {

						Message.Show("Sessão expirou!", Message.MSG_INFO);
					}

					// Logout();
				}
			break;

			default:

				Message.Show("Módulo não definido!", Message.MSG_ERROR);

				break;
		}
	}, 500);

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