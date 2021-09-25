<?php          //「名前」と「性別」と「お知らせメール」の値を取得したいので、最初にそれぞれ変数を定義する。
$my_name = ''; //POSTを使って、それぞれ書いた値が$POSTで変数に代入するように記述する
$gender  = '';
$mail    = '';

if (isset($_POST['my_name']) === TRUE && $_POST['my_name'] !== '' ) {
    $my_name = htmlspecialchars($_POST['my_name'],ENT_QUOTES,'UTF-8');
}

if (isset($_POST['gender']) === TRUE) {
    $gender = htmlspecialchars($_POST['gender'],ENT_QUOTES,'UTF-8');
}

if (isset($_POST['mail']) === TRUE) {
    $mail = htmlspecialchars($_POST['mail'],ENT_QUOTES,'UTF-8');
}
print $gender;
?>
<?php print $gender; ?>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>　　　　　　　　　　　　　　　
<?php print $gender; ?>
<?php if (isset($my_name) === TRUE) { ?>　　
            <p>入力した名前: <?php print $my_name; ?></p>
<?php } ?>
<?php print $gender; ?>
<?php if ($gender === 'man' || $gender === 'woman') { ?>
            <p>選択した性別: <?php print $gender; ?></p>
<?php } ?>
<?php if ($mail === 'OK') { ?>
        <p>メールを受け取るかを表示: <?php print $mail; ?></p>
<?php } ?>
        <form method="post">　　　<!--（引き渡す処理）を書いてさらにラジオボタンや送信ボタンを書けば完成-->
            <p><label>お名前: <input id="my_name" type="text" name="my_name"></label></p>
            <label>性別: <input type="radio" name="gender" value="man">男</label>
            <input type="radio" name="gender" value="woman">女
            <input type="checkbox" name="mail" value="OK">お知らせメールを受け取る
            <p><input type="submit" value="送信"></p>
        </form>
    </body>
</html>



















