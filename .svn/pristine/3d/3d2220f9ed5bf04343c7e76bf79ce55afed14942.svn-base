
$(function(){
    var nav = $(".quehuan ul li");
    var centent = $(".Choose_box");
    nav.click(function(){
        var index = nav.index($(this));
        $(this).siblings().removeClass("no");
        $(this).addClass("no");
        centent.hide();
        $(centent.get(index)).show();
    })
})

$(function(){
    var nav_a = $(".jiayti span a");
    var centent_a = $(".frame");
    nav_a.click(function(){
        var index = nav_a.index($(this));
        $(this).siblings().removeClass("no_q");
        $(this).addClass("no_q");
        centent_a.hide();
        $(centent_a.get(index)).show();
    })
})
$(function(){
    var dianj = $(".tancu");
    dianj.click(function(){
        $(".waicehng").show();
    })
    $(".determine").click(function(){
        $(".waicehng").hide();
    })
    $(".cancel").click(function(){
        $(".waicehng").hide();
    })
})
$(function(){
    var obgheight = $(window).height();
    $(".waicehng").height(obgheight)
})
$(document).ready(function(){
    $(".jia").click(function(){
        var n =$(this).prev('.zi').val();
        var num=parseInt(n)+1;
        if(num == 999){
        }

        $(this).siblings('.zi').val(num);
    });
    $(".jian").click(function(){
        var n= $(this).next('.zi').val();
        var num=parseInt(n)-1;
        if(num > 0){
            $(this).next('.zi').val(num);
        }
    });
});
$(function(){
    var nav_v = $(".ernav > ul > li");
    var centent_v = $(".rotation");
    nav_v.click(function(){
        var index = nav_v.index($(this));
        $(this).siblings().removeClass("nou");
        $(this).addClass("nou");
        centent_v.hide();
        $(centent_v.get(index)).show();
    })
})
$(function(){
    var nav_n = $(".zhongjian > span > a");
    var centent_c = $(".cententy_box");
    nav_n.click(function(){
        var index = nav_n.index($(this));
        $(this).siblings().removeClass("no_l");
        $(this).addClass("no_l");
        centent_c.hide();
        $(centent_c.get(index)).show();
    })
})
$(function(){
    var nav_n = $(".ententy_nav > ul > li");
    var centent_c = $(".details");
    nav_n.click(function(){
        var index = nav_n.index($(this));
        $(this).siblings().removeClass("lkg");
        $(this).addClass("lkg");
        centent_c.hide();
        $(centent_c.get(index)).show();
    })
})
$(function(){
    var nav_n = $(".exhibition > ul");
    nav_n.click(function(){
      $(this).siblings("ul").children(".right").removeClass("right_1");
        $(this).children(".right").addClass("right_1");

    })
})
$(function(){
    var nav_n = $(".nav a");
    nav_n.click(function(){
        $(this).siblings().removeClass("jia");
        $(this).addClass("jia");

    })
})

$(function(){
    var zi = $(".right p meter");
    var ziem = $(".right p em");
    ziem.each(function(i){
        zi.eq(i).val($(this).text());
    });
})

$(function(){
    var height = $(window).height();
    $(".queren").height(height)
})


$(function(){
    var nav_y = $(".dianj");
    var erdian = $(".zhan");
    var gunabi = $(".guan");
    var guanbi_w = $(".guan_2");
    nav_y.click(function(){
        $(".queren").show();
        $(".step").show();
        $(".complete").hide();
        $(this).parent("li").siblings().removeClass("ppp");
        $(this).parent("li").addClass("ppp");
    })
    erdian.click(function(){
        $(".complete").show();
        $(".step").hide();
        $(".ppp").remove()
    })
    guanbi_w.click(function(){
        $(".queren").hide();
    })
    gunabi.click(function(){
        $(".queren").hide();
    })
    if(erdian >= 0){ $(".complete").css("display","none");}
})
$(function(){
    var chung = $(".Detailed > p > i");
    chung.click(function(){
        $(this).parent("p").siblings().remove()
    })
})

$(function(){
    var nav_t = $(".rich_box ul li a img");
    nav_t.click(function(){
        $(".rich_box ul li a img").removeClass("addclass");
        $(this).addClass("addclass");

    })
})



















