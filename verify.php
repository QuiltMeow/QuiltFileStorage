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
    header("Location: ./download.php?file=" . $file, true, 301);
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>密碼驗證</title>
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous" />
        <link href="./main.css" rel="stylesheet" />
    </head>

    <body>
        <div class="paragraph"></div>

        <div class="container">
            <div class="card">
                <div class="card-header text-white bg-primary">
                    Quilt Web - File Download
                </div>

                <div class="card-body">
                    <h5 class="card-title text-center">檔案下載</h5>
                    <?php
                    $state = "primary";
                    $content = "這個文件受密碼保護，請輸入密碼進行驗證";
                    if (!empty($_POST["verify"])) {
                        if (isset($_POST["password"]) && $row["password"] == ($use_hash ? hash("sha512", $_POST["password"]) : $_POST["password"])) {
                            $state = "success";
                            $content = "驗證通過，即將開始下載檔案；如無法自動下載，請點選<a href=\"./download.php?file=" . cleanHTMLSpecialChar($file) . "\">這裡</a>開始下載";
                            pushArrayContainCheck($_SESSION["access_file"], $file);
                        } else {
                            addVerifyFailCount();
                            if ($_SESSION["verify_fail"] >= 3) {
                                die("錯誤次數達 3 次，暫時限制使用");
                            }
                            $state = "danger";
                            $content = "驗證失敗";
                        }
                    }
                    ?>
                    <div class="alert alert-<?php echo $state; ?> alert-dismissible fade show" role="alert"><?php echo $content; ?></div>

                    <?php
                    if ($state != "success") {
                        ?>
                        <form action="./verify.php?file=<?php echo cleanHTMLSpecialChar($file); ?>" method="POST">
                            <div class="mb-3 row">
                                <label for="password" class="col-sm-2 col-form-label">密碼</label>
                                <div class="col-sm-10">
                                    <input type="password" class="form-control" name="password" required autofocus />
                                </div>
                            </div>
                            <input type="hidden" name="verify" value="1" />

                            <div class="paragraph"></div>
                            <div class="text-center">
                                <button class="btn btn-success" type="submit">確認送出</button>
                            </div>
                        </form>
                        <?php
                    } else {
                        ?>
                        <meta http-equiv="refresh" content="5" />
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>
        <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    </body>
</html>