<?php
// ユーザー関連
// 「汎用関数、データベース関連」の2つ


// 汎用関数ファイルを読み込む
require_once MODEL_PATH . 'functions.php';
// データベースに関する関数ファイルを読み込む
require_once MODEL_PATH . 'db.php';

// PDOとユーザーIDを受け取り、usersテーブルから1件のユーザー情報を取得する
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
// SQL文を実行して、fetch_query()関数を返す
  return fetch_query($db, $sql);
}

// PDOとユーザー名を受け取り、usersテーブルから1件のユーザー情報を取得する
function get_user_by_name($db, $name){
  $sql = "
    SELECT
      user_id, 
      name,
      password,
      type
    FROM
      users
    WHERE
      name = '{$name}'
    LIMIT 1
  ";

  return fetch_query($db, $sql);
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

// PDOを利用してログインしているユーザー情報を返す
function get_login_user($db){
  // ユーザーIDを利用して、セッションを取得する
  $login_user_id = get_session('user_id');

  return get_user($db, $login_user_id);
}

// PDOとユーザー名、パスワード、$password_confirmation（確認用パスワード）を利用して、
// 新規登録するユーザーの情報を追加する関数を実行する
// もしバリデーション処理に失敗したらFALSEを返す
function regist_user($db, $name, $password, $password_confirmation) {
  if( is_valid_user($name, $password, $password_confirmation) === false){
    return false;
  }
  
  return insert_user($db, $name, $password);
}

// ユーザーの種類が管理者(admin、admin)であれば TRUE、そうでなければ FALSEを返す
function is_admin($user){
  return $user['type'] === USER_TYPE_ADMIN;
}

// ユーザー名、パスワード、 $password_confirmation(確認用パスワード)を受け取り、
// バリデーション処理後のユーザー名とパスワードを返す
function is_valid_user($name, $password, $password_confirmation){
  // 短絡評価を避けるために一旦代入する
  $is_valid_user_name = is_valid_user_name($name);
  $is_valid_password = is_valid_password($password, $password_confirmation);
  return $is_valid_user_name && $is_valid_password ;
}

// バリデーション処理
// ユーザー名を受け取り、TRUEを返す
// 文字数の条件を満たさない、半角英数字でない場合はFALSEを返す
function is_valid_user_name($name) {
  $is_valid = true;
  if(is_valid_length($name, USER_NAME_LENGTH_MIN, USER_NAME_LENGTH_MAX) === false){
    set_error('ユーザー名は'. USER_NAME_LENGTH_MIN . '文字以上、' . USER_NAME_LENGTH_MAX . '文字以内にしてください。');
    $is_valid = false;
  }
  if(is_alphanumeric($name) === false){
    set_error('ユーザー名は半角英数字で入力してください。');
    $is_valid = false;
  }
  return $is_valid;
}

// バリデーション処理
// パスワードと$password_confirmationを受け取り、TRUEを返す
// 文字数の条件を満たさない、半角英数字でない、パスワードがパスワード(確認用)と一致しない場合はFALSEを返す
function is_valid_password($password, $password_confirmation){
  $is_valid = true;
  if(is_valid_length($password, USER_PASSWORD_LENGTH_MIN, USER_PASSWORD_LENGTH_MAX) === false){
    set_error('パスワードは'. USER_PASSWORD_LENGTH_MIN . '文字以上、' . USER_PASSWORD_LENGTH_MAX . '文字以内にしてください。');
    $is_valid = false;
  }
  if(is_alphanumeric($password) === false){
    set_error('パスワードは半角英数字で入力してください。');
    $is_valid = false;
  }
  if($password !== $password_confirmation){
    set_error('パスワードがパスワード(確認用)と一致しません。');
    $is_valid = false;
  }
  return $is_valid;
}

// PDOとユーザー名、パスワードを利用して、新規ユーザーのユーザー名とパスワードをデータベースに追加する
function insert_user($db, $name, $password){
  // 新規ユーザーのユーザー名とパスワードを追加するためのSQL文を構築する
  $sql = "
    INSERT INTO
      users(name, password)
    VALUES ('{$name}', '{$password}');
  ";

  return execute_query($db, $sql);
}

