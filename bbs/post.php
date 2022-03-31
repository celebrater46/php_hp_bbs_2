<?php

namespace php_hp_bbs\bbs;

use Securimage;
use php_hp_bbs\bbs\classes\PostComment;
use my_micro_mailer as mmm;

require_once ( dirname(__FILE__) . '/../init.php');
require_once ( dirname(__FILE__) . '/../' . PHBBS_PIA_PATH . 'securimage/securimage.php');
require_once ( dirname(__FILE__) . '/../' . PHBBS_MMM_PATH);
require_once("classes/Comment.php");
require_once("classes/PostComment.php");

date_default_timezone_set('Asia/Tokyo');

$posted = new PostComment();

if(mb_strlen($posted->user, "UTF-8") > 50){
    header('Location: error.php?code=1');
    exit;
} else if(PHBBS_AUTH){
    check_auth($posted);
} else if($posted->len <= 2000){
    $posted->save_text();
    send_mail($posted);
    header('Location: ../index.php');
    exit;
} else {
    header('Location: error.php?code=2');
    exit;
}

function send_mail($posted){
    $subject = "You got a new message at " . $posted->thread . "!";
    $msg = "Hi, dear my friend." . "\n";
    $msg .= "You got a new message in your " . PHBBS_SITE_NAME . "'s " . $posted->thread . " thread at " . $posted->date . "." . "\n\n";
    $msg .= "Subject: " . $posted->user . "\n";
    $msg .= "Message: " . $posted->text;
    mmm\send_mail($subject, $msg);
}

function check_auth($posted){
    $securimage = new Securimage();
    if(PHBBS_AVAILABLE === false){
        header('Location: error.php?code=4');
    } else if(PHBBS_AUTH === false){
        $posted->save_text();
        send_mail($posted);
        header('Location: ../index.php');
    } else if(isset($_POST['captcha_code'])) {
        if($securimage->check($_POST['captcha_code']) === true) {
            $posted->save_text();
            send_mail($posted);
            header('Location: ../index.php');
        } else {
            header('Location: error.php?code=3');
        }
    }
    exit;
}