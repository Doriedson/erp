let imgLoading = "<div class='flex flex-ai-center flex-jc-center'><i class='fa-solid fa-rotate fa-spin font-size-20'></i><div>";
let warnings = 0;

class WindowManager {

	static page = null;
}

class Modal {

	static POPUP_SIZE_SMALL = 0;
	static POPUP_SIZE_LARGE = 1;
	static POPUP_BUTTONFIX = true;

	static window = null;

	static handles = [];

	static history_productsale = {
		datelock: false,
		datestart: null,
		dateend_sel: false,
		dateend: null
	}

	constructor(window_size, title, content, cancel_func, button_fix = false, title_icon = "") {

		if(!Modal.window) {

			console.log("Modal is null!");
			return;
		}

		this.window = Modal.window.clone();
		this.content = this.window.find('.popup-container');
		this.title = this.window.find('.popup-title');
		this.title_icon = this.window.find('.popup-title-icon');
		this.body = this.window.find('.window-body');
		this.fixwindow = this.window.find('.popup_fixwindow');
		this.cancel_func = cancel_func;

		$('body').append(this.window);

		this.title.html(title);
		this.title_icon.html(title_icon);
		this.body.html(content);

		if (button_fix == Modal.POPUP_BUTTONFIX) {

			this.fixwindow.removeClass('hidden');
		}

		switch (window_size) {

			case Modal.POPUP_SIZE_LARGE:

				this.content.addClass('popup-large');
				break;

			case Modal.POPUP_SIZE_SMALL:

				this.content.addClass('popup-small');
				break;
		}

		this.window.removeClass('hidden');

		AutoFocus(this.body);
	}

	static Show(window_size, title, content, cancel_func, button_fix = false, title_icon = "") {

		Modal.handles.push(new Modal(window_size, title, content, cancel_func, button_fix, title_icon));
	}

	// Show() {

	// 	this.window.removeClass('hidden');
	// }

	static Cancel(popup) {

		Modal.handles.forEach((element, index) => {

			if (popup.is(element.window)) {

				if (element.cancel_func != null) {

					element.cancel_func();
				}
			}
		});

		Modal.Close(popup);
	}

	static CloseAround(popup) {

		$('.popup').each(function() {

			if(!$(this).is(popup)) {

				$(this).remove();
			}
		});
	}

	static CloseAll() {

		$('.popup').remove();

		Modal.handles = [];
	}

	static Close(popup) {

		Modal.handles.forEach((element, index) => {

			if (popup.is(element.window)) {

				Modal.handles.splice(index,1);
			}
		});

		popup.remove();
	}
}

class Printer {

	static Print(content) {

		$(".printable").html(content);

		window.scrollTo(0, 0);
		window.print();

		$(".printable").html("");
	}
}

class Observable {

	constructor() {

		this.observers = [];
	}

	subscribe(f) {

		this.observers.push(f);
	}

	unsubscribe(f) {

		this.observers = this.observers.filter(subscriber => subscriber !== f);
	}

	notify(data) {

		this.observers.forEach(observer => observer.update(data));
	}
}

function currentModule() {

  const p = location.pathname.replace(/\/+$/, '');
  if (p === '/pdv')    return 'pdv';
  if (p === '/garcom') return 'waiter'; // ou 'garcom'
  return 'backend';
}

class Authenticator {

	static _statusCache = { ts: 0, value: { authenticated: false, user: null } };
  	static _statusTTLms = 10000; // 10s de cache (ajuste se quiser)
	static _didAfterLogin = false; // <-- guarda de execução única

	// static window = null;
	static data = {};
	static url = null;
	static success = null;
	static error = null;
	static cancel = null;

	static async afterLogin() {

		$('#auth_container').remove();
		$('#body-container').empty();

		if (this._didAfterLogin) return;   // evita rodar 2x
		this._didAfterLogin = true;

		await this.status(true);            // invalida o cache e confirma auth

		Message.Show('Autenticado com sucesso.', Message.MSG_DONE);

		// segue para o backend (menu + home)
		await this.loadBackendMenu();
	}

