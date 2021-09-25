<?php
// 関数はUPDATE、INSERT、SELECTでそれぞれ分ける
// パスは/htdocs/SnackOnlineSHOP/include/SHOP_function.phpで他と違うので注意する

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
        // SELECTなのでfetchALLを使う
        return $row;   
    // print_rで中身をチェックする (もし同じidなら$rowに入る)
    // print_r($row); 
    // exit();
    } catch (PDOException $e) {
        $err_msg[] = 'カートの情報が取得できませんでした。理由：' . $e->getMessage();
    }

}

//             try {
//                 // カートに追加する商品がすでにカートに入っているかどうか確認する (SELECT文で参照する。)
//                 $sql = 'SELECT item_id, amount 
//                         FROM cart 
//                         WHERE item_id = ? AND user_id = ?';
//                 // SQL文を実行する準備
//                 $stmt = $dbh->prepare($sql);
//                 // SQL文のプレースホルダに値をバインド
//                 $stmt->bindValue(1, $item_id, PDO::PARAM_INT);
//                 $stmt->bindValue(2, $user_id, PDO::PARAM_INT);
//                 // SQLを実行
//                 $stmt->execute();
//                 // レコードの取得
//                 $row = $stmt->fetchAll();
// // print_rで中身をチェックする (もし同じidなら$rowに入る)
// // print_r($row); 
// // exit();
//             } catch (PDOException $e) {
//                 $err_msg[] = 'カートの情報が取得できませんでした。理由：' . $e->getMessage();
//             }

function favorite_confirmation_product_customer($dbh, $item_id, $user_id) {
    global $err_msg;
    
    try {
        $sql = 'SELECT product.id, name, price, img, favorite.customer_id, product_id
                FROM product
                JOIN favorite
                    ON product.id = favorite.product_id
                WHERE product_id = ? AND customer_id = ?';
        // $sql = 'SELECT customer_id, product_id
        //         FROM favorite
        //         WHERE product_id = ? AND customer_id = ?';
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(1, $item_id,   PDO::PARAM_INT);
        $stmt->bindValue(2, $user_id,   PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll();
// print_r($rows);
// exit();
        if (count($rows) > 0) {
            $err_msg[] = '選択した商品は既にお気に入りに登録されています。';
        }
        
        return $rows; 
        
    } catch (PDOException $e) {
        $err_msg[] = 'お気に入りの登録に失敗しました。';
    }
}


// try {
//     $sql = 'SELECT product.id, name, price, img, favorite.customer_id, product_id
//             FROM product
//             JOIN favorite
//                 ON product.id = favorite.product_id
//             WHERE product_id = ? AND customer_id = ?';
//     // $sql = 'SELECT customer_id, product_id
//     //         FROM favorite
//     //         WHERE product_id = ? AND customer_id = ?';
//     $stmt = $dbh->prepare($sql);
//     $stmt->bindValue(1, $item_id,   PDO::PARAM_INT);
//     $stmt->bindValue(2, $user_id,   PDO::PARAM_INT);
//     $stmt->execute();
//     $rows = $stmt->fetchAll();
// // print_r($rows);
// // exit();
//     if (count($rows) > 0) {
//         $err_msg[] = '選択した商品は既にお気に入りに登録されています。';
//     }
// } catch (PDOException $e) {
//     $err_msg[] = 'お気に入りの登録に失敗しました。';
// }
            
 
function get_product_list_unknown($dbh, $keyword) {
    global $err_msg; 
    
    try {
        // ステータスが1(公開)の販売商品のみを一覧で表示する。
        $sql = 'SELECT product.id, name, price, img, status, stock
                FROM product 
                JOIN item_stock
                    ON product.id = item_stock.stock_id
                WHERE status = 1';
        $stmt = $dbh->prepare($sql);
        $stmt->execute();
        //全て取得するのでfetchALLでレコードを取得する
        $result = $stmt->fetchALL();
        $err_msg[] = ($keyword . 'を含む商品は見つかりませんでした。');
        return $result; 
    } catch (PDOException $e) {
        $err_msg[] = '商品を取得できませんでした。';
    }            
}

// try {
//     // ステータスが1(公開)の販売商品のみを一覧で表示する。
//     $sql = 'SELECT product.id, name, price, img, status, stock
//             FROM product 
//             JOIN item_stock
//                 ON product.id = item_stock.stock_id
//             WHERE status = 1';
//     $stmt = $dbh->prepare($sql);
//     $stmt->execute();
//     //全て取得するのでfetchALLでレコードを取得する
//     $result = $stmt->fetchALL();
//     $err_msg[] = ($keyword . 'を含む商品は見つかりませんでした。');
// } catch (PDOException $e) {
//     $err_msg[] = '商品を取得できませんでした。';
// }

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
        //全て取得するのでfetchALLでレコードを取得する
        $result = $stmt->fetchALL();
        // $err_msg[] = ($keyword . 'を含む商品は見つかりませんでした。');
        return $result; 
    } catch (PDOException $e) {
        $err_msg[] = '商品を取得できませんでした。';
    }
}

