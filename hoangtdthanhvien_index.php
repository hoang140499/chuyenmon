
<?php 
    print_r($form); 
    print_r($form_them_tu_file);
?>
<form action="<?php echo __HOST_NAME . __APP_PATH . __APP_CONTROLLER; ?>" id="frmMain" name="frmMain" method="post">
    <h2><?php echo $title; ?></h2>
    <?php
    print_r($data);
    print_r($paging_top);
    print_r($list);
    print_r($paging_bottom);
    
    ?>
    <div class="control">
        <?php
        echo UIHelper::Button("btnAdd", "btnAdd", "Thêm mới", "onclick='prepareThem();'", 'add');

        echo UIHelper::Button('btnDelete', 'btnDelete', 'Xóa', "onclick='processCheckList(\"frmMain\",\"" . __APP_CONTROLLER . "/delete\",\"" . __APP_CONTROLLER . "\")'", 'delete');

        echo UIHelper::Button("btnPrint", "btnPrint", "In", "onclick='newTab(\"frmMain\",\"".__HOST_NAME . __APP_PATH . __APP_CONTROLLER . "/public_in\")'");
        
        echo UIHelper::Button("btnExcel", "btnExcel", "Xuất Excel", "onclick='newTab(\"frmMain\",\"".__HOST_NAME . __APP_PATH . __APP_CONTROLLER . "/public_excel\")'");
        
        echo UIHelper::Button('btnAdd', 'btAdd', 'Thêm từ file', "onclick='prepareThemTuFile();showModal(\"dlg_form_them_tu_file\")'", 'addfile');
        ?>
    </div>
</form>

