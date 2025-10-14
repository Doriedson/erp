"use strict";

/**
  * Event to select product.
  */
 $(document).on("submit", "#frm_selfservice_product", async function(event) {

	event.preventDefault();

	let produto = $(this.product_search).data("sku");

	if (!produto) {

		produto = this.product_search.value;
	}

	let form = $(this);

	let container = $('.w-selfservice-qty')

	FormDisable(form);

	let data = {
		action: "product_select",
		id_produto: produto
	}

	let response = await Post("waiter_self_service.php", data);

    FormEnable(form);

	if (response != null) {

        // $('.w-selfservice-product').addClass("hidden");

        this.product_search.value = "";

		let content = $(response);

		container.replaceWith(content);

        $('.w-selfservice-product').addClass('hidden');

		AutoFocus(content);

	} else {

        this.product_search.select();
        // this.product_search.focus();
        AutoFocus(form);
    }
});

/**
  * Event to opens product search.
  */
$(document).on("click", ".selfservice_bt_productsearch", async function(event) {

    $('.w-selfservice-qty').addClass('hidden');
    $('.w-selfservice-product').removeClass('hidden');

    $('#product_search').focus();
});

/**
  * Event to select qty.
  */
 $(document).on("submit", "#frm_selfservice_qtd", async function(event) {

	event.preventDefault();

    let form = $(this);
	let qtd = parseFloat(this.qtd.value);
    let preco = parseFloat(form.data('preco'));
    let id_produto = form.data('id_produto');
    let produto = form.data('produto');
    let produtounidade = form.data('produtounidade');

    let container = $('.w-selfservice-table-popup');

    container.find('#table_produto').html(produto);
    container.find('#table_preco').html(preco.toLocaleString("pt-BR", {minimumFractionDigits: 2, maximumFractionDigits: 2}));
    container.find('#table_produtounidade').html(produtounidade);
    container.find('#table_qtd').html(qtd.toLocaleString("pt-BR", {minimumFractionDigits: 3, maximumFractionDigits: 3}));
    container.find('#table_total').html((qtd * preco).toLocaleString("pt-BR", {minimumFractionDigits: 2, maximumFractionDigits: 2}));

    container.find('#table_search').val('');
    container.removeClass('hidden');
    AutoFocus(container);
});

/**
  * Event to select table.
  */
 $(document).on("click", ".selfservice_table_bt_select", async function() {

    if ($('.w-selfservice-qty').hasClass('hidden')) {

        Message.Show("Selecione um produto!", Message.MSG_INFO);
        $('#product_search').focus();
        return;
    }

    let container = $('.w-waitertable-container');
    let form = $('.w-waitertable-search');

    let field = $('#table_search');
    let form2 = $(".w-selfservice-qty");
	let qtd = parseFloat($(".selfservice_qtd").val());
    let id_produto = form2.data('id_produto');
    let id_mesa = $(this).closest('.waitertable_table').data('id_mesa');
    let versao = $(this).closest('.waitertable_table').data('versao');

    if (isNaN(qtd) || qtd == 0) {

        Message.Show("Digite uma quantidade maior que zero!", Message.MSG_INFO);
        $(".selfservice_qtd").focus();
        return;
    }

    FormDisable(form);

    let data = {
        action: "table_select",
        id_mesa: id_mesa,
        versao: versao,
        id_produto: id_produto,
        qtd: qtd,
    };

    response = await Post("waiter_self_service.php", data);

    FormEnable(form);

	if (response != null) {

        container.html(response);

        $('.w-selfservice-table-popup').addClass('hidden');

        container = $('.w-selfservice-qty');

        container.find('#qtd').val('');
        field.val('');
        AutoFocus(container);

	} else {

        field.focus();
    }
});

/**
  * Closes product qty popup
  */
//  $(document).on("click", ".selfservice_bt_close_table", function() {

// 	$('.w-selfservice-table-popup').addClass("hidden");

//     AutoFocus($('.w-selfservice-qty'));
// });

/**
  * Shows product search
  */
// $(document).on("click", ".selfservice_bt_product_show", function() {

//     $('.w-selfservice-qty').addClass('hidden');
// 	$('.w-selfservice-product').removeClass("hidden");

//     AutoFocus($('.w-selfservice-product'));
// });

/**
 * Event keyup to change qty.
 */
 $(document).on("keyup", ".selfservice_qtd", function(event) {

    if (event.keyCode == 13) {

        $("#table_search").focus();

    } else {

        let container = $('.w-selfservice-qty');

        let price = parseFloat(container.data('preco'));
        let qty = parseFloat(this.value);

        if (isNaN(qty)) {

            qty = 0;
        }

        $('#selfservice_total').html((price * qty).toLocaleString("pt-BR", {minimumFractionDigits: 2, maximumFractionDigits: 2}));
    }
 });
