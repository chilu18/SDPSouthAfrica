<?php
# mtn S.A subscription & unsubscription syncronization handler

include_once 'db/lib/func.php';
//include_once 'db/connect.php';
$logFile = "res/sub.log";
$data = file_get_contents('php://input');
//$data = file_get_contents('res/sub.xml');
//log_action($data, $logFile);echo 'test3';
//var_dump($data);
$clean_xml = str_ireplace(array('soapenv:', 'soap:', 'ns1:'), array('', '', ''), $data);
$xml = simplexml_load_string($clean_xml);
$body = $xml->Body;

$user_id = $body->syncOrderRelation->userID->ID;
$sp_id = $body->syncOrderRelation->spID;
$product_id = $body->syncOrderRelation->productID;
$service_id = $body->syncOrderRelation->serviceID;
$service_list = $body->syncOrderRelation->serviceList;
$update_time = $body->syncOrderRelation->updateTime;
$update_type = $body->syncOrderRelation->updateType;
$update_desc = $body->syncOrderRelation->updateDesc;
$effective_time = $body->syncOrderRelation->effectiveTime;
$expiry_time = $body->syncOrderRelation->expiryTime;

$item = $body->syncOrderRelation->extensionInfo->item;
$ext = array();
foreach ($item as $key => $v) {
    $ext[strtolower($v->key)] = $v->value;
}

$bind = array(
    'USER_ID' => $user_id,
    'SP_ID' => $sp_id,
    'PRODUCT_ID' => $product_id,
    'SERVICE_ID' => $service_id,
    'SERVICE_LIST' => $service_list,
    'UPDATE_TYPE' => $update_type,
    'UPDATE_TIME' => $update_time,
    'UPDATE_DESC' => $update_desc,
    'EFFECTIVE_TIME' => $effective_time,
    'EXPIRY_TIME' => $expiry_time,
    'CHARGE_MODE' => $ext['chargemode'],
    'MDSP_SUB_EXP_MODE' => $ext['mdspsubexpmode'],
    'OBJECT_TYPE' => $ext['objecttype'],
    'IS_AUTO_EXTEND' => $ext['isautoextend'],
    'IS_FREE_PERIOD' => $ext['isfreeperiod'],
    'OPERATOR_ID' => $ext['operatorid'],
    'PAY_TYPE' => $ext['paytype'],
    'TRANSACTION_ID' => $ext['transactionid'],
    'ORDER_KEY' => $ext['orderkey'],
    'DURATION_OF_GRACE_PERIOD' => $ext['durationofgraceperiod'],
    'SERVICE_PAY_TYPE' => $ext['servicepaytype'],
    'SERVICE_AVAILABILITY' => $ext['serviceavailability'],
    'DURATION_OF_SUSPEND_PERIOD' => $ext['durationofsuspendperiod'],
    'FEE' => $ext['fee'],
    'CYCLE_END_TIME' => $ext['cycleendtime'],
    'START_TIME' => $ext['starttime'],
    'CHANNEL_ID' => $ext['channelid'],
    'TRACE_UNIQUE_ID' => $ext['traceuniqueid'],
    'OPER_CODE' => $ext['opercode'],
    'RENT_SUCCESS' => $ext['rentsuccess'],
    'TRY' => $ext['try'],
    'UPDATE_REASON' => $ext['updatereason'],
);

$response = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:loc="http://www.csapi.org/schema/parlayx/data/sync/v1_0/local"><soapenv:Header/>
                <soapenv:Body>
                <loc:syncOrderRelationResponse> <loc:result>0</loc:result> <loc:resultDescription>OK</loc:resultDescription>
                </loc:syncOrderRelationResponse> </soapenv:Body>
                </soapenv:Envelope>
                ';
if (strtolower($update_desc) == 'addition') {

    $res = db_execute("insert into sdp_subscription(user_id,sp_id,product_id,service_id,service_list,update_type,update_time,update_desc,effective_time,expiry_time,charge_mode,mdsp_sub_exp_mode,object_type,is_auto_extend,is_free_period,operator_id,pay_type,transaction_id,order_key,duration_of_grace_period,service_pay_type,service_availability,duration_of_suspend_period,fee,cycle_end_time,start_time,channel_id,trace_unique_id,oper_code,rent_success,try)
    values( :user_id , :sp_id , :product_id , :service_id , :service_list , :update_type , :update_time, :update_desc , :effective_time , :expiry_time , :charge_mode , :mdsp_sub_exp_mode , :object_type , :is_auto_extend , :is_free_period , :operator_id, :pay_type , :transaction_id , :order_key , :duration_of_grace_period , :service_pay_type, :service_availability , :duration_of_suspend_period , :fee , :cycle_end_time , :start_time, :channel_id , :trace_unique_id , :oper_code , :rent_success , :try)", $bind);
    $res_log = db_execute("insert into sdp_logs(user_id,sp_id,product_id,service_id,service_list,update_type,update_time,update_desc,effective_time,expiry_time,charge_mode,mdsp_sub_exp_mode,object_type,is_auto_extend,is_free_period,operator_id,pay_type,transaction_id,order_key,duration_of_grace_period,service_pay_type,service_availability,duration_of_suspend_period,fee,cycle_end_time,start_time,channel_id,trace_unique_id,oper_code,rent_success,try)
  values( :user_id , :sp_id , :product_id , :service_id , :service_list , :update_type , :update_time, :update_desc , :effective_time , :expiry_time , :charge_mode , :mdsp_sub_exp_mode , :object_type , :is_auto_extend , :is_free_period , :operator_id, :pay_type , :transaction_id , :order_key , :duration_of_grace_period , :service_pay_type, :service_availability , :duration_of_suspend_period , :fee , :cycle_end_time , :start_time, :channel_id , :trace_unique_id , :oper_code , :rent_success , :try)", $bind);

    if ($res == TRUE && $res_log == TRUE) {
        header($logFile);
        echo $response;
    } else {
        echo 'addition error';
    }
} elseif (strtolower($update_desc) == 'deletion') {

    $bindy = array(
        'USER_ID' => $user_id,
        'PRODUCT_ID' => $product_id,
        'SERVICE_ID' => $service_id
    );

    $res_log = db_execute("insert into sdp_logs(user_id,sp_id,product_id,service_id,service_list,update_type,update_time,update_desc,effective_time,expiry_time,charge_mode,mdsp_sub_exp_mode,object_type,is_auto_extend,is_free_period,operator_id,pay_type,transaction_id,order_key,duration_of_grace_period,service_pay_type,service_availability,duration_of_suspend_period,fee,cycle_end_time,start_time,channel_id,trace_unique_id,oper_code,rent_success,try)
  values( :user_id , :sp_id , :product_id , :service_id , :service_list , :update_type , :update_time, :update_desc , :effective_time , :expiry_time , :charge_mode , :mdsp_sub_exp_mode , :object_type , :is_auto_extend , :is_free_period , :operator_id, :pay_type , :transaction_id , :order_key , :duration_of_grace_period , :service_pay_type, :service_availability , :duration_of_suspend_period , :fee , :cycle_end_time , :start_time, :channel_id , :trace_unique_id , :oper_code , :rent_success , :try)", $bind);
    $del = db_execute("delete from sdp_subscription where user_id = :user_id and product_id = :product_id and service_id = :service_id", $bind);
    //var_dump($del);
    if ($del == TRUE && $res_log == TRUE) {
        header($logFile);
        echo $response;
    } else {
        echo 'deletion error';
    }
} else {
    echo 'general error';
}
