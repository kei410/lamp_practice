<?php
session_start();
// 新規会員登録ページ
// ログイン画面にジャンプできる機能を追加する
// ユーザー名、パスワードは半角英数字6文字以上にする

$new_user     = '';  // 新規ユーザー名の変数
$new_pass     = '';  // 新規パスワードの変数
$err_msg      = [];  //エラーメッセージの格納先
$msg          = [];
$date = date('Y-m-d H:i:s');
//$_SESSION['customer']はlogin.phpで定義

$host     = 'localhost';      //ホスト名
$username = 'codecamp42398';  //ユーザー名
$password = 'codecamp42398';  //パスワード
$dbname   = 'codecamp42398';  //データベース名
$charset  = 'utf8';
$dsn      = 'mysql:dbname='.$dbname.';host='.$host.';charset='.$charset;


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_SESSION['customer'])) {
        // $user_name = $_SESSION['customer']['name'];
        $login = $_SESSION['customer']['user_name'];
        $pass = $_SESSION['customer']['password'];
    }
    
    // フォームから入力したユーザー名をチェックをする
    if (isset($_POST['new_user']) === TRUE) {
            $new_user = trim($_POST['new_user']);
            if (mb_strlen($new_user) < 6) {
                $err_msg[] = 'ユーザー名は6文字以上の文字を入力してください。';
            } else if (preg_match('/^[a-zA-Z0-9]+$/', $new_user) !== 1) {
                $err_msg[] = 'ユーザー名は半角英数字を入力してください。';
            }
    }  
    
    // フォームから入力したパスワードをチェックをする
    if (isset($_POST['new_pass']) === TRUE) {
            $new_pass = trim($_POST['new_pass']);
            if (mb_strlen($new_pass) < 6) {
                $err_msg[] = 'パスワードは6文字以上の文字を入力してください。';
            } else if (preg_match('/^[a-zA-Z0-9]+$/', $new_pass) !== 1 ) {
                $err_msg[] = 'パスワードは半角英数字を入力してください';
            }
    }
            
    // if(count($err_msg) === 0) {       
        try {   
            $dbh = new PDO($dsn, $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'));
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            //  ユーザー名が既に存在しているか確認
            $sql = 'SELECT id, user_name, password
                    FROM customer
                    WHERE user_name = ?';
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(1, $new_user, PDO::PARAM_STR);
            $stmt->execute();
            $rows = $stmt->fetchAll();
            // print_r($rows);
            // exit();
            if (count($rows) > 0) {
                $err_msg[] = '同じユーザー名が既に登録されています。';
            }
        } catch (PDOException $e) {
            $err_msg[] = 'ユーザー名の登録に失敗しました。理由：'.$e->getMessage();
        }    
        
        // エラーメッセージが0なら新規登録する
        if(count($err_msg) === 0) {     
            try {   
                $dbh = new PDO($dsn, $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'));
                $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
                // 新規ユーザー情報を登録する
                $sql = 'INSERT INTO customer(user_name, password, create_date, update_date)
                        VALUES (?, ?, ?, ?)';
                $stmt = $dbh->prepare($sql);
                $stmt->bindValue(1, $new_user,   PDO::PARAM_STR);
                $stmt->bindValue(2, $new_pass,   PDO::PARAM_STR);
                $stmt->bindValue(3, $date,       PDO::PARAM_STR);
                $stmt->bindValue(4, $date,       PDO::PARAM_STR);
                $stmt->execute();
                $msg[] = 'ユーザー登録が完了しました!';
            } catch (PDOException $e) {
                $err_msg[] = 'アカウントの作成に失敗しました。理由：'.$e->getMessage();
            }
        }
}

// if (preg_match('/^[0-9a-zA-z]{6,}$/', $username) !== 1) {
//     $err_msg[] = 'ユーザー名は半角英数字6文字以上で入力してください。';
// }

// if (preg_match('/^[0-9a-zA-z]{6,}$/', $userpassword) !== 1) {
//     $err_msg[] = 'パスワードは半角英数字6文字以上で入力してください。';
// }



// 「ユーザ名」「パスワード」を入力する項目を表示する。
// 「登録」ボタンをクリックした場合、入力した「ユーザ名」「パスワード」をデータベースに保存する。
// 画面に登録完了のメッセージを表示する。
// ユーザ情報を追加する場合、「ユーザ名」「パスワード」は、「使用可能文字は半角英数字」かつ
// 「文字数は 6 文字以上」のみを可能とする。それ以外の場合に「登録」ボタンをクリックした場合、
// エラーメッセージを表示して、ユーザ情報を追加できない。
// ユーザ情報を追加する場合、既に同じ「ユーザ名」が登録されている場合、エラーメッセージを表示して、ユーザ情報を追加できない。

?>
<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <title>ユーザー登録</title>
    <style>
        form {
            margin: 0 auto;
            width: 300px;
            background: #fff;
            max-width: 800px;
            padding: 70px 50px;
        }
        
        body {
            background: #ccc;
            padding-top: 50px;
        }
            
        input[type="submit"] {
            background: #ffa60c;
            width: 200px;
            padding: 0.7em;
            color: #fff;
            font-size: 18px;
            border: none;
            border-radius: 6px;
        }
            
        h1 {
            color: #ffa60c;
            font-size: 30px;
            text-align: center;
        }
        
        .register {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .login {
            text-align: center;
        }
        
        .form-item {
            justify-content: center;
            margin-bottom: 50px;
            font-weight: bold;
            font-size: 20px;
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
    <h1>ユーザー登録</h1>
<?php foreach ($err_msg as $value) { ?>
    <p class="error"><?php echo $value; ?></p>
<?php } ?>
<?php foreach ($msg as $value) { ?>
    <p class="success"><?php echo $value; ?></p>
<?php } ?>
    <form method="post">
      <p class="form-item">
        <label>ユーザー名：<input type="text" name="new_user"></label>
      </p>
      <p class="form-item">
        <label>パスワード：<input type="password" name="new_pass"></label>
      </p>
      <div class="register">
          <input type="submit" value="新規登録">
          <a href="membership_registration.php"></a>
      </div>
      <div class="login">
          <a href="login.php">ログイン画面</a>
      </div>
    </form>
  </body>
</html>