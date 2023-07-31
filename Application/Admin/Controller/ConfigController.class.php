<?php

namespace Admin\Controller;

class ConfigController extends AdminController
{
    public function index()
    {
        //$this->checkUpdata();
        $this->data = M('Config')->where(array('id' => 1))->find();
        $this->display();
    }
	public function Extras()
    {
	$setfile="../extras.config.php"; 
	//$settingstr=file_get_contents($setfile);
	include($setfile);
	$this->assign('DEFAULT_MAILER',DEFAULT_MAILER);
    $this->display();
    }
	public function ExtrasSave(){

$setfile="../extras.config.php"; 

$c=array(
   'DEFAULT_MAILER' => $_POST['DEFAULT_MAILER'],
);
        

$settingstr="<?php \n ";
		foreach($c as $key=>$v){
			
    $settingstr.= "define('".$key."','".$v."');\n";
	}
	$settingstr.="\n?>\n";
	file_put_contents($setfile,$settingstr);
     $this->success('Changes Saved!');
}
    public function edit()
    {
        if (APP_DEMO) {
            $this->error(L('SYSTEM_IN_DEMO_MODE'));
        }


        if (M('Config')->where(array('id' => 1))->save($_POST)) {
            $this->success('Changes Saved!');
        } else {
            $this->error('No changes were made!');
        }
    }

    public function image()
    {
        $upload = new \Think\Upload();
        $upload->maxSize = 3145728;
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');
        $upload->rootPath = './Upload/public/';
        $upload->autoSub = false;
        $info = $upload->upload();

        foreach ($info as $k => $v) {
            $path = $v['savepath'] . $v['savename'];
            echo $path;
            exit();
        }
    }

    public function cellphone()
    {
        $this->data = M('Config')->where(array('id' => 1))->find();
        $this->display();
    }

    public function cellphoneEdit()
    {
        if (APP_DEMO) {
            $this->error(L('SYSTEM_IN_DEMO_MODE'));
        }

        if (M('Config')->where(array('id' => 1))->save($_POST)) {
            $this->success('Changes Saved!');
        } else {
            $this->error('No changes were made!');
        }
    }

    public function contact()
    {
        $this->data = M('Config')->where(array('id' => 1))->find();
        $this->display();
    }

