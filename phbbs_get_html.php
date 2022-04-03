<?php

namespace php_hp_bbs;

use php_hp_bbs\bbs\classes\GetComment;
use php_hp_bbs\bbs\classes\State;
use php_hp_bbs\bbs\modules as modules;
use php_img_auth as pia;
use fp_common_modules as cm;

use php_number_link_generator\classes\NumberLink;

require_once "init.php";
require_once "bbs/modules/main.php";
require_once "bbs/classes/Comment.php";
require_once "bbs/classes/GetComment.php";
require_once "bbs/classes/State.php";
require_once PHBBS_HCM_PATH;
//require_once PHBBS_PIA_PATH . "pia_init.php";
require_once PHBBS_PIA_PATH . "pia_get_html.php";

require_once PHBBS_PNLG_PATH . 'init.php';
require_once PHBBS_PNLG_PATH . 'classes/NumberLink.php';

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
    $start_comment = ($state->page - 1) * PHBBS_MAX_COMMENTS;
    $end_comment = $state->page * PHBBS_MAX_COMMENTS;
    for($i = $start_comment; $i < $end_comment; $i++){
        if(isset($comments[$i])){
            $html .= $comments[$i]->get_comment(true);
        }
    }
    if(PHBBS_MAX_COMMENTS < $comments_num){
        $parameters = "";
//        $html .= cm\space_br('<p class="number_link">', )
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
        $list = modules\get_list($thread);
        $comments = get_comments($list, $thread);
        foreach ($comments as $comment){
            if($comment->id === $id){
                return $comment;
            }
        }
//        header('Location: bbs/error.php?code=7');
//        $symbol = strpos("?", PHBBS_INDEX) === false ? "?" : "&";
        header('Location: ' . modules\get_index_and_code() . '407');
        exit;
    }
    return null;
}

function get_word_in_button($state){
    if($state->edit !== null){
        return $state->lang === 1 ? "Change" : "変更する";
    } else if($state->delete !== null){
        return $state->lang === 1 ? "Delete" : "削除する";
    } else {
        return $state->lang === 1 ? "Submit" : "投稿する";
    }
}

function get_link_to($state){
    if($state->edit !== null){
        return "edit.php" . modules\get_url_parameter() . "&edit=" . $state->edit;
    } else if($state->delete !== null){
        return "delete.php" . modules\get_url_parameter() . "&delete=" . $state->delete;
    } else {
        return "post.php" . modules\get_url_parameter();
    }
}

function get_about_password($state){
    if($state->lang === 1){
        return "If register the password, you can delete the comment later.";
    } else {
        return "※パスワードを設定しておくと、後から削除できるようになります。";
    }
}

function phbbs_get_form_html($thread, $state){
    $comment = get_comment_to_edit_delete($thread, $state);
    $button_word = get_word_in_button($state);
    $link_to = get_link_to($state);
    $name = $comment === null ? "" : $comment->get_name_full();
    $text = $comment === null ? "" : implode("\n", $comment->text);
    $html = "";
    $html .= cm\space_br('<div class="phbbs_form_box">', 1);
    $html .= cm\space_br('<form action="' . PHBBS_HTTP_PATH . 'bbs/' . $link_to . '" method="post">', 2);

    if($state->delete !== null && $comment !== null){
        $html .= $comment->get_comment(false);
    } else {
        $html .= cm\space_br('<div class="phbbs_form">', 3);
        $html .= cm\space_br('<label>', 4);
        $html .= cm\space_br('<span class="phbbs_form">' . ($state->lang === 1 ? "Name" : "名前") . '：</span>', 5);
        $html .= cm\space_br('</label>', 4);
        $html .= cm\space_br('<input class="phbbs_comment" type="text" name="name" value="' . $name . '">', 5);
        $html .= cm\space_br('</div>', 3);
        $html .= cm\space_br('<div class="phbbs_form">', 3);
        $html .= cm\space_br('<label>', 4);
        $html .= cm\space_br('<span class="phbbs_form">' . ($state->lang === 1 ? "Comment" : "内容") . '：</span>', 5);
        $html .= cm\space_br('</label>', 4);
        $html .= cm\space_br('<textarea class="phbbs_comment" name="text">' . $text. '</textarea>', 5);
        $html .= cm\space_br('</div>', 3);
    }

    $html .= cm\space_br('<div class="phbbs_form">', 3);
    $html .= cm\space_br('<label>', 4);
    $html .= cm\space_br('<span class="phbbs_form">' . ($state->lang === 1 ? "Password" : "パスワード") . '：</span>', 5);
    $html .= cm\space_br('</label>', 4);
    $html .= cm\space_br('<input class="phbbs_password" type="password" name="password">', 5);
    if($state->edit === null && $state->delete === null){
        $html .= cm\space_br('<p class="password">' . get_about_password($state) . '</p>', 5);
    }
    $html .= cm\space_br('</div>', 3);
    if(PHBBS_AUTH && $state->edit === null && $state->delete === null){
        $html .= pia\pia_get_html(false, $state->lang, PHBBS_PIA_PATH);
    }
    $html .= cm\space_br('<input type="hidden" name="thread_name" value="' . $thread . '">', 3);
    $html .= cm\space_br('<div class="phbbs_form"><button class="phbbs_submit">' . $button_word . '</button></div>', 3);
    $html .= cm\space_br('</form>', 2);
    $html .= cm\space_br('</div>', 1);
    return $html;
}

