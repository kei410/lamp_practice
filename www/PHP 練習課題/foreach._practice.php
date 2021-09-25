<?php
$class = array('ガリ勉' => '鈴木', '委員長' => '佐藤', 'セレブ' => '斎藤', 'メガネ' => '伊藤', '女神' => '杉内');
foreach ($class as $key => $value){
    print $value.'さんのアダ名は'.$key.'です。'.'<br>';
}
?>


<?php
$wdays = array('getsuyou' => '月', 'kayou' => '火',  'suiyou' => '水', 'mokuyou' => '木', 'kinyou' => '金');
foreach ($wdays as $key => $value){
    print $value . '曜日' . '<br>';
}
?>


<?php
$al = array(1,2,3,4,5);
$sum = 0;  //合計用の変数
foreach ($al as $value) {   //配列の要素分ループする
    $sum = $sum + $value;   //配列の要素が順番に$valueに代入される
}                           //その値を$sumに加算する
echo"合計は{$sum}です!" . '<br>';
?>


<!--
foreach文で「◯◯さんのアダ名は△△です。」となるよう、全員分の名前とアダ名を表示してください。
キーと値をそれぞれ取り出すだけでOK。
foreach(配列 as 値を入れる変数){
　　繰り返し行う処理内容
}
日本語に訳すと，「配列の要素をループごとに変数として扱う」．つまり，「配列内の要素を1つずつ変数に入れる間，以下を繰り返せ」
-->


<?php
/*
*  配列$areasは長いので手打ちせずコピーして利用してください。
*/
// 都道府県を配列で定義
$areas = array(
'北海道',
'青森県',
'岩手県',
'宮城県',
'秋田県',
'山形県',
'福島県',
'茨城県',
'栃木県',
'群馬県',
'埼玉県',
'千葉県',
'東京都',
'神奈川県',
'新潟県',
'富山県',
'石川県',
'福井県',
'山梨県',
'長野県',
'岐阜県',
'静岡県',
'愛知県',
'三重県',
'滋賀県',
'京都府',
'大阪府',
'兵庫県',
'奈良県',
'和歌山県',
'鳥取県',
'島根県',
'岡山県',
'広島県',
'山口県',
'徳島県',
'香川県',
'愛媛県',
'高知県',
'福岡県',
'佐賀県',
'長崎県',
'熊本県',
'大分県',
'宮崎県',
'鹿児島県',
'沖縄県',
);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>foreachの使用例</title>
</head>
<body>
  <form action="#">
    出身の都道府県を選択してください
    <select name="area">
    <?php
    // 都道府県の配列をループさせる
    foreach ($areas as $key => $area) {
    ?>
      <option value="<?php print $key; ?>"><?php print $area;?></option>
    <?php
    }
    ?>
    </select>
  </form>
</body>
</html>