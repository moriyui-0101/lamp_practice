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

//POST受信した$nameをissetで安全確認する
$total = get_post('total');
$user_id = get_post('user_id');
$order_id = get_post('order_id');
$item_id = get_post('item_id');
$purchase_quantity = get_post('purchase_quantity');
$purchase_price = get_post('purchase_price');

//regist_historyでmodelの中にあるトランザクションを開始の指示を出す
if(regist_history($db, $purchase_datatime, $total, $user_id)){
  //商品を登録しましたというメッセージを返す
  set_message('購入履歴を登録しました。');
}else{
  //商品登録に失敗しましたというメッセージを返す
  set_error('購入履歴の登録に失敗しました。');
}

//detail_tableでmodelの中にあるトランザクションを開始の指示を出す
if(regist_detail($db, $order_id, $item_id, $purchase_quantity, $purchase_price)){
  //購入明細履歴を登録しましたというメッセージを返す
  set_message('購入明細履歴を登録しました');
}else{
  set_error('購入詳細履歴の登録に失敗しました');
}

//
$token = get_csrf_token();

//viewの中のfinish_view.phpを読み込む
include_once '../view/finish_view.php';