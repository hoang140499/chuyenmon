<?php

class HOANG_TD_CAU_HOI extends AbstractModel {
    protected $fields = "ottoan_td_cau_hoi_id,ottoan_td_cau_hoi_ma,ottoan_td_cau_hoi_ten, ottoan_td_cau_hoi_ten_vn,ottoan_td_mon_hoc_id";
    protected $pk = 'ottoan_td_cau_hoi_id';
    protected $table_name = "ottoan_td_cau_hoi";
    
    // Hàm lấy tổng số dòng dữ liệu
    public function getTotalData($search) {
        $fields = " DISTINCT b.ottoan_td_mon_hoc_id";
        $tables =  " ottoan_td_cau_tra_loi a 
                    LEFT JOIN ottoan_td_cau_hoi b ON a.ottoan_td_cau_hoi_id = b.ottoan_td_cau_hoi_id
                    LEFT JOIN ottoan_td_mon_hoc c ON b.ottoan_td_mon_hoc_id = c.ottoan_td_mon_hoc_id";
        $wheres = $this->makeWhereClause($search);
        $where = " WHERE b.ottoan_td_cau_hoi_id IS NOT NULL AND c.ottoan_td_mon_hoc_id IS NOT NULL";
        $params = NULL;
        if ($wheres){
            $where .= " AND " . $wheres['where'];
            $params = $wheres['params'];
        }
        return $this->countData($fields, $tables, $where, $params);
    }
    // Hàm lấy tất cả data
    public function getDataAll($search, $sort, $offset = null, $limit = null) {
        if (is_null($limit) || is_null($offset)) {
             $strLimit = "";
        } else {
             $strLimit = "LIMIT $offset, $limit";
        }
        $fields = " DISTINCT
                    b.ottoan_td_cau_hoi_loai,
                    a.ottoan_td_cau_tra_loi_id,
                    b.ottoan_td_cau_hoi_id,
                    c.ottoan_td_mon_hoc_id,
                    a.ottoan_td_cau_tra_loi_ma,
                    a.ottoan_td_cau_tra_loi_ten_vn,
                    a.ottoan_td_cau_tra_loi_dap_an,
                    b.ottoan_td_cau_hoi_ma,
                    b.ottoan_td_cau_hoi_ten_vn,
                    c.ottoan_td_mon_hoc_ma,
                    c.ottoan_td_mon_hoc_ten_vn,
                    slmon.slmon,
                    sltl.sltl";
        $where = " WHERE slmon IS NOT NULL AND sltl IS NOT NULL";
//        $wheres = $this->makeWhereClause($search);
        $params = NULL;
//        if ($wheres) {
//            $where .= " AND " . $wheres['where'];
//            $params = $wheres['params'];
//        }
        $where_mh = ' WHERE 1=1 ';
        foreach ($search as $key => $value) {
            if ($value['col'] == 'ottoan_td_mon_hoc_ma'){
                $where_mh .= " AND ottoan_td_mon_hoc_ma LIKE '%". $value['value']. "%'";
            }
        }
        $where_ch = ' WHERE 1=1 ';
        foreach ($search as $key => $value) {
            if($value['col'] == 'ottoan_td_cau_hoi_ma'){
                $where_ch .= " AND ottoan_td_cau_hoi_ma LIKE '%" .$value['value']. "%'";
                $where_mh .= " AND ottoan_td_cau_hoi_ma LIKE '%" .$value['value']. "%'";
            }
        }
        //ottoan
        // $table = " ottoan_td_cau_hoi b
        //             LEFT JOIN ottoan_td_cau_tra_loi a ON a.ottoan_td_cau_hoi_id = b.ottoan_td_cau_hoi_id
        //             LEFT JOIN ottoan_td_mon_hoc c ON b.ottoan_td_mon_hoc_id = c.ottoan_td_mon_hoc_id,
        //             (
        //             SELECT
        //             count( b.ottoan_td_cau_hoi_id ) AS sltl,
        //             b.ottoan_td_cau_hoi_id 
        //             FROM
        //             ottoan_td_cau_tra_loi a
        //             LEFT JOIN ottoan_td_cau_hoi b ON a.ottoan_td_cau_hoi_id = b.ottoan_td_cau_hoi_id
        //             LEFT JOIN ottoan_td_mon_hoc c ON b.ottoan_td_mon_hoc_id = c.ottoan_td_mon_hoc_id 
        //             $where_ch
        //             GROUP BY
        //             b.ottoan_td_cau_hoi_id
        //             ) sltl,
        //             (
        //             SELECT
        //             count( c.ottoan_td_mon_hoc_id ) AS slmon,
        //             c.ottoan_td_mon_hoc_id 
        //             FROM
        //             ottoan_td_cau_tra_loi a
        //             LEFT JOIN ottoan_td_cau_hoi b ON a.ottoan_td_cau_hoi_id = b.ottoan_td_cau_hoi_id
        //             LEFT JOIN ottoan_td_mon_hoc c ON b.ottoan_td_mon_hoc_id = c.ottoan_td_mon_hoc_id 
        //             $where_mh
        //             GROUP BY
        //             c.ottoan_td_mon_hoc_id
        //             $strLimit
        //             ) slmon";

        $table = " ottoan_td_cau_hoi b
                LEFT JOIN ottoan_td_cau_tra_loi a ON a.ottoan_td_cau_hoi_id = b.ottoan_td_cau_hoi_id
                LEFT JOIN ottoan_td_mon_hoc c ON b.ottoan_td_mon_hoc_id = c.ottoan_td_mon_hoc_id
                LEFT JOIN (
                    SELECT b.ottoan_td_mon_hoc_id, count(b.ottoan_td_mon_hoc_id) as slmon
                    FROM ottoan_td_cau_hoi b
                    LEFT JOIN ottoan_td_cau_tra_loi a ON a.ottoan_td_cau_hoi_id = b.ottoan_td_cau_hoi_id 
                    LEFT JOIN ottoan_td_mon_hoc c ON c.ottoan_td_mon_hoc_id = b.ottoan_td_mon_hoc_id
                    $where_mh
                    GROUP BY b.ottoan_td_mon_hoc_id
                    $strLimit
                ) slmon ON c.ottoan_td_mon_hoc_id = slmon.ottoan_td_mon_hoc_id 
                LEFT JOIN (
                    SELECT b.ottoan_td_cau_hoi_id, count(a.ottoan_td_cau_tra_loi_ma) as sltl
                    FROM ottoan_td_cau_hoi b
                    LEFT JOIN ottoan_td_cau_tra_loi a ON a.ottoan_td_cau_hoi_id = b.ottoan_td_cau_hoi_id 
                    LEFT JOIN ottoan_td_mon_hoc c ON c.ottoan_td_mon_hoc_id = b.ottoan_td_mon_hoc_id
                    $where_ch
                    GROUP BY b.ottoan_td_cau_hoi_ma
                    
                ) sltl ON b.ottoan_td_cau_hoi_id = sltl.ottoan_td_cau_hoi_id ";
        if($sort == ''){
                $sort .= ' ORDER BY b.ottoan_td_cau_hoi_ma, c.ottoan_td_mon_hoc_ma, a.ottoan_td_cau_tra_loi_stt';
        }else{
                $sort .= ' ,b.ottoan_td_cau_hoi_ma, c.ottoan_td_mon_hoc_ma, a.ottoan_td_cau_tra_loi_stt';
        }
        return $this->load($fields, $table, $where , $params, $sort);
    }
    //ottoan
    // public function getInfoByIDCauHoi($id) {
    //     $fields = " ottoan_td_cau_hoi_loai,
    //                 a.ottoan_td_cau_tra_loi_dap_an,
    //                 a.ottoan_td_cau_tra_loi_stt,
    //                 a.ottoan_td_cau_tra_loi_id,
    //                 b.ottoan_td_cau_hoi_id,
    //                 c.ottoan_td_mon_hoc_id,
    //                 a.ottoan_td_cau_tra_loi_ma,
    //                 a.ottoan_td_cau_tra_loi_ten_vn,
    //                 b.ottoan_td_cau_hoi_ma,
    //                 b.ottoan_td_cau_hoi_ten_vn,
    //                 c.ottoan_td_mon_hoc_ma,
    //                 c.ottoan_td_mon_hoc_ten_vn";
    //     $table = " ottoan_td_cau_tra_loi a
    //                 LEFT JOIN ottoan_td_cau_hoi b ON a.ottoan_td_cau_hoi_id = b.ottoan_td_cau_hoi_id
    //                 LEFT JOIN ottoan_td_mon_hoc c ON b.ottoan_td_mon_hoc_id = c.ottoan_td_mon_hoc_id ";
    //     $where =" WHERE b.ottoan_td_cau_hoi_id = #a";
    //     $params = Array('a' => $id);
    //     return $this->load($fields, $table, $where, $params);
    // }

