<?php

namespace Home\Controller;

class TradeController extends HomeController
{
    public function index($market = NULL)
    {
        $market = strtolower($market);

        $showPW = 1;
        check_server();
        if (!$market) {
            $market = C("market_mr");
        }

        if (!in_array(strtolower($market), array_keys(C('Market')))) {
            //		$this->redirect('Home/Index');
        }
        if (userid()) {
            $user = M('User')->where(array('id' => userid()))->find();

            if ($user['tpwdsetting'] == 3) {
                $showPW = 0;
            }

            if ($user['tpwdsetting'] == 1) {
                if (session(userid() . 'tpwdsetting')) {
                    $showPW = 2;
                }
            }

        }


        check_server();

        if (!$market) {
            $market = C('market_mr');
        }


        $market_time_codono = C('market')[$market]['begintrade'] . "-" . C('market')[$market]['endtrade'];
        $codono_getCoreConfig = codono_getCoreConfig();
        $user_coin = M('UserCoin')->where(array('userid' => userid()))->find();
        $xnb = explode('_', $market)[0];
        $rmb = explode('_', $market)[1];

        $buy_fees=discount(C('market')[$market]['fee_buy'],userid());
        $sell_fees=discount(C('market')[$market]['fee_sell'],userid());
        $this->assign('buy_fees', $buy_fees);
        $this->assign('sell_fees', $sell_fees);
        $this->assign('activated_base_markets', $codono_getCoreConfig['codono_indexcat']);
        $this->assign('market_time', $market_time_codono);
        $this->assign('showPW', $showPW);
        $this->assign('market', $market);
        $this->assign('xnb', $xnb);
        $this->assign('rmb', $rmb);
        $this->assign('user_coin', $user_coin[$xnb]);
        $this->assign('xnbimg', C('market')[$market]['xnbimg']);
        $this->assign('base_coin', $user_coin[$rmb]);
        $this->assign('page_title', "Trade " . $xnb . " & " . $rmb . " | " . $market);
        $this->display();
    }

    public function chart($market = NULL)
    {
        if (!$market) {
            $market = C('market_mr');
        }

        $xnb = explode('_', $market)[0];
        $rmb = explode('_', $market)[1];
        $this->assign('market', $market);
        $this->assign('xnb', $xnb);
        $this->assign('rmb', $rmb);
        $this->assign('page_title', "Market Graph of " . $xnb . " vs " . $rmb . " | " . $market);
        $this->display();
    }

    public function info($market = NULL)
    {

        check_server();

        if (!$market) {
            $market = C('market_mr');
        }
        // TODO: SEPARATE
        // TODO: SEPARATE

        $this->assign('market', $market);
        $this->assign('xnb', explode('_', $market)[0]);
        $this->assign('rmb', explode('_', $market)[1]);
        $this->display();
    }

    public function comment($market = NULL)
    {


        if (!check($market, 'market')) {
            $market = C('market_mr');
        }

        if (!$market) {
            $market = C('market_mr');
        }

        if ($market != C('market')[$market]['name']) {
            $market = C('market_mr');
        }


        $this->assign('xnb', explode('_', $market)[0]);
        $this->assign('rmb', explode('_', $market)[1]);
        $where['coinname'] = explode('_', $market)[0];
        $Model = M('CoinComment');

        $count = $Model->where($where)->count();
        $Page = new \Think\Page($count, 15);
        $show = $Page->show();
        $list = $Model->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

        foreach ($list as $k => $v) {
            $list[$k]['username'] = M('User')->where(array('id' => $v['userid']))->getField('username');
        }
        $this->assign('market', $market);
        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }

    public function ordinary($market = NULL)
    {
        if (!$market) {
            $market = C('market_mr');
        }
        $this->assign('round', C('MARKET')[$market]['round']);
        $this->assign('market', $market);
        $this->display();
    }

    public function highstock($market = NULL)
    {
        if (!$market) {
            $market = C('market_mr');
        }
        $this->assign('round', C('MARKET')[$market]['round']);
        $this->assign('market', $market);
        $this->display();
    }

    public function specialty($market = NULL)
    {
        if (!$market) {
            $market = C('market_mr');
        }

        $this->assign('market', $market);
        $this->display();
    }

    public function tradingview($market = NULL)
    {
        if (!$market) {
            $market = C('market_mr');
        }
        $this->assign('market', $market);
        $this->display();
    }

    public function depth($market = NULL)
    {
        if (!$market) {
            $market = C('market_mr');
        }

        $this->assign('market', $market);
        $this->display();
    }

    public function upTrade($paypassword = NULL, $market = NULL, $price, $num, $type, $tradeType = 'limit')
    {
        if (!kyced()) {
            $this->error(L('Complete KYC First!'));
        }

        if ($tradeType == 'market') {

            //This one does not have a price
            return $this->MarketTrade($paypassword, $market, $num, $type);
        }
        if ($tradeType == 'limit') {

            return $this->upTradeLimit($paypassword, $market, $price, $num, $type);
        }

        return false;
    }

    private function MarketTrade($paypassword = NULL, $market = NULL, $num, $type)
    {

        if (!userid()) {
            $this->error(L('PLEASE_LOGIN'));
        }

        if (C('market')[$market]['begintrade']) {
            $begintrade = C('market')[$market]['begintrade'];
        } else {
            $begintrade = "00:00:00";
        }

        if (C('market')[$market]['endtrade']) {
            $endtrade = C('market')[$market]['endtrade'];
        } else {
            $endtrade = "23:59:59";
        }


        $trade_begin_time = strtotime(date("Y-m-d") . " " . $begintrade);
        $trade_end_time = strtotime(date("Y-m-d") . " " . $endtrade);
        $cur_time = time();

        if ($cur_time < $trade_begin_time || $cur_time > $trade_end_time) {
            $this->error('The current market is prohibited transaction,Trading hours daily' . $begintrade . '-' . $endtrade);
        }
        $generate_price_and_qty_for_market = $this->getPriceNumForMarket($market, $type);
        if ($generate_price_and_qty_for_market['num'] < $num) {
            $num = $generate_price_and_qty_for_market['num'];
        }
        $price = $generate_price_and_qty_for_market['price'];


        if (!check($price, 'double')) {
            $this->error(L('The transaction price is malformed'));
        }

        if (!check($num, 'double')) {
            $this->error(L('The number of transactions is malformed'));
        }

        if (($type != 1) && ($type != 2)) {
            $this->error(L('Transaction type format error'));
        }

        $user = M('User')->where(array('id' => userid()))->find();
        if (IF_TRADING_PASS == 1) {
            if ($user['tpwdsetting'] == 3) {
            }

            if ($user['tpwdsetting'] == 2) {
                if (md5($paypassword) != $user['paypassword']) {
                    $this->error(L('Trading password is wrong!'));
                }
            }

            if ($user['tpwdsetting'] == 1) {
                if (!session(userid() . 'tpwdsetting')) {
                    if (md5($paypassword) != $user['paypassword']) {
                        $this->error(L('Trading password is wrong!'));
                    } else {
                        session(userid() . 'tpwdsetting', 1);
                    }
                }
            }
        }

        if (!C('market')[$market]) {
            $this->error(L('Error market'));
        } else {
            $xnb = explode('_', $market)[0];
            $rmb = explode('_', $market)[1];
        }

        // TODO: SEPARATE

        $price = round(floatval($price), 8);

        if (!$price) {
            $this->error(L('The transaction price error') . $price);
        }

        //$num = round($num, 8);

        if (!check($num, 'double')) {
            $this->error(L('INCORRECT_QTY'));
        }

        if ($type == 1) {
            $min_price = (C('market')[$market]['buy_min'] ? C('market')[$market]['buy_min'] : 1.0E-8);
            $max_price = (C('market')[$market]['buy_max'] ? C('market')[$market]['buy_max'] : 10000000);
        } else if ($type == 2) {
            $min_price = (C('market')[$market]['sell_min'] ? C('market')[$market]['sell_min'] : 1.0E-8);
            $max_price = (C('market')[$market]['sell_max'] ? C('market')[$market]['sell_max'] : 10000000);
        } else {
            $this->error(L('Transaction type error'));
        }


        $user_coin = M('UserCoin')->where(array('userid' => userid()))->find();

        if ($type == 1) {
            $trade_fee = disount(C('market')[$market]['fee_buy'],userid());

            if ($trade_fee) {
                $fee = round((($num * $price) / 100) * $trade_fee, 8);
                $mum = round((($num * $price) / 100) * (100 + $trade_fee), 8);
            } else {
                $fee = 0;
                $mum = round($num * $price, 8);
            }

            if ($user_coin[$rmb] < $mum) {
                $this->error(L('INSUFFICIENT') . C('coin')[$rmb]['title']);
            }
        } else if ($type == 2) {
            $trade_fee = disount(C('market')[$market]['fee_sell'],userid());

            if ($trade_fee) {
                $fee = round((($num * $price) / 100) * $trade_fee, 8);
                $mum = round((($num * $price) / 100) * (100 - $trade_fee), 8);
            } else {
                $fee = 0;
                $mum = round($num * $price, 8);
            }

            if ($user_coin[$xnb] < $num) {
                $this->error(L('INSUFFICIENT') . C('coin')[$xnb]['title']);
            }
        } else {
            $this->error(L('Transaction type error'));
        }

        if (C('coin')[$xnb]['fee_bili']) {
            if ($type == 2) {
                // TODO: SEPARATE
                $bili_user = round($user_coin[$xnb] + $user_coin[$xnb . 'd'], C('market')[$market]['round']);

                if ($bili_user) {
                    // TODO: SEPARATE
                    $bili_keyi = round(($bili_user / 100) * C('coin')[$xnb]['fee_bili'], C('market')[$market]['round']);

                    if ($bili_keyi) {
                        //$zheng_query="select id,price,sum(num-deal)as nums from codono_trade where userid=" . userid() . " and status=0 and type=2 and market like %" . $xnb . "%";

                        $zheng_query = "SELECT * FROM `codono_trade` WHERE `userid` = '" . userid() . "' and status=0 and type=2 and market like '%" . $xnb . "%'";

                        $bili_zheng = M()->query($zheng_query);

                        if (!$bili_zheng[0]['nums']) {
                            $bili_zheng[0]['nums'] = 0;
                        }

                        $bili_kegua = $bili_keyi - $bili_zheng[0]['nums'];

                        if ($bili_kegua < 0) {
                            $bili_kegua = 0;
                        }

                        if ($bili_kegua < $num) {
                            $this->error(L('Your total number of pending orders exceeds the system limit, you currently hold ') . C('coin')[$xnb]['title'] . $bili_user . L(' , has been pending') . $bili_zheng[0]['nums'] . L(' One can also pending') . $bili_kegua . 'More', '', 5);
                            // $this->error('query='.$zheng_query.'   Keyi='.$bili_keyi. 'and zheng='.json_encode($bili_zheng). 'and kegua='.$bili_kegua.' and < num='.$num);
                        }
                    } else {
                        $this->error(L('Trading volume can be wrong'));
                    }
                }
            }
        }

        if (C('coin')[$xnb]['fee_meitian']) {
            if ($type == 2) {
                $bili_user = round($user_coin[$xnb] + $user_coin[$xnb . 'd'], 8);

                if ($bili_user < 0) {
                    $this->error(L('Trading volume can be wrong'));
                }

                $kemai_bili = ($bili_user / 100) * C('coin')[$xnb]['fee_meitian'];

                if ($kemai_bili < 0) {
                    $this->error(L('You can then sell today') . C('coin')[$xnb]['title'] . 0 . 'More', '', 5);
                }

                $kaishi_time = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
                $jintian_sell = M('Trade')->where(array(
                    'userid' => userid(),
                    'addtime' => array('egt', $kaishi_time),
                    'type' => 2,
                    'status' => array('neq', 2),
                    'market' => array('like', '%' . $xnb . '%')
                ))->sum('num');

                if ($jintian_sell) {
                    $kemai = $kemai_bili - $jintian_sell;
                } else {
                    $kemai = $kemai_bili;
                }

                if ($kemai < $num) {
                    if ($kemai < 0) {
                        $kemai = 0;
                    }

                    $this->error(L('Your pending the total number exceeds the system limit, you can then sell today') . C('coin')[$xnb]['title'] . $kemai . 'More', '', 5);
                }
            }
        }

        if (C('market')[$market]['trade_min']) {
            if ($mum < C('market')[$market]['trade_min']) {
                $this->error(L('THE_MINIMUM_AMOUNT_FOR_EACH_PU') . ' ' . C('coin')[$rmb]['title'] . C('market')[$market]['trade_min']);
            }
        }

        if (C('market')[$market]['trade_max']) {
            if (C('market')[$market]['trade_max'] < $mum) {
                $this->error(L('MAX_ORDER_QTY') . C('market')[$market]['trade_max']);
            }
        }

        if (!$rmb) {
            $this->error('data error1');
        }

        if (!$xnb) {
            $this->error('data error2');
        }

        if (!$market) {
            $this->error('data error3');
        }

        if (!$price) {
            $this->error('data error4');
        }

        if (!$num) {
            $this->error('data error5');
        }

        if (!$mum) {
            $this->error('data error6');
        }

        if (!$type) {
            $this->error('data error7');
        }

        $mo = M();
        $mo->execute('set autocommit=0');
        $mo->execute('lock tables codono_trade write ,codono_user_coin write ,codono_finance write');
        $rs = array();
        $user_coin = $mo->table('codono_user_coin')->where(array('userid' => userid()))->find();

        if ($type == 1) {
            if ($user_coin[$rmb] < $mum) {
                $this->error(L('INSUFFICIENT') . C('coin')[$rmb]['title']);
            }

            $finance = $mo->table('codono_finance')->where(array('userid' => userid()))->order('id desc')->find();
            $finance_num_user_coin = $mo->table('codono_user_coin')->where(array('userid' => userid()))->find();
            $rs[] = $mo->table('codono_user_coin')->where(array('userid' => userid()))->setDec($rmb, $mum);
            $rs[] = $mo->table('codono_user_coin')->where(array('userid' => userid()))->setInc($rmb . 'd', $mum);
            $rs[] = $finance_nameid = $mo->table('codono_trade')->add(array('userid' => userid(), 'market' => $market, 'price' => $price, 'num' => $num, 'mum' => $mum, 'fee' => $fee, 'type' => 1, 'addtime' => time(), 'status' => 0));


            //20170531 modify only statistics USD transaction Money TYPE
            if ($rmb == "usd") {
                $finance_mum_user_coin = $mo->table('codono_user_coin')->where(array('userid' => userid()))->find();
                $finance_hash = md5(userid() . $finance_num_user_coin['usd'] . $finance_num_user_coin['usdd'] . $mum . $finance_mum_user_coin['usd'] . $finance_mum_user_coin['usdd'] . CODONOLIC . 'auth.codono.com');
                $finance_num = $finance_num_user_coin['usd'] + $finance_num_user_coin['usdd'];

                if ($finance['mum'] < $finance_num) {
                    $finance_status = (1 < ($finance_num - $finance['mum']) ? 0 : 1);
                } else {
                    $finance_status = (1 < ($finance['mum'] - $finance_num) ? 0 : 1);
                }

                $rs[] = $mo->table('codono_finance')->add(array('userid' => userid(), 'coinname' => 'usd', 'num_a' => $finance_num_user_coin['usd'], 'num_b' => $finance_num_user_coin['usdd'], 'num' => $finance_num_user_coin['usd'] + $finance_num_user_coin['usdd'], 'fee' => $mum, 'type' => 2, 'name' => 'trade', 'nameid' => $finance_nameid, 'remark' => 'Trading Center-Buying commission-market' . $market, 'mum_a' => $finance_mum_user_coin['usd'], 'mum_b' => $finance_mum_user_coin['usdd'], 'mum' => $finance_mum_user_coin['usd'] + $finance_mum_user_coin['usdd'], 'move' => $finance_hash, 'addtime' => time(), 'status' => $finance_status));
            }


        } else if ($type == 2) {
            if ($user_coin[$xnb] < $num) {
                $this->error(C('coin')[$xnb]['title'] . 'Insufficient balance2!');
            }

            $rs[] = $mo->table('codono_user_coin')->where(array('userid' => userid()))->setDec($xnb, $num);
            $rs[] = $mo->table('codono_user_coin')->where(array('userid' => userid()))->setInc($xnb . 'd', $num);
            $rs[] = $mo->table('codono_trade')->add(array('userid' => userid(), 'market' => $market, 'price' => $price, 'num' => $num, 'mum' => $mum, 'fee' => $fee, 'type' => 2, 'addtime' => time(), 'status' => 0));
        } else {
            $mo->execute('rollback');
            $mo->execute('unlock tables');
            $this->error(L('Transaction type error'));
        }
        if (check_arr($rs)) {
            $mo->execute('commit');
            $mo->execute('unlock tables');
            S('getDepth', null);
            S('getActiveDepth' . $market, null);
            S('getActiveDepth', null);
            S('getDepthNew', null);

            exec($this->matchingTrade($market));          //Matches the orders
            exec($this->stopcheck($market, $type)); //StopLoss execution
            //exec($this->callLiquidityServer($market,$type,$price,$num)); //liquidity Server
            exec($this->callSetTradeLiq($market, $type, $price, $num));
            $this->success(L('Trading success!'));
        } else {
            $mo->execute('rollback');
            $mo->execute('unlock tables');
            $this->error(L('transaction failed!'));

        }
    }

