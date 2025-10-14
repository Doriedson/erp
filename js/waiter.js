class Waiter {

	constructor() {

		this._selected_table = null;
		this.tables = {};
	}

	setTable(id_table, description) {

		this._selected_table = id_table;

		if (!(this.tables["table_" + id_table])) {

			this.tables["table_" + id_table] = new Table(id_table, description);
		}
	}

	get selected_table() {

		return this._selected_table;
	}

	setVersao(versao) {
console.log ("new version: " + versao);
		this.tables["table_" + this._selected_table].setVersao(versao);
	}

	getVersao() {

		return this.tables["table_" + this._selected_table].getVersao();
	}


	getTableDescription(id_table) {

		return this.tables["table_" + id_table].description;
	}

	getTable(id_table) {

		return this.tables['table_' + id_table];
	}

	setProduct(id_produto, qty) {

		this.tables["table_" + this._selected_table].setProduct(id_produto, qty);
	}

	addProduct(id_produto, qty) {

		this.tables["table_" + this._selected_table].addProduct(id_produto, qty);
	}

	delProduct(id_table, id_produto, qty) {

		return this.tables["table_" + id_table].delProduct(id_produto, qty);
	}

	getQty(id_table, id_produto) {

		return this.tables['table_' + id_table].getQty(id_produto);
	}

	setObs(id_table, id_produto, obs) {

		this.tables['table_' + id_table].setObs(id_produto, obs);
	}

	getProductsID(id_table) {

		return this.tables['table_' + id_table].getProductsID();
	}

	getProducts(id_table) {

		return this.tables['table_' + id_table].getProducts();
	}

	clearTable(id_table) {

		delete this.tables['table_' + id_table];
	}
}

class Table {

	constructor(id, description) {

		this.id = id;
		this.description = description;
		this.products = {};
		this.versao = 0;
	}

	setVersao(versao) {

		this.versao = versao;
	}

	getVersao() {

		return this.versao;
	}

	setProduct(id_produto, qty) {

		if (("product_" + id_produto) in this.products) {

			// if (qty > 0) {

				this.products["product_" + id_produto].set(qty);

			// } else {

			if (qty == 0) {

					delete this.products["product_" + id_produto];
			}

		} else {

			if (qty > 0) {

				this.products["product_" + id_produto] = new Product(id_produto, qty, '');
			}
		}
	}

	addProduct(id_produto, qty) {

		let product_key = "product_" + id_produto;

		if (product_key in this.products) {

			this.products[product_key].add(qty);

		} else {

			this.products[product_key] = new Product(id_produto, qty, '');
		}
	}

	delProduct(id_produto, qty) {

		let product_key = "product_" + id_produto;

		if (product_key in this.products) {

			this.products[product_key].del(qty);

			if (this.products[product_key].qty == 0) {

				delete this.products[product_key];

				return true;
			}

			return false;
		}

		return true;
	}

	getQty(id_produto) {

		return this.products['product_' + id_produto].qty;
	}

	setObs(id_produto, obs) {

		if (("product_" + id_produto) in this.products) {

			this.products["product_" + id_produto].obs = obs;

		} else {

			$(".waiterproduct_" + id_produto + "_obs").val("");

			Message.Show("Adicione pelo menos 1 item antes de colocar observação!", Message.MSG_ERROR);
		}
	}

	getProductsID() {

		let products = [];

		Object.values(this.products).forEach(function(product, index) {

			products.push(product.id_product);
		});

		return products;
	}

	getProducts() {

		return this.products;
	}
}

class Product {

	constructor(id_product, qty, obs) {

		this._id_product = id_product;
		this._qty = qty;
		this._obs = obs;

		this.HUD();
	}

