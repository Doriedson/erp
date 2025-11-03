class ProductSearch {

	static get timeout() { return 600; }

	constructor() {

		this.searching = false;
		this.timeout = 0;
		this.queue = [];
	}
}

let productSearch = new ProductSearch();

async function ProductFormEdit(container, button, action) {

	let data = {
		action: action,
		id_produto: button.data("id_produto"),
	}

	return await FormEdit(container, button, data, "product.php");
}

async function ProductFormCancel(container, field, action) {

	let form = field.closest('form');

	let id_produto = form.data('id_produto');

	let data = {
		action: action,
		id_produto: id_produto,
	}

	return await FormCancel(container, form, field, data, "product.php");
}

async function ProductFormSave(container, form, field, action) {

	let data = {
		action: action,
		id_produto: form.data('id_produto'),
		value: field.val(),
		page: WindowManager.page
	}

	return await FormSave(container, form, field, data, "product.php");
}

/**
 * Autosearch for products
 * @param {*} field
 * @returns
 */
async function ProductAutoSearch(field, value, handle) {

	let source = field.data('source');
	let sort = field.data('sort');
	let container;

	// productSearch.searching = true;

	if (field.data("last_search") == value) {

		productSearch.searching = false;
		// productSearch.timeout = 0;
		return;
	}

	field.data("last_search", value);

	switch(source) {

		case "popup":

			container = field.closest('.autocomplete-dropdown').find('.dropdown-list');
			container.html(imgLoading);

			if (field.is(':focus')) {
				container.show();
			}

			break;

		case "product":

			$('.w_productsector_not_found').addClass('hidden');
			container = $('.productsector_table');
			container.html(imgLoading);

			break;

		case "waiter":

			$('.waitersector_notfound').addClass('hidden');
			container = $('.w_waitersector_table');
			container.html(imgLoading);

			break;

		default:

			Message.Show("Container não localizado para procura de produto!", Message.MSG_ERROR);
			productSearch.searching = false;
			// productSearch.timeout = 0;
			return;
	}

	data = {
		action: 'product_search_autocomplete',
		sort: sort || '',
		value: value,
		source: source
	}

	response = await Post("product.php", data);

	if (response != null) {

		// Consulta descartada
		if (handle != productSearch.timeout) {

			return;
		}

		if (response == "" && source == "waiter") {

			$('.waitersector_notfound').removeClass('hidden');
			container.html("");

		} else {

			container.html(response);

			switch(source) {

				case "popup":

					container[0].scrollIntoView(false);
				break;

				case "product":

					$('.expandable').slideDown("fast");
				break;

				case "waiter":

					$('.w_waiter_product_sector > .expandable').slideDown("fast");
					WaiterProductUpdate();
				break;
			}
		}


	} else {

		switch(source) {

			case "product":

				$('.w_productsector_not_found').removeClass('hidden');
				break;

			case "waiter":

				$('.waitersector_notfound').removeClass('hidden');
				break;
		}

		container.html("");
	}

	// if (productSearch.queue.length) {

	// 	let _field = productSearch.queue[0].field;
	// 	let _value = productSearch.queue[0].value;
	// 	productSearch.queue = [];

	// 	productSearch.timeout = setTimeout(function() {

	// 		ProductAutoSearch(_field, _value, productSearch.timeout);
	// 	}, 0)

	// } else {

		productSearch.searching = false;
		// productSearch.timeout = 0;
	// }
}

async function ProductSearchKeyup(field, keycode) {

	switch (keycode) {

		case 38: // up
		case 40: // down
			return;

			break;
	}

// 	if (productSearch.searching) {

// 		productSearch.queue = [{
// 			field: field,
// 			value: field.val()
// 		}];

// 	} else {


		if (productSearch.timeout > 0) {

			clearTimeout(productSearch.timeout);
		}

		productSearch.timeout = setTimeout(function() {

			ProductAutoSearch(field, field.val(), productSearch.timeout);

		}, ProductSearch.timeout);
	// }
}

/**
  * Loads product by name for autocomplete.
  */
 $(document).on("keyup", ".product_search", async function(event) {

	event.preventDefault();

	let field = $(this);

	ProductSearchKeyup(field, event.keyCode);

});

/**
  * Toggle active and not active to the product on the store
  */
$(document).on("click", ".product_bt_status", async function() {

	var button = $(this);

	var id_produto = button.data('id_produto');

	Disable(button);

	data = {
		action: "produto_change_status",
		id_produto: id_produto,
	}

	response = await Post("product.php", data);

	if (response != null) {

		$('.product_' + id_produto + '_status').replaceWith(response);
	}

	Enable(button);
});

/**
  * Open "produto" edition
  */
 $(document).on("click", ".product_bt_produto", async function() {

	ProductFormEdit($(this), $(this), "product_produto_edit");
});

/**
  * Cancels "produto" edition
  */
 $(document).on("focusout", "#frm_product_produto #produto", async function() {

	ProductFormCancel($(this).closest('form'), $(this), "product_produto_cancel");
});

/**
  * Saves "produto" edition.
  */
 $(document).on("submit", "#frm_product_produto", async function(event) {

	event.preventDefault();

	let form = $(this);

	let id_produto = form.data("id_produto");

	FormDisable(form);

	let response = await ProductFormSave(form, form, $(this.produto), "product_produto_save");

	$(".product_" + id_produto + "_produto").replaceWith(response['data']);
});

/**
  * Open "obs" edition
  */
 $(document).on("click", ".product_bt_obs", async function() {

	ProductFormEdit($(this).closest('.container'), $(this), "product_obs_edit");
});

/**
  * Cancels "obs" edition
  */
 $(document).on("focusout", "#frm_product_obs #obs", async function() {

	ProductFormCancel($(this).closest('form'), $(this), "product_obs_cancel");
});

/**
  * Saves "obs" edition.
  */
 $(document).on("submit", "#frm_product_obs", async function(event) {

	event.preventDefault();

	FormDisable($(this));

	ProductFormSave($(this), $(this), $(this.obs), "product_obs_save");
});

/**
  * Open "setor" edition
  */
 $(document).on("click", ".product_bt_setor", async function() {

	ProductFormEdit($(this).closest('.container'), $(this), "product_setor_edit");
});

/**
  * Cancels "setor" edition
  */
 $(document).on("focusout", "#frm_product_setor #id_produtosetor", async function() {

	ProductFormCancel($(this).closest('form'), $(this), "product_setor_cancel");
});

/**
  * Saves "setor" edition.
  */
 $(document).on("change", "#frm_product_setor", async function(event) {

	event.preventDefault();

	FormDisable($(this));

	ProductFormSave($(this), $(this), $(this.id_produtosetor), "product_setor_save");
});

