<?php
require_once '../conf/const.php';
require_once MODEL_PATH. 'functions.php';
require_once MODEL_PATH. 'user.php';
require_once MODEL_PATH. 'item.php';
require_once MODEL_PATH. 'cart.php';
require_once MODEL_PATH. 'histories.php';

session_start();

if(is_logined() === false){
  redirect_to(LOGIN_URL);
}


$db = get_db_connect();

$user = get_login_user($db);

// ログインユーザーが管理者の場合は履歴を全て表示して
// 一般ユーザーの場合にはユーザーの履歴のみを表示する
if(is_admin($user) === true){
  // 全件取得する
  $histories = get_history($db);
// ログインしているユーザーの履歴のみ表示する
}  else if (is_admin($user) === false) {
  $histories = get_user_history($db, $user['user_id']);
  
}

// PDOとユーザーIDを利用して、ユーザーの購入履歴を取得
/* $histories = get_history($db, $user['user_id']); */

$order_id = get_post('order_id');

include_once VIEW_PATH. 'history_view.php';