	HUD() {

		let field_qtd = $(".waiterproduct_" + this._id_product + "_qtd");

		field_qtd.html((this._qty).toLocaleString("pt-BR", {minimumFractionDigits: 0, maximumFractionDigits: 3}));

		let field_obs = $(".waiterproduct_" + this._id_product + "_obs");

		field_obs.val(this._obs);
		field_obs.html(this._obs);

		let tooltip = field_obs.closest(".waiterproduct_obs");

		let tooltiptext = tooltip.find(".tooltiptext");

		tooltiptext.html(this._obs);

		let button = tooltip.find(".bt_comment");

		if (this._obs.length == 0) {

			button.removeClass("fa-comment-dots");
			button.addClass("fa-comment");
			tooltip.removeClass("tooltip");

		} else {

			button.removeClass("fa-comment");
			button.addClass("fa-comment-dots");
			tooltip.addClass("tooltip");
		}

		if (this._qty == 0) {

			$(".waiterproduct_produto_" + this._id_product).removeClass("selected");
			// tooltip.find(".bt_comment").addClass("hidden");
			Disable(tooltip.find(".bt_comment"), false);
			// tooltip.find(".bt_comment").prop("disabled", "disabled");

		} else {

			$(".waiterproduct_produto_" + this._id_product).addClass("selected");
			// tooltip.find(".bt_comment").removeClass("hidden");
			Enable(tooltip.find(".bt_comment"));
			// tooltip.find(".bt_comment").prop("disabled", "");
		}

		WaiterOrderTotal();
	}

	add(qty) {

		this.set(this._qty + qty);
	}

	del(qty) {

		this.set(this._qty - qty);
	}

	set(qty) {

		this._qty = qty;

		if (this._qty < 0) this.qty = 0;

		if (this._qty == 0) this._obs = "";

		this.HUD();
	}

	get qty() {

		return this._qty;
	}

	set obs(obs) {

		this._obs = obs;

		this.HUD();
	}

	get obs() {

		return this._obs;
	}

	get id_product() {

		return this._id_product;
	}
}

let waiter = new Waiter();

let table_search_timeout = 0;

/**
 * Autosearch for tables
 * @param {*} field
 * @returns
 */
async function TableAutoSearch(field) {

	let id_mesa = field.val();
	let screen = field.data('screen'); // "waiter_table";

	if (field.data("last_search") == id_mesa) {

		return;
	}

	// if (field.data('screen') == "selfservice") {

	// 	screen = "selfservice";
	// }

	field.data("last_search", field.val());

	let dropdownlist = field.closest('.autocomplete-dropdown').find('.dropdown-list');

	if (dropdownlist.length == 0) {

		dropdownlist = field.closest('.autocomplete-dropdown').find('.w-waitertable-container');

		let data = {
			action: 'table_smart_search',
			value: id_mesa,
			screen: screen
		}

		response = await Post("waiter.php", data);

		if (response != null) {

			dropdownlist.html(response);

		} else {

			dropdownlist.html("");
		}

	} else {

		if (id_mesa == "") {

			dropdownlist.html("");
			return;
		}

		let data = {
			action: 'table_smart_search_popup',
			value: id_mesa,
		}

		response = await Post("waiter.php", data);

		if (response != null) {

			dropdownlist.html(response);

		} else {

			dropdownlist.html("");
		}
	}
}

/**
  * Loads table by name for autocomplete.
  */
 $(document).on("keyup", ".table_search", async function(event) {

	switch (event.keyCode) {

		case 38: // up
		case 40: // down
			return;

		break;
	}

	var field = $(this);

	clearTimeout(table_search_timeout);

	table_search_timeout = setTimeout(function() {

		TableAutoSearch(field);
	}, 300);
});

function WaiterInit(response) {

	$(".leftmenu_container").html(response['menu']);
	$(".leftmenu_container").removeClass("hidden");
	$(".body-header").removeClass("hidden");
	$("#body-container").html(response['data']);

	// $('.waiter-display').html("Seleção de mesa");
	// $('.waiter-hud').html($('.w_waitertable_header').html());
}

