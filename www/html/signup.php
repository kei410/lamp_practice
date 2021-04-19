<?php
// 定数ファイルを読み込む
require_once '../conf/const.php';
// 汎用関数ファイルを読み込む
require_once MODEL_PATH . 'functions.php';

// セッション機能を開始するためセッション関数を記述
session_start();

// もしログインされていれば、
if(is_logined() === true){
  // 会員トップページに移動する
  redirect_to(HOME_URL);
}

// 会員登録のviewを読み込む
include_once VIEW_PATH . 'signup_view.php';



