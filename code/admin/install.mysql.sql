CREATE TABLE IF NOT EXISTS `#__api_keys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `hash` varchar(255) NOT NULL,
  `domain` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `published` int(11) NOT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `#__api_logs` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `hash` varchar(20) NOT NULL,
  `ip_address` varchar(20) NOT NULL,
  `time` int(11) NOT NULL,
  `request` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `search` (`hash`,`ip_address`,`time`)
);