/**
  * Open "impressora" edition
  */
 $(document).on("click", ".product_bt_impressora", async function() {

	ProductFormEdit($(this).closest('.container'), $(this), "product_impressora_edit");
});

/**
  * Cancels "impressora" edition
  */
 $(document).on("focusout", "#frm_product_impressora #impressora", async function() {

	ProductFormCancel($(this).closest('form'), $(this), "product_impressora_cancel");
});

/**
  * Saves "impressora" edition.
  */
 $(document).on("change", "#frm_product_impressora", async function(event) {

	event.preventDefault();

	FormDisable($(this));

	ProductFormSave($(this), $(this), $(this.impressora), "product_impressora_save");
});

/**
  * Open "unidade" edition
  */
 $(document).on("click", ".product_bt_unidade", async function() {

	ProductFormEdit($(this).closest('.container'), $(this), "product_unidade_edit");
});

/**
  * Cancels "unidade" edition
  */
 $(document).on("focusout", "#frm_product_unidade #id_produtounidade", async function() {

	ProductFormCancel($(this).closest('form'), $(this), "product_unidade_cancel");
});

/**
  * Saves "unidade" edition.
  */
 $(document).on("change", "#frm_product_unidade", async function(event) {

	event.preventDefault();

	let form = $(this);

	let container = form.closest('.w-product');

	FormDisable(form);

	let resp = await ProductFormSave(form, form, $(this.id_produtounidade), "product_unidade_save");

	if (resp != null) {

		if (container.closest(".popup").length > 0) {

			container.replaceWith($(resp['product']).removeClass("tr"));

		} else {

			container.replaceWith(resp['product']);
		}
	}
});

/**
  * Open "preco" edition
  */
$(document).on("click", ".product_bt_preco", async function() {

	let button = $(this);
	let container = button.closest(".container");

	let data = {
		action: "product_preco_edit",
		id_produto: button.data("id_produto"),
		page: WindowManager.page
	}

	if (WindowManager.page == "purchase_order.php") {

		data["id_compraitem"] = button.data("id_compraitem");
	}

	let response = await Post("product.php", data);

	if (response != null) {

		let content = $(response);

		container.replaceWith(content);

		AutoFocus(content);
	}
});

/**
  * Cancels "preco" edition
  */
$(document).on("focusout", "#frm_product_preco #preco", async function() {

	if ($(this).prop('disabled')) {
		return;
	}

	let field = $(this);

	let form = field.closest('form');

	let id_produto = form.data('id_produto');

	let data = {
		action: "product_preco_cancel",
		id_produto: id_produto,
		page: WindowManager.page
	}

	if (WindowManager.page == "purchase_order.php") {

		data["id_compraitem"] = form.data("id_compraitem");
	}

	let response = await Post("product.php", data);

	if (response != null) {

		form.replaceWith(response);
	}
});

/**
  * Saves "preco" edition.
  */
$(document).on("submit", "#frm_product_preco", async function(event) {

	event.preventDefault();

	let form = $(this);

	let field = $(this.preco);

	let id_produto = form.data('id_produto');

	FormDisable(form);

	let data = {
		action: "product_preco_save",
		id_produto: form.data('id_produto'),
		value: field.val(),
		page: WindowManager.page
	}

	if (WindowManager.page == "purchase_order.php") {

		data["id_compraitem"] = form.data("id_compraitem");
	}

	let response = await Post("product.php", data);

	if (response != null) {

		$('.product_' + id_produto + '_prices').replaceWith(response["data"]);

	} else {

		FormEnable(form);
	}
});

/**
  * Open "preco_promo" edition
  */
$(document).on("click", ".product_bt_preco_promo", async function() {

	let button = $(this);
	let container = button.closest(".container");

	let data = {
		action: "product_preco_promo_edit",
		id_produto: button.data("id_produto"),
		page: WindowManager.page
	}

	if (WindowManager.page == "purchase_order.php") {

		data["id_compraitem"] = button.data("id_compraitem");
	}

	let response = await Post("product.php", data);

	if (response != null) {

		let content = $(response);

		container.replaceWith(content);

		AutoFocus(content);
	}
});

/**
  * Cancels "preco_promo" edition
  */
$(document).on("focusout", "#frm_product_preco_promo #preco_promo", async function() {

	if ($(this).prop('disabled')) {
		return;
	}

	let field = $(this);

	let form = field.closest('form');

	let id_produto = form.data('id_produto');

	let data = {
		action: "product_preco_promo_cancel",
		id_produto: id_produto,
		page: WindowManager.page
	}

	if (WindowManager.page == "purchase_order.php") {

		data["id_compraitem"] = form.data("id_compraitem");
	}

	let response = await Post("product.php", data);

	if (response != null) {

		form.replaceWith(response);
	}
});

/**
  * Saves "preco_promo" edition.
  */
 $(document).on("submit", "#frm_product_preco_promo", async function(event) {

	event.preventDefault();

	let form = $(this);

	let field = $(this.preco_promo);

	let id_produto = form.data('id_produto');

	FormDisable(form);

	let data = {
		action: "product_preco_promo_save",
		id_produto: form.data('id_produto'),
		value: field.val(),
		page: WindowManager.page
	}

	if (WindowManager.page == "purchase_order.php") {

		data["id_compraitem"] = form.data("id_compraitem");
	}

	let response = await Post("product.php", data);

	if (response != null) {

		$('.product_' + id_produto + '_prices').replaceWith(response["data"]);
	}
});

/**
  * Opens "image_produto" edition
  */
$(document).on("click", ".product_bt_img", async function() {

	let button = $(this);

	let id_produto = button.data('id_produto');

	Disable(button);

	let data = {
		action: "image_edit",
		id_produto: id_produto,
	}

	response = await Post("product.php", data);

	if (response != null) {

		Modal.Show(Modal.POPUP_SIZE_SMALL, "Imagem do Produto", response, null);
	}

	Enable(button);
});

/**
  * Saves image edition.
  */
$(document).on("submit", "#frm_product_image", async function(event) {

	event.preventDefault();

	let form = $(this);

	let id_produto = form.data('id_produto');
	let imagem = this.imagem.value;

	FormDisable(form);

	data = {
		action: "image_save",
		id_produto: id_produto,
		imagem: imagem,
	}

	let response = await Post("product.php", data);

	if (response != null) {

		$('.w_product_image_popup').addClass('hidden');

		$('.w_product_' + id_produto).replaceWith(response);

		Modal.Close(form.closest('.popup'));

	} else {

		FormEnable(form);
	}
});

