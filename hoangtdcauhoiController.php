<?php
    include_once __SITE_PATH . "/modules/com/list.php";
    include_once __SITE_PATH . "/modules/com/form.php";
    include_once __SITE_PATH . "/modules/com/search.php";
    include_once __SITE_PATH . "/model/ottoan_td_mon_hoc.php";
    include_once __SITE_PATH . "/model/ottoan_td_cau_hoi.php";
    include_once __SITE_PATH . "/model/hoang_td_cau_hoi.php";
    include_once __SITE_PATH . "/model/ottoan_td_cau_tra_loi.php";
    include_once __SITE_PATH . "/modules/controller/quanly/danhgiahocphan/_QLDANHGIAHOCPHANHelper.php";


    class hoangtdcauhoiController extends AbstractController{
        private $form;
        private $searchMng;
        //private $form_them_tu_file;
       
        public function init() {
            //Khoi tao form search
            $this->searchMng = new SearchManager('frmMain');
            $this->searchMng->setSortable(true); //hien thi cot sap xep tang dan/giam dan
            $this->searchMng->regCom('ottoan_td_mon_hoc_ma', true, Array(__ERR_VALID_STR => ''), NULL, NULL, NULL, Array('cmba','txt'));
            $this->searchMng->regCom('ottoan_td_cau_hoi_ma', true, Array(__ERR_VALID_STR => ''), NULL, NULL, NULL, Array('cmba','txt'));            
                    
            // Khoi tao form them/cap nhat
            $this->form = new SimpleFloatForm(__APP_CONTROLLER, 'Cập nhật câu hỏi'); // co 2 loai form 1 loai mo modal 1 loai mo link moi
            $this->form->addRowAsCom('ottoan_td_mon_hoc_ma', Array('cmb','txt'), NULL, NULL, "class='notered'",NULL, NULL, FALSE);
            $this->form->addRowAsCom('ottoan_td_cau_hoi_ma', Array('txt'), NULL, NULL, "class='notered'",NULL, NULL, FALSE);
            $this->form->addRowAsCom('ottoan_td_cau_hoi_ten_vn', Array('txt'), NULL, NULL, "class='notered'");
            $this->form->addRow('Loại trả lời câu hỏi ', UIHelper::RadioBox("loai_cau_hoi", Array(Array('id' => 'nhap_gop_y', 'text' => ' Nhập câu trả lời', 'value' => '0')), 1) . "<br>"
                . UIHelper::RadioBox("loai_cau_hoi", Array(Array('id' => 'chon_cau_tra_loi', 'text' => ' Chọn một câu trả lời', 'value' => '1')), 1) . "<br>"
                . UIHelper::RadioBox("loai_cau_hoi", Array(Array('id' => 'chon_nhieu_cau_tra_loi', 'text' => ' Chọn nhiều câu trả lời', 'value' => '2')), 1)
        );
            $this->form->addRow(UIHelper::Frame('Danh sách câu trả lời', UIHelper::EmptyTable('tb_goiycauhoi', 'tb_goiycauhoi', Array('class' => 'table-list', 'style' => 'width:100%'))));
            $this->form->addSubmitRow(UIHelper::Button("btnSave", "bt_" .__APP_CONTROLLER . "_submit", "Thực hiện", "onclick='thucHienThem()'"));
            
        }// end function init

        public function index() {
            //tim kiem
            $modelCauHoi = new HOANG_TD_CAU_HOI();
            $this->searchMng->setValue($this->objectsValue);
            $sort = $this->searchMng->getSort();
            $search = $this->searchMng->getSearch();

            // Lấy tổng số dòng của bảng
            $total = $modelCauHoi->getTotalData($search);
            //print_r($total);die;
            //Thuc hien phan trang
            $paging = (PagingHelper::getCurPage($this->objectsValue, __APP_CONTROLLER, $total));
            $curPage = $paging['cur_page'];
            $limit = $paging['limit'];
            $offset = $paging['offset'];
            // print_r($limit.'-'.$offset);
            // Lấy all data
            $data = $modelCauHoi->getDataAll($search, $sort, $offset, $limit);
            //print_r($search.'-'. $sort.'-'. $offset.'-'. $limit); ổn
            //print_r($data);
            $this->registry->template->form = $this->form->getHTML();
            $html = $this->public_changeArrayToHtml($data, FALSE, $offset + 1);
            $this->registry->template->paging_top = PagingHelper::getHTML('frmMain', __APP_CONTROLLER, $curPage, $total, false, "dòng");
            $this->registry->template->paging_bottom = PagingHelper::getHTML('frmMain', __APP_CONTROLLER, $curPage, $total, true, "dòng");
            $this->registry->template->title = "Quản lý câu hỏi";
            $this->registry->template->list = $html;
            $this->registry->template->data = $this->searchMng->getHTML();
            $this->registry->template->show(__APP_CONTROLLER . "_index");
        }

        public function public_changeArrayToHtml($data, $in = false) {
            //print_r($data);
            //tao list
            if ($in) {
                $clist = new CList(__APP_CONTROLLER, __PRINT_SIZE_A4_PORTAIL, Array('border' => '1', 'class' => 'table-print'), false);
            } else {
                $clist = new CList(__APP_CONTROLLER, '100%', Array('border' => '1', 'class' => 'table-list'), false);
            }
            //data rong
            if (empty($data)) {
                $row = Array();
                $row[] = Array('html' => 'Không có dữ liệu', 'width' => '100%', 'align' => 'center', 'force_tag' => 'th');
                $clist->addRow($row);
            } else {
                //tao tieu de
                $header = array();
                if ($in) { //$in=false
                    $header[] = array(
                        // nêu là in thì không có cột xóa vs edit
                        array('html' => 'STT'),
                        array('html' => 'Mã môn học'),
                        array('html' => 'Tên môn học'),
                        array('html' => 'Mã câu hỏi'),
                        array('html' => 'Tên câu hỏi'),
                        array('html' => 'Mã câu trả lời'),
                        array('html' => 'Tên câu trả lời'),
                        array('html' => 'Đáp án')
                    );
                } else {
                    $header[] = array(
                        array('html' => 'STT'),
                        array('html' => 'Mã môn học'),
                        array('html' => 'Tên môn học'),
                        array('html' => 'Mã câu hỏi'),
                        array('html' => 'Tên câu hỏi'),
                        array('html' => 'Mã câu trả lời'),
                        array('html' => 'Tên câu trả lời'),
                        array('html' => 'Đáp án'),
                        array('html' => 'Sửa'),
                        array('html' => 'Chọn')
                    );
                }
                //print_r($header);die;
                $clist->addHeader($header);
                $flagMonHoc = 0;
                $flagCauHoi = 0;
                $stt = 1;
                foreach ($data as $key => $value) {
                    //print_r($value);die;
                    $idMonHoc = $value['ottoan_td_mon_hoc_id'];
                    $idCauHoi = $value['ottoan_td_cau_hoi_id'];
                    $maMonHoc = $value['ottoan_td_mon_hoc_ma'];
                    $tenMonHoc = $value['ottoan_td_mon_hoc_ten_vn'];
                    $maCauHoi = $value['ottoan_td_cau_hoi_ma'];
                    $tenCauHoi = $value['ottoan_td_cau_hoi_ten_vn'];
                    $maCauTL = $value['ottoan_td_cau_tra_loi_ma'];
                    $tenCauTL = $value['ottoan_td_cau_tra_loi_ten_vn'];
                    $dapAn = $value['ottoan_td_cau_tra_loi_dap_an'];
                    
                    
                    $row = array();
                    //print_r($value);
                    if($flagMonHoc != $idMonHoc){
                        $row[] = array('html' => $stt++, 'rowspan' => $value['slmon'], 'class' => 'bold', 'align' => 'center', 'style' => '');
                        $row[] = array('html' => $maMonHoc, 'rowspan' => $value['slmon'], 'class' => 'bold', 'align' => 'center', 'style' => '');
                        $row[] = array('html' => $tenMonHoc, 'rowspan' => $value['slmon'], 'class' => 'bold', 'align' => 'center', 'style' => '');
                        
                        $row[] = array('html' => $maCauHoi, 'rowspan' => $value['sltl'], 'class' => 'bold', 'align' => 'center', 'style' => '');
                        $row[] = array('html' => $tenCauHoi, 'rowspan' => $value['sltl'], 'class' => 'bold', 'align' => 'center', 'style' => '');
                        if ($maCauTL != '') {
                            $row[] = array('html' => $maCauTL, 'class' => 'bold', 'align' => 'center', 'style' => '');
                            $row[] = array('html' => $tenCauTL, 'class' => 'bold', 'align' => 'center', 'style' => '');
                        }else {
                            $row[] = array('html' => 'Tự luận', 'class' => 'bold', 'align' => 'center', 'style' => '');
                            $row[] = array('html' => 'Tự luận', 'class' => 'bold', 'align' => 'center', 'style' => '');
                        }

                        // $row[] = array('html' => $maCauTL, 'class' => 'bold', 'align' => 'center', 'style' => '');
                        // $row[] = array('html' => $tenCauTL, 'class' => 'bold', 'align' => 'center', 'style' => '');
                        if($dapAn == 1){
                            $row[] = array('html' => 'X','align' => 'center', 'style' => 'color:red;', 'class' => 'bold');
                        }else {
                            $row[] = array('html' => '','align' => 'center');
                        }
                        if(!$in){
                            //sửa
                            $row[] = Array('html' => UIHelper::Hidden("h_list[]", "h_list_" . ($key), $idCauHoi) .
                                UIHelper::Image('img_edit_list_' . ($key), 'img_edit_list_' . ($key), __HOST_NAME . __TEMPLATE . 'images/iconedit.png', 'Sửa', "onclick='goToEditList(\"list\"," . ($key) . ")'"), 'class' => 'center pointer', 'rowspan' => $value['sltl']);
                            //xóa
                            $row[] = Array('html' => UIHelper::CheckBox('chk_list_all', Array(Array('id' => 'chk_list_all_' . ($key), 'text' => '')))
                                . UIHelper::Hidden("h_list[]", "h_list_" . ($key), $idCauHoi)
                                , 'align' => 'center', 'rowspan' => $value['sltl']
                            );
                        }
                    // print_r($flagMonHoc);;die;
                    } else {
                        //print_r($flagCauHoi);die;
                        if ($flagCauHoi != $idCauHoi) {
                            $row[] = array('html' => $maCauHoi, 'rowspan' => $value['sltl'], 'class' => 'bold', 'align' => 'center', 'style' => '');
                            $row[] = array('html' => $tenCauHoi, 'rowspan' => $value['sltl'], 'class' => 'bold', 'align' => 'center', 'style' => '');
                            if ($maCauTL != '') {
                                $row[] = array('html' => $maCauTL, 'class' => 'bold', 'align' => 'center', 'style' => '');
                                $row[] = array('html' => $tenCauTL, 'class' => 'bold', 'align' => 'center', 'style' => '');
                            }else {
                                $row[] = array('html' => 'Tự luận', 'class' => 'bold', 'align' => 'center', 'style' => '');
                                $row[] = array('html' => 'Tự luận', 'class' => 'bold', 'align' => 'center', 'style' => '');
                            }
                            // $row[] = array('html' => $maCauTL, 'class' => 'bold', 'align' => 'center', 'style' => '');
                            // $row[] = array('html' => $tenCauTL, 'class' => 'bold', 'align' => 'center', 'style' => '');
                            if($dapAn == 1){
                                $row[] = array('html' => 'X','align' => 'center', 'style' => 'color:red;', 'class' => 'bold');
                            }else {
                                $row[] = array('html' => '','align' => 'center');
                            }
                            //neu in se an button sua, xoa
                            if(!$in){
                                //sửa
                                $row[] = Array('html' => UIHelper::Hidden("h_list[]", "h_list_" . ($key), $idCauHoi) .
                                    UIHelper::Image('img_edit_list_' . ($key), 'img_edit_list_' . ($key), __HOST_NAME . __TEMPLATE . 'images/iconedit.png', 'Sửa', "onclick='goToEditList(\"list\"," . ($key) . ")'"), 'class' => 'center pointer', 'rowspan' => $value['sltl']);
                                //xóa
                                $row[] = Array('html' => UIHelper::CheckBox('chk_list_all', Array(Array('id' => 'chk_list_all_' . ($key), 'text' => '')))
                                    . UIHelper::Hidden("h_list[]", "h_list_" . ($key), $idCauHoi)
                                    , 'align' => 'center', 'rowspan' => $value['sltl']
                                );
                            }
                        }else{
                            $row[] = array('html' => $maCauTL, 'class' => 'bold', 'align' => 'center', 'style' => '');
                            $row[] = array('html' => $tenCauTL, 'class' => 'bold', 'align' => 'center', 'style' => '');
                            if($dapAn == 1){
                                $row[] = array('html' => 'X','align' => 'center', 'style' => 'color:red;', 'class' => 'bold');
                            }else {
                                $row[] = array('html' => '','align' => 'center');
                            }
                        }
                    }
                    $clist->addRow($row);
                    $flagMonHoc = $idMonHoc;
                    $flagCauHoi = $idCauHoi;
                    //print_r($flagMonHoc); 
                } 
                if (!$in) {
                    $row = Array();
                    $row[] = Array('html' => 'Chọn tất cả'
                        . UIHelper::Hidden('countRows', 'countRows', $key)
                        , 'colspan' => 9, 'align' => 'center', 'class' => 'bold', 'force_tag' => 'th');
                    $row[] = Array('html' => UIHelper::CheckBox('chk_list_check_all', Array(Array('id' => 'chk_list_check_all', 'text' => '', 'options' => "onclick='processCheckAll(\"list\")   '"))), 'class' => 'center');
                    $clist->addRow($row);
                }
            }
            return $clist->getHTML();
            
        }// end function public_changeArrayToHtml

        public function validate_add() {
            $dbHelper = DBHelper::getInstance();
            $maMon = $this->objectsValue[$this->form->getNameHTML('ottoan_td_mon_hoc_ma')];
            $maCauHoi = $this->objectsValue[$this->form->getNameHTML('ottoan_td_cau_hoi_ma')];
            $tenCauHoi = $this->objectsValue[$this->form->getNameHTML('ottoan_td_cau_hoi_ten_vn')];
            $arrMaGoiy = $this->objectsValue['ma_goi_y'];
            $arrGoiy = $this->objectsValue['goi_y'];
            $arrDapan = $this->objectsValue['dap_an'];
            $arrStt = $this->objectsValue['so_tt'];
            $loaiCauhoi = $this->objectsValue['h_loai_cau_hoi'];
            //print_r($arrMaGoiy);
            //print_r($arrGoiy);
            //print_r($arrDapan);
            //print_r($arrStt);die;
            //print_r($maMon.'-'.$maCauHoi.'-'.$tenCauHoi.'-'.$arrMaGoiy.'-'.$arrGoiy.'-'.$arrDapan.'-'.$arrStt.'-'.$loaiCauhoi);die;
            MsgHelper::checkValue($dbHelper->getDBInfo('ottoan_td_mon_hoc_ma')->getTitle(), Array(__ERR_REQUIRE => '', __ERR_VALID_STR => ''), $maMon);
            MsgHelper::checkValue($dbHelper->getDBInfo('ottoan_td_cau_hoi_ma')->getTitle(), Array(__ERR_REQUIRE => '', __ERR_VALID_STR => ''), $maCauHoi);
            MsgHelper::checkValue($dbHelper->getDBInfo('ottoan_td_cau_hoi_ten_vn')->getTitle(), Array(__ERR_REQUIRE => '', __ERR_VALID_STR => ''), $tenCauHoi);
    
            $n = count($arrDapan);
            $checkMaGoiY = Array();
            $checkDapAn = Array();
            //kiem tra ma cau tra loi co bi trung khong
            for ($i = 0; $i < $n; $i++) {
                $maGoiY = strtoupper($arrMaGoiy[$i]); 
                $MaGoiy = $arrMaGoiy[$i];
                $Goiy = $arrGoiy[$i];
                $dapAn = $arrDapan[$i];
                $Stt = $arrStt[$i];
                //kiem tra rong
                if ($MaGoiy == '') {
                    MsgHelper::addErr('err_magoiy', 'err_magoiy', 'Mã câu trả lời không được rỗng.');
                }
                if ($Goiy == '') {
                    MsgHelper::addErr('err_goiy', 'err_goiy', 'Câu trả lời không được rỗng.');
                }
                if ($dapAn == '') {
                    MsgHelper::addErr('err_dapan', 'err_dapan', 'Đáp không được rỗng.');
                }
                if ($Stt == '') {
                    MsgHelper::addErr('err_stt', 'err_stt', 'Số thứ tự không được rỗng.');
                }
                // kiểm tra từ khóa vn
                if ($maGoiY != '') {
                    if (isset($checkMaGoiY[$maGoiY])) {
                        MsgHelper::addErr('__' . $i, '__' . $i, ' Mã gợi ý định nghĩa lại ở dòng thứ  ' . ($i + 1) . ' trùng với dùng thứ ' . ($checkMaGoiY[$maGoiY] + 1));
                    } else {
                        $checkMaGoiY[$maGoiY] = $i;
                    }
                }
                 //dua dap an vao mang
                if ($dapAn != '' && $loaiCauhoi == _QLDANHGIAHOCPHANHelper::$__LoaiCauHoi_ChonGoiY || $dapAn != '' && $loaiCauhoi == _QLDANHGIAHOCPHANHelper::$__LoaiCauHoi_ChonNhieu) {
                    $checkDapAn[$i] = $dapAn;
                }
            }
             if (!MsgHelper::hasError()) {
                $modelCauHoi = new HOANG_TD_CAU_HOI();
                //kiem tra ma cau hoi co ton tai trong csdl k
                if ($modelCauHoi->check(Array('ottoan_td_cau_hoi_ma' => strtoupper(Util::stripUnicode($maCauHoi))))) {
                    MsgHelper::addErrByCode(__ERR_EXIST, __ERR_EXIST, $dbHelper->getDBInfo('ottoan_td_cau_hoi_ma')->getTitle());
                }
            }
            
            //thêm chọn nhiều
            if ($loaiCauhoi == _QLDANHGIAHOCPHANHelper::$__LoaiCauHoi_ChonGoiY || $loaiCauhoi == _QLDANHGIAHOCPHANHelper::$__LoaiCauHoi_ChonNhieu) {
                $numRows = count($arrGoiy) - 1;
                //kiem tra phai tren 2 cau tra loi
                if ($numRows < _QLDANHGIAHOCPHANHelper::$__SoGoiY_ToiThieu) {
                    MsgHelper::addErr("mingoiy", "mingoiy", "Vui lòng nhập từ " . _QLDANHGIAHOCPHANHelper::$__SoGoiY_ToiThieu . " gợi ý trở lên.");
                }
                if (!MsgHelper::hasError()) {
                    $modelMonHoc = new OTTOAN_TD_MON_HOC();
                    //kiem tra ma co ton tai trong csdl k
                    if (!$modelMonHoc->check(Array('ottoan_td_mon_hoc_ma' => strtoupper(Util::stripUnicode($maMon))))) {
                        MsgHelper::addErrByCode(__ERR_NOT_EXIST, __ERR_NOT_EXIST, $dbHelper->getDBInfo('ottoan_td_mon_hoc_ma')->getTitle());
                    }
                    foreach ($arrGoiy as $key => $tenGoiY) {
                        $modelCauTL = new OTTOAN_TD_CAU_TRA_LOI();
                        //kiem tra ma co ton tai trong csdl k
                        if ($modelCauTL->check(Array('ottoan_td_cau_tra_loi_ma' => strtoupper(Util::stripUnicode($arrMaGoiy[$key]))))) {
                            MsgHelper::addErrByCode(__ERR_EXIST, __ERR_EXIST, 'Mã câu trả lời '.($key+1).' đã tồn tại');
                        }
                        //kiem tra dap an chi nhap la 0 hoac 1
                        if ($arrDapan[$key] != 0 && $arrDapan[$key] != 1) {
                            MsgHelper::addErr('err_ngaykt', 'err_ngaykt', 'Đáp án phải là 1(đúng) hoặc 0(sai).');
                        }
                        //kiem tra tung dong
                        if ($key != $numRows) {
                            MsgHelper::checkValue("mã câu trả lời " . ($key + 1), Array(__ERR_REQUIRE => '', __ERR_VALID_STR => ''), $arrMaGoiy[$key]);
                            MsgHelper::checkValue("gợi ý dòng thứ " . ($key + 1), Array(__ERR_REQUIRE => '', __ERR_VALID_STR => ''), $tenGoiY);
                            MsgHelper::checkValue("đáp án dòng thứ " . ($key + 1), Array(__ERR_REQUIRE => '', __ERR_FLOAT_NUMBER => '', __ERR_MAX => '2147483647'), $arrDapan[$key]);
                            MsgHelper::checkValue("số thứ tự dòng thứ " . ($key + 1), Array(__ERR_FLOAT_NUMBER => '', __ERR_MAX => '100'), $arrStt[$key]);
                        }
                    }
                }
            }
            //kiểm tra loại câu trả lời có phải Chọn một câu trả lời
            $countValueInArray = array_count_values($checkDapAn);
            if ($loaiCauhoi == _QLDANHGIAHOCPHANHelper::$__LoaiCauHoi_ChonGoiY) {
                if (!isset($countValueInArray[1]) || $countValueInArray[1] != 1) {
                    MsgHelper::addErr('err_ngaykt', 'err_ngaykt', 'Vui lòng chọn 1 đáp án đúng duy nhất.');
                }
            }
            if ($loaiCauhoi == _QLDANHGIAHOCPHANHelper::$__LoaiCauHoi_ChonNhieu) {
                if (!isset($countValueInArray[1])) {
                    MsgHelper::addErr('err_ngaykt', 'err_ngaykt', 'Vui lòng chọn ít nhất 1 đáp án đúng.');
                }
            }
            return !MsgHelper::hasError();
        }// end function validate_add
    
        public function process_add() {
            $modelCauHoi = new HOANG_TD_CAU_HOI();
            $modelCauTL = new OTTOAN_TD_CAU_TRA_LOI();
            $modelMonHoc = new OTTOAN_TD_MON_HOC();
            //bat dau transaction (neu co loi nao do thi rollback trang thai ban dau) tuong tu nhu chuyen tien ngan hang
            $modelCauTL->begin();
            $err = '';
            $maMon = $this->objectsValue[$this->form->getNameHTML('ottoan_td_mon_hoc_ma')];
            $maCauHoi = $this->objectsValue[$this->form->getNameHTML('ottoan_td_cau_hoi_ma')];
            $tenCauHoi = $this->objectsValue[$this->form->getNameHTML('ottoan_td_cau_hoi_ten_vn')];
            $arrMaGoiy = $this->objectsValue['ma_goi_y'];
            $arrGoiy = $this->objectsValue['goi_y'];
            $arrDapan = $this->objectsValue['dap_an'];
            $arrStt = $this->objectsValue['so_tt'];
            $loaiCauhoi = $this->objectsValue['loai_cau_hoi'];
            //print_r($loaiCauhoi);die;
            // print_r($arrMaGoiy);
            // print_r($arrGoiy);
            // print_r($arrDapan);
            // print_r($arrStt);die;
            //get id ma ki thi
            $idCauHoi = $modelCauHoi->getIdByMa($maCauHoi);
            $idMonHoc = $modelMonHoc->getIdByMa($maMon);
            //print_r($maCauHoi);die;
            if ($idCauHoi == '') {
                $err .= "<li>Câu hỏi không tồn tại</li>";
            }
            $loaiCH = '';
    
            if ($loaiCauhoi == 0) {
                $loaiCH = "Nhập câu trả lời";
            } else if ($loaiCauhoi == 1) {
                $loaiCH = "Chọn 1 câu trả lời";
            } else {
                $loaiCH = "Chọn nhiều câu trả lời";
            }
            // $arrLogs[] = " - <i>Người thực hiện</i> : " . UserInfo::get_UserName();
            //neu $loaiCauHoi = 1 or 2
            // print_r($arrGoiy);die;
            // foreach ($arrGoiy as $key => $tenGoiy) {
            //     print_r($arrMaGoiy[$key]);die;
            // }
            if ($loaiCauhoi == _QLDANHGIAHOCPHANHelper::$__LoaiCauHoi_ChonGoiY || $loaiCauhoi == _QLDANHGIAHOCPHANHelper::$__LoaiCauHoi_ChonNhieu) {
                //print_r($idCauHoi);die;
                $numRows = count($arrGoiy) - 1;
                $info = Array(
                    'ottoan_td_cau_hoi_ma' => strtoupper($maCauHoi),
                    'ottoan_td_cau_hoi_ten' => strtoupper(Util::stripUnicode($tenCauHoi)),
                    'ottoan_td_cau_hoi_ten_vn' => $tenCauHoi,
                    'ottoan_td_mon_hoc_id' => $idMonHoc,
                    'ottoan_td_cau_hoi_loai' => $loaiCauhoi
                );
                if ($modelCauHoi->insert($info)) {
                    $idCauHoi = $modelCauHoi->getIdByMa($maCauHoi);
                    foreach ($arrGoiy as $key => $tenGoiy) {
                        if ($key != $numRows) {
                            $data = Array(
                                'ottoan_td_cau_tra_loi_ma' => strtoupper($arrMaGoiy[$key]),
                                'ottoan_td_cau_tra_loi_ten' => strtoupper(Util::stripUnicode($tenGoiy)),
                                'ottoan_td_cau_tra_loi_ten_vn' => $tenGoiy,
                                'ottoan_td_cau_hoi_id' => $idCauHoi,
                                'ottoan_td_cau_tra_loi_dap_an' => $arrDapan[$key],
                                'ottoan_td_cau_tra_loi_stt' => $arrStt[$key]
                            );
                            $modelCauTL->insert($data);
                        }
                    }
                }
            }
            //-------truong hop nhap cau tra loi (dang tu luan)--------------
            if ($loaiCauhoi == _QLDANHGIAHOCPHANHelper::$__LoaiCauHoi_GopY) {
                $info = Array(
                    'ottoan_td_cau_hoi_ma' => strtoupper($maCauHoi),
                    'ottoan_td_cau_hoi_ten' => strtoupper(Util::stripUnicode($tenCauHoi)),
                    'ottoan_td_cau_hoi_ten_vn' => $tenCauHoi,
                    'ottoan_td_mon_hoc_id' => $idMonHoc,
                    'ottoan_td_cau_hoi_loai' => $loaiCauhoi
                );
                $modelCauHoi->insert($info);
            }
            $modelCauTL->end();// ket thuc transaction
        }// end function process_add
    
        public function add() {
            $xml = '';
            if (!MsgHelper::hasError()) {
                $xml = '<msg>OK</msg>';
            } else {
                $xml = '<msg>' . self::encodeXMLString(MsgHelper::getHTML()) . '</msg>';
            }
            $this->registry->template->showXML($xml);
        }// endfunction add
        
        //Xoa
        public function delete() {
            $modelCauHoi = new HOANG_TD_CAU_HOI();
            $modelCauTL = new OTTOAN_TD_CAU_TRA_LOI();
            $modelMonHoc = new OTTOAN_TD_MON_HOC();
            $post =  $this->objectsValue["h_list"]; // lấy mảng chọn dữ liệu trường input
            //print_r($post);die;
            //---Duyet qua tung dong duoc chon.
            foreach ($post as $key => $value) {
                //print_r($key);die;
                if ($value != '') {
                    if ($value != '') {
                        // Thực hiện xóa các dòng đang xét
                        $modelCauTL->delete("WHERE ottoan_td_cau_hoi_id = #a", array('a' => $value), $value);
                        $modelCauHoi->delete("WHERE ottoan_td_cau_hoi_id = #a", array('a' => $value), $value);
                    }
                }
            }
            if (MsgHelper::hasError()) {
                $this->objectsValue['error'] = MsgHelper::getHTML();
            } else {
                $this->objectsValue['success'] = "Đã xóa thành công các dòng được chọn.";
            }
            $this->registry->template->redirect(__HOST_NAME . __APP_PATH . __APP_CONTROLLER, $this->objectsValue);
        }// end function delete

        public function public_get_info() {
            $model_CauHoi = new HOANG_TD_CAU_HOI();
            // lấy về id
            $id = isset($this->objectsValue['id']) ? $this->objectsValue['id'] : "";
            // Lấy thông tin môn học của 1 id
            $info = $model_CauHoi->getInfoByIDCauHoi($id);
            //print_r($info);die;
            $this->registry->template->showXML(self::putXMLString($info));
        }// end function public_get_info

        public function validate_edit() {
            $dbHelper = DBHelper::getInstance();
            $tenCauHoi = $this->objectsValue[$this->form->getNameHTML('ottoan_td_cau_hoi_ten_vn')];
            $arrMaGoiy = $this->objectsValue['ma_goi_y'];
            $arrGoiy = $this->objectsValue['goi_y'];
            $arrDapan = $this->objectsValue['dap_an'];
            $arrStt = $this->objectsValue['so_tt'];
            $loaiCauhoi = $this->objectsValue['h_loai_cau_hoi'];
            //print_r($arrMaGoiy);
            //print_r($arrGoiy);
            //print_r($arrDapan);
            //print_r($arrStt);die;
            //print_r($maMon.'-'.$maCauHoi.'-'.$tenCauHoi.'-'.$arrMaGoiy.'-'.$arrGoiy.'-'.$arrDapan.'-'.$arrStt.'-'.$loaiCauhoi);die;
            MsgHelper::checkValue($dbHelper->getDBInfo('ottoan_td_cau_hoi_ten_vn')->getTitle(), Array(__ERR_REQUIRE => '', __ERR_VALID_STR => ''), $tenCauHoi);
    
            $n = count($arrDapan);
            $checkMaGoiY = Array();
            $checkDapAn = Array();
            //kiem tra ma cau tra loi co bi trung khong
            for ($i = 0; $i < $n; $i++) {
                $maGoiY = strtoupper($arrMaGoiy[$i]); 
                $MaGoiy = $arrMaGoiy[$i];
                $Goiy = $arrGoiy[$i];
                $dapAn = $arrDapan[$i];
                $Stt = $arrStt[$i];
                //kiemk tra rong
                if ($MaGoiy == '') {
                    MsgHelper::addErr('err_magoiy', 'err_magoiy', 'Mã câu trả lời không được rỗng.');
                }
                if ($Goiy == '') {
                    MsgHelper::addErr('err_goiy', 'err_goiy', 'Câu trả lời không được rỗng.');
                }
                if ($dapAn == '') {
                    MsgHelper::addErr('err_dapan', 'err_dapan', 'Đáp không được rỗng.');
                }
                if ($Stt == '') {
                    MsgHelper::addErr('err_stt', 'err_stt', 'Số thứ tự không được rỗng.');
                }
                // kiểm tra từ khóa vn
                if ($maGoiY != '') {
                    if (isset($checkMaGoiY[$maGoiY])) {
                        MsgHelper::addErr('__' . $i, '__' . $i, ' Mã gợi ý định nghĩa lại ở dòng thứ  ' . ($i + 1) . ' trùng với dùng thứ ' . ($checkMaGoiY[$maGoiY] + 1));
                    } else {
                        $checkMaGoiY[$maGoiY] = $i;
                    }
                }
                //dua dap an vao mang
                if ($dapAn != '' && $loaiCauhoi == _QLDANHGIAHOCPHANHelper::$__LoaiCauHoi_ChonGoiY || $dapAn != '' && $loaiCauhoi == _QLDANHGIAHOCPHANHelper::$__LoaiCauHoi_ChonNhieu) {
                    $checkDapAn[$i] = $dapAn;
                }
            }
            //thêm chọn nhiều
            if ($loaiCauhoi == _QLDANHGIAHOCPHANHelper::$__LoaiCauHoi_ChonGoiY || $loaiCauhoi == _QLDANHGIAHOCPHANHelper::$__LoaiCauHoi_ChonNhieu) {
                $numRows = count($arrGoiy) - 1;
                //kiem tra phai tren 2 cau tra loi
                if ($numRows < _QLDANHGIAHOCPHANHelper::$__SoGoiY_ToiThieu) {
                    MsgHelper::addErr("mingoiy", "mingoiy", "Vui lòng nhập từ " . _QLDANHGIAHOCPHANHelper::$__SoGoiY_ToiThieu . " gợi ý trở lên.");
                }
                if (!MsgHelper::hasError()) {
                    foreach ($arrGoiy as $key => $tenGoiY) {
                        $modelCauTL = new OTTOAN_TD_CAU_TRA_LOI();
                        //kiem tra dap an nhap la 0 hoac 1
                        if ($arrDapan[$key] != 0 && $arrDapan[$key] != 1) {
                            MsgHelper::addErr('err_dapan', 'err_dapan', 'Đáp án phải là 1(đúng) hoặc 0(sai).');
                        }
                        //kiem tra tung dong
                        if ($key != $numRows) {
                            MsgHelper::checkValue("mã câu trả lời " . ($key + 1), Array(__ERR_REQUIRE => '', __ERR_VALID_STR => ''), $arrMaGoiy[$key]);
                            MsgHelper::checkValue("gợi ý dòng thứ " . ($key + 1), Array(__ERR_REQUIRE => '', __ERR_VALID_STR => ''), $tenGoiY);
                            MsgHelper::checkValue("đáp án dòng thứ " . ($key + 1), Array(__ERR_REQUIRE => '', __ERR_FLOAT_NUMBER => '', __ERR_MAX => '2147483647'), $arrDapan[$key]);
                            MsgHelper::checkValue("số thứ tự dòng thứ " . ($key + 1), Array(__ERR_FLOAT_NUMBER => '', __ERR_MAX => '100'), $arrStt[$key]);
                        }
                    }
                }
            }
            //kiểm tra loại câu trả lời có phải Chọn một câu trả lời
            $countValueInArray = array_count_values($checkDapAn);
            if ($loaiCauhoi == _QLDANHGIAHOCPHANHelper::$__LoaiCauHoi_ChonGoiY) {
                if (!isset($countValueInArray[1]) || $countValueInArray[1] != 1) {
                    MsgHelper::addErr('err_ngaykt', 'err_ngaykt', 'Vui lòng chọn 1 đáp án đúng duy nhất.');
                }
            }
            if ($loaiCauhoi == _QLDANHGIAHOCPHANHelper::$__LoaiCauHoi_ChonNhieu) {
                if (!isset($countValueInArray[1])) {
                    MsgHelper::addErr('err_ngaykt', 'err_ngaykt', 'Vui lòng chọn ít nhất 1 đáp án đúng.');
                }
            }
            return !MsgHelper::hasError();
        }// end function validate_edit
    
        public function process_edit() {
            $modelCauHoi = new HOANG_TD_CAU_HOI();
            $modelCauTL = new OTTOAN_TD_CAU_TRA_LOI();
            $modelMonHoc = new OTTOAN_TD_MON_HOC();
            $id = $this->objectsValue["h_" . __APP_CONTROLLER . "_id"];
            $tenCauHoi = $this->objectsValue[$this->form->getNameHTML('ottoan_td_cau_hoi_ten_vn')];
            $arrMaGoiy = $this->objectsValue['ma_goi_y'];
            $arrGoiy = $this->objectsValue['goi_y'];
            $arrDapan = $this->objectsValue['dap_an'];
            $arrStt = $this->objectsValue['so_tt'];
            $loaiCH = '';
            $loaiCauhoi = $this->objectsValue['loai_cau_hoi'];

            //print_r($loaiCauhoi);die;
            $loaiCH = '';
    
            if ($loaiCauhoi == 0) {
                $loaiCH = "Nhập câu trả lời";
            } else if ($loaiCauhoi == 1) {
                $loaiCH = "Chọn 1 câu trả lời";
            } else {
                $loaiCH = "Chọn nhiều câu trả lời";
            }

            $fields = "ottoan_td_cau_hoi_ten = #b,
                    ottoan_td_cau_hoi_ten_vn = #c,
                    ottoan_td_cau_hoi_loai = #d
            ";
            
            $wheres = "ottoan_td_cau_hoi_id = #z";
            //bien #b,c,z thanh $b,c,z
            $params = Array(
                'b' => strtoupper(Util::stripUnicode($tenCauHoi)),
                'c' => $tenCauHoi,
                'd' => $loaiCauhoi,
                'z' => $id
            );
            $modelCauHoi->update($fields, $wheres, $params);
            $modelCauTL->delete("WHERE ottoan_td_cau_hoi_id = $id");

            if ($loaiCauhoi == _QLDANHGIAHOCPHANHelper::$__LoaiCauHoi_ChonGoiY || $loaiCauhoi == _QLDANHGIAHOCPHANHelper::$__LoaiCauHoi_ChonNhieu) {
                //print_r($arrGoiy);die;
                $numRows = count($arrGoiy) - 1;
                foreach ($arrGoiy as $key => $tenGoiy) {
                    if ($key != $numRows) {
                        $data = Array(
                            'ottoan_td_cau_tra_loi_ma' => strtoupper($arrMaGoiy[$key]),
                            'ottoan_td_cau_tra_loi_ten' => strtoupper(Util::stripUnicode($tenGoiy)),
                            'ottoan_td_cau_tra_loi_ten_vn' => $tenGoiy,
                            'ottoan_td_cau_hoi_id' => $id,
                            'ottoan_td_cau_tra_loi_dap_an' => $arrDapan[$key],
                            'ottoan_td_cau_tra_loi_stt' => $arrStt[$key]
                        );
                        //print_r($data);
                        $modelCauTL->insert($data);
                    }
                }
            }
        }// end process_edit
    
        public function edit() {
            $xml = '';
            if (!MsgHelper::hasError()) {
                $xml = '<msg>OK</msg>';
            } else {
                $xml = '<msg>' . self::encodeXMLString(MsgHelper::getHTML()) . '</msg>';
            }
            $this->registry->template->showXML($xml);
        }// end function edit

        public function public_in() {
            $this->process_inxuat();
        }// end function public_in

        //excel
        public function public_excel() {
            $this->process_inxuat(FALSE);
        }// end function public_excel

        //in vs excel
        private function process_inxuat($in=TRUE) {
            $modelCauHoi = new HOANG_TD_CAU_HOI();
            $print = new PrintHelper('main', __PRINT_TYPE_A4);
            $this->searchMng->setValue($this->objectsValue);
            $sort = $this->searchMng->getSort();
            $search = $this->searchMng->getSearch();
            $getList = $modelCauHoi->getDataAll($search, $sort);
            if (empty($getList)) {
                // khong co du lieu
                $print->showNoDataMsg();
                $this->registry->template->export($print->getHTML());
                return;
            }
            $keys = $getList[0];
            $num_col = count($keys) - 1; 
            $title = Array();
            $title[] = PrintCom::Logo(2, $num_col - 2);
            $title[] = PrintCom::Title('TỰ ĐIỂN CÁN BỘ', $num_col);
            //print_r($list);
            $print->addMainTitle($title);
            $dataIn = $this->public_changeArrayToHtml($getList,TRUE);
            $print->addBodyFromHTML($dataIn);
            $print->addFooterNote(PrintCom::SignNotes());
            if ($in) {
                $this->registry->template->export($print->getHTML(true));
            } else {
                $this->registry->template->export($print->getHTML(true), __EXPORT_TYPE_EXCEL, __APP_CONTROLLER);
            }
        }// endfunction process_inxuat
    }

    