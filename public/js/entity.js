class Entity {

	constructor() {

		this.searching = false;
		this.timeout = 0;
		this.queue = [];
	}

	async New() {

		// if($('.entity_window').length == 0) {

		// 	await LoadPage("entity.php");
		// }

		let data = {
			action: 'entity_new'
		}

		let response = await Post("entity.php", data);

		if (response != null) {

			Modal.Show(Modal.POPUP_SIZE_LARGE, "Novo Cliente", response["data"], null);

			return response["id_entidade"];
		}

		return null;
	}

	Search(field) {

		if (this.timeout > 0) {

			clearTimeout(this.timeout);
		}

		this.timeout = setTimeout(function() {

			entity.AutoSearch(field, field.val(), entity.timeout);

		}, 600);
	}

	/**
	 * Autosearch for entitys
	 * @param {*} field
	 * @returns
	 */
	async AutoSearch(field, value, handle) {

		let dropdownlist = "";

		let source = field.data('source');

		if (field.data("last_search") == value) {

			this.searching = false;
			return;
		}

		field.data("last_search", value);

		switch(source) {

			case "popup":

				dropdownlist = field.closest('.autocomplete-dropdown').find('.dropdown-list');
				dropdownlist.html(imgLoading);

				if (field.is(':focus')) {
					dropdownlist.show();
				}

				break;

			case "entity":

				$('.entity_not_found').addClass('hidden');
				dropdownlist = $('.entity_table');
				dropdownlist.html(imgLoading);

				break;

			default:

				Message.Show("Container não localizado para procura de cliente!", Message.MSG_ERROR);
				this.searching = false;
				return;
		}

		let data = {
			action: 'entity_smart_search',
			value: value,
			source: source
		}

		response = await Post("entity.php", data);

		if (response != null) {

			// Consulta descartada
			if (handle != this.timeout) {
				return;
			}

			dropdownlist.html(response);

			if (response.length == 0) {

				switch(source) {

					case "popup":

						dropdownlist[0].scrollIntoView(false);
					break;

					case "entity":

						if (value != "") {

							Message.Show("Termo não encontrado:<br>" + value, Message.MSG_INFO);
						}

						$('.entity_not_found').removeClass('hidden');
					break;
				}
			}

		} else {

			dropdownlist.html("");
		}

		this.searching = false;
	}
}

let entity = new Entity();

/**
  * Loads collaborator/entity by name for autocomplete.
  */
 $(document).on("keyup", ".entity_search", async function(event) {

	switch (event.keyCode) {

		case 38: // up
		case 40: // down
			return;

		break;
	}

	let field = $(this);

	entity.Search(field);
});

/**
  * Gets People list.
  */
 $(document).on("click", ".entity_bt_listall", async function(event) {

	var button = $(this);

	var container = $(".entity_table");

	button.addClass('disabled');

	$('.entity_not_found').addClass('hidden');
	container.html(imgLoading);

	var data = {
		action: "entity_listall"
	}

	var response = await Post("entity.php", data);

	if (response != null) {

		if (response.length == 0) {

			$('.entity_not_found').removeClass('hidden');

		} else {

			container.html(response);
		}
	}

	button.removeClass('disabled');

	MenuClose();
});

/**
  * Add new Entity.
  */
$(document).on("click", ".entity_bt_new", async function() {

	let button = $(this);

	let window = button.data("window");

	let data;
	let response;

	Disable(button);

	let id_entidade = await entity.New();

	if (id_entidade != null) {

		switch(window) {

			case "entity":

				data = {
					action: 'entity_smart_search',
					value: id_entidade,
					source: "entity"
				}

				response = await Post("entity.php", data);

				if (response != null) {

					$(".entity_not_found").addClass("hidden");
					let content = $(response);
					$('.entity_table').append(content);
					AutoFocus(content);
				}

			break;

			case "waiter_order_revision":
			case "waiter_order_products":

				data = {
					action: 'waitertable_entity_search',
					id_mesa: waiter.selected_table,
					entidade: id_entidade,
					window: window,
					versao: waiter.getVersao()
				}

				if (window == "waiter_order_revision") {

					data["products"] = waiter.getProductsID(waiter.selected_table);
				}

				response = await Post("waiter.php", data);

				if (response != null) {

					waiter.setVersao(response["versao"]);

					$("#body-container").html(response["data"]);

					if (window == "waiter_order_revision") {

						WaiterProductUpdate();
					}
				}

			break;

			case "collaborator":

				data = {
					action: 'collaborator_add',
					value: id_entidade,
				}

				response = await Post("collaborator.php", data);

				if (response != null) {

					let container = $('.collaborator_container');

					let content = $(response);

					container.append(content);

					ContainerFocus(content, true);
				}

			break;
		}
	}

	MenuClose();

	Enable(button);
});

/**
  * Toggle active and not active to entity for PDV
  */
 $(document).on("click", ".entity_bt_status", async function() {

	var button = $(this);

	var id_entidade = button.data('id_entidade');

	Disable(button)

	data = {
		action: "entity_change_status",
		id_entidade: id_entidade,
	}

	response = await Post("entity.php", data);

	if (response != null) {

		$('.entity_' + id_entidade + '_status').replaceWith(response);

	} else {

		Enable(button);
	}
});

/**
  * Open entity nome edition
  */
$(document).on("click", ".entity_bt_nome", async function() {

	let button = $(this);

	let id_entidade = button.data('id_entidade');

	Disable(button);

	data = {
		action: "entity_nome_edit",
		id_entidade: id_entidade,
	}

	response = await Post("entity.php", data);

	if (response != null) {

		let content = $(response);

		button.replaceWith(content);

		AutoFocus(content);

	} else {

		Enable(button);
	}
});

/**
  * Cancels entity nome edition
  */
$(document).on("focusout", "#frm_entity_nome #nome", async function() {

	var field = $(this);

	//Prevents focusout on save
 	if (field.hasClass('disabled_focusout')) {
 		return;
 	}

	field.addClass('loading');

	var container = field.closest('form');

	var id_entidade = container.data('id_entidade');

	data = {
		action: 'entity_nome_cancel',
		id_entidade: id_entidade,
	}

	var response = await Post("entity.php", data);

	if (response != null) {

		container.replaceWith(response);

	} else {

		field.removeClass('loading');
	}
});

/**
  * Creates new entity
  */
