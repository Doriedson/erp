async function CompanyFormEdit(container, button, action) {

	data = {
		action: action
	}

	return await FormEdit(container, button, data, "company.php");
}

async function CompanyFormCancel(container, field, action) {

	var form = field.closest('form');

	var id_walletsector = form.data('id_walletsector');

	data = {
		action: action
	}

	return await FormCancel(container, form, field, data, "company.php");
}

async function CompanyFormSave(container, form, field, action) {

	var data = {
		action: action,
		value: field.val(),
	}

    return await FormSave(container, form, field, data, "company.php");
}

/**
  * Opens empresa edition
  */
 $(document).on("click", ".company_bt_empresa", async function() {

	CompanyFormEdit($(this), $(this), 'company_empresa_edit');
});

/**
  * Cancel empresa edition.
  */
 $(document).on("focusout", "#frm_company_empresa #empresa", async function() {

	CompanyFormCancel($(this).closest('form'), $(this), 'company_empresa_cancel');
});

/**
  * Save empresa edition.
  */
$(document).on("submit", "#frm_company_empresa", async function(event) {

	event.preventDefault();

	FormDisable($(this));
	
	response = await CompanyFormSave($(this), $(this), $(this.empresa), 'company_empresa_save');

  if (response != null) {

    $('.company').html(response['empresa']);
  }
});

/**
  * Opens company cnpj edition
  */
 $(document).on("click", ".company_bt_cnpj", async function() {

	CompanyFormEdit($(this), $(this), "company_cnpj_edit");
});

/**
  * Cancels company cnpj edition
  */
$(document).on("focusout", "#frm_company_cnpj #cnpj", async function() {

	CompanyFormCancel($(this).closest('form'), $(this), "company_cnpj_cancel");
});

/**
  * Saves company cnpj edition
  */
$(document).on("submit", "#frm_company_cnpj", async function(event) {

	event.preventDefault();

	FormDisable($(this));

	CompanyFormSave($(this), $(this), $(this.cnpj), "company_cnpj_save");
});

/**
  * Opens company ie edition
  */
 $(document).on("click", ".company_bt_ie", async function() {

	CompanyFormEdit($(this), $(this), "company_ie_edit");
});

/**
  * Cancels company ie edition
  */
$(document).on("focusout", "#frm_company_ie #ie", async function() {

	CompanyFormCancel($(this).closest('form'), $(this), "company_ie_cancel");
});

/**
  * Saves company ie edition
  */
$(document).on("submit", "#frm_company_ie", async function(event) {

	event.preventDefault();

	FormDisable($(this));

	CompanyFormSave($(this), $(this), $(this.ie), "company_ie_save");
});

/**
  * Opens company telefone edition
  */
 $(document).on("click", ".company_bt_telefone", async function() {

	CompanyFormEdit($(this), $(this), "company_telefone_edit");
});

/**
  * Cancels company telefone edition
  */
$(document).on("focusout", "#frm_company_telefone #telefone", async function() {

	CompanyFormCancel($(this).closest('form'), $(this), "company_telefone_cancel");
});

/**
  * Saves company telefone edition
  */
$(document).on("submit", "#frm_company_telefone", async function(event) {

	event.preventDefault();

	FormDisable($(this));

	CompanyFormSave($(this), $(this), $(this.telefone), "company_telefone_save");
});

/**
  * Opens company celular edition
  */
 $(document).on("click", ".company_bt_celular", async function() {

	CompanyFormEdit($(this), $(this), "company_celular_edit");
});

/**
  * Cancels company celular edition
  */
$(document).on("focusout", "#frm_company_celular #celular", async function() {

	CompanyFormCancel($(this).closest('form'), $(this), "company_celular_cancel");
});

/**
  * Saves company celular edition
  */
$(document).on("submit", "#frm_company_celular", async function(event) {

	event.preventDefault();

	FormDisable($(this));

	CompanyFormSave($(this), $(this), $(this.celular), "company_celular_save");
});

/**
  * Opens company cep edition
  */
 $(document).on("click", ".company_bt_cep", async function() {

	CompanyFormEdit($(this), $(this), "company_cep_edit");
});

/**
  * Cancels company cep edition
  */
$(document).on("focusout", "#frm_company_cep #cep", async function() {

	CompanyFormCancel($(this).closest('form'), $(this), "company_cep_cancel");
});

/**
  * Saves company cep edition
  */
$(document).on("submit", "#frm_company_cep", async function(event) {

	event.preventDefault();

	FormDisable($(this));

	CompanyFormSave($(this), $(this), $(this.cep), "company_cep_save");
});

/**
  * Opens company cep edition
  */
 $(document).on("click", ".company_bt_cep", async function() {

	CompanyFormEdit($(this), $(this), "company_cep_edit");
});

