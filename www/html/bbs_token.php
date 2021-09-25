<?php
// 一言掲示板 (CSRF対策済み)
header('X-FRAME-OPTIONS: DENY');
session_start();

$filename = './review.txt'; //利用者の過去の発言内容をテキストファイル(review.txt)で管理する。
$my_name  = '';
$comment  = '';
$error    = [];
$data     = [];
$now      = date('Y-m-d H:i:s');

//10行目以降の流れはPHPの基本となる大事な形なので、必ずマスターすること
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SESSION['token'] === $_POST['token']) {
   if (isset($_POST['my_name']) === TRUE) {  // 名前が投稿された場合に入力された内容を$my_nameに代入
       $my_name = $_POST['my_name'];
   } 

   if (isset($_POST['comment']) === TRUE) {  // コメントが投稿された場合に入力された内容を$commentに代入
       $comment = $_POST['comment'];
   } 

   if (mb_strlen($my_name) > 20) {   // 入力された文字数を確認しエラーメッセージを$error[]に格納
       $error[] = '名前は20文字以内で入力してください';
   }

   if (mb_strlen($comment) > 100) {  // 入力された文字数を確認しエラーメッセージを$error[]に格納
       $error[] = '一言は100文字以内で入力してください';
   }

   if ($my_name === '') {
       $error[] = '名前を入力してください';
   }

   if ($comment === '') {
       $error[] = '一言を入力してください';
   }
   
   if (count($error) === 0) {   // $error内のエラー数を確認
       $fp = fopen($filename, 'a');
       if ($fp !== FALSE) {  // エラーが無ければテキストファイルを開く
           $result = fwrite($fp,$my_name. ':' .$comment . '-' . $now . "\n");
           if ($result === FALSE) {
              print 'ファイル書き込み失敗： ' . $filename;
           }
           fclose($fp); //ファイルを閉じる
       }
   }
}    

if (is_readable($filename) === TRUE) {  // ファイルが読み込み可能か確認
   if (( $fp= fopen($filename, 'r')) !== FALSE) {
       while (($tmp = fgets($fp)) !== FALSE) {
           $data[] = htmlspecialchars($tmp, ENT_QUOTES, 'UTF-8');   // テキストの内容を取得し$data[]に格納する
       }
       fclose($fp);
   }
} else {
   $data[] = 'ファイルがありません';
}

$token = substr(base_convert(hash('sha256', uniqid()), 16, 36), 0, 20);
$_SESSION['token'] = $token;
?>
<!DOCTYPE html>
<html lang ="ja">
<head>
   <meta charset ="UTF-8">
   <title>5章提出課題</title>
</head>
<body>
   <h1>ひとこと掲示板</h1>
<?php foreach ($error as $value) { ?>
       <p><?php print $value; ?></p>
<?php } ?> 
   <form method="post">
       <label>名前：<input type="text" name="my_name"></label>
       <label>一言：<input type="text" name="comment"></label>
       <input type='hidden' name='token' value='<?php print $token;?>'>
       <input type="submit" name="submit" value="送信">
   </form>
       <ul>
<?php foreach ($data as $value) { ?>
           <li><?php print $value; ?></li>
<?php } ?>
       </ul>
</body>
</html>