	// ---- STATUS ----
	static async status(force = false) {

		const now = Date.now();

		if (!force && (now - this._statusCache.ts) < this._statusTTLms) {

			return this._statusCache.value;
		}

		try {

			const r = await GET_JSON('/auth/status');     // << JSON!
			// r já é data normalizada por Treat_Receive_Success
			const v = r ? r : { authenticated: false, user: null };
			this._statusCache = { ts: now, value: v };
			return v;

		} catch {

			this._statusCache = { ts: now, value: { authenticated: false, user: null } };
			return this._statusCache.value;
		}
	}

	static async isAuthenticated(force = false) {

		const s = await this.status(force);
		return !!(s && s.authenticated);
	}

	static async showLogin(cancel = null) {

		try {
			const html = await GET_HTML('/login'); // <- HTML puro
			$('#body-container').html(html);
			$('.leftmenu_container').addClass('hidden').empty();
			$('.body-header').addClass('hidden');

		} catch (e) {

			Message.Show('Falha ao carregar tela de login.', Message.MSG_ERROR);
		}
	}

	// ---- GARANTIR AUTENTICAÇÃO (fluxo único) ----
	static async ensure({ anonymous = false, cancel = null } = {}) {

		if (anonymous) return true;

		const ok = await this.isAuthenticated();

		if (ok) return true;

		await this.showLogin(cancel);

		return false; // o submit do form vai continuar o fluxo
	}

	// ---- MONTAGEM DO BACKEND ----
	static async loadBackendMenu() {

		try {

			const html = await GET_HTML('/ui/backend/menu');

			const $menu = $('.leftmenu_container');

			$menu.removeClass('hidden').html(html);

			$('.body-header').removeClass('hidden');
			$('#body-container').empty();

			const home = await GET('/ui/pages/home'); // dataType: 'text'
			$('#body-container').html(home);

		} catch (e) {

			console.error('Falha ao carregar menu:', e);
			Message.Show('Não foi possível carregar o menu.', Message.MSG_ERROR);
		}
	}

	// ---- BOOT ÚNICO DA PÁGINA ----
	static async bootstrap() {

		// 1) mensagens (mantém compat com seu backend atual)
		try {
			const msg = await Post('/message.php', { action: 'load' });
			const m = msg && msg.data ? msg.data : msg;
			if (m) {
				Message.Set(m["message_info"],  Message.MSG_INFO);
				Message.Set(m["message_error"], Message.MSG_ERROR);
				Message.Set(m["message_done"],  Message.MSG_DONE);
				Message.Set(m["message_alert"], Message.MSG_ALERT);
				Modal.window      = $(m["popup"]);
				MessageBox.window = $(m["messagebox"]);
			}
		} catch {}

		this.showWelcome();

		// 2) Após 500ms, decide o que mostrar
		setTimeout(async () => {

			const s = await this.status();

			this.hideWelcome();

			if (s.authenticated) {

				const module = currentModule();

				switch (module) {

					case 'backend':
						await this.loadBackendMenu();
						break;

					case 'waiter':
						if (typeof loadWaiter === 'function') await loadWaiter();
						else Message.Show('Módulo do garçom não implementado.', Message.MSG_INFO);
						break;

					case 'pdv':
						if (typeof loadPdv === 'function') await loadPdv();
						else Message.Show('Módulo do PDV não implementado.', Message.MSG_INFO);
						break;

					default:

						Message.Show('Módulo não definido.', Message.MSG_ERROR);
				}

			} else {

				await this.showLogin();   // <-- login (página), diferente do autenticador/popup
			}

		}, 500);

	}

	// Mostra a tela de boas-vindas (container já existente no index.tpl)
	static showWelcome() {
		$('.splash-screen').removeClass('hidden');   // ajuste o ID/classe conforme seu tpl
	}

	// Oculta a tela de boas-vindas
	static hideWelcome() {
		$('.splash-screen').addClass('hidden');
	}

	// static async Authenticate(data, url, success, error, cancel, anonymous = false) {

	// 	this.data = data;
	// 	this.url = url;
	// 	this.success = success;
	// 	this.error = error;
	// 	this.cancel = cancel;

	// 	if (!anonymous) {

	// 		const ok = await IsAuthenticated();

	// 		if (!ok) {

	// 			const html = await GetAuthPopupHtml(); // pega o form existente
	// 			Modal.Show(Modal.POPUP_SIZE_SMALL, 'Autorização', html, cancel);
	// 			return; // aguarda o usuário logar; o submit vai disparar a continuação
	// 		}
	// 	}

