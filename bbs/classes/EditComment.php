<?php

namespace php_hp_bbs\bbs\classes;

class EditComment extends Comment
{
    function __construct(){
        $this->name_full = cm\h($_POST["name"]);
        $this->thread = cm\h($_POST["thread_name"]);
        $this->log = "lists/" . $this->thread . ".log";
        $this->id = $this->get_id();
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
}