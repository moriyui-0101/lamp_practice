<?php 
//定数ファイルの読み込み
require_once '../conf/const.php';
//汎用関数のファイルの読み込み
require_once MODEL_PATH . 'functions.php';
//ユーザーデータに関するファイルの読み込み
require_once MODEL_PATH . 'user.php';
//商品データに関するファイルの読み込み
require_once MODEL_PATH . 'item.php';
//カート内にあるデータに関するファイルの読み込み
require_once MODEL_PATH . 'cart.php';

require_once MODEL_PATH . 'detail.php';

require_once MODEL_PATH . 'history.php';


//ログイン確認のために、セッションを開始する
session_start();
//もし、is_logined関数でfelse　つまりログイン出来なかった時は、
if(is_logined() === false){
  //ログイン画面へリダイレクトする。
  redirect_to(LOGIN_URL);
}

//PDOの取得する
$db = get_db_connect();
//PDOを活用して、ユーザーデーターを取得する
$user = get_login_user($db);
//
$historys = get_user_historys($db, $user['user_id']);

//adminであればadmin用のget_admin_historysで全ユーザの詳細履歴を取得。viewでは$historysなので同じ＄変数で呼び込む
if(is_admin($user) === true){
  $historys = get_admin_historys($db);
}
$token = get_csrf_token();

include_once '../view/history_view.php';