<!-- BEGIN BLOCK_PAGE -->
<div class="window window-bg flex">
	
	<div class="margin">
		<p class="setor-1">Relatório</p>
		<p class="setor-2">Vendas em Cartão</p>
	</div>
	<div style="width: 50px;"></div>
	<div class="flex flex-jc-center flex-ai-center margin">
		<form method="post" id="frm_report_salecard">
			<input type='date' id="dataini" value='{data}' title="Data ou data inicial." required>
			até <input type="checkbox" id='intervalo' title="Ativa busca de data de vencimento por intervalo.">
			<input type='date' id="datafim" min='{data}' value='{data}' title="Data final." required disabled>
			<button type="submit" class="button-blue" title="Procurar vendas totalizadas">Procurar</button>
		</form>
	</div>
</div>

<div class="report_salecard_container">
	<!-- BEGIN EXTRA_BLOCK_CONTENT -->
	<div class="window">
		<div class="section-header">{header}</div>
		<div class="card-body">
			<table>
				<thead>
					<tr>
						<th>Código</th>
						<th class="textleft">Produto</th>
						<th>Qtd</th>
						<th class="textright">subTotal</th>
					</tr>
				</thead>
				<tbody>
					{extra_block_tr}
					<!-- BEGIN EXTRA_BLOCK_TR -->
					<tr>
						<td class="textcenter">{id_produto}</td>
						<td>{produto}</td>
						<td class="textcenter">{qtd_formatted} {produtounidade}</td>
						<td class="textright">R$ {subtotal_formatted}</td>
					</tr>
					<!-- END EXTRA_BLOCK_TR -->
				</tbody>
				<tfoot>
					<tr>
						<td colspan="4" class="textright">Total: R$ {total_formatted}</td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
	<!-- END EXTRA_BLOCK_CONTENT -->
</div>
<!-- END BLOCK_PAGE -->