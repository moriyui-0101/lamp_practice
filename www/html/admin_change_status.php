<?php
//定数ファイルの読み込み
require_once '../conf/const.php';
//汎用関数ファイルの読み込み
require_once MODEL_PATH . 'functions.php';
//userデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'user.php';
//itemデータに関する関数ファイルを読み込み。
require_once MODEL_PATH . 'item.php';

//ログインチェックのため、セッションを開始する
session_start();

//もし違っていたらログインURL
if(is_logined() === false){
  redirect_to(LOGIN_URL);
}

//DBに接続する
$db = get_db_connect();

//データベースのユーザーにログインする
$user = get_login_user($db);
//is_adminというadmin確認用関数を用いて、adminでなければ、
if(is_admin($user) === false){
  //ログイン画面へリダイレクトする
  redirect_to(LOGIN_URL);
}
//フォームボタンのゲット送信でitem_idに入ったら＄item_idに代入する
$item_id = get_post('item_id');
//フォームボタンのゲット送信でchanges_toに入ったら＄changes_toに代入する
$changes_to = get_post('changes_to');

//もし、$changesがオープンだったら、
if($changes_to === 'open'){
  update_item_status($db, $item_id, ITEM_STATUS_OPEN);
  //ステータスを変更しましたというメッセージを送る
  set_message('ステータスを変更しました。');
  //もし、$changes_toがクローズだったら
}else if($changes_to === 'close'){
  update_item_status($db, $item_id, ITEM_STATUS_CLOSE);
  //ステータスを変更しましたというメッセージを送る
  set_message('ステータスを変更しました。');
  //それ以外であれば、エラー表示する
}else {
  set_error('不正なリクエストです。');
}

//操作が完了したら、adminURLへ移行する
redirect_to(ADMIN_URL);