/**
  * Change the image for the product.
  */
$(document).on("change", "#frm_product_image .image_select", function(event) {

	let image = $(".img_selection");
	let value = $(this).val();

	if (value === undefined || value.length === 0) {

		image.attr('src', 'pic/' + $(this).find(':selected').data('img'));

	} else {

		image.attr('src', 'pic/' + value);
	}

});

/**
  * Open CodBar edition
  */
$(document).on("click", ".product_bt_barcode", async function() {

	let button = $(this);

	let id_produto = button.data('id_produto');

	Disable(button);

	data = {
		action: "product_barcode_edit",
		id_produto: id_produto,
	}

	let response = await Post("product.php", data);

	if (response != null) {

		Modal.Show(Modal.POPUP_SIZE_SMALL, "Código de Barras", response, null, false, "<i class='icon fa-solid fa-barcode'></i>");
	}

	Enable(button);

	MenuClose();
});

/**
  * Add codbar to product.
  */
$(document).on("submit", "#frm_product_new_barcode", async function(event) {

	event.preventDefault();

	let form = $(this);
	let container = $('.w_barcode_table');
	let field = $(this.codbar);

	let id_produto = form.data('id_produto');
	let codbar = this.codbar.value;

	FormDisable(form);

	data = {
		action: "barcode_add",
		id_produto: id_produto,
		value: codbar,
	}

	let response = await Post("product.php", data);

	FormEnable(form);

	if (response != null) {

		let content = $(response);

		$('.barcode_not_found').addClass('hidden');

		container.append(content);

		ContainerFocus(content);

		this.codbar.value = "";
	}

	this.codbar.focus();
});

/**
  * Delete BarCode.
  */
$(document).on("click", ".product_bt_barcode_delete", async function() {

	var button = $(this);

	var barcode = button.closest('.w-barcode');
	// var container = barcode.closest('.table');

	Disable(button);

	data = {
		action: "barcode_delete",
		codbar: barcode.data('codbar'),
		id_produto: button.closest('.w-productbarcode').data('id_produto')
	}

	response = await Post("product.php", data);

	if (response != null) {

		// if (response.length) {

		ContainerRemove(barcode, function() {
			// container.prepend(response);

			if ($('.w-barcode').length == 0) {

				$('.barcode_not_found').removeClass('hidden');
			}
		});

		// } else {

		// 	ContainerRemove(barcode);
		// }

	} else {

		Enable(button);
	}
});

/**
  * Open Validade edition
  */
$(document).on("click", ".product_bt_validade", async function() {

	let button = $(this);

	let id_produto = button.data('id_produto');

	button.addClass('disabled');

	let data = {
		id_produto: id_produto,
	};

	let response = await Post("/ui/products/expirations/modal", data);

	if (response != null) {

		Modal.Show(Modal.POPUP_SIZE_SMALL, "Controle de Validade", response, null, false, "<i class='icon fa-solid fa-calendar-days'></i>");
	}

	button.removeClass('disabled');

	MenuClose();
});

/**
  * Add validade to product.
  */
$(document).on("submit", "#frm_product_new_validade", async function(event) {

	event.preventDefault();

	let form = $(this);
	let container = $('.product_expdate_table');

	let id_produto = form.data('id_produto');
	let validade = this.data.value;

	FormDisable(form);

	let data = {
		id_produto: id_produto,
		validade: validade,
	};

	let response = await Post("/ui/products/expirations/add", data);

	FormEnable(form);

	if (response != null) {

		let content = $(response["product_expdate_tr"]);

		$('.product_expdate_notfound').addClass('hidden');

		container.append(content);

		ContainerFocus(content);

		container = $(".cp_expdate_table");

		if (container.length > 0) {

			$('.cp_expdate_notfound').addClass('hidden');

			if (response["cp_expdate_tr"]) {

				container.append(response["cp_expdate_tr"]);
			}
		}

		$(".productexpdate_expirated").html(response["expirated"]);
		$(".productexpdate_toexpirate").html(response["toexpirate"]);
		$(document).trigger('product:validity-updated', [{
			source: 'validade_add',
			productId: id_produto,
			response: response
		}]);
	}

	this.data.focus();
});

/**
  * Delete Validade.
  */
$(document).on("click", ".product_bt_validade_delete", async function() {

	let button = $(this);

	let id_produtovalidade = button.data('id_produtovalidade');
	let productId = null;

	const modalContainer = button.closest('.w-productvalidade');
	if (modalContainer.length) {
		productId = modalContainer.data('id_produto') || null;
	}

	Disable(button);

	let data = {
		id_produtovalidade: id_produtovalidade
	};

	let response = await Post("/ui/products/expirations/delete", data);

	if (response != null) {

		ContainerRemove($('.product_expdate_' + id_produtovalidade), function() {

			if ($(".product_expdate_tr").length == 0) {

				$(".product_expdate_notfound").removeClass('hidden');
			}
		});

		$(".productexpdate_expirated").html(response["expirated"]);
		$(".productexpdate_toexpirate").html(response["toexpirate"]);

		ContainerRemove($('.cp_expdate_' + id_produtovalidade), function() {

			if ($(".cp_expdate_tr").length == 0) {

				$(".cp_expdate_notfound").removeClass('hidden');
			}
		});

		$(document).trigger('product:validity-updated', [{
			source: 'validade_delete',
			productId: productId,
			response: response
		}]);

	} else {

		Enable(button);
		MenuClose();
	}
});

/**
  * Shows profitmargin edition
  */
$(document).on("click", ".product_bt_profitmargin", async function() {

	let button = $(this);

	let id_produto = button.data('id_produto');

	Disable(button);

	let data = {
		action: "product_profitmargin_open",
		id_produto: id_produto,
	}

	let response = await Post("product.php", data);

	if (response != null) {

		Modal.Show(Modal.POPUP_SIZE_SMALL, "Margem de Lucro", response, null, false, "<i class='icon fa-solid fa-solid fa-arrow-trend-up'></i>");

	}

	Enable(button);

	MenuClose();
});

/**
  * Opens profitmargin edition
  */
$(document).on("click", ".product_bt_profitmargin_edit", async function() {

	let button = $(this);
	let container = button.closest(".addon");

	let id_produto = button.data('id_produto');

	Disable(button);

	let data = {
		action: "product_profitmargin_edit",
		id_produto: id_produto,
	}

	let response = await Post("product.php", data);

	if (response != null) {

		let content = $(response);

		container.replaceWith(content);

		AutoFocus(content);

	} else {

		Enable(button);
	}
});

/**
  * Cancels profitmargin edition
  */
