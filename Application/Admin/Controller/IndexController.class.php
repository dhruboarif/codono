<?php

namespace Admin\Controller;

class IndexController extends AdminController
{
    public function index()
    {
        //$this->checkUpdata();
        $arr = array();
        $arr['reg_sum'] = M('User')->count();
        $arr['usd_num'] = M('UserCoin')->sum('usd') + M('UserCoin')->sum('usdd');
        $arr['trance_mum'] = M('TradeLog')->sum('mum');

        if (10000 < $arr['trance_mum']) {
            $arr['trance_mum'] = round($arr['trance_mum'] / 10000) . 'M';
        }

        if (100000000 < $arr['trance_mum']) {
            $arr['trance_mum'] = round($arr['trance_mum'] / 100000000) . 'B';
        }

        $arr['art_sum'] = M('Article')->count();
        $data = array();
        $time = mktime(0, 0, 0, date('m'), date('d'), date('Y')) - (29 * 24 * 60 * 60);
        $i = 0;

        for (; $i < 30; $i++) {
            $a = $time;
            $time = $time + (60 * 60 * 24);
            $date = addtime($time - (60 * 60), 'Y-m-d');
            $mycz = M('Mycz')->where(array(
                'status' => array('neq', 0),
                'addtime' => array(
                    array('gt', $a),
                    array('lt', $time)
                )
            ))->sum('num');
            $mytx = M('Mytx')->where(array(
                'status' => 1,
                'addtime' => array(
                    array('gt', $a),
                    array('lt', $time)
                )
            ))->sum('num');

            if ($mycz || $mytx) {
                $data['cztx'][] = array('date' => $date, 'charge' => $mycz, 'withdraw' => $mytx);
            }
        }

        $time = time() - (30 * 24 * 60 * 60);
        $i = 0;

        for (; $i < 60; $i++) {
            $a = $time;
            $time = $time + (60 * 60 * 24);
            $date = addtime($time, 'Y-m-d');
            $user = M('User')->where(array(
                'addtime' => array(
                    array('gt', $a),
                    array('lt', $time)
                )
            ))->count();

            if ($user) {
                $data['reg'][] = array('date' => $date, 'sum' => $user);
            }
        }

        $this->assign('cztx', json_encode($data['cztx']));
        $this->assign('reg', json_encode($data['reg']));
        $this->assign('arr', $arr);

        $this->display();
    }

