<?php
include_once("./config/config.php");
startSession();
initializeSession();

$res = $con->query("SELECT COUNT(*) FROM `folder`");
$row_count = $res->fetch_row()[0];
if ($row_count <= 0) {
    die(); // 沒有任何資料夾
}

if (!empty($_GET["folder"])) {
    $folder = $con->real_escape_string($_GET["folder"]);
    $res = $con->query("SELECT COUNT(*) FROM `folder` WHERE `uuid` = \"$folder\"");
    $row_count = $res->fetch_row()[0];
    if ($row_count <= 0) {
        die(); // 查無資料
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>大棉被的文件放置站點</title>
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link href="https://netdna.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" />
        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" />
        <link href="./main.css" rel="stylesheet" />
    </head>

    <body>
        <div class="container">
            <div class="view-account">
                <section class="module">
                    <div class="module-inner">
                        <div class="side-bar">
                            <div class="user-info">
                                <img class="img-profile img-circle img-responsive center-block" src="./profile.png" alt="大棉被" />
                                <ul class="meta list list-unstyled">
                                    <li class="name">大棉被 <label class="label label-info">喵嗚</label></li>
                                    <li class="activity">【Neko 喵】</li>
                                </ul>
                            </div>

                            <nav class="side-menu">
                                <ul class="nav">
                                    <?php
                                    $res = $con->query("SELECT * FROM `folder`");
                                    while ($row = $res->fetch_assoc()) {
                                        $folderUUID = $con->real_escape_string($row["uuid"]);
                                        if (empty($folder)) {
                                            $folder = $folderUUID;
                                        }
                                        ?>
                                        <li>
                                            <a href="./index.php?folder=<?php echo cleanHTMLSpecialChar($folderUUID); ?>"
                                            <?php
                                            if ($folderUUID == $folder) {
                                                ?>
                                                   style="color: green;"
                                                   <?php
                                               }
                                               ?>
                                               >
                                                <span class="fa fa-folder"></span>
                                                <?php
                                                if (!is_null($row["password"])) {
                                                    ?>
                                                    <span class="fa fa-lock"></span>
                                                    <?php
                                                }
                                                echo cleanHTMLSpecialChar($row["name"]);
                                                ?>
                                            </a>
                                        </li>
                                        <?php
                                    }
                                    ?>
                                </ul>
                            </nav>
                        </div>

                        <div class="content-panel">
                            <div class="content-header-wrapper">
                                <h2 class="title">這裡是大棉被的文件放置站點<br />主要用於存放各類文書檔案</h2>
                            </div>

                            <div class="content-utilities">
                                <div class="actions">
                                    <div class="btn-group" role="group">
                                        <a href="javascript: window.location.href = window.location.href" type="button" class="btn btn-default" data-toggle="tooltip" data-placement="bottom" title data-original-title="重新整理">
                                            <i class="fa fa-refresh"> 重新整理</i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php
                            $res = $con->query("SELECT * FROM `folder` WHERE `uuid` = \"$folder\"");
                            $row = $res->fetch_assoc();
                            $password = $row["password"];
                            if (is_null($password)) {
                                pushArrayContainCheck($_SESSION["access_folder"], $folder);
                            } else if (!empty($_POST["verify"]) && isset($_POST["password"])) {
                                if ($password == ($use_hash ? hash("sha512", $_POST["password"]) : $_POST["password"])) {
                                    pushArrayContainCheck($_SESSION["access_folder"], $folder);
                                }
                            }

                            if (in_array($folder, $_SESSION["access_folder"])) {
                                ?>
                                <div class="drive-wrapper drive-grid-view">
                                    <?php
                                    if (!is_null($row["memo"])) {
                                        ?>
                                        <div class="content-header-wrapper">
                                            <h2 class="title"><?php echo $row["memo"]; ?></h2>
                                        </div>
                                        <?php
                                    }
                                    ?>

                                    <div class="content-header-wrapper">
                                        <h2 class="title">釘選文件</h2>
                                    </div>

                                    <div class="grid-items-wrapper">
                                        <?php
                                        $res = $con->query("SELECT * FROM `file` WHERE `folder_uuid` = \"$folder\" AND `category_id` = 1");
                                        while ($row = $res->fetch_assoc()) {
                                            $link = cleanHTMLSpecialChar("./download.php?file=" . $row["uuid"]);
                                            ?>
                                            <div class="drive-item module text-center">
                                                <div class="drive-item-inner module-inner">
                                                    <div class="drive-item-title"><a href="<?php echo $link; ?>" target="_blank"><?php echo cleanHTMLSpecialChar($row["name"]); ?></a></div>
                                                    <div class="drive-item-thumb">
                                                        <a href="<?php echo $link; ?>"><i class="fa fa-file-<?php echo cleanHTMLSpecialChar($row["type"]); ?>-o text-<?php echo cleanHTMLSpecialChar($row["status"]); ?>"></i></a>
                                                    </div>
                                                </div>
                                                <div class="drive-item-footer module-footer">
                                                    <ul class="utilities list-inline">
                                                        <li>
                                                            <a href="<?php echo $link; ?>" target="_blank" data-toggle="tooltip" data-placement="top" title data-original-title="下載"><i class="fa fa-download"></i></a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>

                                <div class="drive-wrapper drive-list-view">
                                    <div class="content-header-wrapper">
                                        <h2 class="title">文件列表</h2>
                                    </div>

                                    <div class="table-responsive drive-items-table-wrapper">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th class="type"></th>
                                                    <th class="name truncate">文件名稱</th>
                                                    <th class="date">上傳日期</th>
                                                    <th class="size">檔案大小</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <?php
                                                $page = 1;
                                                if (!empty($_GET["page"])) {
                                                    $page = (int) $_GET["page"];
                                                }

                                                $res = $con->query("SELECT COUNT(*) FROM `file` WHERE `folder_uuid` = \"$folder\" AND `category_id` = 2");
                                                $row_count = $res->fetch_row()[0];
                                                $page_count = ceil($row_count / $page_size);
                                                if ($page <= 0 || $page > $page_count) {
                                                    $page = 1;
                                                }

                                                $offset = ($page - 1) * $page_size;
                                                $res = $con->query("SELECT * FROM `file` WHERE `folder_uuid` = \"$folder\" AND `category_id` = 2 LIMIT $offset, $page_size");
                                                while ($row = $res->fetch_assoc()) {
                                                    $link = cleanHTMLSpecialChar("./download.php?file=" . $row["uuid"]);
                                                    ?>
                                                    <tr>
                                                        <td class="type"><i class="fa fa-file-<?php echo cleanHTMLSpecialChar($row["type"]); ?>-o text-<?php echo cleanHTMLSpecialChar($row["status"]); ?>"></i>
                                                            <?php
                                                            if (!is_null($row["password"])) {
                                                                ?>
                                                                <i class="fa fa-lock"></i>
                                                                <?php
                                                            }
                                                            ?>
                                                        </td>
                                                        <td class="name truncate"><a href="<?php echo $link; ?>" target="_blank"><?php echo cleanHTMLSpecialChar($row["name"]); ?></a></td>
                                                        <td class="date"><?php echo cleanHTMLSpecialChar(str_replace("-", "／", $row["date"])); ?></td>
                                                        <td class="size"><?php echo getReadableFileSize($row["path"]); ?></td>
                                                    </tr>
                                                    <?php
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <nav aria-label="page">
                                        <ul class="pagination">
                                            <?php
                                            if ($page != 1) {
                                                ?>
                                                <li class="page-item page-li"><a class="page-link" href="./index.php?page=1&folder=<?php echo $folder; ?>">首頁</a></li>
                                                <li class="page-item page-li"><a class="page-link" href="./index.php?page=<?php echo ($page - 1) . "&folder=" . $folder; ?>">上一頁</a></li>
                                                <?php
                                            }

                                            $show_page = 0;
                                            $page_start = $page - 4;
                                            if ($page_start < 1) {
                                                $page_start = 1;
                                            }
                                            for ($i = $page_start; $i <= $page_count && $show_page < 10; $i++, $show_page++) {
                                                if ($page != $i) {
                                                    ?>
                                                    <li class="page-item page-li"><a class="page-link" href="./index.php?page=<?php echo $i . "&folder=" . $folder; ?>"><?php echo $i; ?></a></li>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <li class="page-item page-li"><a class="page-link" href="#" onclick="return false;" style="color: red;"><?php echo $i; ?></a></li>
                                                    <?php
                                                }
                                            }

                                            if ($page < $page_count) {
                                                ?>
                                                <li class="page-item page-li"><a class="page-link" href="./index.php?page=<?php echo ($page + 1) . "&folder=" . $folder; ?>">下一頁</a></li>
                                                <li class="page-item page-li"><a class="page-link" href="./index.php?page=<?php echo $page_count . "&folder=" . $folder; ?>">尾頁</a></li>
                                                <?php
                                            }
                                            ?>
                                        </ul>
                                    </nav>
                                </div>
                                <?php
                            } else {
                                ?>
                                <div class="drive-wrapper drive-grid-view">
                                    <div class="content-header-wrapper">
                                        <h2 class="title">
                                            <?php
                                            if (empty($_POST["verify"])) {
                                                echo "這個資料夾受密碼保護，請輸入密碼進行驗證";
                                            } else {
                                                echo "驗證失敗";
                                                addVerifyFailCount();
                                                if ($_SESSION["verify_fail"] >= 3) {
                                                    echo "，錯誤次數達 3 次，暫時限制使用";
                                                    die();
                                                }
                                            }
                                            ?>
                                        </h2>
                                    </div>

                                    <div class="grid-items-wrapper">
                                        <form action="./index.php?folder=<?php echo $folder; ?>" method="POST">
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
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
        <script src="https://netdna.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <script>
            $(function () {
                $("[data-toggle=\"tooltip\"]").tooltip();
            });
        </script>
    </body>
</html>