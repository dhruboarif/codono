<?php

return array(
    'DB_TYPE' => DB_TYPE,
    'DB_HOST' => DB_HOST,
    'DB_NAME' => DB_NAME,
    'DB_USER' => DB_USER,
    'DB_PWD' => DB_PWD,
    'DB_PORT' => DB_PORT,
    'DB_PREFIX' => 'codono_', //donot change this
    'ACTION_SUFFIX' => '',
    'MULTI_MODULE' => true,
    'MODULE_DENY_LIST' => array('Common', 'Runtime'),
    'MODULE_ALLOW_LIST' => array('Home', 'Admin','Api','Tapi'), //we have disabled mapi
    'DEFAULT_MODULE' => 'Home',
    'URL_CASE_INSENSITIVE' => false,
    'URL_MODEL' => 2,
    'URL_HTML_SUFFIX' => '', //We suggest leave it blank .. Only change it to .html when you know how to handle server rewrites
	'MINIFY' => true,
	'OUTPUT_ENCODE'=>true,
    'URL_ROUTER_ON' => true,
    'URL_ROUTE_RULES' => array(
        'Market' => 'Trade/chart',
        'launchpad' => 'Issue/index',
		'Store' => 'Shop/index',
		'Blog' => 'Article/index',
//		'Finance/Wallet'=>'Finance/myzr'
    ),
    'UPDATE_PATH' => '',
    'CLOUD_PATH' => '',
    'HOST_IP' => HOST_IP,
    'TMPL_CACHFILE_SUFFIX' => '.htmx',
    'DATA_CACHE_TYPE' => 'file',
    'URL_PARAMS_SAFE' => true,
    'DEFAULT_FILTER' => 'check_codono,htmlspecialchars,strip_tags',
    'URL_PARAMS_FILTER_TYPE' => 'check_codono,htmlspecialchars,strip_tags',
	'CODONO_EMAIL' => array(
        'SMTP_HOST' => 'someemail.com', //SMTP SERVER
        'SMTP_PORT' => '25', //PORT
        'SMTP_USER' => 'testmail@someemail.com', //EMAIL
        'SMTP_PASS' => 'password', //PASSWORD
        'FROM_EMAIL' => 'testmail@someemail.com', //FROM SENDER HEADER
        'FROM_NAME' => 'BTC TESTING', //FROM NAME HEADER
        'REPLY_EMAIL' => '', //Reply email (leave blank for sender EMAIL)
        'REPLY_NAME' => '', //Reply name (Leave blank for sender name)
    ),
);
?>