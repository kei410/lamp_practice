<?php   //まず最初にHTML部分から考えた方がイメージしやすい
$arry_hand = array('グー', 'チョキ', 'パー'); //配列を定義する
$me        = '';    //取得したい3つ(グー、チョキ、パー)をそれぞれ定義して、文字列を初期化する
$you       = '';
$result    = '';
// 初期化の=は揃えると見やすい

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['hand']) === TRUE) {
    $me  = htmlspecialchars($_POST['hand'], ENT_QUOTES, 'UTF-8');
    $you = $arry_hand[array_rand($arry_hand)]; //array_rand関数で1つだけを取得する
  }               //array_randは配列から一つ以上のキーをランダムに取得する関数．

  // if ($me === '' || $you === '') {
  //   $result = '選択されていません。';  //データがない時の処理
  // $_SERVERを最初に書いているので、上記のif文は不要
  
  if ($me === $you) {
    $result = 'あいこ';
  } else if ($me === 'グー' && $you === 'チョキ' || $me === 'チョキ' && $you === 'パー' || $me === 'パー' && $you === 'グー') {
    $result = '勝ち';
  } else {
    $result = '負け';
  }
  
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>POSTでじゃんけん</title>
</head>
<body>
    <h1>じゃんけん勝負</h1>     
    自分: <?php echo $me; ?><br>
    相手: <?php echo $you; ?><br>
    結果: <?php echo $result; ?><br>
    <form method="post">
        <label><input type="radio" name="hand" value="グー" <?php if ($me === 'グー') { echo 'checked';} ?> >グー</label>
        <label><input type="radio" name="hand" value="チョキ" <?php if ($me === 'チョキ') { echo 'checked';} ?> >チョキ</label>
        <label><input type="radio" name="hand" value="パー" <?php if ($me === 'パー') { echo 'checked';} ?> >パー</label>
        <p>
            <input type="submit" value="勝負!!" >
        </p>
    </form>
</body>
</html>

 <!--
じゃんけん勝負ができるプログラムを作成
ラジオボタンから選択した値(「グー」あるいは「チョキ」あるいは「パー」)をPOSTで送信し、
ユーザと相手(コンピュータ)が選んだ手と、じゃんけんの勝敗を表示してください。
-->

