<?php

namespace Api\Controller;

class ExchangeController extends CommonController
{
    public function __construct()
    {
        parent::__construct();
    }
	
	//Last trades happened recently
     public function lasttrades($market = NULL,$limit=100, $ajax = 'json')
    {
		 if (!check($limit, 'integer') || $limit > 200) {
			 $this->error(L('limit should be 1-200'));
		 }
        $data = (APP_DEBUG ? null : S('getTradelog' . $market));
        if (!$data) {
            $tradeLog = M('TradeLog')->where(array('status' => 1, 'market' => $market))->order('id desc')->limit(50)->select();
			
            if ($tradeLog) {
				$data['market']=$market;
                foreach ($tradeLog as $k => $v) {
                    $data['tradelog'][$k]['addtime'] = date('d-m H:i:s', $v['addtime']);
                    $data['tradelog'][$k]['type'] = $v['type'];
                    $data['tradelog'][$k]['price'] = format_num($v['price'] * 1,8);
                    $data['tradelog'][$k]['num'] = format_num($v['num'], 8);
                    $data['tradelog'][$k]['mum'] = format_num($v['mum'], 8);
                }
                S('getTradelog' . $market, $data);
            }
        }

        $this->ajaxShow($data);		
    }
	
	public function activeorders($market = NULL, $trade_moshi = 1)
    {
        if (!C('market')[$market]) {
            return null;
        }
		$round_off=C('market')[$market]['round'];
		


        $data_getDepth = (APP_DEBUG ? null : S('activeOrders'));
		$limt = 5;	
        if (!$data_getDepth[$market][$trade_moshi]) {
            
            $trade_moshi = intval($trade_moshi);


            $mo = M();
            if ($trade_moshi == 1) {
                $buy = $mo->query('select id,price,sum(num-deal)as nums from codono_trade where status=0 and type=1 and market =\'' . $market . '\' group by price order by price desc limit ' . $limt . ';');
                $sell = array_reverse($mo->query('select id,price,sum(num-deal)as nums from codono_trade where status=0 and type=2 and market =\'' . $market . '\' group by price order by price desc limit ' . $limt . ';'));
            }

          
            if (isset($buy)) {
                foreach ($buy as $k => $v) {
                    
					$data['depth']['buy'][$k]= array('price'=>format_num($v['price'] * 1,$round_off),'nums'=> format_num($v['nums'] * 1,$round_off));  //When you need point $round_off precision
                }
            } else {
                $data['depth']['buy'] = '';
            }

            if (isset($sell)) {
                foreach ($sell as $k => $v) {
                    $data['depth']['sell'][$k] = array('price'=>format_num($v['price'] * 1,$round_off), 'nums'=>format_num($v['nums'] * 1,$round_off)); //When you need point $round_off precision
                }
            } else {
                $data['depth']['sell'] = '';
            }

            $data_getDepth[$market][$trade_moshi] = $data;
            S('activeOrders', $data_getDepth);
        } else {
            $data = $data_getDepth[$market][$trade_moshi];
        }

        $this->ajaxShow($data);		
    }
	  public function kdata()
    {
		//market and time [from timearr]
        // TODO: SEPARATE
        $input = I('get.');
        $market = (is_array(C('market')[$input['market']]) ? trim($input['market']) : C('market_mr'));

        $timearr = array(1, 3, 5, 10, 15, 30, 60, 120, 240, 360, 720, 1440, 10080);


        if (in_array($input['time'], $timearr)) {
            $time = $input['time'];
        } else {
            $time = 5;
        }

        $timeaa = (APP_DEBUG ? null : S('kdatatime' . $market . $time));

        if (($timeaa + 60) < time()) {
            S('kdata' . $market . $time, null);
            S('kdatatime' . $market . $time, time());
        }

        $tradeJson = (APP_DEBUG ? null : S('kdata' . $market . $time));

        if (!$tradeJson) {
            $tradeJson = M('TradeJson')->where(array(
                'market' => $market,
                'type' => $time,
                'data' => array('neq', '')
            ))->order('id desc')->limit(100)->select();
            S('ChartgetMarketOrdinaryJson' . $market . $time, $tradeJson);
        }

        krsort($tradeJson);

        $json_data = array();
        foreach ($tradeJson as $k => $v) {
            $json_data[] = json_decode($v['data'], true);
        }
		$this->ajaxShow($json_data);		
        
    }
	private function Binancehistory($market,$type,$str_time,$end_time){
		$market=$this->make_compatible_marker($market); //USD to USDT , _ will be removed too
		$binance_node= BINANCE_NODE; //A Node service is required to run to get Binance Prices and Configured in other_config.php
		$binance_history_url=$binance_node.'history?symbol='.$market.'&resolution='.$type.'&from='.$str_time.'&to='.$end_time;
		return file_get_contents($binance_history_url);
	}
	private function make_compatible_marker($market){
		//TODO: make it compatible with any symbols
		$new_market=str_replace('_','',$market);
		if(!substr($market,'usdt'))
		{
			$new_market=str_replace('usd','usdt',$new_market);
		}
		return strtoupper($new_market);
	}
	public function history(){
		$type = I('get.resolution',1,'trim');
		$str_time = I('get.from');
		$end_time = I('get.to');
		$market = trim(I('get.symbol',0,htmlspecialchars));
		if(strpos($market,"_")==false)
		{$market=0;}
		$data= $this->Enginehistory($market,$type,$str_time,$end_time);
		
		$resp['status']=1;
		$resp['data']=$data;
		echo (json_encode($data, JSON_PRESERVE_ZERO_FRACTION));
	}
	private function Enginehistory($market,$type,$str_time,$end_time)
	{
		//$where['addtime']=array('between',array($str_time, $end_time));
		$where['market']=$market;
		$where['type']=$type;
		$trade_json= M('TradeJson')->where($where)->order("addtime asc")->select();
		$data = [];
		krsort($trade_json);
		if ($trade_json) {
			foreach ($trade_json as $k => $v) {

			$tmp = json_decode($v['data'], true);
			if($tmp !=NULL){			
			$data['c'][] = $tmp[5];
				$data['h'][] = $tmp[3];
				$data['l'][] = $tmp[4];
				$data['o'][] = $tmp[2];
				$data['v'][] = $tmp[1];
				$data['t'][] = $tmp[0];
				}
			}
			 if(count($data)) {
            $data['s'] = "ok";
			}else
			{
            $data['s'] = "no_data";  
			}
		}
		else{
			if(ENABLE_BINANCE==1)
			{echo $this->Binancehistory($market,$type,$str_time,$end_time);	exit;}
			}
			return $data;
		//return (json_encode($data, JSON_PRESERVE_ZERO_FRACTION));
	}
	public function reject($id)
    {
		$uid=$this->userid();

        if (!check($id, 'd')) {
            $this->error(L('Something is wrong !!'));
        }
		$where=array('userid'=>userid(),'id'=>$id,'status'=>0);
        $trade = M('Trade')->where($where)->find();
		

        if (!$trade) {
            $this->error(L('No such order!'));
        }


        if ($trade['userid'] != $uid) {
            $this->error(L('Oops , This order does not belong to you!!'));
        }
		$data=D('Trade')->reject($id);

		$send['status']=$data[0];
		$send['data']=$data[1];
        echo json_encode($send);
    }
	public function MyClosedOrders($market = NULL)
    {
		$uid=$this->userid();

        if (!C('market')[$market]) {
            return null;
        }
		else{
			$pieces = explode("_", $market);
			$first_currency=$pieces[0];
			$base_currency=$pieces[1];
		}
		$sql_query='select id,price,num,mum,type,fee_buy,fee_sell,status,addtime from codono_trade_log where status=1 and market=\'' . $market . '\' and userid=' . $uid . ' order by id desc limit 10';
		
        $result = M()->query($sql_query);

        if ($result) {
            foreach ($result as $k => $v) {
                $data['entrust'][$k]['addtime'] = date('m-d H:i:s', $v['addtime']);
                $data['entrust'][$k]['type'] = $v['type'];
                $data['entrust'][$k]['price'] = round($v['price'] * 1,8);
                $data['entrust'][$k]['num'] = round($v['num'], 8);
                $data['entrust'][$k]['deal'] = round($v['num'], 8);
                $data['entrust'][$k]['id'] = round($v['id']);
				$data['entrust'][$k]['first'] = $first_currency;
				$data['entrust'][$k]['base'] = $base_currency;
            }
        } else {
            $data['entrust'] = array();
        }

        $userCoin = M('UserCoin')->where(array('userid' => $uid))->find();

        if ($userCoin) {
            $xnb = explode('_', $market)[0];
            $rmb = explode('_', $market)[1];
            $data['usercoin']['xnb'] = floatval($userCoin[$xnb]);
            $data['usercoin']['xnbd'] = floatval($userCoin[$xnb . 'd']);
            $data['usercoin']['usd'] = floatval($userCoin[$rmb]);
            $data['usercoin']['usdd'] = floatval($userCoin[$rmb . 'd']);
        } else {
            $data['usercoin'] = null;
        }
		   $this->ajaxShow($data);
  }
	public function MyOpenOrders($market = NULL)
    {
		$uid=$this->userid();
        

        if (!C('market')[$market]) {
            return null;
        }
		else{
			$pieces = explode("_", $market);
			$first_currency=$pieces[0];
			$base_currency=$pieces[1];
		}

        $result = M()->query('select id,price,num,deal,mum,type,fee,status,addtime from codono_trade where status=0 and market=\'' . $market . '\' and userid=' . $uid . ' order by id desc limit 10;');

        if ($result) {
            foreach ($result as $k => $v) {
                $data['entrust'][$k]['addtime'] = date('m-d H:i:s', $v['addtime']);
                $data['entrust'][$k]['type'] = $v['type'];
                $data['entrust'][$k]['price'] = round($v['price'] * 1,8);
                $data['entrust'][$k]['num'] = round($v['num'], 8);
                $data['entrust'][$k]['deal'] = round($v['deal'], 8);
                $data['entrust'][$k]['id'] = round($v['id']);
				$data['entrust'][$k]['first'] = $first_currency;
				$data['entrust'][$k]['base'] = $base_currency;
            }
        } else {
            $data['entrust'] = array();
        }

        $userCoin = M('UserCoin')->where(array('userid' => $uid))->find();

        if ($userCoin) {
            $xnb = explode('_', $market)[0];
            $rmb = explode('_', $market)[1];
            $data['usercoin']['xnb'] = floatval($userCoin[$xnb]);
            $data['usercoin']['xnbd'] = floatval($userCoin[$xnb . 'd']);
            $data['usercoin']['usd'] = floatval($userCoin[$rmb]);
            $data['usercoin']['usdd'] = floatval($userCoin[$rmb . 'd']);
        } else {
            $data['usercoin'] = null;
        }
		   $this->ajaxShow($data);
  }
  
  
   public function upTrade()
    {
		$input = I('post.');
		$market=$input['market'];
		$price=round($input['price'], C('market')[$market]['round']);
		$num=round($input['num'],6);
		$type=$input['type'];
		
		$uid=$this->userid();

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
            $this->error('Market is closed in these hours!' . $begintrade . '-' . $endtrade);
        }


