<?php
header('X-Content-Type-Options: nosniff');
header('X-Powered-By: '.SHORT_NAME);

// **PREVENTING SESSION HIJACKING**
// Prevents javascript XSS attacks aimed to steal the session ID
ini_set('session.cookie_httponly', 1);

// **PREVENTING SESSION FIXATION**
// Session ID cannot be passed through URLs
ini_set('session.use_only_cookies', 1);

// Uses a secure connection (HTTPS) if possible
//ini_set('session.cookie_secure', 1);




if (defined('M_DEBUG') && M_DEBUG == 1) {
    define('APP_DEBUG', 1);
    require dirname(__FILE__) . '/Bootstrap.php';
} else {
    if (isset($_GET['debug']) && $_GET['debug'] === ADMIN_KEY) {
        setcookie('ADBUG', 'codono', time() + 60 * 3600);
        exit('ok');
    }

    if (isset($_COOKIE['ADBUG']) && $_COOKIE['ADBUG'] == ADMIN_KEY) {
        // Open debugging mode
        define('APP_DEBUG', 1);
        require dirname(__FILE__) . '/Bootstrap.php';
    } else {
        // Open debugging mode
        if(!defined('APP_DEBUG')){define('APP_DEBUG', 0);}
        try {
            require dirname(__FILE__) . '/Bootstrap.php';
        } catch (\Exception $exception) {

            send_http_status(404);
            $string = file_get_contents('./Public/inline-error.html');
            $string = str_replace('$error', $exception->getMessage(), $string);
            $string = str_replace('SITE_URL', SITE_URL, $string);
            $string = str_replace('SITE_NAME', SHORT_NAME, $string);
            echo $string;
        }
    }
}
