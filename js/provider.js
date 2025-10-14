class ProviderSearch {

	static get timeout() { return 600; }

	constructor() {

		this.searching = false;
		this.timeout = 0;
		this.queue = [];
	}
}

let providerSearch = new ProviderSearch();

async function ProviderFormEdit(container, button, action) {

	data = {
		action: action,
		id_fornecedor: button.data("id_fornecedor"),
	}

	return await FormEdit(container, button, data, "provider.php");
}

async function ProviderFormCancel(container, field, action) {

	var form = field.closest('form');

	var id_fornecedor = form.data('id_fornecedor');

	data = {
		action: action,
		id_fornecedor: id_fornecedor,
	}

	return await FormCancel(container, form, field, data, "provider.php");
}

async function ProviderFormSave(container, form, field, action) {

	var data = {
		action: action,
		id_fornecedor: form.data('id_fornecedor'),
		value: field.val(),
	}

	return await FormSave(container, form, field, data, "provider.php");
}

/**
 * Autosearch for providers.
 * @param {*} field
 * @returns
 */
async function ProviderAutoSearch(field, value, handle) {

	let source = field.data('source');
	// let dropdownlist = field.closest('.autocomplete-dropdown').find('.dropdown-list');
	let container;

	// let id_fornecedor = field.val();

	// if (field.data("last_search") == field.val()) {
	if (field.data("last_search") == value) {

		providerSearch.searching = false;
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

		case "provider":

			$('.provider_not_found').addClass('hidden');
			container = $('.provider_table');
			container.html(imgLoading);

			break;

		default:

			Message.Show("Container não localizado para procura de produto!", Message.MSG_ERROR);
			providerSearch.searching = false;
			// productSearch.timeout = 0;
			return;
	}

	data = {
		action: 'provider_smart_search',
		value: value,
		source: source
	}

	response = await Post("provider.php", data);

	if (response != null) {

		// Consulta descartada
		if (handle != providerSearch.timeout) {

			return;
		}

		if (response == "") {

			if (value != "") {

				Message.Show("Termo não encontrado:<br>" + value, Message.MSG_INFO);
			}

			$('.provider_not_found').removeClass('hidden');
			container.html("");

		} else {

			container.slideUp("fast", function() {

				container.html(response);
				container.slideDown("fast");
			});
		}

	} else {

		switch(source) {

			case "provider":

				$('.provider_not_found').removeClass('hidden');
			break;
		}

		container.html("");
	}

	providerSearch.searching = false;
}

/**
  * Loads provider by name for autocomplete.
  */
 $(document).on("keyup", ".provider_search", async function(event) {

	switch (event.keyCode) {

		case 38: // up
		case 40: // down
			return;

		break;
	}

	let field = $(this);

	// clearTimeout(provider_search_timeout);

	// provider_search_timeout = setTimeout(function() {

	// 	ProviderAutoSearch(field);
	// }, 300);
	if (providerSearch.timeout > 0) {

		clearTimeout(providerSearch.timeout);
	}

	providerSearch.timeout = setTimeout(function() {

		ProviderAutoSearch(field, field.val(), providerSearch.timeout);

	}, ProviderSearch.timeout);
});

/**
  * Provider search.
  */
// $(document).on("submit", "#frm_provider_search", async function(event) {

// 	event.preventDefault();

// 	var form = $(this);

// 	var field = $(this.provider_search);

// 	FormDisable(form);

// 	var provider = field.data("sku");

// 	if (provider) {

// 		field.val(field.data('descricao'));

// 	} else {

// 		provider = field.val();
// 	}

// 	var data = {
// 		action: 'provider_search',
// 		value: provider
// 	}

// 	var response = await Post("provider.php", data);

// 	if (response != null) {

// 		if (response.length == 0) {

// 			$(".provider_table").html("");

// 			$('.provider_not_found').removeClass("hidden");

// 		} else {

