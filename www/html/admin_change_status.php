<?php
// 「定数ファイル、汎用関数ファイル、ユーザーデータ、商品データ」

// 定数ファイルを読み込む
require_once '../conf/const.php';
// 汎用関数ファイルを読み込む
require_once MODEL_PATH . 'functions.php';
// userのデータに関するファイルを読み込む
require_once MODEL_PATH . 'user.php';
// itemデータに関するファイルを読み込む
require_once MODEL_PATH . 'item.php';

// セッション開始
session_start();

// もしログインされていなければ、ログインページに移動する
if(is_logined() === false){
  redirect_to(LOGIN_URL);
}

// PDOを取得して、データベースに接続する
$db = get_db_connect();

// PDOを利用して、ログインしているユーザーのデータを取得
$user = get_login_user($db);

// ログインしているユーザーが管理者(admin、admin)でない場合は、
if(is_admin($user) === false){
  // ログインページに移動する
  redirect_to(LOGIN_URL);
}

// postで受け取ったもの(商品IDとステータス変更)を定義する
$item_id = get_post('item_id');
$changes_to = get_post('changes_to');

// PDO、商品ID、商品ステータスを受け取り、商品ステータスを変更して管理画面に移動する
// update_item_status()関数を実行してset_message()関数を実行する
if($changes_to === 'open'){
  update_item_status($db, $item_id, ITEM_STATUS_OPEN);
  set_message('ステータスを変更しました。');
}else if($changes_to === 'close'){
  update_item_status($db, $item_id, ITEM_STATUS_CLOSE);
  set_message('ステータスを変更しました。');
}else {
  set_error('不正なリクエストです。');
}


redirect_to(ADMIN_URL);