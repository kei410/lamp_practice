<?php

// 設定ファイル読み込み
require_once '../conf/vending_machine_const.php';
// 関数ファイル読み込み
require_once '../model/EC_function.php';

$drink_id = '';
$money    = '';
$data     = array();
$err_msg  = array();

if (get_request_method() === 'POST') {

  $money = get_post_data('money');
  $money = trim_space($money);

  $drink_id = get_post_data('drink_id');

  if ($money === '') {
    $err_msg[] = 'お金を投入してください。';
  } else if (check_number($money) !== TRUE ) {
    $err_msg[] = 'お金は半角数字を入力してください';
  } else if ($money > 10000) {
    $err_msg[] = '投入金額は1万円以下にしてください';
  }

  if ($drink_id === '') {
    $err_msg[] = '商品を選択してください';
  } else if (check_number($drink_id) !== TRUE ) {
    $err_msg[] = 'エラー: 不正な入力値です';
  }

  // 在庫の更新
  if (count($err_msg) === 0) {

    try {

      $link = get_db_connect();

      $data = get_drink_data($link, $drink_id);

      // 投入した金額が販売価格を上回っているかチェック
      $change = $money - $data[0]['price'];
      if (count($err_msg) === 0 && $change < 0) {
          $err_msg[] = 'お金がたりません！';
      }

      // 在庫があるかをチェック
      if (count($err_msg) === 0 && $data[0]['stock'] <= 0) {
        $err_msg[] = '売り切れです！';
      }

      // 在庫があるかをチェック
      if (count($err_msg) === 0 && $data[0]['status'] === 0) {
        $err_msg[] = '販売が終了しました！';
      }

      // 在庫の更新
      if (count($err_msg) === 0 ) {
        // 現在日時を取得
        $now_date = date('Y-m-d H:i:s');
        purchase_drink($link, $drink_id, $data[0]['stock'], $now_date);
        $data = entity_assoc_array($data);
      }

    } catch (PDOException $e) {
      $err_msg[] = 'DBエラーです。理由：'.$e->getMessage();
    }
  }

} else {
  $err_msg[] = '不正なアクセスです';
}

// テンプレートファイル読み込み
include_once '../view/EC_result_view.php';