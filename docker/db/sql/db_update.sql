ALTER TABLE `emails` ADD UNIQUE `email` (`email`);
ALTER TABLE `karma8_db`.`users` ADD COLUMN `notify` INT(2) DEFAULT '0' NOT NULL AFTER `confirmed`;
