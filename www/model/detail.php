<?php 
//定数ファイルの読み込みをする
require_once MODEL_PATH . 'functions.php';
//DB接続用のファイルの読み込みをする
require_once MODEL_PATH . 'db.php';

function regist_detail($db, $order_id, $item_id, $purchase_quantity, $purchase_price){
    if(validate_detail($order_id, $item_id, $purchase_quantity, $purchase_price) === false){
        return false;
    }
    return regist_detail_transaction($db, $order_id, $item_id, $purchase_quantity, $purchase_price);
}

//購入詳細テーブルをトランザクションする
function regist_detail_transaction($db, $order_id, $item_id, $purchase_quantity, $purchase_price){
    $db ->beginTransaction();
    if(insert_detail($db, $order_id, $item_id, $purchase_quantity, $purchase_price)){
        $db->commit();
        return true;
    }
    $db->rollback();
    return false;
}

//insert_detail関数でSQL文を作成する
function insert_detail($db, $order_id, $item_id, $purchase_quantity, $purchase_price){
    $sql = "
       INSERT INTO
       detail(
           order_id,
           item_id,
           purchase_quantity,
           purchase_price
           )
           VALUES(:order_id, :item_id, :purchase_quantity, :purchase_price)
           ";
       return execute_query($db, $sql, array(':order_id' => $order_id, ':item_id' => $item_id, ':purchase_quantity' => $purchase_quantity));   
}

//validate_detail関数で
function validate_detail($order_id, $item_id, $purchase_quantity, $purchase_price){
    $is_valid_detail_order_id = is_valid_detail_order_id($order_id);
    $is_valid_detail_item_id = is_valid_detail_item_id($item_id);
    $is_valid_detail_purchase_quantity = is_valid_detail_purchase_quantity($purchase_quantity);
    $is_valid_detail_purchase_price = is_valid_detail_purchase_price($purchase_price);

    return $is_valid_detail_order_id
      && $is_valid_detail_item_id
      && $is_valid_detail_purchase_quantity
      && $is_valid_detail_purchase_price;
}

//is_valid_detail_order_idでPOST受信で受け取ったtotalのエラーチェックをする
function is_valid_detail_order_id($order_id){
    $is_valid = true;
    if(is_positive_integer($order_id) === false){
      set_error('注文番号はは0以上にして下さい。');
      $is_valid = false;
    }
    return $is_valid;
  }

//is_valid_detail_order_idでPOST受信で受け取ったitem_idのエラーチェックをする
function is_valid_detail_item_id($item_id){
    $is_valid = true;
    if(is_positive_integer($item_id) === false){
        set_error('商品番号は0以上にして下さい。');
        $is_valid = false;
    }
    return $is_valid;
}

//is_valid_detail_purchse_quantityでPOST受信で受け取ったpurchase_quantityのエラーチェックをする
function is_valid_detail_purchase_quantity($purchase_quantity){
    $is_valid = true;
    if(is_positive_integer($purchase_quantity) === false){
        set_error('購入数量は0以上にして下さい。');
        $is_valid = false;
    }
    return $is_valid;
}

//is_valid_detail_purchase_priceでPOST受信で受け取ったpurchase_priceのエラーチェックをする
function is_valid_detail_purchase_price($purchase_price){
    if(is_positive_integer($purchase_price) === false){
        set_error('購入時の金額は0以上にして下さい。');
        $is_valid = false;
    }
    return $is_valid;
}