$(document).on("submit", "#frm_entity_new", async function(event) {

	event.preventDefault();

	let form = $(this);
	let nome = this.nome.value;
	let cpfcnpj = this.cpfcnpj.value;
	let email = this.email.value;
	let telcelular = this.telcelular.value;
	let telresidencial = this.telresidencial.value;
	let telcomercial = this.telcomercial.value;
	let obs = this.obs.value;
	let window = form.data('window');

	FormDisable(form);

	let data = {
		action: "entity_create_new",
		nome: nome,
		cpfcnpj: cpfcnpj,
		email: email,
		telcelular: telcelular,
		telresidencial: telresidencial,
		telcomercial: telcomercial,
		obs: obs,
	}

	let response = await Post("entity.php", data);

	if (response != null) {

		let id_entidade = response;

		switch (window) {

			case "waiter_order_products":
			case "waiter_order_revision":

				data = {
					action: 'waitertable_entity_search',
					id_mesa: waiter.selected_table,
					entidade: id_entidade,
					window: window,
				}

				response = await Post("waiter.php", data);

				if (response != null) {

					$("#body-container").html(response);

					if (window == "waiter_order_products") {

						$('.waiter-display').html(waiter.getTableDescription(waiter.selected_table));

					} else if (window == "waiter_order_revision") {

						$('.waiter-display').html("Confirmar Pedido - " + waiter.getTableDescription(waiter.selected_table));

						WaiterProductUpdate();
					}
				}

			break;
		}

	}

	FormEnable(form);
});

/**
  * Saves Entity nome edition
  */
$(document).on("submit", "#frm_entity_nome", async function(event) {

	event.preventDefault();

	let form = $(this);
	let field = $(this.nome);

	let id_entidade = form.data('id_entidade');
	let nome = this.nome.value;

	field.addClass('disabled_focusout');
	FormDisable(form);

	let data = {
		action: "entity_nome_save",
		id_entidade: id_entidade,
		nome: nome,
	}

	let response = await Post("entity.php", data);

	if (response != null) {

		$('.entity_' + id_entidade + '_nome').html(response['nome']);
		$('.entity_' + id_entidade + '_nick').html(response['nick']);

		form.replaceWith(response['data']);

	} else {

		FormEnable(form);
		field.removeClass('disabled_focusout');
	}
});

/**
 * Deletes entity address
 */
$(document).on("click", ".bt_entity_address_delete", async function() {

	var button = $(this);

	var container = button.closest('.tr');

	var id_endereco = button.data("id_endereco");

	// Disable(button);
	Disable(button)

	let yes = async function() {

		var data = {
			action: "entity_address_delete",
			id_address: id_endereco,
		}

		response = await Post("entity.php", data);

		if (response != null) {

			ContainerRemove(container);

		} else {

			Enable(button);
			// Enable(button);
		}

		return true;
	}

	let no = function() {

		Enable(button);
		// Enable(button);
	}

	MessageBox.Show(container.find('.entityaddress_bt_logradouro').html() + "<br>Excluir Endereço?", yes, no);
});

/**
 * Create new entity address
 */
$(document).on("click", ".bt_entity_address_new", async function() {

	let button = $(this);

	let id_entidade = button.data('id_entidade');
	let page = button.data('page');

	let container = button.closest(".w-entityaddress").find('.tbody');

	Disable(button);

	let data = {
		action: "entity_address_new",
		id_entidade: id_entidade,
		page: page
	}

	let response = await Post("entity.php", data);

	if (response != null) {

		let content = $(response);
		container.append(content);

		ContainerFocus(content, true);
	}

	Enable(button);
});

/**
  * Event to new sale order from entity
  */
$(document).on("click", ".entity_bt_new_saleorder", async function(event) {

	let button = $(this);

	let id_entidade = button.data('id_entidade');
	let id_endereco = button.data('id_endereco');

	Disable(button);

	let data = {
		action: 'sale_order_new',
		cliente: id_entidade,
		from: 'entity',
		id_endereco: 0
	}

	if (id_endereco > 0) {

		data['id_endereco'] = id_endereco;
	}

	let response = await Post("sale_order.php", data);

	if (response != null) {

		Modal.CloseAll();

		await LoadPage("sale_order.php");

		$('.saleorder_header').html("Pedido");

		let content = $(response['saleorder']);

		$('.saleorder_notfound').addClass('hidden');
		$(".w_saleorder_container .tbody").html(content);
		$('.w_saleorder_container').data("window", "saleorder_show");

		SaleOrderHUD(response['total_andamento'], response['total_efetuado'], response['total_producao'], response['total_entrega']);

		// ContainerFocus(content, true);
		AutoFocus(content);

	} else {

		Enable(button);
	}
});

/**
  * Event to edit entity credit
  */
$(document).on("click", ".entity_bt_credito", async function() {

	let button = $(this);
	let id_entidade = button.data("id_entidade");

	Disable(button);

	data = {
		action: 'entity_credit_edit',
		id_entidade: id_entidade
	}

	let response = await Post("entity.php", data);

	Enable(button);

	if (response != null) {

		Modal.Show(Modal.POPUP_SIZE_SMALL, "Crédito do Cliente", response, null);
	}
});

/**
  * Adds credit to entity.
  */
 $(document).on("click", ".entity_bt_addcredit", async function() {

	let button = $(this);

	let container = $(this).closest(".w_entitycredit");

	Disable(button);

	let id_entidade = container.data('id_entidade');
	let credito = parseFloat("0" + container.find("#credito").val());
	let tipo = "add";
	let obs = container.find("#obs").val().trim();

	if (credito == 0) {

		Message.Show("Digite um valor de crédito diferente de zero!", Message.MSG_ERROR);
		Enable(button);
		container.find("#credito").focus();
		return;
	}

	if (obs == '') {

		Message.Show("Digite uma observação!", Message.MSG_ERROR);
		Enable(button);
		container.find("#obs").focus();
		return;
	}

	let data = {
		action: "entity_credit_save",
		id_entidade: id_entidade,
		credito: credito,
		tipo: tipo,
		obs: obs
	}

	let success = async function(response) {

		$('.entitycredit_' + id_entidade).replaceWith(response);

		Modal.Close(button.closest('.popup'));
	}

	let error = async function() {

		Enable(button);
	}

	let cancel = async function() {

		Enable(button);
	}

	Authenticator.Authenticate(data, "entity.php", success, error, cancel);

	// let response = await Post("entity.php", data);

	// if (response != null) {

	// 	$('.entitycredit_' + id_entidade).replaceWith(response);

	// 	Modal.Close($(this).closest('.popup'));

	// } else {

	// 	Enable(button);
	// }
});

