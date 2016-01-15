$(document).ready(function(){
	
	function ajax_do_submit(action,query)
	{
		$.ajax({
			url:action,
			data:query,
			dataType:"json",
			type:"POST",
			success:function(obj){
				if(obj.status)
				{					
					$.weeboxs.close("refund_form");
					alert(obj.info);
					location.reload();
				}
				else
				{
					alert(obj.info);
				}
			}
		});
	}
	
	$(".do_refund").bind("click",function(){		
		var action = $(this).attr("action");
		var query = new Object();
		query.ajax = 1;
		$.ajax({
			url:action,
			type:"POST",
			data:query,
			dataType:"json",
			success:function(obj){
				if(obj.status)
				{
					$.weeboxs.open(obj.html, {boxid:"refund_form",contentType:'text',showButton:false,title:"退款处理",width:530,onopen:function(){
						
						var form = $("#refund_form").find("form[name='refund_form']");
						var query = $(form).serialize();
						$("#confirm").bind("click",function(){
							var action = $(this).attr("action");
							ajax_do_submit(action,query);
						});
						$("#refuse").bind("click",function(){
							var action = $(this).attr("action");
							ajax_do_submit(action,query);
						});
					}});
				}
				else
				{
					alert(obj.info);
				}
				
			}
		});
	});
	
	
	$(".do_verify").bind("click",function(){		
		var action = $(this).attr("action");
		var query = new Object();
		query.ajax = 1;
		$.ajax({
			url:action,
			type:"POST",
			data:query,
			dataType:"json",
			success:function(obj){
				if(obj.status)
				{
					alert(obj.info);
					location.reload();
				}
				else
				{
					alert(obj.info);
				}
				
			}
		});
	});
	
});