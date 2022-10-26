<?php

/*
 * Thông tin từ điển môn học.
 * OTTOAN
 * 14-06-2019
 */

class OTTOAN_TD_CAU_TRA_LOI extends AbstractModel {
    protected $fields = "ottoan_td_cau_tra_loi_id, "
                    . "ottoan_td_cau_tra_loi_ma, "
                    . "ottoan_td_cau_tra_loi_ten, "
                    . "ottoan_td_cau_tra_loi_ten_vn, "
                    . "ottoan_td_cau_hoi_id";
    protected $pk = 'ottoan_td_cau_tra_loi_id';
    protected $table_name = "ottoan_td_cau_tra_loi";
    
    // Hàm lấy tổng số dòng dữ liệu
//    public function getTotalData($search) {
//        $fields = "a.ottoan_td_mon_hoc_id";
//        $tables =  " ottoan_td_cau_hoi a
//                    LEFT JOIN ottoan_td_mon_hoc b ON a.ottoan_td_mon_hoc_id = b.ottoan_td_mon_hoc_id";
//        $wheres = $this->makeWhereClause($search);
//        $where = " WHERE a.ottoan_td_mon_hoc_id IS NOT NULL "
//                . "GROUP BY a.ottoan_td_mon_hoc_id";
//        $params = NULL;
//        if ($wheres){
//            $where = " AND " . $wheres['where'];
//            $params = $wheres['params'];
//        }
//        return $this->countData($fields, $tables, $where, $params);
//    }
//    // Hàm lấy tất cả data
//    public function getDataAll($search, $sort, $offset = null, $limit = null) {
//                if (is_null($limit) || is_null($offset)) {
//             $strLimit = "";
//        } else {
//             $strLimit = "LIMIT $offset, $limit";
//        }
//        $fields = "DISTINCT a.ottoan_td_cau_hoi_id,
//                    b.ottoan_td_mon_hoc_id,
//                    a.ottoan_td_cau_hoi_ma,
//                    a.ottoan_td_cau_hoi_ten_vn,
//                    b.ottoan_td_mon_hoc_ma,
//                    b.ottoan_td_mon_hoc_ten_vn,
//                    c.ottoan_td_cau_tra_loi_ma,
//                    c.ottoan_td_cau_tra_loi_ten_vn,
//                    sltl.sltl,
//                    slmon.slmon";
//        $where1 = " WHERE a.ottoan_td_mon_hoc_id IS NOT NULL ";
//        $where2 = " WHERE a2.ottoan_td_cau_tra_loi_id IS NOT NULL ";
//        $wheres = $this->makeWhereClause($search);
//        $params = NULL;
//        if ($wheres) {
//            $where1 .= " AND " . $wheres['where'];
//            $where2 .= " AND " . $wheres['where'];
//            $params = $wheres['params'];
//        }
//        $table = " ottoan_td_cau_hoi a
//                    LEFT JOIN ottoan_td_mon_hoc b ON a.ottoan_td_mon_hoc_id = b.ottoan_td_mon_hoc_id
//                    LEFT JOIN ottoan_td_cau_tra_loi c ON a.ottoan_td_cau_hoi_id = c.ottoan_td_cau_hoi_id
//                        LEFT JOIN (
//                        SELECT
//                        count( a.ottoan_td_mon_hoc_id ) slmon,
//                        a.ottoan_td_mon_hoc_id 
//                        FROM
//                        ottoan_td_cau_hoi a
//                        LEFT JOIN ottoan_td_mon_hoc b ON a.ottoan_td_mon_hoc_id = b.ottoan_td_mon_hoc_id 
//                        WHERE
//                        a.ottoan_td_mon_hoc_id IS NOT NULL 
//                        GROUP BY
//                        a.ottoan_td_mon_hoc_id 
//                        ) slmon ON a.ottoan_td_mon_hoc_id = slmon.ottoan_td_mon_hoc_id
//                        LEFT JOIN (
//                        SELECT
//                        COUNT( a2.ottoan_td_cau_tra_loi_id ) sltl,
//                        a2.ottoan_td_cau_hoi_id 
//                        FROM
//                        ottoan_td_cau_tra_loi a2 
//                        WHERE
//                        a2.ottoan_td_cau_tra_loi_id IS NOT NULL 
//                        GROUP BY
//                        a2.ottoan_td_cau_hoi_id 
//                        ) sltl ON a.ottoan_td_cau_hoi_id = sltl.ottoan_td_cau_hoi_id  ";
//        if ($sort == ""){
//            $sort = "ORDER BY c.ottoan_td_cau_tra_loi_id,";
//        }else{
//            $sort = str_replace("ORDER BY", "ORDER BY c.ottoan_td_cau_tra_loi_id,", $sort);
//        }
//        return $this->load($fields, $table, " WHERE a.ottoan_td_cau_hoi_id IS NOT NULL ", $params, $sort);
//    }
    public function getInfoById($id) {
        $fields = $this->fields;
        $table = $this->table_name;
        $where =" WHERE ottoan_td_mon_hoc_id = #a";
        $params = Array('a' => $id);
        return $this->load($fields, $table, $where, $params);
    }
    public function getAllCauHoi() {
        $fields = "ottoan_td_cau_hoi_ma, CONCAT(ottoan_td_cau_hoi_ma,'-',ottoan_td_cau_hoi_ten_vn) ten";
        $tables = $this->table_name;
        $order = 'ORDER BY ottoan_td_cau_hoi_ma';
        return $this->load($fields, $tables,null, null , $order);
    }
    public function getInfoCauTLById($id) {
        $fields = "a.ottoan_td_cau_hoi_id,
                    b.ottoan_td_cau_tra_loi_ma,
                    b.ottoan_td_cau_tra_loi_ten_vn,
                    a.ottoan_td_cau_hoi_loai ";
        $tables = " ottoan_td_cau_hoi a
                    LEFT JOIN ottoan_td_cau_tra_loi b ON a.ottoan_td_cau_hoi_id = b.ottoan_td_cau_hoi_id";  
         $wheres = "WHERE a.ottoan_td_cau_hoi_id = #a";
         $params = Array('a' => $id);
         return $this->load($fields, $tables, $wheres, $params);
    }
}