    public function coin($coinname = NULL)
    {

        if (!$coinname) {
            $coinname = C('xnb_mr');
        }

        if (empty($coinname)) {

            $data['coin_message'] = " Go to Config > Coin>  and Set a default Coin, and clean up the cache";
            $this->assign('data', $data);
            $this->display("Errors/coins");
            exit();
        }

        if (!M('Coin')->where(array('name' => $coinname))->find()) {
            $data['coin_message'] = "No coins exist, Go to Config > Coin>  and Add Coin, and clean up the cache";
            $this->assign('data', $data);
            $this->display("Errors/coins");
            exit();
        }

        $this->assign('coinname', $coinname);
        $data = array();
        $data['trance_b'] = M('UserCoin')->sum($coinname);
        $data['trance_s'] = M('UserCoin')->sum($coinname . 'd');
        $data['trance_num'] = $data['trance_b'] + $data['trance_s'];
        $data['trance_song'] = M('Myzr')->where(array('coinname' => $coinname))->sum('fee');
        $data['trance_fee'] = M('Myzc')->where(array('coinname' => $coinname))->sum('fee');
	
        if (C('coin')[$coinname]['type'] == 'qbb') {
            $dj_username = C('coin')[$coinname]['dj_yh'];
            $dj_password = C('coin')[$coinname]['dj_mm'];
            $dj_address = C('coin')[$coinname]['dj_zj'];
            $dj_port = C('coin')[$coinname]['dj_dk'];
            $CoinClient = CoinClient($dj_username, $dj_password, $dj_address, $dj_port, 5, array(), 1);
            $json = $CoinClient->getinfo();

            if (!isset($json['version']) || !$json['version']) {
                $this->error('Wallet link failure!');
            }

            $data['trance_mum'] = $json['balance'];
        }else if (C('coin')[$coinname]['type'] == 'blockio') {
            $dj_username = C('coin')[$coinname]['dj_yh'];
            $dj_password = C('coin')[$coinname]['dj_mm'];
            $dj_address = C('coin')[$coinname]['dj_zj'];
            $dj_port = C('coin')[$coinname]['dj_dk'];
            $BlockIO = BlockIO($dj_username, $dj_password, $dj_address, $dj_port, 5, array(), 1);
            $json = $BlockIO->get_balance();

            if (!isset($json->status) || $json->status!='success') {
            	$this->error($coinname.'Wallet link failure!');
            }

            $data['trance_mum'] = $json->data->available_balance;
        }else if (C('coin')[$coinname]['type'] == 'cryptonote') {
            $dj_username = C('coin')[$coinname]['dj_yh'];
            $dj_password = C('coin')[$coinname]['dj_mm'];
            $dj_address = C('coin')[$coinname]['dj_zj'];
            $dj_port = C('coin')[$coinname]['dj_dk'];
			$cryptonote = CryptoNote($dj_address, $dj_port);
			$open_wallet = $cryptonote->open_wallet($dj_username,$dj_password);
		    $json=json_decode($cryptonote->get_height());
               if (!isset($json->height) || $json->error!=0) {
                       $this->error('CryptoNote Connection Failed for '.$coinname);
               }
			   $data['trance_mum']  =$cryptonote->deAmount($open_wallet->unlocked_balance);
        }else if (C('coin')[$coinname]['type'] == 'waves') {
            $dj_username = C('coin')[$coinname]['dj_yh'];
            $dj_password = C('coin')[$coinname]['dj_mm'];
            $dj_address = C('coin')[$coinname]['dj_zj'];
            $dj_port = C('coin')[$coinname]['dj_dk'];
			$dj_decimal = C('coin')[$coinname]['cs_qk'];
			$waves = WavesClient($dj_username, $dj_password, $dj_address, $dj_port,$dj_decimal, 5, array(), 1);
            $json = json_decode($waves->status(),true);
			$coinpay_coin=strtoupper($coinname);
			

            if (!isset($json['blockchainHeight']) || $json['blockchainHeight']<=0) {
            	$this->error($coinname.'Wallet link failure!');
            }

            $data['trance_mum'] ='NA';
        }else if (C('coin')[$coinname]['type'] == 'coinpay') {
            $dj_username = C('coin')[$coinname]['dj_yh'];
            $dj_password = C('coin')[$coinname]['dj_mm'];
            $dj_address = C('coin')[$coinname]['dj_zj'];
            $dj_port = C('coin')[$coinname]['dj_dk'];
            $cps_api = CoinPay($dj_username, $dj_password, $dj_address, $dj_port, 5, array(), 1);

            $json = $cps_api->GetAllCoinBalances();
			$coinpay_coin=strtoupper($coinname);
			

            if (!isset($json['result']) || $json['result'][$coinpay_coin]['coin_status']!='online') {
            	$this->error($coinname.'Wallet link failure!');
            }

            $data['trance_mum'] =$json['result'][$coinpay_coin]['balancef'];
        }	else {
            $data['trance_mum'] = 0;
        }
		
        $this->assign('data', $data);
	    $market_json = M('CoinJson')->where(array('name' => $coinname))->order('id desc')->find();

        if ($market_json) {
            //$addtime = $market_json['addtime'] + 60;
            $addtime = $market_json['addtime'];
            if (time() > $addtime) {
                $addtime = $market_json['addtime'] + 60;
            }
        } else {
            $addtime = M('Myzr')->where(array('name' => $coinname))->order('id asc')->find()['addtime'];
        }

        if (!$addtime) {
            $addtime = time();
        }
        $t = $addtime;
        $start = mktime(0, 0, 0, date('m', $t), date('d', $t), date('Y', $t));
        $end = mktime(23, 59, 59, date('m', $t), date('d', $t), date('Y', $t));
        if ($addtime) {
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

            if (C($coinname)['type'] == 'qbb') {
                $bb = $json['balance'];
            } else {
                $bb = 0;
            }

            $trade_fee_buy = M('Myzr')->where(array(
                'name' => $coinname,
                'addtime' => array(
                    array('egt', $start),
                    array('elt', $end)
                )
            ))->sum('fee');
            $trade_fee_sell = M('Myzc')->where(array(
                'name' => $coinname,
                'addtime' => array(
                    array('egt', $start),
                    array('elt', $end)
                )
            ))->sum('fee');
            $d = array($aa, $bb, $trade_fee_buy, $trade_fee_sell);

            // in caseturn upadd timeWaitinendtime
            if (M('CoinJson')->where(array('name' => $coinname, 'addtime' => $end))->find()) {
                M('CoinJson')->where(
                    array('name' => $coinname, 'addtime' => $end))->save(

                    array('data' => json_encode($d))
                );
            } else {
                M('CoinJson')->add(array(
                        'name' => $coinname,
                        'data' => json_encode($d),
                        'addtime' => $end)
                );
            }
        }

        $tradeJson = M('CoinJson')->where(array('name' => $coinname))->order('id asc')->limit(100)->select();

        foreach ($tradeJson as $k => $v) {
            if ((addtime($v['addtime']) != '---') && (14634049 < $v['addtime'])) {
                $date = addtime($v['addtime'], 'Y-m-d H:i:s');
                $json_data = json_decode($v['data'], true);
                $cztx[] = array('date' => $date, 'num' => $json_data[0], 'mum' => $json_data[1], 'fee_buy' => $json_data[2], 'fee_sell' => $json_data[3]);
            }
        }

        $this->assign('cztx', json_encode($cztx));
        $this->display();
    }

