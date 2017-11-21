<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$act = $_GET['act'];
switch ($act) {
	case 'store':
		$title = trim($_GET['title']);
		$content = trim($_GET['content']);
		$note = trim($_GET['note']);

		if ($content == '' || $title == '') {
			$_SESSION['msg']['error'] = "问题或意见为空";
			$_SESSION['advice']['title'] = $title;
			$_SESSION['advice']['content'] = $content;
			$_SESSION['advice']['note'] = $note;
		}else{
			$data = [
				'uid'	=>	$uid,
				'title'	=>	$title,
				'content'	=>	$content,
				'note'	=>	$note,
			];

			$rs = C::t("#zcbox#zcbox_tip")->insert($data);
		}

		dheader('location:index.php?&uid=' . $uid);
		break;

	case 'destroy':
		$tid = $_GET['tid'];
		$can_delete = $user->can_delete($uid, $tid);
		if (!$can_delete) {
			$_SESSION['msg']['error'] = "您无权操作";
		}else{
			$rs = C::t("#zcbox#zcbox_tip")->destroy_tip($tid);
		}
		dheader('location:index.php?&uid=' . $uid);
		break;

	case 'score':
		$is_admin = $user->is_admin($uid);
		if (!$is_admin) {
			$_SESSION['msg']['error'] = "您无权操作";
		}else{
			$tid = $_GET['tid'];
			$score = $_GET['score'];
			if ($score != 0) {

				$data = [
					'status'	=>	$score,
				];
				$rs = C::t("#zcbox#zcbox_tip")->update($tid, $data);
			}
		}
		dheader('location:index.php?&uid=' . $uid);
		break;

	case 'reward':
		$is_super_admin = $user->is_super_admin($uid);
		if (!$is_super_admin) {
			$_SESSION['msg']['error'] = "仅超级管理员可以做出该操作";
		}else {
			$reward = $_GET['money'];
			$tid = $_GET['tid'];

			$status = C::t("#zcbox#zcbox_tip")->getField($tid, 'status');
			$reward_status = C::t("#zcbox#zcbox_tip")->getField($tid, 'rewarded');

			if ($status == 2) {
				$_SESSION['msg']['error'] = "这是一条无效建议";
			}else {
				if (!$reward_status) {

					$reward_user = C::t("#zcbox#zcbox_tip")->getField($tid, 'uid');
					$reward_openid = C::t("#zcbox#zcbox_user")->getField($reward_user, 'openid');
					$rewarded = Weixin::sendLuckyMoney($reward_openid, $reward*100);
					LOG::DEBUG('userid:' . $uid . ', username: ' . $user->name($uid) . ' send reward');

					if ($rewarded['result_code'] == 'SUCCESS') {
						$send_reward = C::t("#zcbox#zcbox_tip")->update($tid, ['rewarded' => 1]);
						$money_reward = C::t("#zcbox#zcbox_tip")->update($tid, ['money' => $reward]);
						if ($status == 0) {
							C::t("#zcbox#zcbox_tip")->update($tid, ['status' => 1]);
						}
						$_SESSION['msg']['success'] = $rewarded['return_msg'];
						LOG::DEBUG('tip id:' . $tid . ' rewarded success.');
					}else {
						$_SESSION['msg']['error'] = $rewarded['return_msg'];
						LOG::DEBUG('tip id:' . $tid . ' rewarded fail.');
					}

				}else {
					$_SESSION['msg']['error'] = "已奖励过该条意见";
				}
			}
		}

		dheader('location:index.php?&uid=' . $uid);

		break;

	case 'reply':
		$is_admin = $user->is_admin($uid);
		if (!$is_admin) {
			$_SESSION['msg']['error'] = "仅管理员可以回复";
		}else {
			$reply = trim($_GET['reply']);
			$tid = trim($_GET['tid']);
			if (isset($reply) && $reply != '') {
				$rs = C::t("#zcbox#zcbox_tip")->update($tid, ['reply' => $reply]);

				$tip_user = C::t("#zcbox#zcbox_tip")->getField($tid, 'uid');
				$tip_data = C::t("#zcbox#zcbox_tip")->select("title as content, create_time as tip_time")->where(['id' => $tid])->first();
				$tip_openid = C::t("#zcbox#zcbox_user")->getField($tip_user, 'openid');
				$reply_msg = Weixin::sendReplyMsg($tip_openid, $reply, $tip_data);

				// 返回码识别
				if ($reply_msg['errcode'] == 43004) {
					$_SESSION['msg']['error'] = "需要接收者关注公众号";
				}elseif ($reply_msg['errcode'] == 0) {
					$_SESSION['msg']['success'] = "回复提醒已发送至用户微信";
				}

				LOG::DEBUG("sendReplyMsg:" . print_r($reply_msg,true));
			}else{
				include template('zcbox:tip/reply');
				exit;
			}
		}

		dheader('location:index.php?&uid=' . $uid);

		break;

	case 'delete_reply':
		$is_admin = $user->is_admin($uid);
		if (!$is_admin) {
			$_SESSION['msg']['error'] = "仅管理员可以删除回复";
		}else {
			$tid = trim($_GET['tid']);
			C::t("#zcbox#zcbox_tip")->update($tid, ['reply' => '']);
			LOG::DEBUG("user " . $uid . " delete tip reply, tid: " . $tid);
		}

		dheader('location:index.php?&uid=' . $uid);
		break;

	default:
		# code...
		break;
}
?>