// 			$(".provider_table").html(response);

// 			$('.provider_not_found').addClass("hidden");
// 		}

// 	}

// 	field.select();
// 	field.data('sku', '');
// 	FormEnable(form);
// });

/**
  * Add new Provider.
  */
 $(document).on("click", ".provider_bt_new", async function(event) {

	let button = $(this);

	let container = $(".provider_table");

	let data = {
		action: 'provider_new'
	}

	if (button.closest('.popup-menu').length) {

		button.addClass('disabled');

	} else {

		Disable(button);
	}

	let response = await Post("provider.php", data);

	if (response != null) {

		$(".provider_not_found").remove();

		let content = $(response);
		container.append(content);

		ContainerFocus(content, true);

		$('.provider_not_found').addClass('hidden');
	}

	if (button.closest('.popup-menu').length) {

		button.removeClass('disabled');
		MenuClose();

	} else {

		Enable(button);
	}
});

/**
  * Gets Provider list.
  */
 $(document).on("click", ".provider_bt_list", async function(event) {

	var button = $(this);

	button.addClass('disabled');

	var data = {
		action: "provider_list"
	}

	var response = await Post("provider.php", data);

	if (response != null) {

		if (response.length == 0) {

			$(".provider_table").html("");

			$('.provider_not_found').removeClass("hidden");

		} else {

			$(".provider_table").html(response);

			$('.provider_not_found').addClass("hidden");
		}
	}

	button.removeClass('disabled');
	MenuClose();
});

/**
  * Toggle provider active or not active
  */
 $(document).on("click", ".provider_bt_status", async function() {

	var button = $(this);

	var id_fornecedor = button.data('id_fornecedor');

	Disable(button);

	data = {
		action: "provider_change_status",
		id_fornecedor: id_fornecedor,
	}

	response = await Post("provider.php", data);

	if (response != null) {

		button.replaceWith(response);
	}
});

/**
  * Event to new sale order from entity
  */
 $(document).on("click", ".provider_bt_expand", async function() {

	let button = $(this);

	let id_fornecedor = button.data('id_fornecedor');

    let expandable = $(this).closest('.w-provider').find(".expandable");

    Disable(button);

	data = {
		action: 'provider_expand',
		id_fornecedor: id_fornecedor,
	}

	let response = await Post("provider.php", data);

	if (response != null) {

		$(this).removeClass("provider_bt_expand bt_expand fa-chevron-down");
		$(this).addClass("bt_collapse fa-chevron-up");

		expandable.html(response);

		// expandable.removeClass("hidden");

	}

	expandable.slideDown("fast");
	Enable(button);
});

/**
  * Event button to open new purchase order from provider
  */
 $(document).on("click", ".provider_bt_new_purchaseorder", async function() {

	let button = $(this);

	let id_fornecedor = button.data('id_fornecedor');

	let container = $(".body-container");

	Disable(button);

	data = {
		action: 'purchase_order_new_from_provider',
		id_fornecedor: id_fornecedor,
	}

	let response = await Post("purchase_order.php", data);

	if (response != null) {

		container.html($(response['page']));

		let purchase_order = $(response['purchase_order']);

		$('.w-purchaseorder-container').html(purchase_order);

		ContainerFocus(purchase_order, false);

	} else {

		Enable(button);
	}
});

/**
  * Opens provider razaosocial edition
  */
 $(document).on("click", ".provider_bt_razaosocial", async function() {

	ProviderFormEdit($(this).closest('.container'), $(this), "provider_razaosocial_edit");
});

/**
  * Cancels provider razaosocial edition
  */
 $(document).on("focusout", "#frm_provider_razaosocial #razaosocial", async function() {

	ProviderFormCancel($(this).closest('form'), $(this), "provider_razaosocial_cancel");
});

