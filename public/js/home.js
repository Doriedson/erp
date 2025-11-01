async function CPFormEdit(container, button, path) {

	const data = {
		action: 'edit'
	}

	return await FormEdit(container, button, data, path);
}

async function CPFormCancel(container, field, action) {

	var form = field.closest('form');

	data = {
		action: action
	}

	return await FormCancel(container, form, field, data, "home.php");
}

async function CPFormSave(container, form, field, action) {

	let data = {
		action: action,
		value: field.val(),
	}

	return await FormSave(container, form, field, data, "home.php");
}

$(document).on('click', '.bt_load', function () {

  const page = $(this).data('page'); // 'product', 'entity', 'sale_order'

  if (!page) return;

  LoadPage(`${page}.php`);

});


$(document).on("click",".bt_module", async function(event) {

    let button = $(this);

    let module = button.data("module");

    Disable(button);

    switch(module) {

        case "waiter":

            window.open("./garcom");

            break;

        case "digitalmenu":

            window.open("./cardapiodigital");

            break;

        case "updatelog":

            let data = {
                action: "updatelog"
            }

            let response = await Post("home.php", data);

            $(".body-container").html(response.join("<br>"));

    }

    Enable(button);
});

/**
  * Opens "product_expirate_days" edition
  */
$(document).on("click", ".product_bt_expiratedays", async function() {

	const button = $(this);

	CPFormEdit(button, button, '/ui/home/expirations/days');
});

/**
  * Cancel "product_expirate_days" edition.
  */
 $(document).on("focusout", "#frm_product_expiratedays #product_expirate_days", async function() {

	const form = field.closest('form');

	data = {
		action: action
	}

	return await FormCancel(container, form, field, data, "home.php");
	CPFormCancel($(this).closest('form'), $(this), 'cp_expiratedays_cancel');
});

/**
  * Save "product_expirate_days" edition.
  */
$(document).on("submit", "#frm_product_expiratedays", async function(event) {

	event.preventDefault();

	const form = $(this);
	FormDisable(form);

	const table = $('.cp_expdate_table');

    $('.productexpdate_bt_print').addClass('hidden');

	if (table.length) {

		table.html(imgLoading);

		$(".cp_expdate_notfound").addClass('hidden');
	}

	$(".productexpdate_expirated").html(imgLoading);
	$(".productexpdate_toexpirate").html(imgLoading);

	const d = parseInt($('#product_expirate_days').val(), 10);

	if (isNaN(d) || d < 0 || d > 365) {
		Message.Show('Informe um número entre 0 e 365.', Message.MSG_INFO);
		FormEnable(form);
		return;
	}

	try {

		const resp = await Post('/ui/home/expirations/days', { days: d });
		const data = resp?.data || {};

		// Atualiza contadores do topo
		if (typeof data.expirated !== 'undefined') {
			$(".productexpdate_expirated").text(data.expirated);
		}
		if (typeof data.toexpirate !== 'undefined') {
			$(".productexpdate_toexpirate").text(data.toexpirate);
		}

		// Substitui o botão/“chip” de {extra_block_expiratedays}
		if (data.extra_block_expiratedays) {
		// ajuste o seletor exato do local onde o bloco aparece no topo
		// exemplo: o primeiro .box-container de “Controle de Validade…”
		const $box = $('.box-container:has(.box-header i.fa-calendar-days)').first();
		// pega o container que hoje envolve o chip
		$box.find('.addon').first().html(data.extra_block_expiratedays);
		}

		// Caso a lista esteja aberta em algum popup, você pode recarregá-la:
		// opcionalmente: chamar a rota de lista e injetar

		Message.Show('Preferência salva.', Message.MSG_DONE);
	} catch (e) {
		Message.Show('Falha ao salvar preferência.', Message.MSG_ERROR);
	} finally {
		FormEnable(form);
	}
});

/**
  * Event button to print product to expirate list
  */
 $(document).on("click", ".productexpdate_bt_print", async function() {

	var button = $(this);

    Disable(button);

	let data = {
		action: 'cp_expiratedays_print',
	}

	await Post("home.php", data);

    Enable(button);
});

/**
  * Event to show expirate list
  */
$(document).on("click", ".productexpdate_bt_list", async function() {

	let button = $(this);

    Disable(button);

	try {
		// carrega HTML das linhas
		const days = $('#product_expirate_days').val() || '';
		const html = await GET(`/ui/home/expirations?days=${encodeURIComponent(days)}`);

		// monta popup usando o bloco EXTRA_BLOCK_POPUP_CP_EXPDATE (já no template)
		// aqui podemos simplesmente injetar as linhas no contêiner esperado:
		Modal.Show(Modal.POPUP_SIZE_LARGE, 'Produtos com validade próxima',
		`<div class="cp_expdate_table table tbody flex flex-dc">${html}</div>`,
		null, false, '<i class="fa-solid fa-calendar-days"></i>'
		);
	} catch (e) {
		Message.Show('Não foi possível carregar a lista.', Message.MSG_ERROR);
	}

    Enable(button);
});