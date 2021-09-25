<?php             
// カート一覧のページ

session_start();    //セッション管理を実現する関数を最初に書いておく。

// $value['price'] = 0;
// $value['amount'] = 0;

$total = 0;
$err_msg     = [];  //エラーメッセージの格納先
$success_msg = [];
$data        = [];
$date = date('Y-m-d H:i:s');
$img_dir     = './product_picture/';
//$total       = $value['price'] * $value['amount']; //合計額を計算

$host     = 'localhost';      //ホスト名
$username = 'codecamp42398';  //ユーザー名
$password = 'codecamp42398';  //パスワード
$dbname   = 'codecamp42398';  //データベース名
$charset  = 'utf8';
$dsn      = 'mysql:dbname='.$dbname.';host='.$host.';charset='.$charset;

//もしログインされてない場合は、ログイン画面に強制ジャンプするようにする
// （セッションにuser_idが存在しない場合）
if(isset($_SESSION['customer']['id']) === FALSE) { 
    header('Location: login.php');
    exit();      
}

if (isset($_SESSION['customer']) === TRUE) {   //最初にログインしているかをチェックする。
    $user_id   = $_SESSION['customer']['id'];  //ログインページ(login.php)から飛んでくる情報
    $user_name = $_SESSION['customer']['user_name'];
}

