<?php echo $this->fetch('./inc/header.html'); ?>
<div class="wrap">
	
	<div class="content">
	<div class="comment_list_txt1">
		<div id="Tab1">
		<div class="Menubox">
		<ul>
		   <li id="one1" onclick="setTab('one',1,7)"  class="hover" style=" border-right:1px solid #ccc;">账号登录</li>
		   <li id="one2" onclick="setTab('one',2,7)">手机登录</li>
		</ul>
		</div>
		 <div class="Contentbox">  
		   <div id="con_one_1" class="hover">
				<div class="inputtxt">
				<div class="inputpc"><i class="fa fa-user"></i></div>	
				<div class="input_sr"><input type="text" id = "email" placeholder="请输入邮箱或昵称" name="email" ></div>
				</div>
				<div class="inputtxt"> 
				<div class="inputpc"><i class="fa fa-lock"></i></div>
				<div class="input_sr"><input type="password" id = "pwd" placeholder="请输入密码" name="pwd"></div>	 
				</div>
				
				<div class="btn_login">
				<input type="Button" value="登录" onclick = "javascript:do_login()" style="background: none;">
				</div>
		   </div>
		   <div id="con_one_2" style="display:none;">
				<div class="inputtxt2">
					<div class="first">
						<input type="text" class="phone" id = "mobile" placeholder="请输入手机号"  style=" width:100%; background:none;box-shadow:none; border:none; float:none;">
					</div>
				
				
				<div class="second">
				    <input class="btn_phone" type="Button"  id = "btn_send" onclick = "javascript:do_send()" value="发送验证码" >
				</div>
				
				<div class="blank"></div>
				</div>
				<div class="inputtxt"> 
				<div class="input_sr" style=" margin-left:10px;">
					<input class="testing third" type="text" id = "code"  placeholder="请输入手机短信中的验证码">
					</div>	 
				</div>
				
				
				<div class="btn_login">
				<?php if ($this->_var['ref_uid']): ?><input type="hidden" name="ref_uid" id="ref_uid" value="<?php echo $this->_var['ref_uid']; ?>"><?php endif; ?>
				<input type="Button" value="登录" onclick = "javascript:mobile_login()" style="background: none;">
				</div>
		   </div>

		 </div>
		</div>             
		
	 </div>
	</div>
</div>
<script type="text/javascript">
var left_time_act = null;
var left_time = 0;
function setTab(name,cursel,n){
	 for(i=1;i<=n;i++){
	  var menu=document.getElementById(name+i);
	  var con=document.getElementById("con_"+name+"_"+i);
	  menu.className=i==cursel?"hover":"";
	  con.style.display=i==cursel?"block":"none";
	 }
	}
function left_time_func(){
	clearTimeout(left_time_act);
	if(left_time > 0){
		$("#btn_send").val(left_time + "秒后重新发送" );
		$("#btn_send").addClass("dis");
		$("#btn_send").css({"color":"#999","border":"1px solid #999"});
		left_time --;
		left_time_act = setTimeout(left_time_func,1000);
	}
	else{
		$("#btn_send").css({"color":"#fc8600","border":"1px solid #fc8600"});
		$("#btn_send").removeClass("dis");
		$("#btn_send").val("重新发送" );
	}
}	
function  do_send(){
	if($("#btn_send").hasClass("dis")) return false;
	var mobile=$("#mobile").val();	
	
	if(!mobile){
		alert("请填写手机号码");
		return false;	
	}
	
	//alert(mobile.length);
	if(mobile.length != 11){
		alert("请填写正确的手机号码");
		return false;	
	}
	
	//http://o2o.fanwe.net/sjmapi/index.php?act=register_verify_phone&mobile=13559110609&is_login=1&r_type=2
	var query = new Object();
	
	query.mobile = mobile;
	query.is_login = 1;
	//query.r_type = 1;
	query.post_type = "json";
	var ajaxurl = '<?php
echo parse_wap_url_tag("u:index|register_verify_phone|"."".""); 
?>';
	//alert(ajaxurl);
	
	$.ajax({
		url:ajaxurl,
		data:query,
		type:"Post",
		dataType:"json",
		success:function(data){
			alert(data.info);
			
			if(data.status == 1){
				left_time = 60;
				left_time_func();
				//location.replace(document.referrer);
				//window.location.href = "<?php
echo parse_url_tag("u:index|index#index|"."".""); 
?>";
			}else{
				
			}
		}
		,error:function(){
			alert("服务器提交错误");
		}
	});
}

function  mobile_login(){
	
	var mobile=$("#mobile").val();	
	var ref_uid=$("#ref_uid").val();
	if(!mobile){
		alert("请填写手机号码");
		return false;	
	}
	
	//alert(mobile.length);
	if(mobile.length != 11){
		alert("请填写正确的手机号码");
		return false;	
	}	
	
	var code=$("#code").val();	
	if(!code){
		alert("请填入验证码");
		return false;	
	}
	
	//http://o2o.fanwe.net/sjmapi/index.php?act=register_verify_code&mobile=13559110609&is_register=1&code=9602&r_type=2
	var query = new Object();
	if(ref_uid){
		query.ref_uid=ref_uid;
	}
	query.mobile = mobile;
	query.is_register = 1;
	query.code = code;
	query.post_type = "json";
	var ajaxurl = '<?php
echo parse_wap_url_tag("u:index|register_verify_code|"."".""); 
?>';
	//alert(ajaxurl);
	
	$.ajax({
		url:ajaxurl,
		data:query,
		type:"Post",
		dataType:"json",
		success:function(data){
			alert(data.info);
			
			if(data.user_login_status == 1){
				if(document.referrer.indexOf("login") > 0){
					window.location.href = "<?php
echo parse_wap_url_tag("u:index|index#index|"."".""); 
?>";
				}else{
					location.replace(document.referrer);
				}
			}
		}
		,error:function(){
			alert("服务器提交错误");
		}
	});
}

function  do_login(){
		
	var obj1=$("#email").val();
	var obj2=$("#pwd").val();
	if(!obj1){
		alert("请填写账户或邮箱");
		return false;	
	}
	if(!obj2){
		alert("请填写密码");
		return false;	
	}	
	
	var query = new Object();
	query.email = obj1;
	query.pwd = obj2;
	query.post_type = "json";
	var ajaxurl = '<?php
echo parse_wap_url_tag("u:index|login|"."".""); 
?>';
	//alert(ajaxurl);
	
	$.ajax({
		url:ajaxurl,
		data:query,
		type:"Post",
		dataType:"json",
		success:function(data){
			alert(data.info);
			if(data.user_login_status == 1){
				if(document.referrer.indexOf("login") > 0){
					window.location.href = "<?php
echo parse_wap_url_tag("u:index|index#index|"."".""); 
?>";
				}else{
					location.replace(document.referrer);
				}
			}
		}
		,error:function(){
			alert("服务器提交错误");
		}
	});
	return false;
}
    
</script>

<?php echo $this->fetch('./inc/footer.html'); ?> 