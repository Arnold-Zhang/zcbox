<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(file_exists(__DIR__ . "/../.env"))
	require_once __DIR__ . "/../.env";

define("APP_NAME", "总裁意见箱");
define("DATA_DIR", __DIR__ . '/..');
define( "RECORD_LIMIT_PER_PAGE", 10 );

define("SERVER_IP", "");
// 微信相关设置
define("APPID" , "weixin appid");
define("APP_SECRET", "weixin app secret");
define("TOKEN", "weixin token");
define("MCHID", "wxpay mchid");
define("PayKey", "wxpay paykey");
define("REDIRECT_URI", 'project url');
define("SCOPE", "snsapi_userinfo");
define('WXPAY_SSLCERT_PATH', 'ssl cert path');
define('WXPAY_SSLKEY_PATH', 'ssl key path');

// 意见相关设置
$TIP_STATUS = [
	0	=>	"未处理",
	1	=>	"有效",
	2	=>	"无效",
];

// 用户角色
define("ZC_USER", "0");
define("ZC_SUPER_ADMIN", "1");
define("ZC_ADMIN", "2");
?>
