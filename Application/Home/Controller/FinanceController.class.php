<?php

namespace Home\Controller;

class FinanceController extends HomeController
{
	const G2FA_REQUIRED_FOR_WITHDRAWAL = 1;  // IF Google2Fa required for withdrawal
	const SHOW_SITE_DIVIDEND = 0;  // IF Google2Fa required for withdrawal
    public function index()
    {
		
        if (!userid()) {
            redirect('/#login');
        }

        $CoinList = M('Coin')->where(array('status' => 1))->select();
        $UserCoin = M('UserCoin')->where(array('userid' => userid()))->find();
        $Market = M('Market')->where(array('status' => 1))->select();

        foreach ($Market as $k => $v) {
            $Market[$v['name']] = $v;
        }

        $usd['zj'] = 0;

        /* 		foreach ($CoinList as $k => $v) {
                    if ($v['name'] == 'usd') {
                        $usd['ky'] = round($UserCoin[$v['name']], 2) * 1;
                        $usd['dj'] = round($UserCoin[$v['name'] . 'd'], 2) * 1;
                        $usd['zj'] = $usd['zj'] + $usd['ky'] + $usd['dj'];
                    }
                    else {
                        if ($Market[$v['name'] . '_usd']['new_price']) {
                            $jia = $Market[$v['name'] . '_usd']['new_price'];
                        }
                        else {
                            $jia = 1;
                        }
        
                        $coinList[$v['name']] = array('name' => $v['name'], 'img' => $v['img'], 'title' => $v['title'] . '(' . strtoupper($v['name']) . ')', 'xnb' => round($UserCoin[$v['name']], 6) * 1, 'xnbd' => round($UserCoin[$v['name'] . 'd'], 6) * 1, 'xnbz' => round($UserCoin[$v['name']] + $UserCoin[$v['name'] . 'd'], 6), 'jia' => $jia * 1, 'zhehe' => round(($UserCoin[$v['name']] + $UserCoin[$v['name'] . 'd']) * $jia, 2));
                        $usd['zj'] = round($usd['zj'] + (($UserCoin[$v['name']] + $UserCoin[$v['name'] . 'd']) * $jia), 2) * 1;
                    }
                } */

        //20170514Modify the statistics by type


        foreach ($CoinList as $k => $v) {


            if ($v['name'] == 'usd') {
                $usd['ky'] = round($UserCoin[$v['name']], 2) * 1;
                $usd['dj'] = round($UserCoin[$v['name'] . 'd'], 2) * 1;
                $usd['zj'] = $usd['zj'] + $usd['ky'] + $usd['dj'];
            } else {

                if ($Market[C('market_type')[$v['name']]]['new_price']) {
                    $jia = $Market[C('market_type')[$v['name']]]['new_price'];
                    //echo $jia;
                } else {
                    $jia = 1;
                }
                //OpenmarketWhendisplaycorrespondingcurrency
               // if (in_array($v['name'], C('coin_on'))) 
			   {
                    $coinList[$v['name']] = array('name' => $v['name'], 'img' => $v['img'], 'title' => $v['title'] . '(' . strtoupper($v['name']) . ')', 'xnb' => round($UserCoin[$v['name']], 6) * 1, 'xnbd' => round($UserCoin[$v['name'] . 'd'], 6) * 1, 'xnbz' => round($UserCoin[$v['name']] + $UserCoin[$v['name'] . 'd'], 6), 'jia' => $jia * 1, 'zhehe' => round(($UserCoin[$v['name']] + $UserCoin[$v['name'] . 'd']) * $jia, 2));
                }
                $usd['zj'] = round($usd['zj'] + (($UserCoin[$v['name']] + $UserCoin[$v['name'] . 'd']) * $jia), 2) * 1;
            }
        }
        $this->assign('usd', $usd);
        $this->assign('coinList', $coinList);
        $this->assign('prompt_text', D('Text')->get_content('finance_index'));
        $this->display();
    }

    public function fhindex()
    {
		$this->assign('show_dividend',self::SHOW_SITE_DIVIDEND);
		if(self::SHOW_SITE_DIVIDEND==0)
		{
			die();
		}
        if (!userid()) {
            redirect('/#login');
        }

        $this->assign('prompt_text', D('Text')->get_content('game_dividend'));
        $coin_list = D('Coin')->get_all_xnb_list_allow();

        foreach ($coin_list as $k => $v) {
            $list[$k]['img'] = D('Coin')->get_img($k);
            $list[$k]['title'] = $v;
            $list[$k]['quanbu'] = D('Coin')->get_sum_coin($k);
            $list[$k]['wodi'] = D('Coin')->get_sum_coin($k, userid());
            $list[$k]['bili'] = round(($list[$k]['wodi'] / $list[$k]['quanbu']) * 100, 2) . '%';
        }

        $this->assign('list', $list);
        $this->display();
    }


    public function myfhroebx()
    {
        if (!userid()) {
            redirect('/#login');
        }

        $this->assign('prompt_text', D('Text')->get_content('game_dividend_log'));
        $where['userid'] = userid();
        $Model = M('DividendLog');
        $count = $Model->where($where)->count();
        $Page = new \Think\Page($count, 15);
        $show = $Page->show();
        $list = $Model->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }


    public function bank()
    {
        if (!userid()) {
            redirect('/#login');
        }


        $UserBankType = M('UserBankType')->where(array('status' => 1))->order('id desc')->select();
        $this->assign('UserBankType', $UserBankType);


        //$truename = M('User')->where(array('id' => userid()))->getField('truename');
        $user = M('User')->where(array('id' => userid()))->find();

        if ($user['idcardauth'] == 0 && KYC_OPTIONAL == 0) {
            redirect('/user/nameauth');
        }

        $truename = $user['truename'];
        $this->assign('truename', $truename);
        //$UserBank = M('UserBank')->where(array('userid' => userid(), 'status' => 1))->order('id desc')->limit(1)->select();
        $UserBank = M('UserBank')->where(array('userid' => userid(), 'status' => 1))->order('id desc')->select();

        $this->assign('UserBank', $UserBank);
        $this->assign('prompt_text', D('Text')->get_content('user_bank'));
        $this->display();
    }


    public function upbank($name, $bank, $bankprov, $bankcity, $bankaddr, $bankcard, $paypassword)
    {
        if (!userid()) {
            redirect('/#login');
        }

        if (!check($name, 'a')) {
            $this->error(L('Note name of the wrong format!'));
        }

        if (!check($bank, 'a')) {
            $this->error(L('Bank malformed!'));
        }

        if (!check($bankprov, 'c')) {
            $this->error(L('Opening provinces format error!'));
        }

        if (!check($bankcity, 'c')) {
            $this->error('Format of the city is wrong!');
        }

        if (!check($bankaddr, 'a')) {
            $this->error(L('Bank address format error!'));
        }

        if (!check($bankcard, 'a')) {
            $this->error(L('Bank account number format error!'));
        }

        if (strlen($bankcard) < 4 || strlen($bankcard) > 50) {

            $this->error(L('Bank account number format error!'));

        }


        if (!check($paypassword, 'password')) {
            $this->error(L('Fund Pwd format error!'));
        }

        $user_paypassword = M('User')->where(array('id' => userid()))->getField('paypassword');

        if (md5($paypassword) != $user_paypassword) {
            $this->error(L('Trading password is wrong!'));
        }

        if (!M('UserBankType')->where(array('title' => $bank))->find()) {
            $this->error(L('Bank error!'));
        }

        $userBank = M('UserBank')->where(array('userid' => userid()))->select();

        foreach ($userBank as $k => $v) {
            if ($v['name'] == $name) {
                $this->error(L('Please do not use the same name Notes!'));
            }

            if ($v['bankcard'] == $bankcard) {
                $this->error(L('Bank card number already exists!'));
            }
        }

        if (10 <= count($userBank)) {
            $this->error('Each user can add upto 10 accounts only!');
        }

        if (M('UserBank')->add(array('userid' => userid(), 'name' => $name, 'bank' => $bank, 'bankprov' => $bankprov, 'bankcity' => $bankcity, 'bankaddr' => $bankaddr, 'bankcard' => $bankcard, 'addtime' => time(), 'status' => 1))) {
            $this->success(L('Banks added successfully!'));
        } else {
            $this->error(L('Bank Add Failed!'));
        }
    }

    public function delbank($id, $paypassword)
    {

        if (!userid()) {
            redirect('/#login');
        }

        if (!check($paypassword, 'password')) {
            $this->error(L('Fund Pwd format error!'));
        }

        if (!check($id, 'd')) {
            $this->error(L('INCORRECT_REQ'));
        }

        $user_paypassword = M('User')->where(array('id' => userid()))->getField('paypassword');

        if (md5($paypassword) != $user_paypassword) {
            $this->error(L('Trading password is wrong!'));
        }

        if (!M('UserBank')->where(array('userid' => userid(), 'id' => $id))->find()) {
            $this->error(L('Unauthorized access!'));
        } else if (M('UserBank')->where(array('userid' => userid(), 'id' => $id))->delete()) {
            $this->success(L('successfully deleted!'));
        } else {
            $this->error(L('failed to delete!'));
        }
    }


    public function mycz($status = NULL)
    {
        if (!userid()) {
            redirect('/#login');
        }

        $this->assign('prompt_text', D('Text')->get_content('finance_mycz'));
        $myczType = M('MyczType')->where(array('status' => 1))->select();

        foreach ($myczType as $k => $v) {
            $myczTypeList[$v['name']] = $v['title'];
        }

        $this->assign('myczTypeList', $myczTypeList);
        $user_coin = M('UserCoin')->where(array('userid' => userid()))->find();
        $user_coin['usd'] = round($user_coin['usd'], 2);
        $user_coin['usdd'] = round($user_coin['usdd'], 2);
        $this->assign('user_coin', $user_coin);

        if (($status == 1) || ($status == 2) || ($status == 3) || ($status == 4)) {
            $where['status'] = $status - 1;
        }

        $this->assign('status', $status);
        $where['userid'] = userid();
        $count = M('Mycz')->where($where)->count();
        $Page = new \Think\Page($count, 15);
        $show = $Page->show();
        $list = M('Mycz')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

        foreach ($list as $k => $v) {
            $list[$k]['type'] = M('MyczType')->where(array('name' => $v['type']))->getField('title');
            $list[$k]['typeEn'] = $v['type'];
            $list[$k]['num'] = (Num($v['num']) ? Num($v['num']) : '');
            $list[$k]['mum'] = (Num($v['mum']) ? Num($v['mum']) : '');
        }
		
        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }

    public function myczRemittance($id = NULL)
    {
        if (!userid()) {
            $this->error(L('PLEASE_LOGIN'));
        }

        if (!check($id, 'd')) {
            $this->error(L('INCORRECT_REQ'));
        }

        $mycz = M('Mycz')->where(array('id' => $id))->find();

        if (!$mycz) {
            $this->error(L('Top-order does not exist!'));
        }

        if ($mycz['userid'] != userid()) {
            $this->error(L('Illegal operation!'));
        }

        if ($mycz['status'] != 0) {
            $this->error(L('Order has been processed!'));
        }

        $rs = M('Mycz')->where(array('id' => $id))->save(array('status' => 3));

        if ($rs) {
            $this->success(L('Successful operation'));
        } else {
            $this->error(L('OPERATION_FAILED'));
        }
    }

    public function myczChakan($id = NULL)
    {
        if (!userid()) {
            $this->error(L('PLEASE_LOGIN'));
        }

        if (!check($id, 'd')) {
            $this->error(L('INCORRECT_REQ'));
        }

        $mycz = M('Mycz')->where(array('id' => $id))->find();

        if (!$mycz) {
            $this->error(L('Top-order does not exist!'));
        }

        if ($mycz['userid'] != userid()) {
            $this->error(L('Illegal operation!'));
        }

        if ($mycz['status'] != 0) {
            $this->error(L('Order has been processed!'));
        }

        $rs = M('Mycz')->where(array('id' => $id))->save(array('status' => 3));

        if ($rs) {
            $this->success('', array('id' => $id));
        } else {
            $this->error(L('OPERATION_FAILED'));
        }
    }

