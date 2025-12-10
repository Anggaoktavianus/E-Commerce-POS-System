# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 5.7.39)
# Database: db_samsae
# Generation Time: 2025-12-01 08:43:05 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table banners
# ------------------------------------------------------------

DROP TABLE IF EXISTS `banners`;

CREATE TABLE `banners` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subtitle` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `button_text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `button_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'home_middle',
  `sort_order` int(10) unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `banners` WRITE;
/*!40000 ALTER TABLE `banners` DISABLE KEYS */;

INSERT INTO `banners` (`id`, `title`, `subtitle`, `button_text`, `button_url`, `image_path`, `position`, `sort_order`, `is_active`, `created_at`, `updated_at`)
VALUES
	(1,'Fresh Fruits 50% Off','Limited Time Offer','Shop Fruits','http://127.0.0.1:8000/shop','storage/uploads/banners/ALFfB7I0SZX41ldFOTXHFnbjnWKTK2lZZWiFSGNe.png','home_top',2,1,'2025-11-24 07:00:58','2025-11-27 06:48:19'),
	(2,'Bakpia Coklat Mini Pack','Bakpia Coklat Mini Pack','Bakpia Coklat Mini Pack','http://127.0.0.1:8000/shop','storage/uploads/banners/ALFfB7I0SZX41ldFOTXHFnbjnWKTK2lZZWiFSGNe.png','home_top',1,1,'2025-11-24 07:00:58','2025-11-27 06:26:26'),
	(3,'Bakpia Coklat Mini Pack','Bakpia Coklat Mini Pack','Bakpia Coklat Mini Pack','http://127.0.0.1:8000/shop','storage/uploads/banners/ALFfB7I0SZX41ldFOTXHFnbjnWKTK2lZZWiFSGNe.png','home_middle',1,1,'2025-11-24 07:00:58','2025-11-27 06:26:26'),
	(4,'Bakpia Coklat Mini Pack','Bakpia Coklat Mini Pack','Bakpia Coklat Mini Pack','http://127.0.0.1:8000/shop','storage/uploads/banners/ALFfB7I0SZX41ldFOTXHFnbjnWKTK2lZZWiFSGNe.png','home_middle',1,1,'2025-11-24 07:00:58','2025-11-27 06:26:26'),
	(5,'Bakpia Coklat Mini Pack','Bakpia Coklat Mini Pack','Bakpia Coklat Mini Pack','http://127.0.0.1:8000/shop','storage/uploads/banners/ALFfB7I0SZX41ldFOTXHFnbjnWKTK2lZZWiFSGNe.png','home_middle',1,1,'2025-11-24 07:00:58','2025-11-27 06:26:26'),
	(6,'Bakpia Coklat Mini Pack','Bakpia Coklat Mini Pack','Bakpia Coklat Mini Pack','http://127.0.0.1:8000/shop','storage/uploads/banners/ALFfB7I0SZX41ldFOTXHFnbjnWKTK2lZZWiFSGNe.png','home_middle',1,1,'2025-11-24 07:00:58','2025-11-27 06:26:26');

/*!40000 ALTER TABLE `banners` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table cache
# ------------------------------------------------------------

DROP TABLE IF EXISTS `cache`;

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;

INSERT INTO `cache` (`key`, `value`, `expiration`)
VALUES
	('samsae-store-cache-home.bestseller','O:29:\"Illuminate\\Support\\Collection\":2:{s:8:\"\0*\0items\";a:5:{i:0;O:8:\"stdClass\":17:{s:2:\"id\";i:2;s:4:\"name\";s:27:\"Bakpia Coklat Traveler Pack\";s:4:\"slug\";s:27:\"Bakpia Coklat Traveler Pack\";s:3:\"sku\";N;s:17:\"short_description\";s:27:\"Bakpia Coklat Traveler Pack\";s:11:\"description\";s:27:\"Bakpia Coklat Traveler Pack\";s:5:\"price\";s:8:\"40000.00\";s:16:\"compare_at_price\";N;s:9:\"stock_qty\";i:150;s:4:\"unit\";s:3:\"pcs\";s:15:\"main_image_path\";s:61:\"uploads/products/pe63VFglD3gZOQR3SrffZv7btqK4w5FkKR2Mt9bo.png\";s:9:\"is_active\";i:1;s:11:\"is_featured\";i:0;s:13:\"is_bestseller\";i:1;s:10:\"created_at\";s:19:\"2025-11-24 07:00:58\";s:10:\"updated_at\";s:19:\"2025-11-26 08:34:23\";s:10:\"sort_order\";i:2;}i:1;O:8:\"stdClass\":17:{s:2:\"id\";i:3;s:4:\"name\";s:33:\"Bakpia Kacang Hijau Traveler Pack\";s:4:\"slug\";s:33:\"Bakpia Kacang Hijau Traveler Pack\";s:3:\"sku\";N;s:17:\"short_description\";s:33:\"Bakpia Kacang Hijau Traveler Pack\";s:11:\"description\";s:33:\"Bakpia Kacang Hijau Traveler Pack\";s:5:\"price\";s:8:\"40000.00\";s:16:\"compare_at_price\";N;s:9:\"stock_qty\";i:200;s:4:\"unit\";s:3:\"pcs\";s:15:\"main_image_path\";s:61:\"uploads/products/QRD43csYSjIxpB8fXJXo4uIGi3OZ2SokKJGZU17c.png\";s:9:\"is_active\";i:1;s:11:\"is_featured\";i:0;s:13:\"is_bestseller\";i:0;s:10:\"created_at\";s:19:\"2025-11-24 07:00:58\";s:10:\"updated_at\";s:19:\"2025-11-26 08:35:29\";s:10:\"sort_order\";i:3;}i:2;O:8:\"stdClass\":17:{s:2:\"id\";i:4;s:4:\"name\";s:29:\"Bakpia Kacang Hijau Mini Pack\";s:4:\"slug\";s:29:\"Bakpia Kacang Hijau Mini Pack\";s:3:\"sku\";N;s:17:\"short_description\";s:29:\"Bakpia Kacang Hijau Mini Pack\";s:11:\"description\";s:29:\"Bakpia Kacang Hijau Mini Pack\";s:5:\"price\";s:8:\"30000.00\";s:16:\"compare_at_price\";N;s:9:\"stock_qty\";i:120;s:4:\"unit\";s:3:\"pcs\";s:15:\"main_image_path\";s:61:\"uploads/products/vq6W4JgdqF3RlvbPVrzk9KB51pnm1UTSWWJSUCUK.png\";s:9:\"is_active\";i:1;s:11:\"is_featured\";i:1;s:13:\"is_bestseller\";i:1;s:10:\"created_at\";s:19:\"2025-11-24 07:00:58\";s:10:\"updated_at\";s:19:\"2025-11-26 08:36:12\";s:10:\"sort_order\";i:4;}i:3;O:8:\"stdClass\":17:{s:2:\"id\";i:5;s:4:\"name\";s:26:\"Bakpia Coklat Regular Pack\";s:4:\"slug\";s:26:\"Bakpia Coklat Regular Pack\";s:3:\"sku\";N;s:17:\"short_description\";N;s:11:\"description\";N;s:5:\"price\";s:8:\"40000.00\";s:16:\"compare_at_price\";N;s:9:\"stock_qty\";i:180;s:4:\"unit\";s:3:\"pcs\";s:15:\"main_image_path\";s:61:\"uploads/products/f29Hnh5Hf5fKKk5E3404hfmJCflScR401GpSQvU9.png\";s:9:\"is_active\";i:1;s:11:\"is_featured\";i:1;s:13:\"is_bestseller\";i:0;s:10:\"created_at\";s:19:\"2025-11-24 07:00:58\";s:10:\"updated_at\";s:19:\"2025-11-26 08:54:33\";s:10:\"sort_order\";i:5;}i:4;O:8:\"stdClass\":17:{s:2:\"id\";i:6;s:4:\"name\";s:23:\"Bakpia Coklat Mini Pack\";s:4:\"slug\";s:23:\"Bakpia Coklat Mini Pack\";s:3:\"sku\";N;s:17:\"short_description\";N;s:11:\"description\";N;s:5:\"price\";s:8:\"35000.00\";s:16:\"compare_at_price\";N;s:9:\"stock_qty\";i:160;s:4:\"unit\";s:3:\"pcs\";s:15:\"main_image_path\";s:61:\"uploads/products/K7mFR3BRj4tG5lOxCO5PXWwMcLJ4V6IPiDAe6vZi.png\";s:9:\"is_active\";i:1;s:11:\"is_featured\";i:1;s:13:\"is_bestseller\";i:0;s:10:\"created_at\";s:19:\"2025-11-24 07:00:58\";s:10:\"updated_at\";s:19:\"2025-11-26 08:33:29\";s:10:\"sort_order\";i:6;}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}',1764578678),
	('samsae-store-cache-home.categories','O:29:\"Illuminate\\Support\\Collection\":2:{s:8:\"\0*\0items\";a:2:{i:0;O:8:\"stdClass\":7:{s:2:\"id\";i:1;s:4:\"name\";s:6:\"Bakpia\";s:4:\"slug\";s:6:\"bakpia\";s:9:\"parent_id\";N;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2025-11-24 07:00:58\";s:10:\"updated_at\";s:19:\"2025-11-24 08:28:01\";}i:1;O:8:\"stdClass\":7:{s:2:\"id\";i:2;s:4:\"name\";s:4:\"Bolu\";s:4:\"slug\";s:4:\"bolu\";s:9:\"parent_id\";N;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2025-11-24 07:00:58\";s:10:\"updated_at\";s:19:\"2025-11-24 08:27:49\";}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}',1764578678),
	('samsae-store-cache-home.facts','O:29:\"Illuminate\\Support\\Collection\":2:{s:8:\"\0*\0items\";a:4:{i:0;O:8:\"stdClass\":9:{s:2:\"id\";i:1;s:5:\"label\";s:15:\"Happy Customers\";s:5:\"value\";i:1234;s:10:\"icon_class\";s:11:\"bx bx-smile\";s:10:\"image_path\";N;s:10:\"sort_order\";i:1;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2025-11-24 07:00:58\";s:10:\"updated_at\";s:19:\"2025-11-24 07:00:58\";}i:1;O:8:\"stdClass\":9:{s:2:\"id\";i:2;s:5:\"label\";s:16:\"Quality Products\";s:5:\"value\";i:245;s:10:\"icon_class\";s:17:\"bx bx-badge-check\";s:10:\"image_path\";N;s:10:\"sort_order\";i:2;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2025-11-24 07:00:58\";s:10:\"updated_at\";s:19:\"2025-11-24 07:00:58\";}i:2;O:8:\"stdClass\":9:{s:2:\"id\";i:3;s:5:\"label\";s:13:\"Orders Served\";s:5:\"value\";i:5821;s:10:\"icon_class\";s:10:\"bx bx-cart\";s:10:\"image_path\";N;s:10:\"sort_order\";i:3;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2025-11-24 07:00:58\";s:10:\"updated_at\";s:19:\"2025-11-24 07:00:58\";}i:3;O:8:\"stdClass\":9:{s:2:\"id\";i:4;s:5:\"label\";s:6:\"Awards\";s:5:\"value\";i:12;s:10:\"icon_class\";s:11:\"bx bx-award\";s:10:\"image_path\";N;s:10:\"sort_order\";i:4;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2025-11-24 07:00:58\";s:10:\"updated_at\";s:19:\"2025-11-24 07:00:58\";}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}',1764578678),
	('samsae-store-cache-home.features','O:29:\"Illuminate\\Support\\Collection\":2:{s:8:\"\0*\0items\";a:4:{i:0;O:8:\"stdClass\":9:{s:2:\"id\";i:1;s:5:\"title\";s:13:\"Free Shipping\";s:11:\"description\";s:22:\"On all orders over $50\";s:10:\"icon_class\";s:20:\"fas fa-shipping-fast\";s:10:\"image_path\";N;s:10:\"sort_order\";i:1;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2025-11-24 07:00:58\";s:10:\"updated_at\";s:19:\"2025-11-24 07:00:58\";}i:1;O:8:\"stdClass\":9:{s:2:\"id\";i:2;s:5:\"title\";s:12:\"Always Fresh\";s:11:\"description\";s:20:\"Product well package\";s:10:\"icon_class\";s:11:\"fas fa-leaf\";s:10:\"image_path\";N;s:10:\"sort_order\";i:2;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2025-11-24 07:00:58\";s:10:\"updated_at\";s:19:\"2025-11-24 07:00:58\";}i:2;O:8:\"stdClass\":9:{s:2:\"id\";i:3;s:5:\"title\";s:16:\"Superior Quality\";s:11:\"description\";s:16:\"Quality Products\";s:10:\"icon_class\";s:12:\"fas fa-award\";s:10:\"image_path\";N;s:10:\"sort_order\";i:3;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2025-11-24 07:00:58\";s:10:\"updated_at\";s:19:\"2025-11-24 07:00:58\";}i:3;O:8:\"stdClass\":9:{s:2:\"id\";i:4;s:5:\"title\";s:7:\"Support\";s:11:\"description\";s:12:\"24/7 support\";s:10:\"icon_class\";s:14:\"fas fa-headset\";s:10:\"image_path\";N;s:10:\"sort_order\";i:4;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2025-11-24 07:00:58\";s:10:\"updated_at\";s:19:\"2025-11-24 07:00:58\";}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}',1764578678),
	('samsae-store-cache-home.footer_menus','O:29:\"Illuminate\\Support\\Collection\":2:{s:8:\"\0*\0items\";a:2:{i:0;O:8:\"stdClass\":7:{s:2:\"id\";i:8;s:4:\"name\";s:12:\"Tentang Kami\";s:8:\"location\";s:15:\"footer_column_1\";s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2025-11-26 05:26:11\";s:10:\"updated_at\";s:19:\"2025-11-27 07:53:05\";s:5:\"links\";O:29:\"Illuminate\\Support\\Collection\":2:{s:8:\"\0*\0items\";a:2:{i:0;O:8:\"stdClass\":13:{s:2:\"id\";i:26;s:18:\"navigation_menu_id\";i:8;s:9:\"parent_id\";N;s:5:\"label\";s:14:\"Tentang Samsae\";s:3:\"url\";N;s:10:\"route_name\";N;s:7:\"page_id\";i:2;s:6:\"target\";s:5:\"_self\";s:10:\"sort_order\";i:1;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2025-11-26 05:26:11\";s:10:\"updated_at\";s:19:\"2025-11-26 05:26:11\";s:9:\"page_slug\";s:12:\"tentang-kami\";}i:1;O:8:\"stdClass\":13:{s:2:\"id\";i:27;s:18:\"navigation_menu_id\";i:8;s:9:\"parent_id\";N;s:5:\"label\";s:3:\"FAQ\";s:3:\"url\";N;s:10:\"route_name\";N;s:7:\"page_id\";i:4;s:6:\"target\";s:5:\"_self\";s:10:\"sort_order\";i:2;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2025-11-26 05:26:11\";s:10:\"updated_at\";s:19:\"2025-11-26 05:26:11\";s:9:\"page_slug\";s:3:\"faq\";}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}i:1;O:8:\"stdClass\":7:{s:2:\"id\";i:9;s:4:\"name\";s:10:\"Store Kami\";s:8:\"location\";s:15:\"footer_column_2\";s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2025-11-28 04:47:08\";s:10:\"updated_at\";s:19:\"2025-11-28 04:48:21\";s:5:\"links\";O:29:\"Illuminate\\Support\\Collection\":2:{s:8:\"\0*\0items\";a:1:{i:0;O:8:\"stdClass\":13:{s:2:\"id\";i:30;s:18:\"navigation_menu_id\";i:9;s:9:\"parent_id\";N;s:5:\"label\";s:15:\"Store Majapahit\";s:3:\"url\";N;s:10:\"route_name\";N;s:7:\"page_id\";i:6;s:6:\"target\";s:5:\"_self\";s:10:\"sort_order\";i:1;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2025-11-28 04:47:31\";s:10:\"updated_at\";s:19:\"2025-11-28 04:47:57\";s:9:\"page_slug\";s:15:\"store-majapahit\";}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}',1764578678),
	('samsae-store-cache-home.header_links','O:29:\"Illuminate\\Support\\Collection\":2:{s:8:\"\0*\0items\";a:5:{i:0;O:8:\"stdClass\":14:{s:2:\"id\";i:20;s:18:\"navigation_menu_id\";i:7;s:9:\"parent_id\";N;s:5:\"label\";s:7:\"Beranda\";s:3:\"url\";s:1:\"/\";s:10:\"route_name\";N;s:7:\"page_id\";N;s:6:\"target\";s:5:\"_self\";s:10:\"sort_order\";i:1;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2025-11-26 05:26:11\";s:10:\"updated_at\";s:19:\"2025-11-26 05:26:11\";s:9:\"page_slug\";N;s:8:\"children\";O:29:\"Illuminate\\Support\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}i:1;O:8:\"stdClass\":14:{s:2:\"id\";i:21;s:18:\"navigation_menu_id\";i:7;s:9:\"parent_id\";N;s:5:\"label\";s:6:\"Produk\";s:3:\"url\";N;s:10:\"route_name\";s:4:\"shop\";s:7:\"page_id\";N;s:6:\"target\";s:5:\"_self\";s:10:\"sort_order\";i:2;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2025-11-26 05:26:11\";s:10:\"updated_at\";s:19:\"2025-11-26 05:26:11\";s:9:\"page_slug\";N;s:8:\"children\";O:29:\"Illuminate\\Support\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}i:2;O:8:\"stdClass\":14:{s:2:\"id\";i:22;s:18:\"navigation_menu_id\";i:7;s:9:\"parent_id\";N;s:5:\"label\";s:7:\"Tentang\";s:3:\"url\";N;s:10:\"route_name\";N;s:7:\"page_id\";N;s:6:\"target\";s:5:\"_self\";s:10:\"sort_order\";i:3;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2025-11-26 05:26:11\";s:10:\"updated_at\";s:19:\"2025-11-26 05:26:11\";s:9:\"page_slug\";N;s:8:\"children\";O:29:\"Illuminate\\Support\\Collection\":2:{s:8:\"\0*\0items\";a:2:{i:0;O:8:\"stdClass\":14:{s:2:\"id\";i:23;s:18:\"navigation_menu_id\";i:7;s:9:\"parent_id\";i:22;s:5:\"label\";s:12:\"Tentang Kami\";s:3:\"url\";N;s:10:\"route_name\";N;s:7:\"page_id\";i:2;s:6:\"target\";s:5:\"_self\";s:10:\"sort_order\";i:1;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2025-11-26 05:26:11\";s:10:\"updated_at\";s:19:\"2025-11-26 05:26:11\";s:9:\"page_slug\";s:12:\"tentang-kami\";s:8:\"children\";O:29:\"Illuminate\\Support\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}i:1;O:8:\"stdClass\":14:{s:2:\"id\";i:24;s:18:\"navigation_menu_id\";i:7;s:9:\"parent_id\";i:22;s:5:\"label\";s:3:\"FAQ\";s:3:\"url\";N;s:10:\"route_name\";N;s:7:\"page_id\";i:4;s:6:\"target\";s:5:\"_self\";s:10:\"sort_order\";i:2;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2025-11-26 05:26:11\";s:10:\"updated_at\";s:19:\"2025-11-26 05:26:11\";s:9:\"page_slug\";s:3:\"faq\";s:8:\"children\";O:29:\"Illuminate\\Support\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}i:3;O:8:\"stdClass\":14:{s:2:\"id\";i:25;s:18:\"navigation_menu_id\";i:7;s:9:\"parent_id\";N;s:5:\"label\";s:6:\"Kontak\";s:3:\"url\";N;s:10:\"route_name\";N;s:7:\"page_id\";i:3;s:6:\"target\";s:5:\"_self\";s:10:\"sort_order\";i:4;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2025-11-26 05:26:11\";s:10:\"updated_at\";s:19:\"2025-11-26 05:26:11\";s:9:\"page_slug\";s:6:\"kontak\";s:8:\"children\";O:29:\"Illuminate\\Support\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}i:4;O:8:\"stdClass\":14:{s:2:\"id\";i:28;s:18:\"navigation_menu_id\";i:7;s:9:\"parent_id\";N;s:5:\"label\";s:6:\"Outlet\";s:3:\"url\";N;s:10:\"route_name\";N;s:7:\"page_id\";N;s:6:\"target\";s:5:\"_self\";s:10:\"sort_order\";i:4;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2025-11-27 07:55:49\";s:10:\"updated_at\";s:19:\"2025-11-27 07:55:49\";s:9:\"page_slug\";N;s:8:\"children\";O:29:\"Illuminate\\Support\\Collection\":2:{s:8:\"\0*\0items\";a:2:{i:0;O:8:\"stdClass\":14:{s:2:\"id\";i:29;s:18:\"navigation_menu_id\";i:7;s:9:\"parent_id\";i:28;s:5:\"label\";s:9:\"Gayamsari\";s:3:\"url\";N;s:10:\"route_name\";N;s:7:\"page_id\";i:6;s:6:\"target\";s:5:\"_self\";s:10:\"sort_order\";i:0;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2025-11-27 07:56:14\";s:10:\"updated_at\";s:19:\"2025-11-27 07:56:14\";s:9:\"page_slug\";s:15:\"store-majapahit\";s:8:\"children\";O:29:\"Illuminate\\Support\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}i:1;O:8:\"stdClass\":14:{s:2:\"id\";i:31;s:18:\"navigation_menu_id\";i:7;s:9:\"parent_id\";i:28;s:5:\"label\";s:10:\"Jatingaleh\";s:3:\"url\";N;s:10:\"route_name\";N;s:7:\"page_id\";i:7;s:6:\"target\";s:5:\"_self\";s:10:\"sort_order\";i:2;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2025-11-28 08:33:05\";s:10:\"updated_at\";s:19:\"2025-11-28 08:33:05\";s:9:\"page_slug\";s:49:\"store-bakpia-kukus-tugu-jogja-jatingaleh-semarang\";s:8:\"children\";O:29:\"Illuminate\\Support\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}',1764578678),
	('samsae-store-cache-home.header_menu','O:8:\"stdClass\":6:{s:2:\"id\";i:7;s:4:\"name\";s:6:\"Header\";s:8:\"location\";s:6:\"header\";s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2025-11-26 05:26:11\";s:10:\"updated_at\";s:19:\"2025-11-26 05:26:11\";}',1764578678),
	('samsae-store-cache-home.settings','O:29:\"Illuminate\\Support\\Collection\":2:{s:8:\"\0*\0items\";a:95:{s:13:\"brand_tagline\";s:9:\"Oleh-Oleh\";s:7:\"address\";s:23:\"1429 Netus Rd, NY 48247\";s:5:\"phone\";s:15:\"+0123 4567 8910\";s:5:\"email\";s:15:\"info@samsae.com\";s:15:\"newsletter_text\";s:22:\"Get updates and offers\";s:18:\"payment_image_path\";s:26:\"fruitables/img/payment.png\";s:10:\"brand_name\";s:12:\"Samsae Store\";s:7:\"hero_bg\";s:69:\"storage/uploads/settings/Q6L92jFqs4FHkCOk4Z8ZrCXOZS2nekSAfmouN0FO.png\";s:17:\"footer_about_text\";s:133:\"typesetting, remaining essentially unchanged. It was popularised in the 1960s with the like Aldus PageMaker including of Lorem Ipsum.\";s:18:\"footer_about_title\";s:19:\"Why People Like us!\";s:21:\"footer_about_link_url\";s:6:\"/about\";s:9:\"site_logo\";s:69:\"storage/uploads/settings/pA4eU0uxj49qQm9buxJyHQVoP8u8u6MyfjDbSNic.png\";s:14:\"site_name_logo\";N;s:10:\"site_title\";s:6:\"Samsae\";s:16:\"site_description\";s:34:\"Samsae - Your trusted online store\";s:23:\"homepage_products_title\";s:12:\"Our Products\";s:26:\"homepage_products_subtitle\";s:43:\"Browse our wide selection of fresh products\";s:25:\"homepage_vegetables_title\";s:24:\"Fresh Organic Vegetables\";s:28:\"homepage_vegetables_subtitle\";s:34:\"Premium quality organic vegetables\";s:25:\"homepage_bestseller_title\";s:19:\"Bestseller Products\";s:28:\"homepage_bestseller_subtitle\";s:45:\"Most popular products chosen by our customers\";s:26:\"homepage_testimonial_title\";s:15:\"Our Testimonial\";s:29:\"homepage_testimonial_subtitle\";s:18:\"Our Client Saying!\";s:18:\"search_placeholder\";s:22:\"Search for products...\";s:18:\"search_button_text\";s:6:\"Search\";s:21:\"subscribe_placeholder\";s:10:\"Your Email\";s:21:\"subscribe_button_text\";s:13:\"Subscribe Now\";s:22:\"footer_about_link_text\";s:9:\"Read More\";s:14:\"copyright_text\";s:12:\"Samsae Store\";s:14:\"copyright_year\";s:4:\"2024\";s:22:\"copyright_designer_url\";s:21:\"https://htmlcodex.com\";s:26:\"copyright_distributor_text\";s:26:\"Distributed By Samsaestore\";s:25:\"copyright_distributor_url\";s:22:\"https://themewagon.com\";s:21:\"contact_section_title\";s:7:\"Contact\";s:21:\"contact_payment_title\";s:16:\"Payment Accepted\";s:19:\"footer_menu_columns\";s:1:\"2\";s:20:\"footer_menu_show_all\";s:4:\"true\";s:15:\"currency_symbol\";s:3:\"Rp.\";s:27:\"product_default_description\";s:26:\"Fresh product from Samsae.\";s:18:\"product_badge_text\";s:7:\"Product\";s:16:\"add_to_cart_text\";s:11:\"Add to cart\";s:20:\"quantity_placeholder\";s:3:\"Qty\";s:20:\"product_detail_title\";s:11:\"Shop Detail\";s:22:\"product_category_label\";s:8:\"Category\";s:23:\"product_description_tab\";s:11:\"Description\";s:19:\"product_reviews_tab\";s:7:\"Reviews\";s:11:\"price_label\";s:5:\"Harga\";s:10:\"unit_label\";s:4:\"Unit\";s:11:\"stock_label\";s:5:\"Stock\";s:10:\"size_label\";s:4:\"Size\";s:11:\"color_label\";s:5:\"Color\";s:12:\"weight_label\";s:6:\"Weight\";s:20:\"breadcrumb_home_text\";s:4:\"Home\";s:21:\"breadcrumb_pages_text\";s:5:\"Pages\";s:27:\"breadcrumb_shop_detail_text\";s:11:\"Shop Detail\";s:23:\"breadcrumb_contact_text\";s:7:\"Contact\";s:18:\"contact_page_title\";s:7:\"Contact\";s:18:\"contact_form_title\";s:12:\"Get in touch\";s:24:\"contact_form_description\";s:181:\"The contact form is currently inactive. Get a functional and working contact form with Ajax & PHP in a few minutes. Just copy and paste the files, add a little code and you\'re done.\";s:22:\"contact_form_link_text\";s:12:\"Download Now\";s:21:\"contact_form_link_url\";s:34:\"https://htmlcodex.com/contact-form\";s:22:\"no_products_found_text\";s:18:\"No products found.\";s:25:\"no_category_products_text\";s:14:\"No products in\";s:24:\"contact_name_placeholder\";s:9:\"Your Name\";s:25:\"contact_email_placeholder\";s:16:\"Enter Your Email\";s:27:\"contact_message_placeholder\";s:12:\"Your Message\";s:21:\"contact_submit_button\";s:6:\"Submit\";s:21:\"contact_address_title\";s:7:\"Address\";s:19:\"contact_email_title\";s:7:\"Mail Us\";s:19:\"contact_phone_title\";s:9:\"Telephone\";s:21:\"mitra_dashboard_title\";s:15:\"Dashboard Mitra\";s:21:\"mitra_welcome_message\";s:14:\"Selamat datang\";s:24:\"mitra_dashboard_subtitle\";s:44:\"Berikut ringkasan aktivitas akun mitra Anda.\";s:26:\"mitra_account_status_title\";s:11:\"Status Akun\";s:19:\"mitra_verified_text\";s:13:\"Terverifikasi\";s:26:\"mitra_verified_description\";s:73:\"Akun Anda sudah diverifikasi admin. Anda bisa melakukan pemesanan grosir.\";s:18:\"mitra_pending_text\";s:19:\"Menunggu Verifikasi\";s:25:\"mitra_pending_description\";s:80:\"Akun Anda sedang menunggu verifikasi admin sebelum dapat mengakses harga grosir.\";s:18:\"mitra_orders_title\";s:23:\"Pesanan Besar Bulan Ini\";s:24:\"mitra_orders_description\";s:56:\"Integrasi dengan data pesanan akan ditambahkan kemudian.\";s:24:\"mitra_sales_target_title\";s:16:\"Target Penjualan\";s:18:\"mitra_target_label\";s:16:\"Target bulan ini\";s:19:\"mitra_no_target_set\";s:16:\"Belum ditetapkan\";s:24:\"mitra_target_description\";s:84:\"Fitur kuota/target penjualan akan dihubungkan ke data penjualan di tahap berikutnya.\";s:25:\"mitra_order_history_title\";s:17:\"Riwayat Pemesanan\";s:31:\"mitra_order_history_description\";s:92:\"Tabel riwayat pemesanan mitra akan ditampilkan di sini setelah modul pesanan selesai dibuat.\";s:13:\"nav_home_text\";s:7:\"Beranda\";s:24:\"nav_admin_dashboard_text\";s:15:\"Dashboard Admin\";s:24:\"nav_mitra_dashboard_text\";s:15:\"Dashboard Mitra\";s:15:\"nav_logout_text\";s:6:\"Logout\";s:14:\"nav_login_text\";s:5:\"Login\";s:17:\"nav_register_text\";s:6:\"Daftar\";s:23:\"nav_mitra_register_text\";s:12:\"Daftar Mitra\";s:9:\"site_name\";s:12:\"Samsae Store\";s:23:\"copyright_designer_text\";s:22:\"Designed By HTML Codex\";}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}',1764578678),
	('samsae-store-cache-home.slides.home_hero','O:29:\"Illuminate\\Support\\Collection\":2:{s:8:\"\0*\0items\";a:3:{i:0;O:8:\"stdClass\":12:{s:2:\"id\";i:3;s:11:\"carousel_id\";i:1;s:5:\"title\";s:23:\"Bakpia Coklat Mini Pack\";s:8:\"subtitle\";s:23:\"Bakpia Coklat Mini Pack\";s:11:\"button_text\";s:4:\"Shop\";s:10:\"button_url\";s:26:\"http://127.0.0.1:8000/shop\";s:10:\"image_path\";s:67:\"storage/uploads/slides/57RVOlxdA4elyT3mMWmR27argueYJKXuwWG0LngG.png\";s:17:\"mobile_image_path\";N;s:10:\"sort_order\";i:1;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2025-11-24 07:00:58\";s:10:\"updated_at\";s:19:\"2025-11-27 06:32:28\";}i:1;O:8:\"stdClass\":12:{s:2:\"id\";i:2;s:11:\"carousel_id\";i:1;s:5:\"title\";s:27:\"Bakpia Coklat Traveler Pack\";s:8:\"subtitle\";s:27:\"Bakpia Coklat Traveler Pack\";s:11:\"button_text\";s:4:\"Shop\";s:10:\"button_url\";s:26:\"http://127.0.0.1:8000/shop\";s:10:\"image_path\";s:67:\"storage/uploads/slides/BPf6Bsn5qQwtlATlzaxo99NOaLwDEVpvW6dtueEV.png\";s:17:\"mobile_image_path\";N;s:10:\"sort_order\";i:2;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2025-11-24 07:00:58\";s:10:\"updated_at\";s:19:\"2025-11-27 06:33:55\";}i:2;O:8:\"stdClass\":12:{s:2:\"id\";i:1;s:11:\"carousel_id\";i:1;s:5:\"title\";s:33:\"Bakpia Kacang Hijau Traveler Pack\";s:8:\"subtitle\";s:33:\"Bakpia Kacang Hijau Traveler Pack\";s:11:\"button_text\";s:4:\"Shop\";s:10:\"button_url\";s:26:\"http://127.0.0.1:8000/shop\";s:10:\"image_path\";s:67:\"storage/uploads/slides/hILoTNC5sHb61XXmxkMCT96Y8ttO0YCzL8sypanx.png\";s:17:\"mobile_image_path\";N;s:10:\"sort_order\";i:3;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2025-11-24 07:00:58\";s:10:\"updated_at\";s:19:\"2025-11-27 06:34:45\";}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}',1764578678),
	('samsae-store-cache-home.social_links','O:29:\"Illuminate\\Support\\Collection\":2:{s:8:\"\0*\0items\";a:4:{i:0;O:8:\"stdClass\":8:{s:2:\"id\";i:1;s:8:\"platform\";s:7:\"twitter\";s:3:\"url\";s:20:\"https://twitter.com/\";s:10:\"icon_class\";s:14:\"fab fa-twitter\";s:10:\"sort_order\";i:1;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2025-11-24 07:00:58\";s:10:\"updated_at\";s:19:\"2025-11-24 07:00:58\";}i:1;O:8:\"stdClass\":8:{s:2:\"id\";i:2;s:8:\"platform\";s:8:\"facebook\";s:3:\"url\";s:21:\"https://facebook.com/\";s:10:\"icon_class\";s:17:\"fab fa-facebook-f\";s:10:\"sort_order\";i:2;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2025-11-24 07:00:58\";s:10:\"updated_at\";s:19:\"2025-11-24 07:00:58\";}i:2;O:8:\"stdClass\":8:{s:2:\"id\";i:3;s:8:\"platform\";s:7:\"youtube\";s:3:\"url\";s:20:\"https://youtube.com/\";s:10:\"icon_class\";s:14:\"fab fa-youtube\";s:10:\"sort_order\";i:3;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2025-11-24 07:00:58\";s:10:\"updated_at\";s:19:\"2025-11-24 07:00:58\";}i:3;O:8:\"stdClass\":8:{s:2:\"id\";i:4;s:8:\"platform\";s:9:\"instagram\";s:3:\"url\";s:22:\"https://instagram.com/\";s:10:\"icon_class\";s:16:\"fab fa-instagram\";s:10:\"sort_order\";i:4;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2025-11-24 07:00:58\";s:10:\"updated_at\";s:19:\"2025-11-24 07:00:58\";}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}',1764578678),
	('samsae-store-cache-home.testimonials','O:29:\"Illuminate\\Support\\Collection\":2:{s:8:\"\0*\0items\";a:3:{i:0;O:8:\"stdClass\":10:{s:2:\"id\";i:1;s:11:\"author_name\";s:3:\"Ari\";s:12:\"author_title\";s:8:\"Customer\";s:11:\"avatar_path\";N;s:7:\"content\";s:34:\"Produk fresh dan pengiriman cepat!\";s:6:\"rating\";i:5;s:10:\"sort_order\";i:1;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2025-11-24 07:00:58\";s:10:\"updated_at\";s:19:\"2025-11-24 07:00:58\";}i:1;O:8:\"stdClass\":10:{s:2:\"id\";i:2;s:11:\"author_name\";s:4:\"Bima\";s:12:\"author_title\";s:8:\"Customer\";s:11:\"avatar_path\";N;s:7:\"content\";s:33:\"Harga terjangkau, kualitas bagus.\";s:6:\"rating\";i:5;s:10:\"sort_order\";i:2;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2025-11-24 07:00:58\";s:10:\"updated_at\";s:19:\"2025-11-24 07:00:58\";}i:2;O:8:\"stdClass\":10:{s:2:\"id\";i:3;s:11:\"author_name\";s:5:\"Citra\";s:12:\"author_title\";s:8:\"Customer\";s:11:\"avatar_path\";N;s:7:\"content\";s:31:\"Pilihan buah dan sayur lengkap!\";s:6:\"rating\";i:4;s:10:\"sort_order\";i:3;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2025-11-24 07:00:58\";s:10:\"updated_at\";s:19:\"2025-11-24 07:00:58\";}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}',1764578678);

/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table cache_locks
# ------------------------------------------------------------

DROP TABLE IF EXISTS `cache_locks`;

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table carousel_slides
# ------------------------------------------------------------

DROP TABLE IF EXISTS `carousel_slides`;

CREATE TABLE `carousel_slides` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `carousel_id` bigint(20) unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subtitle` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `button_text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `button_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile_image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int(10) unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `carousel_slides_carousel_id_foreign` (`carousel_id`),
  CONSTRAINT `carousel_slides_carousel_id_foreign` FOREIGN KEY (`carousel_id`) REFERENCES `carousels` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `carousel_slides` WRITE;
