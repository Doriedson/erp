async function ScalesBarcodeFormEdit(container, button, action) {

	data = {
		action: action
	}

	return await FormEdit(container, button, data, "scales_barcode.php");
}

async function ScalesBarcodeFormCancel(container, field, action) {

	let form = field.closest('form');

	data = {
		action: action
	}

	return await FormCancel(container, form, field, data, "scales_barcode.php");
}

async function ScalesBarcodeFormSave(container, form, field, action) {

	let data = {
		action: action,
		value: field.val()
	}

	return await FormSave(container, form, field, data, "scales_barcode.php");
}

/**
  * Activate/Deactivate scalesbarcode.
  */
 $(document).on("click", ".check_scalesbarcode", async function() {

    let button = $(this);

    Disable(button);

    let data = {
        action: "scalesbarcode",
        value: this.checked
    }

    response = await Post("scales_barcode.php", data);

    if (response != null) {

        this.checked = response;

	} else {

        this.checked = !this.checked;
    } 

    Enable(button);
});

/**
  * Open "startnumber" edition
  */
$(document).on("click", ".bt_scalesbarcode_startnumber", async function() {

	ScalesBarcodeFormEdit($(this).closest('.container'), $(this), "scalesbarcode_startnumber_edit");
});

/**
  * Cancels "startnumber" edition
  */
 $(document).on("focusout", "#frm_scalesbarcode_startnumber #scalesbarcode_startnumber", async function() {

	ScalesBarcodeFormCancel($(this).closest('form'), $(this), "scalesbarcode_startnumber_cancel");
});

/**
  * Saves "startnumber" edition.
  */
 $(document).on("submit", "#frm_scalesbarcode_startnumber", async function(event) {

	event.preventDefault();

	FormDisable($(this));

	ScalesBarcodeFormSave($(this), $(this), $(this.scalesbarcode_startnumber), "scalesbarcode_startnumber_save");
});

/**
  * Open "sizecode" edition
  */
$(document).on("click", ".bt_scalesbarcode_sizecode", async function() {

	ScalesBarcodeFormEdit($(this).closest('.container'), $(this), "scalesbarcode_sizecode_edit");
});

/**
  * Cancels "sizecode" edition
  */
 $(document).on("focusout", "#frm_scalesbarcode_sizecode #scalesbarcode_sizecode", async function() {

	ScalesBarcodeFormCancel($(this).closest('form'), $(this), "scalesbarcode_sizecode_cancel");
});

/**
  * Saves "sizecode" edition.
  */
 $(document).on("submit", "#frm_scalesbarcode_sizecode", async function(event) {

	event.preventDefault();

	FormDisable($(this));

	ScalesBarcodeFormSave($(this), $(this), $(this.scalesbarcode_sizecode), "scalesbarcode_sizecode_save");
});

/**
  * Open "productstartposition" edition
  */
$(document).on("click", ".bt_scalesbarcode_productstartposition", async function() {

	ScalesBarcodeFormEdit($(this), $(this), "scalesbarcode_productstartposition_edit");
});

/**
  * Cancels "productstartposition" edition
  */
 $(document).on("focusout", "#frm_scalesbarcode_productstartposition #scalesbarcode_productstartposition", async function() {

	ScalesBarcodeFormCancel($(this).closest('form'), $(this), "scalesbarcode_productstartposition_cancel");
});

/**
  * Saves "productstartposition" edition.
  */
 $(document).on("submit", "#frm_scalesbarcode_productstartposition", async function(event) {

	event.preventDefault();

	FormDisable($(this));

	ScalesBarcodeFormSave($(this), $(this), $(this.scalesbarcode_productstartposition), "scalesbarcode_productstartposition_save");
});

/**
  * Open "productendposition" edition
  */
$(document).on("click", ".bt_scalesbarcode_productendposition", async function() {

	ScalesBarcodeFormEdit($(this), $(this), "scalesbarcode_productendposition_edit");
});

/**
  * Cancels "productendposition" edition
  */
 $(document).on("focusout", "#frm_scalesbarcode_productendposition #scalesbarcode_productendposition", async function() {

	ScalesBarcodeFormCancel($(this).closest('form'), $(this), "scalesbarcode_productendposition_cancel");
});

