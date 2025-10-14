/**
  * Toggle active second stock
  */
 $(document).on("click", ".stock_bt_ativo", async function() {

    let button = $(this);

	Disable(button);

    let data = { 
        action: 'stock_toggle_active'
    }

    response = await Post("stock.php", data);

    if (response != null) {

        button.replaceWith(response);

    } else {

        Enable(button);
    }
});