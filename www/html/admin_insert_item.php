<?php
// 「定数ファイル、汎用関数ファイル、ユーザーデータ、商品データ」の4つ

// 定数ファイルを読み込む
require_once '../conf/const.php';
// 汎用関数ファイルを読み込み
require_once MODEL_PATH . 'functions.php';
// userデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'user.php';
// itemデータに関する関数ファイルを読み込み。
require_once MODEL_PATH . 'item.php';

// セッションスタート (セッション関数は最初に記述する)
session_start();


// セッション関数の直後に書く
if (is_valid_csrf_token(get_post('csrf_token')) === false || get_request_method() !== 'POST') {
  set_error('不正なリクエストです。');
  redirect_to(LOGIN_URL);
} 

// もしログインされていなければ、ログインページに移動する
if(is_logined() === false){
  redirect_to(LOGIN_URL);
}

// PDOを取得してデータベースに接続する
$db = get_db_connect();

// PDOを利用して、ログインしているユーザーのデータを取得する
$user = get_login_user($db);

// もしユーザーが管理者でないなら、
// ログインページに移動する
if(is_admin($user) === false){
  redirect_to(LOGIN_URL);
}

// postで受け取ったものをそれぞれ以下のように定義する
// ユーザー名、商品名、価格、ステータス、在庫数の5つ
$name = get_post('name');
$name = get_post('name');
$price = get_post('price');
$status = get_post('status');
$stock = get_post('stock');

// 画像データを取得する
$image = get_file('image');

// PDO、商品名、価格、在庫数、ステータス、画像を受け取り、商品管理画面に移動する
// 登録できたらset_message()関数を実行してメッセージを表示する
// 登録できなければset_error()関数を実行してエラーメッセージを表示する
if(regist_item($db, $name, $price, $stock, $status, $image)){
  set_message('商品を登録しました。');
}else {
  set_error('商品の登録に失敗しました。');
}

redirect_to(ADMIN_URL);
