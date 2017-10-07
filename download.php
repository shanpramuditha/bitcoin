<?php

$minutes = $_GET['minutes'];

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=data.csv');
$output = fopen('php://output', 'w');

if($minutes != null or $minutes != ''){
    $curl = curl_init();
    $agent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)';

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.gdax.com/products",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_USERAGENT => $agent,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "cache-control: no-cache",
            "postman-token: 86e8d4f7-802a-624d-f6e6-17e900f2b7b4"
        ),
    ));

    $file =fopen('php://output', 'w'); ;


    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);
    $today = new \DateTime('now');
    $dateBefore = new \DateTime('now');
    $dateBefore->modify("-".$minutes." minutes");
//    var_dump($today);
//    var_dump($dateBefore);


    if ($err) {
        echo "cURL Error #:" . $err;
    } else {
        $response = json_decode($response,true);
        foreach ($response as $item){
            fputcsv($file,array($item['id']));
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.gdax.com/products/".$item['id']."/candles?start=".$dateBefore->format('Y-m-d')."T".$dateBefore->format('H')."%3A".$dateBefore->format('i')."%3A".$dateBefore->format('s').".00000Z&end=".$today->format('Y-m-d')."T".$today->format('H')."%3A".$today->format('H')."%3A".$today->format('H').".00000Z&granularity=2000",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_USERAGENT => $agent,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_POSTFIELDS => "{\n\t\"granularity\":60\n}",
                CURLOPT_HTTPHEADER => array(
                    "cache-control: no-cache",
                    "content-type: application/json",
                    "postman-token: f9a0b489-602b-58ed-6dbc-f49e8c3feeca"
                ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
                echo "cURL Error #:" . $err;
            } else {
                $response = json_decode($response,true);
                foreach ($response as $item){
                    $row = $item[0].','.$item[1].','.$item[2].','.$item[3].','.$item[4].','.$item[5];
                    $item[0] = date('Y-m-d H:i:s', $item[0]);
                    fputcsv($file,$item);
                }

            }
        }
}


}