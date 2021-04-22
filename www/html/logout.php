<?php
// 定数ファイルを読み込む
require_once '../conf/const.php';
// 汎用関数ファイルを読み込む
require_once MODEL_PATH . 'functions.php';

// ログアウトでもセッションを利用するので、セッション関数を記述
session_start();

// セッション変数を全て解除する
$_SESSION = array();

 // 変数$paramsにセッションクッキーのパラメータが格納された配列を取得する関数 session_get_cookie_params() を代入する
$params = session_get_cookie_params();

// setcookie() はクッキーを設定(送信)する関数
// setcookie() の引数の設定

// 第一引数 session_name()  セッションIDを設定する引数。破棄するので何も指定しない
// 第二引数 '', セッションIDの値を設定する引数。破棄するので空の文字列になっている
// 第三引数 クッキーの有効期限を指定する引数。関数 time() で指定
// ここではクッキーを無効にするために負の値を指定している
// 値が-42000である事には特に重要ではなく、負の値である事が重要
// 第四引数 セッションのパラメータを指定する引数。
// ここでは変数$paramsに代入された関数 session_get_cookie_params() の配列からそれぞれを呼び出している
// path クッキーが保存されている場所のパス
// domain クッキーが有効になるドメイン
// secure セキュアな接続でのみクッキーを送信する
// httpsonly  HTTPSを通してのみアクセスを可能にする


setcookie(session_name(), '', time() - 42000,
  $params["path"], 
  $params["domain"],
  $params["secure"], 
  $params["httponly"]
);
// 最終的にセッションを破壊する
session_destroy();
// ログアウトしたらログイン画面へ移動する
redirect_to(LOGIN_URL);

