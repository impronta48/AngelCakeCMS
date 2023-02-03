ALTER TABLE `articles`
	ADD COLUMN `slider` TINYINT(1) NULL DEFAULT '0' AFTER `promoted`;
