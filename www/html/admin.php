<?php
// 「定数ファイル、汎用関数ファイル、ユーザーデータ、商品データ」

// すでに読み込まれている場合はそのファイルを読み込まない．
// 読み込みエラー時はFatalエラーとなり処理が中断する（required_onceの場合）

// 定数ファイルを読み込む
require_once '../conf/const.php';
// 汎用関数ファイルを読み込む
require_once MODEL_PATH . 'functions.php';
// userデータに関する関数ファイルを読み込む
require_once MODEL_PATH . 'user.php';
// itemデータに関する関数ファイルを読み込む
require_once MODEL_PATH . 'item.php';

/* header('X-FRAME-OPTIONS: DENY'); */
// セッションを使うので、session_start関数を記述する
// (セッションはページをまたいで情報を共有できる仕組み)
session_start();

$token = get_csrf_token();

/* $csrf_token = get_post('csrf_token'); */

// もしログインしていなければ、
if(is_logined() === false){
// ログインページに移動する
  redirect_to(LOGIN_URL);
}

// PDOを取得してデータベースに接続
$db = get_db_connect();

// PDOを利用して、ログインユーザーのデータを取得する
$user = get_login_user($db);

// もしログインしているユーザーが管理者(admin、admin)でなければ、
if(is_admin($user) === false){
// ログインページに移動する
  redirect_to(LOGIN_URL);
}

// 変数itemsは変数dbを受け取ったget_all_items関数を呼び出す
// PDOを利用して、商品一覧情報を取得する
$items = get_all_items($db);

// admin.phpはPOSTされてこないので以下の処理は不要
/* if (is_valid_csrf_token($token) === false || get_request_method() !== 'POST') {
  set_error('不正なリクエストです。');
  redirect_to(LOGIN_URL);
} 

 */

// ファイルからのコードが既に読み込まれている場合は，再度読み込まれない
// 読み込みエラー時は警告だけで処理が続行する (include_once)
// 管理画面のviewを読み込む
include_once VIEW_PATH . '/admin_view.php';
