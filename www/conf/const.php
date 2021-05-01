<?php

// データベースなどの設定情報をまとめて１つのファイルで定義する
// const.phpに「変わることのない情報」を定数としてまとめている

// X-Frame-Options は HTTP のレスポンスヘッダーで、ブラウザーがページを <frame> , <iframe> , <embed> , <object> の中に
// 表示することを許可するかどうかを示すために使用される
header('X-FRAME-OPTIONS: DENY');

// ディレクトリパス
define('MODEL_PATH', $_SERVER['DOCUMENT_ROOT'] . '/../model/');
define('VIEW_PATH', $_SERVER['DOCUMENT_ROOT'] . '/../view/');

// 画像関連
define('IMAGE_PATH', '/assets/images/');
define('STYLESHEET_PATH', '/assets/css/');
define('IMAGE_DIR', $_SERVER['DOCUMENT_ROOT'] . '/assets/images/' );

// MySQL接続用
define('DB_HOST', 'mysql');
define('DB_NAME', 'sample');
define('DB_USER', 'testuser');
define('DB_PASS', 'password');
define('DB_CHARSET', 'utf8');

// URL
define('SIGNUP_URL', '/signup.php');
define('LOGIN_URL', '/login.php');
define('LOGOUT_URL', '/logout.php');
define('HOME_URL', '/index.php');
define('CART_URL', '/cart.php');
define('FINISH_URL', '/finish.php');
define('ADMIN_URL', '/admin.php');
define('HISTORY_URL', '/order_history.php');
define('DETAIL_URL', 'order_detail.php');

// 正規表現
define('REGEXP_ALPHANUMERIC', '/\A[0-9a-zA-Z]+\z/');
// 正の整数 正規表現
define('REGEXP_POSITIVE_INTEGER', '/\A([1-9][0-9]*|0)\z/');

// 会員登録の際の文字数制限 (ユーザー名、パスワード)
define('USER_NAME_LENGTH_MIN', 6);
define('USER_NAME_LENGTH_MAX', 100);
define('USER_PASSWORD_LENGTH_MIN', 6);
define('USER_PASSWORD_LENGTH_MAX', 100);

// ユーザーのタイプ
define('USER_TYPE_ADMIN', 1);
define('USER_TYPE_NORMAL', 2);

// 商品名の文字数制限
define('ITEM_NAME_LENGTH_MIN', 1);
define('ITEM_NAME_LENGTH_MAX', 100);

// 商品ステータス(公開、非公開)
define('ITEM_STATUS_OPEN', 1);
define('ITEM_STATUS_CLOSE', 0);

// 商品ステータスが公開なら1、非公開なら0とする
define('PERMITTED_ITEM_STATUSES', array(
  'open' => 1,
  'close' => 0,
));

// 拡張子を定義する (jpgかpng形式)
define('PERMITTED_IMAGE_TYPES', array(
  IMAGETYPE_JPEG => 'jpg',
  IMAGETYPE_PNG => 'png',
));