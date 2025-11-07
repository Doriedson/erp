<div class="leftmenu-background button_menu"></div>

<div class="menu">
	<div class="menu-btclose not-desktop">
		<button type="button" class="button_menu button-white button-icon fa-solid fa-angle-left"></button>
	</div>

	<div class="left_menu">

		<div class="button bt_about flex flex-jc-center" style="width: 215px; padding-left: 15px">
			<div class="flex flex-ai-center flex-jc-center" style="
				width: 135px;
				height: 135px;
				min-width: 135px;
				min-height: 135px;
				border-radius:50%;
				background-color: white;
				box-shadow: 0px 3px 5px 0px gray;">
				<img src="./assets/icons/icon-128x128.png?t={timestamp}">
			</div>
		</div>
		<!-- <div class="flex flex-jc-center"><img src="./assets/icons/icon-96x96.png"></div> -->
		<div class="menu-title padding-t10 padding-b20 padding-h5 textcenter" style="width: 215px; padding-left: 15px">
			<span class="company">{empresa}</span>
		</div>

		<div class="ul flex flex-dc">

			<div class="li menu-container">
				<a href="waiter_self_service.php" class='flex flex-ai-center gap-10'>
					<i class="icon fa-solid fa-bell-concierge"></i>
					Self-Service
				</a>
			</div>

			<div class="li menu-container">
				<a href="waiter_table.php" class='flex flex-ai-center gap-10'>
					<i class="icon fa-solid fa-chair"></i>
					Lista de Mesas
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