/**
  * Saves "productendposition" edition.
  */
 $(document).on("submit", "#frm_scalesbarcode_productendposition", async function(event) {

	event.preventDefault();

	FormDisable($(this));

	ScalesBarcodeFormSave($(this), $(this), $(this.scalesbarcode_productendposition), "scalesbarcode_productendposition_save");
});

/**
  * Open "weightstartposition" edition
  */
$(document).on("click", ".bt_scalesbarcode_weightstartposition", async function() {

	ScalesBarcodeFormEdit($(this), $(this), "scalesbarcode_weightstartposition_edit");
});

/**
  * Cancels "weightstartposition" edition
  */
 $(document).on("focusout", "#frm_scalesbarcode_weightstartposition #scalesbarcode_weightstartposition", async function() {

	ScalesBarcodeFormCancel($(this).closest('form'), $(this), "scalesbarcode_weightstartposition_cancel");
});

/**
  * Saves "weightstartposition" edition.
  */
 $(document).on("submit", "#frm_scalesbarcode_weightstartposition", async function(event) {

	event.preventDefault();

	FormDisable($(this));

	ScalesBarcodeFormSave($(this), $(this), $(this.scalesbarcode_weightstartposition), "scalesbarcode_weightstartposition_save");
});

/**
  * Open "weightendposition" edition
  */
$(document).on("click", ".bt_scalesbarcode_weightendposition", async function() {

	ScalesBarcodeFormEdit($(this), $(this), "scalesbarcode_weightendposition_edit");
});

/**
  * Cancels "weightendposition" edition
  */
 $(document).on("focusout", "#frm_scalesbarcode_weightendposition #scalesbarcode_weightendposition", async function() {

	ScalesBarcodeFormCancel($(this).closest('form'), $(this), "scalesbarcode_weightendposition_cancel");
});

/**
  * Saves "weightendposition" edition.
  */
 $(document).on("submit", "#frm_scalesbarcode_weightendposition", async function(event) {

	event.preventDefault();

	FormDisable($(this));

	ScalesBarcodeFormSave($(this), $(this), $(this.scalesbarcode_weightendposition), "scalesbarcode_weightendposition_save");
});

/**
  * Open "scalesbarcode_weightorprice" edition
  */
$(document).on("click", ".bt_scalesbarcode_weightorprice", async function() {

	ScalesBarcodeFormEdit($(this), $(this), "scalesbarcode_weightorprice_edit");
});

/**
  * Cancels "scalesbarcode_weightorprice" edition
  */
 $(document).on("focusout", "#frm_scalesbarcode_weightorprice #scalesbarcode_weightorprice", async function() {

	ScalesBarcodeFormCancel($(this).closest('form'), $(this), "scalesbarcode_weightorprice_cancel");
});

/**
  * Saves "scalesbarcode_weightorprice" edition.
  */
 $(document).on("change", "#frm_scalesbarcode_weightorprice", async function(event) {

	event.preventDefault();

	FormDisable($(this));

	ScalesBarcodeFormSave($(this), $(this), $(this.scalesbarcode_weightorprice), "scalesbarcode_weightorprice_save");
});

/**
  * Open "weightdecimals" edition
  */
$(document).on("click", ".bt_scalesbarcode_weightdecimals", async function() {

	ScalesBarcodeFormEdit($(this), $(this), "scalesbarcode_weightdecimals_edit");
});

/**
  * Cancels "weightdecimals" edition
  */
 $(document).on("focusout", "#frm_scalesbarcode_weightdecimals #scalesbarcode_weightdecimals", async function() {

	ScalesBarcodeFormCancel($(this).closest('form'), $(this), "scalesbarcode_weightdecimals_cancel");
});

/**
  * Saves "weightdecimals" edition.
  */
 $(document).on("submit", "#frm_scalesbarcode_weightdecimals", async function(event) {

	event.preventDefault();

	FormDisable($(this));

	ScalesBarcodeFormSave($(this), $(this), $(this.scalesbarcode_weightdecimals), "scalesbarcode_weightdecimals_save");
});