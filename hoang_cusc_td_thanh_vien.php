<?php

 class HOANG_CUSC_TD_THANH_VIEN extends AbstractModel{
    //$fields là mảng dữ liệu
    protected $fields = Array(
        'hoang_cusc_td_thanh_vien_id' => '',
        'hoang_cusc_td_nhom_du_an_id' => '',
        'hoang_cusc_td_thanh_vien_ma' => '',
        'hoang_cusc_td_thanh_vien_ho_lot' => '',
        'hoang_cusc_td_thanh_vien_ho_lot_vn' => '',
        'hoang_cusc_td_thanh_vien_ten' => '',
        'hoang_cusc_td_thanh_vien_ten_vn' => '',
        'hoang_cusc_td_thanh_vien_phai' => '',
        'hoang_cusc_td_thanh_vien_ngay_bd' => '',
        'hoang_cusc_td_thanh_vien_ngay_kt' => '',
    );
    //$pk là khoá chính
    protected $pk = 'hoang_cusc_td_thanh_vien_id';
    //$table_name là tên bảng được sử dụng
    protected $table_name = 'hoang_cusc_td_thanh_vien ';

    //ham lay tong so
    public function getTotalData($search){
        $fields = 'tv.hoang_cusc_td_thanh_vien_id';
        $tables = $this->table_name.' tv
                    LEFT JOIN hoang_cusc_td_nhom_du_an nhom ON tv.hoang_cusc_td_nhom_du_an_id = nhom.hoang_cusc_td_nhom_du_an_id';
        $wheres = $this->makeWhereClause($search);
        $where = NULL;
        $params = NULL;
        if($wheres){
            $where = " WHERE ". $wheres['where'];
            $params = $wheres['params'];
        }
        //print_r($tables);
        return $this->countData($fields, $tables, $where, $params);
    }//end function getTotalData

    //ham danh sach du lieu
    public function getDataAll($search, $sort, $offset = NULL, $limit = NULL){
        $fields = "tv.hoang_cusc_td_thanh_vien_id,
                   tv.hoang_cusc_td_thanh_vien_ma,
                   CONCAT(tv.hoang_cusc_td_thanh_vien_ho_lot_vn,' ',tv.hoang_cusc_td_thanh_vien_ten_vn) hoang_cusc_td_thanh_vien_ho_ten,
                   CASE tv.hoang_cusc_td_thanh_vien_phai
                        WHEN 1 then 'Nữ'
                        ELSE 'Nam' END hoang_cusc_td_thanh_vien_phai,
                    nhom.hoang_cusc_td_nhom_du_an_ma,
                    nhom.hoang_cusc_td_nhom_du_an_ten_vn,
                    tv.hoang_cusc_td_thanh_vien_ngay_bd,
                    tv.hoang_cusc_td_thanh_vien_ngay_kt";
        $table = $this->table_name.' tv
                LEFT JOIN hoang_cusc_td_nhom_du_an nhom ON tv.hoang_cusc_td_nhom_du_an_id = nhom.hoang_cusc_td_nhom_du_an_id';
        $wheres = $this->makeWhereClause($search);
        $where = NULL;
        $params = NULL;
        if($wheres){
            $where = " WHERE ". $wheres['where'];
            $params = $wheres['params'];
        }
        return $this->load($fields, $table, $where, $params, $sort, $offset, $limit);
    }// end function getDataAll
    
    //Đổ dữ liệu giới tính ra combobox
    public function getDSGioiTinh() {
        return Array(
            0 => Array('ma' => 0, 'ten' => 'Nam'),
            1 => Array('ma' => 1, 'ten' => 'Nữ')
        );
    }// end function getDSGioiTinh
    
    //ham lay thong tin theo id
    public function getInfoById($IDCanBo) {
        $fields = "nhom.hoang_cusc_td_nhom_du_an_ma manhom,
                tv.hoang_cusc_td_thanh_vien_ma macb,
                tv.hoang_cusc_td_thanh_vien_ho_lot_vn holot,
                tv.hoang_cusc_td_thanh_vien_ten_vn ten,
                tv.hoang_cusc_td_thanh_vien_phai gioitinh,
                tv.hoang_cusc_td_thanh_vien_ngay_bd ngaybd,
                tv.hoang_cusc_td_thanh_vien_ngay_kt ngaykt";
        $table = $this->table_name.' tv
                LEFT JOIN hoang_cusc_td_nhom_du_an nhom ON tv.hoang_cusc_td_nhom_du_an_id = nhom.hoang_cusc_td_nhom_du_an_id';
        $where = 'WHERE tv.hoang_cusc_td_thanh_vien_id = #a';
        $params = Array('a' => $IDCanBo);
        return $this->load($fields,$table,$where,$params);
    }//end function getInfoById
    
}// end class
?>