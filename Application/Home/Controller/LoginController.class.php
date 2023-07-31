<?php

namespace Home\Controller;
use Think\SuperEmail;
class LoginController extends HomeController
{
	public function emailcode($email=0)
    {
		$code = rand(111111, 999999);
        session('real_verify', $code);
        
		if (!check($email, 'email')) {
            $this->error(L('INVALID_EMAIL_FORMAT'));
			}

			if (M('User')->where(array('email' => $email))->find()) {
            $this->error(L('Email already exists, Please login instead!'));
			}

			$subject="Signup code for ".SHORT_NAME;
			$content ='Hello There,<br/>
			You have just tried signing up using our app of '.SHORT_NAME.'<br/>
			Your verification code is :'. $code.' <br/>
			Please disregard if you didnt attempt to register.
			';
			$mail_Sent= json_decode(SuperEmail::sendemail($email, $subject, $content));
			if ($mail_Sent->status==1) {
                $this->success(L('Verification code has been sent'));
			} else {
            $this->error(L('Unable to send email Code, Retry in sometime'));
			}
    }
	public function login()
    {
        $this->display();
    }
    public function register()
    {
        $this->display();
    }
	public function getuid()
    {
        $arr["userid"] = userid();
        $arr["moble"] = username(userid());
        $arr["nickName"] = session("nickname");
		/*userInfo: {
        cny: '0',
        cnyd: '0',
		mobile: '',
		nickName: '',
		token: '',
		userid: '',
		vip: {
			vipdengji: '0',
			fee_discounts: '0%'
		}
	},*/
        echo json_encode($arr);
    }

    public function signup()
    {
        $this->display();
    }

    public function webreg()
    {
        $this->display();
    }

    public function upregister($username = '', $password = '', $repassword = '', $verify = '', $invit = '', $cellphone = '',  $cellphones = '',$cellphone_verify = '')
    {

        if (M_ONLY == 0) {
            if (!check_verify($verify)) {
                $this->error('Incorrect Captcha !');
            }

            if (!check($username, 'username')) {
                $this->error(L('INVALID_USERNAME'));
            }

            if (!check($password, 'password')) {
                $this->error(L('INVALID_PASSWORD'));
            }

            if ($password != $repassword) {
                $this->error(L('Confirm password wrong!'));
            }
        } else {

            if (!check($password, 'password')) {
                $this->error(L('INVALID_PASSWORD'));
            }
            $username = $cellphone;
        }

        if (!check($cellphone, 'cellphone')) {
            $this->error(L('INVALID_PHONE_FORMAT'));
        }
		 if (!$cellphones) {
            $this->error(L('INVALID COUNTRY FORMAT'));
        }

        if (!check($cellphone_verify, 'd')) {
            $this->error(L('INVALID_SMS_CODE'));
        }

        if ($cellphone_verify != session('real_verify')) {
            $this->error(L('INCORRECT_SMS_CODE'));
        }

        if (M('User')->where(array('cellphone' => $cellphone))->find()) {
            $this->error(L('Phone number already exists!'));
        }

        if (M('User')->where(array('username' => $username))->find()) {
            $this->error(L('Username already exists'));
        }

        if (!$invit) {
            $invit = session('invit');
        }

        $invituser = M('User')->where(array('invit' => $invit))->find();

        if (!$invituser) {
            $invituser = M('User')->where(array('id' => $invit))->find();
        }

        if (!$invituser) {
            $invituser = M('User')->where(array('username' => $invit))->find();
        }

        if (!$invituser) {
            $invituser = M('User')->where(array('cellphone' => $invit))->find();
        }

        if ($invituser) {
            $invit_1 = $invituser['id'];
            $invit_2 = $invituser['invit_1'];
            $invit_3 = $invituser['invit_2'];
        } else {
            $invit_1 = 0;
            $invit_2 = 0;
            $invit_3 = 0;
        }

        for (; true;) {
            $tradeno = tradenoa();

            if (!M('User')->where(array('invit' => $tradeno))->find()) {
                break;
            }
        }

        $mo = M();
        $mo->execute('set autocommit=0');
        $mo->execute('lock tables codono_user write , codono_user_coin write ');
        $rs = array();
        $rs[] = $mo->table('codono_user')->add(array('username' => $username, 'cellphone' => $cellphone,'cellphones' => $cellphones, 'cellphonetime' => time(), 'password' => md5($password), 'invit' => $tradeno, 'tpwdsetting' => 1, 'invit_1' => $invit_1, 'invit_2' => $invit_2, 'invit_3' => $invit_3, 'addip' => get_client_ip(), 'addr' => get_city_ip(), 'addtime' => time(), 'status' => 1));
        $rs[] = $mo->table('codono_user_coin')->add(array('userid' => $rs[0]));
	
        if (check_arr($rs)) {
            $mo->execute('commit');
            $mo->execute('unlock tables');
            session('reguserId', $rs[0]);
            $this->success(L('registration success!'));
        } else {
            $mo->execute('rollback');
            $this->error(L('check_arr.registration failed!'));
        }
    }

