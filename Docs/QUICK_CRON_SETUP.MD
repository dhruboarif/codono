Crons Setup [replace here cronkey with key you have setup in pure_config.php]



[Optional]ONLY Run if you are running WavesPlatform API [Run every minute and make sure your block is synced]
https://yourdomain.com/Queuea536ur360n7roll3rnam3/wallet_waves_deposit/securecode/cronkey


[Optional]ONLY Run if you are running Coinpayments.net API [Every 10 mins or more]
https://yourdomain.com/Queuea536ur360n7roll3rnam3/wallet_coinpay_deposit/securecode/cronkey
https://yourdomain.com/Queuea536ur360n7roll3rnam3/wallet_coinpay_withdraw/securecode/cronkey


[Optional]ONLY Run if you are running Block.net API [10 mins or more
https://yourdomain.com/Queuea536ur360n7roll3rnam3/wallet_blockio_deposit/securecode/cronkey
https://yourdomain.com/Queuea536ur360n7roll3rnam3/wallet_blockio_withdraw/securecode/cronkey

Bitcoin and Bitcoin Type [every 5 mins]
https://yourdomain.com/Queuea536ur360n7roll3rnam3/wallet_v3ryun10u3n4m3/securecode/cronkey

[Optional]ONLY ETH and ETH Type [Depends upon txs:Usual 5 mins]
https://yourdomain.com/Queuea536ur360n7roll3rnam3/walleteth_v3ryun10u3n4m3/securecode/cronkey


[CAUTION: DO NOT RUN UNTIL YOU UNDERSTAND IT] Move Balance to Cold wallet 
RISK : MAKE SURE YOU CONFIGURE COLDWALLETS CORRECTLY IN other_config.php before running this cron
https://yourdomain.com/Queuea536ur360n7roll3rnam3/move2cold/securecode/cronkey



[OPTIONAL:Investbox]ONLY if Investment Release Cron 
Cron will check any matured investment and release it compare to current time
https://yourdomain.com/Queuea536ur360n7roll3rnam3/checkInvest/securecode/cronkey



[REQUIERED] Other to run 
https://yourdomain.com/Queuea536ur360n7roll3rnam3/marketandcoin_v3ryun10u3n4m3/securecode/cronkey [Every 2nd minute]
https://yourdomain.com/Queuea536ur360n7roll3rnam3/chart_v3ryun10u3n4m3/securecode/cronkey [Every minute]
https://yourdomain.com/Queuea536ur360n7roll3rnam3/tendency_v3ryun10u3n4m3/securecode/cronkey [every day or hourly]
https://yourdomain.com/Queuea536ur360n7roll3rnam3/houprice_v3ryun10u3n4m3/securecode/cronkey [hourly]



PHP extensions required
PHP 7+
mbstring
openssl
curl
gettext
gd2
pdo
xmlrpc
bcmath


Adding New lang
Step 1
copy below file and save as your language.php
Framework/Lang/en.php
and save it as say jp.php


Now Add your language parameter to following file
Application/Home/View/Public/lang_bar.html




