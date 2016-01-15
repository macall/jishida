$(document).ready(function () { 

  var x=$(document).height();
   $(".hide_list").height(x-93);
   /*--隐藏菜单透明背景高度--*/
  
   $(".h_search").click(function(){
       $(".pull_down").toggle();
  });
  /*头部右边的菜单显示隐藏*/
  
/*--------------------------------------------------------------------------------------------------一以下为20140910修改的头部菜单-------*/  
  $(".mall-cate li").click(function(){
  	if($(this).hasClass("this")){
		$(this).removeClass("this");
		$(this).find("i").removeClass("fa-caret-up").addClass("fa-sort-desc");
		$(".hide_list").hide();
		
	}
	else{
		$(".hide_list").show();
		var y=$(this).index();
		$(".abbr").hide();
		$(".abbr").eq(y).show();
		/*-----------------------------------*/
		//alert(y);
		$(".abbr").eq(y).find(".second_list ul").eq(0).show();
		/*-----------------------------------*/
		$(".mall-cate li").removeClass("this");
		$(".hide_list").show();
		$(".mall-cate li i").removeClass("fa-caret-up").addClass("fa-sort-desc");
		$(this).addClass("this");
		$(this).find("i").addClass("fa-caret-up").removeClass("fa-sort-desc");
	}	   
  })
  /*隐藏菜单的事件操作*/
 
 
 
/*--------------------------------------20140905-----------*/  
 $(".second_list ul").hide();
 $(".directory").click(function(){
  	            $(".directory").removeClass("select");
  	            $(this).addClass("select");
				var z=$(this).index();
				var a=$(this).parent().parent().parent().index();
				
				$(".second_list ul").hide();
				$(".abbr").eq(a).find(".second_list").find("ul").eq(z).show();
				//alert(a);
               })

/*--------------------------------------------------------------------------------------------------一以下为20140910修改的头部菜单-------*/  
      $(".color_set li").click(function(){
	  $(".color_set li").removeClass("this");
	  $(this).addClass("this");  
   })

        $(".size_set li").click(function(){
	  $(".size_set li").removeClass("this");
	  $(this).addClass("this");  
   })

  $(".list-view .hot_search_list").eq(0).show();

/*-----------------------------滑动星星评分-------------下------------------------- */
$(".tx_star .five_star_grey i").mouseover(function(){
 	    var t=$(this).index() + 1;
        $(".tx_star .five_star_grey i").css("color","#e2e2e2");
	 	$(".tx_star .five_star_grey i:lt("+t+")").css("color","#fc8600");
        $(".tx_star b span").html(t);
})
/*-----------------------------滑动星星评分---------------上------------------------- */
 });