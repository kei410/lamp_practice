<?php                    //tool.phpは追加、変更、表示の役割をする
$drink_master     = [];
$err_msg          = [];  // エラーメッセージの格納先
$complete_msg     = [];
$drink_data       = [];
$drink_id         = '';  //下からデータが飛んでくる変数は全て、''で初期化する
$stock            = '';
$new_img          = '';
$new_status       = '';
$drink_name       = '';
$price            = '';
$status           = '';
$new_img_filename = '';  // アップロードした新しい画像ファイル名
$img_dir          = './drink_picture/';
$now_date         = date('Y-m-d H:i:s');

//改行して初期化の部分とデータベース接続の部分に分ける
$host     = 'localhost';      //ホスト名
$username = 'codecamp42398';  //ユーザー名
$password = 'codecamp42398';  //パスワード
$dbname   = 'codecamp42398';  //データベース名
$charset  = 'utf8';
$dsn      = 'mysql:dbname='.$dbname.';host='.$host.';charset='.$charset;
// date_default_timezone_set('Asia/Tokyo');  // タイムゾーン設定

//アップロードできる「商品画像」のファイル形式は「JPEG」「PNG」のみ可能とする。（大文字小文字両方） 
//「JPEG」、「PNG」以外はエラーメッセージを表示して、商品を追加できない。
// アップロード画像ファイルの保存