    public function contactEdit()
    {
        if (APP_DEMO) {
            $this->error(L('SYSTEM_IN_DEMO_MODE'));
        }


        if (M('Config')->where(array('id' => 1))->save($_POST)) {
            $this->success('Changes Saved!');
        } else {
            $this->error('No changes were made!');
        }
    }
	public function coinColdTransfer($coin = NULL){
		$coldwallet_info=COLD_WALLET[strtoupper($coin)];
		
		if(!$coldwallet_info || substr_count($coldwallet_info,':')!=2){
			$this->assign('error','NO COLD WALLET DEFINED');
		}else{
			$this->assign('error',0);
		}
		$info=explode(":",$coldwallet_info);
		
		$coldwallet_address=$info[0];
		$maxkeep=$info[1];
		$minsendrequired=$info[2];
		$this->assign('address',$coldwallet_address);
		$this->assign('maxkeep',$maxkeep);
		$this->assign('minsendrequired',$minsendrequired);
		$this->assign('coinname',$coin);
		$this->assign('coldwallet',COLD_WALLET[strtoupper($coin)]);
		$this->display();
	}
	public function upColdTransfer($coinname)
	{
		
		$coldwallet_info=COLD_WALLET[strtoupper($coinname)];
		if(!$coldwallet_info || substr_count($coldwallet_info,':')!=2){
			$this->assign('error','NO COLD WALLET DEFINED');
		}else{
			$this->assign('error',0);
		}
		$info=explode(":",$coldwallet_info);
		
		$coldwallet_address=$info[0];
		$maxkeep=$info[1];
		$minsendrequired=$info[2];
		

        $tobesent=1; //calculate balance-maxkeep 
		//check if  tobesent> minsendrequired then only proceed
		
        $dj_username = C('coin')[$coinname]['dj_yh'];
        $dj_password = C('coin')[$coinname]['dj_mm'];
        $dj_address = C('coin')[$coinname]['dj_zj'];
        $dj_port = C('coin')[$coinname]['dj_dk'];
		$dj_decimal = C('coin')[$coinname]['cs_qk'];
		$main_address = C('coin')[$coinname]['codono_coinaddress'];
        
		//Ethereum and ERC20
		if (C('coin')[$coinname]['type'] == 'eth') {
		$erc20_contract = $dj_username; //Contract Address
		$dj_password = cryptString(C('coin')[$coinname]['dj_mm'],'d');
		
		$EthCommon = new \Org\Util\EthCommon($dj_address, $dj_port, "2.0");
        $EthPayLocal = new \Org\Util\EthPayLocal($dj_address, $dj_port, "2.0", $erc20_contract,$dj_password);
        
		$master=EthMaster($dj_address, $dj_port,$main_address,$dj_password, $erc20_contract);
		
        $Coin = M('Coin')->where(array('name' => $coinname))->find();

  		//Check if coin is ERC20	
			if ($erc20_contract) {
						$bal_token=$master->balanceOfToken($main_address,$erc20_contract,$dj_decimal);
						$token_balance=format_num($bal_token,8);
						
						$tobesent=$token_balance-$maxkeep;
						if((int)$token_balance<0.000000001){
						$this->error($coinname."Balance is low, cant transfer:".$token_balance);
						}
						if($tobesent<$minsendrequired){
							$this->error('You have '.$token_balance.$coinname.' but you want to send '.$tobesent.' minimum tx should be '.$minsendrequired);
						}
						
                        //Contract Address transfer out
						$zhuan['fromaddress'] = $main_address;
                        $zhuan['toaddress'] = $coldwallet_address;
                        $zhuan['token'] = $erc20_contract;
                        $zhuan['type'] = $coinname;
                        $zhuan['amount'] = (double)$tobesent;
						$zhuan['password'] =$dj_password;
						$sendrs=$master->transferToken($zhuan['toaddress'], $zhuan['amount'], $zhuan['token'],$dj_decimal);
              } else {

                        //eth
						$ubal=$this->floordec($master->balanceOf($main_address,$ContractAddress),3);
						$tobesent=$ubal-$maxkeep;
						
						if($tobesent<$minsendrequired){
							$this->error('You have '.$ubal.$coinname.' but you want to send '.$tobesent.' minimum tx should be '.$minsendrequired);
						}
						$zhuan['fromaddress'] = $main_address;
                        $zhuan['toaddress'] = $coldwallet_address;
                        $zhuan['amount'] = (double)$tobesent;
						$zhuan['password'] =$dj_password;
                        $sendrs=$master->transferFromCoinbase($zhuan['toaddress'],(double)$tobesent);
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
                $this->error('wallet server Withdraw currency failure!');
            } else {
                $this->success('Transfer success!'.$hash);
            }
		}
	    //Bitcoin type starts qbb
		
		if (C('coin')[$coinname]['type'] == 'qbb') {
		
		$CoinClient = CoinClient($dj_username, $dj_password, $dj_address, $dj_port, 5, array(), 1);
        $json = $CoinClient->getinfo();
        
        if (!isset($json['version']) || !$json['version']) {
            $this->error('System can not connect with '.$coinname .' node');
        }

        $Coin = M('Coin')->where(array('name' => $coinname))->find();
                        $daemon_balance=$CoinClient->getbalance();
                $tobesent=$daemon_balance-$maxkeep;
						if($tobesent<$minsendrequired){
							$this->error('You have '.$daemon_balance.$coinname.' but you want to send '.$daemon_balance.' minimum hotwallet balance should be '.$minsendrequired);
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
                $this->error('wallet server Withdraw currency failure!');
            } else {
                $this->success('Transfer success!'.$sendrs);
            }
        } else {
            $this->error('Coin Type is not compatible for Cold wallet transfer!');
        }
	
 }
    public function coin($name = NULL, $field = NULL, $status = NULL)
    {
        $where = array();

        if ($field && $name) {
            if ($field == 'username') {
                $where['userid'] = M('User')->where(array('username' => $name))->getField('id');
            } else {
                $where[$field] = $name;
            }
        }

        if ($status) {
            $where['status'] = $status - 1;
        }

        $count = M('Coin')->where($where)->count();
        $Page = new \Think\Page($count, 15);
        $show = $Page->show();
        $list = M('Coin')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }

