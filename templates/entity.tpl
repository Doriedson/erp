<!-- BEGIN BLOCK_PAGE -->

<!-- BEGIN EXTRA_BLOCK_CEP_ADDRESS_UPDATE -->
<div class="flex-7">

	<div class="box-header">Deseja atualizar os campos de endereço?</div>

	<div class="entity_cepsearch_container flex flex-dc gap-10 table tbody">
		<div class="tr">
			<div class="">
				<label class="caption">Logradouro (rua, avenida, estrada...)</label>
				<div class="addon">
					<span>{logradouro}</span>
				</div>
			</div>

			<div class="flex gap-10">
				<div class="flex-6">
					<label class="caption">Bairro</label>
					<div class="addon">
						<span>{bairro}</span>
					</div>
				</div>
			</div>

			<div class="flex gap-10">
				<div class="flex-5">
					<label class="caption">Cidade</label>
					<div class="addon">
						<span>{cidade}</span>
					</div>
				</div>
				<div class="flex-1">
					<label class="caption">UF</label>
					<div class="addon">
						<span>{uf}</span>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- END EXTRA_BLOCK_CEP_ADDRESS_UPDATE -->


<!-- BEGIN EXTRA_BLOCK_CEP_SEARCH -->
<div class="flex-7">

	<form id="frm_entity_cepsearch" method="post" class="fill flex flex-dc gap-10" data-id_endereco="{id_endereco}">

		<div>
			<label class="caption">Logradouro (rua, avenida, estrada...)</label>
			<div class="addon">
				<input
					type="text"
					id="frm_entity_cepsearch_logradouro"
					class="fill"
					maxlength="100"
					value=""
					required
					autofocus>
			</div>
		</div>
		<div class="flex gap-10">
			<div class="flex-4">
				<label class="caption">Cidade</label>
				<div class="addon">
					<input
						type="text"
						id="frm_entity_cepsearch_cidade"
						class="fill"
						maxlength="100"
						value="{cidade}"
						required
						autofocus>
				</div>
			</div>

			<div class="flex-2">
				<label class="caption">UF</label>
				<div class="addon">
					<select id="frm_entity_cepsearch_uf" class="fill" required>{uf}</select>
				</div>
			</div>

			<div class="margin-t10 flex-2 flex flex-ai-fe">
				<button type="submit" class="button-blue fill">Buscar</button>
			</div>
		</div>
	</form>

	<div class="section-header">Endereços</div>

	<div class="entity_cepsearch_container flex flex-dc gap-10 table tbody">
		<!-- BEGIN EXTRA_BLOCK_ENTITY_CEPSEARCH_ADDRESS -->
		<div class="tr flex flex-dc gap-10">
			<div class="">
				<label class="caption">Logradouro (rua, avenida, estrada...)</label>
				<div class="addon">
					<span>{logradouro} {complemento}</span>
				</div>
			</div>

			<div class="flex gap-10">
				<div class="flex-2">
					<label class="caption">CEP</label>
					<div class="addon">
						<span>{cep}</span>
					</div>
				</div>

				<div class="flex-6">
					<label class="caption">Bairro</label>
					<div class="addon">
						<span>{bairro}</span>
					</div>
				</div>
			</div>

			<div class="flex gap-10">
				<div class="flex-5">
					<label class="caption">Cidade</label>
					<div class="addon">
						<span>{localidade}</span>
					</div>
				</div>
				<div class="flex-1">
					<label class="caption">UF</label>
					<div class="addon">
						<span>{uf}</span>
					</div>
				</div>
				<div class="flex flex-ai-fe flex-2">
					<button type="button" class="entity_bt_cepselect button-blue" data-id_endereco="{id_endereco}" data-cep="{cep}" data-logradouro="{logradouro}" data-bairro="{bairro}" data-cidade="{localidade}" data-uf="{uf}">Selecionar</button>
				</div>
			</div>
		</div>
		<!-- END EXTRA_BLOCK_ENTITY_CEPSEARCH_ADDRESS -->
	</div>
</div>
<!-- END EXTRA_BLOCK_CEP_SEARCH -->

