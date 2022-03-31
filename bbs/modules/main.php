<?php

namespace php_hp_bbs\bbs\modules;

function check_password($password){
    if(isset($password)){
        $ptn = "/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]){8,}/"; // 大文字小文字数字を含む8文字以上
        if(strlen($password) > 10
            && preg_match($ptn, $password))
        {
            return true;
        } else {
            return false;
        }
    } else {
        return true;
    }
}

function check_posted_data($posted){
    if(mb_strlen($posted->user, "UTF-8") > 50){
        header('Location: error.php?code=1');
        exit;
    } else if($posted->len < 2000){
        header('Location: error.php?code=2');
        exit;
    } else if(preg_match('/[!#<>:;&~@%+$"\'\*\^\(\)\[\]\|\/\.,_-]+/', $_POST["password"])){
        header('Location: error.php?code=5');
        exit;
    } else if(check_password($_POST["password"]) === false){
        header('Location: error.php?code=6');
        exit;
    }
}

function send_mail($posted){
    $subject = "You got a new message at " . $posted->thread . "!";
    $msg = "Hi, dear my friend." . "\n";
    $msg .= "You got a new message in your " . PHBBS_SITE_NAME . "'s " . $posted->thread . " thread at " . $posted->date . "." . "\n\n";
    $msg .= "Subject: " . $posted->user . "\n";
    $msg .= "Message: " . $posted->text;
    mmm\send_mail($subject, $msg);
}