/**
 * Loads Scales Barcode settings.
 */
 $(document).on("click", ".bt_load_scales_barcode", async function() {

	let button = $(this);

	Disable(button);

	let data = {
		action: 'load'
	}

	let response = await Post("scales_barcode.php", data);

	if (response != null) {

		$(".scales_barcode_container").html(response);

		button.removeClass("bt_load_scales_barcode fa-chevron-down");
		button.addClass("bt_collapse fa-chevron-up");

		$(".scales_barcode_container").slideDown("fast");
	}

	Enable(button);
});

/**
 * Loads Black Friday settings.
 */
 $(document).on("click", ".bt_load_black_friday", async function() {

	let button = $(this);

	Disable(button);

	let data = {
		action: 'load'
	}

	let response = await Post("black_friday.php", data);

	if (response != null) {

		$(".black_friday_container").html(response);

		button.removeClass("bt_load_black_friday fa-chevron-down");
		button.addClass("bt_collapse fa-chevron-up");

		$(".black_friday_container").slideDown("fast");
	}

	Enable(button);
});

/**
 * Loads Black Friday settings.
 */
 $(document).on("click", ".bt_load_company", async function() {

	let button = $(this);

	Disable(button);

	let data = {
		action: 'load'
	}

	let response = await Post("company.php", data);

	if (response != null) {

		$(".company_container").html(response);

		button.removeClass("bt_load_company fa-chevron-down");
		button.addClass("bt_collapse fa-chevron-up");

		$(".company_container").slideDown("fast");
	}

	Enable(button);
});

/**
 * Loads Cashier Closing settings.
 */
 $(document).on("click", ".bt_load_cashier_closing", async function() {

	let button = $(this);

	Disable(button);

	let data = {
		action: 'load'
	}

	let response = await Post("cashier_closing.php", data);

	if (response != null) {

		$(".cashier_closing_container").html(response);

		button.removeClass("bt_load_cashier_closing fa-chevron-down");
		button.addClass("bt_collapse fa-chevron-up");

		$(".cashier_closing_container").slideDown("fast");
	}

	Enable(button);
});

/**
 * Loads Stock Product settings.
 */
 $(document).on("click", ".bt_load_stock", async function() {

	let button = $(this);

	Disable(button);

	let data = {
		action: 'load'
	}

	let response = await Post("stock.php", data);

	if (response != null) {

		$(".stock_container").html(response);

		button.removeClass("bt_load_stock fa-chevron-down");
		button.addClass("bt_collapse fa-chevron-up");

		$(".stock_container").slideDown("fast");
	}

	Enable(button);
});

/**
 * Loads Config Shipment settings.
 */
 $(document).on("click", ".bt_load_config_shipment", async function() {

	let button = $(this);

	Disable(button);

	let data = {
		action: 'load'
	}

	let response = await Post("config_shipment.php", data);

	if (response != null) {

		$(".config_shipment_container").html(response);

		button.removeClass("bt_load_config_shipment fa-chevron-down");
		button.addClass("bt_collapse fa-chevron-up");

		$(".config_shipment_container").slideDown("fast");
	}

	Enable(button);
});

/**
 * Loads Sale Cashtype settings.
 */
 $(document).on("click", ".bt_load_sale_cashtype", async function() {

	let button = $(this);

	Disable(button);

	let data = {
		action: 'load'
	}

	let response = await Post("sale_cashtype.php", data);

	if (response != null) {

		$(".sale_cashtype_container").html(response);

		button.removeClass("bt_load_sale_cashtype fa-chevron-down");
		button.addClass("bt_collapse fa-chevron-up");

		$(".sale_cashtype_container").slideDown("fast");
	}

	Enable(button);
});

/**
 * Loads Printing settings.
 */
 $(document).on("click", ".bt_load_printing", async function() {

	let button = $(this);

	Disable(button);

	let data = {
		action: 'load'
	}

	let response = await Post("printing.php", data);

	if (response != null) {

		$(".printing_container").html(response);

		button.removeClass("bt_load_printing fa-chevron-down");
		button.addClass("bt_collapse fa-chevron-up");

		$(".printing_container").slideDown("fast");
	}

	Enable(button);
});

/**
 * Loads Printer settings.
 */
 $(document).on("click", ".bt_load_printer", async function() {

	let button = $(this);

	Disable(button);

	let data = {
		action: 'load'
	}

	let response = await Post("printer.php", data);

	if (response != null) {

		$(".printer_container").html(response);

		button.removeClass("bt_load_printer fa-chevron-down");
		button.addClass("bt_collapse fa-chevron-up");

		$(".printer_container").slideDown("fast");
	}

	Enable(button);
});

/**
 * Loads Table Config settings.
 */
$(document).on("click", ".bt_load_table_config", async function() {

	let button = $(this);

	Disable(button);

	let data = {
		action: 'load'
	}

	let response = await Post("table_config.php", data);

	if (response != null) {

		$(".table_config_container").html(response);

		button.removeClass("bt_load_table_config fa-chevron-down");
		button.addClass("bt_collapse fa-chevron-up");

		$(".table_config_container").slideDown("fast");
	}

	Enable(button);
});

/**
 * Loads waiter Tip settings.
 */
$(document).on("click", ".bt_load_waiter_tip", async function() {

	let button = $(this);

	Disable(button);

	let data = {
		action: 'load'
	}

	let response = await Post("waiter_tip.php", data);

	if (response != null) {

		$(".waiter_tip_container").html(response);

		button.removeClass("bt_load_waiter_tip fa-chevron-down");
		button.addClass("bt_collapse fa-chevron-up");

		$(".waiter_tip_container").slideDown("fast");
	}

	Enable(button);
});

/**
 * Loads PDV Config settings.
 */
$(document).on("click", ".bt_load_pdv_config", async function() {

	let button = $(this);

	Disable(button);

	let data = {
		action: 'load'
	}

	let response = await Post("pdv_config.php", data);

	if (response != null) {

		$(".pdv_config_container").html(response);

		button.removeClass("bt_load_pdv_config fa-chevron-down");
		button.addClass("bt_collapse fa-chevron-up");

		$(".pdv_config_container").slideDown("fast");
	}

	Enable(button);
});

/**
 * Loads Fidelity Program settings.
 */
 $(document).on("click", ".bt_load_fidelity_program", async function() {

	let button = $(this);

	Disable(button);

	let data = {
		action: 'load'
	}

	let response = await Post("fidelity_program.php", data);

	if (response != null) {

		$(".fidelity_program_container").html(response);

		button.removeClass("bt_load_fidelity_program fa-chevron-down");
		button.addClass("bt_collapse fa-chevron-up");

		$(".fidelity_program_container").slideDown("fast");
	}

	Enable(button);
});

/**
 * Loads Sound settings.
 */
 $(document).on("click", ".bt_load_sound", async function() {

	let button = $(this);

	Disable(button);

	let data = {
		action: 'load'
	}

	let response = await Post("sound.php", data);

	if (response != null) {

		$(".sound_container").html(response);

		button.removeClass("bt_load_sound fa-chevron-down");
		button.addClass("bt_collapse fa-chevron-up");

		$(".sound_container").slideDown("fast");
	}

	Enable(button);
});