<?php
define('CHAT_LIMIT_LINES', 100);

//If trading needs password enabled
define('IF_TRADING_PASS', 1);

//Etherscan API KEY
define('GETH_MODE', 'light'); //light , full 
define('ETHERSCAN_KEY', 'ENTERYOURKEY');

//Enable /disable subscriptions
define('ENABLE_SUBS', 0);
define('SUBSCRIPTION_PLANS',array(array('id'=>1,'coin'=>"usd",'duration'=>"12",'price'=>'1','name'=>'Premium','is_popular'=>0,'discount'=>'50','condition'=>'For <b>Everyone</b>'),array('id'=>2,'coin'=>"usd",'duration'=>"6",'price'=>'0','name'=>'Premium Plus','is_popular'=>1,'discount'=>'50','condition'=>'Free for First <b>100k users</b>')));

//If trading needs password enabled
define('SHOW_TRADING_FEES', 1);

// Enable Disable RECAPTCHA
define('RECAPTCHA', 0);
define('RECAPTCHA_KEY', '6LfMoEwUAAAAAG1luBQgvqRx4U_OdhvOMdVvJoZ4');
define('RECAPTCHA_SECRET', '6LfMoEwUAAAAAGvcxJBpJB0HywBh2jTwa7wuq6jW');
// Enable Disable RECAPTCHA

define('DEFAULT_MAILER', 'phpmail'); //Choose between sendgrid,sendinblue,mailgun,mandrill,amazonses,postmark,mailjet,phpmail,smtpmail
define('GLOBAL_EMAIL_SENDER', 'sender@yoursite.com'); //Make sure you change this 

define('MAILGUN_API_KEY', 'key-DDD');
define('MAILGUN_DOMAIN', 'https://api.mailgun.net/v3/DDD.DDD');

// MailJet
/* In case of cacert.pem error Read https://codono.com/curl-error-60-ssl-certificate-problem/  */
define('MAILJET_PUBLIC_KEY', 'XX');
define('MAILJET_PRIVATE_SECRET', 'Xx');

//AMAZONSES
define('AMAZONSES_accessKey', "Access Key here");
define('AMAZONSES_secretKey', "Secret Key here");
define('AMAZONSES_region', "Region here");
define('AMAZONSES_verifyPeer', "verifyPeer here");
define('AMAZONSES_verifyHost', "verifyHost here");

//MANDRILL_API_KEY
define('MANDRILL_API_KEY', "Key here");

//POSTMARK_serverApiToken
define('POSTMARK_serverApiToken', "API token here");

//SendGrid
define('SENDGRID_API_KEY', "SG.XX.XX");

//SEND IN BLUE // Join Here https://www.sendinblue.com/?tap_a=30591-fb13f0&tap_s=269382-3fd2bd
define('SENDINBLUE_API_KEY', "XX");

//For tradingview :https://tradingview.com/HTML5-stock-forex-bitcoin-charting-library/ and replace with your licensed files in /Public/Home/js/tradingview
define('ENABLE_TRADINGVIEW',0);

//Optional Not required : Dont use until you know it 
define('ENABLE_BINANCE',0);
define('BINANCE_NODE', "http://localhost:8888/");
define('WEBSITE_START_DATE',"1523102946"); 

//Go through Manual Look for coinpayments
define('COINPAY_MERCHANT_ID', 'XX');
define('COINPAY_SECRET_PIN', 'XX');

//SumSub.com KYC Info put_a_secret is some strong string you need to place , do not change it often
define('SUMSUB_KYC',array('status'=>'0','mode'=>'test','clientid'=>'precharge','username'=>'precharge_precharge_precharge_test','password'=>'1xvxnfxr3i','put_a_secret'=>'3gr437tg84'));

//Authorize.net Settings **Make Sure your clear cache after changing this ** mode value to be live or sandbox
define('AUTHORIZE_NET',array("status"=>"0","mode"=>"sandbox","clientkey"=>"XX","loginid"=>"XX","transactionkey"=>"XX","signature"=>"X")); 

// DO NOT ENABLE BELOW IF YOU DONT KNOW WHAT IS THIS CONSULT CODONO LIVECHAT : This tool is experimental
define('LIQUIDITY_ARRAY',array("LTC_BTC"=>"LTC/BTC")); 
//define('LIQUIDITY_ARRAY',array("LTC_BTC"=>"LTC/BTC","ETH_USD"=>"ETH/USDT")); //System to binance liquidity Please read documentation about liquidity
//System to binance liquidity Please read documentation about liquidity
define('LIQUIDITY_CONF',array("URL"=>"http://x.x.x.x/codebase/","USERID"=>"someusername","TOKEN"=>"TOKEN_YOU_DEFINED")); 



define('SOCKET_WS_ENABLE',0); //This Socket server URL
define('MARKETS_WS_SOCKET',array("btc_usd"=>"btcusdt","ltc_usd"=>"ltcusd")); //This is required to read websockets
define('SOCKET_WS_URL',"ws://localhost:7272"); //This Socket server URL
// DO NOT ENABLE ABOVE IF YOU DONT KNOW WHAT IS THIS CONSULT CODONO LIVECHAT : This tool is experimental

//ColdWallet Storage Move , Applicable for BTC and ETH based coins
//coin=>address:minkeep:mintransfer
//define('COLD_WALLET',array("BTC"=>"3L2qg4wCSVJ22ChQQ5Z8Z5XCSDh6saqVsd:1.2:0.5"));
define('COLD_WALLET',array());