/**
  * Removes credit to entity.
  */
$(document).on("click", ".entity_bt_removecredit", async function() {

	let button = $(this);

	let container = $(this).closest(".w_entitycredit");

	Disable(button);

	let id_entidade = container.data('id_entidade');
	let credito = parseFloat("0" + container.find("#credito").val());
	let tipo = "remove";
	let obs = container.find("#obs").val().trim();

	if (credito == 0) {

		Message.Show("Digite um valor de crédito diferente de zero!", Message.MSG_ERROR);
		Enable(button);
		container.find("#credito").focus();
		return;
	}

	if (obs == '') {

		Message.Show("Digite uma observação!", Message.MSG_ERROR);
		Enable(button);
		container.find("#obs").focus();
		return;
	}

	let data = {
		action: "entity_credit_save",
		id_entidade: id_entidade,
		credito: credito,
		tipo: tipo,
		obs: obs
	}

	let success = async function(response) {

		$('.entitycredit_' + id_entidade).replaceWith(response);

		Modal.Close(button.closest('.popup'));
	}

	let error = async function() {

		Enable(button);
	}

	let cancel = async function() {

		Enable(button);
	}

	Authenticator.Authenticate(data, "entity.php", success, error, cancel);

	// let response = await Post("entity.php", data);

	// if (response != null) {

	// 	$('.entitycredit_' + id_entidade).replaceWith(response);

	// 	Modal.Close($(this).closest('.popup'));

	// } else {

	// 	Enable(button);
	// }
});

/**
  * Event to close entity credit edition
  */
//  $(document).on("click", ".entity_bt_credito_close", async function() {

// 	var button = $(this);

// 	var container = $(this).closest(".w-entitycredit");

// 	var id_entidade = container.data('id_entidade');

// 	Disable(button);

// 	data = {
// 		action: 'entity_credit_edit_cancel',
// 		id_entidade: id_entidade
// 	}

// 	var response = await Post("entity.php", data);

// 	if (response != null) {

// 		container.html(response);

// 	} else {

// 		Enable(button);
// 	}
// });

/**
  * Opens entity cpfcnpj edition
  */
 $(document).on("click", ".entity_bt_cpfcnpj", async function() {

	var button = $(this);

	var id_entidade = button.data("id_entidade");

	Disable(button);

	data = {
		action: 'entity_cpfcnpj_edit',
		id_entidade: id_entidade
	}

	var response = await Post("entity.php", data);

	if (response != null) {

		var content = $(response);
		button.replaceWith(content);

		AutoFocus(content);

	} else {

		Enable(button);
	}
});

/**
  * Cancels entity cpfcnpj edition
  */
 $(document).on("focusout", "#frm_entity_cpfcnpj #cpfcnpj", async function() {

	var field = $(this);

	//Prevents focusout on save
 	if (field.hasClass('disabled_focusout')) {
 		return;
 	}

	field.addClass('loading');

	var container = field.closest('form');

	var id_entidade = container.data('id_entidade');

	data = {
		action: 'entity_cpfcnpj_cancel',
		id_entidade: id_entidade,
	}

	var response = await Post("entity.php", data);

	if (response != null) {

		container.replaceWith(response);

	} else {

		field.removeClass('loading');
	}
});

/**
  * Saves entity cpfcnpj edition
  */
 $(document).on("submit", "#frm_entity_cpfcnpj", async function(event) {

	event.preventDefault();

	var form = $(this);
	var field = $(this.cpfcnpj);

	var id_entidade = form.data('id_entidade');
	var cpfcnpj = this.cpfcnpj.value;

	field.addClass('disabled_focusout');
	FormDisable(form);

	data = {
		action: "entity_cpfcnpj_save",
		id_entidade: id_entidade,
		cpfcnpj: cpfcnpj,
	}

	response = await Post("entity.php", data);

	if (response != null) {

		form.replaceWith(response);

	} else {

		FormEnable(form);
		field.removeClass('disabled_focusout').focus();
	}
});

/**
  * Opens entity limite edition
  */
 $(document).on("click", ".entity_bt_limite", async function() {

	var button = $(this);

	var id_entidade = button.data("id_entidade");

	Disable(button);

	data = {
		action: 'entity_limite_edit',
		id_entidade: id_entidade
	}

	var response = await Post("entity.php", data);

	if (response != null) {

		var content = $(response);
		button.replaceWith(content);

		AutoFocus(content);

	} else {

		Enable(button);
	}
});

/**
  * Cancels entity limite edition
  */
 $(document).on("focusout", "#frm_entity_limite #limite", async function() {

	var field = $(this);

	//Prevents focusout on save
 	if (field.hasClass('disabled_focusout')) {
 		return;
 	}

	field.addClass('loading');

	var container = field.closest('form');

	var id_entidade = container.data('id_entidade');

	data = {
		action: 'entity_limite_cancel',
		id_entidade: id_entidade,
	}

	var response = await Post("entity.php", data);

	if (response != null) {

		container.replaceWith(response);

	} else {

		field.removeClass('loading');
	}
});

/**
  * Saves entity limite edition
  */
 $(document).on("submit", "#frm_entity_limite", async function(event) {

	event.preventDefault();

	var form = $(this);
	var field = $(this.limite);

	var id_entidade = form.data('id_entidade');
	var limite = this.limite.value;

	field.addClass('disabled_focusout');
	FormDisable(form);

	data = {
		action: "entity_limite_save",
		id_entidade: id_entidade,
		limite: limite,
	}

	response = await Post("entity.php", data);

	if (response != null) {

		form.replaceWith(response);

	} else {

		FormEnable(form);
		field.removeClass('disabled_focusout').focus();
	}
});

/**
  * Opens entity email edition
  */
 $(document).on("click", ".entity_bt_email", async function() {

	var button = $(this);

	var id_entidade = button.data("id_entidade");

	Disable(button);

	data = {
		action: 'entity_email_edit',
		id_entidade: id_entidade
	}

	var response = await Post("entity.php", data);

	if (response != null) {

		var content = $(response);
		button.replaceWith(content);

		AutoFocus(content);

	} else {

		Enable(button);
	}
});

