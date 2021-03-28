<?php
//定数ファイルの読み込み
require_once '../conf/const.php';
//汎用定数ファイルの読み込み
require_once MODEL_PATH . 'functions.php';
//ログイン確認のためにセッションを開始する。
session_start();
//$_SESSIONの配列を用意する
$_SESSION = array();

//session_get_cookie_paramsでセッションに登録されたクッキー情報を取得し、$params代入うする
$params = session_get_cookie_params();
//sessionに利用しているクッキーの有効期限を過去に設定することで無効化
setcookie(session_name(), '', time() - 42000,
  $params["path"], 
  $params["domain"],
  $params["secure"], 
  $params["httponly"]
);
//セッションの無効化
session_destroy();
//LOGIN_URLへリダイレクト
redirect_to(LOGIN_URL);

