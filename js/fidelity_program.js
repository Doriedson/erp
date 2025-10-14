async function FidelityFormEdit(container, button, action) {

	data = {
		action: action, 
		id_fidelidaderegra: button.closest('.w-fidelity').data("id_fidelidaderegra"),
	}

	return await FormEdit(container, button, data, "fidelity_program.php");
}

async function FidelityFormCancel(container, field, action) {

	var form = field.closest('form');

	var id_fidelidaderegra = form.closest('.w-fidelity').data('id_fidelidaderegra');

	data = {
		action: action, 
		id_fidelidaderegra: id_fidelidaderegra,
	}

	return await FormCancel(container, form, field, data, "fidelity_program.php");
}

async function FidelityFormSave(container, form, field, action) {

	var data = {
		action: action,
		id_fidelidaderegra: form.closest('.w-fidelity').data('id_fidelidaderegra'),
		value: field.val(),
	}

	return await FormSave(container, form, field, data, "fidelity_program.php");
}

/**
  * Adds rule
  */
$(document).on("click", ".fidelity_bt_new", async function() {

	let button = $(this);

	Disable(button);

	let container = $('.fidelity_table');

	let data = {
		action: 'fidelity_new', 
	}

	let response = await Post("fidelity_program.php", data);

	if (response != null) {

		$('.fidelity_not_found').addClass('hidden');

		let content = $(response);

        container.append(content);

		ContainerFocus(content, true);
	}

	Enable(button);
});

/**
  * Deletes rule
  */
$(document).on("click", ".fidelity_bt_delete", async function() {

	let button = $(this);

	Disable(button);

    let container = button.closest('.w-fidelity');
	
	let id_fidelidaderegra = container.data('id_fidelidaderegra');

	let data = {
		action: 'fidelity_delete', 
		id_fidelidaderegra: id_fidelidaderegra,
	}

	let response = await Post("fidelity_program.php", data);

	if (response != null) {
		
		ContainerRemove(container, function() {

			if ($('.w-fidelity').length == 0) {

				$('.fidelity_not_found').removeClass('hidden');
			}
		});
	
	} else {

		Enable(button);
	}
});

/**
  * Event button to priority up rule
  */
$(document).on("click", ".fidelity_bt_up", async function() {

	var button = $(this);

	Disable(button);

	var container = $('.fidelity_table');

	var id_fidelidaderegra = button.closest('.w-fidelity').data('id_fidelidaderegra');

	data = {
		action: 'fidelity_up', 
		id_fidelidaderegra: id_fidelidaderegra,
	}

	var response = await Post("fidelity_program.php", data);

	if (response != null) {
		
        container.html(response);

	} else {

		Enable(button);
	}
});

/**
  * Event button to priority down rule
  */
$(document).on("click", ".fidelity_bt_down", async function() {

	var button = $(this);

	Disable(button);

	var container = $('.fidelity_table');

	var id_fidelidaderegra = button.closest('.w-fidelity').data('id_fidelidaderegra');

	data = {
		action: 'fidelity_down', 
		id_fidelidaderegra: id_fidelidaderegra,
	}

	var response = await Post("fidelity_program.php", data);

	if (response != null) {
		
        container.html(response);

	} else {

		Enable(button);
	}
});

/**
  * Opens priority rule edition.
  */
$(document).on("click", ".fidelity_bt_condicao", async function() {

	FidelityFormEdit($(this), $(this), "fidelity_condicao_edit");
});

/**
  * Cancels priority rule edition.
  */
 $(document).on("focusout", "#frm_fidelity_condicao #condicao", async function() {

	FidelityFormCancel($(this).closest('form'), $(this), "fidelity_condicao_cancel");
});

/**
  * Saves priority rule edition.
  */
 $(document).on("change", "#frm_fidelity_condicao", async function(event) {

	event.preventDefault();

	FormDisable($(this));

	FidelityFormSave($(this), $(this), $(this.condicao), "fidelity_condicao_save");
});

/**
  * Opens "valor" edition.
  */
 $(document).on("click", ".fidelity_bt_valor", async function() {

	FidelityFormEdit($(this), $(this), "fidelity_valor_edit");
});

/**
  * Cancels "valor" edition.
  */
$(document).on("focusout", "#frm_fidelity_valor #valor", async function() {

	FidelityFormCancel($(this).closest('form'), $(this), "fidelity_valor_cancel");
});

/**
  * Saves "valor" edition.
  */
$(document).on("submit", "#frm_fidelity_valor", async function(event) {

	event.preventDefault();

	FormDisable($(this));

	FidelityFormSave($(this), $(this), $(this.valor), "fidelity_valor_save");
});

/**
  * Opens "desconto" edition.
  */
$(document).on("click", ".fidelity_bt_desconto", async function() {

	FidelityFormEdit($(this), $(this), "fidelity_desconto_edit");
});

/**
  * Cancels "desconto" edition.
  */
$(document).on("focusout", "#frm_fidelity_desconto #desconto", async function() {

	FidelityFormCancel($(this).closest('form'), $(this), "fidelity_desconto_cancel");
});

/**
  * Event button to save edit "desconto"
  */
$(document).on("submit", "#frm_fidelity_desconto", async function(event) {

	event.preventDefault();

	FormDisable($(this));

	FidelityFormSave($(this), $(this), $(this.desconto), "fidelity_desconto_save");
});

/**
  * Opens "dias" edition.
  */
 $(document).on("click", ".fidelity_bt_dias", async function() {

	var button = $(this);

	Disable(button);

	data = {
		action: 'fidelity_dias_edit', 
	}

	var response = await Post("fidelity_program.php", data);

	if (response != null) {
		
		var content = $(response);
		
		button.replaceWith(content);
		
		AutoFocus(content);

	} else {

		Enable(button);
 	}
});

/**
  * Cancels "dias" edition.
  */
 $(document).on("focusout", "#frm_fidelity_dias #dias_compra", async function() {

	if ($(this).prop('disabled')) {
		return;
	}

	var form = $(this).closest('form');

	FormDisable(form);

	data = {
		action: 'fidelity_dias_cancel', 
	}

	var response = await Post("fidelity_program.php", data);

	if (response != null) {
		
        form.replaceWith(response);

	} else {

		FormEnable(form);
	}
});

/**
  * Saves "dias" edition
  */
 $(document).on("submit", "#frm_fidelity_dias", async function(event) {

	event.preventDefault();

    var form = $(this).closest('form');

	var dias_compra = this.dias_compra.value;

	FormDisable(form);

	data = {
		action: 'fidelity_dias_save', 
		dias_compra: dias_compra,
	}

	var response = await Post("fidelity_program.php", data);

	if (response != null) {
		
        form.replaceWith(response);

	} else {

		FormEnable(form);
	}
});