ALTER TABLE bikesquare_b2b_cake3.participants
DROP `diet`,
CHANGE `modified` `modified` timestamp NULL AFTER `created`,
DROP `destination_id`,
DROP `pob`,
DROP `address`,
ADD `destination` varchar(255) COLLATE 'latin1_swedish_ci' NOT NULL AFTER `facebook`,
ADD `experience` text COLLATE 'latin1_swedish_ci' NOT NULL AFTER `destination`,
ADD `past` text COLLATE 'latin1_swedish_ci' NOT NULL AFTER `experience`,
DROP `ente`,
DROP `forum_id_prima_scelta`,
DROP `forum_id_seconda_scelta`;