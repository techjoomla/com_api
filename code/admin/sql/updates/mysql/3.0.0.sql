ALTER TABLE `#__api_keys` CHANGE `userid` `userid` int(11) NOT NULL DEFAULT 0;
ALTER TABLE `#__api_keys` CHANGE `hash` `hash` varchar(255) NOT NULL DEFAULT '';
ALTER TABLE `#__api_keys` CHANGE `domain` `domain` varchar(255) NOT NULL DEFAULT '';
ALTER TABLE `#__api_keys` CHANGE `state` `state` tinyint(1) NOT NULL DEFAULT 0;
ALTER TABLE `#__api_keys` CHANGE `checked_out` `checked_out` int(11) NOT NULL DEFAULT 0;
ALTER TABLE `#__api_keys` CHANGE `checked_out_time` `checked_out_time` datetime DEFAULT NULL;
ALTER TABLE `#__api_keys` CHANGE `created` `created` datetime DEFAULT NULL;
ALTER TABLE `#__api_keys` CHANGE `created_by` `created_by` int(11) NOT NULL DEFAULT 0;
ALTER TABLE `#__api_keys` CHANGE `last_used` `last_used` datetime DEFAULT NULL;
ALTER TABLE `#__api_keys` CHANGE `per_hour` `per_hour` int(10) NOT NULL DEFAULT 0;

ALTER TABLE `#__api_logs` CHANGE `hash` `hash` varchar(255) NOT NULL DEFAULT '';
ALTER TABLE `#__api_logs` CHANGE `ip_address` `ip_address` varchar(20) NOT NULL DEFAULT '';
ALTER TABLE `#__api_logs` CHANGE `time` `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;
ALTER TABLE `#__api_logs` CHANGE `request_method` `request_method` varchar(20) NOT NULL DEFAULT '';
ALTER TABLE `#__api_logs` CHANGE `request` `request` varchar(255) NOT NULL DEFAULT '';
ALTER TABLE `#__api_logs` CHANGE `post_data` `post_data` text DEFAULT NULL;
