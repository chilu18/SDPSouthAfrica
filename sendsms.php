<?php
include_once 'func.php';
//include_once ('db/connect.php');
include_once ('db/lib/func.php');
/*$spid = ($_REQUEST['spid']) ? @$_REQUEST['spid'] : "270110000268";
$pwd =  ($_REQUEST['pwd']) ? @$_REQUEST['pwd'] : "bmeB500";*/

$spid = "270110000268";
$pwd =  "bmeB500";

$bundleId = "";
$serviceId= ($_REQUEST['service']) ? $_REQUEST['service'] : "27012000001458";
$ts = date('Ymdhis');
$address = ($_REQUEST['address']) ? $_REQUEST['address'] : "27838548135";
$password = md5($spid.$pwd.$ts);
$senderName = ($_REQUEST['sender']) ? $_REQUEST['sender'] : "839300688011";

$endpoint = "http://localhost/SDPSouthAfrica/notifySmsDeliveryReceiptResponse.php";

$msg = ($_REQUEST['msg']) ? $_REQUEST['msg'] : "";
$message = str_replace('&', '&amp;', $msg);

$xml = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:v2="http://www.huawei.com.cn/schema/common/v2_1" xmlns:loc="http://www.csapi.org/schema/parlayx/sms/send/v2_2/local">
<soapenv:Header>
<v2:RequestSOAPHeader>
<v2:spId>'.$spid.'</v2:spId> 
<v2:spPassword>'.$password.'</v2:spPassword> 
<v2:serviceId>'.$serviceId.'</v2:serviceId> 
<v2:timeStamp>'.$ts.'</v2:timeStamp> 
<v2:OA>'.$address.'</v2:OA> 
<v2:FA>'.$address.'</v2:FA> 
</v2:RequestSOAPHeader>
</soapenv:Header>
<soapenv:Body>
<loc:sendSms>
<loc:addresses>tel:'.$address.'</loc:addresses> 
<loc:senderName>'.$senderName.'</loc:senderName> 
<loc:message>'.$message.'</loc:message> 
<loc:receiptRequest> 
<endpoint>'.$endpoint.'</endpoint> 
<interfaceName>SmsNotification</interfaceName> 
<correlator>00001</correlator>
</loc:receiptRequest>
</loc:sendSms>
</soapenv:Body>
</soapenv:Envelope>';
$headers = array(
    "POST  HTTP/1.1",
    "Host: 196.11.240.224",
    "Content-type: application/soap+xml; charset=\"utf-8\"",
    "SOAPAction: \"\"",
    "Content-length: " . strlen($xml)
);
echo "<br>====Request XML====<br>".PHP_EOL;
echo $xml.PHP_EOL;

#$url = "http://83.138.190.170:80/parleyx/adapter.php";
#$url = "http://41.206.4.219:8310/SmsNotificationManagerService/services/SmsNotificationManager";
$url = "http://196.11.240.223:8310/SendSmsService/services/SendSms";
$soap_do = curl_init();
curl_setopt($soap_do, CURLOPT_URL, $url);
curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true);
curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($soap_do, CURLOPT_POST, true);
curl_setopt($soap_do, CURLOPT_HTTPHEADER, $headers);
curl_setopt($soap_do, CURLOPT_POSTFIELDS, $xml);
//curl_setopt($soap_do, CURLOPT_USERPWD, $username . ":" . $password);
$result = curl_exec($soap_do);
$err = curl_error($soap_do);
log_action($result, 'messages.log');

$bind = array(
    'userid' => $address,
    'msg' => $message,
    'sid' => $serviceId
);
//var_dump($bind);
db_execute("insert into sdp_sent_logs (user_id, msg, ref_id) values (:userid, :msg, :sid)", $bind);

echo "<br>======Response XML======<br>".PHP_EOL;
print_r($result);
echo PHP_EOL;

