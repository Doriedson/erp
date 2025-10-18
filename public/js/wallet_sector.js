async function WalletsectorFormEdit(container, button, action) {

	data = {
		action: action,
		id_walletsector: button.data("id_walletsector"),
	}

	return await FormEdit(container, button, data, "wallet_sector.php");
}

async function WalletsectorFormCancel(container, field, action) {

	var form = field.closest('form');

	var id_walletsector = form.data('id_walletsector');

	data = {
		action: action,
		id_walletsector: id_walletsector,
	}

	return await FormCancel(container, form, field, data, "wallet_sector.php");
}

async function WalletsectorFormSave(container, form, field, action) {

	var data = {
		action: action,
		id_walletsector: form.data('id_walletsector'),
		value: field.val(),
	}

	return await FormSave(container, form, field, data, "wallet_sector.php");
}

/**
  * Add sector
  */
$(document).on("submit", "#frm_walletsector", async function(event) {

	event.preventDefault();

	let form = $(this);
	let id_wallet = form.data('id_wallet');
	let walletsector = this.walletsector.value;

	let popup = form.closest(".popup");

	FormDisable(form);

	data = {
		action: 'walletsector_add',
		id_wallet: id_wallet,
		walletsector: walletsector,
	}

	response = await Post("wallet_sector.php", data);

	if (response != null) {

		$('.select_id_walletsector').html(response["list"]);

		let content = $(response['walletsector']);

		let container = $('.walletsector_container');

		if (container.length > 0) {

			container.append(content);

			ContainerFocus(content, true);
		}

		Modal.Close(popup);
	}

	FormEnable(form);
});

/**
  * Opens "walletsector" edition
  */
 $(document).on("click", ".walletsector_bt_walletsector", async function() {

	WalletsectorFormEdit($(this), $(this), 'walletsector_edit');
});

/**
  * Cancel sector edition.
  */
 $(document).on("focusout", "#frm_walletsector_walletsector #walletsector", async function() {

	WalletsectorFormCancel($(this).closest('form'), $(this), 'walletsector_cancel');
});

/**
  * Save sector edition.
  */
$(document).on("submit", "#frm_walletsector_walletsector", async function(event) {

	event.preventDefault();

	FormDisable($(this));

	let response = await WalletsectorFormSave($(this), $(this), $(this.walletsector), 'walletsector_save');

	if (response != null) {

		$('.select_id_walletsector').html(response['list']);
	}
});

/**
  * Deletes sector
  */
 $(document).on("click", ".walletsector_bt_del", async function() {

	let button = $(this);

	let container = button.closest('.w-walletsector');

	let id_walletsector = container.data('id_walletsector');

	Disable(button);

	let yes = async function() {

		let data = {
			action: 'walletsector_delete',
			id_walletsector: id_walletsector
		}

		response = await Post("wallet_sector.php", data);

		if (response != null) {

			ContainerRemove(container, function() {

				if ($('.w-walletsector').length == 0) {

					$('.walletsector_notfound').removeClass('hidden');
				}
			});

			$('.select_id_walletsector').html(response);

		} else {

			Enable(button);
		}

		return true;
	}

	let no = async function() {

		Enable(button);
	}

	MessageBox.Show("Remover setor?", yes, no);
});

/**
  * Shows new walletsector popup
  */
 $(document).on("click", ".walletsector_bt_show_new", async function() {

	let button = $(this);

	Disable(button);

	let data = {
		action: "walletsector_popup_new",
		id_wallet: button.data("id_wallet"),
	}

	let response = await Post("wallet_sector.php", data);

	if (response != null) {

		Modal.Show(Modal.POPUP_SIZE_SMALL, "Cadastro de Setor", response, null, false, "<i class='icon fa-solid fa-square-plus'></i>");
	}

	Enable(button);

	MenuClose();
});