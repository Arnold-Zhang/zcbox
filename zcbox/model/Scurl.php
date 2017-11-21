<?php

class sCurl {
	var $ch;
	var $api;
	var $param;

	public function __construct($api, $type = 'GET', $data, $curl_config = NULL) {
		$this->ch = curl_init();
		if ( $this->ch ) {
			$this->api = $api;
			$this->param = $this->data( $type, $data );
			curl_setopt($this->ch, CURLOPT_URL, $this->api);
			curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);

			if($curl_config && is_array($curl_config)) {
				foreach ($curl_config as $k => $v) {
					curl_setopt($this->ch, $k, $v);
				}
			}
		} else {
            throw new Exception('CURL MOD can not running!');
		}
	}

	public function data( $type, $data ) {
		if ( $type == 'GET' ) {
			$param = array();
			foreach ($data as $key => $value) {
				$param[] = "$key=$value";
			}
			//modified by qqm
			if($data)
				$this->api .= "?".implode("&", $param);
		} elseif ( $type == 'POST' ) {
			$this->param = $data;
			curl_setopt($this->ch, CURLOPT_POST, 1);
			curl_setopt($this->ch, CURLOPT_POSTFIELDS, $this->param);
			// curl_setopt($this->ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json; charset=utf-8', 'Content-Length:' . strlen($data)]);
		}
	}

	public function sendRequest() {
		$reponse = curl_exec($this->ch);

		if (curl_errno($this->ch)) {
			throw new Exception(curl_error($this->ch), 0);
		} else {
			$httpStatusCode = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
			if (200 !== $httpStatusCode) {
				throw new Exception($reponse, $httpStatusCode);
			}
		}
		curl_close($this->ch);
		return $reponse;
	}
}
