<?php
// 下書き用のファイル

// POSTで飛んでくるページだけに以下の処理を追加する。
// PHPファイル（HTMLディレクトリ内）
// get_post();
/////////////////////////////////////////

if (is_valid_csrf_token($token) === false || get_request_method() !== 'POST') {
  set_error('不正なリクエストです。');
  redirect_to(LOGIN_URL);
} 

// controllerに追加する
$token = get_csrf_token();

get_post('csrf_token')

// 1つのフォームタグに1つ入れる  viewのページ

<input type="hidden" name='csrf_token' value="<?php print $token;?>">

/////////////////////////////////////////
// CSRF対策
// 1. トークンの生成
// トークンは合言葉や鍵の意味
function get_csrf_token(){
  // get_random_string()はユーザー定義関数。
  $token = get_random_string(30);
  // set_session()はユーザー定義関数。
  // CSRFのトークンを生成してセッションに格納したものを返す
  set_session('csrf_token', $token);
  // $_SESSION['csrf_token'] = $token; 
  return $token;
}

// 2.トークンのチェック
function is_valid_csrf_token($token){
  if($token === '') {
    return false;
  }
  // 引数に与えられたもの$tokenと$_SESSION[‘csrf_token’]とを比較して
  // 合っていればtrue、そうでなければfalseを返す
  return $token === get_session('csrf_token');
  // $_SESSION['csrf_token']; 
  // $token=$_POST["csrf_token"]?
}

function get_random_string($length = 20){
  return substr(base_convert(hash('sha256', uniqid()), 16, 36), 0, $length);
}

// POSTで飛んでくるページだけに以下の処理を追加する。
/////////////////////////////////////////
if (is_valid_csrf_token($token) === false || get_request_method() !== 'POST') {
  set_error('不正なリクエストです。');
  redirect_to(LOGIN_URL);
} 

// リクエストメソッドを取得
function get_request_method(){
  return $_SERVER['REQUEST_METHOD'];
}

function get_request_method(){
  return $_SERVER['REQUEST_METHOD'];
}
/////////////////////////////////////////

// 各種phpファイルに以下の処理を追加する? 最初のところ
if (get_session('csrf_token') === get_post('csrf_token')) {

}


