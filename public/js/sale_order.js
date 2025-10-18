class SaleOrders {

	static sale_orders = {};

	static UpdateSaleOrder(row) {

		this.sale_orders[row["id_venda"]] = row;

	}

	static UpdateSaleOrderItem(row) {

		this.sale_orders[row["id_venda"]].id_vendaitem = row;
	}
}
// function SaleOrderTotalUpdate() {

// 	var total = 0;

// 	var container = $(".w_saleorder_container");

// 	if ($(".w_saleorder").length == 0) {

// 		$(".saleorder_notfound").removeClass('hidden');

// 	} else {

// 		$(".w_saleorder", container).each(
// 			function(index, saleorder) {

// 				total += parseFloat($(saleorder).data("total"));
// 			}
// 		);
// 	}

// 	$(".w_saleorder-total").html(total.toLocaleString("pt-BR", {minimumFractionDigits: 2} ));
// }

function SaleOrderTotalCalc(saleorder) {

	// var table = saleorder.find(".table").find(".tbody");
	var frete = parseFloat(saleorder.data('frete'));
	var valor_servico = parseFloat(saleorder.data('valor_servico'));
	var subtotal = 0;
	var desconto = 0;
	var total = 0;

	// $(".tr:not(.reversed)", table).each(
	$(".w-saleorderitem:not(.reversed)", saleorder).each(
		function(index, data) {

			subtotal += parseFloat($(data).data("subtotal"));
			desconto += parseFloat($(data).data("desconto"));
			total += parseFloat($(data).data("total"));
		}
	);

	total+= frete + valor_servico;

	$(".sale_subtotal", saleorder).html(subtotal.toLocaleString("pt-BR", {minimumFractionDigits: 2} ));
	$(".sale_desconto", saleorder).html(desconto.toLocaleString("pt-BR", {minimumFractionDigits: 2} ));
	$(".sale_total", saleorder).html(total.toLocaleString("pt-BR", {minimumFractionDigits: 2} ));

	saleorder.data('total', total);

	// SaleOrderTotalUpdate();
}

/**
  * Opens sale order itens
  */
// $(document).on("click", ".saleorder_bt_expand", async function() {

// 	var button = $(this);

// 	var container = button.closest('.w_saleorder');

// 	var id_venda = container.data("id_venda");

//     var expandable = container.find(".expandable");

// 	Disable(button);

// 	button.removeClass("saleorder_bt_expand bt_expand fa-chevron-down");
// 	button.addClass("bt_collapse fa-chevron-up");

// 	var data = {
// 		action: 'sale_order_items',
// 		id_venda: id_venda,
// 	}

// 	var response = await Post("sale_order.php", data);

// 	if (response != null) {

// 		var content = $(response);

// 		expandable.html(content);

// 		AutoFocus(content);

// 	} else {

// 		expandable.html("Ocorreu um erro ao carregar pedido.");
// 	}

// 	expandable.slideDown("fast");
//     Enable(button);
// });

function SaleOrderHUD(andamento, efetuado, producao, entrega) {

	$('.w_saleorder_andamento').html(andamento);
	$('.w_saleorder_efetuado').html(efetuado);
	$('.w_saleorder_producao').html(producao);
	$('.w_saleorder_entrega').html(entrega);
}

/**
  * Event to new sale order
  */
$(document).on("submit", "#frm_sale_order", async function(event) {

	event.preventDefault();

	let form = $(this);

	FormDisable(form);

	let field = form.find('.entity_search');

	let entidade = field.data("sku");

	if (entidade) {

		field.val(field.data('descricao'));

	} else {

		entidade = field.val();
	}

	data = {
		action: 'sale_order_new',
		cliente: entidade,
		from: 'saleorder',
		id_endereco: 0
	}

	let response = await Post("sale_order.php", data);

	if (response != null) {

		$('.saleorder_header').html("Pedido");

		let content = $(response['saleorder']);

		$('.saleorder_notfound').addClass('hidden');
		$(".w_saleorder_container .tbody").html(content);
		$('.w_saleorder_container').data("window", "saleorder_show");

		SaleOrderHUD(response['total_andamento'], response['total_efetuado'], response['total_producao'], response['total_entrega']);

		// ContainerFocus(content, true);
		AutoFocus(content);

		FormEnable(form);

		field.val("");

	} else {

		FormEnable(form);
	}


});

/**
  * Opens "address" sale order for select
  */
$(document).on("click", ".sale_order_bt_address", async function() {

	let button = $(this);

	let id_venda = button.closest(".w_saleorder").data('id_venda');

	Disable(button);

	data = {
		action: 'sale_order_endereco_select_open',
		id_venda: id_venda,
	}

	let response = await Post("sale_order.php", data);

	Enable(button);

	if (response != null) {

		Modal.Show(Modal.POPUP_SIZE_LARGE, "Endereço de Entrega", response, null);
	}
});

/**
  * Selects "address" sale order.
  */
$(document).on("click", ".sale_order_bt_address_select", async function() {

	let button = $(this);

	let popup = button.closest(".popup");

	let saleorder = button.closest(".w_saleorder_address");

	let id_venda = saleorder.data("id_venda");
	let id_endereco = button.data("id_endereco");
	let versao = $('.w_saleorder_' + id_venda).data('versao');

	Disable(button);

	let data = {
		action: 'sale_order_endereco_select',
		id_venda: id_venda,
		versao: versao,
		id_endereco: id_endereco,
	}

	let response = await Post("sale_order.php", data);

	if (response != null) {

		saleorder = $('.w_saleorder_' + id_venda);

		saleorder.data("versao", response["versao"]);
		saleorder.find('.w-entityaddress').html(response['address']);
		saleorder.data('frete', response['frete']);
		saleorder.find('.sale_order_frete').html(response['frete_formatted']);

		SaleOrderTotalCalc(saleorder);

		ContainerFocus(saleorder.find('.w-entityaddress'), true);

		Modal.Close(popup);

	} else {

		Enable(button);
	}
});