    public function coinEdit($id = NULL)
    {
        if (empty($_POST)) {
            if (empty($id)) {
                $this->data = array();
            } else {
				$the_data=M('Coin')->where(array('id' => trim($_GET['id'])))->find();
				if($the_data['type']=='eth'){
					$the_data['dj_mm']=cryptString($the_data['dj_mm'],'d');
				}
                $this->data = $the_data;
            }

            $codono_getCoreConfig = codono_getCoreConfig();
            if (!$codono_getCoreConfig) {
                $this->error('Incorrect Core Config');
            }

            $this->assign("codono_opencoin", $codono_getCoreConfig['codono_opencoin']);


            $this->display();
        } else {
            if (APP_DEMO) {
                $this->error(L('SYSTEM_IN_DEMO_MODE'));
            }

            $_POST['fee_bili'] = floatval($_POST['fee_bili']);

            if ($_POST['fee_bili'] && (($_POST['fee_bili'] < 0.01) || (100 < $_POST['fee_bili']))) {
                $this->error('The proportion is only pending0.01--100between(Do not fill%)!');
            }

            $_POST['zr_zs'] = floatval($_POST['zr_zs']);

            if ($_POST['zr_zs'] && (($_POST['zr_zs'] < 0.01) || (100 < $_POST['zr_zs']))) {
                $this->error('Gift can only be transferred0.01--100between(Do not fill%)!');
            }

            $_POST['zr_dz'] = intval($_POST['zr_dz']);
            $_POST['zc_fee'] = floatval($_POST['zc_fee']);

            if ($_POST['zc_fee'] && (($_POST['zc_fee'] < 0.01) || (100 < $_POST['zc_fee']))) {
                $this->error('Withdrawal fee :Between 0.01-100 (Dont add % symbol)!');
            }

            if ($_POST['zc_user']) {
                if (!check($_POST['zc_user'], 'dw')) {
                    $this->error('Official fee address format is not correct!');
                }

                $ZcUser = M('UserCoin')->where(array($_POST['name'] . 'b' => $_POST['zc_user']))->find();
				
                if (!$ZcUser) {
                    $this->error('There is no such user account generated using this system');
                }
            }


			 $_POST['zc_min'] = (double)($_POST['zc_min']);
            $_POST['zc_max'] = (double)($_POST['zc_max']);
			if(strlen($_POST['dj_mm'])<1){
				unset($_POST['dj_mm']);
			}else{
			if($_POST['type']=='eth'){
			$_POST['dj_mm']=cryptString($_POST['dj_mm']);
			}
			}
			
            if ($_POST['id']) {
                $rs = M('Coin')->save($_POST);
            } else {
                if (!check($_POST['name'], 'n')) {
                    $this->error('Lowercase Letters!');
                }

                $_POST['name'] = strtolower($_POST['name']);

                if (check($_POST['name'], 'general')) {
                    $this->error($_POST['name'].' Currency name format is not correct!');
                }

                if (M('Coin')->where(array('name' => $_POST['name']))->find()) {
                    $this->error('Currency exist!');
                }

                $rea = M()->execute('ALTER TABLE  `codono_user_coin` ADD  `' . $_POST['name'] . '` DECIMAL(20,8) UNSIGNED  NULL DEFAULT "0.00000000"');
                $reb = M()->execute('ALTER TABLE  `codono_user_coin` ADD  `' . $_POST['name'] . 'd` DECIMAL(20,8) UNSIGNED NULL DEFAULT "0.00000000" ');
                $rec = M()->execute('ALTER TABLE  `codono_user_coin` ADD  `' . $_POST['name'] . 'b` VARCHAR(200) DEFAULT NULL ');
				/*Adding user Wallet Unique passwords in table
				 For previous eth based coins run this query 
				ALTER TABLE  `codono_user_coin` ADD  `eth_pass` VARCHAR(255) DEFAULT NULL;
				and replace eth with coin name*/
				
				if($_POST['type']=='eth'){
					
					$rec = M()->execute('ALTER TABLE  `codono_user_coin` ADD  `' . $_POST['name'] . '_pass` VARCHAR(200) DEFAULT NULL ');
				}
                //corresponding Product payment Types of increase Currencies
                $rea = M()->execute('ALTER TABLE  `codono_shop_coin` ADD  `' . $_POST['name'] . '` VARCHAR(200) DEFAULT NULL');


                $rs = M('Coin')->add($_POST);
            }

            if ($rs) {
                $this->success(L('SUCCESSFULLY_DONE'));
            } else {

                $this->error(L('No Changes'));
            }
        }
    }

    public function coinStatus()
    {
        if (APP_DEMO) {
            $this->error(L('SYSTEM_IN_DEMO_MODE'));
        }

        if (IS_POST) {
            $id = array();
            $id = implode(',', $_POST['id']);
        } else {
            $id = $_GET['id'];
        }

        if (empty($id)) {
            $this->error('please choose coin to be operated!');
        }

        $where['id'] = array('in', $id);
        $method = $_GET['type'];

        switch (strtolower($method)) {
            case 'forbid':
                $data = array('status' => 0);
                break;

            case 'resume':
                $data = array('status' => 1);
                break;

            case 'delete':
                $rs = M('Coin')->where($where)->select();

                foreach ($rs as $k => $v) {
                    $rs[] = M()->execute('ALTER TABLE  `codono_user_coin` DROP COLUMN ' . $v['name']);
                    $rs[] = M()->execute('ALTER TABLE  `codono_user_coin` DROP COLUMN ' . $v['name'] . 'd');
                    $rs[] = M()->execute('ALTER TABLE  `codono_user_coin` DROP COLUMN ' . $v['name'] . 'b');

                    $rs[] = M()->execute('ALTER TABLE  `codono_shop_coin` DROP COLUMN ' . $v['name']);
                }

                if (M('Coin')->where($where)->delete()) {
                    $this->success(L('SUCCESSFULLY_DONE'));
                } else {
                    $this->error(L('OPERATION_FAILED'));
                }

                break;

            default:
                $this->error('Illegal parameters');
        }

        if (M('Coin')->where($where)->save($data)) {
            $this->success(L('SUCCESSFULLY_DONE'));
        } else {
            $this->error(L('OPERATION_FAILED'));
        }
    }

