/**
* Table transfer
*/
$(document).on("click", ".waitertable_bt_transf", async function(event) {

    let button = $(this);
    let mesa_from = $('.w_waitertabletransf_container');
    let id_mesa_from = mesa_from.data('id_mesa');
    let versao_from = mesa_from.data('versao');
    let id_mesa_to = button.data('id_mesa');
    let versao_to = button.data('versao');

    let data = {
        action: 'waitertable_transf',
        id_mesa_from: id_mesa_from,
        versao_from: versao_from,
        id_mesa_to: id_mesa_to,
        versao_to: versao_to
    }

    Disable(button);

    let response = await Post("waiter_table_transf.php", data);

    if (response != null) {

        WaitertableLoadTableproducts(id_mesa_to, "mesa_desc");

    } else {

        Enable(button);
    }
});