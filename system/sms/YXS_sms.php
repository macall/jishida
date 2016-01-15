<?php
// +----------------------------------------------------------------------
// | Fanwe 青创o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2010 http://www.qckj.cc All rights reserved.
// +----------------------------------------------------------------------

$sms_lang = array(
	'ContentType'	=>	'消息类型',
	'ContentType_15'	=>	'普通短信通道(15)',
	'ContentType_8'	=>	'长短信通道(8)',

);
$config = array(
	'ContentType'	=>	array(
	'INPUT_TYPE'	=>	'1',
	'VALUES'	=> 	array(15,8)
	),

);
/* 模块的基本信息 */
if (isset($read_modules) && $read_modules == true)
{
    $module['class_name']    = 'YXS';
    /* 名称 */
    $module['name']    = "短信平台";
    $module['lang']  = $sms_lang;
    $module['config'] = $config;
    $module['server_url'] = 'http://api.sms.cn/mtutf8/';

    return $module;
}

// 企信通短信平台
require_once APP_ROOT_PATH."system/libs/sms.php";  //引入接口
require_once APP_ROOT_PATH."system/sms/YXS/transport.php";
//require_once APP_ROOT_PATH."system/sms/QXT/XmlBase.php";

class YXS_sms implements sms
{
	public $sms;
	public $message = "";

	private $statusStr = array(
		"00"  => "批量短信提交成功（批量短信待审批）",
		"01"  => "批量短信提交成功（批量短信跳过审批环节）",
		"03"  => "单条短信提交成功",
		"04"  => "用户名错误",
		"05" => "密码错误",
		"06" => "剩余条数不足",
		"07" => "信息内容中含有限制词(违禁词)",
		"08" => "信息内容为黑内容",
		"09" => "该用户的该内容 受同天内内容不能重复发 限制",
		"10" => "批量下限不足",
		"97" => "短信参数有误",
		"98" => "防火墙无法处理这种短信"
	);

    public function __construct($smsInfo = '')
    {
		if(!empty($smsInfo))
		{
			$this->sms = $smsInfo;
		}
    }

	public function sendSMS($mobile_number,$content)
	{
		if(is_array($mobile_number))
		{
			$mobile_number = implode(",",$mobile_number);
		}
		$sms = new transport();

				$pwd=md5($this->sms['password'].$this->sms['user_name']);
				$params = array(
					"uid"=>$this->sms['user_name'],
					"pwd"=>$pwd,
					"mobile"=>$mobile_number,
					//"content"=>urlencode(iconv("gbk","utf-8",$content))
                      "content"=>$content

				);

				$res= $sms->request($this->sms['server_url'],$params);
				/*$smsStatus = toArray($result['body']);

				$code = $smsStatus['code'][0];

				if($code=='00'||$code=='01'||$code=='03')
				{
							$result['status'] = 1;
				}
				else
				{
							$result['status'] = 0;
							$result['msg'] = $this->statusStr[$code];
				}*/
		$sz=explode("&",$res);
		if($sz[1]="stat=100")
		{

			$result['status']=100;
			$result['msg']='发送成功';
		}
		else if($sz[1]="stat=101")
		{

			$result['status']=101;
			$result['msg']='验证失败';
		}
		else if($sz[1]="stat=102")
		{

			$result['status']=102;
			$result['msg']='余额不足';
		}
		else if($sz[1]="stat=107")
		{

			$result['status']=107;
			$result['msg']='频率过快';
		}
		else
		{
			$str=substr($sz[1],5,3);
			$result['status']=$str;
			$result['msg']='发送失败';
		}
		return $result;
	}

	public function getSmsInfo()
	{

		return "短信平台";

	}

	public function check_fee()
	{
		$sms = new transport();
		$pwd=md5($this->sms['password'].$this->sms['user_name']);
			$params = array(
						"uid"=>$this->sms['user_name'],
						"pwd"=>$pwd
					);

		$url = "http://api.sms.cn/mm/";
		$result = $sms->request($url,$params);
		$jg=explode("&",$result);
		if($jg[1]='stat=100')
		{
			$tiaoshu=explode('=',$jg[2]);
			$tiaoshu=$tiaoshu[1];
		}
		//$result = toArray($result['body'],"resRoot");

		$str = "短信平台，剩余：".$tiaoshu."条";

		return $str;

	}
}
?>