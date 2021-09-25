<?php

/**
* 税込み価格へ変換する(端数は切り上げ)
* @param str  $price 税抜き価格
* @return str 税込み価格
*/
function price_before_tax($price) {
  return ceil($price * TAX);
}

/**
* 商品の値段を税込みに変換する(配列)
* @param array  $assoc_array 税抜き商品一覧配列データ
* @return array 税込み商品一覧配列データ
*/
function price_before_tax_assoc_array($assoc_array) {

  foreach ($assoc_array as $key => $value) {
    // 税込み価格へ変換(端数は切り上げ)
    $assoc_array[$key]['price'] = price_before_tax($assoc_array[$key]['price']);
  }

  return $assoc_array;
}

/**
* 特殊文字をHTMLエンティティに変換する
* @param str  $str 変換前文字
* @return str 変換後文字
*/
function entity_str($str) {
  return htmlspecialchars($str, ENT_QUOTES, HTML_CHARACTER_SET);
}

/**
* 特殊文字をHTMLエンティティに変換する(2次元配列の値)
* @param array  $assoc_array 変換前配列
* @return array 変換後配列
*/
function entity_assoc_array($assoc_array) {

  foreach ($assoc_array as $key => $value) {

    foreach ($value as $keys => $values) {
      // 特殊文字をHTMLエンティティに変換
      $assoc_array[$key][$keys] = entity_str($values);
    }
  }

  return $assoc_array;
}

/**
* DBハンドルを取得
* @return obj $dbh DBハンドル
*/
function get_db_connect() {

  try {
    // データベースに接続
    $dbh = new PDO(DNS, DB_USER, DB_PASSWD);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
  } catch (PDOException $e) {throw $e;
    throw $e;
  }

  return $dbh;
}


/**
* クエリを実行しその結果を配列で取得する
*
* @param obj  $dbh DBハンドル
* @param str  $sql SQL文
* @return array 結果配列データ
*/
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

