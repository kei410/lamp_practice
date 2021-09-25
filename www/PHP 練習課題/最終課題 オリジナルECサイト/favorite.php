<?php
// お気に入りのページ  (商品番号の順に一覧表示する)

session_start();

$err_msg     = [];  //エラーエッセージの格納先
$success_msg = [];
$product_id  = '';
$img_dir     = './product_picture/';
$date        = date('Y-m-d H:i:s');

$host     = 'localhost';      
$username = 'codecamp42398';  
$password = 'codecamp42398';  
$dbname   = 'codecamp42398';  
$charset  = 'utf8';
$dsn      = 'mysql:dbname='.$dbname.';host='.$host.';charset='.$charset;

//お気に入りに登録した商品一覧を表示する。 ログインしていないとお気に入りは表示できないようにする。


//  もしログインされてない場合はログイン画面に移動 (セッションにuser_idが存在しない)
if(isset($_SESSION['customer']['id']) === FALSE) { 
    header('Location: login.php');
    exit();      
}

// 最初にログインしているかをチェックする。
if (isset($_SESSION['customer']) === TRUE) {     
    $user_id   = $_SESSION['customer']['id'];  
    $user_name = $_SESSION['customer']['user_name'];  
}


try {   
    $dbh = new PDO($dsn, $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'));
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    
    // 下のフォームから飛んでくるのでPOST形式のみ
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['delete']) === TRUE) {
                if (isset($_POST['item_id']) === TRUE) {
                    $product_id = trim($_POST['item_id']);
                }
                // $user_id   = $_SESSION['customer']['id']; 
                try { 
                    $sql = 'DELETE FROM favorite
                            WHERE customer_id = ? and product_id = ?';
                    $stmt = $dbh->prepare($sql);
                    $stmt->bindValue(1, $user_id,    PDO::PARAM_INT);  
                    $stmt->bindValue(2, $product_id, PDO::PARAM_INT);  
                    $stmt->execute([$user_id, $product_id]);
                    $rows = $stmt->fetchAll();
                    $success_msg[] =  'お気に入りから商品を削除しました。';
                } catch (PDOException $e) {
                    $err_msg[] = 'お気に入りの削除に失敗しました。 '.$e->getMessage();
                }
        }
    }
    
    // お気に入りの商品を一覧表示する
    // 全て取得するのでSELECTは*にする
    try {
        $sql = 'SELECT *
                FROM favorite, product '.
               'WHERE customer_id = ? and product_id = product.id
                ORDER BY product_id';
                // ?に顧客番号を指定する。
                // 商品番号順に表示する
                // favoriteテーブルの商品番号 (product_id列)と、productテーブルの商品番号 (id列)が
                // 一致している行だけが必要なので、WHERE句に条件を追加する。
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(1, $user_id,  PDO::PARAM_INT); 
        //変数名とプレースホルダ名は同じものを使うように設定する。
        $stmt->execute([$user_id]);
        $favorite = $stmt->fetchAll();
// print_r($favorite);
// exit();   
        if (count($favorite) === 0) {
            $err_msg[] = '現在お気に入りに商品が登録されていません。';
        }
        
    } catch (PDOException $e) {
        $err_msg[] = 'お気に入り一覧が取得できませんでした。';
    } 
    
} catch (PDOException $e) {
    $err_msg[] = '予期せぬエラーが発生しました。管理者へお問い合わせください。理由：'.$e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta chartset="UTF-8">
    <title>お気に入り一覧</title>
    <style>
        table {
            width: 70%;
            margin: 0 auto;
            background-color: #fff;
            border: 2px solid;
        }
        
        body {
            background-color: #dfd;
        }
        
        td {
            text-align: center;
        }
        
        input[type="submit"] {
            background: #1e8eff;
            width: 240px;
            padding: 0.3em;
            color: #fff;
            font-size: 18px;
            border: none;
            border-radius: 8px;
            margin-right: 20px;
        }
        
        input[type="button"] {
            background: #1e8eff;
            width: 120px;
            padding: 0.4em;
            color: #fff;
            font-size: 16px;
            border: none;
            border-radius: 4px;
        }
        
        .img {
            text-align: center;
            margin: 25px 0px;
        }
        
        .error {
            color: #f00;
            font-weight: bold;
            text-align: center;
            font-size: 25px;
        }
        
        .soldout {
            color: #f00;
            font-weight: bold;
            text-align: center;
        }
    
        .name {
            font-size: 20px;
            font-weight: bold;
            text-align: center;
        }
        
        .price {
            text-align: center;
            font-size: 20px;
            color: #ea5550;
        }
        
        .success {
            color: #00ae95;
            font-weight: bold;
            font-size: 20px;
            text-align: center;
        }

        .search_container input[type="submit"] {
            border: none;
            background: #1b9aaa;
            color: #000;
            width: 80px;
            height: 36px;
            border-radius: 2px;
            margin: 0px 25px;
        }
        
        .search_container {
            text-align: center;
        }
    </style>
  </head>
  <body>
  <h1>お気に入り一覧</h1>
  <a href="top.php">商品一覧</a>
  <a href="cart.php"><img src="./product_picture/cart.png" width=60 height=60></a>
  <a href="logout.php">ログアウト</a>
<?php foreach ($err_msg as $value) { ?>
  <p class="error"><?php echo $value; ?></p>
<?php } ?>
<?php foreach ($success_msg as $value) { ?>
  <p class="success"><?php echo $value; ?></p>
<?php } ?>
<?php if (count($favorite) > 0){ ?>
<table>
      <tr>
        <th>商品番号</th>
        <th>商品画像</th>
        <th>商品名</th>
        <th>価　格</th>
        <th>操作</th>
      </tr>
<?php foreach ($favorite as $value)  { ?>
      <tr>
          <td><?php echo htmlspecialchars($value['product_id'], ENT_QUOTES, 'UTF-8');?></td>
          <td><img class="img" src="<?php echo $img_dir . $value['img']; ?>"></td>
          <td class="name"><?php echo htmlspecialchars($value['name'], ENT_QUOTES, 'UTF-8');?></td>
          <td><?php echo htmlspecialchars($value['price'], ENT_QUOTES, 'UTF-8');?>円</td>
        <form method="post">
          <td><input type="submit" name="delete" value="お気に入りから削除する"></td>
          <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($value['id'], ENT_QUOTES, 'UTF-8');?>">
          <input type="hidden" name="sql_kind" value="delete">
        </form>
      </tr>
<?php } ?>
    </table>
  </section>
<?php } ?>
</body>
</html>