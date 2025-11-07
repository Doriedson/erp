<!-- BEGIN BLOCK_PAGE -->
<div class="w-provider-search-popup">
	<div class="box-container flex flex-dc gap-10">

		<div class="box-header gap-10">
			<i class="icon fa-solid fa-file-invoice-dollar"></i>
			<span>Compra / Fornecedor</span>
		</div>

		<div class="flex-responsive gap-10 flex-jc-sb">

			<!-- <form method="post" id="frm_provider_search"> -->

				<div class="flex gap-10">

					<div class="fill">
						<label class="caption flex flex-ai-center gap-5">
							Fornecedor [Código, Razão ou Fantasia]
						</label>

						<div class="autocomplete-dropdown">
							<input
								type="text"
								id="provider_search"
								class="uppercase provider_search smart-search fill"
								data-source="provider"
								maxlength="40"
								required
								placeholder=""
								autocomplete="off"
								autofocus>

							<!-- BEGIN BLOCK_PROVIDER_AUTOCOMPLETE_SEARCH -->
							<ul class="dropdown-list">

								<!-- BEGIN BLOCK_ITEM_SEARCH_NONE -->
								<!-- <li class="padding-10">Digite ao menos 3 caracteres para consulta.</li> -->
								<!-- END BLOCK_ITEM_SEARCH_NONE -->

								<!-- BEGIN EXTRA_BLOCK_ITEM_SEARCH_NOTFOUND -->
								<li class="padding-10">Nenhum fornecedor encontrado.</li>
								<!-- END EXTRA_BLOCK_ITEM_SEARCH_NOTFOUND -->

								<!-- BEGIN EXTRA_BLOCK_ITEM_SEARCH -->
								<li class="dropdown-item" data-descricao="{razaosocial}" data-sku="{id_fornecedor}">
									<div class="flex-responsive gap-10">
										<div>
											<label class="caption">Razão</label>
											<div class="addon">
												<!-- {extra_block_provider_button_status} -->
												<span class="{class_status}">{id_fornecedor}</span>
												<span class="field fill">{razaosocial}</span>
											</div>
										</div>

										<div>
											<label class="caption">Fantasia</label>
											<div class="addon">
												<span class="field fill">{nomefantasia}</span>
											</div>
										</div>
									</div>
								</li>
								<!-- END EXTRA_BLOCK_ITEM_SEARCH -->
							</ul>
							<!-- END BLOCK_PROVIDER_AUTOCOMPLETE_SEARCH -->
						</div>
					</div>

					<!-- <div class="flex flex-ai-fe">
						<button type="submit" class="button-blue" title="Procura de produtos por código ou nome">Procurar</button>
					</div> -->
				</div>
			<!-- </form> -->

			<div class="flex flex-ai-fe desktop">
				<button type="button" class="provider_bt_new button-blue">Novo fornecedor</button>
			</div>
		</div>
	</div>
</div>

<div class="setor-2">
	Fornecedores
</div>