/*!40000 ALTER TABLE `carousel_slides` DISABLE KEYS */;

INSERT INTO `carousel_slides` (`id`, `carousel_id`, `title`, `subtitle`, `button_text`, `button_url`, `image_path`, `mobile_image_path`, `sort_order`, `is_active`, `created_at`, `updated_at`)
VALUES
	(1,1,'Bakpia Kacang Hijau Traveler Pack','Bakpia Kacang Hijau Traveler Pack','Shop','http://127.0.0.1:8000/shop','storage/uploads/slides/hILoTNC5sHb61XXmxkMCT96Y8ttO0YCzL8sypanx.png',NULL,3,1,'2025-11-24 07:00:58','2025-11-27 06:34:45'),
	(2,1,'Bakpia Coklat Traveler Pack','Bakpia Coklat Traveler Pack','Shop','http://127.0.0.1:8000/shop','storage/uploads/slides/BPf6Bsn5qQwtlATlzaxo99NOaLwDEVpvW6dtueEV.png',NULL,2,1,'2025-11-24 07:00:58','2025-11-27 06:33:55'),
	(3,1,'Bakpia Coklat Mini Pack','Bakpia Coklat Mini Pack','Shop','http://127.0.0.1:8000/shop','storage/uploads/slides/57RVOlxdA4elyT3mMWmR27argueYJKXuwWG0LngG.png',NULL,1,1,'2025-11-24 07:00:58','2025-11-27 06:32:28');

