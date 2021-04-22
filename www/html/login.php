<?php
// 定数ファイルを読み込む
require_once '../conf/const.php';
// 汎用関数ファイルを読み込む
require_once MODEL_PATH . 'functions.php';

// セッションスタート (必ず最初に記述する)
session_start();

// もしログインされていれば、会員トップページに移動する
if(is_logined() === true){
  redirect_to(HOME_URL);
}

// ログイン画面のviewの読み込み。
include_once VIEW_PATH . 'login_view.php';