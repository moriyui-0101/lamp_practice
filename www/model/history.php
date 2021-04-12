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