	// 	try {

	// 		const resp = await Post(url, data); // seu Post existente
	// 		if (typeof success === 'function') success(resp);

	// 	} catch (e) {

	// 		if (typeof error === 'function') error(e);
	// 	}
	// }
}

// let authenticator = new Authenticator();

function setCookie(cname, cvalue, exdays = 365) {

	const d = new Date();

	d.setTime(d.getTime() + (exdays*24*60*60*1000));

	let expires = "expires="+ d.toUTCString();

	document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function isAbsoluteUrl(u) {
  return /^https?:\/\//i.test(u || '');
}

function cleanPath(u) {
  return String(u || '').trim();
}

async function GetAuthPopupHtml() {

  const r = await GET_JSON('/ui/auth/popup');
  const v = r && r.data ? r.data : r;
  return v.html || '';
}

/**
 * Form to authenticate
*/
$(document).on("submit", "#frm_authenticator", async function(event) {

	event.preventDefault();

	const form = $(this);

	FormDisable(form);

	const payload = {
		id_entidade: this.id_entidade.value,
		senha: this.senha.value
	}

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
		FormEnable($form);
	}
});

class MessageBox {

	static #yes = async function() {};
	static #no = async function() {};
	static window = null;

	static Show(message, yes, no, obs = false) {

		this.#yes = yes;
		this.#no = no;

		$('body').append(this.window);

		let msg = this.window.find('.messagebox_message');

		msg.html(message);

		if (obs == false) {

			this.window.find('.messagebox_obs').addClass("hidden");

		} else {

			this.window.find('#messagebox_obs').val("");
			this.window.find('.messagebox_obs').removeClass("hidden");
		}

		this.window.removeClass('hidden');

		if (obs == true) {

			AutoFocus(this.window);
		}
	}

	static async Yes() {

		Disable($(".messagebox_bt_yes"));
		Disable($(".messagebox_bt_no"));

		let ret = await this.#yes();

		Enable($(".messagebox_bt_yes"));
		Enable($(".messagebox_bt_no"));

		if (ret == true) {

			this.Close();
		}
	}

	static async No() {

		Disable($(".messagebox_bt_yes"));
		Disable($(".messagebox_bt_no"));

		await this.#no();

		Enable($(".messagebox_bt_yes"));
		Enable($(".messagebox_bt_no"));

		this.Close();
	}

	static Close() {

		this.window.addClass('hidden');
		this.window.remove();
	}
}

/**
 * Event for yes from MessageBox
*/
$(document).on("click", ".messagebox_bt_yes", async function() {

	await MessageBox.Yes();
});

/**
 * Event for no from MessageBox
*/
$(document).on("click", ".messagebox_bt_no", async function() {

	await MessageBox.No();
});

class Message {

	static MSG_INFO = 0;
	static MSG_ERROR = 1;
	static MSG_DONE = 2;
	static MSG_ALERT = 3;

	static messages = [];

	static queue = [];
	static queue_timer = null;

	static Set(message_prototype, type) {

		Message.messages[type] = message_prototype;
	}

	static Show(message, type) {

		Message.queue.push([message, type]);

		if (Message.queue_timer == null) {

			Message.queue_timer = 0;
			Message.Dequeue();
		}
	}

	static Dequeue() {

		if (Message.queue.length == 0) {

			Message.queue_timer = null;
			return;
		}

		let [message, type] = Message.queue.shift(1);

		let msg = Message.messages[type];
		let content = $(msg.replace("{mensagem}", message));

		$(".message-container").prepend(content);

		content.slideDown("fast");

		let timeout = setTimeout(function() {

			content.css("animation-name", "bounceOutRight");

			setTimeout(function() {

				content.slideUp();
			}, 200);

			setTimeout(function() {

				content.remove();
			},1000);

		}, 5000);

		content.data("timeout", timeout);

		Message.queue_timer = setTimeout(function() {

			Message.Dequeue();
		}, 1000);
	}
}

