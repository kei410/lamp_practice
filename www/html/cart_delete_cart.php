<?php
// カートの商品を削除する
// 「定数ファイル、汎用関数ファイル、ユーザーデータ、商品データ、カートデータ」の5つ

// 定数ファイルを読み込む
require_once '../conf/const.php';
// 汎用関数ファイルを読み込む
require_once MODEL_PATH . 'functions.php';
// userデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'user.php';
// itemデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'item.php';
// cartデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'cart.php';

// セッション開始 (最初に記述する)
session_start();

// もしログインされていなければ、
if(is_logined() === false){
  // ログインページに移動する
  redirect_to(LOGIN_URL);
}

// PDO1を取得して、データベース接続
$db = get_db_connect();

// PDOを利用してログインしているユーザーのデータを取得する
$user = get_login_user($db);

// postで受け取ったカートIDを以下のように定義する
$cart_id = get_post('cart_id');

// カート内の商品を削除する処理を実行して、
// 削除できた場合はメッセージを表示して、
// 削除できなかった場合はエラーメッセージを表示する
if(delete_cart($db, $cart_id)){
  set_message('カートを削除しました。');
} else {
  set_error('カートの削除に失敗しました。');
}
// カートページに移動する
redirect_to(CART_URL);