/**
  * Deletes "address" from sale order.
  */
$(document).on("click", ".sale_order_bt_address_delete", async function() {

	let button = $(this);

	let popup = button.closest(".popup");

	let id_venda = button.closest('.w_saleorder_address').data('id_venda');
	let versao = $('.w_saleorder_' + id_venda).data('versao');

	Disable(button);

	data = {
		action: 'sale_order_endereco_delete',
		id_venda: id_venda,
		versao: versao
	}

	let response = await Post("sale_order.php", data);

	if (response != null) {

		let saleorder = $('.w_saleorder_' + id_venda);

		saleorder.data("versao", response["versao"]);
		saleorder.find('.w-entityaddress').html(response['address']);
		saleorder.data('frete', response['frete']);
		saleorder.find('.sale_order_frete').html(response['frete_formatted']);

		SaleOrderTotalCalc(saleorder);

		ContainerFocus(saleorder.find('.w-entityaddress'), true);

		Modal.Close(popup);
	}

	Enable(button);
});

/**
  * Saves "obs" sale order edition.
  */
$(document).on("submit", ".frm_sale_order_obs", async function(event) {

	event.preventDefault();

	let form = $(this);

	let container = form.closest('.w_saleorder_obs');

	let obs = form.find(".field_obs").val();
	let id_venda = form.closest(".w_saleorder").data('id_venda');
	let versao = form.closest(".w_saleorder").data('versao');
	// let id_venda = form.data('id_venda');

	data = {
		action: 'sale_order_obs_save',
		id_venda: id_venda,
		versao: versao,
		obs: obs,
	}

	FormDisable(form);

	let response = await Post("sale_order.php", data);

	if (response != null) {

		form.closest(".w_saleorder").data('versao', response["versao"]);
		container.replaceWith(response["data"]);

	} else {

		FormEnable(form);
	}
});

/**
  * Opens "frete" sale order edition
  */
$(document).on("click", ".sale_order_bt_frete", async function() {

	var button = $(this);

	var container = button.closest('.addon');

	var id_venda = button.data('id_venda');

	Disable(button);

	data = {
		action: 'sale_order_frete_edit',
		id_venda: id_venda,
	}

	var response = await Post("sale_order.php", data);

	if (response != null) {

		var content = $(response);
		container.replaceWith(content);
		content.find("input").select();

	} else {

		Enable(button);
	}
});

/**
  * Cancels "frete" sale order edition.
  */
$(document).on("focusout", "#frm_sale_order_frete #frete", async function() {

	let field = $(this);

	//Prevents focusout on save
 	if (field.hasClass('disabled_focusout')) {
 		return;
 	}

	Disable(field);

	let container = field.closest('form');

	let id_venda = container.closest(".w_saleorder").data('id_venda');

	data = {
		action: 'sale_order_frete_edit_cancel',
		id_venda: id_venda,
	}

	let response = await Post("sale_order.php", data);

	if (response != null) {

		container.replaceWith(response);

	} else {

		Enable(field);
	}
});

/**
  * Saves "frete" sale order edition.
  */
$(document).on("submit", "#frm_sale_order_frete", async function(event) {

	event.preventDefault();

	let container = $(this);

	let saleorder = container.closest('.w_saleorder');

	let field = $(this.frete);

	let frete = this.frete.value;
	let id_venda = saleorder.data('id_venda');
	let versao = saleorder.data('versao');

	field.addClass("disabled_focusout");
	FormDisable(container);

	let data = {
		action: 'sale_order_frete_save',
		id_venda: id_venda,
		versao: versao,
		frete: frete,
	}

	let response = await Post("sale_order.php", data);

	if (response != null) {

		saleorder.data("versao", response["versao"]);
		saleorder.data("frete", frete);

		container.replaceWith(response['data']);

		SaleOrderTotalCalc(saleorder);

	} else {

		FormEnable(container);
		field.removeClass("disabled_focusout");
	}
});

/**
  * Event to add item for sale order
  */
$(document).on("submit", "#frm_sale_order_item", async function(event) {

	event.preventDefault();

	let form = $(this);

	FormDisable(form);

	let container = form.closest('.table');
	let saleorder = form.closest('.w_saleorder');
	let versao = saleorder.data("versao");

	let field = $(this.product_search);

	let produto = field.data("sku");

	if (produto) {

		field.val(field.data('descricao'));

	} else {

		produto = field.val();
	}

	let data = {
		action: 'sale_order_item_add',
		id_venda: form.data('id_venda'),
		versao: versao,
		produto: produto,
		qtd: this.qtd.value,
		obs: this.obs.value,
	}

	let response = await Post("sale_order.php", data);

	if (response != null) {

		this.product_search.value = "";
		this.qtd.value = "";
		this.obs.value="";

		FormEnable(form);

		if (response["complemento"]) {

			Modal.Show(Modal.POPUP_SIZE_LARGE, "Complementos do Produto", response["complemento"], null, false, "<i class='icon fa-solid fa-list-ul'></i>");

		} else {

			$('.w-saleorderitem-notfound').remove();

			$(".tbody", container).append(response["item"]);

			saleorder.data('frete', response['frete']);
			saleorder.data('versao', response['versao']);
			saleorder.find('.sale_order_frete').html(response['frete_formatted']);

			let tr = $(".tbody .tr:last", container);

			SaleOrderTotalCalc(saleorder);

			ContainerFocus(tr);

			$(this.product_search).focus();
		}

	} else {

		FormEnable(form);

		$(this.product_search).focus();
	}
});

/**
 * Reverse item from sale order
 */