function Treat_Receive_Success(response, status, request, url) {

console.log(url);

	// 202 = aceito e nada a fazer
  	if (request.status === 202) return null;

	try {
		// Detectar HTML/texto (ex.: /ui/login, /ui/backend/menu)
		// const ct = (request.getResponseHeader('Content-Type') || '').toLowerCase();

		if (typeof response === 'string') { //} || ct.includes('text/html')) {

			return response; // não tenta ler messages nem version
		}

		// Se é objeto (JSON), normaliza campos opcionais
		const data      = response?.data ?? null;
		const messages  = Array.isArray(response?.messages) ? response.messages : [];
		const srvVer   = response?.version;

		// Renderiza mensagens se existirem (apenas informativas)
		for (const m of messages) {
			Message.Show(m.text || String(m), Message.MSG_INFO);
		}

		// Verificação de versão (somente se houver variável global version definida)
		// e apenas quando a resposta tem version (evita loop de reload quando é HTML).
		if (typeof srvVer !== 'undefined' && typeof window.version !== 'undefined') {
		if (srvVer != window.version) {
			Message.Show('O sistema está sendo atualizado...', Message.MSG_INFO);
			setTimeout(() => window.location.reload(true), 1000);
			return null;
		}
		}

		// Compat: headers de autorização (legado)
		if (data && data['logged']) {
			const token = request.getResponseHeader('Authorization');
			if (token) localStorage.setItem('token', token);
		}

		return data ?? response;

	} catch (e) {

		console.error('Treat_Receive_Success falhou:', e);
		Message.Show('Erro ao tratar resposta do servidor.', Message.MSG_ERROR);
		return null;
	}
}

function Treat_Receive_Error(data, request, url) {

	switch (request.status) {

		case 302:

			console.log("302");

			break;

		case 401:

			localStorage.removeItem('token');
			Authenticator.status(true);
			Authenticator.showLogin();
			Message.Show('Sessão expirada. Faça login novamente.', Message.MSG_INFO);
			return null;

			break;

		case 410:

			if (localStorage.getItem("token")) {

				Message.Show("Sessão finalizada!", Message.MSG_INFO);
			}

			Logout();

			break;

		default:

			console.log("ERROR " + request.status);

			break;
	}

	return null;
}

async function Post(url, data, processData = true, contentType = 'application/x-www-form-urlencoded; charset=UTF-8') {

	let ret = null;

	try {

		await $.ajax({
			url: url,
			type: "POST",
			data: data,
			dataType: 'json',
			cache: processData,
			processData: processData,
			contentType: contentType,
			headers: {"Authorization": localStorage.getItem('token')},
			// dataType: "JSON",
			success: function(data, status, request) {

				ret = Treat_Receive_Success(data, status, request, url);

			},
			error: function(request) {

				ret = Treat_Receive_Error(data, request, url);
			}
		});
	}
	catch (e) {

		switch (e.status) {

			case 302:
			// Logout();
			break;

			case 410:
			break;

			case 401:
				console.log(data);
			break;

			default:
				Message.Show("Ocorreu um erro na comunicação com o Servidor! [POST] [" + e.status + "]", Message.MSG_ERROR);
			break;
		}
	}

	return ret;
}

function ShowLogin() {

  $("#auth_container").removeClass("hidden");   // onde está o <form id="frm_authenticator"...>
  $("#id_entidade").focus();
}


async function GET(url, data, contentType = 'application/x-www-form-urlencoded; charset=UTF-8') {

	let ret = null;

	try {

		await $.ajax({
			url: url,
			type: "GET",
			data: data,
			contentType: contentType,
			headers: {"Authorization": localStorage.getItem('token')},
			cache: false,
			success: function(data, status, request) {

				ret = Treat_Receive_Success(data, status, request, url);

			},
			error: function(request) {

				ret = Treat_Receive_Error(data, request, url);
			}
		});
	}
	catch (e) {

		switch (e.status) {

			case 302:
			// Logout();
			break;

			case 410:
			break;

			case 401:
			break;

			default:
				Message.Show("Ocorreu um erro na comunicação com o Servidor! [GET] [" + e.status + "]", Message.MSG_ERROR);
			break;
		}
	}

	return ret;
}

async function GET_JSON(url, data) {
  let ret = null;
  try {
    await $.ajax({
      url,
      type: 'GET',
      data,
      dataType: 'json',              // << quer JSON
      headers: { "Authorization": localStorage.getItem('token') },
      cache: false,
      success: function (data, status, request) {
        ret = Treat_Receive_Success(data, status, request, url);
      },
      error: function (request) {
        ret = Treat_Receive_Error(data, request, url);
      }
    });
  } catch (e) {
    Message.Show(`Erro de comunicação [GET_JSON] [${e.status||'?'}]`, Message.MSG_ERROR);
  }
  return ret;
}

