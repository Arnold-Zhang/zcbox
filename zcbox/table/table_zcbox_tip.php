<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

include_once "table_zcbox.php";

class table_zcbox_tip extends table_zcbox
{

	static $_t = 'zcbox_tip';

	public function __construct()
	{
		$this->_table = 'zcbox_tip';
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

    public function destroy_tip($tid){
    	return DB::delete($this->_table, array('id' => $tid));
    }

}
