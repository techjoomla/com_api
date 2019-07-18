ALTER TABLE `#__api_keys` ENGINE = InnoDB;
ALTER TABLE `#__api_logs` ENGINE = InnoDB;

ALTER TABLE `#__api_keys` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__api_logs` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
