<?php 
challenge_cookie.php

$meg      = '';
$lastdate = '';
$date     = date("Y/m/d H:i:s");
date_default_timezone_set('Asia/Tokyo');

session_start(); //セッションスタート

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
<body>
    <p><?php echo $msg; ?></p>
    <p><?php echo $date . '(現在日時)'; ?></p>
    <p><?php echo $lastdate . '(前回のアクセス日時)'; ?></p>
</body>    
</head>
</html>

