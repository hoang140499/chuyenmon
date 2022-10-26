<?php 
    print_r($form); 
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
        //dung btnPrint, btnbtnExcel, btnDelete kh dc phai doi thanh btnPrint1, btnExcel1, btnDelete1
        echo UIHelper::Button('btnDelete1', 'btnDelete1', 'Xóa', "onclick='processCheckList(\"frmMain\",\"".__APP_CONTROLLER."/delete\",\"list\")'", 'delete');
        
        echo UIHelper::Button("btnAdd", "btnAdd", "Thêm mới", "onclick='prepareThem();'", 'add');

        echo UIHelper::Button('btnPrint1', 'btnPrint1', 'In', "onclick='newTab(\"frmMain\",\"". __HOST_NAME . __APP_PATH . __APP_CONTROLLER ."/public_in\")'");
        
        echo UIHelper::Button('btnExcel1', 'btnExcel1', 'Xuất Excel', "onclick='newTab(\"frmMain\",\"". __HOST_NAME . __APP_PATH . __APP_CONTROLLER ."/public_excel\")'");
        
        //them 2 input an
        echo UIHelper::Hidden("h_loai_cau_hoi", "h_loai_cau_hoi", "");
        echo UIHelper::Hidden("h_stt", "h_stt", "");
        ?>
    </div>
</form>

