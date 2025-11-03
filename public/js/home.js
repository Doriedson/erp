async function CPFormEdit(container, button, path) {

	const data = {
		action: 'edit'
	};

	const context = resolveExpirationContext($(button));

	if (context) {
		data.context = context;
	}

	return await FormEdit(container, button, data, path);
}

async function CPFormCancel(container, field, action) {

	const form = field.closest('form');
	const data = {
		action: action
	};

	const context = resolveExpirationContext(form);

	if (context) {
		data.context = context;
	}

	return await FormCancel(container, form, field, data, '/ui/home/expirations/days');
}

async function CPFormSave(container, form, field, action) {

	let data = {
		action: action,
		value: field.val(),
	};

	const context = resolveExpirationContext(form);

	if (context) {
		data.context = context;
	}

	return await FormSave(container, form, field, data, '/ui/home/expirations/days');
}

function resolveExpirationContext(element) {

	const $element = element && element.jquery ? element : $(element);

	if ($element.closest('.popup').length > 0) {
		return 'list';
	}

	return $('.cp_expdate_table').length > 0 ? 'list' : '';
}

function updateExpirationChip(html) {

	if (typeof html !== 'string' || !html.trim().length) {
		return;
	}

	const tpl = $(html);

	$('#frm_product_expiratedays, .product_bt_expiratedays').each(function() {
		$(this).replaceWith(tpl.clone());
	});
}

async function refreshExpirationList(popup, payload, fallbackDays) {

	const $popup = (popup && popup.length) ? popup : $('.popup:has(.cp_expdate_table)').last();

	if (!$popup.length) {
		return false;
	}

	const table = $popup.find('.cp_expdate_table');
	const notFoundBlock = $popup.find('.cp_expdate_notfound');
	const printButton = $popup.find('.productexpdate_bt_print');
	const daysField = $popup.find('#product_expirate_days');

	let usedPayload = false;

	if (payload) {

		if (typeof payload.extra_block_cp_expdate_tr === 'string' && table.length) {
			table.html(payload.extra_block_cp_expdate_tr);
			usedPayload = true;
		}

		if (typeof payload.cp_expdate_notfound === 'string' && notFoundBlock.length) {
			if (payload.cp_expdate_notfound === 'hidden') {
				notFoundBlock.addClass('hidden');
			} else {
				notFoundBlock.removeClass('hidden');
			}
			usedPayload = true;
		}

		if (typeof payload.productexpdate_bt_print === 'string' && printButton.length) {
			if (payload.productexpdate_bt_print === 'hidden') {
				printButton.addClass('hidden');
			} else {
				printButton.removeClass('hidden');
			}
			usedPayload = true;
		}

		if (typeof payload.product_expirate_days !== 'undefined' && daysField.length) {
			daysField.val(payload.product_expirate_days);
		}
	}

	if (usedPayload) {
		return true;
	}

	return await fetchExpirationListHtml($popup, payload?.days ?? fallbackDays);
}

async function fetchExpirationListHtml(popup, days) {

	let target;

	if (typeof days === 'number' && !Number.isNaN(days)) {
		target = days;
	} else {
		const fallback = parseInt($('#product_expirate_days').val(), 10);
		target = Number.isNaN(fallback) ? '' : fallback;
	}

	try {
		const html = await GET(`/ui/home/expirations?days=${encodeURIComponent(target)}`);

		if (typeof html !== 'string' || !html.trim()) {
			return false;
		}

		const $popup = (popup && popup.length) ? popup : $('.popup:has(.cp_expdate_table)').last();

		if (!$popup.length) {
			return false;
		}

		const body = $popup.find('.window-body');

		if (body.length) {
			body.html(html);
			return true;
		}

		const container = $popup.find('.popup-container');

		if (container.length) {
			container.html(html);
			return true;
		}

		$popup.html(html);
		return true;

	} catch (err) {
		console.error('fetchExpirationListHtml failed', err);
		return false;
	}
}

