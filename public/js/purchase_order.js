function PurchaseTotalUpdate() {

	var total = 0;

	var container = $('.purchaseorder_table');

	$(".w-purchaseorder", container).each(
		function(index, purchaseorder) {
			total += parseFloat($(purchaseorder).data("total"));
		}
	);

	$('.w-purchaseorder-total').html(total.toLocaleString("pt-BR", {minimumFractionDigits: 2, maximumFractionDigits: 2}));
}

function CalcTotalTablePurchaseOrderItem(purchaseorder, id_compra) {

	var total = 0;
	var vol = 0;
	var custo = 0;

	$(".w-purchaseorder-item", purchaseorder).each(
		function(index, purchaseitem) {

			vol = $(purchaseitem).data("vol");
			custo = $(purchaseitem).data("custo");
			total += vol * custo;
		}
	);

	purchaseorder.data('total', total);

	$(".purchaseorder-total", purchaseorder).html(total.toLocaleString("pt-BR", {minimumFractionDigits: 2, maximumFractionDigits: 2}));

	PurchaseTotalUpdate();
}

/**
  * Event button to save the new purchase order
  */
$(document).on("submit", "#frm_purchase_order", async function(event) {

	event.preventDefault();

	let form = $(this);

	let popup = form.closest(".popup");

	let field = $(this.provider_search);

	FormDisable(form);

	let container = $(".purchaseorder_table");

	let obs = this.obs;
	let lista = this.lista;

	let provider = field.data("sku");

	if (provider) {

		field.val(field.data('descricao'));

	} else {

		provider = field.val();
	}

	let data = {
		action: 'purchase_order_new',
		data: this.data.value,
		fornecedor: provider,
		obs: obs.value,
		lista: lista.value,
	}

	let response = await Post("purchase_order.php", data);

	if (response != null) {

		lista.selectedIndex = 0;
		field.val("");
		obs.value = "";

		$('.purchaseorder_not_found').addClass('hidden');

		let div = $(response);
		container.append(div);

		ContainerFocus(div, true);

		FormEnable(form);

		if (popup.find('.popup_fixwindow').hasClass('fa-thumbtack')) {

			Modal.Close(popup);

		} else {

			AutoFocus(form);
		}

	} else {

		FormEnable(form);
	}
});

/**
 * Enables/Disables second date field for search.
 */
$(document).on("click", "#frm_purchase_order_search #intervalo", function() {

	$("#frm_purchase_order_search #datafim").prop( "disabled", !this.checked);

});

/**
 * Estimates purchase order value.
 */
$(document).on("click", ".bt_purchaseorder_estimate", async function() {

	let button = $(this);
	let id_compra = button.data('id_compra');

	// let container = button.closest(".w-purchaseorder");

	Disable(button);

	let data = {
		action: "purchaseorder_estimate",
		id_compra: id_compra
	}

	await Post("purchase_order.php", data);

	Enable(button);
	MenuClose();
});

/**
 * Deletes purchase order.
 */
$(document).on("click", ".bt_purchase_order_delete", async function() {

	var button = $(this);
	var id_compra = button.data('id_compra');

	var container = button.closest(".w-purchaseorder");

	button.addClass('disabled');

	var data = {
		action: "purchase_order_delete",
		id_compra: id_compra
	}

	var response = await Post("purchase_order.php", data);

	if (response != null) {

		var content = $(response);
		container.replaceWith(content);

		ContainerFocus(content);

		PurchaseTotalUpdate();
	}

	MenuClose();
});

/**
 * Prints purchase order for conference.
 */
$(document).on("click", ".purchase_order_bt_print", async function() {

	var button = $(this);

	button.addClass('disabled');

	var id_compra = $(this).data("id_compra");

	var data = {
		action: "purchase_order_print",
		id_compra: id_compra
	}

	// var response =
	await GET("cgi.php", data);

	// if (response != null) {

		button.removeClass('disabled');

		MenuClose();
	// }
});

/**
  * Adds item for purchase order
  */
