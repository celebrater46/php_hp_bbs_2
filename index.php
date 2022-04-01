<?php

// Copyright (C) Enin Fujimi All Rights Reserved.

namespace php_hp_bbs;

require_once "init.php";
require_once "phbbs_get_html.php";

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="Author" content="<?php echo PHBBS_AUTHOR; ?>">
    <link rel="stylesheet" href="css/style.css" type="text/css">
    <title><?php echo PHBBS_SITE_NAME; ?></title>
</head>
<body>
    <div class="containter">
        <h1><?php echo PHBBS_SITE_NAME; ?></h1>
        <?php echo phbbs_get_html("test"); ?>
    </div>
</body>
</html>
