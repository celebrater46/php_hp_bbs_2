<?php

namespace php_hp_bbs\bbs\classes;

use common_modules as cm;
use my_micro_mailer as mmm;

require_once ( dirname(__FILE__) . '/../../init.php');
require_once ( dirname(__FILE__) . '/../modules/main.php');
require_once PHBBS_HCM_PATH;
require_once PHBBS_MMM_PATH;

class DeleteComment extends Comment
{
    private $list;
    private $key;

    function __construct(){
        $this->thread = cm\h($_POST["thread_name"]);
        $this->log = "lists/" . $this->thread . ".log";
        $this->id = isset($_GET["delete"]) ? (int)$_GET["delete"] : null;
//        $this->id = isset($_POST["delete"]) ? (int)$_POST["delete"] : null;
        $this->password = cm\h($_POST["password"]);
        if($this->id === null){
            header('Location: error.php?code=10');
            exit;
        }
    }

    function rewrite_log(){
        unlink($this->log);
        for($i = 0; $i < count($this->list); $i++){
            error_log($this->list[$i], 3, $this->log);
//            if($i === $this->key){
//                continue;
//            } else {
//                error_log($this->list[$i], 3, $this->log);
//            }
        }
    }

    function get_key(){
        $i = 0;
        foreach ($this->list as $line){
            $temp = explode("<>", $line);
            if((int)$temp[0] === $this->id){
                return $i;
            }
            $i++;
        }
        return false;
    }

    function delete_password(){
        $array = explode("<>", $this->list[$this->key]);
        $array[11] = "__DELETED__";
        $this->list[$this->key] = implode("<>", $array);
    }

    function auth_delete(){
        if(file_exists($this->log)){
            $this->list = file($this->log);
            $this->key = $this->get_key();
            if($this->key === false){
                header('Location: error.php?code=8');
                exit;
            } else {
                $exploded = explode("<>", $this->list[$this->key]);
                var_dump($exploded[11]);
                var_dump($this->password);
                if($exploded[11] === $this->password){
                    $this->delete_password();
                    $this->rewrite_log();
                    $this->send_mail_deleted();
                } else {
                    header('Location: error.php?code=9');
                    exit;
                }
            }
        } else {
            header('Location: error.php?code=7');
            exit;
        }
    }

    function send_mail_deleted(){
        $subject = "【" . PHBBS_SITE_NAME . "】コメントが削除されました";
        $msg = PHBBS_SITE_NAME . " の  " . $this->thread . " スレッドの ID: " . $this->id . " のコメントが投稿者によって削除されました。";
        mmm\send_mail($subject, $msg);
    }
}