    //public function emailregister($username='', $password='', $repassword='', $verify='', $invit='', $email='')
    public function emailregister()
    {
		$username = I('post.username', '', 'text');
        $email = I('post.email', '', 'text');
        $verify = I('post.verify', '', 'text');
        $password = I('post.password', '', 'text');
        $invit = I('post.invit', '', 'text');

		 if ($verify != session('real_verify')) {

			$this->error(L('Incorrect Email Code'));
        }

        if (!check($password, 'password')) {
            $this->error(L('INVALID_PASSWORD'));
        }


		if (!check($username, 'username')) {
            $this->error('Incorrect Username : 4-15 chars');
        }
        if (!check($email, 'email')) {
            $this->error(L('INVALID_EMAIL_FORMAT') . $email);
        }

        if (M('User')->where(array('email' => $email))->find()) {
            $this->error(L('Email already exists!'));
        }
        if (M('User')->where(array('username' => $username))->find()) {
            $this->error(L('Username already exists!'));
        }

        if (!$invit) {
            $invit = session('invit');
        }

        $invituser = M('User')->where(array('invit' => $invit))->find();

        if (!$invituser) {
            $invituser = M('User')->where(array('id' => $invit))->find();
        }

        if (!$invituser) {
            $invituser = M('User')->where(array('email' => $email))->find();
        }

        if ($invituser) {
            $invit_1 = $invituser['id'];
            $invit_2 = $invituser['invit_1'];
            $invit_3 = $invituser['invit_2'];
        } else {
            $invit_1 = 0;
            $invit_2 = 0;
            $invit_3 = 0;
        }

        for (; true;) {
            $tradeno = tradenoa();

            if (!M('User')->where(array('invit' => $tradeno))->find()) {
                break;
            }
        }
		$add_user_info=array( 'username' => $username,'email' => $email,  'password' => md5($password), 'invit' => $tradeno, 'tpwdsetting' => 1, 'invit_1' => $invit_1, 'invit_2' => $invit_2, 'invit_3' => $invit_3, 'addip' => get_client_ip(), 'addr' => get_city_ip(), 'status' => 1);
		
        $mo = M();
		$mo->execute('unlock tables');
        $mo->execute('set autocommit=0');
        $mo->execute('lock tables codono_user write , codono_user_coin write, codono_invit write ');
        $rs = array();
		
        $rs[] = $mo->table('codono_user')->add($add_user_info);
        $rs[] = $mo->table('codono_user_coin')->add(array('userid' => $rs[0]));
		$invit_insert=array('userid' =>  $invit_1, 'invit' => $rs[0], 'name' => C('ref_award_coin'), 'type' => 'Signup:Reward-'.C('ref_award_coin'), 'num' => C('ref_award_num'), 'mum' => C('ref_award_num'), 'fee' => '0', 'addtime' => time(), 'status' => 1);
		
		if( C('ref_award') && $invit_1 > 0 && $rs[0]>0){
		$rs[]=$mo->table('codono_invit')->add($invit_insert);
		$rs[] = $mo->table('codono_user_coin')->where(array('userid' => $invit_1))->setInc( C('ref_award_coin'),  C('ref_award_num'));
		}
		
        if (check_arr($rs)) {
            $mo->execute('commit');
            $mo->execute('unlock tables');
            session('reguserId', $rs[0]);
			  $this->success(L('SUCCESSFULLY_REGISTERED'), U('User/index'));
        } else {
            $mo->execute('rollback');
            $this->error(L('Registration Failed!'));
        }
    }

