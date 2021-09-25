<?php
//会員トップページ   商品一覧ページ
require_once('../include/SHOP_function.php'); // 関数を読み込み

session_start();

$err_msg   = [];  //エラーエッセージの格納先
$msg       = [];
$row       = [];
$item_id   = '';
$date = date('Y-m-d H:i:s');

$host     = 'localhost';      //ホスト名
$username = 'codecamp42398';  //ユーザー名
$password = 'codecamp42398';  //パスワード
$dbname   = 'codecamp42398';  //データベース名
$charset  = 'utf8';
$dsn      = 'mysql:dbname='.$dbname.';host='.$host.';charset='.$charset;

// ログインされてない場合（セッションにuser_idが存在しない）は、ログイン画面にジャンプする
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
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['keyword']) && !isset($_POST['favorite'])) { 
        if (isset($_POST['item_id']) === TRUE) {
            $item_id = trim($_POST['item_id']);  //trim関数でスペースを削除する。文字列の先頭と末尾にある空白文字を取り除く。
        }     //37行目以降の一連の流れはパターンなので覚える ('' → pregmatch → count===0)
        if ($item_id === '' ) {
            $err_msg[] = '商品を選択してください。';
        } else if (preg_match('/^[1-9][0-9]*$/', $item_id) !== 1 ) {
            $err_msg[] = '商品が正しくありません。';
        }
        
        if (count($err_msg) === 0 ) {
            $row = get_cart_user_item($dbh, $item_id, $user_id);
        }
        
            // ここからカートの更新処理をする
            // if(empty($row)) {
            // emptyではなくcountを使う
            //カートに追加の対象商品がまだカートに入っていない 
            
            if(count($row) === 0 && !isset($_POST['favorite'])) {
                insert_cart_user_item($dbh, $user_id, $item_id, $date);
            } else if (!isset($_POST['favorite'])) { 
                update_cart_user_item($dbh, $user_id, $item_id, $date);
            }
    }   //37行目のREQUEST_METHODの}
            
    // お気に入りに関する処理
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {        
        if (isset($_POST['favorite']) === TRUE) {
            $item_id = trim($_POST['item_id']);
            //  既にお気に入りに選択した商品が存在しているかを確認する。
             $rows = favorite_confirmation_product_customer($dbh, $item_id, $user_id);
             
            // お気に入りに追加する   (既に登録済みなら追加できない)
            if(count($err_msg) === 0) { 
                // $favorite_id = trim($_POST['favorite']);
                insert_favorite_user_item($dbh, $user_id, $item_id, $date);
            }
        }
    }

    //  81行目以降はユーザーが商品を検索した場合の処理 
    //  ifは真1か偽0かしか見ていないので、結果がboolで返される関数の場合は、比較演算を省略可能
    //  isset($_POST['keyword']) === TRUEの===TRUEは省略できるので省略する。
    if (isset($_POST['keyword']) && mb_strlen ($_POST['keyword']) > 0) { 
        $keyword = trim($_POST['keyword']);
        try {
            $result = partial_match_search($dbh, $keyword);
            if (count($result) > 0 ) {
                $msg[] = ($keyword . 'を含む商品が見つかりました!');
            } else {
                $result = get_product_list_unknown($dbh, $keyword);
            }
        } catch (PDOException $e) {
            $err_msg[] = '商品を検索できませんでした。';
        } 
    } else {
         $result = get_product_list($dbh);
    }
    
//print_rで中身を確認する
//1行ずつ結果を配列で取得
} catch (PDOException $e) {
    $err_msg[] ='予期せぬエラーが発生しました。管理者へお問い合わせください。理由：'.$e->getMessage();
}

// 商品一覧ページ (top.php)
// カートに入れるボタンの処理
// 同一ユーザー、同一商品があればカート数量をカウントアップする
// なければ、新規カートを追加する
// // 商品のステータスが「公開」の商品情報（「商品名」「値段」「画像」）を一覧で表示する。
// 商品の在庫が0の場合、「カートに入れる」ボタンは表示せず、「売り切れ」などの情報を表示する。
// 「カートに入れる」ボタンをクリックした場合、指定の商品をカートに入れる。
// 「ショッピングカートページに遷移する。
?>
<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta chartset="UTF-8">
    <title>商品一覧</title>
    <style>
        table {
            width: 75%;
            margin: 0 auto;
        }
        
        td {
            text-align: center;
        }
        
        body {
            background-color: #eddcbc;
        }
        
        input[type="submit"] {
            background: #1e8eff;
            width: 170px;
            padding: 0.3em;
            color: #fff;
            font-size: 18px;
            border: none;
            border-radius: 8px;
            margin: 25px 25px;
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
        
        .addition {
            display: flex;
            justify-content: center; 
        }
        
        .img {
            margin-top: 40px;
            
        }
        
        .error {
            color: #f00;
            font-size: 24px;
            font-weight: bold;
            text-align: center;
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
  <h1>商品一覧</h1>
  <p>ようこそ、<?php echo $user_name; ?>さん。</p>
  <a href="cart.php"><img src="./product_picture/cart.png" width=65 height=65></a>
  <a href="favorite.php">お気に入り</a>
  <a href="logout.php">ログアウト</a>
<?php foreach ($err_msg as $value) { ?>
  <p class="error"><?php echo $value; ?></p>
<?php } ?>
<?php foreach ($msg as $value) { ?>
  <p class="success"><?php echo $value; ?></p>
<?php } ?>
<form method="post" class="search_container">
    <label>商品検索:
        <input type="text" name="keyword">
        <input type="submit" name="submit" value="検索">
    </label>
</form>
    <table>
<?php foreach ($result as $value) { ?>   
        <tr>
            <td><img class="img" src="./product_picture/<?php echo $value['img']; ?>" width=500 height=400></td>
        </tr>
        <tr>
            <td class="name"><?php echo $value['name']; ?></td>
        </tr>
        <tr>
            <td class="price">&yen;<?php echo $value['price']; ?>
<?php if ($value['stock'] > 0) { ?>
                <div class="addition"><form method="post">
                    <input type="hidden" name="item_id" value="<?php echo($value['id']) ?>">
                    <input type="submit" value="カートに追加">
                </form>
                <form method="post">
                    <input type="hidden" name="item_id" value="<?php echo($value['id']) ?>">
                    <input type="submit" name="favorite" value="お気に入りに追加">
                </form></div>
<?php } else { ?>
                <input type="radio" name="item_id" value="<?php echo $value['id']; ?>">
                <p class="soldout">大変申し訳ございません。現在、売り切れです。</p>
            </td>
<?php } ?>
        </tr>
<?php } ?><hr>
    </table>
  </body>
</html>


