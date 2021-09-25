<?php
//関数を作成し曜日をグループ化
function Week(){
    return array('日', '月', '火', '水', '木', '金', '土');
}
//曜日を取得して変数化
$w = Week()[date("w")];
//全て表示
echo date("Y/m/d($w)");
?>


<?php
function Week(){
    return array('日', '月', '火', '水', '木', '金', '土');
}

$w = Week()[date("w")];

echo date("Y/m/d($w)");
?>
