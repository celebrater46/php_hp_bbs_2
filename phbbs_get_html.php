<?php

namespace php_hp_bbs;

use php_img_auth\modules as modules;
use php_hp_bbs\bbs\classes\GetComment;
use php_hp_bbs\bbs\classes\State;
use php_img_auth as pia;

use php_number_link_generator\classes\NumberLink;

require_once "init.php";
require_once "bbs/classes/GetComment.php";
require_once "bbs/classes/State.php";
require_once PHBBS_HCM_PATH;
require_once PHBBS_PIA_PATH . "init.php";
require_once PHBBS_PIA_PATH . "pia_get_html.php";

require_once( PHBBS_PNLG_PATH . 'init.php');
require_once( PHBBS_PNLG_PATH . 'classes/NumberLink.php');

function get_list($thread){
    $txt = PHBBS_PATH . "bbs/lists/" . $thread . ".log";
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
        array_push($array, new GetComment($line, $thread));
    }
    return $array;
}

function phbbs_get_comments_html($thread){
    $list = get_list($thread);
    $comments = get_comments($list, $thread);
    $comments_num = count($comments);
    $link = new NumberLink($comments_num, PHBBS_MAX_COMMENTS);
    rsort($comments); // reverse the order of the list
    $html = "";
    $state = new State();
    $start_comment = ($state->page - 1) * PHBBS_MAX_COMMENTS;
    $end_comment = $state->page * PHBBS_MAX_COMMENTS;
    for($i = $start_comment; $i < $end_comment; $i++){
        if(isset($comments[$i])){
//            $html .= get_comment($comments[$i]);
            $html .= $comments[$i]->get_comment();
        }
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
    $html .= modules\space_br('</label>', 4);
    $html .= modules\space_br('<input class="phbbs_comment" type="text" name="name">', 5);
    $html .= modules\space_br('</div>', 3);
    $html .= modules\space_br('<div class="phbbs_form">', 3);
    $html .= modules\space_br('<label>', 4);
    $html .= modules\space_br('<span class="phbbs_form">内容：</span>', 5);
    $html .= modules\space_br('</label>', 4);
    $html .= modules\space_br('<textarea class="phbbs_comment" name="text"></textarea>', 5);
    $html .= modules\space_br('</div>', 3);
    $html .= modules\space_br('<div class="phbbs_form">', 3);
    $html .= modules\space_br('<label>', 4);
    $html .= modules\space_br('<span class="phbbs_form">パスワード：</span>', 5);
    $html .= modules\space_br('</label>', 4);
    $html .= modules\space_br('<input class="phbbs_password" type="password" name="password">', 5);
    $html .= modules\space_br('<p class="password">※パスワードを設定しておくと、後から編集したり、削除できるようになります。</p>', 5);
    $html .= modules\space_br('</div>', 3);
    if(PHBBS_AUTH){
        $html .= pia\pia_get_html(false, 0);
    }
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