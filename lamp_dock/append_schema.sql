SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- 購入履歴TABLE　history_table
-- 「注文番号」(order_id)「購入日時」(purchase_datatime)「注文の合計金額」(total)「ユーザーID」(user_id)
-- 「注文番号」オートインクリメント　主key

CREATE TABLE `history_table`(
    `order_id` int(11) NOT NULL AUTO_INCREMENT,
    `purchase_datatime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `total` int(11) NOT NULL,
    `user_id` int(11) NOT NULL,
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 購入明細履歴TABLE detail_table
-- 「明細番号」（detail_id)「注文番号」(order_id)「商品ID」(item_id)「購入数」（purchase_quantity)「購入時の商品価格」（purchase_price)
-- 「明細番号」がオートインクリメント　主key

CREATE TABLE `detail_table` (
  `detail_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `purchase_quantity` int(11) NOT NULL,
  `purchase_price` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
