<?php
// 「定数ファイル、汎用関数ファイル、ユーザーデータ、商品データ」の4つ

// 定数ファイルを読み込む
require_once '../conf/const.php';
// 汎用関数ファイルを読み込む
require_once MODEL_PATH . 'functions.php';
// userデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'user.php';
// itemデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'item.php';

// セッションスタート
session_start();

// もしログインされていなければ、
if(is_logined() === false){
  // ログインページに移動する
  redirect_to(LOGIN_URL);
}

// PDOを取得して、データベースに接続する
$db = get_db_connect();

// PDOを利用してログインユーザーのデータを取得
$user = get_login_user($db);

// ログインしているユーザーが管理者(admin、admin)でない場合は
if(is_admin($user) === false){
  // ログインページに移動する
  redirect_to(LOGIN_URL);
}

// postで受け取った商品IDと在庫数を以下のように定義する
$item_id = get_post('item_id');
$stock = get_post('stock');

// 在庫数を変更して、管理画面に移動する
if(update_item_stock($db, $item_id, $stock)){
  set_message('在庫数を変更しました。');
} else {
  set_error('在庫数の変更に失敗しました。');
}

redirect_to(ADMIN_URL);