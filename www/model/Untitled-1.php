<?php
// 下書き用ファイル

// XSS対策
//特殊文字をHTMLエンティティに変換する
function entity_str($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

/**
* 特殊文字をHTMLエンティティに変換する (2次元配列の値)
* @param array  $assoc_array 変換前配列
* @return array 変換後配列
*/

function entity_assoc_array($assoc_array) {

// 2次元配列は縦横の表のようなものとイメージする
// 二重の foreach により、$values に[$key][$keys] の値が1つずつ入って処理される
// entity_str(内容の1つ) と言う処理をした結果を再度 [$key][$keys] に入れているので、
// 結局は配列に格納されている全ての値について、 entity_str() によって処理をする
//単純に$assoc_arrayの中身をentity_str()を通した内容で置き換えています。
  foreach ($assoc_array as $key => $value) {
    foreach ($value as $keys => $values) {
      // 特殊文字をHTMLエンティティに変換する
      $assoc_array[$key][$keys] = entity_str($values);
    }
  }

  return $assoc_array;
}



/**
* クエリを実行しその結果を配列で取得する
*
* @param obj  $dbh DBハンドル
* @param str  $sql SQL文
* @return array 結果配列データ
*/

// クエリを実行してその結果を配列で取得する。
function get_as_array($dbh, $sql) {
  try {
    // SQL文を実行する準備
    $stmt = $dbh->prepare($sql);
    // SQLを実行
    $stmt->execute();
    // レコードの取得
    $rows = $stmt->fetchAll();
  } catch (PDOException $e) {
    throw $e;
  }
 
  return $rows;
}
function get_as_array($dbh, $sql) {
    try {
      $stmt = $dbh->prepare($sql);
      $stmt->execute();
      $rows = $stmt->fetchALL();
    } catch(){

    }
}
function execute_query($db, $sql, $params = array()){
  try{
    $statement = $db->prepare($sql);
    return $statement->execute($params);
  }catch(PDOException $e){
    set_error('更新に失敗しました。');
  }
  return false;
}


// PDO、商品ID、ステータスを受け取り、商品の公開ステータスを更新する
function update_item_status($db, $item_id, $status){
  $sql = "
    UPDATE
      items
    SET
      status = {$status}
    WHERE
      item_id = {$item_id}
    LIMIT 1
  ";
  
  return execute_query($db, $sql);
}

function update_item($db, $item_id) {
    $sql = '';
    

}
// PDO、商品ID、在庫数を受け取り、商品の在庫情報を更新する
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







function get_as_array($dbh, $sql){
    try {
      $stmt = $dbh->prepare($sql);
      $stmt->execute();
      $rows = $stmt->fetchALL();
    }

}


function get_array($dbh, $sql){
    try{
      $stme = $dbh->prepare($sql);
      $stmt->execute();
      $rows = $stmt->fetchall();
    }
}
/**
* 商品の一覧を取得する
*
* @param obj $dbh DBハンドル
* @return array 商品一覧配列データ
*/
function get_goods_table_list($dbh) {
 
  // SQL生成
  $sql = 'SELECT name, price FROM test_products';
  // クエリ実行
  return get_as_array($dbh, $sql);
}

function get_goods_table($dbh){
  $sql = 'SELECT name, price
          FROM test_products';
}



// 1. SELECT文
function get_cart_user_item($dbh, $item_id, $user_id) {
  global $err_msg;
  
  try {
      // カートに追加する商品がすでにカートに入っているかどうか確認する (SELECT文で参照する。)
      $sql = 'SELECT item_id, amount 
              FROM cart 
              WHERE item_id = ? AND user_id = ?';
      // SQL文を実行する準備
      $stmt = $dbh->prepare($sql);
      // SQL文のプレースホルダに値をバインド
      $stmt->bindValue(1, $item_id, PDO::PARAM_INT);
      $stmt->bindValue(2, $user_id, PDO::PARAM_INT);
      // SQLを実行
      $stmt->execute();
      // レコードの取得
      $row = $stmt->fetchAll();
      return $row;   // fetchで取得するのでreturnを書く
  // print_rで中身をチェックする (もし同じidなら$rowに入る)
  // print_r($row); 
  // exit();
  } catch (PDOException $e) {
      $err_msg[] = 'カートの情報が取得できませんでした。理由：' . $e->getMessage();
  }

}


function get_product_list($dbh) {
  global $err_msg; 

  try {
      // ステータスが1(公開)の販売商品のみを一覧で表示
      $sql = 'SELECT product.id, name, price, img, status, stock
              FROM product 
              JOIN item_stock
                  ON product.id = item_stock.stock_id
              WHERE status = 1';
      $stmt = $dbh->prepare($sql);
      $stmt->execute();
      //全て取得するのでfetchALLで、レコードを取得する
      $result = $stmt->fetchALL();
      // $err_msg[] = ($keyword . 'を含む商品は見つかりませんでした。');
      return $result; 
  } catch (PDOException $e) {
      $err_msg[] = '商品を取得できませんでした。';
  }
}

function get_product_list($dbh) {
  global $err_msg;

  try {
    $sql = 'SELECT product.id, name, price, img, status, stock
            FROM product
            JOIN item_stock
                ON product.id = item_stock.stock_id
            WHERE status = 1';
    $stmt = $dbh->prepare($sql);
    $stme->execute();
    $result = $stmt->fetchALL();

    return $result;

  }
}




// execute_query() はPDO($db)とSQL文($sql)、SQL文の中に代入する値($params)を受け取り、SQL文を実行して成功したら実行結果のインスタンスを返す
// 失敗したらset_error()関数を実行しFALSEを返す
// prepare関数を使ってSQL文を実行するようになっているので、SQL文内のメタ文字を実際の値に変換して実行するために$paramsで値を指定している
// クエリはデータベースへの問い合わせという意味
// もしSQL文の中に変動値が入る場合はプレースホルダを使う

// query は1回毎にSQL文を書いて実行するのに使う  queryは戻り値がレコードセットなので、select文等でよく使われる
// prepareとexecuteは同じSQL文で検索条件の値や挿入する値だけを変えながら繰り返し実行する場合に使う

function execute_query($db, $sql, $params = array()){
    try{
      $statement = $db->prepare($sql);
      return $statement->execute($params);
    }catch(PDOException $e){
      set_error('更新に失敗しました。');
    }
    return false;
  }



function execute_query($db, $sql, $param = array()){
    try{
      $statement - $db->prepare($sql);
      return $statement->execute($param);
    }
}

function execute_query($db, $sql, $param = array()) {

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
    return '';

}


// PDO、ユーザー名、パスワードを利用してログイン処理
function login_as($db, $name, $password){
    // PDOとユーザー名からユーザーデータを取得する
    $user = get_user_by_name($db, $name);
    // $userがFALSEまたは$userのパスワードが$passwordでない場合は、FALSEを返す
    if($user === false || $user['password'] !== $password){
      return false;
    }
    // ユーザーIDのセッション情報を定義して、ユーザーデータを返す
    // 第一引数がキーで、第二引数が値
    set_session('user_id', $user['user_id']);
    return $user;
  }

  function login_as($db, $name, $password) {
      // PDOとユーザー名からユーザーデータを取得する
      $user = get_user_by_name($db, $name);


  }
  
  // セッションに値を設定する
  // 第1引数がキーで、第２引数が値
  function set_session($name, $value){
    $_SESSION[$name] = $value;
  }

  
// セッションに値を設定する
// 第1引数がキーで，第2引数が値
function set_session($name, $value){
  $_SESSION[$name] = $value;
}

function set_session($name, $value){
  $_SESSION[$name] = $value;

}
function set_session($name, $value){
  $_SESSION[$name] = $value;

}
// セッションに値を設定する
// 第1引数がキーで、第2引数が値
function set_session($name, $value) {
    $_SESSION[$name] = $value;
}

  function set_session($name, $value){
      $_SESSION[$name] = $value;
  }
  // 配列を設定する
  // 添え字が '__error' のものをさらに配列として（二次元配列）要素を追加して変数 $errorの内容とする
  // $_SESSION['__error'] を配列として、エラー内容[]を追加している
  //（要素数が 0でなければ何らかのエラーがあって、その内容が記録されている）
  function set_error($error){
  // 複数のエラーメッセージを格納する
  // グローバル変数$_SESSIONが連想配列として定義して、連想配列の添え字'__errors'を配列として定義する
  // その配列に変数$errorを追加する
    $_SESSION['__errors'][] = $error;
  }
  
  // エラーがセットされているときは、$errorsを返す
  // もしセッション変数にエラーがないときは、空の配列を返す
  function get_errors(){
    // セッション変数にエラーがセットされているときは$errorsを以下のように定義する
    $errors = get_session('__errors');
    if($errors === ''){
      return array();
    }
    set_session('__errors',  array());
    return $errors;
  }





// MySQLに接続して（その接続した）PDOクラスのインスタンスを返す
function get_db_connect(){
  // MySQL用のDSN文字列
$dsn = 'mysql:dbname='. DB_NAME .';host='. DB_HOST .';charset='.DB_CHARSET;
  // try~catchを使うことでエラー時の処理をcatchの中にまとめられる
try {
    // データベースに接続する
    // PHPでデータベースにアクセスする際にPDOを利用する
    // new PDOのところは()内の条件でPDOを利用できる状態にする命令と考える
    $dbh = new PDO($dsn, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'));
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    exit('接続できませんでした。理由：'.$e->getMessage() );
}
return $dbh;
}

// PDOを利用して、商品情報を返す
function get_all_items($db){
    return get_items($db);
}

// $dbと$user_idを受け取り、itemsテーブルから商品情報を全件取得する
function get_items($db, $is_open = false){
    // 商品を取得するSQL文を構築する
    // もし商品のステータスが1の商品の場合はステータスが1の商品のみを全て取得する
    $sql = '
SELECT
        item_id, 
        name,
        stock,
        price,
        image,
        status
FROM
        items
    ';
    
    if($is_open === true){
$sql .= '
        WHERE status = 1
';
    }

    return fetch_all_query($db, $sql);
}

// 商品のステータスが1ならTRUE、そうでなければFALSEを返す
function is_open($item){
    return $item['status'] === 1;
}

// $db、$sql、$paramを受け取り、プリペアドステートメントを利用して
// 結果が複数存在する際にそれらの結果を全て取得する
// データ取得に失敗した場合にはFALSEを返す
function fetch_all_query($db, $sql, $params = array()){
    try{
        $statement = $db->prepare($sql);
        $statement->execute($params);
        return $statement->fetchAll();
    }catch(PDOException $e){
        set_error('データ取得に失敗しました。');
    }
    return false;
    }

// $dbと$login_user_idを受け取ったget_user関数を返す
// PDOとセッション情報(ユーザーID)を利用してユーザー情報を返す
function get_login_user($db){
    // 変数login_user_idをセッションのユーザーIDと定義する
    $login_user_id = get_session('user_id');

    return get_user($db, $login_user_id);
}

// セッションのユーザーIDが空でなければ、TRUE 空であればFALSEを返す
function is_logined(){
    return get_session('user_id') !== '';
}

// もしセッション変数にユーザーの名前($name)がセットされているなら、セッション情報を取得する
// セットされていないときは空文字を返す
function get_session($name){
if(isset($_SESSION[$name]) === true){
    return $_SESSION[$name];
};
return '';
}

// $dbと$user_idを受け取り、usersテーブルから1件のユーザー情報を取得する
function get_user($db, $user_id){
// $user_idを1件取得するSQL文を構築する
    $sql = "
SELECT
        user_id, 
        name,
        password,
        type
FROM
        users
WHERE
        user_id = {$user_id}
LIMIT 1
    ";
  // SQL文を実行して、fetch_query関数を返す
    return fetch_query($db, $sql);
}

// $db、$sql、$paramを受け取り、プリペアドステートメントを利用してデータを配列として1行のみ取得する
// データ取得に失敗した場合はFALSEを返す
// プリペアドステートメントとはSQL文で値がいつでも変更できるように変更する箇所だけ
// 変数のようにした命令文を作る仕組み
function fetch_query($db, $sql, $params = array()){
    try{
        $statement = $db->prepare($sql);
        $statement->execute($params);
        return $statement->fetch();
    }catch(PDOException $e){
        set_error('データ取得に失敗しました。');
    }
    return false;
    }

    // 添え字が '__error' のものをさらに配列として（二次元配列）要素を追加して変数 $errorの内容とする
// $_SESSION['__error'] を配列として、エラー内容[]を追加している
//（要素数が 0でなければ何らかのエラーがあって、その内容が記録されている）
function set_error($error){
    $_SESSION['__errors'][] = $error;
}

// セッション情報（ユーザー名）を$valueと定義する
function set_session($name, $value){
    $_SESSION[$name] = $value;
}

// ユーザーの種類が管理者であれば TRUE、そうでなければ FALSEを返す
function is_admin($user){
    return $user['type'] === USER_TYPE_ADMIN;
}





// 文字列を受け取り、特殊文字をHTMLエンティティに変換してから返す
function h($str){
    return htmlspecialchars($str,ENT_QUOTES,'UTF-8');
  }

// 文字列を受け取
function h($str){
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}



// execute_query() はPDO($db)とSQL文($sql)、SQL文の中に代入する値($params)を受け取り、SQL文を実行して成功したら実行結果のインスタンスを返す
// 失敗したらset_error()関数を実行しFALSEを返す
// prepare関数を使ってSQL文を実行するようになっているので、SQL文内のメタ文字を実際の値に変換して実行するために$paramsで値を指定している
// クエリはデータベースへの問い合わせという意味
// もしSQL文の中に変動値が入る場合はプレースホルダを使う
// queryはselect文等でよく使われる
function execute_query($db, $sql, $params = array()){
    try{
      $statement = $db->prepare($sql);
      return $statement->execute($params);
    }catch(PDOException $e){
      set_error('更新に失敗しました。');
    }
    return false;
}

// queryはselect文でよく使う
function execute_query($db, $sql, $param = []) {
    try{
        $statement = $db->prepare($sql);
        return $statement->execute($param);
    }catch(){
        set_error();
    }
    return false;
}


// 画像関連の処理
$image = get_file('image');

function get_file($name){
    if(isset($_FILES[$name]) === true){
      return $_FILES[$name];
    };
    return array();
}

// $fileを受け取り、アップロードする画像のファイル名を取得する
// get_random_string() . '.' . $extを返す
// 保存する新しいファイル名を生成する（ユニークな値を設定する）
function get_upload_filename($file){
    // 画像のバリデーション処理に失敗した場合は空文字を返す
    if(is_valid_upload_image($file) === false){
      return '';
    }
    // 画像であるかどうかをexif_imagetype()を使って判別する
    $mimetype = exif_imagetype($file['tmp_name']);
    // 画像の拡張子を取得する
    $ext = PERMITTED_IMAGE_TYPES[$mimetype];
    return get_random_string() . '.' . $ext;
}

// より安全なファイル名にするために保存する新しいファイル名を生成する（ユニークな値を設定する）
function get_random_string($length = 20){
    return substr(base_convert(hash('sha256', uniqid()), 16, 36), 0, $length);
}

// HTTP POSTでファイルがアップロードされたかどうかチェックしてTRUEを返す
function is_valid_upload_image($image){
    // ファイルの形式が異なる場合はエラーメッセージを表示してFALSEを返す
    if(is_uploaded_file($image['tmp_name']) === false){
      set_error('ファイル形式が不正です。');
      return false;
    }
    // 画像であるかどうかをexif_imagetype()を使って判別して
    // 画像でない場合はエラーメッセージを表示してFALSEを返す
    $mimetype = exif_imagetype($image['tmp_name']);
    if( isset(PERMITTED_IMAGE_TYPES[$mimetype]) === false ){
      set_error('ファイル形式は' . implode('、', PERMITTED_IMAGE_TYPES) . 'のみ利用可能です。');
      return false;
    }
    return true;
}

define('PERMITTED_IMAGE_TYPES', array(
    IMAGETYPE_JPEG => 'jpg',
    IMAGETYPE_PNG => 'png',
  ));


  $new_img_filename = '';   // アップロードした新しい画像ファイル名
  
      
      // 指定の拡張子であるかどうかチェック
      if ($extension === 'jpg' || $extension === 'jpeg') {
        // 保存する新しいファイル名の生成（ユニークな値を設定する）
        $new_img_filename = sha1(uniqid(mt_rand(), true)). '.' . $extension;
        // 同名ファイルが存在するかどうかチェック
        if (is_file($img_dir . $new_img_filename) !== TRUE) {
          // アップロードされたファイルを指定ディレクトリに移動して保存
          if (move_uploaded_file($_FILES['new_img']['tmp_name'], $img_dir . $new_img_filename) !== TRUE) {
              $err_msg[] = 'ファイルアップロードに失敗しました';
          }
        } else {
          $err_msg[] = 'ファイルアップロードに失敗しました。再度お試しください。';
        }
      } else {
        $err_msg[] = 'ファイル形式が異なります。画像ファイルはJPEGのみ利用可能です。';
      }
    } else {
      $err_msg[] = 'ファイルを選択してください';
    }
  }
   