<?php

# mtn S.A subscription & unsubscription handler
include_once 'func.php';
$logFile = "sub.log";
$data = file_get_contents('php://input');
log_action($data, $logFile);
#$request = $_REQUEST;
/*
  foreach ($_REQUEST as $k=>$v){
  log_action("Parameter: $k, Value: $v", $logFile);
  }
 */
header($logFile);
$response = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:loc="http://www.csapi.org/schema/parlayx/sms/notification/v2_2/local">
       <soapenv:Header/>
       <soapenv:Body>
<loc:notifySmsDeliveryReceiptResponse/> </soapenv:Body>
    </soapenv:Envelope>';
echo $response;
?>
