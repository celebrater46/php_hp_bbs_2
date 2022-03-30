<?php

namespace php_hp_bbs\bbs;

use Securimage;

require_once ( dirname(__FILE__) . '/../init.php');
require_once ( dirname(__FILE__) . '/../' . PHBBS_PIA_PATH . 'securimage/securimage.php');

date_default_timezone_set('Asia/Tokyo');

$name_full = h($_POST["name"]);
//$setting = get_setting();

// 12|11|2007/06/13(Wed) 00:23:15|1181661795|名も無き投稿者|無題|7I7Cj53d7YIoI|https://google.com/|hoge123@google.com|192.168.1.100|53|0
$array = [
    "id" => get_id(),
    "reply" => isset($_POST["reply"]) ? (int)$_POST["reply"] : 0,
    "date" =>  date("Y-m-d_H:i:s"), // 2021-01-12 09:45:31
    "date_unix" => time(),
    "user" => get_name($name_full),
    "title" => h($_POST["title"]),
    "cap" => get_cap($name_full),
    "hp" => "",
    "mail" => "",
    "ip" => $_SERVER['REMOTE_ADDR']
];

if(mb_strlen($array["user"], "UTF-8") > 50){
    header('Location: error.php?code=1');
} else {
    $bool = save_text($array["id"]);
    if($bool){
        add_log($array);
        header('Location: ../index.php');
    } else {
        header('Location: error.php?code=2');
    }
}

function h($s) {
    return htmlspecialchars($s, ENT_QUOTES, "UTF-8");
}

function save_text($id){
    $securimage = new Securimage();
    if(isset($_POST['captcha_code'])) {
        if($securimage->check($_POST['captcha_code']) === true) {
//            echo 'Authorized.';
            $text = h($_POST["text"]);
            $path = "comments/" . $id . ".txt";
            $len = mb_strlen($text, "UTF-8");
            if($len <= 2000){
                error_log($text, 3, $path);
                return true;
            } else {
                return false;
            }
        } else {
            header('Location: error.php?code=3');
            exit;
//            echo 'エラー：認証用の英数字が正しくありません。';
        }
    }
}

//function check_strlen($text){
//    $len = mb_strlen($text, "UTF-8");
//}

function add_log($array){
    $path = "list.txt";
    $line = implode("|", $array) . "|0";
    error_log($line . "\n", 3, $path);
}

function get_name($name){
    $sharp = strpos($name, "#");
    if($sharp === false){
        return $name;
    } else {
        return substr($name, 0, $sharp);
    }
}

function get_cap($name){
    $sharp = strpos($name, "#");
    if($sharp === false){
        return "";
    }
    $key = substr($name, $sharp + 1);

    $salt = substr($key . 'H.', 1, 2);
    $salt = preg_replace('/[^\.-z]/', '.', $salt);
    $salt = strtr($salt, ':;<=>?@[\\]^_`', 'ABCDEFGabcdef');

    $cap = crypt($key, $salt);
    $cap = substr($cap, -10);

    return $cap;
}

function get_id(){
    $list = file("list.txt");
    $array = [];
    foreach ($list as $line){
        $temp = explode("|", $line);
        array_push($array, (int)$temp[0]);
    }
    rsort($array);
    return $array[0] + 1;
}

//function get_setting(){
//    $settings = file("../setting.txt");
//    $txt_mode = str_replace([" ", "\n", "\r", "\r\n"], "", $settings[3]);
//    if($txt_mode !== "true"){
//        return false;
//    } else {
//        return true;
//    }
//}