/**
  * Cancels entity email edition
  */
 $(document).on("focusout", "#frm_entity_email #email", async function() {

	var field = $(this);

	//Prevents focusout on save
 	if (field.hasClass('disabled_focusout')) {
 		return;
 	}

	field.addClass('loading');

	var container = field.closest('form');

	var id_entidade = container.data('id_entidade');

	data = {
		action: 'entity_email_cancel',
		id_entidade: id_entidade,
	}

	var response = await Post("entity.php", data);

	if (response != null) {

		container.replaceWith(response);

	} else {

		field.removeClass('loading');
	}
});

/**
  * Saves entity email edition
  */
 $(document).on("submit", "#frm_entity_email", async function(event) {

	event.preventDefault();

	var form = $(this);
	var field = $(this.email);

	var id_entidade = form.data('id_entidade');
	var email = this.email.value;

	field.addClass('disabled_focusout');
	FormDisable(form);

	data = {
		action: "entity_email_save",
		id_entidade: id_entidade,
		email: email,
	}

	response = await Post("entity.php", data);

	if (response != null) {

		form.replaceWith(response);

	} else {

		FormEnable(form);
		field.removeClass('disabled_focusout').focus();
	}
});

/**
  * Opens entity telcelular edition
  */
 $(document).on("click", ".entity_bt_telcelular", async function() {

	var button = $(this);

	var id_entidade = button.data("id_entidade");

	Disable(button);

	data = {
		action: 'entity_telcelular_edit',
		id_entidade: id_entidade
	}

	var response = await Post("entity.php", data);

	if (response != null) {

		var content = $(response);
		button.replaceWith(content);

		AutoFocus(content);

	} else {

		Enable(button);
	}
});

/**
  * Cancels entity telcelular edition
  */
 $(document).on("focusout", "#frm_entity_telcelular #telcelular", async function() {

	var field = $(this);

	//Prevents focusout on save
 	if (field.hasClass('disabled_focusout')) {
 		return;
 	}

	field.addClass('loading');

	var container = field.closest('form');

	var id_entidade = container.data('id_entidade');

	data = {
		action: 'entity_telcelular_cancel',
		id_entidade: id_entidade,
	}

	var response = await Post("entity.php", data);

	if (response != null) {

		container.replaceWith(response);

	} else {

		field.removeClass('loading');
	}
});

/**
  * Saves entity telcelular edition
  */
 $(document).on("submit", "#frm_entity_telcelular", async function(event) {

	event.preventDefault();

	var form = $(this);
	var field = $(this.telcelular);

	var id_entidade = form.data('id_entidade');
	var telcelular = this.telcelular.value;

	field.addClass('disabled_focusout');
	FormDisable(form);

	data = {
		action: "entity_telcelular_save",
		id_entidade: id_entidade,
		telcelular: telcelular,
	}

	response = await Post("entity.php", data);

	if (response != null) {

		form.replaceWith(response);

	} else {

		FormEnable(form);
		field.removeClass('disabled_focusout').focus();
	}
});

/**
  * Opens entity telresidencial edition
  */
 $(document).on("click", ".entity_bt_telresidencial", async function() {

	var button = $(this);

	var id_entidade = button.data("id_entidade");

	Disable(button);

	data = {
		action: 'entity_telresidencial_edit',
		id_entidade: id_entidade
	}

	var response = await Post("entity.php", data);

	if (response != null) {

		var content = $(response);
		button.replaceWith(content);

		AutoFocus(content);

	} else {

		Enable(button);
	}
});

/**
  * Cancels entity telresidencial edition
  */
 $(document).on("focusout", "#frm_entity_telresidencial #telresidencial", async function() {

	var field = $(this);

	//Prevents focusout on save
 	if (field.hasClass('disabled_focusout')) {
 		return;
 	}

	field.addClass('loading');

	var container = field.closest('form');

	var id_entidade = container.data('id_entidade');

	data = {
		action: 'entity_telresidencial_cancel',
		id_entidade: id_entidade,
	}

	var response = await Post("entity.php", data);

	if (response != null) {

		container.replaceWith(response);

	} else {

		field.removeClass('loading');
	}
});

/**
  * Saves entity telresidencial edition
  */
 $(document).on("submit", "#frm_entity_telresidencial", async function(event) {

	event.preventDefault();

	var form = $(this);
	var field = $(this.telresidencial);

	var id_entidade = form.data('id_entidade');
	var telresidencial = this.telresidencial.value;

	field.addClass('disabled_focusout');
	FormDisable(form);

	data = {
		action: "entity_telresidencial_save",
		id_entidade: id_entidade,
		telresidencial: telresidencial,
	}

	response = await Post("entity.php", data);

	if (response != null) {

		form.replaceWith(response);

	} else {

		FormEnable(form);
		field.removeClass('disabled_focusout').focus();
	}
});

/**
  * Opens entity telcomercial edition
  */
 $(document).on("click", ".entity_bt_telcomercial", async function() {

	var button = $(this);

	var id_entidade = button.data("id_entidade");

	Disable(button);

	data = {
		action: 'entity_telcomercial_edit',
		id_entidade: id_entidade
	}

	var response = await Post("entity.php", data);

	if (response != null) {

		var content = $(response);
		button.replaceWith(content);

		AutoFocus(content);

	} else {

		Enable(button);
	}
});

/**
  * Cancels entity telcomercial edition
  */
 $(document).on("focusout", "#frm_entity_telcomercial #telcomercial", async function() {

	var field = $(this);

	//Prevents focusout on save
 	if (field.hasClass('disabled_focusout')) {
 		return;
 	}

	field.addClass('loading');

	var container = field.closest('form');

	var id_entidade = container.data('id_entidade');

	data = {
		action: 'entity_telcomercial_cancel',
		id_entidade: id_entidade,
	}

	var response = await Post("entity.php", data);

	if (response != null) {

		container.replaceWith(response);

	} else {

		field.removeClass('loading');
	}
});