    public function coinSet($coinname = NULL)
    {
        if (!$coinname) {
            $this->error(L('INCORRECT_REQ'));
        }

        if (M('CoinJson')->where(array('name' => $coinname))->delete()) {
            $this->success(L('SUCCESSFULLY_DONE'));
        } else {
            $this->error(L('OPERATION_FAILED'));
        }
    }

    public function market($market = NULL)
    {
        if (!$market) {
            $market = C('market_mr');
        }

        if (!$market) {
            echo 'Please Select correct markety';
            exit();
        }

        $market = trim($market);
        $xnb = explode('_', $market)[0];
        $rmb = explode('_', $market)[1];
        $this->assign('xnb', $xnb);
        $this->assign('rmb', $rmb);
        $this->assign('market', $market);
        $data = array();
        $data['trance_num'] = M('TradeLog')->where(array('market' => $market))->sum('num');
        $data['trance_buyfee'] = M('TradeLog')->where(array('market' => $market))->sum('fee_buy');
        $data['trance_sellfee'] = M('TradeLog')->where(array('market' => $market))->sum('fee_sell');
        $data['trance_fee'] = $data['trance_buyfee'] + $data['trance_sellfee'];
        $data['trance_mum'] = M('TradeLog')->where(array('market' => $market))->sum('mum');
        $data['trance_ci'] = M('TradeLog')->where(array('market' => $market))->count();
        $market_json = M('MarketJson')->where(array('name' => $market))->order('id desc')->find();

        if ($market_json) {
            $addtime = $market_json['addtime'] + 60;
        } else {
            $addtime = M('TradeLog')->where(array('market' => $market))->order('addtime asc')->find()['addtime'];
        }

        if (!$addtime) {
            $addtime = time();
        }

        if ($addtime) {
            if ($addtime < (time() + (60 * 60 * 24))) {
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

                    if (M('MarketJson')->where(array('name' => $market, 'addtime' => $end))->find()) {
                        M('MarketJson')->where(array('name' => $market, 'addtime' => $end))->save(array('data' => json_encode($d)));
                    } else {
                        M('MarketJson')->add(array('name' => $market, 'data' => json_encode($d), 'addtime' => $end));
                    }
                } else {
                    $d = null;

                    if (M('MarketJson')->where(array('name' => $market, 'data' => ''))->find()) {
                        M('MarketJson')->where(array('name' => $market, 'data' => ''))->save(array('addtime' => $end));
                    } else {
                        M('MarketJson')->add(array('name' => $market, 'data' => '', 'addtime' => $end));
                    }
                }
            }
        }

        $tradeJson = M('MarketJson')->where(array('name' => $market))->order('id asc')->limit(100)->select();

        foreach ($tradeJson as $k => $v) {
            if ((addtime($v['addtime']) != '---') && (14634049 < $v['addtime'])) {
                $date = addtime($v['addtime'] - (60 * 60 * 24), 'Y-m-d H:i:s');
                $json_data = json_decode($v['data'], true);
                $cztx[] = array('date' => $date, 'num' => $json_data[0], 'mum' => $json_data[1], 'fee_buy' => $json_data[2], 'fee_sell' => $json_data[3]);
            }
        }