/*!40000 ALTER TABLE `carousel_slides` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table carousels
# ------------------------------------------------------------

DROP TABLE IF EXISTS `carousels`;

CREATE TABLE `carousels` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `carousels_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `carousels` WRITE;
/*!40000 ALTER TABLE `carousels` DISABLE KEYS */;

INSERT INTO `carousels` (`id`, `name`, `key`, `is_active`, `created_at`, `updated_at`)
VALUES
	(1,'Home Hero','home_hero',1,'2025-11-24 07:00:58','2025-11-24 07:00:58');

/*!40000 ALTER TABLE `carousels` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table categories
# ------------------------------------------------------------

DROP TABLE IF EXISTS `categories`;

CREATE TABLE `categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_slug_unique` (`slug`),
  KEY `categories_parent_id_foreign` (`parent_id`),
  CONSTRAINT `categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;

INSERT INTO `categories` (`id`, `name`, `slug`, `parent_id`, `is_active`, `created_at`, `updated_at`)
VALUES
	(1,'Bakpia','bakpia',NULL,1,'2025-11-24 07:00:58','2025-11-24 08:28:01'),
	(2,'Bolu','bolu',NULL,1,'2025-11-24 07:00:58','2025-11-24 08:27:49');

/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table facts
# ------------------------------------------------------------

DROP TABLE IF EXISTS `facts`;

CREATE TABLE `facts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` bigint(20) unsigned NOT NULL DEFAULT '0',
  `icon_class` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int(10) unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `facts` WRITE;
/*!40000 ALTER TABLE `facts` DISABLE KEYS */;

INSERT INTO `facts` (`id`, `label`, `value`, `icon_class`, `image_path`, `sort_order`, `is_active`, `created_at`, `updated_at`)
VALUES
	(1,'Happy Customers',1234,'bx bx-smile',NULL,1,1,'2025-11-24 07:00:58','2025-11-24 07:00:58'),
	(2,'Quality Products',245,'bx bx-badge-check',NULL,2,1,'2025-11-24 07:00:58','2025-11-24 07:00:58'),
	(3,'Orders Served',5821,'bx bx-cart',NULL,3,1,'2025-11-24 07:00:58','2025-11-24 07:00:58'),
	(4,'Awards',12,'bx bx-award',NULL,4,1,'2025-11-24 07:00:58','2025-11-24 07:00:58');

/*!40000 ALTER TABLE `facts` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table failed_jobs
# ------------------------------------------------------------

DROP TABLE IF EXISTS `failed_jobs`;

CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table features
# ------------------------------------------------------------

DROP TABLE IF EXISTS `features`;

CREATE TABLE `features` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icon_class` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int(10) unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `features` WRITE;
/*!40000 ALTER TABLE `features` DISABLE KEYS */;

INSERT INTO `features` (`id`, `title`, `description`, `icon_class`, `image_path`, `sort_order`, `is_active`, `created_at`, `updated_at`)
VALUES
	(1,'Free Shipping','On all orders over $50','fas fa-shipping-fast',NULL,1,1,'2025-11-24 07:00:58','2025-11-24 07:00:58'),
	(2,'Always Fresh','Product well package','fas fa-leaf',NULL,2,1,'2025-11-24 07:00:58','2025-11-24 07:00:58'),
	(3,'Superior Quality','Quality Products','fas fa-award',NULL,3,1,'2025-11-24 07:00:58','2025-11-24 07:00:58'),
	(4,'Support','24/7 support','fas fa-headset',NULL,4,1,'2025-11-24 07:00:58','2025-11-24 07:00:58');

/*!40000 ALTER TABLE `features` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table home_collection_items
# ------------------------------------------------------------

DROP TABLE IF EXISTS `home_collection_items`;

CREATE TABLE `home_collection_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `home_collection_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `sort_order` int(10) unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `home_collection_items_home_collection_id_product_id_unique` (`home_collection_id`,`product_id`),
  KEY `home_collection_items_product_id_foreign` (`product_id`),
  CONSTRAINT `home_collection_items_home_collection_id_foreign` FOREIGN KEY (`home_collection_id`) REFERENCES `home_collections` (`id`) ON DELETE CASCADE,
  CONSTRAINT `home_collection_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `home_collection_items` WRITE;
/*!40000 ALTER TABLE `home_collection_items` DISABLE KEYS */;

