<?php
// 配列の練習問題

$area = ['tohoku' => '東北', 'kanto' =>'関東', 'chubu' => '中部', 'kinki' => '近畿', 'chugoku' => '中国', 'shikoku' => '四国', 'kyushu' => '九州'];
print $area['chubu'];
?>

<?php
$al = array('コーヒー' => 150, 'ジュース' => 200, '水' => 0);
echo $al ['ジュース'];
echo '<br>';
$al ['お茶'] = 100;
$al [] = 65535;
var_dump($al);
?>


<?php       //連想配列$fruitを定義した後、500を表示してください。
$fruit = array('name' => 'Lemon', 'price' => 500, 'stock' => 3);
print $fruit['price'];
?>


