<?php

namespace Tapi\Controller;

class CryptoController extends CommonController
{
    public function index()
    {
        $array = array('status' => 1, 'message' => 'Connected to Crypto API');
        echo json_encode($array);
    }


    public function MyWithdrawals($coin = NULL)
    {

        $uid = $this->userid();

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

        $where['userid'] = $uid;
        $where['coinname'] = $coin;
        $Model = M('Myzc');
        $count = $Model->where($where)->count();
        $Page = new \Think\Page($count, 10);
        $show = $Page->show();
        $list = $Model->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

        $this->assign('explorer',$explorer);
        $this->assign('list', $list);
        $this->assign('page', $show);
        if($list){
            $send_data['status']=1;
            $send_data['data']=$list;
        }else{
            $send_data['status']=0;
            $send_data['data']=null;
        }

        header('Content-type: application/json');
        echo(json_encode($send_data));exit;
    }


    public function Mydeposits($coin = NULL)
    {

        $uid = $this->userid();
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

        $where['userid'] = $uid;
        $where['coinname'] = $coin;
        $Model = M('Myzr');
        $count = $Model->where($where)->count();
        $Page = new \Think\Page($count, 10);
        $show = $Page->show();
        $list = $Model->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

        $this->assign('list', $list);
        $this->assign('page', $show);
        if($list){
            $send_data['status']=1;
            $send_data['data']=$list;
        }else{
            $send_data['status']=0;
            $send_data['data']=null;
        }

        header('Content-type: application/json');
        echo(json_encode($send_data));exit;
    }

