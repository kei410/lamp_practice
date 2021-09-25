<?php
// 「変わることのない情報」を定数でまとめています。
// データベースの接続情報
/* define('DB_HOST', 'mysql'); */
define('DB_USER',   'testuser');      // MySQLのユーザ名（マイページのアカウント情報を参照）
define('DB_PASSWD', 'password');    // MySQLのパスワード（マイページのアカウント情報を参
define('DB_NAME', 'sample'); // MySQLのDB名(このコースではMySQLのユーザ名と同じで
define('DB_CHARSET', 'SET NAMES utf8mb4');  // MySQLのcharset
define('DSN', 'mysql:dbname='.DB_NAME.';host=mysql;charset=utf8');  // データベースのDSN情報
// ホスト名にlocalhostを指定すると上手くいかないので注意する
define('TAX', 1.08);  // 消費税
 
define('HTML_CHARACTER_SET', 'UTF-8');  // HTML文字エンコーディング
?>