function WaiterProductUpdate() {

	let table = waiter.getTable(waiter.selected_table);

	// let field;

	Object.entries(table.products).forEach(function([key, product]) {

		product.HUD();
	});

	// 	field = $(".waiter" + product + "_qtd");

	// 	if (field.length) {

	// 		field.html((table.products[product].qty).toLocaleString("pt-BR", {minimumFractionDigits: 0, maximumFractionDigits: 3}));

	// 		$(".waiter" + product + "_obs").val(table.products[product].obs);

	// 		let button = field.closest(".waiterproduct_obs").find(".bt_comment");

	// 		if (table.products[product].obs.length == 0) {

	// 			button.removeClass("fa-comment-dots");
	// 			button.addClass("fa-comment");

	// 		} else {

	// 			button.removeClass("fa-comment");
	// 			button.addClass("fa-comment-dots");
	// 		}
	// 	}
	// });

	// WaiterOrderTotal();
}

function WaiterOrderTotal() {

	let waitertotal = $('.waiterorder-total');

	if (waitertotal.length == 0) return;

	let total = 0;

	let table = waiter.getTable(waiter.selected_table);

	Object.keys(table.products).forEach(function(product, index) {

		total += table.products[product].qty * $(".waiterproduct_produto_" + table.products[product]._id_product).data('preco');
	});

	waitertotal.html(total.toLocaleString("pt-BR", {minimumFractionDigits: 2, maximumFractionDigits: 2}));
}

/**
  * Form to waiter login
  */
$(document).on("submit", "#frm_waiter", async function(event) {

	event.preventDefault();

	var id_entidade = this.id_entidade.value;
	var pass = this.pass.value;

	var data = {
		action: "login",
		id_entidade: id_entidade,
		pass: pass
	}

	response = await Post("waiter.php", data)

	if(response) {

		WaiterInit(response);
	}
});

/**
  * Selects Table
  */
 $(document).on("click", ".waitertable_bt_select", async function(event) {

	let button = $(this);

	let container = button.closest('.waitertable_table');

	let id_mesa = container.data('id_mesa');

	// let mesa_desc = container.data('mesa');

	// let content = $("#body-container").html();

	let data = {
		action: "waiter_sector",
		id_mesa: id_mesa
	}

	if (container.data("versao")) {

		data["versao"] = container.data("versao");
	}

	response = await Post("waiter.php", data)

	if(response) {

		waiter.setTable(id_mesa, "");
		waiter.setVersao(response["versao"]);

		let content = $(response["data"]);

		$("#body-container").html(content);

		// $('.waiter-display').html("Pedido - " + waiter.getTableDescription(waiter.selected_table));
		// $('.waiter-hud').html($('.w_waitertable_header').html());

		content.find('#product_search').focus();
	}
});

/**
  * Opens table selection
  */
 $(document).on("click", ".waitertable_bt_table", async function(event) {

	var button = $(this);

	Disable(button);

	var data = {
		action: "waiter_table",
	}

	response = await Post("waiter.php", data)

	if(response) {

		$("#body-container").html(response);

		// $('.waiter-display').html("Seleção de mesa");
		// $('.waiter-hud').html($('.w_waitertable_header').html());

	} else {

		Enable(button);
	}
});

/**
  * Opens Self-Service
  */
 $(document).on("click", ".waitertable_bt_selfservice", async function(event) {

	var button = $(this);

	Disable(button);

	LoadPage('waiter_self_service.php');
});

/**
  * Opens sector selection
  */
 $(document).on("click", ".waitertable_bt_sector", async function(event) {

	let button = $(this);

	let id_mesa = button.data('id_mesa');

	Disable(button);

	let data = {
		action: "waiter_sector",
		versao: waiter.getVersao(),
		id_mesa: id_mesa
	}

	response = await Post("waiter.php", data)

	if(response) {

		waiter.setVersao(response["versao"]);

		$("#body-container").html(response["data"]);

	} else {

		Enable(button);
	}
});

/**
  * Adds product to table
  */
 $(document).on("click", ".waiterproduct_bt_add", async function(event) {

	let button = $(this);

	let id_produto = button.closest('.waiterproduct_produto').data('id_produto');

	waiter.addProduct(id_produto, 1);

	// WaiterProductUpdate();
	// $(".product_" + id_produto + "-qtd").html(waiter.getQty(waiter.selected_table, id_produto) + " x");

	// WaiterOrderTotal();
});

