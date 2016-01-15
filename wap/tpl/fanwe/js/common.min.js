        function click_c(nav_1)
        {   
				$("#quyu").removeClass("quyu2");
				$("#quyu").addClass("quyu1");
				$("#paixu").removeClass("paixu2");
				$("#paixu").addClass("paixu1");
			 document.getElementById('nav_2').style.display = "none";
			 document.getElementById('nav_3').style.display = "none";
			$("#uls_2 ul").css("display",'none');
			$("#nav_1").css("width","300%")
			$("#nav_1 span").css("margin-left","14%")
			var uls = document.getElementById('uls').getElementsByTagName('ul');
	
		for (var i = 0; i < uls.length; i++) {
		uls[i].style.display = 'none';
					}
		var id=document.getElementById('nav_1');
		
		
            if(document.getElementById('nav_1').style.display != "block")
            {
                document.getElementById('nav_1').style.display = "block";
				$("#car").removeClass("caret");
				$("#car").addClass("acaret");
				//var cha=document.getElementById('cate1').getElementsByTagName('a')[0].innerHTML="选择分类∧";
				//console.log(cha);
            }
            else
            {
                document.getElementById(nav_1).style.display = "none";
				$("#car").removeClass("acaret");
				$("#car").addClass("caret");
				//var cha=document.getElementById('cate1').getElementsByTagName('a')[0].innerHTML="选择分类∨";
            }
        }
		
		
        function click_b(nav_2)
        {	
			document.getElementById('nav_1').style.display = "none";
			 document.getElementById('nav_3').style.display = "none";
			 $("#nav_2 span").css("margin-left","14%")
			 $("#uls ul").css("display",'none');
			 $("#nav_2").css("width","300%")
			 $("#car").removeClass("acaret");
				$("#car").addClass("caret");
				$("#paixu").removeClass("paixu2");
				$("#paixu").addClass("paixu1");
			var uls = document.getElementById('uls_2').getElementsByTagName('ul');
	
			for (var i = 0; i < uls.length; i++) {
			uls[i].style.display = 'none';
						}
			var id=document.getElementById('nav_2');
            if(document.getElementById('nav_2').style.display != "block")
            {	
                document.getElementById('nav_2').style.display = "block";
				$("#quyu").removeClass("quyu1");
				$("#quyu").addClass("quyu2");
				
				//console.log(cha);
            }
            else
            {
                document.getElementById(nav_2).style.display = "none";
			
				$("#quyu").removeClass("quyu2");
				$("#quyu").addClass("quyu1");
            }
        }
		
		
		
		
		
        function click_a(nav_3)
        {		
			$("#quyu").removeClass("quyu2");
				$("#quyu").addClass("quyu1");
				$("#car").removeClass("acaret");
				$("#car").addClass("caret");
			document.getElementById('nav_1').style.display = "none";
			 document.getElementById('nav_2').style.display = "none";
			 $("#uls_2 ul").css("display",'none');
			  $("#uls ul").css("display",'none');
            if(document.getElementById('nav_3').style.display != "block")
            {
                document.getElementById('nav_3').style.display = "block";
				//var cha=document.getElementById('cate3').getElementsByTagName('a')[0].innerHTML="默认排序∧";
				//console.log(cha);
				$("#paixu").removeClass("paixu1");
				$("#paixu").addClass("paixu2");
            }
            else
            {
                document.getElementById(nav_3).style.display = "none";
				//var cha=document.getElementById('cate3').getElementsByTagName('a')[0].innerHTML="默认排序∨";
				$("#paixu").removeClass("paixu2");
				$("#paixu").addClass("paixu1");
            }
        }
		
	$('document').ready(function(){
		
		$(".show_m").bind("click",function(){
			$("#nav_1").css("width","151%")
			$("#nav_1 span").css("margin-left","28%")
			$(".show_m a").css('border-left',"3px solid #f7f7f7"); 
			$(".show_m a").css('background-color',"#f7f7f7"); 
			var uls = $("#uls ul");
			obj1 = $(this).attr('title');
			uls.each(function(){
				$(this).css("display",'none');
			});
			 if($('#cud_'+obj1).css("display") != "block")
	            {
	                $('#cud_'+obj1).css("display","block");
					
	            }
	       $(this).find("a").css('background-color',"#eeeeee");
		     $(this).find("a").css('border-left',"3px solid #7c170a");  
		});
		
		
		
		
		
		$(".show_q").bind("click",function(){
			$("#nav_2").css("width","149.2%");
			$("#nav_2 span").css("margin-left","28%");
			
			$(".show_q a").css('background-color',"#f7f7f7");
			$(".show_q a").css('border-left',"3px solid #f7f7f7");  
			var uls = $("#uls_2 ul");
			obj1 = $(this).attr('title');
			uls.each(function(){
				$(this).css("display",'none');
			});
		   //if($("#quan_"+obj1).css("display") == "block")
           $("#quan_"+obj1).css("display","block");	
           
	       $(this).find("a").css("background-color","#eeeeee"); 
		    $(this).find("a").css('border-left',"3px solid #7c170a");
		});
		
	});




	
	
		
	
   
