<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />	
    <!-- Mobile Devices Support @begin -->
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0;" name="viewport">
    <meta content="black" name="apple-mobile-web-app-status-bar-style">
    <meta content="false" name="twcClient" id="twcClient">
    <meta content="no-cache,must-revalidate" http-equiv="Cache-Control">
    <meta content="no-cache" http-equiv="pragma">
    <meta content="0" http-equiv="expires">
    <!--允许全屏模式-->
    <meta content="yes" name="apple-mobile-web-app-capable" />
    <!--指定sari的样式-->
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta content="telephone=no" name="format-detection" />
    <!-- Mobile Devices Support @end -->
	<title><?php echo $this->_var['data']['page_title']; ?></title>
	<!--
	<link rel="stylesheet" type="text/css" href="./css/style.css" >
	<link rel="stylesheet" type="text/css" href="./css/activity_list.css">  
	<link rel="stylesheet" type="text/css" href="./css/activity_subject.css"> 
	<link rel="stylesheet" type="text/css" href="./css/business_details.css"> 
	<link rel="stylesheet" type="text/css" href="./css/business_list.css"> 
	<link rel="stylesheet" type="text/css" href="./css/good_details.css">
	<link rel="stylesheet" type="text/css" href="./css/personal_index.css"> 
	<link rel="stylesheet" type="text/css" href="./css/login.css"> 
	<link rel="stylesheet" type="text/css" href="./css/order.css">
	<link rel="stylesheet" type="text/css" href="./css/comment_list.css">
	<link rel="stylesheet" type="text/css" href="./css/search.css">
	<link rel="stylesheet" type="text/css" href="./css/color.css">
	-->
	<link rel="stylesheet" type="text/css" href="./css/font-awesome/css/font-awesome.css"/>        
	<script type="text/javascript" src="./js/jquery-1.6.2.min.js"></script>
	<script type="text/javascript" src="./js/public_jquery.js" ></script>
	
	<script type="text/javascript">
		var APP_ROOT = '<?php echo $this->_var['APP_ROOT']; ?>';
		var APP_ROOT_ORA = '<?php echo $this->_var['PC_URL']; ?>';
	</script>
	
    <?php
		$this->_var['pagecss'][] = $this->_var['TMPL_REAL']."/css/style.css";	
		$this->_var['pagecss'][] = $this->_var['TMPL_REAL']."/css/activity_list.css";	
		$this->_var['pagecss'][] = $this->_var['TMPL_REAL']."/css/activity_subject.css";	
		$this->_var['pagecss'][] = $this->_var['TMPL_REAL']."/css/business_details.css";	
		$this->_var['pagecss'][] = $this->_var['TMPL_REAL']."/css/business_list.css";	
		$this->_var['pagecss'][] = $this->_var['TMPL_REAL']."/css/good_details.css";	
		$this->_var['pagecss'][] = $this->_var['TMPL_REAL']."/css/personal_index.css";
		$this->_var['pagecss'][] = $this->_var['TMPL_REAL']."/css/login.css";	
		$this->_var['pagecss'][] = $this->_var['TMPL_REAL']."/css/order.css";	
		$this->_var['pagecss'][] = $this->_var['TMPL_REAL']."/css/comment_list.css";
		$this->_var['pagecss'][] = $this->_var['TMPL_REAL']."/css/search.css";
		$this->_var['pagecss'][] = $this->_var['TMPL_REAL']."/css/color.css";
			
	?>
   <link rel="stylesheet" type="text/css" href="<?php 
$k = array (
  'name' => 'parse_css',
  'v' => $this->_var['pagecss'],
);
echo $k['name']($k['v']);
?>" />
<link rel="stylesheet" type="text/css" href="css/style.css">
	<script src="js/jquery.js"></script>
	<script src="js/myswipe.js"></script>
	<script src="js/swipe.js"></script>
	<script src="js/jqcery-mian.js"></script>
</head> 
<body>
<link rel="stylesheet" href="css/head.css" type="text/css">
<!--header-->
<section class="order_data_box">
    <section class="top_data">
        <img src="images/bj_3.png">
        <ul>
            <li class="return"><a href="index.php">返回</a> </li>
            <li class="phone"><?php if ($this->_var['user_info']): ?><img src="<?php echo $this->_var['user_info']['user_avatar']; ?>" width="59" height="59"><i><?php echo $this->_var['user_info']['user_name']; ?><?php else: ?>登录</i><?php endif; ?></li>
        </ul>
    </section>
</section>
<!--header end-->	