INSERT INTO `home_collection_items` (`id`, `home_collection_id`, `product_id`, `sort_order`, `created_at`, `updated_at`)
VALUES
	(2,1,2,2,'2025-11-24 07:00:58','2025-11-24 07:00:58'),
	(3,1,3,3,'2025-11-24 07:00:58','2025-11-24 07:00:58'),
	(4,1,4,4,'2025-11-24 07:00:58','2025-11-24 07:00:58'),
	(5,1,5,5,'2025-11-24 07:00:58','2025-11-24 07:00:58'),
	(6,1,6,6,'2025-11-24 07:00:58','2025-11-24 07:00:58');

/*!40000 ALTER TABLE `home_collection_items` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table home_collections
# ------------------------------------------------------------

DROP TABLE IF EXISTS `home_collections`;

CREATE TABLE `home_collections` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `home_collections_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `home_collections` WRITE;
/*!40000 ALTER TABLE `home_collections` DISABLE KEYS */;

INSERT INTO `home_collections` (`id`, `name`, `key`, `description`, `is_active`, `created_at`, `updated_at`)
VALUES
	(1,'Bestseller','bestseller','Top selling items',1,'2025-11-24 07:00:58','2025-11-24 07:00:58');

/*!40000 ALTER TABLE `home_collections` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table job_batches
# ------------------------------------------------------------

DROP TABLE IF EXISTS `job_batches`;

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table jobs
# ------------------------------------------------------------

DROP TABLE IF EXISTS `jobs`;

CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table migrations
# ------------------------------------------------------------

DROP TABLE IF EXISTS `migrations`;

CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;

INSERT INTO `migrations` (`id`, `migration`, `batch`)
VALUES
	(1,'0001_01_01_000000_create_users_table',1),
	(2,'0001_01_01_000001_create_cache_table',1),
	(3,'0001_01_01_000002_create_jobs_table',1),
	(4,'2025_11_24_050000_create_cms_home_tables',1),
	(6,'2025_11_26_043448_create_pages_table',2),
	(7,'2025_11_26_045820_add_role_and_profile_to_users_table',2),
	(8,'2025_11_26_060000_add_page_id_to_navigation_links_table',3),
	(9,'2025_11_26_080000_create_product_reviews_table',4),
	(10,'2025_11_28_043206_add_columns_to_settings_table',5),
	(11,'2025_11_28_044003_add_logo_dimensions_to_settings_table',6),
	(12,'2025_12_01_000001_create_orders_table',7),
	(13,'2025_12_01_000002_create_order_items_table',8),
	(14,'2025_12_01_000003_create_payment_transactions_table',9);

/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table navigation_links
# ------------------------------------------------------------

DROP TABLE IF EXISTS `navigation_links`;

CREATE TABLE `navigation_links` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `navigation_menu_id` bigint(20) unsigned NOT NULL,
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `route_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `page_id` bigint(20) unsigned DEFAULT NULL,
  `target` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int(10) unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `navigation_links_navigation_menu_id_foreign` (`navigation_menu_id`),
  KEY `navigation_links_parent_id_foreign` (`parent_id`),
  KEY `navigation_links_page_id_foreign` (`page_id`),
  CONSTRAINT `navigation_links_navigation_menu_id_foreign` FOREIGN KEY (`navigation_menu_id`) REFERENCES `navigation_menus` (`id`) ON DELETE CASCADE,
  CONSTRAINT `navigation_links_page_id_foreign` FOREIGN KEY (`page_id`) REFERENCES `pages` (`id`) ON DELETE SET NULL,
  CONSTRAINT `navigation_links_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `navigation_links` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `navigation_links` WRITE;
/*!40000 ALTER TABLE `navigation_links` DISABLE KEYS */;

INSERT INTO `navigation_links` (`id`, `navigation_menu_id`, `parent_id`, `label`, `url`, `route_name`, `page_id`, `target`, `sort_order`, `is_active`, `created_at`, `updated_at`)
VALUES
	(20,7,NULL,'Beranda','/',NULL,NULL,'_self',1,1,'2025-11-26 05:26:11','2025-11-26 05:26:11'),
	(21,7,NULL,'Produk',NULL,'shop',NULL,'_self',2,1,'2025-11-26 05:26:11','2025-11-26 05:26:11'),
	(22,7,NULL,'Tentang',NULL,NULL,NULL,'_self',3,1,'2025-11-26 05:26:11','2025-11-26 05:26:11'),
	(23,7,22,'Tentang Kami',NULL,NULL,2,'_self',1,1,'2025-11-26 05:26:11','2025-11-26 05:26:11'),
	(24,7,22,'FAQ',NULL,NULL,4,'_self',2,1,'2025-11-26 05:26:11','2025-11-26 05:26:11'),
	(25,7,NULL,'Kontak',NULL,NULL,3,'_self',4,1,'2025-11-26 05:26:11','2025-11-26 05:26:11'),
	(26,8,NULL,'Tentang Samsae',NULL,NULL,2,'_self',1,1,'2025-11-26 05:26:11','2025-11-26 05:26:11'),
	(27,8,NULL,'FAQ',NULL,NULL,4,'_self',2,1,'2025-11-26 05:26:11','2025-11-26 05:26:11'),
	(28,7,NULL,'Outlet',NULL,NULL,NULL,'_self',4,1,'2025-11-27 07:55:49','2025-11-27 07:55:49'),
	(29,7,28,'Gayamsari',NULL,NULL,6,'_self',0,1,'2025-11-27 07:56:14','2025-11-27 07:56:14'),
	(30,9,NULL,'Store Majapahit',NULL,NULL,6,'_self',1,1,'2025-11-28 04:47:31','2025-11-28 04:47:57'),
	(31,7,28,'Jatingaleh',NULL,NULL,7,'_self',2,1,'2025-11-28 08:33:05','2025-11-28 08:33:05');

/*!40000 ALTER TABLE `navigation_links` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table navigation_menus
# ------------------------------------------------------------

DROP TABLE IF EXISTS `navigation_menus`;

CREATE TABLE `navigation_menus` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `navigation_menus_location_index` (`location`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `navigation_menus` WRITE;
/*!40000 ALTER TABLE `navigation_menus` DISABLE KEYS */;

INSERT INTO `navigation_menus` (`id`, `name`, `location`, `is_active`, `created_at`, `updated_at`)
VALUES
	(7,'Header','header',1,'2025-11-26 05:26:11','2025-11-26 05:26:11'),
	(8,'Tentang Kami','footer_column_1',1,'2025-11-26 05:26:11','2025-11-27 07:53:05'),
	(9,'Store Kami','footer_column_2',1,'2025-11-28 04:47:08','2025-11-28 04:48:21');

