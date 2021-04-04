<?php
// 定数ファイルを読み込み
require_once '../conf/const.php';
// 汎用関数ファイルを読み込み
require_once MODEL_PATH . 'functions.php';
//ユーザーデーターに関するファイルの読み込み
require_once MODEL_PATH . 'user.php';
//itemデーターに関するファイルの読み込み
require_once MODEL_PATH . 'item.php';
//カートデータに関するファイルの読み込み
require_once MODEL_PATH . 'cart.php';
//ログインのためにセッションを開始する
session_start();
//is_loginedというログイン確認用の関数がfalseであれば、
if(is_logined() === false){
  //ログイン画面へリダイレクトする
  redirect_to(LOGIN_URL);
}

$token = get_post('csrf_token');

if(is_valid_csrf_token($token) === false){
  redirect_to(LOGIN_URL);
}
unset($_SESSION['csrf_token']);

//PDOを取得する
$db = get_db_connect();
//PDOを利用してログインユーザーのデータを取得
$user = get_login_user($db);
//POST受信のcart_idを＄cart_idに代入する
$cart_id = get_post('cart_id');
//POST受信のamountを＄amountに代入する
$amount = get_post('amount');


//updata_cart_amount関数を使用し、POST受信されたcart_id,amountが更新されたら
if(update_cart_amount($db, $cart_id, $amount)){
  //購入数を購入しましたというメッセージを返す
  set_message('購入数を更新しました。');
  //それ以外は
} else {
  //購入更新に失敗しましたというメッセージを返す
  set_error('購入数の更新に失敗しました。');
}
//cart_urlをリダイレクトする
redirect_to(CART_URL);