<?php

namespace Home\Controller;

class AjaxController extends HomeController
{

    public function imgUser()
    {
//		var_dump(I());
        //Upload a user ID
        if (!userid()) {
            echo "nologin";
        }

        $userimg = M('User')->where(array('id' => userid()))->getField("idcardimg1");
        if ($userimg) {
            $img_arr = array();
            $img_arr = explode("_", $userimg);
            if (count($img_arr) >= 3) {
                M('User')->where(array('id' => userid()))->save(array('idcardimg1' => ''));
            }
        }

        $upload = new \Think\Upload();
        $upload->maxSize = 2048000;
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');
        $upload->rootPath = './Upload/idcard/';
        $upload->autoSub = false;
        $info = $upload->upload();

        if (!$info) {
            echo "error";
            exit();
        }

        foreach ($info as $k => $v) {


            $userimg = M('User')->where(array('id' => userid()))->getField("idcardimg1");
            if ($userimg) {
                $img_arr = array();
                $img_arr = explode("_", $userimg);
                if (count($img_arr) >= 3) {
                    echo "error2";
                    exit();
                }

                $path = $userimg . "_" . $v['savename'];
            } else {
                $path = $v['savename'];
            }
            if (count($img_arr) >= 2) {
                M('User')->where(array('id' => userid()))->save(array('idcardimg1' => $path, 'idcardinfo' => ''));
            } else {
                M('User')->where(array('id' => userid()))->save(array('idcardimg1' => $path));
            }
            echo $v['savename'];
            exit();
        }
    }


    public function getJsonMenu($ajax = 'json')
    {
        $data = (APP_DEBUG ? null : S('getJsonMenu'));

        if (!$data) {
            foreach (C('market') as $k => $v) {
                $v['xnb'] = explode('_', $v['name'])[0];
                $v['rmb'] = explode('_', $v['name'])[1];
                $data[$k]['name'] = $v['name'];
                $data[$k]['img'] = $v['xnbimg'];
                $data[$k]['title'] = $v['title'];
            }

            S('getJsonMenu', $data);
        }

        if ($ajax) {
            exit(json_encode($data));
        } else {
            return $data;
        }
    }

    public function top_coin_menu($ajax = 'json')
    {

        $data = (APP_DEBUG ? null : S('codono_getTopCoinMenu'));


        $codono_getCoreConfig = codono_getCoreConfig();
        if(!$codono_getCoreConfig){
            $this->error('Incorrect Core Config');
        }





        if (!$data) {
            $data = array();

            foreach($codono_getCoreConfig['codono_indexcat'] as $k=>$v){
                $data[$k]['title'] = $v;
            }
            foreach (C('market') as $k => $v) {

                $v['xnb'] = explode('_', $v['name'])[0];
                $v['rmb'] = explode('_', $v['name'])[1];
                $data_tmp['img'] = $v['xnbimg'];
                $data_tmp['title'] = $v['navtitle'];

                $data[$v['jiaoyiqu']]['data'][$k] = $data_tmp;
                unset($data_tmp);
            }
            S('codono_getTopCoinMenu', $data);
        }

        if ($ajax) {
            exit(json_encode($data));
        }
        else {
            return $data;
        }
    }

    public function xtop_coin_menu($ajax = 'json')
    {

        $data = (APP_DEBUG ? null : S('codono_getTopCoinMenu'));


        $codono_getCoreConfig = codono_getCoreConfig();
        if (!$codono_getCoreConfig) {
            $this->error(L('Incorrect Core Config'));
        }

        if (!$data) {
            $data = array();

            foreach ($codono_getCoreConfig['codono_indexcat'] as $k => $v) {
                $data[$k]['title'] = $v;
            }

            foreach (C('market') as $k => $v) {

                $v['xnb'] = explode('_', $v['name'])[0];
                $v['rmb'] = explode('_', $v['name'])[1];

                $data_tmp['img'] = $v['xnbimg'];
                $data_tmp['title'] = $v['navtitle'];

                $data[$v['jiaoyiqu']]['data'][$k] = $data_tmp;

                unset($data_tmp);
            }

            S('codono_getTopCoinMenu', $data);
        }

        if ($ajax) {
            exit(json_encode($data));
        } else {
            return $data;
        }
    }

    public function allfinance($ajax = 'json')
    {
        if (!userid()) {
            return false;
        }

        $UserCoin = M('UserCoin')->where(array('userid' => userid()))->find();
        $usd['zj'] = 0;

        foreach (C('coin') as $k => $v) {
            if ($v['name'] == 'usd') {
                $usd['ky'] = $UserCoin[$v['name']] * 1;
                $usd['dj'] = $UserCoin[$v['name'] . 'd'] * 1;
                $usd['zj'] = $usd['zj'] + $usd['ky'] + $usd['dj'];
            } else {
                /* 				if (C('market')[$v['name'] . '_usd']['new_price']) {
                                    $jia = C('market')[$v['name'] . '_usd']['new_price'];
                                } */

                if (C('market')[C('market_type')[$v['name']]]['new_price']) {
                    $jia = C('market')[C('market_type')[$v['name']]]['new_price'];
                } else {
                    $jia = 1;
                }

                $usd['zj'] = format_num($usd['zj'] + (($UserCoin[$v['name']] + $UserCoin[$v['name'] . 'd']) * $jia), 2) * 1;
            }
        }

        $data = format_num($usd['zj'], 8);
        $data = NumToStr($data);

        if ($ajax) {
            exit(json_encode($data));
        } else {
            return $data;
        }
    }