<script>
    // mo form
    function prepareThem() {
        showModal('dlg_<?php echo __APP_CONTROLLER; ?>');
        $("#sp_<?php echo __APP_CONTROLLER; ?>_Title").html("Thêm mới cán bộ");
        // disable de phan biet voi form sua vi form sua khong dc thay doi ma
        $("#txt_<?php echo __APP_CONTROLLER; ?>_hoang_cusc_td_thanh_vien_ma").removeAttr('disabled', false).addClass("notered").val('');
        $("#txt_<?php echo __APP_CONTROLLER; ?>_hoang_cusc_td_thanh_vien_ho_lot").val('');
        $("#txt_<?php echo __APP_CONTROLLER; ?>_hoang_cusc_td_thanh_vien_ten").val('');
        $("#cmb_<?php echo __APP_CONTROLLER; ?>_hoang_cusc_td_thanh_vien_phai option:first").attr('selected', 'selected');
        $("#date_<?php echo __APP_CONTROLLER; ?>_hoang_cusc_td_thanh_vien_ngay_bd").val('');
        $("#txt_<?php echo __APP_CONTROLLER; ?>_hoang_cusc_td_thanh_vien_ngay_kt").val('');
        $("#cmb_<?php echo __APP_CONTROLLER; ?>_hoang_cusc_td_nhom_du_an_ma").removeAttr('disabled', false);
        $("#cmb_<?php echo __APP_CONTROLLER; ?>_hoang_cusc_td_nhom_du_an_ma option:first").attr('selected', 'selected');
        $("#bt_<?php echo __APP_CONTROLLER; ?>_submit").attr('onclick', '').unbind('click').click(function() {
            thucHienThem();
        });
        
    }

    //thuc hien them du lieu
    function thucHienThem() {
        //alert(555);
        var dataString = $("#frm_<?php echo __APP_CONTROLLER; ?>").serialize();
        $("#loading").show();
        $.ajax({
            type: "POST",
            url: "<?php echo __HOST_NAME . __APP_PATH . __APP_CONTROLLER; ?>/add",
            data: dataString,
            dataType: "xml",
            success: function(xml) {
                if (!xml) {
                    alert("Failed to connect");
                }
                $(xml).find('Result').each(function() {
                    var msg = $(this).find('msg').text();
                    if (msg == "OK") {
                        $("#frmMain").append("<input type='hidden' name='success' value='Đã thêm dữ liệu thành công!'/>");
                        document.frmMain.submit();
                        $('#loading').hide();
                    }
                    else {
                        $("#dlg_<?php echo __APP_CONTROLLER; ?>").slideDown();
                        $("#tb_msg_<?php echo __APP_CONTROLLER; ?>").show();
                        $("#ul_<?php echo __APP_CONTROLLER; ?>").html(decodeURI(msg)).show();
                        $('#loading').hide();
                    }
                })
            }
        })
    }

    //Lay du lieu khi chon sua
    function goToEditList (name, index) {
        showLoading();
        $.ajax({
            type: "POST",
            url: "<?php echo __HOST_NAME . __APP_PATH . __APP_CONTROLLER; ?>/public_get_info",
            data: 'id=' + $("#h_" + name + "_" + index).val(), //lay gia tri id cua hang index
            dataType: "xml",
            success: function(xml) {
                if (!xml) {
                    alert("Failed to connect.");
                }
                $(xml).find('Result').each(function() {
                    var msg = $(this).find('msg').text();
                    if (msg == "OK") {
                        //alert($("#h_"+ name + "_" + index).val());
                        //lay du lieu getInfoById ben model
                        var id = $("#h_" + name + "_" + index).val();
                        var manhom = $(this).find('manhom').text().htmlEntitiesDecode();
                        var macb = $(this).find('macb').text().htmlEntitiesDecode();
                        var holot = $(this).find('holot').text().htmlEntitiesDecode();
                        var ten = $(this).find('ten').text().htmlEntitiesDecode();
                        var gioitinh = $(this).find('gioitinh').text().htmlEntitiesDecode();
                        var ngaybd = $(this).find('ngaybd').text().htmlEntitiesDecode();
                        var ngaykt = $(this).find('ngaykt').text().htmlEntitiesDecode();

                        //alert(id+'-'+manhom+'-'+macb+'-'+holot+'-'+ten+'-'+gioitinh+'-'+ngaybd+'-'+ngaykt);
                        //h_tddghoang_id
                        //alert(ngaybd);
                        $("#h_<?php echo __APP_CONTROLLER;?>_id").val(id);
                        $("#cmb_<?php echo __APP_CONTROLLER; ?>_hoang_cusc_td_nhom_du_an_ma").val(manhom);
                        $("#txt_<?php echo __APP_CONTROLLER; ?>_hoang_cusc_td_nhom_du_an_ma").val(manhom);
                        $("#txt_<?php echo __APP_CONTROLLER; ?>_hoang_cusc_td_thanh_vien_ma").val(macb);
                        $("#txt_<?php echo __APP_CONTROLLER; ?>_hoang_cusc_td_thanh_vien_ho_lot").val(holot);
                        $("#txt_<?php echo __APP_CONTROLLER; ?>_hoang_cusc_td_thanh_vien_ten").val(ten);
                        $("#cmb_<?php echo __APP_CONTROLLER; ?>_hoang_cusc_td_thanh_vien_phai").val(gioitinh);
                        $("#date_<?php echo __APP_CONTROLLER; ?>_hoang_cusc_td_thanh_vien_ngay_bd").val(ngaybd);
                        $("#txt_<?php echo __APP_CONTROLLER; ?>_hoang_cusc_td_thanh_vien_ngay_kt").val(ngaykt);
                        $("#dlg_<?php echo __APP_CONTROLLER; ?>").slideDown();
                        $('#loading').hide();
                        prepareSua();
                    } else {
                        //alert(456);
                        alert("Không thể lấy thông tin theo yêu cầu");
                        $('#loading').hide();
                        $('#overlay').slideUp();
                    }
                });
            }
        });
    }// end function goToEditList
    
    function prepareSua() {
        //alert(123);
        $("#sp_<?php echo __APP_CONTROLLER; ?>_Title").html("Sửa thành viên");
        $("#txt_<?php echo __APP_CONTROLLER; ?>_hoang_cusc_td_nhom_du_an_ma").attr('disabled', true).removeClass("notered");
        $("#cmb_<?php echo __APP_CONTROLLER; ?>_hoang_cusc_td_nhom_du_an_ma").attr('disabled', true).removeClass("notered");
        $("#txt_<?php echo __APP_CONTROLLER; ?>_hoang_cusc_td_thanh_vien_ma").attr('disabled', true).removeClass("notered");
        $("#cmb_<?php echo __APP_CONTROLLER; ?>_hoang_cusc_td_thanh_vien_ma").attr('disabled', true).removeClass("notered");
        $("#bt_<?php echo __APP_CONTROLLER; ?>_submit").attr('onclick', '').unbind('click').click(function() {
            thucHienSua();
        })
    }// end function prepareSua

    function thucHienSua() {
        //alert(54654);
        var dataString = $("#frm_<?php echo __APP_CONTROLLER; ?>").serialize();
        //alert(dataString);
        $('#dlg_<?php echo __APP_CONTROLLER; ?>').hide();
        $('#loading').show();
        $.ajax({
            type: "POST",
            url: "<?php echo __HOST_NAME . __APP_PATH . __APP_CONTROLLER; ?>/edit",
            data: dataString,
            dataType: "xml",
            success: function(xml) {
                if (!xml) {
                    alert("Failed to connect.");
                }
                $(xml).find('Result').each(function() {
                    //alert(msg);
                    var msg = $(this).find('msg').text();
                    if (msg == "OK") {
                        $('#frmMain').append("<input type='hidden' name='success' value='Đã cập nhật dữ liệu thành công!'/>");
                        document.frmMain.submit();
                        //$('#loading').hide();   
                    } else {
                        $('#dlg_<?php echo __APP_CONTROLLER; ?>').slideDown();
                        $('#tb_msg_<?php echo __APP_CONTROLLER; ?>').show();
                        $('#ul_<?php echo __APP_CONTROLLER; ?>').html(decodeURI(msg)).show();
                        $('#loading').hide();     
                    }
                });
            }
        });
    }// end fuction thucHienSua

    //tao function click cho btn_submit
    function prepareThemTuFile() {
        $('#bt_form_them_tu_file_submit').attr('onclick', '').unbind('click').click(function () {
            thucHienThemTuFile();
        });
    }// end function prepareThemTuFile

    // goi den ham addfile ben Controller
    function thucHienThemTuFile() {
        $('#frm_form_them_tu_file').attr('action', "<?php echo __HOST_NAME . __APP_PATH . __APP_CONTROLLER; ?>/addfile");
        $('#frm_form_them_tu_file').submit();
    }// enf function thucHienThemTuFile

</script>


