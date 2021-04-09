<?php 
//定数ファイルの読み込みをする
require_once MODEL_PATH . 'functions.php';
//DB接続用のファイルの読み込みをする
require_once MODEL_PATH . 'db.php';

//get_user_cartsという関数に、
function get_user_carts($db, $user_id){
  //items_tableから商品ID・名前、価格、在庫数、ステータス、イメージ画像をcarts_tableからカートID、ユーザーID、購入数をSQLで選ぶ
  $sql = "
    SELECT
      items.item_id,
      items.name,
      items.price,
      items.stock,
      items.status,
      items.image,
      carts.cart_id,
      carts.user_id,
      carts.amount
    FROM
      carts
    JOIN
      items
    ON
      carts.item_id = items.item_id
    WHERE
      carts.user_id = :user_id
  ";
  //fetch_all_queryでPDOを取得し、上記のSQL文を返す
  return fetch_all_query($db, $sql, array(':user_id' => $user_id));
}

//get_user_cartでPDOを利用し、user_id,item_idを引数に渡す
function get_user_cart($db, $user_id, $item_id){
  //carts_tableとitems_tableをitem_idでJOINしてtableを結合したものから、POST受信したユーザーIDと商品IDの
  //items_tableから商品ID該当、名前、価格、在庫数、イメージ図、cartsからカートID、ユーザーID、数量をSELECT文で選び、＄sqlに代入する
  $sql = "
    SELECT
      items.item_id,
      items.name,
      items.price,
      items.stock,
      items.status,
      items.image,
      carts.cart_id,
      carts.user_id,
      carts.amount
    FROM
      carts
    JOIN
      items
    ON
      carts.item_id = items.item_id
    WHERE
      carts.user_id = :user_id
    AND
      items.item_id = :item_id
  ";
  //fetch_queryでPDOを取得し、上記のSQL文を取得する
  return fetch_query($db, $sql, array(':user_id' => $user_id, 'item_id' => $item_id));
  //fetch_queryでPDOを取得し、上記のSQL文を取得する

}

//add_cartという関数でPDOを利用し、user_id,item_idを引数にわたす
function add_cart($db, $user_id, $item_id ) {
  //get_user_cartからPDOを取得し、POST受信したuser_id,item_idを$cartに代入する
  $cart = get_user_cart($db, $user_id, $item_id);
  //もし、$cartが違えば
  if($cart === false){
    //
    return insert_cart($db, $user_id, $item_id);
  }
  //update_cart_amountでPDO活用し、$cartにあるcart_idの数量を＋１して購入数量を変更する
  return update_cart_amount($db, $cart['cart_id'], $cart['amount'] + 1);
}

//insert_cart関数でPDOを利用してuser_id,item_id、＄amountを１引数として
//item_id,user_id,amountの値を
function insert_cart($db, $user_id, $item_id, $amount = 1){
  $sql = "
    INSERT INTO
      carts(
        item_id,
        user_id,
        amount
      )
    VALUES(:item_id, :user_id, :amount)
  ";
//execute_queryでSQLを実行
return execute_query($db, $sql, array(':item_id' => $item_id, ':user_id' => $user_id, ':amount' => $amount));
}

//update_cart_amount関数でPDO
function update_cart_amount($db, $cart_id, $amount){
  //POST受信したcart_idの数量を変更する（SQLの上限は１行）
  $sql = "
    UPDATE
      carts
    SET
      amount = :amount
    WHERE
      cart_id = :cart_id
    LIMIT 1
  ";
//execute_queryでSQLを返す
  return execute_query($db, $sql, array(':amount' => $amount, ':cart_id' => $cart_id));
}

//delete_cart関数を利用して
function delete_cart($db, $cart_id){
  //cart_idの情報を消去する。（SQLの上限は１行）
  $sql = "
    DELETE FROM
      carts
    WHERE
      cart_id = {$cart_id}
    LIMIT 1
  ";
//execute_queryでPDOを利用しsql文を返す
  return execute_query($db, $sql);
}

//purchase_carts関数で
function purchase_carts($db, $carts){
//validate_cart_purchase関数をカートに商品が入っているか確認して入っていなければ
  if(validate_cart_purchase($carts) === false){
    //falseを返す
    return false;
  }
  //＄cartsなかにある１つ１つの$cartが
  foreach($carts as $cart){
    //もし、update_item_stockを利用し、在庫数が変更できなければエラー表示をする
    if(update_item_stock(
        $db, 
        $cart['item_id'], 
        $cart['stock'] - $cart['amount']
      ) === false){
      set_error($cart['name'] . 'の購入に失敗しました。');
    }
  }
  //そうでなければ、delete_user_cartsを利用し、カート情報を消去する
  delete_user_carts($db, $carts[0]['user_id']);
}

//delete_user_carts関数で
function delete_user_carts($db, $user_id){
  //$user_idのカート内の商品を消去する
  $sql = "
    DELETE FROM
      carts
    WHERE
      user_id = :user_id
  ";
//execute_queryで上記のSQL文を実行するようにする
  execute_query($db, $sql, array(':user_id' => $user_id));
}

//sum_carts関数で合計数を計算する
function sum_carts($carts){
  //$total_priceを０とする
  $total_price = 0;
  //カート内の１つ１つの商品を
  foreach($carts as $cart){
    //価格、数量を$total_priceに１つ１つ代入する
    $total_price += $cart['price'] * $cart['amount'];
  }
  //$total_priceで処理を返す
  return $total_price;
}

//validate_cart_purchase関数で
function validate_cart_purchase($carts){
  //もし、カート内に商品が入っていなければ、
  if(count($carts) === 0){
    //エラー表示をセットして
    set_error('カートに商品が入っていません。');
    //falseで返す
    return false;
  }
  //カート内の１つ１つの商品を
  foreach($carts as $cart){
    //is_open関数を利用して在庫がなければ
    if(is_open($cart) === false){
      //エラー表示をする
      set_error($cart['name'] . 'は現在購入できません。');
    }
    //条件式で＄cartにある在庫数が購入数量を上回る時
    if($cart['stock'] - $cart['amount'] < 0){
      //set_errorでエラー表記する
      set_error($cart['name'] . 'は在庫が足りません。購入可能数:' . $cart['stock']);
    }
  }
  //has_errorでエラーがあれば
  if(has_error() === true){
    //falseで返す
    return false;
  }
  //そうでなければ、trueで返す
  return true;
}

