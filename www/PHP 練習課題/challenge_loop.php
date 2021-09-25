<?php
for ($i = 1; $i<=100; $i++) {
  if ($i % 3 === 0 && $i % 5 === 0) {
        print "FizzBuzz\n";
    } else if ($i % 5 === 0) {
        print "Buzz\n";                 //ソースコードの改行コード指定は'（シングルクォーテーション）ではなく、
        print "Fizz\n";                 //"（ダブルクォーテーション）で括る必要があります。 \nを使う
    } else if($i % 3 === 0) {
    } else 
        print $i."\n";
} 
?>  


<!-- 1から100までの整数に対し、

3で割り切れる場合は「Fizz」
5で割り切れる場合は「Buzz」
3でも5でも割り切れる場合は「FizzBuzz」
上記以外は数値そのまま表示してください。


JSだと数値は多少異なるが、以下のようになる -->


<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>Fizz Buzz</title>
</head>
<script>
    for (var i = 1; i < 100001; i++) {
        if (i % 5 === 0) {
            document.write('FizzBuzz');
        } else if (i % 5 === 0) {
            document.write;
        } else if (i % 3 === 0) {
            document.write('Fizz');
        } else {
            document.write(i + '  ')
        }
    }
</script>
</body>
</head>
