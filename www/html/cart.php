<?php
//定数ファイルの読み込み
require_once '../conf/const.php';
//汎用関数のファイルの読み込み
require_once MODEL_PATH . 'functions.php';
//ユーザーデーターに関するファイルの読み込み
require_once MODEL_PATH . 'user.php';
//商品に関するファイルの読み込み
require_once MODEL_PATH . 'item.php';
//カート内に関するファイルの読み込み
require_once MODEL_PATH . 'cart.php';

//ログイン確認のために、セッションを開始する
session_start();

//もし、is_loginedというログイン確認用の関数がfalseであれば
if(is_logined() === false){
  //LOGIN_URLにリダイレクトする
  redirect_to(LOGIN_URL);
}
//PDOの取得
$db = get_db_connect();
//PDOを活用して、ユーザーデータを取得
$user = get_login_user($db);
//$user['user_id']をget_user_carts関数を利用して、＄cartsに代入する
$carts = get_user_carts($db, $user['user_id']);
//合計金額をsum_cartsで計算し、$total_priceへ代入する
$total_price = sum_carts($carts);

//
$token = get_csrf_token();
//'cart_view.php'を読み込む
include_once VIEW_PATH . 'cart_view.php';