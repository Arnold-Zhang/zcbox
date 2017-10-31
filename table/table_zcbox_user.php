<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

include_once "table_zcbox.php";

class table_zcbox_user extends table_zcbox
{
	const EVENT_SUBSCRIBE   = 1;    // 用户关注事件
	const EVENT_UNSUBSCRIBE = 2;    // 用户取关事件

	static $_t = 'zcbox_user';

	public function __construct()
	{
		$this->_table = 'zcbox_user';
		$this->_pk    = 'id';
		parent::__construct();
	}

	public function get_all_field($start = 0,$limit = 0,$field = null)
	{
		if(is_array($field)){
			$field = join(',', $field);
		}
		$sql = "select $field from %t limit $start,$limit ";
		return DB::fetch_all($sql,[$this->_table]);
	}

	public function count_by_field($k,$v)
	{
		return DB::result_first('SELECT COUNT(*) FROM %t WHERE %i ', array($this->_table, DB::field($k, $v)));
	}

	public function fetch_by_field($k,$v)
	{
		return DB::fetch_first('SELECT * FROM %t WHERE %i ', array($this->_table, DB::field($k, $v)));
	}

	public function fetch_all_by_field($k, $v, $start = 0, $limit = 0)
	{
		return DB::fetch_all('SELECT * FROM %t WHERE %i '.DB::limit($start, $limit), array($this->_table, DB::field($k, $v)));
	}

	public function getField($id, $field)
	{
		$ret = DB::fetch_first("SELECT `{$field}` FROM %t WHERE %i ", array($this->_table, DB::field('id', $id)));
		return $ret[$field];
    }

    public function getAllByOpenid($openid, $platform)
	{
        $ret = DB::fetch_first("select * from %t where %i",[$this -> _table, DB::field('openid',$openid), DB::field('platform',$platform)]);
		return $ret;
    }

	public function updateByOpenID($openid, $data)
	{
		return DB::update($this->_table, $data, DB::field('openid', $openid));
	}

	public function getUID($openid)
	{
		$rs = C::t("#zcbox#zcbox_user")->select('id')->where(['openid'	=> $openid])->first();
		return $rs['id'];
	}
}