/**
  * Saves provider razaosocial edition
  */
 $(document).on("submit", "#frm_provider_razaosocial", async function(event) {

	event.preventDefault();

	FormDisable($(this));

	ProviderFormSave($(this), $(this), $(this.razaosocial), "provider_razaosocial_save");
});

/**
  * Opens provider nomefantasia edition
  */
 $(document).on("click", ".provider_bt_nomefantasia", async function() {

	ProviderFormEdit($(this).closest('.container'), $(this), "provider_nomefantasia_edit");
});

/**
  * Cancels provider nomefantasia edition
  */
 $(document).on("focusout", "#frm_provider_nomefantasia #nomefantasia", async function() {

	ProviderFormCancel($(this).closest('form'), $(this), "provider_nomefantasia_cancel");
});

/**
  * Saves provider nomefantasia edition
  */
 $(document).on("submit", "#frm_provider_nomefantasia", async function(event) {

	event.preventDefault();

	FormDisable($(this));

	ProviderFormSave($(this), $(this), $(this.nomefantasia), "provider_nomefantasia_save");
});

/**
  * Opens provider cpfcnpj edition
  */
$(document).on("click", ".provider_bt_cpfcnpj", async function() {

	ProviderFormEdit($(this).closest('.container'), $(this), "provider_cpfcnpj_edit");
});

/**
  * Cancels provider cpfcnpj edition
  */
$(document).on("focusout", "#frm_provider_cpfcnpj #cpfcnpj", async function() {

	ProviderFormCancel($(this).closest('form'), $(this), "provider_cpfcnpj_cancel");
});

/**
  * Saves provider cpfcnpj edition
  */
$(document).on("submit", "#frm_provider_cpfcnpj", async function(event) {

	event.preventDefault();

	FormDisable($(this));

	ProviderFormSave($(this), $(this), $(this.cpfcnpj), "provider_cpfcnpj_save");
});

/**
  * Opens provider ie edition
  */
 $(document).on("click", ".provider_bt_ie", async function() {

	ProviderFormEdit($(this).closest('.container'), $(this), "provider_ie_edit");
});

/**
  * Cancels provider ie edition
  */
 $(document).on("focusout", "#frm_provider_ie #ie", async function() {

	ProviderFormCancel($(this).closest('form'), $(this), "provider_ie_cancel");
});

/**
  * Saves provider ie edition
  */
 $(document).on("submit", "#frm_provider_ie", async function(event) {

	event.preventDefault();

	FormDisable($(this));

	ProviderFormSave($(this), $(this), $(this.ie), "provider_ie_save");
});

/**
  * Opens provider email edition
  */
 $(document).on("click", ".provider_bt_email", async function() {

	ProviderFormEdit($(this).closest('.container'), $(this), "provider_email_edit");
});

/**
  * Cancels provider email edition
  */
 $(document).on("focusout", "#frm_provider_email #email", async function() {

	ProviderFormCancel($(this).closest('form'), $(this), "provider_email_cancel");
});

/**
  * Saves provider email edition
  */
 $(document).on("submit", "#frm_provider_email", async function(event) {

	event.preventDefault();

	FormDisable($(this));

	ProviderFormSave($(this), $(this), $(this.email), "provider_email_save");
});

/**
  * Opens provider obs edition
  */
 $(document).on("click", ".provider_bt_obs", async function() {

	ProviderFormEdit($(this).closest('.container'), $(this), "provider_obs_edit");
});

/**
  * Cancels provider obs edition
  */
 $(document).on("focusout", "#frm_provider_obs #obs", async function() {

	ProviderFormCancel($(this).closest('form'), $(this), "provider_obs_cancel");
});

/**
  * Saves provider obs edition
  */
 $(document).on("submit", "#frm_provider_obs", async function(event) {

	event.preventDefault();

	FormDisable($(this));

	ProviderFormSave($(this), $(this), $(this.obs), "provider_obs_save");
});

/**
  * Opens provider cep edition
  */
 $(document).on("click", ".provider_bt_cep", async function() {

	ProviderFormEdit($(this).closest('.container'), $(this), "provider_cep_edit");
});