async function syncExpirationWidgets(originElement, options = {}) {

	const $origin = originElement && originElement.jquery ? originElement : $(originElement || null);
	const context = resolveExpirationContext($origin);
	const isListContext = context === 'list';
	const popup = isListContext
		? ($origin.closest('.popup').length ? $origin.closest('.popup') : $('.popup:has(.cp_expdate_table)').last())
		: $('.popup:has(.cp_expdate_table)').last();

	const table = isListContext ? popup.find('.cp_expdate_table') : $('.cp_expdate_table');
	const notFoundBlock = isListContext ? popup.find('.cp_expdate_notfound') : $('.cp_expdate_notfound');
	const printButton = isListContext ? popup.find('.productexpdate_bt_print') : $('.productexpdate_bt_print');

	if (options.loading && isListContext) {
		if (table.length) {
			table.html(imgLoading);
		}
		if (notFoundBlock.length) {
			notFoundBlock.addClass('hidden');
		}
		if (printButton.length) {
			printButton.addClass('hidden');
		}
	}

	try {
		const payload = { action: 'update' };

		if (context) {
			payload.context = context;
		}

		const data = await Post('/ui/home/expirations/days', payload);

		if (!data) {
			return false;
		}

		if (typeof data.expirated !== 'undefined') {
			$('.productexpdate_expirated').text(data.expirated);
		}

		if (typeof data.toexpirate !== 'undefined') {
			$('.productexpdate_toexpirate').text(data.toexpirate);
		}

		updateExpirationChip(data.extra_block_expiratedays);

		if (isListContext) {
			const fallbackDays = typeof data.days === 'number'
				? data.days
				: parseInt($('#product_expirate_days').val(), 10);

			await refreshExpirationList(popup, data, Number.isNaN(fallbackDays) ? undefined : fallbackDays);
		}

		return true;

	} catch (err) {
		console.error('syncExpirationWidgets failed', err);
		return false;
	}
}

$(document).on('click', '.bt_load', function () {

	const page = $(this).data('page'); // 'product', 'entity', 'sale_order'

	if (!page) return;

	LoadPage(`${page}.php`);

});

$(document).on('click', '.bt_module', async function() {

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
			};

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
	await CPFormCancel($(this).closest('form'), $(this), 'cp_expiratedays_cancel');
});

/**
  * Save "product_expirate_days" edition.
  */
$(document).on("submit", "#frm_product_expiratedays", async function(event) {

	event.preventDefault();

	const form = $(this);
	const context = resolveExpirationContext(form);
	const hasListContext = context === 'list';
	const popup = hasListContext ? form.closest('.popup') : $('.cp_expdate_table').closest('.popup');
	const table = hasListContext ? popup.find('.cp_expdate_table') : $('.cp_expdate_table');
	const notFoundBlock = hasListContext ? popup.find('.cp_expdate_notfound') : $('.cp_expdate_notfound');
	const printButton = hasListContext ? popup.find('.productexpdate_bt_print') : $('.productexpdate_bt_print');
	const field = form.find('#product_expirate_days');

	FormDisable(form);

	if (hasListContext) {
		printButton.addClass('hidden');
		if (table.length) {
			table.html(imgLoading);
		}
		if (notFoundBlock.length) {
			notFoundBlock.addClass('hidden');
		}
	}

	$('.productexpdate_expirated').html(imgLoading);
	$('.productexpdate_toexpirate').html(imgLoading);

	const d = parseInt(field.val(), 10);

	if (isNaN(d) || d < 0 || d > 365) {
		Message.Show('Informe um número entre 0 e 365.', Message.MSG_INFO);
		FormEnable(form);
		return;
	}

	const payload = { days: d };

	if (context) {
		payload.context = context;
	}

	try {

		const resp = await Post('/ui/home/expirations/days', payload);
		const data = resp || {};

		if (typeof data.expirated !== 'undefined') {
			$('.productexpdate_expirated').text(data.expirated);
		}
		if (typeof data.toexpirate !== 'undefined') {
			$('.productexpdate_toexpirate').text(data.toexpirate);
		}

		updateExpirationChip(data.extra_block_expiratedays);

		if (hasListContext) {
			const refreshed = await refreshExpirationList(popup, data, d);

			if (!refreshed) {
				if (table.length) {
					table.html('');
				}
				if (notFoundBlock.length) {
					notFoundBlock.removeClass('hidden');
				}
				if (printButton.length) {
					printButton.addClass('hidden');
				}
			}
		}

		Message.Show('Preferência salva.', Message.MSG_DONE);
	} catch (e) {
		if (hasListContext) {
			if (table.length) {
				table.html('');
			}
			if (notFoundBlock.length) {
				notFoundBlock.removeClass('hidden');
			}
			if (printButton.length) {
				printButton.addClass('hidden');
			}
		}
		Message.Show('Falha ao salvar preferência.', Message.MSG_ERROR);
	} finally {
		if (form.length && $.contains(document, form[0])) {
			FormEnable(form);
		}
	}
});

/**
  * Remove validade dentro da lista de próximos vencimentos.
  */