try {
    $dbh = new PDO($dsn, $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'));
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {  //下からPOSTで飛んでくる
        // formタグ(name=sql_kind)
        if (isset($_POST['sql_kind']) === TRUE) {
            $sql_kind = trim($_POST['sql_kind']);
        }
        // sql_kind === 'delete' (削除) 、sql_kind === 'update' (更新)で場合分けする
        if ($sql_kind === 'delete') {
            if (isset($_POST['cart_id']) === TRUE) {
                $cart_id = $_POST['cart_id'];
            }
            if ($cart_id === '') {
                $err_msg[] = '削除を選択してください。';
            } else if (ctype_digit($cart_id) === FALSE) { //preg_matchでもOK
                $err_msg[] = '不正な要求です。';
            }
            //商品を削除するためにDELETE文を作る
            if (count($err_msg) === 0) {  
                try {
                    $sql = 'DELETE FROM cart
                            WHERE cart_id = ? AND user_id = ?';
                    $stmt = $dbh->prepare($sql);
                    $stmt->bindValue(1, $cart_id,  PDO::PARAM_INT);  
                    $stmt->bindValue(2, $user_id,  PDO::PARAM_INT);
                    $stmt->execute();
                    $success_msg[] = 'カート内の商品を削除しました。';
                } catch (PDOException $e) {
                    $err_msg[] = 'カート内の商品が削除できませんでした。';
                }
            }
        } else if ($sql_kind === 'update') { //カート内の商品の数量を変更する。
            if (isset($_POST['item_id']) === TRUE) {
                $item_id = $_POST['item_id'];
            }
            if (isset($_POST['amount'])) {
                $amount = $_POST['amount'];
            }    
            if (mb_strlen($amount) === 0) {
                $err_msg[] = '個数を入力してください。';
            } else if (ctype_digit($amount) === FALSE) {
                $err_msg[] = '個数は数字で入力してください。';
            }
            if (ctype_digit($item_id) === FALSE) {
                $err_msg[] = '不正な要求です。';
            }
            if (count($err_msg) === 0) {
                try {
                    $sql = 'UPDATE cart
                            SET amount = ?, update_date = ?
                            WHERE item_id = ? AND user_id = ?';
                    $stmt = $dbh->prepare($sql);
                    $stmt->bindValue(1, $amount,   PDO::PARAM_INT);
                    $stmt->bindValue(2, $date,     PDO::PARAM_STR);
                    $stmt->bindValue(3, $item_id,  PDO::PARAM_INT);
                    $stmt->bindValue(4, $user_id,  PDO::PARAM_INT);
                    $stmt->execute();
                    $success_msg[] = 'カート内の個数を変更しました。';
                } catch (PDOException $e) {
                    $err_msg[] = 'カート内の個数を変更できませんでした。理由：' . $e->getMessage();
                }
            }
        }        
    } // 35行目REQUEST METHOD の}            
    
    
     // ログインしているユーザーのカートに入っている商品だけを表示する。
        
     $sql = 'SELECT product.id, name, price, img, status, cart.cart_id, user_id, item_id, amount
             FROM cart
             JOIN product
                ON cart.item_id = product.id
             WHERE cart.user_id = ?';
     $stmt = $dbh->prepare($sql);
     $stmt->bindValue(1, $user_id, PDO::PARAM_INT);
     $stmt->execute();
     //全て取得するのでfetchALLでレコードを取得する
     $data = $stmt->fetchALL();
    //  $sum = $data;
// print_r($data);
// exit();
    
    // カート内の商品をforeachを使って合計金額を計算
    // $total = 0;
    foreach ($data as $value) {
        $total += $value['price'] * $value['amount'];
    }
    
} catch (PDOException $e) {
    $err_msg[] = '予期せぬエラーが発生しました。管理者へお問い合わせください。理由：'.$e->getMessage();
}

// 指定の商品の削除と指定の商品数量変更ができる。 (DELETE文とUPDATE文)
// ショッピングカートにある商品の合計金額を表示する。
// 商品を購入する （「購入完了ページ」に遷移する）。
// カート一覧から購入予定の商品数を変更する場合、正の整数のみ可能とする。
// 正の整数以外はエラーメッセージを表示して、変更できない。
// ログインされてない場合（セッションにuser_idが存在しない）は、ログイン画面に強制ジャンプする
// ログインユーザーのカート情報を一覧表示する
// 数量の変更機能、商品の削除機能、合計金額の計算機能
// 購入完了ページへのジャンプ機能
// 一言掲示板のように、エラーメッセージはHTML側に表示する。
//<div class="total">
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>カート</title>
    <style>
        body {
            min-width:800px;
            padding-bottom: 30px;
        }
        
        table {
            width: 75%;
            margin: 0 auto;
        }
        
        td {
            text-align: center;
            font-size: 20px;
        }
    
        .purchase {
            display: block;
            margin: 0 auto;
            width: 500px;
            padding: 0.2em;
            height: 70px;
            color: #fff;
            background-color: #ff8a00;
            font-size: 24px;
            border: none;
        }
        
        .total {
            color: #f00;
            font-weight: bold;
            font-size: 30px;
            text-align: center;
        }  
     
        .delete  {
            background-color: #27e800;
            font-size: 16px;
            padding: 0.6em;
            color: #fff;
            border-radius: 6px;
            border : none;
            margin: 0px 20px;
        }
        
        .update {
            background-color: #00c9e8;
            margin-left: 35px;
            font-size: 16px;
            padding: 0.6em;
            color: #fff;
            border-radius: 6px;
            border : none;
        }
        
        .cart  {
            color: #f00;
            font-weight: bold;
            font-size: 25px;
        }
        
        .img {
            margin: 30px 0px;
        }
    
        .error {
            color: #f00;
            font-weight: bold;
            text-align: center;
        }
        
        .success {
            color: #00ae95;
            font-weight: bold;
            font-size: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <h1>カート一覧</h1>
        <p>ようこそ、<?php echo $user_name; ?>さん。</p>
        <a href="admin.php">商品管理</a>
        <a href="top.php">商品一覧</a></a>
        <a href="logout.php">ログアウト</a>
  <!-- メッセージ・エラーメッセージ を表示-->
<?php foreach ($err_msg as $value) { ?>
        <p class="error"><?php echo $value; ?></p>
<?php } ?>
 <?php foreach ($success_msg as $value) { ?>
        <p class="success"><?php echo $value; ?></p>
<?php } ?>
  <!-- カート一覧 -->
<?php if (count($data) > 0) { ?>
    <table>
        <thead>
            <tr>
                <th>商品画像</th>
                <th>商品名</th>
                <th>価格</th>
                <th>数量</th>
                <th>小計</th>
                <th>削除</th>
                <th>個数変更</th>
            </tr>
        </thead>
        <hr>
      <tbody>
<?php foreach ($data as $value) { ?>
        <tr>
          <td><img class="img" src="<?php echo $img_dir . $value['img']; ?>" width=400 height=300></td>
          <td><?php echo($value['name']); ?></td>
          <td>&yen;<?php echo($value['price']); ?></td>
          <td><?php echo($value['amount']); ?></td>
          <td>&yen;<?php echo($value['price'] * $value['amount']); ?></td>
          <td>
                <form method="post">
                    <input type="hidden" name="cart_id" value="<?php echo($value['cart_id']); ?>">
                    <input type="hidden" name="sql_kind" value="delete">
                    <input type="submit" value="削除" class="delete">
                </form>
          </td>
          <td>
                <form method="post">
                    <input type="hidden" name="item_id" value="<?php print $value['item_id']; ?>">
                    <input type="hidden" name="sql_kind" value="update">
                    <input type="text" style="width: 60px;" name="amount" min="1" value="<?php echo $value['amount']; ?>">個<input type="submit" value="変更する" class="update">
                </form>
          </td>
        </tr>
<?php } ?>
      </tbody>
    </table>
    <hr>
    <p class="total">合計：&yen;<?php echo $total;?></p>
    <form method="post" action="complete.php">
        <input type="submit" value="購入する" class="purchase">
    </form>
<?php } else { ?>
    <p class="cart">現在カートに商品はありません。</p>
<?php } ?>
  </body>

</html>

    