<?php
//  「定数ファイル、汎用関数ファイル、ユーザーデータ」

// 定数ファイルを読み込む
require_once '../conf/const.php';
// 汎用関数ファイルを読み込む
require_once MODEL_PATH . 'functions.php';
// ユーザーデータに関するファイルを読み込む
require_once MODEL_PATH . 'user.php';

// セッション開始 (最初に書いておく)
session_start();

if (is_valid_csrf_token($token) === false || get_request_method() !== 'POST') {
  set_error('不正なリクエストです。');
  redirect_to(LOGIN_URL);
} 

// もしログインされているなら、会員トップページに移動する
if(is_logined() === true){
  redirect_to(HOME_URL);
}

// postで受け取ったものを定義する (ユーザー名とパスワード)
$name = get_post('name');
$password = get_post('password');
// パスワードの確認をする
$password_confirmation = get_post('password_confirmation');

// PDOを取得して、データベースに接続する
$db = get_db_connect();


// 新規ユーザー登録処理を行う
// 会員登録に失敗した場合は会員登録ページに遷移する
try{
  $result = regist_user($db, $name, $password, $password_confirmation);
  if( $result=== false){
    set_error('ユーザー登録に失敗しました。');
    redirect_to(SIGNUP_URL);
  }
}catch(PDOException $e){
  set_error('ユーザー登録に失敗しました。');
  redirect_to(SIGNUP_URL);
}

// ユーザー登録完了後、そのままログインして会員トップページへ遷移する
set_message('ユーザー登録が完了しました。');
login_as($db, $name, $password);
redirect_to(HOME_URL);
