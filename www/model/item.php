<?php
//定数ファイルの読み込みをする
require_once MODEL_PATH . 'functions.php';
//DB接続のファイルの読み込みをする
require_once MODEL_PATH . 'db.php';

// DB利用
//get_item関数を利用し、PDOを利用、＄item_idを変数に渡し、
function get_item($db, $item_id){
  //items_tableから＄item_idのitem_id,name,stock,price,image,statusを選ぶ
  $sql = "
    SELECT
      item_id, 
      name,
      stock,
      price,
      image,
      status
    FROM
      items
    WHERE
      item_id = {$item_id}
  ";
　//fetch_queryでPDOを利用して、上記のSQL文を返す
  return fetch_query($db, $sql);
}

//get_items関数をPDO利用し、＄is_openがfalseであれば
function get_items($db, $is_open = false){
  //items_tableから、商品ID、名前、在庫数、価格、画像、ステータスをSELECT文で選ぶ
  $sql = '
    SELECT
      item_id, 
      name,
      stock,
      price,
      image,
      status
    FROM
      items
  ';
  //もし、$is_open === trueであれば、
  if($is_open === true){
    //ステータスが公開になっている商品の商品ID、名前、在庫数、価格、画像を選び、SQL文を作成する
    $sql .= '
      WHERE status = 1
    ';
  }
　//fetch_all_queryでPDOを利用し上記のSQL文を返す
  return fetch_all_query($db, $sql);
}

//get_all_items関数で$db(PDO利用する）を引数とし
function get_all_items($db){
  //PDOを利用し、get_itemsを返す
  return get_items($db);
}

//get_open_items関数で$db(PDO利用する）を引数とし
function get_open_items($db){
  //
  return get_items($db, true);
}

//regist_item関数で$dbでPDOを利用し、$name, $price, $stock, $status, $image取得し、引数で渡す
function regist_item($db, $name, $price, $stock, $status, $image){
  //$imageに入っているimegeをget_upload_filename変数でアップロードして$filenameに代入する
  $filename = get_upload_filename($image);
  //もし、validate_itemの中の$name, $price, $stock, $filename, $statusがfalseであれば、
  if(validate_item($name, $price, $stock, $filename, $status) === false){
    //falseと返す
    return false;
  }
  //そうでなければ、regist_item_transaction関数ででPDOを利用し、$name, $price, $stock, $status, $image, $filenameをトランザクション開始する
  return regist_item_transaction($db, $name, $price, $stock, $status, $image, $filename);
}

//regist_item_transaction関数でPDOを利用し、$name, $price, $stock, $status, $image, $filenameを引数で渡す
function regist_item_transaction($db, $name, $price, $stock, $status, $image, $filename){
  //トランザクション開始する
  $db->beginTransaction();
  //もし、PDOを利用して、$name, $price, $stock, $filename, $statusが登録でき
  if(insert_item($db, $name, $price, $stock, $filename, $status) 
    //かつ save_image関数で$image, $filenameを保存できたら
    && save_image($image, $filename)){
    //コミットする
    $db->commit();
    //trueを返す
    return true;
  }
  //そうでなければ、ロールバックをして
  $db->rollback();
  //falseを返す
  return false;
  
}

//insert_item関数でPDOを利用し、$name, $price, $stock, $filename, $status引数に渡す
function insert_item($db, $name, $price, $stock, $filename, $status){
  
  $status_value = PERMITTED_ITEM_STATUSES[$status];
  //{$name}', {$price}, {$stock}, '{$filename}', {$status_value}をitems_tableのname,price,stock,image,statusに登録する
  //SQL文を作成する
  $sql = "
    INSERT INTO
      items(
        name,
        price,
        stock,
        image,
        status
      )
    VALUES('{$name}', {$price}, {$stock}, '{$filename}', {$status_value});
  ";
　//execute_queryでPDO利用し、上記SQL文を返す
  return execute_query($db, $sql);
}

//update_item_statusでPDOを利用し、$item_id, $status引数で渡す
function update_item_status($db, $item_id, $status){
  //選んだ商品IDのステータスを変更する（ただし、SQL文の上限は１行とする）
  $sql = "
    UPDATE
      items
    SET
      status = {$status}
    WHERE
      item_id = {$item_id}
    LIMIT 1
  ";
  //execute_queryでPDOを利用して、SQLを返す
  return execute_query($db, $sql);
}

