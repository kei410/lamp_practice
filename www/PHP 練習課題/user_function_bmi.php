<?php
// 最初にHTMLから考えると分かりやすい

// 初期化
$height = '';
$weight = '';
$bmi    = '';
$err_msg = [];  // エラーメッセージ用の配列
// リクエストメソッドを取得する １回目のコールがGET（ただし意図的にGETしたわけではない）
//2回目がコールがPOSTになってるってことに気が付くことが大事

$request_method = $_SERVER['REQUEST_METHOD'];
//続く関数が、$height = get_post_data('height');で、POST「前」を検出するためにこのようにしている。
//最初の1回目はFORMを表示してる段階でPOSTはありませんから、その時どうするか？をこの判定で行っている。
//１．PHPやHTMLなどからの表示物－＞ブラウザ。 ２．その表示物のFORMを押した時ー＞PHPなどCGI/SSI実行
//なので、最初は、送信ではなく「受信」からスタートなので、PHPは最初は「送信物」を持っていない。
/*通常の HTTP 要求は GET メソッドを使用します。

フォーム動作が
* action 省略 → 現在と同じ URL へ HTTP 要求
* POST メソッドで HTTP 要求
となっているので、
以下のような流れとするためにメソッドで判定していると思われます。

ブラウザ:
"/bmi.php" を GET 要求
↓
サーバー:
投稿された値がないので(=POSTではない)、
「あなたの...」は省略した状態で HTML を応答 (初回表示)
↓
ブラウザ:
利用者が入力したフォーム値を添加して
"/bmi.php" を POST 要求
↓
サーバー:
投稿された値があるので(=POST)、
BMIを計算して「あなたの...」を含んだ状態で HTML を応答 (結果表示)
*/

// 「BMI計算」ボタンをクリックした（POSTされた）場合に処理する
if ($request_method === 'POST') {
 // POSTデータを取得する
 $height = get_post_data('height');
 $weight = get_post_data('weight');

 // 身長の値が数値かどうかをチェックする
 if (is_numeric($height) === FALSE) {
   $err_msg[] = '身長は数値を入力してください';
 }
 // 体重の値が数値かどうかをチェックする
 if (is_numeric($weight) === FALSE) {
   $err_msg[] = '体重は数値を入力してください';
 }
 // エラーがない場合にBMIを算出する
 if (count($err_msg) === 0) {
   // BMIを算出する
   $bmi = calc_bmi($height, $weight);
 }
}

/**
* BMIを計算する
* @param mixed $height 身長(cm)
* @param mixed $weight 体重(kg)
* @return float 計算したBMIの値を返す
*/
function calc_bmi($height, $weight) {
return round($weight/($height/100*$height/100), 1); 
}
/**
* POSTデータを取得する
* @param str $key 配列キー
* @return str POSTの値
*/
function get_post_data($key) {
 $str = '';
 if (isset($_POST[$key]) === TRUE) {
   $str = $_POST[$key];
 }
 return $str;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
 <meta charset="UTF-8">
 <title>BMIの計算</title>
</head>
<body>
 <h1>BMI計算</h1>
 <form method="post">
   身長(cm) : <input type="text" name="height" value="<?php print $height;?>"><br>
   体重(kg) : <input type="text" name="weight" value="<?php print $weight; ?>"><br>
   <input type="submit" value="BMIを計算する">
 </form>
<?php if (count($err_msg) > 0) { ?>
<?php   foreach ($err_msg as $value) { ?>
 <p><?php print $value; ?></p>
<?php   } ?>
<?php } ?>
<?php if ($request_method === 'POST' && count($err_msg) === 0) { ?>
 <p>あなたのBMIは<?php print $bmi; ?>です。</p>
<?php } ?>
</body>
</html>

