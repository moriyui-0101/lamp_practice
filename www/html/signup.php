<?php
//定数ファイルの読み込み
require_once '../conf/const.php';
//汎用関数ファイルの読み込み
require_once MODEL_PATH . 'functions.php';
//ログイン確認のために、セッションを開始する
session_start();
//もし、ログイン確認の関数がtrueであれば、
if(is_logined() === true){
  //HOME_URLにリダイレクトする
  redirect_to(HOME_URL);
}
//signup_view.phpを読み込む
include_once VIEW_PATH . 'signup_view.php';



