<?php

namespace php_hp_bbs\bbs\classes;

use common_modules as cm;

require_once ( dirname(__FILE__) . '/../../init.php');
require_once ( dirname(__FILE__) . '/../../' . PHBBS_HCM_PATH);

class PostComment extends Comment
{
    private $log;
    public $len;

    function __construct(){
        $this->name_full = cm\h($_POST["name"]);
        $this->thread = cm\h($_POST["thread_name"]);
        $this->log = "lists/" . $this->thread . ".log";
        $this->id = $this->get_id($this->thread);
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
        $this->password = cm\h($_POST["thread_name"]);
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
            "ip" => $this->ip
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

    function get_id(){
        $list = file($this->log);
        $array = [];
        foreach ($list as $line){
            $temp = explode("<>", $line);
            array_push($array, (int)$temp[0]);
        }
        rsort($array);
        return $array[0] + 1;
    }
}