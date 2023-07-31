<?php

namespace Tapi\Controller;

class UserController extends CommonController
{
	public function profile(){
		$ret = array();
        $uid = $this->userid();
        $user_all_info= M('User')->where(array('id' => $uid))->find();
		$ret['username']=$user_all_info['username'];
		$ret['truename']=$user_all_info['truename'];
		$ret['kyc']=$user_all_info['idcardauth'];
		$ret['email']=$user_all_info['email'];
		if($user_all_info['cellphone']){
		$ret['mobile']=$user_all_info['cellphones'].$user_all_info['cellphone'];
		}
		$return[]=$ret;
	
		$this->ajaxShow($return);
	}
	/*
	curpass
	newpass
	repass 
	header with UID and TOKEN
	*/
	public function updatepass()
    {
		
		$input =  $_POST = json_decode(file_get_contents('php://input'), true);
		
		$curpass=$input['curpass'];
		$newpass=$input['newpass'];
		$repass=$input['repass'];
		$uid = $this->userid();
	
        if (!check($curpass, 'password')) {
   //         $this->error(L('Incorrect Current password format!'));
        }

        if (!check($newpass, 'password')) {
            $this->error(L('New password incorrect format!'));
        }

        if ($newpass != $repass) {
            $this->error(L('Password does not match!'));
        }

        $password = M('User')->where(array('id' => $uid))->getField('password');

        if (md5($curpass) != $password) {
            $this->error(L('Your current password is wrong!'));
        }

        $rs = M('User')->where(array('id' => $uid))->save(array('password' => md5($newpass)));

        if ($rs) {
            $this->success(L('Successfully changed'));
        } else {
            $this->error('No changes could be made!');
        }
    }
	public function FiatBalance(){
		$uid = $this->userid();

        $CoinList = M('Coin')->where(array('status' => 1))->select();
        $UserCoin = M('UserCoin')->where(array('userid' => $uid))->find();
        $Market = M('Market')->where(array('status' => 1))->select();

        foreach ($Market as $k => $v) {
            $Market[$v['name']] = $v;
        }

        $usd['zj'] = 0;

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

			   {
                    $coinList[$v['name']] = array('name' => $v['name'], 'img' => $v['img'], 'title' => $v['title'] . '(' . strtoupper($v['name']) . ')', 'xnb' => round($UserCoin[$v['name']], 6) * 1, 'xnbd' => round($UserCoin[$v['name'] . 'd'], 6) * 1, 'xnbz' => round($UserCoin[$v['name']] + $UserCoin[$v['name'] . 'd'], 6), 'jia' =>NumToStr( $jia * 1), 'zhehe' => round(($UserCoin[$v['name']] + $UserCoin[$v['name'] . 'd']) * $jia, 2));
                }
                $usd['zj'] = round($usd['zj'] + (($UserCoin[$v['name']] + $UserCoin[$v['name'] . 'd']) * $jia), 2) * 1;
            }
        }

		$ret['coin']['usd'] = $usd;
        $ret['coin']['coinList'] = $coinList;
        $this->ajaxShow($ret);

	}
    public function userinfo()
    {
        $ret = array();
        $uid = $this->userid();
        $user_all_info= M('User')->where(array('id' => $uid))->find();
		$ret['baseinfo']['username']=$user_all_info['username'];
		$ret['baseinfo']['truename']=$user_all_info['truename'];
		$ret['baseinfo']['kyc']=$user_all_info['idcardauth'];
		$ret['baseinfo']['email']=$user_all_info['email'];
		$ret['baseinfo']['mobile']=$user_all_info['cellphones'].$user_all_info['cellphone'];
		
        $CoinList = M('Coin')->where(array('status' => 1))->select();
        $UserCoin = M('UserCoin')->where(array('userid' => $uid))->find();
        $Market = M('Market')->where(array('status' => 1))->select();

        $market_type = array();
        foreach ($Market as $k => $v) {
            $Market[$v['name']] = $v;
            $keykey = explode('_', $v['name'])[0];
            $market_type[$keykey] = $v['name'];
        }

        $usd['zj'] = 0;

        foreach ($CoinList as $k => $v) {
            if ($v['name'] == 'usd') {
                $usd['ky'] = $UserCoin[$v['name']] * 1;
                $usd['dj'] = $UserCoin[$v['name'] . 'd'] * 1;
                $usd['zj'] = $usd['zj'] + $usd['ky'] + $usd['dj'];
            } else {

                $curMarketType = $market_type[$v['name']];

                if (isset($Market[$curMarketType])) {
                    $jia = $Market[$curMarketType]['new_price'];
                    $marketid = $Market[$curMarketType]['id'];
                } else {
                    $jia = 1;
                    $marketid = 0;
                }

                $coinList[] = array('id' => $marketid, 'name' => $v['name'], 'ico' => $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/Upload/coin/' . $v['img'], 'title' => $v['title'] . '(' . strtoupper($v['name']) . ')', 'xnb' => round($UserCoin[$v['name']] * 1, 4), 'xnbd' => round($UserCoin[$v['name'] . 'd'] * 1, 4), 'xnbz' => round($UserCoin[$v['name']] + $UserCoin[$v['name'] . 'd'], 4), 'jia' => $jia * 1, 'zhehe' => round(($UserCoin[$v['name']] + $UserCoin[$v['name'] . 'd']) * $jia, 4));
                $usd['zj'] = $usd['zj'] + (round(($UserCoin[$v['name']] + $UserCoin[$v['name'] . 'd']) * $jia, 4) * 1);
            }
        }

        $ret['coin']['usd'] = $usd;
        $ret['coin']['coinList'] = $coinList;
        $this->ajaxShow($ret);
    }
	 public function wallet()
    {
        $ret = array();
        $uid = $this->userid();
        $ret['baseinfo'] = M('User')->where(array('id' => $uid))->find();
        $CoinList = M('Coin')->where(array('status' => 1))->select();
        $UserCoin = M('UserCoin')->where(array('userid' => $uid))->find();
        $Market = M('Market')->where(array('status' => 1))->select();

        $market_type = array();
        foreach ($Market as $k => $v) {
            $Market[$v['name']] = $v;
            $keykey = explode('_', $v['name'])[0];
            $market_type[$keykey] = $v['name'];
        }

        $usd['zj'] = 0;

        foreach ($CoinList as $k => $v) {
            if ($v['name'] == 'usd') {
                $usd['ky'] = $UserCoin[$v['name']] * 1;
                $usd['dj'] = $UserCoin[$v['name'] . 'd'] * 1;
                $usd['zj'] = $usd['zj'] + $usd['ky'] + $usd['dj'];
            } else {

                $curMarketType = $market_type[$v['name']];

                if (isset($Market[$curMarketType])) {
                    $jia = $Market[$curMarketType]['new_price'];
                    $marketid = $Market[$curMarketType]['id'];
                } else {
                    $jia = 1;
                    $marketid = 0;
                }
				$icon=$v['img']?$v['img']:'usd.png';
                $coinList[] = array('id' => $marketid,'sort' => (int)$v['sort'], 'name' => $v['title'],'symbol' => $v['name'], 'ico' => SITE_URL. '/Upload/coin/' .$icon, 'title' => $v['title'] . '(' . strtoupper($v['name']) . ')', 'total' => round($UserCoin[$v['name']] * 1, 4),'xnb' => round($UserCoin[$v['name']] * 1, 4), 'xnbd' => round($UserCoin[$v['name'] . 'd'] * 1, 4), 'xnbz' => round($UserCoin[$v['name']] + $UserCoin[$v['name'] . 'd'], 4), 'jia' => $jia * 1, 'zhehe' => round(($UserCoin[$v['name']] + $UserCoin[$v['name'] . 'd']) * $jia, 4),'deposits'=>(int)$v['zr_jz'],'withdrawals'=>(int)$v['zc_jz'],'min_withdrawal'=>$v['zc_min'],'withdraw_fees'=>$v['zc_fee']);
                $usd['zj'] = $usd['zj'] + (round(($UserCoin[$v['name']] + $UserCoin[$v['name'] . 'd']) * $jia, 4) * 1);
            }
        }

        $ret['coin']['usd'] = $usd;
        $ret['coin']['coinList'] = $coinList;
		$fiat['worth']=$usd['zj'];
		$fiat['intrading']=$usd['dj'];
		$fiat['available']=$usd['ky'];
        //$this->ajaxShow($coinList);
		 $array = array('status' => 1, 'message' => 'Wallet Balance', 'data'=>$coinList,'fiat'=>$fiat);
        echo json_encode($array);exit;
    }

    public function bank()
    {
        $uid = $this->userid();
        $truename = M('User')->where(array('id' => $uid))->getField('truename');
        $UserBank = M('UserBank')->where(array('userid' => $uid, 'status' => 1))->order('id desc')->select();

        foreach ($UserBank as $key => $val) {
            $UserBank[$key]['truename'] = $truename . rand(0, 100);
            $UserBank[$key]['addtime'] = addtime($val['addtime']);
        }

        $this->ajaxShow($UserBank);
    }

    public function upbank($name, $bank, $bankprov, $bankcity, $bankaddr, $bankcard)
    {
        $uid = $this->userid();

        if (!check($name, 'a')) {
            $this->error('Note the name of the wrong format!');
        }

        if (!check($bank, 'a')) {
            $this->error('Bank malformed!');
        }

        if (!check($bankprov, 'c')) {
            $this->error('Opening provinces format error!');
        }

        if (!check($bankcity, 'c')) {
            $this->error('Opening provinces malformed2!');
        }

        if (!check($bankaddr, 'a')) {
            $this->error('Bank address format error!');
        }

        if (!check($bankcard, 'd')) {
            $this->error('Bank account number format error!');
        }

        if (!M('UserBankType')->where(array('title' => $bank))->find()) {
            $this->error('Bank error!');
        }

        $userBank = M('UserBank')->where(array('userid' => $uid))->select();

        foreach ($userBank as $k => $v) {
            if ($v['name'] == $name) {
                $this->error('Please do not use the same name Notes!');
            }

            if ($v['bankcard'] == $bankcard) {
                $this->error('Bank card number already exists!');
            }
        }

        if (10 <= count($userBank)) {
            $this->error('EachuserCan onlyAdd to10Addresses!');
        }

        if (M('UserBank')->add(array('userid' => $uid, 'name' => $name, 'bank' => $bank, 'bankprov' => $bankprov, 'bankcity' => $bankcity, 'bankaddr' => $bankaddr, 'bankcard' => $bankcard, 'addtime' => time(), 'status' => 1))) {
            $this->success('Banks added successfully!');
        } else {
            $this->error('Bank Add Failed!');
        }
    }

    public function delbank($id)
    {
        $uid = $this->userid();

        if (!check($id, 'd')) {
            $this->error(L('INCORRECT_REQ'));
        }

        $Bank = M('UserBank')->where(array('userid' => $uid, 'id' => $id))->find();

        if (!$Bank) {
            $this->error('Unauthorized access!');
        } else if (M('UserBank')->where(array('id' => $id))->delete()) {
            $this->success('successfully deleted!');
        } else {
            $this->error('failed to delete!');
        }
    }

    public function mybank()
    {
        $ret = array();
        $uid = $this->userid();
        $ret['id'] = $uid;
        $user = M('User')->where(array('id' => $uid))->field('cellphone')->find();
        $ret['cellphone'] = $user['cellphone'];
        $user_coin = M('UserCoin')->where(array('userid' => $uid))->field('usd')->find();
        $ret['usd'] = $user_coin['usd'] * 1;
        $userBankList = M('UserBank')->where(array('userid' => $uid, 'status' => 1))->order('id desc')->select();

        foreach ($userBankList as $key => $val) {
            $userBankList[$key]['show'] = $val['name'] . '(Tail number:' . substr($val['bankcard'], -4) . ')';
        }

        $ret['userBankList'] = $userBankList;
        $this->ajaxShow($ret);
    }

    public function sendCellphone()
    {
        $uid = $this->userid();
        $user = M('User')->where(array('id' => $uid))->field('cellphone')->find();
        $ret['cellphone'] = $user['cellphone'];
        $code = rand(1000, 9999);
        S('sendMobile_code_' . $ret['cellphone'], $code);
        S('sendMobile_cellphone_' . $ret['cellphone'], $ret['cellphone']);
        //$this->success('Verification code has been sent to the:' . $ret['cellphone'] . '(' . $code . ')');
        $this->success('Verification code has been sent to the:' . $ret['cellphone']);
    }

    public function upmytx($cellphone_verify, $num, $paypassword, $type)
    {
        $uid = $this->userid();
        $user = M('User')->where(array('id' => $uid))->field('cellphone')->find();
        $code = S('sendMobile_code_' . $user['cellphone']);

        if ($cellphone_verify != $code) {
            $this->error(L('INCORRECT_SMS_CODE'));
        } else {
            S('sendMobile_code_' . $user['cellphone'], null);
        }

        if (!check($num, 'd')) {
            $this->error('The amount of withdrawals format error!');
        }

        $mytx_min = (C('mytx_min') ? C('mytx_min') : 1);
        $mytx_max = (C('mytx_max') ? C('mytx_max') : 1000000);
        $mytx_bei = C('mytx_bei');
        $mytx_fee = C('mytx_fee');

        if ($num < $mytx_min) {
            $this->error('Every withdrawal amount can not be less than ' . $mytx_min . ' !');
        }

        if ($mytx_max < $num) {
            $this->error('Every withdrawal amount can not exceed ' . $mytx_max . ' !');
        }

        if ($mytx_bei) {
            if ($num % $mytx_bei != 0) {
                $this->error('Every mention the amount of cash must be' . $mytx_bei . ' integer multiples!');
            }
        }

        if (!check($paypassword, 'password')) {
            $this->error('Fund Pwd format error!');
        }

        $userBank = M('UserBank')->where(array('id' => $type))->find();

        if (!$userBank) {
            $this->error('Withdraw wrong address!');
        }

        $user = M('User')->where(array('id' => $uid))->find();

        if (md5($paypassword) != $user['paypassword']) {
            $this->error('Trading password is wrong!');
        }

        $usd = M('UserCoin')->where(array('userid' => $uid))->getField('usd');

        if ($usd < $num) {
            $this->error('Lack of available USD Balance!');
        }

        $fee = round(($num / 100) * $mytx_fee, 2);
        $mum = round(($num / 100) * (100 - $mytx_fee), 2);
        $mo = M();
        $mo->execute('set autocommit=0');
        $mo->execute('lock tables codono_mytx write , codono_user_coin  write ');
        $rs = array();
        $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $uid))->setDec('usd', $num);
        $rs[] = $mo->table('codono_mytx')->add(array('userid' => $uid, 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'name' => $userBank['name'], 'truename' => $user['truename'], 'bank' => $userBank['bank'], 'bankprov' => $userBank['bankprov'], 'bankcity' => $userBank['bankcity'], 'bankaddr' => $userBank['bankaddr'], 'bankcard' => $userBank['bankcard'], 'addtime' => time(), 'status' => 0));

        if (check_arr($rs)) {
            $mo->execute('commit');
            $mo->execute('unlock tables');
            $this->success('Withdrawal order to create success!');
        } else {
            $mo->execute('rollback');
            $this->error('Withdraw order creation failed!');
        }
    }

    public function mytx_log()
    {
        $pid = (isset($_GET['pid']) ? abs(intval($_GET['pid'])) : 1);
        $limit = 5;
        $id = $this->userid();
        $list = M('Mytx')->where(array('userid = ' . $id))->limit(($pid - 1) * $limit, $limit)->select();

        if (!$list) {
            $this->ajaxShow('No withdrawal records', 0);
        }

        foreach ($list as $key => $val) {
            $list[$key]['addtime'] = addtime($val['addtime']);
        }

        $this->ajaxShow($list);
    }

    public function upmycz($type, $num)
    {
        $id = $this->userid();

        if (!check($type, 'n')) {
            $this->error('Recharge way malformed!');
        }

        $myczType = M('MyczType')->where(array('status' => 1))->select();

        foreach ($myczType as $k => $v) {
            $myczTypeList[$v['name']] = $v['title'];
        }

        if (!$myczTypeList[$type]) {
            $this->error('Recharge the wrong way!');
        }

        if (!check($num, 'usd')) {
            $this->error('Recharge amount malformed!');
        }

        $mycz_min = (C('mycz_min') ? C('mycz_min') : 1);
        $mycz_max = (C('mycz_max') ? C('mycz_max') : 100000);

        if ($num < $mycz_min) {
            $this->error('Recharge amount can not be less than' . $mycz_min . ' !');
        }

        if ($mycz_max < $num) {
            $this->error('Recharge amount can not exceed' . $mycz_max . ' !');
        }

        for (; true;) {
            $tradeno = tradeno();

            if (!M('Mycz')->where(array('tradeno' => $tradeno))->find()) {
                break;
            }
        }

        $time = time();
        $mycz = M('Mycz')->add(array('userid' => $id, 'num' => $num, 'type' => $type, 'tradeno' => $tradeno, 'addtime' => $time, 'status' => 0));

        if ($mycz) {
            $this->success(array('time' => addtime($time), 'data' => 'Prepaid orders created successfully!', 'tradeno' => $tradeno, 'type' => $type, 'num' => $num));
        } else {
            $this->error('Withdraw order creation failed!');
        }
    }

    public function mycz_log()
    {
        $pid = (isset($_GET['pid']) ? abs(intval($_GET['pid'])) : 1);
        $limit = 5;
        $id = $this->userid();
        $list = M('Mycz')->where(array('userid = ' . $id))->limit(($pid - 1) * $limit, $limit)->select();

        if (!$list) {
            if ($pid == 1) {
                $this->ajaxShow('No Recharge Records', 0);
            } else {
                $this->ajaxShow('No more recharge records', 0);
            }
        }

        foreach ($list as $key => $val) {
            $list[$key]['addtime'] = addtime($val['addtime']);
        }

        $this->ajaxShow($list);
    }

    public function myzr($coin_id)
    {
        $coin_id = (int)$coin_id;

        if (!$coin_id) {
            $this->error('coin_id error');
        }

        $uid = $this->userid();
        $user_coin = M('UserCoin')->where(array('userid' => $uid))->find();
        $CoinArr = M('Coin')->where(array('id' => $coin_id))->find();
        $coin = $CoinArr['name'];

        if (!$CoinArr['zr_jz']) {
            $this->error('The current ban into the currency!');
        } else {
            $qbdz = $coin . 'b';

            if (!$user_coin[$qbdz]) {
                if ($CoinArr['type'] == 'rgb') {
                    $wallet = md5(md5(rand(0, 10000)) . $coin);
                    $rs = M('UserCoin')->where(array('userid' => $uid))->save(array($qbdz => $wallet));

                    if (!$rs) {
                        $this->error('Generate wallet address wrong!');
                    }
                }
				if ($CoinArr['type'] == 'eth') {
                        $heyue = $CoinArr['dj_yh'];//Contract Address
						$dj_password = $CoinArr['dj_mm'];
						$dj_address = $CoinArr['dj_zj'];
						$dj_port = $CoinArr['dj_dk'];
                        $EthCommon = new \Org\Util\EthCommon($dj_address, $dj_port, "2.0");
                        $EthPayLocal = new \Org\Util\EthPayLocal($dj_address, $dj_port, "2.0", $heyue);
                        if (!$heyue) {
                            //eth
                            //Call the interface to generate a new wallet address
							$wall_pass=ETH_USER_PASS;
							$wallet = $EthPayLocal->personal_newAccount($wall_pass);
                           
                            if ($wallet) {
                                $saveme[$qbdz]=$wallet;
								$saveme[$coin.'_pass']=cryptString($wall_pass);
								$rs[] = M('UserCoin')->where(array('userid' => userid()))->save($saveme);
                            } else {
								//$this->error('Error generating wallet address 2!');
								$wallet=L('Wallet service is currently offline! ErrorCode:USER1');
                            }
                        } else {
                            //Eth contract
                            $rs1 = M('UserCoin')->where(array('userid' => userid()))->find();
                            if ($rs1['ethb']) {
                                $wallet = $rs1['ethb'];
								$saveme[$qbdz]=$wallet;
								$saveme[$coin.'_pass']=cryptString(ETH_USER_PASS);
                                $rs[] = M('UserCoin')->where(array('userid' => userid()))->save($saveme);
                            } else {
                                //Call the interface to generate a new wallet address
								$wall_pass= ETH_USER_PASS;
								$wallet = $EthPayLocal->personal_newAccount($wall_pass);
                              
                                if ($wallet) {
									$saveme['ethb']=$wallet;
									$saveme[$qbdz]=$wallet;
									$saveme[$coin.'_pass']=cryptString(ETH_USER_PASS);
                                    $rs[] = M('UserCoin')->where(array('userid' => userid()))->save($saveme);
                                } else {
                                    //$this->error('Error generating wallet address 2!');
									$wallet=L('Wallet service is currently offline! ErrorCode:USER2');
                                }

                            }
                        }

                    }
				//Blockio Ends
				if ($CoinArr['type'] == 'blockio') {
                    $dj_username = $CoinArr['dj_yh'];
                    $dj_password = $CoinArr['dj_mm'];
                    $dj_address = $CoinArr['dj_zj'];
                    $dj_port = $CoinArr['dj_dk'];

					$block_io = BlockIO($dj_username, $dj_password, $dj_address, $dj_port, 5, array(), 1);
					$json = $block_io->get_balance();
                    if (!isset($json->status) || $json->status!='success') {
                        $this->error('Wallet link failure!');
                    }

                    $wallet_addr = $block_io->get_address_by_label(array('label' => username()));

                    if (!is_array($wallet_addr)) {
						  $getNewAddressInfo = $block_io->get_new_address(array('label' => username()));
						  $wallet_ad=$getNewAddressInfo->data->address;

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

                    $rs = M('UserCoin')->where(array('userid' => $uid))->save(array($qbdz => $wallet));

                    if (!$rs) {
                        $this->error('Add error address wallet3!');
                    }
                }
				//Blockio Ends
                if ($CoinArr['type'] == 'qbb') {
                    $dj_username = $CoinArr['dj_yh'];
                    $dj_password = $CoinArr['dj_mm'];
                    $dj_address = $CoinArr['dj_zj'];
                    $dj_port = $CoinArr['dj_dk'];
                    $CoinArrClient = CoinClient($dj_username, $dj_password, $dj_address, $dj_port, 5, array(), 1);
                    $json = $CoinArrClient->getinfo();

                    if (!isset($json['version']) || !$json['version']) {
                        $this->error('Wallet link failure!');
                    }

                    $wallet_addr = $CoinArrClient->getaddressesbyaccount(username());

                    if (!is_array($wallet_addr)) {
                        $wallet_ad = $CoinArrClient->getnewaddress(username());

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

                    $rs = M('UserCoin')->where(array('userid' => $uid))->save(array($qbdz => $wallet));

                    if (!$rs) {
                        $this->error('Add error address wallet3!');
                    }
                }
            } else {
                $wallet = $user_coin[$coin . 'b'];
            }
        }

        $this->ajaxShow(array(
            'type' => $CoinArr['type'] == 'rgb' ? 'Subscription currency' : 'Coin Wallet',
            'confirm' => (int)$CoinArr['zr_dz'],
            'coin' => array('num' => NumToStr($user_coin[$coin]), 'fnum' => NumToStr($user_coin[$coin . 'd'])),
            'addr' => $wallet
        ));
    }

    public function myzr_log($coin_id)
    {
        $pid = (isset($_GET['pid']) ? abs(intval($_GET['pid'])) : 1);
        $limit = 5;
        $id = $this->userid();
        $coin_id = (int)$coin_id;

        if (!$coin_id) {
            $this->error('coin_id error');
        }

        $CoinArr = M('Coin')->where(array('id' => $coin_id))->find();
        $list = M('Myzr')->where(array('userid = ' . $id . ' and coinname = \'' . $CoinArr['name'] . '\''))->order('id desc')->limit(($pid - 1) * $limit, $limit)->select();

        if (!$list) {
            if ($pid == 1) {
                $this->ajaxShow('No record', 0);
            } else {
                $this->ajaxShow('No more a', 0);
            }
        }

        $ret = array();

        foreach ($list as $key => $val) {
            $ret[$key]['id'] = $val['id'];
            $ret[$key]['addr'] = $val['username'];
            $ret[$key]['txid'] = $val['txid'];
            $ret[$key]['num'] = NumToStr($val['num']);
            $ret[$key]['mum'] = NumToStr($val['mum']);
            $ret[$key]['fee'] = NumToStr($val['fee']);
            $ret[$key]['status'] = $val['status'];
            $ret[$key]['status_str'] = ($val['status'] == 1 ? 'Into success' : 'Need' . abs($val['status']) . 'confirm');
            $ret[$key]['addtime'] = addtime($val['addtime']);
        }

        $this->ajaxShow($ret);
    }

    public function myzc($coin_id)
    {
        $coin_id = (int)$coin_id;

        if (!$coin_id) {
            $this->error('coin_id error');
        }

        $uid = $this->userid();
        $user_coin = M('UserCoin')->where(array('userid' => $uid))->find();
        $Coin = M('Coin')->where(array(
            'status' => 1,
            'name' => array('neq', 'usd')
        ))->select();

        foreach ($Coin as $k => $v) {
            $coin_list[$v['name']] = $v;
        }

        $CoinArr = M('Coin')->where(array('id' => $coin_id))->find();
        $coin = $CoinArr['name'];

        if (!$coin_list[$coin]['zc_jz']) {
            $this->assign('zc_jz', 'The current currency prohibited turn out!');
        } else {
            $userWalletList = M('UserWallet')->where(array('userid' => $uid, 'status' => 1, 'coinname' => $coin))->order('id desc')->select();
            $cellphone = M('User')->where(array('id' => $uid))->getField('cellphone');

            if ($cellphone) {
                $cellphone = substr_replace($cellphone, '****', 3, 4);
            } else {
                $cellphone = '';
            }
        }

        $this->ajaxShow(array(
            'coin' => array('num' => NumToStr($user_coin[$coin]), 'fnum' => NumToStr($user_coin[$coin . 'd'])),
            'addr' => $userWalletList,
            'cellphone' => $cellphone
        ));
    }

    public function upmyzc($coin_id, $cellphone_verify, $num, $paypassword, $addr)
    {
        $coin_id = (int)$coin_id;

        if (!$coin_id) {
            $this->error('coin_id error');
        }

        $CoinArr = M('Coin')->where(array('id' => $coin_id))->find();
        $coin = $CoinArr['name'];
        $uid = $this->userid();
        $user = M('User')->where(array('id' => $uid))->field('cellphone')->find();
        $code = S('sendMobile_code_' . $user['cellphone']);

        if ($cellphone_verify != $code) {
            $this->error(L('INCORRECT_SMS_CODE'));
        } else {
            S('sendMobile_code_' . $user['cellphone'], null);
        }

        $num = abs($num);

        if (!check($num, 'currency')) {
            $this->error('Number format error!');
        }

        if (!check($addr, 'dw')) {
            $this->error('Select wallet wrong!');
        }

        if (!check($paypassword, 'password')) {
            $this->error('Fund Pwd format error!');
        }

        if (!check($coin, 'n')) {
            $this->error('Currency format error!');
        }

        if (!C('coin')[$coin]) {
            $this->error('Currency wrong!');
        }

        $Coin = M('Coin')->where(array('name' => $coin))->find();

        if (!$Coin) {
            $this->error('Currency wrong!');
        }

        $myzc_min = ($Coin['zc_min'] ? abs($Coin['zc_min']) : 0.0001);
        $myzc_max = ($Coin['zc_max'] ? abs($Coin['zc_max']) : 10000000);

        if ($num < $myzc_min) {
            $this->error('Out beyond the minimum quantity limit system!');
        }

        if ($myzc_max < $num) {
            $this->error('Out of the number exceeds the maximum limit system!');
        }

        $user = M('User')->where(array('id' => $uid))->find();

        if (md5($paypassword) != $user['paypassword']) {
            $this->error('Trading password is wrong!');
        }

        $user_coin = M('UserCoin')->where(array('userid' => $uid))->find();

        if ($user_coin[$coin] < $num) {
            $this->error('Insufficient funds available');
        }

        $qbdz = $coin . 'b';
        $fee_user = M('UserCoin')->where(array($qbdz => $Coin['zc_user']))->find();

        if ($fee_user) {
            debug('Fee Address: ' . $Coin['zc_user'] . ' exist,There are fees');
            $fee = round(($num / 100) * $Coin['zc_fee'], 8);
            $mum = round($num - $fee, 8);

            if ($mum < 0) {
                $this->error('Roll-out fee mistake!');
            }

            if ($fee < 0) {
                $this->error('Roll-out fee set wrong!');
            }
        } else {
            debug('Fee Address: ' . $Coin['zc_user'] . ' does not exist,No fees');
            $fee = 0;
            $mum = $num;
        }

        if ($Coin['type'] == 'rgb') {
            debug($Coin, 'He began to turn out the subscription currency');
            $peer = M('UserCoin')->where(array($qbdz => $addr))->find();

            if (!$peer) {
                $this->error('Out of the subscription money address does not exist!');
            }

            $mo = M();
            $mo->execute('set autocommit=0');
            $mo->execute('lock tables  codono_user_coin write  , codono_myzc write  , codono_myzr write , codono_myzc_fee write');
            $rs = array();
            $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $uid))->setDec($coin, $num);
            $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $peer['userid']))->setInc($coin, $mum);

            if ($fee) {
                if ($mo->table('codono_user_coin')->where(array($qbdz => $Coin['zc_user']))->find()) {
                    //$rs[] = $mo->table('codono_user_coin')->where(array($qbdz => $Coin['zc_user']))->setInc($coin, $fee);
                    debug(array('msg' => 'Turn out to charge a fee' . $fee), 'fee');
                } else {
                    $rs[] = $mo->table('codono_user_coin')->add(array($qbdz => $Coin['zc_user'], $coin => $fee));
                    debug(array('msg' => 'Turn out to charge a fee' . $fee), 'fee');
                }
            }

            $rs[] = $mo->table('codono_myzc')->add(array('userid' => $uid, 'username' => $addr, 'coinname' => $coin, 'txid' => md5($addr . $user_coin[$coin . 'b'] . time()), 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'addtime' => time(), 'status' => 1));
            $rs[] = $mo->table('codono_myzr')->add(array('userid' => $peer['userid'], 'username' => $user_coin[$coin . 'b'], 'coinname' => $coin, 'txid' => md5($user_coin[$coin . 'b'] . $addr . time()), 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'addtime' => time(), 'status' => 1));

            if ($fee_user) {
                $rs[] = $mo->table('codono_myzc_fee')->add(array('userid' => $fee_user['userid'], 'username' => $Coin['zc_user'], 'coinname' => $coin, 'txid' => md5($user_coin[$coin . 'b'] . $Coin['zc_user'] . time()), 'num' => $num, 'fee' => $fee, 'type' => 1, 'mum' => $mum, 'addtime' => time(), 'status' => 1));
            }

            if (check_arr($rs)) {
                $mo->execute('commit');
                $mo->execute('unlock tables');
                $this->success('Transfer success!');
            } else {
                $mo->execute('rollback');
                debug(implode('|', $rs), 'failure');
                $this->error('Transfer Failed!');
            }
        }
		//Blockio Starts
		if ($Coin['type'] == 'blockio') {
            $mo = M();

            if ($mo->table('codono_user_coin')->where(array($qbdz => $addr))->find()) {
                debug($Coin, 'System starts the wallet credits');
                $peer = M('UserCoin')->where(array($qbdz => $addr))->find();

                if (!$peer) {
                    $this->error('Withdrawal address does not exist!');
                }

                $mo = M();
                $mo->execute('set autocommit=0');
                $mo->execute('lock tables  codono_user_coin write  , codono_myzc write  , codono_myzr write , codono_myzc_fee write');
                $rs = array();
                $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $uid))->setDec($coin, $num);
                $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $peer['userid']))->setInc($coin, $mum);

                if ($fee) {
                    if ($mo->table('codono_user_coin')->where(array($qbdz => $Coin['zc_user']))->find()) {
                        //$rs[] = $mo->table('codono_user_coin')->where(array($qbdz => $Coin['zc_user']))->setInc($coin, $fee);
                        debug(array('msg' => 'Withdraw to charge a fee' . $fee), 'fee');
                    } else {
                        $rs[] = $mo->table('codono_user_coin')->add(array($qbdz => $Coin['zc_user'], $coin => $fee));
                        debug(array('msg' => 'Withdraw to charge a fee' . $fee), 'fee');
                    }
                }

                $rs[] = $mo->table('codono_myzc')->add(array('userid' => $uid, 'username' => $addr, 'coinname' => $coin, 'txid' => md5($addr . $user_coin[$coin . 'b'] . time()), 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'addtime' => time(), 'status' => 1));
                $rs[] = $mo->table('codono_myzr')->add(array('userid' => $peer['userid'], 'username' => $user_coin[$coin . 'b'], 'coinname' => $coin, 'txid' => md5($user_coin[$coin . 'b'] . $addr . time()), 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'addtime' => time(), 'status' => 1));

                if ($fee_user) {
                    $rs[] = $mo->table('codono_myzc_fee')->add(array('userid' => $fee_user['userid'], 'username' => $Coin['zc_user'], 'coinname' => $coin, 'txid' => md5($user_coin[$coin . 'b'] . $Coin['zc_user'] . time()), 'num' => $num, 'fee' => $fee, 'type' => 1, 'mum' => $mum, 'addtime' => time(), 'status' => 1));
                }

                if (check_arr($rs)) {
                    $mo->execute('commit');
                    $mo->execute('unlock tables');
                    $this->success('Transfer success!');
                } else {
                    $mo->execute('rollback');
                    debug(implode('|', $rs), 'failure');
                    $this->error('Transfer Failed!');
                }
            } else {
                debug($Coin, 'Start transfer station outside the wallet credits');
                $dj_username = $Coin['dj_yh'];
                $dj_password = $Coin['dj_mm'];
                $dj_address = $Coin['dj_zj'];
                $dj_port = $Coin['dj_dk'];
                $block_io = BlockIO($dj_username, $dj_password, $dj_address, $dj_port, 5, array(), 1);
					$json = $block_io->get_balance();
                    if (!isset($json->status) || $json->status!='success') {
                        $this->error('Wallet link failure!');
                    }

                    $wallet_addr = $block_io->get_address_by_label(array('label' => username()));


                $valid_res = $block_io->validateaddress($addr);

                if (!$valid_res['isvalid']) {
                    $this->error($addr . 'It is not a valid address wallet!');
                }

                $auto_status = ($Coin['zc_zd'] && ($num < $Coin['zc_zd']) ? 1 : 0);
                debug(array('zc_zd' => $Coin['zc_zd'], 'mum' => $mum, 'auto_status' => $auto_status), 'The need for audit');

                if ($json->data->available_balance < $num) {
                    $this->error('Wallet balance of less than');
                }

                $mo = M();
                $mo->execute('set autocommit=0');
                $mo->execute('lock tables  codono_user_coin write  , codono_myzc write ,codono_myzr write, codono_myzc_fee write');
                $rs = array();
                $rs[] = $r = $mo->table('codono_user_coin')->where(array('userid' => $uid))->setDec($coin, $num);
                debug(array('res' => $r, 'lastsql' => $mo->table('codono_user_coin')->getLastSql()), 'Update user USD');
                $rs[] = $aid = $mo->table('codono_myzc')->add(array('userid' => $uid, 'username' => $addr, 'coinname' => $coin, 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'addtime' => time(), 'status' => $auto_status));

                if ($fee && $auto_status) {
                    $rs[] = $mo->table('codono_myzc_fee')->add(array('userid' => $fee_user['userid'], 'username' => $Coin['zc_user'], 'coinname' => $coin, 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'type' => 2, 'addtime' => time(), 'status' => 1));

                    if ($mo->table('codono_user_coin')->where(array($qbdz => $Coin['zc_user']))->find()) {
                        //$rs[] = $r = $mo->table('codono_user_coin')->where(array($qbdz => $Coin['zc_user']))->setInc($coin, $fee);
                        debug(array('res' => $r, 'lastsql' => $mo->table('codono_user_coin')->getLastSql()), 'Additional costs');
                    } else {
                        $rs[] = $r = $mo->table('codono_user_coin')->add(array($qbdz => $Coin['zc_user'], $coin => $fee));
                    }
                }

                debug(array('res' => $r, 'lastsql' => $mo->table('codono_myzc')->getLastSql()), ' Out Record');

                if (check_arr($rs)) {
                    if ($auto_status) {
                        $sendrs = $block_io->sendtoaddress($addr, $mum);

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
                            $mo->execute('rollback');
                            $mo->execute('unlock tables');
                            $this->error('wallet server Withdraw currency failure!');
                        }
                    }

                    if ($auto_status) {
                        $mo->execute('commit');
                        $mo->execute('unlock tables');
                        $this->success('Successful Withdrawal!');
                    } else {
                        $mo->execute('commit');
                        $mo->execute('unlock tables');
                        $this->success('The application is successful Withdrawal,Please wait for the review!');
                    }
                } else {
                    $mo->execute('rollback');
                    $this->error('Roll-out failure!');
                }
            }
        }
		//Blockio Starts

        if ($Coin['type'] == 'qbb') {
            $mo = M();

            if ($mo->table('codono_user_coin')->where(array($qbdz => $addr))->find()) {
                debug($Coin, 'The transfer station starts the wallet credits');
                $peer = M('UserCoin')->where(array($qbdz => $addr))->find();

                if (!$peer) {
                    $this->error('Turn the address does not exist!');
                }

                $mo = M();
                $mo->execute('set autocommit=0');
                $mo->execute('lock tables  codono_user_coin write  , codono_myzc write  , codono_myzr write , codono_myzc_fee write');
                $rs = array();
                $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $uid))->setDec($coin, $num);
                $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $peer['userid']))->setInc($coin, $mum);

                if ($fee) {
                    if ($mo->table('codono_user_coin')->where(array($qbdz => $Coin['zc_user']))->find()) {
                        //$rs[] = $mo->table('codono_user_coin')->where(array($qbdz => $Coin['zc_user']))->setInc($coin, $fee);
                        debug(array('msg' => 'Withdraw to charge a fee' . $fee), 'fee');
                    } else {
                        $rs[] = $mo->table('codono_user_coin')->add(array($qbdz => $Coin['zc_user'], $coin => $fee));
                        debug(array('msg' => 'Withdraw to charge a fee' . $fee), 'fee');
                    }
                }

                $rs[] = $mo->table('codono_myzc')->add(array('userid' => $uid, 'username' => $addr, 'coinname' => $coin, 'txid' => md5($addr . $user_coin[$coin . 'b'] . time()), 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'addtime' => time(), 'status' => 1));
                $rs[] = $mo->table('codono_myzr')->add(array('userid' => $peer['userid'], 'username' => $user_coin[$coin . 'b'], 'coinname' => $coin, 'txid' => md5($user_coin[$coin . 'b'] . $addr . time()), 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'addtime' => time(), 'status' => 1));

                if ($fee_user) {
                    $rs[] = $mo->table('codono_myzc_fee')->add(array('userid' => $fee_user['userid'], 'username' => $Coin['zc_user'], 'coinname' => $coin, 'txid' => md5($user_coin[$coin . 'b'] . $Coin['zc_user'] . time()), 'num' => $num, 'fee' => $fee, 'type' => 1, 'mum' => $mum, 'addtime' => time(), 'status' => 1));
                }

                if (check_arr($rs)) {
                    $mo->execute('commit');
                    $mo->execute('unlock tables');
                    $this->success('Transfer success!');
                } else {
                    $mo->execute('rollback');
                    debug(implode('|', $rs), 'failure');
                    $this->error('Transfer Failed!');
                }
            } else {
                debug($Coin, 'Start transfer station outside the wallet credits');
                $dj_username = $Coin['dj_yh'];
                $dj_password = $Coin['dj_mm'];
                $dj_address = $Coin['dj_zj'];
                $dj_port = $Coin['dj_dk'];
                $CoinClient = CoinClient($dj_username, $dj_password, $dj_address, $dj_port, 5, array(), 1);
                $json = $CoinClient->getinfo();

                if (!isset($json['version']) || !$json['version']) {
                    $this->error('Wallet link failure!');
                }

                $valid_res = $CoinClient->validateaddress($addr);

                if (!$valid_res['isvalid']) {
                    $this->error($addr . 'It is not a valid address wallet!');
                }

                $auto_status = ($Coin['zc_zd'] && ($num < $Coin['zc_zd']) ? 1 : 0);
                debug(array('zc_zd' => $Coin['zc_zd'], 'mum' => $mum, 'auto_status' => $auto_status), 'The need for audit');

                if ($json['balance'] < $num) {
                    $this->error('Wallet balance of less than');
                }

                $mo = M();
                $mo->execute('set autocommit=0');
                $mo->execute('lock tables  codono_user_coin write  , codono_myzc write ,codono_myzr write, codono_myzc_fee write');
                $rs = array();
                $rs[] = $r = $mo->table('codono_user_coin')->where(array('userid' => $uid))->setDec($coin, $num);
                debug(array('res' => $r, 'lastsql' => $mo->table('codono_user_coin')->getLastSql()), 'Update user USD');
                $rs[] = $aid = $mo->table('codono_myzc')->add(array('userid' => $uid, 'username' => $addr, 'coinname' => $coin, 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'addtime' => time(), 'status' => $auto_status));

                if ($fee && $auto_status) {
                    $rs[] = $mo->table('codono_myzc_fee')->add(array('userid' => $fee_user['userid'], 'username' => $Coin['zc_user'], 'coinname' => $coin, 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'type' => 2, 'addtime' => time(), 'status' => 1));

                    if ($mo->table('codono_user_coin')->where(array($qbdz => $Coin['zc_user']))->find()) {
                        //$rs[] = $r = $mo->table('codono_user_coin')->where(array($qbdz => $Coin['zc_user']))->setInc($coin, $fee);
                        debug(array('res' => $r, 'lastsql' => $mo->table('codono_user_coin')->getLastSql()), 'Additional costs');
                    } else {
                        $rs[] = $r = $mo->table('codono_user_coin')->add(array($qbdz => $Coin['zc_user'], $coin => $fee));
                    }
                }

                debug(array('res' => $r, 'lastsql' => $mo->table('codono_myzc')->getLastSql()), 'Out Record');

                if (check_arr($rs)) {
                    if ($auto_status) {
                        $sendrs = $CoinClient->sendtoaddress($addr, $mum);

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
                            $mo->execute('rollback');
                            $mo->execute('unlock tables');
                            $this->error('wallet server Withdraw currency failure!');
                        }
                    }

                    if ($auto_status) {
                        $mo->execute('commit');
                        $mo->execute('unlock tables');
                        $this->success('Successful Withdrawal!');
                    } else {
                        $mo->execute('commit');
                        $mo->execute('unlock tables');
                        $this->success('The application is successful Withdrawal,Please wait for the review!');
                    }
                } else {
                    $mo->execute('rollback');
                    $this->error('Roll-out failure!');
                }
            }
        }
    }

    public function myzc_log($coin_id)
    {
        $pid = (isset($_GET['pid']) ? abs(intval($_GET['pid'])) : 1);
        $limit = 5;
        $uid = $this->userid();
        $coin_id = (int)$coin_id;

        if (!$coin_id) {
            $this->error('coin_id error');
        }

        $CoinArr = M('Coin')->where(array('id' => $coin_id))->find();
        $list = M('Myzc')->where(array('userid = ' . $uid . ' and coinname = \'' . $CoinArr['name'] . '\''))->order('id desc')->limit(($pid - 1) * $limit, $limit)->select();

        if (!$list) {
            if ($pid == 1) {
                $this->ajaxShow('No record', 0);
            } else {
                $this->ajaxShow('No more a', 0);
            }
        }

        $ret = array();

        foreach ($list as $key => $val) {
            $ret[$key]['id'] = $val['id'];
            $ret[$key]['addr'] = $val['username'];
            $ret[$key]['txid'] = $val['txid'];
            $ret[$key]['num'] = NumToStr($val['num']);
            $ret[$key]['mum'] = NumToStr($val['mum']);
            $ret[$key]['fee'] = NumToStr($val['fee']);
            $ret[$key]['status'] = $val['status'];
            $ret[$key]['addtime'] = addtime($val['addtime']);
        }

        $this->ajaxShow($ret);
    }

    public function wallet_addr($coin_id)
    {
        $coin_id = (int)$coin_id;

        if (!$coin_id) {
            $this->error('coin_id error');
        }

        $id = $this->userid();
        $CoinArr = M('Coin')->where(array('id' => $coin_id))->find();
        $userWalletList = M('UserWallet')->where(array('userid' => $id, 'status' => 1, 'coinname' => $CoinArr['name']))->order('id desc')->select();

        foreach ($userWalletList as $key => $val) {
            $userWalletList[$key]['addtime'] = addtime($val['addtime']);
        }

        $this->ajaxShow($userWalletList);
    }

    public function upwallet($coin_id, $name, $addr, $paypassword)
    {
        $coin_id = (int)$coin_id;

        if (!$coin_id) {
            $this->error('coin_id error');
        }

        $uid = $this->userid();
        $CoinArr = M('Coin')->where(array('id' => $coin_id))->find();
        $coin = $CoinArr['name'];

        if (!check($name, 'a')) {
            $this->error('Note the name of the wrong format!');
        }

        if (!check($addr, 'dw')) {
            $this->error('Wallet address format error!');
        }

        if (!check($paypassword, 'password')) {
            $this->error('Fund Pwd format error!');
        }

        $user_paypassword = M('User')->where(array('id' => $uid))->getField('paypassword');

        if (md5($paypassword) != $user_paypassword) {
            $this->error('Trading password is wrong!');
        }

        if (!M('Coin')->where(array('name' => $coin))->find()) {
            $this->error('Currency wrong!');
        }

        $userWallet = M('UserWallet')->where(array('userid' => $uid, 'coinname' => $coin))->select();

        foreach ($userWallet as $k => $v) {
            if ($v['name'] == $name) {
                $this->error('Please do not use the same wallet logo!');
            }

            if ($v['addr'] == $addr) {
                $this->error('Wallet address already exists!');
            }
        }

        if (10 <= count($userWallet)) {
            $this->error('10 addresses max per users!');
        }

        if (M('UserWallet')->add(array('userid' => $uid, 'name' => $name, 'addr' => $addr, 'coinname' => $coin, 'addtime' => time(), 'status' => 1))) {
            $this->success(L('ADDED_SUCCESSFULLY'));
        } else {
            $this->error(L('FAILED_TO_ADD'));
        }
    }

    public function delwallet($id)
    {
        $uid = $this->userid();

        if (!check($id, 'd')) {
            $this->error(L('INCORRECT_REQ'));
        }

        if (!M('UserWallet')->where(array('userid' => $uid, 'id' => $id))->find()) {
            $this->error('Unauthorized access!');
        } else if (M('UserWallet')->where(array('userid' => $uid, 'id' => $id))->delete()) {
            $this->success('successfully deleted!');
        } else {
            $this->error('failed to delete!');
        }
    }

    public function mywt($market_id)
    {
        $pid = (isset($_GET['pid']) ? abs(intval($_GET['pid'])) : 1);
        $uid = $this->userid();
        $market_id = (int)$market_id;

        if (!$market_id) {
            $this->error('market_id error');
        }

        $marketArr = M('Market')->where(array('id' => $market_id))->find();
        $limit = 5;
        $list = M('Trade')->where(array('userid = ' . $uid . ' and market = \'' . $marketArr['name'] . '\''))->order('id desc')->limit(($pid - 1) * $limit, $limit)->select();

        if (!$list) {
            if ($pid == 1) {
                $this->ajaxShow('No record', 0);
            } else {
                $this->ajaxShow('No more a', 0);
            }
        }

        foreach ($list as $key => $val) {
            $list[$key]['num'] = NumToStr($val['num']) * 1;
            $list[$key]['mum'] = NumToStr($val['mum']) * 1;
            $list[$key]['fee'] = NumToStr($val['fee']) * 1;
            $list[$key]['price'] = NumToStr($val['price']) * 1;
            $list[$key]['deal'] = NumToStr($val['deal']) * 1;
            $list[$key]['addtime'] = addtime($val['addtime']);
        }

        $this->ajaxShow($list);
    }

    public function cancel_mywt($id)
    {
        $uid = $this->userid();

        if (!check($id, 'd')) {
            $this->error('Please select Undo commission!');
        }

        $trade = M('Trade')->where(array('id' => $id, 'userid' => $uid, 'status' => 0))->find();

        if (!$trade) {
            $this->error('The countermand parameter error!');
        }

        $market = $trade['market'];
        $xnb = explode('_', $market)[0];
        $rmb = explode('_', $market)[1];

        if ($trade['type'] == 1) {
            $trade_fee = C('market')[$trade['market']]['fee_buy'];
        } else if ($trade['type'] == 2) {
            $trade_fee = C('market')[$trade['market']]['fee_sell'];
        } else {
            $this->error('Transaction type error');
        }

        $mo = M();
        $mo->execute('set autocommit=0');
        $mo->execute('lock tables codono_user_coin write  , codono_trade write ');
        $rs = array();

        if ($trade['type'] == 1) {
            $mun = round(((($trade['num'] - $trade['deal']) * $trade['price']) / 100) * (100 + $trade_fee), 8);
            $user_buy = $mo->table('codono_user_coin')->where(array('userid' => $trade['userid']))->find();

            if ($mun <= round($user_buy[$rmb . 'd'], 8)) {
                $save_buy_rmb = $mun;
            } else if ($mun <= round($user_buy[$rmb . 'd'], 8) + 1) {
                $save_buy_rmb = $user_buy[$rmb . 'd'];
            } else {
                $mo->execute('rollback');
                $mo->execute('unlock tables');
                M('Trade')->where(array('id' => $id))->setField('status', 1);
                $this->error('Undo failed!');
            }

            $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $trade['userid']))->setInc($rmb, $save_buy_rmb);
            $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $trade['userid']))->setDec($rmb . 'd', $save_buy_rmb);
            $rs[] = $mo->table('codono_trade')->where(array('id' => $trade['id']))->setField('status', 2);
            $you_buy = $mo->table('codono_trade')->where(array(
                'market' => array('like', '%' . $rmb . '%'),
                'status' => 0,
                'userid' => $trade['userid']
            ))->find();

            if (!$you_buy) {
                $you_user_buy = $mo->table('codono_user_coin')->where(array('userid' => $trade['userid']))->find();

                if (0 < $you_user_buy[$rmb . 'd']) {
                    $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $trade['userid']))->setField($rmb . 'd', 0);
                }
            }
        } else if ($trade['type'] == 2) {
            $mun = round($trade['num'] - $trade['deal'], 8);
            $user_sell = $mo->table('codono_user_coin')->where(array('userid' => $trade['userid']))->find();

            if ($mun <= round($user_sell[$xnb . 'd'], 8)) {
                $save_sell_xnb = $mun;
            } else if ($mun <= round($user_sell[$xnb . 'd'], 8) + 1) {
                $save_sell_xnb = $user_sell[$xnb . 'd'];
            } else {
                $mo->execute('rollback');
                M('Trade')->where(array('id' => $id))->setField('status', 1);
                $this->error('Undo failed!');
            }

            $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $trade['userid']))->setInc($xnb, $save_sell_xnb);
            $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $trade['userid']))->setDec($xnb . 'd', $save_sell_xnb);
            $rs[] = $mo->table('codono_trade')->where(array('id' => $trade['id']))->setField('status', 2);
            $you_sell = $mo->table('codono_trade')->where(array(
                'market' => array('like', '%' . $xnb . '%'),
                'status' => 0,
                'userid' => $trade['userid']
            ))->find();

            if (!$you_sell) {
                $you_user_sell = $mo->table('codono_user_coin')->where(array('userid' => $trade['userid']))->find();

                if (0 < $you_user_sell[$xnb . 'd']) {
                    $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $trade['userid']))->setField($xnb . 'd', 0);
                }
            }
        } else {
            $mo->execute('rollback');
            $this->error('The countermand parameter error!');
        }

        if (check_arr($rs)) {
            $mo->execute('commit');
            $mo->execute('unlock tables');
            $this->success('Order has been canceled!');
        } else {
            $mo->execute('rollback');
            $this->error('Undo failed!');
        }
    }

    public function mycj($market_id)
    {
        $pid = (isset($_GET['pid']) ? abs(intval($_GET['pid'])) : 1);
        $uid = $this->userid();
        $market_id = (int)$market_id;

        if (!$market_id) {
            $this->error('market_id error');
        }

        $marketArr = M('Market')->where(array('id' => $market_id))->find();
        $limit = 5;
        $list = M('TradeLog')->where(array('userid = ' . $uid . ' and market = \'' . $marketArr['name'] . '\''))->order('id desc')->limit(($pid - 1) * $limit, $limit)->select();

        if (!$list) {
            if ($pid == 1) {
                $this->ajaxShow('No record', 0);
            } else {
                $this->ajaxShow('No more a', 0);
            }
        }

        foreach ($list as $key => $val) {
            $list[$key]['num'] = NumToStr($val['num']) * 1;
            $list[$key]['mum'] = NumToStr($val['mum']) * 1;
            $list[$key]['fee_buy'] = NumToStr($val['fee_buy']) * 1;
            $list[$key]['fee_sell'] = NumToStr($val['fee_sell']) * 1;
            $list[$key]['price'] = NumToStr($val['price']) * 1;
            $list[$key]['addtime'] = addtime($val['addtime']);
        }

        $this->ajaxShow($list);
    }

    public function auth_check()
    {
        $uid = $this->userid();
        $res = M('User')->where(array('id' => $uid))->find();
        $res['idcard'] = mb_substr($res['idcard'], 0, 6) . '******' . mb_substr($res['idcard'], -4);
        $res['addtime'] = addtime($res['addtime']);
        $this->ajaxShow($res);
    }

    public function uppassword($oldpassword, $newpassword)
    {
        $uid = $this->userid();

        if (!check($oldpassword, 'password')) {
            $this->error('Old password format error!');
        }

        if (!check($newpassword, 'password')) {
            $this->error('The new password is malformed!');
        }

        $password = M('User')->where(array('id' => $uid))->getField('password');

        if (md5($oldpassword) != $password) {
            $this->error('Old login password is incorrect!');
        }

        $rs = M('User')->where(array('id' => $uid))->save(array('password' => md5($newpassword)));

        if ($rs) {
            $this->success('Successfully modified');
        } else {
            $this->error('No changes were made!');
        }
    }

    public function uppaypassword($oldpaypassword, $newpaypassword)
    {
        $uid = $this->userid();

        if (!check($oldpaypassword, 'password')) {
            $this->error('Old Fund Pwd format error!');
        }

        if (!check($newpaypassword, 'password')) {
            $this->error('The new Fund Pwd format error!');
        }

        $user = M('User')->where(array('id' => $uid))->find();

        if (md5($oldpaypassword) != $user['paypassword']) {
            $this->error('Old Fund Pwd is wrong!');
        }

        $rs = M('User')->where(array('id' => $uid))->save(array('paypassword' => md5($newpaypassword)));

        if ($rs) {
            $this->success('Successfully modified');
        } else {
            $this->error('No changes were made!');
        }
    }

    public function sendnewCellphone($new_cellphone)
    {
        $this->userid();
        $code = rand(1000, 9999);
        S('sendnewMobile_code_' . $new_cellphone, $code);
        S('sendnewMobile_cellphone_' . $new_cellphone, $new_cellphone);
        $this->success('Verification code has been sent to the:' . $new_cellphone . '(' . $code . ')');
    }

    public function altercellphone($new_cellphone, $cellphone_verify)
    {
        $code = S('sendnewMobile_code_' . $new_cellphone);
        $v_cellphone = S('sendnewMobile_cellphone_' . $new_cellphone);

        if (($cellphone_verify != $code) || ($v_cellphone != $new_cellphone)) {
            $this->error(L('INCORRECT_SMS_CODE') . $cellphone_verify . '|' . $code . '#' . $v_cellphone . '|' . $new_cellphone);
        } else {
            S('sendMobile_code_' . $new_cellphone, null);
        }

        if (!check($new_cellphone, 'cellphone')) {
            $this->error('The new phone number format error!');
        }

        $uid = $this->userid();
        $res = M('User')->where(array('id' => $uid))->save(array('cellphone' => $new_cellphone));

        if ($res) {
            $this->ajaxShow('update completed');
        } else {
            $this->ajaxShow('Update failed', -1);
        }
    }
	
	/* Google 2FA Function to set your 2FA code*/
	public function GetSet2FA()
	{
		$uid = $this->userid();
        if (empty($_POST)) {
            if (!$uid) {
                $this->error('Please login first to receive code!');
            }

            
            $user = M('User')->where(array('id' => $uid))->find();
            $is_ga = ($user['ga'] ? 1 : 0);

            if (!$is_ga) {
                $ga = new \Common\Ext\GoogleAuthenticator();
                $secret = $ga->createSecret();
                session('secret', $secret);
               
                $qrCodeUrl = $ga->getQRCodeGoogleUrl($user['username'] . '%20-%20' . $_SERVER['HTTP_HOST'], $secret);
				$ret_array=array('Asecret'=>$secret,'qrCodeUrl'=>$qrCodeUrl);
				$ret_array['status']=1;
				$ret_array['message']="Now use this secret and enter in authy ,save it and post in GetSet2FA";
				echo json_encode($ret_array);exit;
            } else {
                $arr = explode('|', $user['ga']);
				$out['status']=0;
				$out['ga_login']=$arr[1];
                $out['ga_transfer']= $arr[2];
				$out['message']="2FA required for login=$arr[1], 2FA required for withdrawal=$arr[2], Use post function and send your 2FA to add/update/or delete";
				echo json_encode($out);exit;
				
            }
        } else {
            if (!$uid) {
                $this->error('Please login first!');
            }

            $delete = '';
            $gacode = trim(I('ga'));
            $type = trim(I('type'));
            $ga_login = (I('ga_login') == false ? 0 : 1);
            $ga_transfer = (I('ga_transfer') == false ? 0 : 1);

            if (!$gacode) {
                $this->error('Enter 2FA Code!');
            }

            if ($type == 'add') {
                $secret = session('secret');

                if (!$secret) {
                    $this->error('2FA has expired,Please resend!');
                }
            } else if (($type == 'update') || ($type == 'delete')) {
                $user = M('User')->where('id = ' . $uid)->find();

                if (!$user['ga']) {
                    $this->error('2FA setup isnt done yet!');
                }

                $arr = explode('|', $user['ga']);
                $secret = $arr[0];
                $delete = ($type == 'delete' ? 1 : 0);
            } else {
                $this->error(L('Type is undefined'));
            }

            $ga = new \Common\Ext\GoogleAuthenticator();
		   

            if ($ga->verifyCode($secret, $gacode, 1)) {
                $ga_val = ($delete == '' ? $secret . '|' . $ga_login . '|' . $ga_transfer : '');
                M('User')->save(array('id' => $uid, 'ga' => $ga_val));
                $this->success(L('Successful operation'));
            } else {
                $this->error(L('Verification failed'));
            }
        }
    }
	
	public function changeFundPassword($oldpaypassword, $newpaypassword, $repaypassword)
    {
		$uid = $this->userid();
        if (!$uid) {
            $this->error(L('PLEASE_LOGIN'));
        }


        if (!check($oldpaypassword, 'password')) {
            //$this->error(L('Old Fund Pwd format error!'));
			$oldpaypassword='NEW';
			
        }

        if (!check($newpaypassword, 'password')) {
            $this->error(L('The new Fund Pwd format error!'));
        }

        if ($newpaypassword != $repaypassword) {
            $this->error(L('Confirm New Password wrong!'));
        }

        $user = M('User')->where(array('id' => $uid))->find();
		if($user['paypassword']){
			if (md5($oldpaypassword) != $user['paypassword']) {
				$this->error(L('Old Fund Pwd is wrong!'));
			}	
		}

        if (md5($newpaypassword) == $user['password']) {
            $this->error(L('Fund Pwd and login password can not be the same!'));
        }

        $rs = M('User')->where(array('id' => userid()))->save(array('paypassword' => md5($newpaypassword)));

        if (!($rs === false)) {
            $this->success(L('Successfully modified'));
        } else {
            $this->error('No changes were made!');
        }
    }
}

?>