async function GET_HTML(path, data) {

	const url = legacyToUi(path);

	let ret = null;

	try {
		await $.ajax({
		url,
		type: 'GET',
		data,
		dataType: 'text',              // << quer HTML
		headers: { "Authorization": localStorage.getItem('token') },
		cache: false,
		success: function (data, status, request) {
			ret = Treat_Receive_Success(data, status, request, url);
		},
		error: function (request) {
			ret = Treat_Receive_Error(data, request, url);
		}
		});
	} catch (e) {
		Message.Show(`Erro de comunicação [GET_HTML] [${e.status||'?'}]`, Message.MSG_ERROR);
	}
	return ret;
}

async function CEPSearch(cep) {

	//Expressão regular para validar o CEP.
	let validacep = /^[0-9]{8}$/;

	let ret = null;

	//Valida o formato do CEP.
	if(validacep.test(cep)) {

		// let response = await GET("https://viacep.com.br/ws/" + cep + "/json/");

		// console.log(response);

		// return;
		await $.getJSON("https://viacep.com.br/ws/"+ cep +"/json/", function(dados) {

			if (!("erro" in dados)) {
				//Atualiza os campos com os valores da consulta.
				// $("#rua").val(dados.logradouro);
				// $("#bairro").val(dados.bairro);
				// $("#cidade").val(dados.localidade);
				// $("#uf").val(dados.uf);
				// $("#ibge").val(dados.ibge);
				// console.log(dados);
				ret = dados;

			} else {
				//CEP pesquisado não foi encontrado.
				// alert("CEP não encontrado.");
				Message.Show(cep + " - CEP não localizado.", Message.MSG_ERROR);
			}
		});

	} else {

		if (cep != "") {

			Message.Show(cep + " - CEP inválido.", Message.MSG_ERROR);
		}
	}

	return ret;
}

async function CEPSearchAddress(logradouro, cidade, uf) {

	let ret = null;

	await $.getJSON("https://viacep.com.br/ws/" + uf + "/" + cidade + "/" + logradouro + "/json/", function(dados) {

		if (!("erro" in dados)) {
			//Atualiza os campos com os valores da consulta.
			// $("#rua").val(dados.logradouro);
			// $("#bairro").val(dados.bairro);
			// $("#cidade").val(dados.localidade);
			// $("#uf").val(dados.uf);
			// $("#ibge").val(dados.ibge);
			// console.log(dados);
			if (dados.length == 0) {

				Message.Show("Endereço não localizado:<br>" + logradouro + " - " + cidade + " - " + uf, Message.MSG_INFO);

			} else {

				ret = dados;
			}

		} else {
			//CEP pesquisado não foi encontrado.
			// alert("CEP não encontrado.");
			Message.Show("Endereço não localizado:<br>" + logradouro + " - " + cidade + " - " + uf, Message.MSG_INFO);
		}
	});

	return ret;
}

/**
  * Closes message
  */
$(document).on("click", ".message_bt_close", function() {

	let button = $(this);

	clearTimeout(button.data("timeout"));

	button.css("animation-name", "bounceOutRight");

	setTimeout(function() {

		button.slideUp();
	}, 200);

	setTimeout(function() {

		button.remove();
	}, 1000);


	// $(this).remove();
});

async function Logout() {

  // 1) tenta encerrar no servidor (ignora falhas/401)
  try { await Post('/auth/logout', {}); } catch(e) {}

  // 2) limpa UI
  $('.leftmenu_container').addClass('hidden').empty();
  $('.body-header').addClass('hidden');

  // 3) limpa estado local
  localStorage.removeItem('token');
  // se quiser manter o último colaborador selecionado:
  // localStorage.removeItem('id_entidade');

  // 4) invalida cache de status e mostra a tela de login
  await Authenticator.status(true);
  await Authenticator.showLogin();
}