//update_item_stock関数で、PDOの利用、＄item_id,$stockを引数として渡す
function update_item_stock($db, $item_id, $stock){
  //選んだ商品のストックを変更する（SQLの上限は１行）
  $sql = "
    UPDATE
      items
    SET
      stock = {$stock}
    WHERE
      item_id = {$item_id}
    LIMIT 1
  ";
  //execute_queryでPDOを利用し、上記SQLを返す
  return execute_query($db, $sql);
}
//destroy_item関数でPDOの利用、　$item_idを引数として渡し
function destroy_item($db, $item_id){
 //PDOを利用、get_item関数使用し、$item_idの商品情報を$item_idへ代入する（※get_item関数の詳細は上記記載あり）
  $item = get_item($db, $item_id);
  //もし、＄itemの内容が違えば
  if($item === false){
    //falseを返す
    return false;
  }
  //トランザクション開始
  $db->beginTransaction();
  //もし、delete_item関数を使用し$itemのitem_id（選択した商品ID）が削除でき（※delete_itemは下記記載あり）
  if(delete_item($db, $item['item_id'])
  　//かつイメージ図もダク所できたら（選択した商品イメージ）が削除できたら
    && delete_image($item['image'])){
    //コミットする
    $db->commit();
    //trueを返す
    return true;
  }
  //そうでなければロールバックをして
  $db->rollback();
  //falseを返す
  return false;
}

//delete_item関数で$item_idを引数として渡し
function delete_item($db, $item_id){
  //$item_idを削除する。SQL文の上限は１行とする
  $sql = "
    DELETE FROM
      items
    WHERE
      item_id = {$item_id}
    LIMIT 1
  ";
  //execute_queryで上記SQL文を返す
  return execute_query($db, $sql);
}


// 非DB
//is_open関数で＄item_idを引数に渡し
function is_open($item){
  //$itemのステータスを１（公開）で返す
  return $item['status'] === 1;
}

//validate_item関数で$name, $price, $stock, $filename, $status引数に渡し
function validate_item($name, $price, $stock, $filename, $status){
  //is_valid_item_name($name)を$$is_valid_item_nameへ代入
  $is_valid_item_name = is_valid_item_name($name);
  //is_valid_item_name($price)を$$is_valid_item_priceへ代入
  $is_valid_item_price = is_valid_item_price($price);
  //is_valid_item_name($stock)を$$is_valid_item_stockへ代入
  $is_valid_item_stock = is_valid_item_stock($stock);
  //is_valid_item_name($filename)を$$is_valid_item_filenameへ代入
  $is_valid_item_filename = is_valid_item_filename($filename);
  //is_valid_item_name($status)を$$is_valid_item_statusへ代入
  $is_valid_item_status = is_valid_item_status($status);
  
  //$is_valid_itemのname,price,stock,filename,statusを返す
  return $is_valid_item_name
    && $is_valid_item_price
    && $is_valid_item_stock
    && $is_valid_item_filename
    && $is_valid_item_status;
}

//is_valid_item_nameで$nameを引数にし
function is_valid_item_name($name){
  //$is_validがtrueであれば、
  $is_valid = true;
  //条件式でエラーでないか確認。もし、＄nameの文字数の長さが短すぎる、長すぎる場合は
  if(is_valid_length($name, ITEM_NAME_LENGTH_MIN, ITEM_NAME_LENGTH_MAX) === false){
    //商品名の文字数のエラーをセットする
    set_error('商品名は'. ITEM_NAME_LENGTH_MIN . '文字以上、' . ITEM_NAME_LENGTH_MAX . '文字以内にしてください。');
    //$is_validはfalseとする
    $is_valid = false;
  }
  //$is_valid登録せず、falseとする。
  return $is_valid;
}
//is_valid_item_price関数で＄priceを引数にし
function is_valid_item_price($price){
  //$is_validが正しければ
  $is_valid = true;
  //条件式でエラーでないか確認。もし、is_positive_integer変数を用いて、$priceが整数でなければ
  if(is_positive_integer($price) === false){
    //価格は0以上の整数で入力してくださいというエラーを設定する
    set_error('価格は0以上の整数で入力してください。');
    //$is_validはfalseとする
    $is_valid = false;
  }
  //$is_validを登録せず、返す。
  return $is_valid;
}

//is_valid_item_stockの$stockを引数にし
function is_valid_item_stock($stock){
  //$is_validが正しければ
  $is_valid = true;
  //条件式でエラーでないか確認。もし、is_positive_integer変数を用いて、$stockが整数でなければ
  if(is_positive_integer($stock) === false){
    //set_errorでエラーとるする
    set_error('在庫数は0以上の整数で入力してください。');
    //$is_validはfalseとする。
    $is_valid = false;
  }
  //$is_validを登録せず、返す。
  return $is_valid;
}

//is_valid_item_filename関数を使用し、$filenameとする
function is_valid_item_filename($filename){
  //$is_validが正しければ
  $is_valid = true;
  //もし、$filenameが空白であれば、
  if($filename === ''){
    //$is_validはfalseとする。
    $is_valid = false;
  }
  //$is_validを登録せず、返す。
  return $is_valid;
}

//is_valid_item_status関数を用いて、$statusを引数とし
function is_valid_item_status($status){
  //$is_validが正しければ
  $is_valid = true;
  //ステータスが０または１でなければfalseとし
  if(isset(PERMITTED_ITEM_STATUSES[$status]) === false){
    //$is_validはfalseとする。
    $is_valid = false;
  }
  //$is_validを登録せず、返す。
  return $is_valid;
}