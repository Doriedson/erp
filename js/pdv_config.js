async function PDVFormEdit(container, button, action) {

	data = {
		action: action, 
		id_pdv: button.data("id_pdv"),
	}

	return await FormEdit(container, button, data, "pdv_config.php");
}

async function PDVFormCancel(container, field, action) {

	var form = field.closest('form');

	var id_pdv = form.data('id_pdv');

	data = {
		action: action, 
		id_pdv: id_pdv,
	}

	return await FormCancel(container, form, field, data, "pdv_config.php");
}

async function PDVFormSave(container, form, field, action) {

	var data = {
		action: action,
		id_pdv: form.data('id_pdv'),
		value: field.val(),
	}

	return await FormSave(container, form, field, data, "pdv_config.php");
}

/**
  * Open "descricao" edition
  */
 $(document).on("click", ".pdv_bt_descricao", async function() {

	PDVFormEdit($(this).closest('.container'), $(this), "pdv_descricao_edit");
});

/**
  * Cancels "descricao" edition
  */
 $(document).on("focusout", "#frm_pdv_descricao #descricao", async function() {

	PDVFormCancel($(this).closest('form'), $(this), "pdv_descricao_cancel");
});

/**
  * Saves "descricao" edition.
  */
 $(document).on("submit", "#frm_pdv_descricao", async function(event) {

	event.preventDefault();

	FormDisable($(this));

	PDVFormSave($(this), $(this), $(this.descricao), "pdv_descricao_save");
});

/**
  * Toggle active and not active "trocoini"
  */
 $(document).on("click", ".pdv_bt_trocoini", async function() {

	let button = $(this);

	let id_pdv = button.data('id_pdv');

	Disable(button);

	let data = {
		action: "pdv_trocoini", 
		id_pdv: id_pdv,	
	}

	let response = await Post("pdv_config.php", data);

	Enable(button);

	if (response != null) {

		button.prop("checke", response);
	}
});

/**
  * Toggle active and not active "balança"
  */
 $(document).on("click", ".pdv_bt_balanca", async function() {

	let button = $(this);

	let id_pdv = button.data('id_pdv');

	Disable(button);

	let data = {
		action: "pdv_balanca", 
		id_pdv: id_pdv,	
	}

	let response = await Post("pdv_config.php", data);

	Enable(button);

	if (response != null) {

		button.prop("checked", response);
	// 	button.replaceWith(response);

	// } else {
	}
});

/**
  * Open "charwrite" edition
  */
 $(document).on("click", ".pdv_bt_charwrite", async function() {

	PDVFormEdit($(this).closest('.container'), $(this), "pdv_charwrite_edit");
});

/**
  * Cancels "charwrite" edition
  */
 $(document).on("focusout", "#frm_pdv_charwrite #balanca_charwrite", async function() {

	PDVFormCancel($(this).closest('form'), $(this), "pdv_charwrite_cancel");
});

/**
  * Saves "charwrite" edition.
  */
 $(document).on("submit", "#frm_pdv_charwrite", async function(event) {

	event.preventDefault();

	FormDisable($(this));

	PDVFormSave($(this), $(this), $(this.balanca_charwrite), "pdv_charwrite_save");
});

/**
  * Open "charend" edition
  */
 $(document).on("click", ".pdv_bt_charend", async function() {

	PDVFormEdit($(this).closest('.container'), $(this), "pdv_charend_edit");
});

/**
  * Cancels "charend" edition
  */
 $(document).on("focusout", "#frm_pdv_charend #balanca_charend", async function() {

	PDVFormCancel($(this).closest('form'), $(this), "pdv_charend_cancel");
});

/**
  * Saves "charend" edition.
  */
 $(document).on("submit", "#frm_pdv_charend", async function(event) {

	event.preventDefault();

	FormDisable($(this));

	PDVFormSave($(this), $(this), $(this.balanca_charend), "pdv_charend_save");
});

/**
  * Toggle active and not active "impressora"
  */
 $(document).on("click", ".pdv_bt_impressora", async function() {

	let button = $(this);

	let id_pdv = button.data('id_pdv');

	Disable(button);

	data = {
		action: "pdv_impressora", 
		id_pdv: id_pdv,	
	}

	let response = await Post("pdv_config.php", data);

	Enable(button);

	if (response != null) {

		// button.replaceWith(response);
		button.prop("checked", response);
	}
});

/**
  * Toggle active and not active "gaveta"
  */
 $(document).on("click", ".pdv_bt_gaveta", async function() {

	let button = $(this);

	let id_pdv = button.data('id_pdv');

	Disable(button);

	let data = {
		action: "pdv_gaveta", 
		id_pdv: id_pdv,	
	}

	let response = await Post("pdv_config.php", data);

	Enable(button);

	if (response != null) {

		button.prop("checked", response);
	// 	button.replaceWith(response);

	// } else {
	}
});

/**
  * Open "impressora_path" edition
  */
 $(document).on("click", ".pdv_bt_impressora_path", async function() {

	PDVFormEdit($(this), $(this), "pdv_impressora_path_edit");
});

/**
  * Cancels "impressora_path" edition
  */
 $(document).on("focusout", "#frm_pdv_impressora_path #id_impressora", async function() {

	PDVFormCancel($(this).closest('form'), $(this), "pdv_impressora_path_cancel");
});

/**
  * Saves "impressora_path" edition.
  */
 $(document).on("change", "#frm_pdv_impressora_path", async function(event) {

	event.preventDefault();

	FormDisable($(this));

	PDVFormSave($(this), $(this), $(this.id_impressora), "pdv_impressora_path_save");
});

/**
  * Open "cashdrawertype" edition
  */
 $(document).on("click", ".pdv_bt_cashdrawertype", async function() {

	PDVFormEdit($(this), $(this), "pdv_cashdrawertype_edit");
});

/**
  * Cancels "cashdrawertype" edition
  */
 $(document).on("focusout", "#frm_pdv_cashdrawertype #id_gaveteiro", async function() {

	PDVFormCancel($(this).closest('form'), $(this), "pdv_cashdrawertype_cancel");
});

/**
  * Saves "cashdrawertype" edition.
  */
 $(document).on("change", "#frm_pdv_cashdrawertype", async function(event) {

	event.preventDefault();

	FormDisable($(this));

	PDVFormSave($(this), $(this), $(this.id_gaveteiro), "pdv_cashdrawertype_save");
});


/**
  * Adds new PDV
  */
 $(document).on("click", ".bt_pdv_new", async function() {

	let data = {
		action: "pdv_new"
	}

	response = await Post("pdv_config.php", data);

	if (response != null) {

	}
});