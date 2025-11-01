<!-- BEGIN BLOCK_PAGE -->
	<div class="pdv">
		<div id="body-container" class="pdv-body-container flex-1 margin" style="background-color: #e9e9e9;
		border: 2px solid #858585;
		box-shadow: 0px 0px 2px 2px #fff;">
			<div class="login no-margin">
				<h1 class="splash-screen">Bem Vindo!</h1>
			</div>

			<!-- BEGIN EXTRA_BLOCK_SALE_CONTAINER -->
			<div class="flex-table gap-10 margin">
				<div class="flex-4-col gap-10">lkjlkjfks<br>jflksjf<br> lkjsf lksjf lkjsf kls</div>
				<div class="flex-4-col gap-10">lkjlkjfks<br>jflksjf<br> lkjsf lksjf lkjsf kls</div>
				<div class="flex-4-col gap-10">lkjlkjfks<br>jflksjf<br> lkjsf lksjf lkjsf kls</div>
				<div class="flex-4-col gap-10">lkjlkjfks<br>jflksjf<br> lkjsf lksjf lkjsf kls</div>
				<div class="flex-4-col gap-10">lkjlkjfks<br>jflksjf<br> lkjsf lksjf lkjsf kls</div>
				<div class="flex-4-col gap-10">lkjlkjfks<br>jflksjf<br> lkjsf lksjf lkjsf kls</div>
			</div>
			<!-- END EXTRA_BLOCK_SALE_CONTAINER -->
		</div>

		<div class="flex flex-dc flex-jc-sb gap-10">

			<div class="flex gap-40 padding-10 flex-jc-center">
				<div class="box-container flex flex-ai-center gap-20">

					<div>
						<div class="flex flex-jc-sb gap-20">
							<span>- Desconto: </span>
							<span id="pdv_desconto">R$ 0,00</span>
						</div>

						<div class="flex flex-jc-sb gap-20">
							<span>+ Frete: </span>
							<span id="pdv_frete">R$ 0,00</span>
						</div>

						<div class="flex flex-jc-sb gap-20">
							<span>+ Serviço: </span>
							<span id="pdv_servico">R$ 0,00</span>
						</div>
					</div>
				</div>

				<div class="box-container flex flex-ai-center gap-20">
					<div>
						<label class="caption">Total</label>
						<div class="font-size-25 color-red">
							<span id="pdv_total" class="field">R$ 0,00</span>
						</div>
					</div>

				</div>
			</div>

			<div class="flex flex-dc gap-10">
				<div id="pdv-header" class="pdv-header flex flex-ai-center">
					<div class="header-container"></div>
				</div>

				<div class="pdv-leftmenu">

				</div>

				<!-- <div class="pdv-footer flex flex-ai-center flex-jc-fe padding-h10"> -->



					<div class="felx padding-10">
						<div class="box-container flex flex-ai-center gap-10">
							<div class="fill">
								<label class="caption">Código ou Descrição</label>

								<div class="autocomplete-dropdown">
									<div class="pos-rel">
									<ul class="dropdown-list" style="display: none; bottom: 0; right: 0;">
										<li class="padding-10">Digite ao menos 3 caracteres para consulta.</li>
									</ul>
									</div>

									<input
										type="text"
										id="product_search"
										class="uppercase product_search smart_search smart-search fill flex-4"
										data-source="popup"
										data-sort="active"
										maxlength="40"
										required=""
										placeholder=""
										autocomplete="off"
										autofocus="">
								</div>
							</div>

							<div class="flex flex-ai-center">
								<div class="font-size-12 padding-5">
									<span>X</span>
								</div>
							</div>

							<div>
								<label class="caption">Quantidade</label>
								<div class="addon">
									<input
										type="number"
										class="fill textcenter"
										id="qtd"
										step="0.001"
										min="0"
										max="999999.999"
										required=""
										title="Quantidade do produto."
										placeholder="">
								</div>
							</div>
						</div>
					</div>


				<!-- </div> -->
			</div>

		</div>
	</div>
<!-- END BLOCK_PAGE -->