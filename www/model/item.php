<?php
// 商品関連タ

// 「汎用関数、デーベース関連」の2つ

// 汎用関数ファイルを読み込む
require_once MODEL_PATH . 'functions.php';
// データベースに関する関数ファイルを読み込む
require_once MODEL_PATH . 'db.php';

// PDOとSQL文を利用して、itemsテーブルから1件の商品情報を取得する
function get_item($db, $item_id){
// $item_idを1件取得するSQL文を構築する
  $sql = "
    SELECT
      item_id, 
      name,
      stock,
      price,
      image,
      status
    FROM
      items
    WHERE
      item_id = ?
  ";

  return fetch_query($db, $sql, array($item_id));
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

// PDOを利用して、商品のデータを返す
function get_all_items($db){
  return get_items($db);
}

// PDOを利用して、ステータスが１の商品の情報を返す
function get_open_items($db){
  return get_items($db, true);
}

// PDO、商品名、価格、在庫数、ステータス、画像、画像ファイル名を受け取って、商品を
// 新規登録する処理(トランザクション処理)を実行する
// もし入力値チェック（バリデーション処理）に失敗したらFALSEを返す
function regist_item($db, $name, $price, $stock, $status, $image){
// アップロードする画像ファイル名を取得する
  $filename = get_upload_filename($image);
  if(validate_item($name, $price, $stock, $filename, $status) === false){
    return false;
  }
  return regist_item_transaction($db, $name, $price, $stock, $status, $image, $filename);
}

// PDO、商品名、価格、在庫数、ステータス、商品画像、ファイル名を受け取り、商品登録のトランザクション処理を実行
// トランザクション処理 (商品情報の追加と在庫の追加)に成功したら、TRUEを返して
// トランザクション処理に失敗したらFALSEを返す
function regist_item_transaction($db, $name, $price, $stock, $status, $image, $filename){
  $db->beginTransaction();
  if(insert_item($db, $name, $price, $stock, $filename, $status) 
    && save_image($image, $filename)){
    $db->commit();
    return true;
  }
  $db->rollback();
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



// PDO、商品ID、在庫数を受け取り、商品の在庫情報を更新する
function update_item_stock($db, $item_id, $stock){
  $sql = "
    UPDATE
      items
    SET
      stock = ?      
    WHERE
      item_id = ?   
    LIMIT 1
  ";

  return execute_query($db, $sql , array($stock, $item_id));
}


// PDO、商品IDを受け取り、商品削除のトランザクション処理を実行する
// トランザクション処理 (商品情報の削除と在庫情報を削除)に成功したら、TRUEを返して
// トランザクション処理に失敗したらFALSEを返す
function destroy_item($db, $item_id){
  $item = get_item($db, $item_id);
  if($item === false){
    return false;
  }
  $db->beginTransaction();
  if(delete_item($db, $item['item_id'])
    && delete_image($item['image'])){
    $db->commit();
    return true;
  }
  $db->rollback();
  return false;
}

// PDO、商品IDを利用して、データベースから商品情報を削除する
function delete_item($db, $item_id){
  $sql = "
    DELETE FROM
      items
    WHERE
      item_id = ?
    LIMIT 1
  ";
  
  return execute_query($db, $sql, array($item_id));
}


// 非DB（非データベース）の関数


// $itemを受け取り、ステータスが1の商品を返す
function is_open($item){
  return $item['status'] === 1;
}

// 商品名、価格、在庫数、画像ファイル名、ステータスを受け取り、それらの入力値をチェックする（バリデーション処理）
// バリデーション処理後のデータを返す
function validate_item($name, $price, $stock, $filename, $status){
  $is_valid_item_name = is_valid_item_name($name);
  $is_valid_item_price = is_valid_item_price($price);
  $is_valid_item_stock = is_valid_item_stock($stock);
  $is_valid_item_filename = is_valid_item_filename($filename);
  $is_valid_item_status = is_valid_item_status($status);

  return $is_valid_item_name
    && $is_valid_item_price
    && $is_valid_item_stock
    && $is_valid_item_filename
    && $is_valid_item_status;
}

// 商品名を受け取り、$is_validを返す （TRUEかFALSE）
// もし商品名が1文字以上100文字以下なら、TRUEでそれ以外はset_error()関数を実行してFALSEを返す
function is_valid_item_name($name){
  $is_valid = true;
  if(is_valid_length($name, ITEM_NAME_LENGTH_MIN, ITEM_NAME_LENGTH_MAX) === false){
    set_error('商品名は'. ITEM_NAME_LENGTH_MIN . '文字以上、' . ITEM_NAME_LENGTH_MAX . '文字以内にしてください。');
    $is_valid = false;
  }
  return $is_valid;
}

// 価格を受け取り、$is_validを返す （TRUEかFALSE）
// 価格が0以上の整数ならTRUE、それ以外はFALSEを返す
// それ以外はset_error()関数を実行してFALSEを返す
function is_valid_item_price($price){
  $is_valid = true;
  if(is_positive_integer($price) === false){
    set_error('価格は0以上の整数で入力してください。');
    $is_valid = false;
  }
  return $is_valid;
}

// 在庫数を受け取り、$is_validを返す （TRUEかFALSE）
// 在庫数が0以上の整数ならTRUE、それ以外はFALSEを返す
// それ以外はset_error()関数を実行してFALSEを返す
function is_valid_item_stock($stock){
  $is_valid = true;
  if(is_positive_integer($stock) === false){
    set_error('在庫数は0以上の整数で入力してください。');
    $is_valid = false;
  }
  return $is_valid;
}

// ファイル名を受け取り、$is_validを返す （TRUEかFALSE）
// もしファイル名が設定されていないときはFALSE
function is_valid_item_filename($filename){
  $is_valid = true;
  if($filename === ''){
    $is_valid = false;
  }
  return $is_valid;
}

// 商品ステータスを受け取り、$is_validを返す （TRUEかFALSE）
// もし商品ステータスが0、1以外ならFALSE
function is_valid_item_status($status){
  $is_valid = true;
  if(isset(PERMITTED_ITEM_STATUSES[$status]) === false){
    $is_valid = false;
  }
  return $is_valid;
}