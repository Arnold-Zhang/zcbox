<?php
// 只在cli模式下运行
if(PHP_SAPI != 'cli') {
    exit;
}

require __DIR__ . '/../../../class/class_core.php';
require_once __DIR__ . '/../vendor/autoload.php';

$discuz = C::app();
$discuz->init();

$LOG_FILENAME = '_set_admin';

require_once __DIR__ . '/../lib/func.php';
require_once __DIR__ . '/../config/config.php';

$user = C::t('#zcbox#zcbox_user');

// $admin_names = ['name1', 'name2', 'name3'...];
$admin_names = [];

LOG::DEBUG('------------------------------------------');
LOG::DEBUG('unset Admin start');

foreach ($admin_names as $k => $v) {
	$rs_new = $user->count_by_field("name", $v);	//判断管理员是否存在
	if(!$rs_new){
		LOG::DEBUG('admin name :' . $v . ' dont exist');
		continue;
	}
	$uid = $user->fetch_by_field('name', $v)['id'];
	$user->update($uid, ['role' => ZC_USER]);
	LOG::DEBUG('user name:' . $v . " now is not admin");
}

LOG::DEBUG('unset Admin end');
LOG::DEBUG('------------------------------------------');

?>
