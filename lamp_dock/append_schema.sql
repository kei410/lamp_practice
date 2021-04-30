/* 購入履歴テーブル */
CREATE TABLE histories (
  order_id INT AUTO_INCREMENT,
  user_id INT,
  order_date DATETIME,
  primary key(order_id)
);


/* 購入明細テーブル */
CREATE TABLE details (
    order_id INT,
    item_id INT,
    price INT,
    amount INT
);