$(document).on("focusout", "#frm_product_margem_lucro #margem_lucro", async function() {

	let field = $(this);

	let form = field.closest('form');

	let data = {
		action: "product_profitmargin_cancel",
		id_produto: form.data("id_produto"),
	}

	FormCancel(form, form, field, data, "product.php");
});

/**
  * Saves profitmargin edition
  */
$(document).on("submit", "#frm_product_margem_lucro", async function(event) {

	event.preventDefault();

	let form = $(this);

	FormDisable(form);

	let field = $(this.margem_lucro);

	let data = {
		action: "product_profitmargin_save",
		id_produto: form.data("id_produto"),
		margem_lucro: field.val(),
	}

	FormSave(form, form, field, data, "product.php");
});

/**
  * Shows lossmargin edition
  */
$(document).on("click", ".product_bt_lossmargin", async function() {

	let button = $(this);

	let id_produto = button.data('id_produto');

	Disable(button);

	let data = {
		action: "product_lossmargin_open",
		id_produto: id_produto,
	}

	let response = await Post("product.php", data);

	if (response != null) {

		Modal.Show(Modal.POPUP_SIZE_SMALL, "Margem de Perda", response, null, false, "<i class='icon fa-solid fa-arrow-trend-down'></i>");

	}

	Enable(button);

	MenuClose();
});

/**
  * Opens lossmargin edition
  */
$(document).on("click", ".product_bt_lossmargin_edit", async function() {

	let button = $(this);
	let container = button.closest(".addon");

	let id_produto = button.data('id_produto');

	Disable(button);

	let data = {
		action: "product_lossmargin_edit",
		id_produto: id_produto,
	}

	let response = await Post("product.php", data);

	if (response != null) {

		let content = $(response);

		container.replaceWith(content);

		AutoFocus(content);

	} else {

		Enable(button);
	}
});

/**
  * Cancels lossmargin edition
  */
$(document).on("focusout", "#frm_product_margem_perda #margem_perda", async function() {

	let field = $(this);

	let form = field.closest('form');

	let data = {
		action: "product_lossmargin_cancel",
		id_produto: form.data("id_produto"),
	}

	FormCancel(form, form, field, data, "product.php");
});

/**
  * Saves lossmargin edition
  */
$(document).on("submit", "#frm_product_margem_perda", async function(event) {

	event.preventDefault();

	let form = $(this);

	FormDisable(form);

	let field = $(this.margem_perda);

	let data = {
		action: "product_lossmargin_save",
		id_produto: form.data("id_produto"),
		margem_perda: field.val(),
	}

	FormSave(form, form, field, data, "product.php");
});

/**
  * Opens complement window
  */
$(document).on("click", ".product_bt_complement", async function() {

	let button = $(this);

	// let container = button.closest('.group-compositionkit');

	let id_produto = button.data('id_produto');

	Disable(button);
	// button.addClass('disabled');

	data = {
		action: "product_complement_show",
		id_produto: id_produto,
	}

	let response = await Post("product.php", data);

	if (response != null) {

		Modal.Show(Modal.POPUP_SIZE_LARGE, "Complementos do Produto", response, null, false, "<i class='icon fa-solid fa-list-ul'></i>");

	}

	Enable(button);

	MenuClose();
});

/**
  * Creates new complement group
  */
$(document).on("click", ".product_bt_complementgroup_new", async function() {

	let button = $(this);

	let id_produto = button.data('id_produto');

	Disable(button);

	data = {
		action: "product_complementgroup_new",
		id_produto: id_produto,
	}

	let response = await Post("product.php", data);

	if (response != null) {

		let content = $(response);

		$(".complementgroup_not_found_" + id_produto).addClass("hidden");

		$(".w_complementgroup_table").append(content);

		ContainerFocus(content);
	}

	Enable(button);
});

/**
  * Shows complement group selection
  */
$(document).on("click", ".product_bt_complementgroup_selectshow", async function() {

	let button = $(this);

	let id_produto = button.data('id_produto');

	Disable(button);

	data = {
		action: "product_complementgroup_selectshow",
		id_produto: id_produto,
	}

	let response = await Post("product.php", data);

	if (response != null) {

		Modal.Show(Modal.POPUP_SIZE_SMALL, "Seleção de Grupo de Complementos", response, null, false);
	}

	Enable(button);
});

/**
  * Selects complement group for product
  */
$(document).on("click", ".product_bt_complementgroup_select", async function() {

	let button = $(this);

	let container = button.closest(".produtct_complementgroup_container");

	let id_complementogrupo = button.data('id_complementogrupo');
	let id_produto = button.data('id_produto');

	Disable(button);

	let data = {
		action: "product_complementgroup_select",
		id_complementogrupo: id_complementogrupo,
		id_produto: id_produto,
	}

	let response = await Post("product.php", data);

	if (response != null) {

		let content = $(response);

		$(".complementgroup_not_found_" + id_produto).addClass("hidden");

		$(".w_complementgroup_table").append(content);

		ContainerFocus(content, true);

		ContainerRemove(container);

	} else {

		Enable(button);
	}
});

/**
  * Expands complement group
  */
$(document).on("click", ".product_bt_complementgroup_expand", async function() {

	let button = $(this);

	let id_complementogrupo = button.data("id_complementogrupo");
	let id_produto = button.data("id_produto");

	let expandable = button.closest('.window').find(".expandable");

	Disable(button);

	let data = {
		action: "product_complementgroup_expand",
		id_complementogrupo: id_complementogrupo,
		id_produto: id_produto
	}

	let response = await Post("product.php", data);

	if (response != null) {

		button.removeClass("product_bt_complementgroup_expand bt_expand fa-chevron-down");
		button.addClass("bt_collapse fa-chevron-up");

		expandable.html(response);

		expandable.slideDown("fast");
	}

	Enable(button);

});

/**
  * Creates new complement group
  */
$(document).on("click", ".product_bt_complement_new", async function() {

	let button = $(this);

	let id_complementogrupo = button.data('id_complementogrupo');

	Disable(button);

	data = {
		action: "product_complement_new",
		id_complementogrupo: id_complementogrupo,
	}

	let response = await Post("product.php", data);

	if (response != null) {

		Modal.Show(Modal.POPUP_SIZE_SMALL, "Adicionar Complemento...", response, null, false);
	}

	Enable(button);
});

/**
  * Add complement to complement group.
  */
