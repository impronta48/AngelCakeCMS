ALTER TABLE `articles`
CHANGE `user_id` `old_user_id` char(36) NOT NULL AFTER `archived`,
ADD `user_id` int NOT NULL AFTER `old_user_id`;

ALTER TABLE `users`
CHANGE `id` `oldid` char(36) NOT NULL FIRST,
ADD `id` int NOT NULL AUTO_INCREMENT UNIQUE AFTER `oldid`,
ADD `gmail` varchar(255) null,
ADD `fbmail` varchar(255) null;

update articles set user_id = 3;
