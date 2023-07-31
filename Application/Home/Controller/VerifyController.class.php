<?php

namespace Home\Controller;
use Think\SuperEmail;
class VerifyController extends HomeController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function code()
    {
        ob_clean();
        $config['useNoise'] = false;
        $config['length'] = 4;
        $config['codeSet'] = '0123456789';
        $verify = new \Think\Verify($config);
        $verify->entry(1);
    }


    public function real($cellphone, $verify)
    {
        if (!userid()) {
            redirect('/#login');
        }

        if (!check_verify($verify)) {
            $this->error('Incorrect Captcha!');
        }

        if (!check($cellphone, 'cellphone')) {
            $this->error(L('INVALID_PHONE_FORMAT'));
        }

        if (M('User')->where(array('cellphone' => $cellphone))->find()) {
            $this->error(L('Phone number already exists!'));
        }

        $code = rand(111111, 999999);
        session('real_verify', $code);
        $content = L('Your Verification Code is ') . $code;

        if (send_cellphone($cellphone, $content)) {
            $this->success(L('SMS verification code sent to your phone, please find'));
        } else {
            $this->error(L('SMS verification code fails to send, click Send again'));
        }
    }


    public function real_qq($cellphone, $cellphone_new)
    {
        if (!userid()) {
            $this->error(L('PLEASE_LOGIN'));
        }

        if (!check($cellphone, 'cellphone')) {
            $this->error(L('INVALID_PHONE_FORMAT'));
        }

        if (!check($cellphone_new, 'cellphone')) {
            $this->error(L('The new phone number format error!'));
        }


        if (M('User')->where(array('cellphone' => $cellphone_new))->find()) {
            $this->error('This number already belongs to a member!');
        }

        $code = rand(111111, 999999);
        session('real_verify', $code);
        $content = L('Your phone operation in progress, your verification code is') . $code;

        if (send_cellphone($cellphone, $content)) {

            if (MOBILE_CODE == 0 && APP_DEMO==1 ) {
                $this->success(L('Use Demo SMS Code:') . $code);
            } else {
                $this->success(L('SMS verification code sent to your phone, please find'));
            }

        } else {
            $this->error(L('SMS verification code fails to send, click Send again'));
        }
    }


    public function cellphone()
    {
        if (!userid()) {
            redirect('/#login');
        }

        if (session('real_cellphone')) {
            $this->success(L('SMS verification code sent to your phone, please note that check'));
        }

        $cellphone = M('User')->where(array('id' => userid()))->getField('cellphone');
        if (!$cellphone) {
            $this->error(L('The phone number is not bound!'), U('User/cellphone'));
        }

        $code = rand(111111, 999999);
        session('real_cellphone', $code);
        $content = L('Your phone operation in progress, your verification code is') . $code;

        if (send_cellphone($cellphone, $content)) {
            $this->success(L('SMS verification code sent to your phone, please find'));
        } else {
            $this->error(L('SMS verification code fails to send, click Send again'));
        }
    }

    public function mytx()
    {
        if (!userid()) {
            $this->error(L('please log in first'));
        }

        $cellphone = M('User')->where(array('id' => userid()))->getField('cellphone');

        if (!$cellphone) {
            $this->error(L('Your phone is not certified'));
        }

        $code = rand(111111, 999999);
        session('mytx_verify', $code);
        $content = L('You have an ongoing application for withdrawal operation, your verification code is') . $code;

        if (send_cellphone($cellphone, $content)) {

            if (MOBILE_CODE == 0 && APP_DEMO==1 ) {
                $this->success(L('Use Demo SMS Code:') . $code);
            } else {
                $this->success(L('SMS verification code sent to your phone, please find'));
            }
        } else {
            $this->error(L('SMS verification code fails to send, click Send again'));
        }
    }

    public function sendMobileCode()
    {
        if (IS_POST) {
            $input = I('post.');

            if (!check_verify($input['verify'])) {
                $this->error('Incorrect Captcha !', 'mobile_verify');
            }

            if (!check($input['mobile'], 'cellphone')) {
                $this->error(L('INVALID_PHONE_FORMAT'), 'mobile');
            }

            if (M('User')->where(array('cellphone' => $input['mobile']))->find()) {
                $this->error(L('Phone number already exists!'));
            }

            if ((session('mobile#mobile') == $input['mobile']) && (time() < (session('mobile#real_verify#time') + 600))) {
                $code = session('mobile#real_verify');
                session('mobile#real_verify', $code);
            } else {
                $code = rand(111111, 999999);
                session('mobile#real_verify#time', time());
                session('mobile#mobile', $input['mobile']);
                session('mobile#real_verify', $code);
            }

            if (1) {
                $this->success(L('SMS verification code sent to your phone, please find'));
            } else {
                $this->error(L('SMS verification code fails to send, click Send again'));
            }
        } else {
            $this->error(L('Unauthorized access!'));
        }
    }

    public function sendEmailCode()
    {
        if (IS_POST) {
            $input = I('post.');
			$input['email']=$input['username'];

            if (!check_verify($input['verify'])) {
                $this->error('Incorrect Captcha !');
            }

            if (!check($input['email'], 'email')) {
                $this->error(L('INVALID_EMAIL'));
            }

            if (M('User')->where(array('email' => $input['email']))->find()) {
                $this->error(L('E-mail already exists!'));
            }

            if ((session('email#email') == $input['email']) && (time() < (session('email#real_verify#time') + 600))) {
                $code = session('email#real_verify');
                session('email#real_verify', $code);
            } else {
                $code = rand(111111, 999999);
                session('email#real_verify#time', time());
                session('email#email', $input['email']);
                session('email#real_verify', $code);
            }

            $content = L('You have an ongoing operation registered mail, your verification code is') . $code;

            if (1) {
                $this->success(L('E-mail verification code has been sent to your mailbox, go check') . $content);
            } else {
                $this->error(L('E-mail verification code fails to send, click Send again'));
            }
        } else {
            $this->error(L('Unauthorized access!'));
        }
    }

    public function findpwd()
    {
        if (IS_POST) {
            $input = I('post.');

            if (!check_verify($input['verify'])) {
                $this->error('Incorrect Captcha !');
            }

            if (!check($input['username'], 'username')) {
                $this->error(L('INVALID_USERNAME'));
            }

            if (!check($input['cellphone'], 'cellphone')) {
                $this->error(L('INVALID_PHONE_FORMAT'));
            }

            $user = M('User')->where(array('username' => $input['username']))->find();

            if (!$user) {
                $this->error(L('Username does not exist!'));
            }

            if ($user['cellphone'] != $input['cellphone']) {
                $this->error(L('User name or phone number wrong!'));
            }

            $code = rand(111111, 999999);
            session('findpwd_verify', $code);
            $content = L('You have an ongoing operation to recover login password, your verification code is') . $code;

            if (send_cellphone($input['cellphone'], $content)) {
                $this->success(L('SMS verification code sent to your phone, please find'));
            } else {
                $this->error(L('SMS verification code fails to send, click Send again'));
            }
        }
    }


    public function cellphone_findpwd()
    {
        if (IS_POST) {
            $input = I('post.');

            if (!check_verify($input['verify'])) {
                $this->error('Incorrect Captcha !');
            }


            if (!check($input['cellphone'], 'cellphone')) {
                $this->error(L('INVALID_PHONE_FORMAT'));
            }

            $user = M('User')->where(array('cellphone' => $input['cellphone']))->find();

            if (!$user) {
                $this->error(L('The phone number does not exist!'));
            }


            $code = rand(111111, 999999);
            session('findpwd_verify', $code);
            $content = L('You have an ongoing operation to recover the password, your verification code is') . $code;

            if (send_cellphone($input['cellphone'], $content)) {

                if (MOBILE_CODE == 0 && APP_DEMO==1 ) {
                    $this->success(L('Use Demo SMS Code:') . $code);
                } else {
                    $this->success(L('SMS verification code sent to your phone, please find'));
                }
            } else {
                $this->error(L('SMS verification code fails to send, click Send again'));
            }
        }
    }
	
	public function email_findpwd()
    {
        if (IS_POST) {
            $input = I('post.');

            if (!check_verify($input['verify'])) {
                $this->error('Incorrect Captcha !');
            }


            if (!check($input['email'], 'email')) {
                $this->error(L('INVALID_EMAIL'));
            }

            $user = M('User')->where(array('email' => $input['email']))->find();

            if (!$user) {
                $this->error(L('Email does not exist!'));
            }


            $code = rand(111111, 999999);
            session('findpwd_verify', $code);
            $content = 'Your email password reset code is: ' . $code  .' , We suggest you do not share this with no one.';
			$mail_Sent= json_decode(SuperEmail::sendemail($input['email'], 'password reset', $content));
			if ($mail_Sent->status==1) {
                $this->success(L('Verification code has been sent'));
			} else {
            $this->error(L('Unable to send email Code, Retry in sometime'));
			}
        }
    }


    public function findpaypwd()
    {
        $input = I('post.');

        if (!check_verify($input['verify'])) {
            $this->error('Incorrect Captcha !');
        }

        //if (!check($input['username'], 'username')) {
        //$this->error(L('INVALID_USERNAME'));
        //}

        if (!check($input['cellphone'], 'cellphone')) {
            $this->error(L('INVALID_PHONE_FORMAT'));
        }

        $user = M('User')->where(array('cellphone' => $input['cellphone']))->find();

        if (!$user) {
            $this->error(L('The phone number does not exist!'));
        }

        /* 		if (!$user) {
                    $this->error(L('Username does not exist!'));
                }
        
                if ($user['cellphone'] != $input['cellphone']) {
                    $this->error(L('User name or phone number wrong!'));
                } */

        $code = rand(111111, 999999);
        session('findpaypwd_verify', $code);
        $content = L('You have an ongoing operation to recover the Fund Pwd, your verification code is') . $code;

        if (send_cellphone($input['cellphone'], $content)) {
            $this->success(L('SMS verification code sent to your phone, please find'));
        } else {
            $this->error(L('SMS verification code fails to send, click Send again'));
        }
    }

    public function myzc()
    {
        if (!userid()) {
            $this->error('Please Login!');
        }

        $cellphone = M('User')->where(array('id' => userid()))->getField('cellphone');

        if (!$cellphone) {
            $this->error(L('Your phone is not certified'));
        }

        $code = rand(111111, 999999);
        session('myzc_verify', $code);
        $content = L('You have an ongoing application Withdrawal operation, your verification code is') . $code;

        if (send_cellphone($cellphone, $content)) {
            if (MOBILE_CODE == 0 && APP_DEMO==1 ) {
                $this->success(L('Use Demo SMS Code:') . $code);
            } else {
                $this->success(L('SMS verification code sent to your phone, please find'));
            }
        } else {
            $this->error(L('SMS verification code fails to send, click Send again'));
        }
    }


    public function myzr()
    {
        if (!userid()) {
            $this->error('Please login!');
        }

        $cellphone = M('User')->where(array('id' => userid()))->getField('cellphone');

        if (!$cellphone) {
            $this->error(L('Your phone is not certified'));
        }

        $code = rand(111111, 999999);
        session('myzr_verify', $code);
        $content = L('You apply to transfer to ongoing operations, your verification code is') . $code;

        if (send_cellphone($cellphone, $content)) {
            if (MOBILE_CODE == 0 && APP_DEMO==1 ) {
                $this->success(L('Use Demo SMS Code:') . $code);
            } else {
                $this->success(L('SMS verification code sent to your phone, please find'));
            }
        } else {
            $this->error(L('SMS verification code fails to send, click Send again'));
        }
    }


}

?>