$(document).on("click", ".sale_order_item_bt_reverse", async function() {

	let button = $(this);

	let tr = button.closest('.tr');
	let saleorder = button.closest(".w_saleorder");
	let id_venda = saleorder.data("id_venda");
	let versao = saleorder.data("versao");
	let id_vendaitem = tr.data("id_vendaitem");

	Disable(button);

	let data = {
		action: 'sale_order_item_delete',
		id_venda: id_venda,
		versao: versao,
		id_vendaitem: id_vendaitem,
	}

	let response = await Post("sale_order.php", data);

	if (response != null) {

		tr.replaceWith(response['item']);

		saleorder.data("frete", response["frete"]);
		saleorder.data("versao", response["versao"]);
		saleorder.find(".sale_order_frete").html(response["frete_formatted"]);

		SaleOrderTotalCalc(saleorder);

	} else {

		Enable(button);
	}
});

/**
 * Restore item reversed from sale order
 */
$(document).on("click", ".sale_order_item_bt_restore", async function() {

	let button = $(this);

	let container = button.closest('.w-saleorderitem');
	let saleorder = button.closest('.w_saleorder');
	let id_venda = saleorder.data("id_venda");
	let versao = saleorder.data("versao");
	let id_vendaitem = container.data("id_vendaitem");

	Disable(button);

	let data = {
		action: 'sale_order_item_restore',
		id_venda: id_venda,
		versao: versao,
		id_vendaitem: id_vendaitem,
	}

	let response = await Post("sale_order.php", data);

	if (response != null) {

		container.replaceWith(response['item']);

		saleorder.data("frete", response["frete"]);
		saleorder.data("versao", response["versao"]);
		saleorder.find(".sale_order_frete").html(response["frete_formatted"]);

		SaleOrderTotalCalc(saleorder);

	} else {

		Enable(button);
	}
});

/**
  * Saves "product obs" edition.
  */
$(document).on("submit", ".frm_saleorderitem_obs", async function(event) {

	event.preventDefault();

	let form = $(this);

	let container = form.closest('.w_saleorderitem_obs');

	let obs = form.find(".field_obs").val();
	let id_venda = form.closest(".w_saleorder").data('id_venda');
	let versao = form.closest(".w_saleorder").data('versao');
	let id_vendaitem = form.data('id_vendaitem');

	data = {
		action: 'sale_order_item_obs_save',
		id_venda: id_venda,
		versao: versao,
		id_vendaitem: id_vendaitem,
		obs: obs,
	}

	FormDisable(form);

	let response = await Post("sale_order.php", data);

	if (response != null) {

		container.replaceWith(response["data"]);
		$(".w_saleorder_" + id_venda).data("versao", response["versao"]);

	} else {

		FormEnable(form);
	}
});

/**
  * Opens "qtd" edition
  */
$(document).on("click", ".sale_order_item_qtd", async function() {

	let button = $(this);

	let container = button.closest('.addon');

	let id_venda = button.closest('.w_saleorder').data("id_venda");
	let id_vendaitem = button.closest('.w-saleorderitem').data('id_vendaitem');

	Disable(button);

	data = {
		action: 'sale_order_item_qtd_edit',
		id_venda: id_venda,
		id_vendaitem: id_vendaitem,
	}

	let response = await Post("sale_order.php", data);

	if (response != null) {

		let content = $(response);
		container.replaceWith(content);
		content.find("input").select();

	} else {

		Enable(button);
	}
});

/**
  * Cancels "qtd" edition.
  */
$(document).on("focusout", "#frm_sale_order_item_qtd #qtd", async function() {

	let field = $(this);
	let container = field.closest('form');

	//Prevents focusout on save
 	if (field.hasClass('disabled_focusout')) {
 		return;
 	}

	let id_venda = container.closest(".w_saleorder").data("id_venda");
	let id_vendaitem = container.data('id_vendaitem');

	Disable(field);

	data = {
		action: 'sale_order_item_qtd_cancel',
		id_venda: id_venda,
		id_vendaitem: id_vendaitem,
	}

	let response = await Post("sale_order.php", data);

	if (response != null) {

		container.replaceWith(response);

	} else {

		Enable(field);
	}
});

/**
  * Saves "qtd" edition.
  */
$(document).on("submit", "#frm_sale_order_item_qtd", async function(event) {

	event.preventDefault();

	let form = $(this);
	let field = $(this.qtd);

	let saleorder_item = form.closest('.w-saleorderitem');
	let saleorder = form.closest(".w_saleorder");

	// field.addClass("loading");
	field.addClass("disabled_focusout");
	FormDisable(form);

	let qtd = this.qtd.value;
	let id_venda = saleorder.data('id_venda');
	let versao = saleorder.data('versao');
	let id_vendaitem = form.data('id_vendaitem');

	data = {
		action: 'sale_order_item_qtd_save',
		id_venda: id_venda,
		versao: versao,
		id_vendaitem: id_vendaitem,
		qtd: qtd,
	}

	let response = await Post("sale_order.php", data);

	if (response != null) {

		saleorder_item.replaceWith(response['item']);

		saleorder.data('frete', response['frete']);
		saleorder.data("versao", response["versao"]);
		saleorder.find('.sale_order_frete').html(response['frete_formatted']);

		SaleOrderTotalCalc(saleorder);

	} else {

		FormEnable(form);
		// field.removeClass("loading");
		field.removeClass("disabled_focusout");
	}
});

/**
  * Opens "preco" edition
  */
$(document).on("click", ".sale_order_item_preco", async function() {

	let button = $(this);

	let container = button.closest(".addon");

	let id_venda = button.closest('.w_saleorder').data("id_venda");
	let id_vendaitem = button.closest('.w-saleorderitem').data('id_vendaitem');

	Disable(button);

	data = {
		action: 'sale_order_item_preco_edit',
		id_venda: id_venda,
		id_vendaitem: id_vendaitem,
	}

	let response = await Post("sale_order.php", data);

	if (response != null) {

		let content = $(response);
		container.replaceWith(content);
		content.find("input").select();

	} else {

		Enable(button);
	}
});

/**
  * Cancels "preco" edition.
  */
