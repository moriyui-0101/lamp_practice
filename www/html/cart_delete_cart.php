<?php
//定数ファイルを読み込み
require_once '../conf/const.php';
//汎用関数ファイルの読み込み
require_once MODEL_PATH . 'functions.php';
//userデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'user.php';
//itemデータに関する関数ファイルの読み込み
require_once MODEL_PATH . 'item.php';
//カートデータに関する関数ファイルの読み込み
require_once MODEL_PATH . 'cart.php';

//ログインチェックのためのセッションを開始する
session_start();

//is_loginedというログイン確認用の関数でfalseならば
if(is_logined() === false){
  //ログインページへリダイレクトする
  redirect_to(LOGIN_URL);
}

//PDOを取得
$db = get_db_connect();
//PDOを利用してログインユーザーのデータを取得
$user = get_login_user($db);
//$cart_idにPOST受信したcart_idを代入する
$cart_id = get_post('cart_id');

//もし、PDOを利用し、delete_cartという関数で$cart_idのカート中身を削除するしたら
if(delete_cart($db, $cart_id)){
  //カートを削除しましたというメッセージを飛ばす
  set_message('カートを削除しました。');
  //それ以外は、
} else {
  //カートの削除に失敗しましたいうメッセージを送る
  set_error('カートの削除に失敗しました。');
}
//CART_URLにリダイレクトする。
redirect_to(CART_URL);