        $this->assign('cztx', json_encode($cztx));
        $this->assign('data', $data);
        $this->display();
    }

    public function marketSet($market = NULL)
    {
        if (!$market) {
            $this->error(L('INCORRECT_REQ'));
        }

        if (M('MarketJson')->where(array('name' => $market))->delete()) {
            $this->success(L('SUCCESSFULLY_DONE'));
        } else {
            $this->error(L('OPERATION_FAILED'));
        }
    }

    public function checkUpdata()
    {
        if (!S(MODULE_NAME . CONTROLLER_NAME . 'checkUpdata')) {
            $list = M('Menu')->where(array(
                'url' => 'Index/index',
                'pid' => array('neq', 0)
            ))->select();

            if ($list[1]) {
                M('Menu')->where(array('id' => $list[1]['id']))->delete();
            } else if (!$list) {
                M('Menu')->add(array('url' => 'Index/index', 'title' => 'System Overview', 'pid' => 1, 'sort' => 1, 'hide' => 0, 'group' => 'system', 'ico_name' => 'home'));
            } else {
                M('Menu')->where(array(
                    'url' => 'Index/index',
                    'pid' => array('neq', 0)
                ))->save(array('title' => 'System Overview', 'pid' => 1, 'sort' => 1, 'hide' => 0, 'group' => 'system', 'ico_name' => 'home'));
            }

            $list = M('Menu')->where(array('url' => 'Index/coin'))->select();

            if ($list[1]) {
                M('Menu')->where(array('id' => $list[1]['id']))->delete();
            } else if (!$list) {
                M('Menu')->add(array('url' => 'Index/coin', 'title' => 'Currency statistics', 'pid' => 1, 'sort' => 2, 'hide' => 0, 'group' => 'system', 'ico_name' => 'home'));
            } else {
                M('Menu')->where(array('url' => 'Index/coin'))->save(array('title' => 'Currency statistics', 'pid' => 1, 'sort' => 2, 'hide' => 0, 'group' => 'system', 'ico_name' => 'home'));
            }

            $list = M('Menu')->where(array('url' => 'Index/market'))->select();

            if ($list[1]) {
                M('Menu')->where(array('id' => $list[1]['id']))->delete();
            } else if (!$list) {
                M('Menu')->add(array('url' => 'Index/market', 'title' => 'Market Statistics', 'pid' => 1, 'sort' => 3, 'hide' => 0, 'group' => 'system', 'ico_name' => 'home'));
            } else {
                M('Menu')->where(array('url' => 'Index/market'))->save(array('title' => 'Market Statistics', 'pid' => 1, 'sort' => 3, 'hide' => 0, 'group' => 'system', 'ico_name' => 'home'));
            }

            if (M('Menu')->where(array('url' => 'Index/info'))->delete()) {
                M('AuthRule')->where(array('status' => 1))->delete();
            }

            M('Menu')->where(array('id' => 1))->save(array('title' => 'system', 'url' => 'Index/index', 'pid' => 0, 'sort' => 1, 'hide' => 0, 'group' => '', 'ico_name' => 'home'));
            M('Menu')->where(array('id' => 2))->save(array('title' => 'content', 'url' => 'Article/index', 'pid' => 0, 'sort' => 1, 'hide' => 0, 'group' => '', 'ico_name' => 'list-alt'));
            M('Menu')->where(array('id' => 3))->save(array('title' => 'user', 'url' => 'User/index', 'pid' => 0, 'sort' => 1, 'hide' => 0, 'group' => '', 'ico_name' => 'user'));
            M('Menu')->where(array('id' => 4))->save(array('title' => 'Financial affairs', 'url' => 'Finance/index', 'pid' => 0, 'sort' => 1, 'hide' => 0, 'group' => '', 'ico_name' => 'th-list'));
            M('Menu')->where(array('id' => 5))->save(array('title' => 'transaction', 'url' => 'Trade/index', 'pid' => 0, 'sort' => 1, 'hide' => 0, 'group' => '', 'ico_name' => 'stats'));
            M('Menu')->where(array('id' => 6))->save(array('title' => 'application', 'url' => 'Game/index', 'pid' => 0, 'sort' => 1, 'hide' => 0, 'group' => '', 'ico_name' => 'globe'));
            M('Menu')->where(array('id' => 7))->save(array('title' => 'Set up', 'url' => 'Config/index', 'pid' => 0, 'sort' => 1, 'hide' => 0, 'group' => '', 'ico_name' => 'cog'));
            M('Menu')->where(array('id' => 8))->save(array('title' => 'Operations', 'url' => 'Operate/index', 'pid' => 0, 'sort' => 1, 'hide' => 0, 'group' => '', 'ico_name' => 'share'));
            M('Menu')->where(array('id' => 9))->save(array('title' => 'tool', 'url' => 'Tools/index', 'pid' => 0, 'sort' => 1, 'hide' => 0, 'group' => '', 'ico_name' => 'wrench'));
            M('Menu')->where(array('id' => 10))->save(array('title' => 'Spread', 'url' => 'Cloud/index', 'pid' => 0, 'sort' => 1, 'hide' => 0, 'group' => '', 'ico_name' => 'tasks'));
            S(MODULE_NAME . CONTROLLER_NAME . 'checkUpdata', 1);
        }
    }
}

?>