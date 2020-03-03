<?php
// HTTP

define('HTTP_SERVER', 'http://watchtag.rocketon.ru/');

// HTTPS
define('HTTPS_SERVER', 'http://watchtag.rocketon.ru/');

// DIR
// define('DIR_APPLICATION', '/var/www/rocketon/data/www/watchtag.rocketon.ru/upload/catalog/');
// define('DIR_SYSTEM', '/var/www/rocketon/data/www/watchtag.rocketon.ru/upload/system/');
// define('DIR_IMAGE', '/var/www/rocketon/data/www/watchtag.rocketon.ru/upload/image/');
define('DIR_APPLICATION', 'W:/domains/watchtag.rocketon.ru/catalog/');
define('DIR_SYSTEM', 'W:/domains/watchtag.rocketon.ru/system/');
define('DIR_IMAGE', 'W:/domains/watchtag.rocketon.ru/image/');
define('DIR_STORAGE', DIR_SYSTEM . 'storage/');
define('DIR_LANGUAGE', DIR_APPLICATION . 'language/');
define('DIR_TEMPLATE', DIR_APPLICATION . 'view/theme/');
define('DIR_CONFIG', DIR_SYSTEM . 'config/');
define('DIR_CACHE', DIR_STORAGE . 'cache/');
define('DIR_DOWNLOAD', DIR_STORAGE . 'download/');
define('DIR_LOGS', DIR_STORAGE . 'logs/');
define('DIR_MODIFICATION', DIR_STORAGE . 'modification/');
define('DIR_SESSION', DIR_STORAGE . 'session/');
define('DIR_UPLOAD', DIR_STORAGE . '/');

// DB
define('DB_DRIVER', 'mysqli');
define('DB_HOSTNAME', 'localhost');
define('DB_USERNAME', 'watchtag');
define('DB_PASSWORD', 'F1a4S3v7');
define('DB_DATABASE', 'watchtag');
define('DB_PORT', '3306');
define('DB_PREFIX', 'oc_');
