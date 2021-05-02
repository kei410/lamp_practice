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

require_once MODEL_PATH . 'histories.php';

// セッションスタート (最初に記述する)
session_start();

// controllerに追加する
$token = get_csrf_token();

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

// ランキング表示
$ranking = get_ranking($db); 

// viewの読み込み
include_once VIEW_PATH . 'index_view.php';