$(document).on("submit", "#frm_purchase_order_item", async function(event) {

	event.preventDefault();

	var form = $(this);

	var field = $(this.product_search);

	var container = form.closest('.w-purchaseorder').find('.tbody');

	var id_compra = $(this).data('id_compra');

	var id_produto = field.data("sku");

	if (id_produto) {

		field.val(field.data('descricao'));

	} else {

		id_produto = field.val();
	}

	FormDisable(form);

	var data = {
		action: 'purchase_order_item_add',
		id_compra: id_compra,
		value: id_produto
	}

	var response = await Post("purchase_order.php", data);

	if (response != null) {

		container.find('.w-purchaseorder-item-notfound').remove();
		var content = $(response);

		container.append(content);

		this.product_search.value = "";

		ContainerFocus(content);

	}

	FormEnable(form);

	AutoFocus(form);
});

/**
  * Deletes purchase order item
  */
 $(document).on("click", ".bt_delete_purchase_order_item", async function() {

	let button = $(this);

	let purchaseorder = button.closest(".w-purchaseorder");

	let tr = button.closest(".tr");

	let id_compra = purchaseorder.data("id_compra");

	Disable(button);

	let yes = async function() {
		let data = {
			action: 'purchase_order_item_del',
			value: button.data("id_compraitem"),
			id_compra: id_compra
		}

		let response = await Post("purchase_order.php", data);

		if (response != null) {

			if (response.length) {

				ContainerRemove(tr, function() {

					CalcTotalTablePurchaseOrderItem(purchaseorder, id_compra);
					purchaseorder.find('.tbody').html(response);
				});

			} else {

				ContainerRemove(tr, function() {

					CalcTotalTablePurchaseOrderItem(purchaseorder, id_compra);
				});
			}
		}

		return true;
	}

	let no = async function() {

		Enable(button);
	}

	MessageBox.Show("Excluir produto da Ordem de Compra?", yes, no);
});

/**
  * Open "data" edition
  */
$(document).on("click", ".purchase_order_bt_data", async function() {

	let button = $(this);

	// let div = $(this).closest("div");
	let id_compra = $(this).closest('.window').data('id_compra');

	// div.html(imgLoading);

	let data = {
		action: 'purchase_order_date_edit',
		id_compra: id_compra
	}

	let response = await Post("purchase_order.php", data);

	if (response != null) {

		let content = $(response);

		button.replaceWith(content);
		AutoFocus(content);
		// div.html(response);
		// div.find("input").focus();
	} else {

		Enable(button);
	}
});

/**
  * Cancels "data" edition.
  */
$(document).on("focusout", "#frm_purchase_order_date #data", async function() {

	//Prevents focusout on save
	if ($(this).prop('disabled')) {
		return;
	}

	let form = $(this).closest("form");

	FormDisable(form);

	let id_compra = $(this).closest('form').data('id_compra');

	let data = {
		action: 'purchase_order_date_cancel',
		id_compra: id_compra,
	}

	let response = await Post("purchase_order.php", data);

	if (response != null) {

		form.replaceWith(response);

	} else {

		FormEnable(form);
	}
});

/**
  * Saves "data" edition.
  */
$(document).on("change", "#frm_purchase_order_date #data", async function(event) {


	let form = $(this).closest("form");
	let id_compra = form.data("id_compra");
	let date = this.value;

	FormDisable(form);

	let data = {
		action: 'purchase_order_date_save',
		id_compra: id_compra,
		data: date,
	}

	let response = await Post("purchase_order.php", data);

	if (response != null) {

		form.replaceWith(response);

	} else {

		FormEnable(form);
	}
});

/**
  * Open purchase order provider edition
  */
$(document).on("click", ".purchase_order_bt_provider", async function() {

	let button = $(this);

	var id_compra = $(this).closest('.window').data('id_compra');

	var data = {
		action: 'purchase_order_provider_edit',
		id_compra: id_compra,
	}

	Disable(button);

	var response = await Post("purchase_order.php", data);

	if (response != null) {

		let content = $(response);
		button.replaceWith(content);
		AutoFocus(content);

	} else {

		Enable(button);
	}
});

/**
  * Cancels purchase order provider edition.
  */