    public function coinInfo($coin)
    {
        $dj_username = C('coin')[$coin]['dj_yh'];
        $dj_password = C('coin')[$coin]['dj_mm'];
        $dj_address = C('coin')[$coin]['dj_zj'];
        $dj_port = C('coin')[$coin]['dj_dk'];
		$dj_decimal = C('coin')[$coin]['cs_qk'];
		$main_address = C('coin')[$coin]['codono_coinaddress'];
		if (C('coin')[$coin]['type'] == 'waves') {
					$waves = WavesClient($dj_username, $dj_password, $dj_address, $dj_port,$dj_decimal, 5, array(), 1);
					 $addresses= $waves->GetAddresses();
					 $addr_array=json_decode($addresses);
					 $waves_coin=strtoupper($coin);

					$info['b']['balance'] ='Main Balance:'.$waves->Balance($main_address,$dj_username);
					$info['b']['paytxfee'] ='Waves takes 0.001/tx It would be double in your case since you move from customer to main account.';
					$info['b']['connection']=$addr_array['result'][$waves_coin]['coin_status'];
					
		}
			if (C('coin')[$coin]['type'] == 'coinpay') {
					$cps_api = CoinPay($dj_username, $dj_password, $dj_address, $dj_port, 5, array(), 1);
					 $json = ($cps_api->GetAllCoinBalances());
					 $coinpay_coin=strtoupper($coin);

					$info['b']['balance'] =$json['result'][$coinpay_coin]['balancef'];
					$info['b']['paytxfee'] ='Please check coinpayments.net for fees';
					$info['b']['connection']=$json['result'][$coinpay_coin]['coin_status'];
					
		}
		if (C('coin')[$coin]['type'] == 'blockio') {
					$block_io = BlockIO($dj_username, $dj_password, $dj_address, $dj_port, 5, array(), 1);
					$json = $block_io->get_balance();
                    if (!isset($json->status) || $json->status!='success') {
                        $this->error('Wallet Docking failed!'.$coin);
                    }
					$all_info = $block_io->get_my_addresses();
					
					$info['b']['balance'] ='Please use Block.io for more information';
					$info['b']['paytxfee'] ='Please use Block.io for more information';
					$info['b']['connection']=$json->status;
		}
		if (C('coin')[$coin]['type'] == 'cryptonote') {
					$cryptonote = CryptoNote($dj_address, $dj_port);
					
					$open_wallet = $cryptonote->open_wallet($dj_username,$dj_password);
					
					$json=json_decode($cryptonote->get_height());
					
                    if (!isset($json->height) || $json->error!=0) {
						$status=1;
                        $this->error('Wallet Docking failed!'.$coin);
                    }
					$all_info = $cryptonote->getAddress();
					$bal_info = json_decode($cryptonote->getBalance(0));
					$cryptonote_balance=$cryptonote->deAmount($bal_info->balance);
					$info['b']['balance'] =$cryptonote_balance;
					$info['b']['paytxfee'] ='Please read documentation';
					$info['b']['connection']='Block Height at '.$json->height;
		}
		if (C('coin')[$coin]['type'] == 'qbb') {
			$CoinClient = CoinClient($dj_username, $dj_password, $dj_address, $dj_port);
			if (!$CoinClient) {
            $this->error('Wallet Docking failed!'.$coin);
			}
			$info['b'] = $CoinClient->getinfo();
			$info['b']['connection']=$json->status;
		}
		
		if (C('coin')[$coin]['type'] == 'eth') {
			$EthCommon = new \Org\Util\EthCommon($dj_address, $dj_port, "2.0");
			$EthPayLocal = new \Org\Util\EthPayLocal($dj_address, $dj_port, "2.0", $main_address);
			$info['b'] = $EthPayLocal->eth_accounts();
			if (!$EthPayLocal) {
            $this->error('Wallet Docking failed!'.$coin);
			}
		}
		
        $info['num'] = M('UserCoin')->sum($coin) + M('UserCoin')->sum($coin . 'd');
        $info['coin'] = $coin;
        $this->assign('data', $info);
        $this->display();
    }

