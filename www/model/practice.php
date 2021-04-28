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
  
 