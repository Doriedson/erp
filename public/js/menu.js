$(document).on("click",".menu .ul .li", function(event) {

	event.stopPropagation();

	let submenu_visible = $(this).find(".submenu").is(":visible");

	SubMenuClose();

	let submenu = $(this).find(".submenu");

	if (!submenu_visible) {

		submenu.slideDown("fast", function() {

			submenu[0].scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
		})
	}
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

// Mapeia endpoints legados (.php) para rotas novas do front controller
function legacyToUi(path) {
  try {
    // Normaliza quando vier absoluto (ex.: this.href => http://localhost:8080/home.php)
    const u = new URL(path, window.location.origin);
    const pathname = u.pathname; // "/home.php"
    const search   = u.search    || '';

    if (pathname.endsWith('.php')) {
      const slug = pathname.replace(/^\//, '').replace(/\.php$/, '');
      return `/ui/pages/${slug}${search}`;   // ex.: /ui/pages/home?...
    }
    // se já for uma rota nova, mantém
    return pathname + search;
  } catch {
    // fallback simples
    if (path.endsWith('.php')) {
      const slug = path.replace(/^\//, '').replace(/\.php$/, '');
      return `/ui/pages/${slug}`;
    }
    return path;
  }
}


async function LoadPage(path, payload = {}) {

	try {
		$("#body-container").html(imgLoading);

		// Para páginas UI não precisamos forçar action=load
		const html = await GET_HTML(path, payload);

		const $body = $('#body-container');

		if ($body.length) $body.html(html);

		// pós-carregamento (mantém seus cases)
		const page_id = cleanPath(path).split('/').pop();

		WindowManager.page = page_id;

		switch (page_id) {

			case 'bills_to_pay.php':
				billstopayChart = new MyChart($("#billstopay_chart"), MyChart.DOUGHNUT);
				billstopayChartPending = new MyChart($("#billstopay_pendingchart"), MyChart.DOUGHNUT);
				BillstopayUpdateChart();
				break;

			case 'wallet.php':
				walletDespesaChart = new MyChart($("#wallet_expense_chart"), MyChart.DOUGHNUT);
				walletReceitaChart = new MyChart($("#wallet_receita_chart"), MyChart.DOUGHNUT);
				WalletUpdateChart();
				$('.waiter-hud').html("Garçom");

				break;

			case 'waiter_table.php':
			case 'waiter_self_service.php':
			case 'waiter_table_transf.php':
				$('.waiter-hud').html($('.w_waitertable_header').html());
				break;

			case 'sale_order.php':
				$('.saleorder_loading').addClass('hidden');
				break;

			case 'report_cashdrain.php':
				$('#frm_report_cashdrain').submit();
				break;
		}

		AutoFocus(html);

	} catch (e) {
		console.error('Falha ao carregar página:', e, path);
		Message.Show('Não foi possível carregar a página.', Message.MSG_ERROR);
		$("#body-container").html("");
	}

}

$(document).on("click",".menu a", async function(event) {

	event.preventDefault();

	SubMenuClose();

	$(".leftmenu_container").removeClass('leftmenu-open');

	const a   = $(this);
	const raw = a.attr('data-page') || a.attr('href') || '';
	LoadPage(raw);
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