$(document).on("submit", "#frm_product_complement_item", async function(event) {

	event.preventDefault();

	let form = $(this);

	let popup = form.closest(".popup");

	let id_complementogrupo = form.data("id_complementogrupo");

	let container = $('.product_complement_table_' + id_complementogrupo);

	let product_field = $(".product_search", this);

	let id_produto = product_field.data("sku");

	if (id_produto) {

		product_field.val(product_field.data('descricao'));

	} else {

		Message.Show("Produto não localizado!", Message.MSG_ERROR);
		return;
	}

	FormDisable(form);

	data = {
		action: "product_complement_add",
		id_complementogrupo: id_complementogrupo,
		id_produto: id_produto
	}

	response = await Post("product.php", data);

	if (response != null) {

		Modal.Close(popup);

		let content = $(response);

		container.append(content);

		ContainerFocus(content, true);

		$(".product_complement_not_found_" + id_complementogrupo).addClass("hidden");

	} else {

		FormEnable(form);
		product_field.val("");
		product_field.focus();
	}
});

/**
  * Opens complement group description for edition
  */
$(document).on("click", ".product_bt_complementgroup_descricao", async function() {

	let button = $(this);

	let id_complementogrupo = button.data('id_complementogrupo');

	Disable(button);

	data = {
		action: "product_complementgroup_edit",
		id_complementogrupo: id_complementogrupo,
	}

	let response = await Post("product.php", data);

	if (response != null) {

		let content = $(response);

		button.replaceWith(content);

		AutoFocus(content);

	} else {

		Enable(button);
	}
});

/**
  * Cancels complement group description for edition
  */
$(document).on("focusout", "#frm_product_complementgroup_descricao", async function() {

	let form = $(this);

	let field = $("#frm_product_complementgroup_descricao_field", form);

	if (field.prop('disabled')) {

		return;
	}

	let id_complementogrupo = form.data('id_complementogrupo');

	FormDisable(form);

	data = {
		action: "product_complementgroup_cancel",
		id_complementogrupo: id_complementogrupo,
	}

	let response = await Post("product.php", data);

	if (response != null) {

		let content = $(response);

		form.replaceWith(content);

	} else {

		FormEnable(form);
	}
});

/**
  * Saves complement group description for edition
  */
$(document).on("submit", "#frm_product_complementgroup_descricao", async function(event) {

	event.preventDefault();

	let form = $(this);

	FormDisable(form);

	let field = $("#frm_product_complementgroup_descricao_field", form);

	let id_complementogrupo = form.data('id_complementogrupo');

	data = {
		action: "product_complementgroup_save",
		id_complementogrupo: id_complementogrupo,
		descricao: field.val()
	}

	let response = await Post("product.php", data);

	if (response != null) {

		let content = $(response);

		form.replaceWith(content);

	} else {

		FormEnable(form);
	}
});

/**
  * Adds qtd_min for complement group
  */
$(document).on("click", ".product_bt_complementgroup_min_add", async function() {

	let button = $(this);

	Disable(button);

	let id_complementogrupo = button.data('id_complementogrupo');
	let container = button.closest(".complementgroup_minmax_container");

	data = {
		action: "product_complementgroup_min_add",
		id_complementogrupo: id_complementogrupo
	}

	let response = await Post("product.php", data);

	if (response != null) {

		container.html(response);

	} else {

		Enable(button);
	}
});

/**
  * Dels qtd_min for complement group
  */
$(document).on("click", ".product_bt_complementgroup_min_del", async function() {

	let button = $(this);

	Disable(button);

	let id_complementogrupo = button.data('id_complementogrupo');
	let container = button.closest(".complementgroup_minmax_container");

	data = {
		action: "product_complementgroup_min_del",
		id_complementogrupo: id_complementogrupo
	}

	let response = await Post("product.php", data);

	if (response != null) {

		container.html(response);

	} else {

		Enable(button);
	}
});

/**
  * Adds qtd_max for complement group
  */
$(document).on("click", ".product_bt_complementgroup_max_add", async function() {

	let button = $(this);

	Disable(button);

	let id_complementogrupo = button.data('id_complementogrupo');
	let container = button.closest(".complementgroup_minmax_container");

	data = {
		action: "product_complementgroup_max_add",
		id_complementogrupo: id_complementogrupo
	}

	let response = await Post("product.php", data);

	if (response != null) {

		container.html(response);

	} else {

		Enable(button);
	}
});

/**
  * Dels qtd_min for complement group
  */
$(document).on("click", ".product_bt_complementgroup_max_del", async function() {

	let button = $(this);

	Disable(button);

	let id_complementogrupo = button.data('id_complementogrupo');
	let container = button.closest(".complementgroup_minmax_container");

	data = {
		action: "product_complementgroup_max_del",
		id_complementogrupo: id_complementogrupo
	}

	let response = await Post("product.php", data);

	if (response != null) {

		container.html(response);

	} else {

		Enable(button);
	}
});

/**
  * Dels product from complement group
  */
$(document).on("click", ".product_bt_complementgroup_product_del", async function() {

	let button = $(this);

	Disable(button);

	let id_produtocomplemento = button.data('id_produtocomplemento');
	let id_complementogrupo = button.data('id_complementogrupo');
	let container = button.closest(".tr");

	data = {
		action: "product_complementgroup_product_del",
		id_produtocomplemento: id_produtocomplemento
	}

	let response = await Post("product.php", data);

	if (response != null) {

		ContainerRemove(container, function() {

			if ($(".product_complement_table_" + id_complementogrupo + " .tr").length == 0) {

				$(".product_complement_not_found_" + id_complementogrupo).removeClass("hidden");
			}
		});

	} else {

		Enable(button);
	}
});

/**
  * Opens composition edition
  */
$(document).on("click", ".product_bt_composition", async function() {

	let button = $(this);

	// let container = button.closest('.group-compositionkit');

	let id_produto = button.data('id_produto');

	Disable(button);
	// button.addClass('disabled');

	data = {
		action: "composition_open",
		id_produto: id_produto,
	}

	let response = await Post("product.php", data);

	if (response != null) {

		Modal.Show(Modal.POPUP_SIZE_SMALL, "Composição do Produto", response, null, false, "<i class='icon fa-solid fa-code-merge'></i>");

	}

	Enable(button);

	MenuClose();
});

/**
  * Add composition product.
  */
$(document).on("submit", "#frm_product_composition", async function(event) {

	event.preventDefault();

	let form = $(this);

	let container = $('.w_composition_table');

	FormDisable(form);

	let id_produto = this.id_produto.value;

	if($(this.id_produto).data("sku")) {

		id_produto = $(this.id_produto).data("sku")
	}

	let id_composicao = form.data('id_composicao');
	let qtd = this.qtd.value;

	let data = {
		action: "composition_add",
		id_composicao: id_composicao,
		id_produto: id_produto,
		qtd: qtd,
	}

	let response = await Post("product.php", data);

	if (response != null) {

		let content = $(response["composition"]);

		container.append(content);

		$('.w_product_' + id_composicao).replaceWith(response['product']);

		$('.composition_not_found').addClass('hidden');

		ContainerFocus(content);

		this.id_produto.value = "";
		this.qtd.value = "";
	}

	FormEnable(form);
	this.id_produto.focus();
});

