<?php
// セッション開始
session_start();

$access_flag = FALSE;
$count       = 1;
$lasttime    = '';

if (isset($_SESSION['visited'])) {
  $count = $_SESSION['visited'] + 1;
}

if (isset($_SESSION['timestamp'])) {
  $lasttime = $_SESSION['timestamp'];
  $access_flag = TRUE;
}

$now = date("Y年m月d日 H時i分s秒");

$_SESSION['visited']  = $count;
$_SESSION['timestamp'] = $now;
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>課題 (セッションの利用)</title>
</head>
<body>
<?php if ($count > 1) { ?>
  <p>合計<?php print $count;?>回目のアクセスです</p>
<?php } else { ?>
  <p>初めてのアクセスです</p>
<?php } ?>
  <p><?php print $now; ?> (現在日時)</p>
<?php if ($access_flag) { ?>
  <p><?php print $lasttime; ?> (前回のアクセス日時)</p>
<?php } ?>
  <form action="challenge_session_delete.php" method="post">
    <input type="submit" value="履歴削除">
  </form>
</body>
</html>




<?php 
$msg      = '';
$lastdate = '';
$date     = date("Y/m/d H:i:s");
date_default_timezone_set('Asia/Tokyo');

session_start();

//$_SESSION = array();

if (isset($_SESSION['count']) === TRUE) {

    $_SESSION['count']++;
    $msg = '合計' . $_SESSION['count'] . '回目のアクセスです';
} else {
    $_SESSION['count'] = 1;
    $msg = '初めてのアクセスです';
}

if (isset($_SESSION['lastdate']) === TRUE) {
$lastdate = $_SESSION['lastdate'];
$_SESSION['lastdate'] = $date;
} else {
$_SESSION['lastdate'] = $date;
}
?>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>セッション</title>
    </head>
    <body>
        <p><?php echo $msg; ?></p>
        <p><?php echo $date . '(現在日時)'; ?></p>
        <p><?php echo $lastdate . '(前回のアクセス日時)'; ?></p>
    </body>    
</html>

<!--
セッションを利用して、現在アクセスした日時と前回アクセスした日時を表示する。
初めてアクセスした場合には、「初めてのアクセスです」というメッセージとともに現在の日時を表示
2回目以降のアクセスの場合には、アクセスした回数と、現在アクセスした日時と前回アクセスした日時を表示
-->
