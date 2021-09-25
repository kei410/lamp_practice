<?php
//商品一覧ページ
//session_start();

//データベース接続関連
$host     = 'localhost';      //ホスト名
$username = 'codecamp42398';  //ユーザー名
$password = 'codecamp42398';  //パスワード
$dbname   = 'codecamp42398';  //データベース名
$charset  = 'utf8';
$dsn      = 'mysql:dbname='.$dbname.';host='.$host.';charset='.$charset;

//ログイン機能ページから遷移したときのみ行う処理
// if (isset($_SESSION['customer']['name'])) {
//         echo 'いらっしゃいませ、' . $_SESSION['customer']['name'] . 'さん。';
//     }
    
try {
    // データベースに接続
    $dbh = new PDO($dsn, $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'));
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    //echo 'データベースに接続しました';
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['keyword']) === TRUE) { //&& mb_strlen ($_POST['keyword']) > 0
            $keyword = $_POST['keyword'];
            try {   
                //select * from ... と書かない。全列取得する場合でも select a, b, c, d from ... と書いて*は使わない
                $sql = 'SELECT id, name, price
                        FROM product 
                        WHERE name like ?';
                // prepareでSQL文を実行する準備
                $stmt = $dbh->prepare($sql);
                $stmt->bindValue(1, $keyword, PDO::PARAM_STR);  //文字列なのでSTRにかえる
                // SQLを実行
                $stmt->execute(['%' . $keyword . '%']);
            } catch (PDOException $e) {
              echo '接続できませんでした。理由：'.$e->getMessage();
            } 
        } 
    } else {
            try {   //もしリクエストパラメータに検索キーワードが含まれていないときは商品の一覧を表示する
                $sql = 'SELECT id, name, price
                        FROM product ';
                // prepareでSQL文を実行する準備
                $stmt = $dbh->prepare($sql);
                // SQLを実行
                $stmt->execute([]);
            } catch (PDOException $e) {
              echo '接続できませんでした。理由：'.$e->getMessage();
            }
        } 
    } catch (PDOException $e) {
   echo '予期せぬエラーが発生しました。管理者へお問い合わせください。理由：'.$e->getMessage();
}


//検索して商品が見つからない場合の処理も追加する
//'商品が見つかりませんでした。';


    echo '<table border="2">';
    echo '<th>商品番号</th> <th>商品名</th> <th>価格</th>';
    foreach ($stmt as $row) {  //$sqlに変更する？
        $id = $row['id'];
        echo '<tr>';
        echo '<td>' .  $id . '</td>';
        echo '<td>';
        echo '<a href="detail.php?id='. $id . '">' . $row['name'] . '</a>';
        echo '</td>';
        echo '<td>' . $row['price'] . '</td>';
        echo '</tr>';
    }
    echo '</table>';
?>
<!DOCTYPE html>
<html lang ="ja">
<head>
   <meta charset ="UTF-8">
   <title>商品一覧</title>
</head>
<body>
   <h1>商品一覧</h1>
   <table border="2">
       <tr>
           <th>商品画像</th>
           <th>商品番号</th>
           <th>商品名</th>
           <th>価格</th>
       </tr>
   </table>
   <form method="post">
       商品検索
       <input type="text" name="keyword">
       <input type="submit" name="submit" value="検索">
   </form>
   <hr>
</body>
</html>

<!--
商品の詳細情報を表示するページからカートに商品を追加する
商品テーブルに登録されている商品を検索する機能もある-->