/*!40000 ALTER TABLE `navigation_menus` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table order_items
# ------------------------------------------------------------

DROP TABLE IF EXISTS `order_items`;

CREATE TABLE `order_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) unsigned NOT NULL,
  `product_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `product_details` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_items_order_id_index` (`order_id`),
  KEY `order_items_product_id_index` (`product_id`),
  CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `price`, `quantity`, `total`, `product_details`, `created_at`, `updated_at`)
VALUES
	(1,3,'6','Bakpia Coklat Mini Pack',35000.00,1,35000.00,X'7B22696D616765223A202273746F726167652F75706C6F6164732F70726F64756374732F4B376D46523342526A347447356C4F78434F35505857774D634C4A34563649506944416536765A692E706E67222C20226465736372697074696F6E223A206E756C6C7D','2025-12-01 05:12:04','2025-12-01 05:12:04'),
	(2,3,'18','Bakpia Coklat Mini Pack',35000.00,1,35000.00,X'7B22696D616765223A202273746F726167652F75706C6F6164732F70726F64756374732F4B376D46523342526A347447356C4F78434F35505857774D634C4A34563649506944416536765A692E706E67222C20226465736372697074696F6E223A206E756C6C7D','2025-12-01 05:12:04','2025-12-01 05:12:04'),
	(3,6,'6','Bakpia Coklat Mini Pack',35000.00,1,35000.00,X'7B22696D616765223A202273746F726167652F75706C6F6164732F70726F64756374732F4B376D46523342526A347447356C4F78434F35505857774D634C4A34563649506944416536765A692E706E67222C20226465736372697074696F6E223A206E756C6C7D','2025-12-01 05:31:28','2025-12-01 05:31:28'),
	(4,6,'18','Bakpia Coklat Mini Pack',35000.00,1,35000.00,X'7B22696D616765223A202273746F726167652F75706C6F6164732F70726F64756374732F4B376D46523342526A347447356C4F78434F35505857774D634C4A34563649506944416536765A692E706E67222C20226465736372697074696F6E223A206E756C6C7D','2025-12-01 05:31:28','2025-12-01 05:31:28'),
	(5,7,'6','Bakpia Coklat Mini Pack',35000.00,1,35000.00,X'7B22696D616765223A202273746F726167652F75706C6F6164732F70726F64756374732F4B376D46523342526A347447356C4F78434F35505857774D634C4A34563649506944416536765A692E706E67222C20226465736372697074696F6E223A206E756C6C7D','2025-12-01 05:39:07','2025-12-01 05:39:07'),
	(6,8,'6','Bakpia Coklat Mini Pack',35000.00,1,35000.00,X'7B22696D616765223A202273746F726167652F75706C6F6164732F70726F64756374732F4B376D46523342526A347447356C4F78434F35505857774D634C4A34563649506944416536765A692E706E67222C20226465736372697074696F6E223A206E756C6C7D','2025-12-01 05:40:54','2025-12-01 05:40:54'),
	(7,9,'6','Bakpia Coklat Mini Pack',35000.00,1,35000.00,X'7B22696D616765223A202273746F726167652F75706C6F6164732F70726F64756374732F4B376D46523342526A347447356C4F78434F35505857774D634C4A34563649506944416536765A692E706E67222C20226465736372697074696F6E223A206E756C6C7D','2025-12-01 05:42:56','2025-12-01 05:42:56'),
	(8,11,'1','Test Product',30000.00,1,30000.00,NULL,'2025-12-01 05:48:27','2025-12-01 05:48:27'),
	(9,12,'30','Bakpia Coklat Mini Pack',35000.00,1,35000.00,X'7B22696D616765223A202273746F726167652F75706C6F6164732F70726F64756374732F4B376D46523342526A347447356C4F78434F35505857774D634C4A34563649506944416536765A692E706E67222C20226465736372697074696F6E223A206E756C6C7D','2025-12-01 05:56:10','2025-12-01 05:56:10'),
	(10,13,'6','Bakpia Coklat Mini Pack',35000.00,1,35000.00,X'7B22696D616765223A202273746F726167652F75706C6F6164732F70726F64756374732F4B376D46523342526A347447356C4F78434F35505857774D634C4A34563649506944416536765A692E706E67222C20226465736372697074696F6E223A206E756C6C7D','2025-12-01 05:57:48','2025-12-01 05:57:48'),
	(11,14,'1','Bakpia Coklat Mini Pack',35000.00,1,35000.00,NULL,'2025-12-01 06:01:05','2025-12-01 06:01:05'),
	(12,15,'6','Bakpia Coklat Mini Pack',35000.00,1,35000.00,X'7B22696D616765223A202273746F726167652F75706C6F6164732F70726F64756374732F4B376D46523342526A347447356C4F78434F35505857774D634C4A34563649506944416536765A692E706E67222C20226465736372697074696F6E223A206E756C6C7D','2025-12-01 06:04:29','2025-12-01 06:04:29'),
	(13,16,'1','Baklia Coklat Mini Pack',35000.00,1,35000.00,NULL,'2025-12-01 06:07:33','2025-12-01 06:07:33'),
	(14,17,'18','Bakpia Coklat Mini Pack',35000.00,1,35000.00,X'7B22696D616765223A202273746F726167652F75706C6F6164732F70726F64756374732F4B376D46523342526A347447356C4F78434F35505857774D634C4A34563649506944416536765A692E706E67222C20226465736372697074696F6E223A206E756C6C7D','2025-12-01 06:15:20','2025-12-01 06:15:20'),
	(15,18,'6','Bakpia Coklat Mini Pack',35000.00,1,35000.00,X'7B22696D616765223A202273746F726167652F75706C6F6164732F70726F64756374732F4B376D46523342526A347447356C4F78434F35505857774D634C4A34563649506944416536765A692E706E67222C20226465736372697074696F6E223A206E756C6C7D','2025-12-01 06:18:57','2025-12-01 06:18:57');

/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table orders
# ------------------------------------------------------------

DROP TABLE IF EXISTS `orders`;

CREATE TABLE `orders` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `shipping_cost` decimal(10,2) NOT NULL,
  `discount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(10,2) NOT NULL,
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'IDR',
  `status` enum('pending','paid','failed','cancelled','expired') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `payment_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_details` json DEFAULT NULL,
  `shipping_address` json NOT NULL,
  `billing_address` json DEFAULT NULL,
  `midtrans_order_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `midtrans_transaction_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `shipping_method_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_order_number_unique` (`order_number`),
  KEY `orders_status_created_at_index` (`status`,`created_at`),
  KEY `orders_midtrans_order_id_index` (`midtrans_order_id`),
  KEY `orders_user_id_index` (`user_id`),
  CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;

INSERT INTO `orders` (`id`, `order_number`, `user_id`, `subtotal`, `shipping_cost`, `discount`, `total_amount`, `currency`, `status`, `payment_type`, `payment_method`, `payment_details`, `shipping_address`, `billing_address`, `midtrans_order_id`, `midtrans_transaction_id`, `paid_at`, `created_at`, `updated_at`, `shipping_method_id`)
VALUES
	(3,'ORD-1764565924-GEM5',5,70000.00,15000.00,0.00,85000.00,'IDR','pending',NULL,NULL,NULL,X'7B2263697479223A20224B6F74612053656D6172616E67222C2022656D61696C223A202273616D73616573746F726540676D61696C2E636F6D222C202270686F6E65223A2022303832323232323035323034222C202261646472657373223A2022476179616D736172692C204B6F74612073656D6172616E67222C2022636F756E747279223A2022496E646F6E65736961222C20226C6173745F6E616D65223A2022596F68616E6573222C202266697273745F6E616D65223A2022446F6E69222C2022706F7374616C5F636F6465223A20223333383939227D',NULL,'ORDER-1764565924-5',NULL,NULL,'2025-12-01 05:12:04','2025-12-01 05:12:04',NULL),
	(5,'TEST-001',NULL,50000.00,15000.00,0.00,65000.00,'IDR','pending','bank_transfer','Bank Transfer - BCA',X'7B226F726465725F6964223A20224F524445522D544553542D303031222C202267726F73735F616D6F756E74223A2036353030302C20227061796D656E745F74797065223A202262616E6B5F7472616E73666572222C20227472616E73616374696F6E5F6964223A2022544553542D5452414E532D303031222C20227472616E73616374696F6E5F737461747573223A2022736574746C656D656E74227D',X'7B226E616D65223A2022546573742055736572227D',NULL,'ORDER-TEST-001','TEST-TRANS-001',NULL,'2025-12-01 05:24:24','2025-12-01 05:30:55',NULL),
	(6,'ORD-1764567088-8UIJ',5,70000.00,15000.00,0.00,85000.00,'IDR','pending',NULL,NULL,NULL,X'7B2263697479223A20224B6F74612053656D6172616E67222C2022656D61696C223A202273616D73616573746F726540676D61696C2E636F6D222C202270686F6E65223A2022303832323232323035323034222C202261646472657373223A2022476179616D736172692C204B6F74612073656D6172616E67222C2022636F756E747279223A2022496E646F6E65736961222C20226C6173745F6E616D65223A2022596F68616E6573222C202266697273745F6E616D65223A2022446F6E69222C2022706F7374616C5F636F6465223A20223333383939227D',NULL,'ORDER-1764567088-5',NULL,NULL,'2025-12-01 05:31:28','2025-12-01 05:31:28',NULL),
	(7,'ORD-1764567547-KQ7F',5,35000.00,15000.00,0.00,50000.00,'IDR','pending',NULL,NULL,NULL,X'7B2263697479223A20224B6F74612053656D6172616E67222C2022656D61696C223A202273616D73616573746F726540676D61696C2E636F6D222C202270686F6E65223A2022303832323232323035323034222C202261646472657373223A2022476179616D736172692C204B6F74612073656D6172616E67222C2022636F756E747279223A2022496E646F6E65736961222C20226C6173745F6E616D65223A2022596F68616E6573222C202266697273745F6E616D65223A2022446F6E69222C2022706F7374616C5F636F6465223A20223333383939227D',NULL,'ORDER-1764567547-5',NULL,NULL,'2025-12-01 05:39:07','2025-12-01 05:39:07',NULL),
	(8,'ORD-1764567654-65DB',5,35000.00,15000.00,0.00,50000.00,'IDR','pending',NULL,NULL,NULL,X'7B2263697479223A20224B6F74612053656D6172616E67222C2022656D61696C223A202273616D73616573746F726540676D61696C2E636F6D222C202270686F6E65223A2022303832323232323035323034222C202261646472657373223A2022476179616D736172692C204B6F74612073656D6172616E67222C2022636F756E747279223A2022496E646F6E65736961222C20226C6173745F6E616D65223A2022596F68616E6573222C202266697273745F6E616D65223A2022446F6E69222C2022706F7374616C5F636F6465223A20223333383939227D',NULL,'ORDER-1764567654-5',NULL,NULL,'2025-12-01 05:40:54','2025-12-01 05:40:54',NULL),
	(9,'ORD-1764567776-JEF5',5,35000.00,15000.00,0.00,50000.00,'IDR','pending',NULL,NULL,NULL,X'7B2263697479223A20224B6F74612053656D6172616E67222C2022656D61696C223A202273616D73616573746F726540676D61696C2E636F6D222C202270686F6E65223A2022303832323232323035323034222C202261646472657373223A2022476179616D736172692C204B6F74612073656D6172616E67222C2022636F756E747279223A2022496E646F6E65736961222C20226C6173745F6E616D65223A2022596F68616E6573222C202266697273745F6E616D65223A2022446F6E69222C2022706F7374616C5F636F6465223A20223333383939227D',NULL,'ORDER-1764567776-5',NULL,NULL,'2025-12-01 05:42:56','2025-12-01 05:42:56',NULL),
	(11,'ORD-1764568008-TEST',3,15000.00,15000.00,0.00,30000.00,'IDR','pending',NULL,NULL,NULL,X'7B22656D61696C223A202274657374406578616D706C652E636F6D222C202266697273745F6E616D65223A202254657374227D',NULL,'ORDER-1764568008-TEST',NULL,NULL,'2025-12-01 05:46:48','2025-12-01 05:47:43',NULL),
	(12,'ORD-1764568570-FPXL',5,35000.00,15000.00,0.00,50000.00,'IDR','pending',NULL,NULL,NULL,X'7B2263697479223A20224B6F74612053656D6172616E67222C2022656D61696C223A202273616D73616573746F726540676D61696C2E636F6D222C202270686F6E65223A2022303832323232323035323034222C202261646472657373223A2022476179616D736172692C204B6F74612073656D6172616E67222C2022636F756E747279223A2022496E646F6E65736961222C20226C6173745F6E616D65223A2022596F68616E6573222C202266697273745F6E616D65223A2022446F6E69222C2022706F7374616C5F636F6465223A20223333383939227D',NULL,'ORDER-1764568570-5',NULL,NULL,'2025-12-01 05:56:10','2025-12-01 05:56:10',NULL),
	(13,'ORD-1764568668-IVN2',5,35000.00,15000.00,0.00,50000.00,'IDR','pending',NULL,NULL,NULL,X'7B2263697479223A20224B6F74612053656D6172616E67222C2022656D61696C223A202273616D73616573746F726540676D61696C2E636F6D222C202270686F6E65223A2022303832323232323035323034222C202261646472657373223A2022476179616D736172692C204B6F74612073656D6172616E67222C2022636F756E747279223A2022496E646F6E65736961222C20226C6173745F6E616D65223A2022596F68616E6573222C202266697273745F6E616D65223A2022446F6E69222C2022706F7374616C5F636F6465223A20223333383939227D',NULL,'ORDER-1764568668-5',NULL,NULL,'2025-12-01 05:57:48','2025-12-01 05:57:48',NULL),
	(14,'ORD-1764568865-TEST',3,35000.00,15000.00,0.00,50000.00,'IDR','pending',NULL,NULL,NULL,X'7B22656D61696C223A202274657374406578616D706C652E636F6D222C202266697273745F6E616D65223A202254657374227D',NULL,'ORDER-1764568865-TEST',NULL,NULL,'2025-12-01 06:01:05','2025-12-01 06:01:05',NULL),
	(15,'ORD-1764569069-ZFKU',5,35000.00,15000.00,0.00,50000.00,'IDR','pending',NULL,NULL,NULL,X'7B2263697479223A20224B6F74612053656D6172616E67222C2022656D61696C223A202273616D73616573746F726540676D61696C2E636F6D222C202270686F6E65223A2022303832323232323035323034222C202261646472657373223A2022476179616D736172692C204B6F74612073656D6172616E67222C2022636F756E747279223A2022496E646F6E65736961222C20226C6173745F6E616D65223A2022596F68616E6573222C202266697273745F6E616D65223A2022446F6E69222C2022706F7374616C5F636F6465223A20223333383939227D',NULL,'ORDER-1764569069-5',NULL,NULL,'2025-12-01 06:04:29','2025-12-01 06:04:29',NULL),
	(16,'ORD-1764569253-FINAL',3,35000.00,15000.00,0.00,50000.00,'IDR','pending',NULL,NULL,NULL,X'7B22656D61696C223A202274657374406578616D706C652E636F6D222C202266697273745F6E616D65223A202254657374227D',NULL,'ORDER-1764569253-FINAL',NULL,NULL,'2025-12-01 06:07:33','2025-12-01 06:07:33',NULL),
	(17,'ORD-1764569720-FMBF',5,35000.00,15000.00,0.00,50000.00,'IDR','paid','qris','QRIS',X'7B22697373756572223A2022676F706179222C20226163717569726572223A2022676F706179222C202263757272656E6379223A2022494452222C20226F726465725F6964223A20224F524445522D313736343536393732302D35222C20226578706972795F74696D65223A2022323032352D31322D30312031333A33303A3237222C20226D65726368616E745F6964223A202247313334363236343130222C20227374617475735F636F6465223A2022323030222C202266726175645F737461747573223A2022616363657074222C202267726F73735F616D6F756E74223A202233353030302E3030222C20227061796D656E745F74797065223A202271726973222C20227369676E61747572655F6B6579223A20223835633263613139623738323739656439613439393461383861346537656438656136636366666433343934373439663564383437333838666562363837646564366561636536316331306437363938663231626633356135653166373563653264346365396261393765303661316563636236623733363135336664383836222C20227374617475735F6D657373616765223A2022537563636573732C207472616E73616374696F6E20697320666F756E64222C20227472616E73616374696F6E5F6964223A202266333634616335312D366561612D343732362D616539642D383636616365616363633266222C2022736574746C656D656E745F74696D65223A2022323032352D31322D30312031333A31353A3430222C20227472616E73616374696F6E5F74696D65223A2022323032352D31322D30312031333A31353A3237222C20227472616E73616374696F6E5F74797065223A20226F6E2D7573222C20227472616E73616374696F6E5F737461747573223A2022736574746C656D656E74227D',X'7B2263697479223A20224B6F74612053656D6172616E67222C2022656D61696C223A202273616D73616573746F726540676D61696C2E636F6D222C202270686F6E65223A2022303832323232323035323034222C202261646472657373223A2022476179616D736172692C204B6F74612073656D6172616E67222C2022636F756E747279223A2022496E646F6E65736961222C20226C6173745F6E616D65223A2022596F68616E6573222C202266697273745F6E616D65223A2022446F6E69222C2022706F7374616C5F636F6465223A20223333383939227D',NULL,'ORDER-1764569720-5','f364ac51-6eaa-4726-ae9d-866aceaccc2f','2025-12-01 06:18:03','2025-12-01 06:15:20','2025-12-01 06:18:03',NULL),
	(18,'ORD-1764569937-MILL',5,35000.00,15000.00,0.00,50000.00,'IDR','paid','qris','QRIS',X'7B22697373756572223A2022676F706179222C20226163717569726572223A2022676F706179222C202263757272656E6379223A2022494452222C20226F726465725F6964223A20224F524445522D313736343536393933372D35222C20226578706972795F74696D65223A2022323032352D31322D30312031333A33343A3036222C20226D65726368616E745F6964223A202247313334363236343130222C20227374617475735F636F6465223A2022323030222C202266726175645F737461747573223A2022616363657074222C202267726F73735F616D6F756E74223A202233353030302E3030222C20227061796D656E745F74797065223A202271726973222C20227369676E61747572655F6B6579223A20223965336134313762396335666531386336643933623835303333323964346137376330326537303638346638353630336261333561306562343162346138323165393035663566643437336631373434633231363664396233663137613632656166653465383766636161643766646331333961313464386139653661316231222C20227374617475735F6D657373616765223A2022537563636573732C207472616E73616374696F6E20697320666F756E64222C20227472616E73616374696F6E5F6964223A202234363838383730392D613935612D343438352D383162622D303363323239316534373531222C2022736574746C656D656E745F74696D65223A2022323032352D31322D30312031333A31393A3233222C20227472616E73616374696F6E5F74696D65223A2022323032352D31322D30312031333A31393A3036222C20227472616E73616374696F6E5F74797065223A20226F6E2D7573222C20227472616E73616374696F6E5F737461747573223A2022736574746C656D656E74227D',X'7B2263697479223A20224B6F74612053656D6172616E67222C2022656D61696C223A202273616D73616573746F726540676D61696C2E636F6D222C202270686F6E65223A2022303832323232323035323034222C202261646472657373223A2022476179616D736172692C204B6F74612073656D6172616E67222C2022636F756E747279223A2022496E646F6E65736961222C20226C6173745F6E616D65223A2022596F68616E6573222C202266697273745F6E616D65223A2022446F6E69222C2022706F7374616C5F636F6465223A20223333383939227D',NULL,'ORDER-1764569937-5','46888709-a95a-4485-81bb-03c2291e4751','2025-12-01 06:21:15','2025-12-01 06:18:57','2025-12-01 06:21:15',NULL);

/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table pages
# ------------------------------------------------------------

DROP TABLE IF EXISTS `pages`;

CREATE TABLE `pages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci,
  `featured_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attachments` json DEFAULT NULL,
  `video_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` text COLLATE utf8mb4_unicode_ci,
  `is_published` tinyint(1) NOT NULL DEFAULT '0',
  `created_by` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pages_slug_unique` (`slug`),
  KEY `pages_created_by_foreign` (`created_by`),
  CONSTRAINT `pages_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `pages` WRITE;
/*!40000 ALTER TABLE `pages` DISABLE KEYS */;

INSERT INTO `pages` (`id`, `title`, `slug`, `content`, `featured_image`, `attachments`, `video_url`, `meta_title`, `meta_description`, `is_published`, `created_by`, `created_at`, `updated_at`, `deleted_at`)
VALUES
	(2,'Tentang Kami','tentang-kami','<h1>Tentang Samsae</h1><p>Informasi tentang toko Samsae.</p>',NULL,NULL,NULL,'Tentang Samsae','Informasi tentang toko Samsae.',1,4,'2025-11-26 05:26:11','2025-11-26 05:26:11',NULL),
	(3,'Kontak','kontak','<h1>Kontak</h1><p>Hubungi kami untuk informasi lebih lanjut.</p>',NULL,NULL,NULL,'Kontak Samsae','Halaman kontak Samsae.',1,4,'2025-11-26 05:26:11','2025-11-26 05:26:11',NULL),
	(4,'FAQ','faq','<h1>Pertanyaan yang Sering Diajukan</h1><p>Beberapa FAQ tentang layanan kami.</p>',NULL,NULL,NULL,'FAQ Samsae','Pertanyaan umum tentang Samsae.',1,4,'2025-11-26 05:26:11','2025-11-26 05:26:11',NULL),
	(5,'coba','coba','aa','storage/uploads/pages/lsntECCBuNv0W20Q4Am15rd6vpbGORFWsu4yQ2Sd.jpg',NULL,NULL,'aa','as',0,4,'2025-11-26 07:09:10','2025-11-27 07:54:09','2025-11-27 07:54:09'),
	(6,'Store Majapahit ','store-majapahit','<p><b>Bakpia Kukus Tugu Jogja Semarang - Gayamsari</b></p>\r\n<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3960.053267443057!2d110.4501298!3d-7.003010300000001!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e708d16ec0d11f9%3A0xabd32ed4a896e79c!2sBakpia%20Kukus%20Tugu%20Jogja%20Semarang%20-%20Gayamsari!5e0!3m2!1sen!2ssg!4v1764230328222!5m2!1sen!2ssg\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>',NULL,NULL,NULL,'Bakpia Kukus Tugu Jogja Semarang - Gayamsari',NULL,1,4,'2025-11-27 07:54:54','2025-11-27 08:58:28',NULL),
	(7,'Store Jatingaleh - Semarang','store-bakpia-kukus-tugu-jogja-jatingaleh-semarang','<p>Store Bakpia Kukus Tugu Jogja Jatingaleh - Semarang</p><p><br></p>',NULL,NULL,NULL,'Store Bakpia Kukus Tugu Jogja Jatingaleh - Semarang','Store Bakpia Kukus Tugu Jogja Jatingaleh - Semarang',1,4,'2025-11-28 08:29:16','2025-11-28 08:29:50',NULL);

