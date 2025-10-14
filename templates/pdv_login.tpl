<!-- BEGIN BLOCK_PAGE -->
<div class="login">
	
	<div class="login-container card-container window no-margin flex flex-dc gap-10">
		<div class="section-header">
			<h3>PDV</h3>
		</div>

		<form method="post" id="frm_pdv_login" class="fill">
			<div class="flex flex-dc gap-10">
				<div>
					<label class="caption">Operador(a)</label>
					<div class="addon">
						<select id="id_entidade" class="fill">
							{entitys}
							<!-- BEGIN EXTRA_BLOCK_ENTITY_NONE -->
							<option value="0">Nenhum(a) operador(a) cadastrado(a)</option>
							<!-- END EXTRA_BLOCK_ENTITY_NONE -->
							
							<!-- BEGIN EXTRA_BLOCK_ENTITY -->
							<option value="{id_entidade}" {selected}>{nome}</option>
							<!-- END EXTRA_BLOCK_ENTITY -->
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
							autofocus>
					</div>
				</div>
				<button type="submit" class="button-blue block">Entrar</button>
			</div>
		</form>
	</div>
</div>
<!-- END BLOCK_PAGE -->