Number.prototype.formatMoney = function(decPlaces, thouSeparator, decSeparator) {
    var n = this,
    decPlaces = isNaN(decPlaces = Math.abs(decPlaces)) ? 2 : decPlaces,
    decSeparator = decSeparator == undefined ? "," : decSeparator,
    thouSeparator = thouSeparator == undefined ? "." : thouSeparator,
    sign = n < 0 ? "-" : "",
    i = parseInt(n = Math.abs(+n || 0).toFixed(decPlaces)) + "",
    j = (j = i.length) > 3 ? j % 3 : 0;
    return sign + (j ? i.substr(0, j) + thouSeparator : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thouSeparator) + (decPlaces ? decSeparator + Math.abs(n - i).toFixed(decPlaces).slice(2) : "");
};

/**
 * Shows animation on tr focus.
 */
 function ContainerFocus(div, focus = false) {

	if (focus) {

		div[0].scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
	}

	div.addClass("transition-background");

	// div.css("background", "#7fff00");//#ffdfa5

	setTimeout(function(){

		// div.css("background", "");

		div.removeClass("transition-background");
	}, 2000);
}

function ContainerRemove(container, _function) {

	container.removeClass("fill");

	container.addClass('removing');

	container.slideUp(function() {

		container.remove();

		if (_function) {
			_function();
		}
	})
}

/**
 * Set focus on first field with autofocus.
 */
function AutoFocus(content) {

	let autofocusedElements = $("input[autofocus]:first", content);

	if (autofocusedElements.length) {

		autofocusedElements[0].select(); //focus().

	} else {

		autofocusedElements = $("select[autofocus]:first", content);

		if (autofocusedElements.length) {

			autofocusedElements[0].focus();
		}
	}

	if (autofocusedElements.length) {

		const posicoes = autofocusedElements[0].getBoundingClientRect();
		const inicio = posicoes.top;
		const fim = posicoes.bottom;

		if((inicio < 0) || (fim > window.innerHeight)) {

			autofocusedElements[0].scrollIntoView(false);
		}
	}
}

// function onResize() {

// 	Message.Show("resize " + window.innerHeight, Message.MSG_INFO);
// }

/**
  * Expand window.
  */
$(document).on("click", ".bt_expand", async function() {

	await $(this).closest('.window').find(".expandable").first().slideDown("fast"); //.removeClass("hidden");

	$(this).removeClass("bt_expand fa-chevron-down");
	$(this).addClass("bt_collapse fa-chevron-up");
});

/**
  * Collapse window.
  */
$(document).on("click", ".bt_collapse", async function() {

	$(this).closest('.window').find(".expandable").first().slideUp(); //.addClass("hidden");

	$(this).removeClass("bt_collapse fa-chevron-up");
	$(this).addClass("bt_expand fa-chevron-down");
});

/**
  * Expand window and set focus.
  */
$(document).on("click", ".bt_expand_focus", async function() {

	let window = $(this).closest('.window').find(".expandable").first();

	window.slideDown("fast"); //.removeClass("hidden");

	AutoFocus(window);

	$(this).removeClass("bt_expand_focus fa-chevron-down");
	$(this).addClass("bt_collapse_focus fa-chevron-up");
});

/**
  * Collapse window with set focus.
  */
$(document).on("click", ".bt_collapse_focus", async function() {

	$(this).closest('.window').find(".expandable").first().slideUp(); //.addClass("hidden");

	if ($(this).data('icon') == "keep") {

		$(this).removeClass("bt_collapse_focus");
		$(this).addClass("bt_expand_focus");

	} else {

		$(this).removeClass("bt_collapse_focus fa-chevron-up");
		$(this).addClass("bt_expand_focus fa-chevron-down");
	}
});

/**
 * Disables field.
 * @param {*} field
 */
function Disable(field, loading = true) {

	if (field.closest('.popup-menu').length > 0 || field.closest('.menu-inter').length > 0) {

		field.addClass('disabled');

	} else {

		field.prop("disabled", true);

		if (loading == true) {

			field.addClass('disabled');
		}
	}
}

/**
 * Enables field.
 * @param {*} form
 */
function Enable(field) {

	field.removeClass('disabled');

	if (field.closest('.popup-menu').length > 0 || field.closest('.menu-inter').length > 0) {

		// field.removeClass('disabled');

	} else {

		field.prop("disabled", false);
	}
}

/**
 * Disables form to submit.
 * @param {*} form
 * @param {*} button:submit
 */
