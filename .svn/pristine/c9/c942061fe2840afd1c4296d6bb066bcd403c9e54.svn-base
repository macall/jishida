$(document).ready(function(){
	 
	$(".exchange").bind("click",function(){
		var url=$(this).attr("url");
		$.showConfirm("确定要兑换吗？",function(){				
			exchange(url);
		});			
	});
	
});



function exchange(url){	
		var ajaxurl = url;
		$.ajax({ 
			url: ajaxurl,
			dataType: "json",
			type: "GET",
			success: function(obj){
				if(obj.status==2){
					ajax_login();
				}else if(obj.status==1){
					$.showSuccess("兑换成功",function(){
						location.href = obj.url;
					});				
				}else{
					$.showErr(obj.info);
				}
			},
			error:function(ajaxobj)
			{
				
			}
		});		

}



