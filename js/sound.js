/**
  * Opens "descricao" edition
  */
$(document).on("click", ".sound_bt_descricao", async function() {

	let button = $(this);

	Disable(button);

	let data = {
		action: "sound_descricao_edit",
		id_som: button.data("id_som")
	}

	let response = await Post("sound.php", data);

	if (response != null) {

		let content = $(response);
		button.replaceWith(content);
		AutoFocus(content);

	} else {

		Enable(button);
	}
});

/**
  * Cancels "descricao" edition
  */
 $(document).on("focusout", "#frm_sound_descricao #sound_descricao", async function() {

	let field = $(this);
	let form = field.closest("form");

	//Prevents focusout on save
	if (field.prop('disabled')) {
		return;
 	}

	Disable(field);
	// field.addClass('loading');

	let data = {
		action: "sound_descricao_cancel",
		id_som: form.data("id_som")
	}

	let response = await Post("sound.php", data);

	if (response != null) {

		form.replaceWith(response);

	} else {

		Enable(field);
	}
});

/**
  * Saves "descricao" edition.
  */
$(document).on("submit", "#frm_sound_descricao", async function(event) {

	event.preventDefault();

	let form = $(this);
	let descricao = this.sound_descricao.value;
	let id_som = form.data("id_som");

	Disable(form);

	let data = {
		action: "sound_descricao_save",
		id_som: id_som,
		descricao: descricao
	}

	let response = await Post("sound.php", data);

	if (response != null) {

		form.replaceWith(response);

	} else {

		Enable(form);
	}
});

/**
  * Plays sound
  */
$(document).on("click", ".sound_bt_play", async function() {

	let button = $(this);

	Disable(button);

	let data = {
		action: "sound_play",
		id_som: button.data("id_som")
	}

	await Post("sound.php", data);

	Enable(button);
});

/**
  * Opens sound file change
  */
$(document).on("click", ".sound_bt_sound", async function() {

	Message.Show("Ainda não é possível alterar o arquivo de som.", Message.MSG_INFO);
	// let button = $(this);

	// Disable(button);

	// let data = {
	// 	action: "sound_play",
	// 	id_som: button.data("id_som")
	// }

	// await Post("sound.php", data);

	// Enable(button);
});

/**
  * Opens "volume" edition
  */
$(document).on("click", ".sound_bt_volume", async function() {

	let button = $(this);

	Disable(button);

	let data = {
		action: "sound_volume_edit",
		id_som: button.data("id_som")
	}

	let response = await Post("sound.php", data);

	if (response != null) {

		let content = $(response);
		button.replaceWith(content);
		AutoFocus(content);

	} else {

		Enable(button);
	}
});

/**
  * Cancels "volume" edition
  */
 $(document).on("focusout", "#frm_sound_volume #sound_volume", async function() {

	let field = $(this);
	let form = field.closest("form");

	//Prevents focusout on save
	if (field.prop('disabled')) {
		return;
 	}

	Disable(field);
	// field.addClass('loading');

	let data = {
		action: "sound_volume_cancel",
		id_som: form.data("id_som")
	}

	let response = await Post("sound.php", data);

	if (response != null) {

		form.replaceWith(response);

	} else {

		Enable(field);
	}
});

/**
  * Saves "volume" edition.
  */
$(document).on("change", "#frm_sound_volume", async function(event) {

	event.preventDefault();

	let form = $(this);
	let volume = this.sound_volume.value;
	let id_som = form.data("id_som");

	Disable(form);

	let data = {
		action: "sound_volume_save",
		id_som: id_som,
		volume: volume
	}

	let response = await Post("sound.php", data);

	if (response != null) {

		form.replaceWith(response);

	} else {

		Enable(form);
	}
});