$(document).on("click", ".purchaseorder_bt_provider_cancel", async function() {

	var container = $(this).closest('form');
	var id_compra = container.data('id_compra');

	var data = {
		action: 'purchase_order_provider_cancel',
		id_compra: id_compra,
	}

	container.html(imgLoading);

	var response = await Post("purchase_order.php", data);

	if (response != null) {

		container.replaceWith(response);
	}
});

/**
  * Saves purchase order provider edition.
  */
$(document).on("submit", "#frm_purchase_order_provider", async function(event) {

	event.preventDefault();

	var form = $(this).closest('form');
	var id_compra = $(this).data('id_compra');

	FormDisable(form);

	var provider = $(this.razaosocial).data("sku");

	if (!provider) {

		provider = this.razaosocial.value;
	}

	var data = {
		action: 'purchase_order_provider_save',
		id_compra: id_compra,
		razaosocial: provider,
	}

	var response = await Post("purchase_order.php", data);

	if (response != null) {

		form.replaceWith(response);

	} else {

		FormEnable(form);
		AutoFocus(form);
	}
});

/**
  * Save "note" edition.
  */
$(document).on("submit", ".frm_purchase_order_note", async function(event) {

	event.preventDefault();

	let form = $(this);

	let container = form.closest('.w_purchaseorder_obs');

	let id_compra = form.data("id_compra");
	let obs = form.find(".field_obs").val();

	FormDisable(form);

	let data = {
		action: 'purchase_order_note_save',
		id_compra: id_compra,
		obs: obs,
	}

	let response = await Post("purchase_order.php", data);

	if (response != null) {

		container.replaceWith(response);
	}

	FormEnable(form);
});

/**
  * Saves purchase order item note edition.
  */
$(document).on("submit", ".frm_purchase_order_item_obs", async function(event) {

	event.preventDefault();

	let form = $(this);

	let container = form.closest('.w_purchaseorderitem_obs');

	let id_compraitem = form.data('id_compraitem');
	let obs = form.find(".field_obs").val();

	let data = {
		action: 'purchase_order_item_note_save',
		id_compraitem: id_compraitem,
		obs: obs,
	}

	FormDisable(form);

	let response = await Post("purchase_order.php", data);

	if (response != null) {

		container.replaceWith(response);

	} else {

		FormEnable(form);
	}
});

/**
  * Defines datafim as minimum from dataini.
  */
$(document).on("change", "#frm_purchase_order_search #dataini", function() {

 	$(this).closest('form').find("#datafim").prop({min: this.value});

});

/**
  * Searchs purchase order.
  */
$(document).on("submit", "#frm_purchase_order_search", async function(event) {

	event.preventDefault();

	var form = $(this);

	var container = $(".purchaseorder_table");

	var datafim_field = $("#frm_purchase_order_search #datafim").prop( "disabled");

	FormDisable(form);

	var data = {
		action: 'purchase_order_search',
		status: this.status.value,
		dataini: this.dataini.value,
		intervalo: this.intervalo.checked,
		datafim: this.datafim.value,
	}

	var response = await Post("purchase_order.php", data);

	if (response != null) {

		if(response.length == 0) {

			container.html("");
			$('.purchaseorder_not_found').removeClass('hidden');

		} else {

			$('.purchaseorder_not_found').addClass('hidden');
			container.html(response);
		}

		PurchaseTotalUpdate();
	}

	FormEnable(form);
	$("#frm_purchase_order_search #datafim").prop( "disabled", datafim_field);
	// AutoFocus(form);
});

/**
  * Event button to launch purchase order
  */
$(document).on("click", ".bt_purchase_order_close", async function() {

	let button = $(this);

	let container = button.closest(".w-purchaseorder");

	let id_compra = button.data("id_compra");

	Disable(button);

	let data = {
		action: "purchase_order_close",
		id_compra: id_compra,
	}

	let response = await Post("purchase_order.php", data);

	if (response != null) {

		let content = $(response)
		container.replaceWith(content);

		ContainerFocus(content, false);
		PurchaseTotalUpdate();

	} else {

		Enable(button);
	}
});

/**
  * Event button to list purchase order with opened status
  */
