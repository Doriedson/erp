async function WaitertipFormEdit(container, button, action) {

	data = {
		action: action,
	}

	return await FormEdit(container, button, data, "waiter_tip.php");
}

async function WaitertipFormCancel(container, field, action) {

	var form = field.closest('form');

	data = {
		action: action,
	}

	return await FormCancel(container, form, field, data, "waiter_tip.php");
}

async function WaitertipFormSave(container, form, field, action) {

	var data = {
		action: action,
		value: field.val(),
	}

	return await FormSave(container, form, field, data, "waiter_tip.php");
}

/**
  * Opens "taxa_servico" to edition.
  */
 $(document).on("click", ".waitertip_bt_taxaservico", async function() {

	WaitertipFormEdit($(this).closest('.container'), $(this), 'waitertip_taxaservico_edit');
});

/**
  * Cancels "taxa_servico" edition.
  */
 $(document).on("focusout", "#frm_waitertip_taxaservico #taxa_servico", async function() {

	WaitertipFormCancel($(this).closest('form'), $(this), 'waitertip_taxaservico_cancel');
});

/**
  * Saves "taxa_servico" edition.
  */
 $(document).on("submit", "#frm_waitertip_taxaservico", async function(event) {

	event.preventDefault();

	FormDisable($(this));

	WaitertipFormSave($(this), $(this), $(this.taxa_servico), 'waitertip_taxaservico_save');
});