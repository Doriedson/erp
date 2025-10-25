/**
  * Form to login
  */
$(document).on("submit", "#frm_login", async function(event) {

	event.preventDefault();

	const form = $(this);
	FormDisable(form);

	const payload = {
		id_entidade: this.id_entidade.value,
		senha: this.senha.value
	};

	try {
		// faz o login
		const resp = await Post('/auth/login', payload);

		// sucesso: dispara o pós-login único
		await Authenticator.afterLogin();

	} catch (e) {

		const msg = (e && e.responseJSON && e.responseJSON.error)
		? e.responseJSON.error
		: 'Falha no login';
		Message.Show(msg, Message.MSG_ERROR);
		FormEnable(form);

	}
});