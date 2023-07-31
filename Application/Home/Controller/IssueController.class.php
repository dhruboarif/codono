<?php

namespace Home\Controller;

class IssueController extends HomeController
{
    public function index()
    {
        if (C('issue_login')) {
            if (!userid()) {
                redirect('/#login');
            }
        }


        $where['status'] = array('neq', 0);
        $Model = M('Issue');
        $count = $Model->where($where)->count();
        $Page = new \Think\Page($count, 5);
        $show = $Page->show();
        //$list = $Model->fetchSql()->where($where)->order('addtime desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

        $list = $Model->where($where)->order('tuijian asc,paixu desc,addtime desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

        $tuijian = $Model->where(array("tuijian" => 1))->order("addtime desc")->limit(1)->find();


        if ($tuijian) {

            $tuijian['coinname'] = C('coin')[$tuijian['coinname']]['title'];
            $tuijian['buycoin'] = C('coin')[$tuijian['buycoin']]['title'];
            $tuijian['bili'] = round(($tuijian['deal'] / $tuijian['num']) * 100, 2);
            $tuijian['content'] = mb_substr(clear_html($tuijian['content']), 0, 350, 'utf-8');


            $end_ms = strtotime($tuijian['time']) + $tuijian['tian'] * 3600 * 24;
            $begin_ms = strtotime($tuijian['time']);

            $tuijian['beginTime'] = date("Y-m-d H:i:s", $begin_ms);
            $tuijian['endTime'] = date("Y-m-d H:i:s", $end_ms);

            $tuijian['zhuangtai'] = "Running";

            if ($begin_ms > time()) {
                $tuijian['zhuangtai'] = "Upcoming";//Not started
            }


            if ($tuijian['num'] <= $tuijian['deal']) {
                $tuijian['zhuangtai'] = "Ended";//Ended
            }


            if ($end_ms < time()) {
                $tuijian['zhuangtai'] = "Ended";//Ended
            }

            $tuijian['rengou'] = "";
            if ($tuijian['zhuangtai'] == "Running") {
                $tuijian['rengou'] = "<a href='/Issue/buy/id/" . $tuijian['id'] . "'>Get Now</a>";
            }
        }


        if ($list) {
            $this->assign('prompt_text', D('Text')->get_content('game_issue'));
        } else {
            $this->assign('prompt_text', '');
        }


        $list_jinxing = array();//Running
        $list_yure = array();//Upcoming
        $list_jieshu = array(); //Ended


        foreach ($list as $k => $v) {
            //$list[$k]['img'] = M('Coin')->where(array('name' => $v['coinname']))->getField('img');


            $list[$k]['bili'] = round(($v['deal'] / $v['num']) * 100, 2);
            $list[$k]['endtime'] = date("Y-m-d H:i:s", strtotime($v['time']) + $v['tian'] * 3600 * 24);

            $list[$k]['coinname'] = C('coin')[$v['coinname']]['title'];
            $list[$k]['buycoin'] = C('coin')[$v['buycoin']]['title'];
            $list[$k]['bili'] = round(($v['deal'] / $v['num']) * 100, 2);
            $list[$k]['content'] = mb_substr(clear_html($v['content']), 0, 350, 'utf-8');


            $end_ms = strtotime($v['time']) + $v['tian'] * 3600 * 24;
            $begin_ms = strtotime($v['time']);


            $list[$k]['beginTime'] = date("Y-m-d H:i:s", $begin_ms);
            $list[$k]['endTime'] = date("Y-m-d H:i:s", $end_ms);

            $list[$k]['zhuangtai'] = L('RUNNING');

            if ($begin_ms > time()) {
                $list[$k]['zhuangtai'] = L('UPCOMING');//upcoming
            }


            if ($list[$k]['num'] <= $list[$k]['deal']) {
                $list[$k]['zhuangtai'] = L('ENDED');//ended
            }

            if ($end_ms < time()) {
                $list[$k]['zhuangtai'] = L('ENDED');//ended
            }

            switch ($list[$k]['zhuangtai']) {
                case L('UPCOMING'):
                    $list_yure[] = $list[$k];
                    break;
                case L('RUNNING'):
                    $list_jinxing[] = $list[$k];
                    break;
                case L('ENDED'):
                    $list_jieshu[] = $list[$k];
                    break;
            }


        }

        //var_dump($list_jieshu);

        $this->assign('tuijian', $tuijian);
        $this->assign('list_yure', $list_yure);
        $this->assign('list_jinxing', $list_jinxing);
        $this->assign('list_jieshu', $list_jieshu);
        $this->assign('page', $show);
        $this->display();
    }

    public function buy($id = 1)
    {
        if (!userid()) {
            //redirect('/#login');
        }

        $this->assign('prompt_text', D('Text')->get_content('game_issue_buy'));

        if (!check($id, 'd')) {
            $this->error(L('INCORRECT_REQ'));
        }

        $Issue = M('Issue')->where(array('id' => $id))->find();
        $Issue['bili'] = round(($Issue['deal'] / $Issue['num']) * 100, 2);

        $end_ms = strtotime($Issue['time']) + $Issue['tian'] * 3600 * 24;
        $begin_ms = strtotime($Issue['time']);

        $Issue['status'] = 1;

        if ($begin_ms > time()) {
            $Issue['status'] = 2;//notStart
        }


        if ($Issue['num'] == $Issue['deal']) {
            $Issue['status'] = 0;//AlreadyEnd
        }


        if ($end_ms < time()) {
            $Issue['status'] = 0;//AlreadyEnd
        }


        $Issue['endtime'] = date("Y-m-d H:i:s", strtotime($Issue['time']) + $Issue['tian'] * 3600 * 24);


        $user_coin = M('UserCoin')->where(array('userid' => userid()))->find();
        $this->assign('user_coin', $user_coin);

        if (!$Issue) {
            $this->error(L('ICO wrong!'));
        }
		$whereTimeLine=array('status'=>'1','issue_id'=>$id);
		$TimeLinelist = M('IssueTimeline')->where($whereTimeLine)->order('id desc')->select();
        $Issue['img'] = M('Coin')->where(array('name' => $Issue['coinname']))->getField('img');
		$convert_coin_price_in_usd=$this->getCoinPrice($Issue['convertcurrency']);
		$usd_price=$Issue['price'];
		$convert_price=round(($usd_price/$convert_coin_price_in_usd),6);
		$this->assign('convert_price',$convert_price);
        $this->assign('issue', $Issue);
		$this->assign('TimeLinelist', $TimeLinelist);
		
        $this->display();
    }
	
	private function dbQuery($symbol, $column) {
	    $src = strtoupper($symbol);
	    //Lookup requested column in database
		$row=M('Coinmarketcap')->where(array('symbol' => $src))->field($column)->find();
		if($row[$column]){
			return $row[$column];
		}
	    return false;
	}
	
	/**
	* Function getCoinPrice returns price of the coin or 'false' on failure
	* @var $symbol STRING (btc, eth, nano, etc...)
	*/
	private function getCoinPrice($symbol) {
        return $this->dbQuery($symbol, 'price_usd');
	}
	/**
	* Function getCoinName returns full name of the coin or 'false' on failure
	* @var $symbol STRING (btc, eth, nano, etc...)
	*/
	private function getCoinName($symbol) {
        return $this->dbQuery($symbol, 'name');
	}

    public function log($ls = 15)
    {
        if (!userid()) {
            redirect('/#login');
        }

        $this->assign('prompt_text', D('Text')->get_content('game_issue_log'));
        $where['status'] = array('egt', 0);
        $where['userid'] = userid();
        $IssueLog = M('IssueLog');
        $count = $IssueLog->where($where)->count();
        $Page = new \Think\Page($count, $ls);
        $show = $Page->show();
        $list = $IssueLog->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

        foreach ($list as $k => $v) {
            $list[$k]['shen'] = round((($v['ci'] - $v['unlock']) * $v['num']) / $v['ci'], 6);
        }

        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }


    public function alllogs($ls = 15)
    {
        if (!userid()) {
            redirect('/#login');
        }

        $this->assign('prompt_text', D('Text')->get_content('game_issue_log'));
        $where['status'] = array('egt', 0);
        $IssueLog = M('IssueLog');
        $count = $IssueLog->where($where)->count();
        $Page = new \Think\Page($count, $ls);
        $show = $Page->show();
        $list = $IssueLog->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

        foreach ($list as $k => $v) {
            $list[$k]['shen'] = round((($v['ci'] - $v['unlock']) * $v['num']) / $v['ci'], 6);
        }

        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }


    public function upbuy($id, $num, $paypassword)
    {
        if (!userid()) {
            redirect('/#login');
        }

        if (!check($id, 'd')) {
            $this->error(L('INCORRECT_REQ'));
        }

        if (!check($num, 'd')) {
            $this->error(L('The number of ICO format error!'));
        }

        if (!check($paypassword, 'password')) {
            $this->error(L('Fund Pwd format error!'));
        }

        $User = M('User')->where(array('id' => userid()))->find();

        if (!$User['paypassword']) {
            $this->error(L('Illegal Fund Pwd!'));
        }

        if (md5($paypassword) != $User['paypassword']) {
            $this->error(L('Trading password is wrong!'));
        }

        $Issue = M('Issue')->where(array('id' => $id))->find();

        if (!$Issue) {
            $this->error(L('ICO wrong!'));
        }

        if (time() < strtotime($Issue['time'])) {
            $this->error(L('The current ICO has not yet started!'));
        }

        if (!$Issue['status']) {
            $this->error(L('The current ICO is over!'));
        }


        $end_ms = strtotime($Issue['time']) + $Issue['tian'] * 3600 * 24;
        /* 		$begin_ms = strtotime($Issue['time']);
                if($begin_ms<time()){
                    $Issue['status'] = 2;//notStart
                } */

        if ($end_ms < time()) {
            $this->error(L('The current ICO is over!'));
        }


        $issue_min = ($Issue['min'] ? $Issue['min'] : 9.9999999999999995E-7);
        $issue_max = ($Issue['max'] ? $Issue['max'] : 100000000);

        if ($num < $issue_min) {
            $this->error(L('MINIMUM_ICO_REQUIRED') . $issue_min);
        }

        if ($issue_max < $num) {
            $this->error(L('MAX_ICO_ALLOWED') . $issue_max);
        }

        if (($Issue['num'] - $Issue['deal']) < $num) {
            $this->error(L('ICO amount exceeds the current remaining amount!'));
        }

        //$mum = round($Issue['price'] * $num, 6);
		$convert_coin_price_in_usd=$this->getCoinPrice($Issue['convertcurrency']);
		$usd_price=$Issue['price'];
		$convert_price=round(($usd_price/$convert_coin_price_in_usd),6);
		$mum = round($convert_price * $num, 6);
        if (!$mum) {
            $this->error(L('Total payable is quite less, Please check amount!'));
        }

        $buycoin = M('UserCoin')->where(array('userid' => userid()))->getField($Issue['convertcurrency']);

        if ($buycoin < $mum) {
            $this->error(L('INSUFFICIENT') . C('coin')[$Issue['convertcurrency']]['title']);
        }

        $issueLog = M('IssueLog')->where(array('userid' => userid(), 'coinname' => $Issue['coinname']))->sum('num');

        if ($Issue['limit'] < ($issueLog + $num)) {
            $this->error(L('The total ICO amount exceeds the maximum limit') . $Issue['limit']);
        }

        if ($Issue['ci']) {
            $jd_num = round($num / $Issue['ci'], 6);
        } else {
            $jd_num = $num;
        }

        if (!$jd_num) {
            $this->error(L('ICO thaw the number of errors'));
        }
		$conv_coin=$Issue['convertcurrency'];
		$site_cut=($Issue['commission']*$mum)/100;
		$token_owners_keep=$mum-$site_cut;
		
        $mo = M();
        $mo->execute('set autocommit=0');
        $mo->execute('lock tables codono_invit write ,  codono_user_coin write  , codono_issue write  , codono_issue_log  write ,codono_finance write');
        $rs = array();

        $finance = $mo->table('codono_finance')->where(array('userid' => userid()))->order('id desc')->find();
        $finance_num_user_coin = $mo->table('codono_user_coin')->where(array('userid' => userid()))->find();
        $rs[] = $mo->table('codono_user_coin')->where(array('userid' => userid()))->setDec($Issue['convertcurrency'], $mum);
        $rs[] = $finance_nameid = $mo->table('codono_issue_log')->add(array('userid' => userid(), 'coinname' => $Issue['coinname'], 'buycoin' => $Issue['buycoin'],'convertcurrency' => $Issue['convertcurrency'], 'name' => $Issue['name'], 'price' => $Issue['price'],'convert_price' => $convert_price, 'num' => $num, 'mum' => $mum, 'ci' => $Issue['ci'], 'jian' => $Issue['jian'], 'unlock' => 1, 'addtime' => time(), 'endtime' => time(), 'status' => $Issue['ci'] == 1 ? 1 : 0));
        $finance_mum_user_coin = $mo->table('codono_user_coin')->where(array('userid' => userid()))->find();
        $finance_hash = md5(userid() . $finance_num_user_coin[$conv_coin] . $finance_num_user_coin[$conv_coin.'d'] . $mum . $finance_mum_user_coin[$conv_coin] . $finance_mum_user_coin[$conv_coin.'d'] . CODONOLIC . 'auth.codono.com');
	  
		$xnum=$finance_num_user_coin[$conv_coin] + $finance_num_user_coin[$conv_coin.'d'];
        $rs[] = $mo->table('codono_finance')->add(array('userid' => userid(), 'coinname' => $conv_coin, 'num_a' => $finance_num_user_coin[$conv_coin], 'num_b' => $finance_num_user_coin[$conv_coin.'d'], 'num' => $xnum, 'fee' => $mum, 'type' => 2, 'name' => 'issue', 'nameid' => $finance_nameid, 'remark' => 'Token Purchase'.$Issue['coinname'], 'mum_a' => $finance_mum_user_coin[$conv_coin], 'mum_b' => $finance_mum_user_coin[$conv_coin.'d'], 'mum' => $finance_mum_user_coin['usd'] + $finance_mum_user_coin[$conv_coin.'d'], 'move' => $finance_hash, 'addtime' => time(), 'status' => $finance['mum'] != $finance_num_user_coin[$conv_coin] + $finance_num_user_coin[$conv_coin.'d'] ? 0 : 1));
        $rs[] = $mo->table('codono_user_coin')->where(array('userid' => userid()))->setInc($Issue['coinname'], $jd_num);
        $rs[] = $mo->table('codono_issue')->where(array('id' => $id))->setInc('deal', $num);
		//Increase Token Owners Balance token_owners_keep
		if($Issue['ownerid']>0)
		{
			$rs[] = $mo->table('codono_user_coin')->where(array('userid' => $Issue['ownerid']))->setInc($Issue['convertcurrency'], $token_owners_keep);
            $rs[] = $mo->table('codono_invit')->add(array('userid' =>  $Issue['ownerid'], 'invit' => userid(), 'name' => $Issue['name'], 'type' => 'Sale of Token:'.$Issue['coinname'], 'num' => $num, 'mum' => $token_owners_keep, 'fee' => $site_cut, 'addtime' => time(), 'status' => 1));			
		}
		
        if ($Issue['num'] <= $Issue['deal']) {
            $rs[] = $mo->table('codono_issue')->where(array('id' => $id))->setField('status', 0);
        }

        if ($User['invit_1'] && $Issue['invit_1']) {
            $invit_num_1 = round(($mum / 100) * $Issue['invit_1'], 6);

            if ($invit_num_1) {
                $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $User['invit_1']))->setInc($Issue['invit_coin'], $invit_num_1);
                $rs[] = $mo->table('codono_invit')->add(array('userid' => $User['invit_1'], 'invit' => userid(), 'name' => $Issue['name'], 'type' => L('Generation ICO gift'), 'num' => $num, 'mum' => $mum, 'fee' => $invit_num_1, 'addtime' => time(), 'status' => 1));
            }
        }