/**
  * Cancels provider cep edition
  */
 $(document).on("focusout", "#frm_provider_cep #cep", async function() {

	ProviderFormCancel($(this).closest('form'), $(this), "provider_cep_cancel");
});

/**
  * Saves provider cep edition
  */
 $(document).on("submit", "#frm_provider_cep", async function(event) {

	event.preventDefault();

	FormDisable($(this));

	ProviderFormSave($(this), $(this), $(this.cep), "provider_cep_save");
});

/**
  * Opens provider endereco edition
  */
 $(document).on("click", ".provider_bt_endereco", async function() {

	ProviderFormEdit($(this).closest('.container'), $(this), "provider_endereco_edit");
});

/**
  * Cancels provider endereco edition
  */
 $(document).on("focusout", "#frm_provider_endereco #endereco", async function() {

	ProviderFormCancel($(this).closest('form'), $(this), "provider_endereco_cancel");
});

/**
  * Saves provider endereco edition
  */
 $(document).on("submit", "#frm_provider_endereco", async function(event) {

	event.preventDefault();

	FormDisable($(this));

	ProviderFormSave($(this), $(this), $(this.endereco), "provider_endereco_save");
});

/**
  * Opens provider bairro edition
  */
 $(document).on("click", ".provider_bt_bairro", async function() {

	ProviderFormEdit($(this).closest('.container'), $(this), "provider_bairro_edit");
});

/**
  * Cancels provider bairro edition
  */
 $(document).on("focusout", "#frm_provider_bairro #bairro", async function() {

	ProviderFormCancel($(this).closest('form'), $(this), "provider_bairro_cancel");
});

/**
  * Saves provider bairro edition
  */
 $(document).on("submit", "#frm_provider_bairro", async function(event) {

	event.preventDefault();

	FormDisable($(this));

	ProviderFormSave($(this), $(this), $(this.bairro), "provider_bairro_save");
});

/**
  * Opens provider cidade edition
  */
 $(document).on("click", ".provider_bt_cidade", async function() {

	ProviderFormEdit($(this).closest('.container'), $(this), "provider_cidade_edit");
});

/**
  * Cancels provider cidade edition
  */
 $(document).on("focusout", "#frm_provider_cidade #cidade", async function() {

	ProviderFormCancel($(this).closest('form'), $(this), "provider_cidade_cancel");
});

/**
  * Saves provider cidade edition
  */
 $(document).on("submit", "#frm_provider_cidade", async function(event) {

	event.preventDefault();

	FormDisable($(this));

	ProviderFormSave($(this), $(this), $(this.cidade), "provider_cidade_save");
});

/**
  * Opens provider uf edition
  */
$(document).on("click", ".provider_bt_uf", async function() {

	ProviderFormEdit($(this).closest('.container'), $(this), "provider_uf_edit");
});

/**
  * Cancels provider uf edition
  */
$(document).on("focusout", "#frm_provider_uf #uf", async function() {

	ProviderFormCancel($(this).closest('form'), $(this), "provider_uf_cancel");
});

/**
  * Saves provider uf edition
  */
$(document).on("change", "#frm_provider_uf", async function(event) {

	event.preventDefault();

	FormDisable($(this));

	ProviderFormSave($(this), $(this), $(this.uf), "provider_uf_save");
});

/**
  * Opens provider telefone1 edition
  */
 $(document).on("click", ".provider_bt_telefone1", async function() {

	ProviderFormEdit($(this).closest('.container'), $(this), "provider_telefone1_edit");
});

/**
  * Cancels provider telefone1 edition
  */
 $(document).on("focusout", "#frm_provider_telefone1 #telefone1", async function() {

	ProviderFormCancel($(this).closest('form'), $(this), "provider_telefone1_cancel");
});