function get_description($state){
    $html = "";
    if($state->lang === 1){
        if($state->edit === null){
            if($state->delete === null){
                $html .= cm\space_br("<p>Please post any report of any bug and review about my arts and products.</p>", 3);
                $html .= cm\space_br("<p>If you want to prevent someone pretends to be you, type some word after '#'.</p>", 3);
                $html .= cm\space_br("<p>It will turn into the specific alphanumerical that proves your identity.</p>", 3);
            } else {
                $html .= cm\space_br("<p>Type the password you posted.</p>", 3);
                $html .= cm\space_br("<p>But a comment you delete cannot restore never agarin.</p>", 3);
            }
        } else {
            $html .= cm\space_br("<p>Edit your name and comment then type the password you posted.</p>", 3);
        }
    } else {
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
    }

    return $html;
}

function get_msg_jp($code){
    switch ($code){
        case 200: return "コメントの投稿に成功しました！";
        case 201: return "コメントの削除に成功しました。";
        case 401: return "投稿者名は 50 文字以内に収めてください。";
        case 402: return "投稿できる文字数は最大で 2000 文字までです。";
        case 403: return "認証用の英数字が正しくありません。";
        case 404: return "ただいま投稿が一時的に制限されています。";
        case 405: return "パスワードは半角英数字のみで入力してください。";
        case 406: return "パスワードは半角英数字で 8 文字以上、大文字と小文字を両方使って入力してください。";
        case 407: return "コメントファイルの読み込みに失敗しました。";
        case 408: return "コメントファイルを読み込みましたが、データが見つかりませんでした。";
        case 409: return "パスワードが間違っています。";
        case 410: return "URL パラメータの値が不正です。";
        default: return "不明なエラーが発生しました！";
    }
}

function get_msg_en($code){
    switch ($code){
        case 200: return "Posted your comment successfully.";
        case 201: return "Deleted your comment successfully.";
        case 401: return "Cannot post a text over 50 characters.";
        case 402: return "Cannot post a text over 2000 characters.";
        case 403: return "Authentication failed because the code you typed was wrong.";
        case 404: return "Post failed because of the administrator's inconvenience.";
        case 405: return "Please type a password only half-width alphanumeric characters.";
        case 406: return "Password must contain at least one uppercase letter, one lowercase letter and a number.";
        case 407: return "Failed to load your comment.";
        case 408: return "Not found your comment in the log.";
        case 409: return "Password is wrong.";
        case 410: return "URL parameter is invalid.";
        default: return "Unknown error occurred!";
    }
}

function get_h1($state){
    if($state->code >= 400){
        return $state->lang === 1 ? "ERROR" : "エラー";
    } else if($state->code === 201){
        return $state->lang === 1 ? "Delete Succeeded" : "削除成功";
    } else if($state->code === 200){
        return $state->lang === 1 ? "Post Succeeded" : "投稿成功";
    }
}

function get_succeed_and_error_html($state){
    $msg = $state->lang === 1 ? get_msg_en($state->code) : get_msg_jp($state->code);
    $html = cm\space_br("<h2>" . get_h1($state) . ":</h2>", 1);
    $html .= cm\space_br("<p>" . $msg . "</p>", 1);
    $html .= cm\space_br('<p class="phbbs_link_to_index"><a href="' . PHBBS_INDEX . modules\get_url_parameter($state) . '">- ' . ($state->lang === 1 ? "BACK" : "戻る") . ' -</a></p>', 1);
    return $html;
}

function phbbs_get_html($str){
    $state = new State();
    if($state->code >= 200){
        return get_succeed_and_error_html($state);
    } else {
        $thread = $str ?? PHBBS_DEFAULT_THREAD;
        $html = get_description($state);
        $html .= phbbs_get_form_html($thread, $state);
        if($state->edit === null && $state->delete === null){
            $html .= phbbs_get_comments_html($thread, $state);
        }
        $html .= cm\space_br("<p class='phbbs_end_blank'>　</p>", 2);
        $html .= pia\pia_get_script_html(PHBBS_PIA_PATH);
//    $html .= cm\space_br('<script src="' . PHBBS_PIA_PATH . 'main.js"></script>', 1);
        return $html;
    }
}