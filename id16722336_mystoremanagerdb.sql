-- phpMyAdmin SQL Dump
-- version 4.9.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 22, 2021 at 08:39 AM
-- Server version: 10.3.16-MariaDB
-- PHP Version: 7.3.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `id16722336_mystoremanagerdb`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`id16722336_gabrielonso`@`%` PROCEDURE `count_users` ()  NO SQL
SELECT COUNT(*)
				AS total_users,
    			COUNT(CASE WHEN user_status = 'active' THEN 1 END)
    			AS active_users,
    			COUNT(CASE WHEN user_status = 'inactive' THEN 1 END)
    			AS inactive_users,
    			COUNT(CASE WHEN user_curr_login_status = 'online' THEN 1 END)
    			AS online_users
FROM users_tbl$$

CREATE DEFINER=`id16722336_gabrielonso`@`%` PROCEDURE `fetch_all_users_dtl` ()  NO SQL
BEGIN
	SELECT * FROM users_tbl;
END$$

CREATE DEFINER=`id16722336_gabrielonso`@`%` PROCEDURE `first_daily_update` ()  NO SQL
UPDATE goods_items_tbl itm JOIN rtl_stock_tbl rtl ON itm.item_id = rtl.item_id JOIN wh_stock_tbl wh ON itm.item_id = wh.item_id SET rtl.rtl_strt_stock_tdy = rtl_curr_stock, rtl.rtl_tdy_strt_date = CURRENT_DATE(), rtl.rtl_tdy_strt_time = '00:00:00', rtl.rtl_to_cust_tdy = 0, rtl.rtl_ins_tdy = 0, rtl.rtl_out_tdy = 0, wh.wh_ins_tdy = 0, wh.wh_out_tdy = 0, wh.wh_to_rtl_tdy = 0, wh.wh_to_cust_tdy = 0 WHERE rtl.rtl_tdy_strt_date < CURRENT_DATE()$$

CREATE DEFINER=`id16722336_gabrielonso`@`%` PROCEDURE `goods_item_stock_dtl` ()  NO SQL
SELECT 
	COUNT(itm.item_id) AS ttl_goods_item,
    COUNT(CASE WHEN wh.wh_curr_stock = 0 OR wh.wh_curr_stock IS NULL THEN 1 END) AS out_of_stock
FROM goods_items_tbl itm LEFT JOIN wh_stock_tbl wh ON itm.item_id = wh.item_id$$

CREATE DEFINER=`id16722336_gabrielonso`@`%` PROCEDURE `insert_cust_orders` (IN `invId` INT(11), IN `orderItem` VARCHAR(60), IN `itemId` INT(11), IN `prevOrder_qty` INT(11), IN `orderQty` INT(11), IN `qtyType` ENUM('ctn','pcs'), IN `orderPrice` DECIMAL(12,2), IN `orderAmt` DECIMAL(12,2), IN `orderDate` DATE, IN `orderTime` TIME, IN `orderDatetime` DATETIME)  BEGIN

INSERT INTO cust_orders_tbl (inv_id, order_item, item_id, prev_order_qty, order_qty, qty_type, order_price, order_amt, order_date, order_time, order_datetime)
VALUES (invId, orderItem, itemId, prevOrder_qty, orderQty, qtyType, orderPrice, orderAmt, orderDate, orderTime, orderDatetime);

END$$

CREATE DEFINER=`id16722336_gabrielonso`@`%` PROCEDURE `insert_invoice_dtls` (IN `invNo` VARCHAR(30), IN `invDate` DATE, IN `invTime` TIME, IN `custName` VARCHAR(30), IN `custMobileNo` INT(15), IN `custAddress` VARCHAR(200), IN `salesPerson` VARCHAR(30), IN `invSubTtl` DECIMAL(12,2), IN `invDscTtl` DECIMAL(10,2), IN `invFnlTtl` DECIMAL(12,2), IN `invDatetime` DATETIME)  BEGIN
INSERT INTO sales_inv_tbl (inv_no, inv_date, inv_time, cust_name, cust_mobile_no, cust_address, sales_person, inv_sub_ttl, inv_dsc_ttl, inv_fnl_ttl, inv_datetime)
VALUES (invNo, invDate, invTime, custName, custMobileNo, custAddress, salesPerson, invSubTtl, invDscTtl, invFnlTtl, invDatetime);
END$$

CREATE DEFINER=`id16722336_gabrielonso`@`%` PROCEDURE `insert_payments_dtls` (IN `invId` INT(10), IN `paidBy` VARCHAR(30), IN `paidTo` VARCHAR(30), IN `payAmt` DECIMAL(12,2), IN `anyOutstanding` ENUM('yes','no'), IN `outstandingAmt` DECIMAL(12,2), IN `payType` ENUM('cash','bank','null'), IN `payDate` DATE, IN `payTime` TIME, IN `payDatetime` DATETIME)  BEGIN
INSERT INTO payments (inv_id, paid_by, paid_to, pay_amt, any_outstanding, outstanding_amt, pay_type, pay_date, pay_time, pay_datetime)
VALUES (invId, paidBy, paidTo, payAmt, anyOutstanding, outstandingAmt, payType, payDate, payTime, payDatetime);
END$$

