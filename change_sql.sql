ALTER TABLE `pa_users` ADD `otp` INT NULL AFTER `phoneNo`;
ALTER TABLE `tbl_parking_place` ADD `del_status` INT NOT NULL DEFAULT '1' AFTER `slots`;
ALTER TABLE `tbl_parking_place` ADD `status` ENUM('0','1') NOT NULL AFTER `slots`;
ALTER TABLE `tbl_parking_place` ADD `fk_place_status_id` INT NULL DEFAULT NULL AFTER `slots`;
ALTER TABLE `pa_users` CHANGE `address` `address` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `pa_users` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT, CHANGE `userName` `userName` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `firstName` `firstName` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `lastName` `lastName` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `email` `email` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `phoneNo` `phoneNo` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `password` `password` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `token` `token` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `password_reset_code` `password_reset_code` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `last_ip` `last_ip` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `referal_code` `referal_code` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `referenced_by` `referenced_by` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `device_id` `device_id` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `refrel_applied` `refrel_applied` INT(7) NULL DEFAULT NULL, CHANGE `verifier_referral_id` `verifier_referral_id` INT(11) NULL DEFAULT NULL, CHANGE `device_type` `device_type` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `notifn_topic` `notifn_topic` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `created_at` `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE `updated_at` `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, CHANGE `app_version` `app_version` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL, CHANGE `app_build_no` `app_build_no` INT(11) NULL DEFAULT NULL;

ALTER TABLE `pa_users` ADD INDEX(`id`);
ALTER TABLE `pa_users` ADD INDEX(`userName`);
ALTER TABLE `pa_users` ADD `del_status` INT NOT NULL DEFAULT '1' AFTER `app_build_no`;
ALTER TABLE `tbl_parking_place_status` ADD `del_status` INT NOT NULL DEFAULT '1' AFTER `status`;
ALTER TABLE `tbl_parking_place` ADD `fk_parking_price_type` INT NULL DEFAULT NULL AFTER `fk_place_status_id`;
ALTER TABLE `tbl_parking_place` ADD `ext_price` INT NULL DEFAULT NULL AFTER `fk_parking_price_type`;
ALTER TABLE `tbl_device` ADD `del_status` INT NOT NULL DEFAULT '1' AFTER `status`;

-- 23/12/2022
ALTER TABLE `tbl_duty_allocation` ADD INDEX(`id`);
ALTER TABLE `tbl_duty_allocation` ADD INDEX(`fk_place_id`);
ALTER TABLE `tbl_duty_allocation` ADD INDEX(`fk_verifier_id`);
ALTER TABLE `tbl_duty_allocation` ADD `date` DATE NULL DEFAULT NULL AFTER `fk_verifier_id`;
ALTER TABLE `tbl_duty_allocation` CHANGE `date` `date` VARCHAR(20) NULL DEFAULT NULL;
--09/01/2023

INSERT INTO `tbl_user_type` (`id`, `user_type`, `status`, `created_at`, `updated_at`) VALUES (NULL, 'POS Verifier', '1', current_timestamp(), current_timestamp());
ALTER TABLE `tbl_parking_place` ADD `per_hour_charges` DOUBLE NULL DEFAULT NULL AFTER `ext_price`;

--13/01/2023

ALTER TABLE `pa_users` ADD `aadhaar_card` LONGTEXT NULL DEFAULT NULL AFTER `image`, ADD `pan_card` LONGTEXT NULL DEFAULT NULL AFTER `aadhaar_card`;
--16/01/2023
ALTER TABLE `pa_users` ADD `pos_device_id` LONGTEXT NULL DEFAULT NULL AFTER `pan_card`;
ALTER TABLE `pa_users` CHANGE `pos_device_id` `pos_device_id` BIGINT NULL DEFAULT NULL;

--17/01/2023
ALTER TABLE `tbl_pos_booking` ADD `book_status` INT NULL DEFAULT NULL COMMENT '1. Check-in \r\n2. Check-out' AFTER `longitude`;

--19/01/2023

ALTER TABLE `tbl_sensor` CHANGE `sensor_time` `sensor_time` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;



ALTER TABLE `tbl_parking_place` ADD `parking_place_type` INT NULL DEFAULT NULL COMMENT ' 1: Normal Vendor 2: POS Vendor 3: Both' AFTER `fk_city_id`;
ALTER TABLE `tbl_parking_place` ADD `parking_place_type` INT NULL DEFAULT NULL COMMENT ' 1: Normal Vendor 2: POS Vendor 3: Both' AFTER `fk_city_id`;
