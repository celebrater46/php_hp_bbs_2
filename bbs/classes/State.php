<?php

namespace php_hp_bbs\bbs\classes;

class State
{
    public $page;
    public $color;
    public $mode;
    public $edit;
    public $delete;

    function __construct(){
        $this->page = isset($_GET["page"]) ? (int)$_GET["page"] : 1;
        $this->color = isset($_GET["color"]) ? (int)$_GET["color"] : 0;
        $this->mode = isset($_GET["mode"]) ? (int)$_GET["mode"] : 0;
        $this->edit = isset($_GET["edit"]) ? (int)$_GET["edit"] : null;
        $this->delete = isset($_GET["delete"]) ? (int)$_GET["delete"] : null;
    }
}