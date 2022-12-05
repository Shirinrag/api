ALTER TABLE `pa_users` ADD `otp` INT NULL AFTER `phoneNo`;
ALTER TABLE `tbl_parking_place` ADD `del_status` INT NOT NULL DEFAULT '1' AFTER `slots`;
ALTER TABLE `tbl_parking_place` ADD `status` ENUM('0','1') NOT NULL AFTER `slots`;
