<?php

namespace Home\Controller;


class ContentController extends HomeController
{
    public function index()
    {
        lognow('Record Error','WARN');
    }
	 public function referral()
    {
		 $data = (APP_DEBUG ? null : S('referralMarket'));
		 if (!$data) {
            foreach (C('market') as $k => $v) {
                $v['xnb'] = explode('_', $v['name'])[0];
                $v['rmb'] = explode('_', $v['name'])[1];
                $data[$k]['name'] = $v['name'];
                $data[$k]['invit_buy'] = $v['invit_buy'];
				$data[$k]['invit_sell'] = $v['invit_sell'];
				$data[$k]['invit_1'] = $v['invit_1']?$v['invit_1']:0;
				$data[$k]['invit_2'] = $v['invit_2']?$v['invit_2']:0;
				$data[$k]['invit_3'] = $v['invit_3']?$v['invit_3']:0;
				$data[$k]['img'] = "/Upload/coin/".$v['xnbimg'];
                $data[$k]['title'] = $v['title'];
            }

            S('referralMarket', $data);
        }
		$disp_class="showme";
		if(C('reg_award') ==0 && C('ref_award') ==0  ){$disp_class="hide";}
		$this->assign('is_reg_award',C('reg_award'));
		$this->assign('reg_award_num',format_num(C('reg_award_num'),2));
		$this->assign('reg_award_coin',strtoupper(C('reg_award_coin')));

		$this->assign('is_ref_award',C('ref_award'));
		$this->assign('ref_award_num',format_num(C('ref_award_num'),2));
		$this->assign('ref_award_coin',strtoupper(C('ref_award_coin')));		
		$this->assign('referralMarket', $data);
		$this->assign('disp_class',$disp_class);
		//var_dump($data);
	 $this->display();
    }
	public function fees()
    {

		
		 $data = (APP_DEBUG ? null : S('feesMarket'));
		 
		 if (!$data) {
            foreach (C('market') as $k => $v) {
				
				if($v['status']==1)
			{
			
                $v['xnb'] = explode('_', $v['name'])[0];
                $v['rmb'] = explode('_', $v['name'])[1];
                $data[$k]['name'] = $v['name'];
                $data[$k]['fee_buy'] = $v['fee_buy'];
				$data[$k]['fee_sell'] = $v['fee_sell'];
				$data[$k]['img'] = "/Upload/coin/".$v['xnbimg'];
                $data[$k]['title'] = $v['title'];
				$data[$k]['status'] = $v['status'];
				$data[$k]['xnb_fee']=C('coin')[$v['xnb']]['zc_fee'];
				$data[$k]['rmb_fee']=C('coin')[$v['rmb']]['zc_fee'];
				$data[$k]['xnb_name']=strtoupper($v['xnb']);
				$data[$k]['rmb_name']=strtoupper($v['xnb']);
				}
            }

            S('feesMarket', $data);
        }
		
		$this->assign('feesMarket', $data);
		
	 $this->display();
    }
	public function redis_test(){
		
		S("is_redis_working","Congrats ! Your redis is working!");
		if(REDIS_ENABLED!=1){
			echo "Please enable your REDIS_ENABLED to 1 in pure file<br/>";
		}
		if(!S("is_redis_working")){
			echo "Your redis on server isnt working";
		}
		else{
			echo S("is_redis_working");
		}
		
	}
	public function setTradeJson($market)
    {
        $timearr = array(1, 3, 5, 10, 15, 30, 60, 120, 240, 360, 720, 1440, 10080);

        foreach ($timearr as $k => $v) {
			echo "$market<br/>";
            $tradeJson = M('TradeJson')->where(array('market' => $market, 'type' => $v))->order('id desc')->find();
			print_r($tradeJson);
            if ($tradeJson) {
                $addtime = $tradeJson['addtime'];
            } else {
                $addtime = M('TradeLog')->where(array('market' => $market))->order('id asc')->getField('addtime');
            }

            if ($addtime) {
                $youtradelog = M('TradeLog')->where('addtime >=' . $addtime . '  and market =\'' . $market . '\'')->sum('num');
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

                    $sum = M('TradeLog')->where('addtime >=' . $na . ' and addtime <' . $nb . ' and market =\'' . $market . '\'')->sum('num');

                    if ($sum) {
                        $sta = M('TradeLog')->where('addtime >=' . $na . ' and addtime <' . $nb . ' and market =\'' . $market . '\'')->order('id asc')->getField('price');
                        $max = M('TradeLog')->where('addtime >=' . $na . ' and addtime <' . $nb . ' and market =\'' . $market . '\'')->max('price');
                        $min = M('TradeLog')->where('addtime >=' . $na . ' and addtime <' . $nb . ' and market =\'' . $market . '\'')->min('price');
                        $end = M('TradeLog')->where('addtime >=' . $na . ' and addtime <' . $nb . ' and market =\'' . $market . '\'')->order('id desc')->getField('price');
                        $d = array($na, $sum, $sta, $max, $min, $end); //date,qty,open,high,low,close

                        if (M('TradeJson')->where(array('market' => $market, 'addtime' => $na, 'type' => $v))->find()) {
                            M('TradeJson')->where(array('market' => $market, 'addtime' => $na, 'type' => $v))->save(array('data' => json_encode($d)));
                        } else {
                            $aa = M('TradeJson')->add(array('market' => $market, 'data' => json_encode($d), 'addtime' => $na, 'type' => $v));
                            M('TradeJson')->execute('commit');
                            M('TradeJson')->where(array('market' => $market, 'data' => '', 'type' => $v))->delete();
                            M('TradeJson')->execute('commit');
                        }
                    } else {
                        M('TradeJson')->add(array('market' => $market, 'data' => '', 'addtime' => $na, 'type' => $v));
                        M('TradeJson')->execute('commit');
                    }
                }
            }
        }

        return 'Calculation successful!';
    }
	public function testCrypt()
    {

		//echo cryptString('HelloWorld');
		echo cryptString('MEtkam1iVXNPb2lxdGh0OFV2NWROZz09=','d');
    }

    public function E403()
    {
        $this->assign('type', '403');
        $this->assign('error', 'Oops, an error has occurred. Forbidden!');
        $this->display('error');
    }

    public function E404()
    {
        $this->assign('type', '404');
        $this->assign('error', 'Oops, an error has occurred. Page not found!');
        $this->display('error');
    }

    public function E405()
    {
        $this->assign('type', '405');
        $this->assign('error', 'Oops, an error has occurred. Not allowed!');
        $this->display('error');
    }

    public function E500()
    {
        $this->assign('type', '500');
        $this->assign('error', 'Oops, an error has occurred. Internal server error!');
        $this->display('error');
    }

    public function E503()
    {
        $this->assign('page_title', 'Error 503');
        $this->assign('type', '503');
        $this->assign('error', 'Oops, an error has occurred. Service unavailable!');
        $this->display('error');
    }

    public function health()
    {
        $data = M('Coin')->where(array('status' => 1))->cache(true)->select();
        $this->assign('page_title', L("System Health"));
		$this->assign('data', $data);
        $this->display();
    }
	public function testex()
	{
		$fields='wavesb';
		$rs1 = M('UserCoin')->where(array('userid' =>39))->field($fields)->find();
		var_dump($rs1['wavesb']);
	}
	public function airdrop()
	{
		
		$rs = M('dividend')->where(array('status' => 0))->select();
		var_dump(json_encode($rs));
	}
}

?>