<?php
/**
 * System with the file
 * All system-level configuration
 */

define('SHORT_NAME', 'codono.com');
define('HOST_IP', '127.0.0.1');
// SITE URL
define('SITE_URL', 'http://localhost:1005/');

// Database Type
define('DB_TYPE', 'mysql');
// DB Host
define('DB_HOST', '127.0.0.1');
// DB Name
define('DB_NAME', 'codonov5');
// DB User
define('DB_USER', '1');
// DB PASSWORD
define('DB_PWD', '1');
//DB PORT Dont change if you are not sure
define('DB_PORT', '3306');
// Exchange Goes into DEMO mode if 1 : Let it be 0
define('APP_DEMO', 0);

// Set it 0 to turn DEMO SMS MODE
define('MOBILE_CODE', 0);

//Enable Mobile only login
define('M_ONLY', 0);

//SITE WIDE DEBUGGIN ON : Not recommended for Prodcution mode [For production site Put it 0]
define('M_DEBUG', 0);

//Admin  Debugging
define('ADMIN_DEBUG', 0);

//Show debug window on everypage
define('DEBUG_WINDOW', 0);

//Turn On KYC on Signup and make optional
define('KYC_OPTIONAL', 1);

//If you enforce kyc only people with KYC true will able to Withdraw and trade , So keep it zero
define('ENFORCE_KYC', 0);

// Backend Security
define('ADMIN_KEY', 'securekey');

// Key to ACCESS CRONS 
define('CRON_KEY', 'cronkey');

//Your License Number Or Codono ORDERID
define('CODONOLIC', 'XXXXXX');

//NEVER CHANGE ETH_USER_PASS AGAIN .. IF YOU DO, YOUR ETH USER WALLET PASSWORDS WOULD NEVER WORK
define('ETH_USER_PASS','C4Qx3YZjvd6rghKKJ7H5w4cmTEBrEFgjAV'); //YOU CAN CHANGE THIS ONLY ONCE IN LIFE TIME BEFORE USING ETHEREUM

define('REDIS_ENABLED', 1); //Turn 1 only when REDIS is running Read Manual for more info

define('DIR_SECURE_CONTENT', 'ACCESS DENIED!');