CREATE DEFINER=`id16722336_gabrielonso`@`%` PROCEDURE `invoice_n_payments` ()  NO SQL
SELECT inv.*, pay.pay_amt, pay.outstanding_amt, pay.any_outstanding
	FROM sales_inv_tbl inv
	LEFT JOIN
	(SELECT inv_id, SUM(pay_amt) AS pay_amt, MIN(outstanding_amt) AS outstanding_amt, any_outstanding
		FROM payments
			GROUP BY inv_id, any_outstanding) pay
				ON inv.inv_id = pay.inv_id$$

CREATE DEFINER=`id16722336_gabrielonso`@`%` PROCEDURE `payment_dtls` ()  NO SQL
SELECT SUM(CASE WHEN pay_amt <> 0 THEN pay_amt ELSE 0 END) AS ttl_payments,
    		SUM(CASE WHEN pay_amt <> 0 AND pay_type = 'bank' THEN pay_amt ELSE 0 END) AS ttl_bank_pay,
    		SUM(CASE WHEN pay_amt <> 0 AND pay_type = 'cash' THEN pay_amt ELSE 0 END) AS ttl_cash_pay
FROM payments$$

CREATE DEFINER=`id16722336_gabrielonso`@`%` PROCEDURE `search_goods_item` (IN `itemName` VARCHAR(50))  BEGIN
SELECT * FROM goods_items_tbl WHERE item_name LIKE itemName;
END$$

CREATE DEFINER=`id16722336_gabrielonso`@`%` PROCEDURE `users_perform` ()  NO SQL
SELECT user.user_name,user.user_last_login, user.user_curr_login_status, user.user_last_logout, pay.ttl_pay_recieved, inv.ttl_inv_created 
FROM users_tbl user
	LEFT JOIN (SELECT paid_to, SUM(pay_amt) AS ttl_pay_recieved
               FROM payments
					GROUP BY paid_to) pay ON user.user_name = pay.paid_to
	LEFT JOIN (SELECT sales_person, COUNT(*) AS ttl_inv_created
               FROM sales_inv_tbl
					GROUP BY sales_person) inv ON user.user_name = inv.sales_person
WHERE (ttl_pay_recieved IS NOT NULL OR ttl_pay_recieved <> 0 OR ttl_pay_recieved <> 'null')
					OR (ttl_inv_created IS NOT NULL OR ttl_inv_created <> 0)$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `cust_orders_tbl`
--

