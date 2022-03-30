<?php

namespace php_hp_bbs;

use php_img_auth\modules as modules;
use php_hp_bbs\bbs\classes\Comment;
use php_img_auth as pia;

use php_number_link_generator\classes\NumberLink;

require_once "init.php";
require_once "bbs/classes/Comment.php";
require_once PHBBS_HCM_PATH;
require_once PHBBS_PIA_PATH . "init.php";
require_once PHBBS_PIA_PATH . "pia_get_html.php";

require_once( PHBBS_PNLG_PATH . 'init.php');
require_once( PHBBS_PNLG_PATH . 'classes/NumberLink.php');

function get_list($thread){
    $txt = PHBBS_PATH . "bbs/threads/" . $thread . "/list.txt";
    if(file_exists($txt)){
        return file($txt);
    } else {
        echo "NOT FOUND: " . $txt;
        return null;
    }
}

function get_comments($list, $thread){
    $array = [];
    foreach ($list as $line){
        array_push($array, new Comment($line, $thread));
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

function phbbs_get_comments_html($thread){
    $list = get_list($thread);
    $comments = get_comments($list, $thread);
    $comments_num = count($comments);
    $link = new NumberLink($comments_num);
    rsort($comments); // reverse the order of the list
    $html = "";
    foreach ($comments as $comment){
        $html .= get_comment($comment);
    }
    if(PHBBS_MAX_COMMENTS < $comments_num){
        $parameters = "";
        $html .= $link->get_page_links_html($parameters);
    }
    return $html;
}

function phbbs_get_form_html($thread){
    $html = modules\space_br('<div class="phbbs_form_box">', 1);
    $html .= modules\space_br('<form action="' . PHBBS_PATH . 'bbs/post.php" method="post">', 2);
    $html .= modules\space_br('<div class="phbbs_form">', 3);
    $html .= modules\space_br('<label>', 4);
    $html .= modules\space_br('<span class="phbbs_form">名前：</span>', 5);
    $html .= modules\space_br('</label><br>', 4);
    $html .= modules\space_br('<input class="phbbs_comment" type="text" name="name">', 5);
    $html .= modules\space_br('</div>', 3);
    $html .= modules\space_br('<div class="phbbs_form">', 3);
    $html .= modules\space_br('<label>', 4);
    $html .= modules\space_br('<span class="phbbs_form">内容：</span>', 5);
    $html .= modules\space_br('</label><br>', 4);
    $html .= modules\space_br('<textarea class="phbbs_comment" name="text"></textarea>', 5);
    $html .= modules\space_br('</div>', 3);
    $html .= pia\pia_get_html(false, 0);
    $html .= modules\space_br('<input type="hidden" name="thread_name" value="' . $thread . '">', 3);
    $html .= modules\space_br('<div class="phbbs_form"><button class="submit">投稿する</button></div>', 3);
    $html .= modules\space_br('</form>', 2);
    $html .= modules\space_br('</div>', 1);
    return $html;
}

function phbbs_get_html($str){
    $thread = $str ?? PHBBS_DEFAULT_THREAD;
    $html = phbbs_get_form_html($thread);
    $html .= phbbs_get_comments_html($thread);
    $html .= modules\space_br('<script src="' . PHBBS_PIA_PATH . 'main.js"></script>', 1);
    return $html;
}