$(document).on("focusout", "#frm_sale_order_item_preco #preco", async function() {

	let field = $(this);
	let container = field.closest('form');

	//Prevents focusout on save
 	if (field.hasClass('disabled_focusout')) {
 		return;
 	}

	let id_venda = container.closest(".w_saleorder").data("id_venda");
	let id_vendaitem = container.data('id_vendaitem');

	Disable(field);

	data = {
		action: 'sale_order_item_preco_cancel',
		id_venda: id_venda,
		id_vendaitem: id_vendaitem,
	}

	let response = await Post("sale_order.php", data);

	if (response != null) {

		container.replaceWith(response);

	} else {

		Enable(field);
	}
});

/**
  * Saves "preco" edition.
  */
$(document).on("submit", "#frm_sale_order_item_preco", async function(event) {

	event.preventDefault();

	let container = $(this);
	let field = $(this.preco);

	let saleorder_item = container.closest('.w-saleorderitem');
	let saleorder = container.closest(".w_saleorder");

	field.addClass("disabled_focusout");
	FormDisable(container);

	let preco = this.preco.value;
	let id_venda = saleorder.data('id_venda');
	let versao = saleorder.data('versao');
	let id_vendaitem = container.data('id_vendaitem');

	data = {
		action: 'sale_order_item_preco_save',
		id_venda: id_venda,
		versao: versao,
		id_vendaitem: id_vendaitem,
		preco: preco,
	}

	let response = await Post("sale_order.php", data);

	if (response != null) {

		saleorder_item.replaceWith(response['item']);

		saleorder.data("versao", response["versao"]);
		saleorder.data('frete', response['frete']);
		saleorder.find('.sale_order_frete').html(response['frete_formatted']);

		SaleOrderTotalCalc(saleorder);

	} else {

		FormEnable(container);
		field.removeClass("disabled_focusout");
	}
});

/**
  * Opens "desconto" edition
  */
$(document).on("click", ".sale_order_item_desconto", async function() {

	let container = $(this).closest(".addon");

	let id_venda = $(this).closest('.w_saleorder').data("id_venda");
	let id_vendaitem = $(this).closest('.w-saleorderitem').data('id_vendaitem');

	Disable(container);

	data = {
		action: 'sale_order_item_desconto_edit',
		id_venda: id_venda,
		id_vendaitem: id_vendaitem,
	}

	let response = await Post("sale_order.php", data);

	if (response != null) {

		let content = $(response);

		container.replaceWith(content);

		AutoFocus(content);

	} else {

		Enable(container);
	}
});

/**
  * Cancels "desconto" edition.
  */
$(document).on("focusout", "#frm_sale_order_item_desconto #desconto", async function() {

	let field = $(this);
	let container = field.closest('form');

	//Prevents focusout on save
 	if (field.hasClass('disabled_focusout')) {
 		return;
 	}

	let id_venda = container.closest(".w_saleorder").data("id_venda");
	let id_vendaitem = container.data('id_vendaitem');

	Disable(field);

	data = {
		action: 'sale_order_item_desconto_cancel',
		id_venda: id_venda,
		id_vendaitem: id_vendaitem,
	}

	let response = await Post("sale_order.php", data);

	if (response != null) {

		container.replaceWith(response);

	} else {

		Enable(field);
	}
});

/**
  * Saves "desconto" edition.
  */
$(document).on("submit", "#frm_sale_order_item_desconto", async function(event) {

	event.preventDefault();

	let form = $(this);
	let field = $(this.desconto);

	let saleorder_item = form.closest('.w-saleorderitem');
	let saleorder = form.closest(".w_saleorder");

	field.addClass("disabled_focusout");
	FormDisable(form);

	let desconto = this.desconto.value;
	let id_venda = saleorder.data('id_venda');
	let versao = saleorder.data('versao');
	let id_vendaitem = form.data('id_vendaitem');

	data = {
		action: 'sale_order_item_desconto_save',
		id_venda: id_venda,
		versao: versao,
		id_vendaitem: id_vendaitem,
		desconto: desconto,
	}

	let response = await Post("sale_order.php", data);

	if (response != null) {

		saleorder_item.replaceWith(response['item']);

		saleorder.data('versao', response['versao']);
		saleorder.data('frete', response['frete']);
		saleorder.find('.sale_order_frete').html(response['frete_formatted']);

		SaleOrderTotalCalc(saleorder);

	} else {

		FormEnable(form)
		field.removeClass("disabled_focusout");
		AutoFocus(form);
	}
});

/**
  * Event button to change status to close
  */
$(document).on("click", ".saleorder_bt_close", async function() {

	let button = $(this);
	let buttons = $('.saleorder_bt_close');

	let id_venda = button.data('id_venda');
	let toprint = button.data("print");
	let versao = $(".w_saleorder_" + id_venda).data("versao");

	if (WindowManager.page != "sale_order.php") {

		await LoadPage("sale_order.php");
	}

	let window = $('.w_saleorder_container').data('window');

	Disable(buttons);

	let data = {
		action: 'saleorder_close',
		id_venda: id_venda,
		versao: versao,
		toprint: toprint,
		window: window
	}

	let response = await Post("sale_order.php", data);

	if (response != null) {

		await SaleOrderShowEfetuado();

		let saleorder = $('.w_saleorder_' + id_venda);

		ContainerFocus(saleorder, true);

		SaleOrderHUD(response['total_andamento'], response['total_efetuado'], response['total_producao'], response['total_entrega']);

		Modal.CloseAll();
	}

	Enable(buttons);
});

/**
  * Event button to change status to open
  */
