<?php

// Copyright (C) Enin Fujimi All Rights Reserved.

namespace php_hp_bbs;

require_once "init.php";
require_once "bbs/classes/Comment.php";
require_once "phbbs_get_html.php";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="Author" content="Enin Fujimi">
    <link rel="stylesheet" href="css/style.css" type="text/css">
    <title><?php echo PHBBS_SITE_NAME; ?></title>
</head>
<body>
    <div class="containter">
        <h1><?php echo PHBBS_SITE_NAME; ?></h1>
        <p>作品の感想、アプリやゲームのバグ報告等あったらください。</p>
        <p>名前の後に #（半角シャープ）を追加し、任意の文字列を入れると、</p>
        <p>なりすまし防止用の暗号キーが追加されます（2ch でいう「トリ」です）。</p>
        <?php echo phbbs_get_html("test2"); ?>
    </div>
</body>
</html>
