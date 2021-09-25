<?php
$rand = mt_rand(1, 6);
                                     //  PHPでは改行に\nをよく使う
print '出た目は' . $rand . '<br>';   //今回は<br>で改行してブラウザ上で見やすく表示 
if($rand % 2 === 0){
          print '偶数';
       } 
       else {
          print '奇数';
       }
?>

<!--サイコロ(1〜6)を振り、「出た数字」と
「偶数か奇数か」の2つの情報を表示してください-->


<?php           　　　　　　      //0～2のランダムな数値を2つ取得し、「それぞれの数値」と「どちらの数値のほうが大きいか」
$rand1 = mt_rand(0, 2);           //の情報を表示してください。 (2つの値が同じ場合は[同じ値]と表示) 
$rand2 = mt_rand(0, 2);

print 'rand1:' . $rand1 . '<br>';
print 'rand2:' . $rand2 . '<br>';

if($rand1 > $rand2) {
    print 'rand1のほうが大きい値です';
} else if ($rand2 > $rand1) {
    print 'rand2のほうが大きい値です';
} else {
    print '2つは同じ値です';
}
?>













