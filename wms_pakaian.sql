/*
SQLyog Ultimate v12.4.3 (64 bit)
MySQL - 10.4.32-MariaDB : Database - gudang_pakaian_db
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`gudang_pakaian_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `gudang_pakaian_db`;

/*Table structure for table `inbound_details` */

DROP TABLE IF EXISTS `inbound_details`;

CREATE TABLE `inbound_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `inbound_id` bigint(20) unsigned NOT NULL,
  `variant_id` bigint(20) unsigned NOT NULL,
  `qty` int(11) NOT NULL,
  `unit_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `inbound_details_inbound_id_foreign` (`inbound_id`),
  KEY `inbound_details_variant_id_foreign` (`variant_id`),
  CONSTRAINT `inbound_details_inbound_id_foreign` FOREIGN KEY (`inbound_id`) REFERENCES `inbounds` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inbound_details_variant_id_foreign` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `inbound_details` */

insert  into `inbound_details`(`id`,`inbound_id`,`variant_id`,`qty`,`unit_price`,`subtotal`,`created_at`,`updated_at`) values 
(4,2,4,100,85000.00,8500000.00,'2026-01-02 12:29:19','2026-01-02 12:29:19'),
(5,2,5,100,95000.00,9500000.00,'2026-01-02 12:29:19','2026-01-02 12:29:19'),
(6,2,6,100,105000.00,10500000.00,'2026-01-02 12:29:19','2026-01-02 12:29:19'),
(7,3,4,25,85000.00,2125000.00,'2026-01-02 13:02:35','2026-01-02 13:02:35'),
(11,4,5,30,95000.00,2850000.00,'2026-01-02 16:19:40','2026-01-02 16:19:40'),
(18,5,7,100,80000.00,8000000.00,'2026-01-02 18:25:06','2026-01-02 18:25:06'),
(19,5,8,100,85000.00,8500000.00,'2026-01-02 18:25:06','2026-01-02 18:25:06'),
(20,5,9,100,90000.00,9000000.00,'2026-01-02 18:25:06','2026-01-02 18:25:06');

/*Table structure for table `inbounds` */

DROP TABLE IF EXISTS `inbounds`;

CREATE TABLE `inbounds` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `inbound_date` date NOT NULL,
  `photo_proof` varchar(255) DEFAULT NULL,
  `status` enum('Requested','Revision','Approved','Cancel') NOT NULL DEFAULT 'Requested',
  `note` text DEFAULT NULL,
  `total_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `user_id` bigint(20) unsigned NOT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `inbounds_user_id_foreign` (`user_id`),
  KEY `inbounds_approved_by_foreign` (`approved_by`),
  CONSTRAINT `inbounds_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`),
  CONSTRAINT `inbounds_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `inbounds` */

insert  into `inbounds`(`id`,`inbound_date`,`photo_proof`,`status`,`note`,`total_amount`,`user_id`,`approved_by`,`approved_at`,`created_at`,`updated_at`) values 
(2,'2026-01-02','inbounds/GWb2qE6DTHMFsFrgdKdatHE4MBA5NooOiRDFeTt0.jpg','Approved',NULL,28500000.00,7,6,'2026-01-02 12:30:03','2026-01-02 12:29:19','2026-01-02 12:30:03'),
(3,'2026-01-02','inbounds/cwxNxMziGXDEunkE863caI563PMkhPIKKgvJMPAu.jpg','Approved',NULL,2125000.00,7,6,'2026-01-02 13:10:04','2026-01-02 13:02:35','2026-01-02 13:10:04'),
(4,'2026-01-02','inbounds/seYYzV4dcdNId6lhqCqU9k0JoxMoyWK8UO1OMLd8.jpg','Requested','itu harusnya pembelian ada 30, perbaiki',2850000.00,7,NULL,NULL,'2026-01-02 14:38:52','2026-01-02 16:19:40'),
(5,'2026-01-02','inbounds/KzM4rNcuVRz0Y3DiKwC80Wnn1EZMcGJsJ3YQevEu.jpg','Approved','tes revisi',25500000.00,7,6,'2026-01-02 18:25:26','2026-01-02 18:23:47','2026-01-02 18:25:26');

/*Table structure for table `migrations` */

DROP TABLE IF EXISTS `migrations`;

CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `migrations` */

insert  into `migrations`(`id`,`migration`,`batch`) values 
(4,'2025_12_26_082615_create_all_warehouse_tables',1),
(5,'2025_12_27_165020_add_price_to_inbounds_table',2),
(6,'2025_12_27_174537_add_price_to_outbounds_table',3),
(7,'2025_12_29_085640_upgrade_outbound_features',4),
(8,'2025_12_29_135721_add_percent_columns_to_outbounds',5),
(9,'2025_12_30_135144_create_permission_tables',6),
(10,'2026_01_02_151810_add_note_to_inbounds_table',7),
(11,'2026_01_02_162529_add_is_active_to_products_table',8);

/*Table structure for table `model_has_permissions` */

DROP TABLE IF EXISTS `model_has_permissions`;

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `model_has_permissions` */

/*Table structure for table `model_has_roles` */

DROP TABLE IF EXISTS `model_has_roles`;

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `model_has_roles` */

insert  into `model_has_roles`(`role_id`,`model_type`,`model_id`) values 
(5,'App\\Models\\User',7),
(6,'App\\Models\\User',8),
(7,'App\\Models\\User',6),
(8,'App\\Models\\User',5);

/*Table structure for table `outbound_details` */

DROP TABLE IF EXISTS `outbound_details`;

CREATE TABLE `outbound_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `outbound_id` bigint(20) unsigned NOT NULL,
  `variant_id` bigint(20) unsigned NOT NULL,
  `qty` int(11) NOT NULL,
  `unit_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `discount_percent` decimal(5,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `outbound_details_outbound_id_foreign` (`outbound_id`),
  KEY `outbound_details_variant_id_foreign` (`variant_id`),
  CONSTRAINT `outbound_details_outbound_id_foreign` FOREIGN KEY (`outbound_id`) REFERENCES `outbounds` (`id`) ON DELETE CASCADE,
  CONSTRAINT `outbound_details_variant_id_foreign` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `outbound_details` */

insert  into `outbound_details`(`id`,`outbound_id`,`variant_id`,`qty`,`unit_price`,`discount_percent`,`discount_amount`,`subtotal`,`created_at`,`updated_at`) values 
(13,3,4,25,100000.00,5.00,5000.00,2375000.00,'2026-01-02 12:31:37','2026-01-02 12:31:37'),
(14,3,5,25,110000.00,5.00,5500.00,2612500.00,'2026-01-02 12:31:37','2026-01-02 12:31:37'),
(15,3,6,25,120000.00,5.00,6000.00,2850000.00,'2026-01-02 12:31:37','2026-01-02 12:31:37'),
(20,4,4,40,100000.00,10.00,10000.00,3600000.00,'2026-01-02 16:20:31','2026-01-02 16:20:31'),
(21,5,7,25,90000.00,5.00,4500.00,2137500.00,'2026-01-02 18:26:39','2026-01-02 18:26:39'),
(22,5,8,25,95000.00,5.00,4750.00,2256250.00,'2026-01-02 18:26:39','2026-01-02 18:26:39'),
(23,5,9,25,100000.00,5.00,5000.00,2375000.00,'2026-01-02 18:26:39','2026-01-02 18:26:39');

/*Table structure for table `outbounds` */

DROP TABLE IF EXISTS `outbounds`;

CREATE TABLE `outbounds` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `outbound_date` date NOT NULL,
  `delivery_data` text DEFAULT NULL,
  `photo_proof` varchar(255) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `status` enum('Requested','Sent','Cancel','Revision') NOT NULL DEFAULT 'Requested',
  `total_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `tax_rate` decimal(5,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `grand_total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `user_id` bigint(20) unsigned NOT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `outbounds_user_id_foreign` (`user_id`),
  KEY `outbounds_approved_by_foreign` (`approved_by`),
  CONSTRAINT `outbounds_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`),
  CONSTRAINT `outbounds_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `outbounds` */

insert  into `outbounds`(`id`,`outbound_date`,`delivery_data`,`photo_proof`,`note`,`status`,`total_amount`,`tax_rate`,`tax_amount`,`grand_total`,`user_id`,`approved_by`,`approved_at`,`created_at`,`updated_at`) values 
(3,'2026-01-02','PTK - PUSAT CANTIK','outbounds/GQUoxQ0X3pbgTrEQaopKngtls0RZYD7DfMoOVOuM.jpg',NULL,'Sent',7837500.00,11.00,862125.00,8699625.00,8,6,'2026-01-02 12:32:11','2026-01-02 12:31:37','2026-01-02 12:32:11'),
(4,'2026-01-02','PTK - MATAHARI','outbounds/OFY6TgMDmDwzW98EsiOc7nV1FRrJvso2HnPO9DEl.jpg','jumlah belinya banyak, boleh kasih diskon 10%','Requested',3600000.00,11.00,396000.00,3996000.00,8,NULL,NULL,'2026-01-02 15:04:50','2026-01-02 16:20:31'),
(5,'2026-01-02','PTK - PUSAT CANTIK','outbounds/L8CGq2Y8b03CLBLqn4cEw7ryYapAPEZ3Ery2fciH.jpg',NULL,'Sent',6768750.00,11.00,744562.50,7513312.50,8,6,'2026-01-02 18:27:04','2026-01-02 18:26:39','2026-01-02 18:27:04');

/*Table structure for table `permissions` */

DROP TABLE IF EXISTS `permissions`;

CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `permissions` */

insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values 
(11,'view_dashboard','web','2026-01-02 10:48:47','2026-01-02 10:48:47'),
(12,'view_financials','web','2026-01-02 10:48:47','2026-01-02 10:48:47'),
(13,'manage_products','web','2026-01-02 10:48:47','2026-01-02 10:48:47'),
(14,'create_inbound','web','2026-01-02 10:48:47','2026-01-02 10:48:47'),
(15,'approve_inbound','web','2026-01-02 10:48:47','2026-01-02 10:48:47'),
(16,'create_outbound','web','2026-01-02 10:48:47','2026-01-02 10:48:47'),
(17,'approve_outbound','web','2026-01-02 10:48:47','2026-01-02 10:48:47'),
(18,'create_so','web','2026-01-02 10:48:47','2026-01-02 10:48:47'),
(19,'approve_so','web','2026-01-02 10:48:47','2026-01-02 10:48:47'),
(20,'manage_users','web','2026-01-02 10:48:47','2026-01-02 10:48:47');

/*Table structure for table `product_variants` */

DROP TABLE IF EXISTS `product_variants`;

CREATE TABLE `product_variants` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL,
  `sku_variant` varchar(50) NOT NULL,
  `size` varchar(10) NOT NULL,
  `color` varchar(20) NOT NULL,
  `price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `stock_qty` int(11) NOT NULL DEFAULT 0,
  `photo_variant` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_variants_sku_variant_unique` (`sku_variant`),
  KEY `product_variants_product_id_foreign` (`product_id`),
  CONSTRAINT `product_variants_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `product_variants` */

insert  into `product_variants`(`id`,`product_id`,`sku_variant`,`size`,`color`,`price`,`stock_qty`,`photo_variant`,`created_at`,`updated_at`) values 
(4,2,'KMJ-FLN01-MERAH-S','S','MERAH',100000.00,100,'variants/Yogz5Stmbgrf0XBlKzyDq2WA0eCZUg4tlC8i70Ye.jpg','2026-01-02 12:27:58','2026-01-02 13:10:04'),
(5,2,'KMJ-FLN01-MERAH-L','L','MERAH',110000.00,75,'variants/QFltL7xKjcIGdl0vPWi5wp2hoWG0yfGcUVnYZh0c.jpg','2026-01-02 12:27:58','2026-01-02 12:32:11'),
(6,2,'KMJ-FLN01-MERAH-XL','XL','MERAH',120000.00,75,'variants/bX32m7gzuYerp6MKyYUsSWJAq9OwIi0nrvwYLRsG.jpg','2026-01-02 12:27:58','2026-01-02 12:32:11'),
(7,3,'KMJ-KTK01-HITAM-S','S','HITAM',90000.00,75,'variants/39jwqbOpSqfEq7EuoGTUGTN85A0xxl1uP3fEDHja.jpg','2026-01-02 18:22:48','2026-01-02 18:27:04'),
(8,3,'KMJ-KTK01-PUTIH-L','L','PUTIH',95000.00,73,'variants/1gqDom7Ut2hIZdr2tcKEVXPFpgMpG2F4wRYQX9j3.jpg','2026-01-02 18:22:48','2026-01-02 18:28:58'),
(9,3,'KMJ-KTK01-MERAH-XL','XL','MERAH',100000.00,70,'variants/zgfcDsCQ619qw3Y1eMbS0HS0ePzsDe0OBxD4PbYW.jpg','2026-01-02 18:22:48','2026-01-02 18:28:58');

/*Table structure for table `products` */

DROP TABLE IF EXISTS `products`;

CREATE TABLE `products` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `sku_base` varchar(50) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `photo_main` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `products` */

insert  into `products`(`id`,`name`,`sku_base`,`is_active`,`photo_main`,`created_at`,`updated_at`) values 
(2,'Kemeja Flannel','KMJ-FLN01',1,'products/Hw5iknh6gU7nysxfAP3jSg8ia5Yccry46u7KNcq2.jpg','2026-01-02 12:27:58','2026-01-02 12:27:58'),
(3,'Kemeja Kotak','KMJ-KTK01',1,'products/umjTPxFWLPkGj7aCCsNDApCfXjNltx5ZDMGDorBg.jpg','2026-01-02 18:22:48','2026-01-02 18:22:48');

/*Table structure for table `role_has_permissions` */

DROP TABLE IF EXISTS `role_has_permissions`;

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `role_has_permissions` */

insert  into `role_has_permissions`(`permission_id`,`role_id`) values 
(11,5),
(11,6),
(11,7),
(11,8),
(12,8),
(13,7),
(13,8),
(14,5),
(14,8),
(15,7),
(15,8),
(16,6),
(16,8),
(17,7),
(17,8),
(18,5),
(18,6),
(18,7),
(18,8),
(19,7),
(19,8),
(20,8);

/*Table structure for table `roles` */

DROP TABLE IF EXISTS `roles`;

CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `roles` */

insert  into `roles`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values 
(5,'Staff Inbound','web','2026-01-02 10:48:47','2026-01-02 10:48:47'),
(6,'Staff Outbound','web','2026-01-02 10:48:47','2026-01-02 10:48:47'),
(7,'Supervisor','web','2026-01-02 10:48:47','2026-01-02 10:48:47'),
(8,'Super Admin','web','2026-01-02 10:48:48','2026-01-02 10:48:48');

/*Table structure for table `stock_opname_details` */

DROP TABLE IF EXISTS `stock_opname_details`;

CREATE TABLE `stock_opname_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `so_id` bigint(20) unsigned NOT NULL,
  `variant_id` bigint(20) unsigned NOT NULL,
  `qty_system` int(11) NOT NULL,
  `qty_actual` int(11) NOT NULL,
  `qty_diff` int(11) NOT NULL DEFAULT 0,
  `reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_opname_details_so_id_foreign` (`so_id`),
  KEY `stock_opname_details_variant_id_foreign` (`variant_id`),
  CONSTRAINT `stock_opname_details_so_id_foreign` FOREIGN KEY (`so_id`) REFERENCES `stock_opnames` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stock_opname_details_variant_id_foreign` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `stock_opname_details` */

insert  into `stock_opname_details`(`id`,`so_id`,`variant_id`,`qty_system`,`qty_actual`,`qty_diff`,`reason`,`created_at`,`updated_at`) values 
(15,5,4,100,97,-3,'3 sobek','2026-01-02 15:31:16','2026-01-02 15:31:16'),
(16,5,5,75,75,0,'baik','2026-01-02 15:31:16','2026-01-02 15:31:16'),
(17,5,6,75,75,0,'baik','2026-01-02 15:31:16','2026-01-02 15:31:16'),
(18,6,7,75,75,0,'baik','2026-01-02 18:28:11','2026-01-02 18:28:11'),
(19,6,8,75,73,-2,'2 sobek','2026-01-02 18:28:11','2026-01-02 18:28:11'),
(20,6,9,75,70,-5,'5 cacat','2026-01-02 18:28:11','2026-01-02 18:28:11');

/*Table structure for table `stock_opnames` */

DROP TABLE IF EXISTS `stock_opnames`;

CREATE TABLE `stock_opnames` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `so_date` date NOT NULL,
  `photo_proof` varchar(255) DEFAULT NULL,
  `status` enum('Requested','Approved','Cancel') NOT NULL DEFAULT 'Requested',
  `user_id` bigint(20) unsigned NOT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_opnames_user_id_foreign` (`user_id`),
  KEY `stock_opnames_approved_by_foreign` (`approved_by`),
  CONSTRAINT `stock_opnames_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`),
  CONSTRAINT `stock_opnames_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `stock_opnames` */

insert  into `stock_opnames`(`id`,`so_date`,`photo_proof`,`status`,`user_id`,`approved_by`,`approved_at`,`created_at`,`updated_at`) values 
(5,'2026-01-02','stock_opnames/QlJ9apZMrJKMcJ9GfkRpXYkiMi9xlHRP9JWyI91Y.jpg','Requested',7,NULL,NULL,'2026-01-02 13:16:47','2026-01-02 15:31:16'),
(6,'2026-01-02','stock_opnames/f2ZqoyTpIA6TREPKSDBnkkrozP16v8dM4paGsPwZ.jpg','Approved',8,6,'2026-01-02 18:28:58','2026-01-02 18:28:11','2026-01-02 18:28:58');

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `users` */

insert  into `users`(`id`,`name`,`username`,`password`,`remember_token`,`created_at`,`updated_at`) values 
(5,'Super Admin','admin','$2y$12$sYalcCjxMcMOv94g7QmiZu4/ZikukdmB6QgOQMMGveUEiLVLP/JWe',NULL,'2026-01-02 10:48:48','2026-01-02 10:48:48'),
(6,'Bryan Henrilsen','Bryan','$2y$12$97iuMrjPhFOYNzzncYhU0OUPA0OFAGPEeqc2WsYl7au7jgeqAGoGy',NULL,'2026-01-02 10:48:48','2026-01-02 17:33:09'),
(7,'Dhavid Bhertus','Dhavid','$2y$12$JWkpoPjLcssDA5btOQlWIOZxuCC/GViWhrjWnjoRk1ApBHAMUoWr2',NULL,'2026-01-02 10:48:48','2026-01-02 17:33:17'),
(8,'Felix Filbert','Felix','$2y$12$bboX2K5R6hvt53UjQRVUjuULC9/Ws1dB.20umWasVzBPeEGmX7Xna',NULL,'2026-01-02 10:48:48','2026-01-02 17:33:36');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
