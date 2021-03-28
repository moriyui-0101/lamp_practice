<?php
//定数用ファイルの読み込み
require_once '../conf/const.php';
//汎用関数ファイルの読み込み
require_once MODEL_PATH . 'functions.php';
//userデータに関するファイルの読み込み
require_once MODEL_PATH . 'user.php';
//itemデータに関するファイルの読み込み
require_once MODEL_PATH . 'item.php';
//ログインチェックを開始するため、セッションを開始する
session_start();
//is_loginedというログイン関数を使ってfalseであれば、
if(is_logined() === false){
  //ログイン画面へリダイレクトする
  redirect_to(LOGIN_URL);
}
//PDOの取得
$db = get_db_connect();
// PDOを利用してログインユーザーのデータを取得
$user = get_login_user($db);

//もし、is_admin関数の＄userつまりadminでなければ
if(is_admin($user) === false){
  //ログイン画面へリダイレクトする
  redirect_to(LOGIN_URL);
}
//get_POST関数で受け取ったnameを＄nameへ代入
$name = get_post('name');
//get_POST関数で受け取ったpriceを＄priceへ代入
$price = get_post('price');
//get_POST関数で受け取ったstatusを＄statusへ代入
$status = get_post('status');
//get_POST関数で受け取ったstockを$stockへ代入
$stock = get_post('stock');
//get_POST関数で受け取ったimageを$imageへ代入
$image = get_file('image');

//regist_item関数（item.phpでトランザクションし、インサートする）PDOの$name, $price, $stock, $status, $imageを登録し
if(regist_item($db, $name, $price, $stock, $status, $image)){
  //商品を登録しましたというメッセージを返す
  set_message('商品を登録しました。');
  //そうでなければ、
}else {
  //商品登録に失敗しましたというメッセージを返す
  set_error('商品の登録に失敗しました。');
}

//操作が終わればadmin_urlへリダイレクトする
redirect_to(ADMIN_URL);