<?php
// 「定数ファイル、汎用関数ファイル、ユーザーデータ」

// 定数ファイルを読み込む
require_once '../conf/const.php';
// 汎用関数ファイルを読み込み
require_once MODEL_PATH . 'functions.php';
// userデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'user.php';

 // セッションスタート (必ず最初に記述する)
session_start();

// ログインされていれば、トップページに遷移する
if(is_logined() === true){
  redirect_to(HOME_URL);
}

// postで受け取ったものをそれぞれ定義する（ユーザー名とパスワードの2つ）
$name = get_post('name');
$password = get_post('password');

// PDOを取得してデータベースに接続する
$db = get_db_connect();

// ログイン処理
// もしログインに失敗したら、set_error()関数を実行してエラーメッセージを表示する
// ログインページに移動する
$user = login_as($db, $name, $password);
if( $user === false){
  set_error('ログインに失敗しました。');
  redirect_to(LOGIN_URL);
}

// ログインに成功したら、set_message()関数を実行してメッセージを表示して
// 会員トップページに遷移する
// もしユーザーが管理者でログイン(admin、admin)した場合は、商品管理ページに移動する
set_message('ログインしました。');
if ($user['type'] === USER_TYPE_ADMIN){
  redirect_to(ADMIN_URL);
}
redirect_to(HOME_URL);

if (is_valid_csrf_token($token) === false || get_request_method() !== 'POST') {
  set_error('不正なリクエストです。');
  redirect_to(LOGIN_URL);
} 