$(document).on("click", ".saleorder_bt_open", async function() {

	let button = $(this);

	let container = button.closest(".w_saleorder");

	let id_venda = button.data('id_venda');
	let versao = button.closest(".w_saleorder").data("versao");

	let popup = button.closest(".popup");

	Disable(button);
	MenuClose();

	let data = {
		action: 'saleorder_open',
		id_venda: id_venda,
		versao: versao
	}

	let yes = async function() {

		let success = async function(response) {

			// let response = await Post("sale_order.php", data);

			// if (response != null) {

			if (WindowManager.page != "sale_order.php") {

				await LoadPage("sale_order.php");
			}

			let resp = await SaleOrderShow(id_venda);

			if (resp != null) {

				$('.saleorder_header').html("Dados do Pedido");
				$(".w_saleorder_container .tbody").html(resp);
				$('.w_saleorder_container').data("window", "saleorder_show");
			}

			Modal.Close(popup);

			SaleOrderHUD(response['total_andamento'], response['total_efetuado'], response['total_producao'], response['total_entrega']);

			// } else {


			// }
		}

		let error = async function(){

			Enable(button);
		}

		Authenticator.Authenticate(data, "sale_order.php", success, error, null, false);

		return true;
	}

	let no = async function() {

		Enable(button);
	}

	MessageBox.Show("Pedido: #" + id_venda + "<br>Valor: R$ " + container.data("total").toLocaleString("pt-BR", {minimumFractionDigits: 2} ) + "<br>Abrir pedido para edição?", yes, no);
});

/**
  * Event button to change status to cancel
  */
$(document).on("click", ".saleorder_bt_cancel", async function() {

	let button = $(this);

	let container = button.closest(".w_saleorder");

	let id_venda = button.data('id_venda');
	let versao = $(".w_saleorder_" + id_venda).data("versao");

	let data = {};

	MenuClose();

	let yes = async function() {

		let obs = $("#messagebox_obs").val().trim();

		if (obs == "") {

			Message.Show("Digite um motivo para o cancelamento!", Message.MSG_ALERT);
			$("#messagebox_obs").focus();
			return false;
		}

		data = {
			action: 'saleorder_delete',
			id_venda: id_venda,
			versao: versao,
			obs: obs,
			page: WindowManager.page
		}

		let success = async function(response) {

			switch (WindowManager.page) {

				case "sale_order.php":

					let window = $('.w_saleorder_container').data('window');

					if (window == "saleorder_show") {

						await SaleOrderShowAndamento();

					} else {

						ContainerRemove(container, function() {

							if ($('.w_saleorder').length == 0) {

								$('.saleorder_notfound').removeClass('hidden');
							}
						});
					}

					SaleOrderHUD(response['total_andamento'], response['total_efetuado'], response['total_producao'], response['total_entrega']);

				break;

				case "report_sale_coupon.php":

					Modal.CloseAll();
					$(".w_saleorder_" + id_venda).replaceWith(response);

				break;

				case "entity.php":

					button.closest(".w_saleorder").replaceWith(response);

				break;

				case "bills_to_receive.php":

					let billstoreceive = container.closest(".w_billstoreceive");

					Modal.CloseAll();

					ContainerRemove($(".w_saleorder_" + id_venda), function() {

						BillsToReceiveTotalCalc();
						// if ($('.w_saleorder').length == 0) {

						// 	$('.saleorder_notfound').removeClass('hidden');
						// }
					});
				break;

				default:

					console.log("DORI TODO: " + WindowManager.page);
				break;
			}

		}

		let error = async function() {

			// MenuClose();
		}

		Authenticator.Authenticate(data, "sale_order.php", success, error, null, true);

		return true;
	}

	let no = async function() {

		// MenuClose();
	}

	data = {
		action: 'saleorder_delete_popup',
		id_venda: id_venda,
	}

	let response = await Post("sale_order.php", data);

	if (response != null) {

		MessageBox.Show(response, yes, no, true);
		// MessageBox.Show("Pedido: #" + id_venda + "<br>Valor: R$ " + container.data("total").toLocaleString("pt-BR", {minimumFractionDigits: 2} ) + "<br><br>Confirma cancelamento do pedido?", yes, no);
	}

});

/**
  * Event button to show status history
  */
$(document).on("click", ".saleorder_bt_statushistory", async function() {

	let button = $(this);

	// let container = button.closest(".w_saleorder");

	let id_venda = button.data('id_venda');

	// let popup = button.closest(".popup");

	Disable(button);

	data = {
		action: 'saleorder_statushistory',
		id_venda: id_venda,
	}

	let response = await Post("sale_order.php", data);

	MenuClose();

	if (response != null) {

		Modal.Show(Modal.POPUP_SIZE_SMALL, "Histórico de Status", response, null, false, "<i class='icon fa-solid fa-list'></i>");

	}

	Enable(button);
});

/**
  * Event button to clear sale order discount
  */
$(document).on("click", ".saleorder_bt_discountclear", async function() {

	let button = $(this);

	let saleorder = button.closest('.w_saleorder');

	let id_venda = saleorder.data("id_venda");
	let versao = saleorder.data("versao");

	let window = $('.w_saleorder_container').data('window');

	Disable(button);

	let data = {
		action: 'saleorder_discountclear',
		id_venda: id_venda,
		versao: versao,
		window: window
	}

	let response = await Post("sale_order.php", data);

	if (response != null) {

		if (window == 'saleorder_show') {

			saleorder.data("versao", response["versao"]);
			$(".w_saleorder_container .tbody").html(response["data"]);

		} else {

			saleorder.replaceWith(response);
		}
	}

	Enable(button);

	// MenuClose();
});

/**
  * Event button to send sale order by WhatsApp
  */
$(document).on("click", ".sale_order_bt_whats", async function() {

	var button = $(this);

	var id_venda = button.data('id_venda');

	button.addClass('disabled');

	data = {
		action: 'sale_order_whatsapp',
		id_venda: id_venda,
	}

	var response = await Post("sale_order.php", data);

	if (response != null) {

		window.open("https://api.whatsapp.com/send?text=" + response, "_blank");
	}

	button.removeClass('disabled');

	MenuClose();
});

/**
  * Event button to copy sale order to clipboard
  */
$(document).on("click", ".sale_order_bt_copy", async function() {

	let button = $(this);

	let id_venda = button.data('id_venda');

	button.addClass('disabled');

	let data = {
		action: 'sale_order_whatsapp',
		id_venda: id_venda,
	}

	let response = await Post("sale_order.php", data);

	if (response != null) {

		response = response.replace(/%0A/g, "\n");

		CopyAndPaste(response);
	}

	button.removeClass('disabled');

	MenuClose();
});

