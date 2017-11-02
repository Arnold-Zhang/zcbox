<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

/**
 * 用户自动登录入口(微信)
 * 拦截微信端浏览器发起的请求, 若拦截到则自动登录
 * 获取登录用户的uid, openid 并存于session中
 */
session_start();
$user = new User();

if(DEV_ENV) {
	$uid = $_GET['uid']? : $_SESSION['uid'];
} else {
	$uid = $_SESSION['uid'];
}

$openid = $_SESSION['openid']? : NULL;

if (empty($uid)) {
	$openid = $user->GetOpenid();
	$_SESSION['openid'] = $openid;
}elseif (empty($openid)) {
	$openid = $user->openid($uid);
	$_SESSION['openid'] = $openid;
}

if(empty($uid) && !empty($openid)) {
	$uid = $user->getUID($openid);
	if (empty($uid)) {
		// new user
		$uid = $user->addUser($openid);
	}
	$_SESSION['uid'] = $uid;
}
LOG::DEBUG('user id:'. $uid .' openid:' . $openid);