<!-- BEGIN EXTRA_BLOCK_CEP_SEARCHFREIGHT -->
<div class="flex-7">

	<form id="frm_entity_cepsearchfreight" method="post" class="fill flex flex-dc gap-10">

		<div>
			<label class="caption">CEP/Logradouro (rua, avenida, estrada...)</label>
			<div class="addon">
				<input
					type="text"
					id="frm_entity_cepsearchfreight_logradouro"
					class="fill"
					maxlength="100"
					value=""
					required
					autofocus>
			</div>
		</div>
		<div class="flex gap-10">
			<div class="flex-4">
				<label class="caption">Cidade</label>
				<div class="addon">
					<input
						type="text"
						id="frm_entity_cepsearchfreight_cidade"
						class="fill"
						maxlength="100"
						value="{cidade}"
						required
						autofocus>
				</div>
			</div>

			<div class="flex-2">
				<label class="caption">UF</label>
				<div class="addon">
					<select id="frm_entity_cepsearchfreight_uf" class="fill" required>{uf}</select>
				</div>
			</div>

			<div class="margin-t10 flex-2 flex flex-ai-fe">
				<button type="submit" class="button-blue fill">Buscar</button>
			</div>
		</div>
	</form>

	<div class="section-header">Endereços</div>

	<div class="entity_cepsearchfreight_container flex flex-dc gap-10 table tbody">
		<!-- BEGIN EXTRA_BLOCK_ENTITY_CEPSEARCHFREIGHT_ADDRESS -->
		<div class="tr flex flex-dc gap-10">
			<div class="">
				<label class="caption">Endereço</label>
				<div class="addon">
					<span>{cep} - {logradouro} {complemento} - {bairro} - {localidade} - {uf}</span>
				</div>
			</div>

			<div class="">
				<label class="caption">Frete</label>
				<div class="addon">
					<span class="blue">{freight}</span>
				</div>
			</div>
		</div>
		<!-- END EXTRA_BLOCK_ENTITY_CEPSEARCHFREIGHT_ADDRESS -->
	</div>
</div>
<!-- END EXTRA_BLOCK_CEP_SEARCHFREIGHT -->

