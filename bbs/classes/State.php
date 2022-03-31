<?php

namespace php_hp_bbs\bbs\classes;

class State
{
    public $page;
    public $color;
    public $mode;
//    public $start_comment;
//    public $end_comment;

    function __construct(){
        $this->page = isset($_GET["page"]) ? (int)$_GET["page"] : 1;
        $this->color = isset($_GET["color"]) ? (int)$_GET["color"] : 0;
        $this->mode = isset($_GET["mode"]) ? (int)$_GET["mode"] : 0;
//        $this->start_comment = ($this->page - 1) * PHBBS_MAX_COMMENTS;
    }
}