$(document).on("click", ".purchase_order_bt_list", async function() {

	var button = $(this);

	var container = $(".purchaseorder_table");

	Disable(button);

	var data = {
		"action": "purchase_order_list",
	}

	var response = await Post("purchase_order.php", data);

	if (response != null) {

		if (response.length == 0) {

			$('.purchaseorder_not_found').removeClass('hidden');
			container.html("");

		} else {

			$('.purchaseorder_not_found').addClass('hidden');
			container.html(response);
		}

		PurchaseTotalUpdate();
	}

	Enable(button);

	MenuClose();
});

/**
  * Event button to send purchase order by WhatsApp
  */
$(document).on("click", ".purchase_order_bt_whatsapp", async function() {

	var button = $(this);

	var id_compra = button.data('id_compra');

	button.addClass('disabled');

	data = {
		action: 'purchase_order_whatsapp',
		id_compra: id_compra,
	}

	var response = await Post("purchase_order.php", data);

	if (response != null) {

		window.open("https://api.whatsapp.com/send?text=" + response, "_blank");
	}

	button.removeClass('disabled');

	MenuClose();
});

/**
  * Event button to copy sale order to clipboard
  */
$(document).on("click", ".purchase_order_bt_copy", async function() {

	var button = $(this);

	var id_compra = button.data('id_compra');

	button.addClass('disabled');

	data = {
		action: 'purchase_order_whatsapp',
		id_compra: id_compra,
	}

	var response = await Post("purchase_order.php", data);

	if (response != null) {

		response = response.replace(/%0A/g, "\n");

		CopyAndPaste(response);
	}

	button.removeClass('disabled');

	MenuClose();
});

/**
  * Opens "preco" edition
  */
// $(document).on("click", ".purchase_order_product_bt_preco", async function() {

// 	var button = $(this);

// 	var id_compraitem = button.data('id_compraitem');
// 	var id_produto = button.data('id_produto');

// 	button.addClass("loading");

// 	data = {
// 		action: "purchase_item_preco_edit",
// 		id_compraitem: id_compraitem,
// 		id_produto: id_produto,
// 	}

// 	response = await Post("purchase_order.php", data);

// 	if (response != null) {

// 		var content = $(response);
// 		button.replaceWith(content);

// 		AutoFocus(content);

// 	} else {

// 		button.removeClass("loading");
// 	}
// });

/**
  * Cancels "preco" edition.
  */
// $(document).on("focusout", "#frm_purchase_preco #preco", async function() {

// 	//Prevents focusout on save
//  	if ($(this).prop('disabled')) {
//  		return;
//  	}

// 	var form = $(this).closest('form');

// 	var purchaseItem = form.closest('.w-purchaseorder-item');

// 	var id_compraitem = purchaseItem.data('id_compraitem');
// 	var id_produto = purchaseItem.data('id_produto');

// 	FormDisable(form);

// 	data = {
// 		action: "purchase_item_preco_get",
// 		id_compraitem: id_compraitem,
// 		id_produto: id_produto,
// 	}

// 	response = await Post("purchase_order.php", data);

// 	if (response != null) {

// 		form.replaceWith(response);

// 	} else {

// 		FormEnable(form);
// 	}
// });

/**
  * Saves "preço" edition.
  */
// $(document).on("submit", "#frm_purchase_preco", async function(event) {

// 	event.preventDefault();

// 	var form = $(this);
// 	// var group = form.closest('.group-preco');

// 	var purchaseItem = form.closest('.w-purchaseorder-item');

// 	var id_compraitem = purchaseItem.data('id_compraitem');
// 	var id_produto = purchaseItem.data('id_produto');

// 	var preco = this.preco.value;

// 	FormDisable(form);

// 	data = {
// 		action: "purchase_item_preco_save",
// 		id_compraitem: id_compraitem,
// 		id_produto: id_produto,
// 		preco: preco,
// 	}

// 	response = await Post("purchase_order.php", data);

// 	if (response != null) {

// 		$('.preco_' + id_produto).replaceWith(response);
// 		// group.replaceWith(response);

// 	} else {

