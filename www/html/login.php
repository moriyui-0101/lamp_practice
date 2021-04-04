<?php
//定数ファイルの読み込み
require_once '../conf/const.php';
//汎用関数ファイルの読み込み
require_once MODEL_PATH . 'functions.php';
//ログイン確認のために、セッションを開始する
session_start();
//もしログインがis_logined関数をしようし、ログインデーターが正しければ、
if(is_logined() === true){
  //HOME_URLへリダイレクトする
  redirect_to(HOME_URL);
}
//
$token = get_csrf_token();

//login_view.phpを読み込む
include_once VIEW_PATH . 'login_view.php';