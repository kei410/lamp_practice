<?php
// 「定数ファイル、汎用関数ファイル、ユーザーデータ、商品データ」

// 定数ファイルを読み込む
require_once '../conf/const.php';
// 汎用関数ファイルを読み込み
require_once MODEL_PATH . 'functions.php';
// userデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'user.php';
// itemデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'item.php';

// セッションスタート (最初に記述する)
session_start();

// ログインされていなければ、ログインページに移動する
if(is_logined() === false){
  redirect_to(LOGIN_URL);
}

// PDOを取得してデータベースに接続する
$db = get_db_connect();

// PDOを利用して、ログインユーザーのデータを取得する
$user = get_login_user($db);

// PDOを利用して、公開ステータスの商品情報を取得する
$items = get_open_items($db);

// viewの読み込み
include_once VIEW_PATH . 'index_view.php';