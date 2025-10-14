async function PrintingFormEdit(container, button, action) {

	data = {
		action: action, 
		id_impressao: button.data("id_impressao"),
	}

	return await FormEdit(container, button, data, "printing.php");
}

async function PrintingFormCancel(container, field, action) {

	var form = field.closest('form');

	var id_impressao = form.data('id_impressao');

	data = {
		action: action, 
		id_impressao: id_impressao,
	}

	return await FormCancel(container, form, field, data, "printing.php");
}

async function PrintingFormSave(container, form, field, action) {

	var data = {
		action: action,
		id_impressao: form.data('id_impressao'),
		value: field.val(),
	}

	return await FormSave(container, form, field, data, "printing.php");
}

/**
  * Opens "impressora" edition
  */
 $(document).on("click", ".printing_bt_impressora", async function() {

	PrintingFormEdit($(this), $(this), 'printing_impressora_edit');
});

/**
  * Cancel "impressora" edition.
  */
 $(document).on("focusout", "#frm_printing_impressora #id_impressora", async function() {

	PrintingFormCancel($(this).closest('form'), $(this), 'printing_impressora_cancel');
});

/**
  * Save "impressora" edition.
  */
$(document).on("change", "#frm_printing_impressora", async function(event) {

	event.preventDefault();

	FormDisable($(this));
	
	PrintingFormSave($(this), $(this), $(this.id_impressora), 'printing_impressora_save');
});