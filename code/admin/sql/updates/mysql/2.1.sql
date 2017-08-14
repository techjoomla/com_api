ALTER TABLE #__api_keys ADD INDEX `userid` (userid);
ALTER TABLE #__api_keys ADD INDEX `hash` (hash);
ALTER TABLE #__api_logs ADD INDEX `hash` (hash);