    public function coinUser($coin)
    {
		$dj_username = C('coin')[$coin]['dj_yh'];
        $dj_password = C('coin')[$coin]['dj_mm'];
        $dj_address = C('coin')[$coin]['dj_zj'];
        $dj_port = C('coin')[$coin]['dj_dk'];
		$dj_decimal = C('coin')[$coin]['cs_qk'];
			if (C('coin')[$coin]['type'] == 'waves') {
					$waves = WavesClient($dj_username, $dj_password, $dj_address, $dj_port,$dj_decimal, 5, array(), 1);
					 $addresses= $waves->GetAddresses();
$addr_array=json_decode($addresses);
echo "<br/>Validating Addreses<br/>";
foreach ($addr_array as $addr){
echo $waves->ValidateAddress($addr)."<br/>";
}
echo "<br/>Waves Balances<br/>";
foreach ($addr_array as $addr){
echo $waves->Balance($addr,$coin)."<br/>";
}


		die("Raw balance map");
		}

		if (C('coin')[$coin]['type'] == 'coinpay') {
					die("This feature is not required in case of coinpayments");
        }
		if (C('coin')[$coin]['type'] == 'cryptonote') {
											$cryptonote = CryptoNote($dj_address, $dj_port);
						$open_wallet = $cryptonote->open_wallet($dj_username,$dj_password);
						$json=json_decode($cryptonote->get_height());
						if (!isset($json->height) || $json->error!=0) {
						$this->error('Wallet System is currently offline 2!');
						}
						$address = $cryptonote->getAddress();
echo "<pre>";
print_r(json_decode($address,true));
echo "</pre>";
exit;

        }
		if (C('coin')[$coin]['type'] == 'blockio') {
					$block_io = BlockIO($dj_username, $dj_password, $dj_address, $dj_port, 5, array(), 1);
					$json = $block_io->get_balance();
                    if (!isset($json->status) || $json->status!='success') {
                        $this->error('Wallet Docking failed!'.$coin);
                    }
					$addrlist = $block_io->get_my_addresses();
					$arr=$addrlist->data->addresses;
				foreach ($arr as $k) {
                $str = '';
                $addr = $k->address;//$CoinClient->getaddressesbyaccount($k);

                
                $str .= $addr . '<br>';
                $userid = M('User')->where(array('username' => $k->label))->getField('id');
				
				if($userid>0){
                $list[$userid]['num'] = $k->available_balance;
				$list[$userid]['addr'] = $str;
                $user_coin = M('UserCoin')->where(array('userid' => $userid))->find();
                $list[$userid]['xnb'] = $user_coin[$coin];
                $list[$userid]['xnbd'] = $user_coin[$coin . 'd'];
                $list[$userid]['zj'] = $list[$k]['xnb'] + $list[$k]['xnbd'];
                $list[$userid]['xnbb'] = $user_coin[$coin . 'b'];
				}
                unset($str);
            }
        }
		if (C('coin')[$coin]['type'] == 'eth') {
		die("Please use ETH Node to list your accounts");
		}
		if (C('coin')[$coin]['type'] == 'qbb') {
        $CoinClient = CoinClient($dj_username, $dj_password, $dj_address, $dj_port);

        if (!$CoinClient) {
            $this->error('Wallet Docking failed!');
        }

        $arr = $CoinClient->listaccounts();
		foreach ($arr as $k => $v) {
            if ($k) {
                if ($v < 1.0000000000000001E-5) {
                    $v = 0;
                }

                $list[$k]['num'] = $v;
				
                $str = '';exit;
                $addr = $CoinClient->getaddressesbyaccount($k);

                foreach ($addr as $kk => $vv) {
                    $str .= $vv . '<br>';
                }

                $list[$k]['addr'] = $str;
                $userid = M('User')->where(array('username' => $k))->getField('id');
                $user_coin = M('UserCoin')->where(array('userid' => $userid))->find();
                $list[$k]['xnb'] = $user_coin[$coin];
                $list[$k]['xnbd'] = $user_coin[$coin . 'd'];
                $list[$k]['zj'] = $list[$k]['xnb'] + $list[$k]['xnbd'];
                $list[$k]['xnbb'] = $user_coin[$coin . 'b'];
                unset($str);
            }
        }
		}

        

        $this->assign('list', $list);
        $this->display();
    }

    public function coinEmpty($coin)
    {
        if (!C('coin')[$coin]) {
            $this->error(L('INCORRECT_REQ'));
        }

        $info = M()->execute('UPDATE `codono_user_coin` SET `' . trim($coin) . 'b`=\'\' ;');

        if ($info) {
            $this->success(L('SUCCESSFULLY_DONE'));
        } else {
            $this->error(L('OPERATION_FAILED'));
        }
    }

    public function coinImage()
    {
        $upload = new \Think\Upload();
        $upload->maxSize = 3145728;
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');
        $upload->rootPath = './Upload/coin/';
        $upload->autoSub = false;
        $info = $upload->upload();

        foreach ($info as $k => $v) {
            $path = $v['savepath'] . $v['savename'];
            echo $path;
            exit();
        }
    }

    public function text($name = NULL, $field = NULL, $status = NULL)
    {
        $where = array();

        if ($field && $name) {
            if ($field == 'username') {
                $where['userid'] = M('User')->where(array('username' => $name))->getField('id');
            } else {
                $where[$field] = $name;
            }
        }

        if ($status) {
            $where['status'] = $status - 1;
        }

        $count = M('Text')->where($where)->count();
        $Page = new \Think\Page($count, 15);
        $show = $Page->show();
        $list = M('Text')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

        foreach ($list as $k => $v) {

        }

        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }

    public function textEdit($id = NULL)
    {
        if (empty($_POST)) {
            if ($id) {
                $this->data = M('Text')->where(array('id' => trim($id)))->find();
            } else {
                $this->data = null;
            }

            $this->display();
        } else {
            if (APP_DEMO) {
                $this->error(L('SYSTEM_IN_DEMO_MODE'));
            }

            if ($_POST['id']) {
                $rs = M('Text')->save($_POST);
            } else {
                $_POST['adminid'] = session('admin_id');
                $rs = M('Text')->add($_POST);
            }

            if ($rs) {
                $this->success(L('SAVED_SUCCESSFULLY'));
            } else {
                $this->error(L('COULD_NOT_SAVE'));
            }
        }
    }