/**
  * Saves provider telefone1 edition
  */
 $(document).on("submit", "#frm_provider_telefone1", async function(event) {

	event.preventDefault();

	FormDisable($(this));

	ProviderFormSave($(this), $(this), $(this.telefone1), "provider_telefone1_save");
});

/**
  * Opens provider contato1 edition
  */
 $(document).on("click", ".provider_bt_contato1", async function() {

	ProviderFormEdit($(this).closest('.container'), $(this), "provider_contato1_edit");
});

/**
  * Cancels provider contato1 edition
  */
 $(document).on("focusout", "#frm_provider_contato1 #contato1", async function() {

	ProviderFormCancel($(this).closest('form'), $(this), "provider_contato1_cancel");
});

/**
  * Saves provider contato1 edition
  */
 $(document).on("submit", "#frm_provider_contato1", async function(event) {

	event.preventDefault();

	FormDisable($(this));

	ProviderFormSave($(this), $(this), $(this.contato1), "provider_contato1_save");
});

/**
  * Opens provider telefone2 edition
  */
 $(document).on("click", ".provider_bt_telefone2", async function() {

	ProviderFormEdit($(this).closest('.container'), $(this), "provider_telefone2_edit");
});

/**
  * Cancels provider telefone2 edition
  */
 $(document).on("focusout", "#frm_provider_telefone2 #telefone2", async function() {

	ProviderFormCancel($(this).closest('form'), $(this), "provider_telefone2_cancel");
});

/**
  * Saves provider telefone2 edition
  */
 $(document).on("submit", "#frm_provider_telefone2", async function(event) {

	event.preventDefault();

	FormDisable($(this));

	ProviderFormSave($(this), $(this), $(this.telefone2), "provider_telefone2_save");
});

/**
  * Opens provider contato2 edition
  */
 $(document).on("click", ".provider_bt_contato2", async function() {

	ProviderFormEdit($(this).closest('.container'), $(this), "provider_contato2_edit");
});

/**
  * Cancels provider contato2 edition
  */
 $(document).on("focusout", "#frm_provider_contato2 #contato2", async function() {

	ProviderFormCancel($(this).closest('form'), $(this), "provider_contato2_cancel");
});

/**
  * Saves provider contato2 edition
  */
 $(document).on("submit", "#frm_provider_contato2", async function(event) {

	event.preventDefault();

	FormDisable($(this));

	ProviderFormSave($(this), $(this), $(this.contato2), "provider_contato2_save");
});

/**
  * Opens provider telefone3 edition
  */
 $(document).on("click", ".provider_bt_telefone3", async function() {

	ProviderFormEdit($(this).closest('.container'), $(this), "provider_telefone3_edit");
});

/**
  * Cancels provider telefone3 edition
  */
 $(document).on("focusout", "#frm_provider_telefone3 #telefone3", async function() {

	ProviderFormCancel($(this).closest('form'), $(this), "provider_telefone3_cancel");
});

/**
  * Saves provider telefone3 edition
  */
 $(document).on("submit", "#frm_provider_telefone3", async function(event) {

	event.preventDefault();

	FormDisable($(this));

	ProviderFormSave($(this), $(this), $(this.telefone3), "provider_telefone3_save");
});

/**
  * Opens provider contato3 edition
  */
 $(document).on("click", ".provider_bt_contato3", async function() {

	ProviderFormEdit($(this).closest('.container'), $(this), "provider_contato3_edit");
});

/**
  * Cancels provider contato3 edition
  */
 $(document).on("focusout", "#frm_provider_contato3 #contato3", async function() {

	ProviderFormCancel($(this).closest('form'), $(this), "provider_contato3_cancel");
});

/**
  * Saves provider contato3 edition
  */
 $(document).on("submit", "#frm_provider_contato3", async function(event) {

	event.preventDefault();

	FormDisable($(this));

	ProviderFormSave($(this), $(this), $(this.contato3), "provider_contato3_save");
});