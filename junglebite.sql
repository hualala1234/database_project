-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1
-- 產生時間： 2025-05-11 06:35:15
-- 伺服器版本： 10.4.32-MariaDB
-- PHP 版本： 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 資料庫： `junglebite`
--

-- --------------------------------------------------------

--
-- 資料表結構 `allergenicingredient`
--

CREATE TABLE `allergenicingredient` (
  `allergenicid` int(11) NOT NULL,
  `allergenicIngrediant` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `allergy`
--

CREATE TABLE `allergy` (
  `cid` int(11) NOT NULL,
  `allergens` varchar(255) DEFAULT NULL,
  `other_allergen` varchar(255) DEFAULT NULL,
  `cName` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `allergy`
--

INSERT INTO `allergy` (`cid`, `allergens`, `other_allergen`, `cName`) VALUES
(14, 'shellfish', '', NULL);

-- --------------------------------------------------------

--
-- 資料表結構 `bank`
--

CREATE TABLE `bank` (
  `cid` int(11) NOT NULL,
  `bankCode` varchar(3) NOT NULL,
  `accountNumber` int(16) NOT NULL,
  `withdraw` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `bank`
--

INSERT INTO `bank` (`cid`, `bankCode`, `accountNumber`, `withdraw`) VALUES
(1, '007', 11112121, 0),
(9, '719', 2147483647, 0);

-- --------------------------------------------------------

--
-- 資料表結構 `browse`
--

CREATE TABLE `browse` (
  `cid` int(11) NOT NULL,
  `pid` int(10) NOT NULL,
  `productBrowsedCount` int(10) NOT NULL DEFAULT 0,
  `categoryBrowsedCount` int(10) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `caddress`
--

CREATE TABLE `caddress` (
  `address_id` int(11) NOT NULL,
  `cid` int(11) NOT NULL,
  `address_text` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `caddress`
--

INSERT INTO `caddress` (`address_id`, `cid`, `address_text`) VALUES
(1, 1, 'YYY'),
(2, 9, 'pppp');

-- --------------------------------------------------------

--
-- 資料表結構 `card`
--

CREATE TABLE `card` (
  `cid` int(11) NOT NULL,
  `role` varchar(1) NOT NULL DEFAULT 'c',
  `cardName` varchar(20) NOT NULL,
  `cardHolder` varchar(10) NOT NULL,
  `cardNumber` varchar(16) NOT NULL,
  `cardType` varchar(10) NOT NULL DEFAULT 'Visa',
  `cvv` varchar(3) NOT NULL,
  `expirationDate` varchar(7) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `card`
--

INSERT INTO `card` (`cid`, `role`, `cardName`, `cardHolder`, `cardNumber`, `cardType`, `cvv`, `expirationDate`) VALUES
(1, 'c', 'c10', 'Andy10', '1010101010101010', 'visa', '101', '2029-03'),
(1, 'c', 'c8', 'Wendy8', '8888888888888888', 'JSB', '888', '2029-09'),
(1, 'c', 'card11', 'Wendy8', '8888888888888888', 'JSB', '888', '0000-00'),
(1, 'c', 'card111', 'Andy10', '1010101010101010', 'visa', '101', '2031-11'),
(1, 'c', 'card121', 'Mr.Andy', '1222222222222222', 'MasterCard', '122', '10-2043'),
(1, 'c', 'card2', 'Jerry', '2222333311111111', 'visa', '222', '2031-11'),
(1, 'c', 'card4', 'Cindy', '2222222222222220', 'MasterCard', '888', '2030-10'),
(1, 'c', 'creditCard', 'James', '11110', 'MasterCard', '111', '2025-04'),
(2, 'c', 'card6', 'Wang6', '6666666666666667', 'JSB', '666', '0000-00'),
(2, 'c', 'card7', 'Amy7', '7777777777777777', 'JSB', '777', '0000-00'),
(3, 'c', 'card5', 'Herry5', '5555555555555555', 'visa', '555', '0000-00'),
(9, 'c', 'card4', 'Cindy', '2222222222222220', 'MasterCard', '888', '2030-10'),
(9, 'c', 'card5', 'Herry5', '5555555555555555', 'visa', '555', '0000-00'),
(9, 'c', 'card6', 'Wang6', '6666666666666667', 'JSB', '666', '0000-00'),
(14, 'c', 'card7', 'Amy7', '7777777777777777', 'JSB', '777', '0000-00'),
(14, 'c', 'CreditCard', 'James', '11110', 'MasterCard', '111', '0000-00'),
(15, 'c', 'first card', 'jaja', '1111111111111111', 'visa', '111', '2025-05'),
(15, 'c', 'second card', 'j2', '2222333322222222', 'MasterCard', '222', '2027-05'),
(15, 'c', 'third card', 'j3', '3333343433333333', 'JSB', '333', '2030-09'),
(18, 'c', 'aa1', 'aa', '1212121212121212', 'visa', '121', '2027-07'),
(18, 'c', 'aa2', 'aa', '2121212121212121', 'MasterCard', '212', '2030-10'),
(18, 'c', 'aa3', 'aa3', '1313131313131313', 'visa', '131', '2043-12');

-- --------------------------------------------------------

--
-- 資料表結構 `cart`
--

CREATE TABLE `cart` (
  `cid` int(11) NOT NULL,
  `cartTime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `cart`
--

INSERT INTO `cart` (`cid`, `cartTime`) VALUES
(14, '2025-05-02 22:04:03'),
(14, '2025-05-02 22:08:31'),
(15, '2025-05-09 16:53:30');

-- --------------------------------------------------------

--
-- 資料表結構 `cartitem`
--

CREATE TABLE `cartitem` (
  `cid` int(11) NOT NULL,
  `cartTime` datetime NOT NULL,
  `pid` int(11) NOT NULL,
  `quantity` int(11) DEFAULT NULL,
  `specialNote` text DEFAULT NULL,
  `mid` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `cartitem`
--

INSERT INTO `cartitem` (`cid`, `cartTime`, `pid`, `quantity`, `specialNote`, `mid`) VALUES
(14, '2025-05-02 22:04:03', 1, 1, NULL, 1),
(14, '2025-05-02 22:08:31', 1, 1, '', 1);

-- --------------------------------------------------------

--
-- 資料表結構 `cbank`
--

CREATE TABLE `cbank` (
  `cid` int(11) NOT NULL,
  `bankCode` varchar(3) NOT NULL,
  `accountNumber` int(16) NOT NULL,
  `withdraw` int(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `companyaccount`
--

CREATE TABLE `companyaccount` (
  `account` int(16) NOT NULL,
  `settlementTime` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `coupons`
--

CREATE TABLE `coupons` (
  `id` int(11) NOT NULL,
  `message` varchar(255) NOT NULL,
  `code` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL,
  `customerid` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `coupons`
--

INSERT INTO `coupons` (`id`, `message`, `code`, `created_at`, `customerid`) VALUES
(1, '???? 恭喜你獲得 15% 折扣優惠！', 'CLAWWIN15', '2025-05-02 12:19:54', NULL),
(2, '???? 恭喜你獲得 20% 折扣優惠！', 'CLAWWIN20', '2025-05-02 12:31:40', NULL),
(3, '???? 恭喜你獲得免運費1次！', 'CLAWSHIP', '2025-05-02 12:33:00', NULL),
(4, '???? 恭喜你獲得 15% 折扣優惠！', 'CLAWWIN15', '2025-05-02 12:34:21', NULL),
(5, '???? 恭喜你獲得免運費1次！', 'CLAWSHIP', '2025-05-02 12:59:50', NULL),
(6, '???? 恭喜你獲得免運費1次！', 'CLAWSHIP', '2025-05-03 06:38:07', NULL),
(7, '???? 恭喜你獲得免運費1次！', 'CLAWSHIP', '2025-05-04 02:32:35', NULL),
(8, '???? 恭喜你獲得 20% 折扣優惠！', 'CLAWWIN20', '2025-05-04 03:32:23', 16),
(9, '🎉 恭喜你獲得 15% 折扣優惠！', 'CLAWWIN15', '2025-05-04 03:35:40', 16);

-- --------------------------------------------------------

--
-- 資料表結構 `customer`
--

CREATE TABLE `customer` (
  `cid` int(11) NOT NULL,
  `role` varchar(1) NOT NULL DEFAULT 'c',
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `cName` varchar(255) NOT NULL,
  `cRegistrationTime` timestamp NOT NULL DEFAULT current_timestamp(),
  `coupons` int(11) DEFAULT 0,
  `birthday` date DEFAULT NULL,
  `imageURL` longtext DEFAULT NULL,
  `membership` tinyint(1) DEFAULT 0,
  `vipTime` timestamp NULL DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `introducer` int(11) DEFAULT NULL,
  `initiator` int(11) DEFAULT NULL,
  `gId` int(11) DEFAULT NULL,
  `address` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `customer`
--

INSERT INTO `customer` (`cid`, `role`, `email`, `password`, `cName`, `cRegistrationTime`, `coupons`, `birthday`, `imageURL`, `membership`, `vipTime`, `phone`, `introducer`, `initiator`, `gId`, `address`) VALUES
(1, 'c', 'c1@gmail.com', 'c1', 'Andy', '2025-04-25 10:07:18', 0, '2025-04-25', 'images/ad-image-1.png', 0, NULL, '1234', NULL, NULL, NULL, ''),
(2, 'c', 'wang@gmail.com', 'wang', 'wang', '2025-05-06 07:03:56', 0, '2013-01-15', NULL, 0, NULL, NULL, NULL, NULL, NULL, ''),
(3, 'c', 'herry@gmail.com', 'herry', 'Herry', '2025-05-06 07:03:56', 0, '2015-09-23', NULL, 0, NULL, NULL, NULL, NULL, NULL, ''),
(9, 'c', 'c2@gmail.com', 'c2', 'Cindy', '2025-05-01 20:04:38', 0, NULL, 'default-avatar.png', 0, NULL, NULL, 1, NULL, NULL, 'c2'),
(14, 'c', 'c3@gmail.com', 'c3', 'Tom', '2025-05-01 21:13:45', 0, NULL, 'upload_images/6813e40918f18-7.jpg', 0, NULL, NULL, NULL, NULL, NULL, 'c3'),
(15, 'c', 'ja@gmail.com', 'jaja', 'jaja', '2025-05-08 18:29:33', 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, 'here'),
(18, 'c', 'aa@gmail.com', 'aa', 'aa', '2025-05-08 18:49:02', 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, 'aa');

-- --------------------------------------------------------

--
-- 資料表結構 `cwallet`
--

CREATE TABLE `cwallet` (
  `cid` int(10) NOT NULL,
  `wid` varchar(10) NOT NULL,
  `balance` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `cwallet`
--

INSERT INTO `cwallet` (`cid`, `wid`, `balance`) VALUES
(1, 'w1', 111);

-- --------------------------------------------------------

--
-- 資料表結構 `dbank`
--

CREATE TABLE `dbank` (
  `did` int(11) NOT NULL,
  `bankCode` varchar(3) NOT NULL,
  `accountNumber` int(16) NOT NULL,
  `withdraw` int(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `dbank`
--

INSERT INTO `dbank` (`did`, `bankCode`, `accountNumber`, `withdraw`) VALUES
(1, '007', 11112121, 300),
(13, '', 0, NULL);

-- --------------------------------------------------------

--
-- 資料表結構 `deliveryperson`
--

CREATE TABLE `deliveryperson` (
  `did` int(100) NOT NULL,
  `role` varchar(1) NOT NULL DEFAULT 'd',
  `password` varchar(50) NOT NULL,
  `dpAddress` varchar(100) NOT NULL,
  `dpName` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `dPicture` longtext DEFAULT NULL,
  `dRegistrationTime` timestamp NOT NULL DEFAULT current_timestamp(),
  `currentLocation` varchar(200) DEFAULT NULL,
  `rating` int(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `deliveryperson`
--

INSERT INTO `deliveryperson` (`did`, `role`, `password`, `dpAddress`, `dpName`, `email`, `dPicture`, `dRegistrationTime`, `currentLocation`, `rating`) VALUES
(1, 'd', '111', 'sss', 'uuu', 'aa@gmail.com', 'default-avatar.png', '2025-04-25 09:27:21', NULL, NULL),
(2, 'd', '111', 'sss', 'eee', 'nsysu.mis.pr@gmail.com', 'default-avatar.png', '2025-04-25 09:29:47', NULL, NULL),
(3, 'd', '111', '111', 'rrr', 'ss@gmail.com', 'default-avatar.png', '2025-04-27 14:48:16', NULL, NULL),
(4, 'd', '111', '111', 'qq', 'qqqq@gmail.com', 'default-avatar.png', '2025-04-29 07:43:26', NULL, NULL),
(5, 'd', '111', 'aaa', 'wendy', 'wwww@gmail.com', 'default-avatar.png', '2025-04-29 07:43:48', NULL, NULL),
(6, 'd', 'rrrr', 'rrrr', 'rrrr', 'rrrr@gmail.com', 'default-avatar.png', '2025-05-01 10:40:32', NULL, NULL),
(7, 'd', 'ccc', 'ccc', 'ccc', 'ccc@gmail.com', 'default-avatar.png', '2025-05-01 13:43:59', NULL, NULL),
(8, 'd', '111', '111', 'wendy', 'lai@gmail.com', 'default-avatar.png', '2025-05-01 13:51:58', NULL, NULL),
(9, 'd', '111', 'FFF', 'FFFF', 'FFF@gmail.com', 'default-avatar.png', '2025-05-01 14:05:33', NULL, NULL),
(10, 'd', 'dd', 'dd.street', 'dd', 'dd@gmail.com', 'default-avatar.png', '2025-05-08 18:31:38', NULL, NULL),
(11, 'd', 'd1', 'd1.road', 'd1', 'd1@gmail.com', 'default-avatar.png', '2025-05-08 20:22:59', NULL, NULL),
(12, 'd', 'd2', 'd2.road', 'd2', 'd2@gmail.com', 'default-avatar.png', '2025-05-08 20:24:55', NULL, NULL),
(13, 'd', 'd3', 'd3.road', 'd3', 'd3@gmail.com', 'default-avatar.png', '2025-05-08 20:26:21', NULL, NULL);

-- --------------------------------------------------------

--
-- 資料表結構 `dorders`
--

CREATE TABLE `dorders` (
  `tranId` int(11) NOT NULL,
  `did` int(11) NOT NULL,
  `dAcceptTime` datetime DEFAULT current_timestamp(),
  `cid` int(11) NOT NULL,
  `takeTime` datetime DEFAULT NULL,
  `arriveTime` datetime DEFAULT NULL,
  `arrivePicture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `dwallet`
--

CREATE TABLE `dwallet` (
  `did` int(10) NOT NULL,
  `wid` varchar(10) NOT NULL,
  `balance` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `dwallet`
--

INSERT INTO `dwallet` (`did`, `wid`, `balance`) VALUES
(1, 'w2', 222);

-- --------------------------------------------------------

--
-- 資料表結構 `foodpreferences`
--

CREATE TABLE `foodpreferences` (
  `id` int(11) NOT NULL,
  `cid` int(11) NOT NULL,
  `foodPreference` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `mbank`
--

CREATE TABLE `mbank` (
  `mid` int(11) NOT NULL,
  `bankCode` varchar(3) NOT NULL,
  `accountNumber` int(17) NOT NULL,
  `withdraw` int(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `mbank`
--

INSERT INTO `mbank` (`mid`, `bankCode`, `accountNumber`, `withdraw`) VALUES
(1, '001', 2147483647, 200);

-- --------------------------------------------------------

--
-- 資料表結構 `merchant`
--

CREATE TABLE `merchant` (
  `mid` int(11) NOT NULL,
  `role` varchar(1) NOT NULL DEFAULT 'm',
  `mName` varchar(100) NOT NULL,
  `mEmail` varchar(100) NOT NULL,
  `registrationTime` timestamp NOT NULL DEFAULT current_timestamp(),
  `password` varchar(255) NOT NULL,
  `businessHours` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `favoritesCount` int(11) DEFAULT 0,
  `mAddress` varchar(255) NOT NULL,
  `mPicture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `merchant`
--

INSERT INTO `merchant` (`mid`, `role`, `mName`, `mEmail`, `registrationTime`, `password`, `businessHours`, `favoritesCount`, `mAddress`, `mPicture`) VALUES
(1, 'm', '123', 'm100@gmail.com', '2025-04-24 15:55:48', 'test', '09:00-18:00', 0, 'here', 'upload_images/1745913819-681087dbb0a51.jpg'),
(2, 'm', 'm10', 'm10@gmail.com', '2025-04-24 20:57:17', 'test', '09:00-17:00', 0, 'Test Address', 'img/fruite-item-2.jpg'),
(3, 'm', 'Wilsom', 'wilsom@gmail.com', '2025-05-06 06:48:11', 'wilsom', NULL, 0, 'wilsom home', NULL),
(4, 'm', 'Andy', 'andy@gmail.com', '2025-05-06 06:48:11', 'andy', NULL, 0, 'andy house', NULL),
(5, 'm', 'Cindy', 'cindy@gmail.com', '2025-05-06 06:49:11', 'cindy', NULL, 0, 'cindy street', NULL),
(6, 'm', 'Tom', 'tom@gamil.com', '2025-05-06 06:49:11', 'tom', NULL, 0, 'tom road', NULL),
(7, 'm', 'm3', 'm3@gmail.com', '2025-05-01 13:28:27', 'm3', NULL, 0, 'm3', 'default-avatar.png'),
(8, 'm', 'm4', 'm4@gmail.com', '2025-05-01 13:29:12', 'm4', NULL, 0, 'm4', 'upload_images/6813e7a8a876f-8.jpg'),
(9, 'm', 'm5', 'm5@gmail.com', '2025-05-03 04:07:16', '0000', NULL, 0, 'here', 'default-avatar.png');

-- --------------------------------------------------------

--
-- 資料表結構 `mwallet`
--

CREATE TABLE `mwallet` (
  `mid` int(10) NOT NULL,
  `wid` varchar(10) NOT NULL,
  `balance` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `mwallet`
--

INSERT INTO `mwallet` (`mid`, `wid`, `balance`) VALUES
(1, 'w3', 222);

-- --------------------------------------------------------

--
-- 資料表結構 `orders`
--

CREATE TABLE `orders` (
  `cid` int(11) NOT NULL,
  `cartTime` datetime NOT NULL,
  `pid` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `quantity` int(11) DEFAULT NULL,
  `mid` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `platform`
--

CREATE TABLE `platform` (
  `platId` int(100) NOT NULL,
  `password` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `imageURL` longtext DEFAULT NULL,
  `DateCreate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `platform`
--

INSERT INTO `platform` (`platId`, `password`, `name`, `email`, `imageURL`, `DateCreate`) VALUES
(1, 'jjj', 'jjj', 'jjj@gmail.com', 'default-avatar.png', '2025-05-01 14:22:31'),
(22, '111', '2222', '22@gmail.com', 'default-avatar.png', '2025-04-25 09:51:45'),
(23, '111', 'ppp', 'www@gmail.com', 'default-avatar.png', '2025-04-25 09:39:22'),
(24, '111', 'wendy', 'lai@gmail.com', 'default-avatar.png', '2025-04-25 09:41:16'),
(25, '111', 'ccc', 'aa@gmail.com', 'default-avatar.png', '2025-04-27 14:59:11'),
(26, 'ppp', 'ppp', 'ppp@gmail.com', 'default-avatar.png', '2025-05-01 14:30:46'),
(27, 'rrr', 'rrr', 'rrr@gmail.com', 'default-avatar.png', '2025-05-01 14:37:43');

-- --------------------------------------------------------

--
-- 資料表結構 `product`
--

CREATE TABLE `product` (
  `pid` int(11) NOT NULL,
  `mid` int(11) NOT NULL,
  `pName` varchar(255) DEFAULT NULL,
  `pDescription` varchar(255) DEFAULT NULL,
  `price` int(11) NOT NULL,
  `pPicture` varchar(255) DEFAULT NULL,
  `favoriteCount` int(10) UNSIGNED DEFAULT 0,
  `purchaseCount` int(10) UNSIGNED DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `product`
--

INSERT INTO `product` (`pid`, `mid`, `pName`, `pDescription`, `price`, `pPicture`, `favoriteCount`, `purchaseCount`) VALUES
(1, 1, 'newp1', 'pDescription1', 100, 'upload_images/1745978717-6811855db57f3.jpg', 0, 0),
(4, 1, 'p3', 'pDescription3', 300, 'upload_images/1745979207-68118747354f0.jpg', 0, 0),
(6, 1, 'p2', '無商品敘述', 200, 'upload_images/1745979371-681187eb168b1.jpg', 0, 0),
(7, 1, 'p4', '', 400, 'upload_images/1745979371-681187eb168b1.jpg', 0, 0),
(11, 1, 'j', 'j', 10, 'upload_images/1745979371-681187eb168b1.jpg', 0, 0),
(14, 1, 'jdjd', '', 100, 'upload_images/1745979371-681187eb168b1.jpg', 0, 0),
(16, 1, '山豬', '好吃', 100, 'upload_images/1745979371-681187eb168b1.jpg', 0, 0),
(17, 1, 'rxfr3', '', 1000, 'upload_images/1.jpg', 0, 0),
(18, 1, 'jenwdjd', '', 200, 'upload_images/2.jpg', 0, 0),
(19, 2, 'bh', 'v', 12, NULL, 0, 0);

-- --------------------------------------------------------

--
-- 資料表結構 `productcategories`
--

CREATE TABLE `productcategories` (
  `pid` int(11) NOT NULL,
  `productCategoriesId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `productcategories`
--

INSERT INTO `productcategories` (`pid`, `productCategoriesId`) VALUES
(1, 2),
(4, 2),
(6, 2),
(7, 2),
(11, 1),
(14, 2),
(16, 10),
(17, 11),
(18, 12),
(19, 23);

-- --------------------------------------------------------

--
-- 資料表結構 `productcategorylist`
--

CREATE TABLE `productcategorylist` (
  `productCategoriesId` int(11) NOT NULL,
  `productCategoryName` varchar(255) NOT NULL,
  `mid` int(11) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `productcategorylist`
--

INSERT INTO `productcategorylist` (`productCategoriesId`, `productCategoryName`, `mid`, `sort_order`) VALUES
(1, 'new category1', 1, 1),
(2, 'category2', 1, 2),
(10, 'rubyy', 1, 3),
(11, 'tttttttt', 1, 4),
(12, 'eeeeeeee', 1, 5),
(22, 'rbvrggf', 1, 6),
(23, 'm2ca1', 2, 1);

-- --------------------------------------------------------

--
-- 資料表結構 `record`
--

CREATE TABLE `record` (
  `tranId` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `cid` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `pRating` float DEFAULT NULL,
  `pComment` text DEFAULT NULL,
  `salePrice` int(11) GENERATED ALWAYS AS (`quantity` * `price`) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `record`
--

INSERT INTO `record` (`tranId`, `pid`, `cid`, `quantity`, `price`, `pRating`, `pComment`) VALUES
(4, 1, 1, 6, 24, 4.5, 'GOOOOOOODDDDDDDDDDD!!!!');

-- --------------------------------------------------------

--
-- 資料表結構 `refund`
--

CREATE TABLE `refund` (
  `cid` int(11) NOT NULL,
  `returnCode` int(11) NOT NULL,
  `cancelDate` date DEFAULT curdate(),
  `cancelTime` time DEFAULT curtime(),
  `refundDate` date DEFAULT NULL,
  `refundTime` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `restaurantcategories`
--

CREATE TABLE `restaurantcategories` (
  `id` int(11) NOT NULL,
  `mid` int(11) NOT NULL,
  `categoryId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `restaurantcategories`
--

INSERT INTO `restaurantcategories` (`id`, `mid`, `categoryId`) VALUES
(7, 8, 1),
(8, 2, 2),
(9, 1, 2),
(10, 1, 1);

-- --------------------------------------------------------

--
-- 資料表結構 `restaurantcategorylist`
--

CREATE TABLE `restaurantcategorylist` (
  `categoryId` int(11) NOT NULL,
  `categoryName` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `restaurantcategorylist`
--

INSERT INTO `restaurantcategorylist` (`categoryId`, `categoryName`) VALUES
(2, '珍珠奶茶'),
(1, '韓國美食');

-- --------------------------------------------------------

--
-- 資料表結構 `returnform`
--

CREATE TABLE `returnform` (
  `returnCode` int(11) NOT NULL,
  `cid` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `tranId` int(11) NOT NULL,
  `reasion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `transaction`
--

CREATE TABLE `transaction` (
  `tranId` int(11) NOT NULL,
  `cid` int(11) NOT NULL,
  `address_text` varchar(255) NOT NULL,
  `did` int(11) NOT NULL,
  `dRating` float DEFAULT NULL,
  `dComment` text DEFAULT NULL,
  `mid` int(11) NOT NULL,
  `mRating` float DEFAULT NULL,
  `mComment` text DEFAULT NULL,
  `number` int(10) NOT NULL,
  `transactionTime` date NOT NULL DEFAULT current_timestamp(),
  `paymentMethod` varchar(20) NOT NULL,
  `cardName` varchar(20) DEFAULT NULL,
  `totalPrice` int(20) NOT NULL,
  `tNote` varchar(255) DEFAULT NULL,
  `id` varchar(100) DEFAULT NULL,
  `orderStatus` varchar(20) NOT NULL DEFAULT 'new'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `transaction`
--

INSERT INTO `transaction` (`tranId`, `cid`, `address_text`, `did`, `dRating`, `dComment`, `mid`, `mRating`, `mComment`, `number`, `transactionTime`, `paymentMethod`, `cardName`, `totalPrice`, `tNote`, `id`, `orderStatus`) VALUES
(2, 2, '', 1, 1, 'Extremely disappointed. The delivery was late, and the driver was rude when I asked about the delay. My drink was missing and the bag looked crushed. Really poor service.', 1, 3, 'Food is delicious and arrives warm. I appreciate the neat packaging and separate sauces. Loved the vegan wrap and fries! Even included a thank-you note, which was a nice touch. Will definitely order again.', 3, '2025-04-23', 'walletBalance', 'card6', 864, NULL, NULL, 'new'),
(3, 3, '', 5, 5, 'The delivery driver was amazing—super friendly and arrived earlier than expected. My food was handled with care and nothing spilled. He even called to double-check the location. Great experience!', 7, 1, 'Very poor experience. The food was cold, one item was missing, and the packaging was careless. Definitely not worth the money. Won’t be ordering again.', 0, '2025-04-23', 'cardName', 'card5', 123, NULL, NULL, 'new'),
(4, 1, '', 3, 4, 'Delivery was smooth, but the driver didn’t follow the delivery instructions and left the food at the wrong entrance. Luckily, I found it quickly and the food was still warm.', 2, 4, 'Really tasty meal and great value. I especially liked the dumplings. Just one small issue — the drink was slightly spilled. Otherwise, everything was great!', 2, '2025-04-25', 'cashOnDelivery', 'cash', 559, NULL, NULL, 'new'),
(5, 1, '', 5, 3, 'The driver was on time, but there was no communication at all. They left the bag on the ground without knocking or texting. It was okay, but not very thoughtful.', 1, 3.5, 'The food was delicious and packaged really well. I ordered the curry chicken and it had great flavor. Delivery was on time, and the meal was still warm. Only downside was the rice portion being a bit small.', 5, '2025-04-25', 'walletBalance', 'balance', 508, NULL, NULL, 'new'),
(6, 1, '', 1, 2, 'Food arrived 20 minutes late and was already cold. The driver didn’t apologize or explain the delay. I understand traffic happens, but some communication would’ve helped.', 1, 5, 'First time ordering and everything was great. The beef noodles and dumplings were delicious. Fast delivery and everything was packed well. You can tell they care about quality. I’ll definitely order again!', 5, '2025-04-25', 'walletBalance', 'balance', 1020, NULL, NULL, 'new'),
(7, 1, '', 8, 0, 'The driver never showed up at my door. The app said \"delivered\" but I received nothing. Tried calling but no response. Had to contact support for a refund.', 1, 1, 'Totally disappointed. My order was wrong, and when I contacted the restaurant, they didn’t respond. The drink was missing, and the main dish wasn’t what I selected. Not ordering from here again.', 7, '2025-04-25', 'cardName', 'card4', 111, NULL, NULL, 'new'),
(8, 1, '', 4, 4, 'sususupernova', 6, 5, 'Absolutely amazing! The food was flavorful, fresh, and looked exactly like the photos. Everything was well-packed and arrived fast. One of the best delivery meals I’ve had lately!', 2, '2025-04-25', 'cardName', 'creditCard', 7878, NULL, NULL, 'new'),
(9, 1, '', 6, 3, 'Well', 3, 2, 'Food was okay, but the wait time was way too long—almost 90 minutes. When it arrived, it was barely warm. I might give them another try, but not during busy hours.', 7, '2025-04-25', 'cardName', 'card2', 90, NULL, NULL, 'new'),
(10, 1, '', 2, 2, 'The food was late by almost 30 minutes and the delivery person didn’t apologize. The bag was also slightly damaged, and the drink spilled a little. Not the best experience.', 2, 5, 'Excellent food and fast preparation! My order was ready quickly and arrived still hot. The portions were generous and everything tasted fresh. Will definitely order again.', 11, '2025-04-26', 'cardName', 'card11', 345657654, NULL, NULL, 'new'),
(11, 1, '', 3, 3, 'Delivery was on time, but the driver seemed confused about the address. He called twice for directions even though it was clearly written. Friendly attitude though, so not bad overall.', 4, 3, 'Decent food, but nothing special. The burger was just okay and the fries were a bit soggy. Not bad, but not something I’d rush to order again.', 23456, '2025-04-26', 'cardName', 'card111', 45, NULL, NULL, 'new'),
(12, 1, '', 4, 4, 'Very polite and quick delivery! The food was still warm and well-packed. Just wish the driver had followed the “leave at door” instruction instead of knocking loudly.', 5, 2, 'The food looked great in the photos, but the actual portion was small and lacked flavor. Also, my special request was ignored. Disappointed considering the price.', 23456, '2025-04-26', 'cardName', 'card111', 4523, NULL, NULL, 'new');

-- --------------------------------------------------------

--
-- 資料表結構 `wallet`
--

CREATE TABLE `wallet` (
  `cid` int(10) NOT NULL,
  `role` varchar(1) NOT NULL,
  `wid` varchar(10) NOT NULL,
  `balance` int(20) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `wallet`
--

INSERT INTO `wallet` (`cid`, `role`, `wid`, `balance`) VALUES
(1, '', '1', 998),
(9, '', '2', 210),
(14, '', '3', 5),
(15, 'c', '', 99999),
(18, 'c', '', 0);

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `allergenicingredient`
--
ALTER TABLE `allergenicingredient`
  ADD PRIMARY KEY (`allergenicid`,`allergenicIngrediant`);

--
-- 資料表索引 `allergy`
--
ALTER TABLE `allergy`
  ADD PRIMARY KEY (`cid`);

--
-- 資料表索引 `bank`
--
ALTER TABLE `bank`
  ADD PRIMARY KEY (`accountNumber`),
  ADD KEY `fk_bank_customer` (`cid`);

--
-- 資料表索引 `browse`
--
ALTER TABLE `browse`
  ADD KEY `pid` (`pid`),
  ADD KEY `idx_browse_cid` (`cid`);

--
-- 資料表索引 `caddress`
--
ALTER TABLE `caddress`
  ADD PRIMARY KEY (`address_id`),
  ADD KEY `cid` (`cid`);

--
-- 資料表索引 `card`
--
ALTER TABLE `card`
  ADD PRIMARY KEY (`cid`,`cardName`);

--
-- 資料表索引 `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cid`,`cartTime`);

--
-- 資料表索引 `cartitem`
--
ALTER TABLE `cartitem`
  ADD PRIMARY KEY (`cid`,`cartTime`,`pid`),
  ADD KEY `pid` (`pid`),
  ADD KEY `mid` (`mid`);

--
-- 資料表索引 `cbank`
--
ALTER TABLE `cbank`
  ADD KEY `fk_cid_cbank` (`cid`);

--
-- 資料表索引 `companyaccount`
--
ALTER TABLE `companyaccount`
  ADD PRIMARY KEY (`account`);

--
-- 資料表索引 `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_customerid` (`customerid`);

--
-- 資料表索引 `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`cid`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `gId` (`gId`),
  ADD KEY `introdecer` (`introducer`),
  ADD KEY `initiator` (`initiator`);

--
-- 資料表索引 `cwallet`
--
ALTER TABLE `cwallet`
  ADD KEY `fk_cid_cwallet` (`cid`);

--
-- 資料表索引 `dbank`
--
ALTER TABLE `dbank`
  ADD KEY `fk_did_dbank` (`did`);

--
-- 資料表索引 `deliveryperson`
--
ALTER TABLE `deliveryperson`
  ADD PRIMARY KEY (`did`);

--
-- 資料表索引 `dorders`
--
ALTER TABLE `dorders`
  ADD PRIMARY KEY (`tranId`),
  ADD KEY `did` (`did`),
  ADD KEY `cid` (`cid`);

--
-- 資料表索引 `dwallet`
--
ALTER TABLE `dwallet`
  ADD KEY `fk_dwallet_did` (`did`);

--
-- 資料表索引 `foodpreferences`
--
ALTER TABLE `foodpreferences`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cid` (`cid`);

--
-- 資料表索引 `mbank`
--
ALTER TABLE `mbank`
  ADD KEY `fk_mid_cbank` (`mid`);

--
-- 資料表索引 `merchant`
--
ALTER TABLE `merchant`
  ADD PRIMARY KEY (`mid`);

--
-- 資料表索引 `mwallet`
--
ALTER TABLE `mwallet`
  ADD KEY `fk_mwallet_mid` (`mid`);

--
-- 資料表索引 `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`cid`,`cartTime`,`pid`),
  ADD KEY `mid` (`mid`);

--
-- 資料表索引 `platform`
--
ALTER TABLE `platform`
  ADD PRIMARY KEY (`platId`);

--
-- 資料表索引 `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`pid`),
  ADD KEY `Products_ibfk_1` (`mid`);

--
-- 資料表索引 `productcategories`
--
ALTER TABLE `productcategories`
  ADD PRIMARY KEY (`pid`,`productCategoriesId`),
  ADD KEY `productcategories_ibfk_2` (`productCategoriesId`);

--
-- 資料表索引 `productcategorylist`
--
ALTER TABLE `productcategorylist`
  ADD PRIMARY KEY (`productCategoriesId`),
  ADD UNIQUE KEY `productCategoryName` (`productCategoryName`),
  ADD KEY `mid` (`mid`);

--
-- 資料表索引 `record`
--
ALTER TABLE `record`
  ADD PRIMARY KEY (`tranId`,`pid`,`cid`),
  ADD KEY `pid` (`pid`),
  ADD KEY `cid` (`cid`);

--
-- 資料表索引 `refund`
--
ALTER TABLE `refund`
  ADD PRIMARY KEY (`cid`,`returnCode`),
  ADD KEY `returnCode` (`returnCode`);

--
-- 資料表索引 `restaurantcategories`
--
ALTER TABLE `restaurantcategories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mid` (`mid`),
  ADD KEY `categoryId` (`categoryId`);

--
-- 資料表索引 `restaurantcategorylist`
--
ALTER TABLE `restaurantcategorylist`
  ADD PRIMARY KEY (`categoryId`),
  ADD UNIQUE KEY `categoryName` (`categoryName`);

--
-- 資料表索引 `returnform`
--
ALTER TABLE `returnform`
  ADD PRIMARY KEY (`returnCode`),
  ADD KEY `cid` (`cid`),
  ADD KEY `pid` (`pid`),
  ADD KEY `tranId` (`tranId`);

--
-- 資料表索引 `transaction`
--
ALTER TABLE `transaction`
  ADD PRIMARY KEY (`tranId`),
  ADD KEY `did` (`did`),
  ADD KEY `fk_merchant_customer` (`mid`),
  ADD KEY `fk_transaction_customer` (`cid`);

--
-- 資料表索引 `wallet`
--
ALTER TABLE `wallet`
  ADD PRIMARY KEY (`cid`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `caddress`
--
ALTER TABLE `caddress`
  MODIFY `address_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `customer`
--
ALTER TABLE `customer`
  MODIFY `cid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `deliveryperson`
--
ALTER TABLE `deliveryperson`
  MODIFY `did` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `foodpreferences`
--
ALTER TABLE `foodpreferences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `merchant`
--
ALTER TABLE `merchant`
  MODIFY `mid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `platform`
--
ALTER TABLE `platform`
  MODIFY `platId` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `returnform`
--
ALTER TABLE `returnform`
  MODIFY `returnCode` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `transaction`
--
ALTER TABLE `transaction`
  MODIFY `tranId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- 已傾印資料表的限制式
--

--
-- 資料表的限制式 `allergenicingredient`
--
ALTER TABLE `allergenicingredient`
  ADD CONSTRAINT `allergenicingredient_ibfk_1` FOREIGN KEY (`allergenicid`) REFERENCES `product` (`pid`);

--
-- 資料表的限制式 `bank`
--
ALTER TABLE `bank`
  ADD CONSTRAINT `fk_bank_customer` FOREIGN KEY (`cid`) REFERENCES `customer` (`cid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 資料表的限制式 `browse`
--
ALTER TABLE `browse`
  ADD CONSTRAINT `fk_browse_customer` FOREIGN KEY (`cid`) REFERENCES `customer` (`cid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pid` FOREIGN KEY (`pid`) REFERENCES `product` (`pid`);

--
-- 資料表的限制式 `caddress`
--
ALTER TABLE `caddress`
  ADD CONSTRAINT `fk_cid_customer` FOREIGN KEY (`cid`) REFERENCES `customer` (`cid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 資料表的限制式 `card`
--
ALTER TABLE `card`
  ADD CONSTRAINT `cid` FOREIGN KEY (`cid`) REFERENCES `customer` (`cid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 資料表的限制式 `cbank`
--
ALTER TABLE `cbank`
  ADD CONSTRAINT `fk_cid_cbank` FOREIGN KEY (`cid`) REFERENCES `customer` (`cid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 資料表的限制式 `cwallet`
--
ALTER TABLE `cwallet`
  ADD CONSTRAINT `fk_cid_cwallet` FOREIGN KEY (`cid`) REFERENCES `customer` (`cid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 資料表的限制式 `dbank`
--
ALTER TABLE `dbank`
  ADD CONSTRAINT `fk_did_dbank` FOREIGN KEY (`did`) REFERENCES `deliveryperson` (`did`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 資料表的限制式 `dorders`
--
ALTER TABLE `dorders`
  ADD CONSTRAINT `dorders_ibfk_1` FOREIGN KEY (`tranId`) REFERENCES `transaction` (`tranId`),
  ADD CONSTRAINT `dorders_ibfk_2` FOREIGN KEY (`did`) REFERENCES `deliveryperson` (`did`),
  ADD CONSTRAINT `dorders_ibfk_3` FOREIGN KEY (`cid`) REFERENCES `customer` (`cid`);

--
-- 資料表的限制式 `dwallet`
--
ALTER TABLE `dwallet`
  ADD CONSTRAINT `fk_did_dwallet` FOREIGN KEY (`did`) REFERENCES `deliveryperson` (`did`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_dwallet_did` FOREIGN KEY (`did`) REFERENCES `deliveryperson` (`did`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 資料表的限制式 `foodpreferences`
--
ALTER TABLE `foodpreferences`
  ADD CONSTRAINT `foodpreferences_ibfk_1` FOREIGN KEY (`cid`) REFERENCES `customer` (`cid`);

--
-- 資料表的限制式 `mbank`
--
ALTER TABLE `mbank`
  ADD CONSTRAINT `fk_mid_cbank` FOREIGN KEY (`mid`) REFERENCES `merchant` (`mid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 資料表的限制式 `mwallet`
--
ALTER TABLE `mwallet`
  ADD CONSTRAINT `fk_mid_mwallet` FOREIGN KEY (`mid`) REFERENCES `merchant` (`mid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_mwallet_mid` FOREIGN KEY (`mid`) REFERENCES `merchant` (`mid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 資料表的限制式 `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`cid`,`cartTime`,`pid`) REFERENCES `cartitem` (`cid`, `cartTime`, `pid`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`mid`) REFERENCES `merchant` (`mid`) ON DELETE CASCADE;

--
-- 資料表的限制式 `record`
--
ALTER TABLE `record`
  ADD CONSTRAINT `record_ibfk_1` FOREIGN KEY (`tranId`) REFERENCES `transaction` (`tranId`),
  ADD CONSTRAINT `record_ibfk_2` FOREIGN KEY (`pid`) REFERENCES `product` (`pid`),
  ADD CONSTRAINT `record_ibfk_3` FOREIGN KEY (`cid`) REFERENCES `customer` (`cid`);

--
-- 資料表的限制式 `refund`
--
ALTER TABLE `refund`
  ADD CONSTRAINT `refund_ibfk_1` FOREIGN KEY (`cid`) REFERENCES `customer` (`cid`),
  ADD CONSTRAINT `refund_ibfk_2` FOREIGN KEY (`returnCode`) REFERENCES `returnform` (`returnCode`);

--
-- 資料表的限制式 `returnform`
--
ALTER TABLE `returnform`
  ADD CONSTRAINT `returnform_ibfk_1` FOREIGN KEY (`cid`) REFERENCES `customer` (`cid`),
  ADD CONSTRAINT `returnform_ibfk_2` FOREIGN KEY (`pid`) REFERENCES `product` (`pid`),
  ADD CONSTRAINT `returnform_ibfk_3` FOREIGN KEY (`tranId`) REFERENCES `transaction` (`tranId`);

--
-- 資料表的限制式 `transaction`
--
ALTER TABLE `transaction`
  ADD CONSTRAINT `fk_did_deliveryperson` FOREIGN KEY (`did`) REFERENCES `deliveryperson` (`did`),
  ADD CONSTRAINT `fk_mid_merchant` FOREIGN KEY (`mid`) REFERENCES `merchant` (`mid`),
  ADD CONSTRAINT `fk_transaction_customer` FOREIGN KEY (`cid`) REFERENCES `customer` (`cid`);

--
-- 資料表的限制式 `wallet`
--
ALTER TABLE `wallet`
  ADD CONSTRAINT `fk_wallet_cid` FOREIGN KEY (`cid`) REFERENCES `customer` (`cid`),
  ADD CONSTRAINT `fk_wallet_customer` FOREIGN KEY (`cid`) REFERENCES `customer` (`cid`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
