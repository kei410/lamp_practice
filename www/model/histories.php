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

// 1. ユーザ毎の購入履歴  (全件取得する)
// 全件取得のためWHEREは不要
// get_history関数の方は全件表示するので、引数に$user_idは不要
function get_history($db){
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
      GROUP BY
        order_id
      ORDER BY
        order_date
      DESC
    ";

    return fetch_all_query($db, $sql);
}

/* , array($user_id) */
function get_user_history($db, $user_id){
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

// 2. ユーザ毎の購入明細
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


/* SUMを利用して商品ごとの全てのユーザーの購入数を求める*/
/* ORDER BY 句でユーザーの購入数を基準に並べ替える*/
/* 購入数の多い順に1位から3位の商品を表示させる */
/* group byをうまく使って詳細テーブルを集計してランキング表示するselect分を作る */


// 3. ランキング機能
/* function get_ranking($db, $user_id){
  $sql = "
    SELECT
      details.item_id,
      details.price,
      details.amount
      SUM(details.price * details.amount) AS subtotal,
      items.name
    FROM
      details
    JOIN
      items
    GROUP BY
      details.item_id,
      details.price,
      details.amount,
      items.name
    ORDER BY
      amount
    LIMIT 3
    ";

    return fetch_all_query($db, $sql, array($user_id));

} */