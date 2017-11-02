<?php

class WxPayDataBase
{
	public $values = array();

	/**
	* 设置签名，详见签名生成算法
	* @param string $value
	**/
	public function SetSign()
	{
		$sign = $this->MakeSign();
		$this->values['sign'] = $sign;
		return $sign;
	}

	/**
	* 获取签名，详见签名生成算法的值
	* @return 值
	**/
	public function GetSign()
	{
		return $this->values['sign'];
	}

	/**
	* 判断签名，详见签名生成算法是否存在
	* @return true 或 false
	**/
	public function IsSignSet()
	{
		return array_key_exists('sign', $this->values);
	}

	/**
	 * 输出xml字符
	 * @throws WxPayException
	**/
	public function ToXml()
	{
		if(!is_array($this->values)
			|| count($this->values) <= 0)
		{
    		Log::ERROR("数组数据异常！");
    	}

    	$xml = "<xml>";
    	foreach ($this->values as $key=>$val)
    	{
    		if (is_numeric($val)){
    			$xml.="<".$key.">".$val."</".$key.">";
    		}else{
    			$xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
    		}
        }
        $xml.="</xml>";
        return $xml;
	}

    /**
     * 将xml转为array
     * @param string $xml
     * @throws WxPayException
     */
	public function FromXml($xml)
	{
		if(!$xml){
			Log::ERROR("xml数据异常！");
		}
        //将XML转为array
        $this->values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
		return $this->values;
	}

	/**
	 * 格式化参数格式化成url参数
	 */
	public function ToUrlParams()
	{
		$buff = "";
		foreach ($this->values as $k => $v)
		{
			if($k != "sign" && $v != "" && !is_array($v)){
				$buff .= $k . "=" . $v . "&";
			}
		}

		$buff = trim($buff, "&");
		return $buff;
	}

	/**
	 * 生成签名
	 * @return 签名，本函数不覆盖sign成员变量，如要设置签名需要调用SetSign方法赋值
	 */
	public function MakeSign()
	{
		//签名步骤一：按字典序排序参数
		ksort($this->values);
		$string = $this->ToUrlParams();
		//签名步骤二：在string后加入KEY
		$string = $string . "&key=".PayKey;
		//签名步骤三：MD5加密
		$string = md5($string);
		//签名步骤四：所有字符转为大写
		$result = strtoupper($string);
		return $result;
	}

	/**
	 * 获取设置的值
	 */
	public function GetValues()
	{
		return $this->values;
	}

	public function SetData($key, $value)
	{
		$this->values[$key] = $value;
	}
}

/**
 *
 * 接口调用结果类
 * @author widyhu
 *
 */
class WxPayResults extends WxPayDataBase
{
	/**
	 *
	 * 检测签名
	 */
	public function CheckSign()
	{
		if(!$this->IsSignSet()){
			return true;
		}

		$sign = $this->MakeSign();
		if($this->GetSign() == $sign){
			return true;
		}
		Log::ERROR("签名错误！");
	}

	/**
	 *
	 * 使用数组初始化
	 * @param array $array
	 */
	public function FromArray($array)
	{
		$this->values = $array;
	}

	/**
	 *
	 * 使用数组初始化对象
	 * @param array $array
	 * @param 是否检测签名 $noCheckSign
	 */
	public static function InitFromArray($array, $noCheckSign = false)
	{
		$obj = new self();
		$obj->FromArray($array);
		if($noCheckSign == false){
			$obj->CheckSign();
		}
        return $obj;
	}

    /**
     * 将xml转为array
     * @param string $xml
     * @throws WxPayException
     */
	public static function Init($xml)
	{
		$obj = new self();
		$obj->FromXml($xml);
		$obj->CheckSign();
        return $obj->GetValues();
	}
}

class Weixin
{

	function __construct()
	{

	}

	public static function valid($isfirsttime = false) {
		$echoStr = $_GET["echostr"];

		//valid signature , option
		if(self::checkSignature()){
			if ( $isfirsttime ) {
				echo $echoStr;
				exit;
			} else {
				return true;
			}
		}
	}

	private function checkSignature()
	{
	    $signature = $_GET["signature"];
	    $timestamp = $_GET["timestamp"];
	    $nonce = $_GET["nonce"];

	    $token = TOKEN;
	    $tmpArr = array($token, $timestamp, $nonce);
	    sort($tmpArr, SORT_STRING);
	    $tmpStr = implode( $tmpArr );
	    $tmpStr = sha1( $tmpStr );

	    if( $tmpStr == $signature ){
	        return true;
	    }else{
	        return false;
	    }
	}

	/**
	 *
	 * 产生随机字符串，不长于32位
	 * @param int $length
	 * @return 产生的随机字符串
	 */
	public static function getNonceStr($length = 32)
	{
		$chars = "abcdefghijklmnopqrstuvwxyz0123456789";
		$str ="";
		for ( $i = 0; $i < $length; $i++ )  {
			$str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);
		}
		return $str;
	}

	public static function sendLuckyMoney($openid, $money){
		$url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack";

		$wxpaydata = new WxPayDataBase;
		$wxpaydata->values = array(
			'mch_billno' => time(),	//商户订单号
			'mch_id' => MCHID,	//商户号
			'wxappid' => APPID,	//appid
			'send_name' => APP_NAME,	//发送者名称
			're_openid' => $openid,	//用户openid
			'total_amount' => $money,	//金额（分）
			'total_num' => 1,	//红包人数
			'wishing' => "-",	//祝福语
			'client_ip' => SERVER_IP,	//调用接口的ip地址
			'act_name' => "意见奖励",	//活动名称
			'remark' => "-",	//备注
			'scene_id' => "PRODUCT_4",	//场景id
			'nonce_str' => self::getNonceStr(),	//随机字符串
		);

		$wxpaydata->SetSign();

		$xml = $wxpaydata->ToXml();

		$data = self::postXmlCurl($xml, $url, true);
		$rs = WxPayResults::Init($data);
		LOG::DEBUG(print_r($rs, true));

		return $rs;
	}

	/**
	 * 以post方式提交xml到对应的接口url
	 * Enter description here ...
	 * @param string $xml  需要post的xml数据
	 * @param string $url  url
	 * @param bool $useCert 是否需要证书，默认不需要
	 * @param int $second   url执行超时时间，默认30s
	 * @throws WxPayException
	 */
	public static function postXmlCurl($xml, $url, $useCert = false, $second = 30)
	{
        //初始化curl
       	$ch = curl_init();
		//设置超时
		curl_setopt($ch, CURLOPT_TIMEOUT, $second);

        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
		//设置header
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

		if($useCert == true){
			//设置证书
			//使用证书：cert 与 key 分别属于两个.pem文件
			curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
			curl_setopt($ch,CURLOPT_SSLCERT, WXPAY_SSLCERT_PATH);
			curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
			curl_setopt($ch,CURLOPT_SSLKEY, WXPAY_SSLKEY_PATH);
		}
		//post提交方式
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
		//运行curl
        $data = curl_exec($ch);
		//返回结果
		if($data){
			curl_close($ch);
			return $data;
		} else {
			$error = curl_errno($ch);
			curl_close($ch);
			Log::ERROR("curl出错，错误码:$error");
		}
	}
}