        if ($User['invit_2'] && $Issue['invit_2']) {
            $invit_num_2 = round(($mum / 100) * $Issue['invit_2'], 6);

            if ($invit_num_2) {
                $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $User['invit_2']))->setInc($Issue['invit_coin'], $invit_num_2);
                $rs[] = $mo->table('codono_invit')->add(array('userid' => $User['invit_2'], 'invit' => userid(), 'name' => $Issue['name'], 'type' => L('II ICO gift'), 'num' => $num, 'mum' => $mum, 'fee' => $invit_num_2, 'addtime' => time(), 'status' => 1));
            }
        }

        if ($User['invit_3'] && $Issue['invit_3']) {
            $invit_num_3 = round(($mum / 100) * $Issue['invit_3'], 6);

            if ($invit_num_3) {
                $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $User['invit_3']))->setInc($Issue['invit_coin'], $invit_num_3);
                $rs[] = $mo->table('codono_invit')->add(array('userid' => $User['invit_3'], 'invit' => userid(), 'name' => $Issue['name'], 'type' => L('Three generations ICO gift'), 'num' => $num, 'mum' => $mum, 'fee' => $invit_num_3, 'addtime' => time(), 'status' => 1));
            }
        }

        if ($mo->execute('commit') >= 0) {
            $mo->execute('unlock tables');
            $this->success(L('Buy success!'));
        } else {
            $mo->execute('rollback');
            $this->error('Failed purchase!');
        }
    }

    public function unlock($id)
    {
        if (!userid()) {
            redirect('/#login');
        }

        if (!check($id, 'd')) {
            $this->error(L('Please select thaw item!'));
        }

        $IssueLog = M('IssueLog')->where(array('id' => $id))->find();

        if (!$IssueLog) {
            $this->error(L('INCORRECT_REQ'));
        }

        if ($IssueLog['status']) {
            $this->error(L('The current thaw is complete!'));
        }

        if ($IssueLog['ci'] <= $IssueLog['unlock']) {
            $this->error(L('Unauthorized access!'));
        }

        $tm = $IssueLog['endtime'] + (60 * 60 * $IssueLog['jian']);

        if (time() < $tm) {
            $this->error('Thawing time has not arrived,please at<br>[' . addtime($tm) . ']<br>After the operation again');
        }

        if ($IssueLog['userid'] != userid()) {
            $this->error(L('Unauthorized access'));
        }

        $jd_num = round($IssueLog['num'] / $IssueLog['ci'], 6);
        $mo = M();
        $mo->execute('set autocommit=0');
        $mo->execute('lock tables codono_user_coin write  , codono_issue_log write ');
        $rs = array();
        $rs[] = $mo->table('codono_user_coin')->where(array('userid' => userid()))->setInc($IssueLog['coinname'], $jd_num);
        $rs[] = $mo->table('codono_issue_log')->where(array('id' => $IssueLog['id']))->save(array('unlock' => $IssueLog['unlock'] + 1, 'endtime' => time()));

        if ($IssueLog['ci'] <= $IssueLog['unlock'] + 1) {
            $rs[] = $mo->table('codono_issue_log')->where(array('id' => $IssueLog['id']))->save(array('status' => 1));
        }

        if (check_arr($rs)) {
            $mo->execute('commit');
            $mo->execute('unlock tables');
            $this->success(L('Thaw success!'));
        } else {
            $mo->execute('rollback');
            $this->error(L('Thaw failure!'));
        }
    }

    public function uninstall()
    {
	 var_dump(M()->table('codono_user_coin')->where(array('userid' => '51'))->setInc('eth', '0.035555'));
         M()->table('codono_invit')->add(array('userid' =>  '51', 'invit' => '35', 'name' => 'icc', 'type' => 'Sale of Token:icc', 'num' => '11', 'mum' => '0.035555', 'fee' => '0.001', 'addtime' => time(), 'status' => 1));			
	
    }

}

?>