/**
  * Removes product to table
  */
 $(document).on("click", ".waiterproduct_bt_del", async function(event) {

	let button = $(this);

	let container = button.closest('.waiterproduct_produto');

	let id_produto = container.data('id_produto');

	if (waiter.delProduct(waiter.selected_table, id_produto, 1)) {

		if ($(".w-waiterorder-container").length > 0) {

			ContainerRemove(container, async function() {

				if ($('.waiterproduct_produto').length == 0) {

					let data = {
						action: "extra_block_product_none"
					}

					response = await Post("waiter.php", data)

					if(response) {

						$(".table").html(response);
					}
				}
			});
		}
	}
});

/**
  * Updates product obs
  */
$(document).on("change", ".waiterproduct_bt_obs", async function(event) {

	let field = $(this);

	let id_produto = field.closest('.waiterproduct_produto').data('id_produto');

	waiter.setObs(waiter.selected_table, id_produto, field.val());

	$('.float-form').addClass('hidden');
	$('.tooltiptext').removeClass('hidden');
});

/**
  * Updates product obs by confirma button
  */
$(document).on("click", ".waiterproduct_bt_obs_confirma", async function(event) {

	$('.float-form').addClass('hidden');
	$('.tooltiptext').removeClass('hidden');
});

/**
  * Opens revision order
  */
$(document).on("click", ".waitertable_bt_revision", async function(event) {

	let button = $(this);

	Disable(button);

	let content = $("#body-container").html();

	$("#body-container").html(imgLoading);

	let data = {
		action: "waiter_order_revision",
		id_mesa: waiter.selected_table,
		products: waiter.getProductsID(waiter.selected_table)
	}

	response = await Post("waiter.php", data)

	if(response) {

		$("#body-container").html(response["data"]);

		WaiterProductUpdate();

	} else {

		$("#body-container").html(content);
		$("#body-container").find('.waitertable_bt_order').removeClass('loading');
	}
});

/**
  * Sends order
  */
 $(document).on("click", ".waitertable_bt_order", async function(event) {

	let button = $(this);

	Disable(button);

	let content = $("#body-container").html();

	$("#body-container").html(imgLoading);

	let codes = waiter.getProductsID(waiter.selected_table);

	if(codes.length == 0) {

		Message.Show("Nenhum produto selecionado!", Message.MSG_ERROR);
		$("#body-container").html(content);
		$(".waitertable_bt_order").removeClass('loading');
		return
	}

	let products = waiter.getProducts(waiter.selected_table);

	let data = {
		action: "waiter_order",
		id_mesa: waiter.selected_table,
		versao: waiter.getVersao(),
		codes,
		...products
	};

	response = await Post("waiter.php", data);

	if(response) {

		waiter.clearTable(waiter.selected_table);

		$("#body-container").html(response["data"]);

	} else {

		$("#body-container").html(content);
		$("#body-container").find('.waitertable_bt_order').removeClass('loading');
	}
});

/**
  * Opens entity search
  */
 $(document).on("click", ".waiterorder_bt_entity", async function(event) {

	let button = $(this);
	let window = button.data("window");

	Disable(button);

	let data = {
		action: "waitertable_entity_search_open",
		window: window,
	};

	let response = await Post("waiter.php", data);

	if(response) {

		let content = $(response);

		$(".w_waiterorder_entidade").html(content);
		// button.closest('.addon').replaceWith(content);

		AutoFocus(content);

	} else {

		Enable(button);
	}
});

