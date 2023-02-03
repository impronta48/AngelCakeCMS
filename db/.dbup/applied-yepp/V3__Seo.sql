ALTER TABLE `articles`
ADD COLUMN `keywords` VARCHAR(255) NULL,
ADD COLUMN `description` VARCHAR(255) NULL AFTER `keywords`,
ADD COLUMN `url_canonical` VARCHAR(255) NULL AFTER `description`;