        if (!check($price, 'double')) {
            $this->error(L('Invalid price'));
        }

        if (!check($num, 'double')) {
            $this->error(L('Invalid quantity').$num);
        }

        if (($type != 1) && ($type != 2)) {
            $this->error(L('Transaction type format error'));
        }

        $user = M('User')->where(array('id' => $uid))->find();



        if (!C('market')[$market]) {
            $this->error(L('Error market'));
        } else {
            $xnb = explode('_', $market)[0];
            $rmb = explode('_', $market)[1];
        }

        // TODO: SEPARATE

        $price = round(floatval($price), C('market')[$market]['round']);

        if (!$price) {
            $this->error(L('The transaction price error') . $price);
        }

        $num = round($num, 8);

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
            $this->error(L('Price is low,Keep it above').' '.$min_price);
        }

        $hou_price = C('market')[$market]['hou_price'];

        if (!$hou_price) {
            $hou_price = C('market')[$market]['market_ico_price'];
        }

        if ($hou_price) {
            if (C('market')[$market]['zhang']) {
                $zhang_price = round(($hou_price / 100) * (100 + C('market')[$market]['zhang']), C('market')[$market]['round']);

                if ($zhang_price < $price) {
                    $this->error(L('Transaction prices rose more than limit today!'));
                }
            }

            if (C('market')[$market]['die']) {
                $die_price = round(($hou_price / 100) * (100 - C('market')[$market]['die']), C('market')[$market]['round']);
                //var_dump(C('market')[$market]);
                if ($price < $die_price) {
                    $this->error(L('Least sale bid suggested:') . $rmb . ' ' . $die_price);
                }
            }
        }

        $user_coin = M('UserCoin')->where(array('userid' => $uid))->find();

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
                $this->error(L('INSUFFICIENT') . C('coin')[$rmb]['title'].' You have '.$user_coin[$rmb]. ' and you need '.$mum);
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
						//$zheng_query="select id,price,sum(num-deal)as nums from codono_trade where userid=" . $uid . " and status=0 and type=2 and market like %" . $xnb . "%";
						
						$zheng_query="SELECT * FROM `codono_trade` WHERE `userid` = '". $uid ."' and status=0 and type=2 and market like '%" . $xnb . "%'";

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
                    'userid' => $uid,
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
                $this->error(L('THE_MINIMUM_AMOUNT_FOR_EACH_PU').' ' .C('coin')[$rmb]['title']. '' . C('market')[$market]['trade_min']. ' and you are buying for '.$mum);
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
        $user_coin = $mo->table('codono_user_coin')->where(array('userid' => $uid))->find();

        if ($type == 1) {
            if ($user_coin[$rmb] < $mum) {
                $this->error(L('INSUFFICIENT') . C('coin')[$rmb]['title']);
            }

            $finance = $mo->table('codono_finance')->where(array('userid' => $uid))->order('id desc')->find();
            $finance_num_user_coin = $mo->table('codono_user_coin')->where(array('userid' => $uid))->find();
            $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $uid))->setDec($rmb, $mum);
            $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $uid))->setInc($rmb . 'd', $mum);
            $rs[] = $finance_nameid = $mo->table('codono_trade')->add(array('userid' => $uid, 'market' => $market, 'price' => $price, 'num' => $num, 'mum' => $mum, 'fee' => $fee, 'type' => 1, 'addtime' => time(), 'status' => 0));


            //20170531 modify only statistics USD transaction Money TYPE
            if ($rmb == "usd") {
                $finance_mum_user_coin = $mo->table('codono_user_coin')->where(array('userid' => $uid))->find();
                $finance_hash = md5($uid . $finance_num_user_coin['usd'] . $finance_num_user_coin['usdd'] . $mum . $finance_mum_user_coin['usd'] . $finance_mum_user_coin['usdd'] . CODONOLIC . 'auth.codono.com');
                $finance_num = $finance_num_user_coin['usd'] + $finance_num_user_coin['usdd'];

                if ($finance['mum'] < $finance_num) {
                    $finance_status = (1 < ($finance_num - $finance['mum']) ? 0 : 1);
                } else {
                    $finance_status = (1 < ($finance['mum'] - $finance_num) ? 0 : 1);
                }

                $rs[] = $mo->table('codono_finance')->add(array('userid' => $uid, 'coinname' => 'usd', 'num_a' => $finance_num_user_coin['usd'], 'num_b' => $finance_num_user_coin['usdd'], 'num' => $finance_num_user_coin['usd'] + $finance_num_user_coin['usdd'], 'fee' => $mum, 'type' => 2, 'name' => 'trade', 'nameid' => $finance_nameid, 'remark' => 'Trading Center-Buying commission-market' . $market, 'mum_a' => $finance_mum_user_coin['usd'], 'mum_b' => $finance_mum_user_coin['usdd'], 'mum' => $finance_mum_user_coin['usd'] + $finance_mum_user_coin['usdd'], 'move' => $finance_hash, 'addtime' => time(), 'status' => $finance_status));
            }


        } else if ($type == 2) {
            if ($user_coin[$xnb] < $num) {
                $this->error(C('coin')[$xnb]['title'] . 'Insufficient balance2!');
            }

            $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $uid))->setDec($xnb, $num);
            $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $uid))->setInc($xnb . 'd', $num);
            $rs[] = $mo->table('codono_trade')->add(array('userid' => $uid, 'market' => $market, 'price' => $price, 'num' => $num, 'mum' => $mum, 'fee' => $fee, 'type' => 2, 'addtime' => time(), 'status' => 0));
        } else {
            $mo->execute('rollback');
            $mo->execute('unlock tables');
            $this->error(L('Transaction type error'));
        }
        if (check_arr($rs)) {
            $mo->execute('commit');
            $mo->execute('unlock tables');
            S('getDepth', null);

            $this->matchingTrade($market);
            $this->success(L('Trading success!'));
        } else {
            $mo->execute('rollback');
            $mo->execute('unlock tables');
            $this->error(L('transaction failed!'));

        }
    }
	public function matchingTrade($market = NULL)
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
                    $log = 'error 1 market:' . $market . 'Error: Buy OrderID:' . $buy['id'] . L( ' Sell orderID: ') . $sell['id'] . L('TRANSACTION_TYPE') . $type . "\n";
                    $log .= 'ERR: Deal Quantity Error,Quantity :' . $amount;
                    mlog($log);
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
                    $log = 'error2 market:' . $market . 'Error: Buy OrderID:' . $buy['id'] . L( ' Sell orderID: ') . $sell['id'] . L('TRANSACTION_TYPE') . $type . L('TRANSACTION_AMOUNT') . $amount . "\n";
                    $log .= 'ERR: Deal price Error,price :' . $price;
                    mlog($log);
                    break;
                } else {
                    // TODO: SEPARATE
                    $price = round($price, C('market')[$market]['round']);
                }

                $mum = round($price * $amount, 8);

                if (!$mum) {
                    $log = 'error3 market:' . $market . 'Error: Buy OrderID:' . $buy['id'] . L( ' Sell orderID: ') . $sell['id'] . L('TRANSACTION_TYPE') . $type . L('TRANSACTION_AMOUNT') . $amount . "\n";
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
                    $log = 'error4 market:' . $market . 'Error: Buy OrderID:' . $buy['id'] . L( ' Sell orderID: ') . $sell['id'] . L('TRANSACTION_TYPE') . $type . L('TRANSACTION_AMOUNT') . $amount . L('deal price') . $price . L('Total turnover') . $mum . "\n";
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
                    $log = 'error5 market:' . $market . 'Error: Buy OrderID:' . $buy['id'] . L( ' Sell orderID: ') . $sell['id'] . L('TRANSACTION_TYPE') . $type . L('TRANSACTION_AMOUNT') . $amount . L('deal price') . $price . L('Total turnover') . $mum . "\n";
                    $log .= 'ERR: Sellers Update Quantity Error,Update Quantity :' . $sell_save;
                    mlog($log);
                    break;
                }

                $user_buy = M('UserCoin')->where(array('userid' => $buy['userid']))->find();
			
                if (!$user_buy[$rmb . 'd']) {
                    $log = 'error6 market:' . $market . 'Error: Buy OrderID:' . $buy['id'] . L( ' Sell orderID: ') . $sell['id'] .'  '. L('TRANSACTION_TYPE') . $type . L('TRANSACTION_AMOUNT') . $amount . L('deal price') . $price . L('Total turnover') . $mum . "\n";
                    $log .= 'ERR: Property buyers error,freeze Property is ' . $rmb . 'd' . $user_buy[$rmb . 'd'];
                    mlog($log);
                    mlog(json_encode($user_buy));
					break;
                }

                $user_sell = M('UserCoin')->where(array('userid' => $sell['userid']))->find();

                if (!$user_sell[$xnb . 'd']) {
                    $log = 'error7 market:' . $market . 'Error: Buy OrderID:' . $buy['id'] . L( ' Sell orderID: ') . $sell['id'] . L('TRANSACTION_TYPE') . $type . L('TRANSACTION_AMOUNT') . $amount . L('deal price') . $price . L('Total turnover') . $mum . "\n";
                    $log .= 'ERR: Sellers of property error,freeze Property is' . $user_sell[$xnb . 'd'];
                    mlog($log);
					mlog($sell['userid']);
                    mlog(json_encode($user_sell));	
					mlog($xnb . 'd');
					break;
                }

                if ($user_buy[$rmb . 'd'] < 1.0E-8) {
                    $log = '/n/n error88 market:' . $market . 'Error: Buy OrderID:' . $buy['id'] . L( ' Sell orderID: ') . $sell['id'] . L('TRANSACTION_TYPE') . $type . L('TRANSACTION_AMOUNT') . $amount . L('deal price') . $price . L('Total turnover') . $mum . "\n";
                    $log .= 'ERR: Buyers Update Freeze currency appear error,Should be updated' . $buy_save . L('Account Balance') . $user_buy[$rmb . 'd'] . L('Error handling');
                    mlog($log);
                    M('Trade')->where(array('id' => $buy['id']))->setField('status', 1);
                    break;

                }

                if ($buy_save <= round($user_buy[$rmb . 'd'], 8)) {
                    $save_buy_rmb = $buy_save;
                } else if ($buy_save <= round($user_buy[$rmb . 'd'], 8) + 1) {
                    $save_buy_rmb = $user_buy[$rmb . 'd'];
                    $log = 'error8 market:' . $market . 'Error: Buy OrderID:' . $buy['id'] . L( ' Sell orderID: ') . $sell['id'] . L('TRANSACTION_TYPE') . $type . L('TRANSACTION_AMOUNT') . $amount . L('deal price') . $price . L('Total turnover') . $mum . "\n";
                    $log .= 'ERR: Buyers Update Freeze currency Error occurs,Should be updated' . $buy_save . L('Account Balance') . $user_buy[$rmb . 'd'] . L('The actual update') . $save_buy_rmb;
                    mlog($log);
                } else {
                    $log = 'error9 market:' . $market . 'Error: Buy OrderID:' . $buy['id'] . L( ' Sell orderID: ') . $sell['id'] . L('TRANSACTION_TYPE') . $type . L('TRANSACTION_AMOUNT') . $amount . L('deal price') . $price . L('Total turnover') . $mum . "\n";
                    $log .= 'ERR: Buyers Update Freeze USD appear error,Should be updated' . $buy_save . L('Account Balance') . $user_buy[$rmb . 'd'] . L('Error handling');
                    mlog($log);
                    M('Trade')->where(array('id' => $buy['id']))->setField('status', 1);
                    break;
                }
                // TODO: SEPARATE

                if ($amount <= round($user_sell[$xnb . 'd'], C('market')[$market]['round'])) {
                    $save_sell_xnb = $amount;
                } else {
                    // TODO: SEPARATE

                    if ($amount <= round($user_sell[$xnb . 'd'], C('market')[$market]['round']) + 1) {
                        $save_sell_xnb = $user_sell[$xnb . 'd'];
                        $log = 'error10 market:' . $market . 'Error: Buy OrderID:' . $buy['id'] . L( ' Sell orderID: ') . $sell['id'] .' ' . L('TYPE') . $type . L('TRANSACTION_AMOUNT') . $amount . L('deal price') . $price . L('Total turnover') . $mum . "\n";
						$log .='ERR: The seller updated the frozen token with an error and should update '. $amount . ' account balance ' . $user_sell[$xnb . 'd'] . ' Actual update ' . $save_sell_xnb;
						
                        mlog($log);
                    } else {
                        $log = 'error11 market:' . $market . 'Error: Buy OrderID:' . $buy['id'] . L( ' Sell orderID: ') . $sell['id'] . L('TRANSACTION_TYPE') . $type . L('TRANSACTION_AMOUNT') . $amount . L('deal price') . $price . L('Total turnover') . $mum . "\n";
                        $log .= 'ERR: Sellers Update freeze Virtual currency appear error,Should be updated' . $amount . L('Account Balance') . $user_sell[$xnb . 'd'] . L('Error handling');
                        mlog($log);
                        M('Trade')->where(array('id' => $sell['id']))->setField('status', 1);
                        break;
                    }
                }

                if (!$save_buy_rmb) {
                    $log = 'error12 market:' . $market . 'Error: Buy OrderID:' . $buy['id'] . L( ' Sell orderID: ') . $sell['id'] . L('TRANSACTION_TYPE') . $type . L('TRANSACTION_AMOUNT') . $amount . L('deal price') . $price . L('Total turnover') . $mum . "\n";
                    $log .= 'ERR: Buyers Update Quantity Error,Update Quantity:' . $save_buy_rmb;
                    mlog($log);
                    M('Trade')->where(array('id' => $buy['id']))->setField('status', 1);
                    break;
                }

                if (!$save_sell_xnb) {
                    $log = 'error13 market:' . $market . 'Error: Buy OrderID:' . $buy['id'] . L( ' Sell orderID: ') . $sell['id'] . L('TRANSACTION_TYPE') . $type . L('TRANSACTION_AMOUNT') . $amount . L('deal price') . $price . L('Total turnover') . $mum . "\n";
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
                            mlog('error91 market:' . $market . 'Error: Buy OrderID:' . $buy['id'] . L( ' Sell orderID: ') . $sell['id'] . L('TRANSACTION_TYPE') . $type . L('TRANSACTION_AMOUNT') . $amount, L('deal price') . $price . L('Total turnover') . $mum . "\n");
                            mlog(L('market place') . $market . 'Error: Buy OrderID:' . $buy['id'] . L( ' Sell orderID: ') . $sell['id'] . L('TRANSACTION_AMOUNT') . $amount . L('TRANSACTION_TYPE') . $type . 'SellersUpdatefreezeVirtual currencyError occurs,Should be updated' . $chajia . L('Account Balance') . $chajia_user_buy[$rmb . 'd'] . L('The actual update') . $chajia_save_buy_rmb);
                        } else {
                            mlog('error92 market:' . $market . 'Error: Buy OrderID:' . $buy['id'] . L( ' Sell orderID: ') . $sell['id'] . L('TRANSACTION_TYPE') . $type . L('TRANSACTION_AMOUNT') . $amount, L('deal price') . $price . L('Total turnover') . $mum . "\n");
                            mlog(L('market place') . $market . 'Error: Buy OrderID:' . $buy['id'] . L( ' Sell orderID: ') . $sell['id'] . L('TRANSACTION_AMOUNT') . $amount . L('TRANSACTION_TYPE') . $type . 'SellersUpdatefreezeVirtual currencyappearerror,Should be updated' . $chajia . L('Account Balance') . $chajia_user_buy[$rmb . 'd'] . L('Error handling'));
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
            $new_price = round(M('TradeLog')->where(array('market' => $market, 'status' => 1))->order('id desc')->getField('price'), 6);
            $buy_price = round(M('Trade')->where(array('type' => 1, 'market' => $market, 'status' => 0))->max('price'), 6);
            $sell_price = round(M('Trade')->where(array('type' => 2, 'market' => $market, 'status' => 0))->min('price'), 6);
            $min_price = round(M('TradeLog')->where(array(
                'market' => $market,
                'addtime' => array('gt', time() - (60 * 60 * 24))
            ))->min('price'), 6);
            $max_price = round(M('TradeLog')->where(array(
                'market' => $market,
                'addtime' => array('gt', time() - (60 * 60 * 24))
            ))->max('price'), 6);
            $volume = round(M('TradeLog')->where(array(
                'market' => $market,
                'addtime' => array('gt', time() - (60 * 60 * 24))
            ))->sum('num'), 6);
            $sta_price = round(M('TradeLog')->where(array(
                'market' => $market,
                'status' => 1,
                'addtime' => array('gt', time() - (60 * 60 * 24))
            ))->order('id asc')->getField('price'), 6);
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
    }

}

?>