<?php
//定数ファイルの読み込み
require_once '../conf/const.php';
//汎用定数ファイルの読み込み
require_once MODEL_PATH . 'functions.php';
//ユーザーデータに関するファイルの読み込み
require_once MODEL_PATH . 'user.php';
//ログイン確認のためにセッションを開始する。
session_start();
//もし、ログイン確認用関数がtrueであれば
if(is_logined() === true){
  //HOME_URLへリダイレクトする
  redirect_to(HOME_URL);
}
//$nameにPOST受診したnameを代入する
$name = get_post('name');
//$passwordにPOST受信したpasswordを代入する
$password = get_post('password');
//$password_confirmationにPOST受信したpassword_confirmationを代入する
$password_confirmation = get_post('password_confirmation');
//PDOを取得する
$db = get_db_connect();

//処理を開始する
try{
  //regist_user関数を利用し、ユーザー情報を$resultに代入する
  $result = regist_user($db, $name, $password, $password_confirmation);
  //もしユーザー情報が違えば
  if( $result=== false){
    //登録失敗のエラー表示を出し
    set_error('ユーザー登録に失敗しました。');
    //SIGNUP_URLへリダイレクトする
    redirect_to(SIGNUP_URL);
  }

  //catchで例外処理をし
}catch(PDOException $e){
  //登録失敗のエラー表示を出し、
  set_error('ユーザー登録に失敗しました。');
  //SIGNUP_URLへリダイレクトする
  redirect_to(SIGNUP_URL);
}

//エラーが無ければ、登録完了とメッセージをセットし
set_message('ユーザー登録が完了しました。');
//login_as関数を利用してにユーザー情報を登録し
login_as($db, $name, $password);
//HOME_URLへリダイレクトする
redirect_to(HOME_URL);