<?php

include_once("./config/config.php");
startSession();
initializeSession();

if (!isset($_GET["file"])) {
    die();
}
$file = $con->real_escape_string($_GET["file"]);
$res = $con->query("SELECT * FROM `file` WHERE `uuid` = \"$file\"");
$row = $res->fetch_assoc();

$folder = $row["folder_uuid"];
if (!in_array($folder, $_SESSION["access_folder"])) {
    die();
}

if (is_null($row["password"]) || in_array($file, $_SESSION["access_file"])) {
    downloadFile($row["path"], $row["name"]);
} else {
    header("Location: ./verify.php?file=" . $file, true, 302);
}

function downloadFile($path, $name) {
    if (!file_exists($path)) {
        die();
    }
    header("Content-Description: File Transfer");
    header("Content-Type: application/octet-stream");
    header("Content-Disposition: attachment; filename=" . $name);
    header("Content-Transfer-Encoding: binary");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Pragma: public");
    header("Content-Length: " . filesize($path));

    ob_clean();
    flush();
    readfile($path);
}

?>