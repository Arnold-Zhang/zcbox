<?php
/**
 *
 */
class User
{

	function __construct()
	{
		# code...
	}

	public function GetOpenid($wxAuthHost = NULL)
	{
		//通过code获得openid
		if (!isset($_GET['code'])){
			//触发微信返回code码
			$baseUrl = urlencode('http://'.($wxAuthHost? : $_SERVER['HTTP_HOST']).$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING']);
			$url = $this->__CreateOauthUrlForCode($baseUrl);
			Header("Location: $url");
			exit();
		} else {
			//获取code码，以获取openid
		    $code = $_GET['code'];
		    LOG::DEBUG('code:' . "$code");
			$openid = $this->GetOpenidFromMp($code);
			return $openid;
		}
	}

	private function ToUrlParams($urlObj)
	{
		$buff = "";
		foreach ($urlObj as $k => $v)
		{
			if($k != "sign"){
				$buff .= $k . "=" . $v . "&";
			}
		}

		$buff = trim($buff, "&");
		return $buff;
	}

	private function __CreateOauthUrlForCode($redirectUrl)
	{
		$urlObj["appid"] = APPID;
		$urlObj["redirect_uri"] = REDIRECT_URI;
		$urlObj["response_type"] = "code";
		$urlObj["scope"] = SCOPE;
		$urlObj["state"] = "STATE"."#wechat_redirect";
		$bizString = $this->ToUrlParams($urlObj);
		return "https://open.weixin.qq.com/connect/oauth2/authorize?".$bizString;
	}

	public function GetOpenidFromMp($code)
	{
		$url = $this->__CreateOauthUrlForOpenid($code);
		//初始化curl
		$ch = curl_init();
		//设置超时
		curl_setopt($ch, CURLOP_TIMEOUT, $this->curl_timeout);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,FALSE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		if(WXPAY_CURL_PROXY_HOST != "0.0.0.0"
			&& WXPAY_CURL_PROXY_PORT != 0){
			curl_setopt($ch,CURLOPT_PROXY, WXPAY_CURL_PROXY_HOST);
			curl_setopt($ch,CURLOPT_PROXYPORT, WXPAY_CURL_PROXY_PORT);
		}
		//运行curl，结果以jason形式返回
		$res = curl_exec($ch);
		curl_close($ch);
		//取出openid
		$data = json_decode($res,true);
		$openid = $data['openid'];
		return $openid;
	}

	private function __CreateOauthUrlForOpenid($code)
	{
		$urlObj["appid"] = APPID;
		$urlObj["secret"] = APP_SECRET;
		$urlObj["code"] = $code;
		$urlObj["grant_type"] = "authorization_code";
		$bizString = $this->ToUrlParams($urlObj);
		return "https://api.weixin.qq.com/sns/oauth2/access_token?".$bizString;
	}

	public function openid($uid){
		return C::t('#zcbox#zcbox_user')->getField($uid, 'openid');
	}

	public function getUID($openid){
		return C::t('#zcbox#zcbox_user')->getUID($openid);
	}

	public function addUser($openid){
		$data = [
			'openid'		=>	$openid,
			'create_time'	=>	date("y-m-d H:i:s",time()),
		];
		C::t("#zcbox#zcbox_user")->insert($data);
		$uid = $this->getUID($openid);
		return $uid;
	}

	public function name($uid){
		return C::t('#zcbox#zcbox_user')->getField($uid, 'name');
	}

	public function company($uid){
		return C::t('#zcbox#zcbox_user')->getField($uid, 'company');
	}

	public function is_admin($uid){
		$role_id = C::t('#zcbox#zcbox_user')->getField($uid, 'role');
		return in_array($role_id, [ZC_ADMIN, ZC_SUPER_ADMIN]);
	}

	public function is_super_admin($uid){
		$role_id = C::t('#zcbox#zcbox_user')->getField($uid, 'role');
		return in_array($role_id, [ZC_SUPER_ADMIN]);
	}

	public function can_delete($uid, $tid){
		$status = C::t("#zcbox#zcbox_tip")->getField($tid, 'status');
		if ($status != 0) {
			return false;
		}

		$tip_user = C::t("#zcbox#zcbox_tip")->getField($tid, 'uid');

		return $uid == $tip_user || $this->is_admin($uid);
	}

}
