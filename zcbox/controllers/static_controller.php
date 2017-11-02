<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$name = $user->name($uid);
$company = $user->company($uid);
$is_admin = $user->is_admin($uid);
$is_super_admin = $user->is_super_admin($uid);
$status = $_GET['status'];
$is_rewarded = $_GET['rewarded'];

// 超级管理员进入首页优先显示有效意见
if (!isset($status)) {
	if ($is_super_admin) {
		$status = 1;
	}else {
		$status = 0;
	}
}

if ($status != 'all') {
	$where = ['status'	=>	$status];
}

if ($is_rewarded) {
		$where['rewarded'] = 1;
	}

if ($is_admin) {
	$tips = C::t("#zcbox#zcbox_tip")
		->where($where)
		->limit(($page - 1) * RECORD_LIMIT_PER_PAGE, RECORD_LIMIT_PER_PAGE)
		->order('create_time desc')
		->get();
	$counts = C::t("#zcbox#zcbox_tip")
		->where($where)
		->order('create_time desc')
		->count();
}else{
	$where['uid'] = $uid;
	$tips = C::t("#zcbox#zcbox_tip")
		->where($where)
		->limit(($page - 1) * RECORD_LIMIT_PER_PAGE, RECORD_LIMIT_PER_PAGE)
		->order('create_time desc')
		->get();
	$counts = C::t("#zcbox#zcbox_tip")
		->where($where)
		->order('create_time desc')
		->count();
}

unset($_GET['page']);
$pagehtm = getPages($counts, $page - 1, RECORD_LIMIT_PER_PAGE,'/index.php?'.http_build_query($_GET));

include template('zcbox:common/index');
?>
