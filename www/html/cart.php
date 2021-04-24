<?php
// 「定数ファイル、汎用関数ファイル、ユーザーデータ、商品データ、カートデータ」の5つ

// 定数ファイルを読み込む
require_once '../conf/const.php';
// 汎用関数ファイルを読み込み
require_once MODEL_PATH . 'functions.php';
// userデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'user.php';
// itemデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'item.php';
// cartデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'cart.php';

// セッションスタート (これは必ず最初に書く)
session_start();

// もしログインされていなければ、
if(is_logined() === false){
  // ログインページに移動する
  redirect_to(LOGIN_URL);
}

// PDOを取得してデータベースに接続する
$db = get_db_connect();

// PDOを利用してログインしているユーザーのデータを取得する
$user = get_login_user($db);

// PDOとユーザーIDを利用してユーザーのカートデータを取得する
$carts = get_user_carts($db, $user['user_id']);

// $carts(ユーザーのカート情報)を利用してカートに入っている商品額を合計する
$total_price = sum_carts($carts);

// カートのviewを読み込む
include_once VIEW_PATH . 'cart_view.php';