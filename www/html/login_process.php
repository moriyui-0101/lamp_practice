<?php
//定数ファイルの読み込み
require_once '../conf/const.php';
//汎用関数ファイルの読み込み
require_once MODEL_PATH . 'functions.php';
//ユーザーデータに関するファイルの読み込み
require_once MODEL_PATH . 'user.php';

//ログインチェックのため、セッションを開始する
session_start();

//ログイン用関数でログインできた場合
if(is_logined() === true){
  //HOME_URLへリダイレクトする
  redirect_to(HOME_URL);
}

//＄nameにPOST受信で得たnameを代入するう
$name = get_post('name');
//＄passwordにPOST受信で得たpasswordを代入する
$password = get_post('password');

//PDOを取得する
$db = get_db_connect();

//$userにPDOで取得した$name,$passwordを代入する
$user = login_as($db, $name, $password);
//もし、＄userに代入されたname,passwordが違えば
if( $user === false){
  //ログインに失敗しましたというエラーを表示を送り
  set_error('ログインに失敗しました。');
  //LOGIN_URLにリダイレクトする
  redirect_to(LOGIN_URL);
}
//ログインしましたというメッセージをおくる
set_message('ログインしました。');
//もし、入力がadminの名前、passwordであれば
if ($user['type'] === USER_TYPE_ADMIN){
  //ADMIN_URLへリダイレクトする
  redirect_to(ADMIN_URL);
}
//admin以外は、HOME_URLへリダイレクトする
redirect_to(HOME_URL);