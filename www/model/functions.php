<?php
//dd関数で$varを引数に渡し
function dd($var){
  //$varの中身をvar_dumpして詳細を確認する
  var_dump($var);
  //プログラムを終了する
  exit();
}

//redierct_to関数で＄urlを引数に渡し
function redirect_to($url){
  //headerを使用し,$uelに代入したURLへリダイレクトする
  header('Location: ' . $url);
  //プログラムを終了する
  exit;
}

//get_getで$nameを引数として渡しGET受信の安全確認をする
function get_get($name){
  //もし、GET受信した$nameが正しければ、issetで安全確認ができれば
  if(isset($_GET[$name]) === true){
    //GETで受信した$nameを返す
    return $_GET[$name];
  };
  //上記の処理をし、get_get関数は空で返す
  return '';
}

//get_post関数でPOST受診した$nameを引数で渡し、POST受信の安全確認をする
function get_post($name){
  //もし、issetで、POST受信した$nameが正しければ、つまり安全確認ができれば
  if(isset($_POST[$name]) === true){
    //POSTで受信した$nameを返す
    return $_POST[$name];
  };
  //上記の処理をし、get_post関数は空で返す
  return '';
}

//get_file関数で＄nameを引数に渡し,アップロードしたファイルの安全確認をする
function get_file($name){
  //もし、issetで＄＿FILES変数を使用し、引数＄nameが正しいつまり安全確認ができれば
  if(isset($_FILES[$name]) === true){
    //アップロードしたファイルの$nameを返す
    return $_FILES[$name];
  };
  //上記の処理をし、$get_file関数は空配列で返す
  return array();
}
function get_session($name){
 if(isset($_SESSION[$name]) === true){
    return $_SESSION[$name];
  };
  //処理をし、＄get＿session関数は空で返す
  return '';
}

//set_sessionで＄name,$valueをセッションをセットする
function set_session($name, $value){
  //SESSION[$name]に＄valueを代入する
  $_SESSION[$name] = $value;
}

//set_error関数で＄errorを引数として
function set_error($error){
  //$_SESSION変数でエラーをセットし、１つずつ$errorに代入する
  $_SESSION['__errors'][] = $error;
}

//get_errors関数で$get_errorsを引数として
function get_errors(){
  //$errorsにセッションで得たエラーを代入する
  $errors = get_session('__errors');
  //もしエラーが空白であれば
  if($errors === ''){
    //空配列で返す
    return array();
  }
  //エラー内容と空配列をsetssionにセットして、
  set_session('__errors',  array());
  //$get_errors関数は上記処理後、＄errorsに入っているものを返す
  return $errors;
}

//has_error関数で
function has_error(){
  //＄_SESSION配列でsessionセットしたエラーの安全確認とエラー回数を確認し、０でなければhas_error関数を返す
  return isset($_SESSION['__errors']) && count($_SESSION['__errors']) !== 0;
}

//set_message関数で$messageを引数として渡し
function set_message($message){
  //$_SESSION変数で$messageを__messages'のセッションをセットする
  $_SESSION['__messages'][] = $message;
}

//get_messages関数で
function get_messages(){
  //＄messagesに__messages'のセッションを代入する
  $messages = get_session('__messages');
  //もし、$messagesが空であれば
  if($messages === ''){
    //空配列を返す
    return array();
  }
  //もし、$messagesの時は、__messagesと空配列をセッションセットし
  set_session('__messages',  array());
  //処理後、$messagesを返す
  return $messages;
}

//is_logined関数で
function is_logined(){
  //セッションで得たuser_idが空でなければ、返す
  return get_session('user_id') !== '';
}

//get_upload_filename関数で$fileを変数で渡し
function get_upload_filename($file){
  //もし$fileのアップロードしたimageが正しく登録できてない場合は、
  if(is_valid_upload_image($file) === false){
    //登録せず空で返す
    return '';
  }
  //exif_imagetype変数を使用し、ファイルのイメージ型を定義して、$minitypeに代入する
  $mimetype = exif_imagetype($file['tmp_name']);
  //ファイルの拡張子を$extに代入する
  $ext = PERMITTED_IMAGE_TYPES[$mimetype];
  //ユニークな値.$extで返す
  return get_random_string() . '.' . $ext;
}

//getget_random_string変数で$length引数にする。$lengthは20文字とする
function get_random_string($length = 20){
  //substrで文字列の一部を返す
  //base_convert変数で数値の基数を任意に変換する
  //hash関数でランダムに決めた文字のhash値を決める
  return substr(base_convert(hash('sha256', uniqid()), 16, 36), 0, $length);
}

//save_image関数で、＄image,$filenameを引数に渡し
function save_image($image, $filename){
  //アップロードされたファイル（画像、画像の名前）を指定ディレクトリに移動して保存
  return move_uploaded_file($image['tmp_name'], IMAGE_DIR . $filename);
}

//delete_image関数を$filenameを引数として渡し
function delete_image($filename){
  //もし、＄filenameがfile_exists変数でファイルまたはディレクトリが存在するかどうか調べ、あれば
  if(file_exists(IMAGE_DIR . $filename) === true){
    //$filenameをunlink変数を用いて削除する（unlinkは成功すれば返り値は0）
    unlink(IMAGE_DIR . $filename);
    //削除できばtrueとする
    return true;
  }
  //そうでなければ、falseとする
  return false;
  
}


//is_valid_lengthで文字数設定をする
function is_valid_length($string, $minimum_length, $maximum_length = PHP_INT_MAX){

  $length = mb_strlen($string);
  return ($minimum_length <= $length) && ($length <= $maximum_length);
}

//is_alphanumeric関数で文字数が英数字か確認する
function is_alphanumeric($string){
  //$stringが英数字であれば、is_valid_formatで返す
  return is_valid_format($string, REGEXP_ALPHANUMERIC);
}

//is_positive_integerで$stringが整数か確認する
function is_positive_integer($string){
  //整数であれば、is_valid_formatで返す
  return is_valid_format($string, REGEXP_POSITIVE_INTEGER);
}

//is_valid_format関数で$stringが$formatの条件と一致するか確認する
function is_valid_format($string, $format){
  //正規表現で正しいとすれば、返す
  return preg_match($format, $string) === 1;
}

//is_valid_upload_image関数で＄imageのファイル形式を確認する
function is_valid_upload_image($image){
  //条件式で、is_uploaded_file関数を使用し、正しくなければ、
  if(is_uploaded_file($image['tmp_name']) === false){
    //エラー表示とし
    set_error('ファイル形式が不正です。');
    //falseで返す
    return false;
  }
  //exif_imagetype関数を使用しtmp_nameを$mimetypeに代入する
  $mimetype = exif_imagetype($image['tmp_name']);
  //$mimetype安全確認をし、falseであれば、
  if( isset(PERMITTED_IMAGE_TYPES[$mimetype]) === false ){
    //エラー表示をし
    set_error('ファイル形式は' . implode('、', PERMITTED_IMAGE_TYPES) . 'のみ利用可能です。');
   //falseで返す
    return false;
  }
  //エラー表示がなければ、trueとする
  return true;
}

//h変数で＄h引数とし
function h($h) {
  //htmlspesoalcharsを使用し、＄hを無害化する
  return htmlspecialchars($h, ENT_QUOTES, 'UTF-8');

}


