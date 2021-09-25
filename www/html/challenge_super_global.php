<?php                  //最初にHTMLのform内容を考えてからPHPを考えると分かりやすい
$my_name = '';         //「名前」と「性別」と「お知らせメール」の値を取得したいので、最初にそれぞれ変数を定義して文字列を初期化する
$gender  = '';         //POSTを使って、それぞれ書いた値が$POSTで変数に代入するように記述する
$mail    = '';         //下のフォームから飛んでくるので、初期化の式は=''の形になることを覚えておく
//=を揃えると見やすくなる
//2~4行目ですでに空文字''がセットされていることに注意する

// 送信ボタンがクリックされた場合の処理(リクエストメソッド)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['my_name']) === TRUE && mb_strlen ($_POST['my_name']) > 0) {
        $my_name = htmlspecialchars($_POST['my_name'], ENT_QUOTES, 'UTF-8');
    } else {
        echo '名前が入力されていません。';
    }

    if (isset($_POST['gender']) === TRUE) {
        $gender = htmlspecialchars($_POST['gender'], ENT_QUOTES, 'UTF-8');
    }

    if (isset($_POST['mail']) === TRUE) {
        $mail = htmlspecialchars($_POST['mail'], ENT_QUOTES, 'UTF-8');
    }
}
?>
<!--ここまででそれぞれの変数が初期化されています。
また、それぞれのif文により、$_POST['my_name']などがセットされている場合（=ページがPOSTにより読み込まれ、適切に値が送信されている場合）にはその値がセットされます。-->
<!DOCTYPE HTML>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>テキストボックス、ラジオボタン、チェックボックスをHTMLで作成して、ユーザが選択した値をPOSTで受け取り、表示するプログラム</title>
</head>
<body>
<?php 
if (mb_strlen ($my_name) > 0) { ?>
    <p>入力した名前: <?php echo $my_name; ?></p>
<?php } ?>
<?php 
if ($gender === '男性' || $gender === '女性') { ?>
    <p>選択した性別: <?php echo $gender; ?></p>
<?php } ?>
<?php 
if ($mail === 'OK') { ?>
    <p>メールを受け取るかを表示: <?php echo $mail; ?></p>
<?php } 
?>
    <!--25～33行目の部分では$my_nameや$genderなどがセットされているかどうかをifで判別し、セットされている場合にはその値をprintで出力しています。-->
    <!--動作の条件分岐としては、1.初めてページを開いた場合 2.送信ボタンが押されたが何も入力されていない場合 3.何かを入力して送信ボタンが押された場合になる。
    1・2はほぼ同じで、$_POST['my_name']等がセットされていない状況、3はそれらがセットされているので、「入力した名前」などとして表示がされます。-->
    
    <!--（引き渡す処理）を書いてさらにラジオボタンや送信ボタンを書けば完成-->
    <form method="post">         
        <p>
            <label>お名前: <input type="text" name="my_name"></label>
        </p>
        <p>
            性別: 
            <label><input type="radio" name="gender" value="男性">男</label>
            <label><input type="radio" name="gender" value="女性">女</label>
        </p>
        <p>
            <label><input type="checkbox" name="mail" value="OK">お知らせメールを受け取る</label>
        </p>
        <p>
            <input type="submit" value="送信">
        </p>
    </form>
</body>
</html>

