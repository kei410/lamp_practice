<?php
//ECサイトログインページ

session_start();   //クッキーにセッションIDという合言葉を記憶して、サーバーも同じ合言葉を記憶する。
//unset($_SESSION['customer']); //顧客情報を配列$_SESSIONの添え字customerに格納する
//同名のユーザーが既にログインしている場合はログアウトにする。
//セッションデータから既に存在する顧客情報を削除する。(unset関数)
// unset($_SESSION['loginstatus']);

$login = '';  //ブラウザで開いたときにNotice: Undefined indexが表示されないように初期化しておく
$pass  = '';
$msg   = '';  //エラーメッセージ

$host     = 'localhost';      //ホスト名
$username = 'codecamp42398';  //ユーザー名
$password = 'codecamp42398';  //パスワード
$dbname   = 'codecamp42398';  //データベース名
$charset  = 'utf8';
$dsn      = 'mysql:dbname='.$dbname.';host='.$host.';charset='.$charset;

//  下から飛んできたログインとパスワードをそれぞれ定義する
if ($_SERVER['REQUEST_METHOD'] === 'POST') { //下からpostで飛んでくるチェックする。
    if (isset($_POST['user_name']) === TRUE) {
        $login = filter_input(INPUT_POST, 'user_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS); //バリデーション処理
        //FILTER_SANITIZE_FULL_SPECIAL_CHARSはhtmlspecialchars() に ENT_QUOTES を指定してコールするのと同じ
        //$login = htmlspecialchars($_POST['login'], ENT_QUOTES, 'UTF-8');
    }
    if (isset($_POST['password']) === TRUE) {
        $pass = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        //$pass = htmlspecialchars($_POST['password'], ENT_QUOTES, 'UTF-8');
    }
    
// var_dump($pass,$login);
// exit();

    // admin、adminでログインした場合のみ商品管理ページに移動する
    if (isset($_POST['user_name']) && isset($_POST['password']) ) {
        if ($_POST['user_name'] === 'admin' && $_POST['password'] === 'admin') {
            $_SESSION['customer'] = [
                                     'user_name'   => 'admin'
                                    ];
            header("location: ./admin.php");   
            exit();
        }
    }
    
    try {   
        $dbh = new PDO($dsn, $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'));
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $sql = 'SELECT id, user_name, password
                FROM customer 
                WHERE user_name = ? and password = ?';
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(1, $login,  PDO::PARAM_STR);  
        $stmt->bindValue(2, $pass,   PDO::PARAM_STR);  //文字列はSTRにかえる
        $stmt->execute([$login, $pass]);
        
        foreach ($stmt as $row) { //$rowにはデータベースから取得した顧客テーブルの行が格納されている。
        //var_dump($stmt);
        // 'name'    => $row['name'],
        // パスワードはセキュリティの点からセッションには保存しない
            $_SESSION['customer'] = [
                                     'id'          => $row['id'], 
                                     'user_name'   => $row['user_name'],
                                    ];
        } 
    
    } catch (PDOException $e) {
      echo '接続できませんでした。理由：'.$e->getMessage(); 
    } 
    
    if (isset($_SESSION['customer']) === TRUE ) {
        header("location: ./top.php");   //ログインに成功した場合のみ商品一覧ページに移動できるようにする。
        exit();    //リダイレクト後は何もさせないようにexitを記述する。
                   //echo 'いらっしゃいませ、' . $_SESSION['customer']['name'] . 'さん。';
    } else if ($login === '' || $pass === '') {  //ユーザー名 パスワードのどちらかが入力されていないときの処理
        $msg = 'ログインする際は、ログイン名とパスワードを入力してください。';
        // ログインに失敗したらログイン専用ページに遷移する。
    } else {
        $msg = 'ログイン名またはパスワードが違います。';
        // ログインに失敗したらログイン専用ページに遷移する。
    }
}


// すでにログイン済みの場合は会員トップページにジャンプする   (REQUESTMETHODの外側に記述する)
if (isset($_SESSION['customer']['user_name']) === TRUE) { 
    header('Location: top.php');
    exit();      
} 
    
    
//コメントはHTML側ではなく、PHP側に書いておくこと
// 「ユーザ名」「パスワード」を入力する項目を表示する。
// 「ログイン」ボタンをクリックした場合、「ユーザ名」と「パスワード」が登録されているユーザ情報と一致したとき、
// 「商品一覧ページ」に遷移する。
// 「ログイン」ボタンをクリックした場合、「ユーザ名」と「パスワード」が登録されているユーザ情報と一致しない、
// エラーメッセージを表示して、ログインすることができない。
?>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>ログイン専用画面</title>
        <style>
            h1 {
                font-size: 30px;
                color: #1b9aaa;
                text-align: center;
                margin: 50px 0;     /*[上下][左右]のマージン指定*/
            }
            
            body {
                background: #ccc;
                padding-top: 50px;
            }
            
            form {
                margin: 0 auto;
                width: 300px;
                background: #fff;
                max-width: 800px;
                padding: 70px 50px;
            }
            
            input[type="submit"] {
                background: #1b9aaa;
                width: 170px;
                padding: 0.7em;
                color: #fff;
                font-size: 16px;
                border: none;
                border-radius: 4px;
            }
            
            input[type="checkbox"] {
                margin-bottom: 30px;
            }
            
            .back { /*aタグはinline要素なので、divで囲む*/
                text-align: center;
            }
    
            .form-item {
                justify-content: center;
                margin-bottom: 50px;
                font-weight: bold;
                font-size: 20px;
            }
            
            .button {
                text-align: center;
            }
            
            .login {
              text-align: center;
              margin-bottom: 45px;　
            }
        </style>
    </head>
    <body>
        <form method="post"> 
            <h1>ログイン画面</h1>
            <p class="form-item">
                <label>ユーザー名: <input type="text" name="user_name"></label>
            </p>
            <p class="form-item">
                <label>パスワード: <input type="password" name="password"></label>
            </p>
            <!--<p>-->
            <!--    <label><input type="checkbox" id="save" name="save" value="on" />次回からは自動的にログインする</label>-->
            <!--</p>-->
            <p class="login">
                <input type="submit" value="ログイン">
            </p>
            <form method="post">
            <div class="button">
                <a href="membership_registration.php">新規登録フォームはこちらから!</a>
            </div>
            </form>
        </form>
        <h1 style="color:#f00;"><?php echo $msg; ?></h1>
        <div class="back">
            <a href="login.php">戻る</a>
        </div>
    </body>
</html>

