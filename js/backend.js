/**
  * Form to login
  */
$(document).on("submit", "#frm_backend_login", async function(event) {
	
	event.preventDefault();

	let id_entidade = this.id_entidade.value;
	let pass = this.pass.value;

	let data = {
		action: "login",
		id_entidade: id_entidade,
		pass: pass,
		module: $('.body-container').data('module')
	}

	let response = await Post("backend.php", data)

	if(response) {

		$(".leftmenu_container").html(response['data']);
		$(".leftmenu_container").removeClass("hidden");
		$(".body-header").removeClass('hidden');
		$("#body-container").html("");

		localStorage.setItem("id_entidade", id_entidade);

		if (response['page']) {

			LoadPage(response['page']);

		} else {

			LoadPage('home.php');
		}

		observerStart.notify("login");
	}
});