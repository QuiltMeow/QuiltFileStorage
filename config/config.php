<?php

$use_hash = true;
$page_size = 10;

if (basename($_SERVER["PHP_SELF"]) == "config.php") {
    die(); // 喵嗚 OWO ? 你想幹嘛 ><
}

$db_host = "127.0.0.1:4599";
$db_account = "document";
$db_password = "p7_8rrdTKs0yz4wPvbCqD1wkjByTYlKv";
$database = "document";
$con = new mysqli($db_host, $db_account, $db_password, $database);

if ($con->connect_errno) {
    die(); // 資料庫連線失敗
}

$con->query("SET NAMES \"UTF8MB4\"");

function cleanHTMLSpecialChar($data) {
    return htmlspecialchars($data, ENT_QUOTES);
}

function byteConvert($byte) {
    if ($byte <= 0) {
        return "0.00 B";
    }
    $exponential = floor(log($byte, 1000));

    $byteSymbol = array("B", "KB", "MB", "GB", "TB", "PB");
    return round($byte / pow(1000, $exponential), 2) . " " . $byteSymbol[$exponential];
}

function getReadableFileSize($path) {
    if (!file_exists($path)) {
        return byteConvert(0);
    }
    return byteConvert(filesize($path));
}

function startSession($expire = 1200) {
    if ($expire == 0) {
        $expire = ini_get("session.gc_maxlifetime");
    } else {
        ini_set("session.gc_maxlifetime", $expire);
    }

    if (empty($_COOKIE["PHPSESSID"])) {
        session_set_cookie_params($expire);
        session_start();
    } else {
        session_start();
        if (isset($_SESSION["verify_fail"]) && $_SESSION["verify_fail"] >= 3) {
            die();
        }
        setcookie("PHPSESSID", session_id(), time() + $expire);
    }
}

function initializeSession() {
    if (!isset($_SESSION["access_folder"])) {
        $_SESSION["access_folder"] = array();
    }
    if (!isset($_SESSION["access_file"])) {
        $_SESSION["access_file"] = array();
    }
}

function pushArrayContainCheck(&$array, $data) {
    if (!in_array($data, $array)) {
        array_push($array, $data);
    }
}

function addVerifyFailCount() {
    if (!isset($_SESSION["verify_fail"])) {
        $_SESSION["verify_fail"] = 1;
    } else {
        $_SESSION["verify_fail"] ++;
    }
}

?>