/**
  * Event button to print sale order
  */
$(document).on("click", ".saleorder_bt_print", async function() {

	let button = $(this);

	let id_venda = button.data('id_venda');

	Disable(button);

	let data = {
		action: 'PrintPedido',
		id_venda: id_venda,
	}

	await GET("cgi.php", data);

	// data = {
	// 	action: 'saleorder_get',
	// 	id_venda: id_venda,
	// }

	// let response = await Post("sale_order.php", data);

	// if (response != null) {

	// 	$(this).closest().replaceWith(response);
	// }

	Enable(button);
	MenuClose();
});

/**
  * Event button to convert sale order to VENDA_A_PRAZO
  */
$(document).on("click", ".saleorder_bt_prazo", async function() {

	let button = $(this);

	let id_venda = button.data('id_venda');

	let versao = $(".w_saleorder_" + id_venda).data("versao");

	let container = button.closest('.w_saleorder');

	Disable(button);

	data = {
		action: 'saleorder_prazo',
		id_venda: id_venda,
		versao: versao
	}

	MenuClose();

	let yes = async function() {

		let response = await Post("sale_order.php", data);

		if (response != null) {

			let window = $('.w_saleorder_container').data('window');

			switch (window) {

				case "saleorder_show":

					await SaleOrderShowAndamento();

					break;

				default:

					ContainerRemove(container, function() {

						if ($('.w_saleorder').length == 0) {

							$('.saleorder_notfound').removeClass('hidden');
						}
					});

					break;
			}

			SaleOrderHUD(response['total_andamento'], response['total_efetuado'], response['total_producao'], response['total_entrega']);

		} else {

			button.removeClass('disabled');
		}

		// MenuClose();

		return true;
	}

	let no = async function() {

		Enable(button);

		// MenuClose();
	}

	MessageBox.Show("Pedido: #" + id_venda + "<br>Valor: R$ " + container.data("total").toLocaleString("pt-BR", {minimumFractionDigits: 2} ) + "<br>Confirma venda a prazo?", yes, no);

});

async function SaleOrderShow(id_venda) {

	let data = {
		action: 'saleorder_show',
		id_venda: id_venda,
	}

	let response = await Post("sale_order.php", data);

	return response;
}

/**
  * Event button to show saleorder details
  */
$(document).on("click", ".saleorder_bt_show", async function() {

	let button = $(this);

	let id_venda = button.data('id_venda');

	Disable(button);

	let response = await SaleOrderShow(id_venda);

	if (response != null) {

		if($('.saleorder_controlpanel').length > 0 && button.closest('.entity_history_container').length == 0) {

			$('.saleorder_header').html("Dados do Pedido");

			$(".w_saleorder_container .tbody").html(response);

			$('.w_saleorder_container').data("window", "saleorder_show");

			$("#product_search").focus();

		} else {

			Modal.Show(Modal.POPUP_SIZE_LARGE, "Visualização de Pedido", response, null);

			Enable(button);
			MenuClose();
		}

	} else {

		Enable(button);
		MenuClose();
	}
});

/**
 * Opens sale order history from client
 */
$(document).on("click", ".history_order_bt_expand", async function() {

	let button = $(this);

	let id_entidade = button.data("id_entidade");
	let id_venda = button.data("id_venda");

	let expandable = button.closest('.window').children(".expandable:first");

	let history = expandable.find(".entity_history_container");

	history.html(imgLoading);

	button.removeClass("history_order_bt_expand bt_expand fa-chevron-down");
	button.addClass("bt_collapse fa-chevron-up");

	expandable.slideDown("fast");
	// Disable(button);

	let data = {
		action: 'sale_order_history_order',
		id_entidade: id_entidade,
		id_venda: id_venda,
		page: 1,
	}

	let response = await Post("sale_order.php", data);

	if (response != null) {

		history.html(response);

	} else {

		history.html("Nenhum histórico encontrado.");
	}

	// Enable(button);
});

/**
 * Opens especific page sale order history from client
 */
$(document).on("click", ".history_order_bt_page", async function() {

	let button = $(this);

	let id_entidade = button.data("id_entidade");
	let page = button.data("page");

	let container = button.closest("div");

    Disable(button);

	let data = {
		action: 'sale_order_history_order',
		id_entidade: id_entidade,
		id_venda: 0,
		page: page,
	}

	let response = await Post("sale_order.php", data);

	if (response != null) {

		container.replaceWith(response);

	} else {

		container.replaceWith("Fim do histórico.");
		// container.remove();
	}
});

/**
 * Opens itens history from sale order
 */
// $(document).on("click", ".history_order_item_bt_expand", async function() {

// 	let button = $(this);

// 	let id_venda = button.data("id_venda");

// 	let window = $(this).closest('.window');

//     let expandable = window.find(".expandable:first");

// 	Disable(button);

// 	let data = {
// 		action: 'sale_order_history_order_item',
// 		id_venda: id_venda,
// 	}

// 	let response = await Post("sale_order.php", data);

// 	if (response != null) {

// 		expandable.html(response);

// 		$(this).removeClass("history_order_item_bt_expand bt_expand fa-chevron-down");
// 		$(this).addClass("bt_collapse fa-chevron-up");
// 	}

// 	expandable.slideDown("fast");
// 	Enable(button);
// });

/**
  * Event to register payment
  */
