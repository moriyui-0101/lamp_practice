<?php
//定数ファイルの読み込み
require_once MODEL_PATH . 'functions.php';
//DB接続用のファイルの読み込み
require_once MODEL_PATH . 'db.php';

//get_user関数を利用し、PDOを利用、$user_idを引数として、
function get_user($db, $user_id){
  //$user_idのid,name,passeword,typeをusers_tableから選ぶ(SQLの上限は１行）
  $sql = "
    SELECT
      user_id, 
      name,
      password,
      type
    FROM
      users
    WHERE
      user_id = :user_id
    LIMIT 1
  ";
//fetch_queryでSQL文を処理し、返す
  return fetch_query($db, $sql, array(':user_id' => $user_id));
}

//get_user_by_name関数で、$nameを引数とする
function get_user_by_name($db, $name){
  //$user_nameのid,name,passeword,typeをusers_tableから選ぶ(SQLの上限は１行）
  $sql = "
    SELECT
      user_id, 
      name,
      password,
      type
    FROM
      users
    WHERE
      name = :name
    LIMIT 1
  ";
  //fetch_queryで上記SQLを実行し、返す
  return fetch_query($db, $sql, array(':name' => $name));
}

//login_as関数で、$name, $passwordを引数で渡す
function login_as($db, $name, $password){
  //get_user_by_name関数で＄namaを取得し、$nameに代入する
  $user = get_user_by_name($db, $name);
  //もし、$userが違う　または　$userのパスワードが違う場合は
  if($user === false || $user['password'] !== $password){
    //falseで返す
    return false;
  }
  //そうでなければ、セッションに$userのuser_idをセットし
  set_session('user_id', $user['user_id']);
  //$userで返す
  return $user;
}

//get_login_user関数で
function get_login_user($db){
  //セッションにセットしたuser_idを＄login_user_idに代入する
  $login_user_id = get_session('user_id');
//get_userでPDOを利用し$login_user_id取得し、戻す
  return get_user($db, $login_user_id);
}

//regist_user関数で、PDOの利用し$name, $password, $password_confirmationを引数として
function regist_user($db, $name, $password, $password_confirmation) {
  //もし、is_valid_user関数を利用し、ログイン情報が違えば（※is_valid_user下記記載）
  if( is_valid_user($name, $password, $password_confirmation) === false){
    //falseで返す
    return false;
  }
  //そうでなければ、insert_userを利用し、BD上に名前とパスワードを登録する
  return insert_user($db, $name, $password);
}

//is_admin関数で
function is_admin($user){
  //userがadminであればadminで返す
  return $user['type'] === USER_TYPE_ADMIN;
}

//is_valid_user関数で
function is_valid_user($name, $password, $password_confirmation){
  // 短絡評価を避けるため一旦代入。
  //is_validのユーザー情報を$is_validに代入する
  $is_valid_user_name = is_valid_user_name($name);
  $is_valid_password = is_valid_password($password, $password_confirmation);
  //代入できたら$is_validを返す
  return $is_valid_user_name && $is_valid_password ;
}

//is_valid_user_name関数を利用し
function is_valid_user_name($name) {
  //$is_valid がtrueならば、
  $is_valid = true;
  //条件式でユーザー名の文字数のエラーチェックをし、falseであれば
  if(is_valid_length($name, USER_NAME_LENGTH_MIN, USER_NAME_LENGTH_MAX) === false){
//set_errorでエラー表示をする
    set_error('ユーザー名は'. USER_NAME_LENGTH_MIN . '文字以上、' . USER_NAME_LENGTH_MAX . '文字以内にしてください。');
    //$is_validはfalseとする
    $is_valid = false;
  }
  //$nameが英数字でなければ
  if(is_alphanumeric($name) === false){
//set_errorでエラー表示をする
    set_error('ユーザー名は半角英数字で入力してください。');
    //$is_validはfalseとする
    $is_valid = false;
  }
  //$is_valid登録せず、返す
  return $is_valid;
}

//is_valid_password関数で
function is_valid_password($password, $password_confirmation){
  //$is_validがtrueであれば、
  $is_valid = true;
  //条件式で、パスワードの長さが良いかエラーチェックをし、falseであれば
  if(is_valid_length($password, USER_PASSWORD_LENGTH_MIN, USER_PASSWORD_LENGTH_MAX) === false){
    //set_errorでエラー表示をする
    set_error('パスワードは'. USER_PASSWORD_LENGTH_MIN . '文字以上、' . USER_PASSWORD_LENGTH_MAX . '文字以内にしてください。');
    //$is_validはfalseとする
    $is_valid = false;
  }
  
  //条件式で、パスワードが英数字でなければ、
  if(is_alphanumeric($password) === false){
    //エラー表示をする
    set_error('パスワードは半角英数字で入力してください。');
    //$is_validはfalseとする
    $is_valid = false;
  }

  //もし、パスワードがユーザーIDに登録されているパスワードと一致しなければ
  if($password !== $password_confirmation){
    //エラー表示をし
    set_error('パスワードがパスワード(確認用)と一致しません。');
    //$is_validはfalseとする
    $is_valid = false;
  }
  //$is_valid登録せず、返す
  return $is_valid;
}

//insert_user関数を利用し、
function insert_user($db, $name, $password){
 //名前とパスワードを登録する
  $sql = "
    INSERT INTO
      users(name, password)
    VALUES (:name, :password);
  ";
//execute_queryでPDOを利用して、SQL文を実行する
  return execute_query($db, $sql, array(':name' => $name, ':password' => $password));
}