$(document).on('click', '.cp_expdate_table .product_bt_validade_delete', async function(event) {

	event.preventDefault();
	event.stopImmediatePropagation();

	const button = $(this);
	const id = button.data('id_produtovalidade');

	if (!id) {
		return;
	}

	Disable(button);
	MenuClose();

	try {
		const response = await Post('/ui/products/expirations/delete', {
			id_produtovalidade: id
		});

		if (response == null) {
			return;
		}

		if (typeof response.expirated !== 'undefined') {
			$('.productexpdate_expirated').text(response.expirated);
		}

		if (typeof response.toexpirate !== 'undefined') {
			$('.productexpdate_toexpirate').text(response.toexpirate);
		}

		const synced = await syncExpirationWidgets(button, { loading: true });

		if (!synced) {
			const row = button.closest('.cp_expdate_tr');
			if (row.length) {
				ContainerRemove(row, function() {
					if ($('.cp_expdate_tr').length === 0) {
						$('.cp_expdate_notfound').removeClass('hidden');
						$('.productexpdate_bt_print').addClass('hidden');
					}
				});
			}
			Message.Show('Não foi possível atualizar a lista automaticamente.', Message.MSG_ALERT);
		}

	} finally {
		Enable(button);
	}
});

/**
  * Abre controle de validade direto da lista.
  */
$(document).on('click', '.cp_expdate_table .product_bt_validade', async function(event) {

	event.preventDefault();
	event.stopImmediatePropagation();

	const button = $(this);
	const idProduto = button.data('id_produto');

	if (!idProduto) {
		return;
	}

	Disable(button);
	MenuClose();

	try {
		const response = await Post('/ui/products/expirations/modal', {
			id_produto: idProduto
		});

		if (response != null) {
			Modal.Show(
				Modal.POPUP_SIZE_SMALL,
				'Controle de Validade',
				response,
				null,
				false,
				"<i class='icon fa-solid fa-calendar-days'></i>"
			);
		}
	} finally {
		Enable(button);
	}
});

/**
  * Histórico de venda a partir da lista.
  */
$(document).on('click', '.cp_expdate_table .bt_purchaseorder_history', async function(event) {

	event.preventDefault();
	event.stopImmediatePropagation();

	const button = $(this);
	const idProduto = button.data('id_produto');

	if (!idProduto) {
		return;
	}

	Disable(button);
	MenuClose();

	try {
		if (Modal.history_productsale.datelock === false) {
			const lastEntry = await Post('/ui/products/history/last-entry', {
				id_produto: idProduto
			});

			if (lastEntry == null) {
				Message.Show('Erro ao carregar data do histórico de venda!', Message.MSG_ERROR);
				return;
			}

			Modal.history_productsale.datestart = lastEntry['datestart'];
			Modal.history_productsale.dateend = lastEntry['dateend'];
			Modal.history_productsale.dateend_sel = lastEntry['dateend_sel'];
		}

		const payload = {
			id_produto: idProduto,
			datestart: Modal.history_productsale.datestart,
			dateend_sel: Modal.history_productsale.dateend_sel,
			dateend: Modal.history_productsale.dateend,
			datelock: Modal.history_productsale.datelock
		};

		const response = await Post('/ui/products/history/popup', payload);

		if (response != null) {

			Modal.Show(
				Modal.POPUP_SIZE_SMALL,
				'Histórico de Venda do Produto',
				response,
				null,
				false,
				"<i class='icon fa-solid fa-chart-column'></i>"
			);

			UpdateHistoryProductsaleLock();
			$('#frm_purchaseorder_reportsaleoneproduct').submit();
		}

	} finally {
		Enable(button);
	}
});

/**
  * Event button to print product to expirate list
  */
$(document).on("click", ".productexpdate_bt_print", async function() {

	const button = $(this);

    Disable(button);

	let data = {
		action: 'cp_expiratedays_print',
	};

	await Post("home.php", data);

    Enable(button);
});

/**
  * Event to show expirate list
  */
$(document).on("click", ".productexpdate_bt_list", async function() {

	const button = $(this);

    Disable(button);

	try {
		const days = $('#product_expirate_days').val() || '';
		const html = await GET(`/ui/home/expirations?days=${encodeURIComponent(days)}`);

		if (typeof html !== 'string' || !html.trim()) {
			Message.Show('Nenhum dado para exibir.', Message.MSG_INFO);
			return;
		}

		Modal.Show(
			Modal.POPUP_SIZE_LARGE,
			'Produtos com validade próxima',
			html,
			null,
			false,
			'<i class="fa-solid fa-calendar-days"></i>'
		);
	} catch (e) {
		Message.Show('Não foi possível carregar a lista.', Message.MSG_ERROR);
	}

    Enable(button);
});

$(document).on('product:validity-updated', async function(event, meta) {

	if (!$('.cp_expdate_table').length) {
		return;
	}

	if (meta && meta.skipHomeSync) {
		return;
	}

	if (meta && meta.response == null) {
		return;
	}

	await syncExpirationWidgets($('.cp_expdate_table'), { loading: true });
});
