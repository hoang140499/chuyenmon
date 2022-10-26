<?php
    include_once __SITE_PATH . "/modules/com/list.php";
    include_once __SITE_PATH . "/modules/com/form.php";
    include_once __SITE_PATH . "/modules/com/search.php";
    include_once __SITE_PATH . "/model/hoang_cusc_td_thanh_vien.php";
    include_once __SITE_PATH . "/model/hoang_cusc_td_nhom_du_an.php";

    class hoangtdthanhvienController extends AbstractController{
        private $form;
        private $searchMng;
        private $form_them_tu_file;
       
        public function init() {
            //Khoi tao form search
            $this->searchMng = new SearchManager('frmMain');
            $this->searchMng->setSortable(true); //hien thi cot sap xep tang dan/giam dan
            $this->searchMng->regCom('hoang_cusc_td_thanh_vien_ma', true, Array(__ERR_VALID_STR => ''), NULL, NULL, NULL, Array('txt'));
            $this->searchMng->regCom('hoang_cusc_td_thanh_vien_ho_ten', true, Array(__ERR_VALID_STR => ''), NULL, 
                                        Array('col' => "CONCAT(tv.hoang_cusc_td_thanh_vien_ho_lot,' ',tv.hoang_cusc_td_thanh_vien_ten)"), NULL, Array('txt'));
            $this->searchMng->regCom('hoang_cusc_td_nhom_du_an_ma', true, Array(__ERR_VALID_STR => ''), NULL, NULL, NULL, Array('cmba','txt'));
            $this->searchMng->regCom('hoang_cusc_td_nhom_du_an_ten', FALSE, Array(__ERR_VALID_STR => ''), NULL, NULL, NULL, Array('txt'));

            // Khoi tao form them/cap nhat
            $this->form = new SimpleFloatForm(__APP_CONTROLLER, 'Cập nhật thành viên'); // co 2 loai form 1 loai mo modal 1 loai mo link moi
            $this->form->addRowAsCom('hoang_cusc_td_nhom_du_an_ma', Array('cmb','txt'), NULL, NULL, "class='notered'");
            $this->form->addRowAsCom('hoang_cusc_td_thanh_vien_ma', Array('txt'), NULL, NULL, "class='notered'");
            $this->form->addRowAsCom('hoang_cusc_td_thanh_vien_ho_lot', Array('txt'), NULL, NULL, "class='notered'");
            $this->form->addRowAsCom('hoang_cusc_td_thanh_vien_ten', Array('txta'), NULL, NULL, "class='notered'");
            $this->form->addRowAsCom('hoang_cusc_td_thanh_vien_phai', Array('cmb'), NULL, NULL, "class='notered'");
            $this->form->addRowAsCom('hoang_cusc_td_thanh_vien_ngay_bd', Array('date'), NULL, NULL);
            $this->form->addRowAsCom('hoang_cusc_td_thanh_vien_ngay_kt', Array('txt'), NULL, NULL);
            $this->form->addSubmitRow(UIHelper::Button("btnSave", "bt_" .__APP_CONTROLLER . "_submit", "Thực hiện", "onclick='thucHienThem()'"));

            // khoi tao form them tu file
            $this->form_them_tu_file= new SimpleFloatForm('form_them_tu_file', 'Thêm dự án từ file', '500', '', TRUE);
            $this->form_them_tu_file->addRow("Chọn file", UIHelper::Upload("up_Them_tu_file", "up_Them_tu_file", NULL, __UPLOAD_SIZE_EXCEL));
            //goi toi ham public_sample
            $link = UIHelper::Link('#', 'Tải file mẫu', '', Array('class' => 'download', 'onclick' => 'newTab("frmMain","' . __HOST_NAME . __APP_PATH . __APP_CONTROLLER . '/public_sample")'));
            $this->form_them_tu_file->addSubmitRow(UIHelper::Button('btnSave', 'bt_' . 'form_them_tu_file' . '_submit', 'Thực hiện', '') . $link);
            $this->form_them_tu_file->showRequireRow('');
            
        }// end function init

        public function index() {
            $nhhk_dang_xet = 20211;
            $nam_vao = 2010;
            $hk_vao = 2;
            $so_hk_dao_tao = 8;
            if ($so_hk_dao_tao % 2 == 0) {
                $so_nam_dao_tao = $so_hk_dao_tao / 2;
                $nhhk_ra = ($nam_vao + $so_nam_dao_tao).''.$hk_vao;
            }else{
                $so_hk_dao_tao --;
                $so_nam_dao_tao = $so_hk_dao_tao / 2;
                if ($hk_vao == 2) {
                    $hk_vao = 1;
                    $nhhk_ra = ($nam_vao + $so_nam_dao_tao + 1).''.$hk_vao;
                }
                else{
                    $nhhk_ra = ($nam_vao + $so_nam_dao_tao).''.($hk_vao + 1);
                }   
            }
            print_r($nhhk_ra);
            
            $modelThanhVien = new HOANG_CUSC_TD_THANH_VIEN();

            //lay thong tin tu form tim kiem
            $this->searchMng->setValue($this->objectsValue);
            $sort = $this->searchMng->getSort();
            $search = $this->searchMng->getSearch();

            //Thuc hien phan trang
            $total = $modelThanhVien->getTotalData($search);
            $paging = (PagingHelper::getCurPage($this->objectsValue, __APP_CONTROLLER, $total));
            $curPage = $paging['cur_page'];
            $limit = $paging['limit'];
            $offset = $paging['offset'];

            //Khai bao bien data lay danh sach ket qua tim kiem
            $data = $modelThanhVien->getDataAll($search, $sort, $offset, $limit);
            $params = new SListParams();
            $params->name = __APP_CONTROLLER;
            $params->id_col = Array('hoang_cusc_td_thanh_vien_id'); // hien thi truong id moi hang
            $params->hide_cols = Array('hoang_cusc_td_thanh_vien_id' => ''); // an truong id
            $params->offset = $offset;
            $params->data = $data;

            //hien thi cot chon
            $params->select_col = UserInfo::checkPermission("delete"); //kiem tra quyen

            //khai bao hien thi cot sua du lieu
            $params->edit_col = UserInfo::checkPermission("edit");
            //print_r(__SITE_PATH . __SAMPLE_PATH . 'cusc/them_tu_file_td_thanh_vien_hoang.xls'); die;
            //dua du lieu ra view
            //print_r($total);
            $list = SList::getHTML($params);
            $this->registry->template->form = $this->form->getHTML();
            $this->registry->template->paging_top = PagingHelper::getHTML('frmMain', __APP_CONTROLLER, $curPage, $total, false);
            $this->registry->template->paging_bottom = PagingHelper::getHTML('frmMain', __APP_CONTROLLER, $curPage, $total, true, null);
            $this->registry->template->list = $list;
            $this->registry->template->title = "Tự điển thành viên";
            $this->registry->template->form_them_tu_file = $this->form_them_tu_file->getHTML();
            $this->registry->template->data = $this->searchMng->getHtml();
            $this->registry->template->show(__APP_CONTROLLER . "_index");
        }//end function index

        //in
        public function public_in() {
            $this->process_inxuat();
        }// end function public_in

        //excel
        public function public_excel() {
            $this->process_inxuat(FALSE);
        }// end function public_excel

        //in vs excel
        private function process_inxuat($in=TRUE) {
            $modelThanhVien = new HOANG_CUSC_TD_THANH_VIEN();
            $print = new PrintHelper('main', __PRINT_TYPE_A4);
            $this->searchMng->setValue($this->objectsValue);
            $sort = $this->searchMng->getSort();
            $search = $this->searchMng->getSearch();
            $getList = $modelThanhVien->getDataAll($search, $sort);
            if (empty($getList)) {
                // khong co du lieu
                $print->showNoDataMsg();
                $this->registry->template->export($print->getHTML());
                return;
            }
            //Xoa truong id khi in
            $list = $print->removeFields(Array("hoang_cusc_td_thanh_vien_id"), $getList);
            $keys = $list[0];
            //print_r($keys);
            $keys['hoang_cusc_td_thanh_vien_ngay_bd'] = Array('is_date' => TRUE); //Xu ly ngay bat dau tro ve dung dinh dang
            $keys['hoang_cusc_td_thanh_vien_ngay_kt'] = Array('is_date' => TRUE); //Xu ly ngay ket thuc tro ve dung dinh dang
            $num_col = count($keys) + 1;
            $title = Array();
            $title[] = PrintCom::Logo(2, $num_col - 2);
            $title[] = PrintCom::Title('TỰ ĐIỂN CÁN BỘ', $num_col);
            //print_r($list);
            $print->addMainTitle($title);
            $print->addBody(SimpleList::makeHeader($list[0], NULL, NULL, NULL, TRUE), $list, $keys);
            if ($in) {
                $this->registry->template->export($print->getHTML(true));
            } else {
                $this->registry->template->export($print->getHTML(true), __EXPORT_TYPE_EXCEL, __APP_CONTROLLER);
            }
        }// endfunction process_inxuat

        //kiem tra du lieu nhap vao
        public function validate_add() {
            $dbHelper = DBHelper::getInstance();
            //lay du lieu tu form
            $maNhom = $this->objectsValue[$this->form->getNameHTML('hoang_cusc_td_nhom_du_an_ma')];
            $maCB = $this->objectsValue[$this->form->getNameHTML('hoang_cusc_td_thanh_vien_ma')];
            $holot = $this->objectsValue[$this->form->getNameHTML('hoang_cusc_td_thanh_vien_ho_lot')];
            $ten = $this->objectsValue[$this->form->getNameHTML('hoang_cusc_td_thanh_vien_ten')];
            $gioiTinh = $this->objectsValue[$this->form->getNameHTML('hoang_cusc_td_thanh_vien_phai')];
            $ngayBD = $this->objectsValue[$this->form->getNameHTML('hoang_cusc_td_thanh_vien_ngay_bd')];
            $ngayKT = $this->objectsValue[$this->form->getNameHTML('hoang_cusc_td_thanh_vien_ngay_kt')];

            //Kiem tra rong vs kiem tra kieu du lieu va tra ve thong bao loi
            MsgHelper::checkValue($dbHelper->getDBInfo('hoang_cusc_td_nhom_du_an_ma')->getTitle(), Array(__ERR_REQUIRE => '', __ERR_VALID_STR => ''), $maNhom);
            MsgHelper::checkValue($dbHelper->getDBInfo('hoang_cusc_td_thanh_vien_ma')->getTitle(), Array(__ERR_REQUIRE => '', __ERR_VALID_STR => ''), $maCB);
            MsgHelper::checkValue($dbHelper->getDBInfo('hoang_cusc_td_thanh_vien_ho_lot')->getTitle(), Array(__ERR_REQUIRE => '', __ERR_VALID_STR => ''), $holot);
            MsgHelper::checkValue($dbHelper->getDBInfo('hoang_cusc_td_thanh_vien_ten')->getTitle(), Array(__ERR_MIN_LENGTH => '11', __ERR_LENGTH => '11', __ERR_REQUIRE => '', __ERR_VALID_STR => ''), $ten);
            MsgHelper::checkValue($dbHelper->getDBInfo('hoang_cusc_td_thanh_vien_phai')->getTitle(), Array(__ERR_REQUIRE => '', __ERR_VALID_STR => ''), $gioiTinh);
            MsgHelper::checkValue($dbHelper->getDBInfo('hoang_cusc_td_thanh_vien_ngay_bd')->getTitle(), Array(__ERR_REQUIRE => '', __ERR_DATE => ''), $ngayBD);
            MsgHelper::checkValue($dbHelper->getDBInfo('hoang_cusc_td_thanh_vien_ngay_kt')->getTitle(), Array(__ERR_DATE => ''), $ngayKT);

            if ($ngayBD != '' && $ngayKT != '' && Util::strToTime($ngayBD) > Util::strToTime($ngayKT)) {
                MsgHelper::addErr('err_ngaykt', 'err_ngaykt', 'Giá trị ngày kết thúc không thể nhỏ hơn ngày bắt đầu.');
            }
            if ($gioiTinh != 0 && $gioiTinh != 1) {
                MsgHelper::addErrByCode(__ERR_EXIST, __ERR_EXIST, $dbHelper->getDBInfo('hoang_cusc_td_thanh_vien_phai')->getTitle());
            }
            if (!MsgHelper::hasError()) {
                $modelNhomDuAn = new HOANG_CUSC_TD_NHOM_DU_AN();
                //kiem tra ma co ton tai trong csdl khong
                if (!$modelNhomDuAn->check(Array('hoang_cusc_td_nhom_du_an_ma' => strtoupper(Util::stripUnicode($maNhom))))) {
                    MsgHelper::addErrByCode(__ERR_EXIST, __ERR_EXIST, $dbHelper->getDBInfo('hoang_cusc_td_nhom_du_an_ma')->getTitle());
                }
                $modelThanhVien = new HOANG_CUSC_TD_THANH_VIEN();
                //kiem tra ma co ton tai trong csdl khong
                if ($modelThanhVien->check(Array('hoang_cusc_td_thanh_vien_ma' => strtoupper(Util::stripUnicode($maCB))))) {
                    MsgHelper::addErrByCode(__ERR_EXIST, __ERR_EXIST, $dbHelper->getDBInfo('hoang_cusc_td_thanh_vien_ma')->getTitle());
                }
            }
            
            return !MsgHelper::hasError();
        }// end function validate_add

        //thuc hien thao tac them voi dieu kien validate tra ve true
        public function process_add() {
            $dbHelper = DBHelper::getInstance();
            //lay du lieu tu form
            $maNhom = $this->objectsValue[$this->form->getNameHTML('hoang_cusc_td_nhom_du_an_ma')];
            $maCB = $this->objectsValue[$this->form->getNameHTML('hoang_cusc_td_thanh_vien_ma')];
            $holot = $this->objectsValue[$this->form->getNameHTML('hoang_cusc_td_thanh_vien_ho_lot')];
            $ten = $this->objectsValue[$this->form->getNameHTML('hoang_cusc_td_thanh_vien_ten')];
            $gioiTinh = $this->objectsValue[$this->form->getNameHTML('hoang_cusc_td_thanh_vien_phai')];
            $ngayBD = $this->objectsValue[$this->form->getNameHTML('hoang_cusc_td_thanh_vien_ngay_bd')];
            $ngayKT = $this->objectsValue[$this->form->getNameHTML('hoang_cusc_td_thanh_vien_ngay_kt')];
            //Do form chi lay dc ma nhom du an nen can ham getIDByMa de lay id du an
            $modelNhomDuAn = new HOANG_CUSC_TD_NHOM_DU_AN();
            $modelThanhVien = new HOANG_CUSC_TD_THANH_VIEN();
            //print_r($maNhom);
            $IDNhom = $modelNhomDuAn->getIDByMa($maNhom);
            //tạo data insert
            $data = Array(
                'hoang_cusc_td_nhom_du_an_id' => $IDNhom,
                'hoang_cusc_td_thanh_vien_ma' => strtoupper(Util::stripUnicode($maCB)),
                'hoang_cusc_td_thanh_vien_ho_lot' =>  strtoupper(Util::stripUnicode($holot)),
                'hoang_cusc_td_thanh_vien_ho_lot_vn' => $holot,
                'hoang_cusc_td_thanh_vien_ten' =>  strtoupper(Util::stripUnicode($ten)),
                'hoang_cusc_td_thanh_vien_ten_vn' => $ten,
                'hoang_cusc_td_thanh_vien_phai' => $gioiTinh,
                'hoang_cusc_td_thanh_vien_ngay_bd' => Util::strToTime($ngayBD),
                'hoang_cusc_td_thanh_vien_ngay_kt' => Util::strToTime($ngayKT),
            );
            $modelThanhVien->insert($data);
        }// end function process_add

        //tra ket qua ve ham ajax thucHienThem(o file index), tra ve dang xml
        public function add() {
            $xml = '';
            if (!MsgHelper::hasError()) {
                $xml = '<msg>OK</msg>';
            } else {
                $xml = '<msg>' . self::encodeXMLString (MsgHelper::getHTML()) . '</msg>';
            }
            $this->registry->template->showXML($xml);
        }// end function add

         //lay du lieu hien thi khi edit
         public function public_get_info() {
            $modelThanhVien = new HOANG_CUSC_TD_THANH_VIEN();
            $id = isset($this->objectsValue['id']) ? $this->objectsValue['id'] : "";
            $info = $modelThanhVien->getInfoById($id);
            if (!empty($info)) {
                $info[0]['ngaybd'] = Util::timeToStr($info[0]['ngaybd']);
                $info[0]['ngaykt'] = Util::timeToStr($info[0]['ngaykt']);
            }
            $this->registry->template->showXML(self::putXMLString($info));
        }// end function public_get_info

        //kiem tra du lieu nhap vao
        public function validate_edit() {
            $dbHelper = DBHelper::getInstance();
            //lay du lieu tu form
            $holot = $this->objectsValue[$this->form->getNameHTML('hoang_cusc_td_thanh_vien_ho_lot')];
            $ten = $this->objectsValue[$this->form->getNameHTML('hoang_cusc_td_thanh_vien_ten')];
            $gioiTinh = $this->objectsValue[$this->form->getNameHTML('hoang_cusc_td_thanh_vien_phai')];
            $ngayBD = $this->objectsValue[$this->form->getNameHTML('hoang_cusc_td_thanh_vien_ngay_bd')];
            $ngayKT = $this->objectsValue[$this->form->getNameHTML('hoang_cusc_td_thanh_vien_ngay_kt')];
            //Kiem tra rong va kiem tra kieu du lieu
            MsgHelper::checkValue($dbHelper->getDBInfo('hoang_cusc_td_thanh_vien_ho_lot')->getTitle(), Array(__ERR_REQUIRE => '', __ERR_VALID_STR => ''), $holot);
            MsgHelper::checkValue($dbHelper->getDBInfo('hoang_cusc_td_thanh_vien_ten')->getTitle(), Array(__ERR_REQUIRE => '', __ERR_VALID_STR => ''), $ten);
            MsgHelper::checkValue($dbHelper->getDBInfo('hoang_cusc_td_thanh_vien_phai')->getTitle(), Array(__ERR_REQUIRE => '', __ERR_VALID_STR => ''), $gioiTinh);
            MsgHelper::checkValue($dbHelper->getDBInfo('hoang_cusc_td_thanh_vien_ngay_bd')->getTitle(), Array(__ERR_REQUIRE => '', __ERR_DATE => ''), $ngayBD);
            MsgHelper::checkValue($dbHelper->getDBInfo('hoang_cusc_td_thanh_vien_ngay_kt')->getTitle(), Array(__ERR_DATE => ''), $ngayKT);
            //
            if ($ngayBD != '' && $ngayKT != '' && Util::strToTime($ngayBD) > Util::strToTime($ngayKT)) {
                MsgHelper::addErr('err_ngaykt', 'err_ngaykt', 'Giá trị ngày kết thúc không thể nhỏ hơn ngày bắt đầu.');
            }
            if ($gioiTinh != 0 && $gioiTinh != 1) {
                MsgHelper::addErrByCode(__ERR_NOT_EXIST, __ERR_NOT_EXIST, $dbHelper->getDBInfo('hoang_cusc_td_thanh_vien_phai')->getTitle());

            }
            return !MsgHelper::hasError();
        } // end function validate_edit

        //thuc hien thao tac them voi dieu kien validate tra ve true
        public function process_edit() {
            $modelThanhVien = new HOANG_CUSC_TD_THANH_VIEN();
            $id = $this->objectsValue["h_". __APP_CONTROLLER . "_id"];
            $holot = $this->objectsValue[$this->form->getNameHTML('hoang_cusc_td_thanh_vien_ho_lot')];
            $ten = $this->objectsValue[$this->form->getNameHTML('hoang_cusc_td_thanh_vien_ten')];
            $gioiTinh = $this->objectsValue[$this->form->getNameHTML('hoang_cusc_td_thanh_vien_phai')];
            $ngayBD = $this->objectsValue[$this->form->getNameHTML('hoang_cusc_td_thanh_vien_ngay_bd')];
            $ngayKT = $this->objectsValue[$this->form->getNameHTML('hoang_cusc_td_thanh_vien_ngay_kt')];
            //print_r($id.'-'.$holot.'-'.$ten.'-'.$gioiTinh.'-'.$ngayBD.'-'.$ngayKT);die;
            $fields = "hoang_cusc_td_thanh_vien_ho_lot = #b,
                    hoang_cusc_td_thanh_vien_ho_lot_vn = #c,
                    hoang_cusc_td_thanh_vien_ten = #d,
                    hoang_cusc_td_thanh_vien_ten_vn = #e,
                    hoang_cusc_td_thanh_vien_phai = #f,
                    hoang_cusc_td_thanh_vien_ngay_bd = #g,
                    hoang_cusc_td_thanh_vien_ngay_kt = #h
            ";
            
            $wheres = "hoang_cusc_td_thanh_vien_id = #z";
            //bien #b,c,z thanh $b,c,z
            $params = Array(
                'b' => strtoupper(Util::stripUnicode($holot)),
                'c' => $holot,
                'd' => strtoupper(Util::stripUnicode($ten)),
                'e' => $ten,
                'f' => $gioiTinh,
                'g' => Util::strToTime($ngayBD),
                'h' => Util::strToTime($ngayKT),
                'z' => $id
            );
            $modelThanhVien->update($fields, $wheres, $params);
        }// end function process_edit

        //tra ket qua ve ham ajax thucHienSua(o file index), tra ve dang xml
        public function edit() {
            $xml = '';
            if (!MsgHelper::hasError()) {
                $xml = '<msg>OK</msg>';
            } else {
                $xml = '<msg>' . self::encodeXMLString (MsgHelper::getHTML()) . '</msg>';
            }
            $this->registry->template->showXML($xml);
        }// end function edit

        //Xoa
        public function delete() {
            $modelThanhVien = new HOANG_CUSC_TD_THANH_VIEN();
            $post =  $this->objectsValue["h_". __APP_CONTROLLER]; // lấy mảng chọn dữ liệu trường input
            // Duyệt qua từng dòng được chọn
            foreach ($post as $key => $value) {
                if ($value != '') {
                    // Thực hiện xóa các dòng đang xét
                    $modelThanhVien->delete("WHERE hoang_cusc_td_thanh_vien_id = #a", array('a' => $value), $value);
                }
            }
            if (MsgHelper::hasError()) {
                $this->objectsValue['error'] = MsgHelper::getHTML();
            } else {
                $this->objectsValue['success'] = "Đã xóa thành công các dòng được chọn.";
            }
            $this->registry->template->redirect(__HOST_NAME . __APP_PATH . __APP_CONTROLLER, $this->objectsValue);
        }// end function delete

         //Tai file mau
        public function public_sample() {
            //print_r(__SITE_PATH . __SAMPLE_PATH . 'cusc/them_tu_file_td_thanh_vien_hoang.xls'); die;
            //Tai file mau ve may
            $this->registry->template->exportFromFile(__SITE_PATH . __SAMPLE_PATH . 'cusc/them_tu_file_td_thanh_vien_hoang.xls', 'them_tu_file_td_thanh_vien_hoang.xls');   
        }

        //Validate excel do nguoi dung nhap
        public function validate_addfile() {
            //print_r(__TMP_PATH); die;
            $files_info = Array(0 => Array('input_name' => "up_Them_tu_file", 'file_path' => __TMP_PATH . 'them_tu_file_td_thanh_vien_hoang', 'type' => __UPLOAD_TYPE_EXCEL));
            //kiem tra file upload co hop le khong
            if (!UploadHelper::upload($files_info)) {
                MsgHelper::addErrByCode('UPLOAD', '', '');
                return !MsgHelper::hasError();
            }
            //print_r(4464);die;
            $excel = ReadExcelHelper::getInstance();
            // Doc thong tin tu file (G:\daotao/tmp/file vua gửi)
            if (file_exists(__TMP_PATH . 'them_tu_file_td_thanh_vien_hoang.xls')) {
                $excel->readExcel(__TMP_PATH . 'them_tu_file_td_thanh_vien_hoang.xls', __EXCEL_VER_2003);
            } else {
                $excel->readExcel(__TMP_PATH . 'them_tu_file_td_thanh_vien_hoang.xlsx', __EXCEL_VER_2007);               
            }
            // Lay du lieu file (sua toArray($option=Array()) thi se kh con hien loi)
            $data = $excel->toArray(1, 2, 7); //cot 1, hang 2 đến cot 7
            //print_r($data); die;
            //print_r(!MsgHelper::hasError());die;
            if (empty($data) && !MsgHelper::hasError()) {
                //print_r(454);
                $fileInfo = UploadHelper::getFileInfo("up_Them_tu_file");
                MsgHelper::addErrByCode(__ERR_UPLOAD_FILE_EMPTY, NULL, $fileInfo['name']);
            }
            // Luu cac thong tin vao session
            if (!MsgHelper::hasError()) {
                $s = Array();
                $s['data'] = $data;
                SessionHelper::Controller_SetSession($s);
            }
        
            return !MsgHelper::hasError();
        }// end function validate_addfile

        //tra ve ket qua (chay qua ham public_save->public_check->public_excelloi truoc khi tra kq)
        public function addfile() {
            set_time_limit(5000);
            //Thong bao thanh cong hay loi neu co
            if (!MsgHelper::hasError()) {                   
                $this->objectsValue['success'] = 'Đã upload thành công. Vui lòng xem thông tin bên dưới';
            } else {
                $this->objectsValue['error'] = self::encodeXMLString(MsgHelper::getHTML());
                $this->registry->template->redirect(__HOST_NAME . __APP_PATH . __APP_CONTROLLER, $this->objectsValue);
            }
            $this->registry->template->redirect(__HOST_NAME . __APP_PATH . __APP_CONTROLLER . '/public_save', $this->objectsValue);
    
        }// end function addfile

        public function public_save() {
            // lay session du lieu excel
            $s = SessionHelper::Controller_GetSession();
            if (!$s['data']) {
                $this->objectsValue['error'] = "Đã có lỗi xảy ra trong quá trình tải tệp. Vui lòng thử lại!";
                $this->registry->template->redirect(__HOST_NAME . __APP_PATH . __APP_CONTROLLER, $this->objectsValue);
                return;
            }
            $soThanhCong = 0;
            $soLoi = 0;
            $s['dataloi'] = array();
            $dataloi = array();
            $info = array();
            // Lap qua danh sach du lieu
    
            foreach ($s['data'] as $key => $value) {
                $maNhom          = trim($value[0]);
                $maCB            = trim($value[1]);
                $holot_vn        = trim($value[2]);
                $holot           = strtoupper(Util::stripUnicode($holot_vn));
                $ten_vn          = trim($value[3]);
                $ten             = strtoupper(Util::stripUnicode($ten_vn));
                $gioiTinh        = trim($value[4]);
                $ngayBD          = trim($value[5]);
                $ngayKT          = trim($value[6]);
                //print_r($maNhom.'-'.$maCB.'-'.$holot.'-'.$holot_vn.'-'.$ten.'-'.$ten_vn.'-'.$gioiTinh.'-'.$ngayBD.'-'.$ngayKT);die;
                //end get data form xls
                //==================================================================
                // Check và them du lieu
                $rs = $this->public_check($maNhom, $maCB, $holot_vn, $ten_vn, $gioiTinh, $ngayBD, $ngayKT);
                if ($rs['success']) {
                    $soThanhCong++;
                    $value[9] = "<span class ='green'><ul><li>Thêm mới nhân viên từ file thành công!</li></ul></span>";
                } else {
                    $soLoi++;
                    $v[0] = $value[0];
                    $v[1] = $value[1];
                    $v[2] = $value[2];
                    $v[3] = $value[3];
                    $v[4] = $value[4];
                    $v[5] = $value[5];
                    $v[6] = $value[6];
                    $v[7] = $value[7] = "<span class ='red'><ul>" . $rs['error'] . "</ul></span>";
                    // Luu lai du lieu loi va ly do loi neu co
                    $dataloi[] = $v;
                }
    //            $data[] = $value;
            }//exit;
            $soLoi = count($dataloi);
            $s['dataloi'] = $dataloi;
            SessionHelper::Controller_SetSession($s);
            $skey = $this->getPOST('sskey');
            // Tao chuoi thong bao thanh cong
            $str = '';
            if ($soLoi == 0 && $soThanhCong > 0) {
                $str = 'Đã thêm dữ iệu từ file thành công.';
            } else {
                $str .= isset($soThanhCong) ? "Tổng số lượt thêm thành công: $soThanhCong lượt" : "Tổng số lượt thêm thành công: 0 lượt";
                $str .= isset($soLoi) ? '<br>Tổng số lượt lỗi:</b> ' . $soLoi . ' lượt ' : 'Tổng số lượt lỗi: 0 lượt';
                $str .= '<br/> Vui lòng nhấn <a href="' . __HOST_NAME . __APP_PATH . __APP_CONTROLLER . '/public_excelloi?sskey=' . $skey . '"><b>[vào đây]</b></a> để xuất excel lỗi.';
            }
            $this->objectsValue['success'] = $str;
            $this->registry->template->redirect("" . __HOST_NAME . __APP_PATH . __APP_CONTROLLER, $this->objectsValue);
        }// end function public_save
        
        public function public_check($maNhom, $maCB, $holot_vn, $ten_vn, $gioiTinh, $ngayBD, $ngayKT) {
            //Kiem tra du lieu hop le nhap vao
            $dbHelper      = DBHelper::getInstance();
            $modelThanhVien   = new HOANG_CUSC_TD_THANH_VIEN();
            $modelNhomDuAn = new HOANG_CUSC_TD_NHOM_DU_AN();
            $err            = "";
            $temp           = "";
            $IDNhom = $modelNhomDuAn->getIDByMa($maNhom);
            //Kiem tra rong vs kiem tra kieu du lieu vs kiem tra du lieu ton tai va tra ve thong bao loi            
            $check_maNhom = MsgHelper::checkValue('Mã nhóm dự án', Array(__ERR_REQUIRE => '', __ERR_VALID_STR => ''), $maNhom, NULL, TRUE);
            if (!is_bool($check_maNhom)) {
                $err .= "<li>$check_maNhom</li>";
            } else {
                if ($IDNhom == '') {
                    $check_maNhom = 'Mã nhóm dự án không tồn tại trong hệ thống.';
                    $err .= "<li>$check_maNhom</li>";
                }
            }
            $check_maCB = MsgHelper::checkValue('Mã cán bộ', Array(__ERR_REQUIRE => '', __ERR_VALID_STR => ''), $maCB, NULL, TRUE);
            if (!is_bool($check_maCB)) {
                $err .= "<li>$check_maCB</li>";
            } else {
                if ($modelThanhVien->check(Array('hoang_cusc_td_thanh_vien_ma' => $maCB))) {
                    $check_maCB = 'Mã cán bộ đã tồn tại trong hệ thống.';
                    $err .= "<li>$check_maCB</li>";
                }
            }
            $check_hoLotVN = MsgHelper::checkValue('Họ lót', Array(__ERR_REQUIRE => '', __ERR_VALID_STR => ''), $holot_vn, NULL, TRUE);
            if (!is_bool($check_hoLotVN)) {
                $err .= "<li>$check_hoLotVN</li>";
            }
            $check_tenVN = MsgHelper::checkValue('Tên', Array(__ERR_REQUIRE => '', __ERR_VALID_STR => ''), $ten_vn, NULL, TRUE);
            if (!is_bool($check_tenVN)) {
                $err .= "<li>$check_tenVN</li>";
            }
            $check_gioiTinh = MsgHelper::checkValue('Giới tính', Array(__ERR_REQUIRE => '', __ERR_VALID_STR => ''), $gioiTinh, NULL, TRUE);
            if (!is_bool($check_gioiTinh)) {
                $err .= "<li>$check_gioiTinh</li>";
            } else {
                if ($gioiTinh != 0 && $gioiTinh != 1) {
                    $check_gioiTinh = 'Giới tính phải là 0 hoặc 1.';
                    $err .= "<li>$check_gioiTinh</li>";
                }
            }
            $check_ngayBD = MsgHelper::checkValue('Ngày bắt đầu', Array(__ERR_REQUIRE => '', __ERR_DATE => ''), $ngayBD, NULL, TRUE);
            if (!is_bool($check_ngayBD)) {
                $err .= "<li>$check_ngayBD</li>";
            }
            $check_ngayKT = MsgHelper::checkValue('Ngày kết thúc', Array(__ERR_DATE => ''), $ngayKT, NULL, TRUE);
            if (!is_bool($check_ngayKT)) {
                $err .= "<li>$check_ngayKT</li>";
            } else {
                if ($ngayBD != '' && $ngayKT != '' && Util::strToTime($ngayBD) > Util::strToTime($ngayKT)) {
                    $check_ngayKT = 'Giá trị ngày kết thúc không thể nhỏ hơn ngày bắt đầu.';
                    $err .= "<li>$check_ngayKT</li>";
                }
            }
            //print_r($err);die;
            //=====================Tao data de insert===============================
            if ($err == "") {//Khong co loi
                $data = Array(
                    'hoang_cusc_td_nhom_du_an_id'     => $IDNhom,
                    'hoang_cusc_td_thanh_vien_ma'    => $maCB,
                    'hoang_cusc_td_thanh_vien_ho_lot' => strtoupper(Util::stripUnicode($holot_vn)),
                    'hoang_cusc_td_thanh_vien_ho_lot_vn' => $holot_vn,
                    'hoang_cusc_td_thanh_vien_ten'      => strtoupper(Util::stripUnicode($ten_vn)),
                    'hoang_cusc_td_thanh_vien_ten_vn' => $ten_vn,
                    'hoang_cusc_td_thanh_vien_phai'     => $gioiTinh,
                    'hoang_cusc_td_thanh_vien_ngay_bd'     => Util::strToTime($ngayBD),
                    'hoang_cusc_td_thanh_vien_ngay_kt'     => Util::strToTime($ngayKT)
                );
                //print_r($data);die;
                $modelThanhVien->insert($data);
                $rs['success'] = TRUE;
                $rs['error'] = FALSE;
            } else {
                $rs['error'] = $err;
                $rs['success'] = FALSE;
            }
            return $rs;
        }// end function public_check

        //Xuat cac loi ra file excelloi
        public function public_excelloi() {
            // Lay thong tin tu session
            $s = SessionHelper::Controller_GetSession();
            $print = new PrintHelper('main', __PRINT_TYPE_A4, __PRINT_PORTAIL, 1, false, false);
            // Thong bao kho danh sach rong
            if (count($s['dataloi']) == 0) {
                $print->showNoDataMsg();
                $this->registry->template->export($print->getHTML());
                return;
            }
            $keys = $s['dataloi'][0];
            $num_col = count($keys) + 1;

            $title = Array();
            $title[] = PrintCom::Logo(3, $num_col - 3);
            // Tao du lieu cho file excel
            $title[] = PrintCom::Title('DANH SÁCH THÊM TỪ FILE XẢY RA LỖI', $num_col);
            $print->addMainTitle($title);
            $header = Array();
            // Tao tieu de cho file excel
            $header[] = Array(
                            Array('html' => 'STT'),
                            Array('html' => 'Mã nhóm dự án(*)'),
                            Array('html' => 'Mã cán bộ(*)'),
                            Array('html' => 'Họ lót(*)'),
                            Array('html' => 'Tên(*)'),
                            Array('html' => 'Giới tính(*)[0: Nam, 1: Nu]'),
                            Array('html' => 'Ngày BD(*)'),
                            Array('html' => 'Ngày KT(*)'),
                            Array('html' => 'Tình trạng lỗi')
                        );
            // Them noi dung moi vao file excel
            $print->addBody($header, $s['dataloi'], $keys);
            $this->registry->template->export($print->getHTML(true), __EXPORT_TYPE_EXCEL, 'danhsachloi_them_tu_file_td_thanh_vien_hoang');
        }// end function public_excelloi
        
        
    }

?>