<div class="provider_container flex flex-dc gap-10">

	<div class="provider_not_found card-container window">

		<div class="font-size-12 textcenter" style="padding: 80px 10px;">
			Digite o código, a razão social ou o nome fantasia no campo de busca para localizar o fornecedor.
		</div>
	</div>

	<div class="provider_table flex flex-dc gap-10">
		<!-- BEGIN EXTRA_BLOCK_PROVIDER -->
		<div class="w-provider window card-container flex flex-dc gap-10" data-id_fornecedor="{id_fornecedor}">

			<div class="flex flex-dc gap-10">

				<div class="flex-responsive gap-10">

					<div class='flex-6'>
						<label class="caption">Razão Social</label>

						<!-- BEGIN BLOCK_PROVIDER_RAZAOSOCIAL -->
						<div class="addon container">
							{extra_block_provider_button_status}

							<!-- BEGIN EXTRA_BLOCK_PROVIDER_BUTTON_ATIVO -->
							<button type="button" class="provider_bt_status provider_{id_fornecedor}_status button-green" data-id_fornecedor="{id_fornecedor}" title="Desativar fornecedor">
								{id_fornecedor}
							</button>
							<!-- END EXTRA_BLOCK_PROVIDER_BUTTON_ATIVO -->

							<!-- BEGIN EXTRA_BLOCK_PROVIDER_BUTTON_INATIVO -->
							<button type="button" class="provider_bt_status provider_{id_fornecedor}_status button-red" data-id_fornecedor="{id_fornecedor}" title="Ativar fornecedor">
								{id_fornecedor}
							</button>
							<!-- END EXTRA_BLOCK_PROVIDER_BUTTON_INATIVO -->

							<button type="button" class="provider_bt_razaosocial button-field textleft fill" data-id_fornecedor="{id_fornecedor}" alt="Alterar a razão social do fornecedor">
								{razaosocial}

							</button>
						</div>
						<!-- END BLOCK_PROVIDER_RAZAOSOCIAL -->

						<!-- BEGIN EXTRA_BLOCK_PROVIDER_RAZAOSOCIAL_FORM -->
						<form id="frm_provider_razaosocial" method="post" class="fill" data-id_fornecedor="{id_fornecedor}">
							<div class="addon">
								{extra_block_provider_button_status}
								<input
									type='text'
									id='razaosocial'
									class="fill"
									required
									value='{razaosocial}'
									maxlength='100'
									autocomplete="off"
									autofocus>
							</div>
						</form>
						<!-- END EXTRA_BLOCK_PROVIDER_RAZAOSOCIAL_FORM -->
					</div>

					<div class='flex-6'>
						<label class="caption">Nome Fantasia</label>
						<!-- BEGIN BLOCK_PROVIDER_NOMEFANTASIA -->
						<div class="addon container">
							<button type="button" class="provider_bt_nomefantasia button-field textleft fill" data-id_fornecedor="{id_fornecedor}" alt="Alterar o nome fantasia do fornecedor">
								{nomefantasia}

							</button>
						</div>
						<!-- END BLOCK_PROVIDER_NOMEFANTASIA -->
						<!-- BEGIN EXTRA_BLOCK_PROVIDER_NOMEFANTASIA_FORM -->
						<form id="frm_provider_nomefantasia" method="post" class="fill" data-id_fornecedor="{id_fornecedor}">
							<div class="addon">
								<input
									type='text'
									id='nomefantasia'
									class="fill"
									required
									value='{nomefantasia}'
									maxlength='100'
									autocomplete="off"
									autofocus>
							</div>
						</form>
						<!-- END EXTRA_BLOCK_PROVIDER_NOMEFANTASIA_FORM -->
					</div>

					<div class="flex gap-10 flex-jc-fe flex-ai-fe">
						<div>
							<button type="button" class="provider_bt_new_purchaseorder button-blue" data-id_fornecedor="{id_fornecedor}" title="Abrir nova ordem de compra para o fornecedor">Abrir Ordem</button>
						</div>
						<div class="flex flex-jc-fe ">
							<button class="provider_bt_expand button-icon button-blue fa-solid fa-chevron-down" data-id_fornecedor="{id_fornecedor}"></button>
						</div>
					</div>
				</div>

				<div class="expandable" style="display: none;">

					<!-- BEGIN EXTRA_BLOCK_PROVIDER_DATA -->
					<div class="w-providerdata flex flex-dc gap-10">
						<div class="flex-responsive gap-10">

							<div class="flex gap-10 flex-6">
								<div class="flex-2">
									<label class="caption">Cadastro</label>
									<div class="addon">
										<span class="field disabled fill">{datacad_formatted}</span>
									</div>
								</div>

								<div class="flex-4">
									<label class="caption">CPF / CNPJ</label>
									<!-- BEGIN BLOCK_PROVIDER_CPFCNPJ -->
									<div class="addon container">
										<button class="provider_bt_cpfcnpj button-field textleft fill" data-id_fornecedor="{id_fornecedor}" title="Alterar CPF / CNPJ do fornecedor">
											{cpfcnpj_formatted}

										</button>
									</div>
									<!-- END BLOCK_PROVIDER_CPFCNPJ -->

									<!-- BEGIN EXTRA_BLOCK_PROVIDER_CPFCNPJ_FORM -->
									<form method="post" id="frm_provider_cpfcnpj" class="fill" data-id_fornecedor="{id_fornecedor}">
										<div class="addon">
											<input
												type="text"
												id="cpfcnpj"
												class="fill"
												pattern="\d{11}|\d{14}"
												value="{cpfcnpj}"
												autofocus
												>
										</div>
									</form>
									<!-- END EXTRA_BLOCK_PROVIDER_CPFCNPJ_FORM -->
								</div>
							</div>

							<div class="flex-4">
								<label class="caption">IE</label>
								<!-- BEGIN BLOCK_PROVIDER_IE -->
								<div class="addon container">
									<button class="provider_bt_ie button-field textleft fill" data-id_fornecedor="{id_fornecedor}" title="Alterar Inscrição Estadual do fornecedor">
										{ie_formatted}

									</button>
								</div>
								<!-- END BLOCK_PROVIDER_IE -->

								<!-- BEGIN EXTRA_BLOCK_PROVIDER_IE_FORM -->
								<form method="post" id="frm_provider_ie" class="fill" data-id_fornecedor="{id_fornecedor}">
									<div class="addon">
										<input
											type="text"
											id="ie"
											class="fill"
											pattern="\d{9}"
											value="{ie}"
											autofocus
											>
									</div>
								</form>
								<!-- END EXTRA_BLOCK_PROVIDER_IE_FORM -->
							</div>

							<div class="flex-6">
								<label class="caption">Email</label>
								<!-- BEGIN BLOCK_PROVIDER_EMAIL -->
								<div class="addon container">
									<button type="button" class="provider_bt_email button-field textleft fill" data-id_fornecedor="{id_fornecedor}" alt="Alterar email do cliente">
										{email}

									</button>
								</div>
								<!-- END BLOCK_PROVIDER_EMAIL -->
								<!-- BEGIN EXTRA_BLOCK_PROVIDER_EMAIL_FORM -->
								<form id="frm_provider_email" method="post" class="fill" data-id_fornecedor="{id_fornecedor}">
									<div class="addon">
										<input
											type="text"
											id="email"
											class="fill"
											maxlength="40"
											value="{email}"
											autofocus>
									</div>
								</form>
								<!-- END EXTRA_BLOCK_PROVIDER_EMAIL_FORM -->
							</div>
						</div>

						<div>
							<label class="caption">Obs</label>
							<!-- BEGIN BLOCK_PROVIDER_OBS -->
							<div class="addon container">
								<button type="button" class="provider_bt_obs button-field textleft fill" data-id_fornecedor="{id_fornecedor}" alt="Alterar a observação do endereço">
									{obs}

								</button>
							</div>
							<!-- END BLOCK_PROVIDER_OBS -->
							<!-- BEGIN EXTRA_BLOCK_PROVIDER_OBS_FORM -->
							<form id="frm_provider_obs" method="post" class="fill" data-id_fornecedor="{id_fornecedor}">
								<div class="addon">
									<input
										type="text"
										id="obs"
										class="fill"
										maxlength="50"
										value="{obs}"
										autofocus>
								</div>
							</form>
							<!-- END EXTRA_BLOCK_PROVIDER_OBS_FORM -->
						</div>

						<div class="section-header">
							Contato
						</div>

						<div class="flex-responsive gap-10">
							<div class="flex gap-10 flex-6">
								<div class="flex-3">
									<label class="caption">Telefone 1</label>
									<!-- BEGIN BLOCK_PROVIDER_TELEFONE1 -->
									<div class="addon container">
										<button type="button" class="provider_bt_telefone1 button-field textleft fill" data-id_fornecedor="{id_fornecedor}" alt="Alterar o número de telefone celular do cliente">
											{telefone1_formatted}

										</button>
									</div>
									<!-- END BLOCK_PROVIDER_TELEFONE1 -->
									<!-- BEGIN EXTRA_BLOCK_PROVIDER_TELEFONE1_FORM -->
									<form id="frm_provider_telefone1" method="post" class="fill" data-id_fornecedor="{id_fornecedor}">
										<input
											type="tel"
											id="telefone1"
											class="fill"
											pattern="[0-9]{8,13}"
											maxlength="13"
											size="13"
											value="{telefone1}"
											autofocus>
									</form>
									<!-- END EXTRA_BLOCK_PROVIDER_TELEFONE1_FORM -->
								</div>

								<div class='flex-3'>
									<label class="caption">Contato 1</label>
									<!-- BEGIN BLOCK_PROVIDER_CONTATO1 -->
									<div class="addon container">
										<button type="button" class="provider_bt_contato1 button-field textleft fill" data-id_fornecedor="{id_fornecedor}" alt="Alterar o nome fantasia do fornecedor">
											{contato1}

										</button>
									</div>
									<!-- END BLOCK_PROVIDER_CONTATO1 -->
									<!-- BEGIN EXTRA_BLOCK_PROVIDER_CONTATO1_FORM -->
									<form id="frm_provider_contato1" method="post" class="fill" data-id_fornecedor="{id_fornecedor}">
										<div class="addon">
											<input
												type='text'
												id='contato1'
												class="fill"
												required
												value='{contato1}'
												maxlength='50'
												autocomplete="off"
												autofocus>
										</div>
									</form>
									<!-- END EXTRA_BLOCK_PROVIDER_CONTATO1_FORM -->
								</div>
							</div>

							<div class="flex gap-10 flex-6">
								<div class="flex-3">
									<label class="caption">Telefone 2</label>
									<div class="addon container">
										<!-- BEGIN BLOCK_PROVIDER_TELEFONE2 -->
										<button type="button" class="provider_bt_telefone2 button-field textleft fill" data-id_fornecedor="{id_fornecedor}" alt="Alterar o número de telefone celular do cliente">
											{telefone2_formatted}

										</button>
										<!-- END BLOCK_PROVIDER_TELEFONE2 -->
										<!-- BEGIN EXTRA_BLOCK_PROVIDER_TELEFONE2_FORM -->
										<form id="frm_provider_telefone2" method="post" class="fill" data-id_fornecedor="{id_fornecedor}">
											<input
												type="tel"
												id="telefone2"
												class="fill"
												pattern="[0-9]{8,13}"
												maxlength="13"
												size="13"
												value="{telefone2}"
												autofocus>
										</form>
										<!-- END EXTRA_BLOCK_PROVIDER_TELEFONE2_FORM -->
									</div>
								</div>

								<div class='flex-3'>
									<label class="caption">Contato 2</label>
									<!-- BEGIN BLOCK_PROVIDER_CONTATO2 -->
									<div class="addon container">
										<button type="button" class="provider_bt_contato2 button-field textleft fill" data-id_fornecedor="{id_fornecedor}" alt="Alterar o nome fantasia do fornecedor">
											{contato2}

										</button>
									</div>
									<!-- END BLOCK_PROVIDER_CONTATO2 -->
									<!-- BEGIN EXTRA_BLOCK_PROVIDER_CONTATO2_FORM -->
									<form id="frm_provider_contato2" method="post" class="fill" data-id_fornecedor="{id_fornecedor}">
										<div class="addon">
											<input
												type='text'
												id='contato2'
												class="fill"
												required
												value='{contato2}'
												maxlength='50'
												autocomplete="off"
												autofocus>
										</div>
									</form>
									<!-- END EXTRA_BLOCK_PROVIDER_CONTATO2_FORM -->
								</div>
							</div>

							<div class="flex gap-10 flex-6">
								<div class="flex-3">
									<label class="caption">Telefone 3</label>
									<div class="addon container">
										<!-- BEGIN BLOCK_PROVIDER_TELEFONE3 -->
										<button type="button" class="provider_bt_telefone3 button-field textleft fill" data-id_fornecedor="{id_fornecedor}" alt="Alterar o número de telefone celular do cliente">
											{telefone3_formatted}

										</button>
										<!-- END BLOCK_PROVIDER_TELEFONE3 -->
										<!-- BEGIN EXTRA_BLOCK_PROVIDER_TELEFONE3_FORM -->
										<form id="frm_provider_telefone3" method="post" class="fill" data-id_fornecedor="{id_fornecedor}">
											<input
												type="tel"
												id="telefone3"
												class="fill"
												pattern="[0-9]{8,13}"
												maxlength="13"
												size="13"
												value="{telefone3}"
												autofocus>
										</form>
										<!-- END EXTRA_BLOCK_PROVIDER_TELEFONE3_FORM -->
									</div>
								</div>

								<div class='flex-3'>
									<label class="caption">Contato 3</label>
									<!-- BEGIN BLOCK_PROVIDER_CONTATO3 -->
									<div class="addon container">
										<button type="button" class="provider_bt_contato3 button-field textleft fill" data-id_fornecedor="{id_fornecedor}" alt="Alterar o nome fantasia do fornecedor">
											{contato3}

										</button>
									</div>
									<!-- END BLOCK_PROVIDER_CONTATO3 -->
									<!-- BEGIN EXTRA_BLOCK_PROVIDER_CONTATO3_FORM -->
									<form id="frm_provider_contato3" method="post" class="fill" data-id_fornecedor="{id_fornecedor}">
										<div class="addon">
											<input
												type='text'
												id='contato3'
												class="fill"
												required
												value='{contato3}'
												maxlength='50'
												autocomplete="off"
												autofocus>
										</div>
									</form>
									<!-- END EXTRA_BLOCK_PROVIDER_CONTATO3_FORM -->
								</div>
							</div>
						</div>

						<div class="section-header">
							Endereço
						</div>

						<div class="flex-responsive gap-10">

							<div class="flex-2">
								<label class="caption">CEP</label>
								<!-- BEGIN BLOCK_PROVIDER_CEP -->
								<div class="addon container">
									<button type="button" class="provider_bt_cep button-field textleft fill" data-id_fornecedor="{id_fornecedor}" alt="Alterar o CEP do endereço">
										{cep_formatted}

									</button>
								</div>
								<!-- END BLOCK_PROVIDER_CEP -->
								<!-- BEGIN EXTRA_BLOCK_PROVIDER_CEP_FORM -->
								<form id="frm_provider_cep" method="post" class="fill" data-id_fornecedor="{id_fornecedor}">
									<div class="addon">
										<input
											type="text"
											id="cep"
											class="fill"
											pattern="\d{8}"
											maxlength="8"
											size="8"
											value="{cep}"
											autofocus>
									</div>
								</form>
								<!-- END EXTRA_BLOCK_PROVIDER_CEP_FORM -->
							</div>

							<div class="flex-14">
								<label class="caption">Endereço</label>
								<!-- BEGIN BLOCK_PROVIDER_ENDERECO -->
								<div class="addon container">
									<button type="button" class="provider_bt_endereco button-field textleft fill" data-id_fornecedor="{id_fornecedor}" alt="Alterar o endereço">
										{endereco}

									</button>
								</div>
								<!-- END BLOCK_PROVIDER_ENDERECO -->
								<!-- BEGIN EXTRA_BLOCK_PROVIDER_ENDERECO_FORM -->
								<form id="frm_provider_endereco" method="post" class="fill" data-id_fornecedor="{id_fornecedor}">
									<div class="addon">
										<input
											type="text"
											id="endereco"
											class="fill"
											maxlength="50"
											value="{endereco}"
											autofocus>
									</div>
								</form>
								<!-- END EXTRA_BLOCK_PROVIDER_ENDERECO_FORM -->
							</div>
						</div>

						<div class="flex-responsive gap-10">
							<div class="flex-3">
								<label class="caption">Bairro</label>
								<!-- BEGIN BLOCK_PROVIDER_BAIRRO -->
								<div class="addon container">
									<button type="button" class="provider_bt_bairro button-field textleft fill" data-id_fornecedor="{id_fornecedor}" alt="Alterar o bairro do endereço">
										{bairro}

									</button>
								</div>
								<!-- END BLOCK_PROVIDER_BAIRRO -->
								<!-- BEGIN EXTRA_BLOCK_PROVIDER_BAIRRO_FORM -->
								<form id="frm_provider_bairro" method="post" class="fill" data-id_fornecedor="{id_fornecedor}">
									<div class="addon">
										<input
											type="text"
											id="bairro"
											class="fill"
											maxlength="50"
											value="{bairro}"
											autofocus>
									</div>
								</form>
								<!-- END EXTRA_BLOCK_PROVIDER_BAIRRO_FORM -->
							</div>

							<div class="flex gap-10 flex-4">
								<div class="flex-3">
									<label class="caption">Cidade</label>
									<!-- BEGIN BLOCK_PROVIDER_CIDADE -->
									<div class="addon container">
										<button type="button" class="provider_bt_cidade button-field textleft fill" data-id_fornecedor="{id_fornecedor}" alt="Alterar a cidade do endereço">
											{cidade}

										</button>
									</div>
									<!-- END BLOCK_PROVIDER_CIDADE -->
									<!-- BEGIN EXTRA_BLOCK_PROVIDER_CIDADE_FORM -->
									<form id="frm_provider_cidade" method="post" class="fill" data-id_fornecedor="{id_fornecedor}">
										<div class="addon">
											<input
												type="text"
												id="cidade"
												class="fill"
												maxlength="50"
												value="{cidade}"
												autofocus>
										</div>
									</form>
									<!-- END EXTRA_BLOCK_PROVIDER_CIDADE_FORM -->
								</div>

								<div class="flex-1">
									<label class="caption">UF</label>
									<!-- BEGIN BLOCK_PROVIDER_UF -->
									<div class="addon container">
										<button type="button" class="provider_bt_uf button-field textleft fill" data-id_fornecedor="{id_fornecedor}" alt="Alterar a UF do endereço">
											{uf}

										</button>
									</div>
									<!-- END BLOCK_PROVIDER_UF -->
									<!-- BEGIN EXTRA_BLOCK_PROVIDER_UF_FORM -->
									<form id="frm_provider_uf" method="post" class="fill" data-id_fornecedor="{id_fornecedor}">
										<div class="addon">
											<select id="uf" class="fill" autofocus>{uf_option}</select>
										</div>
									</form>
									<!-- END EXTRA_BLOCK_PROVIDER_UF_FORM -->
								</div>
							</div>
						</div>
						</div>
					<!-- END EXTRA_BLOCK_PROVIDER_DATA -->
				</div>
			</div>
		</div>
		<!-- END EXTRA_BLOCK_PROVIDER -->
	</div>
