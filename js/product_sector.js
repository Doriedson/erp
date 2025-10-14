async function ProductsectorFormEdit(container, button, action) {

	data = {
		action: action,
		id_produtosetor: button.data("id_produtosetor"),
	}

	return await FormEdit(container, button, data, "product_sector.php");
}

async function ProductsectorFormCancel(container, field, action) {

	var form = field.closest('form');

	var id_produtosetor = form.data('id_produtosetor');

	data = {
		action: action,
		id_produtosetor: id_produtosetor,
	}

	return await FormCancel(container, form, field, data, "product_sector.php");
}

async function ProductsectorFormSave(container, form, field, action) {

	var data = {
		action: action,
		id_produtosetor: form.data('id_produtosetor'),
		value: field.val(),
	}

	return await FormSave(container, form, field, data, "product_sector.php");
}

/**
  * Add sector
  */
$(document).on("submit", "#frm_productsector", async function(event) {

	event.preventDefault();

	let form = $(this);

	let popup = form.closest('.popup');

	let container = $('.productsector_table');

	FormDisable(form);

	let data = {
		action: 'productsector_add',
		produtosetor: this.sector.value
	}

	let response = await Post("product_sector.php", data);

	if (response != null) {

		let content = $(response);

		container.append(content);

		let list_sector = $('.w_productsector');

		list_sector.sort(function(a, b) {

			return ($(a).data("produtosetor").toString().toUpperCase() < $(b).data("produtosetor").toString().toUpperCase())? -1: 1;
		});

		container.html(list_sector);

		$('.w_productsector_not_found').addClass('hidden');

		ContainerFocus(content, true);

		content.slideDown("fast");

		if (popup.find('.popup_fixwindow').hasClass('fa-thumbtack')) {

			Modal.Close(popup);

		} else {

			FormEnable(form);
			this.sector.value = "";
			this.sector.focus();
		}

	} else {

		FormEnable(form);
	}


});

/**
  * Opens "produtosetor" edition
  */
 $(document).on("click", ".productsector_produtosetor", async function() {

	ProductsectorFormEdit($(this), $(this), 'productsector_produtosetor_edit');
});

/**
  * Cancel sector edition.
  */
 $(document).on("focusout", "#frm_productsector_produtosetor #produtosetor", async function() {

	ProductsectorFormCancel($(this).closest('form'), $(this), 'productsector_produtosetor_cancel');
});

/**
  * Save sector edition.
  */
$(document).on("submit", "#frm_productsector_produtosetor", async function(event) {

	event.preventDefault();

	FormDisable($(this));

	ProductsectorFormSave($(this), $(this), $(this.produtosetor), 'productsector_produtosetor_save');
});

/**
  * Deletes sector
  */
 $(document).on("click", ".productsector_bt_del", async function() {

	var button = $(this);

	var container = button.closest('.w_productsector');

	Disable(button);
	// Disable(button);

	data = {
		action: 'productsector_delete',
		id_produtosetor: button.data('id_produtosetor')
	}

	let yes = async function() {

		response = await Post("product_sector.php", data);

		if (response != null) {

			ContainerRemove(container);

		} else {

			Enable(button);
			// Enable(button);
		}

		return true;
	}

	let no = async function() {

		Enable(button);
		// Enable(button);
	}

	MessageBox.Show("Remover setor de produtos: <br>" + button.data('text') + " ?", yes, no);
});

/**
  * Toggle active / not active to the product sector on waiter
  */
 $(document).on("click", ".productsector_bt_garcom", async function() {

	let button = $(this);

	let id_produtosetor = button.data('id_produtosetor');

	Disable(button);

	data = {
		action: "produtosetor_waiter_status",
		id_produtosetor: id_produtosetor,
	}

	let response = await Post("product_sector.php", data);

	Enable(button);

	if (response != null) {

		button.prop("checked", response);
	}
});

/**
  * Shows new sector popup
  */
$(document).on("click", ".productsector_bt_show_new", async function() {

	let button = $(this);

	let data = {
		action: "produtosetor_new"
	}

	Disable(button);

	let response = await Post("product.php", data);

	if (response != null) {

		Modal.Show(Modal.POPUP_SIZE_SMALL, "Produto - Novo Setor", response, null, true, "<i class='icon fa-solid fa-folder-plus'></i>");
	}

	Enable(button);
});