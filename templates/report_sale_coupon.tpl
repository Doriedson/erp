<!-- BEGIN BLOCK_PAGE -->
<div class="box-container flex flex-dc gap-10">

	<div class="box-header gap-10">
		<i class="icon fa-solid fa-cart-shopping"></i>
		<span>Vendas / Consulta</span>
	</div>

	<div class="flex-responsive gap-10">

		<form method="post" id="frm_reportsalecoupon">

			<div class="flex-responsive gap-10">

				<div class="flex gap-10">
					<div class="flex-1">
						<label class="caption">Data / Venda</label>
						<div class="addon">
							<input
								type='date'
								id="frm_reportsalecoupon_data"
								class="fill"
								value='{data}'
								title="Data"
								required>
						</div>
					</div>
				</div>

				<div class="flex gap-10">

					<div class="flex-1">
						<label class="caption flex flex-ai-center gap-5">
							Status
						</label>
						<div class="addon">
							<select id="frm_reportsalecoupon_id_vendastatus" class="fill" autofocus>
								<option value="-1">Todos</option>
								{venda_status}
								<!-- BEGIN EXTRA_BLOCK_SALEORDER_STATUS -->
								<option value="{id_vendastatus}">{vendastatus}</option>
								<!-- END EXTRA_BLOCK_SALEORDER_STATUS -->
							</select>
						</div>
					</div>
				</div>

				<div class="flex gap-10">

					<div class="flex-1">
						<label class="caption flex flex-ai-center gap-5">
							Código do Pedido / Cupom
						</label>
						<div class="addon">
							<span class="flex">
								<input type="checkbox" id="frm_reportsalecoupon_chk">
							</span>
							<input
								type="number"
								id="frm_reportsalecoupon_id_venda"
								class="fill"
								step='1'
								min='1'
								max='999999999999999999'
								maxlength="18"
								required
								disabled
								placeholder=""
								autocomplete="off"
							>
						</div>
					</div>

					<div class="flex flex-ai-fe">
						<button type="submit" class="button-blue fill" title="Procurar cupom">Procurar</button>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>

<div class="box-container flex flex-dc gap-10">
	<div class="reportsalecoupon_header hidden box-header flex-responsive gap-10">

		<div class="setor-1 flex-1">
			<span><span class="reportsalecoupon_data">00/00/0000</span> - <span class="w-reportsale-counter">0</span> cupom(s)</span>
		</div>

		<div class="flex flex-1 flex-jc-fe color-blue">
			<span>Total R$ <span class="w-reportsale-total">0,00</span></span>
		</div>
	</div>

	<div class="reportsalecoupon_notfound fill">

		<div class="font-size-12 textcenter" style="padding: 80px 10px;">
			Use os campos acima para pesquisa.
		</div>
	</div>

	<div class="reportsalecoupon_container table tbody flex flex-dc gap-10">

		<!-- BEGIN EXTRA_BLOCK_REPORTSALE -->
		<div class="w-reportsale w_saleorder w_saleorder_{id_venda} tr flex flex-dc gap-10" data-id_venda="{id_venda}" data-versao="{versao}" data-total="{total}">

			<div class="flex-responsive gap-10">

				<div class="flex gap-10 flex-6">
					<div class="flex gap-10 flex-2">
						<div class="flex-1">
							<label class="caption">{salelegend} # {id_venda}</label>

							{extra_block_saleorder_show_ticket}
						</div>
					</div>

					<div class="flex gap-10 flex-2">
						<div class="flex-1">
							<label class="caption">Data</label>
							<div class="addon flex-jc-center">
								<span>{data_formatted}</span>
							</div>
						</div>
					</div>
				</div>

				<!-- <div class="flex flex-4 gap-10">
					<div class="flex-1">
						<label class="caption">Cupom</label>
						<div class="addon">
							<span class="field">{id_venda}</span>
						</div>
					</div>

					<div class="flex-3">
						<label class="caption">Status</label>
						<div class="addon">
							<span class="field">{vendastatus}</span>
						</div>
					</div>
				</div> -->

				<!-- <div class="flex-3">
					<label class="caption">Data / Venda</label>
					<div class="addon">
						<span class="field">{data_formatted}</span>
					</div>
				</div> -->

				<div class="flex-7">
					<label class="caption">Cliente</label>
					<div class="addon">
						<!-- BEGIN EXTRA_BLOCK_SALEORDER_ENTITY_NONE -->
						<span class="field">Varejo</span>
						<!-- END EXTRA_BLOCK_SALEORDER_ENTITY_NONE -->

						<!-- BEGIN EXTRA_BLOCK_SALEORDER_ENTITY -->
						{extra_block_entity_button_status}
						<span class="entity_{id_entidade}_nome fill">{nome}</span>
						<!-- END EXTRA_BLOCK_SALEORDER_ENTITY -->

						{extra_block_saleorder_entity}
					</div>
				</div>

				<div class="flex flex-5 gap-10">
					<div class="flex-3">
						<label class="caption">Total</label>
						<div class="addon color-blue font-size-15 {reversed}">
							<span>R$ {total_formatted}</span>
						</div>
					</div>

					{extra_block_saleorder_obs}

					{extra_block_saleorder_menu}
				</div>
			</div>

			<div class="w-reportsale-item expandable" style="display: none;">
			</div>
		</div>
		<!-- END EXTRA_BLOCK_REPORTSALE -->
	</div>
</div>
<!-- END BLOCK_PAGE -->