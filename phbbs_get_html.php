<?php

namespace php_hp_bbs;

//use php_img_auth\modules as modules;
use php_hp_bbs\bbs\classes\GetComment;
use php_hp_bbs\bbs\classes\State;
use php_hp_bbs\bbs\modules as modules;
use php_img_auth as pia;
use common_modules as cm;

use php_number_link_generator\classes\NumberLink;

require_once "init.php";
require_once "modules/main.php";
require_once "bbs/classes/GetComment.php";
require_once "bbs/classes/State.php";
require_once PHBBS_HCM_PATH;
require_once PHBBS_PIA_PATH . "init.php";
require_once PHBBS_PIA_PATH . "pia_get_html.php";

require_once( PHBBS_PNLG_PATH . 'init.php');
require_once( PHBBS_PNLG_PATH . 'classes/NumberLink.php');

//function get_list($thread){
//    $txt = PHBBS_PATH . "bbs/lists/" . $thread . ".log";
//    if(file_exists($txt)){
//        return file($txt);
//    } else {
//        echo "NOT FOUND: " . $txt;
//        return null;
//    }
//}

function get_comments($list, $thread){
    $array = [];
    foreach ($list as $line){
        array_push($array, new GetComment($line, $thread));
    }
    return $array;
}

function phbbs_get_comments_html($thread, $state){
    $list = modules\get_list($thread);
    $comments = get_comments($list, $thread);
    $comments_num = count($comments);
    $link = new NumberLink($comments_num, PHBBS_MAX_COMMENTS);
    rsort($comments); // reverse the order of the list
    $html = "";
//    $state = new State();
    $start_comment = ($state->page - 1) * PHBBS_MAX_COMMENTS;
    $end_comment = $state->page * PHBBS_MAX_COMMENTS;
    for($i = $start_comment; $i < $end_comment; $i++){
        if(isset($comments[$i])){
//            $html .= get_comment($comments[$i]);
            $html .= $comments[$i]->get_comment(true);
        }
    }
    if(PHBBS_MAX_COMMENTS < $comments_num){
        $parameters = "";
        $html .= $link->get_page_links_html($parameters);
    }
    return $html;
}

function get_id_edit_or_delete($state){
    if($state->edit !== null){
        return $state->edit;
    } else if($state->delete !== null){
        return $state->delete;
    } else {
        return null;
    }
}

function get_comment_to_edit_delete($thread, $state){
    $id = get_id_edit_or_delete($state);
    if($id !== null){
        $list = get_list($thread);
        $comments = get_comments($list, $thread);
//        var_dump($comments);
        foreach ($comments as $comment){
            if($comment->id === $id){
                return $comment;
            }
        }
        header('Location: bbs/error.php?code=7');
        exit;
    }
    return null;
}

function get_word_in_button($state){
    if($state->edit !== null){
        return "変更する";
    } else if($state->delete !== null){
        return "削除する";
    } else {
        return "投稿する";
    }
}

function phbbs_get_form_html($thread, $state){
    $comment = get_comment_to_edit_delete($thread, $state);
    $button_word = get_word_in_button($state);
//    var_dump($comment);
    $name = $comment === null ? "" : $comment->get_name_full();
    $text = $comment === null ? "" : implode("\n", $comment->text);
    $html = "";
    $html .= cm\space_br('<div class="phbbs_form_box">', 1);
    $html .= cm\space_br('<form action="' . PHBBS_PATH . 'bbs/post.php" method="post">', 2);

    if($state->delete !== null && $comment !== null){
        $html .= $comment->get_comment(false);
    } else {
        $html .= cm\space_br('<div class="phbbs_form">', 3);
        $html .= cm\space_br('<label>', 4);
        $html .= cm\space_br('<span class="phbbs_form">名前：</span>', 5);
        $html .= cm\space_br('</label>', 4);
        $html .= cm\space_br('<input class="phbbs_comment" type="text" name="name" value="' . $name . '">', 5);
        $html .= cm\space_br('</div>', 3);
        $html .= cm\space_br('<div class="phbbs_form">', 3);
        $html .= cm\space_br('<label>', 4);
        $html .= cm\space_br('<span class="phbbs_form">内容：</span>', 5);
        $html .= cm\space_br('</label>', 4);
        $html .= cm\space_br('<textarea class="phbbs_comment" name="text">' . $text. '</textarea>', 5);
        $html .= cm\space_br('</div>', 3);
    }

    $html .= cm\space_br('<div class="phbbs_form">', 3);
    $html .= cm\space_br('<label>', 4);
    $html .= cm\space_br('<span class="phbbs_form">パスワード：</span>', 5);
    $html .= cm\space_br('</label>', 4);
    $html .= cm\space_br('<input class="phbbs_password" type="password" name="password">', 5);
    $html .= cm\space_br('<p class="password">※パスワードを設定しておくと、後から削除できるようになります。</p>', 5);
    $html .= cm\space_br('</div>', 3);
    if(PHBBS_AUTH){
        $html .= pia\pia_get_html(false, 0);
    }
    $html .= cm\space_br('<input type="hidden" name="thread_name" value="' . $thread . '">', 3);
    $html .= cm\space_br('<div class="phbbs_form"><button class="submit">' . $button_word . '</button></div>', 3);
    $html .= cm\space_br('</form>', 2);
    $html .= cm\space_br('</div>', 1);
    return $html;
}

function get_description($state){
    $html = "";
    if($state->edit === null){
        if($state->delete === null){
            $html .= cm\space_br("<p>作品の感想、アプリやゲームのバグ報告等あったらください。</p>", 3);
            $html .= cm\space_br("<p>名前の後に #（半角シャープ）を追加し、任意の文字列を入れると、</p>", 3);
            $html .= cm\space_br("<p>なりすまし防止用の暗号キーが追加されます（2ch でいう「トリ」です）。</p>", 3);
        } else {
            $html .= cm\space_br("<p>記事を削除するには、パスワードを入力してください。</p>", 3);
            $html .= cm\space_br("<p>なお、一度削除したコメントは復元できません。</p>", 3);
        }
    } else {
        $html .= cm\space_br("<p>内容を変更し、パスワードを入力してください。</p>", 3);
    }
    return $html;
}

function phbbs_get_html($str){
    $state = new State();
    $thread = $str ?? PHBBS_DEFAULT_THREAD;
    $html = get_description($state);
    $html .= phbbs_get_form_html($thread, $state);
    if($state->edit === null && $state->delete === null){
        $html .= phbbs_get_comments_html($thread, $state);
    }
    $html .= cm\space_br('<script src="' . PHBBS_PIA_PATH . 'main.js"></script>', 1);
    return $html;
}