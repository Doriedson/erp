async function WalletsFormEdit(container, button, action) {

	let data = {
		action: action,
		id_wallet: button.data("id_wallet"),
	}

	return await FormEdit(container, button, data, "wallets.php");
}

async function WalletsFormCancel(container, field, action) {

	let form = field.closest('form');

	let id_wallet = form.data('id_wallet');

	let data = {
		action: action,
		id_wallet: id_wallet,
	}

	return await FormCancel(container, form, field, data, "wallets.php");
}

async function WalletsFormSave(container, form, field, action) {

	let data = {
		action: action,
		id_wallet: form.data('id_wallet'),
		value: field.val(),
	}

	let response = await FormSave(container, form, field, data, "wallets.php");

	return response;
}

/**
  * Opens "description" edition
  */
$(document).on("click", ".wallet_bt_description", function() {

	WalletsFormEdit($(this), $(this), 'wallet_description_edit');
});

/**
  * Cancels "description" edition.
  */
$(document).on("focusout", "#frm_wallet_description #description", function() {

	WalletsFormCancel($(this).closest('form'), $(this), 'wallet_description_cancel');
});

/**
  * Saves "description" edition.
  */
$(document).on("submit", "#frm_wallet_description", async function(event) {

	event.preventDefault();

	FormDisable($(this));

	WalletsFormSave($(this), $(this), $(this.description), 'wallet_description_save');
});

/**
  * Shows wallet window
  */
// $(document).on("click", ".expense_bt_wallet", function() {

// 	Disable($(this));

// 	LoadPage("wallet.php");
// });

/**
  * Adds new wallet
  */
$(document).on("click", ".wallet_bt_new", async function() {

	let button = $(this);
	let container = $('.wallets_table');

	Disable(button);

	let data = {
		action: "wallet_new",
	};

	response = await Post("wallets.php", data);

	if (response != null) {

		container.append(response);

		$('.wallet_not_found').addClass('hidden');
	}

	Enable(button);
});

/**
  * removes wallet
  */
$(document).on("click", ".wallet_bt_del", async function() {

	let button = $(this);
	let id_wallet = button.data('id_wallet');
	let container = button.closest('.w_wallet');
	let carteira = container.find('.wallet_bt_description').html();

	Disable(button);

	let data = {
		action: "wallet_del",
		id_wallet: id_wallet
	};

	let yes = async function() {

		let response = await Post("wallets.php", data);

		if (response != null) {

			container.slideUp("fast", function() {

				container.remove();
				if ($('.w_wallet').length == 0) {

					$('.wallet_not_found').removeClass('hidden');
				}
			});

		} else {

			Enable(button);
		}

		return true;
	}

	let no = async function() {

		Enable(button);
	}

	MessageBox.Show("Apagar carteira: " + carteira + "?", yes, no);

	MenuClose();
});

/**
  * removes wallet sharing
  */
$(document).on("click", ".walletsharing_bt_del", async function() {

	let button = $(this);
	let id_wallet = button.data('id_wallet');
	let container = button.closest('.w_wallet');
	let carteira = container.find('.wallet_bt_description').html();

	Disable(button);

	let data = {
		action: "walletsharing_del",
		id_wallet: id_wallet
	};

	let yes = async function() {

		let response = await Post("wallets.php", data);

		if (response != null) {

			container.slideUp("fast", function() {

				container.remove();
				if ($('.w_wallet').length == 0) {

					$('.wallet_not_found').removeClass('hidden');
				}
			});

		} else {

			Enable(button);
		}

		return true;
	}

	let no = async function() {

		Enable(button);
	}

	MessageBox.Show("Remover carteira: " + carteira + "?", yes, no);

	MenuClose();
});

/**
  * Opens wallet sharing
  */
$(document).on("click", ".wallet_bt_share", async function() {

	let button = $(this);
	let id_wallet = button.data('id_wallet');

	Disable(button);

	let data = {
		action: "wallet_popup_sharing",
		id_wallet: id_wallet,
	};

	response = await Post("wallets.php", data);

	if (response != null) {

		Modal.Show(Modal.POPUP_SIZE_SMALL, "Compartilhar Carteira", response, null, false, "<i class='icon fa-solid fa-share-nodes'></i>");
	}

	Enable(button);

	MenuClose();
});

/**
  * Toggle wallet sharing
  */
$(document).on("click", ".walletsharing_share", async function() {

	let button = $(this);
	let id_entidade = button.data('id_entidade');
	let id_wallet = button.data('id_wallet');

	Disable(button);

	let data = {
		action: "walletsharing_notshare",
		id_wallet: id_wallet,
		id_entidade: id_entidade
	};

	if (button.is(":checked")) {

		data['action'] = "walletsharing_share";
	}

	response = await Post("wallets.php", data);

	Enable(button);
});

/**
  * Opens wallet
  */
$(document).on("click", ".wallet_bt_open", async function() {

	let button = $(this);
	let id_wallet = button.data('id_wallet');

	Disable(button);

	let data = {
		action: "load",
		id_wallet: id_wallet
	};

	LoadPage("wallet.php", data);
});

/**
  * Shows wallets
  */
$(document).on("click", ".wallets_bt_show", async function() {

	Disable($(this));

	LoadPage("wallets.php");
});

/**
 * Enables/Disables parcelado.
 */
$(document).on("click", "#walletdespesanew_parcelado", function() {

	$("#frm_walletdespesanew_parcelado").prop( "disabled", !this.checked);

});