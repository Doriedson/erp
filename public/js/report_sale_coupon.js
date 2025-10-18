function ReportSaleCouponTotalUpdate() {

	let total = 0;

	let container = $('.reportsalecoupon_container');

	$(".w-reportsale", container).each(

		function(index, reportsale) {

			total += $(reportsale).data("total");
		}
	);

	$('.w-reportsale-total').html(total.toLocaleString("pt-BR", {minimumFractionDigits: 2, maximumFractionDigits: 2}));
}

/**
  * Enables/Disables report coupon sales from id_venda.
  */
$(document).on("change", "#frm_reportsalecoupon_chk", async function() {

  $("#frm_reportsalecoupon_id_venda").prop( "disabled", !this.checked);
	$("#frm_reportsalecoupon_data").prop( "disabled", this.checked);
	$("#frm_reportsalecoupon_id_vendastatus").prop( "disabled", this.checked);

  if (this.checked == true) {

    $("#frm_reportsalecoupon_id_venda").focus();
  }
});

/**
  * Search report coupon sales from date.
  */
$(document).on("submit", "#frm_reportsalecoupon", async function(event) {

  event.preventDefault();

  let form = $(this);

  let container = $(".reportsalecoupon_container");

  FormDisable(form);

  let data = {}

  if (this.frm_reportsalecoupon_chk.checked) {

    data = {
      action: 'reportsalecoupon_id_venda_search',
      id_venda: this.frm_reportsalecoupon_id_venda.value
    }

  } else {

    data = {
      action: 'reportsale_coupon',
      id_vendastatus: this.frm_reportsalecoupon_id_vendastatus.value,
      data: this.frm_reportsalecoupon_data.value,
    }
  }

  $(".reportsalecoupon_notfound").addClass("hidden");
  $(".reportsalecoupon_header").addClass("hidden");

  container.html(imgLoading);

  let response = await Post("report_sale_coupon.php", data);

  if (response != null) {

      container.html(response['data']);
      $('.w-reportsale-counter').html(response['counter']);
      $('.w-reportsale-total').html(response['total_formatted']);
      $('.reportsalecoupon_data').html(response['data_formatted']);

      if (this.frm_reportsalecoupon_chk.checked == false) {

        $(".reportsalecoupon_header").removeClass("hidden");
      }

  } else {

    container.html("");
    $(".reportsalecoupon_notfound").removeClass("hidden");
  }

  FormEnable(form);
});

/**
  * Expands products from sales coupon.
  */
 $(document).on("click", ".coupon_bt_expand", async function() {

    let button = $(this);

    let reportsale = button.closest('.w-reportsale');

    let id_venda = reportsale.data("id_venda");

    let container = reportsale.find(".w-reportsale-item");

    let expandable = reportsale.children(".expandable:first");

    Disable(button);
    // button.prop("disabled", true);

    button.removeClass("coupon_bt_expand fa-chevron-down");
    button.addClass("bt_collapse fa-chevron-up");

    let data = {
        action: 'coupon_expand',
        id_venda: id_venda,
    }

    let response = await Post("report_sale_coupon.php", data);

    if (response != null) {

        container.html(response);

        // expandable.removeClass("hidden");
    }

    expandable.slideDown("fast");
    Enable(button);
    // button.prop("disabled", false);
});