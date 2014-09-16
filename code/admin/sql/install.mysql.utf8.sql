CREATE TABLE IF NOT EXISTS `#__api_keys` (
`id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `hash` varchar(255) NOT NULL,
  `domain` varchar(255) NOT NULL,
  `state` tinyint(1) NOT NULL,
  `checked_out` int(11) NOT NULL,
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL,
  `last_used` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `per_hour` int(10) NOT NULL,
  PRIMARY KEY (`id`)  
) DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__api_logs` (
`id` bigint(20) NOT NULL AUTO_INCREMENT,
  `hash` varchar(255) NOT NULL,
  `ip_address` varchar(20) NOT NULL,
  `time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `request` varchar(255) NOT NULL,
  `post_data` text NOT NULL,
  PRIMARY KEY (`id`)  
) DEFAULT CHARSET=utf8;