    public function testEmail()
    {
	/*	$string='t_'.time();
        var_dump($this->emailregister($username = $string, $password = $string, $repassword = $string, $verify = '1235426', $invit = 'WERGYJ', $email = $string.'@testing21.com'));exit;
	*/	
    }

    public function check_cellphone($cellphone = 0)
    {

        if (!check($cellphone, 'cellphone')) {
            $this->error(L('INVALID_PHONE_FORMAT'));
        }

        if (M('User')->where(array('cellphone' => $cellphone))->find()) {
            $this->error(L('Phone number already exists!'));
        }

        $this->success('');

    }
	 public function check_email($email = 0)
    {

        if (!check($email, 'email')) {
            $this->error(L('INVALID_FORMAT'));
        }

        if (M('User')->where(array('email' => $email))->find()) {
            $this->error(L('Email already exists!'));
        }
        $this->success('We have sent you a code via email!');
    }
	 public function check_username($username = 0)
    {

        if (!check($username, 'username')) {
            $this->error(L('INVALID_FORMAT'));
        }

        if (M('User')->where(array('username' => $username))->find()) {
            $this->error(L('Username already exists!'));
        }
        $this->success('');
    }

    public function check_pwdcellphone($cellphone = 0)
    {

        if (!check($cellphone, 'cellphone')) {
            $this->error(L('INVALID_PHONE_FORMAT'));
        }

        if (!M('User')->where(array('cellphone' => $cellphone))->find()) {
            $this->error(L('The phone number does not exist!'));
        }

        $this->success('');

    }
	
	public function check_pwdemail($email = 0)
    {

        if (!check($email, 'email')) {
            $this->error(L('INVALID_EMAIL_FORMAT'));
        }

        if (!M('User')->where(array('email' => $email))->find()) {
            $this->error(L('Email does not exist!'));
        }

        $this->success('');

    }


    public function real($email=0,$type='sms',$cellphone=0, $verify=0, $cellphones = '')
    {
		$code = rand(111111, 999999);
        session('real_verify', $code);
        $content = L('Your verification code is') .' '. $code;
		
		if (!check_verify($verify)) {
            $this->error('Incorrect Captcha !');
        }
		if($type=='sms'){
        if (!check($cellphone, 'cellphone')) {
            $this->error(L('INVALID_PHONE_FORMAT'));
        }

        if (M('User')->where(array('cellphone' => $cellphone))->find()) {
            $this->error(L('Phone number already exists!'));
        }

        $full_number = $cellphones . $cellphone;
			if (send_cellphone($full_number, $content)) {
            if (MOBILE_CODE == 0) {
                $this->success(L('Use Demo SMS Code:') . $code);
            } else {
                $this->success(L('Verification code has been sent'));
            }
			} else {
            $this->error(L('Unable to send SMS Code, Retry in sometime'));
			}
		}
		else if($type=='email'){
			if (!check($email, 'email')) {
            $this->error(L('INVALID_EMAIL_FORMAT'));
			}

			if (M('User')->where(array('email' => $email))->find()) {
            $this->error(L('Email already exists!'));
			}
			//$mail_Sent= json_decode(SuperEmail::sendemail($email, "Verification Code", $content));
			$subject="Thank you for signing up for ".SHORT_NAME;
			$content="<br/><strong>DO NOT SHARE THIS CODE WITH ANYONE!!</strong><br/>To complete the sign-up process,<br/><br/>You may be asked to enter this confirmation code:<strong>".$code. "<strong><br/><br/><small><i>
			You are receiving this email because you registered on ".SHORT_NAME." with this email address";
			//$this->addnotification($email,$subject,$content);
			exec($mail_Sent=json_decode(tmail($email,$subject,$content)));
			$this->success(L('Please check email for code'));

			/*if ($mail_Sent->status==1) {
                $this->success(L('Verification code has been sent'));
			} else {
            $this->error(L('Unable to send email Code, Retry in sometime'));
			}*/
			
			
		}
    }


