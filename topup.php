<?php 
// CREATE BY VIET
    require_once("../../config/config.php");
    require_once("../../config/function.php");
    $user = $getUser['username'];
    $desiredDate = date("Y-m-d");
    $loaithe = check_string($_POST['loaithe']);
    $menhgia = check_string($_POST['menhgia']);
    $seri = check_string($_POST['seri']);
    $pin = check_string($_POST['pin']);
    $query = "SELECT * FROM `cards` WHERE DATE(`createdate`) = '$desiredDate' AND `status` = 'thatbai' AND `username` = '$user'";
    $result = $CMSNAV->get_list($query);
    if(empty($_SESSION['username']))
    {
        msg_error("Vui lòng đăng nhập ", BASE_URL(''), 2000);
    }
    if(empty($loaithe))
    {
        msg_error2("Vui lòng chọn loại thẻ");
    }
    if(empty($menhgia))
    {
        msg_error2("Vui lòng chọn mệnh giá");
    }
    if(empty($seri))
    {
        msg_error2("Vui lòng nhập seri thẻ");
    }
    if(empty($pin))
    {
        msg_error2("Vui lòng nhập mã thẻ");
    }
    if (strlen($seri) < 5 || strlen($pin) < 5)
    {
        msg_error2("Mã thẻ hoặc seri không đúng định dạng!");
    }
    if(count($result) > 2) {
    $isUpdate = $CMSNAV->update("users", [
        'banned' => '1'
    ], " `username` = '$user' ");

    echo '<meta http-equiv="refresh" content="2;url='.BASE_URL('Auth/Logout').'">';
    
    msg_error2("Tài Khoản Đã Bị Khóa Do Sai Thẻ!");
}

    $partner_id = $CMSNAV->site('Partner_ID');
    $partner_key = $CMSNAV->site('Partner_Key');
        $request_id = rand(100000000, 999999999);  //Mã đơn hàng của bạn
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_CONNECTTIMEOUT => 0,
            CURLOPT_TIMEOUT => 16,
          CURLOPT_URL => 'https://doithecao.vn/chargingws/v2',
            CURLOPT_USERAGENT => 'VIETPHUC CURL',
            CURLOPT_POST => 1,
            CURLOPT_SSL_VERIFYPEER => false, //Bỏ kiểm SSL
            CURLOPT_POSTFIELDS => http_build_query(array(
                'sign' => md5($partner_key.$pin.$seri),
                'telco' => $loaithe,
                'code' => $pin,
                'serial' => $seri,
                'amount' => $menhgia,
                'request_id' => $request_id,
                'partner_id' => $partner_id,
                'command'   => 'charging'
            ))
        ));
        $resp = curl_exec($curl);
        curl_close($curl);
        $obj = json_decode($resp, true);
        if(isset($obj['status']))
        {
            if ($obj['status'] == 99)
            {
                $CMSNAV->insert("cards", array(
                    'code' => $request_id,
                    'seri' => $seri,
                    'pin'  => $pin,
                    'loaithe' => $loaithe,
                    'menhgia' => $menhgia,
                    'thucnhan' => '0',
                    'username' => $getUser['username'],
                    'status' => 'xuly',
                    'note' => '',
                    'createdate' => gettime()
                ));
                napthecode( $getUser['username'], $loaithe,$menhgia,$pin,$seri);
                msg_success("Nạp thẻ thành công, đang chờ kết quả!", "", 2000);
            }
            else
            {
                msg_error2($obj['message']);
            }
        }
        else
        {
            msg_error2("Hệ Thống Thẻ Đang Lỗi Không Thể Gửi Được!");
        }
