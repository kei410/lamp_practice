<?php
// ユーザー一覧のページ

session_start();

// 変数初期化
$err_msg = [];  //エラーエッセージの格納先
$msg     = [];

$host     = 'localhost';      
$username = 'codecamp42398';  
$password = 'codecamp42398';  
$dbname   = 'codecamp42398';  
$charset  = 'utf8';
$dsn      = 'mysql:dbname='.$dbname.';host='.$host.';charset='.$charset;

// 管理者としてログインしているかチェックする
if (isset($_SESSION['customer']['user_name']) === TRUE) { 
    $user_name = htmlspecialchars($_SESSION['customer']['user_name'], ENT_QUOTES, 'UTF-8');
    if ($user_name !== 'admin') {
        header("location: ./login.php");   
        exit();
    }
} else {
    header("location: ./login.php");   
    exit();
}

    try {
        $dbh = new PDO($dsn, $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'));
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            //  ユーザー情報を作成した日付順に並べる
            $sql = 'SELECT user_name, create_date
                    FROM customer 
                    ORDER BY create_date';
            $stmt = $dbh->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll();
// print_r($result);
// exit();
            if ($result) {
                foreach ($result as $key => $value) {
                    foreach ($value as $keys => $values) {
                        // 特殊文字をHTMLエンティティに変換する
                        $result[$key][$keys] = htmlspecialchars($values, ENT_QUOTES, 'UTF-8');
                    }
                }
            } else {
                $err_msg[] = 'ユーザー情報が登録されていません。';
            }
            
// vardampで中身を確認する
// var_dump($result);
// exit();
    } catch (PDOException $e) {
      $err_msg[] = 'エラーが発生しました。管理者へお問い合わせください。'.$e->getMessage();
    }

// 管理者としてログインされてない場合(ユーザー名とパスワードがadmin以外)は、ログイン画面にジャンプする
// 登録済ユーザーの一覧表の表示
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>ユーザー管理</title>
  <style>
      table {
          width: 30%;
          margin: 0 auto;
      }
      
      h2 {
          text-align: center;
      }
      
      .error {
          color: #f00;
          font-weight: bold;
          text-align: center;
      }
  </style>
</head>
<body>
<?php foreach ($err_msg as $value) { ?>
  <p class="error"><?php echo $value; ?></p>
<?php } ?>
  <h1>Snack Online SHOP ユーザー管理ページ</h1>
  <div>
    <a href="./admin.php" target="_blank">商品管理</a>
  </div>
  <h2>ユーザー情報一覧</h2>
  <table border="2">
    <tr>
      <th>ユーザー名</th>
      <th>登録日</th>
    </tr>
<?php foreach ($result as $value)  { ?>
    <tr>
      <td><?php echo $value['user_name']; ?></td>
      <td><?php echo $value['create_date']; ?></td>
    </tr>
<?php } ?>
  </table>
</body>
</html>