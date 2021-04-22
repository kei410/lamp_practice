<?php
// 「定数ファイル、汎用関数ファイル、ユーザーデータ、商品データ、カートデータ」5つ

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

// セッションスタート (必ず最初に記述する)
session_start();

// ログインされていなければ、ログインページへ遷移する
if(is_logined() === false){
  redirect_to(LOGIN_URL);
}

// PDOを取得して、データベース接続
$db = get_db_connect();

// PDOを利用してログインしているユーザーのデータを取得
$user = get_login_user($db);

// PDOとユーザーIDを利用して、ユーザーのカートデータを取得
$carts = get_user_carts($db, $user['user_id']);

// 購入処理を実行して、購入に失敗したらカートページに移動する
if(purchase_carts($db, $carts) === false){
  set_error('商品が購入できませんでした。');
  redirect_to(CART_URL);
} 

// カート内の合計金額を定義する
$total_price = sum_carts($carts);

// 購入完了のviewを読み込む
include_once '../view/finish_view.php';