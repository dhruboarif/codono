<?php

namespace Home\Controller;

class Queuea536ur360n7roll3rnam3Controller extends HomeController
{
    //Remember to add etherscankey in EthPayLocal if node is light
    const eth_node_type = 'light';  // It should be light/full/fast

    public function callLiquidityServer($num, $market, $type, $price)
    {
        if ($_GET['securecode'] != CRON_KEY) {
            die('No Code defined');
        }
        $num = str_replace("x", ".", $num);
        $Match_market = strtoupper($market);
        if (array_key_exists($Match_market, LIQUIDITY_ARRAY)) {
            $liq_market = LIQUIDITY_ARRAY["$Match_market"]; //binance market pair needs to be defined in other_config.php
        } else {
            //return ("NOMARKET");
            return false;
        }

        if ($type == 1) {
            $type = 'buy';
        } else if ($type == 2) {
            $type = 'sell';
        } else {
            return 0;
        }
        $price = format_num($price, 8);

        if (!check($price, 'double')) {
            //exit("PRICEISSUE");
            return false;
        }
        $num = format_num($num, 8);
        if (!check($num, 'double')) {
            //exit("NUMISSUE" . $num);
            return false;
            //$num="0.001";
        }

        $liq_server_url = LIQUIDITY_CONF['URL'] . "public/api/place_order/" . $liq_market;
        $post_vars = "user_id=" . LIQUIDITY_CONF['USERID'] . "&token=" . LIQUIDITY_CONF['TOKEN'] . "&type=$type&price=$price&amount=$num";


        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $liq_server_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $post_vars,
            CURLOPT_HTTPHEADER => array(
                "Accept: */*",
                "Accept-Encoding: gzip, deflate",
                "Cache-Control: no-cache",
                "Content-Type: application/x-www-form-urlencoded",

            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            //return "cURL Error #:" . $err;
            return false;
        } else {
            return $response;
        }
    }

    private function investinfo($id)
    {
        $Model = M('Investbox');
        $where['id'] = $id;
        $investbox = $Model->where($where)->find();
        return $investbox;
    }

    public function checkInvest()
    {
        if ($_GET['securecode'] != CRON_KEY) {
            die('No Code defined');
        }
        $mo = M();
        $map = array('status' => 1);
        $now = time();
        $map['endtime'] = array('lt', $now);
		$map['status'] = 1;
        $logs = M('InvestboxLog')->where($map)->order('id asc')->limit(100)->select();
		
        if (!is_array($logs)) {
            echo 'Ok No records to be proceeded';
            exit;
        }else{
			echo count($logs). " Record/s found to be processed<br/>";
			
		}
        foreach ($logs as $ibl) {
			
            if ($ibl['status'] != 1) {
                continue;
            }
			
            $userid = $ibl['userid'];

            $rs = array();

            if ($ibl['status'] == 1) {

                $credit = format_num($ibl['maturity'], 8);

                $invest_info = $this->investinfo($ibl['boxid']);
				if($invest_info==NULL){
						$mo->table('codono_investbox_log')->where(array('id' => $ibl['id']))->save(array('status' => 0, 'withdrawn' => time(), 'credited' => $credit));
						continue;
				}
                $coinname = strval(strtolower($invest_info['coinname']));
                $coinnamed = strval(strtolower($invest_info['coinname'] . 'd'));
                $query = "SELECT `$coinname`,`$coinnamed` FROM `codono_user_coin` WHERE `userid` = $userid";
                $res_bal = $mo->query($query);
                $user_coin_bal = $res_bal[0];

                $mum_a = bcadd($user_coin_bal[$coinname], $credit, 8);
                $mum_b = $user_coin_bal[$coinnamed];
                $num = bcadd($user_coin_bal[$coinname], $user_coin_bal[$coinnamed], 8);
                $mum = bcadd($num, $credit, 8);

                $mo->execute('set autocommit=0');
                $mo->execute('lock tables codono_user_coin write  , codono_investbox_log write ,codono_finance write');
                if (0 < $credit) {
                    $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $ibl['userid']))->setInc($coinname, $credit);
                }
                $move_stamp = '3_' . $ibl['docid'];
                $rs[] = $mo->table('codono_investbox_log')->where(array('id' => $ibl['id']))->save(array('status' => 3, 'withdrawn' => time(), 'credited' => $credit));
                $finance_array = array('userid' => $userid, 'coinname' => $coinname, 'num_a' => $user_coin_bal[$coinname], 'num_b' => $credit, 'num' => $num, 'fee' => $credit, 'type' => 1, 'name' => 'investbox', 'nameid' => $ibl['id'], 'remark' => 'InvestBox_Maturity', 'move' => $move_stamp, 'addtime' => time(), 'status' => 1, 'mum' => $mum, 'mum_a' => $mum_a, 'mum_b' => $mum_b);

                $rs[] = $mo->table('codono_finance')->add($finance_array);
            } else {
                $mo->execute('rollback');
                echo 'Invalid status of investment !<br/>';
                continue;
            }

