<?php

namespace php_hp_bbs\bbs;

use Securimage;
use php_hp_bbs\bbs\classes\PostComment;
//use my_micro_mailer as mmm;
use php_hp_bbs\bbs\modules as modules;

require_once ( dirname(__FILE__) . '/../init.php');
require_once ( dirname(__FILE__) . '/../' . PHBBS_PIA_PATH . 'securimage/securimage.php');
//require_once ( dirname(__FILE__) . '/../' . PHBBS_MMM_PATH);
require_once("classes/Comment.php");
require_once("classes/PostComment.php");
require_once("modules/main.php");

date_default_timezone_set('Asia/Tokyo');

$posted = new PostComment();

//if(mb_strlen($posted->user, "UTF-8") > 50){
//    header('Location: error.php?code=1');
//    exit;
//} else if($posted->len < 2000){
//    header('Location: error.php?code=2');
//    exit;
//} else if(preg_match('/[!#<>:;&~@%+$"\'\*\^\(\)\[\]\|\/\.,_-]+/', $_POST["password"])){
//    header('Location: error.php?code=5');
//    exit;
//} else if(check_password($_POST["password"]) === false){
//    header('Location: error.php?code=6');
//    exit;
//}

modules\check_posted_data($posted);

if(PHBBS_AUTH){
    check_auth($posted);
} else {
    $posted->save_text();
//    modules\send_mail_posted($posted);
//    header('Location: ../index.php');
    header('Location: succeed.php?code=0');
    exit;
}

//function check_password($password){
//    if(isset($password)){
//        $ptn = "/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]){8,}/"; // 大文字小文字数字を含む8文字以上
//        if(strlen($password) > 10
//        && preg_match($ptn, $password))
//        {
//            return true;
//        } else {
//            return false;
//        }
//    } else {
//        return true;
//    }
//}

//function send_mail($posted){
//    $subject = "You got a new message at " . $posted->thread . "!";
//    $msg = "Hi, dear my friend." . "\n";
//    $msg .= "You got a new message in your " . PHBBS_SITE_NAME . "'s " . $posted->thread . " thread at " . $posted->date . "." . "\n\n";
//    $msg .= "Subject: " . $posted->user . "\n";
//    $msg .= "Message: " . $posted->text;
//    mmm\send_mail($subject, $msg);
//}

function check_auth($posted){
    $securimage = new Securimage();
    if(PHBBS_AVAILABLE === false){
        header('Location: error.php?code=4');
    } else if(PHBBS_AUTH === false){
        $posted->save_text();
//        modules\send_mail_posted($posted);
        header('Location: succeed.php?code=0');
    } else if(isset($_POST['captcha_code'])) {
        if($securimage->check($_POST['captcha_code']) === true) {
            $posted->save_text();
//            modules\send_mail_posted($posted);
            header('Location: succeed.php?code=0');
        } else {
            header('Location: error.php?code=3');
        }
    }
    exit;
}