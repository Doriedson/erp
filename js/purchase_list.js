/**
  * Event button to save the new purchase list
  */
$(document).on("submit", "#frm_purchaselist", async function(event) {

	event.preventDefault();

	var form = $(this);

	FormDisable(form);

	data = {
		action: 'purchaselist_new',
		descricao: this.descricao.value,
	}

	var response = await Post("purchase_list.php", data);

	if (response != null) {

		var content = $(response);

		$(".w-purchaselist-container").prepend(content);

		ContainerFocus(content);
		this.descricao.value = "";

		$('.purchaselist_not_found').addClass('hidden');
	}

	FormEnable(form);
	// this.descricao.focus();
});

/**
  * Deletes purchase list
  */
 $(document).on("click", ".purchaselist_bt_delete", async function() {

	let button = $(this);

	let container = button.closest('.w-purchaselist');

	let id_compralista = container.data("id_compralista");

	Disable(button);

	let data = {
		action: 'purchaselist_delete',
		id_compralista: id_compralista,
	}

	let yes = async function() {

		let response = await Post("purchase_list.php", data);

		if (response != null) {

			ContainerRemove(container, function(){

				if ($('.w-purchaselist').length == 0) {

					$('.purchaselist_not_found').removeClass('hidden');
				}
			});

		} else {

			Enable(button);
		}

		return true;
	}

	let no = async function() {

		Enable(button);
	}

	MessageBox.Show("Remover lista de compra?", yes, no);
});

/**
  * Opens "descricao" edition
  */
 $(document).on("click", ".purchaselist_bt_descricao", async function() {

	var button = $(this);

	var id_compralista = button.closest(".w-purchaselist").data("id_compralista");

	Disable(button);

	var data = {
		action: 'purchaselist_descricao_edit',
		id_compralista: id_compralista,
	}

	var response = await Post("purchase_list.php", data);

	if (response != null) {

		var content = $(response);

		button.replaceWith(content);

		AutoFocus(content);

	} else {

		Enable(button);
	}
});

/**
  * Cancels "descricao" edition.
  */
 $(document).on("focusout", "#frm_purchaselist_descricao #descricao", async function() {

	//Prevents focusout on save
 	if ($(this).prop('disabled')) {
 		return;
 	}

	var form = $(this).closest('form');

	FormDisable(form);

	var id_compralista = form.closest(".w-purchaselist").data("id_compralista");

	var data = {
		action: 'purchaselist_descricao_cancel',
		id_compralista: id_compralista,
	}

	var response = await Post("purchase_list.php", data);

	if (response != null) {

		form.replaceWith(response);

	} else {

		FormEnable(form);
	}
});

/**
  * Saves "descricao" edition.
  */
 $(document).on("submit", "#frm_purchaselist_descricao", async function(event) {

	event.preventDefault();

	var form = $(this);

	var id_compralista = form.closest('.w-purchaselist').data('id_compralista');
	var descricao = this.descricao.value;

	FormDisable(form);

	var data = {
		action: 'purchaselist_descricao_save',
		id_compralista: id_compralista,
		descricao: descricao,
	}

	var response = await Post("purchase_list.php", data);

	if (response != null) {

		form.replaceWith(response);

	} else {

		FormEnable(form);
	}
});

/**
  * Opens purchase list items
  */
 $(document).on("click", ".purchaselist_bt_expand", async function() {

	let button = $(this);

	let id_compralista = button.closest('.w-purchaselist').data('id_compralista');

	Disable(button);

	let expandable = button.closest('.window').children(".expandable:first");

	// expandable.html(imgLoading);
	// expandable.removeClass("hidden");

	let data = {
		action: 'purchaselist_open',
		id_compralista: id_compralista,
	}

	let response = await Post("purchase_list.php", data);

	if (response != null) {

		button.removeClass("purchaselist_bt_expand bt_expand fa-chevron-down");
		button.addClass("bt_collapse fa-chevron-up");

		let content = $(response);

		expandable.html(content);

		AutoFocus(content);

	} else {

		expandable.html("");
	}

	expandable.slideDown("fast");
	Enable(button);
});

/**
  * Event submit to add item for purchase list
  */
 $(document).on("submit", "#frm_purchaselist_item", async function(event) {

	event.preventDefault();

	var form = $(this);

	var field = $(this.product_search);

	var container = form.closest('.w-purchaselist').find('.table:first');

	var id_produto = field.data("sku");

	if (id_produto) {

		field.val(field.data('descricao'));

	} else {

		id_produto = field.val();
	}

	FormDisable(form);

	var data = {
		action: 'purchaselist_item_add',
		id_compralista: form.closest('.w-purchaselist').data('id_compralista'),
		produto: id_produto,
	}

	var response = await Post("purchase_list.php", data);

	if (response != null) {

		$('.w-purchaselist-notfound').remove();

		var content = $(response);

		container.append(content);

		ContainerFocus(content);

		field.val("");
	}

	FormEnable(form);
	AutoFocus(form);
});

/**
  * Event button to delete purchase list item
  */
$(document).on("click", ".purchaselist_bt_itemdelete", async function() {

	let button = $(this);

	let container = button.closest('.w-purchaselist-item');

	let table = button.closest('.table');

	Disable(button);

	let data = {
		action: 'purchaselist_item_delete',
		id_compralista: button.closest('.w-purchaselist').data('id_compralista'),
		id_compralistaitem: container.data('id_compralistaitem'),
	}

	let yes = async function() {

		let response = await Post("purchase_list.php", data);

		if (response != null) {

			if (response.length) {

				ContainerRemove(container, function() {
					table.append(response);
				});

			} else {

				ContainerRemove(container);
			}

		} else {

			Enable(button);
		}

		return true;
	}

	let no = async function() {

		Enable(button);
	}

	MessageBox.Show("Remover item da lista?", yes, no);
});

/**
  * Close purchase list edition.
  */
 $(document).on("click", ".purchase_list_bt_close_new", async function() {

	var box = $(this).closest('.purchase_list_box');

	data = {
		action: "purchase_list_close"
	}

	response = await Post("purchase_list.php", data);

	if (response != null) {

		box.html(response);
	}
});

/**
 * Shows purchalist popup.
 */
//  $(document).on("click", ".purchaselist_bt_show_new", function() {

// 	$('.w-purchaselist-new-popup').removeClass('hidden');

// 	AutoFocus($('.w-purchaselist-new-popup'));

// });