    public function maincoin()
    {
        $coin=MAINCOIN;
        $uid=$this->userid();

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
        $user_coin = M('UserCoin')->where(array('userid' => $uid))->find();
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
                        $rs = M('UserCoin')->where(array('userid' => $uid))->save(array($qbdz => $wallet));
                        $user_exists = M('User')->where(array('id' => $uid))->getField('id');

                        if (!$rs && !$user_exists) {
                            $this->error(L('Generate wallet address wrong!'));
                        }
                        if (!$rs && $user_exists) {

                            $ucoin[$qbdz] = $wallet;
                            $ucoin['userid'] = $user_exists;
                            $new_rs = M('UserCoin')->add($ucoin);
                            exit;
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
                                $rs = M('UserCoin')->where(array('userid' => $uid))->save($saveme);

                            } else {
                                $wallet=L('Wallet System is currently offline 2! '.$coin);
                                $show_qr=0;
                            }
                        } else {
                            //Eth contract
                            $rs1 = M('UserCoin')->where(array('userid' => $uid))->find();
                            if ($rs1['ethb']) {
                                $wallet = $rs1['ethb'];
                                $saveme[$qbdz]=$wallet;
                                $saveme[$coin.'_pass']=cryptString(ETH_USER_PASS);
                                $rs = M('UserCoin')->where(array('userid' => $uid))->save($saveme);

                            } else {
                                //Call the interface to generate a new wallet address
                                $wall_pass=ETH_USER_PASS;
                                $wallet = $EthPayLocal->personal_newAccount($wall_pass);
                                if ($wallet) {
                                    $saveme[$qbdz]=$wallet;
                                    $saveme[$coin.'_pass']=cryptString($wall_pass);
                                    $saveme["ethb"] = $wallet;
                                    $rs = M('UserCoin')->where(array('userid' => $uid))->save($saveme);
                                } else {
                                    //            $this->error('Error generating wallet address 2!');
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

                            $rs = M('UserCoin')->where(array('userid' => $uid))->save(array($qbdz => $wallet));

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
                            $rs1 = M('UserCoin')->where(array('userid' => $uid))->find();
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
                                $rs = M('UserCoin')->where(array('userid' => $uid))->save(array($qbdz => $wallet));
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

                            $rs = M('UserCoin')->where(array('userid' => $uid))->save(array($qbdz => $wallet));

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
                            $rs = M('UserCoin')->where(array('userid' => $uid))->save(array($cryptofields => $wallet));

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

                            $rs = M('UserCoin')->where(array('userid' => $uid))->save(array($qbdz => $wallet));

                            if (!$rs) {
                                $this->error('Add error address wallet3!');
                            }
                        }
                    }
                } else {

                    $wallet = $user_coin[$coin . 'b'];
                }
            }
        } else {

            if (!$Coin['zr_jz']) {
                $wallet = L('The current ban into the currency!');
            } else {

                $wallet = $Coin['codono_coinaddress'];

                $cellphone = M('User')->where(array('id' => $uid))->getField('cellphone');
                $email = M('User')->where(array('id' => $uid))->getField('email');

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

        if($wallet){
            $send_data['status']=1;
            $send_data['wallet']=$wallet;
            $send_data['showqr']=$show_qr;
        }else{
            $send_data['status']=0;
            $send_data['wallet']='Not available';
            $send_data['showqr']=0;
        }

        header('Content-type: application/json');
        echo(json_encode($send_data));exit;
    }
    public function depositaddress($coin = NULL)
    {
        $uid=$this->userid();

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
        $user_coin = M('UserCoin')->where(array('userid' => $uid))->find();
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
                        $rs = M('UserCoin')->where(array('userid' => $uid))->save(array($qbdz => $wallet));
                        $user_exists = M('User')->where(array('id' => $uid))->getField('id');

                        if (!$rs && !$user_exists) {
                            $this->error(L('Generate wallet address wrong!'));
                        }
                        if (!$rs && $user_exists) {

                            $ucoin[$qbdz] = $wallet;
                            $ucoin['userid'] = $user_exists;
                            $new_rs = M('UserCoin')->add($ucoin);
                            exit;
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
                                $rs = M('UserCoin')->where(array('userid' => $uid))->save($saveme);

                            } else {
                                $wallet=L('Wallet System is currently offline 2! '.$coin);
                                $show_qr=0;
                            }
                        } else {
                            //Eth contract
                            $rs1 = M('UserCoin')->where(array('userid' => $uid))->find();
                            if ($rs1['ethb']) {
                                $wallet = $rs1['ethb'];
                                $saveme[$qbdz]=$wallet;
                                $saveme[$coin.'_pass']=cryptString(ETH_USER_PASS);
                                $rs = M('UserCoin')->where(array('userid' => $uid))->save($saveme);

                            } else {
                                //Call the interface to generate a new wallet address
                                $wall_pass=ETH_USER_PASS;
                                $wallet = $EthPayLocal->personal_newAccount($wall_pass);
                                if ($wallet) {
                                    $saveme[$qbdz]=$wallet;
                                    $saveme[$coin.'_pass']=cryptString($wall_pass);
                                    $saveme["ethb"] = $wallet;
                                    $rs = M('UserCoin')->where(array('userid' => $uid))->save($saveme);
                                } else {
                                    //            $this->error('Error generating wallet address 2!');
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

                            $rs = M('UserCoin')->where(array('userid' => $uid))->save(array($qbdz => $wallet));

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
                            $rs1 = M('UserCoin')->where(array('userid' => $uid))->find();
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
                                $rs = M('UserCoin')->where(array('userid' => $uid))->save(array($qbdz => $wallet));
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

                            $rs = M('UserCoin')->where(array('userid' => $uid))->save(array($qbdz => $wallet));

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
                            $rs = M('UserCoin')->where(array('userid' => $uid))->save(array($cryptofields => $wallet));

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

                            $rs = M('UserCoin')->where(array('userid' => $uid))->save(array($qbdz => $wallet));

                            if (!$rs) {
                                $this->error('Add error address wallet3!');
                            }
                        }
                    }
                } else {

                    $wallet = $user_coin[$coin . 'b'];
                }
            }
        } else {

            if (!$Coin['zr_jz']) {
                $wallet = L('The current ban into the currency!');
            } else {

                $wallet = $Coin['codono_coinaddress'];

                $cellphone = M('User')->where(array('id' => $uid))->getField('cellphone');
                $email = M('User')->where(array('id' => $uid))->getField('email');

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

        if($wallet){
            $send_data['status']=1;
            $send_data['wallet']=$wallet;
            $send_data['showqr']=$show_qr;
        }else{
            $send_data['status']=0;
            $send_data['wallet']='Not available';
            $send_data['showqr']=0;
        }

        header('Content-type: application/json');
        echo(json_encode($send_data));exit;
    }
    public function coinbalance($coin='btc')
    {
        $coin=strip_tags(htmlspecialchars($coin));
        $uid=$this->userid();
        $ret = array();
        $uid = $this->userid();
        $ret['baseinfo'] = M('User')->where(array('id' => $uid))->find();
        $CoinList = M('Coin')->where(array('status' => 1,'name'=>$coin))->select();
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

                $coinList[] = array('id' => $marketid,'sort' => (int)$v['sort'], 'name' => $v['title'],'symbol' => $v['name'], 'ico' => SITE_URL. 'Upload/coin/' . $v['img'], 'title' => $v['title'] . '(' . strtoupper($v['name']) . ')', 'total' => round($UserCoin[$v['name']] * 1, 4),'xnb' => round($UserCoin[$v['name']] * 1, 4), 'xnbd' => round($UserCoin[$v['name'] . 'd'] * 1, 4), 'xnbz' => round($UserCoin[$v['name']] + $UserCoin[$v['name'] . 'd'], 4), 'jia' => $jia * 1, 'zhehe' => round(($UserCoin[$v['name']] + $UserCoin[$v['name'] . 'd']) * $jia, 4));
                $usd['zj'] = $usd['zj'] + (round(($UserCoin[$v['name']] + $UserCoin[$v['name'] . 'd']) * $jia, 4) * 1);
            }
        }

        $ret['coin']['usd'] = $usd;
        $ret['coin']['coinList'] = $coinList[0];
        $this->ajaxShow($coinList[0]);
    }

    public function doWithdraw()
    {
        $input =  $_POST = json_decode(file_get_contents('php://input'), true);
        $coin=$input['coin'];
        $num=$input['num'];
        $addr=$input['addr'];
        $paypassword=$input['paypassword'];
        $uid = $this->userid();
        $num = abs($num);
        if (!check($num, 'currency')) {
            $this->error(L('Number format error!'));
        }

        if (!check($addr, 'dw')) {
            $this->error(L('Wallet address format error!'));
        }

        if (!check($paypassword, 'password')) {
            $this->error(L('Fund Pwd format error!'));
        }

        if (!check($coin, 'n')) {
            $this->error(L('Currency format error! ').$coin);
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

        $user = M('User')->where(array('id' => $uid))->find();

        if (md5($paypassword) != $user['paypassword']) {
            $this->error(L('Trading password is wrong!'));
        }

        $user_coin = M('UserCoin')->where(array('userid' => $uid))->find();

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
                $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $uid))->setDec($coin, $num);
                $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $peer['userid']))->setInc($coin, $mum);

                $rs[] = $mo->table('codono_myzc')->add(array('userid' => $uid, 'username' => $addr, 'coinname' => $coin, 'txid' => md5($addr . $user_coin[$coin . 'b'] . time()), 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'addtime' => time(), 'status' => 1));
                $rs[] = $mo->table('codono_myzr')->add(array('userid' => $peer['userid'], 'username' => $user_coin[$coin . 'b'], 'coinname' => $coin, 'txid' => md5($user_coin[$coin . 'b'] . $addr . time()), 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'addtime' => time(), 'status' => 1));
                $this->success('You have successfully raised the coins and will automatically transfer them out after the admin review!');
            } else {
                //eth Wallet Withdrawal
                $heyue = $CoinInfo['dj_yh'];//Contract Address
                $dj_password = $CoinInfo['dj_mm'];
                $dj_address = $CoinInfo['dj_zj'];
                $dj_port = $CoinInfo['dj_dk'];
                $auto_status = ($CoinInfo['zc_zd'] && ($num < $CoinInfo['zc_zd']) ? 1 : 0);
                $mo = M();
                $rs = array();
                $rs[] = $r = $mo->table('codono_user_coin')->where(array('userid' => $uid))->setDec($coin, $num);
                $rs[] = $aid = $mo->table('codono_myzc')->add(array('userid' => $uid, 'username' => $addr, 'coinname' => $coin, 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'addtime' => time(), 'status' => $auto_status));
                if ($auto_status) {
                    $EthCommon = new \Org\Util\EthCommon($dj_address, $dj_port, "2.0");
                    $EthPayLocal = new \Org\Util\EthPayLocal($dj_address, $dj_port, "2.0", $heyue,$dj_password);
                    if ($heyue) {
                        //Contract Address transfer out
                        $zhuan['fromaddress'] = $CoinInfo['codono_coinaddress'];
                        $zhuan['toaddress'] = $addr;
                        $zhuan['token'] = $heyue;
                        $zhuan['type'] = $coin;
                        $zhuan['amount'] = floatval($mum);
                        $zhuan['password'] =$CoinInfo['password'];
                        $sendrs = $EthPayLocal->eth_ercsendTransaction($zhuan);

                    } else {
                        //eth
                        $zhuan['fromaddress'] = $CoinInfo['codono_coinaddress'];
                        $zhuan['toaddress'] = $addr;
                        $zhuan['amount'] = floatval($mum);
                        $zhuan['password'] =$CoinInfo['password'];
                        $sendrs = $EthPayLocal->eth_sendTransaction($zhuan);
                    }

                    if ($sendrs && $aid) {
                        $arr = json_decode($sendrs, true);
                        $hash = $arr['result'] ? $arr['result'] : $arr['error']['message'];
                        if ($hash) M()->execute("UPDATE `codono_myzc` SET  `hash` =  '$hash' WHERE id = '$aid' ");
                    }
                    $this->success('You have successfully requested for withdrawal, It will be auto withdrawn after backend review!'  );
                }
                $this->success('You have successfully requested for withdrawal, It will be auto withdrawn after backend review!');

            }
        }
        //eth Ends

        if ($CoinInfo['type'] == 'rgb') {
            //debug($coin, L('Start the transfer of coins'));
            $peer = M('UserCoin')->where(array($qbdz => $addr))->find();
            if (!$peer) {
                $this->error(L('Withdrawal Address of ICO does not exist!'));
            }

            $mo = M();
            $mo->execute('set autocommit=0');
            $mo->execute('lock tables  codono_user_coin write  , codono_myzc write  , codono_myzr write , codono_myzc_fee write');
            $rs = array();
            $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $uid))->setDec($coin, $num);
            $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $peer['userid']))->setInc($coin, $mum);

            if ($fee) {
                if ($mo->table('codono_user_coin')->where(array($qbdz => $CoinInfo['zc_user']))->find()) {
                    $rs[] = $mo->table('codono_user_coin')->where(array($qbdz => $CoinInfo['zc_user']))->setInc($coin, $fee);
                    debug(array('msg' => L('Withdraw to charge a fee') . $fee), 'fee');
                } else {
                    $rs[] = $mo->table('codono_user_coin')->add(array($qbdz => $CoinInfo['zc_user'], $coin => $fee));
                    debug(array('msg' => L('Withdraw to charge a fee') . $fee), 'fee');
                }
            }

            $rs[] = $mo->table('codono_myzc')->add(array('userid' => $uid, 'username' => $addr, 'coinname' => $coin, 'txid' => md5($addr . $user_coin[$coin . 'b'] . time()), 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'addtime' => time(), 'status' => 1));
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
        //Coinpayments starts
        if ($CoinInfo['type'] == 'coinpay') {
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
                $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $uid))->setDec($coin, $num);
                $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $peer['userid']))->setInc($coin, $mum);

                if ($fee) {
                    if ($mo->table('codono_user_coin')->where(array($qbdz => $CoinInfo['zc_user']))->find()) {
                        $rs[] = $mo->table('codono_user_coin')->where(array($qbdz => $CoinInfo['zc_user']))->setInc($coin, $fee);
                    } else {
                        $rs[] = $mo->table('codono_user_coin')->add(array($qbdz => $CoinInfo['zc_user'], $coin => $fee));
                    }
                }

                $rs[] = $mo->table('codono_myzc')->add(array('userid' => $uid, 'username' => $addr, 'coinname' => $coin, 'txid' => md5($addr . $user_coin[$coin . 'b'] . time()), 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'addtime' => time(), 'status' => 1));
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
                $rs[] = $r = $mo->table('codono_user_coin')->where(array('userid' => $uid))->setDec($coin, $num);
                //Add entry of withdraw [zc] in database
                $rs[] = $aid = $mo->table('codono_myzc')->add(array('userid' => $uid, 'username' => $addr, 'coinname' => $coin, 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'addtime' => time(), 'status' => $auto_status));

                if ($fee && $auto_status) {

                    $rs[] = $mo->table('codono_myzc_fee')->add(array('userid' => $fee_user['userid'], 'username' => $CoinInfo['zc_user'], 'coinname' => $coin, 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'type' => 2, 'addtime' => time(), 'status' => 1));

                    if ($mo->table('codono_user_coin')->where(array($qbdz => $CoinInfo['zc_user']))->find()) {
                        $rs[] = $r = $mo->table('codono_user_coin')->where(array($qbdz => $CoinInfo['zc_user']))->setInc($coin, $fee);
                        debug(array('res' => $r, 'lastsql' => $mo->table('codono_user_coin')->getLastSql()), L('Additional costs'));
                    } else {
                        $rs[] = $r = $mo->table('codono_user_coin')->add(array($qbdz => $CoinInfo['zc_user'], $coin => $fee));
                    }
                }

                if (check_arr($rs)) {
                    if ($auto_status && $can_withdraw==1) {
                        $mo->execute('commit');
                        $mo->execute('unlock tables');
                        $buyer_email = M('User')->where(array('id' => $uid))->getField('email');
                        $withdrawals = ['amount' =>$mum,
                            'add_tx_fee' => 0,
                            'auto_confirm'=>1, //Auto confirm 1 or 0
                            'currency' => $coinpay_coin,
                            'address' => $addr,
                            'ipn_url' => SITE_URL.'IPN/confirm',
                            'note'=>$buyer_email];

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
                $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $uid))->setDec($coin, $num);
                $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $peer['userid']))->setInc($coin, $mum);

                if ($fee) {
                    if ($mo->table('codono_user_coin')->where(array($qbdz => $CoinInfo['zc_user']))->find()) {
                        $rs[] = $mo->table('codono_user_coin')->where(array($qbdz => $CoinInfo['zc_user']))->setInc($coin, $fee);
                    } else {
                        $rs[] = $mo->table('codono_user_coin')->add(array($qbdz => $CoinInfo['zc_user'], $coin => $fee));
                    }
                }

                $rs[] = $mo->table('codono_myzc')->add(array('userid' => $uid, 'username' => $addr, 'coinname' => $coin, 'txid' => md5($addr . $user_coin[$coin . 'b'] . time()), 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'addtime' => time(), 'status' => 1));
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
                $rs[] = $r = $mo->table('codono_user_coin')->where(array('userid' => $uid))->setDec($coin, $num);
                //Add entry of withdraw [zc] in database
                $rs[] = $aid = $mo->table('codono_myzc')->add(array('userid' => $uid, 'username' => $addr, 'coinname' => $coin, 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'addtime' => time(), 'status' => $auto_status));

                if ($fee && $auto_status) {

                    $rs[] = $mo->table('codono_myzc_fee')->add(array('userid' => $fee_user['userid'], 'username' => $CoinInfo['zc_user'], 'coinname' => $coin, 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'type' => 2, 'addtime' => time(), 'status' => 1));

                    if ($mo->table('codono_user_coin')->where(array($qbdz => $CoinInfo['zc_user']))->find()) {
                        $rs[] = $r = $mo->table('codono_user_coin')->where(array($qbdz => $CoinInfo['zc_user']))->setInc($coin, $fee);
                        // debug(array('res' => $r, 'lastsql' => $mo->table('codono_user_coin')->getLastSql()), L('Additional costs'));

                    } else {
                        $rs[] = $r = $mo->table('codono_user_coin')->add(array($qbdz => $CoinInfo['zc_user'], $coin => $fee));
                    }
                }

                if (check_arr($rs)) {
                    if ($auto_status && $can_withdraw==1) {
                        $mo->execute('commit');
                        $mo->execute('unlock tables');
                        $buyer_email = M('User')->where(array('id' => $uid))->getField('email');

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
                $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $uid))->setDec($coin, $num);
                $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $peer['userid']))->setInc($coin, $mum);

                if ($fee) {
                    if ($mo->table('codono_user_coin')->where(array($qbdz => $CoinInfo['zc_user']))->find()) {
                        $rs[] = $mo->table('codono_user_coin')->where(array($qbdz => $CoinInfo['zc_user']))->setInc($coin, $fee);
                    } else {
                        $rs[] = $mo->table('codono_user_coin')->add(array($qbdz => $CoinInfo['zc_user'], $coin => $fee));
                    }
                }

                $rs[] = $mo->table('codono_myzc')->add(array('userid' => $uid, 'username' => $addr, 'coinname' => $coin, 'txid' => md5($addr . $user_coin[$coin . 'b'] . time()), 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'addtime' => time(), 'status' => 1));
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
                $rs[] = $r = $mo->table('codono_user_coin')->where(array('userid' => $uid))->setDec($coin, $num);
                //Add entry of withdraw [zc] in database
                $rs[] = $aid = $mo->table('codono_myzc')->add(array('userid' => $uid, 'username' => $addr, 'coinname' => $coin, 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'addtime' => time(), 'status' => $auto_status));

                if ($fee && $auto_status) {

                    $rs[] = $mo->table('codono_myzc_fee')->add(array('userid' => $fee_user['userid'], 'username' => $CoinInfo['zc_user'], 'coinname' => $coin, 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'type' => 2, 'addtime' => time(), 'status' => 1));

                    if ($mo->table('codono_user_coin')->where(array($qbdz => $CoinInfo['zc_user']))->find()) {
                        $rs[] = $r = $mo->table('codono_user_coin')->where(array($qbdz => $CoinInfo['zc_user']))->setInc($coin, $fee);
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
                $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $uid))->setDec($coin, $num);
                $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $peer['userid']))->setInc($coin, $mum);

                if ($fee) {
                    if ($mo->table('codono_user_coin')->where(array($qbdz => $CoinInfo['zc_user']))->find()) {
                        $rs[] = $mo->table('codono_user_coin')->where(array($qbdz => $CoinInfo['zc_user']))->setInc($coin, $fee);
                    } else {
                        $rs[] = $mo->table('codono_user_coin')->add(array($qbdz => $CoinInfo['zc_user'], $coin => $fee));
                    }
                }

                $rs[] = $mo->table('codono_myzc')->add(array('userid' => $uid, 'username' => $addr, 'coinname' => $coin, 'txid' => md5($addr . $user_coin[$coin . 'b'] . time()), 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'addtime' => time(), 'status' => 1));
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
                $rs[] = $r = $mo->table('codono_user_coin')->where(array('userid' => $uid))->setDec($coin, $num);
                //Add entry of withdraw [zc] in database
                $rs[] = $aid = $mo->table('codono_myzc')->add(array('userid' => $uid, 'username' => $addr, 'coinname' => $coin, 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'addtime' => time(), 'status' => $auto_status));

                if ($fee && $auto_status) {

                    $rs[] = $mo->table('codono_myzc_fee')->add(array('userid' => $fee_user['userid'], 'username' => $CoinInfo['zc_user'], 'coinname' => $coin, 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'type' => 2, 'addtime' => time(), 'status' => 1));

                    if ($mo->table('codono_user_coin')->where(array($qbdz => $CoinInfo['zc_user']))->find()) {
                        $rs[] = $r = $mo->table('codono_user_coin')->where(array($qbdz => $CoinInfo['zc_user']))->setInc($coin, $fee);
                        debug(array('res' => $r, 'lastsql' => $mo->table('codono_user_coin')->getLastSql()), L('Additional costs'));
                    } else {
                        $rs[] = $r = $mo->table('codono_user_coin')->add(array($qbdz => $CoinInfo['zc_user'], $coin => $fee));
                    }
                }

                if (check_arr($rs)) {
                    if ($auto_status && $can_withdraw==1) {
                        $mo->execute('commit');
                        $mo->execute('unlock tables');

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
                $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $uid))->setDec($coin, $num);
                $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $peer['userid']))->setInc($coin, $mum);

                if ($fee) {
                    if ($mo->table('codono_user_coin')->where(array($qbdz => $CoinInfo['zc_user']))->find()) {
                        $rs[] = $mo->table('codono_user_coin')->where(array($qbdz => $CoinInfo['zc_user']))->setInc($coin, $fee);
                    } else {
                        $rs[] = $mo->table('codono_user_coin')->add(array($qbdz => $CoinInfo['zc_user'], $coin => $fee));
                    }
                }

                $rs[] = $mo->table('codono_myzc')->add(array('userid' => $uid, 'username' => $addr, 'coinname' => $coin, 'txid' => md5($addr . $user_coin[$coin . 'b'] . time()), 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'addtime' => time(), 'status' => 1));
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
                $CoinClient = CoinClient($dj_username, $dj_password, $dj_address, $dj_port, 5, array(), 1);

                if($can_withdraw==1){
                    $auto_status = ($CoinInfo['zc_zd'] && ($num < $CoinInfo['zc_zd']) ? 1 : 0);

                }
                else{
                    $auto_status=0;
                }
                $json = $CoinClient->getinfo();

                $can_withdraw=1;
                if (!isset($json['version']) || !$json['version']) {
                    //   $this->error(L('Wallet link failure! 2'));
                    debug($coin.' Could not be connected at '.time() .'<br/>');
                    $can_withdraw=0;
                }

                $valid_res = $CoinClient->validateaddress($addr);

                if (!$valid_res['isvalid']) {
                    $this->error($addr . L('It is not a valid address wallet!'));
                }



                if ($json['balance'] < $num) {
                    //$this->error(L('Wallet balance of less than'));
                    debug($coin.' :Low wallet balance: '.time() .'<br/>');
                    $can_withdraw=0;
                }

                $mo = M();
                $mo->execute('set autocommit=0');
                $mo->execute('lock tables  codono_user_coin write  , codono_myzc write ,codono_myzr write, codono_myzc_fee write');
                $rs = array();
                //Reduce Withdrawers balance
                $rs[] = $r = $mo->table('codono_user_coin')->where(array('userid' => $uid))->setDec($coin, $num);
                //Add entry of withdraw [zc] in database
                $rs[] = $aid = $mo->table('codono_myzc')->add(array('userid' => $uid, 'username' => $addr, 'coinname' => $coin, 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'addtime' => time(), 'status' => $auto_status));

                if ($fee && $auto_status) {

                    $rs[] = $mo->table('codono_myzc_fee')->add(array('userid' => $fee_user['userid'], 'username' => $CoinInfo['zc_user'], 'coinname' => $coin, 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'type' => 2, 'addtime' => time(), 'status' => 1));

                    if ($mo->table('codono_user_coin')->where(array($qbdz => $CoinInfo['zc_user']))->find()) {
                        $rs[] = $r = $mo->table('codono_user_coin')->where(array($qbdz => $CoinInfo['zc_user']))->setInc($coin, $fee);
                        debug(array('res' => $r, 'lastsql' => $mo->table('codono_user_coin')->getLastSql()), L(' Received fees amount'));
                    } else {
                        $rs[] = $r = $mo->table('codono_user_coin')->add(array($qbdz => $CoinInfo['zc_user'], $coin => $fee));
                    }
                }

                if (check_arr($rs)) {
                    if ($auto_status && $can_withdraw==1) {
                        $mo->execute('commit');
                        $mo->execute('unlock tables');
                        $sendrs = $CoinClient->sendtoaddress($addr, floatval($mum));

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
                            $this->error('wallet server  Withdraw currency failure,Manually turn out');
                        } else {
                            $this->success('Successful Withdrawal!');
                        }
                    }

                    if ($auto_status && $can_withdraw==1) {
                        M('Myzc')->where(array('id' => $aid))->save(array('txid'=>$sendrs));
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
                    $this->error('Roll-out failure!');
                }
            }
        }
        //Bitcoin Type Ends
    }


}//End of class
?>