<?php

namespace php_hp_bbs\bbs;

use php_hp_bbs\bbs\classes\DeleteComment;

require_once ( dirname(__FILE__) . '/../init.php');
require_once("classes/Comment.php");
require_once("classes/DeleteComment.php");
require_once("modules/main.php");

$comment = new DeleteComment();
$comment->auth_delete();
//header('Location: ../index.php');
header('Location: succeed.php?code=1');
exit;