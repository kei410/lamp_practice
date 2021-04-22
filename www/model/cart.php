<?php 
// カート関連

// 「汎用関数、データベース関連」の2つ

// 汎用関数ファイルの読み込み
require_once MODEL_PATH . 'functions.php';
// データベースに関する関数ファイルを読み込む
require_once MODEL_PATH . 'db.php';


// PDOとユーザーIDを利用して、カートに追加するために必要なデータを全て取得する
function get_user_carts($db, $user_id){
  $sql = "
    SELECT
      items.item_id,
      items.name,
      items.price,
      items.stock,
      items.status,
      items.image,
      carts.cart_id,
      carts.user_id,
      carts.amount
    FROM
      carts
    JOIN
      items
    ON
      carts.item_id = items.item_id
    WHERE
      carts.user_id = {$user_id}
  ";
  return fetch_all_query($db, $sql);
}

// PDOとユーザーIDと商品IDを利用して、カートに追加するために必要なデータを1件だけ取得する
function get_user_cart($db, $user_id, $item_id){
  $sql = "
    SELECT
      items.item_id,
      items.name,
      items.price,
      items.stock,
      items.status,
      items.image,
      carts.cart_id,
      carts.user_id,
      carts.amount
    FROM
      carts
    JOIN
      items
    ON
      carts.item_id = items.item_id
    WHERE
      carts.user_id = {$user_id}
    AND
      items.item_id = {$item_id}
  ";

  return fetch_query($db, $sql);

}

// PDO、ユーザーID、商品IDを受け取り、商品をカートに追加する 
// 既に同じ商品(商品ID)があれば、個数だけ更新する)
function add_cart($db, $user_id, $item_id ) {
  $cart = get_user_cart($db, $user_id, $item_id);
  if($cart === false){
    return insert_cart($db, $user_id, $item_id);
  }
  return update_cart_amount($db, $cart['cart_id'], $cart['amount'] + 1);
}

// PDO、ユーザーID、商品ID、数量を受け取り、商品をcartsテーブルに追加する
function insert_cart($db, $user_id, $item_id, $amount = 1){
  $sql = "
    INSERT INTO
      carts(
        item_id,
        user_id,
        amount
      )
    VALUES({$item_id}, {$user_id}, {$amount})
  ";

  return execute_query($db, $sql);
}

// PDO、カートID、数量を受け取り、cartsテーブルの個数を更新する
function update_cart_amount($db, $cart_id, $amount){
  $sql = "
    UPDATE
      carts
    SET
      amount = {$amount}
    WHERE
      cart_id = {$cart_id}
    LIMIT 1
  ";
  return execute_query($db, $sql);
}

// PDOとカートIDを利用して、cartsテーブルから商品を削除する
function delete_cart($db, $cart_id){
  $sql = "
    DELETE FROM
      carts
    WHERE
      cart_id = {$cart_id}
    LIMIT 1
  ";

  return execute_query($db, $sql);
}

// PDOとカート変数を受け取り、購入処理を実行して、cartsテーブルのデータを削除する
// カートのバリデーション処理に失敗した場合はFALSEを返す
function purchase_carts($db, $carts){
  if(validate_cart_purchase($carts) === false){
    return false;
  }
  // foreachで1商品ずつ取り出し、該当する在庫数を減算する
  foreach($carts as $cart){
    // 在庫テーブルを更新する
    // カートの数量を使って商品の在庫数を減算する
    if(update_item_stock(
        $db, 
        $cart['item_id'], 
        $cart['stock'] - $cart['amount']
      ) === false){
      set_error($cart['name'] . 'の購入に失敗しました。');
    }
  }
  
  delete_user_carts($db, $carts[0]['user_id']);
}

// PDO、ユーザーIDを利用して、cartsテーブルのデータを削除する
function delete_user_carts($db, $user_id){
  $sql = "
    DELETE FROM
      carts
    WHERE
      user_id = {$user_id}
  ";

  execute_query($db, $sql);
}

// カート変数を受け取り、カート内の商品をforeachを使って合計金額を計算して
// カート内の合計金額を返す
function sum_carts($carts){
  $total_price = 0;
  foreach($carts as $cart){
    $total_price += $cart['price'] * $cart['amount'];
  }
  return $total_price;
}

// カートに入っている商品データをバリデーション処理して、TRUEを返す
function validate_cart_purchase($carts){
  // カートデータが0件の場合はエラーメッセージを表示する
  if(count($carts) === 0){
    set_error('カートに商品が入っていません。');
    return false;
  }
  
  // ステータスが非公開の商品の場合はエラーメッセージ
  foreach($carts as $cart){
    if(is_open($cart) === false){
      set_error($cart['name'] . 'は現在購入できません。');
    }
    // もし注文数が在庫数より大きい場合はエラーメッセージ
    if($cart['stock'] - $cart['amount'] < 0){
      set_error($cart['name'] . 'は在庫が足りません。購入可能数:' . $cart['stock']);
    }
  }
  // もしエラーがある場合はある場合はFALSEを返す
  if(has_error() === true){
    return false;
  }
  return true;
}