$(document).on("submit", "#frm_saleorder_payment", async function(event) {

	event.preventDefault();

	let form = $(this);

	FormDisable(form);

	let id_venda = form.data('id_venda');
	let id_entidade = form.data('id_entidade');
	let versao = $(".w_saleorder_" + id_venda).data("versao");
	let id_especie = this.id_especie.value;
	let valor = this.valor.value;


	let data = {
		action: 'saleorder_payment_add',
		id_venda: id_venda,
		versao: versao,
		id_especie: id_especie,
		valor: valor
	}

	let response = await Post("sale_order.php", data);

	if (response != null) {

		let content = $(response['data']);

		$('.w_saleorder_payment_container').replaceWith(content);
		$(".w_saleorder_" + id_venda).data("versao", response["versao"]);

		if (response['credito'] !== null) {

			let credito = parseFloat(response['credito']);

			$('.entitycredit_' + id_entidade).find('.entity_bt_credito').html(credito.toLocaleString("pt-BR", {minimumFractionDigits: 2}));
		}

		AutoFocus(content);

	} else {

		FormEnable(form);
	}
});

/**
  * Event to remove payment from saleorder
  */
$(document).on("click", ".saleorder_bt_payment_del", async function() {

	let button = $(this);

	let id_venda = button.data('id_venda');
	let versao = $(".w_saleorder_" + id_venda).data("versao");
	let id_vendapay = button.data('id_vendapay');
	let id_entidade = button.data('id_entidade');

	Disable(button);

	let data = {
		action: 'saleorder_payment_del',
		id_venda: id_venda,
		versao: versao,
		id_vendapay: id_vendapay
	}

	let response = await Post("sale_order.php", data);

	if (response != null) {

		let content = $(response['data']);

		$('.w_saleorder_payment_container').replaceWith(content);
		$(".w_saleorder_" + id_venda).data("versao", response["versao"]);

		if (response['credito'] !== null) {

			let credito = parseFloat(response['credito']);

			$('.entitycredit_' + id_entidade).find('.entity_bt_credito').html(credito.toLocaleString("pt-BR", {minimumFractionDigits: 2}));
		}

		AutoFocus(content);

	} else {

		Enable(button);
	}

});

/**
  * Event to open payment form from saleorder
  */
 $(document).on("click", ".sale_order_bt_payment", async function() {

	let button = $(this);
	let id_venda = button.data('id_venda');

	Disable(button);

	let data = {
		action: 'saleorder_payment',
		id_venda: id_venda
	}

	let response = await Post("sale_order.php", data);

	if (response != null) {

		Modal.Show(Modal.POPUP_SIZE_SMALL, "Fechar Pedido", response, null);
	}

	Enable(button);

	MenuClose();
});

/**
  * Event to remove obs from saleorder
  */
$(document).on("click", ".bt_saleorder_obs_del", async function() {

	let button = $(this);
	let id_venda = button.data('id_venda');

	Disable(button);

	let data = {
		action: 'saleorder_obs_del',
		id_venda: id_venda
	}

	var response = await Post("sale_order.php", data);

	if (response != null) {

		let container = button.closest('.w_saleorder_obs');

		container.replaceWith(response);

	} else {

		Enable(button);
	}
});

/**
  * Event to remove obs from saleorderitem
  */
// $(document).on("click", ".bt_saleorderitem_obs_del", async function() {

// 	let button = $(this);
// 	let id_venda = button.data('id_venda');
// 	let id_vendaitem = button.data('id_vendaitem');

// 	Disable(button);

// 	let data = {
// 		action: 'saleorderitem_obs_del',
// 		id_venda: id_venda,
// 		id_vendaitem: id_vendaitem
// 	}

// 	var response = await Post("sale_order.php", data);

// 	if (response != null) {

// 		let container = button.closest('.w_saleorderitem_obs');

// 		container.replaceWith(response);

// 	} else {

// 		Enable(button);
// 	}
// });

async function SaleOrderShowAndamento() {

	$('.w_saleorder').remove();

	$('.saleorder_header').html("Pedidos em Andamento");

	$('.saleorder_notfound').addClass('hidden');
	$('.saleorder_loading').removeClass('hidden');

	let data = {
		action: 'saleorder_show_andamento',
	}

	let response = await Post("sale_order.php", data);

	if (response != null) {

		SaleOrderHUD(response['total_andamento'], response['total_efetuado'], response['total_producao'], response['total_entrega']);

		$('.saleorder_loading').addClass('hidden');

		$(".w_saleorder_container .tbody").html(response['extra_block_orders']);
		$('.w_saleorder_container').data("window", "saleorder_andamento");

		if ($('.w_saleorder').length > 0) {

			$('.saleorder_notfound').addClass('hidden');

		} else {

			$('.saleorder_notfound').removeClass('hidden');
		}
	}
}

/**
  * Event to show saleorders "em andamento"
  */
$(document).on("click", ".saleorder_bt_andamento", async function() {

	await SaleOrderShowAndamento();
});

async function SaleOrderShowEfetuado() {

	$('.w_saleorder').remove();

	$('.saleorder_header').html("Pedidos Confirmados");

	$('.saleorder_notfound').addClass('hidden');
	$('.saleorder_loading').removeClass('hidden');

	let data = {

		action: 'saleorder_show_efetuado',
	}

	let response = await Post("sale_order.php", data);

	if (response != null) {

		SaleOrderHUD(response['total_andamento'], response['total_efetuado'], response['total_producao'], response['total_entrega']);

		$('.saleorder_loading').addClass('hidden');

		$(".w_saleorder_container .tbody").html(response['extra_block_orders']);
		$('.w_saleorder_container').data("window", "saleorder_efetuado");

		if ($('.w_saleorder').length > 0) {

			$('.saleorder_notfound').addClass('hidden');

		} else {

			$('.saleorder_notfound').removeClass('hidden');
		}
	}
}

/**
  * Event to show saleorders "confirmados"
  */
$(document).on("click", ".bt_saleorder_efetuado", async function() {

	await SaleOrderShowEfetuado();
});

async function SaleOrderShowPrazo() {

	// $('.w_saleorder').remove();

	// $('.saleorder_header').html("Pedidos Confirmados");

	// $('.saleorder_notfound').addClass('hidden');
	// $('.saleorder_loading').removeClass('hidden');

	let data = {

		action: 'saleorder_show_prazo',
	}

	let response = await Post("sale_order.php", data);

	if (response != null) {

		$('.saleorder_loading').addClass('hidden');

		$(".w_saleorder_container .tbody").html(response['extra_block_orders']);
		$('.w_saleorder_container').data("window", "saleorder_efetuado");

		if ($('.w_saleorder').length > 0) {

			$('.saleorder_notfound').addClass('hidden');

		} else {

			$('.saleorder_notfound').removeClass('hidden');
		}
	}
}

