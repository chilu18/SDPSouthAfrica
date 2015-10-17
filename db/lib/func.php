<?php

function log_action($msg, $logFile) {
    $fp = fopen($logFile, 'a+');
    fputs($fp, $msg . "\n");
    fclose($fp);
    return TRUE;
}

function db_query($sql, $bind = null) {
    $c = db_connect();
    $res = array();
    $s = oci_parse($c, $sql);
    if ($bind != null) {
        foreach ($bind as $key => $value) {
            oci_bind_by_name($s, ":" . $key, $value);
        }
    }
    oci_execute($s);
    #oci_fetch_all($s, $res);
    while ($row = oci_fetch_object($s)) {
        //while($row = oci_fetch_all($s)){
        $res[] = $row;
    }
    return $res;
}

function db_execute($sql, $bind = null) {
    $c = db_connect();
    $res = array();
    $s = oci_parse($c, $sql);
    if ($bind != null) {
        foreach ($bind as $key => $value) {
            oci_bind_by_name($s, ":" . $key, htmlentities($value, ENT_QUOTES));
        }
    }
    $res = oci_execute($s);
    return $res;
}

function db_connect() {
    $desc = "(DESCRIPTION =(ADDRESS = (PROTOCOL = TCP)(HOST = 397970-vm2.db1.localhost.co.uk)(PORT = 1521))(CONNECT_DATA =(SERVER = DEDICATED)(SERVICE_NAME = mydb.397970-vm2.db1.localhost.co.uk)))";
   $conn = oci_connect('v2ngw', 'Fri1007', "$desc");
    return $conn;
}
