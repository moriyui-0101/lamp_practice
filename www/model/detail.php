<?php 
//定数ファイルの読み込みをする
require_once MODEL_PATH . 'functions.php';
//DB接続用のファイルの読み込みをする
require_once MODEL_PATH . 'db.php';
//modelはfanction中心で作成する。

function regist_detail($db, $order_id, $carts){
    //carts配列の1つ1つのcart
    foreach($carts as $cart){
        //$cartに入っているitem_idを$item_idに代入
        $item_id = $cart['item_id'];
        //$cartに入っているamountを$purchase_quantityに代入
        $purchase_quantity = $cart['amount'];
        //$cartに入っているpriceを$purchase_priceに代入(get_user_carts変数でcartsとitemsをjoinしている)
        $purchase_price = $cart['price'];
        //もし、各カラムを登録できてfalseであれば、　
        //foreachの中でinsertしなくては行けないので同じ｛｝に入れる。1つ１つの$cartをinsertする
        if(insert_detail($db, $order_id, $item_id, $purchase_quantity, $purchase_price) === false){
        //falseと返す
        return false;
        }
    }
    //1つ１つの$cartをinsertしてfalseがなく、すべてinsertできたら、trueを返す
    return true;
}

//insert_detailでINSERT文を作成,実行する
function insert_detail($db, $order_id, $item_id, $purchase_quantity, $purchase_price){
        $sql = "
           INSERT INTO
           detail_table(
               order_id,
               item_id,
               purchase_quantity,
               purchase_price
               )
               VALUES(:order_id, :item_id, :purchase_quantity, :purchase_price)
               ";
           return execute_query($db, $sql, array(':order_id' => $order_id, ':item_id' => $item_id, ':purchase_quantity' => $purchase_quantity, ':purchase_price' => $purchase_price));
}