// 		FormEnable(form);
// 		AutoFocus(form);
// 	}
// });

/**
  * Opens "preco_promo" edition
  */
// $(document).on("click", ".purchase_order_product_bt_promo", async function() {

// 	var button = $(this);

// 	var id_compraitem = button.data('id_compraitem');
// 	var id_produto = button.data('id_produto');

// 	button.addClass("loading");

// 	data = {
// 		action: "purchase_item_promo_edit",
// 		id_compraitem: id_compraitem,
// 		id_produto: id_produto,
// 	}

// 	response = await Post("purchase_order.php", data);

// 	if (response != null) {

// 		var content = $(response);
// 		button.replaceWith(content);

// 		AutoFocus(content);

// 	} else {

// 		button.removeClass("loading");
// 	}
// });

/**
  * Cancels "preco_promo" edition.
  */
// $(document).on("focusout", "#frm_purchase_promo #preco_promo", async function() {

// 	//Prevents focusout on save
//  	if ($(this).prop('disabled')) {
//  		return;
//  	}

// 	var form = $(this).closest('form');

// 	var purchaseItem = form.closest('.w-purchaseorder-item');

// 	var id_compraitem = purchaseItem.data('id_compraitem');
// 	var id_produto = purchaseItem.data('id_produto');

// 	// field.addClass("loading");
// 	FormDisable(form);

// 	data = {
// 		action: "purchase_item_promo_get",
// 		id_compraitem: id_compraitem,
// 		id_produto: id_produto,
// 	}

// 	response = await Post("purchase_order.php", data);

// 	if (response != null) {

// 		form.replaceWith(response);

// 	} else {

// 		FormEnable(form);
// 	}
// });

/**
  * Saves "preco_promo" edition.
  */
// $(document).on("submit", "#frm_purchase_promo", async function(event) {

// 	event.preventDefault();

// 	var form = $(this);

// 	var purchaseItem = form.closest('.w-purchaseorder-item');

// 	// var group = form.closest('.group-preco');
// 	var id_compraitem = purchaseItem.data('id_compraitem');
// 	var id_produto = purchaseItem.data('id_produto');
// 	var preco_promo = this.preco_promo.value;

// 	FormDisable(form);

// 	data = {
// 		action: "purchase_item_promo_save",
// 		id_compraitem: id_compraitem,
// 		id_produto: id_produto,
// 		preco_promo: preco_promo,
// 	}

// 	response = await Post("purchase_order.php", data);

// 	if (response != null) {

// 		$('.preco_' + id_produto).replaceWith(response);
// 		// group.html(response);

// 	} else {

// 		FormEnable(form);
// 		AutoFocus(form);
// 	}
// });

/**
  * Opens purchase order information
  */
$(document).on("click", ".purchase_bt_expand", async function() {

	let button = $(this);

	let purchaseorder = button.closest('.w-purchaseorder');

	let id_compra = purchaseorder.data("id_compra");

    let expandable = purchaseorder.children(".expandable:first");

    Disable(button);

	button.removeClass("purchase_bt_expand fa-chevron-down");
	button.addClass("bt_collapse fa-chevron-up");

	let data = {
		action: 'purchase_order_product',
		id_compra: id_compra,
	}

	let response = await Post("purchase_order.php", data);

	if (response != null) {

		let purchaseItem = $(response['data']);

		expandable.html(purchaseItem);

		if ($('.w-purchaseorder-item', purchaseItem).length	== 0) {

			AutoFocus(purchaseItem);
		}

	} else {

		expandable.html("");
	}

	expandable.slideDown("fast");
    Enable(button);
});

/**
  * Opens "vol" edition
  */
$(document).on("click", ".purchase_order_item_bt_vol", async function() {

	var button = $(this);

	var purchaseitem = $(this).closest('.w-purchaseorder-item');

	var id_compraitem = purchaseitem.data('id_compraitem');

	Disable(button);

	data = {
		action: "purchase_item_vol_edit",
		id_compraitem: id_compraitem,
	}

	response = await Post("purchase_order.php", data);

	if (response != null) {

		var content = $(response);
		button.replaceWith(content);

		AutoFocus(content);

	} else {

		button.removeClass("loading");
	}
});

