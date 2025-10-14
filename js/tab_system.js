/**
  * Event on tab click.
  */
$(document).on("click", "ul.tabs li", function(event) {

    $("ul.tabs li").removeClass("active");
    $(this).addClass("active");

    var id = $(this).attr('id');

    $(".tab_content").hide();
    $(".tab_content#tab" + id).show();

    // console.log(id);
    // console.log($(this)[0]);
});

// /**
//   * Product search.
//   */
// $(document).on("hover", ".aba", function(event) {

//     function fnAba(){

//     	$(".aba").unbind('click');
//     	$(".aba").unbind('hover');

//         $(".aba").click(function(){
    		
//     	});
        
//         $(".aba").hover(
//             function(){$(this).addClass("ativa")},
//             function(){$(this).removeClass("ativa")}
//         );      
//     }          
// });