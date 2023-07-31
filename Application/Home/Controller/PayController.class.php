<?php
namespace Home\Controller;
use Omnipay\Omnipay;



class PayController extends HomeController
{
	public function paywall($mycz){
		  if (!userid()) {
            $this->error(L('PLEASE_LOGIN'));
        }
		$email = M('User')->where(array('id' => userid()))->getField("email");
		$paymentwall = paymentwall(userid(),$email,$mycz);
		echo "<iframe src='$paymentwall' width='100%' height='100%'/>";
	}
	public function pbe_requestOTP($coinname,$address,$amount){
		if (!userid()) {
			$this->error(L('Please login first!'));
        }
		$user = M('User')->where(array('id' => userid()))->find();
		if (time() < (session('pbe_requestOTP_last_' . userid()) + 30)) {
				$this->error('Too fast wait for 30 seconds before you proceed again!');
		}
		$receiver_id=M('User')->where(array('email' => $address,'status'=>1))->getField('id');
		if($receiver_id==userid())
		{
		//	$this->error(L('You can not send to your self!'));
		}
		if (!$receiver_id) {
            $this->error(L('No such active user exists!'));
		}
		$user_coin = M('UserCoin')->where(array('userid' => userid()))->find();
		
        if ($user_coin[$coinname] < $amount) {
            $this->error(L('Insufficient funds available'));
        }
		$code=tradeno();
		session('pbe_requestOTP', $code);
		session('pbe_requestOTP_last_'.userid(), time());
		$email=$user['email'];
		$client_ip = get_client_ip();
        $requestTime = date('Y-m-d H:i',time()).'('.date_default_timezone_get().')';
		$subject="Payment Request on ".SHORT_NAME;
		$content="<br/><strong>DO NOT SHARE THIS CODE WITH ANYONE!!</strong><br/>To complete the withdrawal process,<br/><br/>You may be asked to enter this confirmation code:<strong>$code <strong><br/><br/><small><i>
			<table>
			<tr style='border:2px solid black'><td>Email</td><td>$email</td></tr>
			<tr style='border:2px solid black'><td>IP</td><td>$client_ip</td></tr>
			<tr style='border:2px solid black'><td>Coin</td><td>$coinname</td></tr>
			<tr style='border:2px solid black'><td>Amount</td><td>$amount</td></tr>
			<tr style='border:2px solid black'><td>Email</td><td>$address</td></tr>
			<tr style='border:2px solid black'><td>Time</td><td>$requestTime</td></tr>	
			</table>
			<strong>If You didnt request this Payment to above, immediately change passwords,and contact us</strong>";
			exec($mail_Sent=json_decode(tmail($email,$subject,$content)));
			$this->success(L('Please check email for code'));
	}
	public function upPaybyemail($otp=0,$coin, $num, $email, $paypassword)
	{

		if (!userid()) {
            $this->error(L('YOU_NEED_TO_LOGIN'));
        }
		if (!kyced()) {
               $this->error(L('Complete KYC First!'));
		}
		
        $num = abs($num);

        if (!check($num, 'currency')) {
            $this->error(L('Number format error!'));
        }
		if ($otp != session('pbe_requestOTP')) {
                 $this->error('Incorrect OTP!');
        }
		
        if (!check($email, 'email')) {
            $this->error(L('Incorrect Email format!'));
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
		
		 $fee = round(($num / 100) * $CoinInfo['zc_fee'], 8);
            $mum = round($num - $fee, 8);

            if ($mum < 0) {
                $this->error(L('Incorrect withdrawal amount!'));
            }

            if ($fee < 0) {
                $this->error(L('Incorrect withdrawal fee!'));
            }

        $user = M('User')->where(array('id' => userid()))->find();

        if (md5($paypassword) != $user['paypassword']) {
            $this->error(L('Trading password is wrong!'));
        }

        $user_coin = M('UserCoin')->where(array('userid' => userid()))->find();
		
        if ($user_coin[$coin] < $num) {
            $this->error(L('Insufficient funds available'));
        }

        //Lock Tables here;
		$mo = M();
		$peer_id=$mo->table('codono_user')->where(array('email' => $email))->getField("id");
        
		
        if ($peer_id>0) {
		
		$sender_email = M('User')->where(array('id' => userid()))->getField('email');
        $mo->execute('set autocommit=0');
        $mo->execute('lock tables codono_paybyemail write ,  codono_user_coin write  , codono_myzr write  ,codono_finance write');
        $rs = array();
				$txid= md5($email . $otp . time());
				$code=$otp;
                $rs[] = $mo->table('codono_user_coin')->where(array('userid' => userid()))->setDec($coin, $num);
                $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $peer_id))->setInc($coin, $mum);
				$add_array=array('from_userid' => userid(),'to_userid' => $peer_id,'code'=>$code, 'email' => $email, 'coinname' => $coin, 'txid' =>$txid, 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'addtime' => time(), 'status' => 1);
                $rs[] =$add_status= $mo->table('codono_paybyemail')->add($add_array);
                $rs[] = $mo->table('codono_myzr')->add(array('userid' => $peer_id, 'username' => $sender_email, 'coinname' => $coin, 'txid' => $txid, 'num' => $num, 'fee' => $fee, 'mum' => $mum, 'addtime' => time(), 'status' => 1));
			
			//Check Executed Withdrawal
			if (check_arr($rs)) {
				$mo->execute('commit');
				$mo->execute('unlock tables');
			$requestTime = date('Y-m-d H:i',time()).'('.date_default_timezone_get().')';
			$subject="Payment Recevied on ".SHORT_NAME;
			$content="<br/>You have just received a payment on your email,<br/>
			<table>
			<tr style='border:2px solid black'><td>From Email</td><td>$sender_email</td></tr>
			<tr style='border:2px solid black'><td>Coin</td><td>$coin</td></tr>
			<tr style='border:2px solid black'><td>Txid</td><td>$txid</td></tr>
			<tr style='border:2px solid black'><td>Sent Amount</td><td>$num</td></tr>
			<tr style='border:2px solid black'><td>Fee Amount</td><td>$fee</td></tr>
			<tr style='border:2px solid black'><td>Received Amount</td><td>$mum</td></tr>
			<tr style='border:2px solid black'><td>Time</td><td>$requestTime</td></tr>	
			</table>
			<strong>If You think this payment was sent in error contact us on support</strong>";
			exec($mail_Sent=json_decode(tmail($email,$subject,$content)));
				$this->success('You have successfully sent the funds!');
			} else {
				$mo->execute('rollback');
				$this->error(L('Due to some reasons payment could not be sent !'));
			}
         
		}else{
			$this->error('There is no such user, Please cross check with email address!');
		}
	}
	public function paybyemail($coin = NULL){

		if (!userid()) {
            $this->error(L('PLEASE_LOGIN'));
        }
	
		    if (C('coin')[$coin]) {
			$explorer=C('coin')[$coin]['js_wk'];
            $coin = trim($coin);
        } else {
            $coin = C('xnb_mr');
        }

        $this->assign('xnb', $coin);
        $Coininfo = M('Coin')->where(array(
            'status' => 1,
            'name' => array('neq', 'usd')
        ))->select();

        foreach ($Coininfo as $k => $v) {
            $coin_list[$v['name']] = $v;
        }

        $this->assign('coin_list', $coin_list);
        $user_coin = M('UserCoin')->where(array('userid' => userid()))->find();
        $user_coin[$coin] = format_num($user_coin[$coin], 6);
        $this->assign('user_coin', $user_coin);

		$where['from_userid'] = userid();
        $where['coinname'] = $coin;
		$where['status']=1;
        $Model = M('Paybyemail');
        $count = $Model->where($where)->count();
        $Page = new \Think\Page($count, 10);
        $show = $Page->show();
        $list = $Model->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list);
        $this->assign('page', $show);
		$this->display('Pay/paybyemail');
	}
	public function authorize($id){
		if (!userid()) {
            redirect('/#login');
        }
		if(AUTHORIZE_NET['status']==0){
			   $this->error(L('Gateway is currently disabled'));
		}
		if (!check($id, 'idcard')) {
            $this->error(L('Invalid Attempt'));
        }
		$where=array('tradeno' => $id,'userid'=>userid(),'status'=>0);
		$mycz = M('Mycz')->where($where)->find();
		if (!$mycz) {
            $this->error(L('Top-order does not exist!'));
        }
		$user_email = M('User')->where(array('id' => userid()))->getField('email');
		
		require_once(APP_PATH . '../Framework/Library/Vendor/vendor/autoload.php');
		
		$dataDescriptor=I('post.dataDescriptor','','text');
		$dataValue=I('post.dataValue','','text');
		
		$gateway = Omnipay::create('AuthorizeNet_AIM');
		$gateway->setApiLoginId(AUTHORIZE_NET['loginid']);
		$gateway->setTransactionKey(AUTHORIZE_NET['transactionkey']);
		if(AUTHORIZE_NET['mode']=="test"){
		//$gateway->setTestMode(true);
		$gateway->setDeveloperMode(true);
		}
		else{
		$gateway->setTestMode(false);	
		$gateway->setDeveloperMode(false);
		}
		
		$request = $gateway->purchase(
    	[
        'notifyUrl' => SITE_URL.'Pay/webhook_authorize',
		'returnUrl' => SITE_URL.'Pay/webhook_authorize',
		'invoiceNumber'=>$mycz['tradeno'],
		'description'=>'UID_'.userid().'_tradeno_'.$mycz['tradeno'],
		'customerId'=>userid(),
		'email'=>$user_email,
        'amount' => format_num($mycz['num'],2),
        'opaqueDataDescriptor' => $dataDescriptor,
        'opaqueDataValue' => $dataValue]);

		$response = $request->send();

    // Payment was successful
    if ($response->isRedirect()) {
    // redirect to offsite payment gateway
    $response->redirect();
} elseif ($response->isSuccessful()) {
    // payment was successful: update database
    	
		$ipn_response=$response->getTransactionReference();	
		$remark_obj=json_decode($response->getTransactionReference());	
		$transid=$remark_obj->transId;
		$save_array=array('remark' => $transid,'status'=>4,'ipn_response'=>$ipn_response);
		$rs = M('Mycz')->where(array('userid' => userid(),'id'=>$mycz['id'],'tradeno'=>$id))->save($save_array);
		echo '<div class="center"><h2>'.$response->getMessage().'</h2> <br/> Close this window...</div>';
		
} else {
    // payment failed: display message to customer
	echo $response->getMessage();
    echo '<div class="center">We could not process your payment, Please try again in sometime <br/> Close this window...</div>';
		
}
		
}
	public function webhook_authorize(){

		$content=json_encode($_POST);
		$filename='authorize_webhook_'.time();
		file_put_contents('Public/Log/' . $filename . '.log', $content);
		echo "Saved";
	}
    public function index()
    {
        if (IS_POST) {
            if (isset($_POST['alipay'])) {
                $arr = explode('--', $_POST['alipay']);

                if (md5('codono') != $arr[2]) {
                    echo -1;
                    exit();
                }

                if (!strstr($arr[0], 'Pay')) {
                }

                $arr[0] = trim(str_replace(PHP_EOL, '', $arr[0]));
                $arr[1] = trim(str_replace(PHP_EOL, '', $arr[1]));

                if (strstr($arr[0], 'payment-')) {
                    $arr[0] = str_replace('payment-', '', $arr[0]);
                }

                $mycz = M('Mycz')->where(array('tradeno' => $arr[0]))->find();

                if (!$mycz) {
                    echo -3;
                    exit();
                }

                if (($mycz['status'] != 0) && ($mycz['status'] != 3)) {
                    echo -4;
                    exit();
                }

                if ($mycz['num'] != $arr[1]) {
                    echo -5;
                    exit();
                }

                $mo = M();
                $mo->execute('set autocommit=0');
                $mo->execute('lock tables codono_user_coin write,codono_mycz write,codono_finance write');
                $rs = array();
                $finance = $mo->table('codono_finance')->where(array('userid' => $mycz['userid']))->order('id desc')->find();
                $finance_num_user_coin = $mo->table('codono_user_coin')->where(array('userid' => $mycz['userid']))->find();
                $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $mycz['userid']))->setInc('usd', $mycz['num']);
                $rs[] = $mo->table('codono_mycz')->where(array('id' => $mycz['id']))->save(array('status' => 1, 'mum' => $mycz['num'], 'endtime' => time()));
                $finance_mum_user_coin = $mo->table('codono_user_coin')->where(array('userid' => $mycz['userid']))->find();
                $finance_hash = md5($mycz['userid'] . $finance_num_user_coin['usd'] . $finance_num_user_coin['usdd'] . $mycz['num'] . $finance_mum_user_coin['usd'] . $finance_mum_user_coin['usdd'] . CODONOLIC . 'auth.codono.com');
                $finance_num = $finance_num_user_coin['usd'] + $finance_num_user_coin['usdd'];

                if ($finance['mum'] < $finance_num) {
                    $finance_status = (1 < ($finance_num - $finance['mum']) ? 0 : 1);
                } else {
                    $finance_status = (1 < ($finance['mum'] - $finance_num) ? 0 : 1);
                }

                $rs[] = $mo->table('codono_finance')->add(array('userid' => $mycz['userid'], 'coinname' => 'usd', 'num_a' => $finance_num_user_coin['usd'], 'num_b' => $finance_num_user_coin['usdd'], 'num' => $finance_num_user_coin['usd'] + $finance_num_user_coin['usdd'], 'fee' => $mycz['num'], 'type' => 1, 'name' => 'mycz', 'nameid' => $mycz['id'], 'remark' => 'Fiat Recharge-Artificial arrival', 'mum_a' => $finance_mum_user_coin['usd'], 'mum_b' => $finance_mum_user_coin['usdd'], 'mum' => $finance_mum_user_coin['usd'] + $finance_mum_user_coin['usdd'], 'move' => $finance_hash, 'addtime' => time(), 'status' => $finance_status));

                if (check_arr($rs)) {
                    $mo->execute('commit');
                    $mo->execute('unlock tables');
                    echo 1;
                    exit();
                } else {
                    $mo->execute('rollback');
                    $mo->query('rollback');
                    echo -6;
                    exit();
                }
            }
        }
    }

    public function mycz($id = NULL)
    {
        if (check($id, 'd')) {
            $mycz = M('Mycz')->where(array('id' => $id))->find();

            if (!$mycz) {
                $this->redirect('Finance/mycz');
            }

            $myczType = M('MyczType')->where(array('name' => $mycz['type']))->find();
			
			if($myczType['name']=='paymentwall'){
				$this->paywall($mycz);
			}
	

            if ($mycz['type'] == 'bank') {
                $UserBankType = M('UserBankType')->where(array('status' => 1))->order('id desc')->select();
                $this->assign('UserBankType', $UserBankType);
            }

            $this->assign('myczType', $myczType);
            $this->assign('mycz', $mycz);
			
            $this->display($mycz['type']);
        } else {
            $this->redirect('Finance/mycz');
        }
    }

}

?>