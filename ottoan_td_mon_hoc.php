<?php

/*
 * Thông tin từ điển môn học.
 * OTTOAN
 * 14-06-2019
 */

class OTTOAN_TD_MON_HOC extends AbstractModel {
    protected $fields = "ottoan_td_mon_hoc_id, "
                    . "ottoan_td_mon_hoc_ma, "
                    . "ottoan_td_mon_hoc_ten, "
                    . "ottoan_td_mon_hoc_ten_vn";
    protected $pk = 'ottoan_td_mon_hoc_id';
    protected $table_name = "ottoan_td_mon_hoc";
    
    // Hàm lấy tổng số dòng dữ liệu
    public function getTotalData($search) {
        $fields = $this->pk;
        $table =  $this->table_name;
        $wheres = $this->makeWhereClause($search);
        if ($wheres){
            $where = " WHERE " . $wheres['where'];
            $param = $wheres['params'];
            return $this->countData($fields, $table, $where, $param);
        }else{
            return $this->countData($fields, $table, NUlL, NULL);
        }
    }
    // Hàm lấy tất cả data
    public function getDataAll($search, $sort, $offset = null, $limit = null) {
        $fields = "ottoan_td_mon_hoc_id, "
                . "ottoan_td_mon_hoc_ma, "
                . "ottoan_td_mon_hoc_ten_vn";
        $table = $this->table_name;
        $where = NULL;
        $params = NULL;
        $wheres = $this->makeWhereClause($search);
        if($wheres){
            $where = " WHERE " . $wheres['where'];
            $params = $wheres['params'];
        }
        return $this->load($fields, $table, $where, $params, $sort, $offset, $limit);
    }
    public function getInfoById($id) {
        $fields = $this->fields;
        $table = $this->table_name;
        $where =" WHERE ottoan_td_mon_hoc_id = #a";
        $params = Array('a' => $id);
        return $this->load($fields, $table, $where, $params);
    }
    public function getAllMonHoc() {
            $fields = "ottoan_td_mon_hoc_ma, CONCAT(ottoan_td_mon_hoc_ma,'-',ottoan_td_mon_hoc_ten_vn) ten";
        $tables = $this->table_name;
        $order = 'ORDER BY ottoan_td_mon_hoc_ma';
        return $this->load($fields, $tables,null, null , $order);
    }
    public function getIdByMa($Ma) {
        $fields = "ottoan_td_mon_hoc_id id";
        $tables = $this->table_name;
        $where =" WHERE ottoan_td_mon_hoc_ma = #a";
        $params = Array('a' => $Ma);
        $res = $this->load($fields, $tables, $where, $params);
        return (empty($res)) ? '' : $res[0]['id'];
    }
    
    /**
     * @author ottoan - 20/06/2019
     * @abstract lấy dữ liệu đưa vào lookup
     * @param type $input
     * @param type $wheres
     * @param type $offset
     * @param type $listInput
     * @return boolean
     */
    public function getDataLookup($input, $wheres = "", $offset = 0, $listInput = Array()) {
        $r = Array();
        $r['title'] = 'Nhập mã hay tên môn học';
        $r['key'] = 'ottoan_td_mon_hoc_ma';
        $fields = 'ottoan_td_mon_hoc_ma'
                . ', ottoan_td_mon_hoc_ten_vn';
        $tables = $this->table_name;
        $order = 'order by ottoan_td_mon_hoc_ma ';
        $limit = 200;

        $where = "WHERE ottoan_td_mon_hoc_ma LIKE #a OR ottoan_td_mon_hoc_ten_vn LIKE #b ";
        $params = Array('a' => '%' . $input . '%', 'b' => '%' . $input . '%');

        if ($wheres) {
            $where .= " AND " . $wheres;
        }
        //ddphat 23/11/2017 Hiệu chỉnh thêm điều kiện tìm kiếm theo lớp sinh hoạt
        $r['data'] = $this->load($fields, $tables, $where, $params, $order, $offset, $limit);

        $r['continue'] = true;
        if (count($r['data']) > $limit) {
            unset($r['data'][$limit]);
        }

        return $r;
    }
}
