<?php

function log_action($msg, $logFile) {
    #$date_time = date("Y-m-d h:i:s");
    #$logpath = '/var/www/html/nsl/';
    #$logFile = "call.log";
    //$log = "$date_time >> $msg";
    $fp = fopen($logFile, 'a+');
    fputs($fp, $msg."\n");
    fclose($fp);
    return TRUE;
}
?>