function is_valid_upload_image($image){
  // ファイルの形式が異なる場合はエラーメッセージを表示してFALSEを返す
  if(is_uploaded_file($image['tmp_name']) === false){
    set_error('ファイル形式が不正です。');
    return false;
  }

  if(update_item_stock($db, $item_id, $stock)){
    set_message('在庫数を変更しました。');
  } else {
    set_error('在庫数の変更に失敗しました。');
  }
  
  redirect_to(ADMIN_URL);


//////////////
// CSRF対策   bbs.phpから使う部分だけを抜粋

// X-Frame-Options は HTTP のレスポンスヘッダーで、ブラウザーがページを <frame> , <iframe> , <embed> , <object> の中に
// 表示することを許可するかどうかを示すために使用される。
header('X-FRAME-OPTIONS: DENY');
session_start();

/* if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SESSION['token'] === $_POST['token']) { */


$token = substr(base_convert(hash('sha256', uniqid()), 16, 36), 0, 20);
$_SESSION['token'] = $token;

<form method="post">
       <label>名前：<input type="text" name="my_name"></label>
       <input type='hidden' name='token' value='<?php print $token;?>'>
       <input type="submit" name="submit" value="送信">
</form>
//////////////

// セッションのキーを指定して値を取り出す
// セットされていないときは空文字を返す
// 連想配列とは、配列のキーが文字列となったものでキーに文字列を設定できる
// 配列は複数のデータを管理できるもので、キーが整数になっているもの
function get_session($name){
  if(isset($_SESSION[$name]) === true){
    return $_SESSION[$name];
  };
  return '';
}

// セッションに値を設定する
// 第一引数がキーで、第二引数が値になっている
function set_session($name, $value){
  $_SESSION[$name] = $value;
}

// クエリを実行する
// execute()メソッドの引数に配列を渡すと、それらを全て
// バインドした後そのままSQLを実行してくれる
function execute_query($db, $sql, $params = array()){
    try{
      $statement = $db->prepare($sql);
      return $statement->execute($params);
    }catch(PDOException $e){
  // 失敗したらset_error()関数を実行してFALSEを返す
      set_error('更新に失敗しました。');
    }
    return false;
}

// 下記を参考にしてSQLインジェクション対策を実装する。

function update_item_stock($db, $item_id, $stock){
    $sql = "
        UPDATE 
            items 
        SET stock = ? 
        WHERE item_id = ? 
        LIMIT 1";
    $params = array('item_id' => $item_id, 'stock' => $stock);
    return execute_query($db, $sql, $params);
  }
  
  function execute_query($db, $sql, $params = array()){
    try{
      $stmt = $db->prepare($sql);
      $stmt->bindValue(1, $params['stock'], PDO::PARAM_INT);
      $stmt->bindValue(2, $params['item_id'], PDO::PARAM_INT);
      return $stmt->execute();
    }catch(PDOException $e){
      set_error('更新に失敗しました。');
    }
    return false;
  }
 
// 変更前
  function update_item_stock($db, $item_id, $stock){
    $sql = "
      UPDATE
        items
      SET
        stock = {$stock}
      WHERE
        item_id = {$item_id}
      LIMIT 1
    ";
    
    return execute_query($db, $sql);
  }
  

// PDO、商品ID、ステータスを受け取り、商品の公開ステータスを更新する
function update_item_status($db, $item_id, $status){
  $sql = "
    UPDATE
      items
    SET
      status = ?
    WHERE
      item_id = ? 
    LIMIT 1
  ";
  
  return execute_query($db, $sql, array($status, $item_id));
}

function execute_query($db, $sql, $params = array()){
  try{
    $statement = $db->prepare($sql);
    return $statement->execute($params);
  }catch(PDOException $e){
// 失敗したらset_error()関数を実行してFALSEを返す
    set_error('更新に失敗しました。');
  }
  return false;
}

function execute_query($db, $sql, $param = array()){
  try{
    $stmt = $db->prepare($sql);
    return $stmt->execute($param);
    
  }

}

if (count($err_msg) === 0) {
  try {
      $sql = 'UPDATE product
              SET status = ?, update_date = ?
              WHERE id = ?';
      $stmt = $dbh->prepare($sql);
      /* $stmt->bindValue(1, $change_status,   PDO::PARAM_INT);  
      $stmt->bindValue(2, $date,            PDO::PARAM_STR);  //日付は文字列なのでSTRにする
      $stmt->bindValue(3, $item_id,         PDO::PARAM_INT);  */
      $stmt->execute();
      $complete_msg[] = 'ステータスを変更しました。';
  } catch (PDOException $e) {
      $err_msg[] = 'ステータスが変更できませんでした。理由：' . $e->getMessage();
  }
}

function execute_query($db, $sql, $params = array()){
  try{
    $statement = $db->prepare($sql);
    return $statement->execute($params);
  }catch(PDOException $e){
// 失敗したらset_error()関数を実行してFALSEを返す
    set_error('更新に失敗しました。');
  }
  return false;
}


 
<body>
    <script>
        var data = [59, 39, 100, 2, 15, 40, 84, 97];
        var ave;

        ave = calc_ave(data);

        function calc_ave(data) {
            sum = 0;
            for (i = 0; i < data.length; i++) {
                sum += data[i]
            }
            ave = sum / data.length;
            return ave;
        }
        document.write(ave);
        // 関数calc_aveを定義　配列dataの平均値を表示するプログラム
    </script>
</body>

var data = [59, 39, 100, 2, 15, 40, 84, 97];
var ave;

ave = calc_ave(data);


<?php
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
return round($weight/($height/100*$height/100),1); 
}
/**
* POSTデータを取得する
* @param str $key 配列キー
* @return str POSTの値
*/
function get_post_data($key) {
 $str = '';
 if (isset($_POST['height']) === TRUE) {
   $str = $_POST['height'];
 }
 return $str;
}
$height = get_post_data('height');
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






<?php
<form method="post">
   身長(cm) : <input type="text" name="height" value="<?php print $height;?>"><br>
   体重(kg) : <input type="text" name="weight" value="<?php print $weight; ?>"><br>
   <input type="submit" value="BMIを計算する">
 </form>


 <form method="post">
  身長 : <input type="text" name="height" value="<?php echo $height; ?>"><br>
  体重 : <input type="text" name="weight" value="<?php echo $weight; ?>"><br>
</form>
