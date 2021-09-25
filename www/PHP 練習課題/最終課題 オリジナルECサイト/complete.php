<?php
// 購入完了のページ

session_start();

$err_msg     = [];  //エラーメッセージの格納先
$success_msg = [];
$img_dir     = './product_picture/';
$total = 0;
$date = date('Y-m-d H:i:s');

$host     = 'localhost';      //ホスト名
$username = 'codecamp42398';  //ユーザー名
$password = 'codecamp42398';  //パスワード
$dbname   = 'codecamp42398';  //データベース名
$charset  = 'utf8';
$dsn      = 'mysql:dbname='.$dbname.';host='.$host.';charset='.$charset;


// もしログインされてない場合は、ログイン画面に強制移動する。
// （セッションにuser_idが存在しない場合）
if(isset($_SESSION['customer']['id']) === FALSE) { 
    header('Location: login.php');
    exit();      
}

if (isset($_SESSION['customer']) === TRUE) {   //最初にログインしているかをチェックする。
    $user_id   = $_SESSION['customer']['id'];  //ログインページ(login.php)から飛んでくる情報
    $user_name = $_SESSION['customer']['user_name'];
}

// POST以外はエラーメッセージ　cart.phpの購入するボタンから飛んでくる 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $dbh = new PDO($dsn, $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'));
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        
        // ログインしているユーザーのカートに入っている商品を全て表示。
        // JOIN ONが2つあり複雑なので注意する
        $sql = 'SELECT product.id, name, price, img, status, cart.cart_id, user_id, cart.item_id, amount, stock
                FROM cart
                JOIN product
                    ON cart.item_id = product.id
                JOIN item_stock
                    ON product.id = item_stock.item_id
                WHERE cart.user_id = ?';
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(1, $user_id, PDO::PARAM_INT);
        $stmt->execute();
        // 全て取得するのでfetchALLでレコードを取得する
        $data = $stmt->fetchALL();
        // $dataにはカートに入っている商品の情報が入っている
// print_r($data);
// exit(); 

        // カートデータ内の商品をチェックする
        // カート内の商品をforeachを使って合計金額を計算する(HTMLに表示する)
        // カートデータが0件の場合はエラー  ステータスが公開以外(1以外)はエラー
         
        // 注文数が在庫数より大きい場合、ステータスが公開以外、カートデータが0件の場合はエラーメッセージ
        if (count($data) === 0) {
            $err_msg[] = '現在カートに商品が入っていません。';
        } else {
            foreach ($data as $value) {
              // $total = 0;
              $total += $value['price'] * $value['amount'];
              $status = $value['status'];
              if ($status !== 1) {
                  $err_msg[] = '選択した商品は現在公開されていません。';
              }
              if ($value['stock'] - $value['amount'] < 0) {
                  $err_msg[] = ($value['name'] . 'は在庫が足りません。購入可能数:' . $value['stock']);
              }
            }
        }
        // エラーがないときにトランザクション処理を開始
        // 在庫テーブルを更新する  SETで更新対象のカラムと更新後の値を指定
        // カートの数量を使って商品の在庫数を減算する
        // foreachで1商品ずつ取り出し、該当する在庫数を減算する
        // 購入後にカートの中身削除と在庫変動 (cartテーブルとitem_stockにトランザクション処理)
        
        if (count($err_msg) === 0) {
            $dbh->beginTransaction();  // トランザクション開始
            // cartテーブルとitem_stockテーブル
            try {  
                foreach ($data as $key => $value) {
                  $item_id = $value['item_id'];
                  $stock = (int)$value['stock'] - (int)$value['amount'];
                  $sql = 'UPDATE item_stock 
                          SET stock = ?, update_date = ? WHERE item_id = ?';
                  $stmt = $dbh->prepare($sql);
                  $stmt->bindValue(1, $stock,     PDO::PARAM_INT);
                  $stmt->bindValue(2, $date,      PDO::PARAM_STR);
                  $stmt->bindValue(3, $item_id,   PDO::PARAM_INT);
                  $stmt->execute();
                }
                  // 現在ログインしているユーザーのカート情報を削除する
                  $sql = 'DELETE FROM cart
                          WHERE user_id = ?';
                  $stmt = $dbh->prepare($sql);
                  $stmt->bindValue(1, $user_id, PDO::PARAM_INT);
                  $stmt->execute();
                  $carts = $dbh->commit();
                  $success_msg[] = '在庫数を更新しました。';
            } catch (PDOException $e) {
              // ロールバック処理
              $dbh->rollback();
              $err_msg[] = '在庫数の更新に失敗しました。' . $e->getMessage();
            }
        }
  } catch (PDOException $e) {
    $err_msg[] = 'カート内の商品情報が取得できませんでした。理由：'.$e->getMessage();
  }
} else {
  $err_msg[] = '要求の形式が正しくありません。';
}

// foreach文ではforeach($data as $value)のようにvalueを使うようにする
// POST形式以外はエラーとする
// ログインユーザーのカート情報を一覧表示する
// カートデータ内の商品をチェックする
// 注文数が在庫数より大きい場合、ステータスが公開以外、カートデータが0件の場合はエラー
// エラーが無ければ、トランザクションを開始し、以下の処理を行う
// カートの数量を使って商品の在庫数を減算する
// foreachを使って1商品ずつ取り出し、該当する在庫数を減算する
// ログインユーザーのカート情報を削除する
// ログアウトができる
?>
<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <title>購入完了ページ</title>
    <style>
      table {
            width: 80%;
            margin: 0 auto;
      }
      
      td {
            text-align: center;
      }
      /*h1 {*/
        
      /*}*/
      
      .total {
            color: #f00;
            font-weight: bold;
            font-size: 30px;
            text-align: center;
      }  
      
      .img {
            margin-bottom: 20px;
      }
      
      .finish-msg {
            margin-top: 20px;
            height: 80px;
            font-size: 1.4em;
            text-align: center;
            line-height: 80px;
            background-color: #f5c9c6;
      }
      
      .error {
            color: #f00;
            font-weight: bold;
            text-align: center;
      }
        
      .success {
            color: #f00;
            font-weight: bold;
            font-size: 20px;
            text-align: center;
      }
    </style>
  </head>
  <body>
  <h1 class="finish-msg">ご購入ありがとうございました！</h1>
  <a href="logout.php">ログアウト</a>
<?php foreach ($err_msg as $value) { ?>
<p class="error"><?php echo $value; ?></p>
<?php } ?>
 <?php foreach ($success_msg as $value) { ?>
<p class="success"><?php echo $value; ?></p>
<?php } ?>
<?php if (count($data) > 0){ ?>
    <table>
      <thead>
        <tr>
          <th>商品画像</th>
          <th>商品名</th>
          <th>価格</th>
          <th>購入数</th>
          <th>小計</th>
        </tr>
      </thead>  
      <tbody>
<?php foreach ($data as $value) { ?>
        <tr>
          <td><img class="img" src="<?php echo $img_dir . $value['img']; ?>" width=160 height=160></td>
          <td><?php echo($value['name']); ?></td>
          <td><?php echo($value['price']); ?></td>
          <td><?php echo($value['amount']); ?></td>
          <td><?php echo($value['price'] * $value['amount']); ?></td>
        </tr>
<?php } ?>
      </tbody>
    </table>
    <p class="total">合計金額：&yen;<?php echo($total); ?></p>
<?php } else { ?>
    <p>カートに商品はありません。</p>
<?php } ?>
  </body>
</html>