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
$response = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:loc="http://www.csapi.org/schema/parlayx/data/sync/v1_0/local"><soapenv:Header/>
<soapenv:Body>
<loc:syncOrderRelationResponse> <loc:result>0</loc:result> <loc:resultDescription>OK</loc:resultDescription>
</loc:syncOrderRelationResponse> </soapenv:Body>
</soapenv:Envelope>
';
echo $response;
?>
