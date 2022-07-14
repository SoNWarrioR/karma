<?php

include_once "../config/config.php";

$db_name = DB_NAME;
$db_user = DB_USER;
$db_pass = DB_PASSWORD;
$db_host = DB_HOST;

try {
    $db = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo $e->getMessage();
    throw $e;
}

return $db;