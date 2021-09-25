<?php
$sum = 0;
for($i = 3; $i <= 100; $i += 3){
    $sum += $i;
}
print '合計値は' . $sum;
?>


<!--
1から100までの間で、3の倍数の数だけを足した合計値を表示してください。
PHPで、for文をした場合

等差数列の和を利用すると以下のようになる
mからnまでの和は
(m+n)(n-m+a)/2であるから、     項数がn-m+1になる
　
<?php
$d = 3;   //公差d
$n = intdiv(100,$d);    //項数33：
$sum = $n * (2 * $d + (n-1) * $d)/2;
print $sum;
?>

-->


<?php
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
    生まれた西暦を選択してください
    <select name="born_year">
    <?php
    // 1900年〜現在の西暦までをループで処理する
    for ($i = 1900; $i <= $date; $i++) {
    ?>
      <option value="<?php print $i; ?>"><?php print $i; ?>年</option>
    <?php
    }
    ?>
    </select>
  </form>
</body>
</html>