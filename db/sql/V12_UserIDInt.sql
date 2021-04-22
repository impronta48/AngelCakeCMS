ALTER TABLE `articles`
CHANGE `user_id` `old_user_id` char(36) COLLATE 'utf8mb4_0900_ai_ci' NOT NULL AFTER `archived`,
ADD `user_id` int NOT NULL AFTER `old_user_id`;

ALTER TABLE `users`
CHANGE `id` `oldid` char(36) COLLATE 'latin1_swedish_ci' NOT NULL FIRST,
ADD `id` int NOT NULL AUTO_INCREMENT UNIQUE AFTER `oldid`;

update articles set user_id = 3;
