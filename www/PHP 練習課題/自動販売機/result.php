<?php 
$err_msg    = [];
$drink_data = [];
$drink_id   = '';
$drink_name = '';
$change     = '';
$img        = '';

$host     = 'localhost'; //ホスト名
$username = 'codecamp42398'; //ユーザー名
$password = 'codecamp42398'; //パスワード
$dbname   = 'codecamp42398'; //データベース名
$link     = mysqli_connect($host, $username, $password, $dbname);

// (isset関数を使って判定する)
    if ($link) {
        // 文字化け防止
        mysqli_set_charset($link, 'utf8');    
        //POST情報が入っていたら   
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {        
            if (isset($_POST['money']) === TRUE) {
                $money = trim($_POST['money']);
            }
            // drink_idを取得
            if (isset($_POST['drink_id']) === TRUE) {
                $drink_id = trim($_POST['drink_id']);
            } 
            
            if ($money === '') {
               $err_msg[] = 'お金が投入されていません';
            
            // 整数以外が入力
            } else if(preg_match('/^[0-9]+$/', $money) !== 1) {
                $err_msg[] = '投入金額は0以上の整数を入力してください';
            }
            if ($drink_id === '') {
                $err_msg[] = '商品を選択してください';
            } else if (preg_match('/^[1-9][0-9]*$/', $drink_id) !== 1) {
                $err_msg[] = '商品が正しくありません';
            }
            
            if (count($err_msg) === 0) {
            // 商品情報を取得
            //sql文は.と''でつなげる
                $sql = 'SELECT drink_master.drink_id, img, drink_name, price, status, stock
                        FROM drink_master
                        JOIN drink_stock
                            ON drink_master.drink_id = drink_stock.drink_id
                        WHERE drink_master.drink_id = ' . $drink_id;
                    
                //クエリ実行 
                if ($result = mysqli_query($link, $sql)) {
                    while ($row = mysqli_fetch_array($result)) {
//print_r($row);
//exit;
                        foreach ($row as $key => $value) {
                            $row[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                        }
                        $drink_data[] = $row;
                    } //foreach文を使ってシンプルに表現する
//print_r($sql);
//exit;
                } else {
                    $err_msg[] = '取得失敗' . $sql;
                }
                
                if (count($drink_data) > 0) {
                    // 情報を各変数へ代入
                    //二重配列
                    $img          = $drink_data[0]['img'];
                    $drink_name   = $drink_data[0]['drink_name'];
                    $price        = (int)$drink_data[0]['price'];
                    $stock_number = (int)$drink_data[0]['stock'];
                    $open_status  = (int)$drink_data[0]['status'];
                    //$money = $_POST['money'];
                    // エラーチェック
                    //  金額が足りない
                    if ((int)$money < $price) {
                        $err_msg[] = 'お金が足りません';
                    }
                    // 購入ページを開いた後に在庫0になった
                    if ($stock_number <= 0) {
                        $err_msg[] = '在庫が切れました';
                    }
                    // 購入ページを開いた後にステータス0になった
                    if ($open_status !== 1) {
                        $err_msg[] = '公開出来なくなりました・・・・';
                    }
                } else {
                    $err_msg[] = '商品が見つかりませんでした';
                }
            }
            //93行目まではエラーのチェック
            // エラーがなければ
            if (count($err_msg) === 0) {
                // オートコミット
                mysqli_autocommit($link, false);
                
                // 在庫1本減らす
                $sql = 'UPDATE drink_stock
                        SET stock = stock - 1
                        WHERE drink_id = ' . $drink_id;
                // クエリの実行
                if (mysqli_query($link, $sql) === TRUE) {
                    // 購入履歴追記
                    $sql = 'INSERT INTO drink_history
                            (drink_id, create_datetime)
                            VALUES (' . $drink_id . ', now())';
                    // クエリが実行出来なかったら
                    if (mysqli_query($link, $sql) !== TRUE) {
                        // エラー
                        $err_msg[] = 'drink_history: INSERTエラー：' . $sql;
                    }
                // エラー
                } else {
                    $err_msg[] = 'UPDATE drink_stock: UPDATEエラー：' . $sql;
                }

                // トランザクション成否
                if (count($err_msg) === 0) {
                    mysqli_commit($link); // 残金計算
                    $change = $money - $price;
                } else {
                    mysqli_rollback($link);
                }
            }
        } else {
            $err_msg[] = '処理が正しくありません';
        }
    } else {
        $err_msg[] = 'connectエラー' . $link;
    }
?>
<!DOCTYPE html> <!--?>とDOCTYPEは改行しない-->
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>自動販売機 購入結果</title>
    </head>
    <body>
        <h1>購入結果</h1>
<?php foreach ($err_msg as $value) { ?>
    <?php echo $value . "<br>";  ?>
<?php } ?>
<?php if (count($err_msg) === 0) { ?>
        <img src="./drink_picture/<?php echo $img; ?>" width=100 height=150>
        <p>がしゃん！<?php echo $drink_name; ?>が買えました</p>
        <p>おつりは<?php echo $change; ?>円です！</p>
<?php } ?>
        <a href="index.php">戻る</a>
    </body>
</html>
