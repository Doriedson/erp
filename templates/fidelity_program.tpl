<!-- BEGIN BLOCK_PAGE -->
<div class="flex flex-dc gap-10">

	<div>Especifique abaixo o tempo para o cálculo do valor acumulado de compras do cliente.</div>

	<div class="">
		<label class="caption">Últimos</label>

		<div class="addon">
			<!-- BEGIN BLOCK_DIAS -->
			<button class="fidelity_bt_dias button-field" title="Define número de dias do programa fidelidade">
				{dias_compra} dias
			</button>
			<!-- END BLOCK_DIAS -->
			<!-- BEGIN EXTRA_BLOCK_DIAS_FORM -->
			<form method="post" id="frm_fidelity_dias" class="addon">
				<input
					type="number"
					id="dias_compra"
					class=""
					step='1'
					min='1'
					max='365'
					required
					value='{dias_compra}'
					autofocus>
					<span>dias</span>
			</form>
			<!-- END EXTRA_BLOCK_DIAS_FORM -->
		</div>
	</div>

	<div>A ordem de aplicação das regras será de cima para baixo. Se a primeira regra for satisfeita, então ela será aplicada.</div>

	<div>Se a primeira regra não for satisfeita, será analisada a próxima regra, e assim por diante, até a última.</div>

	<div class="section-header">
		Regras
	</div>

	<div class="fidelity_not_found window {fidelity_notfound}">

		<div class="font-size-12" style="padding: 20px 10px;">
			Para ativar o Programa Fidelidade adicione pelo menos 1 regra.
		</div>
	</div>

	<div class="fidelity_table table tbody flex flex-dc">

		{extra_block_fidelity}

		<!-- BEGIN EXTRA_BLOCK_FIDELITY -->
		<div class="w-fidelity tr flex-responsive gap-10" data-id_fidelidaderegra={id_fidelidaderegra}>

			<div class="flex gap-10 flex-5">
				<div>
					<label class="caption">Prioridade</label>
					<div class="addon">
						<button class="fidelity_bt_up button-icon" title="Subir prioridade da regra">
							<i class="fa-solid fa-circle-up"></i>
						</button>
						<span>{prioridade}</span>
						<button class="fidelity_bt_down button-icon" title="Descer prioridade da regra">
							<i class="fa-solid fa-circle-down"></i>
						</button>
					</div>
				</div>

				<div class="flex-3">
					<label class="caption">Regra</label>
					<div class="addon">
						<!-- BEGIN BLOCK_CONDICAO -->
						<button class="fidelity_bt_condicao button-field fill" title="Alterar regra">
							{regra}
						</button>
						<!-- END BLOCK_CONDICAO -->
						<!-- BEGIN EXTRA_BLOCK_CONDICAO_FORM -->
						<form method="post" id="frm_fidelity_condicao" class="fill">
							<select id="condicao" class="fill" autofocus>
								{option}
								<!-- BEGIN EXTRA_BLOCK_CONDICAO_FORM_OPTION -->
								<option value='{id_regra}' {selected}>{regra}</option>
								<!-- END EXTRA_BLOCK_CONDICAO_FORM_OPTION -->
							</select>
						</form>
						<!-- END EXTRA_BLOCK_CONDICAO_FORM -->
					</div>
				</div>
			</div>

			<div class="flex gap-10 flex-6">
				<div class="flex-3">
					<label class="caption">Valor acumulado</label>
					<div class="addon">
						<!-- BEGIN BLOCK_VALOR -->
						<button class="fidelity_bt_valor button-field" title="Alterar valor acumulado para regra">
							R$ {valor_formatted}
						</button>
						<!-- END BLOCK_VALOR -->
						<!-- BEGIN EXTRA_BLOCK_VALOR_FORM -->
						<form method="post" id="frm_fidelity_valor" class="addon fill flex flex-ai-center">
							<span>R$</span>
							<input
								type="number"
								id="valor"
								class="fill"
								step='0.01'
								min='0'
								max='999999.99'
								title="Valor para ativar regra"
								value='{valor}'
								autocomplete="off"
								required
								autofocus>
						</form>
						<!-- END EXTRA_BLOCK_VALOR_FORM -->
					</div>
				</div>

				<div class="flex-2">
					<label class="caption">Desconto</label>
					<div class="addon">
						<!-- BEGIN BLOCK_DESCONTO -->
						<button class="fidelity_bt_desconto button-field" title="Alterar porcentagem de desconto da regra">
							{desconto_formatted} %
						</button>
						<!-- END BLOCK_DESCONTO -->
						<!-- BEGIN EXTRA_BLOCK_DESCONTO_FORM -->
						<form method="post" id="frm_fidelity_desconto" class="fill addon">
							<input
								type="number"
								id="desconto"
								class="fill"
								step='0.01'
								min='0'
								max='100.00'
								title="Desconto a ser aplicado na regra ativada"
								value='{desconto}'
								autocomplete="off"
								required
								autofocus>
								<span>%</span>
						</form>
						<!-- END EXTRA_BLOCK_DESCONTO_FORM -->
					</div>
				</div>

				<div class="flex flex-ai-fe">
					<button class="fidelity_bt_delete button-icon button-red fa-solid fa-trash-can" title="Remove a regra"></button>
				</div>
			</div>

			<div class="flex-5"></div>
		</div>
		<!-- END EXTRA_BLOCK_FIDELITY -->
	</div>

	<div class="flex flex-jc-fe">
		<button type="button" class="fidelity_bt_new button-blue">Nova regra</button>
	</div>

</div>
<!-- END BLOCK_PAGE -->