/**
  * Saves entity telcomercial edition
  */
 $(document).on("submit", "#frm_entity_telcomercial", async function(event) {

	event.preventDefault();

	var form = $(this);
	var field = $(this.telcomercial);

	var id_entidade = form.data('id_entidade');
	var telcomercial = this.telcomercial.value;

	field.addClass('disabled_focusout');
	FormDisable(form);

	data = {
		action: "entity_telcomercial_save",
		id_entidade: id_entidade,
		telcomercial: telcomercial,
	}

	response = await Post("entity.php", data);

	if (response != null) {

		form.replaceWith(response);

	} else {

		FormEnable(form);
		field.removeClass('disabled_focusout').focus();
	}
});

/**
  * Opens entity obs edition
  */
 $(document).on("click", ".entity_bt_obs", async function() {

	var button = $(this);

	var id_entidade = button.data("id_entidade");

	Disable(button);

	data = {
		action: 'entity_obs_edit',
		id_entidade: id_entidade
	}

	var response = await Post("entity.php", data);

	if (response != null) {

		var content = $(response);
		button.replaceWith(content);

		AutoFocus(content);

	} else {

		Enable(button);
	}
});

/**
  * Cancels entity obs edition
  */
 $(document).on("focusout", "#frm_entity_obs #obs", async function() {

	var field = $(this);

	//Prevents focusout on save
 	if (field.hasClass('disabled_focusout')) {
 		return;
 	}

	field.addClass('loading');

	var container = field.closest('form');

	var id_entidade = container.data('id_entidade');

	data = {
		action: 'entity_obs_cancel',
		id_entidade: id_entidade,
	}

	var response = await Post("entity.php", data);

	if (response != null) {

		container.replaceWith(response);

	} else {

		field.removeClass('loading');
	}
});

/**
  * Saves entity obs edition
  */
 $(document).on("submit", "#frm_entity_obs", async function(event) {

	event.preventDefault();

	var form = $(this);
	var field = $(this.obs);

	var id_entidade = form.data('id_entidade');
	var obs = this.obs.value;

	field.addClass('disabled_focusout');
	FormDisable(form);

	data = {
		action: "entity_obs_save",
		id_entidade: id_entidade,
		obs: obs,
	}

	response = await Post("entity.php", data);

	if (response != null) {

		form.replaceWith(response);

	} else {

		FormEnable(form);
		field.removeClass('disabled_focusout').focus();
	}
});

/**
  * Opens entityaddress nickname edition
  */
 $(document).on("click", ".entityaddress_bt_nickname", async function() {

	var button = $(this);

	var id_endereco = button.data("id_endereco");

	Disable(button);

	data = {
		action: 'entityaddress_nickname_edit',
		id_endereco: id_endereco
	}

	var response = await Post("entity.php", data);

	if (response != null) {

		var content = $(response);
		button.replaceWith(content);

		AutoFocus(content);

	} else {

		Enable(button);
	}
});

/**
  * Cancels entityaddress nickname edition
  */
 $(document).on("focusout", "#frm_entityaddress_nickname #nickname", async function() {

	var field = $(this);

	//Prevents focusout on save
 	if (field.hasClass('disabled_focusout')) {
 		return;
 	}

	field.addClass('loading');

	var container = field.closest('form');

	var id_endereco = container.data('id_endereco');

	data = {
		action: 'entityaddress_nickname_cancel',
		id_endereco: id_endereco,
	}

	var response = await Post("entity.php", data);

	if (response != null) {

		container.replaceWith(response);

	} else {

		field.removeClass('loading');
	}
});

/**
  * Saves entityaddress nickname edition
  */
 $(document).on("submit", "#frm_entityaddress_nickname", async function(event) {

	event.preventDefault();

	var form = $(this);
	var field = $(this.nickname);

	var id_endereco = form.data('id_endereco');
	var nickname = this.nickname.value;

	field.addClass('disabled_focusout');
	FormDisable(form);

	data = {
		action: "entityaddress_nickname_save",
		id_endereco: id_endereco,
		nickname: nickname,
	}

	response = await Post("entity.php", data);

	if (response != null) {

		form.replaceWith(response);

	} else {

		FormEnable(form);
		field.removeClass('disabled_focusout').focus();
	}
});

/**
  * Opens entityaddress cep edition
  */
 $(document).on("click", ".entityaddress_bt_cep", async function() {

	var button = $(this);

	var id_endereco = button.data("id_endereco");

	Disable(button);

	data = {
		action: 'entityaddress_cep_edit',
		id_endereco: id_endereco
	}

	var response = await Post("entity.php", data);

	if (response != null) {

		var content = $(response);
		button.replaceWith(content);

		AutoFocus(content);

	} else {

		Enable(button);
	}
});

/**
  * Cancels entityaddress cep edition
  */
 $(document).on("focusout", "#frm_entityaddress_cep #cep", async function() {

	let field = $(this);

	//Prevents focusout on save
 	if (field.prop("disabled")) {
 		return;
 	}

	let form = field.closest("form");

	FormDisable(form);

	let container = field.closest('form');

	let id_endereco = container.data('id_endereco');

	let data = {
		action: 'entityaddress_cep_cancel',
		id_endereco: id_endereco,
	}

	let response = await Post("entity.php", data);

	if (response != null) {

		container.replaceWith(response);

	} else {

		FormEnable(form);
	}
});

/**
  * Saves entityaddress cep edition
  */
 $(document).on("submit", "#frm_entityaddress_cep", async function(event) {

	event.preventDefault();

	let form = $(this);

	let id_endereco = form.data('id_endereco');
	let cep = this.cep.value;

	FormDisable(form);

	let data = {
		action: "entityaddress_cep_save",
		id_endereco: id_endereco,
		cep: cep
	}

	let response = await Post("entity.php", data);

	if (response != null) {

		let data_cep = await CEPSearch(cep);

		if (data_cep != null) {

			let yes = async function() {

				let data = {
					action: "entityaddress_cep_address_save",
					id_endereco: id_endereco,
					saleorder: ($(".w_saleorder_address").length > 0),
					logradouro: data_cep["logradouro"],
					bairro: data_cep["bairro"],
					cidade: data_cep["localidade"],
					uf: data_cep["uf"],
				}

				let response2 = await Post("entity.php", data);

				if (response2 != null) {

					$(".entityaddress_tr_" + id_endereco).replaceWith(response2);
				}

				return true;
			}

			let no = async function() {

				form.replaceWith(response);
			}

			data = {
				action: "entityaddress_extra_block_cep_address_update",
				logradouro: data_cep["logradouro"],
				bairro: data_cep["bairro"],
				cidade: data_cep["localidade"],
				uf: data_cep["uf"],
			}

			let response3 = await Post("entity.php", data);

			if (response3 != null) {

				MessageBox.Show(response3, yes, no);
			}

		} else {

			form.replaceWith(response);
		}

	} else {

		FormEnable(form);
	}
});

