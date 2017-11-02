<?php

require '../../class/class_core.php';
require_once __DIR__ . '/vendor/autoload.php';

// 加载配置
require_once __DIR__ . "/config/config.php";
// 加载通用方法
require_once __DIR__ . "/lib/func.php";

// 初始化 discuz 内核对象
$discuz = C::app();
$discuz -> init();

if ( $_GET['echostr'] ) {
    Weixin::valid(true);
    exit;
}

// 用户登录检测
require_once __DIR__ . '/controllers/user_check.php';

$mod = $_GET['mod'] ? : 'index';
$page = $_GET['page'];
$visit = $_GET['visit'];

switch ( $mod ) {
	case 'index':
		require_once "controllers/static_controller.php";
		break;

	case "user":
		require_once "controllers/user_controller.php";
		break;

	case "tip":
		require_once "controllers/tip_controller.php";
		break;
}