/**
  * Deletes composition product
  */
$(document).on("click", ".product_bt_composition_delete", async function(event) {

	let button = $(this);

	let composition = button.closest(".w-composition");
	let container = composition.closest('.table');

	let id_produto = composition.data("id_produto");
	let id_composicao = button.closest(".w_product_composition").data("id_produto");

	Disable(button);

	data = {
		action: "product_composition_item_del",
		id_composicao: id_composicao,
		id_produto: id_produto,
	}

	let response = await Post("product.php", data);

	if (response != null) {

		ContainerRemove(composition, function() {

			if ($('.w-composition').length == 0) {

				$('.composition_not_found').removeClass('hidden');
			}
		});

		$('.w_product_' + id_composicao).replaceWith(response);

	} else {

		Enable(button)
	}
});

/**
  * Opens "product composition qtd" edition
  */
$(document).on("click", ".composition_bt_qtd", async function() {

	var container = $(this).closest('.container');
	var button = $(this);

	data = {
		action: "product_composition_qtd_edit",
		id_produto: button.closest('.w-composition').data("id_produto"),
		id_composicao: button.closest('.w_product_composition').data('id_produto')
	}

	FormEdit(container, button, data, "product.php");
});

/**
  * Cancels "product composition qtd" edition
  */
$(document).on("focusout", "#frm_composition_qtd #qtd", async function() {

	var field = $(this);
	var form = field.closest('form');

	data = {
		action: "product_composition_qtd_cancel",
		id_produto: form.closest('.w-composition').data("id_produto"),
		id_composicao: form.closest('.w_product_composition').data('id_produto')
	}

	FormCancel(form, form, field, data, "product.php");
});

/**
  * Saves "product composition qtd" edition
  */
$(document).on("submit", "#frm_composition_qtd", async function(event) {

	event.preventDefault();

	var form = $(this);

	FormDisable(form);

	var field = $(this.qtd);

	var data = {
		action: "product_composition_qtd_save",
		id_produto: field.closest('.w-composition').data("id_produto"),
		id_composicao: field.closest('.w_product_composition').data('id_produto'),
		qtd: field.val(),
	}

	FormSave(form, form, field, data, "product.php");
});

/**
  * Open kit edition
  */
 $(document).on("click", ".product_bt_kit", async function() {

	let button = $(this);

	// let container = button.closest('.group-compositionkit');

	let id_produto = button.data('id_produto');

	Disable(button);

	data = {
		action: "kit_open",
		id_produto: id_produto,
	}

	let response = await Post("product.php", data);

	if (response != null) {

		Modal.Show(Modal.POPUP_SIZE_SMALL, "Kit do Produto", response, null, false, "<i class='icon fa-solid fa-boxes-stacked'></i>");
	}

	Enable(button);

	MenuClose();
});

/**
  * Closes kit edition.
  */
//  $(document).on("click", ".bt_kit_close", async function() {

// 	var button = $(this);

// 	var container = button.closest('.w_productkit');
// 	var id_produto = button.data('id_produto');

// 	Disable(button);

// 	data = {
// 		action: 'kit_close',
// 		value: id_produto
// 	}

// 	response = await Post("product.php", data);

// 	if (response != null) {

// 		container.replaceWith(response);

// 	} else {

// 		Enable(button);
// 	}
// });

/**
  * Adds product to kit.
  */
$(document).on("submit", "#frm_product_kit", async function(event) {

	event.preventDefault();

	let form = $(this);

	let container = $('.w_kit_table');

	FormDisable(form);

	let id_produto = this.id_produto.value;
	let id_kit = form.data('id_kit');
	let qtd = this.qtd.value;

	data = {
		action: "kit_add",
		id_kit: id_kit,
		id_produto: id_produto,
		qtd: qtd,
	}

	response = await Post("product.php", data);

	if (response != null) {

		$('.kit_not_found').addClass('hidden');

		$('.w_product_' + id_kit).replaceWith(response['product']);

		let content = $(response["kit"]);

		container.append(content);

		ContainerFocus(content);

		this.id_produto.value = "";
		this.qtd.value = "";
	}

	FormEnable(form);
	this.id_produto.focus();
});

/**
  * Deletes product from kit
  */
 $(document).on("click", ".product_bt_kit_delete", async function(event) {

	let button = $(this);

	let kit = button.closest(".w-kit");

	let id_produto = kit.data("id_produto");
	let id_kit = button.closest(".w_productkit").data("id_produto");

	Disable(button);

	let data = {
		action: "product_kit_item_del",
		id_kit: id_kit,
		id_produto: id_produto,
	}

	let response = await Post("product.php", data);

	if (response != null) {

		$('.w_product_' + id_kit).replaceWith(response);

		ContainerRemove(kit, function() {

			if($('.w-kit').length == 0) {

				$('.kit_not_found').removeClass('hidden');
			}
		});

	} else {

		Enable(button);
	}
});

/**
  * Open "produto qtd kit" edition
  */
 $(document).on("click", ".kit_bt_qtd", async function() {

	var container = $(this).closest('.container');
	var button = $(this);

	data = {
		action: "product_kit_qtd_edit",
		id_produto: button.closest('.w-kit').data("id_produto"),
		id_kit: button.closest('.w_productkit').data('id_produto')
	}

	FormEdit(container, button, data, "product.php");
});

/**
  * Cancels "produto qtd kit" edition
  */
 $(document).on("focusout", "#frm_kit_qtd #qtd", async function() {

	var field = $(this);
	var form = field.closest('form');

	data = {
		action: "product_kit_qtd_cancel",
		id_produto: form.closest('.w-kit').data("id_produto"),
		id_kit: form.closest('.w_productkit').data('id_produto')
	}

	FormCancel(form, form, field, data, "product.php");
});

