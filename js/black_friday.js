/**
  * Adds date to black friday.
  */
$(document).on("submit", "#frm_blackfriday", async function(event) {

  	event.preventDefault();

  	let form = $(this);

    FormDisable(form);

  	let container = $(".blackfriday_table");

  	let data = {
		action: 'blackfriday_add',
		data: this.data.value,
		desconto: this.desconto.value,
		acumulativo: this.acumulativo.checked
	}

	let response = await Post("black_friday.php", data);

	if (response != null) {

		$(".blackfriday_notfound").addClass('hidden');

        let content = $(response);

        container.append(content);

        ContainerFocus(content, true);

		Modal.Close(form.closest('.popup'));

	} else {

		FormEnable(form);
	}
});

/**
 * Removes blackfriday rule.
 */
 $(document).on("click", ".blackfriday_rule_del", async function() {

	var button = $(this);

	Disable(button);

	var data = {
		action: 'blackfriday_del',
		id_blackfriday: button.data('id_blackfriday')
	}

	var response = await Post("black_friday.php", data);

	if (response != null) {

		ContainerRemove(button.closest('.w-blackfriday'), function() {

			if ($('.w-blackfriday').length == 0) {

				$('.blackfriday_notfound').removeClass('hidden');
			}
		});

	} else {

		Enable(button);
	}
});

/**
  * Shows new blackfriday popup
  */
 $(document).on("click", ".blackfriday_bt_show_new", async function() {

	let data = {
		action: "blackfriday_popupnew"
	}

	let response = await Post("black_friday.php", data);

	if (response != null) {

		Modal.Show(Modal.POPUP_SIZE_SMALL, "Cadastro de Data", response, null);
	}
	// $('.w-blackfriday-new-popup').removeClass("hidden");

	// AutoFocus($('.w-blackfriday-new-popup'));
});