<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <title>Cookie</title>
  </head>
  <body>
    <?php
    // cookieが設定されていなければ(初回アクセス)、cookieを設定する
    if ( !isset($_COOKIE['visit_count']) ) {
      // cookieを設定
      setcookie('visit_count', 1);
      print("訪問回数は1回<br>");
    }
    // cookieがすでに設定されていれば(2回目以降のアクセス)、cookieで設定した数値を加算する
    else {
      $count = $_COOKIE['visit_count'] + 1;
      setcookie('visit_count', $count);
      print("訪問回数は".$count."回<br>");
    }
    ?>
  </body>
</html>

<!--
この例では、phpからCookieに訪問回数を設定しています。最初は1を設定し、次に訪問した際には、
Cookieに設定したvisit_countを1加算しています。
この仕組みによりアクセスする度に訪問回数を増やす機能を実現しています。
-->



<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <title>Cookie</title>
  </head>
  <body>
    <?php
    // cookieが設定されていなければ(初回アクセス)、cookieを設定する
    if ( !isset($_COOKIE['visit_count']) ) {
      // cookieを設定
      setcookie('visit_count', 1, time() + 3600);
      print("訪問回数は1回<br>");
    }
    // cookieがすでに設定されていれば(2回目以降のアクセス)、cookieで設定した数値を加算する
    else {
      $count = $_COOKIE['visit_count'] + 1;
      setcookie('visit_count', $count, time() + 3600);
      print("訪問回数は".$count."回<br>");
    }
    ?>
  </body>
</html>

<!--time( )は現在時刻を返す関数です。現在時刻に(60秒×60=1時間)の秒数を足しています。

ブラウザで期限を確認してみましょう。今度は「Session」ではなく1時間後の期限が設定されています。
この期限がCookieのデータの寿命でこの期限をすぎるとCookieのデータは無効となります。-->





