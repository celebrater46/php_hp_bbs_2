<?php

namespace php_hp_bbs\bbs\classes;

//use php_hp_bbs\bbs\classes\State;
use fp_common_modules as cm;
use my_micro_mailer as mmm;

require_once dirname(__FILE__) . '/../../init.php';
require_once PHBBS_HCM_PATH;
require_once PHBBS_MMM_PATH;
require_once "State.php";

class PostComment extends Comment
{
    public $another_index;

    function __construct(){
        $this->name_full = cm\h($_POST["name"]);
        $this->thread = cm\h($_POST["thread_name"]);
        $this->log = "lists/" . $this->thread . ".log";
        $this->id = $this->get_id();
        $this->reply = isset($_POST["reply"]) ? (int)$_POST["reply"] : 0;
        $this->date = date("Y-m-d_H:i:s");
        $this->date_unix = time();
        $this->user = $this->get_name($this->name_full);
        $this->title = cm\h($_POST["title"]);
        $this->text = cm\h($_POST["text"]);
        $this->len = mb_strlen($this->text, "UTF-8");
        $this->cap = $this->get_cap($this->name_full);
        $this->hp = "";
        $this->mail = "";
        $this->ip = $_SERVER['REMOTE_ADDR'];
        $this->password = cm\h($_POST["password"]);
        $this->another_index = $_POST["another_index"] ?? "";
    }

    function get_line(){
        return [
            "id" => $this->id,
            "reply" => $this->reply,
            "date" =>  $this->date,
            "date_unix" => $this->date_unix,
            "user" => $this->user,
            "user_full" => $this->name_full,
            "title" => $this->title,
            "cap" => $this->cap,
            "hp" => $this->hp,
            "mail" => $this->mail,
            "ip" => $this->ip,
            "password" => $this->password
        ];
    }

    function add_log(){
        $array = $this->get_line();
        $line = implode("<>", $array) . "<>0";
        error_log($line . "\n", 3, $this->log);
    }

    function save_text(){
        $path = "comments/" . $this->thread . "/" . $this->id . ".txt";
        error_log($this->text, 3, $path);
        $this->add_log();
        $this->send_mail_posted();
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

    function get_name($name){
        $sharp = strpos($name, "#");
        if($sharp === false){
            return $name;
        } else {
            return substr($name, 0, $sharp);
        }
    }

    function get_subject($state){
        if($state->lang === 1){
            return "You got a new message at " . $this->thread . "!";
        } else {
            return "掲示板 " . PHBBS_SITE_NAME . " の " . $this->thread . " スレッドに書き込みがありました。";
        }
    }

    function get_msg($state){
        if($state->lang === 1){
            $msg = "Hi, dear my friend." . "\n";
            $msg .= "You got a new message in your " . PHBBS_SITE_NAME . "'s " . $this->thread . " thread at " . $this->date . "." . "\n\n";
            $msg .= "Subject: " . $this->user . "\n";
            $msg .= "Message: " . $this->text;
        } else {
            $msg = "ユーザー名: " . $this->user . "\n";
            $msg .= "メッセージ: " . $this->text . "\n";
            $msg .= "投稿時間: " . $this->date;
        }
        return $msg;
    }

    function send_mail_posted(){
        $state = new State();
        $subject = $this->get_subject($state);
        $msg = $subject . "\n\n";
        $msg .= $this->get_msg($state);
        mmm\send_mail($subject, $msg);
    }
}