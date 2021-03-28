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

//viewの中のfinish_view.phpを読み込む
include_once '../view/finish_view.php';