/**
  * Opens entityaddress logradouro edition
  */
$(document).on("click", ".entityaddress_bt_logradouro", async function() {

	let button = $(this);

	let id_endereco = button.data("id_endereco");

	Disable(button);

	let data = {
		action: 'entityaddress_logradouro_edit',
		id_endereco: id_endereco
	}

	let response = await Post("entity.php", data);

	if (response != null) {

		let content = $(response);
		button.replaceWith(content);

		AutoFocus(content);

	} else {

		Enable(button);
	}
});

/**
  * Cancels entityaddress logradouro edition
  */
$(document).on("focusout", "#frm_entityaddress_logradouro #logradouro", async function() {

	var field = $(this);

	//Prevents focusout on save
 	if (field.hasClass('disabled_focusout')) {
 		return;
 	}

	field.addClass('loading');

	var container = field.closest('form');

	var id_endereco = container.data('id_endereco');

	data = {
		action: 'entityaddress_logradouro_cancel',
		id_endereco: id_endereco,
	}

	var response = await Post("entity.php", data);

	if (response != null) {

		container.replaceWith(response);

	} else {

		field.removeClass('loading');
	}
});

/**
  * Saves entityaddress logradouro edition
  */
$(document).on("submit", "#frm_entityaddress_logradouro", async function(event) {

	event.preventDefault();

	var form = $(this);
	var field = $(this.logradouro);

	var id_endereco = form.data('id_endereco');
	var logradouro = this.logradouro.value;

	field.addClass('disabled_focusout');
	FormDisable(form);

	data = {
		action: "entityaddress_logradouro_save",
		id_endereco: id_endereco,
		logradouro: logradouro,
	}

	response = await Post("entity.php", data);

	if (response != null) {

		form.replaceWith(response);

	} else {

		FormEnable(form);
		field.removeClass('disabled_focusout').focus();
	}
});

/**
  * Opens entityaddress numero edition
  */
$(document).on("click", ".entityaddress_bt_numero", async function() {

	let button = $(this);

	let id_endereco = button.data("id_endereco");

	Disable(button);

	let data = {
		action: 'entityaddress_numero_edit',
		id_endereco: id_endereco
	}

	let response = await Post("entity.php", data);

	if (response != null) {

		let content = $(response);
		button.replaceWith(content);

		AutoFocus(content);

	} else {

		Enable(button);
	}
});

/**
  * Cancels entityaddress numero edition
  */
$(document).on("focusout", "#frm_entityaddress_numero #numero", async function() {

	var field = $(this);

	//Prevents focusout on save
 	if (field.hasClass('disabled_focusout')) {
 		return;
 	}

	field.addClass('loading');

	var container = field.closest('form');

	var id_endereco = container.data('id_endereco');

	data = {
		action: 'entityaddress_numero_cancel',
		id_endereco: id_endereco,
	}

	var response = await Post("entity.php", data);

	if (response != null) {

		container.replaceWith(response);

	} else {

		field.removeClass('loading');
	}
});

/**
  * Saves entityaddress numero edition
  */
$(document).on("submit", "#frm_entityaddress_numero", async function(event) {

	event.preventDefault();

	let form = $(this);
	let field = $(this.numero);

	let id_endereco = form.data('id_endereco');
	let numero = this.numero.value;

	field.addClass('disabled_focusout');
	FormDisable(form);

	let data = {
		action: "entityaddress_numero_save",
		id_endereco: id_endereco,
		numero: numero,
	}

	let response = await Post("entity.php", data);

	if (response != null) {

		form.replaceWith(response);

	} else {

		FormEnable(form);
		field.removeClass('disabled_focusout').focus();
	}
});

/**
  * Opens entityaddress complemento edition
  */
$(document).on("click", ".entityaddress_bt_complemento", async function() {

	let button = $(this);

	let id_endereco = button.data("id_endereco");

	Disable(button);

	let data = {
		action: 'entityaddress_complemento_edit',
		id_endereco: id_endereco
	}

	let response = await Post("entity.php", data);

	if (response != null) {

		let content = $(response);
		button.replaceWith(content);

		AutoFocus(content);

	} else {

		Enable(button);
	}
});

/**
  * Cancels entityaddress complemento edition
  */
$(document).on("focusout", "#frm_entityaddress_complemento #complemento", async function() {

	var field = $(this);

	//Prevents focusout on save
 	if (field.hasClass('disabled_focusout')) {
 		return;
 	}

	field.addClass('loading');

	var container = field.closest('form');

	var id_endereco = container.data('id_endereco');

	data = {
		action: 'entityaddress_complemento_cancel',
		id_endereco: id_endereco,
	}

	var response = await Post("entity.php", data);

	if (response != null) {

		container.replaceWith(response);

	} else {

		field.removeClass('loading');
	}
});

/**
  * Saves entityaddress complemento edition
  */
$(document).on("submit", "#frm_entityaddress_complemento", async function(event) {

	event.preventDefault();

	var form = $(this);
	var field = $(this.complemento);

	var id_endereco = form.data('id_endereco');
	var complemento = this.complemento.value;

	field.addClass('disabled_focusout');
	FormDisable(form);

	data = {
		action: "entityaddress_complemento_save",
		id_endereco: id_endereco,
		complemento: complemento,
	}

	response = await Post("entity.php", data);

	if (response != null) {

		form.replaceWith(response);

	} else {

		FormEnable(form);
		field.removeClass('disabled_focusout').focus();
	}
});

/**
  * Opens entityaddress bairro edition
  */
 $(document).on("click", ".entityaddress_bt_bairro", async function() {

	var button = $(this);

	var id_endereco = button.data("id_endereco");

	Disable(button);

	data = {
		action: 'entityaddress_bairro_edit',
		id_endereco: id_endereco
	}

	var response = await Post("entity.php", data);

	if (response != null) {

		var content = $(response);
		button.replaceWith(content);

		AutoFocus(content);

	} else {

		Enable(button);
	}
});