    public function register2()
    {
        if (!session('reguserId')) {
            redirect('/#login');
        }
        $this->display();
    }


    public function paypassword()
    {
        if (!session('reguserId')) {
            $this->error(L('Please login first!'));
        }
		if (M('User')->where(array('id' => session('reguserId'),'paypassword' => array('exp', 'is not null')))->find()) {
            $this->error(L('This step has been already done!'));
        }
        $this->display();
    }


    public function upregister2($paypassword, $repaypassword)
    {
        if (!check($paypassword, 'password')) {
            $this->error(L('Fund Pwd format error!'));
        }

        if ($paypassword != $repaypassword) {
            $this->error(L('INCORRECT_NEW_PWD'));
        }

        if (!session('reguserId')) {
            $this->error(L('Unauthorized access!'));
        }
				   
											   
		 

																			   
		 
        if (M('User')->where(array('id' => session('reguserId'), 'password' => md5($paypassword)))->find()) {
            $this->error(L('Fund Pwd and can not log on as password!'));
        }

        if (M('User')->where(array('id' => session('reguserId')))->save(array('paypassword' => md5($paypassword)))) {
            $this->success(L('success!'));
        } else {
            $this->error(L('failure!'));
        }
    }

    public function register3()
    {
        if (!session('reguserId')) {
            redirect('/#login');
        }
        $this->display();
    }

    public function truename()
    {
        if (!session('reguserId')) {
            redirect('/#login');
        }
		
		if (M('User')->where(array('id' => session('reguserId'), 'truename' => array('exp', 'is not null')))->find()) {
            $this->error(L('Please contact support to for RE-KYC'));
        }
        $this->display();
    }

	public function idimage()
    {
        $upload = new \Think\Upload();
        $upload->maxSize = 3145728;
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg','pdf');
        $upload->rootPath = './Upload/idcard/';
        $upload->autoSub = false;
        $info = $upload->upload();

        foreach ($info as $k => $v) {
            $path = $v['savepath'] . $v['savename'];
            echo $path;
            exit();
        }
    }

    public function upregister3($truename, $idcard,$image)
    {
        if (!check($truename, 'truename')) {
            $this->error(L('Real name format error!'));
        }

        if (!check($idcard, 'idcard')) {
            $this->error(L('ID number format error!'));
        }

        if (!session('reguserId')) {
            $this->error(L('Unauthorized access!'));
        }
		if(!$image){
			$this->error(L('Please upload ID card file!'));
		}

        if (M('User')->where(array('id' => session('reguserId')))->save(array('truename' => $truename, 'idcard' => $idcard,'idcardimg1'=>$image))) {
            $this->success(L('success!'));
        } else {
            $this->error(L('failure!'));
        }
    }

    public function register4()
    {

        if (!session('reguserId')) {
            redirect('/#login');
        }

        $user = M('User')->where(array('id' => session('reguserId')))->find();


        if (!$user) {
            $this->error(L('Please register'));
        }
        if ($user['regaward'] == 0) {
            if (C('reg_award') == 1 && C('reg_award_num') > 0) {
                M('UserCoin')->where(array('userid' => session('reguserId')))->setInc(C('reg_award_coin'), C('reg_award_num'));
                M('User')->where(array('id' => session('reguserId')))->save(array('regaward' => 1));
            }
        }

        session('userId', $user['id']);
        session('userName', $user['username']);
        $this->assign('user', $user);
        $this->display();
    }


