<?php

namespace php_hp_bbs\bbs;

use php_hp_bbs\bbs\classes\DeleteComment;
use php_hp_bbs\bbs\modules as modules;

require_once ( dirname(__FILE__) . '/../init.php');
require_once("classes/Comment.php");
require_once("classes/DeleteComment.php");
require_once("modules/main.php");

$comment = new DeleteComment();
$comment->auth_delete();
header('Location: ' . modules\get_index_and_code("") . '201');
exit;