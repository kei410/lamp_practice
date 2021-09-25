<?php             
// 商品管理ページ
// 自動販売機のtool.phpを利用するとわかりやすい
// SQL文はfunctionでまとめる

session_start();

$err_msg          = [];  // エラーメッセージ
$complete_msg     = [];
                         //下のHTMLからデータが飛んでくる変数は全て、''で初期化する
$sql_kind         = '';  // 処理の種類
$new_name         = '';
$new_price        = '';
$new_stock        = '';
$update_stock     = '';
$new_status       = '';
$item_id          = '';
$new_img_filename = '';   // アップロードした新しい画像ファイル名
$img_dir          = './product_picture/';
$date             = date('Y-m-d H:i:s');

$host     = 'localhost';      //ホスト名
$username = 'codecamp42398';  //ユーザー名
$password = 'codecamp42398';  //パスワード
$dbname   = 'codecamp42398';  //データベース名
$charset  = 'utf8';
$dsn      = 'mysql:dbname='.$dbname.';host='.$host.';charset='.$charset;


//  管理者としてログインされてない場合は、ログイン画面にジャンプする (admin、admin以外)

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
    // echo 'データベースに接続しました';
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // 1. 在庫情報の更新処理
        if (isset($_POST['sql_kind']) === TRUE) {
            $sql_kind = $_POST['sql_kind'];
        }
        
        if ($sql_kind === 'update') {
            if (isset($_POST['item_id']) === TRUE) {
                $item_id = trim($_POST['item_id']);
            }
            
            if (isset($_POST['update_stock']) === TRUE) {
                $update_stock = trim($_POST['update_stock']);
            }
            // 次にパラメータのチェック
            if ($item_id === '') {
                $err_msg[] = '商品が選択されていません。';
            } else if (preg_match('/^[1-9][0-9]*$/', $item_id) !== 1) {
                $err_msg[] = '不正な処理です。商品が正しくありません。';
            } 
            
            if ($update_stock === '') {
                $err_msg[] = '在庫数が入力されていません。';
            } else if (preg_match('/^[0-9]+$/', $update_stock) !== 1) {
                $err_msg[] = '在庫数は0以上の整数で入力してください。';
            } 
            
            if (count($err_msg) === 0) {
                try {
                    $sql = 'UPDATE item_stock
                            SET stock = ?, update_date = ?
                            WHERE item_id = ?';
                    $stmt = $dbh->prepare($sql);
                    $stmt->bindValue(1, $update_stock,  PDO::PARAM_INT);  
                    $stmt->bindValue(2, $date,          PDO::PARAM_STR); 
                    $stmt->bindValue(3, $item_id,       PDO::PARAM_INT);
                    $stmt->execute();
                    $complete_msg[] = '在庫変更に成功しました。';
                } catch (PDOException $e) {
                    $err_msg[] = '在庫数が変更できませんでした。理由：' . $e->getMessage();
                }
            } 
        } else if ($sql_kind === 'change') {    // 2. ステータスを更新する
                if (isset($_POST['item_id']) === TRUE) {
                    $item_id = trim($_POST['item_id']);
                }
                if (isset($_POST['change_status']) === TRUE) {
                    $change_status = trim($_POST['change_status']);
                }
                if ($item_id === '') {
                    $err_msg[] = '商品が選択されていません。';
                } else if (preg_match('/^[1-9][0-9]*$/', $item_id) !== 1) {
                    $err_msg[] = '商品が正しくありません。';
                } 
                
                if ($change_status === '') {
                    $err_msg[] = 'ステータスが正しくありません';
                } else if (preg_match('/^[01]$/', $change_status) !== 1) {   //0か1のみ
                    $err_msg[] = '不正な処理です。ステータスは0か1の整数で入力してください';
                } 
                
                if (count($err_msg) === 0) {
                    try {
                        $sql = 'UPDATE product
                                SET status = ?, update_date = ?
                                WHERE id = ?';
                        $stmt = $dbh->prepare($sql);
                        $stmt->bindValue(1, $change_status,   PDO::PARAM_INT);  
                        $stmt->bindValue(2, $date,            PDO::PARAM_STR);  //日付は文字列なのでSTRにする
                        $stmt->bindValue(3, $item_id,         PDO::PARAM_INT); 
                        $stmt->execute();
                        $complete_msg[] = 'ステータスを変更しました。';
                    } catch (PDOException $e) {
                        $err_msg[] = 'ステータスが変更できませんでした。理由：' . $e->getMessage();
                    }
                }
        }
            
        // 以下は商品の追加処理   (画像のアップロード処理を含む)
        // 最初にパラメータのエラーチェックする
        // 入力チェック、sql文、画像のチェックをサーバーにアップロードの順
        // エラーがないならsql文を実行して画像をアップロードする
        // 最後に「商品の追加」と「在庫の追加」をトランザクション処理
        
        if (isset($_POST['insert']) === TRUE) {
            if (isset($_POST['new_name']) === TRUE) {
                $new_name = trim($_POST['new_name']);
            }
            if ($new_name === '') {
                $err_msg[] = '商品名を入力してください。';
            } 
            
            if (isset($_POST['new_price']) === TRUE) {
                $new_price = trim($_POST['new_price']);   
            }
            if ($new_price === '') {
                $err_msg[] = '値段が入力されていません';
            } else if (preg_match('/^[0-9]+$/', $new_price) !== 1) {
                $err_msg[] = '値段は0以上の整数で入力してください';
            } 
            
            if (isset($_POST['new_stock']) === TRUE) {
                $new_stock = trim($_POST['new_stock']);
            }
            if ($new_stock === '') {
                $err_msg[] = '個数を入力してください。';
            } else if (preg_match('/^[0-9]+$/', $new_stock) !== 1) {
                $err_msg[] = '個数は半角数字を入力してください';
            }
            
            if (isset($_POST['new_status']) === TRUE) {
                $new_status = trim($_POST['new_status']);
            }
            if ($new_status === '') {
                $err_msg[] = 'ステータスが正しくありません';
            } else if (preg_match('/^[01]$/', $new_status) !== 1) {   //0か1のみ
                $err_msg[] = 'ステータスは0か1の整数で入力してください';
            }
            
            //アップロードできる「商品画像」のファイル形式は「JPEG」、「PNG」のみ可能とする。（大文字と小文字両方） 
            //「JPEG」、「PNG」以外はエラーメッセージを表示して、商品を追加できない。
            if (count($err_msg) === 0) {
                //画像関連の処理部分
                //is_uploaded_file関数は、指定したファイルがアップロードされたファイルかどうかを確認する
                if (is_uploaded_file($_FILES['new_img']['tmp_name']) === TRUE) {
                    //tmp_nameを指定することで一時的なファイルの名前が取得できる  
                    // 画像の拡張子を取得する
                    $extension = pathinfo($_FILES['new_img']['name'], PATHINFO_EXTENSION);
                    // 指定の拡張子であるかどうかチェック
                    if ($extension === 'png' || $extension === 'jpeg' || $extension === 'jpg') {
                      // 保存する新しいファイル名の生成（ユニークな値を設定する）
                      $new_img_filename = sha1(uniqid(mt_rand(), true)). '.' . $extension;
                      // 同名ファイルが存在するかどうかチェック
                      if (is_file($img_dir . $new_img_filename) !== TRUE) {
                        // アップロードされたファイルを指定ディレクトリに移動して保存
                        if (move_uploaded_file($_FILES['new_img']['tmp_name'], $img_dir . $new_img_filename) !== TRUE) {
                            $err_msg[] = 'ファイルアップロードに失敗しました。';
                        }
                      } else {
                        $err_msg[] = 'ファイルアップロードに失敗しました。再度お試しください。';
                      }
                    } else {
                      $err_msg[] = 'ファイル形式が異なります。画像ファイルはJPEG又はPNGのみ利用可能です。';
                    }
                } else {
                    $err_msg[] = 'ファイルを選択してください。';
                }
            }

            if (count($err_msg) === 0) {
                // トランザクション開始  (商品情報の追加と在庫の追加)
                $dbh->beginTransaction();
                try { 
                    $sql = 'INSERT INTO product (name, price, img, status, create_date, update_date) 
                            VALUES (?, ?, ?, ?, ?, ?)';
                    $stmt = $dbh->prepare($sql);
                    $stmt->bindValue(1, $new_name,           PDO::PARAM_STR);  
                    $stmt->bindValue(2, $new_price,          PDO::PARAM_INT); 
                    $stmt->bindValue(3, $new_img_filename,   PDO::PARAM_STR); 
                    $stmt->bindValue(4, $new_status,         PDO::PARAM_INT);
                    $stmt->bindValue(5, $date,               PDO::PARAM_STR);
                    $stmt->bindValue(6, $date,               PDO::PARAM_STR);
                    $stmt->execute();
                    // INSERT文ではINSERTされたデータのIDを取得する。(オートインクリメント)
                    $item_id = $dbh->lastInsertId('item_id');
    
                    // 在庫情報の登録
                    $sql = 'INSERT INTO item_stock (item_id, stock, create_date, update_date) 
                            VALUES (?, ?, ?, ?)';
                    $stmt = $dbh->prepare($sql);
                    $stmt->bindValue(1, $item_id,     PDO::PARAM_INT);
                    $stmt->bindValue(2, $new_stock,   PDO::PARAM_INT);
                    $stmt->bindValue(3, $date,        PDO::PARAM_STR);
                    $stmt->bindValue(4, $date,        PDO::PARAM_STR);
                    $stmt->execute();
                    $dbh->commit();
                    $complete_msg[] = '商品の追加に成功しました。';
                } catch (PDOException $e) {
                  // ロールバック処理
                    $dbh->rollback();
                    $err_msg[] = '申し訳ございません。商品が追加できませんでした。' . $e->getMessage();;
                }
            }
        }  // 139行目isset($_POST['insert']の}   
            
            
        //  削除処理は最後に記述する
        // 「商品情報の削除(productテーブル)」と「商品の在庫情報(item_stockテーブル)」をトランザクション処理する。    
            
        if (isset($_POST['delete']) === TRUE) {
            if (isset($_POST['item_id']) === TRUE) {
                    $item_id = trim($_POST['item_id']);
            }
            if ($item_id === '') {
                $err_msg[] = '商品が選択されていません。';
            } else if (preg_match('/^[1-9][0-9]*$/', $item_id) !== 1) {
                $err_msg[] = '不正な処理です。商品が正しくありません。';
            } 
            
            if (count($err_msg) === 0) {
                // トランザクション開始  (商品情報の削除と在庫情報を削除)
                $dbh->beginTransaction();
                try { // 商品情報の削除
                    $sql = 'DELETE FROM product
                            WHERE id = ?';
                    $stmt = $dbh->prepare($sql);
                    $stmt->bindValue(1, $item_id,  PDO::PARAM_INT);
                    $stmt->execute();
                    $rows = $stmt->fetchAll();
                    // 次に在庫情報の削除
                    $sql = 'DELETE FROM item_stock 
                            WHERE item_id = ?';
                    $stmt = $dbh->prepare($sql);
                    $stmt->bindValue(1, $item_id,  PDO::PARAM_INT);
                    $stmt->execute();
                    $rows = $stmt->fetchAll();
                    $dbh->commit();
                    $complete_msg[] = '商品を削除しました。';
                } catch (PDOException $e) {
                    $dbh->rollback();
                    $err_msg[] = '商品の削除に失敗しました。 '.$e->getMessage();
                }
            }
        }    
    } // 59行目の} REQUEST_METHOD


    //  販売商品情報を一覧で取得する。
    try {
        $sql = 'SELECT product.id, name, price, img, status, item_stock.stock
                FROM product
                JOIN item_stock
                    ON  product.id = item_stock.item_id';
        $stmt = $dbh->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll();
// print_r($data);
// exit;
    } catch (PDOException $e) {
        $err_msg[] = '商品一覧が取得できませんでした。';
    }
} catch (PDOException $e) {
    $err_msg[] = '予期せぬエラーが発生しました。管理者へお問い合わせください。理由：'.$e->getMessage();
}