/**
  * Cancels "vol" edition.
  */
$(document).on("focusout", "#frm_purchase_order_item_vol #vol", async function() {

	//Prevents focusout on save
 	if ($(this).prop('disabled')) {
 		return;
 	}

	var form = $(this).closest('form');

	FormDisable(form);

	var id_compraitem = form.closest('.w-purchaseorder-item').data('id_compraitem');

	data = {
		action: "purchase_item_vol_get",
		id_compraitem: id_compraitem,
	}

	response = await Post("purchase_order.php", data);

	if (response != null) {

		form.replaceWith(response);

	} else {

		FormEnable(form);
	}
});

/**
  * Saves "vol" edition.
  */
$(document).on("submit", "#frm_purchase_order_item_vol", async function(event) {

	event.preventDefault();

	var form = $(this);

	var container = form.closest('.w-purchaseorder-item');
	var purchaseorder = form.closest('.w-purchaseorder')

	var id_compra = purchaseorder.data('id_compra');
	var id_compraitem = container.data('id_compraitem');

	var vol = this.vol.value;

	FormDisable(form);

	data = {
		action: "purchase_item_vol_save",
		id_compra: id_compra,
		id_compraitem: id_compraitem,
		vol: vol,
	}

	response = await Post("purchase_order.php", data);

	if (response != null) {

		container.replaceWith(response);

		CalcTotalTablePurchaseOrderItem(purchaseorder, id_compra);

	} else {

		FormEnable(form);
	}
});

/**
  * Opens "custo" edition
  */
$(document).on("click", ".purchase_order_item_bt_custo", async function() {

	var button = $(this);

	var purchaseitem = $(this).closest('.w-purchaseorder-item');

	var id_compraitem = purchaseitem.data('id_compraitem');

	Disable(button);

	data = {
		action: "purchase_item_custo_edit",
		id_compraitem: id_compraitem,
	}

	response = await Post("purchase_order.php", data);

	if (response != null) {

		var content = $(response);
		button.replaceWith(content);

		AutoFocus(content);

	} else {

		button.removeClass("loading");
	}
});

/**
  * Cancels "custo" edition.
  */
$(document).on("focusout", "#frm_purchase_order_item_custo #custo", async function() {

	//Prevents focusout on save
	if ($(this).prop('disabled')) {
		return;
	}

   var form = $(this).closest('form');

   FormDisable(form);

   var id_compraitem = form.closest('.w-purchaseorder-item').data('id_compraitem');

	data = {
		action: "purchase_item_custo_get",
		id_compraitem: id_compraitem,
	}

	response = await Post("purchase_order.php", data);

	if (response != null) {

		form.replaceWith(response);

	} else {

		FormEnable(form);
	}
});

/**
  * Saves "custo" edition.
  */
$(document).on("submit", "#frm_purchase_order_item_custo", async function(event) {

	event.preventDefault();

	var form = $(this);

	var container = form.closest('.w-purchaseorder-item');
	var purchaseorder = form.closest('.w-purchaseorder')

	var id_compra = purchaseorder.data('id_compra');
	var id_compraitem = container.data('id_compraitem');

	var custo = this.custo.value;

	FormDisable(form);

	data = {
		action: "purchase_item_custo_save",
		id_compra: id_compra,
		id_compraitem: id_compraitem,
		custo: custo,
	}

	response = await Post("purchase_order.php", data);

	if (response != null) {

		container.replaceWith(response);

		CalcTotalTablePurchaseOrderItem(purchaseorder, id_compra);

	} else {

		FormEnable(form);
	}
});

/**
  * Opens "qtdvol" edition
  */
$(document).on("click", ".purchase_order_item_bt_qtdvol", async function() {

	let button = $(this);

	let purchaseitem = $(this).closest('.w-purchaseorder-item');

	let id_compraitem = purchaseitem.data('id_compraitem');

	Disable(button);

	data = {
		action: "purchase_item_qtdvol_edit",
		id_compraitem: id_compraitem,
	}

	response = await Post("purchase_order.php", data);

	if (response != null) {

		let content = $(response);

		button.replaceWith(content);

		AutoFocus(content);

	} else {

		Enable(button);
	}
});

