<?php
session_start();

require_once(__DIR__ . '/../functions/activity.php');
require_once(__DIR__ . '/../functions/auth.php');
require_once(__DIR__ . '/../functions/redirect.php');
require_once(__DIR__ . '/../functions/sessions.php');
require_once(__DIR__ . '/../functions/csrf.php');
require_once(__DIR__ . '/../functions/csv.php');

define('BASE_URL', 'http://localhost/IT3B');

define('DB_HOST','localhost');
define('DB_NAME', 'it3b_lab_db');
define('DB_USER', 'root'); 
define('DB_PASS','');

try{
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" .DB_NAME, DB_USER, DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
}catch(PDOException $e){
    die("Connection Failed: " . $e->getMessage());
}

?>