<?php
$submit  = false;
$my_name = '';
$gender  = '';
$mail    = '';

if (isset($_POST['submit']) === TRUE) {

  $submit = true;

  if (isset($_POST['my_name']) === TRUE) {
    $my_name = htmlspecialchars($_POST['my_name'], ENT_QUOTES, 'UTF-8');

    if ($my_name === '') {
      $my_name = '未入力';
    }
  }

  if (isset($_POST['gender']) === TRUE) {
    $gender = htmlspecialchars($_POST['gender'], ENT_QUOTES, 'UTF-8');
  } else {
    $gender = '未選択';
  }

  if (isset($_POST['mail']) === TRUE) {
    $mail = htmlspecialchars($_POST['mail'], ENT_QUOTES, 'UTF-8');
  } else {
    $mail = 'NG';
  }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>課題 (フォームとPOST)</title>
</head>
<body>
<?php
  if ($submit === TRUE) {
?>
  <p>ここに入力したお名前を表示: <?php print $my_name; ?></p>
  <p>ここに選択した性別を表示: <?php print $gender; ?></p>
  <p>ここにメールを受け取るかを表示: <?php print $mail; ?></p>
<?php
  }
?>
  <h1>課題</h1>
  <form method="post">
    <p>
      <label>お名前:<input id="my_name" type="text" name="my_name" value=""></label>
    </p>
    <p>性別: 
      <label><input type="radio" name="gender" value="man">男</label>
      <label><input type="radio" name="gender" value="woman">女</p></label>
    <p>
      <label><input type="checkbox" name="mail" value="OK">お知らせメールを受け取る</label>
    </p>
    <input type="submit" name="submit" value="送信">
  </form>
</body>
</html>