async function SaleOrderShowProducao() {

	$('.w_saleorder').remove();

	$('.saleorder_header').html("Pedidos em Produção");

	$('.saleorder_notfound').addClass('hidden');
	$('.saleorder_loading').removeClass('hidden');

	let data = {

		action: 'saleorder_show_producao',
	}

	let response = await Post("sale_order.php", data);

	if (response != null) {

		SaleOrderHUD(response['total_andamento'], response['total_efetuado'], response['total_producao'], response['total_entrega']);

		$('.saleorder_loading').addClass('hidden');

		$(".w_saleorder_container .tbody").html(response['extra_block_orders']);
		$('.w_saleorder_container').data("window", "saleorder_producao");

		if ($('.w_saleorder').length > 0) {

			$('.saleorder_notfound').addClass('hidden');

		} else {

			$('.saleorder_notfound').removeClass('hidden');
		}
	}
}

/**
  * Event to show saleorders "producao"
  */
$(document).on("click", ".bt_saleorder_producao", async function() {

	SaleOrderShowProducao();
});

/**
  * Event to change saleorder status
  * action {'sale_order_producao'}
  */
$(document).on("click", ".saleorder_bt_changestatus", async function() {

	let button = $(this);
	let container = button.closest('.w_saleorder');

	let id_venda = button.data('id_venda');
	let versao = $(".w_saleorder_" + id_venda).data("versao");
	let total = container.data("total");
	let action = button.data('action');

	Disable(button);
	MenuClose();

	let yes = async function() {

		if (WindowManager.page != "sale_order.php") {

			await LoadPage("sale_order.php");
		}

		let window = $('.w_saleorder_container').data('window');

		data = {
			action: action,
			id_venda: id_venda,
			versao: versao,
			window: window
		}

		let saleorder = null;

		let response = await Post("sale_order.php", data);

		if (response != null) {

			switch(action) {

				case 'saleorder_producao':

					await SaleOrderShowProducao();

					saleorder = $('.w_saleorder_' + id_venda);

					ContainerFocus(saleorder, true);

					// button.closest('.popup').addClass('hidden');

					break;

				case 'saleorder_entrega':

					await SaleOrderShowEntrega();

					saleorder = $('.w_saleorder_' + id_venda);

					ContainerFocus(saleorder, true);

					break;
			}

			SaleOrderHUD(response['total_andamento'], response['total_efetuado'], response['total_producao'], response['total_entrega']);

		} else {

			Enable(button);
		}

		return true;
	}

	let no = async function() {

		Enable(button);
	}

	let ask = "Pedido # " + id_venda + "<br>Valor: R$ " + total.toLocaleString("pt-BR", {minimumFractionDigits: 2} ) + "<br>";

	switch(action) {

		case "saleorder_producao":

			ask +=  "Colocar em Produção?";
			break;

		case "saleorder_entrega":

			ask +=  "Colocar em Entrega?";
			break;

	}

	MessageBox.Show(ask, yes, no);

});

/**
  * Function to show saleorders "em entrega"
  */
async function SaleOrderShowEntrega() {

	$('.w_saleorder').remove();

	$('.saleorder_header').html("Pedidos em Entrega");

	$('.saleorder_notfound').addClass('hidden');
	$('.saleorder_loading').removeClass('hidden');

	let data = {
		action: 'saleorder_show_entrega',
	}

	let response = await Post("sale_order.php", data);

	if (response != null) {

		SaleOrderHUD(response['total_andamento'], response['total_efetuado'], response['total_producao'], response['total_entrega']);

		$('.saleorder_loading').addClass('hidden');

		$(".w_saleorder_container .tbody").html(response['extra_block_orders']);
		$('.w_saleorder_container').data("window", "saleorder_entrega");

		if ($('.w_saleorder').length > 0) {

			$('.saleorder_notfound').addClass('hidden');

		} else {

			$('.saleorder_notfound').removeClass('hidden');
		}
	}
}

/**
  * Event to show saleorders "em entrega"
  */
$(document).on("click", ".bt_saleorder_entrega", async function() {

	SaleOrderShowEntrega();
});

/**
  * Event to add saleorder product with complements
  */
$(document).on("click", ".bt_sale_complement", async function() {

	let saleorder = $(".w_saleorder_complement");

	let complementGroups = $(".produtct_complementgroup_container", saleorder);

	let groups = [];

	complementGroups.each( function(index, complementGroup) {

		groups.push ({
			complement_id: $(complementGroup).data("id_complementogrupo"),
			products: []
		});

		checkbox = $("input[type=checkbox]:checked", complementGroup);

		if (checkbox.length < $(complementGroup).data("min") || checkbox.length > $(complementGroup).data("max")) {

			console.log(complementGroup);

			Message.Show("Selecione entre " + $(complementGroup).data("min") + " e " + $(complementGroup).data("max") + " itens.", Message.MSG_ERROR);
			return;
		}

		checkbox.each( function(index, checkbox) {

			complement_product = $(checkbox).parents(".complementproduct");
			product_obs = $(complement_product).find("input[type=text]");

			groups[groups.length -1].products.push({
				id_produto: $(complement_product).data("id_produto"),
				obs: product_obs[0].value
			})

			console.log(product_obs[0].value);
		});
	});

	// console.log(complementGroup);
	console.log(groups);

	let id_venda = saleorder.data("id_venda");
	let id_produto = saleorder.data("id_produto");
	let qtd = saleorder.data("qtd");
	let obs = saleorder.data("obs");

	let data = {
		id_venda: id_venda,
		id_produto: id_produto,
		qtd: qtd,
		obs: obs
	}

	console.log(data);
});