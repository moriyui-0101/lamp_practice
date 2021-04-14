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

//PDOの取得oreする
$db = get_db_connect();
//PDOを活用して、ユーザーデーターを取得する
$user = get_login_user($db);

//上部の商品ID、購入日時、小計をhiddenで埋め込み送ったものを＄変数に代入する
$order_id = get_post('order_id');
$purchase_datatime = get_post('purchase_datatime');
$total = get_post('total');

//get_user_detailで指定されたユーザーID、POST受信された商品IDを取得する
$details = get_user_details($db, $order_id);

$token = get_csrf_token();

include_once '../view/detail_view.php';