<?php

namespace php_hp_bbs;

use php_img_auth\modules as modules;
use php_hp_bbs\bbs\Comment;

require_once "init.php";
require_once "bbs/Comment.php";
require_once PHBBS_HTML_COMMON_MODULE . "html_common_module.php";

function get_list(){
    $txt = PHBBS_PATH . "bbs/list.txt";
    if(file_exists($txt)){
        return file($txt);
    } else {
        echo "NOT FOUND: " . $txt;
        return null;
    }
}

function get_comments($list){
    $array = [];
    foreach ($list as $line){
        array_push($array, new Comment($line));
    }
    return $array;
}

function get_comment($comment){
    $html = modules\space_br('<div class="phbbs_comment">', 2);
    $html .= modules\space_br('<hr>', 3);
    $html .= modules\space_br('<p>', 3);
    $html .= modules\space_br($comment->id . ": ", 4);
    $html .= modules\space_br('<span class="phbbs_name">' . $comment->sender . $comment->cap . "</span>", 4);
    $html .= modules\space_br($comment->date_string, 4);
    $html .= modules\space_br('</p>', 3);
    $html .= modules\space_br('<div class="phbbs_text">', 3);
    if($comment->reply !== 0 && $comment->reply !== ""){
        $html .= modules\space_br('<p>>> ' . $comment->reply . "</p>", 4);
        $html .= modules\space_br('<p>　</p>', 4);
    }
    foreach ($comment->text as $line){
        $html .= modules\space_br("<p>" . $line . "</p>", 4);
    }
    $html .= modules\space_br('</div>', 3);
    $html .= modules\space_br('</div>', 2);
    return $html;
}

function phbbs_get_comments_html(){
    $list = get_list();
    $comments = get_comments($list);
    rsort($comments); // reverse the order of the list
    $html = "";
    foreach ($comments as $comment){
        $html .= get_comment($comment);
    }
    return $html;
}

function phbbs_get_form_html(){
    $html = modules\space_br('<div class="phbbs_form_box">', 1);
    $html .= modules\space_br('<form action="' . PHBBS_PATH . 'bbs/post.php" method="post">', 2);
    $html .= modules\space_br('<div class="phbbs_form">', 3);
    $html .= modules\space_br('<label>', 4);
    $html .= modules\space_br('<span class="phbbs_form">名前：</span>', 5);
    $html .= modules\space_br('<input class="phbbs_comment" type="text" name="name">', 5);
    $html .= modules\space_br('</label>', 4);
    $html .= modules\space_br('</div>', 3);
    $html .= modules\space_br('<div class="phbbs_form">', 3);
    $html .= modules\space_br('<label>', 4);
    $html .= modules\space_br('<span class="phbbs_form">内容：</span>', 5);
    $html .= modules\space_br('<textarea class="phbbs_comment" name="text"></textarea>', 5);
    $html .= modules\space_br('</label>', 4);
    $html .= modules\space_br('</div>', 3);
    $html .= modules\space_br('<div class="phbbs_form"><button class="submit">送信！</button></div>', 3);
    $html .= modules\space_br('</form>', 2);
    $html .= modules\space_br('</div>', 1);
    return $html;
}

function phbbs_get_html(){
    $html = phbbs_get_form_html();
    $html .= phbbs_get_comments_html();
    return $html;
}