    public function getInfoByIDCauHoi($id) {
        $fields = " ottoan_td_cau_hoi_loai,
                    a.ottoan_td_cau_tra_loi_dap_an,
                    a.ottoan_td_cau_tra_loi_stt,
                    a.ottoan_td_cau_tra_loi_id,
                    b.ottoan_td_cau_hoi_id,
                    c.ottoan_td_mon_hoc_id,
                    a.ottoan_td_cau_tra_loi_ma,
                    a.ottoan_td_cau_tra_loi_ten_vn,
                    b.ottoan_td_cau_hoi_ma,
                    b.ottoan_td_cau_hoi_ten_vn,
                    c.ottoan_td_mon_hoc_ma,
                    c.ottoan_td_mon_hoc_ten_vn";
        $table = " ottoan_td_cau_hoi b
                    LEFT JOIN ottoan_td_cau_tra_loi a ON a.ottoan_td_cau_hoi_id = b.ottoan_td_cau_hoi_id
                    LEFT JOIN ottoan_td_mon_hoc c ON b.ottoan_td_mon_hoc_id = c.ottoan_td_mon_hoc_id ";
        $where =" WHERE b.ottoan_td_cau_hoi_id = #a";
        $params = Array('a' => $id);
        return $this->load($fields, $table, $where, $params);
    }

    public function getAllCauHoi() {
        $fields = "ottoan_td_cau_hoi_ma, CONCAT(ottoan_td_cau_hoi_ma,'-',ottoan_td_cau_hoi_ten_vn) ten";
        $tables = $this->table_name;
        $order = 'ORDER BY ottoan_td_cau_hoi_ma';
        return $this->load($fields, $tables, null , null , $order);
    }
    public function getIdByMa($Ma) {
        $fields = "ottoan_td_cau_hoi_id id";
        $tables = $this->table_name;
        $where = 'WHERE ottoan_td_cau_hoi_ma = #a';
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
        $r['title'] = 'Nhập mã hay tên câu hỏi';
        $r['key'] = 'ottoan_td_cau_hoi_ma';
        $fields = 'ottoan_td_cau_hoi_ma'
                . ', ottoan_td_cau_hoi_ten_vn';
        $tables = $this->table_name;
        $order = 'order by ottoan_td_cau_hoi_ma ';
        $limit = 200;

        $where = "WHERE ottoan_td_cau_hoi_ma LIKE #a OR ottoan_td_cau_hoi_ten_vn LIKE #b ";
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
