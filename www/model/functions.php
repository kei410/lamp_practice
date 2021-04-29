<?php
// 汎用関数ファイル

// $varに関する情報を返して、以降の処理が実行されないようにする
function dd($var){
  var_dump($var);
  exit();
}

// $urlに移動して、以降の処理は実行されないようにする
function redirect_to($url){
  header('Location: ' . $url);
  exit;
}

// もしGETメソッドからの変数の存在が確認できるときは、GETで送信されたデータ（ユーザー名）を返す
// 変数が設定されていないときは空文字を返す
function get_get($name){
  if(isset($_GET[$name]) === true){
    return $_GET[$name];
  };
  return '';
}

// もしPOSTメソッドからの変数の存在が確認できるときは、POSTで送信されたデータ（ユーザー名）を返す
// 変数が設定されていないときは空文字を返す
function get_post($name){
  if(isset($_POST[$name]) === true){
    return $_POST[$name];
  };
  return '';
}

// $nameを受け取り、空配列を返す
// もしファイルアップロード変数$FILESにセットされているなら、アップロードされたファイル情報を返す
function get_file($name){
  if(isset($_FILES[$name]) === true){
    return $_FILES[$name];
  };
  return array();
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
// 第一引数がキーで、第二引数が値
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

// セッション変数にエラーがセットされていて、エラーの数が0以外のときはTRUEを返す
// それ以外はFALSEを返す
function has_error(){
  return isset($_SESSION['__errors']) && count($_SESSION['__errors']) !== 0;
}

// メッセージ変数を受け取ってセッションデータに代入する
// 添え字が '__message' のものをさらに配列として（二次元配列）要素を追加して変数 $messageの内容とする
// $_SESSION['__message'] を配列として、メッセージ内容[]を追加している
//（要素数が 0でなければ何らかのメッセージがあって、その内容が記録されている）
function set_message($message){
  $_SESSION['__messages'][] = $message;
}

// メッセージがセットされているときは、$messagesを返す
// もしセッション変数にメッセージがないときは、空の配列を返す
function get_messages(){
  // セッション変数にメッセージがセットされているときは、$messagesを以下のように定義する
  $messages = get_session('__messages');
  if($messages === ''){
    return array();
  }
  set_session('__messages',  array());
  return $messages;
}

// セッションのユーザーIDが空でなければ、TRUE 空であればFALSEを返す
function is_logined(){
  return get_session('user_id') !== '';
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

// より安全なファイル名にするために、保存する新しいファイル名を生成する（ユニークな値を設定する）
function get_random_string($length = 20){
  return substr(base_convert(hash('sha256', uniqid()), 16, 36), 0, $length);
}

// 画像とファイル名を受け取り、アップロードされたファイルを指定ディレクトリに移動して保存
function save_image($image, $filename){
  return move_uploaded_file($image['tmp_name'], IMAGE_DIR . $filename);
}

// ファイル名を受け取り、ファイル名が既に存在していて、その画像を削除するときにはTRUEを返し
// それ以外はFALSEを返す
function delete_image($filename){
  if(file_exists(IMAGE_DIR . $filename) === true){
    unlink(IMAGE_DIR . $filename);
    return true;
  }
  return false;
  
}

// $string（文字列）、$minimum_length、$maximum_lengthを受け取り、文字数をチェック
// 文字列の文字数を返す
function is_valid_length($string, $minimum_length, $maximum_length = PHP_INT_MAX){
  $length = mb_strlen($string);
  return ($minimum_length <= $length) && ($length <= $maximum_length);
}

// バリデーション処理とは、入力された値が適切かどうかをチェックすること
// $string(文字列)を受け取り、バリデーション処理（英数字）を返す
function is_alphanumeric($string){
  return is_valid_format($string, REGEXP_ALPHANUMERIC);
}

// $string(文字列)を受け取り、バリデーション処理（0以上の整数）を返す
function is_positive_integer($string){
  return is_valid_format($string, REGEXP_POSITIVE_INTEGER);
}

// $stringと$formatを受け取り、バリデーション処理を実行して1を返す
// バリデーション処理とは、入力された値が適切かどうかをチェックすること
function is_valid_format($string, $format){
  return preg_match($format, $string) === 1;
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

// 文字列を受け取り、特殊文字をHTMLエンティティに変換してから返す
function h($str){
  return htmlspecialchars($str,ENT_QUOTES,'UTF-8');
}


// CSRF対策 (トークン)
// 1. トークンの生成
// トークンは合言葉や鍵の意味
function get_csrf_token(){
  // get_random_string()はユーザー定義関数。
  $token = get_random_string(30);
  // set_session()はユーザー定義関数。
  // CSRFのトークンを生成してセッションに格納する
  /* dd($token); */
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

// リクエストメソッドを取得
function get_request_method(){
  return $_SERVER['REQUEST_METHOD'];
}