/*!40000 ALTER TABLE `pages` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table password_reset_tokens
# ------------------------------------------------------------

DROP TABLE IF EXISTS `password_reset_tokens`;

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table payment_transactions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `payment_transactions`;

CREATE TABLE `payment_transactions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) unsigned NOT NULL,
  `transaction_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_id_midtrans` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','capture','settlement','deny','cancel','expire','refund') COLLATE utf8mb4_unicode_ci NOT NULL,
  `gross_amount` decimal(10,2) NOT NULL,
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'IDR',
  `transaction_details` json DEFAULT NULL,
  `va_numbers` json DEFAULT NULL,
  `bill_key` json DEFAULT NULL,
  `biller_code` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payment_transactions_transaction_id_unique` (`transaction_id`),
  KEY `payment_transactions_order_id_index` (`order_id`),
  KEY `payment_transactions_transaction_id_index` (`transaction_id`),
  KEY `payment_transactions_status_index` (`status`),
  CONSTRAINT `payment_transactions_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `payment_transactions` WRITE;
/*!40000 ALTER TABLE `payment_transactions` DISABLE KEYS */;

INSERT INTO `payment_transactions` (`id`, `order_id`, `transaction_id`, `order_id_midtrans`, `payment_type`, `payment_method`, `status`, `gross_amount`, `currency`, `transaction_details`, `va_numbers`, `bill_key`, `biller_code`, `created_at`, `updated_at`)
VALUES
	(1,5,'TEST-TRANS-001','ORDER-TEST-001','bank_transfer',NULL,'settlement',65000.00,'IDR',X'7B226F726465725F6964223A20224F524445522D544553542D303031222C202267726F73735F616D6F756E74223A2036353030302C20227061796D656E745F74797065223A202262616E6B5F7472616E73666572222C20227472616E73616374696F6E5F6964223A2022544553542D5452414E532D303031222C20227472616E73616374696F6E5F737461747573223A2022736574746C656D656E74227D',NULL,NULL,NULL,'2025-12-01 05:26:13','2025-12-01 05:28:49'),
	(2,17,'f364ac51-6eaa-4726-ae9d-866aceaccc2f','ORDER-1764569720-5','qris',NULL,'settlement',35000.00,'IDR',X'7B22697373756572223A2022676F706179222C20226163717569726572223A2022676F706179222C202263757272656E6379223A2022494452222C20226F726465725F6964223A20224F524445522D313736343536393732302D35222C20226578706972795F74696D65223A2022323032352D31322D30312031333A33303A3237222C20226D65726368616E745F6964223A202247313334363236343130222C20227374617475735F636F6465223A2022323030222C202266726175645F737461747573223A2022616363657074222C202267726F73735F616D6F756E74223A202233353030302E3030222C20227061796D656E745F74797065223A202271726973222C20227369676E61747572655F6B6579223A20223835633263613139623738323739656439613439393461383861346537656438656136636366666433343934373439663564383437333838666562363837646564366561636536316331306437363938663231626633356135653166373563653264346365396261393765303661316563636236623733363135336664383836222C20227374617475735F6D657373616765223A2022537563636573732C207472616E73616374696F6E20697320666F756E64222C20227472616E73616374696F6E5F6964223A202266333634616335312D366561612D343732362D616539642D383636616365616363633266222C2022736574746C656D656E745F74696D65223A2022323032352D31322D30312031333A31353A3430222C20227472616E73616374696F6E5F74696D65223A2022323032352D31322D30312031333A31353A3237222C20227472616E73616374696F6E5F74797065223A20226F6E2D7573222C20227472616E73616374696F6E5F737461747573223A2022736574746C656D656E74227D',NULL,NULL,NULL,'2025-12-01 06:18:03','2025-12-01 06:18:03'),
	(3,18,'46888709-a95a-4485-81bb-03c2291e4751','ORDER-1764569937-5','qris',NULL,'settlement',35000.00,'IDR',X'7B22697373756572223A2022676F706179222C20226163717569726572223A2022676F706179222C202263757272656E6379223A2022494452222C20226F726465725F6964223A20224F524445522D313736343536393933372D35222C20226578706972795F74696D65223A2022323032352D31322D30312031333A33343A3036222C20226D65726368616E745F6964223A202247313334363236343130222C20227374617475735F636F6465223A2022323030222C202266726175645F737461747573223A2022616363657074222C202267726F73735F616D6F756E74223A202233353030302E3030222C20227061796D656E745F74797065223A202271726973222C20227369676E61747572655F6B6579223A20223965336134313762396335666531386336643933623835303333323964346137376330326537303638346638353630336261333561306562343162346138323165393035663566643437336631373434633231363664396233663137613632656166653465383766636161643766646331333961313464386139653661316231222C20227374617475735F6D657373616765223A2022537563636573732C207472616E73616374696F6E20697320666F756E64222C20227472616E73616374696F6E5F6964223A202234363838383730392D613935612D343438352D383162622D303363323239316534373531222C2022736574746C656D656E745F74696D65223A2022323032352D31322D30312031333A31393A3233222C20227472616E73616374696F6E5F74696D65223A2022323032352D31322D30312031333A31393A3036222C20227472616E73616374696F6E5F74797065223A20226F6E2D7573222C20227472616E73616374696F6E5F737461747573223A2022736574746C656D656E74227D',NULL,NULL,NULL,'2025-12-01 06:19:43','2025-12-01 06:21:15');

/*!40000 ALTER TABLE `payment_transactions` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table product_categories
# ------------------------------------------------------------

DROP TABLE IF EXISTS `product_categories`;

CREATE TABLE `product_categories` (
  `product_id` bigint(20) unsigned NOT NULL,
  `category_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`product_id`,`category_id`),
  KEY `product_categories_category_id_foreign` (`category_id`),
  CONSTRAINT `product_categories_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_categories_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `product_categories` WRITE;
/*!40000 ALTER TABLE `product_categories` DISABLE KEYS */;

INSERT INTO `product_categories` (`product_id`, `category_id`)
VALUES
	(2,1),
	(3,1),
	(4,1),
	(5,1),
	(6,1);

/*!40000 ALTER TABLE `product_categories` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table product_images
# ------------------------------------------------------------

DROP TABLE IF EXISTS `product_images`;

CREATE TABLE `product_images` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` int(10) unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_images_product_id_foreign` (`product_id`),
  CONSTRAINT `product_images_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `product_images` WRITE;
/*!40000 ALTER TABLE `product_images` DISABLE KEYS */;

INSERT INTO `product_images` (`id`, `product_id`, `image_path`, `sort_order`, `created_at`, `updated_at`)
VALUES
	(1,6,'uploads/products/K7mFR3BRj4tG5lOxCO5PXWwMcLJ4V6IPiDAe6vZi.png',0,'2025-11-24 08:32:10','2025-11-24 08:32:10'),
	(2,5,'uploads/products/f29Hnh5Hf5fKKk5E3404hfmJCflScR401GpSQvU9.png',0,'2025-11-24 08:35:59','2025-11-24 08:35:59'),
	(7,6,'uploads/products/u7Iac9xrGaI4Sa35GcTUAIpEOhMX2RIZuKiT853a.png',1,'2025-11-26 08:22:49','2025-11-26 08:22:49'),
	(8,6,'uploads/products/IyAlXcVQVSThfrpwfJla5tJyawD79lU7aTMxVqAB.png',1,'2025-11-26 08:32:57','2025-11-26 08:32:57'),
	(9,2,'uploads/products/pe63VFglD3gZOQR3SrffZv7btqK4w5FkKR2Mt9bo.png',0,'2025-11-26 08:34:23','2025-11-26 08:34:23'),
	(10,2,'uploads/products/RI5EwgGqkc79J5v8rTnMjXiBvPSfThgg2AteuHvw.png',1,'2025-11-26 08:34:23','2025-11-26 08:34:23'),
	(11,3,'uploads/products/QRD43csYSjIxpB8fXJXo4uIGi3OZ2SokKJGZU17c.png',0,'2025-11-26 08:35:29','2025-11-26 08:35:29'),
	(12,3,'uploads/products/mn5DbyqXn5CBCr585vgxblA3j3uAmDQEwHC0MPcO.png',1,'2025-11-26 08:35:29','2025-11-26 08:35:29'),
	(13,4,'uploads/products/vq6W4JgdqF3RlvbPVrzk9KB51pnm1UTSWWJSUCUK.png',0,'2025-11-26 08:36:12','2025-11-26 08:36:12'),
	(14,4,'uploads/products/D0CUsAWNVxNxnfIEUmrbeWwF6LhGu8RXSrrlYY33.png',1,'2025-11-26 08:36:12','2025-11-26 08:36:12'),
	(15,7,'uploads/products/mOqZbRMf6hCUaJ0yY9yt9QNawu2C5Tm7z6cd4vjY.webp',0,'2025-11-26 08:36:55','2025-11-26 08:36:55'),
	(16,14,'uploads/products/mOqZbRMf6hCUaJ0yY9yt9QNawu2C5Tm7z6cd4vjY.webp',0,'2025-11-26 08:36:55','2025-11-26 08:36:55');

/*!40000 ALTER TABLE `product_images` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table product_reviews
# ------------------------------------------------------------

DROP TABLE IF EXISTS `product_reviews`;

CREATE TABLE `product_reviews` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rating` tinyint(3) unsigned NOT NULL DEFAULT '5',
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_reviews_product_id_foreign` (`product_id`),
  CONSTRAINT `product_reviews_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `product_reviews` WRITE;
/*!40000 ALTER TABLE `product_reviews` DISABLE KEYS */;

INSERT INTO `product_reviews` (`id`, `product_id`, `name`, `email`, `rating`, `content`, `created_at`, `updated_at`)
VALUES
	(1,6,'Admin','admin@example.com',5,'baik','2025-11-26 07:41:29','2025-11-26 07:41:29');