    public function myczUp($type, $num)
    {
        if (!userid()) {
            $this->error('Please Login!');
        }
		

        if (!check($type, 'n')) {
            $this->error(L('Recharge way malformed!'));
        }

        if (!check($num, 'usd')) {
            $this->error(L('Recharge amount malformed!'));
        }

        $myczType = M('MyczType')->where(array('name' => $type))->find();

        if (!$myczType) {
            $this->error(L('There is no way to recharge!'));
        }

        if ($myczType['status'] != 1) {
            $this->error(L('There is no way to recharge open!'));
        }

        $mycz_min = ($myczType['min'] ? $myczType['min'] : 1);
        $mycz_max = ($myczType['max'] ? $myczType['max'] : 100000);

        if ($num < $mycz_min) {
            $this->error(L('Recharge amount can not be less than') . $mycz_min . ' '.L('USD'));
        }

        if ($mycz_max < $num) {
            $this->error(L('Recharge amount can not exceed') . $mycz_max . '  '.L('USD'));
        }

		
        for (; true;) {
            $tradeno = tradeno();

            if (!M('Mycz')->where(array('tradeno' => $tradeno))->find()) {
                break;
            }
        }

        $mycz = M('Mycz')->add(array('userid' => userid(), 'num' => $num, 'type' => $type, 'tradeno' => $tradeno, 'addtime' => time(), 'status' => 0));

        if ($mycz) {
            $this->success(L('Prepaid orders created successfully!'), array('id' => $mycz));
        } else {
            $this->error(L('Withdraw order creation failed!'));
        }
    }


    public function outlog($status = NULL)
    {

        if (!userid()) {
            redirect('/#login');
        }

        $this->assign('prompt_text', D('Text')->get_content('finance_mytx'));


        if (($status == 1) || ($status == 2) || ($status == 3) || ($status == 4)) {
            $where['status'] = $status - 1;
        }
        $where['userid'] = userid();
        $count = M('Mytx')->where($where)->count();
        $Page = new \Think\Page($count, 15);
        $show = $Page->show();
        $list = M('Mytx')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

        foreach ($list as $k => $v) {
            $list[$k]['num'] = (Num($v['num']) ? Num($v['num']) : '');
            $list[$k]['fee'] = (Num($v['fee']) ? Num($v['fee']) : '');
            $list[$k]['mum'] = (Num($v['mum']) ? Num($v['mum']) : '');
        }
        $this->assign('status', $status);
        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();

    }


    public function mytx($status = NULL)
    {
        if (!userid()) {
            redirect('/#login');
        }

        $this->assign('prompt_text', D('Text')->get_content('finance_mytx'));
        $cellphone = M('User')->where(array('id' => userid()))->getField('cellphone');
        $email = M('User')->where(array('id' => userid()))->getField('email');
        
		if ($cellphone || $email) {
            $cellphone = substr_replace($cellphone, '****', 3, 4);
            $email = substr_replace($email, '****', 3, 4);
        } else {
			if(M_ONLY==1){        
			redirect(U('Home/User/cellphone'), $time = 5, $msg = L('Please Verify your Phone!'));
			}
        }

        $this->assign('cellphone', $cellphone);
        $this->assign('email', $email);
        $user_coin = M('UserCoin')->where(array('userid' => userid()))->find();
        $user_coin['usd'] = round($user_coin['usd'], 2);
        $user_coin['usdd'] = round($user_coin['usdd'], 2);
        $this->assign('user_coin', $user_coin);
        $userBankList = M('UserBank')->where(array('userid' => userid(), 'status' => 1))->order('id desc')->limit(10)->select();
        $this->assign('userBankList', $userBankList);

        if (($status == 1) || ($status == 2) || ($status == 3) || ($status == 4)) {
            $where['status'] = $status - 1;
        }

        $this->assign('status', $status);
        $where['userid'] = userid();
        $count = M('Mytx')->where($where)->count();
        $Page = new \Think\Page($count, 15);
        $show = $Page->show();
        $list = M('Mytx')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

        foreach ($list as $k => $v) {
            $list[$k]['num'] = (Num($v['num']) ? Num($v['num']) : '');
            $list[$k]['fee'] = (Num($v['fee']) ? Num($v['fee']) : '');
            $list[$k]['mum'] = (Num($v['mum']) ? Num($v['mum']) : '');
        }

        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }

    public function mytxUp($cellphone_verify=0, $num, $paypassword, $type)
    {
        if (!userid()) {
            $this->error(L('PLEASE_LOGIN'));
        }
		if(M_ONLY==1){
        if (!check($cellphone_verify, 'd')) {
            $this->error(L('INVALID_SMS_CODE'));
        }
		}

        if (!check($num, 'd')) {
            $this->error(L('The amount of withdrawals format error!'));
        }

        if (!check($paypassword, 'password')) {
            $this->error(L('Fund Pwd format error!'));
        }

        if (!check($type, 'd')) {
            $this->error(L('Withdraw way malformed!'));
        }
		if(M_ONLY==1){
        if ($cellphone_verify != session('mytx_verify')) {
            $this->error(L('INCORRECT_SMS_CODE'));
        }
		}
        $userCoin = M('UserCoin')->where(array('userid' => userid()))->find();

        if ($userCoin['usd'] < $num) {
            $this->error(L('Lack of available Balance!'));
        }

        $user = M('User')->where(array('id' => userid()))->find();

        if (md5($paypassword) != $user['paypassword']) {
            $this->error(L('Trading password is wrong!'));
        }

        $userBank = M('UserBank')->where(array('id' => $type))->find();

        if (!$userBank) {
            $this->error(L('Withdraw wrong address!'));
        }

        $mytx_min = (C('mytx_min') ? C('mytx_min') : 1);
        $mytx_max = (C('mytx_max') ? C('mytx_max') : 1000000);
        $mytx_bei = C('mytx_bei');
        $mytx_fee = C('mytx_fee');

        if ($num < $mytx_min) {
            $this->error(L('Every withdrawal amount can not be less than') . $mytx_min . '  '.L('USD'));
        }

        if ($mytx_max < $num) {
            $this->error(L('Every withdrawal amount can not exceed') . $mytx_max . '  '.L('USD'));
        }

        if ($mytx_bei) {
            if ($num % $mytx_bei != 0) {
                $this->error(L('Every mention the amount of cash must be') . $mytx_bei . L('Integral multiples!'));
            }
        }

        $fee = round(($num / 100) * $mytx_fee, 2);
        $mum = round(($num / 100) * (100 - $mytx_fee), 2);
        $mo = M();
        $mo->execute('set autocommit=0');
        $mo->execute('lock tables codono_mytx write , codono_user_coin write ,codono_finance write');
        $rs = array();
        $finance = $mo->table('codono_finance')->where(array('userid' => userid()))->order('id desc')->find();
        $finance_num_user_coin = $mo->table('codono_user_coin')->where(array('userid' => userid()))->find();
        $rs[] = $mo->table('codono_user_coin')->where(array('userid' => userid()))->setDec('usd', $num);
        $rs[] = $finance_nameid = $mo->table('codono_mytx')->add(array('userid' => userid(), 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'name' => $userBank['name'], 'truename' => $user['truename'], 'bank' => $userBank['bank'], 'bankprov' => $userBank['bankprov'], 'bankcity' => $userBank['bankcity'], 'bankaddr' => $userBank['bankaddr'], 'bankcard' => $userBank['bankcard'], 'addtime' => time(), 'status' => 0));
        $finance_mum_user_coin = $mo->table('codono_user_coin')->where(array('userid' => userid()))->find();
        $finance_hash = md5(userid() . $finance_num_user_coin['usd'] . $finance_num_user_coin['usdd'] . $mum . $finance_mum_user_coin['usd'] . $finance_mum_user_coin['usdd'] . CODONOLIC . 'auth.codono.com');
        $finance_num = $finance_num_user_coin['usd'] + $finance_num_user_coin['usdd'];

        if ($finance['mum'] < $finance_num) {
            $finance_status = (1 < ($finance_num - $finance['mum']) ? 0 : 1);
        } else {
            $finance_status = (1 < ($finance['mum'] - $finance_num) ? 0 : 1);
        }

        $rs[] = $mo->table('codono_finance')->add(array('userid' => userid(), 'coinname' => 'usd', 'num_a' => $finance_num_user_coin['usd'], 'num_b' => $finance_num_user_coin['usdd'], 'num' => $finance_num_user_coin['usd'] + $finance_num_user_coin['usdd'], 'fee' => $num, 'type' => 2, 'name' => 'mytx', 'nameid' => $finance_nameid, 'remark' => 'Fiat Withdrawal-Apply to cash', 'mum_a' => $finance_mum_user_coin['usd'], 'mum_b' => $finance_mum_user_coin['usdd'], 'mum' => $finance_mum_user_coin['usd'] + $finance_mum_user_coin['usdd'], 'move' => $finance_hash, 'addtime' => time(), 'status' => $finance_status));

        if (check_arr($rs)) {
            session('mytx_verify', null);
            $mo->execute('commit');
            $mo->execute('unlock tables');
            $this->success(L('Withdrawal order to create success!'));
        } else {
            $mo->execute('rollback');
            $this->error(L('Withdraw order creation failed!'));
        }
    }

