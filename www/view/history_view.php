<?php
  // クリックジャッキング対策
  header('X-FRAME-OPTIONS: DENY');
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <?php include VIEW_PATH . 'templates/head.php'; ?>
  <title>購入履歴</title>
  <link rel="stylesheet" href="<?php print(h(STYLESHEET_PATH . 'admin.css')); ?>">
</head>
<body>
  <?php include VIEW_PATH . 'templates/header_logined.php'; ?>
  <h1>購入履歴</h1>

  <div class="container">
    <?php include VIEW_PATH . 'templates/messages.php'; ?>
    <?php if(count($historys) > 0){ ?>
      <table class="table table-bordered">
        <thead class="thead-light">
          <tr>
            <th>注文番号</th>
            <th>購入日時</th>
            <th>注文購入日時</th>
            <th>購入詳細ボタン</th>
          </tr>
        </thead>
          <?php foreach($historys as $history){ ?>
        
          <tr>
            <td><?php print(h(number_format($history['order_id']))); ?></td>
            <td><?php print(h($history['purchase_datatime'])); ?></td>
            <td><?php print(h(number_format($history['total']))); ?></td>
            <td>
              <form method="post" action="detail.php">
                <input type="submit" value="購入履歴詳細" class="btn btn-secondary">
                <input type="hidden" name="order_id" value="<?php print(h($history['order_id'])); ?>">
                <input type="hidden" name="user_id" value="<?php print(h($history['user_id'])); ?>">
                <!--detail_viewの上部に必要な情報をhiddenで受け渡しをする-->
                <input type="hidden" name="purchase_datatime" value="<?php print(h($history['purchase_datatime'])); ?>">
                <input type="hidden" name="total" value="<?php print(h($history['total'])); ?>">
                <input type="hidden" name="csrf_token" value="<?php print h($token); ?>">
              </form>
            </td>
          </tr>
          <?php } ?>
      </table>
    <?php } ?>
  </div>
</body>
</html>