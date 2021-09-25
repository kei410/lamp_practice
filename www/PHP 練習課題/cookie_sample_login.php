<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <title>ログイン後</title>
  </head>
  <body>
<?php
$now = time();
if (isset($_POST['cookie_check'])) {
  $cookie_check = $_POST['cookie_check'];
} else {
  $cookie_check = '';
}
if (isset($_POST['user_name'])) {
  $cookie_value = $_POST['user_name'];
} else {
  $cookie_value = '';
}
// ユーザ名の入力を省略のチェックがONの場合、Cookieを利用する。OFFの場合、Cookieを削除する
if ($cookie_check === 'checked') {
  // Cookieへ保存する
  setcookie('cookie_check', $cookie_check, $now + 60 * 60 * 24 * 365);
  setcookie('user_name'   , $cookie_value, $now + 60 * 60 * 24 * 365);
} else {
  // Cookieを削除する
  setcookie('cookie_check', '', $now - 3600);
  setcookie('user_name'   , '', $now - 3600);
}
print 'ようこそ';
?>
  </body>
</html>


<!--
チェックボックスをチェックした状態で、ユーザ名を入力してログインボタンを実行すると、次回、cookie_sample_top.php(メモ帳のCookie使用例1)へアクセスした場合、
ユーザ名が自動的に入力された状態で表示されます。cookie_sample_top.php(メモ帳のCookie使用例1)は、Cookieの値を利用しています。

cookie_sample_login.php(Cookie使用例2)で、POSTで送信されたユーザ名を受け取ってCookieへ保存しています。このようにCookieへの保存と取り出しは、別のファイルから行うことが可能です。

Cookieへ保存する情報ですが、パスワードもCookieへ保存することは可能です。しかし、パスワードのような重要な情報は、セキュリティ上からCookieに保存しません。
-->


