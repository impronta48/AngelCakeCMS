ALTER TABLE `participants`
CHANGE COLUMN `modified` `modified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `created`,
CHANGE COLUMN `destination_id` `destination_id` INT NULL AFTER `event_id`,
CHANGE COLUMN `pob` `pob` VARCHAR(255) NULL AFTER `destination_id`,
CHANGE COLUMN `city` `city` VARCHAR(255) NULL AFTER `pob`,
CHANGE COLUMN `address` `address` VARCHAR(255) NULL AFTER `city`,
CHANGE COLUMN `facebook` `facebook` VARCHAR(255) NULL AFTER `address`,
CHANGE COLUMN `ente` `ente` VARCHAR(255) NULL AFTER `facebook`,
CHANGE COLUMN `forum_id_prima_scelta` `forum_id_prima_scelta` INT NULL AFTER `ente`,
CHANGE COLUMN `forum_id_seconda_scelta` `forum_id_seconda_scelta` INT NULL AFTER `forum_id_prima_scelta`,
ADD COLUMN `amount` DECIMAL NULL DEFAULT NULL AFTER `forum_id_seconda_scelta`,
ADD COLUMN `transaction_id` VARCHAR(255) NULL DEFAULT NULL AFTER `amount`,
ADD COLUMN `transaction_date` DATETIME NULL DEFAULT NULL AFTER `transaction_id`,
ADD COLUMN `renewal_date` DATETIME NULL DEFAULT NULL AFTER `transaction_date`;