    public function info()
    {

        if (!session('reguserId')) {
            redirect('/#login');
        }

        $user = M('User')->where(array('id' => session('reguserId')))->find();


        if (!$user) {
            $this->error(L('Please register'));
        }
        if ($user['regaward'] == 0) {
            if (C('reg_award') == 1 && C('reg_award_num') > 0) {
                M('UserCoin')->where(array('userid' => session('reguserId')))->setInc(C('reg_award_coin'), C('reg_award_num'));
                M('User')->where(array('id' => session('reguserId')))->save(array('regaward' => 1));
            }
        }

        session('userId', $user['id']);
        session('userName', $user['username']);
        $this->assign('user', $user);
        $this->display();
    }


    public function chkUser($username)
    {
        if (!check($username, 'username')) {
            $this->error(L('INVALID_USERNAME'));
        }

        if (M('User')->where(array('username' => $username))->find()) {
            $this->error(L('Username already exists'));
        }

        $this->success('');
    }

    public function submit($username = "", $password = "", $cellphone = "",$cellphones = "", $verify = NULL)
    {
        if (C('login_verify')) {
            if (!check_verify($verify, $id = 1, $recap = 1)) {
                $this->error('Incorrect Captcha !');
            }
        }

        if (M_ONLY == 0) {
            //cellphone is holding value of username
            if (check($username, 'email')) {
                $user = M('User')->where(array('email' => $username))->find();
                $remark = L('Login Via Email');
            }

            if (!$user && check($username, 'cellphone')) {
                $user = M('User')->where(array('cellphone' => $username))->find();
                $remark = L('Login Via Mobile');
            }

            if (!$user ) {
                $user = M('User')->where(array('username' => $username))->find();
                $remark = L('Login with username');
            }
        } else {
            if (check($cellphone, 'cellphone')) {
                $user = M('User')->where(array('cellphone' => $cellphone,'cellphones' => $cellphones))->find();
                $remark = L('Login Via Mobile');
            }

            if (!$user) {
                $user = M('User')->where(array('username' => $username))->find();
                $remark = L('Login Via Username');
            }
			
			 if (!$user && check($username, 'email')) {
            
				 $user = M('User')->where(array('email' => $username))->find();
				 
                $remark = L('Login Via Email');
            }
        }

        if (!$user) {
            $this->error(L('USER_DOES_NOT_EXISTS'));
        }

        if (!check($password, 'password')) {
            $this->error(L('INVALID_PASSWORD'));
        }

        if (md5($password) != $user['password']) {
            $this->error(L('Password wrong!'));
        }

        if ($user['status'] != 1) {
            $this->error(L('Your account has been frozen, please contact the administrator!'));
        }
		$is_ga = $user['ga'] ? 1 : 0;
		//Google2FA IS ENABLED 
		if($is_ga==1){
		    session('uid', $user['id']);
            session('uname', $user['username']);
			session('invitecode', $user['invit']);
			session('remarks',$remark);
			$this->success('Enter 2FA!', U('enter2fa'));
		}
	
			
        $ip = get_client_ip();
        $logintime = time();
        $token_user = md5($user['id'] . $logintime);
        session('token_user', $token_user);

        $mo = M();
        $mo->execute('set autocommit=0');
        $mo->execute('lock tables codono_user write , codono_user_log write ');
        $rs = array();
        $rs[] = $mo->table('codono_user')->where(array('id' => $user['id']))->setInc('logins', 1);

        $rs[] = $mo->table('codono_user')->where(array('id' => $user['id']))->save(array('token' => $token_user));

        $rs[] = $mo->table('codono_user_log')->add(array('userid' => $user['id'], 'type' => L('log in'), 'remark' => $remark, 'addtime' => $logintime, 'addip' => $ip, 'addr' => get_city_ip(), 'status' => 1));
		$login_date_time=date('Y-m-d H:i',$logintime).'('.date_default_timezone_get().')';
		$subject ="Successful Login From IP ".$ip." - ".$login_date_time;
		$user_email=$user['email'];
		$user_name=$user['username'];
		
		$login_content="Hi $user_name,<br/> We have detetcted a new login <br/><br/><br/><table style='border:2px solid black;width:100%'><tr style='border:1px solid black;width:100%'><td>Email</td><td>$user_email</td></tr><tr style='border:1px solid black;width:100%'><td>IP</td><td>$ip</td></tr><tr style='border:1px solid black;width:100%'><td>Time</td><td>$login_date_time</td></tr></table><br/><br/>";
		
        if (is_array($rs)) {
            $mo->execute('commit');
            $mo->execute('unlock tables');

            if (!$user['invit']) {
                for (; true;) {
                    $tradeno = tradenoa();

                    if (!M('User')->where(array('invit' => $tradeno))->find()) {
                        break;
                    }
                }

                M('User')->where(array('id' => $user['id']))->setField('invit', $tradeno);
            }

            session('userId', $user['id']);
            session('userName', $user['username']);

            if (!$user['paypassword']) {
                session('regpaypassword', $rs[0]);
                session('reguserId', $user['id']);
            }

            if (!$user['truename']) {
                session('regtruename', $rs[0]);
                session('reguserId', $user['id']);
            }
            session('codono_already', 0);
		//	$notify=$this->addnotification($user_email,$subject,$login_content);
exec($mail_Sent=json_decode(tmail($user_email,$subject,$login_content)));

            $this->success(L('login successful!'));
   
        } else {
            session('codono_already', 0);

            $mo->execute('rollback');
            $this->error(L('LOGIN_FAILED'));
        }
    }
	public function enter2fa()
    {	
		if (session('userId') || session('uid')==0) {
            redirect('/');
        }
	$this->display('enter2fa');
	}
	public function check2fa(){
			$gacode = I('post.gacode', '', 'text');
			if (!$gacode) {
                    $this->error(L('INVALID_CODE'));
			}
			$userx['id']=$_SESSION['uid'];
			$userx['userName']=$_SESSION['uname'];
			$userx['invit']=$_SESSION['invitecode'];
			$userx['remarks']=$_SESSION['remarks'];
			$user = M('User')->where(array('id' => $userx['id']))->find();
			$arr = explode('|', $user['ga']);
			$secret = $arr[0];
			$ga = new \Common\Ext\GoogleAuthenticator();
			$ga_verification=$ga->verifyCode($secret, $gacode, 1);
            if ($ga_verification) {
				//check  ga_login too
                //$this->success(L('Successful operation'));
				//Keep doing the login process now 
				
            } else {
                $this->error(L('Verification failed'));
            }
		
		$ip = get_client_ip();
        $logintime = time();
        $token_user = md5($user['id'] . $logintime);
        session('token_user', $token_user);

        $mo = M();
        $mo->execute('set autocommit=0');
        $mo->execute('lock tables codono_user write , codono_user_log write ');
        $rs = array();
        $rs[] = $mo->table('codono_user')->where(array('id' => $user['id']))->setInc('logins', 1);

        $rs[] = $mo->table('codono_user')->where(array('id' => $user['id']))->save(array('token' => $token_user));

        $rs[] = $mo->table('codono_user_log')->add(array('userid' => $user['id'], 'type' => L('log in'), 'remark' => $userx['remarks'], 'addtime' => $logintime, 'addip' => $ip, 'addr' => get_city_ip(), 'status' => 1));

        //	if (check_arr($rs)) {
        if (is_array($rs)) {
            $mo->execute('commit');
            $mo->execute('unlock tables');

            if (!$user['invit']) {
                for (; true;) {
                    $tradeno = tradenoa();

                    if (!M('User')->where(array('invit' => $tradeno))->find()) {
                        break;
                    }
                }

                M('User')->where(array('id' => $user['id']))->setField('invit', $tradeno);
            }
            $remark=$user['id'].'_'.$user['invit'];
            session('userId', $user['id']);
            session('userName', $user['username']);
			session('invit', $user['invit']);
			session('remark',$remark);
            if (!$user['paypassword']) {
                session('regpaypassword', $rs[0]);
                session('reguserId', $user['id']);
            }

            if (!$user['truename']) {
                session('regtruename', $rs[0]);
                session('reguserId', $user['id']);
            }
            session('codono_already', 0);
            $this->success(L('login successful!'),'/');
        } else {
            session('codono_already', 0);

            $mo->execute('rollback');
            $this->error(L('LOGIN_FAILED'));
        }	
	}
    public function loginout()
    {
        session(null);
        redirect('/');
    }
	public function findpwd(){
		if (M_ONLY == 0) {
			$this->findpwdemail();
		}
		else{
			$this->findpwdcell();
		}
	}
    public function findpwdcell()
    {
        if (IS_POST) {
            $input = I('post.');
            if (M_ONLY == 0) {
                if (!check_verify($input['verify'])) {
                    $this->error('Incorrect Captcha !');
                }

                if (!check($input['username'], 'username')) {
                    $this->error(L('INVALID_USERNAME'));
                }

                if (!check($input['cellphone'], 'cellphone')) {
                    $this->error(L('INVALID_PHONE_FORMAT'));
                }

                if (!check($input['cellphone_verify'], 'd')) {
                    $this->error(L('INVALID_SMS_CODE'));
                }

                if ($input['cellphone_verify'] != session('findpwd_verify')) {
                    $this->error(L('INCORRECT_SMS_CODE'));
                }

                $user = M('User')->where(array('username' => $input['username']))->find();


                if (!$user) {
                    $this->error(L('Username does not exist!'));
                }

                if ($user['cellphone'] != $input['cellphone']) {
                    $this->error(L('User name or phone number wrong!'));
                }

                if (!check($input['password'], 'password')) {
                    $this->error(L('New password is malformed!'));
                }


                if ($input['password'] != $input['repassword']) {
                    $this->error(L('INCORRECT_NEW_PWD'));
                }


                $mo = M();
                $mo->execute('set autocommit=0');
                $mo->execute('lock tables codono_user write , codono_user_log write ');
                $rs = array();
                $rs[] = $mo->table('codono_user')->where(array('id' => $user['id']))->save(array('password' => md5($input['password'])));

                if (check_arr($rs)) {
                    $mo->execute('commit');
                    $mo->execute('unlock tables');
                    $this->success(L('Successfully modified'));
                } else {
                    $mo->execute('rollback');
                    $this->error('No changes were made!');
                }


            } else {


                if (!check($input['cellphone'], 'cellphone')) {
                    $this->error(L('INVALID_PHONE_FORMAT'));
                }

                $user = M('User')->where(array('cellphone' => $input['cellphone']))->find();

                if (!$user) {
                    $this->error(L('Phone number does not exist'));
                }

                if (!check($input['cellphone_verify'], 'd')) {
                    $this->error(L('INVALID_SMS_CODE'));
                }

                if ($input['cellphone_verify'] != session('findpwd_verify')) {
                    $this->error(L('INCORRECT_SMS_CODE'));
                }
                session("findpwdcellphone", $user['cellphone']);
                $this->success(L('Verification success'));
            }

        } else {
            $this->display();
        }
    }
	
