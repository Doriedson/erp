/**
 * Delivery Direto authentication
*/
$(document).on("click", ".bt_dd_test1", async function(event) {
	
    // event.preventDefault();

    let data = {
        action: "deliverydireto_create_address"
    }

    let response = await Post("delivery_direto.php", data);

    console.log(response);
});

$(document).on("click", ".bt_dd_test2", async function(event) {
	
  // event.preventDefault();

  let data = {
      action: "deliverydireto_calculate_fee"
  }

  let response = await Post("delivery_direto.php", data);

  console.log(response);
});

$(document).on("click", ".bt_dd_test3", async function(event) {
	
  // event.preventDefault();

  let data = {
      action: "deliverydireto_get_orders"
  }

  let response = await Post("delivery_direto.php", data);

  console.log(response);
});

/**
  * Activates/Deactivates Delivery Direto integration
  */
$(document).on("click", ".bt_deliverydireto_ativo", async function() {

	let button = $(this);

	Disable(button);

	let data = {
		action: "deliverydireto_ativo",
	}

	let response = await Post('delivery_direto.php', data);

	Enable(button);

	if (response != null) {

		button.prop("checked", response);
	}
});

/**
  * Opens "storeid" edition
  */
$(document).on("click", ".bt_deliverydireto_storeid", async function() {

  let data = {
		action: "deliverydireto_storeid_edit", 
	}

  let container = $(this);
  let button = container;
    
	await FormEdit(container, button, data, "delivery_direto.php");
});

/**
  * Cancel "storeid" edition.
  */
$(document).on("focusout", "#frm_deliverydireto_storeid #storeid", async function() {

  let field = $(this);

  let form = field.closest('form');
  let container = form;

	let data = {
		action: "deliverydireto_storeid_cancel", 
	}

	await FormCancel(container, form, field, data, "delivery_direto.php");
});

/**
  * Save "storeid" edition.
  */
$(document).on("submit", "#frm_deliverydireto_storeid", async function(event) {

  event.preventDefault();	
   
  let form = $(this);
  let container = form;
  let field = $(this.storeid);

  let data = {
    action: "deliverydireto_storeid_save",
    value: field.val(),
  }

  FormDisable(form);

	await FormSave(container, form, field, data, "delivery_direto.php");
});

/**
  * Opens "usuario" edition
  */
$(document).on("click", ".bt_deliverydireto_usuario", async function() {

  let data = {
  action: "deliverydireto_usuario_edit", 
}

  let container = $(this);
  let button = container;
  
  await FormEdit(container, button, data, "delivery_direto.php");
});

/**
* Cancel "usuario" edition.
*/
$(document).on("focusout", "#frm_deliverydireto_usuario #usuario", async function() {

  let field = $(this);

  let form = field.closest('form');
  let container = form;

  data = {
    action: "deliverydireto_usuario_cancel", 
  }

  await FormCancel(container, form, field, data, "delivery_direto.php");
});

/**
* Save "usuario" edition.
*/
$(document).on("submit", "#frm_deliverydireto_usuario", async function(event) {

  event.preventDefault();	
 
  let form = $(this);
  let container = form;
  let field = $(this.usuario);

  let data = {
    action: "deliverydireto_usuario_save",
    value: field.val(),
  }

  FormDisable(form);

  await FormSave(container, form, field, data, "delivery_direto.php");
});

/**
  * Opens "senha" edition
  */
$(document).on("click", ".bt_deliverydireto_senha", async function() {

  let data = {
  action: "deliverydireto_senha_edit", 
}

  let container = $(this);
  let button = container;
  
  await FormEdit(container, button, data, "delivery_direto.php");
});

/**
* Cancel "senha" edition.
*/
$(document).on("focusout", "#frm_deliverydireto_senha #senha", async function() {

  let field = $(this);

  let form = field.closest('form');
  let container = form;

  data = {
    action: "deliverydireto_senha_cancel", 
  }

  await FormCancel(container, form, field, data, "delivery_direto.php");
});

/**
* Save "senha" edition.
*/
$(document).on("submit", "#frm_deliverydireto_senha", async function(event) {

  event.preventDefault();	
 
  let form = $(this);
  let container = form;
  let field = $(this.senha);

  let data = {
    action: "deliverydireto_senha_save",
    value: field.val(),
  }

  FormDisable(form);

  await FormSave(container, form, field, data, "delivery_direto.php");
});