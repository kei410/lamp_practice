<?php

$filename = 'tokyo.csv';

$data = array();

if (is_readable($filename) === TRUE) {

  if (($fp = fopen($filename, 'r')) !== FALSE) {

      // パターン1
      while(($tmp = fgetcsv($fp, 1000, ",")) !== FALSE) {
          $data[] = $tmp;
      }
/*
      // パターン2
      $i = 0;
      while (($tmp = fgetcsv($fp)) !== FALSE) {
          $data[$i]['zipcode'] = htmlspecialchars($tmp[2], ENT_QUOTES, 'UTF-8');
          $data[$i]['pref']    = htmlspecialchars($tmp[6], ENT_QUOTES, 'UTF-8');
          $data[$i]['city']    = htmlspecialchars($tmp[7], ENT_QUOTES, 'UTF-8');
          $data[$i]['town']    = htmlspecialchars($tmp[8], ENT_QUOTES, 'UTF-8');
          $i++;
      }
*/
      fclose($fp);
    }

} else {
  $data[] = 'ファイルがありません';
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>課題</title>
  <style>
    table {
      border-collapse: collapse;
    }
    table, tr, th, td {
      border: solid 1px;
    }
    caption {
      text-align: left;
    }
  </style>
</head>
<body>
  <p>以下にファイルから読み込んだ住所データを表示</p>
  <table>
    <caption>住所データ</caption>
    <tr>
      <th>郵便番号</th>
      <th>都道府県</th>
      <th>市区町村</th>
      <th>町域</th>
    </tr>
<?php foreach ($data as $value) { ?>
      <tr>
        <td><?php print htmlspecialchars($value[2], ENT_QUOTES, 'UTF-8'); ?></td>
        <td><?php print htmlspecialchars($value[6], ENT_QUOTES, 'UTF-8'); ?></td>
        <td><?php print htmlspecialchars($value[7], ENT_QUOTES, 'UTF-8'); ?></td>
        <td><?php print htmlspecialchars($value[8], ENT_QUOTES, 'UTF-8'); ?></td>
      </tr>
<?php } ?>
<?php /* foreach ($data as $value) { ?>
      <tr>
        <td><?php print $value['zipcode']; ?></td>
        <td><?php print $value['pref']; ?></td>
        <td><?php print $value['city']; ?></td>
        <td><?php print $value['town']; ?></td>
      </tr>
<?php } */ ?>
  </table>
</body>
</html>