/**
* 商品の一覧を取得する
*
* @param obj $dbh DBハンドル
* @return array 商品一覧配列データ
*/
function get_goods_table_list($dbh) {

  try {
    // SQL文を作成
    $sql = 'SELECT goods_name, price FROM goods_table';
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

/**
* insertを実行する
*
* @param obj $dbh DBハンドル
* @param str SQL文
* @return bool
*/
function insert_db($dbh, $sql) {

  try {
    // SQL文を実行する準備
    $stmt = $dbh->prepare($sql);
    // SQLを実行
    $stmt->execute();

  } catch (PDOException $e) {
    throw $e;
  }

  return true;
}

/**
* 新規商品を追加する
*
* @param obj $dbh DBハンドル
* @param str $goods_name 商品名
* @param int $price 価格
* @return bool
*/
function insert_goods_table($dbh, $goods_name, $price) {

  try {
  // SQL生成
  $sql = 'INSERT INTO goods_table(goods_name, price) VALUES(\'' . $goods_name . '\', ' . $price . ')';

    // SQL文を実行する準備
    $stmt = $dbh->prepare($sql);
    // SQL文のプレースホルダに値をバインド
    $stmt->bindValue(1, $goods_name,    PDO::PARAM_STR);
    $stmt->bindValue(2, $price, PDO::PARAM_INT);
    $stmt->bindValue(3, $img, PDO::PARAM_STR);
    $stmt->bindValue(4, $date, PDO::PARAM_STR);
    $stmt->bindValue(5, $date, PDO::PARAM_STR);
    $stmt->bindValue(6, $status, PDO::PARAM_INT);
    // SQLを実行
    $stmt->execute();
    // レコードの取得
    $rows = $stmt->fetchAll();

  } catch (PDOException $e) {
    throw $e;
  }

}

/**
* リクエストメソッドを取得
* @return str GET/POST/PUTなど
*/
function get_request_method() {
  return $_SERVER['REQUEST_METHOD'];
}

/**
* POSTデータを取得
* @param str $key 配列キー
* @return str POST値
*/
function get_post_data($key) {

  $str = '';

  if (isset($_POST[$key]) === TRUE) {
    $str = $_POST[$key];
  }

  return $str;
}


/**
* 名前が正しく入力されているかチェック
* @param str $user_name 名前
* @return mixed
*/
function check_user_name($user_name) {

  if (mb_strlen($user_name) === 0){
    return '名前を入力してください';

  } elseif (mb_strlen($user_name) > 20){
    return '名前は20文字以内で入力してください';

  } else {
    return true;

  }
}


/**
* ひとことが正しく入力されているかチェック
* @param str $user_comment ひとこと
* @return mixed
*/
function check_user_comment($user_comment) {

  if (mb_strlen($user_comment) === 0){
    return 'ひとことを入力してください';

  } elseif (mb_strlen($user_comment) > 100){
    return 'ひとことは100文字以内で入力してください';

  } else {
    return true;

  }
}


/**
* 自販機の商品一覧を取得する
*
* @param obj $dbh DBハンドル
* @return array 自販機の商品一覧配列データ
*/
function get_drink_list($dbh) {

  // SQL生成
  $sql = 'SELECT drink_master.drink_id, drink_master.drink_name, drink_master.price,
            drink_master.img, drink_master.status, drink_stock.stock
          FROM drink_master JOIN drink_stock
          ON  drink_master.drink_id = drink_stock.drink_id';

  // クエリ実行
  return get_as_array($dbh, $sql);
}


/**
* 自販機から特定の商品情報を取得する
*
* @param obj $dbh DBハンドル
* @return array 自販機の特定の商品配列データ
*/
function get_drink_data($dbh, $drink_id) {

  // SQL生成
  $sql = 'SELECT drink_master.drink_id, drink_master.drink_name, drink_master.price,
                drink_master.img, drink_stock.stock, drink_master.status
          FROM drink_master JOIN drink_stock
          ON  drink_master.drink_id = drink_stock.drink_id
          WHERE drink_master.drink_id = ' . $drink_id;

  // クエリ実行
  return get_as_array($dbh, $sql);
}


/**
* 自販機の商品在庫数を変更する
*
* @param obj $dbh DBハンドル
* @param int $drink_id 商品ID
* @param int $stock 在庫数
* @param str $date 日付
* @return bool
*/
function update_drink_stock($dbh, $drink_id, $stock, $now_date) {

  try {
    // SQL生成
    $sql = 'UPDATE drink_stock SET stock = ?, update_datetime = ? WHERE drink_id = ? LIMIT 1';
    // SQL文を実行する準備
    $stmt = $dbh->prepare($sql);
    // SQL文のプレースホルダに値をバインド
    $stmt->bindValue(1, $stock,    PDO::PARAM_INT);
    $stmt->bindValue(2, $now_date, PDO::PARAM_STR);
    $stmt->bindValue(3, $drink_id, PDO::PARAM_INT);
    // SQLを実行
    $stmt->execute();
    // レコードの取得
    $rows = $stmt->fetchAll();

  } catch (PDOException $e) {
    throw $e;
  }

}


/**
* 自販機の商品購入履歴を追加する
*
* @param obj $dbh DBハンドル
* @param int $drink_id 商品ID
* @param str $date 日付
* @return bool
*/
function insert_drink_history($dbh, $drink_id) {

  try {
    // SQL生成
    $sql = 'INSERT INTO drink_history(drink_id) VALUES(' . $drink_id . ')';    // SQL文を実行する準備
    $stmt = $dbh->prepare($sql);
    // SQL文のプレースホルダに値をバインド
    $stmt->bindValue(1, $drink_id,    PDO::PARAM_INT);
    // SQLを実行
    $stmt->execute();
    // レコードの取得
    $rows = $stmt->fetchAll();

  } catch (PDOException $e) {
    throw $e;
  }

}

/**
* 自販機の商品ステータスを変更する
*
* @param obj $dbh DBハンドル
* @param int $drink_id 商品ID
* @param int $change_status ステータス
* @param str $date 日付
* @return bool
*/
function update_drink_master_status($dbh, $drink_id, $change_status, $now_date) {

  // SQL生成
  try {
    // SQL生成
    $sql = 'UPDATE drink_master SET status = ?, update_datetime = ? WHERE drink_id = ? LIMIT 1';
    // SQL文を実行する準備
    $stmt = $dbh->prepare($sql);
    // SQL文のプレースホルダに値をバインド
    $stmt->bindValue(1, $change_status, PDO::PARAM_INT);
    $stmt->bindValue(2, $now_date, PDO::PARAM_STR);
    $stmt->bindValue(3, $drink_id, PDO::PARAM_INT);
    // SQLを実行
    $stmt->execute();
    // レコードの取得
    $rows = $stmt->fetchAll();

  } catch (PDOException $e) {
    throw $e;
  }

}


/**
* 自販機に商品を追加する
*
* @param obj $dbh DBハンドル
* @param str $new_name 商品ID
* @param int $new_price 価格
* @param int $new_stock 在庫数
* @param int $new_img 画像
* @param int $date 日付
* @param int $new_status ステータス
* @return bool
*/
function insert_drink_data($dbh, $new_name, $new_price, $new_stock, $new_img, $new_status, $now_date) {

  // トランザクション開始
  $dbh->beginTransaction();

  try {
    // SQL文を作成
    $sql = 'INSERT INTO drink_master (drink_name, price, img, status, create_datetime) VALUES (?, ?, ?, ?, ?)';
    // SQL文を実行する準備
    $stmt = $dbh->prepare($sql);
    // SQL文のプレースホルダに値をバインド
    $stmt->bindValue(1, $new_name,    PDO::PARAM_STR);
    $stmt->bindValue(2, $new_price,  PDO::PARAM_INT);
    $stmt->bindValue(3, $new_img,    PDO::PARAM_STR);
    $stmt->bindValue(4, $new_status,  PDO::PARAM_INT);
    $stmt->bindValue(5, $now_date, PDO::PARAM_STR);
    // SQLを実行
    $stmt->execute();

    // INSERTされたデータのIDを取得
    $drink_id = $dbh->lastInsertId('drink_id');

    // SQL文を作成
    $sql = 'INSERT INTO drink_stock (drink_id, stock) VALUES (?, ?)';
    // SQL文を実行する準備
    $stmt = $dbh->prepare($sql);
    // SQL文のプレースホルダに値をバインド
    $stmt->bindValue(1, $drink_id,    PDO::PARAM_INT);
    $stmt->bindValue(2, $new_stock,  PDO::PARAM_STR);
    // SQLを実行
    $stmt->execute();
    // コミット
    $dbh->commit();

  } catch (PDOException $e) {
    // ロールバック処理
    $dbh->rollback();
    // 例外をスロー
    throw $e;
  }
}


/**
* 自販機から特定の商品情報を購入する
*
* @param obj $dbh DBハンドル
* @return mix
*/
function purchase_drink($dbh, $drink_id, $stocks, $now_date) {

  // トランザクション開始
  $dbh->beginTransaction();

  try {
    // 購入したドリンクの在庫を減らす
    update_drink_stock($dbh, $drink_id, $stocks - 1, $now_date);
    // 購入履歴テーブルに保存する
    insert_drink_history($dbh, $drink_id);

    // コミット
    $dbh->commit();

  } catch (PDOException $e) {
    // ロールバック処理
    $dbh->rollback();
    // 例外をスロー
    throw $e;
  }
}

/**
* 数値かチェック
* @param int $number 数値
* @return bool
*/
function check_number($number) {

  if (preg_match('/\A\d+\z/', $number) === 1 ) {
    return true;
  } else {
    return false;
  }
}

/**
* 前後の空白を削除
* @param str $str 文字列
* @return str 前後の空白を削除した文字列
*/
function trim_space($str) {
  return preg_replace('/\A[　\s]*|[　\s]*\z/u', '', $str);
}