function FormDisable(form, button = null) {

	form.find('button:submit').addClass('loading');

	$('button', form).each(function() {

		$(this).data("disabled", $(this).prop("disabled"));
		$(this).prop("disabled", true);
	});

	$('input', form).each(function() {

		$(this).data("disabled", $(this).prop("disabled"));
		$(this).prop("disabled", true);
	});

	$('select', form).each(function() {

		$(this).data("disabled", $(this).prop("disabled"));
		$(this).prop("disabled", true);
	});

	// form.find('button').prop("disabled", true);
	// form.find('input').prop("disabled", true);
	// form.find('select').prop("disabled", true);

	if (button) {

		Disable(button);
	}
}

/**
 * Enables form to submit.
 * @param {*} form
 */
function FormEnable(form) {

	form.find('button:submit').removeClass('loading');

	$('button', form).each(function() {

		$(this).prop("disabled", $(this).data("disabled"));
	});

	$('input', form).each(function() {

		$(this).prop("disabled", $(this).data("disabled"));
	});

	$('select', form).each(function() {

		$(this).prop("disabled", $(this).data("disabled"));
	});

	// form.find('button').prop("disabled", false);
	// form.find('input').prop("disabled", false);
	// form.find('select').prop("disabled", false);
}

function CopyAndPaste(data) {

	// navigator clipboard api needs a secure context (https)
	if (navigator.clipboard && window.isSecureContext) {

		// navigator clipboard api method'
		navigator.clipboard.writeText(data);

	} else {
		// text area method
		let textArea = document.createElement("textarea");
		textArea.value = data;
		// make the textarea out of viewport
		textArea.style.position = "fixed";
		textArea.style.left = "-999999px";
		textArea.style.top = "-999999px";
		document.body.appendChild(textArea);
		textArea.focus();
		textArea.select();
		new Promise((res, rej) => {
			// here the magic happens
			document.execCommand('copy') ? res() : rej();
			textArea.remove();
		});
	}
}

// function CreateChart(data) {

// 	var chart = $(document.createElement("div"));

// 	chart.CanvasJSChart({ //Pass chart options
// 		data: [{
// 			type: "splineArea", //change it to column, spline, line, pie, etc
// 			dataPoints: [
// 				{ x: 10, y: 10 },
// 				{ x: 20, y: 14 },
// 				{ x: 30, y: 18 },
// 				{ x: 40, y: 22 },
// 				{ x: 50, y: 18 },
// 				{ x: 60, y: 28 }
// 			]
// 		}]
// 	});

// 	return chart;
// }

async function FormEdit(container, button, data, page) {

	Disable(button);

	const response = await Post(page, data);

	if (response != null) {

		const content = $(response);

		container.replaceWith(content);

		AutoFocus(content);

		return true;

	} else {

		Enable(button);
	}

	return false;
}

async function FormCancel(container, form, field, data, page) {

	//Prevents focusout on save
	if (field.prop('disabled')) {
		return;
 	}

	Disable(field);
	// field.addClass('loading');

	response = await Post(page, data);

	if (response != null) {

		container.replaceWith(response);
		return true;

	} else {

		Enable(field);
		// field.removeClass('loading');
	}

	return false;
}

async function FormSave(container, form, field, data, page) {

	// FormDisable(form);

    response = await Post(page, data);

	if (response != null) {

		if (container != null) {

			if (typeof response === 'object') {

				container.replaceWith(response['data']);

			} else {

				container.replaceWith(response);
			}
		}

		return response;

	} else {

		FormEnable(form);
		AutoFocus(form);
	}

	return null;
}

/**
  * Closes popup
  */
$(document).on("click", ".popup", function(event) {

	let popup = $(this);

	if ($(event.target).hasClass("popup")) {

		if (popup.hasClass('w_messagebox')) {

			MessageBox.No();

		} else {

			Modal.Cancel(popup);
		}
	}
});

/**
  * Closes popup
  */
$(document).on("click", ".popup_close", function(event) {

	let popup = $(this).closest(".popup");

	if ($(this).closest('.w_messagebox').length) {

		MessageBox.No();

	} else {

		Modal.Cancel(popup);
	}
});

/**
  * Fixes window
  */
$(document).on("click", ".popup_fixwindow", function(event) {

	let button = $(this);

	if (button.hasClass('fa-thumbtack')) {

		button.removeClass('fa-thumbtack');
		button.addClass('fa-circle-dot');

	} else {

		button.removeClass('fa-circle-dot');
		button.addClass('fa-thumbtack');
	}
});

