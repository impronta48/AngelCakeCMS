ALTER TABLE `users`
CHANGE `destination_id` `destination_id` int(11) NULL AFTER `created`;
CHANGE `password` `password` longtext COLLATE 'utf8mb4_unicode_ci' NULL AFTER `username`;