<?php
// 自動販売機の定数ファイル

define('TAX', 1.08);  // 消費税

// データベースの接続情報
define('DB_USER',  'testuser');      // MySQLのユーザ名
define('DB_PASSWD', 'password');    // MySQLのパスワード
define('DB_NAME', 'sample'); 
define('DNS', 'mysql:dbname='.DB_NAME.';host=mysql;charset=utf8'); 

define('HTML_CHARACTER_SET', 'UTF-8');  // HTML文字エンコーディング
define('DB_CHARACTER_SET',  'UTF8');  // DB文字エンコーディング

/* define('IMG_DIR', './img/');  // 画像ファイル保存ディレクトリ */

define('IMG_DIR', './assets/images/');  // 画像ファイル保存ディレクトリ