<script>
    index_value = 0;
    sttGoiy = 0;
    row_table = '<tr><th align="center" class="class_cau_tra_loi_row"  style="width:50px"><span name="stt[]" id="stt_#no#"></span>&nbsp;</th> \n\
            <td><input type="text" name="ma_goi_y[]" id="ma_goi_y_#no#" value="Mã câu trả lời " onfocus="ThemDongGoiY(#no#)" class="italic textbox lightslategray"  style="width:85px" ></td> \n\
            <td><input type="text" name="goi_y[]" id="goi_y_#no#" value="Chọn để nhập thêm câu trả lời mới" onfocus="ThemDongGoiY(#no#)" class="italic textbox lightslategray" style="width:300px" ></td> \n\
            <td><input type="text" name="dap_an[]" id="dap_an_#no#" value="Đáp án" class="italic textbox lightslategray"  style="width:80px" ></td> \n\
            <td><input type="text" name="so_tt[]" id="so_tt_#no#" value="Số thứ tự" class="italic textbox lightslategray"  style="width:80px" ></td> \n\
            <td align="center" style="width:50px"><img id="icon_#no#" src="<?php echo __HOST_NAME . __TEMPLATE . 'images/delete.png' ?>" title="Xóa" onclick="xoaGoiY(this)" class="center pointer delete_row" style="display:none"/>' + '</td>\n\
        </tr>';
    //<input type="hidden" class="class_cau_tra_loi_row" name="h_class_them_uy_vien[]" id="h_class_them_uy_vien_" value="1,1"/>\n\
    $(document).ready(function() {
        // xu ly nut chon radio
        $("input[name='loai_cau_hoi']").click(function() {
            if ($("#nhap_gop_y").is(":checked")) {
                //alert(1);
                $("#tb_goiycauhoi").hide();
                $("#tb_goiycauhoi").parent().hide();
                $("#h_loai_cau_hoi").val(<?php echo _QLDANHGIAHOCPHANHelper::$__LoaiCauHoi_GopY; ?>);
            } else if ($("#chon_cau_tra_loi").is(":checked")) {
                //alert(2);
                $("#tb_goiycauhoi").show();
                $("#tb_goiycauhoi").parent().show();
                $("#h_loai_cau_hoi").val(<?php echo _QLDANHGIAHOCPHANHelper::$__LoaiCauHoi_ChonGoiY; ?>);
            } if ($("#chon_nhieu_cau_tra_loi").is(":checked")) {
                //alert(3);
                $("#tb_goiycauhoi").show();
                $("#tb_goiycauhoi").parent().show();
                $("#h_loai_cau_hoi").val(<?php echo _QLDANHGIAHOCPHANHelper::$__LoaiCauHoi_ChonNhieu; ?>);
            }
        });
    });
    
    // mo form
    function prepareThem() {
        sttGoiy = 0;
        $('#tb_goiycauhoi').html('<thead></thead><tbody></tbody>');
        $('#tb_goiycauhoi > thead').append('<tr><th align="center">Stt</th> <th align="center">Mã câu trả lời</th> <th align="center">Câu trả lời</th> <th align="center">Đáp án</th> <th align="center">Số thứ tự</th> <th align="center">Xóa</th></tr>');
        html = row_table.replace(/#no#/g, index_value);
        $('#tb_goiycauhoi > tbody:last').append(html);
        $('#tb_goiycauhoi.table-list tr:odd').addClass('alt');
        
        showModal('dlg_<?php echo __APP_CONTROLLER; ?>');
        $("#sp_<?php echo __APP_CONTROLLER; ?>_Title").html("Thêm mới câu hỏi");
        // disable de phan biet voi form sua vi form sua khong dc thay doi ma
        
        $("#txt_<?php echo __APP_CONTROLLER; ?>_ottoan_td_cau_hoi_ma").removeAttr('disabled', false).addClass("notered").val('');
        $("#txt_<?php echo __APP_CONTROLLER; ?>_ottoan_td_cau_hoi_ten_vn").removeAttr('disabled', false).addClass("notered").val('');
        $("#txt_<?php echo __APP_CONTROLLER; ?>_ottoan_td_mon_hoc_ma").removeAttr('disabled', false).addClass("notered").val('');
        $("#cmb_<?php echo __APP_CONTROLLER; ?>_ottoan_td_mon_hoc_ma").removeAttr('disabled', false);
        // $("#txt_<?php echo __APP_CONTROLLER; ?>_ma_goi_y").removeAttr('disabled', false).addClass("notered").val('');
        // $("#txt_<?php echo __APP_CONTROLLER; ?>_goi_y").val('');

        $("#chon_cau_tra_loi").attr('checked', true);
        $("#h_loai_cau_hoi").val(<?php echo _QLDANHGIAHOCPHANHelper::$__LoaiCauHoi_ChonGoiY; ?>);
        $("#tb_goiycauhoi").show();
        $("#tb_goiycauhoi").parent().show();
        $("#bt_<?php echo __APP_CONTROLLER; ?>_submit").attr('onclick', '').unbind('click').click(function() {
            thucHienThem();
        });
    }// end prepareThem

    //thuchienThem
    function thucHienThem() {
        var dataString = $("#frm_<?php echo __APP_CONTROLLER;?>").serialize();
        $("#dlg_<?php echo __APP_CONTROLLER;?>").hide();
        $('#loading').show();
        //alert(dataString);
        $.ajax({
            type: "POST",
            url: "<?php echo __HOST_NAME . __APP_PATH.__APP_CONTROLLER; ?>/add",
            data: dataString+"&h_loai_cau_hoi="+$("#h_loai_cau_hoi").val(),
            dataType: "xml",
            success: function(xml){
                if (!xml) {
                    alert("Failed to connect.");
                }
                $(xml).find('Result').each(function(){
                    var msg = $(this).find('msg').text();
                    if(msg == "OK") {
                        $('#frmMain').append("<input type='hidden' name='success' value='Đã thêm dữ liệu thành công'/>");
                        document.frmMain.submit();
                    } else {
                        $("#dlg_<?php echo __APP_CONTROLLER;?>").slideDown();
                        $("#tb_msg_<?php echo __APP_CONTROLLER;?>").show();
                        $("#ul_<?php echo __APP_CONTROLLER;?>").html(decodeURI(msg)).show();
                        $('#loading').hide();
                    }
                });
            }
        });
    }// end thucHienThem

    //them dong cau tra loi
    function ThemDongGoiY(_id) {
        //alert(4456);
        if ($("#goi_y_"+_id).val() == "Chọn để nhập thêm câu trả lời mới") {
            $("#icon_" + index_value).show();
            sttGoiy++;
            index_value++;
            $("#stt_"+_id).html(sttGoiy);
            $("#ma_goi_y_"+_id).val("").removeClass("italic lightslategray");
            $("#goi_y_"+_id).val("").removeClass("italic lightslategray");
            $("#dap_an_"+_id).val("").removeClass("italic lightslategray");
            $("#so_tt_"+_id).val("").removeClass("italic lightslategray");
            html = row_table.replace(/#no#/g, index_value);
            $('#tb_goiycauhoi > tbody:last').append(html);
            $('#tb_goiycauhoi.table-list tr:odd').addClass('alt');
        }
    }// end fuction ThemDongGoiY

    //xoa dong goi y
    function xoaGoiY(obj) {
        $(obj).parent().parent().remove();
        var stt = 1;
        var len = $('#frm_tudiencauhoi th.class_cau_tra_loi_row').length;
        //bo stt khi xoa dong (hien khong the xoa dc do doan $(this).find('span').text(stt);)
        // var id = $(obj).parent().parent().context.id;
        // var idSlice = id.slice(5, 7);
        // $("#stt_"+(idSlice-1+1)).html('');
        //-----------------        
        $.each($('.class_cau_tra_loi_row'), function () {
            $(this).find('span').text(stt);
            stt++;
        });
        $('#tb_goiycauhoi.table-list tr:odd').addClass('alt');
        sttGoiy--;
        
    }// end function xoaGoiY

    function goToEditList(name, index) {
        showLoading();
        var id = $("#h_"+name+"_"+index).val();
        //alert(id);
        $.ajax({
            type: "POST",
            url: "<?php echo __HOST_NAME . __APP_PATH.__APP_CONTROLLER; ?>/public_get_info",
            data: 'id=' + id,
            dataType: "xml",
            success: function(xml){
                if (!xml) {
                    alert("Failed to connect.");
                }
                //console.log(xml);
                $(xml).find('Result').each(function(){
                    var msg = $(this).find('msg').text();
                    // console.log(msg);
                    if(msg == "OK") {
                        var flag = true;
                        sttGoiy = 0;
                        index_value = 0;
                        $('#tb_goiycauhoi').html('<thead></thead><tbody></tbody>');
                        $('#tb_goiycauhoi > thead').append('<tr><th align="center">Stt</th> <th align="center">Mã câu trả lời</th> <th align="center">Câu trả lời</th> <th align="center">Đáp án</th> <th align="center">Số thứ tự</th> <th align="center">Xóa</th></tr>');
                        var loai = '';
                        $(xml).find('item').each(function(){
                            if (flag) {
                                var maMon = $(this).find('ottoan_td_mon_hoc_ma').text();
                                var maCauHoi = $(this).find('ottoan_td_cau_hoi_ma').text();
                                var tenCauHoi = $(this).find('ottoan_td_cau_hoi_ten_vn').text();
                                loai = $(this).find('ottoan_td_cau_hoi_loai').text();

                                $('#h_<?php echo __APP_CONTROLLER; ?>_id').val(id);
                                $("#cmb_<?php echo __APP_CONTROLLER;?>_ottoan_td_mon_hoc_ma").val(maMon).attr('disabled', true);    
                                $("#txt_<?php echo __APP_CONTROLLER;?>_ottoan_td_cau_hoi_ma").val(maCauHoi).attr('disabled', true);                
                                $("#txt_<?php echo __APP_CONTROLLER;?>_ottoan_td_cau_hoi_ten_vn").val(tenCauHoi);                   
                                if (loai == '<?php echo _QLDANHGIAHOCPHANHelper::$__LoaiCauHoi_ChonGoiY ?>') {
                                    $("#chon_cau_tra_loi").attr('checked', true);
                                }
                                else if (loai == '<?php echo _QLDANHGIAHOCPHANHelper::$__LoaiCauHoi_ChonNhieu ?>') {
                                    $("#chon_nhieu_cau_tra_loi").attr('checked', true);
                                } else {
                                    $("#nhap_gop_y").attr('checked', true);
                                }
                                flag = false;
                                //alert(loai);
                            }
                            // alert(loai);
                            if (loai == '<?php echo _QLDANHGIAHOCPHANHelper::$__LoaiCauHoi_ChonGoiY?>' || loai == '<?php echo _QLDANHGIAHOCPHANHelper::$__LoaiCauHoi_ChonNhieu?>') {
                                var ma = $(this).find('ottoan_td_cau_tra_loi_ma').text();
                                var goiY = $(this).find('ottoan_td_cau_tra_loi_ten_vn').text();
                                var dapAn = $(this).find('ottoan_td_cau_tra_loi_dap_an').text();
                                var stt = $(this).find('ottoan_td_cau_tra_loi_stt').text();
                                sttGoiy++;
                                var html = '<tr><th align="center" class="class_cau_tra_loi_row"  style="width:50px"><span id="stt_'+index_value+'">'+sttGoiy+'</span>&nbsp;</th> \n\
                                                <td><input type="text" name="ma_goi_y[]" id="ma_goi_y_'+index_value+'" value="'+ma+'" class="textbox"  style="width:85px" readOnly="true"></td> \n\
                                                <td><input type="text" name="goi_y[]" id="goiy_'+index_value+'" value="'+goiY+'" onfocus="ThemDongGoiY('+index_value+')" class="textbox" style="width:300px" ></td> \n\
                                                <td><input type="text" name="dap_an[]" id="dap_an_'+index_value+'" value="'+dapAn+'" class="textbox"  style="width:80px" ></td> \n\
                                                <td><input type="text" name="so_tt[]" id="so_tt_'+index_value+'" value="'+stt+'" class="textbox"  style="width:80px" ></td> \n\
                                                <td align="center" style="width:50px"><img id="icon_'+index_value+'" src="<?php echo __HOST_NAME . __TEMPLATE . 'images/delete.png' ?>" title="Xóa" onclick="xoaGoiY(this)" class="center pointer delete_row"/>' + '</td>\n\
                                            </tr>';
                                $('#tb_goiycauhoi > tbody:last').append(html);
                                $('#tb_goiycauhoi.table-list tr:odd').addClass('alt');
                                index_value++;
                            }
                            $("#h_<?php echo __APP_CONTROLLER;?>_id").val(id);
                        });
                        prepareSua(loai);
                        $('#dlg_<?php echo __APP_CONTROLLER; ?>').slideDown();
                        $('#loading').hide();
                    } else {
                        alert("Không thể lấy thông tin theo yêu cầu.");
                        $('#loading').hide();
                        $('#overlay').slideUp();
                    }
                });
            }
        });
    }// end function goToEditList

    function prepareSua(loai) {
        var html = row_table.replace(/#no#/g, index_value);
        //console.log(html);
        $('#tb_goiycauhoi > tbody:last').append(html);
        $('#tb_goiycauhoi.table-list tr:odd').addClass('alt');
        if (loai == '<?php echo _QLDANHGIAHOCPHANHelper::$__LoaiCauHoi_ChonGoiY ?>') {
            $("#h_loai_cau_hoi").val(<?php echo _QLDANHGIAHOCPHANHelper::$__LoaiCauHoi_ChonGoiY; ?>);
            $("#tb_goiycauhoi").show();
            $("#tb_goiycauhoi").parent().show();
        }else if (loai == '<?php echo _QLDANHGIAHOCPHANHelper::$__LoaiCauHoi_ChonNhieu ?>') {
            $("#h_loai_cau_hoi").val(<?php echo _QLDANHGIAHOCPHANHelper::$__LoaiCauHoi_ChonNhieu; ?>);
            $("#tb_goiycauhoi").show();
            $("#tb_goiycauhoi").parent().show();
        } else {
            $("#h_loai_cau_hoi").val(<?php echo _QLDANHGIAHOCPHANHelper::$__LoaiCauHoi_GopY; ?>);
            $("#tb_goiycauhoi").hide();
            $("#tb_goiycauhoi").parent().hide();
        }

        $('#bt_<?php echo __APP_CONTROLLER;?>_submit').attr('onclick','').unbind('click').click(function() {
            thucHienSua();
        });
    }// end function prepareSua

    function thucHienSua() {
        var dataString = $("#frm_<?php echo __APP_CONTROLLER;?>").serialize();
        $('#dlg_<?php echo __APP_CONTROLLER;?>').hide();
        $('#loading').show();
        //console.log(dataString);
        $.ajax({
            type: "POST",
            url: "<?php echo __HOST_NAME . __APP_PATH.__APP_CONTROLLER;?>/edit",
            data: dataString+"&h_loai_cau_hoi="+$("#h_loai_cau_hoi").val(),
            dataType: "xml",
            success: function(xml){
                if (!xml) {
                    alert("Failed to connect.");
                }
                $(xml).find('Result').each(function(){
                    var msg = $(this).find('msg').text();
                    if(msg == "OK") {
                        $('#frmMain').append("<input type='hidden' name='success' value='Đã cập nhật dữ liệu thành công'/>");
                        document.frmMain.submit();
                    } else {
                        $("#dlg_<?php echo __APP_CONTROLLER;?>").slideDown();
                        $("#tb_msg_<?php echo __APP_CONTROLLER;?>").show();
                        $("#ul_<?php echo __APP_CONTROLLER;?>").html(decodeURI(msg)).show();
                        $("#loading").hide();
                    }
                });
            }
        });
    }// end function thucHienSua

</script>