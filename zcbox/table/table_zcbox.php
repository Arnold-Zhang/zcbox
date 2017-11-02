<?php
defined('IN_DISCUZ') || exit('Access Denied');

/**
 * sample:
 *      C::t('#mcs#mcs_user')
 *      ->select('id, uid')
 *      ->where(['id' => 1,  'ids' => [1,2,3], 'update_time' => ['value' => $time, 'glue' => 'like']])
 *      ->anyWhere([['a' => 123, 'b' => 345]], 'or', 'and')
 *      ->group('status, type')
 *      ->order('id desc')
 *      ->limit(1, 20)
 *      ->get()/first()/count()
 */


class table_zcbox extends discuz_table
{

    // 迟静态绑定版 $this -> _table;
    static $_t;   // 默认表
    static $_t1;  // 表1 为多表查询备用
    static $_t2;  // 表2 为多表查询备用

    private $select = '';
    private $where = '';
    private $limit = '';
    private $order = '';
    private $group = '';
    private $anyWhere = '';
    private $stringWhere = '';

    private $sql = ''; //sql语句
    private $sql_count = ''; //用于计数的sql语句,不包含limit
    private $sql_value = ''; //用于替换sql语句中的占位符
    private $sql_has_limit = false; //检查sql语句是否有limit

    private $where_number = 0; //用户where条件计数
    private $anyWhere_number = 0; //用户where中条件计数

    private $where_connector = '';

    private function _before_sql()
    {
        $this->sql = '';
        $this->sql_count = '';
        $this->sql_value = '';
        $this->sql_has_limit = false;
    }

    private function _after_sql()
    {
        $this->select = '';
        $this->where = '';
        $this->limit = '';
        $this->order = '';
        $this->group = '';
        $this->anyWhere = [];
        $this->where_connector = '';
        $this->stringWhere = '';
    }

    private function handleSql()
    {
        $this->_before_sql();

        $this->sql = " from %t ";


        // 查询条件and
        if (is_string($this->where) && $this->where) {
            $this->sql .= " where {$this->where}";
        } elseif (is_array($this->where) && $this->where) {
            $this->where_number = count($this->where);
            $where = str_repeat(' %i ' . $this->where_connector, $this->where_number);
            $where = rtrim($where, $this->where_connector);
            $this->sql .= " where $where";
        } else {
            $this->sql .= " where 1";
        }

        // 查询条件 内连接符 or/and 外连接符 and/or
        if ($this->anyWhere) {
            $this->anyWhere_number = count($this->anyWhere[0]);
            $anyWhere = str_repeat(' %i ' . $this->anyWhere[1], $this->anyWhere_number);
            $anyWhere = rtrim($anyWhere, $this->anyWhere[1]);
            $this->sql .= " {$this->anyWhere[2]} ( $anyWhere )";
        }

        // 查询条件string
        if ($this->stringWhere) {
            $this->sql .= " {$this->stringWhere}";
        }

        // 分组
        if($this->group) {
            $this->sql .= " group by {$this->group} ";
        }

        // 排序
        if($this -> order){
            $this->sql .= " order by {$this -> order} ";
        }

        // 计数SQL语句
        $this->sql_count = $this->sql;

        // 分页
        if($this->limit) {
            $this->sql .= " limit {$this->limit}";
            // 标记limit 用于first方法
            $this->sql_has_limit = true;
        }

        $this->sql_value = [static::$_t];
        if($this->where_number) {
            foreach($this->where as $k => $v) {
                if(is_array($v) && isset($v['value']) && $v['glue']) {
                    $this->sql_value[] = DB::field($k, $v['value'], $v['glue']);
                } else {
                    $this->sql_value[] = DB::field($k, $v);
                }
            }
        }

        if ($this->anyWhere_number) {
            foreach($this->anyWhere[0] as $v) {
                foreach ($v as $k1 => $v1) {
                    if(is_array($v1) && isset($v1['value']) && $v1['glue']) {
                        $this->sql_value[] = DB::field($k1, $v1['value'], $v1['glue']);
                    } else {
                        $this->sql_value[] = DB::field($k1, $v1);
                    }
                }
            }
        }

        // select
        if($this->select) {
            $this->sql = "SELECT {$this->select} {$this->sql}";
        } else {
            $this->sql = "SELECT * {$this->sql}";
        }
        $this->sql_count = "SELECT count(*) {$this->sql_count}";
        $this->_after_sql();
    }

    // 增

    public function batchInsert($data)
    {
        $field = '';
        $values = '';

        foreach ($data[0] as $k => $v) {
            $field .= DB::quote_field($k) . ',';
        }
        $field = rtrim($field, ',');

        foreach ($data as &$v) {
            foreach ($v as &$vv) {
                $vv = DB::quote($vv);
            }
        }
        $_l = '(';
        $_r = ')';
        foreach ($data as $dv) {
            $tmp  = '';
            $tmp .= implode(',', $dv);
            $values .= $_l . rtrim($tmp, ',') . $_r. ',';
        }
        $values = rtrim($values, ',');
        $sql  = '';
        $sql .= 'INSERT INTO %t';
        $sql .= '('.$field.')';
        $sql .= 'VALUES ' . $values;

        return DB::query($sql, [$this->_table]);
    }

    // 删

    // 改

    // 查所有和计数
    public function getResAndCount()
    {
        $this->handleSql();
        return [
            'res' 	=> DB::fetch_all($this->sql, $this->sql_value),
            'count' => DB::result_first($this->sql_count, $this->sql_value)
        ];
    }

    // 查所有
    public function get()
    {
        $this->handleSql();
        return DB::fetch_all($this->sql, $this->sql_value);
    }

    // 查一条
    public function first()
    {
        $this->handleSql();
        // sql语句有limit的话就移除limit
        if ($this->sql_has_limit) {
            $this->sql = substr($this->sql, 0, strrpos($this->sql, 'limit'));
        }
        return DB::fetch_first($this->sql . ' limit 1', $this->sql_value);
    }

    // 查数量
    public function count()
    {
        $this->handleSql();
        return DB::result_first($this->sql_count, $this->sql_value);
    }

    public function select($field = '*')
    {
        if($field) $this->select = $field;
        return $this;
    }

    public function where($where = [], $connector = 'AND')
    {
        $this->where = empty($where) ? 1 : $where;
        $this->where_connector = $connector;
        return $this;
    }

    public function anyWhere($anyWhere = [], $connectorIn = 'OR', $connectorOut = 'AND')
    {
        // 过滤空数组
        if (empty($anyWhere)) return $this;
        $anyWhere = array_filter($anyWhere);
        if (empty($anyWhere)) return $this;
        if(is_array($anyWhere)) {
            $this->anyWhere = [$anyWhere, $connectorIn, $connectorOut];
        }
        return $this;
    }

    public function stringWhere($str)
    {
        if (empty($str)) return $this;
        $this->stringWhere = $str;
        return $this;
    }

    public function order($field)
    {
        if (empty($field)) return $this;
        $this -> order = $field;
        return $this;
    }

    public function group($field)
    {
        $this->group = $field;
        return $this;
    }

    public function limit($start, $size = 0)
    {
        $start = intval($start) <= 0 ? 0 : intval($start);
        $size  = intval($size)  <= 0 ? 0 : intval($size);
        $this->limit = $size == 0 ? $start : $start . ',' . $size;
        return $this;
    }

    public function getLastSql()
    {
        return DB::format($this->sql, $this->sql_value);
    }

}
