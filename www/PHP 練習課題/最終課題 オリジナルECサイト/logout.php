<?php
// ログアウト画面

session_start();    //ログアウトでもセッション変数にアクセスするので、session_start関数を呼び出す。

// セッション名を取得
$session_name = session_name();

// $_SESSIONは連想配列なのでセッション変数を全て削除
$_SESSION = [];

// ユーザーのCookieに保存されているセッションIDを削除
if (isset($_COOKIE[$session_name])) {
    setcookie($session_name, '', time() - 42000);
}

// セッションに関連付けられたデータを破棄する。
session_destroy();

// loginページへ移動する
header("location: ./top.php");
exit();

// 「商品一覧ページ」など商品を購入するユーザ側ページからログアウトできる。
//  ログアウトした場合はログインページに遷移する。
?>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>ログアウト</title>
    </head>
    <body>
    </body>
</html>