// try {
//     // ステータスが1(公開)の販売商品のみを一覧で表示する
//     $sql = 'SELECT product.id, name, price, img, status, stock
//             FROM product 
//             JOIN item_stock
//                 ON product.id = item_stock.stock_id
//             WHERE status = 1';
//     $stmt = $dbh->prepare($sql);
//     $stmt->execute();
//     //全て取得するのでfetchALLでレコードを取得する
//     $result = $stmt->fetchALL();
//     // $err_msg[] = ($keyword . 'を含む商品は見つかりませんでした。');
// } catch (PDOException $e) {
//     $err_msg[] = '商品を取得できませんでした。';
// }

// 部分一致検索関数
function partial_match_search($dbh, $keyword) {
    // global $msg; 
    
    $sql = 'SELECT product.id, name, price, img, status, stock
                    FROM product 
                    JOIN item_stock
                        ON product.id = item_stock.stock_id
                    WHERE name like ?';
    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(1, $keyword,  PDO::PARAM_STR);  
    $stmt->execute(['%' . $keyword . '%']);
    $result = $stmt->fetchALL();
    return $result;
}

            // 変更前の$sqlには、インデントしたスペースやタブコードも混ざるのでヒアドキュメント構文を使う
            // 全角空白の紛れ込みの予防にもなる
//             $sql = <<<EOT
//                     SELECT product.id, name, price, img, status, stock 
//                     FROM product 
//                     JOIN item_stock 
//                     ON product.id = item_stock.stock_id
//                     WHERE name like? 
// EOT;
            // $sql = 'SELECT product.id, name, price, img, status, stock
            //         FROM product 
            //         JOIN item_stock
            //             ON product.id = item_stock.stock_id
            //         WHERE name like ?';
            // $stmt = $dbh->prepare($sql);
            // $stmt->bindValue(1, $keyword, PDO::PARAM_STR);  
            // $stmt->execute(['%' . $keyword . '%']);
            // $result = $stmt->fetchALL();


// 2. INSERT文
function insert_cart_user_item($dbh, $user_id, $item_id, $date) {
    global $err_msg, $msg;
    
    try {
        // カートに追加の対象商品がカートに入っていない場合
        // 追加の対象商品がすでにカートに入っている場合で場合分けする
        $sql = 'INSERT INTO cart (user_id, item_id, amount, create_date, update_date) 
                VALUES (?, ?, ?, ?, ?)';
        // SQL文を実行する準備
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(1, $user_id,   PDO::PARAM_INT);  
        $stmt->bindValue(2, $item_id,   PDO::PARAM_INT);
        $stmt->bindValue(3, 1,          PDO::PARAM_INT);    //在庫は1にする
        $stmt->bindValue(4, $date,      PDO::PARAM_STR);  
        $stmt->bindValue(5, $date,      PDO::PARAM_STR);
        $stmt->execute();
        $msg[] = 'カートに商品を追加しました!';
    } catch (PDOException $e) {
        $err_msg[] = 'カートに商品を追加できませんでした。';
    } // メッセージを表示するだけなのでreturnは不要
}

// try {
//     // カートに追加の対象商品がカートに入っていない場合
//     // 追加の対象商品がすでにカートに入っている場合で場合分けする
//     $sql = 'INSERT INTO cart (user_id, item_id, amount, create_date, update_date) 
//             VALUES (?, ?, ?, ?, ?)';
//     // SQL文を実行する準備
//     $stmt = $dbh->prepare($sql);
//     $stmt->bindValue(1, $user_id,   PDO::PARAM_INT);  
//     $stmt->bindValue(2, $item_id,   PDO::PARAM_INT);
//     $stmt->bindValue(3, 1,          PDO::PARAM_INT);    //在庫は1にする
//     $stmt->bindValue(4, $date,      PDO::PARAM_STR);  
//     $stmt->bindValue(5, $date,      PDO::PARAM_STR);
//     $stmt->execute();
//     $msg[] = 'カートに商品を追加しました!';
// } catch (PDOException $e) {
//     $err_msg[] = 'カートに商品を追加できませんでした。';
// }


