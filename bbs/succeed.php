<?php

namespace php_hp_bbs;

$code = isset($_GET["code"]) ? (int)$_GET["code"] : 0;

function get_title($code){
    switch($code){
        case 1: return "削除成功";
        default: return "投稿成功";
    }
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="Author" content="Enin Fujimi">
    <link rel="stylesheet" href="../css/style.css" type="text/css">
    <title><?php echo get_title($code); ?></title>
</head>
<body>
<div class="containter">
    <h1><?php echo get_title($code); ?></h1>
    <?php switch ($code) : case 1 : ?>
        <p>コメントの削除に成功しました。</p>
        <p>Deleted your comment successfully.</p>
        <?php break; ?>
    <?php default: ?>
        <p>コメントの投稿に成功しました！</p>
        <p>Posted your comment successfully.</p>
    <?php endswitch; ?>
    <p>　</p>
    <p>　</p>
    <p><a href="../index.php">- BACK -</a></p>
</div>
</body>
</html>