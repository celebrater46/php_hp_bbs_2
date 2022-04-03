<?php

namespace php_hp_bbs\bbs\classes;

class State
{
    public $page;
    public $color;
    public $edit;
    public $delete;
    public $code;
    public $lang;
    public $mode; // 自サイト用

    function __construct(){
        $this->page = isset($_GET["page"]) ? (int)$_GET["page"] : 1;
        $this->color = isset($_GET["color"]) ? (int)$_GET["color"] : 0;
        $this->edit = isset($_GET["edit"]) ? (int)$_GET["edit"] : null;
        $this->delete = isset($_GET["delete"]) ? (int)$_GET["delete"] : null;
        $this->lang = isset($_GET["lang"]) ? (int)$_GET["lang"] : 0;
        $this->code = isset($_GET["code"]) ? (int)$_GET["code"] : 0;
        $this->mode = isset($_GET["mode"]) ? (int)$_GET["mode"] : 0;
    }
}