<?php
//定数ファイルを読み込む
require_once '../conf/const.php';
// 汎用関数ファイルを読み込み
require_once MODEL_PATH . 'functions.php';
// userデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'user.php';
// itemデータに関する関数ファイルを読み込み。
require_once MODEL_PATH . 'item.php';

// ログインチェックを行うため、セッションを開始する
session_start();

// ログインチェック用関数を利用
if(is_logined() === false){
  // ログインしていない場合はログインページにリダイレクト
  redirect_to(LOGIN_URL);
}
//PDO取得
$db = get_db_connect();
// PDOを利用してログインユーザーのデータを取得
$user = get_login_user($db);

//adminチェック用関数（user.php)を使用し、adminでない場合は
if(is_admin($user) === false){
  //ログインページへリダイレクトする
  redirect_to(LOGIN_URL);
}
$token = get_post('csrf_token');
if(is_valid_csrf_token($token) === false){
  redirect_to(LOGIN_URL);
}
unset($_SESSION['csrf_token']);

//POSTで送られたitem_idを＄item_idへ代入する
$item_id = get_post('item_id');
//POSTで送られた、stockを＄stockへ代入する
$stock = get_post('stock');

//update_item_stockの関数を使用し、POST送信で得た、$item_idと$stockを変更する
if(update_item_stock($db, $item_id, $stock)){
  //在庫を変更しましたというメッセージを返す
  set_message('在庫数を変更しました。');
  //それ以外は
} else {
  //在庫の変更を失敗しましたとメッセージを変えす
  set_error('在庫数の変更に失敗しました。');
}
//操作が完了したらadminURLへ移行する
redirect_to(ADMIN_URL);