    public function misc()
    {
        $this->data = M('Config')->where(array('id' => 1))->find();
		
        $this->display();
    }

    public function miscEdit()
    {
        if (APP_DEMO) {
            $this->error(L('SYSTEM_IN_DEMO_MODE'));
        }

        if (M('Config')->where(array('id' => 1))->save($_POST)) {
            $this->success('Changes Saved!');
        } else {
            $this->error('No changes were made!');
        }
    }

    public function navigation($name = NULL, $field = NULL, $status = NULL)
    {
        $where = array();

        if ($field && $name) {
            if ($field == 'username') {
                $where['userid'] = M('User')->where(array('username' => $name))->getField('id');
            } else if ($field == 'title') {
                $where['title'] = array('like', '%' . $name . '%');
            } else {
                $where[$field] = $name;
            }
        }

        if ($status) {
            $where['status'] = $status - 1;
        } else {
            $where['status'] = array('neq', -1);
        }


        $count = M('Navigation')->where($where)->count();
        $Page = new \Think\Page($count, 15);
        $show = $Page->show();
        $list = M('Navigation')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }

    public function navigationEdit($id = NULL)
    {
		
        if (empty($_POST)) {
            if ($id) {
                $this->data = M('Navigation')->where(array('id' => trim($id)))->find();
            } else {
                $this->data = null;
            }

            $this->display();
        } else {
            if (APP_DEMO) {
                $this->error(L('SYSTEM_IN_DEMO_MODE'));
            }

            if ($_POST['id']) {
                $rs = M('Navigation')->save($_POST);
            } else {
                $_POST['addtime'] = time();
                $rs = M('Navigation')->add($_POST);
            }
			
            if ($rs) {
                $this->success(L('SAVED_SUCCESSFULLY'));
            } else {
                $this->error(L('COULD_NOT_SAVE'));
            }
        }
    }

    public function navigationStatus($id = NULL, $type = NULL, $model = 'Navigation')
    {
        if (APP_DEMO) {
            $this->error(L('SYSTEM_IN_DEMO_MODE'));
        }

        if (empty($id)) {
            $this->error(L('INCORRECT_REQ'));
        }

        if (empty($type)) {
            $this->error(L('INCORRECT_TYPE'));
        }

        if (strpos(',', $id)) {
            $id = implode(',', $id);
        }

        $where['id'] = array('in', $id);

        switch (strtolower($type)) {
            case 'forbid':
                $data = array('status' => 0);
                break;

            case 'resume':
                $data = array('status' => 1);
                break;

            case 'repeal':
                $data = array('status' => 2, 'endtime' => time());
                break;

            case 'delete':
                $data = array('status' => -1);
                break;

            case 'del':
                if (M($model)->where($where)->delete()) {
                    S('navigation', NULL);
                    $this->success(L('SUCCESSFULLY_DONE'));
                } else {
                    $this->error(L('OPERATION_FAILED'));
                }

                break;

            default:
                $this->error(L('OPERATION_FAILED'));
        }

        if (M($model)->where($where)->save($data)) {
            S('navigation', NULL);
            $this->success(L('SUCCESSFULLY_DONE'));
        } else {
            $this->error(L('OPERATION_FAILED'));
        }
    }