    public function allsum($ajax = 'json')
    {
        $data = (APP_DEBUG ? null : S('allsum'));

        if (!$data) {
            $data = M('TradeLog')->sum('mum');
            S('allsum', $data);
        }

        $data = format_num($data);
        $data = str_repeat('0', 12 - strlen($data)) . (string)$data;

        if ($ajax) {
            exit(json_encode($data));
        } else {
            return $data;
        }
    }

    public function allcoin($ajax = 'json')
    {
        $data = (APP_DEBUG ? null : S('allcoin'));

        // marketTransaction Record
        $marketLogs = array();
        foreach (C('market') as $k => $v) {
            //S('getTradelog' . $market, null);
            //$_tmp = S('getTradelog' . $k);
            $_tmp = null;
            if (!empty($_tmp)) {
                $marketLogs[$k] = $_tmp;
            } else {
                $tradeLog = M('TradeLog')->where(array('status' => 1, 'market' => $k))->order('id desc')->limit(50)->select();
                $_data = array();
                foreach ($tradeLog as $_k => $_v) {
                    $_data['tradelog'][$_k]['addtime'] = date('m-d H:i:s', $_v['addtime']);
                    $_data['tradelog'][$_k]['type'] = $_v['type'];
                    $_data['tradelog'][$_k]['price'] = format_num($v['price']);
                    $_data['tradelog'][$_k]['num'] = format_num($_v['num'], 6);
                    $_data['tradelog'][$_k]['mum'] = format_num($_v['mum'], 6);
                }
                $marketLogs[$k] = $_data;
                S('getTradelog' . $k, $_data);
            }
        }

        $themarketLogs = array();
        if ($marketLogs) {
            $last24 = time() - 86400;
            $_date = date('m-d H:i:s', $last24);
            foreach (C('market') as $k => $v) {
                $tradeLog = isset($marketLogs[$k]['tradelog']) ? $marketLogs[$k]['tradelog'] : null;
                if ($tradeLog) {
                    $sum = 0;
                    foreach ($tradeLog as $_k => $_v) {
                        if ($_v['addtime'] < $_date) {
                            continue;
                        }
                        $sum += $_v['mum'];
                    }
                    $themarketLogs[$k] = $sum;
                }
            }
        }

        if (!$data) {
            foreach (C('market') as $k => $v) {
                $data[$k][0] = $v['title'];
                $data[$k][1] = format_num($v['new_price'], $v['round']);
                $data[$k][2] = format_num($v['buy_price'], $v['round']);
                $data[$k][3] = format_num($v['sell_price'], $v['round']);
                $data[$k][4] = isset($themarketLogs[$k]) ? $themarketLogs[$k] : 0;//format_num($v['volume'] * $v['new_price'], 2) * 1;
                $data[$k][5] = '';
                $data[$k][6] = format_num($v['volume'], 2) * 1;
                $data[$k][7] = format_num($v['change'], 2);
                $data[$k][8] = $v['name'];
                $data[$k][9] = $v['xnbimg'];
                $data[$k][10] = '';
            }

            S('allcoin', $data);
        }

        if ($ajax) {
            exit(json_encode($data));
        } else {
            return $data;
        }
    }