/**
  * Saves "produto qtd kit" edition.
  */
 $(document).on("submit", "#frm_kit_qtd", async function(event) {

	event.preventDefault();

	let form = $(this);

	FormDisable(form);

	let field = $(this.qtd);

	let id_kit = field.closest('.w_productkit').data('id_produto');

	let w_kit = $(this).closest('.w-kit');
	// let group_preco = $(this).closest(".w-product").find(".group-preco");

	let data = {
		action: "product_kit_qtd_save",
		id_produto: field.closest('.w-kit').data("id_produto"),
		id_kit: id_kit,
		value: field.val(),
	}

	let response = await FormSave(w_kit, form, field, data, "product.php");

	if (response != null) {

		// group_preco.replaceWith(response["preco"]);
		$('.w_product_' + id_kit).replaceWith(response['product']);
	}
});

/**
  * Opens "produto preco kit" edition
  */
 $(document).on("click", ".kit_bt_preco", async function() {

	var container = $(this).closest('.container');
	var button = $(this);

	data = {
		action: "product_kit_preco_edit",
		id_produto: button.closest('.w-kit').data("id_produto"),
		id_kit: button.closest('.w_productkit').data('id_produto')
	}

	FormEdit(container, button, data, "product.php");
});

/**
  * Cancels "produto preco kit" edition
  */
$(document).on("focusout", "#frm_kit_preco #preco", async function() {

	var field = $(this);
	var form = field.closest('form');

	data = {
		action: "product_kit_preco_cancel",
		id_produto: form.closest('.w-kit').data("id_produto"),
		id_kit: form.closest('.w_productkit').data('id_produto')
	}

	FormCancel(form, form, field, data, "product.php");
});

/**
  * Saves "produto preco kit" edition
  */
$(document).on("submit", "#frm_kit_preco", async function(event) {

	event.preventDefault();

	let form = $(this);

	FormDisable(form);

	let field = $(this.preco);

	let id_kit = field.closest('.w_productkit').data('id_produto');

	let w_kit = $(this).closest(".w-kit");
	// let group_preco = $(this).closest(".w-product").find(".group-preco");

	let data = {
		action: "product_kit_preco_save",
		id_produto: field.closest('.w-kit').data("id_produto"),
		id_kit: id_kit,
		value: field.val(),
	}

	let response = await FormSave(w_kit, form, field, data, "product.php");

	if (response != null) {

		// group_preco.replaceWith(response["preco"]);
		$('.w_product_' + id_kit).replaceWith(response['product']);
	}
});

/**
  * Open "estoque primário" edition
  */
$(document).on("click", ".product_bt_estoque", async function() {

	// ProductFormEdit($(this), $(this), "product_estoque_edit");
	let button = $(this);

	let id_produto = button.data('id_produto');
	let screen = button.data('screen');

	Disable(button);

	data = {
		action: "product_estoque_edit",
		screen: screen,
		id_produto: id_produto
	}

	response = await Post("product.php", data);

	if (response != null) {

		let title = "";

		switch(screen) {

			case "add":

				title = "Estoque Primário";
				title_icon = "<i class='icon fa-solid fa-square-plus'></i>";
				break;

			case "del":

				title = "Estoque Primário";
				title_icon = "<i class='icon fa-solid fa-square-minus'></i>";
				break;

			case "update":

				title = "Estoque Primário";
				title_icon = "<i class='icon fa-solid fa-equals'></i>";
				break;

			case "transf":

				title = "Estoque Primário";
				title_icon = "<i class='icon fa-solid fa-left-right'></i>";
				break;
		}

		Modal.Show(Modal.POPUP_SIZE_SMALL, title, response, null, false, title_icon);
	}

	Enable(button);
	MenuClose();
});

/**
  * Saves "estoque" edition.
  */
$(document).on("submit", "#frm_product_estoque", async function(event) {

	event.preventDefault();

	let form = $(this);
	let id_produto = form.data('id_produto');
	let screen = form.data('screen');
	let action = "";

	switch(screen) {

		case "add":

			action = "product_estoque_add"
			break;

		case "del":

			action = "product_estoque_remove"
			break;

		case "update":

			action = "product_estoque_update"
			break;

		case "transf":

			action = "product_estoque_transf"
			break;

	}

	FormDisable(form);

	let data = {
		action: action,
		id_produto: id_produto,
		estoque: this.estoque.value
	}

	if (screen != "transf") {

		data['obs'] = this.obs.value;
	}

	let response = await Post("product.php", data);

	if (response != null) {

		// $('.w_product_stock_popup').addClass('hidden');

		if (screen == "transf") {

			$('.estoque_' + id_produto).replaceWith(response['estoque']);
			$('.estoque_secundario_' + id_produto).replaceWith(response['estoque_secundario']);

		} else {

			$('.estoque_' + id_produto).replaceWith(response);
		}

		Modal.Close(form.closest('.popup'));
	}

	FormEnable(form);
});

/**
  * Open "estoque secundário" edition
  */
$(document).on("click", ".product_bt_estoque_secundario", async function() {

	// ProductFormEdit($(this), $(this), "product_estoque_edit");
	let button = $(this);

	let id_produto = button.data('id_produto');
	let screen = button.data('screen');

	Disable(button);

	data = {
		action: "product_estoque_secundario_edit",
		screen: screen,
		id_produto: id_produto
	}

	response = await Post("product.php", data);

	if (response != null) {

		let title = "";

		switch(screen) {

			case "add":

				title = "Estoque Secundário";
				title_icon = "<i class='icon fa-solid fa-square-plus'></i>";
				break;

			case "del":

				title = "Estoque Secundário";
				title_icon = "<i class='icon fa-solid fa-square-minus'></i>";
				break;

			case "update":

				title = "Estoque Secundário";
				title_icon = "<i class='icon fa-solid fa-equals'></i>";
				break;

			case "transf":

				title = "Estoque Secundário";
				title_icon = "<i class='icon fa-solid fa-left-right'></i>";
				break;
		}

		Modal.Show(Modal.POPUP_SIZE_SMALL, title, response, null, false, title_icon);
	}

	Enable(button);
	MenuClose();
});

/**
  * Saves "estoque secundário" edition.
  */
$(document).on("submit", "#frm_product_estoque_secundario", async function(event) {

	event.preventDefault();

	let form = $(this);
	let id_produto = form.data('id_produto');
	let screen = form.data('screen');
	let action = "";

	switch(screen) {

		case "add":

			action = "product_estoque_secundario_add"
			break;

		case "del":

			action = "product_estoque_secundario_remove"
			break;

		case "update":

			action = "product_estoque_secundario_update"
			break;

		case "transf":

			action = "product_estoque_secundario_transf"
			break;

	}

	FormDisable(form);

	let data = {
		action: action,
		id_produto: id_produto,
		estoque: this.estoque.value
	}

	if (screen != "transf") {

		data['obs'] = this.obs.value;
	}

	let response = await Post("product.php", data);

	if (response != null) {

		// $('.w_product_stocksec_popup').addClass('hidden');

		if (screen == "transf") {

			$('.estoque_' + id_produto).replaceWith(response['estoque']);
			$('.estoque_secundario_' + id_produto).replaceWith(response['estoque_secundario']);

		} else {

			$('.estoque_secundario_' + id_produto).replaceWith(response);
		}

		Modal.Close(form.closest('.popup'));
	}

	FormEnable(form);
});

