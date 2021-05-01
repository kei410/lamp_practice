<?php
require_once MODEL_PATH. 'functions.php';
require_once MODEL_PATH. 'db.php';

/* 
１：テーブルに書き込むタイミングがどこかをコードを読み込んで探し出し、適切な位置に
    履歴テーブルと詳細テーブルに書き込む処理を書く（それぞれを関数化して、適切な位置で実行）
２：それが想定通り動くことを確認する。ただし、１回の注文で複数種類の商品を購入した場合、
    ちゃんと履歴テーブルには１件の情報が、詳細テーブルには複数の情報が入力されることを確認する。
    lastInsertIdをうまく使うこと。
３：想定通り動けばトランザクションを実装する。
４：その上で再度テストして動けばpush & pull request */

// select SUM・・・ as total from でレコードの値の合計を求める
// GROUP BYは指定したカラムの値をキーとしてレコードをグループ化することができる機能
// 欲しいデータが「単一のデータ」だけではなく「複数のデータを集計した計算結果」も含まれている場合に使う
// order_idの降順で表示する (DESC)
// JOINでテーブル同士を横に結合する
// historiesテーブルにdetailsテーブルをくっつける
// ON以降は2つのテーブルがどのように紐づいているのかを定義する

// ユーザ毎の購入履歴 
function get_history($db, $user_id){
    $sql = "
      SELECT
        histories.order_id,
        histories.order_date,
        SUM(details.price * details.amount) AS total
      FROM
        histories
      JOIN
        details
      ON
        histories.order_id = details.order_id
      WHERE
        user_id = ?
      GROUP BY
        order_id
      ORDER BY
        order_date
      DESC
    ";

    return fetch_all_query($db, $sql, array($user_id));
}


// select SUM・・・ as total from でレコードの値の合計を求める
// GROUP BYは指定したカラムの値をキーとしてレコードをグループ化することができる機能
// 欲しいデータが「単一のデータ」だけではなく「複数のデータを集計した計算結果」も含まれている場合に使う

// ユーザ毎の購入明細
function get_detail($db, $order_id){
  $sql = "
    SELECT
      details.price,
      details.amount,
      SUM(details.price * details.amount) AS subtotal,
      items.name
    FROM
      details
    JOIN
      items
    ON
      details.item_id = items.item_id
    WHERE
      order_id = ?
    GROUP BY
      details.price, details.amount, items.name
  ";
  
  return fetch_all_query($db, $sql, array($order_id));
}