/**
  * Cancels "qtdvol" edition.
  */
$(document).on("focusout", "#frm_purchase_order_item_qtdvol #qtdvol", async function() {

	//Prevents focusout on save
 	if ($(this).prop('disabled')) {
 		return;
 	}

	var form = $(this).closest('form');

	FormDisable(form);

	var id_compraitem = form.closest('.w-purchaseorder-item').data('id_compraitem');

	data = {
		action: "purchase_item_qtdvol_get",
		id_compraitem: id_compraitem,
	}

	response = await Post("purchase_order.php", data);

	if (response != null) {

		form.replaceWith(response);

	} else {

		FormEnable(form);
	}
});

/**
  * Saves "qtdvol" edition.
  */
$(document).on("submit", "#frm_purchase_order_item_qtdvol", async function(event) {

	event.preventDefault();

	var form = $(this);

	var container = form.closest('.w-purchaseorder-item');
	var purchaseorder = form.closest('.w-purchaseorder')

	var id_compra = purchaseorder.data('id_compra');
	var id_compraitem = container.data('id_compraitem');

	var qtdvol = this.qtdvol.value;

	FormDisable(form);

	data = {
		action: "purchase_item_qtdvol_save",
		id_compra: id_compra,
		id_compraitem: id_compraitem,
		qtdvol: qtdvol,
	}

	response = await Post("purchase_order.php", data);

	if (response != null) {

		container.replaceWith(response);

	} else {

		FormEnable(form);
	}
});

/**
 * Shows new purchaseorder popup.
 */
$(document).on("click", ".purchaseorder_bt_show_new", async function() {

	let button = $(this);

	Disable(button);

	let data = {
		action: "purchaseorder_popup_new"
	}

	let response = await Post("purchase_order.php", data);

	if (response != null) {

		Modal.Show(Modal.POPUP_SIZE_SMALL, "Nova Ordem de Compra", response, null, Modal.POPUP_BUTTONFIX);
	}

	Enable(button);

	MenuClose();
});

/**
 * Enables/Disables second date field for search.
 */
$(document).on("click", "#frm_purchaseorder_reportsaleoneproduct #frm_purchaseorder_reportsaleoneproduct_intervalo", function() {

	$("#frm_purchaseorder_reportsaleoneproduct #frm_purchaseorder_reportsaleoneproduct_datafim").prop( "disabled", !this.checked);

	Modal.history_productsale.dateend_sel = this.checked;
});

/**
  * Defines datafim as minimum from dataini.
  */
$(document).on("change", "#frm_purchaseorder_reportsaleoneproduct #frm_purchaseorder_reportsaleoneproduct_dataini", function() {

	$("#frm_purchaseorder_reportsaleoneproduct #frm_purchaseorder_reportsaleoneproduct_datafim").prop({min: this.value});

	Modal.history_productsale.datestart = this.value;
});

/**
  * Defines datainicio for modal history.
  */
$(document).on("change", "#frm_purchaseorder_reportsaleoneproduct #frm_purchaseorder_reportsaleoneproduct_datafim", function() {

	Modal.history_productsale.dateend = this.value;
});

/**
  * Search report total sales from date / date interval.
  */
