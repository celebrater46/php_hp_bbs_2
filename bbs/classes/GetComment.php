<?php

namespace php_hp_bbs\bbs\classes;

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
        $this->title = $list_data[5] === "" ? "無題" : $list_data[5];
        $this->cap = $list_data[6] === "" ? "----------" : "◆" . $list_data[6];
        $this->hp = $list_data[7];
        $this->mail = $list_data[8];
        $this->ip = $list_data[9];
        $this->get_text();
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