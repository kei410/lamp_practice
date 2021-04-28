<?php
// 「定数ファイル、汎用関数ファイル、ユーザーデータ、商品データ、カートデータ」の5つ

// 定数ファイルを読み込む
require_once '../conf/const.php';
// 汎用関数ファイルを読み込む
require_once MODEL_PATH . 'functions.php';
// userデータに関する関数ファイルを読み込む
require_once MODEL_PATH . 'user.php';
// itemデータに関する関数ファイルを読み込む
require_once MODEL_PATH . 'item.php';
// cartデータに関する関数ファイルを読み込む
require_once MODEL_PATH . 'cart.php';

// セッションスタート
session_start();

// もしログインされていなければ、
if(is_logined() === false){
  // ログインページに移動する
  redirect_to(LOGIN_URL);
}

// PDOを取得して、データベースに接続する
$db = get_db_connect();

// PDOを利用して、ログインユーザーのデータを取得する
$user = get_login_user($db);

// postで受け取ったもの（カートIDと数量）を定義する
$cart_id = get_post('cart_id');
$amount = get_post('amount');

// カートの商品の数量を更新する処理を実行して、
// 更新できた場合はメッセージを表示して、
// 更新できなかった場合はエラーメッセージを表示する
if(update_cart_amount($db, $cart_id, $amount)){
  set_message('購入数を更新しました。');
} else {
  set_error('購入数の更新に失敗しました。');
}
// カートページに移動する
redirect_to(CART_URL);


if (is_valid_csrf_token($token) === false || get_request_method() !== 'POST') {
  set_error('不正なリクエストです。');
  redirect_to(LOGIN_URL);
} 
