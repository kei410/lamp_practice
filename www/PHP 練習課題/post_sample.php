<?php
// 変数を初期化する
$gender = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['gender']) === TRUE) {
     $gender = htmlspecialchars($_POST['gender'], ENT_QUOTES, 'UTF-8');
  }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
   <meta charset="UTF-8">
   <title>スーパーグローバル変数使用例</title>
</head>
<body>
   <h2>性別を選択してください</h2>
<?php if ($gender === '男' || $gender === '女') { ?>
   <p>あなたの性別は「<?php print $gender; ?>」です</p>
<?php } ?>
   <form method="post">
       <label><input type="radio" name="gender" value="男" <?php if ($gender === '男') { print 'checked'; } ?>>男</label> 
       <!--22行目と23行目がポイント。この2行のphp文でラジオボタンの黒丸が残るようにしている。-->
       <label><input type="radio" name="gender" value="女" <?php if ($gender === '女') { print 'checked'; } ?>>女</label>   
       <input type="submit" value="送信">
   </form>
</body>
</html>



