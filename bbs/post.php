<?php

namespace php_hp_bbs\bbs;

use Securimage;
use php_hp_bbs\bbs\classes\Post;
use my_micro_mailer as mmm;

require_once ( dirname(__FILE__) . '/../init.php');
require_once ( dirname(__FILE__) . '/../' . PHBBS_PIA_PATH . 'securimage/securimage.php');
require_once ( dirname(__FILE__) . '/../' . PHBBS_MMM_PATH);
require_once ("classes/Post.php");

date_default_timezone_set('Asia/Tokyo');

$posted = new Post();

//$name_full = h($_POST["name"]);
//$thread = h($_POST["thread_name"]);
//$log = "lists/" . $thread . ".log";

// 12<>11<>2007/06/13(Wed) 00:23:15<>1181661795<>名も無き投稿者<>無題<>7I7Cj53d7YIoI<>https://google.com/<>hoge123@google.com<>192.168.1.100|53<>0
//$posted = [
//    "id" => get_id($thread),
//    "reply" => isset($_POST["reply"]) ? (int)$_POST["reply"] : 0,
//    "date" =>  date("Y-m-d_H:i:s"), // 2021-01-12 09:45:31
//    "date_unix" => time(),
//    "user" => get_name($name_full),
//    "title" => h($_POST["title"]),
//    "cap" => get_cap($name_full),
//    "hp" => "",
//    "mail" => "",
//    "ip" => $_SERVER['REMOTE_ADDR']
//];

//if(mb_strlen($posted["user"], "UTF-8") > 50){
if(mb_strlen($posted->user, "UTF-8") > 50){
    header('Location: error.php?code=1');
} else if(PHBBS_AUTH){
    check_auth($posted);
//    $bool = save_text($posted, $thread);
//    if($bool){
//        add_log($posted, $thread);
//        header('Location: ../index.php');
//    } else {
//        header('Location: error.php?code=2');
//    }
} else if($posted->len <= 2000){
//    error_log($this->text, 3, $path);
    $posted->save_text();
//    $posted->add_log();
    send_mail($posted);
//    $posted->send_mail();
    header('Location: ../index.php');
    exit;
//        return true;
} else {
//        return false;
    header('Location: error.php?code=2');
    exit;
}

//function h($s) {
//    return htmlspecialchars($s, ENT_QUOTES, "UTF-8");
//}

//function save_text($posted, $thread, $log){
//    $text = h($_POST["text"]);
//    $path = "threads/" . $thread . "/comments/" . $posted["id"] . ".txt";
//    $len = mb_strlen($text, "UTF-8");
//    if($len <= 2000){
//        error_log($text, 3, $path);
//        add_log($posted, $log);
//        $subject = "You got a new message at " . $thread . "!";
//        $msg = "Hi, dear my friend." . "\n";
//        $msg .= "You got a new message in your " . PHBBS_SITE_NAME . "'s " . $thread . " thread at " . date('Y/m/d H:i:s') . "." . "\n\n";
//        $msg .= "Subject: " . $posted["user"] . "\n";
//        $msg .= "Message: " . $text;
//        mmm\send_mail($subject, $msg);
//        header('Location: ../index.php');
//        exit;
////        return true;
//    } else {
////        return false;
//        header('Location: error.php?code=2');
//        exit;
//    }
//}

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
        exit;
    } else if(PHBBS_AUTH === false){
        $posted->save_text();
    } else if(isset($_POST['captcha_code'])) {
        if($securimage->check($_POST['captcha_code']) === true) {
            $posted->save_text();
        } else {
            header('Location: error.php?code=3');
            exit;
        }
    }
}

//function add_log($posted, $log){
////    $path = "threads/" . $thread . "/list.txt";
////    $path = "lists/" . $thread . ".log";
//    $line = implode("<>", $posted) . "<>0";
//    error_log($line . "\n", 3, $log);
//}

//function get_name($name){
//    $sharp = strpos($name, "#");
//    if($sharp === false){
//        return $name;
//    } else {
//        return substr($name, 0, $sharp);
//    }
//}

//function get_cap($name){
//    $sharp = strpos($name, "#");
//    if($sharp === false){
//        return "";
//    }
//    $key = substr($name, $sharp + 1);
//
//    $salt = substr($key . 'H.', 1, 2);
//    $salt = preg_replace('/[^\.-z]/', '.', $salt);
//    $salt = strtr($salt, ':;<=>?@[\\]^_`', 'ABCDEFGabcdef');
//
//    $cap = crypt($key, $salt);
//    $cap = substr($cap, -10);
//
//    return $cap;
//}

//function get_id($thread){
//    $list = file("lists/" . $thread . ".log");
////    $list = file("threads/" . $thread . "/list.txt");
//    $array = [];
//    foreach ($list as $line){
//        $temp = explode("<>", $line);
//        array_push($array, (int)$temp[0]);
//    }
//    rsort($array);
//    return $array[0] + 1;
//}