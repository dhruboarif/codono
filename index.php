<?php

header("Access-Control-Allow-Origin: *");

// Defined encoding
define('CODONO_VERSION', '5.5.1');
header("Content-Type: text/html;charset=utf-8");
// Application path
define('APP_PATH', './Application/');
// Cache paths
define('RUNTIME_PATH', './Runtime/');
// BackupPath
define('DATABASE_PATH', './Database/');
// Coin path
define('COIN_PATH', './Coin/');
// Upload Path
define('UPLOAD_PATH', './Upload/');

//$pure_config_file=APP_PATH.'/pure_config.php';
$pure_config_file = 'pure_config.php';
if (file_exists($pure_config_file)) {
    include_once($pure_config_file);
	include_once('other_config.php');
} else {
    die('Your Exchange is\'nt setup properly , Please look into config');
}

require './Framework/codono.php';
