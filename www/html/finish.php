<?php
//定数ファイルの読み込み
require_once '../conf/const.php';
//汎用関数のファイルの読み込み
require_once MODEL_PATH . 'functions.php';
//ユーザーデータに関するファイルの読み込み
require_once MODEL_PATH . 'user.php';
//商品データに関するファイルの読み込み
require_once MODEL_PATH . 'item.php';
//カート内にあるデータに関するファイルの読み込み
require_once MODEL_PATH . 'cart.php';

require_once MODEL_PATH . 'detail.php';

require_once MODEL_PATH . 'history.php';


//ログイン確認のために、セッションを開始する
session_start();
//もし、is_logined関数でfelse　つまりログイン出来なかった時は、
if(is_logined() === false){
  //ログイン画面へリダイレクトする。
  redirect_to(LOGIN_URL);
}

$token = get_post('csrf_token');
if(is_valid_csrf_token($token) === false){
  redirect_to(LOGIN_URL);
}
unset($_SESSION['csrf_token']);

//PDOの取得する
$db = get_db_connect();
//PDOを活用して、ユーザーデーターを取得する
$user = get_login_user($db);
//PDOを利用し、get_user_carts関数でPOST受信されたuser_idの商品のデーターを$carts_idに代入する
$carts = get_user_carts($db, $user['user_id']);

//PDO活用し、purchase_carts関数で$cartが違う
if(purchase_carts($db, $carts) === false){
  //商品が購入できませんでしたというエラーを送る
  set_error('商品が購入できませんでした。');
  //CART_URLへリダイレクトする
  redirect_to(CART_URL);
} 
//$total_priceにuser_idが購入した全商品の合計金額をsum_cart関数で計算し、代入する
$total_price = sum_carts($carts);

//トランザクション開始
$db->beginTransaction();
//regist_historyでsqlに登録する。
if(regist_history($db, $total_price, $user['user_id']) === false){
  //ロールバックをするときは、regist_historyがfalseの時
  $db->rollback();
  //商品登録に失敗しましたというメッセージを返す
  set_error('購入履歴の登録に失敗しました。');
  //CART_URLへリダイレクトする
  redirect_to(CART_URL);
}

//regist_historyでオートインクリメントで作られたorder_idをlastInsertIdでわたす
$order_id = $db->lastInsertId();

//cartsにitem_id、purchase_quantity、purchase_priceに入っている
if(regist_detail($db, $order_id, $carts)){
  //コミットする
  $db->commit();

  //購入明細履歴を登録しましたというメッセージを返す
  set_message('購入明細履歴を登録しました');
}else{
  //ロールバック
  $db->rollback();
  set_error('購入詳細履歴の登録に失敗しました');
  //CART_URLへリダイレクトする
  redirect_to(CART_URL);
}

//
$token = get_csrf_token();

//viewの中のfinish_view.phpを読み込む
include_once '../view/finish_view.php';