    public function mytxReject($id)
    {
        if (!userid()) {
            $this->error(L('PLEASE_LOGIN'));
        }

        if (!check($id, 'd')) {
            $this->error(L('INCORRECT_REQ'));
        }

        $mytx = M('Mytx')->where(array('id' => $id))->find();

        if (!$mytx) {
            $this->error(L('Withdraw order does not exist!'));
        }

        if ($mytx['userid'] != userid()) {
            $this->error(L('Illegal operation!'));
        }

        if ($mytx['status'] != 0) {
            $this->error(L('Orders can not be undone!'));
        }

        $mo = M();
        $mo->execute('set autocommit=0');
        $mo->execute('lock tables codono_user_coin write,codono_mytx write,codono_finance write');
        $rs = array();
        $finance = $mo->table('codono_finance')->where(array('userid' => $mytx['userid']))->order('id desc')->find();
        $finance_num_user_coin = $mo->table('codono_user_coin')->where(array('userid' => $mytx['userid']))->find();
        $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $mytx['userid']))->setInc('usd', $mytx['num']);
        $rs[] = $mo->table('codono_mytx')->where(array('id' => $mytx['id']))->setField('status', 2);
        $finance_mum_user_coin = $mo->table('codono_user_coin')->where(array('userid' => $mytx['userid']))->find();
        $finance_hash = md5($mytx['userid'] . $finance_num_user_coin['usd'] . $finance_num_user_coin['usdd'] . $mytx['num'] . $finance_mum_user_coin['usd'] . $finance_mum_user_coin['usdd'] . CODONOLIC . 'auth.codono.com');
        $finance_num = $finance_num_user_coin['usd'] + $finance_num_user_coin['usdd'];

        if ($finance['mum'] < $finance_num) {
            $finance_status = (1 < ($finance_num - $finance['mum']) ? 0 : 1);
        } else {
            $finance_status = (1 < ($finance['mum'] - $finance_num) ? 0 : 1);
        }

        $rs[] = $mo->table('codono_finance')->add(array('userid' => $mytx['userid'], 'coinname' => 'usd', 'num_a' => $finance_num_user_coin['usd'], 'num_b' => $finance_num_user_coin['usdd'], 'num' => $finance_num_user_coin['usd'] + $finance_num_user_coin['usdd'], 'fee' => $mytx['num'], 'type' => 1, 'name' => 'mytx', 'nameid' => $mytx['id'], 'remark' => 'Fiat Withdrawal-Undo withdrawals', 'mum_a' => $finance_mum_user_coin['usd'], 'mum_b' => $finance_mum_user_coin['usdd'], 'mum' => $finance_mum_user_coin['usd'] + $finance_mum_user_coin['usdd'], 'move' => $finance_hash, 'addtime' => time(), 'status' => $finance_status));

        if (check_arr($rs)) {
            $mo->execute('commit');
            $mo->execute('unlock tables');
            $this->success(L('SUCCESSFULLY_DONE'));
        } else {
            $mo->execute('rollback');
            $this->error(L('OPERATION_FAILED'));
        }
    }

    public function myzr($coin = NULL)
    {
        if (!userid()) {
            redirect('/#login');
        }
		$show_qr=1;
        $this->assign('prompt_text', D('Text')->get_content('finance_myzr'));

        if (C('coin')[$coin]) {
            $coin = trim($coin);
        } else {
            $coin = C('xnb_mr');
        }

        $this->assign('xnb', $coin);
        $Coin = M('Coin')->where(array(
            'status' => 1,
            'name' => array('neq', 'usd')
        ))->select();

        foreach ($Coin as $k => $v) {
            $coin_list[$v['name']] = $v;
        }

        $this->assign('coin_list', $coin_list);
        $user_coin = M('UserCoin')->where(array('userid' => userid()))->find();
        $user_coin[$coin] = round($user_coin[$coin], 6);
        $this->assign('user_coin', $user_coin);
        $Coin = M('Coin')->where(array('name' => $coin))->find();
        $this->assign('zr_jz', $Coin['zr_jz']);


        $codono_getCoreConfig = codono_getCoreConfig();
        if (!$codono_getCoreConfig) {
            $this->error(L('Incorrect Core Config'));
        }

        $this->assign("codono_opencoin", $codono_getCoreConfig['codono_opencoin']);

        if ($codono_getCoreConfig['codono_opencoin'] == 1 && $Coin['type'] != 'offline') {
		    if (!$Coin['zr_jz']) {
                $wallet = L('The current ban into the currency!');
            } else {
                $qbdz = $coin . 'b';

                if (!$user_coin[$qbdz]) {

                    if ($Coin['type'] == 'rgb') {
                        $wallet = md5(username() . $coin);

                        $rs = M('UserCoin')->where(array('userid' => userid()))->save(array($qbdz => $wallet));
                        $user_exists = M('User')->where(array('id' => userid()))->getField('id');

                        if (!$rs && !$user_exists) {
                            $this->error(L('Generate wallet address wrong!'));
                        }
                        //sdie($qbdz);
                        if (!$rs && $user_exists) {

                            $ucoin[$qbdz] = $wallet;
                            $ucoin['userid'] = $user_exists;
                            $new_rs = M('UserCoin')->add($ucoin);

                        }

                    }

					if ($Coin['type'] == 'eth') {
                $heyue = $Coin['dj_yh'];//Contract Address
                $dj_password = $Coin['dj_mm'];
                $dj_address = $Coin['dj_zj'];
                $dj_port = $Coin['dj_dk'];
                        $EthCommon = new \Org\Util\EthCommon($dj_address, $dj_port, "2.0");
                        $EthPayLocal = new \Org\Util\EthPayLocal($dj_address, $dj_port, "2.0", $heyue);
						
                        if (!$heyue) {
                            //eth
                            //Call the interface to generate a new wallet address
							$wall_pass= ETH_USER_PASS;//cryptString($user_coin[$coin.'_pass'],'d');
                            $wallet = $EthPayLocal->personal_newAccount($wall_pass);
							
                            if ($wallet) {
								$saveme[$qbdz]=$wallet;
								$saveme[$coin.'_pass']=cryptString($wall_pass);
								$rs = M('UserCoin')->where(array('userid' => userid()))->save($saveme);
								
                            } else {
                                $wallet=L('Wallet System is currently offline 2! '.$coin);
								$show_qr=0;
                            }
                        } else {
                            //Eth contract
                            $rs1 = M('UserCoin')->where(array('userid' => userid()))->find();
                            if ($rs1['ethb']) {
                                $wallet = $rs1['ethb'];
								$saveme[$qbdz]=$wallet;
								$saveme[$coin.'_pass']=cryptString(ETH_USER_PASS);
								$rs = M('UserCoin')->where(array('userid' => userid()))->save($saveme);
								
                            } else {
                                //Call the interface to generate a new wallet address
								$wall_pass=ETH_USER_PASS;
								$wallet = $EthPayLocal->personal_newAccount($wall_pass);
                                if ($wallet) {
								$saveme[$qbdz]=$wallet;
								$saveme[$coin.'_pass']=cryptString($wall_pass);
								$saveme["ethb"] = $wallet;
                                    $rs = M('UserCoin')->where(array('userid' => userid()))->save($saveme);
                                } else {
								$wallet=L('Wallet System is currently offline 1! '.$coin);
								$show_qr=0;
                                }

                            }
                        }

                    }
                    //eth  Ends
					
				//CoinPayments starts	
				if ($Coin['type'] == 'coinpay') {
					
                        $dj_username = $Coin['dj_yh'];
                        $dj_password = $Coin['dj_mm'];
                        $dj_address = $Coin['dj_zj'];
                        $dj_port = $Coin['dj_dk'];
						
                        $cps_api = CoinPay($dj_username, $dj_password, $dj_address, $dj_port, 5, array(), 1);
						$information = $cps_api->GetBasicInfo();
						$coinpay_coin=strtoupper($coin);
             			 

						if($information['error']!='ok' || !isset($information['result']['username'])){
							$wallet=L('Wallet System is currently offline 1!');
							$show_qr=0;
                        }else{
							$show_qr=1;
                        
						$ipn_url = SITE_URL.'IPN/confirm';
						$transaction_response = $cps_api->GetCallbackAddressWithIpn($coinpay_coin, $ipn_url);
						$dest_tag=$transaction_response['result']['dest_tag']?$transaction_response['result']['dest_tag']:0;
						
						$wallet_addr=$transaction_response['result']['address'];
                        if (!is_array($wallet_addr)) {
								$wallet_ad=$wallet_addr;
                            if (!$wallet_ad) {
								$wallet=$wallet_addr;
                            } else {
                                $wallet = $wallet_ad;
                            }
                        } else {
                            $wallet = $wallet_addr[0];
                        }

                        if (!$wallet) {
                            $this->error('Generate Wallet address error2!');
                        }
					$dest_tag_field=$coin.'_tag';	
					$coinpay_update_array[$qbdz]=$wallet;
					
					$tag_sql="SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = 'codono_user_coin' AND column_name = '$dest_tag_field'";
						$if_tag_exists = M()->execute($tag_sql);
						
						//Create a destination tag
					if(!$if_tag_exists){
						
						M()->execute("ALTER TABLE `codono_user_coin` ADD $dest_tag_field VARCHAR(200) NULL DEFAULT NULL COMMENT 'Tag for xrp,xmr'");
					}
				
						if($dest_tag!=0 || $dest_tag!=NULL){
					$coinpay_update_array[$dest_tag_field]=strval($dest_tag);
					//$dtag_sql='UPDATE `codono_user_coin` SET `'.$dest_tag_field.'` = '.$dest_tag.' WHERE `codono_user_coin`.`userid` = '.userid();
					$dtag_sql='UPDATE `codono_user_coin` SET `'.$dest_tag_field.'` = "'.$dest_tag.'" WHERE `codono_user_coin`.`userid` = '.userid();
					$rs = M('UserCoin')->execute($dtag_sql);
					}
					
					$rs = M('UserCoin')->where(array('userid' => userid()))->save($coinpay_update_array);

                        if (!$rs) {
                            $this->error('Add error address wallet3!');
                        }
						}
                    

                    }
                    //CoinPayments  Ends
					//WavesPlatform Starts
					
						if ($Coin['type'] == 'waves') {
							
						$qbdz='wavesb';
                        $dj_username = $Coin['dj_yh'];
                        $dj_password = $Coin['dj_mm'];
                        $dj_address = $Coin['dj_zj'];
                        $dj_port = $Coin['dj_dk'];
                        $dj_decimal = $Coin['cs_qk'];
                        $waves = WavesClient($dj_username, $dj_password, $dj_address, $dj_port,$dj_decimal, 5, array(), 1);
                        $waves_coin=strtoupper($coin);
                        $information= json_decode($waves->status(),true);
						
						if($information['blockchainHeight'] && $information['blockchainHeight']<=0){
						$wallet=L('Wallet System is currently offline 1!');
						$show_qr=0;
						}else{
                         $show_qr=1;
						 $rs1 = M('UserCoin')->where(array('userid' => userid()))->find();
                         if ($rs1['wavesb']) {
						$waves_good=0;
                         $wallet_addr = $rs1['wavesb'];
						 }else
						 {
						$waves_good=1;	 
                        $transaction_response = $address=json_decode( $waves->CreateAddress(),true);
                        $wallet_addr=$transaction_response['address'];
						
						 }
						 
                        if (!is_array($wallet_addr)) {
                                $wallet_ad=$wallet_addr;
                            if (!$wallet_ad) {
                                $wallet=$wallet_addr;
                            } else {
                                $wallet = $wallet_ad;
                            }
                        } else {
                            $wallet = $wallet_addr[0];
                        }

                        if (!$wallet) {
							$show_qr=0;
                            $wallet=L('Wallet System is currently offline 2!');
                        }
						if($show_qr==1){	
							$rs = M('UserCoin')->where(array('userid' => userid()))->save(array($qbdz => $wallet));
							if (!$rs && $waves_good==1) {
                               $wallet=L('Wallet System is currently offline 3!');
							}
						}
                        }
                    

                    }
                 //WavesPlatform Ends
				//blockio starts	
				if ($Coin['type'] == 'blockio') {
					
                        $dj_username = $Coin['dj_yh'];
                        $dj_password = $Coin['dj_mm'];
                        $dj_address = $Coin['dj_zj'];
                        $dj_port = $Coin['dj_dk'];
						
                        $block_io = BlockIO($dj_username, $dj_password, $dj_address, $dj_port, 5, array(), 1);
             			 $json = $block_io->get_balance();

						if (!isset($json->status) || $json->status!='success') {
                            //$this->error(L('Wallet link failure! 1'));
							$wallet=L('Wallet System is currently offline 2!');
							$show_qr=0;
                        }else{
							$show_qr=1;
                        $wallet_addr = $block_io->get_address_by_label(array('label' => username()))->data->address;
						
                        if (!is_array($wallet_addr)) {
							    $getNewAddressInfo = $block_io->get_new_address(array('label' => username()));
								$wallet_ad=$getNewAddressInfo->data->address;
							

                            if (!$wallet_ad) {
                                //$this->error('Generate Wallet address error1!');
								//$this->error($wallet_addr.'ok');
								$wallet=$wallet_addr;
                            } else {
                                $wallet = $wallet_ad;
                            }
                        } else {
                            $wallet = $wallet_addr[0];
                        }

                        if (!$wallet) {
                            $this->error('Generate Wallet address error2!');
                        }

                        $rs = M('UserCoin')->where(array('userid' => userid()))->save(array($qbdz => $wallet));

                        if (!$rs) {
                            $this->error('Add error address wallet3!');
                        }
						}
                    

                    }
                    //blockio  Ends
					
									//cryptonote starts	
				if ($Coin['type'] == 'cryptonote') {
                        $dj_username = $Coin['dj_yh'];
                        $dj_password = $Coin['dj_mm'];
                        $dj_address = $Coin['dj_zj'];
                        $dj_port = $Coin['dj_dk'];
						$cryptonote = CryptoNote($dj_address, $dj_port);
						$open_wallet = $cryptonote->open_wallet($dj_username,$dj_password);
						$json=json_decode($cryptonote->get_height());
						if (!isset($json->height) || $json->error!=0) {
						$wallet=L('Wallet System is currently offline 2!');
							$show_qr=0;
                        }else{
							$show_qr=1;
							$cryptofields=$coin.'b';	
							$cryptonote_rs1 = M('UserCoin')->where(array('userid' =>39))->field($cryptofields)->find();
							$wallet_addr = $cryptonote_rs1[$cryptofields];
							if (!is_array($wallet_addr)) {
							    $getNewAddressInfo = json_decode($cryptonote->create_address(0,username()));
								$wallet_ad=$getNewAddressInfo->address;
							

								if (!$wallet_ad) {
								$wallet=$wallet_addr;
								} else {
                                $wallet = $wallet_ad;
								}
							} else {
                            $wallet = $wallet_addr[0];
							}

							if (!$wallet) {
                            $this->error('Generate Wallet address error2!');
							//$wallet=L('Can not generate '.$coin.' wallet at the moment');
							}
							$rs = M('UserCoin')->where(array('userid' => userid()))->save(array($cryptofields => $wallet));

							if (!$rs) {
                            $this->error('Add error address wallet3!');
							}
						}
                    

                    }
                    //CryptoNote Ended
					//Bitcoin starts
                    if ($Coin['type'] == 'qbb') {
                        $dj_username = $Coin['dj_yh'];
                        $dj_password = $Coin['dj_mm'];
                        $dj_address = $Coin['dj_zj'];
                        $dj_port = $Coin['dj_dk'];
                        $CoinClient = CoinClient($dj_username, $dj_password, $dj_address, $dj_port, 5, array(), 1);
                        $json = $CoinClient->getinfo();

                        if (!isset($json['version']) || !$json['version']) {
                            //$this->error(L('Wallet link failure! 1'));
							$wallet=L('Wallet System is currently offline 3!');
							$show_qr=0;
                        }else{
							$show_qr=1;
                        $wallet_addr = $CoinClient->getaddressesbyaccount(username());

                        if (!is_array($wallet_addr)) {
                            $wallet_ad = $CoinClient->getnewaddress(username());

                            if (!$wallet_ad) {
                                $this->error('Generate Wallet address error1!');
                            } else {
                                $wallet = $wallet_ad;
                            }
                        } else {
                            $wallet = $wallet_addr[0];
                        }

                        if (!$wallet) {
                            $this->error('Generate Wallet address error2!');
                        }

                        $rs = M('UserCoin')->where(array('userid' => userid()))->save(array($qbdz => $wallet));

                        if (!$rs) {
                            $this->error('Add error address wallet3!');
                        }
						}
                    }
                } else {
					
                    $wallet = $user_coin[$coin . 'b'];
					$dest_tag=$user_coin[$coin . '_tag'];
                }
            }
        } else {

            if (!$Coin['zr_jz']) {
                $wallet = L('The current ban into the currency!');
            } else {

			$wallet = $Coin['codono_coinaddress'];

			$cellphone = M('User')->where(array('id' => userid()))->getField('cellphone');
        $email = M('User')->where(array('id' => userid()))->getField('email');
        
		if ($cellphone || $email) {
            $cellphone = substr_replace($cellphone, '****', 3, 4);
            $email = substr_replace($email, '****', 3, 4);
        } else {
			if(M_ONLY==1){        
			redirect(U('Home/User/cellphone'), $time = 5, $msg = L('Please Verify your Phone!'));
			}
        }

        $this->assign('cellphone', $cellphone);
        $this->assign('email', $email);


            }

        }


        $this->assign('wallet', $wallet);
		$this->assign('dest_tag', $dest_tag);
        $where['userid'] = userid();
        $where['coinname'] = $coin;
        $Model = M('Myzr');
        $count = $Model->where($where)->count();
        $Page = new \Think\Page($count, 10);
        $show = $Page->show();
        $list = $Model->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list);
		$this->assign('show_qr', $show_qr);
        $this->assign('page', $show);
        $this->display();
    }


    public function addnew($coin = NULL)
    {
        if (!userid()) {
            redirect('/#login');
        }

        $Coin = M('Coin')->where(array(
            'status' => 1,
            'name' => array('neq', 'usd')
        ))->select();

        if (!$coin) {
            $coin = "";
        }

        $this->assign('xnb', $coin);

        foreach ($Coin as $k => $v) {
            $coin_list[$v['name']] = $v;
        }

        $this->assign('coin_list', $coin_list);

        $where['userid'] = userid();
        $where['status'] = 1;
        if (!empty($coin)) {
            $where['coinname'] = $coin;
        }


        $count = M('UserWallet')->where($where)->count();
        $Page = new \Think\Page($count, 15);
        $show = $Page->show();

        $userWalletList = M('UserWallet')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

        $this->assign('page', $show);
        $this->assign('userWalletList', $userWalletList);
        $this->assign('prompt_text', D('Text')->get_content('user_wallet'));
        $this->display();
    }

    public function upwallet($coin, $name, $addr, $paypassword)
    {
        if (!userid()) {
            redirect('/#login');
        }

        if (!check($name, 'a')) {
            $this->error(L('Note the name of the wrong format!'));
        }

        if (!check($addr, 'dw')) {
            $this->error(L('Wallet address format error!'));
        }

        if (!check($paypassword, 'password')) {
            $this->error(L('Fund Pwd format error!'));
        }

        $user_paypassword = M('User')->where(array('id' => userid()))->getField('paypassword');

        if (md5($paypassword) != $user_paypassword) {
            $this->error(L('Trading password is wrong!'));
        }

        if (!M('Coin')->where(array('name' => $coin))->find()) {
            $this->error(L('Currency wrong!'));
        }

        $userWallet = M('UserWallet')->where(array('userid' => userid(), 'coinname' => $coin))->select();

        foreach ($userWallet as $k => $v) {
            if ($v['name'] == $name) {
                $this->error(L('Please do not use the same wallet logo!'));
            }

            if ($v['addr'] == $addr) {
                $this->error(L('Wallet address already exists!'));
            }
        }

        if (10 <= count($userWallet)) {
            $this->error('EachpeopleCan onlyAdd to10Addresses!');
        }

        if (M('UserWallet')->add(array('userid' => userid(), 'name' => $name, 'addr' => $addr, 'coinname' => $coin, 'addtime' => time(), 'status' => 1))) {
            $this->success(L('ADDED_SUCCESSFULLY'));
        } else {
            $this->error(L('FAILED_TO_ADD'));
        }
    }

    public function delwallet($id, $paypassword)
    {
        if (!userid()) {
            redirect('/#login');
        }

        if (!check($paypassword, 'password')) {
            $this->error(L('Fund Pwd format error!'));
        }

        if (!check($id, 'd')) {
            $this->error(L('INCORRECT_REQ'));
        }

        $user_paypassword = M('User')->where(array('id' => userid()))->getField('paypassword');

        if (md5($paypassword) != $user_paypassword) {
            $this->error(L('Trading password is wrong!'));
        }

        if (!M('UserWallet')->where(array('userid' => userid(), 'id' => $id))->find()) {
            $this->error(L('Unauthorized access!'));
        } else if (M('UserWallet')->where(array('userid' => userid(), 'id' => $id))->delete()) {
            $this->success(L('successfully deleted!'));
        } else {
            $this->error(L('failed to delete!'));
        }
    }


    public function coinoutLog($coin = NULL)
    {

        if (!userid()) {
            redirect('/#login');
        }

        $this->assign('prompt_text', D('Text')->get_content('finance_myzc'));

        if (C('coin')[$coin]) {
			$explorer=C('coin')[$coin]['js_wk'];
            $coin = trim($coin);
        } else {
            $coin = C('xnb_mr');
        }

        $this->assign('xnb', $coin);
        $Coin = M('Coin')->where(array(
            'status' => 1,
            'name' => array('neq', 'usd')
        ))->select();

        foreach ($Coin as $k => $v) {
            $coin_list[$v['name']] = $v;
        }

        $this->assign('coin_list', $coin_list);

        $where['userid'] = userid();
        $where['coinname'] = $coin;
        $Model = M('Myzc');
        $count = $Model->where($where)->count();
        $Page = new \Think\Page($count, 10);
        $show = $Page->show();
        $list = $Model->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
		$this->assign('explorer',$explorer);
        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();


    }

	public function requestOTP($coinname,$address,$amount){
		if (!userid()) {
			$this->error(L('Please login first!'));
        }

        $user_coin = M('UserCoin')->where(array('userid' => userid()))->find();
        $min_withdrawal_allow=C('coin')[$coinname]['zc_min'];
        $max_withdrawal_allow=C('coin')[$coinname]['zc_max'];

        if ($user_coin[$coinname] < $amount ) {
            $this->error("Insufficient funds available ,You have $user_coin[$coinname] $coinname");
        }

        if ( $amount < $min_withdrawal_allow || $amount<=0) {
            if($min_withdrawal_allow<=0){
                $min_error_message="Please try higher amount for withdrawal";
            }else{
                $min_error_message="Minimum withdrawal amount $min_withdrawal_allow $coinname";
            }
            $this->error($min_error_message);
        }

        if ($amount > $max_withdrawal_allow) {
            if($max_withdrawal_allow<0){
                $max_error_message="Please try lower amount for withdrawal";
            }else{
                $max_error_message="Max withdrawal amount $min_withdrawal_allow $coinname";
            }
            $this->error($max_error_message);
        }


		$user = M('User')->where(array('id' => userid()))->find();
		$code=tradeno();
		session('requestOTP', $code);
		$email=$user['email'];
		$client_ip = get_client_ip();
        $requestTime = date('Y-m-d H:i',time()).'('.date_default_timezone_get().')';
		$subject="Withdrawal Request on ".SHORT_NAME;
		$content="<br/><strong>DO NOT SHARE THIS CODE WITH ANYONE!!</strong><br/>To complete the withdrawal process,<br/><br/>You may be asked to enter this confirmation code:<strong>$code <strong><br/><br/><small><i>
			<table>
			<tr style='border:2px solid black'><td>Email</td><td>$email</td></tr>
			<tr style='border:2px solid black'><td>IP</td><td>$client_ip</td></tr>
			<tr style='border:2px solid black'><td>Coin</td><td>$coinname</td></tr>
			<tr style='border:2px solid black'><td>Amount</td><td>$amount</td></tr>
			<tr style='border:2px solid black'><td>Address</td><td>$address</td></tr>
			<tr style='border:2px solid black'><td>Time</td><td>$requestTime</td></tr>	
			</table>
			<strong>If You didnt request this withdrawal, immediately change passwords,and contact us</strong>";
			//$this->addnotification($email,$subject,$content);
			exec($mail_Sent=json_decode(tmail($email,$subject,$content)));
			$this->success(L('Please check email for code'));
	}
    public function myzc($coin = NULL)
    {
        if (!userid()) {
            redirect('/#login');
        }

        $this->assign('prompt_text', D('Text')->get_content('finance_myzc'));

        if (C('coin')[$coin]) {
            $coin = trim($coin);
        } else {
            $coin = C('xnb_mr');
        }

        $this->assign('xnb', $coin);
        $Coin = M('Coin')->where(array(
            'status' => 1,
            'name' => array('neq', 'usd')
        ))->select();

        foreach ($Coin as $k => $v) {
            $coin_list[$v['name']] = $v;
        }
		$user = M('User')->where(array('id' => userid()))->find();
		$is_ga = $user['ga'] ? 1 : 0;
		
		$this->assign('is_ga', $is_ga);	
		$this->assign('coin_list', $coin_list);
        $user_coin = M('UserCoin')->where(array('userid' => userid()))->find();
        $user_coin[$coin] = round($user_coin[$coin], 6);
        $this->assign('user_coin', $user_coin);

        if (!$coin_list[$coin]['zc_jz']) {
            $this->assign('zc_jz', L($coin . ': Withdrawals are temporarily disabled'));
        } else {
            $userWalletList = M('UserWallet')->where(array('userid' => userid(), 'status' => 1, 'coinname' => $coin))->order('id desc')->select();
            $this->assign('userWalletList', $userWalletList);
            $cellphone = M('User')->where(array('id' => userid()))->getField('cellphone');
			$email = M('User')->where(array('id' => userid()))->getField('email');
        
			if ($cellphone || $email) {
            $cellphone = substr_replace($cellphone, '****', 3, 4);
            $email = substr_replace($email, '****', 3, 4);
			} else {
				if(M_ONLY==1){        
				//redirect(U('Home/User/cellphone'), $time = 5, $msg = L('Please Verify your Phone!'));
				}
			}
		}
        $this->assign('cellphone', $cellphone);
        $this->assign('email', $email);
        $where['userid'] = userid();
        $where['coinname'] = $coin;
        $Model = M('Myzc');
        $count = $Model->where($where)->count();
        $Page = new \Think\Page($count, 10);
        $show = $Page->show();
        $list = $Model->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }
    public function upmyzc($otp=0,$coin, $num, $addr, $paypassword, $cellphone_verify=0,$dest_tag=0,$gacode=0)
    {
		
        if (!userid()) {
            $this->error(L('YOU_NEED_TO_LOGIN'));
        }
		if (!kyced()) {
               $this->error(L('Complete KYC First!'));
		}
		if(M_ONLY==1){
        if (!check($cellphone_verify, 'd')) {
            $this->error(L('INVALID_SMS_CODE'));
        }

        if ($cellphone_verify != session('myzc_verify')) {
            $this->error(L('INCORRECT_SMS_CODE'));
        }
		}
        $num = format_num($num);

        if (!check($num, 'double')) {
            $this->error(L('Number format error!'));
        }
		if ($otp != session('requestOTP')) {
                 $this->error('Incorrect OTP!');
        }
		
        if (!check($addr, 'dw')) {
            $this->error(L('Wallet address format error!'));
        }

        if (!check($paypassword, 'password')) {
            $this->error(L('Fund Pwd format error!'));
        }

        if (!check($coin, 'n')) {
            $this->error(L('Currency format error!'));
        }

        if (!C('coin')[$coin]) {
            $this->error(L('Currency wrong!'));
        }

        $CoinInfo = M('Coin')->where(array('name' => $coin))->find();

        if (!$CoinInfo) {
            $this->error(L('Currency wrong!'));
        }

        $myzc_min = ($CoinInfo['zc_min'] ? abs($CoinInfo['zc_min']) : 0.0001);
        $myzc_max = ($CoinInfo['zc_max'] ? abs($CoinInfo['zc_max']) : 10000000);

        if ($num < $myzc_min) {
            $this->error(L('Amount is less than Minimum Withdrawal Amount!'));
        }

        if ($myzc_max < $num) {
            $this->error(L('Amount Exceeds Maximum Withdrawal Limit!'));
        }

        $user = M('User')->where(array('id' => userid()))->find();
		
		if(self::G2FA_REQUIRED_FOR_WITHDRAWAL==1){
		$is_ga = $user['ga'] ? 1 : 0;
		if($is_ga==1){
			if (!$gacode || $gacode==0) {
                    $this->error(L('You must enter 2FA Code'));
			}
		
			$arr = explode('|', $user['ga']);
			$secret = $arr[0];
			$ga = new \Common\Ext\GoogleAuthenticator();
			$ga_verification=$ga->verifyCode($secret, $gacode, 1);
            if (!$ga_verification) {
                $this->error(L('Incorrect Google 2FA Entered'));
            }
		}
		}

        if (md5($paypassword) != $user['paypassword']) {
            $this->error(L('Trading password is wrong!'));
        }

        $user_coin = M('UserCoin')->where(array('userid' => userid()))->find();

        if ($user_coin[$coin] < $num) {
            $this->error(L('Insufficient funds available'));
        }

        $qbdz = $coin . 'b';
        $fee_user = M('UserCoin')->where(array($qbdz => $CoinInfo['zc_user']))->find();

        if ($fee_user) {
            debug('Fee Address: ' . $CoinInfo['zc_user'] . ' exist, There are fees');
            $fee = round(($num / 100) * $CoinInfo['zc_fee'], 8);
            $mum = round($num - $fee, 8);

            if ($mum < 0) {
                $this->error(L('Incorrect withdrawal amount!'));
            }

            if ($fee < 0) {
                $this->error(L('Incorrect withdrawal fee!'));
            }
        } else {
            debug('Fee Address: ' . $CoinInfo['zc_user'] . ' does not exist,No fees');
            //$fee = 0;
            //$mum = $num;
			$fee = round(($num / 100) * $CoinInfo['zc_fee'], 8);
            $mum = round($num - $fee, 8);
        }
//eth Starts
        if ($CoinInfo['type'] == 'eth') {
			$heyue = $CoinInfo['dj_yh']; //Contract Address
            $mo = M();
//            $peer = M('UserCoin')->where(array($qbdz => $addr))->find();
            $peer = $mo->table('codono_user_coin')->where(array($qbdz => $addr))->find();

            if ($peer) {

                $mo = M();
                $rs = array();
                $rs[] = $mo->table('codono_user_coin')->where(array('userid' => userid()))->setDec($coin, $num);
                $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $peer['userid']))->setInc($coin, $mum);

                $rs[] = $mo->table('codono_myzc')->add(array('userid' => userid(), 'username' => $addr, 'coinname' => $coin, 'txid' => md5($addr . $user_coin[$coin . 'b'] . time()), 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'addtime' => time(), 'status' => 1));
                $rs[] = $mo->table('codono_myzr')->add(array('userid' => $peer['userid'], 'username' => $user_coin[$coin . 'b'], 'coinname' => $coin, 'txid' => md5($user_coin[$coin . 'b'] . $addr . time()), 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'addtime' => time(), 'status' => 1));
                $this->success('You have successfully raised the coins and will automatically transfer them out after the admin review!');
            } else {
                //eth Wallet Withdrawal
                $heyue = $CoinInfo['dj_yh'];//Contract Address
                $dj_password = cryptString($CoinInfo['dj_mm'],'d');
                $dj_address = $CoinInfo['dj_zj'];
                $dj_port = $CoinInfo['dj_dk'];
				$dj_decimal = $CoinInfo['cs_qk'];
                $auto_status = ($CoinInfo['zc_zd'] && ($num < $CoinInfo['zc_zd']) ? 1 : 0);
                $mo = M();
                $rs = array();
                $rs[] = $r = $mo->table('codono_user_coin')->where(array('userid' => userid()))->setDec($coin, $num);
                $rs[] = $aid = $mo->table('codono_myzc')->add(array('userid' => userid(), 'username' => $addr, 'coinname' => $coin, 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'addtime' => time(), 'status' => $auto_status));
                
                if ($auto_status) {
                    $EthCommon = new \Org\Util\EthCommon($dj_address, $dj_port, "2.0");
                    $EthPayLocal = new \Org\Util\EthPayLocal($dj_address, $dj_port, "2.0", $heyue,$dj_password);
                    $ContractAddress=$heyue;
                    $master=EthMaster($dj_address, $dj_port,$CoinInfo['codono_coinaddress'],$dj_password, $ContractAddress);
                    
                    if ($heyue) {
                        //Contract Address transfer out
						$zhuan['fromaddress'] = $CoinInfo['codono_coinaddress'];
                        $zhuan['toaddress'] = $addr;
                        $zhuan['token'] = $heyue;
                        $zhuan['type'] = $coin;
                        $zhuan['amount'] = (double)$mum;
						$zhuan['password'] =$CoinInfo['password'];
			            $sendrs=$master->transferToken($zhuan['toaddress'], $zhuan['amount'], $zhuan['token'],$dj_decimal);
                    } else {
                        //eth
		
		
						$zhuan['fromaddress'] = $CoinInfo['codono_coinaddress'];
                        $zhuan['toaddress'] = $addr;
                        $zhuan['amount'] = floatval($mum);
						$zhuan['password'] =$CoinInfo['password'];
                        $sendrs=$master->transferFromCoinbase($addr,floatval($mum));
		
                        //$sendrs = $EthPayLocal->eth_sendTransaction($zhuan);
                    }

                    if ($sendrs && $aid) {
                        $arr = json_decode($sendrs, true);
                        $hash = $arr['result'] ? $arr['result'] : $arr['error']['message'];
                        M('Myzc')->where(array('id' => $aid))->save(array('txid'=>$sendrs));
                        if ($hash) M()->execute("UPDATE `codono_myzc` SET  `hash` =  '$hash' WHERE id = '$aid' ");
                    }
                    $this->success('You have the success of the coin, background audit will automatically go out!' . $mum);
                }
               $this->success('You have successfully raised the coins and will automatically transfer them out after the background review!');

            }
        }
        //eth Ends
        //eth Ends

        if ($CoinInfo['type'] == 'rgb') {
            debug($coin, L('Start the transfer of coins'));
            $peer = M('UserCoin')->where(array($qbdz => $addr))->find();
            if (!$peer) {
                $this->error(L('Withdrawal Address of ICO does not exist!'));
            }

            $mo = M();
            $mo->execute('set autocommit=0');
            $mo->execute('lock tables  codono_user_coin write  , codono_myzc write  , codono_myzr write , codono_myzc_fee write');
            $rs = array();
            $rs[] = $mo->table('codono_user_coin')->where(array('userid' => userid()))->setDec($coin, $num);
            $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $peer['userid']))->setInc($coin, $mum);

            if ($fee) {
                if ($mo->table('codono_user_coin')->where(array($qbdz => $CoinInfo['zc_user']))->find()) {
                    //$rs[] = $mo->table('codono_user_coin')->where(array($qbdz => $CoinInfo['zc_user']))->setInc($coin, $fee);
                    debug(array('msg' => L('Withdraw to charge a fee') . $fee), 'fee');
                } else {
                    $rs[] = $mo->table('codono_user_coin')->add(array($qbdz => $CoinInfo['zc_user'], $coin => $fee));
                    debug(array('msg' => L('Withdraw to charge a fee') . $fee), 'fee');
                }
            }

            $rs[] = $mo->table('codono_myzc')->add(array('userid' => userid(), 'username' => $addr, 'coinname' => $coin, 'txid' => md5($addr . $user_coin[$coin . 'b'] . time()), 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'addtime' => time(), 'status' => 1));
            $rs[] = $mo->table('codono_myzr')->add(array('userid' => $peer['userid'], 'username' => $user_coin[$coin . 'b'], 'coinname' => $coin, 'txid' => md5($user_coin[$coin . 'b'] . $addr . time()), 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'addtime' => time(), 'status' => 1));

            if ($fee_user) {
                $rs[] = $mo->table('codono_myzc_fee')->add(array('userid' => $fee_user['userid'], 'username' => $CoinInfo['zc_user'], 'coinname' => $coin, 'txid' => md5($user_coin[$coin . 'b'] . $CoinInfo['zc_user'] . time()), 'num' => $num, 'fee' => $fee, 'type' => 1, 'mum' => $mum, 'addtime' => time(), 'status' => 1));
            }

            if (check_arr($rs)) {
                $mo->execute('commit');
                $mo->execute('unlock tables');
                session('myzc_verify', null);
                $this->success(L('Transfer success!'));
            } else {
                $mo->execute('rollback');
                $this->error('Transfer Failed!');
            }
        }
		
		/* Offline Coins Manual withdrawal */
		if ($CoinInfo['type'] == 'offline') {
            debug($coin, L('Offline/Manual withdrawal request'));
            $mo = M();
            $mo->execute('set autocommit=0');
            $mo->execute('lock tables  codono_user_coin write  , codono_myzc write  , codono_myzr write , codono_myzc_fee write');
            $rs = array();
            $rs[] = $mo->table('codono_user_coin')->where(array('userid' => userid()))->setDec($coin, $num);
            

            if ($fee) {
                if ($mo->table('codono_user_coin')->where(array($qbdz => $CoinInfo['zc_user']))->find()) {
                    //$rs[] = $mo->table('codono_user_coin')->where(array($qbdz => $CoinInfo['zc_user']))->setInc($coin, $fee);
                    debug(array('msg' => L('Withdraw to charge a fee') . $fee), 'fee');
                } else {
                    $rs[] = $mo->table('codono_user_coin')->add(array($qbdz => $CoinInfo['zc_user'], $coin => $fee));
                    debug(array('msg' => L('Withdraw to charge a fee') . $fee), 'fee');
                }
            }

            $rs[] = $mo->table('codono_myzc')->add(array('userid' => userid(), 'username' => $addr, 'coinname' => $coin, 'txid' => md5($addr . $user_coin[$coin . 'b'] . time()), 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'addtime' => time(), 'status' => 0));
          
            if ($fee_user) {
                $rs[] = $mo->table('codono_myzc_fee')->add(array('userid' => $fee_user['userid'], 'username' => $CoinInfo['zc_user'], 'coinname' => $coin, 'txid' => md5($user_coin[$coin . 'b'] . $CoinInfo['zc_user'] . time()), 'num' => $num, 'fee' => $fee, 'type' => 1, 'mum' => $mum, 'addtime' => time(), 'status' => 1));
            }

            if (check_arr($rs)) {
                $mo->execute('commit');
                $mo->execute('unlock tables');
                session('myzc_verify', null);
                $this->success(L('Transfer success!'));
            } else {
                $mo->execute('rollback');
                $this->error('Transfer Failed!');
            }
        }
		//Offline manual withdrawal ends
				//Coinpayments starts
		     if ($CoinInfo['type'] == 'coinpay') {
            $mo = M();
			$coinpay_condition[$qbdz]=	$addr;
			if($dest_tag!=NULL && $dest_tag!=0){
				$coinpay_condition[$coin.'_tag']=	$dest_tag;
			}
            if ($mo->table('codono_user_coin')->where($coinpay_condition)->find()) {
                $peer = M('UserCoin')->where($coinpay_condition)->find();

                if (!$peer) {
                    $this->error(L('Withdraw address does not exist!'));
                }

                $mo = M();
                $mo->execute('set autocommit=0');
                $mo->execute('lock tables  codono_user_coin write  , codono_myzc write  , codono_myzr write , codono_myzc_fee write');
                $rs = array();
                $rs[] = $mo->table('codono_user_coin')->where(array('userid' => userid()))->setDec($coin, $num);
                $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $peer['userid']))->setInc($coin, $mum);

               

                $rs[] = $mo->table('codono_myzc')->add(array('userid' => userid(), 'username' => $addr,'dest_tag' => $dest_tag, 'coinname' => $coin, 'txid' => md5($addr . $user_coin[$coin . 'b'] . time()), 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'addtime' => time(), 'status' => 1));
                $rs[] = $mo->table('codono_myzr')->add(array('userid' => $peer['userid'], 'username' => $user_coin[$coin . 'b'], 'coinname' => $coin, 'txid' => md5($user_coin[$coin . 'b'] . $addr . time()), 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'addtime' => time(), 'status' => 1));

                if (check_arr($rs)) {
                    $mo->execute('commit');
                    $mo->execute('unlock tables');
                    session('myzc_verify', null);
                    $this->success(L('Transfer success!'));
                } else {
                    $mo->execute('rollback');
                    $this->error('Transfer Failed!');
                }
            } else {

                $dj_username = $CoinInfo['dj_yh'];
                $dj_password = $CoinInfo['dj_mm'];
                $dj_address = $CoinInfo['dj_zj'];
                $dj_port = $CoinInfo['dj_dk'];
                
				$auto_status = ($CoinInfo['zc_zd'] && ($num < $CoinInfo['zc_zd']) ? 1 : 0);
				$cps_api = CoinPay($dj_username, $dj_password, $dj_address, $dj_port, 5, array(), 1);
						$information = $cps_api->GetBasicInfo();
						$coinpay_coin=strtoupper($coin);
             			 
				$can_withdraw=1;
				if($information['error']!='ok' || !isset($information['result']['username'])){
                   //         $this->error(L('Wallet link failure! Coinpayments'));
				   debug($coin.' can not be connetcted at time:'.time() .'<br/>');	
					$can_withdraw=0;
				}

				//TODO :Find a valid way to validate coin address
                 if(strlen($addr)>8){
					 $valid_res =1;
				 }else{
					 $valid_res =0;
				 }

                if (!$valid_res) {
                    $this->error($addr . L(' It is not a valid address wallet!'));
                }

                $balances = $cps_api->GetAllCoinBalances();
				
                if ($balances['result'][$coinpay_coin]['balancef'] < $num) {
                    //$this->error(L('Can not be withdrawn due to system'));
					debug($coin.' Balance is lower than  '.$num.' at time:'.time() .'<br/>');	
					$can_withdraw=0;
                }

                $mo = M();
                $mo->execute('set autocommit=0');
                $mo->execute('lock tables  codono_user_coin write  , codono_myzc write ,codono_myzr write, codono_myzc_fee write');
                $rs = array();
				//Reduce Withdrawers balance
                $rs[] = $r = $mo->table('codono_user_coin')->where(array('userid' => userid()))->setDec($coin, $num);
                //Add entry of withdraw [zc] in database 
				$rs[] = $aid = $mo->table('codono_myzc')->add(array('userid' => userid(), 'username' => $addr,'dest_tag' => $dest_tag, 'coinname' => $coin, 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'addtime' => time(), 'status' => $auto_status));
				
                if ($fee && $auto_status) {

                    $rs[] = $mo->table('codono_myzc_fee')->add(array('userid' => $fee_user['userid'], 'username' => $CoinInfo['zc_user'], 'coinname' => $coin, 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'type' => 2, 'addtime' => time(), 'status' => 1));

                    if ($mo->table('codono_user_coin')->where(array($qbdz => $CoinInfo['zc_user']))->find()) {
                        //$rs[] = $r = $mo->table('codono_user_coin')->where(array($qbdz => $CoinInfo['zc_user']))->setInc($coin, $fee);
                        debug(array('res' => $r, 'lastsql' => $mo->table('codono_user_coin')->getLastSql()), L('Additional costs'));
                    } else {
                        $rs[] = $r = $mo->table('codono_user_coin')->add(array($qbdz => $CoinInfo['zc_user'], $coin => $fee));
                    }
                }

                if (check_arr($rs)) {
                    if ($auto_status && $can_withdraw==1) {
                        $mo->execute('commit');
                        $mo->execute('unlock tables');
						 $buyer_email = M('User')->where(array('id' => userid()))->getField('email');
						$withdrawals = ['amount' =>$mum,
						'add_tx_fee' => 0,
						'auto_confirm'=>1, //Auto confirm 1 or 0
						'currency' => $coinpay_coin,
						'address' => $addr,
						//'dest_tag'=>$dest_tag,
						'ipn_url' => SITE_URL.'/IPN/confirm',
						'note'=>$buyer_email];
						if($dest_tag!=0 && $dest_tag!=NULL){
						$withdrawals['dest_tag']=$dest_tag;
						}

						$the_withdrawal = $cps_api->CreateWithdrawal($withdrawals);
						
						
                        if ($the_withdrawal["error"] != "ok") {
							$the_status=false;
                           $this->error('Your withdrawal request is sent to admin,'.$the_withdrawal["error"]);
						   
                        } else {
							$the_status=true;
							$cp_withdrawal_id=$the_withdrawal["result"]["id"] ;
							M('Myzc')->where(array('id' => $aid))->save(array('hash' => $cp_withdrawal_id));	
                            //$this->success('Successful Withdrawal!');
                        }
						}

                    if ($auto_status && $the_status && $can_withdraw==1) {
                        $mo->execute('commit');
                        $mo->execute('unlock tables');
                        session('myzc_verify', null);
                        $this->success('Successful Withdrawal!');
                    } else {
                        $mo->execute('commit');
                        $mo->execute('unlock tables');
                        session('myzc_verify', null);
                        $this->success('Being Reviewed!');
                    }
                } else {
                    $mo->execute('rollback');
                    $this->error('Withdrawal failure!');
                }
            }
        }
		//Coinpayments ends
						//WavesPlatform Starts
		     if ($CoinInfo['type'] == 'waves') {
				 
            $mo = M();

            if ($mo->table('codono_user_coin')->where(array($qbdz => $addr))->find()) {
                $peer = M('UserCoin')->where(array($qbdz => $addr))->find();

                if (!$peer) {
                    $this->error(L('Withdraw address does not exist!'));
                }

                $mo = M();
                $mo->execute('set autocommit=0');
                $mo->execute('lock tables  codono_user_coin write  , codono_myzc write  , codono_myzr write , codono_myzc_fee write');
                $rs = array();
                $rs[] = $mo->table('codono_user_coin')->where(array('userid' => userid()))->setDec($coin, $num);
                $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $peer['userid']))->setInc($coin, $mum);

                if ($fee) {
                    if ($mo->table('codono_user_coin')->where(array($qbdz => $CoinInfo['zc_user']))->find()) {
                        //$rs[] = $mo->table('codono_user_coin')->where(array($qbdz => $CoinInfo['zc_user']))->setInc($coin, $fee);
                    } else {
                        $rs[] = $mo->table('codono_user_coin')->add(array($qbdz => $CoinInfo['zc_user'], $coin => $fee));
                    }
                }

                $rs[] = $mo->table('codono_myzc')->add(array('userid' => userid(), 'username' => $addr, 'coinname' => $coin, 'txid' => md5($addr . $user_coin[$coin . 'b'] . time()), 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'addtime' => time(), 'status' => 1));
                $rs[] = $mo->table('codono_myzr')->add(array('userid' => $peer['userid'], 'username' => $user_coin[$coin . 'b'], 'coinname' => $coin, 'txid' => md5($user_coin[$coin . 'b'] . $addr . time()), 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'addtime' => time(), 'status' => 1));

                if ($fee_user) {
                    $rs[] = $mo->table('codono_myzc_fee')->add(array('userid' => $fee_user['userid'], 'username' => $CoinInfo['zc_user'], 'coinname' => $coin, 'txid' => md5($user_coin[$coin . 'b'] . $CoinInfo['zc_user'] . time()), 'num' => $num, 'fee' => $fee, 'type' => 1, 'mum' => $mum, 'addtime' => time(), 'status' => 1));
                }

                if (check_arr($rs)) {
                    $mo->execute('commit');
                    $mo->execute('unlock tables');
                    session('myzc_verify', null);
                    $this->success(L('Transfer success!'));
                } else {
                    $mo->execute('rollback');
                    $this->error('Transfer Failed!');
                }
            } else {

                $dj_username = $CoinInfo['dj_yh'];
                $dj_password = $CoinInfo['dj_mm'];
                $dj_address = $CoinInfo['dj_zj'];
                $dj_port = $CoinInfo['dj_dk'];
				$dj_decimal = $CoinInfo['cs_qk'];
                $main_address = $CoinInfo['codono_coinaddress'];
				$auto_status = ($CoinInfo['zc_zd'] && ($num < $CoinInfo['zc_zd']) ? 1 : 0);
				$can_withdraw=1;
				$waves = WavesClient($dj_username, $dj_password, $dj_address, $dj_port,$dj_decimal, 5, array(), 1);
				$waves_coin=strtoupper($coin);
				$information= json_decode($waves->status(),true);
				if($information['blockchainHeight'] && $information['blockchainHeight']<=0){
				   clog('waves_error',$coin.' can not be connectted at time:'.time() .'<br/>');	
					$can_withdraw=0;
				}

				//TODO :Find a valid way to validate coin address
                 if(strlen($addr)>30){
					 $valid_res =1;
				 }else{
					 $valid_res =0;
				 }

                if (!$valid_res) {
                    $this->error($addr . L(' It is not a valid address wallet!'));
                }

                $balances = json_decode($waves->Balance($main_address,$dj_username),true);
				$dj_decimal=$dj_decimal?$dj_decimal:8;
				$wave_main_balance=$waves->deAmount($balances['balance'],$dj_decimal);
                if ($wave_main_balance < $num) {
					
					  clog('waves_error',$coin.' main_address '.$main_address.' Balance is '.$wave_main_balance.' is'.$dj_decimal.' lower than  '.$num.' at time:'.time() .' '.$dj_username.'<br/>');	
					$can_withdraw=0;
                }

                $mo = M();
                $mo->execute('set autocommit=0');
                $mo->execute('lock tables  codono_user_coin write  , codono_myzc write ,codono_myzr write, codono_myzc_fee write');
                $rs = array();
				//Reduce Withdrawers balance
                $rs[] = $r = $mo->table('codono_user_coin')->where(array('userid' => userid()))->setDec($coin, $num);
                //Add entry of withdraw [zc] in database 
				$rs[] = $aid = $mo->table('codono_myzc')->add(array('userid' => userid(), 'username' => $addr, 'coinname' => $coin, 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'addtime' => time(), 'status' => $auto_status));
				
                if ($fee && $auto_status) {

                    $rs[] = $mo->table('codono_myzc_fee')->add(array('userid' => $fee_user['userid'], 'username' => $CoinInfo['zc_user'], 'coinname' => $coin, 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'type' => 2, 'addtime' => time(), 'status' => 1));

                    if ($mo->table('codono_user_coin')->where(array($qbdz => $CoinInfo['zc_user']))->find()) {
                        //$rs[] = $r = $mo->table('codono_user_coin')->where(array($qbdz => $CoinInfo['zc_user']))->setInc($coin, $fee);
                       // debug(array('res' => $r, 'lastsql' => $mo->table('codono_user_coin')->getLastSql()), L('Additional costs'));
						
                    } else {
                        $rs[] = $r = $mo->table('codono_user_coin')->add(array($qbdz => $CoinInfo['zc_user'], $coin => $fee));
                    }
                }

                if (check_arr($rs)) {
                    if ($auto_status && $can_withdraw==1) {
                        $mo->execute('commit');
                        $mo->execute('unlock tables');
						 $buyer_email = M('User')->where(array('id' => userid()))->getField('email');
					
						$wavesend_response=$waves->Send($main_address,$addr,$mum,$dj_username);
						$the_withdrawal = json_decode($wavesend_response,true);
						if ($the_withdrawal["error"]) {
							$the_status=false;
							clog('waves_error',json_encode($the_withdrawal));
                           $this->error('Your withdrawal request is sent to admin,'.$the_withdrawal["message"]);
						   
                        } else {
							$the_status=true;
							M('Myzc')->where(array('id' => $aid))->save(array('txid'=>$the_withdrawal['id'],'hash' => $the_withdrawal['signature']));	
                            //$this->success('Successful Withdrawal!');
                        }
						}

                    if ($auto_status && $the_status && $can_withdraw==1) {
                        $mo->execute('commit');
                        $mo->execute('unlock tables');
                        session('myzc_verify', null);
                        $this->success('Successful Withdrawal!');
                    } else {
                        $mo->execute('commit');
                        $mo->execute('unlock tables');
                        session('myzc_verify', null);
                        $this->success('Being Reviewed!'.$the_withdrawal["error"]);
                    }
                } else {
                    $mo->execute('rollback');
                    $this->error('Withdrawal failure!');
                }
            }
        }
        //WavesPlatform Ends
		
		//BLOCKIO starts
		     if ($CoinInfo['type'] == 'blockio') {
            $mo = M();

            if ($mo->table('codono_user_coin')->where(array($qbdz => $addr))->find()) {
                $peer = M('UserCoin')->where(array($qbdz => $addr))->find();

                if (!$peer) {
                    $this->error(L('Withdraw address does not exist!'));
                }

                $mo = M();
                $mo->execute('set autocommit=0');
                $mo->execute('lock tables  codono_user_coin write  , codono_myzc write  , codono_myzr write , codono_myzc_fee write');
                $rs = array();
                $rs[] = $mo->table('codono_user_coin')->where(array('userid' => userid()))->setDec($coin, $num);
                $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $peer['userid']))->setInc($coin, $mum);

                if ($fee) {
                    if ($mo->table('codono_user_coin')->where(array($qbdz => $CoinInfo['zc_user']))->find()) {
                        //$rs[] = $mo->table('codono_user_coin')->where(array($qbdz => $CoinInfo['zc_user']))->setInc($coin, $fee);
                    } else {
                        $rs[] = $mo->table('codono_user_coin')->add(array($qbdz => $CoinInfo['zc_user'], $coin => $fee));
                    }
                }

                $rs[] = $mo->table('codono_myzc')->add(array('userid' => userid(), 'username' => $addr, 'coinname' => $coin, 'txid' => md5($addr . $user_coin[$coin . 'b'] . time()), 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'addtime' => time(), 'status' => 1));
                $rs[] = $mo->table('codono_myzr')->add(array('userid' => $peer['userid'], 'username' => $user_coin[$coin . 'b'], 'coinname' => $coin, 'txid' => md5($user_coin[$coin . 'b'] . $addr . time()), 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'addtime' => time(), 'status' => 1));

                if ($fee_user) {
                    $rs[] = $mo->table('codono_myzc_fee')->add(array('userid' => $fee_user['userid'], 'username' => $CoinInfo['zc_user'], 'coinname' => $coin, 'txid' => md5($user_coin[$coin . 'b'] . $CoinInfo['zc_user'] . time()), 'num' => $num, 'fee' => $fee, 'type' => 1, 'mum' => $mum, 'addtime' => time(), 'status' => 1));
                }

                if (check_arr($rs)) {
                    $mo->execute('commit');
                    $mo->execute('unlock tables');
                    session('myzc_verify', null);
                    $this->success(L('Transfer success!'));
                } else {
                    $mo->execute('rollback');
                    $this->error('Transfer Failed!');
                }
            } else {

                $dj_username = $CoinInfo['dj_yh'];
                $dj_password = $CoinInfo['dj_mm'];
                $dj_address = $CoinInfo['dj_zj'];
                $dj_port = $CoinInfo['dj_dk'];
                
				$auto_status = ($CoinInfo['zc_zd'] && ($num < $CoinInfo['zc_zd']) ? 1 : 0);
                $block_io = BlockIO($dj_username, $dj_password, $dj_address, $dj_port, 5, array(), 1);
             	$json = $block_io->get_balance();
				$can_withdraw=1;
				if (!isset($json->status) || $json->status!='success') {
                            //$this->error(L('Wallet link failure! blockio'));
				debug('Blockio Could not be connected at '.time() .'<br/>');			
				$can_withdraw=0;
				}

                $valid_res = $block_io->validateaddress($addr);

                if (!$valid_res) {
                    $this->error($addr .' :'. L('Not valid address!'));
                }

                
                if ($json->data->available_balance < $num) {
                    //$this->error(L('Wallet balance of less than'));
					debug('Blockio Balance is lower than  '.$num.' at time:'.time() .'<br/>');	
					$can_withdraw=0;
                }

                $mo = M();
                $mo->execute('set autocommit=0');
                $mo->execute('lock tables  codono_user_coin write  , codono_myzc write ,codono_myzr write, codono_myzc_fee write');
                $rs = array();
				//Reduce Withdrawers balance
                $rs[] = $r = $mo->table('codono_user_coin')->where(array('userid' => userid()))->setDec($coin, $num);
                //Add entry of withdraw [zc] in database 
				$rs[] = $aid = $mo->table('codono_myzc')->add(array('userid' => userid(), 'username' => $addr, 'coinname' => $coin, 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'addtime' => time(), 'status' => $auto_status));

                if ($fee && $auto_status) {

                    $rs[] = $mo->table('codono_myzc_fee')->add(array('userid' => $fee_user['userid'], 'username' => $CoinInfo['zc_user'], 'coinname' => $coin, 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'type' => 2, 'addtime' => time(), 'status' => 1));

                    if ($mo->table('codono_user_coin')->where(array($qbdz => $CoinInfo['zc_user']))->find()) {
                        //$rs[] = $r = $mo->table('codono_user_coin')->where(array($qbdz => $CoinInfo['zc_user']))->setInc($coin, $fee);
                        debug(array('res' => $r, 'lastsql' => $mo->table('codono_user_coin')->getLastSql()), L('Additional costs'));
                    } else {
                        $rs[] = $r = $mo->table('codono_user_coin')->add(array($qbdz => $CoinInfo['zc_user'], $coin => $fee));
                    }
                }

                if (check_arr($rs)) {
                    if ($auto_status && $can_withdraw==1) {
                        $mo->execute('commit');
                        $mo->execute('unlock tables');
						
                        $sendrs = $block_io->withdraw(array('amounts' => $mum, 'to_addresses' => $addr));
						$flag = 0;
                        if ($sendrs) {
                            if (isset($sendrs->status) && ($sendrs->status == 'success')) {
                                $flag = 1;
                            }
                        } else {
                            $flag = 0;
                        }
                        if (!$flag) {
                            $this->error('wallet server  Withdraw currency failure,Manually turn out');
                        } else {
                            $this->success('Successful Withdrawal!');
                        }
                    }

                    if ($auto_status && $can_withdraw==1) {
                        $mo->execute('commit');
                        $mo->execute('unlock tables');
                        session('myzc_verify', null);
                        $this->success('Successful Withdrawal!');
                    } else {
                        $mo->execute('commit');
                        $mo->execute('unlock tables');
                        session('myzc_verify', null);
                        $this->success('Application is successful Withdrawal,Please wait for the review!');
                    }
                } else {
                    $mo->execute('rollback');
                    $this->error('Withdrawal failure!');
                }
            }
        }
		//BlockIO ends
		//cryptonote starts
		     if ($CoinInfo['type'] == 'cryptonote') {
            $mo = M();

            if ($mo->table('codono_user_coin')->where(array($qbdz => $addr))->find()) {
                $peer = M('UserCoin')->where(array($qbdz => $addr))->find();

                if (!$peer) {
                    $this->error(L('Withdraw address does not exist!'));
                }
                $mo = M();
                $mo->execute('set autocommit=0');
                $mo->execute('lock tables  codono_user_coin write  , codono_myzc write  , codono_myzr write , codono_myzc_fee write');
                $rs = array();
                $rs[] = $mo->table('codono_user_coin')->where(array('userid' => userid()))->setDec($coin, $num);
                $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $peer['userid']))->setInc($coin, $mum);

                if ($fee) {
                    if ($mo->table('codono_user_coin')->where(array($qbdz => $CoinInfo['zc_user']))->find()) {
                        //$rs[] = $mo->table('codono_user_coin')->where(array($qbdz => $CoinInfo['zc_user']))->setInc($coin, $fee);
                    } else {
                        $rs[] = $mo->table('codono_user_coin')->add(array($qbdz => $CoinInfo['zc_user'], $coin => $fee));
                    }
                }

                $rs[] = $mo->table('codono_myzc')->add(array('userid' => userid(), 'username' => $addr, 'coinname' => $coin, 'txid' => md5($addr . $user_coin[$coin . 'b'] . time()), 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'addtime' => time(), 'status' => 1));
                $rs[] = $mo->table('codono_myzr')->add(array('userid' => $peer['userid'], 'username' => $user_coin[$coin . 'b'], 'coinname' => $coin, 'txid' => md5($user_coin[$coin . 'b'] . $addr . time()), 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'addtime' => time(), 'status' => 1));

                if ($fee_user) {
                    $rs[] = $mo->table('codono_myzc_fee')->add(array('userid' => $fee_user['userid'], 'username' => $CoinInfo['zc_user'], 'coinname' => $coin, 'txid' => md5($user_coin[$coin . 'b'] . $CoinInfo['zc_user'] . time()), 'num' => $num, 'fee' => $fee, 'type' => 1, 'mum' => $mum, 'addtime' => time(), 'status' => 1));
                }

                if (check_arr($rs)) {
                    $mo->execute('commit');
                    $mo->execute('unlock tables');
                    session('myzc_verify', null);
                    $this->success(L('Transfer success!'));
                } else {
                    $mo->execute('rollback');
                    $this->error('Transfer Failed!');
                }
            } else {

                $dj_username = $CoinInfo['dj_yh'];
                $dj_password = $CoinInfo['dj_mm'];
                $dj_address = $CoinInfo['dj_zj'];
                $dj_port = $CoinInfo['dj_dk'];
                
				$auto_status = ($CoinInfo['zc_zd'] && ($num < $CoinInfo['zc_zd']) ? 1 : 0);
                $cryptonote = CryptoNote($dj_address, $dj_port);
				$open_wallet = $cryptonote->open_wallet($dj_username,$dj_password);
					
				$json=json_decode($cryptonote->get_height());
				$can_withdraw=1;                
				if (!isset($json->height) || $json->error!=0) {
				debug('CryptoNote '.$coin.' Could not be connected at '.time() .'<br/>');			
				$can_withdraw=0;
                 }
				
             	$bal_info = json_decode($cryptonote->getBalance());
				$crypto_balance=$cryptonote->deAmount($bal_info->unlocked_balance);    
                
                if ($crypto_balance < $num) {
					debug('CryptoNote '.$coin.' Balance is lower than  '.$num.' at time:'.time() .'<br/>');	
					$can_withdraw=0;
                }

                $mo = M();
                $mo->execute('set autocommit=0');
                $mo->execute('lock tables  codono_user_coin write  , codono_myzc write ,codono_myzr write, codono_myzc_fee write');
                $rs = array();
				//Reduce Withdrawers balance
                $rs[] = $r = $mo->table('codono_user_coin')->where(array('userid' => userid()))->setDec($coin, $num);
                //Add entry of withdraw [zc] in database 
				$rs[] = $aid = $mo->table('codono_myzc')->add(array('userid' => userid(), 'username' => $addr, 'coinname' => $coin, 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'addtime' => time(), 'status' => $auto_status));

                if ($fee && $auto_status) {

                    $rs[] = $mo->table('codono_myzc_fee')->add(array('userid' => $fee_user['userid'], 'username' => $CoinInfo['zc_user'], 'coinname' => $coin, 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'type' => 2, 'addtime' => time(), 'status' => 1));

                    if ($mo->table('codono_user_coin')->where(array($qbdz => $CoinInfo['zc_user']))->find()) {
                        //$rs[] = $r = $mo->table('codono_user_coin')->where(array($qbdz => $CoinInfo['zc_user']))->setInc($coin, $fee);
                        debug(array('res' => $r, 'lastsql' => $mo->table('codono_user_coin')->getLastSql()), L('Additional costs'));
                    } else {
                        $rs[] = $r = $mo->table('codono_user_coin')->add(array($qbdz => $CoinInfo['zc_user'], $coin => $fee));
                    }
                }

                if (check_arr($rs)) {
                    if ($auto_status && $can_withdraw==1) {
                        $mo->execute('commit');
                        $mo->execute('unlock tables');
						

						//$sendrs = json_decode($cryptonote->transfer($send_amt, $myzc['username']));     
						$sendrs = json_decode($cryptonote->transfer($num,$addr));        
						
						$flag = 0;
						clog($coin.'_cryptno_error',json_encode($sendrs));
						if ($sendrs->error==0) {
							 if (isset($sendrs->tx_hash) && isset($sendrs->tx_key)) {
                                $flag = 1;
                            }

                        } else {
                            $flag = 0;
                        }
                        if (!$flag) {
                            $this->error('We have sent your withdrawal request to admin');
                        } else {
                            $this->success('Successful Withdrawal!');
                        }
                    }

                    if ($auto_status && $can_withdraw==1) {
                        $mo->execute('commit');
                        $mo->execute('unlock tables');
                        session('myzc_verify', null);
                        $this->success('Successful Withdrawal!');
                    } else {
                        $mo->execute('commit');
                        $mo->execute('unlock tables');
                        session('myzc_verify', null);
                        $this->success('Application is successful Withdrawal,Please wait for the review!');
                    }
                } else {
                    $mo->execute('rollback');
                    $this->error('Withdrawal failure!');
                }
            }
        }
		//CryptoNote Ends
			//Bitcoin Type Starts
        if ($CoinInfo['type'] == 'qbb') {
            $mo = M();

            if ($mo->table('codono_user_coin')->where(array($qbdz => $addr))->find()) {
                $peer = M('UserCoin')->where(array($qbdz => $addr))->find();

                if (!$peer) {
                    $this->error(L('Withdraw address does not exist!'));
                }

                $mo = M();
                $mo->execute('set autocommit=0');
                $mo->execute('lock tables  codono_user_coin write  , codono_myzc write  , codono_myzr write , codono_myzc_fee write');
                $rs = array();
                $rs[] = $mo->table('codono_user_coin')->where(array('userid' => userid()))->setDec($coin, $num);
                $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $peer['userid']))->setInc($coin, $mum);

                if ($fee) {
                    if ($mo->table('codono_user_coin')->where(array($qbdz => $CoinInfo['zc_user']))->find()) {
                        //$rs[] = $mo->table('codono_user_coin')->where(array($qbdz => $CoinInfo['zc_user']))->setInc($coin, $fee);
                    } else {
                        $rs[] = $mo->table('codono_user_coin')->add(array($qbdz => $CoinInfo['zc_user'], $coin => $fee));
                    }
                }

                $rs[] = $mo->table('codono_myzc')->add(array('userid' => userid(), 'username' => $addr, 'coinname' => $coin, 'txid' => md5($addr . $user_coin[$coin . 'b'] . time()), 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'addtime' => time(), 'status' => 1));
                $rs[] = $mo->table('codono_myzr')->add(array('userid' => $peer['userid'], 'username' => $user_coin[$coin . 'b'], 'coinname' => $coin, 'txid' => md5($user_coin[$coin . 'b'] . $addr . time()), 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'addtime' => time(), 'status' => 1));

                if ($fee_user) {
                    $rs[] = $mo->table('codono_myzc_fee')->add(array('userid' => $fee_user['userid'], 'username' => $CoinInfo['zc_user'], 'coinname' => $coin, 'txid' => md5($user_coin[$coin . 'b'] . $CoinInfo['zc_user'] . time()), 'num' => $num, 'fee' => $fee, 'type' => 1, 'mum' => $mum, 'addtime' => time(), 'status' => 1));
                }

                if (check_arr($rs)) {
                    $mo->execute('commit');
                    $mo->execute('unlock tables');
                    session('myzc_verify', null);
                    $this->success(L('Transfer success!'));
                } else {
                    $mo->execute('rollback');
                    $this->error('Transfer Failed!');
                }
            } else {

                $dj_username = $CoinInfo['dj_yh'];
                $dj_password = $CoinInfo['dj_mm'];
                $dj_address = $CoinInfo['dj_zj'];
                $dj_port = $CoinInfo['dj_dk'];
                $dj_decimal=8;
                $CoinClient = CoinClient($dj_username, $dj_password, $dj_address, $dj_port, 5, array(), 1);
                $can_withdraw=1;
				
				$auto_status = ($CoinInfo['zc_zd'] && ($num < $CoinInfo['zc_zd']) ? 1 : 0);
				
                $json = $CoinClient->getinfo();
                
                if (!isset($json['version']) || !$json['version']) {
                 
				debug($coin.' Could not be connected at '.time() .'<br/>');			
				$can_withdraw=0;
                }

                $valid_res = $CoinClient->validateaddress($addr);
                
                if (!$valid_res['isvalid']) {
                    $this->error($addr . L('It is not a valid address wallet!'));
                }
                $daemon_balance=$CoinClient->getbalance();
                
                if ($daemon_balance < $num) {
                    //$this->error(L('Wallet balance of less than'));
				debug($coin.' :Low wallet balance: '.time() .'<br/>');			
				$can_withdraw=0;
                }
              
                $mo = M();
                $mo->execute('set autocommit=0');
                $mo->execute('lock tables codono_user_coin write, codono_myzc write,codono_myzr write, codono_myzc_fee write');
                $rs = array();
				//Reduce Withdrawers balance
                $rs[] = $r = $mo->table('codono_user_coin')->where(array('userid' => userid()))->setDec($coin, $num);
                //Add entry of withdraw [zc] in database 
				$rs[] = $aid = $mo->table('codono_myzc')->add(array('userid' => userid(), 'username' => $addr, 'coinname' => $coin, 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'addtime' => time(), 'status' => $auto_status));

                if ($fee && $auto_status) {
                    
                    $rs[] = $mo->table('codono_myzc_fee')->add(array('userid' => $fee_user['userid'], 'username' => $CoinInfo['zc_user'], 'coinname' => $coin, 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'type' => 2, 'addtime' => time(), 'status' => 1));

                    if ($mo->table('codono_user_coin')->where(array($qbdz => $CoinInfo['zc_user']))->find()) {
                        //$rs[] = $r = $mo->table('codono_user_coin')->where(array($qbdz => $CoinInfo['zc_user']))->setInc($coin, $fee);
                        debug(array('res' => $r, 'lastsql' => $mo->table('codono_user_coin')->getLastSql()), L(' Received fees amount'));
                    } else {
                        $rs[] = $r = $mo->table('codono_user_coin')->add(array($qbdz => $CoinInfo['zc_user'], $coin => $fee));
                    }
                }

                if (check_arr($rs)) {
                    
                    if ($auto_status && $can_withdraw==1) {
                        $mo->execute('commit');
                        $mo->execute('unlock tables');
                              if (strpos($dj_address, 'new') !== false) {
                              $send_amt=bcadd($mum,0,5);
                                  
                              }
                              else{
                                  $send_amt=(double)bcadd($mum,0,5);
                              }
                 
                        $sendrs = $CoinClient->sendtoaddress($addr, $send_amt);
                
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
                            //$this->error('Wallet Server  Withdraw failure:'.$sendrs);
                        $mo->execute('rollback');
                        $this->error('Wallet Server  Withdraw failure:'.($sendrs));
                        } else {
                        M('Myzc')->where(array('id' => $aid))->save(array('txid'=>$sendrs));
                            $this->success('Successful Withdrawal!');
                        }
                    }

                    if ($auto_status && $can_withdraw==1) {
                        $mo->execute('commit');
                        $mo->execute('unlock tables');
                        session('myzc_verify', null);
                        
                        $this->success('Successful Withdrawal!');
                    } else {
                        $mo->execute('commit');
                        $mo->execute('unlock tables');
                        session('myzc_verify', null);
                        $this->success('Withdrawal application is successful,Please wait for the review!');
                    }
                } else {
                    $mo->execute('rollback');
                    $this->error('Withdrawal failure!');
                }
            }
        }
		//Bitcoin Type Ends
    }


    public function upmyzr($coin, $codono_dzbz, $num, $paypassword, $cellphone_verify=0)
    {
        if (!userid()) {
            $this->error(L('YOU_NEED_TO_LOGIN'));
        }
		if(M_ONLY==1){
        if (!check($cellphone_verify, 'd')) {
            $this->error(L('INVALID_SMS_CODE'));
        }
		
        if ($cellphone_verify != session('myzr_verify')) {
            $this->error(L('INCORRECT_SMS_CODE'));
        }
		}

        $num = abs($num);

        if (!check($num, 'currency')) {
            $this->error(L('Number format error!'));
        }


        if (!check($paypassword, 'password')) {
            $this->error(L('Fund Pwd format error!'));
        }

        if (!check($coin, 'n')) {
            $this->error(L('Currency format error!'));
        }

        if (!C('coin')[$coin]) {
            $this->error(L('Currency wrong!'));
        }

        $Coin = M('Coin')->where(array('name' => $coin))->find();

        if (!$Coin) {
            $this->error(L('Currency wrong!'));
        }


        $user = M('User')->where(array('id' => userid()))->find();

        if (md5($paypassword) != $user['paypassword']) {
            $this->error(L('Trading password is wrong!'));
        }

        $codono_zrcoinaddress = $Coin['codono_coinaddress'];

        if ($Coin['type'] == 'offline') {

            M('myzr')->add(array('userid' => userid(), 'username' => $codono_dzbz, 'txid' => $codono_zrcoinaddress, 'coinname' => $coin, 'num' => $num, 'mum' => 0, 'addtime' => time(), 'status' => 0));

            $this->success(L('We have received your deposit request , It will be processes shortly!'));

        } else {
            $this->error("Wallet coins are not allowed to operate!");
        }

    }


    public function mywt($market = NULL, $type = NULL, $status = NULL)
    {
        if (!userid()) {
            redirect('/#login');
        }

        $this->assign('prompt_text', D('Text')->get_content('finance_mywt'));
        check_server();
        $Coin = M('Coin')->where(array('status' => 1))->select();

        foreach ($Coin as $k => $v) {
            $coin_list[$v['name']] = $v;
        }

        $this->assign('coin_list', $coin_list);
        $Market = M('Market')->where(array('status' => 1))->select();

        foreach ($Market as $k => $v) {
            $v['xnb'] = explode('_', $v['name'])[0];
            $v['rmb'] = explode('_', $v['name'])[1];
            $market_list[$v['name']] = $v;
        }

        $this->assign('market_list', $market_list);

        if (!$market_list[$market]) {
            $market = $Market[0]['name'];
        }

        $where['market'] = $market;

        if (($type == 1) || ($type == 2)) {
            $where['type'] = $type;
        }

        if (($status == 1) || ($status == 2) || ($status == 3)) {
            $where['status'] = $status - 1;
        }

        $where['userid'] = userid();
        $this->assign('market', $market);
        $this->assign('type', $type);
        $this->assign('status', $status);
        $Model = M('Trade');
        $count = $Model->where($where)->count();
        $Page = new \Think\Page($count, 10);
        //$Page->parameter .= 'type=' . $type . '&status=' . $status . '&market=' . $market . '&';
        $show = $Page->show();

        $list = $Model->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

        foreach ($list as $k => $v) {
            $list[$k]['num'] = $v['num'] * 1;
            $list[$k]['price'] = $v['price'] * 1;
            $list[$k]['deal'] = $v['deal'] * 1;
        }

        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }

    public function mycj($market = NULL, $type = NULL)
    {
        if (!userid()) {
            redirect('/#login');
        }

        $this->assign('prompt_text', D('Text')->get_content('finance_mycj'));
        check_server();
        $Coin = M('Coin')->where(array('status' => 1))->select();

        foreach ($Coin as $k => $v) {
            $coin_list[$v['name']] = $v;
        }

        $this->assign('coin_list', $coin_list);
        $Market = M('Market')->where(array('status' => 1))->select();

        foreach ($Market as $k => $v) {
            $v['xnb'] = explode('_', $v['name'])[0];
            $v['rmb'] = explode('_', $v['name'])[1];
            $market_list[$v['name']] = $v;
        }

        $this->assign('market_list', $market_list);

        if (!$market_list[$market]) {
            $market = $Market[0]['name'];
        }

        if ($type == 1) {
            $where = 'userid=' . userid() . ' && market=\'' . $market . '\'';
        } else if ($type == 2) {
            $where = 'peerid=' . userid() . ' && market=\'' . $market . '\'';
        } else {
            $where = '((userid=' . userid() . ') || (peerid=' . userid() . ')) && market=\'' . $market . '\'';
        }

        $this->assign('market', $market);
        $this->assign('type', $type);
        $this->assign('userid', userid());
        $Model = M('TradeLog');
        $count = $Model->where($where)->count();
        $Page = new \Think\Page($count, 15);
        $Page->parameter .= 'type=' . $type . '&market=' . $market . '&';
        $show = $Page->show();
        $list = $Model->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

        foreach ($list as $k => $v) {
            $list[$k]['num'] = $v['num'] * 1;
            $list[$k]['price'] = $v['price'] * 1;
            $list[$k]['mum'] = $v['mum'] * 1;
            $list[$k]['fee_buy'] = $v['fee_buy'] * 1;
            $list[$k]['fee_sell'] = $v['fee_sell'] * 1;
        }

        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }

    public function mytj()
    {
        if (!userid()) {
            redirect('/#login');
        }

        $this->assign('prompt_text', D('Text')->get_content('finance_mytj'));
        check_server();
        $user = M('User')->where(array('id' => userid()))->find();

        if (!$user['invit']) {
            for (; true;) {
                $tradeno = tradenoa();

                if (!M('User')->where(array('invit' => $tradeno))->find()) {
                    break;
                }
            }

            M('User')->where(array('id' => userid()))->save(array('invit' => $tradeno));
            $user = M('User')->where(array('id' => userid()))->find();
        }

        $this->assign('user', $user);
        $this->display();
    }

    public function mywd()
    {
        if (!userid()) {
            redirect('/#login');
        }

        $this->assign('prompt_text', D('Text')->get_content('finance_mywd'));
        check_server();
        $where['invit_1'] = userid();
        $Model = M('User');
        $count = $Model->where($where)->count();
        $Page = new \Think\Page($count, 10);
        $show = $Page->show();
        $list = $Model->where($where)->order('id asc')->field('id,username,cellphone,addtime,invit_1')->limit($Page->firstRow . ',' . $Page->listRows)->select();


        foreach ($list as $k => $v) {
            $list[$k]['invits'] = M('User')->where(array('invit_1' => $v['id']))->order('id asc')->field('id,username,cellphone,addtime,invit_1')->select();
            $list[$k]['invitss'] = count($list[$k]['invits']);

            foreach ($list[$k]['invits'] as $kk => $vv) {
                $list[$k]['invits'][$kk]['invits'] = M('User')->where(array('invit_1' => $vv['id']))->order('id asc')->field('id,username,cellphone,addtime,invit_1')->select();
                $list[$k]['invits'][$kk]['invitss'] = count($list[$k]['invits'][$kk]['invits']);
            }
        }

        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }

    public function myjp()
    {
        if (!userid()) {
            redirect('/#login');
        }

        $this->assign('prompt_text', D('Text')->get_content('finance_myjp'));
        check_server();
        $where['userid'] = userid();
        $Model = M('Invit');
        $count = $Model->where($where)->count();
        $Page = new \Think\Page($count, 10);
        $show = $Page->show();
        $list = $Model->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

        foreach ($list as $k => $v) {
            $list[$k]['invit'] = M('User')->where(array('id' => $v['invit']))->getField('username');
        }

        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }

    public function myaward()
    {
        if (!userid()) {
            redirect('/#login');
        }

        $this->assign('prompt_text', D('Text')->get_content('finance_myaward'));
        //check_server();
        $where['userid'] = userid();
        $Model = M('UserAward');
        $count = $Model->where($where)->count();
        $Page = new \Think\Page($count, 10);
        $show = $Page->show();
        $list = $Model->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

        foreach ($list as $k => $v) {
            $list[$k]['username'] = M('User')->where(array('id' => $v['userid']))->getField('username');
        }

        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }
}
?>