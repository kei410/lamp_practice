<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>phpmyadmiinのデータを表で出力する</title>
  </head>
  <body>
    <table border="2">
      <tr>
        <th style="color: #f00;">商品番号</th>
        <th style="color: #f00;">商品名</th>
        <th style="color: #f00;">価格</th>
      </tr>  
    <?php
    $host     = 'localhost';
    $username = 'codecamp42398';   // MySQLのユーザ名
    $password = 'codecamp42398';       // MySQLのパスワード
    $dbname   = 'codecamp42398';   // MySQLのDB名(今回、MySQLのユーザ名を入力してください)
    $charset  = 'utf8';   
    
    $dsn = 'mysql:dbname='.$dbname.';host='.$host.';charset='.$charset;
    
    $dbh = new PDO($dsn, $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'));
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    
    $row = [];
    foreach ($dbh->query('select * from product_practice') as $row) {
        echo '<tr>';
        echo '<td>', $row['id'], '</td>';
        echo '<td>', $row['name'], '</td>';
        echo '<td>', $row['price'], '</td>';
        echo'</tr>';
    }
    ?>
    </table>
</body>

</html>

<!--
あらかじめphpmyadminに、product_practiceという名前のデータを
作成しておくこと
-->