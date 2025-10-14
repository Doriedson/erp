/**
  * Expands product sector to show products
  */
$(document).on("click", ".productsector_digitalmenu_bt_expand", async function() {

	let button = $(this);

    let expandable = button.closest('.w_productsector').find('.expandable');

	let container = expandable.find(".product_table");

	let id_produtosetor = button.closest('.w_productsector').data("id_produtosetor");

	Disable(button);

	button.removeClass("productsector_digitalmenu_bt_expand bt_expand fa-chevron-down");
	button.addClass("bt_collapse fa-chevron-up");

	let data = {
		action: 'productsector_expand',
		id_produtosetor: id_produtosetor,
	}

	let response = await Post("digital_menu_config.php", data);

	if (response != null) {

		let content = $(response);

		container.html(content);

	} else {

		expandable.find('.product_not_found').removeClass('hidden');
		container.html("");
	}

	expandable.slideDown("fast");
    Enable(button);
});

/**
  * Activates/Deactivates digital menu sector
  */
$(document).on("click", ".bt_digitalmenusector", async function() {

	let button = $(this);
	let id_produtosetor = button.data('id_produtosetor');

	Disable(button);

	let data = {
		action: "digitalmenu_sector",
		id_produtosetor: id_produtosetor
	}

	let response = await Post('digital_menu_config.php', data);

	Enable(button);

	if (response != null) {

		button.prop("checked", response);
	}
});

/**
  * Activates/Deactivates digital menu product
  */
$(document).on("click", ".bt_digitalmenuproduct", async function() {

	let button = $(this);
	let id_produto = button.data('id_produto');

	Disable(button);

	let data = {
		action: "digitalmenu_product",
		id_produto: id_produto,
	}

	let response = await Post('digital_menu_config.php', data);

	Enable(button);

	if (response != null) {

		// button.replaceWith(response);
		button.prop("checked", response);

	} else {

		// Enable(button);
	}
});