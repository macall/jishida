$(document).ready(function(){

	init_side_deal_item();

});
function init_side_deal_item()
{
	$(".side_deal_list li").hover(function(){
		$(this).addClass("active");
	},function(){
		$(this).removeClass("active");
	});
}