<?php

namespace php_hp_bbs\bbs\classes;

use common_modules as cm;

require_once ( dirname(__FILE__) . '/../../' . PHBBS_HCM_PATH);

class GetComment extends Comment
{
    public function __construct($line, $thread)
    {
//        parent::__construct($line, $thread);
//        12<>11<>2007/06/13(Wed) 00:23:15<>1181661795<>名も無き投稿者<>無題<>7I7Cj53d7YIoI<>https://google.com/<>hoge123@google.com<>192.168.1.100|53<>0
//        $id<>$reply<>$date<>$date_unix<>$user<>$title<>$text<>$cap<>$hp<>$mail<>$ip<>0（改行コードによるバグ対策）
        $this->thread = $thread;
        $list_data = explode("<>", $line);
        $this->id = (int)$list_data[0];
        $this->reply = (int)$list_data[1];
        $this->date = $list_data[2];
        $this->date_string = $this->get_date_string($list_data[2]);
        $this->date_unix = $list_data[3];
        $this->user = $list_data[4] === "" ? "名も無き投稿者" : $list_data[4];
        $this->name_full = $list_data[5] === "" ? "名も無き投稿者" : $list_data[5];
        $this->title = $list_data[6] === "" ? "無題" : $list_data[6];
        $this->cap = $list_data[7] === "" ? "" : "◆" . $list_data[7];
        $this->hp = $list_data[8];
        $this->mail = $list_data[9];
        $this->ip = $list_data[10];
        $this->password = $list_data[11];
        $this->get_text();
    }

    function get_name_full(){
        return $this->name_full;
    }

    function get_comment($de_links){
        $html = cm\space_br('<div class="phbbs_comment">', 2);
        $html .= cm\space_br('<hr>', 3);
        $html .= cm\space_br('<p>', 3);
        $html .= cm\space_br($this->id . ": ", 4);
        $html .= cm\space_br('<span class="phbbs_name">' . $this->user . $this->cap . "</span>", 4);
        $html .= cm\space_br($this->date_string, 4);
        if($this->password !== "" && $de_links){
            $path = cm\get_url_all();
            $head = strpos($path, "?") === false ? "?" : "&";
            $html .= cm\space_br('<a href="'. $path . $head . 'edit=' . $this->id . '">[編集]</a>', 4);
            $html .= cm\space_br('<a href="'. $path . $head . 'delete=' . $this->id . '">[削除]</a>', 4);
        }
        $html .= cm\space_br('</p>', 3);
        $html .= cm\space_br('<div class="phbbs_text">', 3);
        if($this->reply !== 0 && $this->reply !== ""){
            $html .= cm\space_br('<p>>> ' . $this->reply . "</p>", 4);
            $html .= cm\space_br('<p>　</p>', 4);
        }
        foreach ($this->text as $line){
            $html .= cm\space_br("<p>" . $line . "</p>", 4);
        }
        $html .= cm\space_br('</div>', 3);
        $html .= cm\space_br('</div>', 2);
        return $html;
    }

    function get_text(){
        $path = "bbs/comments/" . $this->thread . "/" . $this->id . ".txt";
        if(file_exists($path)){
            $temp = file($path);
            foreach ($temp as $line){
                if($line === "" || $line === "\r" || $line === "\n" || $line === "\r\n"){
                    array_push($this->text, "　");
                } else {
                    array_push($this->text, $line);
                }
            }
        } else {
            $this->text = [$path . "が存在しないか、読み込めません。"];
        }
    }

    function get_date_string($date){
        $temp = str_replace("-", "/", $date);
        $temp = str_replace("_", " ", $temp);
        return $temp;
    }
}