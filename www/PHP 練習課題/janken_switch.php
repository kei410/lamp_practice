<?php
$janken = array(0=>'グー', 1=>'チョキ', 2=>'パー');
$result = '';
$me     = '';
$com    = rand(0,2);
// $_POST['hand']が存在するか確認するのにissetを使う。$_POST['hand']を使いたいのであれば必ず必要
if (isset($_POST['hand'])) {
    $me = $_POST['hand'];
    $judge = ($me - $com + 3) % 3;
    switch ($judge) {
        case 0:
            $result = 'あいこ';
            break;
        case 1:
            $result = '負け';
            break;
        case 2:
            $result = '勝ち';
            break;
    }
}
?>
<!DOCTYPE HTML>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>じゃんけん</title>
</head>
<body>
    <form method ="post">
        <h1>じゃんけん勝負</h1>
<?php if ($result === '') { ?>
        <p>下の3つの中からどれかを選択してください</p>
<?php } else { ?>
        <p>自分:<?php print $janken[$me]; ?></p>
        <p>相手:<?php print $janken[$com]; ?></p>
        <p>結果:<?php print $result; ?></p>
<?php } ?>
        <label><input type="radio" name="hand" value="0">グー</label>
        <label><input type="radio" name="hand" value="1">チョキ</label>
        <label><input type="radio" name="hand" value="2">パー</label>
        <p><input type="submit" value="勝負!"></p>
    </form>
</body>
</html>