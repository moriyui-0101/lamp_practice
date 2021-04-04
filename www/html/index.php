<?php
//定数ファイルの読み込み
require_once '../conf/const.php';
//汎用関数ファイルの読み込み
require_once MODEL_PATH . 'functions.php';
//ユーザーデーターファイルの読み込み
require_once MODEL_PATH . 'user.php';
//商品データに関するファイルの読み込み
require_once MODEL_PATH . 'item.php';

//ログイン確認のために、セッションを開始する
session_start();
//もし、is_loginedというログイン確認用の関数がfalseだった時、
if(is_logined() === false){
  //LOGIN_URLへリダイレクトする
  redirect_to(LOGIN_URL);
}
//PDO取得する
$db = get_db_connect();
//PDOを活用しユーザーデータを取得する
$user = get_login_user($db);
//PDOを活用し商品データーを取得する
$items = get_open_items($db);

//
$token = get_csrf_token();

//index_view.phpへ読み込みをする
include_once VIEW_PATH . 'index_view.php';