    function findBaseCoin($coin, $combination = 'usd_btc')
    {
        $explo = explode('_', $combination);
		
        //$status = in_array($coin, $explo); //It now holds value as BTC
		if($explo[1]==$coin)
			$status=true;
		else
			$status=false;
        //	var_dump($status);
        return $status;
    }
    public function instrument($coin = 'btc', $ajax = 'json')
    {
        $data = (APP_DEBUG ? null : S('instrument'));
        //echo $coin;
        // marketTransaction Record
        if (!$data) {
            $i = 0;
            foreach (C('market') as $k => $v) {
                //	var_dump($v['name']);
                //echo $this->findBaseCoin($v['name']);
                if ($this->findBaseCoin($coin, $v['name']) == true) {
                    $data[$i][0] = "<a href='/Trade/index/market/{$v['name']}'>{$v['name']}</a>";
                    $data[$i][1] = format_num($v['new_price'], $v['round']);
                    $data[$i][2] = format_num($v['volume'], 2) * 1;
                    $data[$i][3] = format_num($v['change'], 2) . "%";
                    $i++;
                }
            }
            $senddata['data'] = $data;
            S('instrument', $senddata);
        }

        if ($ajax) {
            exit(json_encode($senddata));
        } else {
            return $data;
        }
    }
    public function advanceinstrument($coin = 'btc', $ajax = 'json')
    {
      //  $data = (APP_DEBUG ? null : S('advanceinstrument_'.$coin));
        if (!$data || !$data['data']) {
            $i = 0;
			$market=C('market');
		uasort($market, function($a, $b){
			return strcmp($b['sort'], $a['sort']);
			});
            foreach ($market as $k => $v) {

                if ($this->findBaseCoin($coin, $v['name']) == true) {
                    
                    $data[$i][1] = format_num($v['new_price'], $v['round']);
                    $data[$i][2] = format_num($v['volume'], 2) * 1;
                    $data[$i][3] = format_num($v['change'], 2) . "%";
					$data[$i][4] = (int)$v['id'];
					if(strpos($data[$i][3],'-')=== false){
					$data[$i][5] ='crypt-up';
					}else{$data[$i][5] ='crypt-down';}
					$data[$i][0] = "<a href='/Trade/index/market/{$v['name']}'>{$v['name']}</a>";
                    $i++;
                }
            }
            $senddata['data'] = $data;
            S('advanceinstrument_'.$coin, $senddata);
            exit(json_encode($senddata));
        }
            exit(json_encode( $data));
        
    }
    //New custom partition query 2017-06-05

    public function allcoin_a($id = 1, $ajax = 'json')
    {
        $codono_data = (APP_DEBUG ? null : S('codono_allcoin'));


        $codono_data = array();

        $codono_data['info'] = "Data anomalies";
        $codono_data['status'] = 0;
        $codono_data['url'] = "";

        // marketTransaction Record
        $marketLogs = array();
        foreach (C('market') as $k => $v) {

            $_tmp = null;
            if (!empty($_tmp)) {
                $marketLogs[$k] = $_tmp;
            } else {
                $tradeLog = M('TradeLog')->where(array('status' => 1, 'market' => $k))->order('id desc')->limit(50)->select();
                $_data = array();
                foreach ($tradeLog as $_k => $v) {
                    $_data['tradelog'][$_k]['addtime'] = date('m-d H:i:s', $v['addtime']);
                    $_data['tradelog'][$_k]['type'] = $v['type'];
                    $_data['tradelog'][$_k]['price'] = format_num($v['price']);
                    $_data['tradelog'][$_k]['num'] = format_num($v['num'], 6);
                    $_data['tradelog'][$_k]['mum'] = format_num($v['mum'], 6);
                }
                $marketLogs[$k] = $_data;
                S('getTradelog' . $k, $_data);
            }
        }

        $themarketLogs = array();
        if ($marketLogs) {
            $last24 = time() - 86400;
            $_date = date('m-d H:i:s', $last24);
            foreach (C('market') as $k => $v) {
                $tradeLog = isset($marketLogs[$k]['tradelog']) ? $marketLogs[$k]['tradelog'] : null;
                if ($tradeLog) {
                    $sum = 0;
                    foreach ($tradeLog as $_k => $_v) {
                        if ($_v['addtime'] < $_date) {
                            continue;
                        }
                        $sum += $_v['mum'];
                    }
                    $themarketLogs[$k] = $sum;
                }
            }
        }
        //TODO: Adding caching mechanism
        //if (!$data) {
        {
            $codono_data['info'] = "Normal data";
            $codono_data['status'] = 1;
            $codono_data['url'] = array();


            foreach (C('market') as $k => $v) {
                if ($v['jiaoyiqu'] == $id) {

                    $codono_data['url'][$k][0] = $v['title'];
                    $codono_data['url'][$k][1] = format_num($v['new_price'], $v['round']);
                    $codono_data['url'][$k][2] = format_num($v['buy_price'], $v['round']);
                    $codono_data['url'][$k][3] = format_num($v['sell_price'], $v['round']);
                    $codono_data['url'][$k][4] = isset($themarketLogs[$k]) ? $themarketLogs[$k] : 0;//format_num($v['volume'] * $v['new_price'], 2) * 1;
                    $codono_data['url'][$k][5] = '';
                    $codono_data['url'][$k][6] = format_num($v['volume'], 2) * 1;
                    $codono_data['url'][$k][7] = format_num($v['change'], 2);
                    $codono_data['url'][$k][8] = $v['name'];
                    $codono_data['url'][$k][9] = $v['xnbimg'];
                    $codono_data['url'][$k][10] = '';
                }

            }

            S('codono_allcoin', $codono_data);
        }

        if ($ajax) {
            echo json_encode($codono_data);
            unset($codono_data);
            exit();
        } else {
            return $codono_data;
        }
    }


    public function index_b_trends($ajax = 'json')
    {
        $data = (APP_DEBUG ? null : S('trends'));

        if (!$data) {
            foreach (C('market') as $k => $v) {
                $tendency = json_decode($v['tendency'], true);
                $data[$k]['data'] = $tendency;
                $data[$k]['yprice'] = $v['new_price'];
            }

            S('trends', $data);
        }

        if ($ajax) {
            exit(json_encode($data));
        } else {
            return $data;
        }
    }

