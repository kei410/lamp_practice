<html lang="ja">
  <head>
    <meta charset="utf-8">
    <title>バリデーション</title>
  </head>
  <body>
    <form method="post" action="./validation.php">
      メールアドレス：<input type="text" name="email">
      電話番号：<input type="text" name="tel">
      <input type="submit" value="送信">
    </form>
  </body>
</html>


<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <title>バリデーション</title>
  </head>
  <body>
    <?php
    // メールアドレスをチェック
    $email = $_POST['email'];
 
    // 正規表現
    $email_regex = '/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/iD';
 
    // バリデーション実行
    if( preg_match($email_regex, $email) ) {
      print($email."は正しいメールアドレスです。<br>");
    }else{
      print($email."は正しくないメールアドレスです。<br>");
    }
 
    // 電話番号をチェック
    $tel = $_POST['tel'];
 
    // 正規表現
    $tel_regex = '/^[0-9]{2,4}-[0-9]{2,4}-[0-9]{3,4}/';
 
    // バリデーション実行
    if( preg_match($tel_regex, $tel) ) {
      print($tel."は正しい電話番号です。<br>");
    }else{
      print($tel."は正しくない電話番号です。<br>");
    }
    ?>
  </body>
</html>