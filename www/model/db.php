<?php
//get_db_connectでPDOを取得する
function get_db_connect(){
  // MySQL用のDSN文字列
  $dsn = 'mysql:dbname='. DB_NAME .';host='. DB_HOST .';charset='.DB_CHARSET;
 
  //tryで処理を開始する
  try {
    // データベースに接続
    $dbh = new PDO($dsn, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'));
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    //例外処理をする
  } catch (PDOException $e) {
    //エラー表示の仕方を表記し、プログラムを終了する
    exit('接続できませんでした。理由：'.$e->getMessage() );
  }
  //$dbhで返す
  return $dbh;
}

//fetch_query関数でPDOの取得、$sql、＄paramsに空の配列を代入したものを引数として渡す
function fetch_query($db, $sql, $params = array()){
  //tryで処理をする
  try{
    //PDOを利用して、SQL文を準備し、$statementに代入をする
    $statement = $db->prepare($sql);
    //実行する
    $statement->execute($params);
    
    return $statement->fetch();
    //例外処理をする
  }catch(PDOException $e){
    //データ取得に失敗しました。というエラーの文をset_errorで用意
    set_error('データ取得に失敗しました。');
  }
  //falseで返す
  return false;
}

//fetch_all_queryでPDOを取得し、$sql、＄paramsに空の配列を代入したものを引数として渡す
function fetch_all_query($db, $sql, $params = array()){
  //tryで処理をする
  try{
    //PDOを利用して、SQL文を準備し、$statementに代入をする
    $statement = $db->prepare($sql);
    //$statementで＄paramsを実行する
    $statement->execute($params);
    //$statementをSQL文全て（fetchAll)で返す
    return $statement->fetchAll();
    //例外処理をする
  }catch(PDOException $e){
    //データ取得に失敗しました。というエラーの文をset_errorで用意
    set_error('データ取得に失敗しました。');
  }
  //falseで返す
  return false;
}

//execute_queryでPDOを取得し、$sql、＄paramsに空の配列を代入したものを引数として渡す
function execute_query($db, $sql, $params = array()){
  //tryで処理をする
  try{
    //$statementでPDOを利用し、$sql文を準備し代入をする
    $statement = $db->prepare($sql);
    //paramsを実行する
    return $statement->execute($params);
  //例外処理をする
  }catch(PDOException $e){
    //更新に失敗しましたというエラーの文をset_errorで用意
    set_error('更新に失敗しました。');
  }
  //falseで返す
  return false;
}