<?php

namespace php_hp_bbs\bbs\classes;

class Comment
{
    public $id;
    public $thread;
    protected $reply; // ID 何番への返信か
    protected $date; // 2022-01-24_00:00:00
    protected $date_string; // 2022-01-24_00:00:00
    protected $date_unix; // unix time stamp
    public $user; // 投稿者名
    protected $name_full; // 投稿者名（トリ含む）
    protected $title;
    public $text = []; // コメント内容
    protected $cap; // 2ch とかのなりすまし対策のトリップ
    protected $hp; // 投稿者のホームページの URL
    protected $mail; // 投稿者のメールアドレス
    protected $ip; // 投稿者の IP アドレス
    protected $password; // コメント編集ないし削除用
}