/**
  * Cancels entityaddress bairro edition
  */
 $(document).on("focusout", "#frm_entityaddress_bairro #bairro", async function() {

	var field = $(this);

	//Prevents focusout on save
 	if (field.hasClass('disabled_focusout')) {
 		return;
 	}

	field.addClass('loading');

	var container = field.closest('form');

	var id_endereco = container.data('id_endereco');

	data = {
		action: 'entityaddress_bairro_cancel',
		id_endereco: id_endereco,
	}

	var response = await Post("entity.php", data);

	if (response != null) {

		container.replaceWith(response);

	} else {

		field.removeClass('loading');
	}
});

/**
  * Saves entityaddress bairro edition
  */
 $(document).on("submit", "#frm_entityaddress_bairro", async function(event) {

	event.preventDefault();

	var form = $(this);
	var field = $(this.bairro);

	var id_endereco = form.data('id_endereco');
	var bairro = this.bairro.value;

	field.addClass('disabled_focusout');
	FormDisable(form);

	data = {
		action: "entityaddress_bairro_save",
		id_endereco: id_endereco,
		bairro: bairro,
	}

	response = await Post("entity.php", data);

	if (response != null) {

		form.replaceWith(response);

	} else {

		FormEnable(form);
		field.removeClass('disabled_focusout').focus();
	}
});

/**
  * Opens entityaddress cidade edition
  */
 $(document).on("click", ".entityaddress_bt_cidade", async function() {

	var button = $(this);

	var id_endereco = button.data("id_endereco");

	Disable(button);

	data = {
		action: 'entityaddress_cidade_edit',
		id_endereco: id_endereco
	}

	var response = await Post("entity.php", data);

	if (response != null) {

		var content = $(response);
		button.replaceWith(content);

		AutoFocus(content);

	} else {

		Enable(button);
	}
});

/**
  * Cancels entityaddress cidade edition
  */
 $(document).on("focusout", "#frm_entityaddress_cidade #cidade", async function() {

	var field = $(this);

	//Prevents focusout on save
 	if (field.hasClass('disabled_focusout')) {
 		return;
 	}

	field.addClass('loading');

	var container = field.closest('form');

	var id_endereco = container.data('id_endereco');

	data = {
		action: 'entityaddress_cidade_cancel',
		id_endereco: id_endereco,
	}

	var response = await Post("entity.php", data);

	if (response != null) {

		container.replaceWith(response);

	} else {

		field.removeClass('loading');
	}
});

/**
  * Saves entityaddress cidade edition
  */
 $(document).on("submit", "#frm_entityaddress_cidade", async function(event) {

	event.preventDefault();

	var form = $(this);
	var field = $(this.cidade);

	var id_endereco = form.data('id_endereco');
	var cidade = this.cidade.value;

	field.addClass('disabled_focusout');
	FormDisable(form);

	data = {
		action: "entityaddress_cidade_save",
		id_endereco: id_endereco,
		cidade: cidade,
	}

	response = await Post("entity.php", data);

	if (response != null) {

		form.replaceWith(response);

	} else {

		FormEnable(form);
		field.removeClass('disabled_focusout').focus();
	}
});

/**
  * Opens entityaddress uf edition
  */
 $(document).on("click", ".entityaddress_bt_uf", async function() {

	var button = $(this);

	var id_endereco = button.data("id_endereco");

	Disable(button);

	data = {
		action: 'entityaddress_uf_edit',
		id_endereco: id_endereco
	}

	var response = await Post("entity.php", data);

	if (response != null) {

		var content = $(response);
		button.replaceWith(content);

		$("select", content).focus();

	} else {

		Enable(button);
	}
});

/**
  * Cancels entityaddress uf edition
  */
 $(document).on("focusout", "#frm_entityaddress_uf #uf", async function() {

	var field = $(this);

	//Prevents focusout on save
 	if (field.hasClass('disabled_focusout')) {
 		return;
 	}

	field.addClass('loading');

	var container = field.closest('form');

	var id_endereco = container.data('id_endereco');

	data = {
		action: 'entityaddress_uf_cancel',
		id_endereco: id_endereco,
	}

	var response = await Post("entity.php", data);

	if (response != null) {

		container.replaceWith(response);

	} else {

		field.removeClass('loading');
	}
});

/**
  * Saves entityaddress uf edition
  */
 $(document).on("change", "#frm_entityaddress_uf", async function() {

	var form = $(this);
	var field = form.find('#uf');

	var id_endereco = form.data('id_endereco');
	var uf = this.uf.value;

	form.submit(false);
	field.addClass("loading");
	field.addClass("disabled_focusout");

	data = {
		action: "entityaddress_uf_save",
		id_endereco: id_endereco,
		uf: uf,
	};

	response = await Post("entity.php", data);

	if (response != null) {

		form.replaceWith(response);

	} else {

		form.unbind("submit",false);
		field.removeClass("loading");
		field.removeClass("disabled_focusout");
	}
});

/**
  * Opens entityaddress obs edition
  */
 $(document).on("click", ".entityaddress_bt_obs", async function() {

	var button = $(this);

	var id_endereco = button.data("id_endereco");

	Disable(button);

	data = {
		action: 'entityaddress_obs_edit',
		id_endereco: id_endereco
	}

	var response = await Post("entity.php", data);

	if (response != null) {

		var content = $(response);
		button.replaceWith(content);

		AutoFocus(content);

	} else {

		Enable(button);
	}
});

/**
  * Cancels entityaddress obs edition
  */
 $(document).on("focusout", "#frm_entityaddress_obs #obs", async function() {

	var field = $(this);

	//Prevents focusout on save
 	if (field.hasClass('disabled_focusout')) {
 		return;
 	}

	field.addClass('loading');

	var container = field.closest('form');

	var id_endereco = container.data('id_endereco');

	data = {
		action: 'entityaddress_obs_cancel',
		id_endereco: id_endereco,
	}

	var response = await Post("entity.php", data);

	if (response != null) {

		container.replaceWith(response);

	} else {

		field.removeClass('loading');
	}
});

