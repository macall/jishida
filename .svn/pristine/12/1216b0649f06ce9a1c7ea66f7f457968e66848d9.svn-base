$(document).ready(function(){
	init_deal_item();
	
});

//商品移上
function init_deal_item()
{
	$(".deal_list .deal_item").hover(function(){
		$(this).addClass("deal_item_border_hover");	
		var fx_queue = $(this).find("a.quan").queue("fx");
		if(fx_queue!=null)
		{
			while(fx_queue.length>1)
			{
				fx_queue.pop();
			}
		}
		$(this).find("a.quan").slideDown("fast");
	},function(){
		$(this).removeClass("deal_item_border_hover");
		$(this).find("a.quan").slideUp("fast");
	});
}
//商品移上



