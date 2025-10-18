"use strict";

class PDV {

	static total = 178.65;

	static Show() {

		$('#pdv_total').html("R$ " + this.total.toLocaleString("pt-BR", {minimumFractionDigits: 2, maximumFractionDigits: 2}));
	}
}

function PdvInit(response) {

	$(".pdv-leftmenu").html(response['menu']);
	// $(".leftmenu_container").removeClass("hidden");
	$("#body-container").html(response['data']);
}

/**
  * Form to pdv login
  */
$(document).on("submit", "#frm_pdv_login", async function(event) {
	
	event.preventDefault();

	var id_entidade = this.id_entidade.value;
	var pass = this.pass.value;

	var data = {
		action: "login",
		id_entidade: id_entidade,
		pass: pass
	}

	response = await Post("pdv.php", data)

	if(response) {

		WaiterInit(response);
	}
});

/**
  * Modal to pdv open
  */
 $(document).on("click", ".bt_pdv_open", async function() {
	
	var data = {
		action: "pdv_open_popup",
	}

	console.log(data);
	return;
	response = await Post("pdv.php", data)

	if(response) {

	}
});

/**
 * Event keyup for PDV
 */
$(document).on("keydown", function(event) {

	// console.log(event.keyCode);

	switch (event.keyCode) {

		case 113: // F2

			event.preventDefault();
			console.log("F2");

			break;

		case 114: // F3

			event.preventDefault();
			console.log("F3");

			break;

		case 106: // *

			event.preventDefault();

			if ($("#product_search").is(":focus")) {

				$("#qtd").focus();

			} else if ($("#qtd").is(":focus")) {

				$("#product_search").focus();
			}
			
			console.log("*");

			break;

	}
});