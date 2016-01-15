<?php if (!defined('THINK_PATH')) exit();?>

<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=7" />
<link rel="stylesheet" type="text/css" href="__TMPL__Common/style/style.css" />
<style type="text/css">
	
/**
 * 自定义的font-face
 */
@font-face {font-family: "diyfont";
  src: url('<?php echo APP_ROOT; ?>/public/iconfont/iconfont.eot?r=<?php echo time(); ?>'); /* IE9*/
  src: url('<?php echo APP_ROOT; ?>/public/iconfont/iconfont.eot?#iefix&r=<?php echo time(); ?>') format('embedded-opentype'), /* IE6-IE8 */
  url('<?php echo APP_ROOT; ?>/public/iconfont/iconfont.woff?r=<?php echo time(); ?>') format('woff'), /* chrome、firefox */
  url('<?php echo APP_ROOT; ?>/public/iconfont/iconfont.ttf?r=<?php echo time(); ?>') format('truetype'), /* chrome、firefox、opera、Safari, Android, iOS 4.2+*/
  url('<?php echo APP_ROOT; ?>/public/iconfont/iconfont.svg#iconfont&r=<?php echo time(); ?>') format('svg'); /* iOS 4.1- */}
.diyfont {
  font-family:"diyfont" !important;
  font-size:20px;
  font-style:normal;
  -webkit-font-smoothing: antialiased;
  -webkit-text-stroke-width: 0.2px;
  -moz-osx-font-smoothing: grayscale;
}

</style>
<script type="text/javascript">
 	var VAR_MODULE = "<?php echo conf("VAR_MODULE");?>";
	var VAR_ACTION = "<?php echo conf("VAR_ACTION");?>";
	var MODULE_NAME	=	'<?php echo MODULE_NAME; ?>';
	var ACTION_NAME	=	'<?php echo ACTION_NAME; ?>';
	var ROOT = '__APP__';
	var ROOT_PATH = '<?php echo APP_ROOT; ?>';
	var CURRENT_URL = '<?php echo btrim($_SERVER['REQUEST_URI']);?>';
	var INPUT_KEY_PLEASE = "<?php echo L("INPUT_KEY_PLEASE");?>";
	var TMPL = '__TMPL__';
	var APP_ROOT = '<?php echo APP_ROOT; ?>';
	
	//关于图片上传的定义
	var UPLOAD_SWF = '__TMPL__Common/js/Moxie.swf';
	var UPLOAD_XAP = '__TMPL__Common/js/Moxie.xap';
	var MAX_IMAGE_SIZE = '1000000';
	var ALLOW_IMAGE_EXT = 'zip';
	var UPLOAD_URL = '<?php echo u("File/do_upload_icon");?>';
	var ICON_FETCH_URL = '<?php echo u("File/fetch_icon");?>';
	var ofc_swf = '__TMPL__Common/js/open-flash-chart.swf';
</script>
<script type="text/javascript" src="__TMPL__Common/js/jquery.js"></script>
<script type="text/javascript" src="__TMPL__Common/js/jquery.timer.js"></script>
<script type="text/javascript" src="__TMPL__Common/js/plupload.full.min.js"></script>
<script type="text/javascript" src="__TMPL__Common/js/ui_upload.js"></script>
<script type="text/javascript" src="__TMPL__Common/js/jquery.bgiframe.js"></script>
<script type="text/javascript" src="__TMPL__Common/js/jquery.weebox.js"></script>
<link rel="stylesheet" type="text/css" href="__TMPL__Common/style/weebox.css" />
<script type="text/javascript" src="__TMPL__Common/js/swfobject.js"></script>
<script type="text/javascript" src="__TMPL__Common/js/script.js"></script>

<script type="text/javascript" src="__ROOT__/public/runtime/admin/lang.js"></script>
<script type='text/javascript'  src='__ROOT__/admin/public/kindeditor/kindeditor.js'></script>
</head>
<body>
<div id="info"></div>

<script type="text/javascript">
	function uninstall(id)
	{
		if(confirm("<?php echo L("CONFIRM_DELETE");?>"))
		{
			location.href = ROOT + "?m=Sms&a=uninstall&id="+id;
		}
	}
	function send_demo()
	{		
		$.ajax({ 
				url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=send_demo&test_mobile="+$.trim($("input[name='test_mobile']").val()), 
				data: "ajax=1",
				dataType: "json",
				success: function(obj){
					if(obj.status==0)
					{
						alert(obj.info);
					}
					else
					$("#info").html(obj.info);
				}
		});
	}
	$(document).ready(function(){
		$("input[name='test_mobile_btn']").bind("click",function(){
			var mail = $.trim($("input[name='test_mobile']").val());	
			if(mail!='')
			send_demo();
		});
	});
</script>
<div class="main">
<div class="main_title"><?php echo ($main_title); ?>
			<input type="text" class="textbox" name="test_mobile" />
			<input type="button" class="button" name="test_mobile_btn" value="<?php echo L("TEST");?>" />
</div>
<div class="blank5"></div>

<table cellspacing="0" cellpadding="0" class="dataTable" id="dataTable">
	<tbody>
		<tr>
			<td class="topTd" colspan="3">&nbsp; </td>
			</tr>
			<tr class="row">
				<th><?php echo L("SMS_NAME");?></th>
				<th><?php echo L("DESCRIPTION");?></th>		
				<th><?php echo L("TAG_LANG_OPERATE");?></th>
				</tr>
				<?php if(is_array($sms_list)): foreach($sms_list as $key=>$sms_item): ?><tr class="row">
					<td><?php echo ($sms_item["name"]); ?></td>
					<td><?php echo ($sms_item["description"]); ?></td>				
					<td>
						<?php if($sms_item['installed'] == 0): ?><a href="<?php echo u("Sms/install",array("class_name"=>$sms_item['class_name']));?>"><?php echo L("INSTALL");?></a>
						<?php else: ?>
							<a href="javascript:uninstall(<?php echo ($sms_item["id"]); ?>);" ><?php echo L("UNINSTALL");?></a>
							<a href="javascript:edit(<?php echo ($sms_item["id"]); ?>);" ><?php echo L("EDIT");?></a>
							<?php if($sms_item['is_effect'] == 0): ?><a href="<?php echo u("Sms/set_effect",array("id"=>$sms_item['id']));?>"><?php echo L("USE_THIS_SMS");?></a>
							<?php else: ?>
								<?php echo L("SMS_USING");?><?php endif; ?><?php endif; ?>
					</td>
				</tr><?php endforeach; endif; ?>
				<tr><td class="bottomTd" colspan="3"> &nbsp;</td></tr>
			</tbody>
		</table>


</div>
</body>
</html>