	    public function findpwdemail()
		{
			
        if (IS_POST) {
            $input = I('post.');
            if (1 == 1) {
              
                if (!check($input['email'], 'email')) {
                    $this->error(L('INVALID_EMAIL_FORMAT'));
                }

                if (!check($input['email_verify'], 'd')) {
                    $this->error(L('INVALID_CODE'));
                }

                if ($input['emailverify'] != session('emailverify')) {
                    $this->error(L('INCORRECT_CODE'));
                }

                $user = M('User')->where(array('email' => $input['email']))->find();


                if (!$user) {
                    $this->error(L('Email does not exist!'));
                }

                if ($user['email'] != $input['email']) {
                    $this->error(L('Email is incorrect!'));
                }

                if (!check($input['password'], 'password')) {
                    $this->error(L('New password incorrect format!'));
                }


                if ($input['password'] != $input['repassword']) {
                    $this->error(L('INCORRECT_NEW_PWD'));
                }


                $mo = M();
                $mo->execute('set autocommit=0');
                $mo->execute('lock tables codono_user write , codono_user_log write ');
                $rs = array();
                $rs[] = $mo->table('codono_user')->where(array('id' => $user['id']))->save(array('password' => md5($input['password'])));

                if (check_arr($rs)) {
                    $mo->execute('commit');
                    $mo->execute('unlock tables');
                    $this->success(L('Successfully modified'));
                } else {
                    $mo->execute('rollback');
                    $this->error('No changes were made!');
                }


            } else {


                if (!check($input['email'], 'email')) {
                    $this->error(L('INVALID_EMAIL_FORMAT'));
                }

                $user = M('User')->where(array('email' => $input['email']))->find();

                if (!$user) {
                    $this->error(L('Email does not exist'));
                }

                if (!check($input['email_verify'], 'd')) {
                    $this->error(L('INVALID_CODE'));
                }

                if ($input['email_verify'] != session('findpwd_verify')) {
                    $this->error(L('INCORRECT_CODE'));
                }
                session("findpwdemail", $user['email']);
                $this->success(L('Verification success'));
            }

        } else {
            $this->display('findpwdemail');
        }
    }
	    public function findpwdemailconfirm()
    {

        if (empty(session('findpwdcellphone'))) {
            session(null);
            redirect('/');
        }

        $this->display();
    }

