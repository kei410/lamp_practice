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

// セッションを利用するのでセッション関数を記述する
session_start();

// もしログインされていなければ、
if(is_logined() === false){
  // ログインページに移動する
  redirect_to(LOGIN_URL);
}

// PDOを取得して、データベースに接続する
$db = get_db_connect();

// PDOを利用して、ログインしているユーザーのデータを取得
$user = get_login_user($db);

// ログインしているユーザーが管理者(admin、admin)でない場合は
if(is_admin($user) === false){
  // ログインページに移動する
  redirect_to(LOGIN_URL);
}

// postで受け取った商品IDを定義する
$item_id = get_post('item_id');

// 商品を削除して管理画面に移動する
if(destroy_item($db, $item_id) === true){
  set_message('商品を削除しました。');
} else {
  set_error('商品削除に失敗しました。');
}



redirect_to(ADMIN_URL);