function insert_favorite_user_item($dbh, $user_id, $item_id, $date) {
    global $err_msg, $msg;
    
    try {   
        $sql = 'INSERT INTO favorite (customer_id, product_id, create_date) 
                VALUES (?, ?, ?)';
        //データベースのお気に入りのテーブルに追加する
        // $user_id   = $_SESSION['customer']['id']; 
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(1, $user_id,   PDO::PARAM_INT);  
        $stmt->bindValue(2, $item_id,   PDO::PARAM_INT);  
        $stmt->bindValue(3, $date,      PDO::PARAM_STR);
        $stmt->execute([$user_id, $item_id, $date]);
        $msg[] = 'お気に入りに商品を追加しました！';
    } catch (PDOException $e) {
        $err_msg[] = 'お気に入りの登録に失敗しました。理由：'.$e->getMessage();
    }
}

// try {   
//     $sql = 'INSERT INTO favorite (customer_id, product_id, create_date) 
//             VALUES (?, ?, ?)';
//     //データベースのお気に入りのテーブルに追加する
//     // $user_id   = $_SESSION['customer']['id']; 
//     $stmt = $dbh->prepare($sql);
//     $stmt->bindValue(1, $user_id,   PDO::PARAM_INT);  
//     $stmt->bindValue(2, $item_id,   PDO::PARAM_INT);  
//     $stmt->bindValue(3, $date,      PDO::PARAM_STR);
//     $stmt->execute([$user_id, $item_id, $date]);
//     $msg[] = 'お気に入りに商品を追加しました！';
// } catch (PDOException $e) {
//     $err_msg[] = 'お気に入りの登録に失敗しました。理由：'.$e->getMessage();
// }


// 3. UPDATE文
function update_cart_user_item($dbh, $user_id, $item_id, $date) {
    global $err_msg, $msg;
    
    try {
        // カートに追加の対象商品がすでにカートに入っている場合は個数を更新する。
        // $amount = $row[0]["amount"] + 1;
        $sql = 'UPDATE cart 
                SET amount = amount + 1 , update_date = ? 
                WHERE user_id = ? AND item_id = ?';
        // SETに更新対象カラムを指定する
        // SQL文を実行する準備
        $stmt = $dbh->prepare($sql);
        // SQL文のプレースホルダに値をバインド
        // $stmt->bindValue(1, $amount,  PDO::PARAM_INT);
        $stmt->bindValue(1, $date,    PDO::PARAM_STR);
        $stmt->bindValue(2, $user_id, PDO::PARAM_INT);
        $stmt->bindValue(3, $item_id, PDO::PARAM_INT);
        // SQLを実行
        $stmt->execute();
        $msg[] = 'カート内の個数を更新しました!';
    } catch (PDOException $e) {
        $err_msg[] = 'カート内の個数を更新できませんでした。';
    }
}

// try {
//     // カートに追加の対象商品がすでにカートに入っている場合は個数を更新する。
//     $amount = $row[0]["amount"] + 1;
//     $sql = 'UPDATE cart 
//             SET amount = ? , update_date = ? 
//             WHERE user_id = ? AND item_id = ?';
//     // SETに更新対象カラムを指定する
//     // SQL文を実行する準備
//     $stmt = $dbh->prepare($sql);
//     // SQL文のプレースホルダに値をバインド
//     $stmt->bindValue(1, $amount,  PDO::PARAM_INT);
//     $stmt->bindValue(2, $date,    PDO::PARAM_STR);
//     $stmt->bindValue(3, $user_id, PDO::PARAM_INT);
//     $stmt->bindValue(4, $item_id, PDO::PARAM_INT);
//     // SQLを実行
//     $stmt->execute();
//     $msg[] = 'カート内の個数を更新しました!';
// } catch (PDOException $e) {
//     $err_msg[] = 'カート内の個数を更新できませんでした。';
// }