CREATE TABLE `cust_orders_tbl` (
  `order_id` int(11) NOT NULL,
  `inv_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `order_item` varchar(50) DEFAULT NULL,
  `prev_order_qty` int(11) NOT NULL DEFAULT 0,
  `order_qty` int(11) NOT NULL,
  `qty_type` enum('ctn','pcs') NOT NULL,
  `order_price` int(10) NOT NULL,
  `order_amt` int(12) NOT NULL,
  `order_date` date NOT NULL,
  `order_time` time NOT NULL,
  `order_datetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `cust_orders_tbl`
--

INSERT INTO `cust_orders_tbl` (`order_id`, `inv_id`, `item_id`, `order_item`, `prev_order_qty`, `order_qty`, `qty_type`, `order_price`, `order_amt`, `order_date`, `order_time`, `order_datetime`) VALUES
(1552, 211, 3, 'GALA', 5, 5, 'ctn', 4500, 22500, '2021-04-28', '19:01:37', '2021-04-28 19:01:37'),
(1555, 211, 9, 'LUSH WOW', 5, 3, 'pcs', 1100, 3300, '2021-04-28', '19:01:37', '2021-04-28 19:01:37'),
(1556, 211, 8, 'ORIGIN', 5, 8, 'pcs', 200, 1600, '2021-04-28', '19:01:37', '2021-04-28 19:01:37'),
(1558, 212, 4, 'AMIGO', 0, 5, 'ctn', 25000, 125000, '2021-04-28', '16:05:15', '2021-04-28 16:05:15'),
(1559, 213, 14, 'MALT', 0, 5, 'pcs', 150, 750, '2021-04-29', '16:30:55', '2021-04-29 16:30:55'),
(1561, 214, 14, 'MALT', 10, 5, 'ctn', 3800, 19000, '2021-05-21', '07:14:35', '2021-05-21 07:14:35'),
(1562, 215, 18, 'YAAHU', 10, 5, 'ctn', 4400, 22000, '2021-05-03', '11:24:44', '2021-05-03 11:24:44'),
(1563, 215, 18, 'YAAHU', 6, 11, 'pcs', 150, 1650, '2021-05-03', '11:24:44', '2021-05-03 11:24:44'),
(1564, 216, 17, 'X-PRESSION', 0, 10, 'ctn', 29000, 290000, '2021-04-29', '16:49:21', '2021-04-29 16:49:21'),
(1565, 217, 22, 'HOLLANDIA', 0, 5, 'ctn', 5200, 26000, '2021-05-01', '20:09:45', '2021-05-01 20:09:45'),
(1566, 217, 3, 'GALA', 0, 10, 'pcs', 100, 1000, '2021-05-01', '20:09:45', '2021-05-01 20:09:45'),
(1570, 218, 15, 'GUINESS', 3, 3, 'pcs', 200, 600, '2021-05-21', '07:27:36', '2021-05-21 07:27:36'),
(1571, 218, 9, 'LUSH WOW', 2, 2, 'pcs', 1100, 2200, '2021-05-21', '07:27:36', '2021-05-21 07:27:36'),
(1572, 218, 8, 'ORIGIN', 2, 2, 'pcs', 200, 400, '2021-05-21', '07:27:36', '2021-05-21 07:27:36'),
(1573, 218, 18, 'YAAHU', 4, 4, 'pcs', 150, 600, '2021-05-21', '07:27:36', '2021-05-21 07:27:36'),
(1574, 215, 17, 'X-PRESSION', 0, 5, 'ctn', 30000, 150000, '2021-05-03', '11:24:44', '2021-05-03 11:24:44'),
(1575, 215, 15, 'GUINESS', 0, 5, 'pcs', 200, 1000, '2021-05-03', '11:24:44', '2021-05-03 11:24:44'),
(1576, 215, 14, 'MALT', 0, 5, 'pcs', 150, 750, '2021-05-03', '11:24:44', '2021-05-03 11:24:44'),
(1577, 215, 9, 'LUSH WOW', 0, 5, 'pcs', 1100, 5500, '2021-05-03', '11:24:44', '2021-05-03 11:24:44'),
(1578, 219, 16, 'TURA SOAP', 5, 2, 'ctn', 7000, 14000, '2021-05-04', '20:21:52', '2021-05-04 20:21:52'),
(1579, 220, 23, 'IBOYA', 0, 5, 'ctn', 6200, 31000, '2021-05-04', '20:15:43', '2021-05-04 20:15:43'),
(1580, 220, 22, 'HOLLANDIA', 0, 1, 'ctn', 5300, 5300, '2021-05-04', '20:15:43', '2021-05-04 20:15:43'),
(1581, 220, 22, 'HOLLANDIA', 0, 5, 'pcs', 550, 2750, '2021-05-04', '20:15:43', '2021-05-04 20:15:43'),
(1582, 219, 22, 'HOLLANDIA', 0, 3, 'pcs', 550, 1650, '2021-05-04', '20:21:52', '2021-05-04 20:21:52'),
(1583, 221, 23, 'IBOYA', 2, 3, 'ctn', 6200, 18600, '2021-05-21', '07:05:33', '2021-05-21 07:05:33'),
(1584, 221, 10, 'LUSH JUMBO', 5, 5, 'ctn', 32000, 160000, '2021-05-21', '07:05:33', '2021-05-21 07:05:33'),
(1585, 221, 14, 'MALT', 5, 5, 'ctn', 3800, 19000, '2021-05-21', '07:05:33', '2021-05-21 07:05:33'),
(1586, 221, 15, 'GUINESS', 10, 10, 'pcs', 200, 2000, '2021-05-21', '07:05:33', '2021-05-21 07:05:33'),
(1587, 221, 9, 'LUSH WOW', 10, 5, 'pcs', 1100, 5500, '2021-05-21', '07:05:33', '2021-05-21 07:05:33'),
(1589, 221, 22, 'HOLLANDIA', 0, 2, 'pcs', 550, 1100, '2021-05-21', '07:05:33', '2021-05-21 07:05:33'),
(1590, 221, 22, 'HOLLANDIA', 0, 4, 'ctn', 5300, 21200, '2021-05-21', '07:05:33', '2021-05-21 07:05:33'),
(1591, 221, 23, 'IBOYA', 0, 2, 'pcs', 120, 240, '2021-05-21', '07:05:33', '2021-05-21 07:05:33'),
(1592, 221, 16, 'TURA SOAP', 0, 3, 'ctn', 7000, 21000, '2021-05-21', '07:05:33', '2021-05-21 07:05:33'),
(1593, 222, 16, 'TURA SOAP', 7, 12, 'ctn', 7000, 84000, '2021-05-21', '19:28:52', '2021-05-21 19:28:52'),
(1594, 222, 15, 'GUINESS', 8, 3, 'ctn', 5000, 15000, '2021-05-21', '19:28:52', '2021-05-21 19:28:52'),
(1595, 222, 23, 'IBOYA', 0, 5, 'pcs', 120, 600, '2021-05-21', '19:28:52', '2021-05-21 19:28:52'),
(1596, 223, 18, 'YAAHU', 0, 5, 'pcs', 150, 750, '2021-05-21', '20:45:23', '2021-05-21 20:45:23'),
(1597, 223, 16, 'TURA SOAP', 0, 10, 'pcs', 300, 3000, '2021-05-21', '20:45:23', '2021-05-21 20:45:23'),
(1598, 223, 9, 'LUSH WOW', 0, 5, 'ctn', 28000, 140000, '2021-05-21', '20:45:23', '2021-05-21 20:45:23'),
(1599, 223, 1, 'CREST', 0, 5, 'ctn', 6000, 30000, '2021-05-21', '20:45:23', '2021-05-21 20:45:23'),
(1600, 223, 23, 'IBOYA', 0, 4, 'pcs', 120, 480, '2021-05-21', '20:45:23', '2021-05-21 20:45:23');

-- --------------------------------------------------------

--
-- Table structure for table `goods_items_tbl`
--

CREATE TABLE `goods_items_tbl` (
  `item_id` int(10) NOT NULL,
  `item_name` varchar(200) NOT NULL,
  `ctn_price` int(10) DEFAULT NULL,
  `unit_price` int(10) DEFAULT NULL,
  `purchase_price` int(10) DEFAULT NULL,
  `unit_per_ctn` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `goods_items_tbl`
--

INSERT INTO `goods_items_tbl` (`item_id`, `item_name`, `ctn_price`, `unit_price`, `purchase_price`, `unit_per_ctn`) VALUES
(1, 'CREST', 6000, 250, 5500, 24),
(3, 'GALA', 4500, 100, 4400, 24),
(4, 'AMIGO', 25000, 950, 23500, 30),
(8, 'ORIGIN', 4800, 200, 4500, 32),
(9, 'LUSH WOW', 28000, 1100, 26000, 30),
(10, 'LUSH JUMBO', 32000, 1600, 31500, 24),
(14, 'MALT', 3800, 150, 3400, 24),
(15, 'GUINESS', 5000, 200, 4800, 24),
(16, 'TURA SOAP', 7000, 300, 6800, 20),
(17, 'X-PRESSION', 31000, 1100, 30000, 30),
(18, 'YAAHU', 4400, 150, 4300, 24),
(22, 'HOLLANDIA', 5300, 550, 4900, 10),
(23, 'IBOYA', 6200, 120, 6000, 36);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `pay_id` int(11) NOT NULL,
  `inv_id` int(11) DEFAULT NULL,
  `paid_by` varchar(200) DEFAULT NULL,
  `paid_to` varchar(50) DEFAULT NULL,
  `pay_amt` decimal(12,2) NOT NULL,
  `any_outstanding` enum('yes','no') NOT NULL,
  `outstanding_amt` decimal(10,2) DEFAULT NULL,
  `pay_type` enum('cash','bank','null') DEFAULT 'null',
  `pay_date` date NOT NULL,
  `pay_time` time NOT NULL,
  `pay_datetime` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`pay_id`, `inv_id`, `paid_by`, `paid_to`, `pay_amt`, `any_outstanding`, `outstanding_amt`, `pay_type`, `pay_date`, `pay_time`, `pay_datetime`) VALUES
(36, 211, NULL, NULL, 0.00, 'no', 27000.00, 'null', '2021-04-28', '15:14:56', '2021-04-28 15:14:56'),
(37, 212, '', '', 0.00, 'no', 125000.00, 'null', '2021-04-28', '16:05:15', '2021-04-28 16:05:15'),
(38, 212, 'TEE', 'Gabrielonso', 125000.00, 'no', 0.00, 'bank', '2021-04-29', '01:39:28', '2021-04-29 01:39:28'),
(39, 213, '', '', 0.00, 'yes', 750.00, 'null', '2021-04-29', '16:30:55', '2021-04-29 16:30:55'),
(40, 214, '', '', 0.00, 'no', 19000.00, 'null', '2021-04-29', '16:36:57', '2021-04-29 16:36:57'),
(41, 215, '', '', 0.00, 'yes', 180000.00, 'null', '2021-04-29', '16:43:13', '2021-04-29 16:43:13'),
(42, 215, 'TEE', 'Gabit', 30000.00, 'yes', 150000.00, 'cash', '2021-04-29', '16:45:11', '2021-04-29 16:45:11'),
(43, 215, 'TEE', 'Nonso', 14900.00, 'yes', 135100.00, 'bank', '2021-04-29', '16:46:52', '2021-04-29 16:46:52'),
(44, 216, '', '', 0.00, 'yes', 290000.00, 'null', '2021-04-29', '16:49:21', '2021-04-29 16:49:21'),
(45, 217, '', '', 0.00, 'yes', 27000.00, 'null', '2021-05-01', '20:09:45', '2021-05-01 20:09:45'),
(46, 217, 'Emmy', 'Nonso', 20000.00, 'yes', 7000.00, 'cash', '2021-05-01', '20:10:50', '2021-05-01 20:10:50'),
(47, 218, '', '', 0.00, 'yes', 3000.00, 'null', '2021-05-01', '21:12:42', '2021-05-01 21:12:42'),
(48, 211, 'MAMA FUNMI', 'Gabit', 27000.00, 'no', 0.00, 'cash', '2021-05-01', '21:19:04', '2021-05-01 21:19:04'),
(49, 216, 'cc', 'Gabit', 200000.00, 'yes', 90000.00, 'cash', '2021-05-01', '21:20:01', '2021-05-01 21:20:01'),
(50, 215, 'TEE', 'Nonso', 100100.00, 'yes', 35000.00, 'bank', '2021-05-03', '13:50:21', '2021-05-03 13:50:21'),
(51, 219, '', '', 0.00, 'no', 15650.00, 'null', '2021-05-04', '20:13:40', '2021-05-04 20:13:40'),
(52, 220, '', '', 0.00, 'no', 39000.00, 'null', '2021-05-04', '20:15:43', '2021-05-04 20:15:43'),
(53, 219, 'TEE', 'Nonso', 15650.00, 'no', 0.00, 'bank', '2021-05-04', '20:23:47', '2021-05-04 20:23:47'),
(54, 220, 'MAMA FUNMI', 'Nonso', 30000.00, 'no', 9000.00, 'bank', '2021-05-04', '20:24:31', '2021-05-04 20:24:31'),
(55, 220, 'MAMA FUNMI', 'Gabit', 9000.00, 'no', 0.00, 'cash', '2021-05-04', '20:26:03', '2021-05-04 20:26:03'),
(56, 221, '', '', 0.00, 'yes', 248000.00, 'null', '2021-05-07', '11:58:27', '2021-05-07 11:58:27'),
(57, 214, 'Uju', 'Gabrielonso', 19000.00, 'no', 0.00, 'cash', '2021-05-21', '07:17:26', '2021-05-21 07:17:26'),
(58, 222, '', '', 0.00, 'yes', 99600.00, 'null', '2021-05-21', '07:40:32', '2021-05-21 07:40:32'),
(59, 221, 'Emmy', 'Gabrielonso', 148000.00, 'yes', 100000.00, 'bank', '2021-05-21', '20:22:00', '2021-05-21 20:22:00'),
(60, 223, '', '', 0.00, 'yes', 174000.00, 'null', '2021-05-21', '20:45:23', '2021-05-21 20:45:23');

-- --------------------------------------------------------

--
-- Table structure for table `price_list`
--

CREATE TABLE `price_list` (
  `price_id` int(10) NOT NULL,
  `item_id` int(10) NOT NULL,
  `supply_id` int(11) DEFAULT NULL,
  `ctn_price` decimal(10,2) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `rtl_stock_tbl`
--

CREATE TABLE `rtl_stock_tbl` (
  `rtl_stock_id` int(11) NOT NULL,
  `item_id` int(11) DEFAULT NULL,
  `rtl_to_cust_tdy` int(10) DEFAULT 0,
  `rtl_strt_stock_tdy` int(11) DEFAULT 0,
  `rtl_tdy_strt_date` date DEFAULT NULL,
  `rtl_ins_tdy` int(11) DEFAULT 0,
  `rtl_out_tdy` int(11) DEFAULT 0,
  `rtl_curr_stock` int(11) DEFAULT 0,
  `rtl_tdy_strt_time` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `rtl_stock_tbl`
--

INSERT INTO `rtl_stock_tbl` (`rtl_stock_id`, `item_id`, `rtl_to_cust_tdy`, `rtl_strt_stock_tdy`, `rtl_tdy_strt_date`, `rtl_ins_tdy`, `rtl_out_tdy`, `rtl_curr_stock`, `rtl_tdy_strt_time`) VALUES
(1, 1, 0, 120, '2021-05-22', 0, 0, 120, '00:00:00'),
(2, 3, 0, 110, '2021-05-22', 0, 0, 110, '00:00:00'),
(3, 4, 0, 150, '2021-05-22', 0, 0, 150, '00:00:00'),
(7, 8, 0, 150, '2021-05-22', 0, 0, 150, '00:00:00'),
(8, 9, 0, 135, '2021-05-22', 0, 0, 135, '00:00:00'),
(9, 10, 0, 120, '2021-05-22', 0, 0, 120, '00:00:00'),
(13, 14, 0, 120, '2021-05-22', 0, 0, 120, '00:00:00'),
(14, 15, 0, 150, '2021-05-22', 0, 0, 150, '00:00:00'),
(15, 16, 0, 165, '2021-05-22', 0, 0, 165, '00:00:00'),
(16, 17, 0, 150, '2021-05-22', 0, 0, 150, '00:00:00'),
(17, 18, 0, 100, '2021-05-22', 0, 0, 100, '00:00:00'),
(18, 22, 0, 100, '2021-05-22', 0, 0, 100, '00:00:00'),
(19, 23, 0, 0, '2021-05-22', 0, 0, 0, '00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `sales_inv_tbl`
--

CREATE TABLE `sales_inv_tbl` (
  `inv_id` int(11) NOT NULL,
  `inv_no` varchar(50) NOT NULL,
  `inv_date` date NOT NULL,
  `inv_time` time NOT NULL,
  `cust_name` varchar(50) DEFAULT 'Not specified',
  `cust_mobile_no` varchar(15) DEFAULT NULL,
  `cust_address` varchar(200) DEFAULT NULL,
  `sales_person` varchar(50) NOT NULL,
  `inv_sub_ttl` decimal(10,2) NOT NULL,
  `inv_dsc_ttl` decimal(10,2) NOT NULL,
  `inv_fnl_ttl` decimal(10,2) NOT NULL,
  `inv_datetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `sales_inv_tbl`
--

INSERT INTO `sales_inv_tbl` (`inv_id`, `inv_no`, `inv_date`, `inv_time`, `cust_name`, `cust_mobile_no`, `cust_address`, `sales_person`, `inv_sub_ttl`, `inv_dsc_ttl`, `inv_fnl_ttl`, `inv_datetime`) VALUES
(211, 'CNO-0211', '2021-04-28', '19:01:37', 'MAMA FUNMI', '809', 'NARAYI', 'Nonso', 27400.00, 400.00, 27000.00, '2021-04-28 19:01:37'),
(212, 'CNO-0212', '2021-04-28', '16:05:15', 'TEE', '818', '', 'Nonso', 125000.00, 0.00, 125000.00, '2021-04-28 16:05:15'),
(213, 'CNO-0213', '2021-04-29', '16:30:55', 'MAMA FUNMI', '0', '', 'Nonso', 750.00, 0.00, 750.00, '2021-04-29 16:30:55'),
(214, 'CNO-0214', '2021-05-21', '07:14:35', 'Uju', '0', '', 'Gabrielonso', 19000.00, 0.00, 19000.00, '2021-05-21 07:14:35'),
(215, 'CNO-0215', '2021-05-03', '11:24:44', 'TEE', '0', '', 'Nonso', 180900.00, 900.00, 180000.00, '2021-05-03 11:24:44'),
(216, 'CNO-0216', '2021-04-29', '16:49:21', 'cc', '0', '', 'Nonso', 290000.00, 0.00, 290000.00, '2021-04-29 16:49:21'),
(217, 'CNO-0217', '2021-05-01', '20:09:45', 'Emmy', '0', '', 'Nonso', 27000.00, 0.00, 27000.00, '2021-05-01 20:09:45'),
(218, 'CNO-0218', '2021-05-21', '07:27:36', 'Davido', '0', '', 'Gabit', 3800.00, 800.00, 3000.00, '2021-05-21 07:27:36'),
(219, 'CNO-0219', '2021-05-04', '20:21:52', 'TEE', '0', '', 'Nonso', 15650.00, 0.00, 15650.00, '2021-05-04 20:21:52'),
(220, 'CNO-0220', '2021-05-04', '20:15:43', 'MAMA FUNMI', '0', '', 'Nonso', 39050.00, 50.00, 39000.00, '2021-05-04 20:15:43'),
(221, 'CNO-0221', '2021-05-21', '07:05:33', 'Emmy', '0', '', 'Gabrielonso', 248640.00, 640.00, 248000.00, '2021-05-21 07:05:33'),
(222, 'CNO-0222', '2021-05-21', '19:28:52', 'Marcus', '8099999', '', 'Gabit', 99600.00, 0.00, 99600.00, '2021-05-21 19:28:52'),
(223, 'CNO-0223', '2021-05-21', '20:45:23', 'Marcelo', '0', '', 'Gabit', 174230.00, 230.00, 174000.00, '2021-05-21 20:45:23');

-- --------------------------------------------------------

--
-- Table structure for table `users_tbl`
--

CREATE TABLE `users_tbl` (
  `user_id` int(3) NOT NULL,
  `user_name` varchar(30) NOT NULL,
  `user_mobile_no` varchar(15) DEFAULT NULL,
  `user_email` varchar(50) DEFAULT NULL,
  `user_password` varchar(255) NOT NULL,
  `user_type` enum('master_admin','admin','user') NOT NULL DEFAULT 'user',
  `user_status` enum('active','inactive') DEFAULT NULL,
  `user_last_login` datetime DEFAULT NULL,
  `user_curr_login_status` enum('online','offline') DEFAULT 'offline',
  `user_last_logout` datetime DEFAULT NULL,
  `user_reset_otp` varchar(255) DEFAULT NULL,
  `user_verified` int(5) DEFAULT NULL,
  `user_security_code` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users_tbl`
--

INSERT INTO `users_tbl` (`user_id`, `user_name`, `user_mobile_no`, `user_email`, `user_password`, `user_type`, `user_status`, `user_last_login`, `user_curr_login_status`, `user_last_logout`, `user_reset_otp`, `user_verified`, `user_security_code`) VALUES
(1, 'Nonso', '08021072445', 'gabitgabriel1@gmail.com', '$2y$10$WPppV8OcQ0BD/jUfeSa1q.fYNjVhU3GXYp4wGP7FU/tJesqWxuHDC', 'master_admin', 'active', '2021-05-21 21:22:04', 'offline', '2021-05-21 21:24:40', '$2y$10$.bYRuONZgqAwlUTDIaNEy.Bnu/b3p84QzXnQIQ2ezLMjl.JnvT1Aq', 1, '$2y$10$WTgTMx9.bJ1FTW.JubUtwOP/WBWNFelISK4dTxiHntNjM1uDhUWNa'),
(4, 'Gabit', '08184668135', 'gonyeaka@yahoo.com', '$2y$10$2bSO8Nlq2tkFa.jAREB37.9jxUcWu.JiK401O7.VhQP1UmwYO/QyW', 'admin', 'active', '2021-05-21 21:09:14', 'offline', '2021-05-21 21:13:14', '$2y$10$TCFDfvFfXIneyxL7f76qeuJoPT3HovOskK9ny400sfWBh/GKcc8ay', 1, '$2y$10$416ft/8eetXHXOLBUnB1/.IvL6ef1/sXdgyWAgBTR/FCAqgjUW7Yu'),
(5, 'Gabrielonso', '07036595109', 'gabriel.onyeaka@gmail.com', '$2y$10$cWClm/IgJfmnomgzoZ/Er.HkO3JQfsdEjZEcj9NG10UrnMOwOkcO6', 'user', 'active', '2021-05-21 19:05:55', 'offline', '2021-05-21 21:04:26', NULL, NULL, '$2y$10$F42BRtfPfWcydzxfMYaMu.waa7WCQm6RoNWnRb8rdoJ4AhMDSfdlK'),
(6, 'Habib', '08090000000', 'go@gmail.com', '$2y$10$OymWsOG8Gw.D29uD0KDkRuD1C7Udfj8Fh8zEimsrDmMsiGK61pfQO', 'admin', 'inactive', '2021-05-10 21:24:05', 'offline', '2021-05-10 21:24:42', NULL, NULL, '$2y$10$jcc6g3DyndNGWSLPOtUwQ.G2XICPpjjYe/Kw28UGCvENSwbDywBce'),
(7, 'ILEOMA', '08034522876', 'matzoileoma95@gmail.com', '$2y$10$G911MPPrDtYv7rb/0NcodeaDdV4mJ3xY4UMTYWXscTbGTsq4E5KX6', 'admin', 'inactive', '2021-05-09 18:06:09', 'offline', '2021-05-09 18:06:15', NULL, NULL, '$2y$10$DEhHoQA.W3GvAJCb.CrHyeA2kO643UMla4HccpIavTCumHtK29VCW');

-- --------------------------------------------------------

--
-- Table structure for table `wh_stock_tbl`
--

CREATE TABLE `wh_stock_tbl` (
  `wh_stock_id` int(10) NOT NULL,
  `item_id` int(10) DEFAULT NULL,
  `wh_stock_b4_supply` int(11) DEFAULT 0,
  `wh_stock_after_supply` int(11) DEFAULT 0,
  `wh_ins_tdy` int(11) DEFAULT 0,
  `wh_out_tdy` int(11) DEFAULT 0,
  `wh_strt_stock` int(11) DEFAULT 0,
  `wh_strt_datetime` datetime DEFAULT current_timestamp(),
  `wh_ins_since_strt` int(11) DEFAULT 0,
  `wh_to_rtl_since_strt` int(11) DEFAULT 0,
  `wh_to_cust_since_strt` int(11) DEFAULT 0,
  `wh_to_rtl_tdy` int(10) DEFAULT 0,
  `wh_to_cust_tdy` int(10) DEFAULT 0,
  `wh_out_since_strt` int(11) DEFAULT 0,
  `wh_curr_stock` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `wh_stock_tbl`
--

INSERT INTO `wh_stock_tbl` (`wh_stock_id`, `item_id`, `wh_stock_b4_supply`, `wh_stock_after_supply`, `wh_ins_tdy`, `wh_out_tdy`, `wh_strt_stock`, `wh_strt_datetime`, `wh_ins_since_strt`, `wh_to_rtl_since_strt`, `wh_to_cust_since_strt`, `wh_to_rtl_tdy`, `wh_to_cust_tdy`, `wh_out_since_strt`, `wh_curr_stock`) VALUES
(1, 1, 0, 150, 0, 0, 150, '2021-04-28 23:52:30', 0, -5, -5, 0, 0, -10, 140),
(2, 3, 35, 95, 0, 0, 120, '2021-04-29 01:29:37', 0, 0, 0, 0, 0, 0, 120),
(3, 4, 50, 180, 0, 0, 50, '2021-04-26 11:31:17', 135, -5, -10, 0, 0, -15, 170),
(7, 8, 0, 100, 0, 0, 100, '2021-04-26 14:11:46', 0, -5, 0, 0, 0, -5, 95),
(8, 9, 0, 200, 0, 0, 200, '2021-04-26 14:19:08', 0, -5, -5, 0, 0, -10, 190),
(9, 10, 0, 110, 0, 0, 110, '2021-04-26 14:20:28', 5, -10, -10, 0, 0, -20, 95),
(13, 14, 95, 145, 0, 0, 100, '2021-04-29 14:12:55', 0, 0, -10, 0, 0, -10, 90),
(14, 15, 3, 123, 0, 0, 0, '2021-05-21 07:27:12', 123, 0, -3, 0, 0, -3, 120),
(15, 16, 0, 150, 0, 0, 0, '2021-04-27 14:01:26', 152, -8, -19, 0, 0, -27, 125),
(16, 17, 0, 80, 0, 0, 80, '2021-04-27 14:19:58', 0, -5, -15, 0, 0, -20, 60),
(17, 18, 100, 150, 0, 0, 150, '2021-05-04 20:02:41', 0, 0, 0, 0, 0, 0, 150),
(18, 22, 0, 96, 0, 0, 96, '2021-05-01 19:52:12', 0, -11, -10, 0, 0, -21, 75),
(19, 23, 0, 50, 0, 0, 50, '2021-05-04 19:58:11', 0, -2, -8, 0, 0, -10, 40);

-- --------------------------------------------------------

--
-- Table structure for table `wh_supplies_tbl`
--

CREATE TABLE `wh_supplies_tbl` (
  `supply_id` int(11) NOT NULL,
  `item_id` int(11) DEFAULT NULL,
  `supplier` varchar(200) DEFAULT 'Not specified',
  `supply_datetime` datetime DEFAULT current_timestamp(),
  `supply_qty` int(10) NOT NULL,
  `supply_price` int(10) DEFAULT NULL,
  `supply_amt` int(12) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `wh_supplies_tbl`
--

INSERT INTO `wh_supplies_tbl` (`supply_id`, `item_id`, `supplier`, `supply_datetime`, `supply_qty`, `supply_price`, `supply_amt`) VALUES
(1, 1, 'AYI', '2021-04-26 11:25:18', 150, 1250, 187500),
(2, 3, 'OGA YELLOW', '2021-04-26 11:28:35', 40, 2900, 116000),
(3, 4, 'Not specified', '2021-04-26 11:31:17', 50, 23000, 1150000),
(7, 8, 'LGM', '2021-04-26 14:11:46', 100, 4500, 450000),
(8, 9, 'OBITEX', '2021-04-26 14:19:08', 200, 26000, 5200000),
(9, 10, 'OBITEX', '2021-04-26 14:20:28', 110, 32000, 3520000),
(14, 15, 'CHINOB', '2021-04-27 11:25:45', 250, 5600, 1400000),
(16, 14, 'MATZO', '2021-04-27 11:46:52', 70, 3600, 252000),
(17, 16, 'AUSTINE GEN', '2021-04-27 14:16:35', 150, 6800, 1020000),
(18, 17, 'Not specified', '2021-04-27 14:19:58', 80, 29000, 2320000),
(19, 4, 'HALLMARK', '2021-04-27 14:22:05', 130, 23500, 3055000),
(20, 3, 'UAC', '2021-04-28 00:26:08', 60, NULL, 0),
(21, 18, 'MATZO', '2021-04-29 01:15:20', 90, 4200, 378000),
(22, 14, 'Not specified', '2021-04-29 01:18:04', 30, 3450, 103500),
(23, 14, 'UAC', '2021-04-29 14:10:26', 50, 3400, 170000),
(24, 18, 'Chinob', '2021-04-29 16:34:27', 50, 4300, 215000),
(25, 18, 'Mai', '2021-04-29 17:18:52', 50, 4300, 215000),
(27, 22, 'OGA YELLOW', '2021-05-01 19:52:12', 96, 4900, 470400),
(28, 23, 'Ty', '2021-05-04 19:58:11', 50, 6000, 300000),
(29, 15, 'AYI', '2021-05-21 07:39:47', 120, 4800, 576000);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cust_orders_tbl`
--
ALTER TABLE `cust_orders_tbl`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `sales_inv_id` (`inv_id`) USING BTREE,
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `goods_items_tbl`
--
ALTER TABLE `goods_items_tbl`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `item_id` (`item_id`,`item_name`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`pay_id`),
  ADD KEY `inv_id` (`inv_id`);

--
-- Indexes for table `price_list`
--
ALTER TABLE `price_list`
  ADD PRIMARY KEY (`price_id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `supply_id` (`supply_id`);

--
-- Indexes for table `rtl_stock_tbl`
--
ALTER TABLE `rtl_stock_tbl`
  ADD PRIMARY KEY (`rtl_stock_id`),
  ADD KEY `rtl_stock_tbl_ibfk_1` (`item_id`);

--
-- Indexes for table `sales_inv_tbl`
--
ALTER TABLE `sales_inv_tbl`
  ADD PRIMARY KEY (`inv_id`);

--
-- Indexes for table `users_tbl`
--
ALTER TABLE `users_tbl`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `wh_stock_tbl`
--
ALTER TABLE `wh_stock_tbl`
  ADD PRIMARY KEY (`wh_stock_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `wh_supplies_tbl`
--
ALTER TABLE `wh_supplies_tbl`
  ADD PRIMARY KEY (`supply_id`),
  ADD KEY `wh_supplies_tbl_ibfk_1` (`item_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cust_orders_tbl`
--
ALTER TABLE `cust_orders_tbl`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1601;

--
-- AUTO_INCREMENT for table `goods_items_tbl`
--
ALTER TABLE `goods_items_tbl`
  MODIFY `item_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `pay_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `price_list`
--
ALTER TABLE `price_list`
  MODIFY `price_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1137;

--
-- AUTO_INCREMENT for table `rtl_stock_tbl`
--
ALTER TABLE `rtl_stock_tbl`
  MODIFY `rtl_stock_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `sales_inv_tbl`
--
ALTER TABLE `sales_inv_tbl`
  MODIFY `inv_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=224;

--
-- AUTO_INCREMENT for table `users_tbl`
--
ALTER TABLE `users_tbl`
  MODIFY `user_id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `wh_stock_tbl`
--
ALTER TABLE `wh_stock_tbl`
  MODIFY `wh_stock_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `wh_supplies_tbl`
--
ALTER TABLE `wh_supplies_tbl`
  MODIFY `supply_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