$(document).on("submit", "#frm_purchaseorder_reportsaleoneproduct", async function(event) {

	event.preventDefault();

	let form = $(this);

	let datafim_enabled = $('#frm_purchaseorder_reportsaleoneproduct_datafim', form).prop('disabled');

	let id_produto = form.data("id_produto");

	let dataini = $("#frm_purchaseorder_reportsaleoneproduct_dataini", form).val();
	let datafim = $("#frm_purchaseorder_reportsaleoneproduct_datafim", form).val();
	let intervalo = $("#frm_purchaseorder_reportsaleoneproduct_intervalo", form)[0].checked;

	FormDisable(form);

	let chart_container = $(".w_reportsaleoneproduct_graph_container");

	chart_container.html(imgLoading);

	$('.w_reportsaleoneproduct_graph_notfound').addClass('hidden');

	let data = {
		action: 'report_sale_one_product_search',
		produto: id_produto,
		dataini: dataini,
		intervalo: intervalo,
		datafim: datafim,
	}

	let response = await Post("report_sale_one_product.php", data);

	if (response != null) {

		chart_container.html("");

		let myChart = new MyChart();

		let datasets = response['chart'];

		datasets.forEach(function(dataset) {

			chart_container.append(myChart.getBar(dataset));

		});

		$(".w_reportsaleoneproduct_filter").html(response["filter"]);
		$(".w_reportsaleoneproduct_total").html(response["total"]);

	} else {

		$(".w_reportsaleoneproduct_graph_container").html("");
		$('.w_reportsaleoneproduct_graph_notfound').removeClass('hidden');
	}

	FormEnable(form);
	$("#frm_purchaseorder_reportsaleoneproduct_datafim", form).prop('disabled', datafim_enabled);
});

/**
  * Report from sale one product
  */
$(document).on("click", ".bt_purchaseorder_history", async function() {

	let button = $(this);

	let id_produto = button.data("id_produto");

	Disable(button);
	MenuClose();

	if (Modal.history_productsale.datelock == false) {

		let data = {
			action: "purchaseorder_getlastentry",
			id_produto: id_produto
		}

		let response = await Post("purchase_order.php", data);

		if (response != null) {

			Modal.history_productsale.datestart = response['datestart'];
			Modal.history_productsale.dateend = response['dateend'];
			Modal.history_productsale.dateend_sel = response['dateend_sel'];

		} else {

			Message.Show("Erro ao carregar data de histórico de compra!", Message.MSG_ERROR);
			Enable(button);

			return;
		}
	}

	let data = {
		action: "reportsaleonproduct_popup",
		id_produto: id_produto,
		datestart: Modal.history_productsale.datestart,
		dateend_sel: Modal.history_productsale.dateend_sel,
		dateend: Modal.history_productsale.dateend,
		datelock: Modal.history_productsale.datelock
	}

	let response = await Post("report_sale_one_product.php", data);

	if ( response!= null) {

		Modal.Show(Modal.POPUP_SIZE_SMALL, "Histórico de Venda do Produto", response, null, false, "<i class='icon fa-solid fa-chart-column'></i>");

		UpdateHistoryProductsaleLock();
	}

	$('#frm_purchaseorder_reportsaleoneproduct').submit();

	Enable(button);
});

function UpdateHistoryProductsaleLock() {

let form = $("#frm_purchaseorder_reportsaleoneproduct");

	if (Modal.history_productsale.datelock) {

		// FormDisable(form);
		Disable($("#frm_purchaseorder_reportsaleoneproduct_dataini", form), false);
		Disable($("#frm_purchaseorder_reportsaleoneproduct_intervalo", form), false);
		Disable($("#frm_purchaseorder_reportsaleoneproduct_datafim", form), false);
		Disable($("button:submit", form), false);

	} else {

		Enable($("#frm_purchaseorder_reportsaleoneproduct_dataini", form));
		Enable($("#frm_purchaseorder_reportsaleoneproduct_intervalo", form));
		// Enable($("#frm_purchaseorder_reportsaleoneproduct_datafim", form));
		Enable($("button:submit", form));

		// FormEnable(form);

		if ($("#frm_purchaseorder_reportsaleoneproduct_intervalo", form)[0].checked) {

			$('#frm_purchaseorder_reportsaleoneproduct_datafim', form).prop('disabled', '');

		// } else {

		// 	$('#frm_purchaseorder_reportsaleoneproduct_datafim', form).prop('disabled', 'disabled');
		}
	}
}

/**
  * Lock/Unlock dates to search
  */
$(document).on("click", ".bt_purchaseorder_history_lock", async function() {

	Modal.history_productsale.datelock = !Modal.history_productsale.datelock;

	UpdateHistoryProductsaleLock();

	if (Modal.history_productsale.datelock) {

		$("#frm_purchaseorder_reportsaleoneproduct").submit();
	}
});