    public function checkUpdata()
    {

        if (!S(MODULE_NAME . CONTROLLER_NAME . 'checkUpdata')) {
            $DbFields = M('Config')->getDbFields();

            if (!in_array('footer_logo', $DbFields)) {
                M()->execute('ALTER TABLE `codono_config` ADD COLUMN `footer_logo` VARCHAR(200)  NOT NULL   COMMENT \' \' AFTER `id`;');
            }

            if (in_array('mycz_invit_3', $DbFields)) {
                M()->execute('ALTER TABLE `codono_config` DROP COLUMN `mycz_invit_3`;');
            }

            if (in_array('mycz_invit_2', $DbFields)) {
                M()->execute('ALTER TABLE `codono_config` DROP COLUMN `mycz_invit_2`;');
            }

            if (in_array('mycz_invit_1', $DbFields)) {
                M()->execute('ALTER TABLE `codono_config` DROP COLUMN `mycz_invit_1`;');
            }

            if (in_array('mycz_invit_coin', $DbFields)) {
                M()->execute('ALTER TABLE `codono_config` DROP COLUMN `mycz_invit_coin`;');
            }

            if (in_array('mycz_fee', $DbFields)) {
                M()->execute('ALTER TABLE `codono_config` DROP COLUMN `mycz_fee`;');
            }

            if (in_array('mycz_min', $DbFields)) {
                M()->execute('ALTER TABLE `codono_config` DROP COLUMN `mycz_min`;');
            }

            if (in_array('mycz_max', $DbFields)) {
                M()->execute('ALTER TABLE `codono_config` DROP COLUMN `mycz_max`;');
            }

            if (in_array('mycz_text_index', $DbFields)) {
                M()->execute('ALTER TABLE `codono_config` DROP COLUMN `mycz_text_index`;');
            }

            if (in_array('mycz_text_log', $DbFields)) {
                M()->execute('ALTER TABLE `codono_config` DROP COLUMN `mycz_text_log`;');
            }

            if (in_array('mytx_text_index', $DbFields)) {
                M()->execute('ALTER TABLE `codono_config` DROP COLUMN `mytx_text_index`;');
            }

            if (in_array('mytx_text_log', $DbFields)) {
                M()->execute('ALTER TABLE `codono_config` DROP COLUMN `mytx_text_log`;');
            }

            if (in_array('trade_text_index', $DbFields)) {
                M()->execute('ALTER TABLE `codono_config` DROP COLUMN `trade_text_index`;');
            }

            if (in_array('trade_text_entrust', $DbFields)) {
                M()->execute('ALTER TABLE `codono_config` DROP COLUMN `trade_text_entrust`;');
            }

            if (in_array('issue_text_index', $DbFields)) {
                M()->execute('ALTER TABLE `codono_config` DROP COLUMN `issue_text_index`;');
            }

            if (in_array('issue_text_log', $DbFields)) {
                M()->execute('ALTER TABLE `codono_config` DROP COLUMN `issue_text_log`;');
            }

            if (in_array('issue_text_plan', $DbFields)) {
                M()->execute('ALTER TABLE `codono_config` DROP COLUMN `issue_text_plan`;');
            }

            if (in_array('invit_text_index', $DbFields)) {
                M()->execute('ALTER TABLE `codono_config` DROP COLUMN `invit_text_index`;');
            }

            if (in_array('invit_text_award', $DbFields)) {
                M()->execute('ALTER TABLE `codono_config` DROP COLUMN `invit_text_award`;');
            }

            $tables = M()->query('show tables');
            $tableMap = array();

            foreach ($tables as $table) {
                $tableMap[reset($table)] = 1;
            }

            if (!isset($tableMap['codono_navigation'])) {
                M()->execute("\r\n" . '                    CREATE TABLE `codono_navigation` (' . "\r\n" . '                        `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT \'Incrementid\',' . "\r\n" . '                        `name` VARCHAR(255) NOT NULL COMMENT \'name\',' . "\r\n" . '                          `title` VARCHAR(255) NOT NULL COMMENT \'name\',' . "\r\n" . '                        `url` VARCHAR(255) NOT NULL COMMENT \'url\',' . "\r\n" . '                        `sort` INT(11) UNSIGNED NOT NULL COMMENT \'Sequence\',' . "\r\n" . '                        `addtime` INT(11) UNSIGNED NOT NULL COMMENT \'add time\',' . "\r\n" . '                        `endtime` INT(11) UNSIGNED NOT NULL COMMENT \'Edit time\',' . "\r\n" . '                        `status` TINYINT(4)  NOT NULL COMMENT \'status\',' . "\r\n" . '                        PRIMARY KEY (`id`)' . "\r\n\r\n" . '                  )' . "\r\n" . 'COLLATE=\'gbk_chinese_ci\'' . "\r\n" . 'ENGINE=MyISAM' . "\r\n" . 'AUTO_INCREMENT=1' . "\r\n" . ';' . "\r\n\r\n\r\n" . 'INSERT INTO `codono_navigation` (`name`,`title`, `url`, `sort`, `status`) VALUES (\'finance\',\'Financial Center\', \'Finance/index\', 1, 1);' . "\r\n" . 'INSERT INTO `codono_navigation` (`name`,`title`, `url`, `sort`, `status`) VALUES (\'user\',\'Security center\', \'User/index\', 2, 1);' . "\r\n" . 'INSERT INTO `codono_navigation` (`name`, `title`,`url`, `sort`, `status`) VALUES (\'game\',\'application Center\', \'Game/index\', 3, 1);' . "\r\n" . 'INSERT INTO `codono_navigation` (`name`, `title`,`url`, `sort`, `status`) VALUES (\'article\',\'Help\', \'Article/index\', 4, 1);' . "\r\n\r\n\r\n" . '                ');
            }

            $list = M('Menu')->where(array(
                'url' => 'Config/index',
                'pid' => array('neq', 0)
            ))->select();

            if ($list[1]) {
                M('Menu')->where(array('id' => $list[1]['id']))->delete();
            } else if (!$list) {
                M('Menu')->add(array('url' => 'Config/index', 'title' => 'basic configuration', 'pid' => 7, 'sort' => 1, 'hide' => 0, 'group' => 'Set up', 'ico_name' => 'cog'));
            } else {
                M('Menu')->where(array(
                    'url' => 'Config/index',
                    'pid' => array('neq', 0)
                ))->save(array('title' => 'basic configuration', 'pid' => 7, 'sort' => 1, 'hide' => 0, 'group' => 'Set up', 'ico_name' => 'cog'));
            }

            $list = M('Menu')->where(array(
                'url' => 'Config/cellphone',
                'pid' => array('neq', 0)
            ))->select();

            if ($list[1]) {
                M('Menu')->where(array('id' => $list[1]['id']))->delete();
            } else if (!$list) {
                M('Menu')->add(array('url' => 'Config/cellphone', 'title' => 'SMS Configuration', 'pid' => 7, 'sort' => 2, 'hide' => 0, 'group' => 'Set up', 'ico_name' => 'cog'));
            } else {
                M('Menu')->where(array(
                    'url' => 'Config/cellphone',
                    'pid' => array('neq', 0)
                ))->save(array('title' => 'SMS Configuration', 'pid' => 7, 'sort' => 2, 'hide' => 0, 'group' => 'Set up', 'ico_name' => 'cog'));
            }

            $list = M('Menu')->where(array(
                'url' => 'Config/contact',
                'pid' => array('neq', 0)
            ))->select();

            if ($list[1]) {
                M('Menu')->where(array('id' => $list[1]['id']))->delete();
            } else if (!$list) {
                M('Menu')->add(array('url' => 'Config/contact', 'title' => 'Customer Service Configuration', 'pid' => 7, 'sort' => 3, 'hide' => 0, 'group' => 'Set up', 'ico_name' => 'cog'));
            } else {
                M('Menu')->where(array(
                    'url' => 'Config/contact',
                    'pid' => array('neq', 0)
                ))->save(array('title' => 'Customer Service Configuration', 'pid' => 7, 'sort' => 3, 'hide' => 0, 'group' => 'Set up', 'ico_name' => 'cog'));
            }

            $list = M('Menu')->where(array(
                'url' => 'Config/coin',
                'pid' => array('neq', 0)
            ))->select();

            if ($list[1]) {
                M('Menu')->where(array('id' => $list[1]['id']))->delete();
            } else if (!$list) {
                M('Menu')->add(array('url' => 'Config/coin', 'title' => 'The currency allocation', 'pid' => 7, 'sort' => 4, 'hide' => 0, 'group' => 'Set up', 'ico_name' => 'cog'));
            } else {
                M('Menu')->where(array(
                    'url' => 'Config/coin',
                    'pid' => array('neq', 0)
                ))->save(array('title' => 'The currency allocation', 'pid' => 7, 'sort' => 4, 'hide' => 0, 'group' => 'Set up', 'ico_name' => 'cog'));
            }

            $list = M('Menu')->where(array(
                'url' => 'Config/text',
                'pid' => array('neq', 0)
            ))->select();

            if ($list[1]) {
                M('Menu')->where(array('id' => $list[1]['id']))->delete();
            } else if (!$list) {
                M('Menu')->add(array('url' => 'Config/text', 'title' => 'Text Tips', 'pid' => 7, 'sort' => 5, 'hide' => 0, 'group' => 'Set up', 'ico_name' => 'cog'));
            } else {
                M('Menu')->where(array(
                    'url' => 'Config/text',
                    'pid' => array('neq', 0)
                ))->save(array('title' => 'Text Tips', 'pid' => 7, 'sort' => 5, 'hide' => 0, 'group' => 'Set up', 'ico_name' => 'cog'));
            }

            $list = M('Menu')->where(array(
                'url' => 'Config/misc',
                'pid' => array('neq', 0)
            ))->select();

            if ($list[1]) {
                M('Menu')->where(array('id' => $list[1]['id']))->delete();
            } else if (!$list) {
                M('Menu')->add(array('url' => 'Config/misc', 'title' => 'Other configurations', 'pid' => 7, 'sort' => 6, 'hide' => 0, 'group' => 'Set up', 'ico_name' => 'cog'));
            } else {
                M('Menu')->where(array(
                    'url' => 'Config/misc',
                    'pid' => array('neq', 0)
                ))->save(array('title' => 'Other configurations', 'pid' => 7, 'sort' => 6, 'hide' => 0, 'group' => 'Set up', 'ico_name' => 'cog'));
            }

            $list = M('Menu')->where(array(
                'url' => 'Config/navigation',
                'pid' => array('neq', 0)
            ))->select();

            if ($list[1]) {
                M('Menu')->where(array('id' => $list[1]['id']))->delete();
            } else if (!$list) {
                M('Menu')->add(array('url' => 'Config/navigation', 'title' => 'Navigation configuration', 'pid' => 7, 'sort' => 7, 'hide' => 0, 'group' => 'Set up', 'ico_name' => 'cog'));
            } else {
                M('Menu')->where(array(
                    'url' => 'Config/navigation',
                    'pid' => array('neq', 0)
                ))->save(array('title' => 'Navigation configuration', 'pid' => 7, 'sort' => 7, 'hide' => 0, 'group' => 'Set up', 'ico_name' => 'cog'));
            }

            if (M('Menu')->where(array('url' => 'Market/index'))->delete()) {
                M('AuthRule')->where(array('status' => 1))->delete();
            }

            if (M('Menu')->where(array('url' => 'Coin/index'))->delete()) {
                M('AuthRule')->where(array('status' => 1))->delete();
            }

            S(MODULE_NAME . CONTROLLER_NAME . 'checkUpdata', 1);
        }
    }
}

?>