<?php

namespace php_hp_bbs\bbs\modules;

use php_hp_bbs\bbs\classes\State;

require_once dirname(__FILE__) . '/../../init.php';
require_once dirname(__FILE__) . '/../classes/State.php';

function get_list($thread){
    $txt = PHBBS_PATH . "bbs/lists/" . $thread . ".log";
    if(file_exists($txt)){
        return file($txt);
    } else {
        echo "NOT FOUND: " . $txt;
        return null;
    }
}

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
        header('Location: ' . get_index_and_code() . '401');
        exit;
    } else if($posted->len > 2000){
        header('Location: ' . get_index_and_code() . '402');
        exit;
    } else if(preg_match('/[!#<>:;&~@%+$"\'\*\^\(\)\[\]\|\/\.,_-]+/', $_POST["password"])){
        header('Location: ' . get_index_and_code() . '405');
        exit;
    } else if($_POST["password"] !== "" && check_password($_POST["password"]) === false){
        header('Location: ' . get_index_and_code() . '406');
        exit;
    }
}

function get_url_parameter(){
    $state = new State();
    return "?lang=" . $state->lang . "&mode=" . $state->mode;
}

function get_index_and_code(){
    return PHBBS_INDEX . get_url_parameter() . '&code=';
}