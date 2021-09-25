<!DOCTYPE HTML>
<html lang="ja">
<head>
   <meta charset="UTF-8">
   <title>スーパーグローバル変数課題2受信用</title>
<body>
<?php //8行目の行が重要で、文字が入力されているときのみ(1文字以上あるとき) if文を実行する。
//if ($_SERVER['REQUEST_METHOD'] === 'POST') {
if (isset($_POST['my_name']) === TRUE && mb_strlen ($_POST['my_name']) > 0 ) {
   echo 'ようこそ' . htmlspecialchars($_POST['my_name'], ENT_QUOTES, 'UTF-8') . 'さん';
} else {
   echo '名前を入力してください';
}
//}
?>
</body>
</html>