<div class="entity_window flex flex-dc gap-20">

	<div class="w-entity-search-popup">
		<div class="box-container flex flex-dc gap-10">

			<div class="flex flex-jc-sb gap-10">
				<div class="box-header flex-1 gap-10">
					<i class="icon fa-solid fa-users"></i>
					<span>Cliente / Cadastro & Consulta</span>
				</div>

				<div class="flex flex-ai-fe flex-jc-fe gap-10">
					<button type="button" class="entity_bt_cepsearchfreight button-icon button-blue" title="Consulta valor de frete">
						<i class="fa-solid fa-magnifying-glass-location"></i>
					</button>

					<button type="button" class="entity_bt_new button-icon button-blue" data-window="entity" title="Cadastrar novo cliente">
						<i class="fa-solid fa-person-circle-plus"></i>
					</button>
				</div>
			</div>

			<div class="flex-responsive flex-jc-sb gap-10">

				<div class="flex gap-10">

					<div class="fill">
						<label class="caption flex flex-ai-center gap-5">
							Cliente [Código / Nome / Telefone]
						</label>

						<div class="autocomplete-dropdown">

							<input
								type="text"
								class="uppercase entity_search smart-search fill flex-4"
								data-source="entity"
								maxlength="40"
								required
								placeholder=""
								autocomplete="off"
								autofocus>

							<!-- BEGIN BLOCK_ENTITY_AUTOCOMPLETE_SEARCH -->
							<ul class="dropdown-list">

								<!-- BEGIN BLOCK_ITEM_SEARCH_NONE -->
								<!-- <li class="padding-10">Digite ao menos 3 caracteres para consulta.</li> -->
								<!-- END BLOCK_ITEM_SEARCH_NONE -->

								<!-- BEGIN EXTRA_BLOCK_ITEM_SEARCH_NOTFOUND -->
								<li class="padding-10">Nenhum cliente encontrado.</li>
								<!-- END EXTRA_BLOCK_ITEM_SEARCH_NOTFOUND -->

								<!-- BEGIN EXTRA_BLOCK_ITEM_SEARCH -->
								<li class="dropdown-item" data-descricao="{nome}" data-sku="{id_entidade}">
									<div class="flex-responsive gap-10">
										<div class="flex-16">
											<!-- <label class="caption">Nome</label> -->
											<div class="addon">
												<!-- {extra_block_entity_button_status} -->
												<span class="{class_status}">{id_entidade}</span>
												<span class="entity_{id_entidade}_nome fill">{nome}</span>
											</div>
										</div>
									</div>
								</li>
								<!-- END EXTRA_BLOCK_ITEM_SEARCH -->
							</ul>
							<!-- END BLOCK_ENTITY_AUTOCOMPLETE_SEARCH -->
						</div>
					</div>
				</div>

				<!-- <div class="flex flex-ai-fe flex-jc-fe desktop">
					<button type="button" class="entity_bt_new button-blue" title="Cadastrar novo cliente">Novo cliente</button>
				</div> -->
			</div>
		</div>
	</div>

	<!-- BEGIN EXTRA_BLOCK_ENTITY_CREDIT_EDIT -->
	<div class="w_entitycredit flex flex-dc gap-10" data-id_entidade="{id_entidade}">

		<div>
			<label class="caption">Cliente</label>
			<div class="addon">
				<span class="entity_{id_entidade}_nome">{nome}</span>
			</div>
		</div>

		<div class="flex gap-10">
			<div class="flex-1">
				<label class="caption">Crédito Atual</label>
				<div class="addon">
					<span class="disabled">R$</span>
					<span class="field disabled fill">{credito_formatted}</span>
				</div>
			</div>

			<div class="flex-1">
				<label class="caption">Valor</label>
				<div class="addon">
					<span >R$</span>
					<input
						type="number"
						id="credito"
						class="fill"
						step="0.01"
						min="0.01"
						max="999999.99"
						placeholder="0,00"
						autocomplete="off"
						autofocus
						required>
				</div>
			</div>
		</div>

		<div>
			<label class="caption">Observação</label>
			<div class="addon">
				<input type="text" maxlength="255" id="obs" class="fill" required>
			</div>
		</div>

		<div class="flex gap-10 padding-t10">
			<button type="button" class="entity_bt_addcredit button-blue flex-1" title="Ajusta crédito do cliente adicionando valor">Adicionar</button>
			<button type="button" class="entity_bt_removecredit button-red flex-1" title="Ajusta crédito do cliente removendo valor">Remover</button>
		</div>
	</div>
	<!-- END EXTRA_BLOCK_ENTITY_CREDIT_EDIT -->

	<div class="entity_container card-container flex flex-dc gap-10">

		<div class="box-header">
			Clientes
		</div>

		<div class="entity_not_found window">

			<div class="font-size-12 textcenter" style="padding: 80px 10px;">
				Digite o código ou o nome de um cliente no campo de busca para localizar.
			</div>
		</div>

		<!-- BEGIN EXTRA_BLOCK_TR_NONE -->
		<div class="entity_not_found card-container window">

			<div class="font-size-12 textcenter" style="padding: 80px 10px;">
				Nenhum cliente encontrado.
			</div>
		</div>
		<!-- END EXTRA_BLOCK_TR_NONE -->

		<div class="entity_table table tbody flex flex-dc">

			<!-- BEGIN EXTRA_BLOCK_ENTITY -->
			<div class="tr flex-responsive gap-10" data-id_entidade="{id_entidade}">

				<div class="flex-13">
					<div>
						<label class="caption">Cliente</label>
						<div class="addon">
							{extra_block_entity_button_status}

							<button type="button" class="entity_bt_nome button-field textleft fill" data-id_entidade="{id_entidade}" alt="Alterar nome do cliente">
								<span class="entity_{id_entidade}_nome">{nome}</span>
							</button>
						</div>
					</div>
				</div>

				<div class="flex gap-10 flex-5">
					<div class="flex-3">
						{block_entity_credit}
					</div>

					<div class="flex gap-10 flex-ai-fe">
						<div class="menu-inter">
							<button class="menu-inter-button fa-solid fa-ellipsis-vertical button-blue"></button>

							<ul>
								<!-- <li class="entity_bt_show flex flex-ai-center gap-10 color-blue" data-id_entidade="{id_entidade}" title="Visualizar dados do cliente">
									<i class="icon fa-solid fa-file-lines"></i>
									<span>Ver Cliente</span>
								</li> -->

								<li class="entity_bt_new_saleorder flex flex-ai-center gap-10 color-blue" data-id_entidade="{id_entidade}" data-id_endereco="0" title="Abrir novo pedido de venda para o cliente">
									<i class="icon fa-solid fa-cart-plus"></i>
									<span>Abrir Pedido</span>
								</li>

								<li class="receipt_bt_new flex flex-ai-center gap-10 color-blue" data-id_entidade="{id_entidade}" data-nome="{nome}" title="Emitir recibo para o cliente">
									<i class="icon fa-solid fa-receipt"></i>
									<span>Emitir Recibo</span>
								</li>
							</ul>
						</div>
					</div>

					<div class="flex flex-ai-fe">
						<button type="button" class="entity_bt_show button-blue" data-id_entidade="{id_entidade}" title="Visualizar dados do cliente">
							<i class="icon fa-solid fa-chevron-down"></i>
						</button>
					</div>
				</div>
			</div>
			<!-- END EXTRA_BLOCK_ENTITY -->

			<!-- BEGIN EXTRA_BLOCK_TR -->
			<div class="window flex flex-dc gap-10" data-id_entidade="{id_entidade}">

				<div class="flex flex-dc gap-10">

					<div class="flex-responsive gap-10">

						<div class="flex-10">
							<div>
								<label class="caption">Cliente</label>
								<div class="addon">
									{extra_block_entity_button_status}
									<!-- BEGIN EXTRA_BLOCK_ENTITY_BUTTON_ATIVO -->
									<button type="button" class="entity_bt_status entity_{id_entidade}_status button-green" data-id_entidade="{id_entidade}" title="Desativar cliente">
										{id_entidade}
									</button>
									<!-- END EXTRA_BLOCK_ENTITY_BUTTON_ATIVO -->
									<!-- BEGIN EXTRA_BLOCK_ENTITY_BUTTON_INATIVO -->
									<button type="button" class="entity_bt_status entity_{id_entidade}_status button-red" data-id_entidade="{id_entidade}" title="Ativar cliente">
										{id_entidade}
									</button>
									<!-- END EXTRA_BLOCK_ENTITY_BUTTON_INATIVO -->

									<!-- BEGIN BLOCK_ENTITY_NOME -->
									<button type="button" class="entity_bt_nome entity_{id_entidade}_nome button-field textleft fill" data-id_entidade="{id_entidade}" alt="Alterar nome do cliente">
										<span class="entity_{id_entidade}_nome">{nome}</span>
									</button>
									<!-- END BLOCK_ENTITY_NOME -->
									<!-- BEGIN EXTRA_BLOCK_NOME_FORM -->
									<form id="frm_entity_nome" method="post" class="fill" data-id_entidade="{id_entidade}">
										<input
											type='text'
											id='nome'
											class="fill"
											required
											value='{nome}'
											maxlength='50'
											autocomplete="off"
											autofocus>
									</form>
									<!-- END EXTRA_BLOCK_NOME_FORM -->
								</div>
							</div>
						</div>

						<div class="flex gap-10 flex-6">
							<div class="flex-3">
								<!-- BEGIN BLOCK_ENTITY_CREDIT -->
								<div class="entitycredit_{id_entidade}">
									<label class="caption">Crédito do Cliente</label>
									<div class="addon">
										<button class="entity_bt_credito button-field textleft fill nowrap" data-id_entidade="{id_entidade}" title="Alterar crédito do cliente">
											R$ {credito_formatted}
										</button>
									</div>
								</div>
								<!-- END BLOCK_ENTITY_CREDIT -->
							</div>

							<div class="flex gap-10 flex-ai-fe">
								<div class="menu-inter">
									<button class="menu-inter-button fa-solid fa-ellipsis-vertical button-blue"></button>

									<ul>
										<li class="entity_bt_new_saleorder flex flex-ai-center gap-10 color-blue" data-id_entidade="{id_entidade}" data-id_endereco="0" title="Abrir novo pedido de venda para o cliente">
											<i class="icon fa-solid fa-cart-plus"></i>
											<span>Abrir Pedido</span>
										</li>

										<li class="receipt_bt_new flex flex-ai-center gap-10 color-blue" data-id_entidade="{id_entidade}" data-nome="{nome}" title="Emitir recibo para o cliente">
											<i class="icon fa-solid fa-receipt"></i>
											<span>Emitir Recibo</span>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>

					<div class="flex flex-dc gap-10">
						<div class="flex-responsive gap-10">

							<div class="flex-2">
								<label class="caption">Cadastro</label>
								<div class="addon">
									<!-- BEGIN BLOCK_DATA -->
									<button class="entity_bt_datacad  button-field textleft fill nowrap" title="Alterar data de cadastro">
										{datacad_formatted}
									</button>
									<!-- END BLOCK_DATA -->
									<!-- BEGIN EXTRA_BLOCK_FORM_DATA -->
									<form method="post" id="frm_entity_datacad" class="fill" data-id_entidade="{id_entidade}">
										<input
											type="date"
											id="datacad"
											class="fill"
											value="{datacad}"
											required
										>
									</form>
									<!-- END EXTRA_BLOCK_FORM_DATA -->
								</div>
							</div>

							<div class="flex-3">
								<label class="caption">CPF / CNPJ</label>
								<div class="addon">
									<!-- BEGIN BLOCK_ENTITY_CPFCNPJ -->
									<button class="entity_bt_cpfcnpj button-field textleft fill nowrap" data-id_entidade="{id_entidade}" title="Alterar CPF / CNPJ do cliente">
										{cpfcnpj_formatted}
									</button>
									<!-- END BLOCK_ENTITY_CPFCNPJ -->

									<!-- BEGIN EXTRA_BLOCK_ENTITY_CPFCNPJ_FORM -->
									<form method="post" id="frm_entity_cpfcnpj" class="fill" data-id_entidade="{id_entidade}">
										<input
											type="text"
											id="cpfcnpj"
											class="fill"
											pattern="\d{11}|\d{14}"
											value="{cpfcnpj}"
											autofocus
											>
									</form>
									<!-- END EXTRA_BLOCK_ENTITY_CPFCNPJ_FORM -->
								</div>
							</div>

							<div class="flex-3">
								<label class="caption">Limite (venda a prazo)</label>
								<div class="addon">
									<!-- BEGIN BLOCK_ENTITY_LIMITE -->
									<button type="button" class="entity_bt_limite button-field textleft fill" data-id_entidade="{id_entidade}" alt="Alterar limite de venda a prado para o cliente">
										R$ {limite_formatted}
									</button>
									<!-- END BLOCK_ENTITY_LIMITE -->
									<!-- BEGIN EXTRA_BLOCK_ENTITY_LIMITE_FORM -->
									<form id="frm_entity_limite" method="post" class="fill" data-id_entidade="{id_entidade}">
										<div class="addon">
											<span>R$</span>
											<input
												type="number"
												id="limite"
												class="fill"
												min='0'
												max='999999.99'
												step='0.01'
												placeholder='0,00'
												value="{limite}"
												autofocus>
										</div>
									</form>
									<!-- END EXTRA_BLOCK_ENTITY_LIMITE_FORM -->
								</div>
							</div>

							<div class="flex-8">
								<label class="caption">Email</label>
								<div class="addon">
									<!-- BEGIN BLOCK_ENTITY_EMAIL -->
									<button type="button" class="entity_bt_email button-field textleft fill" data-id_entidade="{id_entidade}" alt="Alterar email do cliente">
										{email}
									</button>
									<!-- END BLOCK_ENTITY_EMAIL -->
									<!-- BEGIN EXTRA_BLOCK_ENTITY_EMAIL_FORM -->
									<form id="frm_entity_email" method="post" class="fill" data-id_entidade="{id_entidade}">
										<input
											type="text"
											id="email"
											class="fill"
											maxlength="40"
											value="{email}"
											autofocus>
									</form>
									<!-- END EXTRA_BLOCK_ENTITY_EMAIL_FORM -->
								</div>
							</div>
						</div>

						<div class="flex-responsive gap-10">
							<div class="flex-3">
								<label class="caption">Telefone celular</label>
								<div class="addon">
									<!-- BEGIN BLOCK_ENTITY_TELCELULAR -->
									<button type="button" class="entity_bt_telcelular button-field textleft fill nowrap" data-id_entidade="{id_entidade}" alt="Alterar o número de telefone celular do cliente">
										{telcelular_formatted}
									</button>
									<!-- END BLOCK_ENTITY_TELCELULAR -->
									<!-- BEGIN EXTRA_BLOCK_ENTITY_TELCELULAR_FORM -->
									<form id="frm_entity_telcelular" method="post" class="fill" data-id_entidade="{id_entidade}">
										<input
											type="tel"
											id="telcelular"
											class="fill"
											pattern="[0-9]{8,13}"
											maxlength="13"
											size="13"
											value="{telcelular}"
											autofocus>
									</form>
									<!-- END EXTRA_BLOCK_ENTITY_TELCELULAR_FORM -->
								</div>
							</div>

							<div class="flex-3">
								<label class="caption">Telefone resid.</label>
								<div class="addon">
									<!-- BEGIN BLOCK_ENTITY_TELRESIDENCIAL -->
									<button type="button" class="entity_bt_telresidencial button-field textleft fill nowrap" data-id_entidade="{id_entidade}" alt="Alterar o número de telefone celular do cliente">
										{telresidencial_formatted}
									</button>
									<!-- END BLOCK_ENTITY_TELRESIDENCIAL -->
									<!-- BEGIN EXTRA_BLOCK_ENTITY_TELRESIDENCIAL_FORM -->
									<form id="frm_entity_telresidencial" method="post" class="fill" data-id_entidade="{id_entidade}">
										<input
											type="tel"
											id="telresidencial"
											class="fill"
											pattern="[0-9]{8,13}"
											maxlength="13"
											size="13"
											value="{telresidencial}"
											autofocus>
									</form>
									<!-- END EXTRA_BLOCK_ENTITY_TELRESIDENCIAL_FORM -->
								</div>
							</div>

							<div class="flex-3">
								<label class="caption">Telefone com.</label>
								<div class="addon">
									<!-- BEGIN BLOCK_ENTITY_TELCOMERCIAL -->
									<button type="button" class="entity_bt_telcomercial button-field textleft fill nowrap" data-id_entidade="{id_entidade}" alt="Alterar o número de telefone celular do cliente">
										{telcomercial_formatted}
									</button>
									<!-- END BLOCK_ENTITY_TELCOMERCIAL -->
									<!-- BEGIN EXTRA_BLOCK_ENTITY_TELCOMERCIAL_FORM -->
									<form id="frm_entity_telcomercial" method="post" class="fill" data-id_entidade="{id_entidade}">
										<input
											type="tel"
											id="telcomercial"
											class="fill"
											pattern="[0-9]{8,13}"
											maxlength="13"
											size="13"
											value="{telcomercial}"
											autofocus>
									</form>
									<!-- END EXTRA_BLOCK_ENTITY_TELCOMERCIAL_FORM -->
								</div>
							</div>

							<div class="flex-7">
								<label class="caption">Observação</label>
								<div class="addon">
									<!-- BEGIN BLOCK_ENTITY_OBS -->
									<button type="button" class="entity_bt_obs button-field textleft fill" data-id_entidade="{id_entidade}" alt="Alterar a observação do cliente">
										{obs}
									</button>
									<!-- END BLOCK_ENTITY_OBS -->
									<!-- BEGIN EXTRA_BLOCK_ENTITY_OBS_FORM -->
									<form id="frm_entity_obs" method="post" class="fill" data-id_entidade="{id_entidade}">
										<input
											type="text"
											id="obs"
											class="fill"
											maxlength="255"
											value="{obs}"
											autofocus>
									</form>
									<!-- END EXTRA_BLOCK_ENTITY_OBS_FORM -->
								</div>
							</div>
						</div>

						<div class="w-entityaddress flex flex-dc gap-10">
							<!-- BEGIN BLOCK_ADDRESS_SHEET -->
							<div class="section-header">
								Endereço
							</div>

							<div class="table tbody flex flex-dc gap-10">

								{extra_block_address}
								<!-- BEGIN EXTRA_BLOCK_ADDRESS -->
								<div class="entityaddress_tr entityaddress_tr_{id_endereco} tr flex flex-dc gap-10">
									<div class="flex-responsive gap-10">

										<div class="flex gap-10 flex-6">
											<div class="flex-3">
												<label class="caption">Apelido</label>
												<div class="addon">
													<!-- BEGIN BLOCK_ENTITYADDRESS_NICKNAME -->
													<button type="button" class="entityaddress_bt_nickname button-field textleft fill" data-id_endereco="{id_endereco}" alt="Alterar o apelido do endereço">
														{nickname}
													</button>
													<!-- END BLOCK_ENTITYADDRESS_NICKNAME -->
													<!-- BEGIN EXTRA_BLOCK_ENTITYADDRESS_NICKNAME_FORM -->
													<form id="frm_entityaddress_nickname" method="post" class="fill" data-id_endereco="{id_endereco}">
														<input
															type="text"
															id="nickname"
															class="fill"
															maxlength="50"
															value="{nickname}"
															autofocus>
													</form>
													<!-- END EXTRA_BLOCK_ENTITYADDRESS_NICKNAME_FORM -->
												</div>
											</div>

											<div class="flex-3">
												<label class="caption">CEP</label>
												<div class="addon">
													<!-- BEGIN BLOCK_ENTITYADDRESS_CEP -->
													<button type="button" class="entityaddress_bt_cep button-field textleft fill nowrap" data-id_endereco="{id_endereco}" alt="Alterar o CEP do endereço">
														{cep_formatted}
													</button>
													<!-- END BLOCK_ENTITYADDRESS_CEP -->
													<!-- BEGIN EXTRA_BLOCK_ENTITYADDRESS_CEP_FORM -->
													<form id="frm_entityaddress_cep" method="post" class="fill" data-id_endereco="{id_endereco}">
														<input
															type="text"
															id="cep"
															class="fill"
															pattern="\d{8}"
															maxlength="8"
															size="8"
															value="{cep}"
															autofocus>
													</form>
													<!-- END EXTRA_BLOCK_ENTITYADDRESS_CEP_FORM -->
												</div>
											</div>
										</div>

										<div class="flex-7">

											<label class="caption">Logradouro (rua, avenida, estrada...)</label>
											<div class="addon">
												<!-- BEGIN BLOCK_ENTITYADDRESS_LOGRADOURO -->
												<button type="button" class="entityaddress_bt_logradouro button-field textleft fill" data-id_endereco="{id_endereco}" alt="Alterar o logradouro do endereço">
													{logradouro}
												</button>
												<!-- END BLOCK_ENTITYADDRESS_LOGRADOURO -->
												<!-- BEGIN EXTRA_BLOCK_ENTITYADDRESS_LOGRADOURO_FORM -->
												<form id="frm_entityaddress_logradouro" method="post" class="fill" data-id_endereco="{id_endereco}">
													<input
														type="text"
														id="logradouro"
														class="fill"
														maxlength="100"
														value="{logradouro}"
														autofocus>
												</form>
												<!-- END EXTRA_BLOCK_ENTITYADDRESS_LOGRADOURO_FORM -->

												<button type="button" class="entity_bt_cepsearch button-blue button-icon" title="Buscar CEP pelo endereço" data-id_endereco="{id_endereco}">
													<i class="fa-solid fa-search"></i>
												</button>

											</div>
										</div>

										<div class="flex gap-10 flex-5">
											<div class="flex-2">
												<label class="caption">Número</label>
												<div class="addon">
													<!-- BEGIN BLOCK_ENTITYADDRESS_NUMERO -->
													<button type="button" class="entityaddress_bt_numero button-field textleft fill" data-id_endereco="{id_endereco}" alt="Alterar o número endereço">
														{numero}
													</button>
													<!-- END BLOCK_ENTITYADDRESS_NUMERO -->
													<!-- BEGIN EXTRA_BLOCK_ENTITYADDRESS_NUMERO_FORM -->
													<form id="frm_entityaddress_numero" method="post" class="fill" data-id_endereco="{id_endereco}">
														<input
															type="text"
															id="numero"
															class="fill"
															pattern="\d{0,8}"
															maxlength="8"
															size="8"
															value="{numero}"
															autofocus>
													</form>
													<!-- END EXTRA_BLOCK_ENTITYADDRESS_NUMERO_FORM -->
												</div>
											</div>

											<div class="flex-3">
												<label class="caption">Complemento</label>
												<div class="addon">
													<!-- BEGIN BLOCK_ENTITYADDRESS_COMPLEMENTO -->
													<button type="button" class="entityaddress_bt_complemento button-field textleft fill" data-id_endereco="{id_endereco}" alt="Alterar o complemento do endereço">
														{complemento}
													</button>
													<!-- END BLOCK_ENTITYADDRESS_COMPLEMENTO -->
													<!-- BEGIN EXTRA_BLOCK_ENTITYADDRESS_COMPLEMENTO_FORM -->
													<form id="frm_entityaddress_complemento" method="post" class="fill" data-id_endereco="{id_endereco}">
														<input
															type="text"
															id="complemento"
															class="fill"
															maxlength="50"
															value="{complemento}"
															autofocus>
													</form>
													<!-- END EXTRA_BLOCK_ENTITYADDRESS_COMPLEMENTO_FORM -->
												</div>
											</div>
										</div>
									</div>

									<div class="flex-responsive gap-10">

										<div class="flex-3">
											<label class="caption">Bairro</label>
											<div class="addon">
												<!-- BEGIN BLOCK_ENTITYADDRESS_BAIRRO -->
												<button type="button" class="entityaddress_bt_bairro button-field textleft fill" data-id_endereco="{id_endereco}" alt="Alterar o bairro do endereço">
													{bairro}
												</button>
												<!-- END BLOCK_ENTITYADDRESS_BAIRRO -->
												<!-- BEGIN EXTRA_BLOCK_ENTITYADDRESS_BAIRRO_FORM -->
												<form id="frm_entityaddress_bairro" method="post" class="fill" data-id_endereco="{id_endereco}">
													<input
														type="text"
														id="bairro"
														class="fill"
														maxlength="100"
														value="{bairro}"
														autofocus>
												</form>
												<!-- END EXTRA_BLOCK_ENTITYADDRESS_BAIRRO_FORM -->
											</div>
										</div>

										<div class="flex gap-10 flex-4">
											<div class="flex-3">
												<label class="caption">Cidade</label>
												<div class="addon">
													<!-- BEGIN BLOCK_ENTITYADDRESS_CIDADE -->
													<button type="button" class="entityaddress_bt_cidade button-field textleft fill" data-id_endereco="{id_endereco}" alt="Alterar a cidade do endereço">
														{cidade}
													</button>
													<!-- END BLOCK_ENTITYADDRESS_CIDADE -->
													<!-- BEGIN EXTRA_BLOCK_ENTITYADDRESS_CIDADE_FORM -->
													<form id="frm_entityaddress_cidade" method="post" class="fill" data-id_endereco="{id_endereco}">
														<input
															type="text"
															id="cidade"
															class="fill"
															maxlength="100"
															value="{cidade}"
															autofocus>
													</form>
													<!-- END EXTRA_BLOCK_ENTITYADDRESS_CIDADE_FORM -->
												</div>
											</div>

											<div class="flex-1">
												<label class="caption">UF</label>
												<div class="addon">
													<!-- BEGIN BLOCK_ENTITYADDRESS_UF -->
													<button type="button" class="entityaddress_bt_uf button-field textleft fill nowrap" data-id_endereco="{id_endereco}" alt="Alterar a UF do endereço">
														{uf}
													</button>
													<!-- END BLOCK_ENTITYADDRESS_UF -->
													<!-- BEGIN EXTRA_BLOCK_ENTITYADDRESS_UF_FORM -->
													<form id="frm_entityaddress_uf" method="post" class="fill" data-id_endereco="{id_endereco}">
														<select id="uf" class="fill">{uf}</select>
													</form>
													<!-- END EXTRA_BLOCK_ENTITYADDRESS_UF_FORM -->
												</div>
											</div>
										</div>

										<div class="flex gap-10 flex-5">
											<div class="flex-4">
												<label class="caption">Obs</label>
												<div class="addon">
													<!-- BEGIN BLOCK_ENTITYADDRESS_OBS -->
													<button type="button" class="entityaddress_bt_obs button-field textleft fill" data-id_endereco="{id_endereco}" alt="Alterar a observação do endereço">
														{obs}
													</button>
													<!-- END BLOCK_ENTITYADDRESS_OBS -->
													<!-- BEGIN EXTRA_BLOCK_ENTITYADDRESS_OBS_FORM -->
													<form id="frm_entityaddress_obs" method="post" class="fill" data-id_endereco="{id_endereco}">
														<input
															type="text"
															id="obs"
															class="fill"
															maxlength="50"
															value="{obs}"
															autofocus>
													</form>
													<!-- END EXTRA_BLOCK_ENTITYADDRESS_OBS_FORM -->
												</div>
											</div>

											{extra_block_button_sale_address}

														<!-- BEGIN EXTRA_BLOCK_BUTTON_SALE_ADDRESS -->
														<div class="flex flex-ai-fe">

															<button class="sale_order_bt_address_select button-green flex flex-ai-center" data-id_endereco="{id_endereco}" title='Selecionar este endereço para entrega'>
																<i class="icon fa-solid fa-check"></i>
																<span>Selecionar</span>
															</button>
														</div>

														<!-- <li class="sale_order_bt_address_select flex flex-ai-center gap-10 color-blue" data-id_endereco="{id_endereco}" title='Selecionar este endereço para entrega'>
															<i class="icon fa-solid"></i>
															<span>Selecionar Endereço</span>
														</li> -->
														<!-- END EXTRA_BLOCK_BUTTON_SALE_ADDRESS -->

											<div class="flex gap-10 flex-ai-fe flex-jc-fe">
												<div class="menu-inter">
													<button class="menu-inter-button fa-solid button-blue fa-ellipsis-vertical"></button>

													<ul>
														<li class="entity_bt_new_saleorder flex flex-ai-center gap-10 color-blue {entity_bt_new_saleorder}" data-id_entidade="{id_entidade}" data-id_endereco="{id_endereco}" title="Abrir novo pedido de venda para o cliente">
															<i class="icon fa-solid fa-cart-plus"></i>
															<span>Abrir Pedido</span>
														</li>

														<li class="bt_entity_address_delete flex flex-ai-center gap-10 color-red" data-id_endereco="{id_endereco}" title="Excluir Endereço">
															<i class="icon fa-solid fa-trash-can"></i>
															<span>Excluir Endereço</span>
														</li>
													</ul>
												</div>
											</div>
										</div>
									</div>
								</div>
								<!-- END EXTRA_BLOCK_ADDRESS -->
							</div>

							<div class="flex flex-jc-fe">
								<button class="bt_entity_address_new button-blue" data-id_entidade="{id_entidade}" data-page="entity" title="Cadastrar novo endereço.">Adicionar Endereço</button>
							</div>
							<!-- END BLOCK_ADDRESS_SHEET -->
						</div>

						{block_history_order}
					</div>
				</div>
			</div>
			<!-- END EXTRA_BLOCK_TR -->
		</div>

	</div>

	<div class="footer-popup flex-jc-fe">
		<div class="popup-menu">
			<button class="popup-main-button fa-solid fa-ellipsis-vertical button-pink"></button>

			<ul>

				<li class="entity_bt_new flex flex-ai-center gap-10 mobile color-blue" title="Cadastrar novo cliente">
					<i class="icon fa-solid fa-person-circle-plus"></i>
					<span>Novo cliente</span>
				</li>

				<li class="entity_bt_listall flex flex-ai-center gap-10 color-blue" title="Listar todos os clientes">
					<i class="icon fa-solid fa-clipboard-list"></i>
					<span>Listar todos</span>
				</li>

				<!-- <li class="entity_bt_show_search flex flex-ai-center gap-10" title="Consulta de cliente">
					<span class="icon color-blue "></span>
					<span>Consulta</span>
				</li> -->

				<!-- <li class="flex flex-ai-center gap-10 mobile">
					<label>Consulta</label>
					<button type="button" class="entity_bt_show_search button-blue mobile"></button>
				</li>			 -->
			</ul>
		</div>
	</div>
</div>
<!-- END BLOCK_PAGE -->