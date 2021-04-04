<?php
//定数ファイルを読み込む
require_once '../conf/const.php';
//汎用関数ファイルを読み込む
require_once MODEL_PATH . 'functions.php';
//userデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'user.php';
//itemデータに関する関数ファイルの読み込み
require_once MODEL_PATH . 'item.php';

//ログインチェックのため、セッションを開始する
session_start();
//ログインチェック用の関数を使用、falseであれば
if(is_logined() === false){
  //ログイン画面へ遷移する
  redirect_to(LOGIN_URL);
}

$token = get_post('csrf_token');
if(is_valid_csrf_token($token) === false){
  redirect_to(LOGIN_URL);
}
unset($_SESSION['csrf_token']);

//PDO取得
$db = get_db_connect();
//PDOを利用してユーザーデータの取得
$user = get_login_user($db);
//adminチェック用関数（user.php)を使用し、adminでない場合は
if(is_admin($user) === false){
  //ログイン画面へリダイレクトする
  redirect_to(LOGIN_URL);
}
//POST送信されたitem_idを＄item_idに代入する
$item_id = get_post('item_id');

//destroy_item関数（item.php)を使用し、trueであれば、
if(destroy_item($db, $item_id) === true){
  //商品を削除しましたというメッセージを返す
  set_message('商品を削除しました。');
  //そうでなければ、
} else {
  //商品削除に失敗しましたというメッセージを返す
  set_error('商品削除に失敗しました。');
}


//操作が終わったらadminURLへリダイレクト
redirect_to(ADMIN_URL);