/**
  * Button lock
  */
$(document).on("click", ".button_lock", function(event) {

	let button = $(this);

	if (button.hasClass('fa-lock')) {

		button.removeClass('fa-lock');
		button.addClass('fa-lock-open');

	} else {

		button.removeClass('fa-lock-open');
		button.addClass('fa-lock');
	}
});

/* View in fullscreen */
function openFullscreen() {

	let elem = document.documentElement;

	if (elem.requestFullscreen) {

		elem.requestFullscreen();

	} else if (elem.webkitRequestFullscreen) { /* Safari */

		elem.webkitRequestFullscreen();

	} else if (elem.msRequestFullscreen) { /* IE11 */

		elem.msRequestFullscreen();
	}
}

/* Close fullscreen */
function closeFullscreen() {

	if (document.exitFullscreen) {

		document.exitFullscreen();

	} else if (document.webkitExitFullscreen) { /* Safari */

		document.webkitExitFullscreen();

	} else if (document.msExitFullscreen) { /* IE11 */

		document.msExitFullscreen();

	}
}

// hide the menu when a click event occurs outside the menu
document.addEventListener('click', (event) => {

	if ($(event.target).closest('.float-form').length || $(event.target).hasClass('bt_comment')) {

		// console.log("é um float-form");

	} else {

		$('.float-form').addClass('hidden');
		$('.tooltiptext').removeClass('hidden');
		// console.log("Não é um float-form");
	}

	if ($(event.target).closest('.menu-inter').length || $(event.target).closest('.popup-menu').length) {

		// console.log("é um menu-inter");

	} else {

		MenuClose();
	}

	// console.log($('.float-form').contains(event.target));
	// if (!menu.contains(event.target) && !menuButton.contains(event.target)) {

	// 	menu.classList.add('hidden');
	// }
});

$(document).on("click", ".bt_comment", function(event) {

	let button = $(this);

	if (!button.parent().find(".float-form").hasClass("hidden")) {

		$('.float-form').addClass('hidden');
		$('.tooltiptext').removeClass('hidden');
		return;
	}

	$('.float-form').addClass('hidden');
	$('.tooltiptext').removeClass('hidden');

	let tooltiptext = button.siblings('.tooltiptext');

	tooltiptext.addClass('hidden');

	let container = button.siblings('.float-form');

	container.removeClass("hidden");

	AutoFocus(container);
});

$(document).on("click", ".bt_commentclose", function(event) {

	let button = $(this);

	let container = button.closest('.float-form');

	container.addClass("hidden");

	let tooltiptext = container.siblings('.tooltiptext');

	tooltiptext.removeClass('hidden');
});

/**
  * Preview image selected for digital menu
  */
async function UploadFile() {

	let fileField = this;
	let target = $(this).data('target');

	if (fileField.files.length <= 0) {
		console.log("no image");
		return;
	}

	let formData = new FormData();
	let fileName = fileField.files[0].name;
	formData.append("fileToUpload", fileField.files[0], fileName);
	formData.append("action", "image_upload");
	formData.append("target", target);

	response = await Post('api.php', formData, false, false);

	if (response != null) {

		let timestamp = new Date().getTime();
		let image = "";

		switch(target) {

			case "digitalmenu-header":

				image = $('#img_digitalmenuheader_view')[0];

				image.src = "./assets/digitalmenu_header.png?t=" + timestamp;

				break;

			case "digitalmenu-logo":

				image = $('#img_digitalmenulogo_view')[0];

				image.src = "./assets/digitalmenu_logo.png?t=" + timestamp;

				break;

			case "produto":

				image = $(".image_select");
				image.append("<option value='" + response + "'>" + response + "</option>");
				image.val(response).change();

				break;

			default:

				console.log("Destino da imagem não foi identificado.");

				break;
		}
	}

	fileField.value ="";
}

/**
  * Opens image selects for upload
  */
$(document).on("click", ".bt_uploadfile", async function() {

	let button = $(this);

	let file_upload = document.createElement("input");

	file_upload.type = "file";
	file_upload.accept = "image/*";
	file_upload.onchange = UploadFile;
	$(file_upload).data('target', button.data("target"));

	$(file_upload).click();
});