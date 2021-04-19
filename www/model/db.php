<?php
// MySQLに接続して（その接続した）PDOクラスのインスタンスを返す
function get_db_connect(){
  // MySQL用のDSN文字列
  $dsn = 'mysql:dbname='. DB_NAME .';host='. DB_HOST .';charset='.DB_CHARSET;
  // try~catchを使うことでエラー時の処理をcatchの中にまとめられる
  try {
    // データベースに接続する
    // PHPでデータベースにアクセスする際にPDOを利用する
    // new PDOのところは()内の条件でPDOを利用できる状態にする命令と考える
    $dbh = new PDO($dsn, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'));
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    exit('接続できませんでした。理由：'.$e->getMessage() );
  }
  return $dbh;
}

// $db、$sql、$paramを受け取り、プリペアドステートメントを利用してデータを配列として1行のみ取得する
// データ取得に失敗した場合はFALSEを返す
// プリペアドステートメントとはSQL文で値がいつでも変更できるように変更する箇所だけ変数のようにした命令文を作る仕組み
function fetch_query($db, $sql, $params = array()){
  try{
    $statement = $db->prepare($sql);
    $statement->execute($params);
    return $statement->fetch();
  }catch(PDOException $e){
    set_error('データ取得に失敗しました。');
  }
  return false;
}

// $db、$sql、$paramを受け取り、プリペアドステートメントを利用して
// 結果が複数存在する際にそれらの結果を全て取得する
// データ取得に失敗した場合はFALSEを返す
function fetch_all_query($db, $sql, $params = array()){
  try{
    $statement = $db->prepare($sql);
    $statement->execute($params);
    return $statement->fetchAll();
  }catch(PDOException $e){
    set_error('データ取得に失敗しました。');
  }
  return false;
}

// execute_query() はPDO($db)とSQL文($sql)、SQL文の中に代入する値($params)を受け取り、SQL文を実行して成功したら実行結果のインスタンスを返す

// prepare関数を使ってSQL文を実行するようになっているので、SQL文内のメタ文字を実際の値に変換して実行するために$paramsで値を指定している
// クエリはデータベースへの問い合わせという意味
// もしSQL文の中に変動値が入る場合はプレースホルダを使う

// query は1回毎にSQL文を書いて実行するのに使う  queryは戻り値がレコードセットなので、select文等でよく使われる
// prepareとexecuteは同じSQL文で検索条件の値や挿入する値だけを変えながら繰り返し実行する場合に使う
// 第三引数が変数名だけでなく代入式になっているが、これは引数がない場合の初期値の設定をしている。
function execute_query($db, $sql, $params = array()){
  try{
    $statement = $db->prepare($sql);
    return $statement->execute($params);
  }catch(PDOException $e){
// 失敗したらset_error()関数を実行してFALSEを返す
    set_error('更新に失敗しました。');
  }
  return false;
}