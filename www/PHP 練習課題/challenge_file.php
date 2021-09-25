<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">

<?php 
$filename = './challenge_log.txt';
$comment  = '';
$date     = date('m/d H:i:s');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {  
   if (isset($_POST['comment']) === TRUE) {   
      $comment = $_POST['comment'];
   }
   if (($fp = fopen($filename, 'a')) !== FALSE) {
      $result = fwrite($fp, $date ." " . $comment . "\n");
         if ($result === FALSE) {
            print 'ファイル書き込み失敗： ' . $filename;
         }
         fclose($fp);
   }
}

$work = [];

if (is_readable($filename) === TRUE) {
   if (($fp=fopen($filename,'r')) !== FALSE) {
      while(($tmp = fgets($fp)) !==FALSE) {
         $work[] = htmlspecialchars($tmp,ENT_QUOTES,'UTF-8');
      }
      fclose($fp);
   }
} else {
   $work[] = 'ファイルがありません';
}
?>

</head>

<body>
   <h1>ファイル操作の課題</h1>
   <form method="post" >
      <label><p>発言:<input type="text" name="comment"></p></label>
      <label><p></p><input type="submit" value="送信"></p></label>
   </form>
   <p>発言一覧</p>
<?php foreach ($work as $value){?>  <!--$work の要素の値を $value として処理する-->
   <p><?php print $value ?></p>
<?php } ?>
</body>
</html>

<!--
テキストボックスの値をPOSTで送信し、日時とユーザが入力した値を1行ずつファイル(challenge_log.txt)に保存し、
ページ下部にファイル内容を1行ずつ表示するプログラムを作成してください。
2個目のfopen() で、ファイル読み込みモードは'r'になる。
-->