/*!40000 ALTER TABLE `product_reviews` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table products
# ------------------------------------------------------------

DROP TABLE IF EXISTS `products`;

CREATE TABLE `products` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sku` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `short_description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `price` decimal(12,2) NOT NULL,
  `compare_at_price` decimal(12,2) DEFAULT NULL,
  `stock_qty` int(11) NOT NULL DEFAULT '0',
  `unit` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `main_image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `is_bestseller` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `shelf_life_days` int(11) NOT NULL DEFAULT '30',
  `requires_cold_chain` tinyint(1) NOT NULL DEFAULT '0',
  `shipping_type` enum('fresh','dry','frozen') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'dry',
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;

INSERT INTO `products` (`id`, `name`, `slug`, `sku`, `short_description`, `description`, `price`, `compare_at_price`, `stock_qty`, `unit`, `main_image_path`, `is_active`, `is_featured`, `is_bestseller`, `created_at`, `updated_at`, `shelf_life_days`, `requires_cold_chain`, `shipping_type`)
VALUES
	(2,'Bakpia Coklat Traveler Pack','Bakpia Coklat Traveler Pack',NULL,'Bakpia Coklat Traveler Pack','Bakpia Coklat Traveler Pack',40000.00,NULL,150,'pcs','uploads/products/pe63VFglD3gZOQR3SrffZv7btqK4w5FkKR2Mt9bo.png',1,0,1,'2025-11-24 07:00:58','2025-11-26 08:34:23',30,0,'dry'),
	(3,'Bakpia Kacang Hijau Traveler Pack','Bakpia Kacang Hijau Traveler Pack',NULL,'Bakpia Kacang Hijau Traveler Pack','Bakpia Kacang Hijau Traveler Pack',40000.00,NULL,200,'pcs','uploads/products/QRD43csYSjIxpB8fXJXo4uIGi3OZ2SokKJGZU17c.png',1,0,0,'2025-11-24 07:00:58','2025-11-26 08:35:29',30,0,'dry'),
	(4,'Bakpia Kacang Hijau Mini Pack','Bakpia Kacang Hijau Mini Pack',NULL,'Bakpia Kacang Hijau Mini Pack','Bakpia Kacang Hijau Mini Pack',30000.00,NULL,120,'pcs','uploads/products/vq6W4JgdqF3RlvbPVrzk9KB51pnm1UTSWWJSUCUK.png',1,1,1,'2025-11-24 07:00:58','2025-11-26 08:36:12',30,0,'dry'),
	(5,'Bakpia Coklat Regular Pack','Bakpia Coklat Regular Pack',NULL,NULL,NULL,40000.00,NULL,180,'pcs','uploads/products/f29Hnh5Hf5fKKk5E3404hfmJCflScR401GpSQvU9.png',1,1,0,'2025-11-24 07:00:58','2025-11-26 08:54:33',30,0,'dry'),
	(6,'Bakpia Coklat Mini Pack','Bakpia Coklat Mini Pack',NULL,NULL,NULL,35000.00,NULL,160,'pcs','uploads/products/K7mFR3BRj4tG5lOxCO5PXWwMcLJ4V6IPiDAe6vZi.png',1,1,0,'2025-11-24 07:00:58','2025-11-26 08:33:29',30,0,'dry'),
	(7,'Bakpia Kacang Hijau Regular Pack','Bakpia Kacang Hijau Regular Pack',NULL,'Bakpia Kacang Hijau Regular Pack','Bakpia Kacang Hijau Regular Pack',45000.00,NULL,100,'pcs','uploads/products/mOqZbRMf6hCUaJ0yY9yt9QNawu2C5Tm7z6cd4vjY.webp',1,1,0,'2025-11-26 08:36:55','2025-11-26 08:36:55',30,0,'dry'),
	(14,'Bakpia Coklat Traveler Pack','Bakpia Coklat Traveler Pack2',NULL,'Bakpia Coklat Traveler Pack','Bakpia Coklat Traveler Pack',40000.00,NULL,150,'pcs','uploads/products/pe63VFglD3gZOQR3SrffZv7btqK4w5FkKR2Mt9bo.png',1,0,1,'2025-11-24 07:00:58','2025-11-26 08:34:23',30,0,'dry'),
	(15,'Bakpia Kacang Hijau Traveler Pack','Bakpia Kacang Hijau Traveler Pack2',NULL,'Bakpia Kacang Hijau Traveler Pack','Bakpia Kacang Hijau Traveler Pack',40000.00,NULL,200,'pcs','uploads/products/QRD43csYSjIxpB8fXJXo4uIGi3OZ2SokKJGZU17c.png',1,0,0,'2025-11-24 07:00:58','2025-11-26 08:35:29',30,0,'dry'),
	(16,'Bakpia Kacang Hijau Mini Pack','Bakpia Kacang Hijau Mini Pack2',NULL,'Bakpia Kacang Hijau Mini Pack','Bakpia Kacang Hijau Mini Pack',30000.00,NULL,120,'pcs','uploads/products/vq6W4JgdqF3RlvbPVrzk9KB51pnm1UTSWWJSUCUK.png',1,1,1,'2025-11-24 07:00:58','2025-11-26 08:36:12',30,0,'dry'),
	(17,'Bakpia Coklat Regular Pack','Bakpia Coklat Regular Pack2',NULL,NULL,NULL,40000.00,NULL,180,'pcs','uploads/products/f29Hnh5Hf5fKKk5E3404hfmJCflScR401GpSQvU9.png',1,1,0,'2025-11-24 07:00:58','2025-11-26 08:54:33',30,0,'dry'),
	(18,'Bakpia Coklat Mini Pack','Bakpia Coklat Mini Pack2',NULL,NULL,NULL,35000.00,NULL,160,'pcs','uploads/products/K7mFR3BRj4tG5lOxCO5PXWwMcLJ4V6IPiDAe6vZi.png',1,1,0,'2025-11-24 07:00:58','2025-11-26 08:33:29',30,0,'dry'),
	(19,'Bakpia Kacang Hijau Regular Pack','Bakpia Kacang Hijau Regular Pack2',NULL,'Bakpia Kacang Hijau Regular Pack','Bakpia Kacang Hijau Regular Pack',45000.00,NULL,100,'pcs','uploads/products/mOqZbRMf6hCUaJ0yY9yt9QNawu2C5Tm7z6cd4vjY.webp',1,1,0,'2025-11-26 08:36:55','2025-11-26 08:36:55',30,0,'dry'),
	(26,'Bakpia Coklat Traveler Pack','Bakpia Coklat Traveler Pack3',NULL,'Bakpia Coklat Traveler Pack','Bakpia Coklat Traveler Pack',40000.00,NULL,150,'pcs','uploads/products/pe63VFglD3gZOQR3SrffZv7btqK4w5FkKR2Mt9bo.png',1,0,1,'2025-11-24 07:00:58','2025-11-26 08:34:23',30,0,'dry'),
	(27,'Bakpia Kacang Hijau Traveler Pack','Bakpia Kacang Hijau Traveler Pack3',NULL,'Bakpia Kacang Hijau Traveler Pack','Bakpia Kacang Hijau Traveler Pack',40000.00,NULL,200,'pcs','uploads/products/QRD43csYSjIxpB8fXJXo4uIGi3OZ2SokKJGZU17c.png',1,0,0,'2025-11-24 07:00:58','2025-11-26 08:35:29',30,0,'dry'),
	(28,'Bakpia Kacang Hijau Mini Pack','Bakpia Kacang Hijau Mini Pack3',NULL,'Bakpia Kacang Hijau Mini Pack','Bakpia Kacang Hijau Mini Pack',30000.00,NULL,120,'pcs','uploads/products/vq6W4JgdqF3RlvbPVrzk9KB51pnm1UTSWWJSUCUK.png',1,1,1,'2025-11-24 07:00:58','2025-11-26 08:36:12',30,0,'dry'),
	(29,'Bakpia Coklat Regular Pack','Bakpia Coklat Regular Pack3',NULL,NULL,NULL,40000.00,NULL,180,'pcs','uploads/products/f29Hnh5Hf5fKKk5E3404hfmJCflScR401GpSQvU9.png',1,1,0,'2025-11-24 07:00:58','2025-11-26 08:54:33',30,0,'dry'),
	(30,'Bakpia Coklat Mini Pack','Bakpia Coklat Mini Pack3',NULL,NULL,NULL,35000.00,NULL,160,'pcs','uploads/products/K7mFR3BRj4tG5lOxCO5PXWwMcLJ4V6IPiDAe6vZi.png',1,1,0,'2025-11-24 07:00:58','2025-11-26 08:33:29',30,0,'dry'),
	(31,'Bakpia Kacang Hijau Regular Pack','Bakpia Kacang Hijau Regular Pack3',NULL,'Bakpia Kacang Hijau Regular Pack','Bakpia Kacang Hijau Regular Pack',45000.00,NULL,100,'pcs','uploads/products/mOqZbRMf6hCUaJ0yY9yt9QNawu2C5Tm7z6cd4vjY.webp',1,1,0,'2025-11-26 08:36:55','2025-11-26 08:36:55',30,0,'dry');

/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table sessions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `sessions`;

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table settings
# ------------------------------------------------------------

DROP TABLE IF EXISTS `settings`;

CREATE TABLE `settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `logo_width` int(11) DEFAULT NULL,
  `logo_height` int(11) DEFAULT NULL,
  `logo_object_fit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'contain',
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text',
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;

INSERT INTO `settings` (`id`, `key`, `value`, `logo_width`, `logo_height`, `logo_object_fit`, `type`, `description`, `created_at`, `updated_at`)
VALUES
	(2,'brand_tagline','Oleh-Oleh',NULL,NULL,'contain','text','Brand tagline',NULL,'2025-11-28 05:06:15'),
	(3,'address','1429 Netus Rd, NY 48247',NULL,NULL,'contain','text','Company address',NULL,'2025-11-28 05:06:15'),
	(4,'phone','+0123 4567 8910',NULL,NULL,'contain','text','Company phone number',NULL,'2025-11-28 05:06:15'),
	(5,'email','info@samsae.com',NULL,NULL,'contain','text','Company email',NULL,'2025-11-28 05:06:15'),
	(6,'newsletter_text','Get updates and offers',NULL,NULL,'contain','text',NULL,NULL,NULL),
	(7,'payment_image_path','fruitables/img/payment.png',NULL,NULL,'contain','image','Payment methods image',NULL,'2025-11-28 05:06:15'),
	(8,'brand_name','Samsae Store',NULL,NULL,'contain','text','Brand name displayed on website','2025-11-24 07:17:26','2025-11-28 05:06:15'),
	(23,'hero_bg','storage/uploads/settings/Q6L92jFqs4FHkCOk4Z8ZrCXOZS2nekSAfmouN0FO.png',NULL,NULL,'contain','image','Hero section background image','2025-11-27 07:05:13','2025-11-28 05:52:41'),
	(24,'footer_about_text','typesetting, remaining essentially unchanged. It was popularised in the 1960s with the like Aldus PageMaker including of Lorem Ipsum.',NULL,NULL,'contain','textarea','Footer about section content','2025-11-27 07:45:39','2025-11-28 05:06:15'),
	(25,'footer_about_title','Why People Like us!',NULL,NULL,'contain','text','Footer about section title','2025-11-27 07:46:08','2025-11-28 05:06:15'),
	(26,'footer_about_link_url','/about',NULL,NULL,'contain','text','Footer about link URL','2025-11-27 07:46:50','2025-11-28 05:06:15'),
	(28,'site_logo','storage/uploads/settings/pA4eU0uxj49qQm9buxJyHQVoP8u8u6MyfjDbSNic.png',250,100,'contain','image','Website logo image','2025-11-28 04:32:51','2025-11-28 05:50:07'),
	(29,'site_name_logo',NULL,NULL,NULL,'contain','image','Website name logo image','2025-11-28 04:32:51','2025-11-28 05:50:15'),
	(30,'site_title','Samsae',NULL,NULL,'contain','text','Website title','2025-11-28 04:32:51','2025-11-28 04:32:51'),
	(31,'site_description','Samsae - Your trusted online store',NULL,NULL,'contain','textarea','Website description','2025-11-28 04:32:51','2025-11-28 04:56:13'),
	(32,'homepage_products_title','Our Products',NULL,NULL,'contain','text','Homepage products section title','2025-11-28 05:06:15','2025-11-28 05:06:15'),
	(33,'homepage_products_subtitle','Browse our wide selection of fresh products',NULL,NULL,'contain','textarea','Homepage products section subtitle','2025-11-28 05:06:15','2025-11-28 05:06:15'),
	(34,'homepage_vegetables_title','Fresh Organic Vegetables',NULL,NULL,'contain','text','Homepage vegetables section title','2025-11-28 05:06:15','2025-11-28 05:06:15'),
	(35,'homepage_vegetables_subtitle','Premium quality organic vegetables',NULL,NULL,'contain','textarea','Homepage vegetables section subtitle','2025-11-28 05:06:15','2025-11-28 05:06:15'),
	(36,'homepage_bestseller_title','Bestseller Products',NULL,NULL,'contain','text','Homepage bestseller section title','2025-11-28 05:06:15','2025-11-28 05:06:15'),
	(37,'homepage_bestseller_subtitle','Most popular products chosen by our customers',NULL,NULL,'contain','textarea','Homepage bestseller section subtitle','2025-11-28 05:06:15','2025-11-28 05:06:15'),
	(38,'homepage_testimonial_title','Our Testimonial',NULL,NULL,'contain','text','Homepage testimonial section title','2025-11-28 05:06:15','2025-11-28 05:06:15'),
	(39,'homepage_testimonial_subtitle','Our Client Saying!',NULL,NULL,'contain','text','Homepage testimonial section subtitle','2025-11-28 05:06:15','2025-11-28 05:06:15'),
	(40,'search_placeholder','Search for products...',NULL,NULL,'contain','text','Search input placeholder text','2025-11-28 05:06:15','2025-11-28 05:06:15'),
	(41,'search_button_text','Search',NULL,NULL,'contain','text','Search button text','2025-11-28 05:06:15','2025-11-28 05:06:15'),
	(42,'subscribe_placeholder','Your Email',NULL,NULL,'contain','text','Subscribe email placeholder','2025-11-28 05:06:15','2025-11-28 05:06:15'),
	(43,'subscribe_button_text','Subscribe Now',NULL,NULL,'contain','text','Subscribe button text','2025-11-28 05:06:15','2025-11-28 05:06:15'),
	(44,'footer_about_link_text','Read More',NULL,NULL,'contain','text','Footer about link text','2025-11-28 05:06:15','2025-11-28 05:06:15'),
	(45,'copyright_text','Samsae Store',NULL,NULL,'contain','text','Copyright text','2025-11-28 05:06:15','2025-11-28 05:06:15'),
	(46,'copyright_year','2024',NULL,NULL,'contain','text','Copyright year','2025-11-28 05:06:15','2025-11-28 05:06:15'),
	(48,'copyright_designer_url','https://htmlcodex.com',NULL,NULL,'contain','text','Designer credit URL','2025-11-28 05:06:15','2025-11-28 05:06:15'),
	(49,'copyright_distributor_text','Distributed By Samsaestore',NULL,NULL,'contain','text','Distributor credit text','2025-11-28 05:06:15','2025-11-28 05:06:15'),
	(50,'copyright_distributor_url','https://themewagon.com',NULL,NULL,'contain','text','Distributor credit URL','2025-11-28 05:06:15','2025-11-28 05:45:47'),
	(51,'contact_section_title','Contact',NULL,NULL,'contain','text','Contact section title','2025-11-28 05:06:15','2025-11-28 05:06:15'),
	(52,'contact_payment_title','Payment Accepted',NULL,NULL,'contain','text','Payment methods title','2025-11-28 05:06:15','2025-11-28 05:06:15'),
	(53,'footer_menu_columns','2',NULL,NULL,'contain','number','Number of footer menu columns to display','2025-11-28 05:09:08','2025-11-28 05:09:08'),
	(54,'footer_menu_show_all','true',NULL,NULL,'contain','boolean','Show all footer menus (true) or limit to specific number (false)','2025-11-28 05:09:08','2025-11-28 05:09:08'),
	(55,'currency_symbol','Rp.',NULL,NULL,'contain','text','Currency symbol for prices','2025-11-28 05:12:47','2025-11-28 05:12:47'),
	(56,'product_default_description','Fresh product from Samsae.',NULL,NULL,'contain','textarea','Default product description when none is provided','2025-11-28 05:12:47','2025-11-28 05:12:47'),
	(57,'product_badge_text','Product',NULL,NULL,'contain','text','Text displayed on product badges','2025-11-28 05:12:47','2025-11-28 05:12:47'),
	(58,'add_to_cart_text','Add to cart',NULL,NULL,'contain','text','Add to cart button text','2025-11-28 05:12:47','2025-11-28 05:12:47'),
	(59,'quantity_placeholder','Qty',NULL,NULL,'contain','text','Quantity input placeholder','2025-11-28 05:12:47','2025-11-28 05:12:47'),
	(60,'product_detail_title','Shop Detail',NULL,NULL,'contain','text','Product detail page title','2025-11-28 05:12:47','2025-11-28 05:12:47'),
	(61,'product_category_label','Category',NULL,NULL,'contain','text','Product category label','2025-11-28 05:12:47','2025-11-28 05:12:47'),
	(62,'product_description_tab','Description',NULL,NULL,'contain','text','Product description tab text','2025-11-28 05:12:47','2025-11-28 05:12:47'),
	(63,'product_reviews_tab','Reviews',NULL,NULL,'contain','text','Product reviews tab text','2025-11-28 05:12:47','2025-11-28 05:12:47'),
	(64,'price_label','Harga',NULL,NULL,'contain','text','Price label text','2025-11-28 05:12:47','2025-11-28 05:12:47'),
	(65,'unit_label','Unit',NULL,NULL,'contain','text','Unit label text','2025-11-28 05:12:47','2025-11-28 05:12:47'),
	(66,'stock_label','Stock',NULL,NULL,'contain','text','Stock label text','2025-11-28 05:12:47','2025-11-28 05:12:47'),
	(67,'size_label','Size',NULL,NULL,'contain','text','Size label text','2025-11-28 05:12:47','2025-11-28 05:12:47'),
	(68,'color_label','Color',NULL,NULL,'contain','text','Color label text','2025-11-28 05:12:47','2025-11-28 05:12:47'),
	(69,'weight_label','Weight',NULL,NULL,'contain','text','Weight label text','2025-11-28 05:12:47','2025-11-28 05:12:47'),
	(70,'breadcrumb_home_text','Home',NULL,NULL,'contain','text','Breadcrumb home text','2025-11-28 05:12:47','2025-11-28 05:12:47'),
	(71,'breadcrumb_pages_text','Pages',NULL,NULL,'contain','text','Breadcrumb pages text','2025-11-28 05:12:47','2025-11-28 05:12:47'),
	(72,'breadcrumb_shop_detail_text','Shop Detail',NULL,NULL,'contain','text','Breadcrumb shop detail text','2025-11-28 05:12:47','2025-11-28 05:12:47'),
	(73,'breadcrumb_contact_text','Contact',NULL,NULL,'contain','text','Breadcrumb contact text','2025-11-28 05:12:47','2025-11-28 05:12:47'),
	(74,'contact_page_title','Contact',NULL,NULL,'contain','text','Contact page title','2025-11-28 05:12:47','2025-11-28 05:12:47'),
	(75,'contact_form_title','Get in touch',NULL,NULL,'contain','text','Contact form title','2025-11-28 05:12:47','2025-11-28 05:12:47'),
	(76,'contact_form_description','The contact form is currently inactive. Get a functional and working contact form with Ajax & PHP in a few minutes. Just copy and paste the files, add a little code and you\'re done.',NULL,NULL,'contain','textarea','Contact form description','2025-11-28 05:12:47','2025-11-28 05:12:47'),
	(77,'contact_form_link_text','Download Now',NULL,NULL,'contain','text','Contact form link text','2025-11-28 05:12:47','2025-11-28 05:12:47'),
	(78,'contact_form_link_url','https://htmlcodex.com/contact-form',NULL,NULL,'contain','text','Contact form link URL','2025-11-28 05:12:47','2025-11-28 05:12:47'),
	(79,'no_products_found_text','No products found.',NULL,NULL,'contain','text','Message when no products are found','2025-11-28 05:12:47','2025-11-28 05:12:47'),
	(80,'no_category_products_text','No products in',NULL,NULL,'contain','text','Message when no products in category','2025-11-28 05:12:47','2025-11-28 05:12:47'),
	(81,'contact_name_placeholder','Your Name',NULL,NULL,'contain','text','Contact form name placeholder','2025-11-28 05:15:40','2025-11-28 05:15:40'),
	(82,'contact_email_placeholder','Enter Your Email',NULL,NULL,'contain','text','Contact form email placeholder','2025-11-28 05:15:40','2025-11-28 05:15:40'),
	(83,'contact_message_placeholder','Your Message',NULL,NULL,'contain','text','Contact form message placeholder','2025-11-28 05:15:40','2025-11-28 05:15:40'),
	(84,'contact_submit_button','Submit',NULL,NULL,'contain','text','Contact form submit button text','2025-11-28 05:15:40','2025-11-28 05:15:40'),
	(85,'contact_address_title','Address',NULL,NULL,'contain','text','Contact page address title','2025-11-28 05:15:40','2025-11-28 05:15:40'),
	(86,'contact_email_title','Mail Us',NULL,NULL,'contain','text','Contact page email title','2025-11-28 05:15:40','2025-11-28 05:15:40'),
	(87,'contact_phone_title','Telephone',NULL,NULL,'contain','text','Contact page phone title','2025-11-28 05:15:40','2025-11-28 05:15:40'),
	(88,'mitra_dashboard_title','Dashboard Mitra',NULL,NULL,'contain','text','Mitra dashboard page title','2025-11-28 05:15:40','2025-11-28 05:15:40'),
	(89,'mitra_welcome_message','Selamat datang',NULL,NULL,'contain','text','Mitra dashboard welcome message','2025-11-28 05:15:40','2025-11-28 05:15:40'),
	(90,'mitra_dashboard_subtitle','Berikut ringkasan aktivitas akun mitra Anda.',NULL,NULL,'contain','text','Mitra dashboard subtitle','2025-11-28 05:15:40','2025-11-28 05:15:40'),
	(91,'mitra_account_status_title','Status Akun',NULL,NULL,'contain','text','Mitra account status title','2025-11-28 05:15:40','2025-11-28 05:15:40'),
	(92,'mitra_verified_text','Terverifikasi',NULL,NULL,'contain','text','Mitra verified status text','2025-11-28 05:15:40','2025-11-28 05:15:40'),
	(93,'mitra_verified_description','Akun Anda sudah diverifikasi admin. Anda bisa melakukan pemesanan grosir.',NULL,NULL,'contain','textarea','Mitra verified status description','2025-11-28 05:15:40','2025-11-28 05:15:40'),
	(94,'mitra_pending_text','Menunggu Verifikasi',NULL,NULL,'contain','text','Mitra pending verification text','2025-11-28 05:15:40','2025-11-28 05:15:40'),
	(95,'mitra_pending_description','Akun Anda sedang menunggu verifikasi admin sebelum dapat mengakses harga grosir.',NULL,NULL,'contain','textarea','Mitra pending verification description','2025-11-28 05:15:40','2025-11-28 05:15:40'),
	(96,'mitra_orders_title','Pesanan Besar Bulan Ini',NULL,NULL,'contain','text','Mitra orders section title','2025-11-28 05:15:40','2025-11-28 05:15:40'),
	(97,'mitra_orders_description','Integrasi dengan data pesanan akan ditambahkan kemudian.',NULL,NULL,'contain','text','Mitra orders description','2025-11-28 05:15:40','2025-11-28 05:15:40'),
	(98,'mitra_sales_target_title','Target Penjualan',NULL,NULL,'contain','text','Mitra sales target title','2025-11-28 05:15:40','2025-11-28 05:15:40'),
	(99,'mitra_target_label','Target bulan ini',NULL,NULL,'contain','text','Mitra target label','2025-11-28 05:15:40','2025-11-28 05:15:40'),
	(100,'mitra_no_target_set','Belum ditetapkan',NULL,NULL,'contain','text','Mitra no target set text','2025-11-28 05:15:40','2025-11-28 05:15:40'),
	(101,'mitra_target_description','Fitur kuota/target penjualan akan dihubungkan ke data penjualan di tahap berikutnya.',NULL,NULL,'contain','textarea','Mitra target description','2025-11-28 05:15:40','2025-11-28 05:15:40'),
	(102,'mitra_order_history_title','Riwayat Pemesanan',NULL,NULL,'contain','text','Mitra order history title','2025-11-28 05:15:40','2025-11-28 05:15:40'),
	(103,'mitra_order_history_description','Tabel riwayat pemesanan mitra akan ditampilkan di sini setelah modul pesanan selesai dibuat.',NULL,NULL,'contain','textarea','Mitra order history description','2025-11-28 05:15:40','2025-11-28 05:15:40'),
	(104,'nav_home_text','Beranda',NULL,NULL,'contain','text','Navigation home text','2025-11-28 05:15:40','2025-11-28 05:15:40'),
	(105,'nav_admin_dashboard_text','Dashboard Admin',NULL,NULL,'contain','text','Navigation admin dashboard text','2025-11-28 05:15:40','2025-11-28 05:15:40'),
	(106,'nav_mitra_dashboard_text','Dashboard Mitra',NULL,NULL,'contain','text','Navigation mitra dashboard text','2025-11-28 05:15:40','2025-11-28 05:15:40'),
	(107,'nav_logout_text','Logout',NULL,NULL,'contain','text','Navigation logout text','2025-11-28 05:15:40','2025-11-28 05:15:40'),
	(108,'nav_login_text','Login',NULL,NULL,'contain','text','Navigation login text','2025-11-28 05:15:40','2025-11-28 05:15:40'),
	(109,'nav_register_text','Daftar',NULL,NULL,'contain','text','Navigation register text','2025-11-28 05:15:40','2025-11-28 05:15:40'),
	(110,'nav_mitra_register_text','Daftar Mitra',NULL,NULL,'contain','text','Navigation mitra register text','2025-11-28 05:15:40','2025-11-28 05:15:40'),
	(111,'site_name','Samsae Store',NULL,NULL,'contain','text','Website name for SEO and branding','2025-11-28 05:45:47','2025-11-28 05:45:47'),
	(112,'copyright_designer_text','Designed By HTML Codex',NULL,NULL,'contain','text','Designer credit text','2025-11-28 05:45:47','2025-11-28 05:45:47');

/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table shipping_costs
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shipping_costs`;

CREATE TABLE `shipping_costs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `shipping_method_id` bigint(20) unsigned NOT NULL,
  `origin_city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `destination_city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cost` decimal(10,0) NOT NULL,
  `estimated_days` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `min_weight` decimal(8,2) NOT NULL DEFAULT '0.00',
  `max_weight` decimal(8,2) NOT NULL DEFAULT '50.00',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `shipping_cities_index` (`origin_city`,`destination_city`),
  KEY `shipping_method_index` (`shipping_method_id`),
  CONSTRAINT `shipping_costs_shipping_method_id_foreign` FOREIGN KEY (`shipping_method_id`) REFERENCES `shipping_methods` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table shipping_methods
# ------------------------------------------------------------

DROP TABLE IF EXISTS `shipping_methods`;

CREATE TABLE `shipping_methods` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('instant','same_day','regular','express') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'regular',
  `logo_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `service_areas` json DEFAULT NULL,
  `max_distance_km` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `shipping_methods_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table social_links
# ------------------------------------------------------------

DROP TABLE IF EXISTS `social_links`;

CREATE TABLE `social_links` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `platform` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon_class` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int(10) unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `social_links` WRITE;
/*!40000 ALTER TABLE `social_links` DISABLE KEYS */;