/**
  * Saves entityaddress obs edition
  */
 $(document).on("submit", "#frm_entityaddress_obs", async function(event) {

	event.preventDefault();

	var form = $(this);
	var field = $(this.obs);

	var id_endereco = form.data('id_endereco');
	var obs = this.obs.value;

	field.addClass('disabled_focusout');
	FormDisable(form);

	data = {
		action: "entityaddress_obs_save",
		id_endereco: id_endereco,
		obs: obs,
	}

	response = await Post("entity.php", data);

	if (response != null) {

		form.replaceWith(response);

	} else {

		FormEnable(form);
		field.removeClass('disabled_focusout').focus();
	}
});

/**
  * Open "datacad" edition
  */
 $(document).on("click", ".entity_bt_datacad", async function() {

	var div = $(this).closest("div");
	var id_entidade = $(this).closest('.window').data('id_entidade');

	div.html(imgLoading);

	var data = {
		action: 'entity_datacad_edit',
		id_entidade: id_entidade
	}

	var response = await Post("entity.php", data);

	if (response != null) {

		div.html(response);
		div.find("input").focus();
	}
});

/**
  * Cancels "datacad" edition.
  */
$(document).on("focusout", "#frm_entity_datacad #datacad", async function() {

	//Prevents focusout on save
 	if ($(this).hasClass('disabled_focusout')) {
 		return;
 	}

	var div = $(this).closest('div');

	var id_entidade = $(this).closest('form').data('id_entidade');

	var data = {
		action: 'entity_datacad_cancel',
		id_entidade: id_entidade,
	}

	div.html(imgLoading);

	var response = await Post("entity.php", data);

	if (response != null) {

		div.html(response);
	}
});

/**
  * Saves "datacad" edition.
  */
$(document).on("change", "#frm_entity_datacad #datacad", async function(event) {

	var div = $(this).closest('div');
	var id_entidade = $(this).closest("form").data("id_entidade");
	var datacad = this.value;

	$(this).addClass('disabled_focusout');

	div.html(imgLoading);

	var data = {
		action: 'entity_datacad_save',
		id_entidade: id_entidade,
		datacad: datacad,
	}

	var response = await Post("entity.php", data);

	if (response != null) {

		div.html(response);
	}
});

/**
  * Shows entity data popup
  */
$(document).on("click", ".entity_bt_show", async function() {

	let button = $(this);

	let id_entidade = button.data('id_entidade');

	if (id_entidade === "") {

		Message.Show("Não há dados de cliente para venda varejo!", Message.MSG_INFO);
		return;
	}

	Disable(button);
	MenuClose();

	let data = {
		action: 'entity_show',
		id_entidade: id_entidade
	}

	let response = await Post("entity.php", data);

	if (response != null) {

		Modal.Show(Modal.POPUP_SIZE_LARGE, "Dados do Cliente", response, null);
	}

	Enable(button);
});

/**
 * Shows CEPs search by logradouro.
 */
$(document).on("click", ".entity_bt_cepsearch", async function() {

	let button = $(this);

	Disable(button);

	let data = {
		action: "entity_cepsearch_show",
		id_endereco: button.data("id_endereco")
	}

	let response = await Post("entity.php", data);

	if (response != null) {

		Modal.Show(Modal.POPUP_SIZE_SMALL, "Busca CEP por endereço", response, null, false, "<i class='icon fa-solid fa-search'></i>");
	}

	Enable(button);
});

/**
 * Shows CEP/logradouro search to calculate freight.
 */
$(document).on("click", ".entity_bt_cepsearchfreight", async function() {

	let button = $(this);

	Disable(button);

	let data = {
		action: "entity_cepsearchfreight_show"
	}

	let response = await Post("entity.php", data);

	if (response != null) {

		Modal.Show(Modal.POPUP_SIZE_SMALL, "Consulta valor de frete", response, null, false, "<i class='icon fa-solid fa-magnifying-glass-location'></i>");
	}

	Enable(button);
});

/**
  * Search CEP by address
  */
$(document).on("submit", "#frm_entity_cepsearch", async function(event) {

	event.preventDefault();

	let form = $(this);

	let id_endereco = form.data("id_endereco");
	let logradouro = this.frm_entity_cepsearch_logradouro.value;
	let cidade = this.frm_entity_cepsearch_cidade.value;
	let uf = this.frm_entity_cepsearch_uf.value;

	FormDisable(form);

	$(".entity_cepsearch_container").html("");

	let response = await CEPSearchAddress(logradouro, cidade, uf);

	if (response != null) {

		let data = {
			action: "entity_cepsearch",
			id_endereco: id_endereco,
			data: response
		};

		response = await Post("entity.php", data);

		if (response != null) {

			$(".entity_cepsearch_container").html(response);
		}
	}

	FormEnable(form);
});

/**
  * Search CEP/address to calculate freight
  */
$(document).on("submit", "#frm_entity_cepsearchfreight", async function(event) {

	event.preventDefault();

	let form = $(this);

	let logradouro = this.frm_entity_cepsearchfreight_logradouro.value;
	let cidade = this.frm_entity_cepsearchfreight_cidade.value;
	let uf = this.frm_entity_cepsearchfreight_uf.value;

	FormDisable(form);

	$(".entity_cepsearchfreight_container").html("");

	let cep_pattern = /[0-9]{5}\-?[0-9]{3}/;

	let response = null;

	if (cep_pattern.test(logradouro)) {

		response = [await CEPSearch(logradouro.replace("-", ""))];

	} else {

		response = await CEPSearchAddress(logradouro, cidade, uf);
	}

	if (response != null) {

		let data = {
			action: "entity_cepsearchfreight",
			data: response
		};

		response = await Post("entity.php", data);

		if (response != null) {

			$(".entity_cepsearchfreight_container").html(response);
		}
	}

	FormEnable(form);
});

/**
 * Saves address from CEPs search by logradouro.
 */
$(document).on("click", ".entity_bt_cepselect", async function() {

	let button = $(this);

	let id_endereco = button.data("id_endereco");

	Disable(button);

	let data = {
		action: "entity_cepsearch_select",
		id_endereco: id_endereco,
		logradouro: button.data("logradouro"),
		cep: button.data("cep"),
		bairro: button.data("bairro"),
		cidade: button.data("cidade"),
		uf: button.data("uf"),
		saleorder: ($(".w_saleorder_address").length > 0)
	}

	let response = await Post("entity.php", data);

	if (response != null) {

		Modal.Close(button.closest(".popup"));

		$(".entityaddress_tr_" + id_endereco).replaceWith(response);
	}

	Enable(button);
});