<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if(file_exists(__DIR__ . "/../.env"))
	require_once __DIR__ . "/../.env";

define("APP_NAME", "总裁意见箱");
define("DATA_DIR", __DIR__ . '/..');
define( "RECORD_LIMIT_PER_PAGE", 10 );

// 意见相关设置
$TIP_STATUS = [
	0	=>	"未处理",
	1	=>	"有效",
	2	=>	"无效",
];

// 用户角色
define("ZC_USER", "0");	//普通用户
define("ZC_SUPER_ADMIN", "1");	//超级管理员，可发微信红包
define("ZC_ADMIN", "2");	//普通管理员，可操作意见
?>
