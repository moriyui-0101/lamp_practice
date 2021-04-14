<?php
  // クリックジャッキング対策
  header('X-FRAME-OPTIONS: DENY');
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <?php include VIEW_PATH . 'templates/head.php'; ?>
  <title>購入履歴詳細</title>
  <link rel="stylesheet" href="<?php print(h(STYLESHEET_PATH . 'admin.css')); ?>">
</head>
<body>
  <?php include VIEW_PATH . 'templates/header_logined.php'; ?>
  <h1>購入履歴詳細</h1>

  <div class="container">
    <?php include VIEW_PATH . 'templates/messages.php'; ?>
    <!--hiddenで受け取り＄変数でもらったものを表示する-->
    <p class="text-right">注文番号: <?php print(h($order_id)); ?></p>
    <p class="text-right">購入日時: <?php print(h($purchase_datatime)); ?></p>
    <p class="text-right">合計金額: <?php print(h(number_format($total))); ?>円</p>

      <table class="table table-bordered">
        <thead class="thead-light">
          <tr>
            <th>商品名</th>
            <th>購入時の商品価格</th>
            <th>購入数</th>
            <th>小計</th>
          </tr>
        </thead>
        <?php foreach($details as $detail){ ?>
          <tr>
            <td><?php print(h($detail['name'])); ?></td>
            <td><?php print(h(number_format($detail['purchase_price']))); ?>円</td>
            <td><?php print(h($detail['purchase_quantity'])); ?>個</td>
            <!--購入時の小計を購入数、購入時の価格と掛け合わせて表示する-->
            <td><?php print h(number_format($detail['purchase_price']*$detail['purchase_quantity'])); ?>円</td>
          </tr>
        <?php } ?>
      </table>
  </div>
</body>
</html>