/**
  * Entity search.
  */
 $(document).on("submit", "#frm_waiterorder_entity", async function(event) {

	event.preventDefault();

	let form = $(this);
	let data = {};

	FormDisable(form);

	let field = $(this).find(".entity_search");

	let entidade = field.data("sku");

	if (entidade) {

		field.val(field.data('descricao'));

	} else {

		entidade = field.val();
	}

	if ($('.w-waiterorder-tableclose').length) {

		data = {
			action: 'waitertable_entity_search',
			id_mesa: waiter.selected_table,
			entidade: entidade,
			window: "waiter_order_products",
			versao: waiter.getVersao()
		}

	} else {

		data = {
			action: 'waitertable_entity_search',
			id_mesa: waiter.selected_table,
			entidade: entidade,
			window: "waiter_order_revision",
			products: waiter.getProductsID(waiter.selected_table),
			versao: waiter.getVersao()
		}
	}

	let response = await Post("waiter.php", data);

	if (response != null) {

		waiter.setVersao(response["versao"]);

		$("#body-container").html(response["data"]);

		if ($('.w-waiterorder-tableclose').length == 0) {

			WaiterProductUpdate();
		}

	} else {

		FormEnable(form);
		this.entity_search.select();
	}
});

/**
  * Entity search cancel.
  */
 $(document).on("click", ".waiterorder_bt_entity_cancel", async function() {

	// var container = $(this).closest('form');
	let window = $(this).closest('form').data('window');

	let data = {
		action: 'waitertable_entity_search_cancel',
		id_mesa: waiter.selected_table,
		window: window
	}

	var response = await Post("waiter.php", data);

	if (response != null) {

		$(".w_waiterorder_entidade").html(response);
		// container.replaceWith(response);

	} else {

		field.removeClass('loading');
	}
 });

 /**
  * Entity remove from table.
  */
  $(document).on("click", ".waiterorder_bt_entity_del", async function() {

	let form = $(this).closest('form');
	let button = $(this);
	let data = {};

	FormDisable(form, button);

	if ($('.w-waiterorder-tableclose').length) {

		//Tela de "ver mesa" para fechar para pagamento
		data = {
			action: 'waitertable_entity_del',
			window: "waiter_order_products",
			id_mesa: waiter.selected_table,
			versao: waiter.getVersao()
		}

	} else {

		//Tela de revisão de pedido para adicionar a mesa
		data = {
			action: 'waitertable_entity_del',
			window: "waiter_order_revision",
			id_mesa: waiter.selected_table,
			products: waiter.getProductsID(waiter.selected_table),
			versao: waiter.getVersao()
		}
	}
console.log(data);
	let response = await Post("waiter.php", data);

	if (response != null) {

		waiter.setVersao(response["versao"]);

		$("#body-container").html(response["data"]);

		if ($('.w-waiterorder-tableclose').length == 0) {

			WaiterProductUpdate();
		}

	} else {

		FormEnable(form);
	}
 });

 /**
  * Opens table products to close for payment
  */
  $(document).on("click", ".waitertable_bt_payment", async function(event) {

	let button = $(this);

	Disable(button);

	let id_mesa = waiter.selected_table;

	if (!await WaitertableLoadTableproducts(id_mesa, waiter.getTableDescription(id_mesa))) {

		Enable(button);
	}
});

async function WaitertableLoadTableproducts(id_mesa, mesa_desc) {

	let data = {
		action: "waitertable_table_products",
		id_mesa: id_mesa,
	};

	let response = await Post("waiter.php", data);

	if(response != null) {

		waiter.setTable(id_mesa, mesa_desc);
		waiter.setVersao(response["versao"]);

		$("#body-container").html(response["data"]);

		return true;
	}

	return false;
}

/**
  * Views table products
  */
 $(document).on("click", ".waitertable_bt_view", async function(event) {

	let button = $(this);

	let waitertable = button.closest('.waitertable_table');

	let id_mesa = waitertable.data('id_mesa');
	let mesa_desc = waitertable.data('mesa');

	Disable(button);

	if (!await WaitertableLoadTableproducts(id_mesa, mesa_desc)) {

		Enable(button);
	}
});

