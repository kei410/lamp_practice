<?php
$filename = './file_write.txt'; 
 //あらかじめプログラムを実行前に、空のfile_write.txtを作成しておくこと
 //$_SERVERはスーパーグローバル変数なので注意する
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
  $comment = $_POST['comment']."\n";
  //$commentは新しい変数で'comment'と同じ名前にしている
  //fopenは"倉庫"のようなもので、$fpは変数で倉庫のカギと捉える
  //fopenはTRUEにしても返ってこないので、無理やりFALSEで条件指定することに注意する
  //!==は左右のオペランド（演算子引数）の値を比較し、型も含めて等しくなければ「真（true）」、型も含めて等しければ「偽（false）」
  if (($fp = fopen($filename, 'a')) !== FALSE) {
    if (fwrite($fp, $comment) === FALSE) {
      print 'ファイル書き込み失敗:  ' . $filename;
    }
    fclose($fp);
  }
}
 //fgetsもfopen同様にFALSEで無理やり条件をつくる
$data = array();
 
if (is_readable($filename) === TRUE) {
  if (($fp = fopen($filename, 'r')) !== FALSE) {
    while (($tmp = fgets($fp)) !== FALSE) {
      $data[] = htmlspecialchars($tmp, ENT_QUOTES, 'UTF-8');
    }
    fclose($fp);
  }
} else {
  $data[] = 'ファイルがありません';
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>ファイル操作</title>
</head>
<body>
  <h1>ファイル操作</h1>
  <form method="post">
    <input type="text" name="comment">
    <input type="submit" name="submit" value="送信">
  </form>
  <p>以下に<?php print $filename; ?>の中身を表示</p>
<?php foreach ($data as $read) { ?>
  <p><?php print $read; ?></p>
<?php } ?>
</body>
</html>