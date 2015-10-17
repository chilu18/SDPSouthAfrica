<?php

//include_once ('../db/connect.php');
include_once ('db/lib/func.php');

$con = mysqli_connect("localhost", "m_health", "m_health", "mhealth");
// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$date_now = strtotime("now");
$date_yday = strtotime("-1 day");
$result = mysqli_query($con, "SELECT * FROM tip where schedule_time between '$date_yday' and '$date_now'");
    //var_dump($result);
while ($row = mysqli_fetch_array($result)) {
    $res = db_query("select * from sdp_subscription b join sdp_services s on s.service_id = b.service_id where s.reference_id = :rid", array('rid' => $row['category_id']));
    //var_dump($res);
    foreach ($res as $r) {
       $url = "http://localhost/SDPSouthAfrica/sendsms.php?address=" . urlencode($r->USER_ID) . "&msg=" . urlencode($row['gist']) . "&service=" . urlencode($r->SERVICE_ID) . "&sender=" . urlencode($r->SENDER_ID);
        echo $r->USER_ID .'<br>'. $row['gist'].'<br>'.$url.'<br>'.$r->SERVICE_ID.'<br>';
        $resp = do_response($url);
        echo '<br>';
        var_dump($resp);
        echo '<br><br>';
    }
}
mysqli_close($con);

function do_response($url) {
    try {
# try pushing request to url;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPGET, 1); // Make sure GET method it used
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // Return the result
        curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/cookies.txt');
        curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/cookies.txt');
        $res = curl_exec($ch); // Run the request
    } catch (Exception $ex) {

        $res = 'NOK';
    }
    return $res;
}

function checkDestination($number) {
    $first = substr($number, 0, 1);
    if ($first == '+') {
        return $number;
    } else {
        return '+' . $number;
    }
}
