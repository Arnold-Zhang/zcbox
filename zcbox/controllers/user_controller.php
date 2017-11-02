<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$act = $_GET['act'];
switch ($act) {
	case 'update':
		$name = trim($_GET['name']);
		$company = trim($_GET['company']);
		if ($name == '' || $company == '') {
			$_SESSION['msg']['error'] = "用户名或公司名称不能为空";
			$_SESSION['register']['name'] = $name;
			$_SESSION['register']['company'] = $company;
		}else{
			$data = [
				'name'	=>	$name,
				'company'	=>	$company,
			];
			$is_exist = C::t("#zcbox#zcbox_user")->where(['name' => $data['name']])->count();
			if ($is_exist) {
				$_SESSION['msg']['error'] = "用户名已存在";
				$_SESSION['register']['name'] = $name;
				$_SESSION['register']['company'] = $company;
			}else{
				$rs = C::t("#zcbox#zcbox_user")->update($uid, $data);
			}
		}

		dheader('location:index.php?visit=1&uid=' . $uid);
		break;

	case 'value':
		# code...
		break;

	default:
		# code...
		break;
}
?>
