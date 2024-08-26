<?php
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