            if (check_arr($rs)) {
                $mo->execute('commit');
                $mo->execute('unlock tables');
                echo 'Investment has been credited!<br/>';
                continue;
            } else {
                $mo->execute('rollback');
                $mo->execute('unlock tables');
                echo 'Investment could not be credited!<br/>';
                continue;
            }

        }
        echo "End of checking";
    }

    /*
      public function cmcUpdate(){
        if($_GET['securecode']!=CRON_KEY){die('No Code defined');}
        $timestamp=time();
        //Create table if it doesn't exist

        $val=M()->query('select 1 from `codono_coinmarketcap` LIMIT 0');

        if($val == FALSE){
            $query = M()->query('CREATE TABLE codono_coinmarketcap (
            id varchar(50),
            name varchar(50),
            symbol varchar(50),
            rank varchar(50),
            price_usd varchar(50),
            price_btc varchar(50),
            24h_volume_usd varchar(50),
            market_cap_usd varchar(50),
            available_supply varchar(50),
            total_supply varchar(50),
            max_supply varchar(50),
            percent_change_1h varchar(50),
            percent_change_24h varchar(50),
            percent_change_7d varchar(50),
            last_updated varchar(50)
            )');
        }

        //Check if we need to update
        $row =M('Coinmarketcap')->limit(1)->select();

        //$row = $query->fetch_assoc();
        if ($row['last_updated'] >= $timestamp) {
            return true;
        }
        //Get full ticker array and write to db
        $request = $this->gcurl('https://api.coinmarketcap.com/v1/ticker/?limit=0');


        if ($request) {
            $array = json_decode($request, true);
        } else {
            return false;
        }
        //Delete table "coinmarketcap"
        M()->query("DELETE FROM codono_coinmarketcap");
        M()->execute('set autocommit=0');
        $i = 0;
        foreach ($array as $coin) {
        M()->query("INSERT INTO codono_coinmarketcap (
            id,
            name,
            symbol,
            rank,
            price_usd,
            price_btc,
            24h_volume_usd,
            market_cap_usd,
            available_supply,
            total_supply,
            max_supply,
            percent_change_1h,
            percent_change_24h,
            percent_change_7d,
            last_updated
            ) VALUES (
            '" . $coin['id'] . "', '" .
            $coin['name'] . "', '" .
            $coin['symbol'] . "', '" .
            $coin['rank'] . "', '" .
            $coin['price_usd'] . "', '" .
            $coin['price_btc'] . "', '" .
            $coin['24h_volume_usd'] . "', '" .
            $coin['market_cap_usd'] . "', '" .
            $coin['available_supply'] . "', '" .
            $coin['total_supply'] . "', '" .
            $coin['max_supply'] . "', '" .
            $coin['percent_change_1h'] . "', '" .
            $coin['percent_change_24h'] . "', '" .
            $coin['percent_change_7d'] . "', '" .
            time() .
            "')");
        }
            M()->execute('commit');
            M()->execute('unlock tables');
        print_r($coin);echo 'added';

    }

    private function gcurl($endpoint, $method = 'GET')
    {
        if (!$endpoint) {
            return "{'error':'No URL'}";
        }
        $call_url = $endpoint;
        $curl = curl_init();
        curl_setopt_array($curl, array(

            CURLOPT_URL => $call_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return $response;
        }
    }
*/
    public function send_notifications()
    {
        if ($_GET['securecode'] != CRON_KEY) {
            die('No Code defined');
        }
        $notifications = M('Notification')->where(array('status' => 0))->order('id desc')->select();
        foreach ($notifications as $note) {
            if (!check($note['to_email'], 'email')) {
                continue;
            }
            $status = tmail($note['to_email'], $note['subject'], $note['content']);
            if ($status) {
                $the_status = json_decode($status);
                if ($the_status->status == 1)// means email has beens sent , Now mark this email as sent
                {
                    echo M('Notification')->where(array('id' => $note['id']))->save(array('status' => 1));
                }
            }
        }
        echo "End";
    }

    public function index()
    {
        echo "ok";
    }
    /*Adjustment of abnormal Trades if deal > num */
    public function fixTrades()
    {
        if ($_GET['securecode'] != CRON_KEY) {
            die('No Code defined');
        }
        $mo = M();
        $mo->execute('set autocommit=0');
        $mo->execute('lock tables codono_trade write');
        $Trade = M('Trade')->where('deal > num')->order('id desc')->find();

        if ($Trade) {
            if ($Trade['status'] == 0) {
                $mo->table('codono_trade')->where(array('id' => $Trade['id']))->save(array('deal' => Num($Trade['num']), 'status' => 1));
            } else {
                $mo->table('codono_trade')->where(array('id' => $Trade['id']))->save(array('deal' => Num($Trade['num'])));
            }

            $mo->execute('commit');
            $mo->execute('unlock tables');
        } else {
            $mo->execute('rollback');
            $mo->execute('unlock tables');
        }
        echo "Cron Ended :)";
    }

    public function clearRedisForLiquidity()
    {
        if ($_GET['securecode'] != CRON_KEY) {
            die('No Code defined');
        }
        foreach (LIQUIDITY_ARRAY as $market => $binance_key) {
            S('allsum', null);
            S('getJsonTop' . $market, null);
            S('getTradelog' . $market, null);
            S('getDepth' . $market . '1', null);
            S('getDepth' . $market . '3', null);
            S('getDepth' . $market . '4', null);
            S('ChartgetJsonData' . $market, null);
            S('allcoin', null);
            S('trends', null);
            S('getActiveDepth', null);

        }

    }

    public function checkDapan()
    {
        if ($_GET['securecode'] != CRON_KEY) {
            die('No Code defined');
        }
        foreach (C('market') as $k => $v) {
            A('Trade')->matchingTrade($v['name']);
        }
    }


    public function marketandcoin_v3ryun10u3n4m3()
    {
        if ($_GET['securecode'] != CRON_KEY) {
            die('No Code defined');
        }
        foreach (C('market') as $k => $v) {
            $this->setMarket($v['name']);
        }
        echo "setcoin starts";
        foreach (C('coin_list') as $k => $v) {
            $this->setcoin($v['name']);
        }
        echo "<br/>setcoin ends";
    }

    private function setMarket($market = NULL)
    {
        if (!$market) {
            return null;
        }

        $market_json = M('Market_json')->where(array('name' => $market))->order('id desc')->find();

        if ($market_json) {
            $addtime = $market_json['addtime'] + 60;
        } else {
            $addtime = M('TradeLog')->where(array('market' => $market))->order('addtime asc')->find()['addtime'];
        }

        $t = $addtime;
        $start = mktime(0, 0, 0, date('m', $t), date('d', $t), date('Y', $t));
        $end = mktime(23, 59, 59, date('m', $t), date('d', $t), date('Y', $t));
        $trade_num = M('TradeLog')->where(array(
            'market' => $market,
            'addtime' => array(
                array('egt', $start),
                array('elt', $end)
            )
        ))->sum('num');

        if ($trade_num) {
            $trade_mum = M('TradeLog')->where(array(
                'market' => $market,
                'addtime' => array(
                    array('egt', $start),
                    array('elt', $end)
                )
            ))->sum('mum');
            $trade_fee_buy = M('TradeLog')->where(array(
                'market' => $market,
                'addtime' => array(
                    array('egt', $start),
                    array('elt', $end)
                )
            ))->sum('fee_buy');
            $trade_fee_sell = M('TradeLog')->where(array(
                'market' => $market,
                'addtime' => array(
                    array('egt', $start),
                    array('elt', $end)
                )
            ))->sum('fee_sell');
            $d = array($trade_num, $trade_mum, $trade_fee_buy, $trade_fee_sell);

            if (M('Market_json')->where(array('name' => $market, 'addtime' => $end))->find()) {
                M('Market_json')->where(array('name' => $market, 'addtime' => $end))->save(array('data' => json_encode($d)));
            } else {
                M('Market_json')->add(array('name' => $market, 'data' => json_encode($d), 'addtime' => $end));
            }
        } else {
            $d = null;

            if (M('Market_json')->where(array('name' => $market, 'data' => ''))->find()) {
                M('Market_json')->where(array('name' => $market, 'data' => ''))->save(array('addtime' => $end));
            } else {
                M('Market_json')->add(array('name' => $market, 'data' => '', 'addtime' => $end));
            }
        }
    }

    private function setcoin($coinname = NULL)
    {
        echo "<br/>Start coin " . $coinname;
        if (!$coinname) {
            return null;
        }

        if (C('coin')[$coinname]['type'] == 'qbb') {
            $dj_username = C('coin')[$coinname]['dj_yh'];
            $dj_password = C('coin')[$coinname]['dj_mm'];
            $dj_address = C('coin')[$coinname]['dj_zj'];
            $dj_port = C('coin')[$coinname]['dj_dk'];
            $CoinClient = CoinClient($dj_username, $dj_password, $dj_address, $dj_port, 5, array(), 1);
            $json = $CoinClient->getinfo();

            if (!isset($json['version']) || !$json['version']) {
                return null;
            }

            $data['trance_mum'] = $json['balance'];
            $bb = $json['balance'];
        } else {
            $data['trance_mum'] = 0;
            $bb=0;
        }

        $market_json = M('CoinJson')->where(array('name' => $coinname))->order('id desc')->find();

        if ($market_json) {
            $addtime = $market_json['addtime'] + 60;
        } else {
            $addtime = M('Myzr')->where(array('name' => $coinname))->order('id asc')->find()['addtime'];
        }

        $t = $addtime;
        $start = mktime(0, 0, 0, date('m', $t), date('d', $t), date('Y', $t));
        $end = mktime(23, 59, 59, date('m', $t), date('d', $t), date('Y', $t));

        if ($addtime) {
            if ((time() + (60 * 60 * 24)) < $addtime) {
                return null;
            }

            $trade_num = M('UserCoin')->where(array(
                'addtime' => array(
                    array('egt', $start),
                    array('elt', $end)
                )
            ))->sum($coinname);
            $trade_mum = M('UserCoin')->where(array(
                'addtime' => array(
                    array('egt', $start),
                    array('elt', $end)
                )
            ))->sum($coinname . 'd');
            $aa = $trade_num + $trade_mum;



            $trade_fee_buy = M('Myzr')->where(array(
                'coinname' => $coinname,
                'addtime' => array(
                    array('egt', $start),
                    array('elt', $end)
                )
            ))->sum('fee');
            $trade_fee_sell = M('Myzc')->where(array(
                'coinname' => $coinname,
                'addtime' => array(
                    array('egt', $start),
                    array('elt', $end)
                )
            ))->sum('fee');
            $d = array($aa, $bb, $trade_fee_buy, $trade_fee_sell);

            if (M('CoinJson')->where(array('name' => $coinname, 'addtime' => $end))->find()) {
                M('CoinJson')->where(array('name' => $coinname, 'addtime' => $end))->save(array('data' => json_encode($d)));
            } else {
                M('CoinJson')->add(array('name' => $coinname, 'data' => json_encode($d), 'addtime' => $end));
            }
        }
    }


    public function houprice_v3ryun10u3n4m3()
    {
        if ($_GET['securecode'] != CRON_KEY) {
            die('No Code defined');
        }
        foreach (C('market') as $k => $v) {
            echo "$k<br/>";
            if (!$v['hou_price'] || (date('H', time()) == '00')) {
                $t = time();
                $start = mktime(0, 0, 0, date('m', $t), date('d', $t), date('Y', $t));
                echo "Start is :$start<br/>";
                $hou_price = M('TradeLog')->where(array(
                    'market' => $v['name'],
                    'addtime' => array('lt', $start)
                ))->order('id desc')->getField('price');
                print_r($hou_price);

                if (!$hou_price) {
                    $hou_price = M('TradeLog')->where(array('market' => $v['name']))->order('id asc')->getField('price');
                }

                M('Market')->where(array('name' => $v['name']))->setField('hou_price', $hou_price);
                S('home_market', null);
            }
        }
    }

    /*First Run
    To set a starting block run this function manually
    replace {BLOCKNUMBER} with first deposits blocknumber
    replace {CRONKEY} with your cronkey
    replace {YOURSITE} with your site url
    http://{YOURSITE}/Home/Queuea536ur360n7roll3rnam3/walleteth_v3ryun10u3n4m3/b/{BLOCKNUMBER}/securecode/{CRONKEY}
    */
    //eth wallet cron starts
    public function walleteth_v3ryun10u3n4m3($b = NULL)
    {
        if ($_GET['securecode'] != CRON_KEY) {
            die('No Code defined');
        }
        $amount = 0;
        $token_address_found=0;
        $coinList = M('Coin')->where(array('status' => 1, 'type' => 'eth'))->select();
        $CoinInfoArray = array();
        //dj_yh = Address 42 lenght
        foreach ($coinList as $singlecoinKey => $singlecoinValue) {
            if (strlen($singlecoinValue['dj_yh']) <= 42) {
                $CoinInfoArray[$singlecoinValue['name']] = $singlecoinValue['dj_yh'];
            }
            if ($singlecoinValue['block'] > $CoinInfoArray['block']) {
                $CoinInfoArray['block'] = $singlecoinValue['block'];
            }
        }
        $eth_details = M('Coin')->where(array('status' => 1, 'name' => 'eth', 'type' => 'eth'))->find();

        $main_account = $eth_details['codono_coinaddress'];

        $EthCommon = new \Org\Util\EthCommon($eth_details['dj_zj'], $eth_details['dj_dk'], "2.0");
        $EthPayLocal = new \Org\Util\EthPayLocal($eth_details['dj_zj'], $eth_details['dj_dk'], "2.0", $eth_details['dj_yh']);
        $decimals = $eth_details['cs_qk'] ? $eth_details['cs_qk'] : 8;

        if ($b) {
            $listtransactions = $EthPayLocal->EthInCrontab($b);
            $next_block = $b;
            $the_sql = "UPDATE `codono_coin` SET  `block` =  '" . $next_block . "' WHERE name='eth' AND type='eth' ";
            M()->execute($the_sql);
        } else {
            echo "You have not defined any Block, So taking a block value from DB which is " . $eth_details['block'] . "<br/>";
            $listtransactions = $EthPayLocal->EthInCrontab($eth_details['block']);
            $next_block = $listtransactions[0]['block'] + 1;
            $the_sql = "UPDATE `codono_coin` SET  `block` =  '" . $next_block . "' WHERE name='eth' AND type='eth' ";
            if ($listtransactions[0]['block']) {
                echo M()->execute($the_sql);
            }
        }

        $time_start = microtime(true);
        $higesthblock = $EthPayLocal->eth_syncing()['result']['highestBlock'];
        $currentBlock = $EthPayLocal->eth_syncing()['result']['currentBlock'];
        echo "<br/>Your currentblock is " . base_convert($currentBlock, 16, 10) . " where is highest block is " . base_convert($higesthblock, 16, 10);


        echo "<br/>There were " . count($listtransactions) . " txs in this block, Now checking how many belongs to your exchange<br/>";

        foreach ($listtransactions as $ks => $trans) {
            if ($trans['from'] == $main_account) {
                //echo "This means , we send some ether to client address for gas do not consider it as deposit";
                continue;
            };
            if (!$trans['to']) continue;//No payee.

            if ($trans['number'] <= 0) {

                if (!$trans['input']) continue;

                //Find if its a token if input != 0x then its a token


                if ($trans['input'] != '0x') {
                    $to_num = substr($trans['input'], 74);//Quantity


                    $tos = substr($trans['input'], 34, 40);//Reciever

                    if (!$tos) continue;

                    $tos = "0x" . $tos;
                    $num = $EthCommon->fromWei($to_num);

                } else {
                    //This is for ethereum it self
                    $tos = $trans['to'];
                    $num = $trans['number'];
                }

                $coin_Query = "SELECT userid as userid FROM `codono_user_coin` WHERE `ethb` LIKE '" . $tos . "'";
                $users = M()->query($coin_Query);

                if (!$users) continue;

                $user = $users[0];

                echo "<br/> UserID Found" . $user['userid'] . " address is " . $tos . "<br/>";

                if (self::eth_node_type == 'light') {

                    //ETHERSCAN METHOD
                    $hash_result = $EthPayLocal->txstatus($trans['hash']);
                    if ($hash_result == 0) {
                        echo "<br/>" . $trans['hash'] . " tx was failed or can not confirm it - Skipping it <br/>";
                        continue;
                    }
                } else {
                    echo "Your have defined GETH TYPE " . self::eth_node_type . "<br/>";
                    //GETH METHOD
                    $hash_result = json_decode($EthPayLocal->eth_getTransactionReceipt($trans['hash']));
                    var_dump($hash_result);
                    if ($hash_result->result->status != '0x1' && $hash_result->result->hash != $trans['hash']) {
                        echo "<br/>" . $trans['hash'] . " tx was failed or can not confirm it - Skipping it <br/>";
                        continue;
                    }
                }
                $func = '0x' . substr($trans['input'], 2, 8);
                $flag = false;
                if ($func == "0xa9059cbb") {
                    $token_address_found = $trans['to'];
                    $from = $trans['from'];
                    $to = '0x' . substr(substr($trans['input'], 10, 64), -40);

                    $coin_Query = "SELECT name,cs_qk FROM `codono_coin` WHERE `dj_yh` LIKE '%" . $token_address_found . "%'";
                    $coin_info = M()->query($coin_Query);
                    $decimals = $coin_info[0]['cs_qk'];
                    $amount = hexdec(substr($trans['input'], 74, 64)) / pow(10, $decimals);

                    $flag = true;
                    echo "<br/> Function is " . $func . "<br>";
                } else if ($func == "0x23b872dd") {
                    $token_address_found = $trans['to'];
                    $from = '0x' . substr(substr($trans['input'], 10, 64), -40);
                    $to = '0x' . substr(substr($trans['input'], 74, 64), -40);
                    $amount = hexdec(substr($trans['input'], 138, 64));

                    $flag = true;
                    echo "<br/> Function is " . $func . "<br>";
                }
                if ($flag) {
                    echo "Token is " . $token_address_found;
                    $coin_Query = "SELECT name,cs_qk FROM `codono_coin` WHERE `dj_yh` LIKE '%" . $token_address_found . "%'";
                    $coin_info = M()->query($coin_Query);
                    $coin = $coin_info[0]['name'];
                }

                echo "<br>=======================" . $coin . "=======================<br/>";
                if ($trans['input'] == '0x' && $coin != 'eth') {
                    echo "<br/>This is eth transaction not for " . $coin . "<br/>";
                    continue;
                }
                if ($trans['input'] != '0x' && $coin == 'eth') {
                    echo "<br/>This is not for eth<br/>";
                    continue;
                }

                if ($trans['input'] != '0x' && $coin != 'eth') {
                    $contract_Address_to_look = $trans['to'];
                    if ($token_address_found != $contract_Address_to_look) {
                        echo "<br/>token != $contract_Address_to_look";
                        echo " <br/>NO not this contract coin" . $coin . "<br/>";
                        continue;
                    }
                    $token_query = "SELECT name,cs_qk as decimals FROM `codono_coin` WHERE `dj_yh` LIKE '%" . $contract_Address_to_look . "%'";

                    $resulto = M()->query($token_query);

                    if (!$resulto[0]['name']) {
                        echo 'This token isnt registered on system';
                        continue;
                    }

                    $coin = $resulto[0]['name'];

                    echo "<br/>func is " . $func . "</br>";
                    $num = $amount;
                    echo "<br/>Amount is " . $num . "</br>";

                }
                if ($num <= 0 && $coin == "eth") {
                    $num = $EthCommon->fromWei($trans['val']);
                }
                echo "amount=$num";

                if ($num <= 0) continue;
                echo "Star=>";

                if (M('Myzr')->where(array('txid' => $trans['hash']))->find()) {
                    //Already recorded
                    echo $tos . '=>' . $num . '=>tx for ' . $coin . ' already credited Checking Next' . "<br>";
                    continue;
                }
                echo $coin . ' all check ok ' . "<br>";

                M()->execute('set autocommit=0');
                M()->execute('lock tables codono_myzr write  , codono_user_coin write');
                $num = format_num($num, 8);
                $coin = strtolower($coin);
                $rs[] = M('myzr')->add(array('userid' => $user['userid'], 'type' => 'eth', 'username' => $tos, 'coinname' => $coin, 'fee' => 0, 'txid' => $trans['hash'], 'num' => $num, 'mum' => $num, 'addtime' => time(), 'status' => 1));
                $rs[] = M()->table('codono_user_coin')->where(array('userid' => $user['userid']))->setInc($coin, $num);

                if (check_arr($rs)) {
                    M()->execute('commit');
                    M()->execute('unlock tables');
                    $this->deposit_notify($user['userid'], $tos, $coin, $trans['hash'], $num, time());
                    echo $coin . '=>' . $tos . '=>' . $num . ' commit ok' . "<br>";
                } else {
                    M()->execute('rollback');
                    M()->execute('unlock tables');
                    echo 'Failed Deposit ok' . "<br/>";
                }


            } else {
                //eth
                $user = M('UserCoin')->where(array('ethb' => $trans['to']))->find();
                if (!$user) continue;
                if (M('Myzr')->where(array('txid' => $trans['hash']))->find()) {
                    echo $trans['to'] . '=>' . $trans['number'] . '=>eth hash had found ,already added!! Checking next!' . "<br>";
                    continue;
                }
                echo 'eth all check ok ' . "<br>";
                M('myzr')->add(array('userid' => $user['userid'], 'username' => $trans['to'], 'coinname' => 'eth', 'fee' => 0, 'txid' => $trans['hash'], 'num' => $trans['number'], 'mum' => $trans['number'], 'addtime' => time(), 'status' => 1, 'type' => 'eth'));
                $rs[] = M()->table('codono_user_coin')->where(array('userid' => $user['userid']))->setInc("eth", $trans['number']);
                echo 'eth=>' . $trans['to'] . '=>' . $trans['number'] . ' commit ok' . "<br>";
            }

        }
        $time_end = microtime(true);

        //dividing with 60 will give the execution time in minutes otherwise seconds
        $execution_time = ($time_end - $time_start) / 60;

        //execution time of the script
        echo '<br/><b>Total Execution Time:</b> ' . $execution_time . ' Mins';
    }


    public function EthXferToMain()
    {
        $coinname = 'eth';
        exit('<span style="color:red">Warning:Goto Queue Controller and Remove this ' . __LINE__ . ' line  manually,<blink> You need to understand this cron will move all your account funds to codono_coinaddress for ' . $coinname . '</blink></span>');
        if ($_GET['securecode'] != CRON_KEY) {
            die('No Code defined');
        }
        $condition['status'] = 1;
        $condition['shifted_to_main'] = 0;
        $condition['type'] = 'eth';
        $condition['coinname'] = $coinname;
        $transactions = M('Myzr')->where($condition)->order('id asc')->limit(50)->select();

        foreach ($transactions as $tx) {

            $from = $tx['username'];
            $password = ETH_USER_PASS;
            if (!$password) {
                echo $password . "No pass found for " . $from . '<br/>';
                continue;
            }
            echo "Futher:" . $tx['username'];
            $CoinInfo = M('Coin')->where(array('name' => $tx['coinname']))->find();
            $heyue = $CoinInfo['dj_yh'];//Contract Address
            $dj_password = $CoinInfo['dj_mm'];
            $dj_address = $CoinInfo['dj_zj'];
            $dj_port = $CoinInfo['dj_dk'];
            $EthCommon = new \Org\Util\EthCommon($dj_address, $dj_port, "2.0");
            $ContractAddress = $heyue;
            $master = EthMaster($dj_address, $dj_port, $CoinInfo['codono_coinaddress'], $dj_password, $ContractAddress = false);
            $ubal = $this->floordec($master->balanceOf($tx['username']), 3);
            echo "<br/>Moving $ubal to main account<br/>";
            $sendrs = $master->emptyEthOfAccount($tx['username'], $ubal, $password);

            if ($sendrs) {
                echo "<pre>";
                var_export($sendrs);
                echo "</pre>";
                $this->markEthMoved($tx['id']);
            } else {
                echo "<br/>No pass found for " . $from . '<br/>';
                continue;
            }

            //Save to myzr table that
        }
        echo "Cron Ended";
        exit;
    }

    private function floordec($value, $precision = 2)
    {

        $mult = pow(10, $precision); // Can be cached in lookup table
        return floor($value * $mult) / $mult;
    }

    /*Run this Cron manually or with extreme caution This may cost double or more gas , and moves tokens from user accounts to your main account*/
    public function MoveTokenToMain($coinname)
    {
        exit('<span style="color:red">Warning:Goto Queue Controller and Remove this ' . __LINE__ . ' line  manually,<blink> You need to understand this cron will move all your account funds to codono_coinaddress for ' . $coinname . '</blink></span>');
        if ($_GET['securecode'] != CRON_KEY) {
            die('No Code defined');
        }
        $condition['status'] = 1;
        $condition['shifted_to_main'] = 0;
        $condition['type'] = 'eth';
        $condition['coinname'] = $coinname;
        $transactions = M('Myzr')->where($condition)->order('id asc')->limit(50)->select();
        $unique_address = array();
        foreach ($transactions as $tx) {
            if (in_array($tx['username'], $unique_address)) {
                $this->markEthMoved($tx['id']);
                continue;
            }
            $unique_address[] = $tx['username'];
            $from = $tx['username'];
            $password = ETH_USER_PASS;//Defined in pure_config
            if (!$password) {
                echo $password . "No pass found for " . $from . '<br/>';
                continue;
            }
            echo "Futher:<br/>Sweeping Tokens  from " . $tx['username'] . "<br/>";
            $CoinInfo = M('Coin')->where(array('name' => $tx['coinname']))->find();
            $heyue = $CoinInfo['dj_yh'];//Contract Address
            $dj_password = cryptString($CoinInfo['dj_mm'], 'd');
            $dj_address = $CoinInfo['dj_zj'];
            $dj_port = $CoinInfo['dj_dk'];
            $dj_decimal = $CoinInfo['cs_qk'];
            $dj_decimal = $dj_decimal ? $dj_decimal : 8; //Blockspeed column in coin config

            $EthCommon = new \Org\Util\EthCommon($dj_address, $dj_port, "2.0");
            $EthPayLocal = new \Org\Util\EthPayLocal($dj_address, $dj_port, "2.0", $heyue, $dj_password);
            $ContractAddress = $heyue;
            $master = EthMaster($dj_address, $dj_port, $CoinInfo['codono_coinaddress'], $dj_password, $ContractAddress);
            $bal_token = $master->balanceOfToken($tx['username'], $ContractAddress);

            $token_balance = format_num($master->balanceOfToken($tx['username'], $ContractAddress, $dj_decimal), 8);
            if ((int)$token_balance < 0.000000001) {
                echo $coinname . "Balance is low, cant transfer:" . $token_balance . "<br/>";
                $this->markEthMoved($tx['id']);
                continue;
            }

            echo $tx['username'] . " has balance of " . $token_balance . $coinname . " moving to main account is " . $CoinInfo['codono_coinaddress'] . "<br/>";
            /*$precheck=json_decode($master->RequiredEthertokenTransfer($tx['username'],$token_balance,$password,$ContractAddress,$dj_decimal));
            if($precheck->status==0)//
            {
                echo "<br/>============================<br/>We need to send ".$precheck->required." ether to ".$tx['username']."<br/>";
                $send_enough_gas=$master->transferFromCoinbase($tx['username'],floatval($precheck->required));
                var_dump($send_enough_gas);
                echo "See above if it received some ether We will try to extract ".$coinname." from it next time<br/>============================<br/>";
            }*/
            $sendrs = $master->transferTokentoCoinbase($tx['username'], $token_balance, $password, $ContractAddress, $dj_decimal);
            echo "<pre>";
            print_r($sendrs);
            echo "</pre>";
            if ($sendrs && !$sendrs->error) {
                $this->markEthMoved($tx['id']);

            } else {
                echo "<br/>Failed Because<br/> <pre>";
                print_r($sendrs);
                echo "</pre>";
            }

            //Save to myzr table that
        }
        echo "Cron Ended";
        exit;
    }


    private function getEthPass($address, $coin)
    {
        $coin_string = $coin . 'b';
        $condition[$coin_string] = $address;
        $password = M('UserCoin')->where(array($coin_string => $address))->getField($coin . '_pass');
        if (!$password) {
            return 0;
        } else {
            return cryptString($password, 'd');
        }

    }

    private function markEthMoved($id)
    {
        $status = M('Myzr')->where(array('id' => $id))->save(array('shifted_to_main' => 1));

        if ($status) {
            return 1;
        } else {
            return 0;
        }

    }
    //eth wallet cron Ends
    //Uses BlockReading Technique :CryptoNote
    public function wallet_cryptonote_deposit()
    {
        if ($_GET['securecode'] != CRON_KEY) {
            die('No Code defined');
        }
        echo "Start<br/>";
        $coinList = M('Coin')->where(array('status' => 1, 'type' => 'cryptonote', 'zr_jz' => 1))->select();

        foreach ($coinList as $k => $v) {
            if ($v['type'] != 'cryptonote') {
                continue;
            }

            $coin = $v['name'];

            if (!$coin) {
                echo 'MM';
                continue;
            }
            $dj_username = C('coin')[$coin]['dj_yh'];
            $dj_password = C('coin')[$coin]['dj_mm'];
            $dj_address = C('coin')[$coin]['dj_zj'];
            $dj_port = C('coin')[$coin]['dj_dk'];
            $dj_decimal = C('coin')[$coin]['cs_qk'];
            $main_address = C('coin')[$coin]['codono_coinaddress'];
            $block = C('coin')[$coin]['block'];

            $cryptonote = CryptoNote($dj_address, $dj_port);
            $open_wallet = $cryptonote->open_wallet($dj_username, $dj_password);

            $json = json_decode($cryptonote->get_height());

            if (!isset($json->height) || $json->error != 0) {
                echo '###ERR#####***** ' . $coin . ' connect fail***** ####ERR####>' . "<br/>";
                continue;
            }

            if ($block > ($json->height)) {
                $block = $json->height - 1;
            }
            $min_height = $block - 30;
            $max_height = $block + 10;


            echo '<br/>***************<b>start Reading ' . $coin . ' on block ' . $block . "</b>***************<br/>";
            echo $coin . ' on CryptoNote,connect ' . (empty($cryptonote) ? 'fail' : 'ok') . ' :' . "<br/>";

            $txs = json_decode($cryptonote->get_transfers('all', 0, '', $min_height, $max_height), true);


            foreach ($txs['in'] as $tx) {
                //Only transfer deposits
                if ($tx['txid']) {

                    //All recipients

                    if ($tx['type'] == 'in') {

                        $coinb = $coin . 'b';
                        $user = M('UserCoin')->where(array($coinb => $tx['address']))->find();
                        if (!$user) continue;

                        $already_processed = M('Myzr')->where(array('txid' => $tx['txid']))->find();
                        if ($already_processed) {
                            echo $tx['address'] . '=>' . $tx['txid'] . ' hash has already been added , Look for another TX' . "<br/>";
                            continue;
                        }

                        //We have found that This tx belongs to someone

                        $to_num = $cryptonote->deAmount($tx['amount']);//Quantity

                        if (M('Myzr')->where(array('txid' => $tx['txid'], 'status' => '1', 'type' => 'cryptonote'))->find()) {
                            echo 'txid had found continue' . "<br/>";
                            continue;
                        }

                        echo 'all check ok ' . "<br/>";

                        echo '<br/>start receive do:' . "<br/>";
                        $sfee = 0;
                        $true_amount = $to_num;

                        if (C('coin')[$coin]['zr_zs']) {
                            $song = round(($to_num / 100) * C('coin')[$coin]['zr_zs'], 8);

                            if ($song) {
                                $sfee = $song;
                                $true_amount = $true_amount + $song;
                            }
                        }

                        // ($trans['confirmations'] < C('coin')[$coin]['zr_dz'])
                        {
                            echo 'All transactions are confirmed' . "<br/>";
                        }

                        $mo = M();
                        $mo->execute('set autocommit=0');
                        $mo->execute('lock tables  codono_user_coin write , codono_myzr  write ');
                        $rs = array();

                        $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $user['userid']))->setInc($coin, $true_amount);

                        if ($res = $mo->table('codono_myzr')->where(array('txid' => $tx['txid']))->find()) {
                            echo 'codono_myzr find and set status 1';
                            $rs[] = $mo->table('codono_myzr')->save(array('id' => $res['id'], 'addtime' => time(), 'status' => 1));
                        } else {
                            echo 'codono_myzr not find and add a new codono_myzr for ' . $tx['address'] . "<br/>";
                            $add_tx = array('userid' => $user['userid'], 'username' => $tx['address'], 'coinname' => $coin, 'fee' => $sfee, 'txid' => $tx['txid'], 'num' => $true_amount, 'mum' => $true_amount, 'addtime' => time(), 'status' => 1);
                            $rs[] = $mo->table('codono_myzr')->add($add_tx);

                        }

                        if (check_arr($rs)) {
                            $mo->execute('commit');
                            echo $true_amount . ' receive ok ' . $coin . ' ' . $true_amount;
                            $mo->execute('unlock tables');
                            echo 'commit ok, Notify customer' . "<br/>";
                            $notify = $this->deposit_notify($user['userid'], $tx['address'], $coin, $tx['txid'], $true_amount, time());
                        } else {
                            echo $true_amount . 'receive fail ' . $coin . ' ' . $true_amount;
                            $mo->execute('rollback');
                            $mo->execute('unlock tables');
                            print_r($rs);
                            echo 'rollback ok' . "<br/>";
                        }
                    }
                }


            }
            //Saving Last read Block
            $block = $block + 1;
            M()->execute("UPDATE `codono_coin` SET  `block` =  '" . $block . "' WHERE name='" . $coin . "' ");

        }
    }

    //blockio Starts Wallet Cron
    public function wallet_blockio_deposit()
    {
        if ($_GET['securecode'] != CRON_KEY) {
            die('No Code defined');
        }
        $coinList = M('Coin')->where(array('status' => 1, 'zr_jz' => 1))->select();

        foreach ($coinList as $k => $v) {
            if ($v['type'] != 'blockio') {
                continue;
            }

            $coin = $v['name'];

            if (!$coin) {
                echo 'MM';
                continue;
            }
            $dj_username = C('coin')[$coin]['dj_yh'];
            $dj_password = C('coin')[$coin]['dj_mm'];
            $dj_address = C('coin')[$coin]['dj_zj'];
            $dj_port = C('coin')[$coin]['dj_dk'];
            echo 'start ' . $coin . "\n";
            $block_io = BlockIO($dj_username, $dj_password, $dj_address, $dj_port, 5, array(), 1);
            $json = $block_io->get_balance();

            if (!isset($json->status) || $json->status != 'success') {
                echo '###ERR#####***** ' . $coin . ' connect fail***** ####ERR####>' . "\n";
                continue;
            }

            echo 'Cmplx ' . $coin . ' start,connect ' . (empty($block_io) ? 'fail' : 'ok') . ' :' . "\n";

            $listtransactions = $block_io->get_transactions(array('type' => 'received', 'labels' => ''));
            echo 'listtransactions:' . count($listtransactions->data->txs) . "\n";
            krsort($listtransactions->data->txs);

            foreach ($listtransactions->data->txs as $tranx) {


                $addr = $tranx->amounts_received[0]->recipient;
                $addr_info = $block_io->get_address_balance(array('addresses' => $addr));
                $account = $addr_info->data->balances[0]->label;

                $trans['account'] = $account;
                $trans['category'] = 'receive';
                $trans['amount'] = $tranx->amounts_received[0]->amount;
                $trans['confirmations'] = $tranx->confirmations;
                $trans['address'] = $tranx->amounts_received[0]->recipient;
                $trans['txid'] = $tranx->txid;


                if (!$trans['account'] || $account == 'default') {
                    echo 'empty account continue' . "\n";
                    continue;
                }

                if (!($user = M('User')->where(array('username' => $trans['account']))->find())) {
                    echo 'no account find continue' . "\n";
                    continue;
                }

                if (M('Myzr')->where(array('txid' => $trans['txid'], 'status' => '1'))->find()) {
                    echo 'txid had found continue' . "\n";
                    continue;
                }

                echo 'all check ok ' . "\n";

                if ($trans['category'] == 'receive') {
                    echo "<pre>";
                    print_r($trans);
                    echo "</pre>";
                    echo '<br/>start receive do:' . "\n";
                    $sfee = 0;
                    $true_amount = $trans['amount'];

                    if (C('coin')[$coin]['zr_zs']) {
                        $song = round(($trans['amount'] / 100) * C('coin')[$coin]['zr_zs'], 8);

                        if ($song) {
                            $sfee = $song;
                            $trans['amount'] = $trans['amount'] + $song;
                        }
                    }

                    if ($trans['confirmations'] < C('coin')[$coin]['zr_dz']) {
                        echo $trans['account'] . ' confirmations ' . $trans['confirmations'] . ' not enough ' . C('coin')[$coin]['zr_dz'] . ' continue ' . "\n";
                        echo 'confirmations <  c_zr_dz continue' . "\n";

                        if ($res = M('myzr')->where(array('txid' => $trans['txid']))->find()) {
                            M('myzr')->save(array('id' => $res['id'], 'addtime' => time(), 'status' => intval($trans['confirmations'] - C('coin')[$coin]['zr_dz'])));
                        } else {
                            M('myzr')->add(array('userid' => $user['id'], 'username' => $trans['address'], 'coinname' => $coin, 'fee' => $sfee, 'txid' => $trans['txid'], 'num' => $true_amount, 'mum' => $trans['amount'], 'addtime' => time(), 'status' => intval($trans['confirmations'] - C('coin')[$coin]['zr_dz'])));

                        }

                        continue;
                    } else {
                        echo 'confirmations full' . "\n";
                    }

                    $mo = M();
                    $mo->execute('set autocommit=0');
                    $mo->execute('lock tables  codono_user_coin write , codono_myzr  write ');
                    $rs = array();
                    $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $user['id']))->setInc($coin, $trans['amount']);

                    if ($res = $mo->table('codono_myzr')->where(array('txid' => $trans['txid']))->find()) {
                        echo 'codono_myzr find and set status 1';
                        $rs[] = $mo->table('codono_myzr')->save(array('id' => $res['id'], 'addtime' => time(), 'status' => 1));
                    } else {
                        echo 'codono_myzr not find and add a new codono_myzr' . "\n";
                        $rs[] = $mo->table('codono_myzr')->add(array('userid' => $user['id'], 'username' => $trans['address'], 'coinname' => $coin, 'fee' => $sfee, 'txid' => $trans['txid'], 'num' => $true_amount, 'mum' => $trans['amount'], 'addtime' => time(), 'status' => 1));

                    }

                    if (check_arr($rs)) {
                        $mo->execute('commit');
                        echo $trans['amount'] . ' receive ok ' . $coin . ' ' . $trans['amount'];
                        $mo->execute('unlock tables');
                        echo 'commit ok, Notify Customer' . "\n";
                        $notify = $this->deposit_notify($user['id'], $trans['address'], $coin, $trans['txid'], $true_amount, time());
                    } else {
                        echo $trans['amount'] . 'receive fail ' . $coin . ' ' . $trans['amount'];
                        echo var_export($rs, true);
                        $mo->execute('rollback');
                        $mo->execute('unlock tables');
                        print_r($rs);
                        echo 'rollback ok' . "\n";
                    }
                }
            }

            if ($trans['category'] == 'send') {
                echo 'start send do:' . "\n";

                if (3 <= $trans['confirmations']) {
                    $myzc = M('Myzc')->where(array('txid' => $trans['txid']))->find();

                    if ($myzc) {
                        if ($myzc['status'] == 0) {
                            M('Myzc')->where(array('txid' => $trans['txid']))->save(array('status' => 1));
                            echo $trans['amount'] . L('Successful Withdrawal') . $coin . ' Coins OK';
                        }
                    }
                }
            }
        }
    }
    //blockio Wallet Cron Ends
    //blockio withdraw Starts Wallet Cron
    public function wallet_blockio_withdraw()
    {
        if ($_GET['securecode'] != CRON_KEY) {
            die('No Code defined');
        }
        $coinList = M('Coin')->where(array('status' => 1))->select();

        foreach ($coinList as $k => $v) {
            if ($v['type'] != 'blockio') {
                continue;
            }

            $coin = $v['name'];

            if (!$coin) {
                echo 'MM';
                continue;
            }
            $dj_username = C('coin')[$coin]['dj_yh'];
            $dj_password = C('coin')[$coin]['dj_mm'];
            $dj_address = C('coin')[$coin]['dj_zj'];
            $dj_port = C('coin')[$coin]['dj_dk'];
            echo 'start ' . $coin . "\n";
            $block_io = BlockIO($dj_username, $dj_password, $dj_address, $dj_port, 5, array(), 1);
            $json = $block_io->get_balance();

            if (!isset($json->status) || $json->status != 'success') {
                echo '###ERR#####***** ' . $coin . ' connect fail***** ####ERR####>' . "\n";
                continue;
            }

            echo 'Cmplx ' . $coin . ' start,connect ' . (empty($block_io) ? 'fail' : 'ok') . ' :' . "\n";

            $listtransactions = $block_io->get_transactions(array('type' => 'sent', 'labels' => ''));
            echo 'listtransactions:' . count($listtransactions->data->txs) . "\n";
            krsort($listtransactions->data->txs);

            foreach ($listtransactions->data->txs as $tranx) {

                $addr = $tranx->amounts_received[0]->recipient;
                $addr_info = $block_io->get_address_balance(array('addresses' => $addr));
                $account = $addr_info->data->balances[0]->label;

                $trans['account'] = $account;
                $trans['category'] = 'send';
                $trans['amount'] = $tranx->amounts_received[0]->amount;
                $trans['confirmations'] = $tranx->confirmations;
                $trans['address'] = $tranx->amounts_received[0]->recipient;
                $trans['txid'] = $tranx->txid;

                if (!$trans['account'] || $account == 'default') {
                    echo ' empty account continue' . "\n";
                    continue;
                }

                if (!($user = M('User')->where(array('username' => $trans['account']))->find())) {
                    echo 'no account find continue' . "\n";
                    continue;
                }

                if (M('Myzr')->where(array('txid' => $trans['txid'], 'status' => '1'))->find()) {
                    echo 'txid had found continue' . "\n";
                    continue;
                }

                echo 'all check ok ' . "\n";


                if ($trans['category'] == 'send') {
                    echo 'start send do:' . "\n";

                    if (3 <= $trans['confirmations']) {
                        $myzc = M('Myzc')->where(array('txid' => $trans['txid']))->find();

                        if ($myzc) {
                            if ($myzc['status'] == 0) {
                                M('Myzc')->where(array('txid' => $trans['txid']))->save(array('status' => 1));
                                echo $trans['amount'] . L('Successful Withdrawal') . $coin . ' Coins OK';
                            }
                        }
                    }
                }
            }
        }
    }
    //blockio withdraw  Wallet Cron Ends
    //Bitcoin QBB type wallets
    public function wallet_v3ryun10u3n4m3()
    {
        if ($_GET['securecode'] != CRON_KEY) {
            die('No Code defined');
        }
        $coinList = M('Coin')->where(array('status' => 1, 'zr_jz' => 1))->select();

        foreach ($coinList as $k => $v) {
            if ($v['type'] != 'qbb') {
                continue;
            }

            $coin = $v['name'];

            if (!$coin) {
                echo 'MM';
                continue;
            }
            $dj_username = C('coin')[$coin]['dj_yh'];
            $dj_password = C('coin')[$coin]['dj_mm'];
            $dj_address = C('coin')[$coin]['dj_zj'];
            $dj_port = C('coin')[$coin]['dj_dk'];
            echo 'start ' . $coin . "\n";
            $CoinClient = CoinClient($dj_username, $dj_password, $dj_address, $dj_port, 5, array(), 1);
            $json = $CoinClient->getinfo();

            if (!isset($json['version']) || !$json['version']) {
                echo '###ERR#####***** ' . $coin . ' connect fail***** ####ERR####>' . "\n";
                continue;
            }

            echo 'Cmplx ' . $coin . ' start,connect ' . (empty($CoinClient) ? 'fail' : 'ok') . ' :' . "\n";
            $listtransactions = $CoinClient->listtransactions('*', 100, 0);
            echo 'listtransactions:' . count($listtransactions) . "\n";
            krsort($listtransactions);

            foreach ($listtransactions as $trans) {
                if (!$trans['account'] && $coin != 'btc') {
                    echo 'empty account continue' . "\n";
                    continue;
                }

                if (!($userid = M('UserCoin')->where(array($coin . 'b' => $trans['address']))->find())) {
                    echo 'no account find continue' . "\n";
                    continue;
                }
                $user = M('User')->where(array('id' => $userid['userid']))->find();

                if (M('Myzr')->where(array('txid' => $trans['txid'], 'status' => '1'))->find()) {
                    echo 'txid had found continue' . "\n";
                    continue;
                }

                echo 'all check ok ' . "\n";

                if ($trans['category'] == 'receive') {
                    echo "<pre>";
                    print_r($trans);
                    echo "</pre>";
                    echo '<br/>start receive do:' . "\n";
                    $sfee = 0;
                    $true_amount = $trans['amount'];

                    if (C('coin')[$coin]['zr_zs']) {
                        $song = round(($trans['amount'] / 100) * C('coin')[$coin]['zr_zs'], 8);

                        if ($song) {
                            $sfee = $song;
                            $trans['amount'] = $trans['amount'] + $song;
                        }
                    }

                    if ($trans['confirmations'] < C('coin')[$coin]['zr_dz']) {
                        echo $trans['account'] . ' confirmations ' . $trans['confirmations'] . ' not elengh ' . C('coin')[$coin]['zr_dz'] . ' continue ' . "\n";
                        echo 'confirmations <  c_zr_dz continue' . "\n";

                        if ($res = M('myzr')->where(array('txid' => $trans['txid']))->find()) {
                            M('myzr')->save(array('id' => $res['id'], 'addtime' => time(), 'status' => intval($trans['confirmations'] - C('coin')[$coin]['zr_dz'])));
                        } else {
                            M('myzr')->add(array('userid' => $user['id'], 'username' => $trans['address'], 'coinname' => $coin, 'fee' => $sfee, 'txid' => $trans['txid'], 'num' => $true_amount, 'mum' => $trans['amount'], 'addtime' => time(), 'status' => intval($trans['confirmations'] - C('coin')[$coin]['zr_dz'])));
                        }

                        continue;
                    } else {
                        echo 'confirmations full' . "\n";
                    }

                    $mo = M();
                    $mo->execute('set autocommit=0');
                    $mo->execute('lock tables  codono_user_coin write , codono_myzr  write ');
                    $rs = array();
                    $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $user['id']))->setInc($coin, $trans['amount']);

                    if ($res = $mo->table('codono_myzr')->where(array('txid' => $trans['txid']))->find()) {
                        echo 'codono_myzr find and set status 1';
                        $rs[] = $mo->table('codono_myzr')->save(array('id' => $res['id'], 'addtime' => time(), 'status' => 1));
                    } else {
                        echo 'codono_myzr not find and add a new codono_myzr' . "\n";
                        $rs[] = $mo->table('codono_myzr')->add(array('userid' => $user['id'], 'username' => $trans['address'], 'coinname' => $coin, 'fee' => $sfee, 'txid' => $trans['txid'], 'num' => $true_amount, 'mum' => $trans['amount'], 'addtime' => time(), 'status' => 1));

                    }

                    if (check_arr($rs)) {
                        $mo->execute('commit');
                        echo $trans['amount'] . ' receive ok ' . $coin . ' ' . $trans['amount'];
                        $mo->execute('unlock tables');
                        echo 'commit ok Notify Customer' . "\n";
                        $notify = $this->deposit_notify($user['id'], $trans['address'], $coin, $trans['txid'], $true_amount, time());
                    } else {
                        echo $trans['amount'] . 'receive fail ' . $coin . ' ' . $trans['amount'];
                        echo var_export($rs, true);
                        $mo->execute('rollback');
                        $mo->execute('unlock tables');
                        print_r($rs);
                        echo 'rollback ok' . "\n";
                    }
                }
            }

            if ($trans['category'] == 'send') {
                echo 'start send do:' . "\n";

                if (3 <= $trans['confirmations']) {
                    $myzc = M('Myzc')->where(array('txid' => $trans['txid']))->find();

                    if ($myzc) {
                        if ($myzc['status'] == 0) {
                            M('Myzc')->where(array('txid' => $trans['txid']))->save(array('status' => 1));
                            echo $trans['amount'] . L('Successful Withdrawal') . $coin . ' Coins OK';
                        }
                    }
                }
            }
        }
    }

    //Coinpayments withdraw Starts Wallet Cron

    public function wallet_coinpay_withdraw()
    {
        if ($_GET['securecode'] != CRON_KEY) {
            die('No Code defined');
        }
        $coinList = M('Coin')->where(array('status' => 1))->select();

        foreach ($coinList as $k => $v) {
            if ($v['type'] != 'coinpay') {
                continue;
            }

            $coin = $v['name'];

            if (!$coin) {
                echo 'MM';
                continue;
            }
            echo '<br/>============******============<br/>start ' . $coin . "<br/>";

            $condition = array('ipn_type' => 'withdrawal', 'currency' => strtoupper($coin), 'funded' => array('neq', "1"));
            $CoinPayIPNresp = M('CoinpayIpn')->where($condition)->order('id asc')->limit(50)->select();
            $count_log = count($CoinPayIPNresp);
            echo "Processing Records:" . $count_log;
            if ($count_log > 0) {
                $coinb = $coin . 'b';

                foreach ($CoinPayIPNresp as $trans) {
                    /*
                            if (!($user = M('UserCoin')->where(array($coinb => $trans['address']))->find())) {
                                     echo 'no user address found continue' . "<br/>";
                                        continue;
                                    }
                      */

                    echo '<br/>all check ok ' . "<br/>";

                    if ($trans['status'] == 2) {
                        if ($myzcid = M('Myzc')->where(array('hash' => $trans['cid'], 'status' => '1'))->find()) {

                            echo 'txid had found saving txid' . "<br/>";
                            M('Myzc')->where(array('id' => $myzcid['id']))->save(array('txid' => $trans['txn_id']));
                            M('CoinpayIpn')->where(array('id' => $trans['id']))->save(array('funded' => '1'));
                            continue;
                        }
                    }
                }// end tx forach
            }//countlog
        }//end coin foreach
    }
    //Coinpay Deposit
    /* this cron checks coinpay_ipn table and proccess records
    IPN code is situated in Home/Controller/IPNController.class.php

    */
    public function wallet_coinpay_deposit()
    {
        if ($_GET['securecode'] != CRON_KEY) {
            die('No Code defined');
        }
        $coinList = M('Coin')->where(array('status' => 1, 'zr_jz' => 1))->select();

        foreach ($coinList as $k => $v) {
            if ($v['type'] != 'coinpay') {
                continue;
            }

            $coin = $v['name'];

            if (!$coin) {
                echo 'MM';
                continue;
            }
            echo '<br/>============******============<br/>start ' . $coin . "<br/>";

            $condition = array('ipn_type' => 'deposit', 'currency' => strtoupper($coin), 'funded' => array('neq', "1"));
            $CoinPayIPNresp = M('CoinpayIpn')->where($condition)->order('id asc')->limit(50)->select();
            $count_log = count($CoinPayIPNresp);
            echo "Processing Records:" . $count_log;
            if ($count_log > 0) {
                $coinb = $coin . 'b';

                foreach ($CoinPayIPNresp as $trans) {
                    $user_condition = array();
                    $user_condition[$coinb] = $trans['address'];
                    if ($trans['dest_tag'] != NULL) {
                        $user_condition[$coin . '_tag'] = $trans['dest_tag'];
                    }

                    if (!($user = M('UserCoin')->where($user_condition)->find())) {

                        echo 'no account found continue' . "<br/>";
                        continue;
                    }
                    if (M('Myzr')->where(array('txid' => $trans['txn_id'], 'status' => '1'))->find()) {
                        echo 'txid had found continue' . "<br/>";
                        M('CoinpayIpn')->where(array('id' => $trans['id']))->save(array('funded' => '1'));
                        continue;
                    }

                    echo '<br/>all check ok ' . "<br/>";

                    if ($trans['status'] == 100) {
                        echo "<pre>";
                        print_r($trans);
                        echo "</pre>";
                        echo '<br/>start receive do:' . "<br/>";
                        $sfee = 0;
                        $true_amount = $trans['amount'];
                        $receivable = $trans['amount'] - $trans['fee']; //Depositable amount
                        if (C('coin')[$coin]['zr_zs']) {
                            $song = round(($trans['amount'] / 100) * C('coin')[$coin]['zr_zs'], 8);

                            if ($song) {
                                $sfee = $song;
                                $trans['amount'] = $trans['amount'] + $song;
                            }
                        }
                        $mo = M();
                        $mo->execute('set autocommit=0');
                        $mo->execute('lock tables  codono_user_coin write , codono_myzr  write ');
                        $rs = array();
                        //Add Balance
                        $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $user['userid']))->setInc($coin, $receivable);
                        $res = $mo->table('codono_myzr')->where(array('txid' => $trans['txn_id']))->find();

                        //continue;
                        if ($res) {
                            echo 'Coinpay myzr found and set status 1<br/>';
                            $rs[] = $mo->table('codono_myzr')->save(array('id' => $res['id'], 'addtime' => time(), 'status' => 1));
                        } else {
                            echo 'myzr entry not found and add new coinpay deposit record<br/>' . "<br/>";
                            $rs[] = $mo->table('codono_myzr')->add(array('userid' => $user['userid'], 'username' => $trans['address'], 'coinname' => $coin, 'fee' => $sfee, 'txid' => $trans['txn_id'], 'num' => $true_amount, 'mum' => $receivable, 'addtime' => time(), 'status' => 1));

                        }

                        if (check_arr($rs)) {

                            echo "Processing CoinpayID:" . $trans['id'] . "<br/>";
                            $mo->execute('commit');
                            echo $trans['amount'] . ' receive ok ' . $coin . ' ' . $trans['amount'];
                            $mo->execute('unlock tables');
                            echo 'commit ok' . "<br/>";
                            M('CoinpayIpn')->where(array('id' => $trans['id']))->save(array('funded' => '1'));
                            $notify = $this->deposit_notify($user['userid'], $trans['address'], $coin, $trans['txn_id'], $true_amount, time());
                        } else {
                            echo $trans['amount'] . 'receive fail ' . $coin . ' ' . $trans['amount'];
                            $mo->execute('rollback');
                            $mo->execute('unlock tables');
                            echo 'rollback ok' . "<br/>";
                        }
                    }
                }
            }
        }
    }

    public function syn_wallet()
    {
    }

    public function tendency_v3ryun10u3n4m3()
    {
        foreach (C('market') as $k => $v) {
            echo '----Computing trend----' . $v['name'] . '------------';
            $tendency_time = 4;
            $t = time();
            $tendency_str = $t - (24 * 60 * 60 * 3);
            $x = 0;

            for (; $x <= 18; $x++) {
                $na = $tendency_str + (60 * 60 * $tendency_time * $x);
                $nb = $tendency_str + (60 * 60 * $tendency_time * ($x + 1));
                $b = M('TradeLog')->where('addtime >=' . $na . ' and addtime <' . $nb . ' and market =\'' . $v['name'] . '\'')->max('price');

                if (!$b) {
                    $b = 0;
                }

                $rs[] = array($na, $b);
            }

            M('Market')->where(array('name' => $v['name']))->setField('tendency', json_encode($rs));
            unset($rs);
            echo 'Computing success!';
            echo "\n";
        }

        echo 'Trend Calculation 0k ' . "\n";
    }

    public function calc_liq_market_stats()
    {

        foreach (C('market') as $k => $v) {

            if (array_key_exists(strtoupper($v['name']), LIQUIDITY_ARRAY)) {
                echo "<b>" . strtoupper($v['name']) . " is under liqiuidity</b><br/>";
                $this->liq_market_stats($v['name']);

            } else {
                echo strtoupper($v['name']) . " is not under liqiuidity<br/>";
            }
        }

        echo 'Calculate Market 0k ' . "\n";
    }

    private function liq_market_stats($market)
    {
        $round = C('market')[$market]['round'];
        $new_price = format_num(M('TradeLog')->where(array('market' => $market, 'status' => 1, 'addtime' => array('gt', time() - (60 * 60 * 24))))->order('id desc')->limit(100)->getField('price'), $round);
        $buy_price = format_num(M('Trade')->where(array('type' => 1, 'market' => $market, 'status' => 0, 'addtime' => array('gt', time() - (60 * 60 * 24))))->order('id desc')->limit(100)->max('price'), $round);
        $sell_price = format_num(M('Trade')->where(array('type' => 2, 'market' => $market, 'status' => 0, 'addtime' => array('gt', time() - (60 * 60 * 24))))->order('id desc')->limit(100)->min('price'), $round);
        $min_price = format_num(M('TradeLog')->where(array(
            'market' => $market,
            'addtime' => array('gt', time() - (60 * 60 * 24))
        ))->order('id desc')->limit(100)->min('price'), $round);
        $max_price = format_num(M('TradeLog')->where(array(
            'market' => $market,
            'addtime' => array('gt', time() - (60 * 60 * 24))
        ))->order('id desc')->limit(100)->max('price'), $round);
        $volume = format_num(M('TradeLog')->where(array(
            'market' => $market,
            'addtime' => array('gt', time() - (60 * 60 * 24))
        ))->order('id desc')->limit(100)->sum('num'), $round);
        $sta_price = format_num(M('TradeLog')->where(array(
            'market' => $market,
            'status' => 1,
            'addtime' => array('gt', time() - (60 * 60 * 24))
        ))->order('id desc')->limit(100)->getField('price'), $round);
        $Cmarket = M('Market')->where(array('name' => $market))->find();

        if ($Cmarket['new_price'] != $new_price) {
            $upCoinData['new_price'] = $new_price;
        }

        if ($Cmarket['buy_price'] != $buy_price) {
            $upCoinData['buy_price'] = $buy_price;
        }

        if ($Cmarket['sell_price'] != $sell_price) {
            $upCoinData['sell_price'] = $sell_price;
        }

        if ($Cmarket['min_price'] != $min_price) {
            $upCoinData['min_price'] = $min_price;
        }

        if ($Cmarket['max_price'] != $max_price) {
            $upCoinData['max_price'] = $max_price;
        }

        if ($Cmarket['volume'] != $volume) {
            $upCoinData['volume'] = $volume;
        }

        $change = format_num((($new_price - $Cmarket['hou_price']) / $Cmarket['hou_price']) * 100, 4);
        $upCoinData['change'] = $change;

        if ($upCoinData) {
            M('Market')->where(array('name' => $market))->save($upCoinData);
            M('Market')->execute('commit');
            S('home_market', null);
        }
    }

    public function chart_v3ryun10u3n4m3()
    {
        $the_time_start = microtime(true);
        foreach (C('market') as $k => $v) {

            if (array_key_exists(strtoupper($v['name']), LIQUIDITY_ARRAY)) {
                echo strtoupper("<br/".$v['name']) . " is liqiuidity chart will be prepare separately<br/>";
            } else {
                $this->setTradeJson($v['name']);
            }
        }

        echo "<br/><b>Calculate Market 0k </b>";
        $the_time_end = microtime(true);

        //dividing with 60 will give the execution time in minutes otherwise seconds
        $total_execution_time = ($the_time_end - $the_time_start) / 60;

        //execution time of the script
        echo "<br/><b>Total Execution Time:</b>  $total_execution_time  Mins";
    }

    private function setTradeJson($market)
    {
        $cron_time_start = microtime(true);
        $timearr = array(1, 3, 5, 10, 15, 30, 60, 120, 240, 360, 720, 1440, 10080);
        echo "<br/>==========================";
        foreach ($timearr as $k => $v) {
            echo "<br/>$market for $v min charts";
            $tradeJson = M('TradeJson')->where(array(
                'market' => $market,
                'type' => $v
            ))->order('id desc')
                ->find();

            if ($tradeJson) {
                $addtime = $tradeJson['addtime'];
            } else {
                $addtime = M('TradeLog')->where(array(
                    'market' => $market
                ))->order('id asc')
                    ->getField('addtime');
            }
            $youtradelog = null;
            if ($addtime) {
                $youtradelog = M('TradeLog')->where('addtime >= %d and market =\'%s\'', $addtime, $market)->sum('num');
            }

            if ($youtradelog) {
                if ($v == 1) {
                    $start_time = $addtime;
                } else {
                    $start_time = mktime(date('H', $addtime), floor(date('i', $addtime) / $v) * $v, 0, date('m', $addtime), date('d', $addtime), date('Y', $addtime));
                }

                $x = 0;

                for (; $x <= 20; $x++) {
                    $na = $start_time + (60 * $v * $x);
                    $nb = $start_time + (60 * $v * ($x + 1));

                    if (time() < $na) {
                        break;
                    }

                    $sum = M('TradeLog')->where('addtime >= %d and addtime < %d and market =\'%s\'', $na, $nb, $market)->sum('num');

                    if ($sum) {
                        $sta = M('TradeLog')->where('addtime >= %d and addtime < %d and market =\'%s\'', $na, $nb, $market)->order('id asc')->getField('price');
                        $max = M('TradeLog')->where('addtime >= %d and addtime < %d and market =\'%s\'', $na, $nb, $market)->max('price');
                        $min = M('TradeLog')->where('addtime >= %d and addtime < %d and market =\'%s\'', $na, $nb, $market)->min('price');
                        $end = M('TradeLog')->where('addtime >= %d and addtime < %d and market =\'%s\'', $na, $nb, $market)->order('id desc')->getField('price');
                        $d = array($na, $sum, $sta, $max, $min, $end); //date,qty,open,high,low,close
                        if (M('TradeJson')->where(array('market' => $market, 'addtime' => $na, 'type' => $v))->find()) {
                            M('TradeJson')->where(array('market' => $market, 'addtime' => $na, 'type' => $v))->save(array('data' => json_encode($d)));
                        } else {
                            M('TradeJson')->add(array('market' => $market, 'data' => json_encode($d), 'addtime' => $na, 'type' => $v));
                            M('TradeJson')->execute('commit');
                        }
                    }
                }
            }
        }

        $time_end = microtime(true);

        //dividing with 60 will give the execution time in minutes otherwise seconds
        $execution_time = ($time_end - $cron_time_start) / 60;

        //execution time of the script
        echo "<br/><b>$market Execution Time:</b>  $execution_time  Mins";
        echo "<br/>==========================";
    }

    private function FindWavesAssetID($assetid)
    {
        //	$assetid="4Zekj8nGpwzuEw1B7JFxLnDWmfcaFHyD8ZZKVWLwBbUC";
        $assetid = trim($assetid);
        $assetid = $assetid ? $assetid : "";
        $coin = M('Coin')->field('name')->where(array('type' => 'waves', 'status' => 1, 'dj_yh' => $assetid))->find();
        return ($coin['name'] ? $coin['name'] : null);
    }

    //Moving Funds from some address to main address [This is for moving assets only  to main account ] Run me first
    public function MoveFundsToWaveMainAccount($name = '')
    {
        if ($_GET['securecode'] != CRON_KEY) {
            die('No Code defined');
        }
        if ($name != '') {
            $cond['name'] = 'waves';
        }
        $min_asset_required = 1;
        $min_balance_required = 0.002;
        if (!check($name, 'a')) {
            die('invalidcoin');
        }
        $cond['name'] = (string)$name;
        $coinList = M('Coin')->where($cond)->select();

        foreach ($coinList as $k => $v) {
            if ($v['type'] != 'waves') {
                continue;
            }
            if ($v['name'] == 'waves') {
                continue;
            }
            $coin = $v;
            $coinname = $coin['name'];
            $dj_username = $coin['dj_yh'];
            $dj_password = $coin['dj_mm'];
            $dj_address = $coin['dj_zj'];
            $dj_port = $coin['dj_dk'];
            $main_address = $coin['codono_coinaddress'];
            $dj_decimal = $coin['cs_qk'];

            echo "<br/>*************Moving all <b>" . $coinname . "</b> funds to " . $main_address . "*************<br/>";

            $waves = WavesClient($dj_username, $dj_password, $dj_address, $dj_port, $dj_decimal, 5, array(), 1);
            $record = json_decode($waves->ValidateAddress($main_address), true);
            if ($record['valid'] == false) {
                exit;
            } else {
                echo $main_address . " is valid <br/>";
            }
            $condition['status'] = 1;
            //$condition['shifted_to_main']=0;
            $condition['coinname'] = $coinname;
            $transactions = M('Myzr')->where($condition)->distinct(true)->field('username')->order('id')->limit(50)->select();

            foreach ($transactions as $txs) {
                echo "<br/>Transferrring <pre>";
                print_r($txs);
                echo "</pre>";

                $addr = $txs['username'];
                if ($addr == $main_address) {
                    continue;
                }
                $info = json_decode($waves->Balance($addr, $dj_username), true);
                $dj_decimal = $dj_decimal ? $dj_decimal : 8;
                $total_balance = $waves->deAmount($info['balance'], $dj_decimal);
                $waves_balance_of_customer = json_decode($waves->Balance($addr, ''), true);
                $waves_balance_of_customer_i = $waves->deAmount($waves_balance_of_customer['balance'], $dj_decimal);
                //Check if User address has enough coins , send them to main account.
                if ($dj_username) {
                    $min_balance_required = 1;
                }
                if ($total_balance > $min_balance_required) {
                    if ($dj_username) {
                        if ($waves_balance_of_customer_i < 0.001) {

                            //Transfer 0.001 wav to customer first

                            echo "<br/>Balance of user is low:" . $waves_balance_of_customer_i . 'Now sending 0.001 wave';
                            $tx_required_wave = $waves->Send($main_address, $addr, 0.001);
                            print_r($tx_required_wave);
                        } else {
                            print_r($waves_balance_of_customer);
                            echo "<br/> User Waves Balance is " . $waves_balance_of_customer_i . "<br/>";
                        }
                    }
                    echo "<br/>Now moving " . $addr . " balance of " . $total_balance . " " . $coinname . " decimals" . $dj_decimal . "<br/>";
                    $asset_balance = $total_balance;
                    if ($coinname == 'waves') {
                        $total_balance = ($info['balance'] - 0.001);
                        $wavesend_response = $waves->Send($addr, $main_address, $total_balance);
                    } else {

                        $wavesend_response = $waves->Send($addr, $main_address, $total_balance, $dj_username);
                    }
                    echo "<pre>";
                    print_r($wavesend_response);
                    echo "</pre>";
                } else {
                    echo $total_balance . "<" . $min_balance_required;
                }
                $where_condition = array('username' => $txs['username']);
                echo "Updating for " . $txs['username'];
                M('Myzr')->where($where_condition)->save(array('shifted_to_main' => 1));
            }//end of txs loop
            //marktx as shifted_to_main

        }//end of coins loop
        echo "End of cron";
        //return ($coin['name']?$coin['name']:null);
    }

    // [This is for moving waves to main account] run me 2nd
    public function MoveWaves2MainAccount($name = '')
    {
        if ($_GET['securecode'] != CRON_KEY) {
            die('No Code defined');
        }
        if ($name != '') {
            $cond['name'] = 'waves';
        }


        $min_asset_required = 1;
        $min_balance_required = 0.002;
        $coinList = M('Coin')->where($cond)->select();

        foreach ($coinList as $k => $v) {
            if ($v['type'] != 'waves') {
                continue;
            }
            if ($v['name'] != 'waves') {
                continue;
            }
            $coin = $v;
            $coinname = $coin['name'];
            $dj_username = $coin['dj_yh'];
            $dj_password = $coin['dj_mm'];
            $dj_address = $coin['dj_zj'];
            $dj_port = $coin['dj_dk'];
            $main_address = $coin['codono_coinaddress'];
            $dj_decimal = $coin['cs_qk'];
            echo "<br/>*************Moving all <b>" . $coinname . "</b> funds to " . $main_address . "*************<br/>";
			
					if ($_GET['agree'] != 'yes') {
        $agree_url=$_SERVER['REQUEST_URI']."/agree/yes";
		die( "<span style='color:red'>Warning:This tool will move all your <b>$coinname</b> funds to $main_address ,  <a href='$agree_url'> Do you agree?</a></span>");}

            $waves = WavesClient($dj_username, $dj_password, $dj_address, $dj_port, $dj_decimal, 5, array(), 1);
            $record = json_decode($waves->ValidateAddress($main_address), true);
            if ($record['valid'] == false) {
                echo "Please check main account";
                exit;
                continue;
            } else {
                echo $main_address . " is valid All funds will be moved there<br/>";
            }
            $condition['status'] = 1;
            $condition['shifted_to_main'] = 1;
            $condition['coinname'] = $coinname;
            $transactions = M('Myzr')->where($condition)->order('id asc')->limit(50)->select();

            foreach ($transactions as $txs) {


                $addr = $txs['username'];
                if ($addr == $main_address) {
                    continue;
                }
                $info = json_decode($waves->Balance($addr), true);
                $the_bal = ($info['balance'] - 100000);
                $total_balance = $waves->deAmount($the_bal, 8);

                if ($total_balance < $min_balance_required) {
                    echo "<br/>" . $addr . " has  Low balance:" . $total_balance . " skipping to next address<br/>";
                    continue;
                }
                $total_balance = ($the_bal);
                echo "<br/>Now moving " . $addr . " balance of " . $total_balance . " " . $coinname . " decimals 8" . "<br/>";

                $wavesend_response = $waves->SendWavesJustForMain($addr, $main_address, $total_balance);


                echo "<pre>";
                print_r($wavesend_response);
                echo "</pre>";

            }//end of txs loop
            //marktx as shifted_to_main
            $status = M('Myzr')->where(array('id' => $txs['id']))->save(array('shifted_to_main' => 1));
        }//end of coins loop
        echo "End of cron";
        //return ($coin['name']?$coin['name']:null);
    }

    //Uses BlockReading Technique :Good for big exchanges
    public function wallet_waves_deposit()
    {
        if ($_GET['securecode'] != CRON_KEY) {
            die('No Code defined');
        }
        $coinList = M('Coin')->where(array('status' => 1, 'type' => 'waves', 'zr_jz' => 1))->select();

        foreach ($coinList as $k => $v) {
            if ($v['name'] != 'waves') {
                continue;
            }

            $coin = $v['name'];

            if (!$coin) {
                echo 'MM';
                continue;
            }
            $dj_username = C('coin')[$coin]['dj_yh'];
            $dj_password = C('coin')[$coin]['dj_mm'];
            $dj_address = C('coin')[$coin]['dj_zj'];
            $dj_port = C('coin')[$coin]['dj_dk'];
            $dj_decimal = C('coin')[$coin]['cs_qk'];
            $main_address = C('coin')[$coin]['codono_coinaddress'];
            $block = C('coin')[$coin]['block'];

            $waves = WavesClient($dj_username, $dj_password, $dj_address, $dj_port, $dj_decimal, 5, array(), 1);
            $waves_coin = strtoupper($coin);
            $information = json_decode($waves->status(), true);
            if ($information['blockchainHeight'] && $information['blockchainHeight'] <= 0) {
                echo '###ERR#####***** Waves connect fail***** ####ERR####>' . "<br/>";
                continue;
            }
            if ($block >= ($information['blockchainHeight'] - 1)) {
                $block = $information['blockchainHeight'] - 1;
            }

            echo 'start Reading ' . $coin . ' on block ' . $block . "<br/>";
            echo 'Cmplx Waves start,connect ' . (empty($waves) ? 'fail' : 'ok') . ' :' . "<br/>";

            $txs = json_decode($waves->GetAtHeight($block), true);

            foreach ($txs['transactions'] as $tx) {
                //Only transfer deposits
                if ($tx['type'] == 4) {

                    //All recipients
                    $record = json_decode($waves->ValidateAddress($tx['recipient']), true);
                    if ($record['valid'] == true && $record['address'] == $tx['recipient']) {


                        $user = M('UserCoin')->where(array('wavesb' => $tx['recipient']))->find();

                        if (!$user) continue;

                        $already_processed = M('Myzr')->where(array('txid' => $tx['id']))->find();
                        if ($already_processed) {
                            echo $tx['recipient'] . '=>' . $tx['id'] . ' hash has already been added , Look for another TX' . "<br/>";
                            continue;
                        }

                        //We have found that This tx belongs to someone

                        //Now lets find if There is such an asset , and which is the coins , WAVES, MAIS or any token_get_all

                        $coin = $this->FindWavesAssetID($tx['assetId']);

                        if ($coin == NULL) {
                            // There was some transaction Where token was not listed by user, inform admin about this tx and check another tx
                            $this->InformAdmin('waves:' . $tx['id']);
                            continue;
                        }

                        $to_num = $waves->deAmount($tx['amount'], $dj_decimal);//Quantity


                        if (M('Myzr')->where(array('txid' => $tx['id'], 'status' => '1', 'type' => 'waves'))->find()) {
                            echo 'txid had found continue' . "<br/>";
                            continue;
                        }

                        echo 'all check ok ' . "<br/>";

                        echo '<br/>start receive do:' . "<br/>";
                        $sfee = 0;
                        $true_amount = $to_num;

                        if (C('coin')[$coin]['zr_zs']) {
                            $song = round(($to_num / 100) * C('coin')[$coin]['zr_zs'], 8);

                            if ($song) {
                                $sfee = $song;
                                $true_amount = $true_amount + $song;
                            }
                        }

                        // ($trans['confirmations'] < C('coin')[$coin]['zr_dz'])
                        {
                            echo 'All transactions are confirmed' . "<br/>";
                        }

                        $mo = M();
                        $mo->execute('set autocommit=0');
                        $mo->execute('lock tables  codono_user_coin write , codono_myzr  write ');
                        $rs = array();

                        $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $user['userid']))->setInc($coin, $true_amount);

                        if ($res = $mo->table('codono_myzr')->where(array('txid' => $tx['id']))->find()) {
                            echo 'codono_myzr find and set status 1';
                            $rs[] = $mo->table('codono_myzr')->save(array('id' => $res['id'], 'addtime' => time(), 'status' => 1));
                        } else {
                            echo 'codono_myzr not find and add a new codono_myzr for ' . $tx['recipient'] . "<br/>";
                            $add_tx = array('userid' => $user['userid'], 'username' => $tx['recipient'], 'coinname' => $coin, 'fee' => $sfee, 'txid' => $tx['id'], 'num' => $true_amount, 'mum' => $true_amount, 'addtime' => time(), 'status' => 1);
                            $rs[] = $mo->table('codono_myzr')->add($add_tx);

                        }

                        if (check_arr($rs)) {
                            $mo->execute('commit');
                            echo $true_amount . ' receive ok ' . $coin . ' ' . $true_amount;
                            $mo->execute('unlock tables');
                            echo 'commit ok' . "<br/>";
                            $notify = $this->deposit_notify($user['userid'], $tx['recipient'], $coin, $tx['id'], $true_amount, time());
                        } else {
                            echo $true_amount . 'receive fail ' . $coin . ' ' . $true_amount;
                            $mo->execute('rollback');
                            $mo->execute('unlock tables');
                            print_r($rs);
                            echo 'rollback ok' . "<br/>";
                        }
                    }
                }


            }
            //Saving Last read Block
            $block = $block + 1;
            M()->execute("UPDATE `codono_coin` SET  `block` =  '" . $block . "' WHERE name='" . $coin . "' ");
            exit();
        }
    }

    //Waves Wallet Cron Ends
    private function InformAdmin($info)
    {
        //Do something , You can send a spefic notification to admin , It be  either email sms , or anything
    }


    public function move2cold()
    {
        if ($_GET['securecode'] != CRON_KEY) {
            die('No Code defined');
        }
        $cold_Wallets_count = sizeof(COLD_WALLET);
        if ($cold_Wallets_count == 0) {
            die('NO Cold wallets defined in Config ');
        } else {
            echo "*****************************" . $cold_Wallets_count . " wallet/s found*****************************<br/>";
        }


        foreach (COLD_WALLET as $coinname => $values) {
            echo "========================Now checking Coldwallet move for $coinname! wallet/s found!======================<br/>";
            $coldwallet_info = COLD_WALLET[strtoupper($coinname)];
            if (!$coldwallet_info || substr_count($coldwallet_info, ':') != 2) {
                echo 'error' . 'NO COLD WALLET DEFINED';
                continue;
            }

			
            $info = explode(":", $coldwallet_info);

            $coldwallet_address = $info[0];
            $maxkeep = $info[1];
            $minsendrequired = $info[2];
			
			
						if ($_GET['agree'] != 'yes') {
        $agree_url=$_SERVER['REQUEST_URI']."/agree/yes";
		die( "<span style='color:red'>Warning:This tool will move all your <b>$coinname</b> funds to $coldwallet_address ,  <a href='$agree_url'> Do you agree?</a></span>");}

            $tobesent = 1; //calculate balance-maxkeep
            //check if  tobesent> minsendrequired then only proceed
            $coinname = strtolower($coinname);
            $dj_username = C('coin')[$coinname]['dj_yh'];
            $dj_password = C('coin')[$coinname]['dj_mm'];
            $dj_address = C('coin')[$coinname]['dj_zj'];
            $dj_port = C('coin')[$coinname]['dj_dk'];
            $dj_decimal = C('coin')[$coinname]['cs_qk'];
            $main_address = C('coin')[$coinname]['codono_coinaddress'];
            $explorer_link = C('coin')[$coinname]['js_wk'];
            echo "========================See <b>$coldwallet_address</b> on blockchain explorer for more details!======================<br/>";
            $Coin = M('Coin')->where(array('name' => $coinname))->find();
            //Ethereum and ERC20
            if (C('coin')[$coinname]['type'] == 'eth') {
                $erc20_contract = $dj_username; //Contract Address
                $dj_password = cryptString(C('coin')[$coinname]['dj_mm'], 'd');

                $EthCommon = new \Org\Util\EthCommon($dj_address, $dj_port, "2.0");
                $EthPayLocal = new \Org\Util\EthPayLocal($dj_address, $dj_port, "2.0", $erc20_contract, $dj_password);

                $master = EthMaster($dj_address, $dj_port, $main_address, $dj_password, $erc20_contract);

                //Check if coin is ERC20
                if ($erc20_contract) {
                    $bal_token = $master->balanceOfToken($main_address, $erc20_contract, $dj_decimal);
                    $token_balance = format_num($bal_token, 8);

                    $tobesent = $token_balance - $maxkeep;
                    if ((int)$token_balance < 0.000000001) {

                        echo $coinname . " Balance is low, cant transfer:" . $token_balance . '!<br/>';
                        continue;
                    }
                    if ($tobesent < $minsendrequired) {
                        echo 'You have ' . $token_balance . $coinname . ' but you want to send ' . $tobesent . ' minimum tx should be ' . $minsendrequired . '!<br/>';
                        continue;
                    }

                    //Contract Address transfer out
                    $zhuan['fromaddress'] = $main_address;
                    $zhuan['toaddress'] = $coldwallet_address;
                    $zhuan['token'] = $erc20_contract;
                    $zhuan['type'] = $coinname;
                    $zhuan['amount'] = (double)$tobesent;
                    $zhuan['password'] = $dj_password;
                    $sendrs = $master->transferToken($zhuan['toaddress'], $zhuan['amount'], $zhuan['token'], $dj_decimal);
                } else {

                    //eth
                    $ubal = $this->floordec($master->balanceOf($main_address, $erc20_contract), 3);
                    $tobesent = $ubal - $maxkeep;

                    if ($tobesent < $minsendrequired) {
                        echo 'You have ' . $ubal . $coinname . ' but you want to send ' . $tobesent . ' minimum tx should be ' . $minsendrequired . '!<br/>';
                        continue;
                    }
                    $zhuan['fromaddress'] = $main_address;
                    $zhuan['toaddress'] = $coldwallet_address;
                    $zhuan['amount'] = (double)$tobesent;
                    $zhuan['password'] = $dj_password;
                    $sendrs = $master->transferFromCoinbase($zhuan['toaddress'], (double)$tobesent);
                }

                if ($sendrs) {
                    $flag = 1;
                    $arr = json_decode($sendrs, true);
                    $hash = $arr['result'] ? $arr['result'] : $arr['error']['message'];

                    if (isset($arr['status']) && ($arr['status'] == 0)) {
                        $flag = 0;
                    }
                } else {
                    $flag = 0;
                }

                if (!$flag) {
                    echo 'wallet server Withdraw currency failure!<br/>';
                    continue;
                } else {
                    echo 'Transfer success!' . $hash . '<br/>';
                    continue;
                }
            }
            //Bitcoin type starts qbb

            if (C('coin')[$coinname]['type'] == 'qbb') {

                $CoinClient = CoinClient($dj_username, $dj_password, $dj_address, $dj_port, 5, array(), 1);
                $json = $CoinClient->getinfo();

                if (!isset($json['version']) || !$json['version']) {

                    echo "System can not connect to $coinname node<br/>";
                    continue;
                }
                $daemon_balance = $CoinClient->getbalance();
                $tobesent = $daemon_balance - $maxkeep;
                if ($tobesent < $minsendrequired) {

                    echo "<br/>You have $daemon_balance $coinname  but balance of $coinname is required to keep $maxkeep and minimum per tx is $minsendrequired !<br/>";
                    continue;
                }

                $sendrs = $CoinClient->sendtoaddress($coldwallet_address, (double)$tobesent);

                if ($sendrs) {
                    $flag = 1;
                    $arr = json_decode($sendrs, true);

                    if (isset($arr['status']) && ($arr['status'] == 0)) {
                        $flag = 0;
                    }
                } else {
                    $flag = 0;
                }

                if (!$flag) {
                    echo 'wallet server Withdraw currency failure!<br/>';
                    continue;
                } else {
                    echo 'Transfer success!' . $sendrs . '<br/>';
                    continue;
                }
            } else {
                echo $coinname . 'Coin Type is not compatible for Cold wallet transfer!<br/>';
                continue;
            }
        }
        echo "*****************************CRON ENDS*****************************<br/>";
    }
	
	public function fix_user_coin(){
	  if ($_GET['securecode'] != CRON_KEY) {
            die('No Code defined');
        }
	if ($_GET['agree'] != 'yes') {
        $agree_url=$_SERVER['REQUEST_URI']."/agree/yes";
		die( "<span style='color:red'>Warning this tool will modify you user_coin table , this is very powerful,  <a href='$agree_url'> Do you agree?</a></span>");}	
		
	$sql="SELECT (column_name) as name FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='".DB_NAME."' AND TABLE_NAME='codono_user_coin'";		
			 $mo = M();
			 $ucoins = $mo->query($sql);
			foreach ($ucoins as $ucoin ) {
    $cnames[] = $ucoin['name'];}
		
		foreach (C('coin') as $coin){
			$name=$coin['name'];
			$address=$coin['name'].'b';
			$balance=$coin['name'].'d';
			$present=in_array($name,$cnames);
			if($present==0){
			echo "$name is not present so creating it<br/>";
					$rea = M()->execute('ALTER TABLE  `codono_user_coin` ADD  `' . $name . '` DECIMAL(20,8) UNSIGNED  NULL DEFAULT "0.00000000"');
					$reb = M()->execute('ALTER TABLE  `codono_user_coin` ADD  `' . $balance . '` DECIMAL(20,8) UNSIGNED NULL DEFAULT "0.00000000" ');
					$rec = M()->execute('ALTER TABLE  `codono_user_coin` ADD  `' . $address . '` VARCHAR(200) DEFAULT NULL ');
			}else{
				echo "$name is present Skipping to next<br/>";
			}
		}
	}
}

?>