<?php

namespace php_hp_bbs\bbs;

use Securimage;
use php_hp_bbs\bbs\classes\PostComment;
//use my_micro_mailer as mmm;
use php_hp_bbs\bbs\modules as modules;

require_once ( dirname(__FILE__) . '/../init.php');
require_once ( PHBBS_PIA_PATH . 'securimage/securimage.php');
require_once("classes/Comment.php");
require_once("classes/PostComment.php");
require_once("modules/main.php");

date_default_timezone_set('Asia/Tokyo');

$posted = new PostComment();

modules\check_posted_data($posted);

if(PHBBS_AUTH){
    check_auth($posted);
} else {
    $posted->save_text();
    header('Location: succeed.php?code=0');
    exit;
}

function check_auth($posted){
    $securimage = new Securimage();
    if(PHBBS_AVAILABLE === false){
        header('Location: error.php?code=4');
    } else if(PHBBS_AUTH === false){
        $posted->save_text();
        header('Location: succeed.php?code=0');
    } else if(isset($_POST['captcha_code'])) {
        if($securimage->check($_POST['captcha_code']) === true) {
            $posted->save_text();
            header('Location: succeed.php?code=0');
        } else {
            header('Location: error.php?code=3');
        }
    }
    exit;
}