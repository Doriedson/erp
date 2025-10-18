async function WalletcashtypeFormEdit(container, button, action) {

	data = {
		action: action,
		id_walletcashtype: button.data("id_walletcashtype"),
	}

	return await FormEdit(container, button, data, "wallet_cashtype.php");
}

async function WalletcashtypeFormCancel(container, field, action) {

	var form = field.closest('form');

	var id_walletcashtype = form.data('id_walletcashtype');

	data = {
		action: action,
		id_walletcashtype: id_walletcashtype,
	}

	return await FormCancel(container, form, field, data, "wallet_cashtype.php");
}

async function WalletcashtypeFormSave(container, form, field, action) {

	let data = {
		action: action,
		id_walletcashtype: form.data('id_walletcashtype'),
		value: field.val(),
	}

    return await FormSave(container, form, field, data, "wallet_cashtype.php");
}

/**
  * Add cashtype
  */
$(document).on("submit", "#frm_walletcashtype", async function(event) {

	event.preventDefault();

	let form = $(this);
	let popup = form.closest(".popup");
	let id_wallet = form.data('id_wallet');
	let source = form.data('source');
	let walletcashtype = this.walletcashtype.value;

	FormDisable(form);

	data = {
		action: 'walletcashtype_add',
		id_wallet: id_wallet,
		walletcashtype: walletcashtype,
		source: source
	}

	response = await Post("wallet_cashtype.php", data);

	FormEnable(form);

	if (response != null) {

	$('.select_id_walletcashtype').html(response['list']);

	let content = $(response['walletcashtype']);

	let container = $('.walletcashtype_container');

	if (container.length > 0) {

		container.append(content);
		ContainerFocus(content, true);
	}

	Modal.Close(popup);
	}
});

/**
  * Opens "walletcashtype" edition
  */
 $(document).on("click", ".walletcashtype_bt_walletcashtype", async function() {

	WalletcashtypeFormEdit($(this), $(this), 'walletcashtype_edit');
});

/**
  * Cancel cashtype edition.
  */
 $(document).on("focusout", "#frm_walletcashtype_walletcashtype #walletcashtype", async function() {

	WalletcashtypeFormCancel($(this).closest('form'), $(this), 'walletcashtype_cancel');
});

/**
  * Save cashtype edition.
  */
$(document).on("submit", "#frm_walletcashtype_walletcashtype", async function(event) {

	event.preventDefault();

	FormDisable($(this));

	let response = await WalletcashtypeFormSave($(this), $(this), $(this.walletcashtype), 'walletcashtype_save');

	if (response != null) {

		$('.select_id_walletcashtype').html(response['list']);
	}
});

/**
  * Deletes cashtype
  */
 $(document).on("click", ".walletcashtype_bt_del", async function() {

	let button = $(this);

	let container = button.closest('.w-walletcashtype');

	let id_walletcashtype = container.data('id_walletcashtype');

	Disable(button);

	let yes = async function() {

		let data = {
			action: 'walletcashtype_delete',
			id_walletcashtype: id_walletcashtype
		}

		response = await Post("wallet_cashtype.php", data);

		if (response != null) {

			ContainerRemove(container, function() {

				if ($('.w-walletcashtype').length == 0) {

					$('.walletcashtype_notfound').removeClass('hidden');
				}
			});

			$('.select_id_walletcashtype').html(response);

		} else {

			Enable(button);
		}

		return true;
	}

	let no = async function () {

		Enable(button);
	}

	MessageBox.Show("Remover espécie?", yes, no);
});

/**
  * Shows new walletcashtype popup
  */
 $(document).on("click", ".walletcashtype_bt_show_new", async function() {

	let button = $(this);
	let id_wallet = button.data("id_wallet");

	Disable(button);

	let data = {
		action: "walletcashtype_popup_new",
		id_wallet: id_wallet
	}

	let response = await Post("wallet_cashtype.php", data);

	if (response != null) {

		Modal.Show(Modal.POPUP_SIZE_SMALL, "Cadastro de Espécie", response, null, false, "<i class='icon fa-solid fa-square-plus'></i>");
	}

	Enable(button);

	MenuClose();
});