INSERT INTO `social_links` (`id`, `platform`, `url`, `icon_class`, `sort_order`, `is_active`, `created_at`, `updated_at`)
VALUES
	(1,'twitter','https://twitter.com/','fab fa-twitter',1,1,'2025-11-24 07:00:58','2025-11-24 07:00:58'),
	(2,'facebook','https://facebook.com/','fab fa-facebook-f',2,1,'2025-11-24 07:00:58','2025-11-24 07:00:58'),
	(3,'youtube','https://youtube.com/','fab fa-youtube',3,1,'2025-11-24 07:00:58','2025-11-24 07:00:58'),
	(4,'instagram','https://instagram.com/','fab fa-instagram',4,1,'2025-11-24 07:00:58','2025-11-24 07:00:58');

/*!40000 ALTER TABLE `social_links` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table testimonials
# ------------------------------------------------------------

DROP TABLE IF EXISTS `testimonials`;

CREATE TABLE `testimonials` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `author_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `author_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `rating` tinyint(3) unsigned NOT NULL DEFAULT '5',
  `sort_order` int(10) unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `testimonials` WRITE;
/*!40000 ALTER TABLE `testimonials` DISABLE KEYS */;

INSERT INTO `testimonials` (`id`, `author_name`, `author_title`, `avatar_path`, `content`, `rating`, `sort_order`, `is_active`, `created_at`, `updated_at`)
VALUES
	(1,'Ari','Customer',NULL,'Produk fresh dan pengiriman cepat!',5,1,1,'2025-11-24 07:00:58','2025-11-24 07:00:58'),
	(2,'Bima','Customer',NULL,'Harga terjangkau, kualitas bagus.',5,2,1,'2025-11-24 07:00:58','2025-11-24 07:00:58'),
	(3,'Citra','Customer',NULL,'Pilihan buah dan sayur lengkap!',4,3,1,'2025-11-24 07:00:58','2025-11-24 07:00:58');

/*!40000 ALTER TABLE `testimonials` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `company_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_address` text COLLATE utf8mb4_unicode_ci,
  `company_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `npwp` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT '0',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','mitra','customer') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'customer',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `address`, `company_name`, `company_address`, `company_phone`, `npwp`, `is_verified`, `email_verified_at`, `password`, `role`, `remember_token`, `created_at`, `updated_at`, `deleted_at`)
VALUES
	(3,'Test User','test@example.com',NULL,NULL,NULL,NULL,NULL,NULL,0,'2025-11-26 05:04:33','$2y$12$NYOGjNmQJsMrazrT5g96bu8voip58bDRKLPHfg..rMbUuertUAnqG','customer','fDHsRsX1ZK','2025-11-26 05:04:33','2025-11-26 05:04:33',NULL),
	(4,'Admin','admin@example.com',NULL,NULL,NULL,NULL,NULL,NULL,1,'2025-11-26 05:15:05','$2y$12$cA9TkxNCDw4NdkXf85NvFeJJHIldAloABYfRqpsLzJdXJMrewdUYK','admin',NULL,'2025-11-26 05:15:05','2025-11-26 05:15:05',NULL),
	(5,'Mitra Contoh','mitra@example.com','081234567890','Alamat mitra contoh','Toko Mitra Contoh','Alamat toko mitra contoh','0211234567','12.345.678.9-012.000',1,'2025-11-26 05:15:05','$2y$12$pOwaenLRp/p7OTXfiV5Nfu2UJNuqKiPcJUevbKWQorfqEMyOjy7EW','mitra',NULL,'2025-11-26 05:15:05','2025-11-28 06:44:30',NULL),
	(6,'Customer Contoh','customer@example.com','089876543210','Alamat customer contoh',NULL,NULL,NULL,NULL,1,'2025-11-26 05:15:06','$2y$12$R9KDCxbvf6YXCodOvdOqwOFRerfDkOP7I4d6SqHb.mbs6DjWENTKu','customer',NULL,'2025-11-26 05:15:06','2025-11-26 05:15:06',NULL);

/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
