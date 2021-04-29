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

// セッション開始
session_start();

if (is_valid_csrf_token(get_post('csrf_token')) === false || get_request_method() !== 'POST') {
  set_error('不正なリクエストです。');
  redirect_to(LOGIN_URL);
} 

// もしログインされていなければ、ログインページに移動する
if(is_logined() === false){
  redirect_to(LOGIN_URL);
}

// PDOを取得して、データベースに接続
$db = get_db_connect();

// PDOを利用して、ログインしているユーザーの情報を取得する
$user = get_login_user($db);

// postで受け取った商品IDを以下のように定義する
$item_id = get_post('item_id');

// 商品をカートに追加して、会員トップページに移動する
if(add_cart($db,$user['user_id'], $item_id)){
  set_message('カートに商品を追加しました。');
} else {
  set_error('カートの更新に失敗しました。');
}

redirect_to(HOME_URL);


