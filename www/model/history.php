<?php 
//定数ファイルの読み込みをする
require_once MODEL_PATH . 'functions.php';
//DB接続用のファイルの読み込みをする
require_once MODEL_PATH . 'db.php';

//
function regist_history($db, $purchase_datatime, $total, $user_id){
    if(validate_history($purchase_datatime, $total, $user_id) === false){
        return false;
    }
    return regist_history_transaction($db, $purchase_datatime, $total, $user_id);
}

//購入履歴テーブルをトランザクションする
  function regist_history_transaction($db, $purchase_datatime, $total, $user_id){
    $db->beginTransaction();
    if(insert_history($db, $purchase_datatime, $total, $user_id)){
        $db->commit();
        return true;
    }
    $db->rollback();
    return false;
  }

//insert_history関数でSQL文を作成する
function insert_history($db, $purchase_datatime, $total, $user_id){
    $sql = "
      INSERT INTO
        history(
            purchase_datatime,
            total,
            user_id
        )
      VALUES(:total, :user_id);
      ";
      return execute_query($db, $sql, array(':total' => $total, ':user_id' => $user_id));
}

//validate_history関数で
function validate_history($total, $user_id){
    $is_valid_history_total = is_valid_history_total($total);
    $is_valid_history_user_id = is_valid_history_user_id($user_id);

    return $is_valid_history_total
       && $is_valid_history_user_id;
}

//is_valid_history_totalでPOST受信で受け取ったtotalのエラーチェックをする
function is_valid_history_total($total){
    $is_valid = true;
    if(is_positive_integer($total) === false){
      set_error('注文合計数は0以上の整数で入力してください。');
      $is_valid = false;
    }
    return $is_valid;
  }

//is_valid_history_user_idでPOST受信で受け取ったuser_idのエラーチェックをする
function is_valid_history_user_id($user_id){
    $is_valid = true;
    if(is_positive_integer($user_id) === false){
      set_error('ユーザーIDは0以上の整数で入力してください。');
      $is_valid = false;
    }
    return $is_valid;
  }
