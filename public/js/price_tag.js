/**
  * Event to clear tag list.
  */
 $(document).on("click", ".pricetag_bt_clear", async function() {

	var button = $(this);

	button.addClass('disabled');

	var data = {
		action: "pricetag_clear",
	};

	response = await Post("price_tag.php", data);

	MenuClose();

	if (response != null) {

		$('.w-pricetag-container').find('.tbody').html(response);

		$('.pricetag_bt_clear').addClass('hidden');

	}

	button.removeClass('disabled');
});

/**
  * Event to delete product from tag list.
  */
 $(document).on("click", ".pricetag_bt_del", async function() {

	var button = $(this);

	var pricetag = button.closest('.w-pricetag');

	var container = pricetag.closest('.tbody');

	// Disable(button);
	Disable(button);

	var data = {
		action: "pricetag_del",
		id_etiqueta: pricetag.data('id_etiqueta')
	};

	response = await Post("price_tag.php", data);

	if (response != null) {

		if (response.length) {

			ContainerRemove(pricetag, function() {

				container.html(response);
			});

			$('.pricetag_bt_clear').addClass('hidden');

		} else {

			ContainerRemove(pricetag);
		}

	} else {

		// Enable(button);
		Enable(button);
	}
});

/**
  * Event to add product to tag print.
  */
$(document).on("submit", ".frm_pricetag", async function(event) {

	event.preventDefault();

	let field = $(this).find(".product_search");

	let produto = field.data("sku");

	if (!produto) {

		produto = field.val();
	}

	let form = $(this);

	let container = $('.w-pricetag-container').find('.tbody');

	FormDisable(form);

	let data = {
		action: "pricetag_add",
		id_produto: produto
	}

	response = await Post("price_tag.php", data);

	if (response != null) {

		$('.pricetag_not_found').remove();

		let content = $(response);

		container.append(content);

		ContainerFocus(content);

		field.val("");

		$('.pricetag_bt_clear').removeClass('hidden');
		$('.w-pricetag-new-popup').addClass("hidden");
	}

	FormEnable(form);
	AutoFocus(form);
});

/**
  * Event to print pricetag.
  */
$(document).on("click", ".pricetag_bt_print", async function() {

	let button = $(this);

	if ($('.pricetag_not_found').length) {

		Message.Show('Não há etiquetas para impressão.', Message.MSG_INFO);

	} else {

		Disable(button);

		let pricetag_option = $("#pricetag_option").val();
		let pricetag_model = $("#pricetag_model").val();

		let	data = {
			action: 'pricetag_print',
			pricetag_option: pricetag_option,
			pricetag_model: pricetag_model,
		}

		let response = await Post("price_tag.php", data);

		if (response != null) {

			Printer.Print(response);

			Modal.CloseAll();
		}

		Enable(button);
	}
});

/**
  * Shows new pricetag popup
  */
 $(document).on("click", ".pricetag_bt_show_new", function() {

	// let button = $(this);

	// button.addClass('disabled');

	$('.w-pricetag-new-popup').removeClass("hidden");

	AutoFocus($('.w-pricetag-new-popup'));

	// button.removeClass('disabled');
});

/**
  * Shows print pricetag popup
  */
 $(document).on("click", ".pricetag_bt_show_print", async function() {

	let button = $(this);

	Disable(button);

	let data = {
		action: "pricetag_popup_print"
	}

	response = await Post("price_tag.php", data);

	if (response != null) {

		Modal.Show(Modal.POPUP_SIZE_SMALL, "Impressão de Etiquetas", response, null);
	}

	MenuClose();

	Enable(button);
});