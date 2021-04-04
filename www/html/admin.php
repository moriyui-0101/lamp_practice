<?php
// 定数ファイルを読み込み
require_once '../conf/const.php';
//汎用関数ファイルの読み込み
require_once MODEL_PATH . 'functions.php';
//userデータに関する関数ファイルを読み込み
require_once MODEL_PATH . 'user.php';
//itemデータに関する関数ファイルを読み込み。
require_once MODEL_PATH . 'item.php';

//ログインチェックのため、セッションを開始する
session_start();
//is_loginedというログイン用関数を使用し、falseであれば、
if(is_logined() === false){
  //ログイン画面へリダイレクトする
  redirect_to(LOGIN_URL);
}
//PDOの取得
$db = get_db_connect();
//PDOを利用してログインユーザーのデータを取得
$user = get_login_user($db);
//is_adminというadminを確認する関数を使用し、falseであれば、
if(is_admin($user) === false){
  //ログイン画面へリダイレクトする
  redirect_to(LOGIN_URL);
}
//PDOを利用し、get_all_items関数で全ての関数を取得し、$itemsに代入する
$items = get_all_items($db);

//get_csrf_token関数を読み込むようにする（トークン生成と読み込みをする）　※返り値　＄tokenの関数
$token = get_csrf_token();
//adminviewへ読み込み
include_once VIEW_PATH . '/admin_view.php';