    public function trends($ajax = 'json')
    {
        $data = (APP_DEBUG ? null : S('trends'));

        if (!$data) {
            foreach (C('market') as $k => $v) {
                $tendency = json_decode($v['tendency'], true);
                $data[$k]['data'] = $tendency;
                $data[$k]['yprice'] = $v['new_price'];
            }

            S('trends', $data);
        }

        if ($ajax) {
            exit(json_encode($data));
        } else {
            return $data;
        }
    }

    public function getJsonTop($market = NULL, $ajax = 'json')
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
                    $data['info']['max_price'] = format_num(C('market')[$market]['max_price'], C('market')[$market]['round']);
                } else {
                    $codono_tempprice = format_num((C('market')[$market]['market_ico_price'] / 100) * (100 + C('market')[$market]['zhang']), C('market')[$market]['round']);
                    $data['info']['max_price'] = $codono_tempprice;
                }

                if (C('market')[$market]['min_price']) {
                    $data['info']['min_price'] = format_num(C('market')[$market]['min_price'], C('market')[$market]['round']);
                } else {
                    $codono_tempprice = format_num((C('market')[$market]['market_ico_price'] / 100) * (100 - C('market')[$market]['die']), C('market')[$market]['round']);
                    $data['info']['min_price'] = $codono_tempprice;
                }


                $data['info']['buy_price'] = format_num(C('market')[$market]['buy_price'], C('market')[$market]['round']);
                $data['info']['sell_price'] = format_num(C('market')[$market]['sell_price'], C('market')[$market]['round']);
                $data['info']['volume'] = format_num(C('market')[$market]['volume'], C('market')[$market]['round']);
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

    public function getTradelog($market = NULL, $ajax = 'json')
    {
		$market=I('get.market','','text');
		if(is_array($market)){
			$market=$market[0];
		}
		
        $data = (APP_DEBUG ? null : S('getTradelog' . $market));

        if (!$data) {

            $tradeLog = M('TradeLog')->where(array('status' => 1, 'market' => $market))->order('id desc')->limit(25)->select();

            if ($tradeLog) {
                foreach ($tradeLog as $k => $v) {
                    $data['tradelog'][$k]['addtime'] = date('m-d H:i:s', $v['addtime']);
                    $data['tradelog'][$k]['type'] = $v['type'];
                    $data['tradelog'][$k]['price'] = format_num($v['price']);
                    $data['tradelog'][$k]['num'] = format_num($v['num'], 8);
                    $data['tradelog'][$k]['mum'] = format_num($v['mum'], $v['round']);
                    $data['tradelog'][$k]['time'] = date('H:i:s', $v['addtime']);
                }

                S('getTradelog' . $market, $data);
            }
        }

        if ($ajax) {
            exit(json_encode($data));
        } else {
            return $data;
        }
    }


    public function getAwardInfo($ajax = 'json')
    {
        $data = (APP_DEBUG ? null : S('getAwardInfo'));
        if (!$data) {
            $awardInfo = M('UserAward')->order('id desc')->limit(50)->select();

            if ($awardInfo) {
                foreach ($awardInfo as $k => $v) {
                    $data['awardInfo'][$k]['addtime'] = date('m-d H:i:s', $v['addtime']);
                    $name_tmp = M('User')->where(array('id' => $v['userid']))->getField('username');
                    $data['awardInfo'][$k]['username'] = substr_replace($name_tmp, '****', 2, strlen($name_tmp) - 4);
                    $data['awardInfo'][$k]['awardname'] = $v['awardname'];
                }

                S('getAwardInfo', $data, 300);
            }
        }

        if ($ajax) {
            exit(json_encode($data));
        } else {
            return $data;
        }
    }


    public function getActiveOrders($market = NULL, $trade_moshi = 1, $ajax = 'json')
    {
		$market=strtolower($market);
        if (!C('market')[$market]) {
            return null;
        }
		$round=C('market')[$market]['round']?C('market')[$market]['round']:8;

        $codono_getCoreConfig = codono_getCoreConfig();
        if (!$codono_getCoreConfig) {
            $this->error(L('Incorrect Core Config'));
        } else {
            $codono_putong = $codono_getCoreConfig['codono_userTradeNum'];
            $codono_teshu = $codono_getCoreConfig['codono_specialUserTradeNum'];
        }
		

        $data_getDepth = (APP_DEBUG ? null : S('getActiveDepth'.$market));

        if (!$data_getDepth[$market][$trade_moshi]) {
            if ($trade_moshi == 1) {
                $limt = 12;
            }

            if (($trade_moshi == 3) || ($trade_moshi == 4)) {
                
                if (userid()) {
                    $usertype = M('User')->where(array('id' => userid()))->getField('usertype');
                    if ($usertype == 1) {
                        $limt = $codono_teshu;
                    } else {
                        $limt = $codono_putong;
                    }
                } else {
                    $limt = $codono_putong;
                }
            }

            $trade_moshi = intval($trade_moshi);
			$mo = M();
			if(array_key_exists(strtoupper($market),LIQUIDITY_ARRAY)){
				$lastupdateid =$mo->query('select MAX(flag) as flag from codono_trade where market =\'' . $market . '\' AND status=0 AND userid=0');
				$flag=$lastupdateid[0]['flag'];
			}else{
				$flag =$lastupdateid =0;
			}
			
			  if ($trade_moshi == 1 && $flag!=0) {
				$buy_query='select id,price,sum(num-deal)as nums from codono_trade where status=0 and type=1 and market =\'' . $market . '\' and flag =\'' . $flag . '\' group by price order by price desc limit ' . $limt ;
			
                $buy = $mo->query($buy_query);

                $sell = array_reverse($mo->query('select id,price,sum(num-deal)as nums from codono_trade where status=0 and type=2 and market =\'' . $market . '\' and flag =\'' . $flag . '\' group by price order by price asc limit ' . $limt . ';'));

            }
            
            if ($trade_moshi == 1) {
				$buy_query='select id,price,sum(num-deal)as nums from codono_trade where status=0 and type=1 and market =\'' . $market . '\' group by price order by price desc limit ' . $limt ;
				
                $buy = $mo->query($buy_query);
	
                $sell = array_reverse($mo->query('select id,price,sum(num-deal)as nums from codono_trade where status=0 and type=2 and market =\'' . $market . '\' group by price order by price asc limit ' . $limt . ';'));

            }

            if ($trade_moshi == 3) {
                $buy = $mo->query('select id,price,sum(num-deal)as nums from codono_trade where status=0 and type=1 and market =\'' . $market . '\' group by price order by price desc limit ' . $limt . ';');
                $sell = null;
            }

            if ($trade_moshi == 4) {
                $buy = null;
                $sell = array_reverse($mo->query('select id,price,sum(num-deal)as nums from codono_trade where status=0 and type=2 and market =\'' . $market . '\' group by price order by price asc limit ' . $limt . ';'));
            }
            $data['buyvol']=0;
            $data['sellvol']=0;
            if ($buy) {

                foreach ($buy as $k => $v) {

                    $data['depth']['buy'][$k] = array(format_num($v['price'],$round), format_num($v['nums'],3));
					$data['buyvol'] = $data['buyvol']+format_num($v['nums']);
                }

            } else {
                $data['depth']['buy'] = '';
            }

            if ($sell) {
                foreach ($sell as $k => $v) {

                    $data['depth']['sell'][$k] = array(format_num($v['price'],$round ), format_num($v['nums'],3 ));
                    $data['sellvol'] = $data['sellvol']+format_num($v['nums']);
                }
            } else {
                $data['depth']['sell'] = '';
            }

            $data_getDepth[$market][$trade_moshi] = $data;
            S('getActiveDepth'.$market, $data_getDepth);
        } else {
            $data = $data_getDepth[$market][$trade_moshi];
        }

        if ($ajax) {
            exit(json_encode($data));
        } else {
            return $data;
        }
    }


    public function getDepth($market = NULL, $trade_moshi = 1, $ajax = 'json')
    {
        if (!C('market')[$market]) {
            return null;
        }


        $codono_getCoreConfig = codono_getCoreConfig();
        if (!$codono_getCoreConfig) {
            $this->error(L('Incorrect Core Config'));
        } else {
            $codono_putong = $codono_getCoreConfig['codono_userTradeNum'];
            $codono_teshu = $codono_getCoreConfig['codono_specialUserTradeNum'];
        }


        $data_getDepth = (APP_DEBUG ? null : S('getDepth'));

        if (!$data_getDepth[$market][$trade_moshi]) {
            if ($trade_moshi == 1) {
                $limt = 15;
            }

            if (($trade_moshi == 3) || ($trade_moshi == 4)) {
                //20170608 increase press user level transfer information 
                if (userid()) {
                    $usertype = M('User')->where(array('id'=> userid()))->getField('usertype');
                    if ($usertype == 1) {
                        $limt = $codono_teshu;
                    } else {
                        $limt = $codono_putong;
                    }
                } else {
                    $limt = $codono_putong;
                }
            }

            $trade_moshi = intval($trade_moshi);


            $mo = M();
            if ($trade_moshi == 1) {
                $buy = $mo->query('select id,price,sum(num-deal)as nums from codono_trade where status=0 and type=1 and market =\'' . $market . '\' group by price order by price desc limit ' . $limt . ';');
                $sell = array_reverse($mo->query('select id,price,sum(num-deal)as nums from codono_trade where status=0 and type=2 and market =\'' . $market . '\' group by price order by price asc limit ' . $limt . ';'));
            }

            if ($trade_moshi == 3) {
                $buy = $mo->query('select id,price,sum(num-deal)as nums from codono_trade where status=0 and type=1 and market =\'' . $market . '\' group by price order by price desc limit ' . $limt . ';');
                $sell = null;
            }

            if ($trade_moshi == 4) {
                $buy = null;
                $sell = array_reverse($mo->query('select id,price,sum(num-deal)as nums from codono_trade where status=0 and type=2 and market =\'' . $market . '\' group by price order by price asc limit ' . $limt . ';'));
            }

            if ($buy) {
                foreach ($buy as $k => $v) {
                    $data['depth']['buy'][$k] = array(floatval($v['price'] * 1), floatval($v['nums'] * 1));

                }
            } else {
                $data['depth']['buy'] = '';
            }

            if ($sell) {
                foreach ($sell as $k => $v) {

                    $data['depth']['sell'][$k] = array(floatval($v['price'] * 1), floatval($v['nums'] * 1));
                }
            } else {
                $data['depth']['sell'] = '';
            }

            $data_getDepth[$market][$trade_moshi] = $data;
            S('getDepth', $data_getDepth);
        } else {
            $data = $data_getDepth[$market][$trade_moshi];
        }

        if ($ajax) {
            exit(json_encode($data));
        } else {
            return $data;
        }
    }
    public function getDepthNew($market = NULL, $trade_moshi = 1, $ajax = 'json')
    {
        if (!C('market')[$market]) {
            return null;
        }


        $codono_getCoreConfig = codono_getCoreConfig();
        if (!$codono_getCoreConfig) {
            $this->error(L('Incorrect Core Config'));
        } else {
            $codono_putong = $codono_getCoreConfig['codono_userTradeNum'];
            $codono_teshu = $codono_getCoreConfig['codono_specialUserTradeNum'];
        }


        $data_getDepth = (APP_DEBUG ? null : S('getDepthNew'));

        if (!$data_getDepth[$market][$trade_moshi]) {
            if ($trade_moshi == 1) {
                $limt = 15;
            }

            if (($trade_moshi == 3) || ($trade_moshi == 4)) {
                //20170608 increase press user level transfer information 
                if (userid()) {
                    $usertype = M('User')->where(array('id' => userid()))->getField('usertype');
                    if ($usertype == 1) {
                        $limt = $codono_teshu;
                    } else {
                        $limt = $codono_putong;
                    }
                } else {
                    $limt = $codono_putong;
                }
            }

            $trade_moshi = intval($trade_moshi);


            $mo = M();
            if ($trade_moshi == 1) {
                $buy = $mo->query('select id,price,sum(num-deal)as nums from codono_trade where status=0 and type=1 and market =\'' . $market . '\' group by id order by price desc limit ' . $limt . ';');
                $sell = array_reverse($mo->query('select id,price,sum(num-deal)as nums from codono_trade where status=0 and type=2 and market =\'' . $market . '\' group by price order by price asc limit ' . $limt . ';'));
            }

            if ($trade_moshi == 3) {
                $buy = $mo->query('select id,price,sum(num-deal)as nums from codono_trade where status=0 and type=1 and market =\'' . $market . '\' group by price order by price desc limit ' . $limt . ';');
                $sell = null;
            }

            if ($trade_moshi == 4) {
                $buy = null;
                $sell = array_reverse($mo->query('select id,price,sum(num-deal)as nums from codono_trade where status=0 and type=2 and market =\'' . $market . '\' group by price order by price asc limit ' . $limt . ';'));
            }

            if ($buy) {
                foreach ($buy as $k => $v) {
                    $data['depth']['bids'][$k] = array(floatval($v['price'] * 1), floatval($v['nums'] * 1));

                }
            } else {
                $data['depth']['bids'] = '';
            }

            if ($sell) {
                foreach ($sell as $k => $v) {

                    $data['depth']['asks'][$k] = array(floatval($v['price'] * 1), floatval($v['nums'] * 1));
                }
            } else {
                $data['depth']['asks'] = '';
            }

            $data_getDepth[$market][$trade_moshi] = $data;
            S('getDepthNew', $data_getDepth);
        } else {
            $data = $data_getDepth[$market][$trade_moshi];
        }
		if($data['depth']['bids']==null){$data['depth']['bids']=array(0,0);}
		if($data['depth']['asks']==null){$data['depth']['asks']=array(0,0);}
		
        if ($ajax) {
            exit(json_encode($data['depth']));
        } else {
            return $data['depth'];
        }
    }

    public function getEntrustAndUsercoin($market = NULL, $ajax = 'json')
    {
        if (!userid()) {
            return null;
        }

        if (!C('market')[$market]) {
            return null;
        }

		$result1 = M()->query('select id,price,num,deal,mum,type,fee,status,addtime from codono_trade where status=0 and market=\'' . $market . '\' and userid=' . userid() . ' order by id desc limit 10;');
		 $result2 = M()->query('select id,price,stop,compare,num,deal,mum,type,fee,status,addtime from codono_stop where status=0 and market=\'' . $market . '\' and userid=' . userid() . ' order by id desc limit 10;');
		$data=$data1=$data2=array();
		
        if ($result1) {
            foreach ($result1 as $k => $v) {
                $data1['entrust'][$k]['addtime'] = date('m-d H:i:s', $v['addtime']);
				$data1['entrust'][$k]['condition'] = '-';
				$data1['entrust'][$k]['stop'] = '-';
                $data1['entrust'][$k]['type'] = $v['type'];
                $data1['entrust'][$k]['price'] = format_num($v['price'] * 1,8);
                $data1['entrust'][$k]['num'] = format_num($v['num'], 8);
                $data1['entrust'][$k]['deal'] = format_num($v['deal'], 8);
                $data1['entrust'][$k]['id'] = $v['id'];
				$data1['entrust'][$k]['tradetype'] = 'Limit';
            }
        } else {
            $data1['entrust'] = array();
        }
		if ($result2) {
            foreach ($result2 as $k => $v) {
				if($v['compare']=='gt'){
				$condition='<'.$v['stop'];}
				else{
				$condition='>'.$v['stop'];}
                $data2['entrust'][$k]['addtime'] = date('m-d H:i:s', $v['addtime']);
				$data2['entrust'][$k]['condition'] = $condition;
				$data2['entrust'][$k]['stop'] = $v['stop'];
                $data2['entrust'][$k]['type'] = $v['type'];
                $data2['entrust'][$k]['price'] = format_num($v['price'] * 1,8);
                $data2['entrust'][$k]['num'] = format_num($v['num'], 8);
                $data2['entrust'][$k]['deal'] = format_num($v['deal'], 8);
                $data2['entrust'][$k]['id'] = $v['id'];
				$data2['entrust'][$k]['tradetype'] = 'Stop-Limit';
            }
        } else {
            $data2['entrust'] = array();
        }
		$data['entrust']=array_merge($data1['entrust'],$data2['entrust']);  
        $userCoin = M('UserCoin')->where(array('userid' => userid()))->find();

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

        if ($ajax) {
            exit(json_encode($data));
        } else {
            return $data;
        }
    }
	public function getFullEntrustAndUsercoin($market = NULL, $ajax = 'json')
    {
        if (!userid()) {
            return null;
        }


		$result1 = M()->query('select id,price,num,deal,mum,type,fee,status,addtime,market from codono_trade where status=0 and  userid=' . userid() . ' order by id desc limit 10;');
		 $result2 = M()->query('select id,price,stop,compare,num,deal,mum,type,fee,status,addtime,market from codono_stop where status=0 and  userid=' . userid() . ' order by id desc limit 10;');
		$data=$data1=$data2=array();
		
        if ($result1) {
            foreach ($result1 as $k => $v) {
				$data1['entrust'][$k]['market'] = $v['market'];
                $data1['entrust'][$k]['addtime'] = date('m-d H:i:s', $v['addtime']);
				$data1['entrust'][$k]['condition'] = '-';
				$data1['entrust'][$k]['stop'] = '-';
                $data1['entrust'][$k]['type'] = $v['type'];
                $data1['entrust'][$k]['price'] = format_num($v['price'] * 1,8);
                $data1['entrust'][$k]['num'] = format_num($v['num'], 8);
                $data1['entrust'][$k]['deal'] = format_num($v['deal'], 8);
                $data1['entrust'][$k]['id'] = $v['id'];
				$data1['entrust'][$k]['tradetype'] = 'Limit';
            }
        } else {
            $data1['entrust'] = array();
        }
		if ($result2) {
            foreach ($result2 as $k => $v) {
				if($v['compare']=='gt'){
				$condition='<'.$v['stop'];}
				else{
				$condition='>'.$v['stop'];}
				$data2['entrust'][$k]['market'] = $v['market'];
                $data2['entrust'][$k]['addtime'] = date('m-d H:i:s', $v['addtime']);
				$data2['entrust'][$k]['condition'] = $condition;
				$data2['entrust'][$k]['stop'] = $v['stop'];
                $data2['entrust'][$k]['type'] = $v['type'];
                $data2['entrust'][$k]['price'] = format_num($v['price'] * 1,8);
                $data2['entrust'][$k]['num'] = format_num($v['num'], 8);
                $data2['entrust'][$k]['deal'] = format_num($v['deal'], 8);
                $data2['entrust'][$k]['id'] = $v['id'];
				$data2['entrust'][$k]['tradetype'] = 'Stop-Limit';
            }
        } else {
            $data2['entrust'] = array();
        }
		$data['entrust']=array_merge($data1['entrust'],$data2['entrust']);  
        $userCoin = M('UserCoin')->where(array('userid' => userid()))->find();

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

        if ($ajax) {
            exit(json_encode($data));
        } else {
            return $data;
        }
    }
	 public function getClosedOrders($market = NULL, $ajax = 'json')
    {
        if (!userid()) {
            return null;
        }

        if (!C('market')[$market]) {
            return null;
        }
	$where['userid'] = userid();
	$where['status'] =1;
	$where['market'] = $market;
	$Model = M('Trade');
    $count = $Model->where($where)->count();
		//$query='select id,price,num,mum,type,status,addtime from codono_trade_log where status=1 and market=\'' . $market . '\' and userid=' . userid() . '  order by id desc limit 15';
    $Page = new \Think\Page($count, 10);
        //$Page->parameter .= 'type=' . $type . '&status=' . $status . '&market=' . $market . '&';
        $show = $Page->show();

        $result = $Model->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
	
    //    $result = M()->query($query);

        if ($result) {
            foreach ($result as $k => $v) {
                $data['entrust'][$k]['addtime'] = date('m-d H:i:s', $v['addtime']);
                $data['entrust'][$k]['type'] = $v['type'];
                $data['entrust'][$k]['price'] = format_num($v['price'] * 1,8);
                $data['entrust'][$k]['num'] = format_num($v['num'], 8);
                $data['entrust'][$k]['id'] = $v['id'];
            }
        } else {
            $data['entrust'] = null;
        }

        $userCoin = M('UserCoin')->where(array('userid' => userid()))->find();

     

        if ($ajax) {
            exit(json_encode($data));
        } else {
            return $data;
        }
    }

    public function getChat($ajax = 'json')
    {
        $chat = (APP_DEBUG ? null : S('getChat'));

        if (!$chat) {
            $chat = M('Chat')->where(array('status' => 1))->order('id desc')->limit(CHAT_LIMIT_LINES)->select();
            S('getChat', $chat);
        }

        asort($chat);

        if ($chat) {
            foreach ($chat as $k => $v) {
                $data[] = array((int)$v['id'], $v['username'], $v['content']);
            }
        } else {
            $data = '';
        }

        if ($ajax) {
            exit(json_encode($data));
        } else {
            return $data;
        }
    }

    public function upChat($content)
    {
        if (!userid()) {
            $this->error(L('PLEASE_LOGIN'));
        }

        $content = msubstr($content, 0, 20, 'utf-8', false);

        if (!$content) {
            $this->error(L('Please enter content'));
        }

        if (APP_DEMO) {
            $this->error(L('Site is in Demo mode, You can not chat!'));
        }

        if (time() < (session('chat' . userid()) + 10)) {
            $this->error(L('You can not send messages too quickly!'));
        }

        $id = M('Chat')->add(array('userid' => userid(), 'username' => username(), 'content' => $content, 'addtime' => time() + 18000, 'endtime' => time(), 'sort' => '0', 'status' => 1));

        if ($id) {
            S('getChat', null);
            session('chat' . userid(), time());
            $this->success($id);
        } else {
            $this->error(L('Failed to send'));
        }
    }

    public function upcomment($msgaaa, $s1, $s2, $s3, $xnb)
    {
        if (empty($msgaaa)) {
            $this->error(L('Submission error'));
        }

        if (!check($s1, 'd')) {
            $this->error(L('Technical score error'));
        }

        if (!check($s2, 'd')) {
            $this->error(L('App Rating Error'));
        }

        if (!check($s3, 'd')) {
            $this->error(L('Ratings outlook error'));
        }

        if (!userid()) {
            $this->error(L('PLEASE_LOGIN'));
        }

        if (M('CoinComment')->where(array(
            'userid' => userid(),
            'coinname' => $xnb,
            'addtime' => array('gt', time() - 60)
        ))->find()) {
            $this->error(L('Please do not submit often!'));
        }

        if (M('Coin')->where(array('name' => $xnb))->save(array(
            'tp_zs' => array('exp', 'tp_zs+1'),
            'tp_js' => array('exp', 'tp_js+' . $s1),
            'tp_yy' => array('exp', 'tp_yy+' . $s2),
            'tp_qj' => array('exp', 'tp_qj+' . $s3)
        ))) {
            if (M('CoinComment')->add(array('userid' => userid(), 'coinname' => $xnb, 'content' => $msgaaa, 'addtime' => time(), 'status' => 1))) {
                $this->success(L('Submitted successfully'));
            } else {
                $this->error(L('Submission Failed!1'));
            }
        } else {
            $this->error(L('Submission Failed!2'));
        }
    }

    public function subcomment($id, $type)
    {
        if ($type != 1) {
            if ($type != 2) {
                if ($type != 3) {
                    $this->error(L('INCORRECT_REQ'));
                } else {
                    $type = 'xcd';
                }
            } else {
                $type = 'tzy';
            }
        } else {
            $type = 'cjz';
        }

        if (!check($id, 'd')) {
            $this->error('Parameter Error 1');
        }

        if (!userid()) {
            $this->error(L('PLEASE_LOGIN'));
        }

        if (S('subcomment' . userid() . $id)) {
            $this->error(L('Please do not submit often!'));
        }

        if (M('CoinComment')->where(array('id' => $id))->setInc($type, 1)) {
            S('subcomment' . userid() . $id, 1);
            $this->success(L('Submitted successfully'));
        } else {
            $this->error(L('Submission Failed 3!'));
        }
    }
}

?>