<?php
//定数ファイルの読み込み
require_once '../conf/const.php';
//汎用関数ファイルの読み込み
require_once MODEL_PATH . 'functions.php';
//ユーザーデータファイルの読み込み
require_once MODEL_PATH . 'user.php';
//商品データファイルの読み込み
require_once MODEL_PATH . 'item.php';
//カート内データファイルの読み込み
require_once MODEL_PATH . 'cart.php';

//ログイン確認のためのセッションを開始する
session_start();
//もし　is_logined関数を使用し、ログインできれなければ、
if(is_logined() === false){
  //LOGIN_URLへリダイレクトする
  redirect_to(LOGIN_URL);
}

$token = get_post('csrf_token');

if(is_valid_csrf_token($token) === false){
  redirect_to(LOGIN_URL);
}
unset($_SESSION['csrf_token']);

//PDOを取得する
$db = get_db_connect();
//PDOを活用しユーザーデータを取得する
$user = get_login_user($db);

//$item_idにget_POSTというPOST受信用の関数で得たitem_idを代入する
$item_id = get_post('item_id');
//もし、PDOを活用し、POST受信で得user_idの商品IDをadd_cartという商品を追加するための関数で追加できたら
if(add_cart($db,$user['user_id'], $item_id)){
  //カートに商品を追加しましたという
  set_message('カートに商品を追加しました。');
  //それ以外は
} else {
  //カート更新に失敗しました
  set_error('カートの更新に失敗しました。');
}
//HOME_URLにリダイレクトする。
redirect_to(HOME_URL);