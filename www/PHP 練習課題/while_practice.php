<?php
$sum = 0;
$i = 3;    //3の倍数だけを考えるから3スタート
while($i <= 100) {
    $sum += $i;
    $i += 3;
}
print '合計値は' . $sum;
?>


<!--
1から100までの間で、3の倍数の数だけを足した合計値を表示してください。
PHPで、while文をした場合

等差数列の和を利用すると以下のようになる
challenge_for.phpを参照
mからnまでの和は
(m+n)(n-m+a)/2であるから、     項数がn-m+1になる
-->


<?php
$i = 1900;
$date = date('Y'); // 現在の西暦を取得
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>ループの使用例</title>
</head>
<body>
  <form action="#">
    生まれ西暦を選択してください
    <select name="born_year">
    <?php
    // 1900年〜現在の西暦までをループで処理する
    while ($i <= $date) {
    ?>
      <option value="<?php print $i; ?>"><?php print $i; ?>年</option>
    <?php
      $i++;
    }
    ?>
    </select>
  </form>
</body>
</html>