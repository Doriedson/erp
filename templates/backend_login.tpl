<!-- BEGIN BLOCK_PAGE -->
<div class="login">

	<div class="login-container card-container window no-margin flex flex-dc gap-10" style="max-width: 250px;">

		<div class="pos-rel" style="margin-top: 40px;">
			<div class="flex flex-ai-center flex-jc-center pos-abs" style="
						width: 100px; 
						height: 100px; 
						min-width: 100px; 
						min-height: 100px;
						border-radius:50%; 
						top: -100px;
    					left: calc(50% - 50px);
						background-color: white; 
						box-shadow: 0px 3px 5px 0px gray;">
				<img src="./assets/icons/icon-96x96.png?t=1685742577">
			</div>
		</div>
		
		<div class="box-header flex-jc-center no-border no-margin">
			Retaguarda
		</div>

		<form method="post" id="frm_backend_login" class="fill">
			<div class="flex flex-dc gap-10">
				<!-- <div>
					<label class="caption">Código</label>
					<div class="addon">
						<input 
						type="text" 
						id="user" 
						class="textcenter fill" 
						maxlength="6" 
						pattern="[0-9]+"
						autofocus 
						required 
						>
					</div>
				</div> -->

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
							autofocus>
					</div>
				</div>

				<button type="submit" class="button-blue margin-t10">Entrar</button>
			
			</div>
		</form>
	</div>
</div>
<!-- END BLOCK_PAGE -->