<?php
$index_data = [];
$err_msg    = [];

$host     = 'localhost'; //ホスト名
$username = 'codecamp42398'; //ユーザー名
$password = 'codecamp42398'; //パスワード
$dbname   = 'codecamp42398'; //データベース名
$link     = mysqli_connect($host, $username, $password, $dbname);

//初期化するときは=をそろえると見やすい
    if ($link) {
        mysqli_set_charset($link, 'utf8');
        // 自動販売機を表示
        // ステータスが1の時に表示する

        $sql = "SELECT drink_master.drink_id, img, drink_name, price, status, stock 
                FROM drink_master
                JOIN drink_stock
                ON drink_master.drink_id = drink_stock.drink_id
                WHERE status = 1";

        if ($result = mysqli_query($link, $sql)) {
            while ($row = mysqli_fetch_array($result)) {
// print_r($row);
// exit;
                foreach ($row as $key => $value) {
                    $row[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                }
                $index_data[] = $row;
            } //foreach文を使ってシンプルに表現する
        } else {
            $err_msg[] = '取得失敗' . $sql;
        }
        mysqli_free_result($result);
        mysqli_close($link);

    } else {
        $err_msg[] = 'error: ' . mysqli_connect_error();
    }
//print_r($index_data);
//exit;
// var_dump($err_msg);
?>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>自動販売機 購入</title>
        <style type="text/css">
            .soldout {
                color: red;
                font-weight: bold;
            }
        </style>
    </head>
    <body>
        <h1>自動販売機</h1>
        <form method="post" action="result.php">
            <p>金額<input type="text" name="money"></p>
            <table>
<?php foreach ($index_data as $value) { ?>    <!--HTML内にPHPを入れるときは、PHPを左端によせる-->
                <tr>
                    <td><img src="./drink_picture/<?php echo $value['img']; ?>" width=100 height=150></td>
                </tr>
                <tr>
                    <td><?php echo $value['drink_name']; ?></td>
                </tr>
                <tr>
                    <td><?php echo $value['price']; ?>
<?php if ($value['stock'] <= 0) { ?>
                        <p class="soldout">売り切れ</p>
<?php } else { ?>
                        <br><input type="radio" name="drink_id" value="<?php echo $value['drink_id']; ?>">
                    </td>
<?php } ?>
                </tr>
<?php } ?>
            </table>
            <input type="submit" value="■□■□■購入■□■□■">
        </form>
    </body>
</html>


