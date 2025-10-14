<!-- BEGIN BLOCK_PAGE -->
<!DOCTYPE HTML>

<html lang="pt-br">

	<head>
		<title>{title}</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="height=device-height, width=device-width, user-scalable=no">
		<link rel="manifest" href="{manifest}?version={version}">

		<link rel="stylesheet" type="text/css" href="css/style.css?version={version}" />
		<link rel="stylesheet" type="text/css" href="css/menu.css?version={version}" />
		<link rel="stylesheet" type="text/css" href="css/print.css?version={version}" />
		<link rel="stylesheet" type="text/css" href="css/login.css?version={version}" />

		<link rel="stylesheet" type="text/css" href="vendor/css/jquery-ui.min.css?version={version}" />
		<link rel="stylesheet" type="text/css" href="vendor/css/fontawesome.css?version={version}" />
		<link rel="stylesheet" type="text/css" href="vendor/css/brands.css?version={version}" />
		<link rel="stylesheet" type="text/css" href="vendor/css/solid.css?version={version}" />
		<link rel="stylesheet" type="text/css" href="vendor/css/regular.css?version={version}" />
		<link rel="shortcut icon" type="imagex/png" href="assets/icons/icon-48x48.png?version={version}">

		<script type="text/javascript" src="service_worker.js?version={version}"></script>
		<script>
			let version = {version};
		</script>

		<script type="text/javascript" src="vendor/js/jquery-3.6.0.min.js?version={version}"></script>
		<script type="text/javascript" src="vendor/js/jquery-ui.min.js?version={version}"></script>

		<script type="text/javascript" src="vendor/js/chart.min.js?version={version}"></script>

		<script type="text/javascript" src="js/funcoes.js?version={version}"></script>
		<script type="text/javascript" src="js/autoload.js?version={version}"></script>
		<script type="text/javascript" src="js/mychart.js?version={version}"></script>
		<script type="text/javascript" src="js/smart_search.js?version={version}"></script>

		<script type="text/javascript" src="js/menu.js?version={version}"></script>
		<script type="text/javascript" src="js/integrations.js?version={version}"></script>
		<script type="text/javascript" src="js/home.js?version={version}"></script>
		<!-- <script type="text/javascript" src="js/controlpanel.js?version={version}"></script> -->
		<script type="text/javascript" src="js/backend.js?version={version}"></script>
		<script type="text/javascript" src="js/tab_system.js?version={version}"></script>
		<script type="text/javascript" src="js/product.js?version={version}"></script>
		<script type="text/javascript" src="js/collaborator.js?version={version}"></script>
		<script type="text/javascript" src="js/user.js?version={version}"></script>
		<script type="text/javascript" src="js/product_sector.js?version={version}"></script>
		<script type="text/javascript" src="js/entity.js?version={version}"></script>
		<script type="text/javascript" src="js/provider.js?version={version}"></script>
		<script type="text/javascript" src="js/sale_order.js?version={version}"></script>
		<script type="text/javascript" src="js/fidelity_program.js?version={version}"></script>
		<script type="text/javascript" src="js/black_friday.js?version={version}"></script>
		<script type="text/javascript" src="js/config_shipment.js?version={version}"></script>
		<script type="text/javascript" src="js/purchase_order.js?version={version}"></script>
		<script type="text/javascript" src="js/purchase_list.js?version={version}"></script>
		<script type="text/javascript" src="js/bills_to_pay.js?version={version}"></script>
		<script type="text/javascript" src="js/bills_to_pay_sector.js?version={version}"></script>
		<script type="text/javascript" src="js/bills_to_receive.js?version={version}"></script>
		<script type="text/javascript" src="js/report_sale_total.js?version={version}"></script>
		<script type="text/javascript" src="js/report_entitycredit.js?version={version}"></script>
		<script type="text/javascript" src="js/report_cashdrain.js?version={version}"></script>
		<script type="text/javascript" src="js/report_cashbreak.js?version={version}"></script>
		<script type="text/javascript" src="js/report_sale_coupon.js?version={version}"></script>
		<script type="text/javascript" src="js/report_sale_product.js?version={version}"></script>
		<script type="text/javascript" src="js/report_sale_one_product.js?version={version}"></script>
		<script type="text/javascript" src="js/report_stockin.js?version={version}"></script>
		<script type="text/javascript" src="js/report_stockinout.js?version={version}"></script>
		<script type="text/javascript" src="js/report_stockupdate.js?version={version}"></script>
		<script type="text/javascript" src="js/report_salecard.js?version={version}"></script>
		<script type="text/javascript" src="js/report_billspay.js?version={version}"></script>
		<script type="text/javascript" src="js/report_waitertip.js?version={version}"></script>
		<script type="text/javascript" src="js/price_tag.js?version={version}"></script>
		<script type="text/javascript" src="js/receipt.js?version={version}"></script>
		<script type="text/javascript" src="js/settings.js?version={version}"></script>
		<script type="text/javascript" src="js/waiter.js?version={version}"></script>
		<script type="text/javascript" src="js/wallet.js?version={version}"></script>
		<script type="text/javascript" src="js/wallets.js?version={version}"></script>
		<script type="text/javascript" src="js/wallet_sector.js?version={version}"></script>
		<script type="text/javascript" src="js/wallet_cashtype.js?version={version}"></script>
		<script type="text/javascript" src="js/printer.js?version={version}"></script>
		<script type="text/javascript" src="js/printing.js?version={version}"></script>
		<script type="text/javascript" src="js/table_config.js?version={version}"></script>
		<script type="text/javascript" src="js/waiter_tip.js?version={version}"></script>
		<script type="text/javascript" src="js/waiter_table_transf.js?version={version}"></script>
		<script type="text/javascript" src="js/waiter_self_service.js?version={version}"></script>
		<script type="text/javascript" src="js/pdv_config.js?version={version}"></script>
		<script type="text/javascript" src="js/sound.js?version={version}"></script>
		<script type="text/javascript" src="js/cashier_closing.js?version={version}"></script>
		<script type="text/javascript" src="js/scales_barcode.js?version={version}"></script>
		<script type="text/javascript" src="js/pdv.js?version={version}"></script>
		<script type="text/javascript" src="js/sale_cashtype.js?version={version}"></script>
		<script type="text/javascript" src="js/stock.js?version={version}"></script>
		<script type="text/javascript" src="js/company.js?version={version}"></script>
		<script type="text/javascript" src="js/digital_menu_config.js?version={version}"></script>
	</head>

	<body>
		<div class="printable"></div>

		<!-- BEGIN EXTRA_BLOCK_POPUP -->
		<div class="w-popup popup hidden">
			<div class="popup-container">
				<div class="window-container">

					<div class="window-header setor-2 flex flex-jc-sb flex-ai-center gap-10">
						<div class="fill">
							<div class='flex gap-10'>
								<div class="popup-title-icon"></div>
								<div class="popup-title"></div>
							</div>
						</div>
						<button class="popup_fixwindow button-icon button-transparent color-white fa-solid fa-thumbtack hidden" title="Fixar / Desafixar janela"></button>
						<button class="popup_close button-icon button-transparent color-white fa-solid fa-xmark" title="Fechar"></button>
					</div>

					<div class="window-body"></div>
				</div>
			</div>
		</div>
		<!-- END EXTRA_BLOCK_POPUP -->

		<!-- BEGIN EXTRA_BLOCK_AUTHENTICATOR -->
		<!-- <div class="w_authenticator popup hidden">
			<div class="popup-container popup-small"> -->
				<!-- <div class="window-container">

					<div class="window-header setor-2 flex flex-jc-sb flex-ai-center gap-10">
						Autorização
						<button class="popup_close button-icon button-transparent color-white fa-solid fa-xmark" title="Fechar"></button>
					</div> -->

					<form method="post" id="frm_authenticator" class="fill">
						<div class="flex flex-dc gap-10">
							<div>
								<label class="caption">Colaborador(a)</label>
								<div class="addon">
									<select id="id_entidade" class="fill">
										{collaborators}

										<!-- BEGIN EXTRA_BLOCK_COLLABORATOR -->
										<option value="{id_entidade}" {selected}>{nome} #{id_entidade}</option>
										<!-- END EXTRA_BLOCK_COLLABORATOR -->
									</select>
								</div>
							</div>

							<div>
								<label class="caption">Senha</label>
								<div class="addon">
									<input
										type="password"
										id="pass"
										class="textcenter fill"
										maxlength="4"
										pattern="[0-9]+"
										required
										autofocus
									>
								</div>
							</div>

							<button type="submit" class="button-blue margin-t10">Confirmar</button>
						</div>
					</form>
				<!-- </div> -->
			<!-- </div>
		</div> -->
		<!-- END EXTRA_BLOCK_AUTHENTICATOR -->

		<!-- BEGIN EXTRA_BLOCK_MESSAGEBOX -->
		<div class="w_messagebox popup hidden" style="z-index: 1002;">
			<div class="popup-container popup-small">
				<div class="window-container flex flex-dc gap-10">

					<div class="window-header setor-2 flex flex-jc-sb flex-ai-center gap-10">
						<div class="messagebox_title">
							Confirmação
						</div>

						<button class="popup_close button-icon button-transparent color-white fa-solid fa-xmark" title="Fechar"></button>
					</div>

					<div class="messagebox_message">
						Mensagem
					</div>

					<div class="messagebox_obs hidden">

						<label class="caption">Motivo</label>

						<input
							type="text"
							id="messagebox_obs"
							class="fill"
							maxlength="250"
							autofocus
						>
					</div>

					<div class="flex flex-jc-center gap-10">
						<button type="button" class="messagebox_bt_yes button-green" style="width: 100px;">Sim</button>
						<button type="button" class="messagebox_bt_no button-red" style="width: 100px;">Não</button>
					</div>
				</div>
			</div>
		</div>
		<!-- END EXTRA_BLOCK_MESSAGEBOX -->

		<div class="message-container">
			<!-- BEGIN EXTRA_BLOCK_MESSAGE_INFO -->
			<div class="message message-info message_bt_close mouseHand flex gap-10" style="display: none;">
				<div class="flex-1">{mensagem}</div>
			</div>
			<!-- END EXTRA_BLOCK_MESSAGE_INFO -->
			<!-- BEGIN EXTRA_BLOCK_MESSAGE_ERROR -->
			<div class="message message-error message_bt_close mouseHand flex gap-10" style="display: none;">
				<div class="flex-1">{mensagem}</div>
			</div>
			<!-- END EXTRA_BLOCK_MESSAGE_ERROR -->
			<!-- BEGIN EXTRA_BLOCK_MESSAGE_DONE -->
			<div class="message message-done message_bt_close mouseHand flex gap-10" style="display: none;">
				<div class="flex-1">{mensagem}</div>
			</div>
			<!-- END EXTRA_BLOCK_MESSAGE_DONE -->
			<!-- BEGIN EXTRA_BLOCK_MESSAGE_ALERT -->
			<div class="message message-alert message_bt_close mouseHand flex gap-10" style="display: none;">
				<div class="flex-1">{mensagem}</div>
			</div>
			<!-- END EXTRA_BLOCK_MESSAGE_ALERT -->

		</div>

		{module}

		<!-- JavaScript -- Aumentar Textarea automático
$("textarea").bind("input", function(e) {
    while($(this).outerHeight() < this.scrollHeight + parseFloat($(this).css("borderTopWidth")) + parseFloat($(this).css("borderBottomWidth")) &&
          $(this).height() < 500
         ) {
        $(this).height($(this).height()+1);
    };
}); -->

		<!-- <div id="header" class="header flex flex-ai-center hidden">
			<div class="header-container"></div>
		</div>

		<div id="body-container" class="body-container" data-module="{module}">
			<div class="login no-margin">
				<h1 class="splash-screen">Bem Vindo!</h1>
			</div>
		</div> -->

		<!-- http://maps.googleapis.com/maps/api/distancematrix/xml?origins=07914060&destinations=07911278&mode=driving&language=pt-BR&sensor=false -->

		<!-- Tolltip personalizado, TODO: implementação futura -->
		<!-- <div class="tooltip fade top in" role="tooltip" id="tooltip737111" style="top: 50; left: 0px; display: block;"><div class="tooltip-arrow" style="left: 50%;"></div><div class="tooltip-inner">Visualizar pedido</div></div> -->
	</body>
</html>
<!-- END BLOCK_PAGE -->