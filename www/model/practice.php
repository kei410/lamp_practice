<?php
// 下書き用のファイル

// CSRF対策
// トークンの生成
// トークンは合言葉や鍵の意味
function get_csrf_token(){
  // get_random_string()はユーザー定義関数。
  $token = get_random_string(30);
  // set_session()はユーザー定義関数。
  set_session('csrf_token', $token);
 /*  $_SESSION['csrf_token'] = $token; */
  return $token;
}

// トークンのチェック
function is_valid_csrf_token($token){
  if($token === '') {
    return false;
  }
  // get_session()はユーザー定義関数
  return $token === get_session('csrf_token');
  /* $_SESSION['csrf_token']; */
}

// CSRF対策 使う部分だけを抜粋
header('X-FRAME-OPTIONS: DENY');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SESSION['token']===$_POST['token']) {

$token = substr(base_convert(hash('sha256', uniqid()), 16, 36), 0, 20);
$_SESSION['token']=$token;

function get_random_string($length = 20){
  return substr(base_convert(hash('sha256', uniqid()), 16, 36), 0, $length);
}













// ひとこと掲示板 CSRF対策済
// X-Frame-Options は HTTP のレスポンスヘッダーで、ブラウザーがページを <frame> , <iframe> , <embed> , <object> の中に
// 表示することを許可するかどうかを示すために使用される。
<?php
header('X-FRAME-OPTIONS: DENY');
session_start();

$filename = './review.txt'; //利用者の過去の発言内容をテキストファイル(review.txt)で管理する。
$my_name  = '';
$comment  = '';
$error    = [];
$data     = [];
$now      = date('Y-m-d H:i:s');

//10行目以降の流れはPHPの基本となる大事な形なので、必ずマスターする
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SESSION['token']===$_POST['token']) {
   if (isset($_POST['my_name']) === TRUE) {  // 名前が投稿された場合に入力された内容を$my_nameに代入
       $my_name = $_POST['my_name'];
   } 
   if (isset($_POST['comment']) === TRUE) {  // コメントが投稿された場合に入力された内容を$commentに代入
       $comment = $_POST['comment'];
   } 
   if (mb_strlen($my_name) > 20) {   // 入力された文字数を確認しエラーメッセージを$error[]に格納
       $error[] = '名前は20文字以内で入力してください';
   }
   if (mb_strlen($comment) > 100) {  // 入力された文字数を確認しエラーメッセージを$error[]に格納
       $error[] = '一言は100文字以内で入力してください';
   }
   if ($my_name === '') {
       $error[] = '名前を入力してください';
   }
   if ($comment === '') {
       $error[] = '一言を入力してください';
   }
   
   if (count($error) === 0) {   // $error内のエラー数を確認
       $fp = fopen($filename, 'a');
       if ($fp !== FALSE) {  // エラーが無ければテキストファイルを開く
           $result = fwrite($fp,$my_name. ':' .$comment . '-' . $now . "\n");
           if ($result === FALSE) {
              print 'ファイル書き込み失敗： ' . $filename;
           }
           fclose($fp); //ファイルを閉じる
       }
   }
}    

if (is_readable($filename) === TRUE) {  // ファイルが読み込み可能か確認
   if (( $fp= fopen($filename, 'r')) !== FALSE) {
       while (($tmp = fgets($fp)) !== FALSE) {
           $data[] = htmlspecialchars($tmp, ENT_QUOTES, 'UTF-8');   // テキストの内容を取得し$data[]に格納する
       }
       fclose($fp);
   }
} else {
   $data[] = 'ファイルがありません';
}

$token = substr(base_convert(hash('sha256', uniqid()), 16, 36), 0, 20);
$_SESSION['token']=$token;
?>
<!DOCTYPE html>
<html lang ="ja">
<head>
   <meta charset ="UTF-8">
   <title>5章提出課題</title>
</head>
<body>
   <h1>ひとこと掲示板</h1>
<?php foreach ($error as $value) { ?>
       <p><?php print $value; ?></p>
<?php } ?> 
   <form method="post">
       <label>名前：<input type="text" name="my_name"></label>
       <label>一言：<input type="text" name="comment"></label>
       <input type='hidden' name='token' value='<?php print $token;?>'>
       <input type="submit" name="submit" value="送信">
   </form>
       <ul>
<?php foreach ($data as $value) { ?>
           <li><?php print $value; ?></li>
<?php } ?>
       </ul>
</body>
</html>




function get_random_string($length = 20){
  return substr(base_convert(hash('sha256', uniqid()), 16, 36), 0, $length);
}


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
function get_session($name){
  if(isset($_SESSION[$name]) === true){
    return $_SESSION[$name];

  }

}

// セッションに値を設定する
// 第一引数がキーで、第二引数が値
function set_session($name, $value){
  $_SESSION[$name] = $value;
}

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
  
  // PDO、商品IDを受け取
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
  
  return execute_query($db, $sql);
}

try {
    $sql = 'UPDATE item_stock
            SET stock = ?, update_date = ?
            WHERE item_id = ?';
    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(1, $update_stock,  PDO::PARAM_INT);  
    $stmt->bindValue(2, $date,          PDO::PARAM_STR); 
    $stmt->bindValue(3, $item_id,       PDO::PARAM_INT);
    $stmt->execute();
    $complete_msg[] = '在庫変更に成功しました。';
} catch (PDOException $e) {
    $err_msg[] = '在庫数が変更できませんでした。理由：' . $e->getMessage();
}



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
    $stmt->bindValue(1, $params[0], PDO::PARAM_INT);
    $stmt->bindValue(2, $params[1], PDO::PARAM_INT);
    return $stmt->execute();
  }catch(PDOException $e){
    set_error('更新に失敗しました。');
  }
  return false;
}


function update_item_stock($db, $item_id, $stock){
  $sql = "
    UPDATE
      items
    SET
      stock = ?       /* {$stock} */
    WHERE
      item_id = ?     /*  {$item_id}  */
    LIMIT 1
  ";

  return execute_query($db, $sql ,array($stock, $item_id));
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
// PDO、商品名、価格、在庫数、ファイル名、ステータスを受け取り、データベースに新たに商品を追加する
function insert_item($db, $name, $price, $stock, $filename, $status){
  // $status_valueは0か1（非公開か公開）
    $status_value = PERMITTED_ITEM_STATUSES[$status];
    $sql = "
      INSERT INTO
        items(
          name,
          price,
          stock,
          image,
          status
        )
        VALUES(?, ?, ?, ?, ?);
    ";
  
    return execute_query($db, $sql, array($name, $price, $stock, $filename, $status_value));
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
  function execute_query($db, $sql, $params = array()){
    try{
      $stmt = $db->prepare($sql);
      rerturn $stmt->execute($params);
    }catch{
      set_error();
    }

  }

  function insert_item($db, $name, $price, $stock, $filename, $status){
    // $status_valueは0か1（非公開か公開）
      $status_value = PERMITTED_ITEM_STATUSES[$status];
      $sql = "
        INSERT INTO
          items(
            name,
            price,
            stock,
            image,
            status
          )
          VALUES(?, ?, ?, ?, ?);
      ";
    
      return execute_query($db, $sql, array($name, $price, $stock, $filename, $status_value));
    }

    function insert_item($db, $name, $price, $stock, $filename, $status){
      $sql = "
        INSERT INTO
          items(
            name,
            price,
            stock,
            image,
            status
          )
          VALUES(?, ?, ?, ?, ?);
          ";

          return execute_query($db, $sql, array($name, $price, $stock, $filename, $status_value));
    }