/**
  * Cancels company cep edition
  */
$(document).on("focusout", "#frm_company_cep #cep", async function() {

	CompanyFormCancel($(this).closest('form'), $(this), "company_cep_cancel");
});

/**
  * Saves company cep edition
  */
$(document).on("submit", "#frm_company_cep", async function(event) {

	event.preventDefault();

	FormDisable($(this));

	CompanyFormSave($(this), $(this), $(this.cep), "company_cep_save");
});

/**
  * Opens company rua edition
  */
 $(document).on("click", ".company_bt_rua", async function() {

	CompanyFormEdit($(this), $(this), "company_rua_edit");
});

/**
  * Cancels company rua edition
  */
$(document).on("focusout", "#frm_company_rua #rua", async function() {

	CompanyFormCancel($(this).closest('form'), $(this), "company_rua_cancel");
});

/**
  * Saves company rua edition
  */
$(document).on("submit", "#frm_company_rua", async function(event) {

	event.preventDefault();

	FormDisable($(this));

	CompanyFormSave($(this), $(this), $(this.rua), "company_rua_save");
});

/**
  * Opens company bairro edition
  */
 $(document).on("click", ".company_bt_bairro", async function() {

	CompanyFormEdit($(this), $(this), "company_bairro_edit");
});

/**
  * Cancels company bairro edition
  */
$(document).on("focusout", "#frm_company_bairro #bairro", async function() {

	CompanyFormCancel($(this).closest('form'), $(this), "company_bairro_cancel");
});

/**
  * Saves company bairro edition
  */
$(document).on("submit", "#frm_company_bairro", async function(event) {

	event.preventDefault();

	FormDisable($(this));

	CompanyFormSave($(this), $(this), $(this.bairro), "company_bairro_save");
});

/**
  * Opens company cidade edition
  */
 $(document).on("click", ".company_bt_cidade", async function() {

	CompanyFormEdit($(this), $(this), "company_cidade_edit");
});

/**
  * Cancels company cidade edition
  */
$(document).on("focusout", "#frm_company_cidade #cidade", async function() {

	CompanyFormCancel($(this).closest('form'), $(this), "company_cidade_cancel");
});

/**
  * Saves company cidade edition
  */
$(document).on("submit", "#frm_company_cidade", async function(event) {

	event.preventDefault();

	FormDisable($(this));

	CompanyFormSave($(this), $(this), $(this.cidade), "company_cidade_save");
});

/**
  * Opens company uf edition
  */
 $(document).on("click", ".company_bt_uf", async function() {

	CompanyFormEdit($(this), $(this), "company_uf_edit");
});

/**
  * Cancels company uf edition
  */
$(document).on("focusout", "#frm_company_uf #uf", async function() {

	CompanyFormCancel($(this).closest('form'), $(this), "company_uf_cancel");
});

/**
  * Saves company uf edition
  */
$(document).on("change", "#frm_company_uf", async function() {

	FormDisable($(this));

	CompanyFormSave($(this), $(this), $(this.uf), "company_uf_save");
});

/**
  * Opens company cupomlinha1 edition
  */
 $(document).on("click", ".company_bt_cupomlinha1", async function() {

	CompanyFormEdit($(this), $(this), "company_cupomlinha1_edit");
});

/**
  * Cancels company cupomlinha1 edition
  */
$(document).on("focusout", "#frm_company_cupomlinha1 #cupomlinha1", async function() {

	CompanyFormCancel($(this).closest('form'), $(this), "company_cupomlinha1_cancel");
});

/**
  * Saves company cupomlinha1 edition
  */
$(document).on("submit", "#frm_company_cupomlinha1", async function(event) {

	event.preventDefault();

	FormDisable($(this));

	CompanyFormSave($(this), $(this), $(this.cupomlinha1), "company_cupomlinha1_save");
});

/**
  * Opens company cupomlinha2 edition
  */
 $(document).on("click", ".company_bt_cupomlinha2", async function() {

	CompanyFormEdit($(this), $(this), "company_cupomlinha2_edit");
});

/**
  * Cancels company cupomlinha2 edition
  */
$(document).on("focusout", "#frm_company_cupomlinha2 #cupomlinha2", async function() {

	CompanyFormCancel($(this).closest('form'), $(this), "company_cupomlinha2_cancel");
});

/**
  * Saves company cupomlinha2 edition
  */
$(document).on("submit", "#frm_company_cupomlinha2", async function(event) {

	event.preventDefault();

	FormDisable($(this));

	CompanyFormSave($(this), $(this), $(this.cupomlinha2), "company_cupomlinha2_save");
});