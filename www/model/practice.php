<?php
// 下書き用のファイル

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