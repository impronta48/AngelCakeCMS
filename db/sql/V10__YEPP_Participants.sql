ALTER TABLE participants
ADD COLUMN `email_parent` VARCHAR(255) NULL AFTER `renewal_date`,
ADD COLUMN `terms` TINYINT(1) NULL AFTER `email_parent`;
