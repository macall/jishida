<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <title>首页</title>
</head>
<link rel="stylesheet" type="text/css" href="css/style.css">
<script src="js/jquery.js"></script>
<script src="js/myswipe.js"></script>
<script src="js/swipe.js"></script>
<script src="js/jqcery-mian.js"></script>
<body>
<header class="header_box">
    <ul>
        <li class="title">技师达</li>
        <li class="coordinates"><a href="<?php
echo parse_wap_url_tag("u:index|index2#index|"."".""); 
?>"><?php if ($this->_var['data']['city_name']): ?><?php echo $this->_var['data']['city_name']; ?><?php else: ?>全国<?php endif; ?><i class="fa fa-angle-down"></i></a></li>
        <li class="rightlogin"><?php if ($this->_var['user_info']): ?><a href="index.php?ctl=user_center"><?php echo $this->_var['user_info']['user_name']; ?></a><?php else: ?><a href="index.php?ctl=register">注册</a>|<a href="index.php?ctl=login">登录</a><?php endif; ?> </li>
    </ul>
</header>
<!--banner-->
<section class="banner">
    <div id="mySwipe" class="swipe">
        <div class="swipe-wrap" >
            <div data-index="0"> <a href=""><img src="images/banle.jpg" ></a></div>
            <div data-index="1"> <a href=""><img src="images/banle.jpg"></a></div>
            <div data-index="2"> <a href=""><img src="images/banle.jpg"></a></div>
        </div>
    </div>
    <ul class="position" id="position">
        <li class=""></li>
        <li class=""></li>
        <li class=""></li>
    </ul>
</section>
<!--content-->
    <section class="content">
        <section class=" vertical-view">
            <ul>
                <li>
                    <a href="service.html">
                        <img src="images/service.png">
                        <p>预约服务</p>
                    </a>
                </li>
                <li>
                    <a href="technician.html">
                        <img src="images/technician.png">
                        <p>预约技师</p>
                    </a>
                </li>
                <li>
                    <a href="Personal.html">
                        <img src="images/personal.png">
                        <p>我的按摩</p>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <img src="images/Customer-.png">
                        <p>在线客服</p>
                    </a>
                </li>
            </ul>
        </section>
        <section class=" horizontal-view">
            <ul>
                <?php $_from = $this->_var['data']['supplier_deal_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'supplier_deal_item');$this->_foreach['supplier_deal_item'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['supplier_deal_item']['total'] > 0):
    foreach ($_from AS $this->_var['key'] => $this->_var['supplier_deal_item']):
        $this->_foreach['supplier_deal_item']['iteration']++;
?>
				<?php if ($this->_var['key'] < 3): ?>
				<li>
                    <a href="<?php
echo parse_wap_url_tag("u:index|goodsdesc#index|"."id=".$this->_var['supplier_deal_item']['id']."".""); 
?>">
                        <img src="<?php echo $this->_var['supplier_deal_item']['img']; ?>">
                    </a>
                </li>
				<?php endif; ?>
				<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
            </ul>
        </section>
        <section class="Choose_box">
            <section class="Choose">
				<?php $_from = $this->_var['data']['allgoodslistallgoodslist']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'supplier_deal_item');$this->_foreach['supplier_deal_item'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['supplier_deal_item']['total'] > 0):
    foreach ($_from AS $this->_var['key'] => $this->_var['supplier_deal_item']):
        $this->_foreach['supplier_deal_item']['iteration']++;
?>
				 <a href="<?php
echo parse_wap_url_tag("u:index|goodsdesc#index|"."id=".$this->_var['supplier_deal_item']['id']."".""); 
?>"><ul>
                    <li>
                    	<img src="<?php echo $this->_var['supplier_deal_item']['img']; ?>" width="140" height="85">
						<P>
							<i class="fl h3_1"><?php if ($this->_var['supplier_deal_item']['sub_name']): ?><?php echo $this->_var['supplier_deal_item']['sub_name']; ?><?php else: ?><?php echo $this->_var['supplier_deal_item']['name']; ?><?php endif; ?></i>
							<i class="fr red"><?php echo $this->_var['supplier_deal_item']['current_price']; ?>元</i>
						</P>
						<P>
							<i class="fl secon">销量：<?php echo $this->_var['supplier_deal_item']['buy_count']; ?></i>
							<i class="fr secon">40分钟</i>
						</P>
						<P>
							<i class="fl secon"><?php echo $this->_var['supplier_deal_item']['name']; ?></i>
							<a href="service_data.html" class="fr">预约</a>
						</P>
                    </li>
                </ul></a>
				<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
            </section>
        </section>
    </section>
<script>
    $(function() {
        var elem = document.getElementById('mySwipe');
        window.mySwipe = Swipe(elem, {
            startSlide: 0,
            speed: 500,
            auto: 5000,
            continuous: true,
            disableScroll: true,
            stopPropagation: false,
            callback: function(index, element) {
                var i = bullets.length;
                while (i--) {
                    bullets[i].className = ' ';
                }
                bullets[index].className = 'on';
            },
            transitionEnd: function(index, element) {},
            getPos: function(index, element) {
                alert(index);
            }

        });
        var bullets = document.getElementById('position').getElementsByTagName('li');
        bullets[0].className = 'on';
    });
</script>
<?php echo $this->fetch('./inc/footer.html'); ?> 