/**
  * Add new "produto"
  */
$(document).on("click", ".bt_product_new", async function() {

	let button = $(this);
	let id_produtosetor = button.data('id_produtosetor');

	Disable(button);

	let bt_expand = button.closest('.w_productsector').find('.productsector_bt_expand');

	if (bt_expand.length > 0) {

		await productsector_bt_expand_click(bt_expand);;

	} else {

		bt_expand = button.closest('.w_productsector').find('.bt_expand');

		if (bt_expand.length > 0) {

			bt_expand.click();
		}
	}

	let container = button.closest('.w_productsector').find('.product_table');

	let data = {
		action: 'product_new',
		id_produtosetor: id_produtosetor
	}

	response = await Post("product.php", data);

	if (response != null) {

		button.closest('.w_productsector').find('.product_not_found').addClass('hidden');

		if (button.data("window") == "purchase_order") {

			let data2 = {
				action: 'purchase_order_item_add',
				id_compra: button.data("id_compra"),
				value: response["id_produto"]
			}

			let response2 = await Post("purchase_order.php", data2);

			if (response2 != null) {

				let container_purchase = button.closest('.w-purchaseorder').find('.tbody');

				container_purchase.find('.w-purchaseorder-item-notfound').remove();

				let content_purchase = $(response2);

				container_purchase.append(content_purchase);

				ContainerFocus(content_purchase);
			}
		}

		let content = $(response["data"]);

		container.append(content);

		let list_products = container.find('.w-product');

		list_products.sort(function(a, b) {

			return ($(a).data("produto").toString().toUpperCase() < $(b).data("produto").toString().toUpperCase())? -1: 1;
		});

		container.html(list_products);

		ContainerFocus(content, true);

		Modal.Show(Modal.POPUP_SIZE_LARGE, "Cadastro de Produto", $(response["data"]).removeClass("tr"), null);
	}

	Enable(button);
});

/**
  * Expands product sector to show products
  */
async function productsector_bt_expand_click(button) {

	let expandable = button.closest('.w_productsector').find('.expandable');

	let container = expandable.find(".product_table");

	let id_produtosetor = button.closest('.w_productsector').data("id_produtosetor");

	Disable(button);

	// container.html(imgLoading);

	// expandable.removeClass("hidden");

	button.removeClass("productsector_bt_expand bt_expand fa-chevron-down");
	button.addClass("bt_collapse fa-chevron-up");

	let data = {
		action: 'productsector_expand',
		id_produtosetor: id_produtosetor,
	}

	let response = await Post("product.php", data);

	if (response != null) {

		let content = $(response);

		container.html(content);

		// container = container.find('.product_table');

	} else {

	// 	expandable.html("Ocorreu um erro ao carregar produtos!");
		expandable.find('.product_not_found').removeClass('hidden');
		container.html("");
	}

	expandable.slideDown("fast");
    Enable(button);
}

/**
  * Expands product sector to show products
  */
 $(document).on("click", ".productsector_bt_expand", async function() {

	let button = $(this);

	await productsector_bt_expand_click(button);
});

/**
  * Removes complement group from product
  */
 $(document).on("click", ".product_bt_complementgroup_del", async function() {

	let button = $(this);
	let id_complementogrupo = button.data("id_complementogrupo");
	let id_produto = button.data("id_produto");
	let container = button.closest(".produtct_complementgroup_container");

	Disable(button);

	let data = {
		action: 'product_complementgroup_del',
		id_complementogrupo: id_complementogrupo,
		id_produto: id_produto,
	}

	let response = await Post("product.php", data);

	if (response != null) {

		let yes = async function() {

			data = {
				action: "product_complementgroup_del_ok",
				id_complementogrupo: id_complementogrupo,
				id_produto: id_produto,
			}

			response = await Post("product.php", data);

			if (response != null) {

				ContainerRemove(container, function() {

					if ($(".produtct_complementgroup_container").length == 0) {

						$(".complementgroup_not_found").removeClass("hidden");
					}
				});

			} else {

				Enable(button);
			}

			return true;
		}

		let no = function() {

			Enable(button);
		}

		MessageBox.Show(response, yes, no);

	} else {

		Enable(button);
	}
});

/**
  * Open "preco complemento" edition
  */
$(document).on("click", ".product_bt_complementgroup_preco", async function() {

	let button = $(this);
	// let container = button.closest(".container");

	let data = {
		action: "product_precocomplemento_edit",
		id_produtocomplemento: button.data("id_produtocomplemento"),
	}

	// if (WindowManager.page == "purchase_order.php") {

	// 	data["id_compraitem"] = button.data("id_compraitem");
	// }

	let response = await Post("product.php", data);

	if (response != null) {

		let content = $(response);

		button.replaceWith(content);

		AutoFocus(content);
	}
});

/**
  * Cancels "preco complemento" edition
  */
$(document).on("focusout", "#frm_product_complementgroup_preco #preco", async function() {

	if ($(this).prop('disabled')) {
		return;
	}

	let field = $(this);

	let form = field.closest('form');

	let id_produtocomplemento = form.data('id_produtocomplemento');

	let data = {
		action: "product_precocomplemento_cancel",
		id_produtocomplemento: id_produtocomplemento,
		// page: WindowManager.page
	}

	// if (WindowManager.page == "purchase_order.php") {

	// 	data["id_compraitem"] = form.data("id_compraitem");
	// }

	let response = await Post("product.php", data);

	if (response != null) {

		form.replaceWith(response);
	}
});

/**
  * Saves "preco complemento" edition.
  */
$(document).on("submit", "#frm_product_complementgroup_preco", async function(event) {

	event.preventDefault();

	let form = $(this);

	let field = $(this.preco);

	let id_produtocomplemento = form.data('id_produtocomplemento');

	FormDisable(form);

	let data = {
		action: "product_precocomplemento_save",
		id_produtocomplemento: id_produtocomplemento,
		value: field.val(),
		// page: WindowManager.page
	}

	// if (WindowManager.page == "purchase_order.php") {

	// 	data["id_compraitem"] = form.data("id_compraitem");
	// }

	let response = await Post("product.php", data);

	if (response != null) {

		form.replaceWith(response);

	} else {

		FormEnable(form);
	}
});