    public function findpwdconfirm()
    {

        if (empty(session('findpwdcellphone'))) {
            session(null);
            redirect('/');
        }

        $this->display();
    }

    public function password_up($password = "")
    {


        if (empty(session('findpwdcellphone'))) {
            $this->error(L('Please return with the first step!'));
        }

        if (!check($password, 'password')) {
            $this->error(L('New Password incorrect format!'));
        }
        $user = M('User')->where(array('cellphone' => session('findpwdcellphone')))->find();

        if (!$user) {
            $this->error(L('Phone number does not exist'));
        }

        if ($user['paypassword'] == md5($password)) {
            $this->error("Funding Password and Login Password can not be same");
        }


        $mo = M();
        $mo->execute('set autocommit=0');
        $mo->execute('lock tables codono_user write , codono_user_log write ');
        $rs = array();
        $rs[] = $mo->table('codono_user')->where(array('cellphone' => $user['cellphone']))->save(array('password' => md5($password)));

        if (check_arr($rs)) {
            $mo->execute('commit');
            $mo->execute('unlock tables');
            $this->success(L('Successful operation'));
        } else {
            $mo->execute('rollback');
            $this->error(L('Operation failed'));
        }

    }

    public function findpwdinfo()
    {

        if (empty(session('findpwdcellphone'))) {
            session(null);
            redirect('/');
        }
        session(null);
        $this->display();
    }


