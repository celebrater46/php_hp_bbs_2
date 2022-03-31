<?php

namespace php_hp_bbs\bbs\classes;

use common_modules as cm;
use Exception;
use php_hp_bbs\bbs\modules as modules;
use my_micro_mailer as mmm;

require_once ( dirname(__FILE__) . '/../../init.php');
require_once ( dirname(__FILE__) . '/../modules/main.php');
require_once ( dirname(__FILE__) . '/../../' . PHBBS_HCM_PATH);
require_once ( dirname(__FILE__) . '/../../' . PHBBS_MMM_PATH);

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
            if($i === $this->key){
                continue;
            } else {
                error_log($this->list[$i], 3, $this->log);
            }
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

    function auth_delete(){
        if(file_exists($this->log)){
            $this->list = file($this->log);
//            var_dump($this->list);
//            var_dump($this->id);
//            $this->key = array_search( (string)$this->id, array_column( $this->list, 0)); // 二次元配列から、特定の id のキーを抜く
            $this->key = $this->get_key();
//            var_dump($this->key);
            if($this->key === false){
                header('Location: error.php?code=8');
                exit;
            } else {
                $exploded = explode("<>", $this->list[$this->key]);
//                var_dump($exploded);
                var_dump($exploded[11]);
                var_dump($this->password);
                if($exploded[11] === $this->password){
                    $this->rewrite_log();
                    $this->send_mail_deleted();
//                    try{
//                    } catch (Exception $ex){
//                        echo $ex->getMessage();
//                        header('Location: error.php');
//                    }
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