try {
    // データベースに接続する
    $dbh = new PDO($dsn, $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'));
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['stock_button']) === TRUE) {
            if (isset($_POST['drink_id']) === TRUE) {
                $drink_id = trim($_POST['drink_id']);
            }
            if (isset($_POST['stock']) === TRUE) {
                $stock = trim($_POST['stock']);
            }
            if ($drink_id === '') {
                $err_msg[] = '商品が選択されていません';
            } else if (preg_match('/^[1-9][0-9]*$/', $drink_id) !== 1) {
                $err_msg[] = '商品が正しくありません';
            } 
            if ($stock === '') {
                
                $err_msg[] = '在庫数が入力されていません';
            } else if (preg_match('/^[0-9]+$/', $stock) !== 1) {
                $err_msg[] = '在庫数は0以上の整数で入力してください';
            } 
            if (count($err_msg) === 0) {
                try {
                    $sql = 'UPDATE drink_stock
                            SET stock = ?,
                                update_datetime = ?
                            WHERE drink_id = ?';
                    $stmt = $dbh->prepare($sql);
                    // SQL文のプレースホルダに値をバインドする
                    $stmt->bindValue(1, $stock,    PDO::PARAM_INT);  //ここをPARAM_STRではなくてINTにかえる
                    $stmt->bindValue(2, $now_date, PDO::PARAM_STR);  //日付は文字列なのでSTRにする
                    $stmt->bindValue(3, $drink_id, PDO::PARAM_INT);
                    // SQLを実行する
                    $stmt->execute();
                    // コミット処理
                    $complete_msg[] = '在庫変更成功';
                } catch (PDOException $e) {
                    $err_msg[] = '在庫数が変更できませんでした。理由：' . $e->getMessage();
                }
            }
        }
        if (isset($_POST['status_button']) === TRUE) {
            if (isset($_POST['drink_id']) === TRUE) {
                $drink_id = trim($_POST['drink_id']);
            }
            if (isset($_POST['status']) === TRUE) {
                $status = trim($_POST['status']);
            }
            if ($drink_id === '') {
                $err_msg[] = '商品が選択されていません';
            } else if (preg_match('/^[1-9][0-9]*$/', $drink_id) !== 1) {
                $err_msg[] = '商品が正しくありません';
            } 
            if ($status === '') {
                $err_msg[] = 'ステータスが正しくありません';
            } else if (preg_match('/^[01]$/', $status) !== 1) { //0か1のみ
                $err_msg[] = 'ステータスは0か1の整数で入力してください';
            } 
            if (count($err_msg) === 0) {
                try {
                    $sql = 'UPDATE drink_master
                            SET status = ?,
                                update_datetime = ?
                            WHERE drink_id = ?';
                    $stmt = $dbh->prepare($sql);
                    // SQL文のプレースホルダに値をバインドする
                    $stmt->bindValue(1, $status,   PDO::PARAM_INT);  //ここをPARAM_STRではなくてINTにかえる
                    $stmt->bindValue(2, $now_date, PDO::PARAM_STR);  //日付は文字列なのでSTRにする
                    $stmt->bindValue(3, $drink_id, PDO::PARAM_INT); 
                    // SQLを実行する
                    $stmt->execute();
                    // コミット
                    $complete_msg[] = 'ステータス変更成功';
                } catch (PDOException $e) {
                    $err_msg[] = 'ステータスが変更できませんでした。理由：' . $e->getMessage();
                }
            }
        }
        
        if (isset($_POST['insert']) === TRUE) {

            if (isset($_POST['drink_name']) === TRUE) {
                $drink_name = trim($_POST['drink_name']);
            }
            if (isset($_POST['price']) === TRUE) {
                $price = trim($_POST['price']);   //4項目(名前drink_name、値段price、在庫数stock、ステータスnew_status)についてつくる
            }
            if (isset($_POST['stock']) === TRUE) {
                $stock = trim($_POST['stock']);
            }
            if (isset($_POST['new_status']) === TRUE) {
                $new_status = trim($_POST['new_status']);
            }
            if ($drink_name === '') {
                $err_msg[] = '商品名を入力してください';
            } 
            if ($price === '') {
                $err_msg[] = '値段が入力されていません';
            } else if (preg_match('/^[0-9]+$/', $price) !== 1) {
                $err_msg[] = '値段は0以上の整数で入力してください';
            } 
            if ($stock === '') {
                $err_msg[] = '個数が入力されていません';
            } else if (preg_match('/^[0-9]+$/', $stock) !== 1) {
                $error[] = '個数は0以上の整数で入力してください';
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

                //追加処理
                //入力チェック、sql文、画像のチェックをサーバーにアップロードの順
                //エラーがないならsql文を実行して画像をアップロードする
                //「商品の追加」と「在庫の追加」をトランザクション処理する
            if (count($err_msg) === 0) {
                // トランザクション開始  (商品の追加と在庫の追加)
                $dbh->beginTransaction();
                
                try { 
                    $sql = 'INSERT INTO drink_master (drink_name, price, img, status, create_datetime) 
                            VALUES (?, ?, ?, ?, ?)';
                    $stmt = $dbh->prepare($sql);
                    $stmt->bindValue(1, $drink_name,         PDO::PARAM_STR);  
                    $stmt->bindValue(2, $price,              PDO::PARAM_INT); 
                    $stmt->bindValue(3, $new_img_filename,   PDO::PARAM_STR); 
                    $stmt->bindValue(4, $new_status,         PDO::PARAM_INT);
                    $stmt->bindValue(5, $now_date,           PDO::PARAM_STR);
                    $stmt->execute();
                    $drink_id = $dbh->lastInsertId();
                    // SQL文を作成
                    $sql = 'INSERT INTO drink_stock (drink_id, stock, create_datetime) 
                            VALUES (?, ?, ?)';
                    // SQL文を実行する準備
                    $stmt = $dbh->prepare($sql);
                    // SQL文のプレースホルダに値をバインドする
                    $stmt->bindValue(1, $drink_id,    PDO::PARAM_INT);
                    $stmt->bindValue(2, $stock,       PDO::PARAM_STR);
                    $stmt->bindValue(3, $now_date,    PDO::PARAM_STR);
                    // SQLを実行
                    $stmt->execute();
                    // コミット
                    $dbh->commit();
                    $complete_msg[] = '商品追加成功';
                } catch (PDOException $e) {
                  // ロールバック処理
                  $dbh->rollback();
                  $err_msg[] = '商品が追加できませんでした。' . $e->getMessage();;
                  // 例外をスロー (今回は不要)
                  //throw $e;
                }
            }
        } 
    } 
    
    try {
        // SQL文を作成
        $sql = 'SELECT drink_master.drink_id, img, drink_name, price, status, stock
                FROM drink_master
                JOIN drink_stock
                    ON drink_master.drink_id = drink_stock.drink_id ';
        // prepareでSQL文を実行する準備
        $stmt = $dbh->prepare($sql);
        // SQLを実行
        $stmt->execute();
        $drink_data = $stmt->fetchAll();
    } catch (PDOException $e) {
        $err_msg[] = '商品一覧が取得できませんでした。';
    }
} catch (PDOException $e) {
    $err_msg[] = '予期せぬエラーが発生しました。管理者へお問い合わせください。理由：'.$e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>自動販売機の管理画面</title>
        <style>
            table,tr,th,td {
                border:solid 1px;
            }
            .status_false{
                background-color:#ccc;
            }
        </style>
    </head>
    <body>
        <h1>自動販売機管理ツール</h1>
<?php foreach ($err_msg as $value) { ?>
            <p><?php echo $value;?></p>
<?php }?>
<?php foreach ($complete_msg as $value) { ?>
            <p><?php echo $value;?></p>
<?php }?>
        <section>
            <h2>新規商品追加</h2>
            <form method="post" enctype="multipart/form-data">
                <p><label>名前<input type="text" name="drink_name"></label></p>
                <p><label>値段<input type="text" name="price"></label></p>
                <p><label>個数<input type="text" name="stock"></label></p>
                <p><input type="file" name="new_img"></p> 
                <select name="new_status"><br>
                    <option value="0">非公開</option>
                    <option value="1">公開</option>
                </select><br>
                <input type="hidden" name="sql_kind" value="insert">
                <p><input type="submit" name="insert" value="■□■□■商品追加■□■□■"></p>
            </form>
        </section>
        <section>
            <h2>商品情報変更</h2>
            <caption>商品一覧</caption>
            <table>
                <tr>
                    <th>商品画像</th>
                    <th>商品名</th>
                    <th>価格</th>
                    <th>在庫数</th>
                    <th>ステータス</th>
                </tr>
<?php foreach ($drink_data as $value) { ?>
<?php if (htmlspecialchars($value['status'], ENT_QUOTES, 'UTF-8') === '1') { ?>
                <tr>
<?php } else {?>
                <tr class="status_false">
<?php }?>
                    
                    <td><img src="<?php print htmlspecialchars($img_dir.$value['img'], ENT_QUOTES, 'UTF-8'); ?>" width=100 height=150></td>
                    <td><?php print htmlspecialchars($value['drink_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php print htmlspecialchars($value['price'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td> <!--HTMLでは、=の前後をくっつける -->
                        <form method="post">
                            <input type="hidden" name="drink_id" value="<?php print htmlspecialchars($value['drink_id'], ENT_QUOTES, 'UTF-8'); ?>">
                            <input type="text" name="stock" value="<?php print htmlspecialchars($value['stock'], ENT_QUOTES, 'UTF-8'); ?>">個
                            <input type="submit" name="stock_button" value="変更">
                        </form>    
                    </td>
                    <td>
                        <form method="post">
                            <input type="hidden" name="drink_id" value="<?php print htmlspecialchars($value['drink_id'], ENT_QUOTES, 'UTF-8'); ?>">
<?php if (htmlspecialchars($value['status'], ENT_QUOTES, 'UTF-8') === '1') { ?> 
                            <input type="hidden" name="status" value='0' >
                            <input type="submit" name="status_button" value="公開→非公開">  
<?php } else {?>
                            <input type="hidden" name="status" value='1' >
                            <input type="submit" name="status_button" value="非公開→公開">  
<?php }?>
                        </form>    
                    </td>
                </tr>
<?php }?>
            </table>
        </section>
    </body>
</html>

