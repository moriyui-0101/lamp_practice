<?php 
//定数ファイルの読み込みをする
require_once MODEL_PATH . 'functions.php';
//DB接続用のファイルの読み込みをする
require_once MODEL_PATH . 'db.php';

//insert_history関数でSQL文を作成し、実行する
  function regist_history($db, $total, $user_id){
    $sql = "
      INSERT INTO
        history_table(
            total,
            user_id
        )
      VALUES(:total, :user_id);
      ";
      return execute_query($db, $sql, array(':total' => $total, ':user_id' => $user_id));
  }

//get_user_historysでユーザーの購入履歴を取得する（全商品なのでfetch_all_queryにする）
function get_user_historys($db, $user_id){
  $sql = "
    SELECT
      order_id,
      purchase_datatime,
      total
    FROM
      history_table
    WHERE
      user_id = :user_id
    ORDER BY
      order_id DESC
    ";
  return fetch_all_query($db, $sql, array(':user_id' => $user_id));
}

//データベースから全ユーザーの商品ID、購入日時、小計を取得する
function get_admin_historys($db){
  $sql = "
    SELECT
      order_id,
      purchase_datatime,
      total
    FROM
      history_table
    ORDER BY
      order_id DESC
    ";
  return fetch_all_query($db, $sql);
}