    public function findpaypwd()
    {
        if (IS_POST) {
            $input = I('post.');

            if (!check($input['username'], 'username')) {
                $this->error(L('INVALID_USERNAME'));
            }

            if (!check($input['cellphone'], 'cellphone')) {
                $this->error(L('INVALID_PHONE_FORMAT'));
            }

            if (!check($input['cellphone_verify'], 'd')) {
                $this->error(L('INVALID_SMS_CODE'));
            }

            if ($input['cellphone_verify'] != session('findpaypwd_verify')) {
                $this->error(L('INCORRECT_SMS_CODE'));
            }

            $user = M('User')->where(array('username' => $input['username']))->find();

            if (!$user) {
                $this->error(L('Username does not exist!'));
            }

            if ($user['cellphone'] != $input['cellphone']) {
                $this->error(L('Username or phone number wrong!'));
            }

            if (!check($input['password'], 'password')) {
                $this->error(L('New Fund Pwd format error!'));
            }

            if ($input['password'] != $input['repassword']) {
                $this->error(L('INCORRECT_NEW_PWD'));
            }

            $mo = M();
            $mo->execute('set autocommit=0');
            $mo->execute('lock tables codono_user write , codono_user_log write ');
            $rs = array();
            $rs[] = $mo->table('codono_user')->where(array('id' => $user['id']))->save(array('paypassword' => md5($input['password'])));

            if (check_arr($rs)) {
                $mo->execute('commit');
                $mo->execute('unlock tables');
                $this->success(L('Successfully modified'));
            } else {
                $mo->execute('rollback');
                $this->error('No changes were made!' . $mo->table('codono_user')->getLastSql());
            }
        } else {
            $this->display();
        }
    }

}

?>