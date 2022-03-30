<?php

namespace php_hp_bbs;

ini_set('display_errors', 1);
define('PHBBS_SITE_NAME', "PHP HP BBS 2");
define('PHBBS_PATH', "");
define('PHBBS_DEFAULT_THREAD', "test"); // スレッド名の指定がない時に表示されるデフォルトスレッド
define('PHBBS_MULTH_PAGE_MODE', false); // 複数のスレッドを使用するか
define('PHBBS_AVAILABLE', false); // 投稿許可（false で投稿を一時的に制限）
//define('PHBBS_REPLY_MODE', false); // true でリプライ先のコメントの直下に返事を表示
define('PHBBS_MAX_COMMENTS', 10); // 1ページあたりのコメント表示数
define("PHBBS_PIA_PATH", 'plugins/php_img_auth/'); // php_img_auth
define("PHBBS_PNLG_PATH", '../common_modules/php_number_link_generator_2/'); // php_number_link_generator
define("PHBBS_HCM_PATH", '../common_modules/html_common_module.php'); // html_common_module.php
