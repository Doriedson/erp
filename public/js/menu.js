/**
  * Events
  */
// $(document).on("mouseenter",".menu .ul .li", function() {

// 	var width = window.innerWidth
// 	|| document.documentElement.clientWidth
// 	|| document.body.clientWidth;

// 	if (width > 767) {

// 		$(this).find(".submenu").css('display','block');
// 	}
// });

// $(document).on("mouseleave",".menu .ul .li", function() {

// 	var width = window.innerWidth
// 	|| document.documentElement.clientWidth
// 	|| document.body.clientWidth;

// 	if (width > 767) {

// 		$(this).find(".submenu").css('display','none');
// 	}
// });

$(document).on("click",".menu .ul .li", function(event) {

	event.stopPropagation();
	// var width = window.innerWidth
	// || document.documentElement.clientWidth
	// || document.body.clientWidth;

	// if (width <= 767) {
	let submenu_visible = $(this).find(".submenu").is(":visible");

	SubMenuClose();

	let submenu = $(this).find(".submenu");

	if (!submenu_visible) {

		submenu.slideDown("fast", function() {

			submenu[0].scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
		})
	}
		// submenu.css('display', "block");

	// } else {

	// 	submenu.css('display', "none");
	// }
	// }
});

$(document).on("click",".bt_logout", function(event) {

	let button = $(this);

	Disable(button);

	Logout();
});

$(document).on("click",".bt_about", async function() {

	let data = {
		action: 'load'
	};

	let response = await Post("about.php", data);

	if (response != null) {

		Modal.Show(Modal.POPUP_SIZE_SMALL, "Sistema ERP", response, null);
	}
});

async function LoadPage(page, data = {}) {

	let page_id = page.split('/').pop();

	WindowManager.page = page_id;

	// if (page_id == "logout") {

	// 	Logout();
	// 	return;
	// }

	data['action'] = 'load';

	// if (page_id != "about.php") {

		$("#body-container").html(imgLoading);
	// }

	response = await Post(page, data);

	if (response != null) {

		let content = "";

		if (response.data) {

			content = $(response['data']);

		} else {

			content = $(response);
		}

		// if (page_id == "about.php") {

		// 	Modal.Show(Modal.POPUP_SIZE_SMALL, "Sobre", content, null);
		// 	return;
		// }

		$("#body-container").html(content);

		switch (page_id) {

			case "bills_to_pay.php":

				billstopayChart = new MyChart($("#billstopay_chart"), MyChart.DOUGHNUT);
				billstopayChartPending = new MyChart($("#billstopay_pendingchart"), MyChart.DOUGHNUT);

				BillstopayUpdateChart();

				break;

			case "wallet.php":

				walletDespesaChart = new MyChart($("#wallet_expense_chart"), MyChart.DOUGHNUT);
				walletReceitaChart = new MyChart($("#wallet_receita_chart"), MyChart.DOUGHNUT);

				WalletUpdateChart();

				if($('.body-container').data('module') == "waiter") {

					// $('.waiter-display').html("Garçom");
					$('.waiter-hud').html("Garçom");
				}

				break;

			case "waiter_table.php":

				// $('.waiter-display').html("Seleção de mesa");
				$('.waiter-hud').html($('.w_waitertable_header').html());

				break;

			case "waiter_self_service.php":

				// $('.waiter-display').html("Self-Service");
				$('.waiter-hud').html($('.w_waitertable_header').html());

				break;

			case "waiter_table_transf.php":

				// $('.waiter-display').html("Transferência de mesa");
				$('.waiter-hud').html($('.w_waitertable_header').html());

				break;

			case "sale_order.php":

				$('.saleorder_loading').addClass('hidden');
				break;

			case "report_cashdrain.php":

				$('#frm_report_cashdrain').submit();
				break;
		}

		AutoFocus(content);

	} else {

		$("#body-container").html("");
	}
}

$(document).on("click",".menu a", async function(event) {

	event.preventDefault();
// console.log("click " + this.href);
	// var width = window.innerWidth
	// || document.documentElement.clientWidth
	// || document.body.clientWidth;

	// $(this).closest(".submenu").css('display','none');
	SubMenuClose();

	// if (width < 1190) {

		$(".leftmenu_container").removeClass('leftmenu-open');
	// }

	LoadPage(this.href);
});

$(document).on("click",".button_menu", async function() {

	if ($(".leftmenu_container").hasClass('leftmenu-open')) {

		$(".leftmenu_container").removeClass('leftmenu-open');

	} else {

		$(".leftmenu_container").addClass('leftmenu-open');
	}
});

$(document).on("click",".popup-main-button", async function() {

	let button = $(this);

	let popup_menu = $(this).closest('.popup-menu');

	let ul = popup_menu.find("ul");

	let menu_visible = (ul.css("display") == "block");

	MenuClose();

	if (!menu_visible) {

		ul.slideDown("fast");

		// popup_menu.addClass('popup-menu-show');
		button.removeClass('fa-ellipsis-vertical');
		button.addClass('fa-arrow-down');
	}
});

$(document).on("click",".menu-inter-button", function() {

	let button = $(this);

	// let menu_inter = button.closest('.menu-inter');

	let ul = button.siblings("ul");

	let show = (ul.css("display") == "block");

	MenuClose();

	if (!show) {

		// menu_inter.addClass('menu-show');
		button.removeClass('fa-ellipsis-vertical');
		button.addClass('fa-arrow-up');

		ul.slideDown("fast", function() {
			ul[0].scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
		});
	}
});

function MenuClose() {

	$(".menu-inter").find("ul:visible").slideUp("fast");
	// $(".menu-inter").removeClass('menu-show');
	$(".menu-inter-button").removeClass('fa-arrow-up');
	$(".menu-inter-button").addClass('fa-ellipsis-vertical');

	$('.popup-menu').find("ul:visible").slideUp("fast");

	// $('.popup-menu').removeClass('popup-menu-show');
	$(".popup-main-button").removeClass('fa-arrow-down');
	$(".popup-main-button").addClass('fa-ellipsis-vertical');
}

function SubMenuClose() {

	$('.submenu:visible').slideUp("fast");
}