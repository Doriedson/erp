<!-- BEGIN BLOCK_PAGE -->
<div class="leftmenu-background button_menu"></div>

<div class="menu">

	<div class="menu-btclose not-desktop">
		<button type="button" class="button_menu button-white button-icon fa-solid fa-angle-left"></button>
	</div>

	<div class="left_menu">

		<div class="ul flex flex-dc">

			<div class="flex flex-jc-center" style="width: 215px; padding-left: 15px">

				<div class="button bt_about flex flex-ai-center flex-jc-center" style="
					width: 135px;
					height: 135px;
					min-width: 135px;
					min-height: 135px;
					border-radius:50%;
					background-color: white;
					box-shadow: 0px 3px 5px 0px gray;">
					<img src="./assets/icons/icon-128x128.png?t={timestamp}">
				</div>

				<div class="desktop">
					<a href="home.php" title="Home" class="pos-abs" style="left: 170px;">
						<div class="flex flex-ai-center flex-jc-center">
							<i class="icon fa-solid fa-grip"></i>
						</div>
					</a>
				</div>

			</div>
			<!-- <div class="flex flex-jc-center"><img src="./assets/icons/icon-96x96.png"></div> -->
			<div class="menu-title padding-t10 padding-b20 padding-h5 textcenter" style="width: 215px; padding-left: 15px">
				<span class="company">{empresa}</span>
			</div>

			<!-- <div class="li">
				<a href="controlpanel.php" class='flex flex-ai-center gap-10 padding-l10' title='Painel de Controle'>
					<i class="icon fa-solid fa-sliders"></i>
					<span class="">Painel de Controle</span>
				</a>
			</div> -->

			<div class="li menu-container">
				<a href='product.php' class="flex flex-ai-center gap-10">
					<i class="icon fa-solid fa-boxes-stacked"></i>
					Produto
				</a>
				<!-- <div class='flex flex-ai-center gap-10 padding-l10'>
					<i class="icon fa-solid fa-boxes-stacked"></i>
					<span class="">Produto</span>
					<span class="dot hidden"></span>
					<i class="caret"></i>
				</div>
				<div class="submenu">
					<span class="custom-caret"></span>
					<div class="submenu-container">
						<div class="ul">
							<div class="li">
								<a href='product.php'>Cadastro / Consulta <span class="dot hidden"></span></a>
							</div>
							<div class="li">
								<a href="price_tag.php">Etiquetas <span class="dot hidden"></span></a>
							</div>
							<div class="li">
								<a href="digital_menu_config.php">Cardápio Digital <span class="dot hidden"></span></a>
							</div>
						</div>
					</div>
				</div> -->
			</div>

			<div class="li menu-container">
				<a href='entity.php' class="flex flex-ai-center gap-10">
					<i class="icon fa-solid fa-users"></i>
					Cliente
				</a>
			</div>

			<div class="li menu-container">
				<a href='collaborator.php' class="flex flex-ai-center gap-10">
					<i class="icon fa-solid fa-user"></i>
					Colaborador
				</a>
			</div>

			<div class="li menu-container">
				<a href="sale_order.php" class="flex flex-ai-center gap-10">
					<i class="icon fa-solid fa-cart-shopping"></i>
					Delivery / Pedido
				</a>
				<!-- <div class='flex flex-ai-center gap-10 padding-l10'>
					<i class="icon fa-solid fa-cart-shopping"></i>
					<span class="">Venda / Delivery</span>
					<i class="caret"></i>
				</div>
				<div class="submenu">
					<span class="custom-caret"></span>
					<div class="submenu-container">
						<div class="ul">
							<div class="li">
								<a href="sale_order.php">Delivery / Pedido</a>
							</div>

						</div>
					</div>
				</div> -->
			</div>

			<div class="li">
				<div class='flex flex-ai-center gap-10 padding-l10'>
					<i class="icon fa-solid fa-file-invoice-dollar"></i>
					<span class="">Compra</span>
					<i class="caret"></i>
				</div>
				<div class="submenu">
					<span class="custom-caret"></span>
					<div class="submenu-container">
						<div class="ul">
							<div class="li">
								<a href="purchase_order.php">Ordem de Compra</a>
							</div>
							<div class="li">
								<a href="purchase_list.php">Lista de Compra</a>
							</div>
							<div class="li">
								<a href='provider.php'>Fornecedor</a>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="li">
				<div class='flex flex-ai-center gap-10 padding-l10'>
					<i class="icon fa-solid fa-sack-dollar"></i>
					<span class="">Financeiro</span>
					<i class="caret"></i>
				</div>
				<div class="submenu">
					<span class="custom-caret"></span>
					<div class="submenu-container">
						<div class="ul">
							<div class="li">
								<a href="bills_to_pay.php">Contas a Pagar</a>
							</div>
							<div class="li">
								<a href='bills_to_pay_sector.php'>Setor - Contas a Pagar</a>
							</div>
							<div class="li">
								<a href="receipt.php">Emissão de Recibos</a>
							</div>
							<!-- <div class="li">
								<a href="bills_to_receive.php">Contas a Receber</a>
							</div> -->
						</div>
					</div>
				</div>
			</div>

			<div class="li">
				<div class='flex flex-ai-center gap-10 padding-l10'>
					<i class="icon fa-solid fa-file-invoice"></i>
					<span class="">Relatório</span>
					<i class="caret"></i>
				</div>
				<div class="submenu">
					<span class="custom-caret"></span>
					<div class="submenu-container">
						<div class="ul">

							<div class="li">
								<a href="report_billspay.php">Contas Pagas</a>
							</div>

							<div class="li">
								<a href="report_entitycredit.php">
									<div style="line-height: 1rem; padding: 10px 0 5px 0;">
										<div>Crédito do Cliente</div>
										<label class="caption">Histórico de lançamentos</label>
								    </div>
								</a>
							</div>

							<div class="li">
								<a href="report_stockin.php">
									<div style="line-height: 1rem; padding: 10px 0 5px 0;">
										<div>Entrada de Estoque</div>
										<label class="caption">Ordem de compra</label>
								    </div>
									<!-- Entrada (Compra) -->
								</a>
							</div>

							<div class="li">
								<a href="report_stockinout.php">
									<div style="line-height: 1rem; padding: 10px 0 5px 0;">
										<div>Entrada / Saída</div>
										<label class="caption">Compra / Venda</label>
								    </div>
									<!-- Entrada / Saída (Compra / Venda) -->
								</a>
							</div>
							<div class="li">
								<a href="report_stockupdate.php">
									<div style="line-height: 1rem; padding: 10px 0 5px 0;">
										<div>Estoque</div>
										<label class="caption">Ajuste manual</label>
								    </div>
									<!-- Ajuste de Estoque -->
								</a>
							</div>
							<div class="li">
								<a href="report_sale_one_product.php">
									<div style="line-height: 1rem; padding: 10px 0 5px 0;">
										<div>Histórico de Produto</div>
										<label class="caption">Gráfico de vendas</label>
								    </div>
									<!-- Histórico de Produto -->
								</a>
							</div>
							<div class="li">
								<a href="report_sale_total.php">
									<div style="line-height: 1rem; padding: 10px 0 5px 0;">
										<div>PDV</div>
										<label class="caption">Fechamento de caixa</label>
								    </div>
								</a>
							</div>
							<div class="li">
								<a href="report_sale_product.php">
									<div style="line-height: 1rem; padding: 10px 0 5px 0;">
										<div>Produtos Vendidos</div>
										<label class="caption">Vendas por período</label>
								    </div>
									<!-- Produtos Vendidos -->
								</a>
							</div>

							<div class="li">
								<a href="report_cashbreak.php">
									<div style="line-height: 1rem; padding: 10px 0 5px 0;">
										<div>Quebra de Caixa</div>
										<label class="caption">Fechamento de caixa</label>
								    </div>
								</a>
							</div>
							<div class="li">
								<a href="report_cashdrain.php">
									<div style="line-height: 1rem; padding: 10px 0 5px 0;">
										<div>Sangria e Reforço</div>
										<label class="caption">PDV / Caixa</label>
								    </div>
								</a>
							</div>
							<div class="li">
								<a href="report_waitertip.php">Taxa Garçom</a>
							</div>

							<div class="li">
								<a href="report_sale_coupon.php">Vendas / Consulta</a>
							</div>

							<div class="li">
								<a href="bills_to_receive.php">Vendas a Prazo</a>
							</div>

							<div class="hidden li ">
								<a href="report_salecard.php">Vendas em Cartões</a>
							</div>

							<div class="li hidden">
								<a href="integrations.php">Integrações</a>
							</div>

						</div>
					</div>
				</div>
			</div>

			<div class="li menu-container">
				<a href='settings.php' class="flex flex-ai-center gap-10">
					<i class="icon fa-solid fa-gear"></i>
					Configurações
				</a>
			</div>

			<div class="li menu-container">
				<div class="flex gap-10">
					<a href='user.php' class="flex-1 flex flex-ai-center gap-10">
						<i class="icon fa-solid fa-user"></i>
						<span class="entity_{id_entidade}_nick">{nome}</span>
					</a>
					<div class="flex flex-ai-center">
						<button type="button" class="bt_logout button-transparent-gray" title="Sair">
							<i class="icon fa-solid fa-right-from-bracket"></i>
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- END BLOCK_PAGE -->