/**
  * Opens table products to close for payment
  */
 $(document).on("click", ".waitertable_bt_tableclose", async function(event) {

	let button = $(this);

	Disable(button);

	let data = {
		action: "waitertable_table_close",
		id_mesa: waiter.selected_table,
		versao: waiter.getVersao()
	};

	response = await Post("waiter.php", data);

	if(response) {

		$("#body-container").html(response);

	} else {

		Enable(button);
	}
});

/**
  * Reverse sale item
  */
 $(document).on("click", ".waiterproduct_bt_reverse", async function(event) {

	let button = $(this);

	let id_vendaitem = button.closest('.waiterproduct_produto').data("id_vendaitem");

	Disable(button);

	let data = {
		action: "waiterproduct_item_reverse",
		id_mesa: waiter.selected_table,
		id_vendaitem: id_vendaitem,
		versao: waiter.getVersao()
	};

	let success = function (response) {

		waiter.setVersao(response["versao"]);
		$("#body-container").html(response["data"]);
	}

	let error = function() {

		Enable(button);
	}

	let cancel = function() {

		Enable(button);
	}

	Authenticator.Authenticate(data, "waiter.php", success, error, cancel);
});

/**
  * Sets product qty to table
  */
 $(document).on("submit", "#frm_product_qty", async function(event) {

	event.preventDefault();

	let form = $(this);

	let popup = form.closest(".popup");

	let qty = parseFloat(this.qty.value);

	let id_produto = form.data('id_produto');

	waiter.setProduct(id_produto, qty);

	if (qty == 0) {

		let waiterrevision = $('.product_' + id_produto);

		// If screen is waiter_revision
		if (waiterrevision.length) {

			waiterrevision.remove();

			if ($('.waiterrevision').length == 0) {

				let data = {
					action: "extra_block_product_none"
				}

				let response = await Post("waiter.php", data)

				if(response) {

					$(".table").html(response);
				}
			}

		} else {

			$(".waiterproduct_" + id_produto + "_qtd").html("0");
			$(".waiterproduct_" + id_produto + "_obs").html("");
		}
	}

	Modal.Close(popup);
});

/**
  * Shows product qty popup
  */
 $(document).on("click", ".waiterproduct_bt_show_qty", async function() {

	let button = $(this);

	let id_produto = button.data("id_produto");

	Disable(button);

	let data = {
		action: "waitersector_popup_peso",
		id_produto: id_produto
	}

	let response = await Post("waiter.php", data);

	if (response != null) {

		Modal.Show(Modal.POPUP_SIZE_SMALL, "Balança", response, null);
	}

	Enable(button);
});

/**
  * expand waiter sector
  */
 $(document).on("click", ".waitersector_bt_expand", async function() {

	let button = $(this);

	let sector = button.closest('.w_waiter_product_sector');

	let id_produtosetor = sector.data("id_produtosetor");

	Disable(button);

    let expandable = sector.find(".expandable");

	let container = expandable.find(".product_table");

	button.removeClass("waitersector_bt_expand bt_expand fa-chevron-down");
	button.addClass("bt_collapse fa-chevron-up");

	let data = {
		action: 'waiter_product',
		id_produtosetor: id_produtosetor,
	}

	let response = await Post("waiter.php", data);

	if (response != null) {

		let content = $(response);

		container.html(content);

		WaiterProductUpdate();

	} else {

		// expandable.html("Ocorreu um erro ao carregar os produtos do setor.");
		expandable.find('.product_not_found').removeClass('hidden');
		container.html("");
	}

	expandable.slideDown("fast");
    Enable(button);
});

/**
  * Opens table transfer
  */
$(document).on("click", ".waitertable_bt_tabletransf", async function() {

	let button = $(this);

	let data = {
		action: 'load',
		id_mesa: button.data('id_mesa')
	}

	Disable(button);

	let response = await Post('waiter_table_transf.php', data);

	if (response != null) {

		$("#body-container").html(response);

	} else {

		Enable(button);
		MenuClose();
	}
});

/**
  * Clean product field search at sector
  */
$(document).on("click", ".waiterproduct_bt_clear", async function() {

	let field = $(".product_search");

	field.val("").focus();

	ProductSearchKeyup(field, 0);
});