//   商品管理ページ    最後につくると分かりやすい
// 　管理者としてログインされてない場合は、ログイン画面にジャンプする
// 　商品一覧表の表示
// 　商品在庫数の変更機能
// 　商品ステータスの変更機能
// 　商品の削除機能
// 　商品の新規登録機能
//   foreach文を使うときは、$valueを使う
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>商品管理</title>
    <style>
        table, tr, th, td {
            border:solid 1px;
        }
        
        .name {
            font-size: 20px;
            font-weight: bold;
            text-align: center;
        }
        
        .img {
            width: 450px;
            height: 350px;
        }
        
        .status_false{
            background-color: #ccc;
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
    <h1>Snack Online SHOP 商品管理</h1>
    <a href="admin_user.php">ユーザー管理</a>
<?php foreach ($err_msg as $value) { ?>
    <p class="error"><?php echo $value;?></p>
<?php }?>
<?php foreach ($complete_msg as $value) { ?>
    <p class="success"><?php echo $value;?></p>
<?php }?>
<section>
    <h2>商品登録</h2>
    <form method="post" enctype="multipart/form-data">
      <p><label>商品名: <input type="text" name="new_name"></label></p>
      <p><label>値　段: <input type="text" name="new_price"></label></p>
      <p><label>個　数: <input type="text" name="new_stock"></label></p>
      <p><label>商品画像: <input type="file" name="new_img"></label></p>
      <div><label>ステータス:
        <select name="new_status">
          <option value="0">非公開</option>
          <option value="1">公開</option>
        </select>
        </label>
      </div><br>
      <input type="hidden" name="sql_kind" value="insert">
      <p><input type="submit" name="insert" value="■□■□■商品追加■□■□■"></p>
      </form>
  </section>
  <section>
    <h2>商品情報の一覧・変更</h2>
    <table>
      <tr>
        <th>商品画像</th>
        <th>商品名</th>
        <th>価　格</th>
        <th>在庫数</th>
        <th>ステータス</th>
        <th>操作</th>
      </tr>
<?php foreach ($data as $value)  { ?>
<?php if (htmlspecialchars($value['status'], ENT_QUOTES, 'UTF-8') === '1') { ?>
      <tr>
<?php } else { ?>
      <tr class="status_false">
<?php } ?>
          <td><img class="img" src="<?php echo $img_dir . $value['img']; ?>"></td>
          <td class="name"><?php echo htmlspecialchars($value['name'], ENT_QUOTES, 'UTF-8');?></td>
          <td><?php echo htmlspecialchars($value['price'], ENT_QUOTES, 'UTF-8');?>円</td>
        <form method="post">
            <td><input type="text" name="update_stock" value="<?php echo htmlspecialchars($value['stock'], ENT_QUOTES, 'UTF-8');?>">個&nbsp;&nbsp;<input type="submit" value="変更する"></td>
            <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($value['id'], ENT_QUOTES, 'UTF-8');?>">
            <input type="hidden" name="sql_kind" value="update">
        </form>
        <form method="post">
<?php if (htmlspecialchars($value['status'], ENT_QUOTES, 'UTF-8') === '1') { ?>
          <td><input type="submit" name="status_button" value="公開 → 非公開"></td>
          <input type="hidden" name="change_status" value="0">
<?php } else { ?>
          <td><input type="submit" name="status_button" value="非公開 → 公開"></td>
          <input type="hidden" name="change_status" value="1">
<?php } ?>
          <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($value['id'], ENT_QUOTES, 'UTF-8');?>">
          <input type="hidden" name="sql_kind" value="change">
        </form>
        <form method="post">
          <td><input type="submit" name="delete" value="削除する"></td>
          <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($value['id'], ENT_QUOTES, 'UTF-8');?>">
          <input type="hidden" name="sql_kind" value="delete">
        </form>
      </tr>
<?php } ?>
    </table>
  </section>
</body>
</html>