    private function getPriceNumForMarket($market, $type)
    {
        if ($type == 2) {
            $query = "SELECT sum(num) as num,min(price) as price FROM `codono_trade` WHERE market='" . $market . "' and type='1' and status=0";
        }
        if ($type == 1) {
            $query = "SELECT sum(num) as num,max(price) as price FROM `codono_trade` WHERE market='" . $market . "' and type='2' and status=0";
        }
        $response = M()->query($query);
        return $response[0];
    }

    public function matchingTrade($market = NULL)
    {
        $Match_market = strtoupper($market);

        if (array_key_exists($Match_market, LIQUIDITY_ARRAY)) {
            $liq_market = LIQUIDITY_ARRAY["$Match_market"]; //binance market pair needs to be defined in other_config
            return $this->domatchingTradeLiquid($market, $liq_market); //Liquidity Matching Engine only
        } else {

            return $this->domatchingTrade($market); //This market does not run on liquidtity
        }

    }

    private function domatchingTradeLiquid($market = NULL, $liq_market = NULL)
    {
        if (!$market) {
            return false;
        } else {
            $xnb = explode('_', $market)[0];
            $rmb = explode('_', $market)[1];
        }

        $fee_buy = C('market')[$market]['fee_buy'];
        $fee_sell = C('market')[$market]['fee_sell'];
        $invit_buy = C('market')[$market]['invit_buy'];
        $invit_sell = C('market')[$market]['invit_sell'];
        $invit_1 = C('market')[$market]['invit_1'];
        $invit_2 = C('market')[$market]['invit_2'];
        $invit_3 = C('market')[$market]['invit_3'];
        $mo = M();
        $new_trade_codono = 0;

        for (; true;) {
            $buy = $mo->table('codono_trade')->where(array('market' => $market, 'type' => 1, 'status' => 0))->order('price desc,id asc')->find();
            $sell = $mo->table('codono_trade')->where(array('market' => $market, 'type' => 2, 'status' => 0))->order('price asc,id asc')->find();

            if ($sell['id'] < $buy['id']) {
                $type = 1;
            } else {
                $type = 2;
            }

            if ($buy && $sell && (0 <= floatval($buy['price']) - floatval($sell['price']))) {
                $rs = array();

                if ($buy['num'] <= $buy['deal']) {
                }

                if ($sell['num'] <= $sell['deal']) {
                }

                $amount = min(format_num($buy['num'] - $buy['deal'], 8), format_num($sell['num'] - $sell['deal'], 8));
                $amount = format_num($amount, 8);

                if ($amount <= 0) {
                    $log = 'error 1 market:' . $market . 'Error: Buy OrderID:' . $buy['id'] . L(' Sell orderID: ') . $sell['id'] . L('TRANSACTION_TYPE') . $type . "\n";
                    $log .= 'ERR: Deal Quantity Error,Quantity :' . $amount;
                    M('Trade')->where(array('id' => $buy['id']))->setField('status', 1);
                    M('Trade')->where(array('id' => $sell['id']))->setField('status', 1);
                    break;
                }

                if ($type == 1) {
                    $price = $sell['price'];
                } else if ($type == 2) {
                    $price = $buy['price'];
                } else {
                    break;
                }

                if (!$price) {
                    $log = 'error2 market:' . $market . 'Error: Buy OrderID:' . $buy['id'] . L(' Sell orderID: ') . $sell['id'] . L('TRANSACTION_TYPE') . $type . L('TRANSACTION_AMOUNT') . $amount . "\n";
                    $log .= 'ERR: Deal price Error,price :' . $price;
                    break;
                } else {
                    // TODO: SEPARATE
                    $price = format_num($price, 8);
                }

                $mum = format_num($price * $amount, 8);

                if (!$mum) {
                    $log = 'error3 market:' . $market . 'Error: Buy OrderID:' . $buy['id'] . L(' Sell orderID: ') . $sell['id'] . L('TRANSACTION_TYPE') . $type . L('TRANSACTION_AMOUNT') . $amount . "\n";
                    $log .= 'ERR: The total turnover of the error, the total is' . $mum;
                    mlog($log);
                    break;
                } else {
                    $mum = round($mum, 8);
                }
                if ($fee_buy) {
                    $buy_fee = format_num(($mum / 100) * $fee_buy, 8);
                    $buy_save = format_num(($mum / 100) * (100 + $fee_buy), 8);
                } else {
                    $buy_fee = 0;
                    $buy_save = $mum;
                }


                if (!$buy_save) {
                    $log = 'error4 market:' . $market . 'Error: Buy OrderID:' . $buy['id'] . L(' Sell orderID: ') . $sell['id'] . L('TRANSACTION_TYPE') . $type . L('TRANSACTION_AMOUNT') . $amount . L('deal price') . $price . L('Total turnover') . $mum . "\n";
                    $log .= 'ERR: Buyers Update Quantity Error,Update Quantity :' . $buy_save;
                    mlog($log);
                    break;
                }
                if ($fee_sell) {
                    $sell_fee = format_num(($mum / 100) * $fee_sell, 8);
                    $sell_save = format_num(($mum / 100) * (100 - $fee_sell), 8);
                } else {
                    $sell_fee = 0;
                    $sell_save = $mum;
                }

                if (!$sell_save) {
                    $log = 'error5 market:' . $market . 'Error: Buy OrderID:' . $buy['id'] . L(' Sell orderID: ') . $sell['id'] . L('TRANSACTION_TYPE') . $type . L('TRANSACTION_AMOUNT') . $amount . L('deal price') . $price . L('Total turnover') . $mum . "\n";
                    $log .= 'ERR: Sellers Update Quantity Error,Update Quantity :' . $sell_save;
                    mlog($log);
                    break;
                }

                $user_buy = M('UserCoin')->where(array('userid' => $buy['userid']))->find();
                //If userid is 0 it means liquidty is coming from LiqEngine
                if (!$user_buy[$rmb . 'd'] && $buy['userid'] != 0) {
                    $log = 'error6 market:' . $market . 'Error: Buy OrderID:' . $buy['id'] . L(' Sell orderID: ') . $sell['id'] . '  ' . L('TRANSACTION_TYPE') . $type . L('TRANSACTION_AMOUNT') . $amount . L('deal price') . $price . L('Total turnover') . $mum . "\n";
                    $log .= 'ERR: Property buyers error,freeze Property is ' . $rmb . 'd' . $user_buy[$rmb . 'd'];
                    mlog($log);
                    mlog(json_encode($user_buy));
                    break;
                }

                $user_sell = M('UserCoin')->where(array('userid' => $sell['userid']))->find();
                //If userid is 0 it means liquidty is coming from LiqEngine
                if (!$user_sell[$xnb . 'd'] && $sell['userid'] != 0) {
                    $log = 'error7 market:' . $market . 'Error: Buy OrderID:' . $buy['id'] . L(' Sell orderID: ') . $sell['id'] . L('TRANSACTION_TYPE') . $type . L('TRANSACTION_AMOUNT') . $amount . L('deal price') . $price . L('Total turnover') . $mum . "\n";
                    $log .= 'ERR: Sellers of property error,freeze Property is' . $user_sell[$xnb . 'd'];
                    mlog($log);
                    mlog($sell['userid']);
                    mlog(json_encode($user_sell));
                    mlog($xnb . 'd');
                    break;
                }
                // This line indicates some issue with Freezing funds of user , freezing funds are less then 0.000000001 , Means no money in order
                //If userid is 0 it means liquidty is coming from LiqEngine
                if ($user_buy[$rmb . 'd'] < 1.0E-8 && $user_buy['userid'] != 0) {
                    $log = '/n/n error88 market:' . $market . 'Error: Buy OrderID:' . $buy['id'] . L(' Sell orderID: ') . $sell['id'] . L('TRANSACTION_TYPE') . $type . L('TRANSACTION_AMOUNT') . $amount . L('deal price') . $price . L('Total turnover') . $mum . "\n";
                    $log .= 'ERR: Buyers Update Freeze currency appear error,Should be updated' . $buy_save . L('Account Balance') . $user_buy[$rmb . 'd'] . L('Error handling');
                    mlog($log);
                    M('Trade')->where(array('id' => $buy['id']))->setField('status', 1);
                    break;

                }

                if ($buy_save <= format_num($user_buy[$rmb . 'd'], 8) && $user_buy['userid'] != 0) {
                    $save_buy_rmb = $buy_save;
                } else if ($buy_save <= format_num($user_buy[$rmb . 'd'], 8) + 1 && $user_buy['userid'] != 0) {
                    $save_buy_rmb = $user_buy[$rmb . 'd'];
                    $log = 'error8 market:' . $market . 'Error: Buy OrderID:' . $buy['id'] . L(' Sell orderID: ') . $sell['id'] . L('TRANSACTION_TYPE') . $type . L('TRANSACTION_AMOUNT') . $amount . L('deal price') . $price . L('Total turnover') . $mum . "\n";
                    $log .= 'ERR: Buyers Update Freeze currency Error occurs,Should be updated' . $buy_save . L('Account Balance') . $user_buy[$rmb . 'd'] . L('The actual update') . $save_buy_rmb;
                    mlog($log);
                } else if ($user_buy['userid'] != 0) {
                    $log = 'error9 market:' . $market . 'Error: Buy OrderID:' . $buy['id'] . L(' Sell orderID: ') . $sell['id'] . L('TRANSACTION_TYPE') . $type . L('TRANSACTION_AMOUNT') . $amount . L('deal price') . $price . L('Total turnover') . $mum . "\n";
                    $log .= 'ERR: Buyers Update Freeze USD appear error,Should be updated' . $buy_save . L('Account Balance') . $user_buy[$rmb . 'd'] . L('Error handling');
                    mlog($log);
                    M('Trade')->where(array('id' => $buy['id']))->setField('status', 1);
                    break;
                }
                // TODO: SEPARATE
                $save_sell_xnb = 0;
                if ($amount <= round($user_sell[$xnb . 'd'], 8) && $user_sell['userid'] != 0) {
                    $save_sell_xnb = $amount;
                } else {
                    // TODO: SEPARATE

                    if ($amount <= round($user_sell[$xnb . 'd'], 8) + 1 && $user_sell['userid'] != 0) {
                        $save_sell_xnb = $user_sell[$xnb . 'd'];
                        $log = 'error10 market:' . $market . 'Error: Buy OrderID:' . $buy['id'] . L(' Sell orderID: ') . $sell['id'] . ' ' . L('TYPE') . $type . L('TRANSACTION_AMOUNT') . $amount . L('deal price') . $price . L('Total turnover') . $mum . "\n";
                        $log .= 'ERR: The seller updated the frozen token with an error and should update ' . $amount . ' account balance ' . $user_sell[$xnb . 'd'] . ' Actual update ' . $save_sell_xnb;

                        mlog($log);
                    } else if ($user_sell['userid'] != 0 && $user_buy['userid'] != 0) {
                        $log = 'error11 market:' . $market . 'Error: Buy OrderID:' . $buy['id'] . L(' Sell orderID: ') . $sell['id'] . L('TRANSACTION_TYPE') . $type . L('TRANSACTION_AMOUNT') . $amount . L('deal price') . $price . L('Total turnover') . $mum . "\n";
                        $log .= 'ERR: Sellers Update freeze Virtual currency appear error,Should be updated' . $amount . L('Account Balance') . $user_sell[$xnb . 'd'] . L('Error handling');
                        mlog($log);
                        M('Trade')->where(array('id' => $sell['id']))->setField('status', 1);
                        break;
                    }
                }

                if (!isset($save_buy_rmb) && $user_sell['userid'] != 0 && $user_buy['userid'] != 0) {
                    $log = 'error12 market:' . $market . 'Error: Buy OrderID:' . $buy['id'] . L(' Sell orderID: ') . $sell['id'] . L('TRANSACTION_TYPE') . $type . L('TRANSACTION_AMOUNT') . $amount . L('deal price') . $price . L('Total turnover') . $mum . "\n";
                    $log .= 'ERR: Buyers Update Quantity Error,Update Quantity:' . $save_buy_rmb;
                    mlog($log);
                    M('Trade')->where(array('id' => $buy['id']))->setField('status', 1);
                    break;
                }

                if (!$save_sell_xnb && $user_sell['userid'] != 0 && $user_buy['userid'] != 0) {
                    $log = 'error13 market:' . $market . 'Error: Buy OrderID:' . $buy['id'] . L(' Sell orderID: ') . $sell['id'] . L('TRANSACTION_TYPE') . $type . L('TRANSACTION_AMOUNT') . $amount . L('deal price') . $price . L('Total turnover') . $mum . "\n";
                    $log .= 'ERR: Sellers Update Quantity Error,Update Quantity:' . $save_sell_xnb;
                    mlog($log);
                    M('Trade')->where(array('id' => $sell['id']))->setField('status', 1);
                    break;
                }

                $mo->execute('set autocommit=0');
                $mo->execute('lock tables codono_trade write ,codono_trade_log write ,codono_user write,codono_user_coin write,codono_invit write ,codono_finance write');
                $rs[] = $mo->table('codono_trade')->where(array('id' => $buy['id']))->setInc('deal', $amount);
                $rs[] = $mo->table('codono_trade')->where(array('id' => $sell['id']))->setInc('deal', $amount);
                $rs[] = $finance_nameid = $mo->table('codono_trade_log')->add(array('userid' => $buy['userid'], 'peerid' => $sell['userid'], 'market' => $market, 'price' => $price, 'num' => $amount, 'mum' => $mum, 'type' => $type, 'fee_buy' => $buy_fee, 'fee_sell' => $sell_fee, 'addtime' => time(), 'status' => 1));
                //TODO: Check here
                if ($buy['userid'] != 0)
                    $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $buy['userid']))->setInc($xnb, $amount);
                $finance = $mo->table('codono_finance')->where(array('userid' => $buy['userid']))->order('id desc')->find();


                $finance_num_user_coin = $mo->table('codono_user_coin')->where(array('userid' => $buy['userid']))->find();
                //TODO: Check here
                if ($buy['userid'] != 0)
                    $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $buy['userid']))->setDec($rmb . 'd', $save_buy_rmb);
                $finance_mum_user_coin = $mo->table('codono_user_coin')->where(array('userid' => $buy['userid']))->find();
                $finance_hash = md5($buy['userid'] . $finance_num_user_coin['usd'] . $finance_num_user_coin['usdd'] . $mum . $finance_mum_user_coin['usd'] . $finance_mum_user_coin['usdd'] . CODONOLIC . 'auth.codono.com');
                $finance_num = $finance_num_user_coin['usd'] + $finance_num_user_coin['usdd'];

                if ($finance['mum'] < $finance_num) {
                    $finance_status = (1 < ($finance_num - $finance['mum']) ? 0 : 1);
                } else {
                    $finance_status = (1 < ($finance['mum'] - $finance_num) ? 0 : 1);
                }


                if ($rmb == "usd") {
                    $rs[] = $mo->table('codono_finance')->add(array('userid' => $buy['userid'], 'coinname' => 'usd', 'num_a' => $finance_num_user_coin['usd'], 'num_b' => $finance_num_user_coin['usdd'], 'num' => $finance_num_user_coin['usd'] + $finance_num_user_coin['usdd'], 'fee' => $save_buy_rmb, 'type' => 2, 'name' => 'tradelog', 'nameid' => $finance_nameid, 'remark' => 'Trading Center-Buy success-market' . $market, 'mum_a' => $finance_mum_user_coin['usd'], 'mum_b' => $finance_mum_user_coin['usdd'], 'mum' => $finance_mum_user_coin['usd'] + $finance_mum_user_coin['usdd'], 'move' => $finance_hash, 'addtime' => time(), 'status' => $finance_status));
                }


                $finance = $mo->table('codono_finance')->where(array('userid' => $buy['userid']))->order('id desc')->find();
                $finance_num_user_coin = $mo->table('codono_user_coin')->where(array('userid' => $sell['userid']))->find();
                //TODO: Check here
                if ($sell['userid'] != 0)
                    $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $sell['userid']))->setInc($rmb, $sell_save);
                $finance_mum_user_coin = $mo->table('codono_user_coin')->where(array('userid' => $sell['userid']))->find();
                $finance_hash = md5($sell['userid'] . $finance_num_user_coin['usd'] . $finance_num_user_coin['usdd'] . $mum . $finance_mum_user_coin['usd'] . $finance_mum_user_coin['usdd'] . CODONOLIC . 'auth.codono.com');
                $finance_num = $finance_num_user_coin['usd'] + $finance_num_user_coin['usdd'];

                if ($finance['mum'] < $finance_num) {
                    $finance_status = (1 < ($finance_num - $finance['mum']) ? 0 : 1);
                } else {
                    $finance_status = (1 < ($finance['mum'] - $finance_num) ? 0 : 1);
                }


                if ($rmb == "usd") {
                    $rs[] = $mo->table('codono_finance')->add(array('userid' => $sell['userid'], 'coinname' => 'usd', 'num_a' => $finance_num_user_coin['usd'], 'num_b' => $finance_num_user_coin['usdd'], 'num' => $finance_num_user_coin['usd'] + $finance_num_user_coin['usdd'], 'fee' => $save_buy_rmb, 'type' => 1, 'name' => 'tradelog', 'nameid' => $finance_nameid, 'remark' => 'Trading Center-Success sell-market' . $market, 'mum_a' => $finance_mum_user_coin['usd'], 'mum_b' => $finance_mum_user_coin['usdd'], 'mum' => $finance_mum_user_coin['usd'] + $finance_mum_user_coin['usdd'], 'move' => $finance_hash, 'addtime' => time(), 'status' => $finance_status));
                }

                //TODO: Check here
                if ($sell['userid'] != 0)
                    $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $sell['userid']))->setDec($xnb . 'd', $save_sell_xnb);
                $buy_list = $mo->table('codono_trade')->where(array('id' => $buy['id'], 'status' => 0))->find();

                if ($buy_list) {
                    if ($buy_list['num'] <= $buy_list['deal']) {
                        $rs[] = $mo->table('codono_trade')->where(array('id' => $buy['id']))->setField('status', 1);
                    }
                }

                $sell_list = $mo->table('codono_trade')->where(array('id' => $sell['id'], 'status' => 0))->find();

                if ($sell_list) {
                    if ($sell_list['num'] <= $sell_list['deal']) {
                        $rs[] = $mo->table('codono_trade')->where(array('id' => $sell['id']))->setField('status', 1);
                    }
                }

                if ($price < $buy['price']) {
                    $chajia_dong = format_num((($amount * $buy['price']) / 100) * (100 + $fee_buy), 8);
                    $chajia_shiji = format_num((($amount * $price) / 100) * (100 + $fee_buy), 8);
                    $chajia = format_num($chajia_dong - $chajia_shiji, 8);

                    if ($chajia) {
                        $chajia_user_buy = $mo->table('codono_user_coin')->where(array('userid' => $buy['userid']))->find();
                        if ($sell['userid'] != 0 && $buy['userid'] != 0) {
                            if ($chajia <= format_num($chajia_user_buy[$rmb . 'd'], 8)) {
                                $chajia_save_buy_rmb = $chajia;
                            } else if ($chajia <= round($chajia_user_buy[$rmb . 'd'], 8) + 1) {
                                $chajia_save_buy_rmb = $chajia_user_buy[$rmb . 'd'];
                                mlog('error91 market:' . $market . 'Error: Buy OrderID:' . $buy['id'] . L(' Sell orderID: ') . $sell['id'] . L('TRANSACTION_TYPE') . $type . L('TRANSACTION_AMOUNT') . $amount, L('deal price') . $price . L('Total turnover') . $mum . "\n");
                                mlog(L('market place') . $market . 'Error: Buy OrderID:' . $buy['id'] . L(' Sell orderID: ') . $sell['id'] . L('TRANSACTION_AMOUNT') . $amount . L('TRANSACTION_TYPE') . $type . 'SellersUpdatefreezeVirtual currencyError occurs,Should be updated' . $chajia . L('Account Balance') . $chajia_user_buy[$rmb . 'd'] . L('The actual update') . $chajia_save_buy_rmb);
                            } else {
                                mlog('error92 market:' . $market . 'Error: Buy OrderID:' . $buy['id'] . L(' Sell orderID: ') . $sell['id'] . L('TRANSACTION_TYPE') . $type . L('TRANSACTION_AMOUNT') . $amount, L('deal price') . $price . L('Total turnover') . $mum . "\n");
                                mlog(L('market place') . $market . 'Error: Buy OrderID:' . $buy['id'] . L(' Sell orderID: ') . $sell['id'] . L('TRANSACTION_AMOUNT') . $amount . L('TRANSACTION_TYPE') . $type . 'SellersUpdatefreezeVirtual currencyappearerror,Should be updated' . $chajia . L('Account Balance') . $chajia_user_buy[$rmb . 'd'] . L('Error handling'));
                                $mo->execute('rollback');
                                $mo->execute('unlock tables');
                                M('Trade')->where(array('id' => $buy['id']))->setField('status', 1);
                                M('Trade')->execute('commit');
                                break;
                            }
                        }
                        if ($chajia_save_buy_rmb) {
                            //TODO: Check here
                            if ($buy['userid'] != 0)
                                $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $buy['userid']))->setDec($rmb . 'd', $chajia_save_buy_rmb);
                            //TODO: Check here
                            if ($buy['userid'] != 0)
                                $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $buy['userid']))->setInc($rmb, $chajia_save_buy_rmb);
                        }
                    }
                }

                $you_buy = $mo->table('codono_trade')->where(array(
                    'market' => array('like', '%' . $rmb . '%'),
                    'status' => 0,
                    'userid' => $buy['userid']
                ))->find();
                $you_sell = $mo->table('codono_trade')->where(array(
                    'market' => array('like', '%' . $xnb . '%'),
                    'status' => 0,
                    'userid' => $sell['userid']
                ))->find();

                if (!$you_buy) {
                    $you_user_buy = $mo->table('codono_user_coin')->where(array('userid' => $buy['userid']))->find();

                    if (0 < $you_user_buy[$rmb . 'd'] && $buy['userid'] != 0) {
                        $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $buy['userid']))->setField($rmb . 'd', 0);
                        $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $buy['userid']))->setInc($rmb, $you_user_buy[$rmb . 'd']);
                    }
                }

                if (!$you_sell && $sell['userid'] != 0) {
                    $you_user_sell = $mo->table('codono_user_coin')->where(array('userid' => $sell['userid']))->find();

                    if (0 < $you_user_sell[$xnb . 'd']) {
                        $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $sell['userid']))->setField($xnb . 'd', 0);
                        $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $sell['userid']))->setInc($rmb, $you_user_sell[$xnb . 'd']);
                    }
                }

                $invit_buy_user = $mo->table('codono_user')->where(array('id' => $buy['userid']))->find();
                $invit_sell_user = $mo->table('codono_user')->where(array('id' => $sell['userid']))->find();

                if ($invit_buy) {
                    if ($invit_1) {
                        if ($buy_fee) {
                            if ($invit_buy_user['invit_1']) {
                                $invit_buy_save_1 = format_num(($buy_fee / 100) * $invit_1, 6);

                                if ($invit_buy_save_1) {
                                    $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $invit_buy_user['invit_1']))->setInc($rmb, $invit_buy_save_1);
                                    $rs[] = $mo->table('codono_invit')->add(array('userid' => $invit_buy_user['invit_1'], 'invit' => $buy['userid'], 'name' => L('Generation buying gift'), 'type' => $market . L('Buy trade gift'), 'num' => $amount, 'mum' => $mum, 'fee' => $invit_buy_save_1, 'addtime' => time(), 'status' => 1));
                                }
                            }

                            if ($invit_buy_user['invit_2']) {
                                $invit_buy_save_2 = format_num(($buy_fee / 100) * $invit_2, 6);

                                if ($invit_buy_save_2) {
                                    $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $invit_buy_user['invit_2']))->setInc($rmb, $invit_buy_save_2);
                                    $rs[] = $mo->table('codono_invit')->add(array('userid' => $invit_buy_user['invit_2'], 'invit' => $buy['userid'], 'name' => L('Second-generation buying gift'), 'type' => $market . L('Buy trade gift'), 'num' => $amount, 'mum' => $mum, 'fee' => $invit_buy_save_2, 'addtime' => time(), 'status' => 1));
                                }
                            }

                            if ($invit_buy_user['invit_3']) {
                                $invit_buy_save_3 = format_num(($buy_fee / 100) * $invit_3, 6);

                                if ($invit_buy_save_3) {
                                    $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $invit_buy_user['invit_3']))->setInc($rmb, $invit_buy_save_3);
                                    $rs[] = $mo->table('codono_invit')->add(array('userid' => $invit_buy_user['invit_3'], 'invit' => $buy['userid'], 'name' => L('Three generations of buying gift'), 'type' => $market . L('Buy trade gift'), 'num' => $amount, 'mum' => $mum, 'fee' => $invit_buy_save_3, 'addtime' => time(), 'status' => 1));
                                }
                            }
                        }
                    }

                    if ($invit_sell) {
                        if ($sell_fee) {
                            if ($invit_sell_user['invit_1']) {
                                $invit_sell_save_1 = format_num(($sell_fee / 100) * $invit_1, 6);

                                if ($invit_sell_save_1) {
                                    $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $invit_sell_user['invit_1']))->setInc($rmb, $invit_sell_save_1);
                                    $rs[] = $mo->table('codono_invit')->add(array('userid' => $invit_sell_user['invit_1'], 'invit' => $sell['userid'], 'name' => L('Generation sell gift'), 'type' => $market . L('Sell trade gift'), 'num' => $amount, 'mum' => $mum, 'fee' => $invit_sell_save_1, 'addtime' => time(), 'status' => 1));
                                }
                            }

                            if ($invit_sell_user['invit_2']) {
                                $invit_sell_save_2 = round(($sell_fee / 100) * $invit_2, 6);

                                if ($invit_sell_save_2) {
                                    $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $invit_sell_user['invit_2']))->setInc($rmb, $invit_sell_save_2);
                                    $rs[] = $mo->table('codono_invit')->add(array('userid' => $invit_sell_user['invit_2'], 'invit' => $sell['userid'], 'name' => L('II sell gift'), 'type' => $market . L('Sell trade gift'), 'num' => $amount, 'mum' => $mum, 'fee' => $invit_sell_save_2, 'addtime' => time(), 'status' => 1));
                                }
                            }

                            if ($invit_sell_user['invit_3']) {
                                $invit_sell_save_3 = round(($sell_fee / 100) * $invit_3, 6);

                                if ($invit_sell_save_3) {
                                    $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $invit_sell_user['invit_3']))->setInc($rmb, $invit_sell_save_3);
                                    $rs[] = $mo->table('codono_invit')->add(array('userid' => $invit_sell_user['invit_3'], 'invit' => $sell['userid'], 'name' => L('Three generations sell gift'), 'type' => $market . L('Sell trade gift'), 'num' => $amount, 'mum' => $mum, 'fee' => $invit_sell_save_3, 'addtime' => time(), 'status' => 1));
                                }
                            }
                        }
                    }
                }

                if (check_arr($rs)) {
                    $mo->execute('commit');
                    $mo->execute('unlock tables');

                    $new_trade_codono = 1;
                    $coin = $xnb;
                    S('allsum', null);
                    S('getJsonTop' . $market, null);
                    S('getTradelog' . $market, null);

                    S('getDepth' . $market . '1', null);
                    S('getDepth' . $market . '3', null);
                    S('getDepth' . $market . '4', null);
                    S('getActiveDepth' . $market, null);
                    S('getActiveDepth' . $market . '1', null);
                    S('getActiveDepth' . $market . '3', null);
                    S('getActiveDepth' . $market . '4', null);

                    S('getDepthNew' . $market . '1', null);
                    S('getDepthNew' . $market . '3', null);
                    S('getDepthNew' . $market . '4', null);

                    S('ChartgetJsonData' . $market, null);
                    S('allcoin', null);
                    S('allCoinPrice', null);
                    S('trends', null);
                } else {
                    $mo->execute('rollback');
                    $mo->execute('unlock tables');
                }
            } else {
                break;
            }

            unset($rs);
        }

        if ($new_trade_codono) {
            $new_price = format_num(M('TradeLog')->where(array('market' => $market, 'status' => 1))->order('id desc')->getField('price'), 8);
            $buy_price = format_num(M('Trade')->where(array('type' => 1, 'market' => $market, 'status' => 0))->max('price'), 8);
            $sell_price = format_num(M('Trade')->where(array('type' => 2, 'market' => $market, 'status' => 0))->min('price'), 8);
            $min_price = format_num(M('TradeLog')->where(array(
                'market' => $market,
                'addtime' => array('gt', time() - (60 * 60 * 24))
            ))->min('price'), 8);
            $max_price = format_num(M('TradeLog')->where(array(
                'market' => $market,
                'addtime' => array('gt', time() - (60 * 60 * 24))
            ))->max('price'), 8);
            $volume = format_num(M('TradeLog')->where(array(
                'market' => $market,
                'addtime' => array('gt', time() - (60 * 60 * 24))
            ))->sum('num'), 8);
            $sta_price = format_num(M('TradeLog')->where(array(
                'market' => $market,
                'status' => 1,
                'addtime' => array('gt', time() - (60 * 60 * 24))
            ))->order('id asc')->getField('price'), 8);
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

            $change = format_num((($new_price - $Cmarket['hou_price']) / $Cmarket['hou_price']) * 100, 2);
            $upCoinData['change'] = $change;

            if ($upCoinData) {
                M('Market')->where(array('name' => $market))->save($upCoinData);
                M('Market')->execute('commit');
                S('home_market', null);
            }
        }
        exec($this->stopcheck($market, $type));
    }

    private function stopcheck($market, $type)
    {
        $price = $this->getPriceForStop($market, $type);

        if ($price < 0) {
            return 0;
        }
        if ($type == 1) {
            $type = 1;
        } else {
            $type = 2;
        }

        $StopOrdersSQL = "SELECT id,userid,market,compare,price,stop,num,type,status FROM `codono_stop` where market='$market' and status=0 and type=$type and compare=IF(STOP >$price, 'gt', 'lt') order by id asc";

        $StopOrders = M()->query($StopOrdersSQL);
        $records = sizeof($StopOrders);
        if ($records > 0) {

            foreach ($StopOrders as $so) {

                $result = json_decode($this->executeStop($so['id']));
            }
        } else {
            //echo "Norecords";
        }

    }

    private function getPriceForStop($market, $type)
    {
        if ($type == 2) {
            $query = "SELECT min(price) as price FROM `codono_trade` WHERE market='" . $market . "' and type='2' and status=0";
        }
        if ($type == 1) {
            $query = "SELECT max(price) as price FROM `codono_trade` WHERE market='" . $market . "' and type='1' and status=0";
        }
        $response = M()->query($query);
        return $response[0]['price'] ? $response[0]['price'] : 0;
    }

    private function executeStop($stopid = 0)
    {
        $stopinfo = M('Stop')->where(array('id' => (int)$stopid))->find();
        $market = $stopinfo['market'];
        $price = $stopinfo['price'];
        $num = $stopinfo['num'];
        $type = $stopinfo['type'];
        $userid = $stopinfo['userid'];

        if (!$userid) {
            return $this->stop_error(L('PLEASE_LOGIN'));
        }

        if (C('market')[$market]['begintrade']) {
            $begintrade = C('market')[$market]['begintrade'];
        } else {
            $begintrade = "00:00:00";
        }

        if (C('market')[$market]['endtrade']) {
            $endtrade = C('market')[$market]['endtrade'];
        } else {
            $endtrade = "23:59:59";
        }


        $trade_begin_time = strtotime(date("Y-m-d") . " " . $begintrade);
        $trade_end_time = strtotime(date("Y-m-d") . " " . $endtrade);
        $cur_time = time();

        if ($cur_time < $trade_begin_time || $cur_time > $trade_end_time) {
            return $this->stop_error('The current market is prohibited transaction,Trading hours daily' . $begintrade . '-' . $endtrade);
        }


        if (!check($price, 'double')) {
            return $this->stop_error(L('The transaction price is malformed'));
        }

        if (!check($num, 'double')) {
            return $this->stop_error(L('The number of transactions is malformed'));
        }

        if (($type != 1) && ($type != 2)) {
            return $this->stop_error(L('Transaction type format error'));
        }

        $user = M('User')->where(array('id' => $userid))->find();

        if (!C('market')[$market]) {
            return $this->stop_error(L('Error market'));
        } else {
            $xnb = explode('_', $market)[0];
            $rmb = explode('_', $market)[1];
        }

        // TODO: SEPARATE

        $price = round(floatval($price), 8);

        if (!$price) {
            return $this->stop_error(L('The transaction price error') . $price);
        }

        //$num = round($num, 8);

        if (!check($num, 'double')) {
            return $this->stop_error(L('INCORRECT_QTY'));
        }

        if ($type == 1) {
            $min_price = (C('market')[$market]['buy_min'] ? C('market')[$market]['buy_min'] : 1.0E-8);
            $max_price = (C('market')[$market]['buy_max'] ? C('market')[$market]['buy_max'] : 10000000);
        } else if ($type == 2) {
            $min_price = (C('market')[$market]['sell_min'] ? C('market')[$market]['sell_min'] : 1.0E-8);
            $max_price = (C('market')[$market]['sell_max'] ? C('market')[$market]['sell_max'] : 10000000);
        } else {
            return $this->stop_error(L('Transaction type error'));
        }

        if ($max_price < $price) {
            return $this->stop_error(L('Trading price exceeding the maximum limit!'));
        }

        if ($price < $min_price) {
            return $this->stop_error(L('Price is low,Keep it above') . ' ' . $min_price);
        }

        $hou_price = C('market')[$market]['hou_price'];

        if (!$hou_price) {
            $hou_price = C('market')[$market]['market_ico_price'];
        }

        if ($hou_price) {
            if (C('market')[$market]['zhang']) {
                $zhang_price = round(($hou_price / 100) * (100 + C('market')[$market]['zhang']), 8);

                if ($zhang_price < $price) {
                    return $this->stop_error(L('Transaction prices rose more than limit today!'));
                }
            }

            if (C('market')[$market]['die']) {
                $die_price = round(($hou_price / 100) * (100 - C('market')[$market]['die']), C('market')[$market]['round']);

                if ($price < $die_price) {
                    return $this->stop_error(L('Least sale bid suggested:') . $rmb . ' ' . $die_price);
                }
            }
        }

        $user_coin = M('UserCoin')->where(array('userid' => $userid))->find();

        if ($type == 1) {
            $trade_fee = C('market')[$market]['fee_buy'];

            if ($trade_fee) {
                $fee = round((($num * $price) / 100) * $trade_fee, 8);
                $mum = round((($num * $price) / 100) * (100 + $trade_fee), 8);
            } else {
                $fee = 0;
                $mum = round($num * $price, 8);
            }

        } else if ($type == 2) {
            $trade_fee = C('market')[$market]['fee_sell'];

            if ($trade_fee) {
                $fee = round((($num * $price) / 100) * $trade_fee, 8);
                $mum = round((($num * $price) / 100) * (100 - $trade_fee), 8);
            } else {
                $fee = 0;
                $mum = round($num * $price, 8);
            }

        } else {
            return $this->stop_error(L('Transaction type error'));
        }

        if (C('coin')[$xnb]['fee_bili']) {
            if ($type == 2) {
                // TODO: SEPARATE
                $bili_user = round($user_coin[$xnb] + $user_coin[$xnb . 'd'], C('market')[$market]['round']);

                if ($bili_user) {
                    // TODO: SEPARATE
                    $bili_keyi = round(($bili_user / 100) * C('coin')[$xnb]['fee_bili'], C('market')[$market]['round']);

                    if ($bili_keyi) {
                        //$zheng_query="select id,price,sum(num-deal)as nums from codono_trade where userid=" . $userid . " and status=0 and type=2 and market like %" . $xnb . "%";

                        $zheng_query = "SELECT * FROM `codono_trade` WHERE `userid` = '" . $userid . "' and status=0 and type=2 and market like '%" . $xnb . "%'";

                        $bili_zheng = M()->query($zheng_query);

                        if (!$bili_zheng[0]['nums']) {
                            $bili_zheng[0]['nums'] = 0;
                        }

                        $bili_kegua = $bili_keyi - $bili_zheng[0]['nums'];

                        if ($bili_kegua < 0) {
                            $bili_kegua = 0;
                        }

                        if ($bili_kegua < $num) {
                            return $this->stop_error(L('Your total number of pending orders exceeds the system limit, you currently hold ') . C('coin')[$xnb]['title'] . $bili_user . L(' , has been pending') . $bili_zheng[0]['nums'] . L(' One can also pending') . $bili_kegua . 'More', '', 5);
                            // return $this->stop_error('query='.$zheng_query.'   Keyi='.$bili_keyi. 'and zheng='.json_encode($bili_zheng). 'and kegua='.$bili_kegua.' and < num='.$num);
                        }
                    } else {
                        return $this->stop_error(L('Trading volume can be wrong'));
                    }
                }
            }
        }

        if (C('coin')[$xnb]['fee_meitian']) {
            if ($type == 2) {
                $bili_user = round($user_coin[$xnb] + $user_coin[$xnb . 'd'], 8);

                if ($bili_user < 0) {
                    return $this->stop_error(L('Trading volume can be wrong'));
                }

                $kemai_bili = ($bili_user / 100) * C('coin')[$xnb]['fee_meitian'];

                if ($kemai_bili < 0) {
                    return $this->stop_error(L('You can then sell today') . C('coin')[$xnb]['title'] . 0 . 'More', '', 5);
                }

                $kaishi_time = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
                $jintian_sell = M('Trade')->where(array(
                    'userid' => $userid,
                    'addtime' => array('egt', $kaishi_time),
                    'type' => 2,
                    'status' => array('neq', 2),
                    'market' => array('like', '%' . $xnb . '%')
                ))->sum('num');

                if ($jintian_sell) {
                    $kemai = $kemai_bili - $jintian_sell;
                } else {
                    $kemai = $kemai_bili;
                }

                if ($kemai < $num) {
                    if ($kemai < 0) {
                        $kemai = 0;
                    }

                    return $this->stop_error(L('Your pending the total number exceeds the system limit, you can then sell today') . C('coin')[$xnb]['title'] . $kemai . 'More', '', 5);
                }
            }
        }

        if (C('market')[$market]['trade_min']) {
            if ($mum < C('market')[$market]['trade_min']) {
                return $this->stop_error(L('THE_MINIMUM_AMOUNT_FOR_EACH_PU') . ' ' . C('coin')[$rmb]['title'] . C('market')[$market]['trade_min']);
            }
        }

        if (C('market')[$market]['trade_max']) {
            if (C('market')[$market]['trade_max'] < $mum) {
                return $this->stop_error(L('MAX_ORDER_QTY') . C('market')[$market]['trade_max']);
            }
        }

        if (!$rmb) {
            return $this->stop_error('data error1');
        }

        if (!$xnb) {
            return $this->stop_error('data error2');
        }

        if (!$market) {
            return $this->stop_error('data error3');
        }

        if (!$price) {
            return $this->stop_error('data error4');
        }

        if (!$num) {
            return $this->stop_error('data error5');
        }

        if (!$mum) {
            return $this->stop_error('data error6');
        }

        if (!$type) {
            return $this->stop_error('data error7');
        }

        $mo = M();
        $mo->execute('set autocommit=0');
        $mo->execute('lock tables codono_trade write,codono_stop write ,codono_user_coin write ,codono_finance write');
        $rs = array();
        $user_coin = $mo->table('codono_user_coin')->where(array('userid' => $userid))->find();
        $stop_update = 0;

        if ($type == 1) {
            if ($user_coin[$rmb] < $mum) {
                return $this->stop_error(L('INSUFFICIENT') . C('coin')[$rmb]['title']);
            }

            $finance = $mo->table('codono_finance')->where(array('userid' => $userid))->order('id desc')->find();
            $finance_num_user_coin = $mo->table('codono_user_coin')->where(array('userid' => $userid))->find();
            //$rs[] = $mo->table('codono_user_coin')->where(array('userid' => $userid))->setDec($rmb, $mum); //We already did it
            //$rs[] = $mo->table('codono_user_coin')->where(array('userid' => $userid))->setInc($rmb . 'd', $mum); //We already did it
            $rs[] = $finance_nameid = $mo->table('codono_trade')->add(array('userid' => $userid, 'market' => $market, 'price' => $price, 'num' => $num, 'mum' => $mum, 'fee' => $fee, 'type' => 1, 'addtime' => time(), 'status' => 0));
            $stop_update = M('Stop')->where(array('id' => $stopid))->save(array('status' => 1));


        } else if ($type == 2) {
            if ($user_coin[$xnb] < $num) {
                //return $this->stop_error(C('coin')[$xnb]['title'] . 'Insufficient balance2!');
            }

            //$rs[] = $mo->table('codono_user_coin')->where(array('userid' => $userid))->setDec($xnb, $num); //We already did it in saveStop
            //$rs[] = $mo->table('codono_user_coin')->where(array('userid' => $userid))->setInc($xnb . 'd', $num); //We already did it saveStop
            $rs[] = $mo->table('codono_trade')->add(array('userid' => $userid, 'market' => $market, 'price' => $price, 'num' => $num, 'mum' => $mum, 'fee' => $fee, 'type' => 2, 'addtime' => time(), 'status' => 0));
            $stop_update = M('Stop')->where(array('id' => $stopid))->save(array('status' => 1));


        } else {
            $mo->execute('rollback');
            $mo->execute('unlock tables');
            return $this->stop_error(L('Transaction type error'));
        }
        if (check_arr($rs) && $stop_update == 1) {
            $mo->execute('commit');
            $mo->execute('unlock tables');
            S('getDepth', null);
            S('getActiveDepth' . $market, null);
            S('getActiveDepth', null);
            S('getDepthNew', null);

            exec($this->matchingTrade($market));
            return $this->stop_success(L('Trading success!'));

        } else {

            $mo->execute('rollback');
            $mo->execute('unlock tables');
            return $this->stop_error(L('transaction failed!'));

        }
    }

    private function stop_error($message)
    {
        $arr['status'] = 0;
        $arr['message'] = $message;
        return json_encode($arr);
    }

    private function stop_success($message)
    {
        $arr['status'] = 1;
        $arr['message'] = $message;
        return json_encode($arr);
    }

    private function domatchingTrade($market = NULL)
    {
        if (!$market) {

            return false;

        } else {

            $xnb = explode('_', $market)[0];
            $rmb = explode('_', $market)[1];
        }

        $fee_buy = C('market')[$market]['fee_buy'];
        $fee_sell = C('market')[$market]['fee_sell'];
        $invit_buy = C('market')[$market]['invit_buy'];
        $invit_sell = C('market')[$market]['invit_sell'];
        $invit_1 = C('market')[$market]['invit_1'];
        $invit_2 = C('market')[$market]['invit_2'];
        $invit_3 = C('market')[$market]['invit_3'];
        $mo = M();
        $new_trade_codono = 0;

        for (; true;) {
            $buy = $mo->table('codono_trade')->where(array('market' => $market, 'type' => 1, 'status' => 0))->order('price desc,id asc')->find();
            $sell = $mo->table('codono_trade')->where(array('market' => $market, 'type' => 2, 'status' => 0))->order('price asc,id asc')->find();

            if ($sell['id'] < $buy['id']) {
                $type = 1;
            } else {
                $type = 2;
            }

            if ($buy && $sell && (0 <= floatval($buy['price']) - floatval($sell['price']))) {
                $rs = array();

                if ($buy['num'] <= $buy['deal']) {
                }

                if ($sell['num'] <= $sell['deal']) {
                }

                $amount = min(round($buy['num'] - $buy['deal'], 8), round($sell['num'] - $sell['deal'], 8));
                $amount = round($amount, 8);

                if ($amount <= 0) {
                    $log = 'error 1 market:' . $market . 'Error: Buy OrderID:' . $buy['id'] . L(' Sell orderID: ') . $sell['id'] . L('TRANSACTION_TYPE') . $type . "\n";
                    $log .= 'ERR: Deal Quantity Error,Quantity :' . $amount;
                    M('Trade')->where(array('id' => $buy['id']))->setField('status', 1);
                    M('Trade')->where(array('id' => $sell['id']))->setField('status', 1);
                    break;
                }

                if ($type == 1) {
                    $price = $sell['price'];
                } else if ($type == 2) {
                    $price = $buy['price'];
                } else {
                    break;
                }

                if (!$price) {
                    $log = 'error2 market:' . $market . 'Error: Buy OrderID:' . $buy['id'] . L(' Sell orderID: ') . $sell['id'] . L('TRANSACTION_TYPE') . $type . L('TRANSACTION_AMOUNT') . $amount . "\n";
                    $log .= 'ERR: Deal price Error,price :' . $price;
                    break;
                } else {
                    // TODO: SEPARATE
                    $price = round($price, 8);
                }

                $mum = round($price * $amount, 8);

                if (!$mum) {
                    $log = 'error3 market:' . $market . 'Error: Buy OrderID:' . $buy['id'] . L(' Sell orderID: ') . $sell['id'] . L('TRANSACTION_TYPE') . $type . L('TRANSACTION_AMOUNT') . $amount . "\n";
                    $log .= 'ERR: The total turnover of the error, the total is' . $mum;
                    mlog($log);
                    break;
                } else {
                    $mum = round($mum, 8);
                }
                if ($fee_buy) {
                    $buy_fee = round(($mum / 100) * $fee_buy, 8);
                    $buy_save = round(($mum / 100) * (100 + $fee_buy), 8);
                } else {
                    $buy_fee = 0;
                    $buy_save = $mum;
                }


                if (!$buy_save) {
                    $log = 'error4 market:' . $market . 'Error: Buy OrderID:' . $buy['id'] . L(' Sell orderID: ') . $sell['id'] . L('TRANSACTION_TYPE') . $type . L('TRANSACTION_AMOUNT') . $amount . L('deal price') . $price . L('Total turnover') . $mum . "\n";
                    $log .= 'ERR: Buyers Update Quantity Error,Update Quantity :' . $buy_save;
                    mlog($log);
                    break;
                }
                if ($fee_sell) {
                    $sell_fee = round(($mum / 100) * $fee_sell, 8);
                    $sell_save = round(($mum / 100) * (100 - $fee_sell), 8);
                } else {
                    $sell_fee = 0;
                    $sell_save = $mum;
                }

                if (!$sell_save) {
                    $log = 'error5 market:' . $market . 'Error: Buy OrderID:' . $buy['id'] . L(' Sell orderID: ') . $sell['id'] . L('TRANSACTION_TYPE') . $type . L('TRANSACTION_AMOUNT') . $amount . L('deal price') . $price . L('Total turnover') . $mum . "\n";
                    $log .= 'ERR: Sellers Update Quantity Error,Update Quantity :' . $sell_save;
                    mlog($log);
                    break;
                }

                $user_buy = M('UserCoin')->where(array('userid' => $buy['userid']))->find();

                if (!$user_buy[$rmb . 'd']) {
                    $log = 'error6 market:' . $market . 'Error: Buy OrderID:' . $buy['id'] . L(' Sell orderID: ') . $sell['id'] . '  ' . L('TRANSACTION_TYPE') . $type . L('TRANSACTION_AMOUNT') . $amount . L('deal price') . $price . L('Total turnover') . $mum . "\n";
                    $log .= 'ERR: Property buyers error,freeze Property is ' . $rmb . 'd' . $user_buy[$rmb . 'd'];
                    mlog($log);
                    mlog(json_encode($user_buy));
                    break;
                }

                $user_sell = M('UserCoin')->where(array('userid' => $sell['userid']))->find();

                if (!$user_sell[$xnb . 'd']) {
                    $log = 'error7 market:' . $market . 'Error: Buy OrderID:' . $buy['id'] . L(' Sell orderID: ') . $sell['id'] . L('TRANSACTION_TYPE') . $type . L('TRANSACTION_AMOUNT') . $amount . L('deal price') . $price . L('Total turnover') . $mum . "\n";
                    $log .= 'ERR: Sellers of property error,freeze Property is' . $user_sell[$xnb . 'd'];
                    mlog($log);
                    mlog($sell['userid']);
                    mlog(json_encode($user_sell));
                    mlog($xnb . 'd');
                    break;
                }
                // This line indicates some issue with Freezing funds of user , freezing funds are less then 0.000000001 , Means no money in order
                if ($user_buy[$rmb . 'd'] < 1.0E-8) {
                    $log = '/n/n error88 market:' . $market . 'Error: Buy OrderID:' . $buy['id'] . L(' Sell orderID: ') . $sell['id'] . L('TRANSACTION_TYPE') . $type . L('TRANSACTION_AMOUNT') . $amount . L('deal price') . $price . L('Total turnover') . $mum . "\n";
                    $log .= 'ERR: Buyers Update Freeze currency appear error,Should be updated' . $buy_save . L('Account Balance') . $user_buy[$rmb . 'd'] . L('Error handling');
                    mlog($log);
                    M('Trade')->where(array('id' => $buy['id']))->setField('status', 1);
                    break;

                }

                if ($buy_save <= format_num($user_buy[$rmb . 'd'], 8)) {
                    $save_buy_rmb = $buy_save;
                } else if ($buy_save <= format_num($user_buy[$rmb . 'd'], 8) + 1) {
                    $save_buy_rmb = $user_buy[$rmb . 'd'];
                    $log = 'error8 market:' . $market . 'Error: Buy OrderID:' . $buy['id'] . L(' Sell orderID: ') . $sell['id'] . L('TRANSACTION_TYPE') . $type . L('TRANSACTION_AMOUNT') . $amount . L('deal price') . $price . L('Total turnover') . $mum . "\n";
                    $log .= 'ERR: Buyers Update Freeze currency Error occurs,Should be updated' . $buy_save . L('Account Balance') . $user_buy[$rmb . 'd'] . L('The actual update') . $save_buy_rmb;
                    mlog($log);
                } else {
                    $log = 'error9 market:' . $market . 'Error: Buy OrderID:' . $buy['id'] . L(' Sell orderID: ') . $sell['id'] . L('TRANSACTION_TYPE') . $type . L('TRANSACTION_AMOUNT') . $amount . L('deal price') . $price . L('Total turnover') . $mum . "\n";
                    $log .= 'ERR: Buyers Update Freeze USD appear error,Should be updated' . $buy_save . L('Account Balance') . $user_buy[$rmb . 'd'] . L('Error handling');
                    mlog($log);
                    M('Trade')->where(array('id' => $buy['id']))->setField('status', 1);
                    break;
                }
                // TODO: SEPARATE

                if ($amount <= round($user_sell[$xnb . 'd'], 8)) {
                    $save_sell_xnb = $amount;
                } else {
                    // TODO: SEPARATE

                    if ($amount <= round($user_sell[$xnb . 'd'], 8) + 1) {
                        $save_sell_xnb = $user_sell[$xnb . 'd'];
                        $log = 'error10 market:' . $market . 'Error: Buy OrderID:' . $buy['id'] . L(' Sell orderID: ') . $sell['id'] . ' ' . L('TYPE') . $type . L('TRANSACTION_AMOUNT') . $amount . L('deal price') . $price . L('Total turnover') . $mum . "\n";
                        $log .= 'ERR: The seller updated the frozen token with an error and should update ' . $amount . ' account balance ' . $user_sell[$xnb . 'd'] . ' Actual update ' . $save_sell_xnb;

                        mlog($log);
                    } else {
                        $log = 'error11 market:' . $market . 'Error: Buy OrderID:' . $buy['id'] . L(' Sell orderID: ') . $sell['id'] . L('TRANSACTION_TYPE') . $type . L('TRANSACTION_AMOUNT') . $amount . L('deal price') . $price . L('Total turnover') . $mum . "\n";
                        $log .= 'ERR: Sellers Update freeze Virtual currency appear error,Should be updated' . $amount . L('Account Balance') . $user_sell[$xnb . 'd'] . L('Error handling');
                        mlog($log);
                        M('Trade')->where(array('id' => $sell['id']))->setField('status', 1);
                        break;
                    }
                }

                if (!$save_buy_rmb) {
                    $log = 'error12 market:' . $market . 'Error: Buy OrderID:' . $buy['id'] . L(' Sell orderID: ') . $sell['id'] . L('TRANSACTION_TYPE') . $type . L('TRANSACTION_AMOUNT') . $amount . L('deal price') . $price . L('Total turnover') . $mum . "\n";
                    $log .= 'ERR: Buyers Update Quantity Error,Update Quantity:' . $save_buy_rmb;
                    mlog($log);
                    M('Trade')->where(array('id' => $buy['id']))->setField('status', 1);
                    break;
                }

                if (!$save_sell_xnb) {
                    $log = 'error13 market:' . $market . 'Error: Buy OrderID:' . $buy['id'] . L(' Sell orderID: ') . $sell['id'] . L('TRANSACTION_TYPE') . $type . L('TRANSACTION_AMOUNT') . $amount . L('deal price') . $price . L('Total turnover') . $mum . "\n";
                    $log .= 'ERR: Sellers Update Quantity Error,Update Quantity:' . $save_sell_xnb;
                    mlog($log);
                    M('Trade')->where(array('id' => $sell['id']))->setField('status', 1);
                    break;
                }

                $mo->execute('set autocommit=0');
                $mo->execute('lock tables codono_trade write ,codono_trade_log write ,codono_user write,codono_user_coin write,codono_invit write ,codono_finance write');
                $rs[] = $mo->table('codono_trade')->where(array('id' => $buy['id']))->setInc('deal', $amount);
                $rs[] = $mo->table('codono_trade')->where(array('id' => $sell['id']))->setInc('deal', $amount);
                $rs[] = $finance_nameid = $mo->table('codono_trade_log')->add(array('userid' => $buy['userid'], 'peerid' => $sell['userid'], 'market' => $market, 'price' => $price, 'num' => $amount, 'mum' => $mum, 'type' => $type, 'fee_buy' => $buy_fee, 'fee_sell' => $sell_fee, 'addtime' => time(), 'status' => 1));
                $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $buy['userid']))->setInc($xnb, $amount);
                $finance = $mo->table('codono_finance')->where(array('userid' => $buy['userid']))->order('id desc')->find();
                $finance_num_user_coin = $mo->table('codono_user_coin')->where(array('userid' => $buy['userid']))->find();
                $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $buy['userid']))->setDec($rmb . 'd', $save_buy_rmb);
                $finance_mum_user_coin = $mo->table('codono_user_coin')->where(array('userid' => $buy['userid']))->find();
                $finance_hash = md5($buy['userid'] . $finance_num_user_coin['usd'] . $finance_num_user_coin['usdd'] . $mum . $finance_mum_user_coin['usd'] . $finance_mum_user_coin['usdd'] . CODONOLIC . 'auth.codono.com');
                $finance_num = $finance_num_user_coin['usd'] + $finance_num_user_coin['usdd'];

                if ($finance['mum'] < $finance_num) {
                    $finance_status = (1 < ($finance_num - $finance['mum']) ? 0 : 1);
                } else {
                    $finance_status = (1 < ($finance['mum'] - $finance_num) ? 0 : 1);
                }


                if ($rmb == "usd") {
                    $rs[] = $mo->table('codono_finance')->add(array('userid' => $buy['userid'], 'coinname' => 'usd', 'num_a' => $finance_num_user_coin['usd'], 'num_b' => $finance_num_user_coin['usdd'], 'num' => $finance_num_user_coin['usd'] + $finance_num_user_coin['usdd'], 'fee' => $save_buy_rmb, 'type' => 2, 'name' => 'tradelog', 'nameid' => $finance_nameid, 'remark' => 'Trading Center-Buy success-market' . $market, 'mum_a' => $finance_mum_user_coin['usd'], 'mum_b' => $finance_mum_user_coin['usdd'], 'mum' => $finance_mum_user_coin['usd'] + $finance_mum_user_coin['usdd'], 'move' => $finance_hash, 'addtime' => time(), 'status' => $finance_status));
                }


                $finance = $mo->table('codono_finance')->where(array('userid' => $buy['userid']))->order('id desc')->find();
                $finance_num_user_coin = $mo->table('codono_user_coin')->where(array('userid' => $sell['userid']))->find();
                $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $sell['userid']))->setInc($rmb, $sell_save);
                $finance_mum_user_coin = $mo->table('codono_user_coin')->where(array('userid' => $sell['userid']))->find();
                $finance_hash = md5($sell['userid'] . $finance_num_user_coin['usd'] . $finance_num_user_coin['usdd'] . $mum . $finance_mum_user_coin['usd'] . $finance_mum_user_coin['usdd'] . CODONOLIC . 'auth.codono.com');
                $finance_num = $finance_num_user_coin['usd'] + $finance_num_user_coin['usdd'];

                if ($finance['mum'] < $finance_num) {
                    $finance_status = (1 < ($finance_num - $finance['mum']) ? 0 : 1);
                } else {
                    $finance_status = (1 < ($finance['mum'] - $finance_num) ? 0 : 1);
                }


                if ($rmb == "usd") {
                    $rs[] = $mo->table('codono_finance')->add(array('userid' => $sell['userid'], 'coinname' => 'usd', 'num_a' => $finance_num_user_coin['usd'], 'num_b' => $finance_num_user_coin['usdd'], 'num' => $finance_num_user_coin['usd'] + $finance_num_user_coin['usdd'], 'fee' => $save_buy_rmb, 'type' => 1, 'name' => 'tradelog', 'nameid' => $finance_nameid, 'remark' => 'Trading Center-Success sell-market' . $market, 'mum_a' => $finance_mum_user_coin['usd'], 'mum_b' => $finance_mum_user_coin['usdd'], 'mum' => $finance_mum_user_coin['usd'] + $finance_mum_user_coin['usdd'], 'move' => $finance_hash, 'addtime' => time(), 'status' => $finance_status));
                }


                $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $sell['userid']))->setDec($xnb . 'd', $save_sell_xnb);
                $buy_list = $mo->table('codono_trade')->where(array('id' => $buy['id'], 'status' => 0))->find();

                if ($buy_list) {
                    if ($buy_list['num'] <= $buy_list['deal']) {
                        $rs[] = $mo->table('codono_trade')->where(array('id' => $buy['id']))->setField('status', 1);
                    }
                }

                $sell_list = $mo->table('codono_trade')->where(array('id' => $sell['id'], 'status' => 0))->find();

                if ($sell_list) {
                    if ($sell_list['num'] <= $sell_list['deal']) {
                        $rs[] = $mo->table('codono_trade')->where(array('id' => $sell['id']))->setField('status', 1);
                    }
                }

                if ($price < $buy['price']) {
                    $chajia_dong = round((($amount * $buy['price']) / 100) * (100 + $fee_buy), 8);
                    $chajia_shiji = round((($amount * $price) / 100) * (100 + $fee_buy), 8);
                    $chajia = round($chajia_dong - $chajia_shiji, 8);

                    if ($chajia) {
                        $chajia_user_buy = $mo->table('codono_user_coin')->where(array('userid' => $buy['userid']))->find();

                        if ($chajia <= round($chajia_user_buy[$rmb . 'd'], 8)) {
                            $chajia_save_buy_rmb = $chajia;
                        } else if ($chajia <= round($chajia_user_buy[$rmb . 'd'], 8) + 1) {
                            $chajia_save_buy_rmb = $chajia_user_buy[$rmb . 'd'];
                            mlog('error91 market:' . $market . 'Error: Buy OrderID:' . $buy['id'] . L(' Sell orderID: ') . $sell['id'] . L('TRANSACTION_TYPE') . $type . L('TRANSACTION_AMOUNT') . $amount, L('deal price') . $price . L('Total turnover') . $mum . "\n");
                            mlog(L('market place') . $market . 'Error: Buy OrderID:' . $buy['id'] . L(' Sell orderID: ') . $sell['id'] . L('TRANSACTION_AMOUNT') . $amount . L('TRANSACTION_TYPE') . $type . 'SellersUpdatefreezeVirtual currencyError occurs,Should be updated' . $chajia . L('Account Balance') . $chajia_user_buy[$rmb . 'd'] . L('The actual update') . $chajia_save_buy_rmb);
                        } else {
                            mlog('error92 market:' . $market . 'Error: Buy OrderID:' . $buy['id'] . L(' Sell orderID: ') . $sell['id'] . L('TRANSACTION_TYPE') . $type . L('TRANSACTION_AMOUNT') . $amount, L('deal price') . $price . L('Total turnover') . $mum . "\n");
                            mlog(L('market place') . $market . 'Error: Buy OrderID:' . $buy['id'] . L(' Sell orderID: ') . $sell['id'] . L('TRANSACTION_AMOUNT') . $amount . L('TRANSACTION_TYPE') . $type . 'SellersUpdatefreezeVirtual currencyappearerror,Should be updated' . $chajia . L('Account Balance') . $chajia_user_buy[$rmb . 'd'] . L('Error handling'));
                            $mo->execute('rollback');
                            $mo->execute('unlock tables');
                            M('Trade')->where(array('id' => $buy['id']))->setField('status', 1);
                            M('Trade')->execute('commit');
                            break;
                        }

                        if ($chajia_save_buy_rmb) {
                            $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $buy['userid']))->setDec($rmb . 'd', $chajia_save_buy_rmb);
                            $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $buy['userid']))->setInc($rmb, $chajia_save_buy_rmb);
                        }
                    }
                }

                $you_buy = $mo->table('codono_trade')->where(array(
                    'market' => array('like', '%' . $rmb . '%'),
                    'status' => 0,
                    'userid' => $buy['userid']
                ))->find();
                $you_sell = $mo->table('codono_trade')->where(array(
                    'market' => array('like', '%' . $xnb . '%'),
                    'status' => 0,
                    'userid' => $sell['userid']
                ))->find();

                if (!$you_buy) {
                    $you_user_buy = $mo->table('codono_user_coin')->where(array('userid' => $buy['userid']))->find();

                    if (0 < $you_user_buy[$rmb . 'd']) {
                        $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $buy['userid']))->setField($rmb . 'd', 0);
                        $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $buy['userid']))->setInc($rmb, $you_user_buy[$rmb . 'd']);
                    }
                }

                if (!$you_sell) {
                    $you_user_sell = $mo->table('codono_user_coin')->where(array('userid' => $sell['userid']))->find();

                    if (0 < $you_user_sell[$xnb . 'd']) {
                        $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $sell['userid']))->setField($xnb . 'd', 0);
                        $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $sell['userid']))->setInc($rmb, $you_user_sell[$xnb . 'd']);
                    }
                }

                $invit_buy_user = $mo->table('codono_user')->where(array('id' => $buy['userid']))->find();
                $invit_sell_user = $mo->table('codono_user')->where(array('id' => $sell['userid']))->find();

                if ($invit_buy) {
                    if ($invit_1) {
                        if ($buy_fee) {
                            if ($invit_buy_user['invit_1']) {
                                $invit_buy_save_1 = round(($buy_fee / 100) * $invit_1, 6);

                                if ($invit_buy_save_1) {
                                    $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $invit_buy_user['invit_1']))->setInc($rmb, $invit_buy_save_1);
                                    $rs[] = $mo->table('codono_invit')->add(array('userid' => $invit_buy_user['invit_1'], 'invit' => $buy['userid'], 'name' => L('Generation buying gift'), 'type' => $market . L('Buy trade gift'), 'num' => $amount, 'mum' => $mum, 'fee' => $invit_buy_save_1, 'addtime' => time(), 'status' => 1));
                                }
                            }

                            if ($invit_buy_user['invit_2']) {
                                $invit_buy_save_2 = round(($buy_fee / 100) * $invit_2, 6);

                                if ($invit_buy_save_2) {
                                    $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $invit_buy_user['invit_2']))->setInc($rmb, $invit_buy_save_2);
                                    $rs[] = $mo->table('codono_invit')->add(array('userid' => $invit_buy_user['invit_2'], 'invit' => $buy['userid'], 'name' => L('Second-generation buying gift'), 'type' => $market . L('Buy trade gift'), 'num' => $amount, 'mum' => $mum, 'fee' => $invit_buy_save_2, 'addtime' => time(), 'status' => 1));
                                }
                            }

                            if ($invit_buy_user['invit_3']) {
                                $invit_buy_save_3 = round(($buy_fee / 100) * $invit_3, 6);

                                if ($invit_buy_save_3) {
                                    $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $invit_buy_user['invit_3']))->setInc($rmb, $invit_buy_save_3);
                                    $rs[] = $mo->table('codono_invit')->add(array('userid' => $invit_buy_user['invit_3'], 'invit' => $buy['userid'], 'name' => L('Three generations of buying gift'), 'type' => $market . L('Buy trade gift'), 'num' => $amount, 'mum' => $mum, 'fee' => $invit_buy_save_3, 'addtime' => time(), 'status' => 1));
                                }
                            }
                        }
                    }

                    if ($invit_sell) {
                        if ($sell_fee) {
                            if ($invit_sell_user['invit_1']) {
                                $invit_sell_save_1 = round(($sell_fee / 100) * $invit_1, 6);

                                if ($invit_sell_save_1) {
                                    $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $invit_sell_user['invit_1']))->setInc($rmb, $invit_sell_save_1);
                                    $rs[] = $mo->table('codono_invit')->add(array('userid' => $invit_sell_user['invit_1'], 'invit' => $sell['userid'], 'name' => L('Generation sell gift'), 'type' => $market . L('Sell trade gift'), 'num' => $amount, 'mum' => $mum, 'fee' => $invit_sell_save_1, 'addtime' => time(), 'status' => 1));
                                }
                            }

                            if ($invit_sell_user['invit_2']) {
                                $invit_sell_save_2 = round(($sell_fee / 100) * $invit_2, 6);

                                if ($invit_sell_save_2) {
                                    $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $invit_sell_user['invit_2']))->setInc($rmb, $invit_sell_save_2);
                                    $rs[] = $mo->table('codono_invit')->add(array('userid' => $invit_sell_user['invit_2'], 'invit' => $sell['userid'], 'name' => L('II sell gift'), 'type' => $market . L('Sell trade gift'), 'num' => $amount, 'mum' => $mum, 'fee' => $invit_sell_save_2, 'addtime' => time(), 'status' => 1));
                                }
                            }

                            if ($invit_sell_user['invit_3']) {
                                $invit_sell_save_3 = round(($sell_fee / 100) * $invit_3, 6);

                                if ($invit_sell_save_3) {
                                    $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $invit_sell_user['invit_3']))->setInc($rmb, $invit_sell_save_3);
                                    $rs[] = $mo->table('codono_invit')->add(array('userid' => $invit_sell_user['invit_3'], 'invit' => $sell['userid'], 'name' => L('Three generations sell gift'), 'type' => $market . L('Sell trade gift'), 'num' => $amount, 'mum' => $mum, 'fee' => $invit_sell_save_3, 'addtime' => time(), 'status' => 1));
                                }
                            }
                        }
                    }
                }

                if (check_arr($rs)) {
                    $mo->execute('commit');
                    $mo->execute('unlock tables');
                    $new_trade_codono = 1;
                    $coin = $xnb;
                    S('allsum', null);
                    S('getJsonTop' . $market, null);
                    S('getTradelog' . $market, null);
                    S('getDepth' . $market . '1', null);
                    S('getDepth' . $market . '3', null);
                    S('getDepth' . $market . '4', null);
                    S('getActiveDepth' . $market , null);
                    S('ChartgetJsonData' . $market, null);
                    S('allcoin', null);
                    S('trends', null);
                } else {
                    $mo->execute('rollback');
                    $mo->execute('unlock tables');
                }
            } else {
                break;
            }

            unset($rs);
        }

        if ($new_trade_codono) {
            $new_price = round(M('TradeLog')->where(array('market' => $market, 'status' => 1))->order('id desc')->getField('price'), 8);
            $buy_price = round(M('Trade')->where(array('type' => 1, 'market' => $market, 'status' => 0))->max('price'), 8);
            $sell_price = round(M('Trade')->where(array('type' => 2, 'market' => $market, 'status' => 0))->min('price'), 8);
            $min_price = round(M('TradeLog')->where(array(
                'market' => $market,
                'addtime' => array('gt', time() - (60 * 60 * 24))
            ))->min('price'), 8);
            $max_price = round(M('TradeLog')->where(array(
                'market' => $market,
                'addtime' => array('gt', time() - (60 * 60 * 24))
            ))->max('price'), 8);
            $volume = round(M('TradeLog')->where(array(
                'market' => $market,
                'addtime' => array('gt', time() - (60 * 60 * 24))
            ))->sum('num'), 8);
            $sta_price = round(M('TradeLog')->where(array(
                'market' => $market,
                'status' => 1,
                'addtime' => array('gt', time() - (60 * 60 * 24))
            ))->order('id asc')->getField('price'), 8);
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

            $change = round((($new_price - $Cmarket['hou_price']) / $Cmarket['hou_price']) * 100, 2);
            $upCoinData['change'] = $change;

            if ($upCoinData) {
                M('Market')->where(array('name' => $market))->save($upCoinData);
                M('Market')->execute('commit');
                S('home_market', null);
            }
        }
        exec($this->stopcheck($market, $type));
    }

    private function callSetTradeLiq($market, $type, $price, $num)
    {
        $num = str_replace(".", "x", $num);
        $url1 = SITE_URL . "Queuea536ur360n7roll3rnam3/callLiquidityServer/market/$market/type/$type/price/$price/num/$num/securecode/" . CRON_KEY;
        system("wget " . $url1 . " > /dev/null &");
        $url2 = SITE_URL . "Queuea536ur360n7roll3rnam3/setTradeJson/securecode/" . CRON_KEY . "/market/" . $market;
        system("wget " . $url2 . " > /dev/null &");
    }

    private function upTradeLimit($paypassword = NULL, $market = NULL, $price, $num, $type)
    {

        if (!userid()) {
            $this->error(L('PLEASE_LOGIN'));
        }

        if (C('market')[$market]['begintrade']) {
            $begintrade = C('market')[$market]['begintrade'];
        } else {
            $begintrade = "00:00:00";
        }

        if (C('market')[$market]['endtrade']) {
            $endtrade = C('market')[$market]['endtrade'];
        } else {
            $endtrade = "23:59:59";
        }


        $trade_begin_time = strtotime(date("Y-m-d") . " " . $begintrade);
        $trade_end_time = strtotime(date("Y-m-d") . " " . $endtrade);
        $cur_time = time();

        if ($cur_time < $trade_begin_time || $cur_time > $trade_end_time) {
            $this->error('The current market is prohibited transaction,Trading hours daily' . $begintrade . '-' . $endtrade);
        }


        if (!check($price, 'double')) {
            $this->error(L('The transaction price is malformed'));
        }

        if (!check($num, 'double')) {
            $this->error(L('The number of transactions is malformed'));
        }

        if (($type != 1) && ($type != 2)) {
            $this->error(L('Transaction type format error'));
        }

        $user = M('User')->where(array('id' => userid()))->find();
        if (IF_TRADING_PASS == 1) {
            if ($user['tpwdsetting'] == 3) {
                //do nothing
            }

            if ($user['tpwdsetting'] == 2) {
                if (md5($paypassword) != $user['paypassword']) {
                    $this->error(L('Trading password is wrong!'));
                }
            }

            if ($user['tpwdsetting'] == 1) {
                if (!session(userid() . 'tpwdsetting')) {
                    if (md5($paypassword) != $user['paypassword']) {
                        $this->error(L('Trading password is wrong!'));
                    } else {
                        session(userid() . 'tpwdsetting', 1);
                    }
                }
            }
        }

        if (!C('market')[$market]) {
            $this->error(L('Error market'));
        } else {
            $xnb = explode('_', $market)[0];
            $rmb = explode('_', $market)[1];
        }

        // TODO: SEPARATE

        $price = round(floatval($price), 8);

        if (!$price) {
            $this->error(L('The transaction price error') . $price);
        }

        //$num = round($num, 8);

        if (!check($num, 'double')) {
            $this->error(L('INCORRECT_QTY'));
        }

        if ($type == 1) {
            $min_price = (C('market')[$market]['buy_min'] ? C('market')[$market]['buy_min'] : 1.0E-8);
            $max_price = (C('market')[$market]['buy_max'] ? C('market')[$market]['buy_max'] : 10000000);
        } else if ($type == 2) {
            $min_price = (C('market')[$market]['sell_min'] ? C('market')[$market]['sell_min'] : 1.0E-8);
            $max_price = (C('market')[$market]['sell_max'] ? C('market')[$market]['sell_max'] : 10000000);
        } else {
            $this->error(L('Transaction type error'));
        }

        if ($max_price < $price) {
            $this->error(L('Trading price exceeding the maximum limit!'));
        }

        if ($price < $min_price) {
            $this->error(L('Price is low,Keep it above') . ' ' . $min_price);
        }

        $hou_price = C('market')[$market]['hou_price'];

        if (!$hou_price) {
            $hou_price = C('market')[$market]['market_ico_price'];
        }

        if ($hou_price) {
            if (C('market')[$market]['zhang']) {
                $zhang_price = round(($hou_price / 100) * (100 + C('market')[$market]['zhang']), 8);

                if ($zhang_price < $price) {
                    $this->error(L('Transaction prices rose more than limit today!'));
                }
            }

            if (C('market')[$market]['die']) {
                $die_price = round(($hou_price / 100) * (100 - C('market')[$market]['die']), C('market')[$market]['round']);

                if ($price < $die_price) {
                    $this->error(L('Least sale bid suggested:') . $rmb . ' ' . $die_price);
                }
            }
        }

        $user_coin = M('UserCoin')->where(array('userid' => userid()))->find();

        if ($type == 1) {
            $trade_fee = discount(C('market')[$market]['fee_buy'],userid());

            if ($trade_fee) {
                $fee = round((($num * $price) / 100) * $trade_fee, 8);
                $mum = round((($num * $price) / 100) * (100 + $trade_fee), 8);
            } else {
                $fee = 0;
                $mum = round($num * $price, 8);
            }

            if ($user_coin[$rmb] < $mum) {
                $this->error(L('INSUFFICIENT') . C('coin')[$rmb]['title']);
            }
        } else if ($type == 2) {
            $trade_fee = discount(C('market')[$market]['fee_sell'],userid());

            if ($trade_fee) {
                $fee = round((($num * $price) / 100) * $trade_fee, 8);
                $mum = round((($num * $price) / 100) * (100 - $trade_fee), 8);
            } else {
                $fee = 0;
                $mum = round($num * $price, 8);
            }

            if ($user_coin[$xnb] < $num) {
                $this->error(L('INSUFFICIENT') . C('coin')[$xnb]['title']);
            }
        } else {
            $this->error(L('Transaction type error'));
        }

        if (C('coin')[$xnb]['fee_bili']) {
            if ($type == 2) {
                // TODO: SEPARATE
                $bili_user = round($user_coin[$xnb] + $user_coin[$xnb . 'd'], C('market')[$market]['round']);

                if ($bili_user) {
                    // TODO: SEPARATE
                    $bili_keyi = round(($bili_user / 100) * C('coin')[$xnb]['fee_bili'], C('market')[$market]['round']);

                    if ($bili_keyi) {
                        //$zheng_query="select id,price,sum(num-deal)as nums from codono_trade where userid=" . userid() . " and status=0 and type=2 and market like %" . $xnb . "%";

                        $zheng_query = "SELECT * FROM `codono_trade` WHERE `userid` = '" . userid() . "' and status=0 and type=2 and market like '%" . $xnb . "%'";

                        $bili_zheng = M()->query($zheng_query);

                        if (!$bili_zheng[0]['nums']) {
                            $bili_zheng[0]['nums'] = 0;
                        }

                        $bili_kegua = $bili_keyi - $bili_zheng[0]['nums'];

                        if ($bili_kegua < 0) {
                            $bili_kegua = 0;
                        }

                        if ($bili_kegua < $num) {
                            $this->error(L('Your total number of pending orders exceeds the system limit, you currently hold ') . C('coin')[$xnb]['title'] . $bili_user . L(' , has been pending') . $bili_zheng[0]['nums'] . L(' One can also pending') . $bili_kegua . 'More', '', 5);
                            // $this->error('query='.$zheng_query.'   Keyi='.$bili_keyi. 'and zheng='.json_encode($bili_zheng). 'and kegua='.$bili_kegua.' and < num='.$num);
                        }
                    } else {
                        $this->error(L('Trading volume can be wrong'));
                    }
                }
            }
        }

        if (C('coin')[$xnb]['fee_meitian']) {
            if ($type == 2) {
                $bili_user = round($user_coin[$xnb] + $user_coin[$xnb . 'd'], 8);

                if ($bili_user < 0) {
                    $this->error(L('Trading volume can be wrong'));
                }

                $kemai_bili = ($bili_user / 100) * C('coin')[$xnb]['fee_meitian'];

                if ($kemai_bili < 0) {
                    $this->error(L('You can then sell today') . C('coin')[$xnb]['title'] . 0 . 'More', '', 5);
                }

                $kaishi_time = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
                $jintian_sell = M('Trade')->where(array(
                    'userid' => userid(),
                    'addtime' => array('egt', $kaishi_time),
                    'type' => 2,
                    'status' => array('neq', 2),
                    'market' => array('like', '%' . $xnb . '%')
                ))->sum('num');

                if ($jintian_sell) {
                    $kemai = $kemai_bili - $jintian_sell;
                } else {
                    $kemai = $kemai_bili;
                }

                if ($kemai < $num) {
                    if ($kemai < 0) {
                        $kemai = 0;
                    }

                    $this->error(L('Your pending the total number exceeds the system limit, you can then sell today') . C('coin')[$xnb]['title'] . $kemai . 'More', '', 5);
                }
            }
        }

        if (C('market')[$market]['trade_min']) {
            if ($mum < C('market')[$market]['trade_min']) {
                $this->error(L('THE_MINIMUM_AMOUNT_FOR_EACH_PU') . ' ' . C('coin')[$rmb]['title'] . C('market')[$market]['trade_min']);
            }
        }

        if (C('market')[$market]['trade_max']) {
            if (C('market')[$market]['trade_max'] < $mum) {
                $this->error(L('MAX_ORDER_QTY') . C('market')[$market]['trade_max']);
            }
        }

        if (!$rmb) {
            $this->error('data error1');
        }

        if (!$xnb) {
            $this->error('data error2');
        }

        if (!$market) {
            $this->error('data error3');
        }

        if (!$price) {
            $this->error('data error4');
        }

        if (!$num) {
            $this->error('data error5');
        }

        if (!$mum) {
            $this->error('data error6');
        }

        if (!$type) {
            $this->error('data error7');
        }

        $mo = M();
        $mo->execute('set autocommit=0');
        $mo->execute('lock tables codono_trade write ,codono_user_coin write ,codono_finance write');
        $rs = array();
        $user_coin = $mo->table('codono_user_coin')->where(array('userid' => userid()))->find();

        if ($type == 1) {
            if ($user_coin[$rmb] < $mum) {
                $this->error(L('INSUFFICIENT') . C('coin')[$rmb]['title']);
            }

            $finance = $mo->table('codono_finance')->where(array('userid' => userid()))->order('id desc')->find();
            $finance_num_user_coin = $mo->table('codono_user_coin')->where(array('userid' => userid()))->find();
            $rs[] = $mo->table('codono_user_coin')->where(array('userid' => userid()))->setDec($rmb, $mum);
            $rs[] = $mo->table('codono_user_coin')->where(array('userid' => userid()))->setInc($rmb . 'd', $mum);
            $rs[] = $finance_nameid = $mo->table('codono_trade')->add(array('userid' => userid(), 'market' => $market, 'price' => $price, 'num' => $num, 'mum' => $mum, 'fee' => $fee, 'type' => 1, 'addtime' => time(), 'status' => 0));


            //20170531 modify only statistics USD transaction Money TYPE
            if ($rmb == "usd") {
                $finance_mum_user_coin = $mo->table('codono_user_coin')->where(array('userid' => userid()))->find();
                $finance_hash = md5(userid() . $finance_num_user_coin['usd'] . $finance_num_user_coin['usdd'] . $mum . $finance_mum_user_coin['usd'] . $finance_mum_user_coin['usdd'] . CODONOLIC . 'auth.codono.com');
                $finance_num = $finance_num_user_coin['usd'] + $finance_num_user_coin['usdd'];

                if ($finance['mum'] < $finance_num) {
                    $finance_status = (1 < ($finance_num - $finance['mum']) ? 0 : 1);
                } else {
                    $finance_status = (1 < ($finance['mum'] - $finance_num) ? 0 : 1);
                }

                $rs[] = $mo->table('codono_finance')->add(array('userid' => userid(), 'coinname' => 'usd', 'num_a' => $finance_num_user_coin['usd'], 'num_b' => $finance_num_user_coin['usdd'], 'num' => $finance_num_user_coin['usd'] + $finance_num_user_coin['usdd'], 'fee' => $mum, 'type' => 2, 'name' => 'trade', 'nameid' => $finance_nameid, 'remark' => 'Trading Center-Buying commission-market' . $market, 'mum_a' => $finance_mum_user_coin['usd'], 'mum_b' => $finance_mum_user_coin['usdd'], 'mum' => $finance_mum_user_coin['usd'] + $finance_mum_user_coin['usdd'], 'move' => $finance_hash, 'addtime' => time(), 'status' => $finance_status));
            }


        } else if ($type == 2) {
            if ($user_coin[$xnb] < $num) {
                $this->error(C('coin')[$xnb]['title'] . 'Insufficient balance2!');
            }

            $rs[] = $mo->table('codono_user_coin')->where(array('userid' => userid()))->setDec($xnb, $num);
            $rs[] = $mo->table('codono_user_coin')->where(array('userid' => userid()))->setInc($xnb . 'd', $num);
            $rs[] = $mo->table('codono_trade')->add(array('userid' => userid(), 'market' => $market, 'price' => $price, 'num' => $num, 'mum' => $mum, 'fee' => $fee, 'type' => 2, 'addtime' => time(), 'status' => 0));
        } else {
            $mo->execute('rollback');
            $mo->execute('unlock tables');
            $this->error(L('Transaction type error'));
        }
        if (check_arr($rs)) {
            $mo->execute('commit');
            $mo->execute('unlock tables');
            S('getDepth', null);
            S('getActiveDepth', null);
            S('getActiveDepth'.$market, null);
            S('getDepthNew', null);

            exec($this->matchingTrade($market));           //Matches the orders
            //exec($this->callLiquidityServer($market,$type,$price,$num)); //liquidity Server
            exec($this->callSetTradeLiq($market, $type, $price, $num));
            $this->success(L('Trading success!'));
        } else {
            $mo->execute('rollback');
            $mo->execute('unlock tables');
            $this->error(L('transaction failed!'));

        }
    }


    public function reject($id)
    {
        if (!userid()) {
            $this->error(L('PLEASE_LOGIN'));
        }

        if (!check($id, 'd')) {
            $this->error(L('Please select Undo commission!'));
        }
        $where = array('userid' => userid(), 'id' => $id, 'status' => 0);
        $trade = M('Trade')->where($where)->find();

        if ($trade['userid'] != userid()) {
            $this->error(L('No such trade under your orders!'));
        }

        if (!$trade) {
            $this->error(L('No such trade for you !'));
        }
        exec($this->stopcheck($trade['market'], $trade['type'])); //StopLoss execution

        $this->show(D('Trade')->reject($id));
    }

    public function show($rs = array())
    {
        if ($rs[0]) {
            $this->success($rs[1]);
        } else {
            $this->error($rs[1]);
        }
    }

    public function install()
    {
    }

    public function config()
    {
        $marketname = trim(I('get.symbol', 0, 'htmlspecialchars'));
        if (!$marketname) {
            $market = D('Market')->where(["status" => 1])->select();
        } else {
            $market = D('Market')->where(["name" => $marketname])->find();
        }
        $markets = D('Market')->where(["status" => 1])->select();
        $data['exchanges'] = ['value' => "Codono", 'name' => "Codono", 'desc' => "Codono"];
        $data['supported_resolutions'] = ["1", "3", "5", "15", "30", "60", "120", "180", "240", "360", "720", "1D", "3D", "1W", "2W", "1M"];
        $data['supports_group_request'] = false;
        $data['supports_marks'] = false;
        $data['supports_timescale_marks'] = false;
        $data['supports_search'] = true;
        $data['has_daily'] = true;
        $data['supports_time'] = true;
        $data['max_bars'] = 10080;
        $data['type'] = 'bitcoin';
        foreach ($markets as $market) {
            $data['symbols_types'][] = ['name' => $market['name'], 'value' => $market['name']];
        }

        exit(json_encode($data));
    }

    public function symbols()
    {
        $marketname = trim(I('get.symbol', 0, 'htmlspecialchars'));
        if (!$marketname) {
            $market = D('Market')->where(["status" => 1])->find();
        } else {
            $market = D('Market')->where(["name" => $marketname])->find();
        }
        if (C('MARKET')[$marketname]['round']) {
            $round = 6;
        } else {
            $round = (int)C('MARKET')[$marketname]['round'];
        }
        $pricescale = pow(10, $round);

        $data = ['name' => $market['name'], 'full_name' => $market['name'], 'symbol' => $market['name'], 'exchange' => "", 'exchange-traded' => "", 'exchange-listed' => "", 'timezone' => "UTC", 'pricescale' => 100000000, 'minmov' => 1, 'minmove2' => 0, 'has_intraday' => true, 'intraday_multipliers' => ["1", "5", "60", "1440"], 'has_daily' => true, 'has_weekly_and_monthly' => false, 'has_empty_bars' => false, 'force_session_rebuild' => false, 'has_no_volume' => false, 'has_fractional_volume' => false, 'ticker' => $market['name'], 'description' => "", 'session' => "24x7", 'data_status' => "streaming", 'supported_resolutions' => ["1", "3", "5", "15", "30", "60", "120", "180", "240", "360", "720", "1D", "3D", "1W", "2W", "1M"], 'type' => "bitcoin"];
        exit(json_encode($data));
    }

    public function time()
    {
        echo time();
    }

    public function xhistory()
    {
        $market = trim(I('get.market', 0, 'htmlspecialchars'));
        if (strpos($market, "_") == false)
            $market = 0;

        $thequery = "select DATE_FORMAT(FROM_UNIXTIME(`addtime`), '%Y-%m-%d') as date,(SELECT c2.price FROM codono_trade c2 WHERE c2.id = MIN(codono_trade.id)) AS open,MAX(codono_trade.price) AS high,MIN(codono_trade.price) AS low,(SELECT c2.price FROM codono_trade c2 WHERE c2.id = MAX(codono_trade.id)) AS close FROM codono_trade codono_trade WHERE codono_trade.market='" . $market . "' GROUP BY DATE_FORMAT(FROM_UNIXTIME(`addtime`), '%Y %m %d') ORDER BY codono_trade.addtime ASC LIMIT 300";
        var_dump($thequery);
        $trade_json = M('TradeJson')->query($thequery);

        $data = [];
        krsort($trade_json);
        if ($trade_json) {
            foreach ($trade_json as $k => $v) {
                $tmp = json_decode($v['data'], true);
                $data['c'][] = $tmp[5];
                $data['h'][] = $tmp[3];
                $data['l'][] = $tmp[4];
                $data['o'][] = $tmp[2];
                $data['v'][] = $tmp[1];
                $data['t'][] = $tmp[0];
            }
            $data['s'] = 'ok';
        }
        exit(json_encode($data, JSON_NUMERIC_CHECK));
    }

    public function history()
    {
        $data = (APP_DEBUG ? null : S('historyData'));
        $type = I('get.resolution');
        $str_time = I('get.from');
        $end_time = I('get.to');
        $market = I('get.symbol');
                $trade_json = M("TradeJson")->where(["market" => $market, "type" => $type, "data#!=" => "","addtime"=> array('egt', $str_time),"endtime"=> array('elt', $end_time)])->order("addtime desc")->select();
        $data = [];
        krsort($trade_json);
        if ($trade_json) {
            foreach ($trade_json as $k => $v) {
                $tmp = json_decode($v['data'], true);
                if ($tmp[5] == NULL) continue;
                $data['c'][] = $tmp[5];
                $data['h'][] = $tmp[3];
                $data['l'][] = $tmp[4];
                $data['o'][] = $tmp[2];
                $data['v'][] = $tmp[1];
                $data['t'][] = $tmp[0];
            }
            $data['s'] = 'ok';
            S('historyData', $data);
        }
        exit(json_encode($data, JSON_NUMERIC_CHECK));
    }

    //				'actions'=>array('history', 'symbols', 'symbol_info','config', 'time', 'marks', 'timescale_marks', 'getLastGraph'),

    /* Stop Trade Functions */

    public function okhistory()
    {
        $type = I('get.resolution', 1, 'trim');
        $str_time = I('get.from');
        $end_time = I('get.to');
        $market = trim(I('get.symbol', 0, 'htmlspecialchars'));

        if (strpos($market, "_") == false) {
            $market = 0;
        }

        echo $this->Enginehistory($market, $type, $str_time, $end_time);
    }

    private function Enginehistory($market, $type, $str_time, $end_time)
    {
        //$where['addtime']=array('between',array($str_time, $end_time));
        $where['market'] = $market;
        $where['type'] = $type;
        $trade_json = M('TradeJson')->where($where)->order("addtime asc")->select();

        $data = [];

        krsort($trade_json);
        if ($trade_json) {
            foreach ($trade_json as $k => $v) {

                $tmp = json_decode($v['data'], true);
                if ($tmp != NULL) {
                    $data['c'][] = $tmp[5];
                    $data['h'][] = $tmp[3];
                    $data['l'][] = $tmp[4];
                    $data['o'][] = $tmp[2];
                    $data['v'][] = $tmp[1];
                    $data['t'][] = $tmp[0];
                }
            }
            if (count($data)) {
                $data['s'] = "ok";
            } else {
                $data['s'] = "no_data";
            }
        } else {
            if (ENABLE_BINANCE == 1) {
                echo $this->Binancehistory($market, $type, $str_time, $end_time);
                exit;
            }
        }
        return (json_encode($data, JSON_PRESERVE_ZERO_FRACTION));
    }

    /*Do not use Testing function make public before testing */

    public function Binancehistory($market, $type, $str_time, $end_time)
    {
        $market = $this->make_compatible_marker($market); //USD to USDT , _ will be removed too
        $binance_node = BINANCE_NODE; //A Node service is required to run to get Binance Prices and Configured in other_config.php
        $binance_history_url = $binance_node . 'history?symbol=' . $market . '&resolution=' . $type . '&from=' . $str_time . '&to=' . $end_time;
        return file_get_contents($binance_history_url);
        exit;
    }

    /*
    check if any trades to be done at this price
    market example eth_usd
    price is at which user is buying or selling
    type is either buy or sell
    In there scroll through the stop table , and find eligible trades, execute them using executeStop.

    */

    private function make_compatible_marker($market)
    {
        //TODO: make it compatible with any symbols
        $new_market = str_replace('_', '', $market);
        if (!substr($market, 'usdt')) {
            $new_market = str_replace('usd', 'usdt', $new_market);
        }
        return strtoupper($new_market);
    }

    /*Mark that stop order as executed to it never executes again*/

    public function statics($market)
    {
        if (!$market) {
            $marketall = D('Market')->where(["status" => 1])->find();
        } else {
            $marketall = D('Market')->where(["name" => $market])->find();
        }
        $this->assign('market', $marketall);
        $this->display();
    }

    //private function executeStop($market = NULL, $price, $num, $type,$userid)

    public function librarys($market = NULL, $ajax = 'json')
    {
        $data = (APP_DEBUG ? null : S('getJsonTop' . $market));

        if (!$data) {
            if ($market) {
                $xnb = explode('_', $market)[0];
                $rmb = explode('_', $market)[1];

                foreach (C('market') as $k => $v) {
                    $v['xnb'] = explode('_', $v['name'])[0];
                    $v['rmb'] = explode('_', $v['name'])[1];
                    $data['list'][$k]['name'] = $v['name'];
                    $data['list'][$k]['img'] = $v['xnbimg'];
                    $data['list'][$k]['title'] = $v['title'];
                    $data['list'][$k]['new_price'] = $v['new_price'];
                }

                $data['info']['img'] = C('market')[$market]['xnbimg'];
                $data['info']['title'] = C('market')[$market]['title'];
                $data['info']['new_price'] = C('market')[$market]['new_price'];

                if (C('market')[$market]['max_price']) {
                    $data['info']['max_price'] = C('market')[$market]['max_price'];
                } else {
                    $codono_tempprice = round((C('market')[$market]['market_ico_price'] / 100) * (100 + C('market')[$market]['zhang']), C('market')[$market]['round']);
                    $data['info']['max_price'] = $codono_tempprice;
                }

                if (C('market')[$market]['min_price']) {
                    $data['info']['min_price'] = C('market')[$market]['min_price'];
                } else {
                    $codono_tempprice = round((C('market')[$market]['market_ico_price'] / 100) * (100 - C('market')[$market]['die']), C('market')[$market]['round']);
                    $data['info']['min_price'] = $codono_tempprice;
                }


                $data['info']['buy_price'] = C('market')[$market]['buy_price'];
                $data['info']['sell_price'] = C('market')[$market]['sell_price'];
                $data['info']['volume'] = C('market')[$market]['volume'];
                $data['info']['change'] = C('market')[$market]['change'];
                S('getJsonTop' . $market, $data);
            }
        }

        if ($ajax) {
            exit(json_encode($data));
        } else {
            return $data;
        }
    }

    public function saveStop($paypassword = NULL, $market = NULL, $price, $num, $type, $stop)
    {

        if (!userid()) {
            $this->error(L('PLEASE_LOGIN'));
        }

        if (C('market')[$market]['begintrade']) {
            $begintrade = C('market')[$market]['begintrade'];
        } else {
            $begintrade = "00:00:00";
        }

        if (C('market')[$market]['endtrade']) {
            $endtrade = C('market')[$market]['endtrade'];
        } else {
            $endtrade = "23:59:59";
        }


        $trade_begin_time = strtotime(date("Y-m-d") . " " . $begintrade);
        $trade_end_time = strtotime(date("Y-m-d") . " " . $endtrade);
        $cur_time = time();

        if ($cur_time < $trade_begin_time || $cur_time > $trade_end_time) {
            $this->error('The current market is prohibited transaction,Trading hours daily' . $begintrade . '-' . $endtrade);
        }


        if (!check($price, 'double')) {
            $this->error(L('Limit Price is incorrect'));
        }
        if (!check($stop, 'double')) {
            $this->error(L('Limit Price is incorrect'));
        }

        if (!check($num, 'double')) {
            $this->error(L('Quantity is incorrect'));
        }

        if (($type != 1) && ($type != 2)) {
            $this->error(L('Transaction type format error'));
        }
        $current = $this->getPriceForStop($market, $type); //best price
        if ($stop < $current) {
            $compare = 'gt';
        } else {
            $compare = 'lt';
        }
        $user = M('User')->where(array('id' => userid()))->find();
        if (IF_TRADING_PASS == 1) {
            if ($user['tpwdsetting'] == 3) {
            }

            if ($user['tpwdsetting'] == 2) {
                if (md5($paypassword) != $user['paypassword']) {
                    $this->error(L('Trading password is wrong!'));
                }
            }

            if ($user['tpwdsetting'] == 1) {
                if (!session(userid() . 'tpwdsetting')) {
                    if (md5($paypassword) != $user['paypassword']) {
                        $this->error(L('Trading password is wrong!'));
                    } else {
                        session(userid() . 'tpwdsetting', 1);
                    }
                }
            }
        }


        if (!C('market')[$market]) {
            $this->error(L('Error market'));
        } else {
            $xnb = explode('_', $market)[0];
            $rmb = explode('_', $market)[1];
        }

        // TODO: SEPARATE

        $price = round(floatval($price), 8);
        $stop = round(floatval($stop), 8);
        if (!$price) {
            $this->error(L('Limit price error') . $price);
        }
        if (!$stop) {
            $this->error(L('Stop price error') . $price);
        }


        //$num = round($num, 8);

        if (!check($num, 'double')) {
            $this->error(L('INCORRECT_QTY'));
        }

        if ($type == 1) {
            $min_price = (C('market')[$market]['buy_min'] ? C('market')[$market]['buy_min'] : 1.0E-8);
            $max_price = (C('market')[$market]['buy_max'] ? C('market')[$market]['buy_max'] : 10000000);
        } else if ($type == 2) {
            $min_price = (C('market')[$market]['sell_min'] ? C('market')[$market]['sell_min'] : 1.0E-8);
            $max_price = (C('market')[$market]['sell_max'] ? C('market')[$market]['sell_max'] : 10000000);
        } else {
            $this->error(L('Transaction type error'));
        }

        if ($max_price < $price) {
            $this->error(L('Trading price exceeding the maximum limit!'));
        }

        if ($price < $min_price) {
            $this->error(L('Price is low,Keep it above') . ' ' . $min_price);
        }

        $hou_price = C('market')[$market]['hou_price'];

        if (!$hou_price) {
            $hou_price = C('market')[$market]['market_ico_price'];
        }

        if ($hou_price) {
            if (C('market')[$market]['zhang']) {
                $zhang_price = round(($hou_price / 100) * (100 + C('market')[$market]['zhang']), 8);

                if ($zhang_price < $price) {
                    $this->error(L('Transaction prices rose more than limit today!'));
                }
            }

            if (C('market')[$market]['die']) {
                $die_price = round(($hou_price / 100) * (100 - C('market')[$market]['die']), C('market')[$market]['round']);

                if ($price < $die_price) {
                    $this->error(L('Least sale bid suggested::') . $rmb . ' ' . $die_price);
                }
            }
        }

        $user_coin = M('UserCoin')->where(array('userid' => userid()))->find();

        if ($type == 1) {
            $trade_fee = C('market')[$market]['fee_buy'];

            if ($trade_fee) {
                $fee = round((($num * $price) / 100) * $trade_fee, 8);
                $mum = round((($num * $price) / 100) * (100 + $trade_fee), 8);
            } else {
                $fee = 0;
                $mum = round($num * $price, 8);
            }

            if ($user_coin[$rmb] < $mum) {
                $this->error(L('INSUFFICIENT') . C('coin')[$rmb]['title']);
            }
        } else if ($type == 2) {
            $trade_fee = C('market')[$market]['fee_sell'];

            if ($trade_fee) {
                $fee = round((($num * $price) / 100) * $trade_fee, 8);
                $mum = round((($num * $price) / 100) * (100 - $trade_fee), 8);
            } else {
                $fee = 0;
                $mum = round($num * $price, 8);
            }

            if ($user_coin[$xnb] < $num) {
                $this->error(L('INSUFFICIENT') . C('coin')[$xnb]['title']);
            }
        } else {
            $this->error(L('Transaction type error'));
        }

        if (C('coin')[$xnb]['fee_bili']) {
            if ($type == 2) {
                // TODO: SEPARATE
                $bili_user = round($user_coin[$xnb] + $user_coin[$xnb . 'd'], C('market')[$market]['round']);

                if ($bili_user) {
                    // TODO: SEPARATE
                    $bili_keyi = round(($bili_user / 100) * C('coin')[$xnb]['fee_bili'], C('market')[$market]['round']);

                    if ($bili_keyi) {
                        // TODO:See if we can add trades from codono_stop table too
                        $zheng_query = "SELECT * FROM `codono_trade` WHERE `userid` = '" . userid() . "' and status=0 and type=2 and market like '%" . $xnb . "%'";

                        $bili_zheng = M()->query($zheng_query);

                        if (!$bili_zheng[0]['nums']) {
                            $bili_zheng[0]['nums'] = 0;
                        }

                        $bili_kegua = $bili_keyi - $bili_zheng[0]['nums'];

                        if ($bili_kegua < 0) {
                            $bili_kegua = 0;
                        }

                        if ($bili_kegua < $num) {
                            $this->error(L('Your total number of pending orders exceeds the system limit, you currently hold ') . C('coin')[$xnb]['title'] . $bili_user . L(' , has been pending') . $bili_zheng[0]['nums'] . L(' One can also pending') . $bili_kegua . 'More', '', 5);
                            // $this->error('query='.$zheng_query.'   Keyi='.$bili_keyi. 'and zheng='.json_encode($bili_zheng). 'and kegua='.$bili_kegua.' and < num='.$num);
                        }
                    } else {
                        $this->error(L('Trading volume can be wrong'));
                    }
                }
            }
        }

        if (C('coin')[$xnb]['fee_meitian']) {
            if ($type == 2) {
                $bili_user = round($user_coin[$xnb] + $user_coin[$xnb . 'd'], 8);

                if ($bili_user < 0) {
                    $this->error(L('Trading volume can be wrong'));
                }

                $kemai_bili = ($bili_user / 100) * C('coin')[$xnb]['fee_meitian'];

                if ($kemai_bili < 0) {
                    $this->error(L('You can then sell today') . C('coin')[$xnb]['title'] . 0 . 'More', '', 5);
                }

                $kaishi_time = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
                $jintian_sell = M('Trade')->where(array(
                    'userid' => userid(),
                    'addtime' => array('egt', $kaishi_time),
                    'type' => 2,
                    'status' => array('neq', 2),
                    'market' => array('like', '%' . $xnb . '%')
                ))->sum('num');

                if ($jintian_sell) {
                    $kemai = $kemai_bili - $jintian_sell;
                } else {
                    $kemai = $kemai_bili;
                }

                if ($kemai < $num) {
                    if ($kemai < 0) {
                        $kemai = 0;
                    }

                    $this->error(L('Your pending the total number exceeds the system limit, you can then sell today') . C('coin')[$xnb]['title'] . $kemai . 'More', '', 5);
                }
            }
        }

        if (C('market')[$market]['trade_min']) {
            if ($mum < C('market')[$market]['trade_min']) {
                $this->error(L('THE_MINIMUM_AMOUNT_FOR_EACH_PU') . ' ' . C('coin')[$rmb]['title'] . C('market')[$market]['trade_min']);
            }
        }

        if (C('market')[$market]['trade_max']) {
            if (C('market')[$market]['trade_max'] < $mum) {
                $this->error(L('MAX_ORDER_QTY') . C('market')[$market]['trade_max']);
            }
        }

        if (!$rmb) {
            $this->error('data error1');
        }

        if (!$xnb) {
            $this->error('data error2');
        }

        if (!$market) {
            $this->error('data error3');
        }

        if (!$price) {
            $this->error('data error4');
        }

        if (!$num) {
            $this->error('data error5');
        }

        if (!$mum) {
            $this->error('data error6');
        }

        if (!$type) {
            $this->error('data error7');
        }

        $mo = M();
        $mo->execute('set autocommit=0');
        $mo->execute('lock tables codono_stop write ,codono_user_coin write ,codono_finance write');
        $rs = array();
        $user_coin = $mo->table('codono_user_coin')->where(array('userid' => userid()))->find();

        if ($type == 1) {
            if ($user_coin[$rmb] < $mum) {
                $this->error(L('INSUFFICIENT') . C('coin')[$rmb]['title']);
            }

            $finance = $mo->table('codono_finance')->where(array('userid' => userid()))->order('id desc')->find();
            $finance_num_user_coin = $mo->table('codono_user_coin')->where(array('userid' => userid()))->find();
            $rs[] = $mo->table('codono_user_coin')->where(array('userid' => userid()))->setDec($rmb, $mum);
            $rs[] = $mo->table('codono_user_coin')->where(array('userid' => userid()))->setInc($rmb . 'd', $mum);
            $rs[] = $finance_nameid = $mo->table('codono_stop')->add(array('userid' => userid(), 'compare' => $compare, 'market' => $market, 'price' => $price, 'stop' => $stop, 'num' => $num, 'mum' => $mum, 'fee' => $fee, 'type' => 1, 'addtime' => time(), 'status' => 0));


            //20170531 modify only statistics USD transaction Money TYPE
            if ($rmb == "usd") {
                $finance_mum_user_coin = $mo->table('codono_user_coin')->where(array('userid' => userid()))->find();
                $finance_hash = md5(userid() . $finance_num_user_coin['usd'] . $finance_num_user_coin['usdd'] . $mum . $finance_mum_user_coin['usd'] . $finance_mum_user_coin['usdd'] . CODONOLIC . 'auth.codono.com');
                $finance_num = $finance_num_user_coin['usd'] + $finance_num_user_coin['usdd'];

                if ($finance['mum'] < $finance_num) {
                    $finance_status = (1 < ($finance_num - $finance['mum']) ? 0 : 1);
                } else {
                    $finance_status = (1 < ($finance['mum'] - $finance_num) ? 0 : 1);
                }

                $rs[] = $mo->table('codono_finance')->add(array('userid' => userid(), 'coinname' => 'usd', 'num_a' => $finance_num_user_coin['usd'], 'num_b' => $finance_num_user_coin['usdd'], 'num' => $finance_num_user_coin['usd'] + $finance_num_user_coin['usdd'], 'fee' => $mum, 'type' => 2, 'name' => 'trade', 'nameid' => $finance_nameid, 'remark' => 'Trading Center-Buying commission-market' . $market, 'mum_a' => $finance_mum_user_coin['usd'], 'mum_b' => $finance_mum_user_coin['usdd'], 'mum' => $finance_mum_user_coin['usd'] + $finance_mum_user_coin['usdd'], 'move' => $finance_hash, 'addtime' => time(), 'status' => $finance_status));
            }


        } else if ($type == 2) {
            if ($user_coin[$xnb] < $num) {
                $this->error(C('coin')[$xnb]['title'] . ' Insufficient balance for sell!');
            }

            $rs[] = $mo->table('codono_user_coin')->where(array('userid' => userid()))->setDec($xnb, $num);
            $rs[] = $mo->table('codono_user_coin')->where(array('userid' => userid()))->setInc($xnb . 'd', $num);
            $rs[] = $mo->table('codono_stop')->add(array('userid' => userid(), 'compare' => $compare, 'market' => $market, 'price' => $price, 'stop' => $stop, 'num' => $num, 'mum' => $mum, 'fee' => $fee, 'type' => 2, 'addtime' => time(), 'status' => 0));
        } else {
            $mo->execute('rollback');
            $mo->execute('unlock tables');
            $this->error(L('Transaction type error'));
        }
        if (check_arr($rs)) {
            $mo->execute('commit');
            $mo->execute('unlock tables');
            S('getDepth', null);
            S('getActiveDepth' . $market, null);
            S('getActiveDepth', null);
            S('getDepthNew', null);

            $this->matchingTrade($market);

            exec($this->stopcheck($market, $type)); //StopLoss execution
//			exec($this->callLiquidityServer($market,$type,$price,$num)); //liquidity Server
            exec($this->callSetTradeLiq($market, $type, $price, $num));
            $this->success(L('Trading success!'));
        } else {
            $mo->execute('rollback');
            $mo->execute('unlock tables');
            $this->error(L('transaction failed!'));

        }


    }

    public function stopreject()
    {
        $id = (int)I('id');
        if (!userid()) {
            $this->error(L('PLEASE_LOGIN'));
        }

        if (!check($id, 'd')) {
            $this->error(L('Please select Undo commission!'));
        }
        $where = array('userid' => userid(), 'id' => $id, 'status' => 0);
        $trade = M('Stop')->where($where)->find();


        if (!$trade) {
            $this->error(L('Undo delegate parameter error!'));
        }

        if ($trade['userid'] != userid()) {
            $this->error(L('Parameters illegal!'));
        }

        $this->show(D('Trade')->stopreject($id));
    }

    /*
Type=1 Buy
Type=2 Sell

Returns
If type==buy then Price [Higest Buy Price]
If type==sale then Price =[Lowest Sale Price]

*/

    public function teststop()
    {
        echo $this->stopcheck('ltc_usd', 2);
        //print_r($this->getPriceForStop('btc_usd',1));

    }

    /*
    Type=1 Buy
    Type=2 Sell

    Returns
    If type==buy

    1.Price [Highest Sale Price]
    2.Qty Total num of sale orders
    If type==sale

    1.Price [Lowest Buy Price]
    2.Qty Total num of buy orders
    */

    private function callLiquidityServer($market, $type, $price, $amount)
    {

        $Match_market = strtoupper($market);

        if (array_key_exists($Match_market, LIQUIDITY_ARRAY)) {
            $liq_market = LIQUIDITY_ARRAY["$Match_market"]; //binance market pair needs to be defined in other_config
        } else {
            return 0;
        }

        if ($type == 1) {
            $type = 'buy';
        } else if ($type == 2) {
            $type = 'sell';
        } else {
            return 0;
        }
        $price = format_num($price, 6);

        if (!check($price, 'double')) {
            return 0;
        }

        if (!check($amount, 'double')) {
            return 0;
        }

        $liq_server_url = LIQUIDITY_CONF['URL'] . "public/api/place_order/" . $liq_market;
        $post_vars = "user_id=" . LIQUIDITY_CONF['USERID'] . "&token=" . LIQUIDITY_CONF['TOKEN'] . "&type=$type&price=$price&amount=$amount";

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
            return "cURL Error #:" . $err;
        } else {
            return $response;
        }
    }

    private function markStopAsExecuted($stopid)
    {
        $data['status'] = 1;
        //$data['endtime']=time();
        return $Statusupdate = M('Stop')->where(array('id' => $stopid))->save($data);
    }

}