</div>

<!-- <div class="footer">
	<div class="footer-container gap-10">
		<button class="provider_bt_list button-blue" title="Listar todos fornecedores">Listar todos</button>
		<button class="provider_bt_new button-blue" title="Abrir ordem de comrpa">Novo Fornecedor</button>
		<button class="provider_bt_show_search button-blue" title="Consulta de fornecedores">Consulta</button>
	</div>
</div> -->

<div class="footer-popup flex-jc-fe">
	<div class="popup-menu">
		<button class="popup-main-button fa-solid fa-ellipsis-vertical button-pink"></button>

		<ul>

			<li class="provider_bt_list flex flex-ai-center gap-10 color-blue" title="Listar todos fornecedores">
				<i class="icon fa-solid fa-clipboard-list"></i>
				<span>Listar todos</span>
			</li>

			<li class="provider_bt_new flex flex-ai-center gap-10 color-blue" title="Cadastrar novo fornecedor">
				<i class="icon fa-solid fa-square-plus"></i>
				<span>Novo Fornecedor</span>
			</li>

			<!-- <li class="flex flex-ai-center gap-10">
				<label>Consulta</label>
				<button class="provider_bt_show_search button-blue" title="Consulta de fornecedores"></button>
			</li> -->
		</ul>
	</div>
</div>
<!-- END BLOCK_PAGE -->