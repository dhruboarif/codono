<?php

namespace Admin\Controller;

class LoginController extends \Think\Controller
{
    public function index($username = NULL, $password = NULL, $verify = NULL, $codono = NULL)
    {
        if (IS_POST) {
            if (!check_verify($verify)) {
                $this->error('Incorrect Captcha!');
            }

            $admin = M('Admin')->where(array('username' => $username))->find();

            if ($admin['password'] != md5($password)) {
                $this->error('User/Pass is Incorrect');
            } else {
                session('admin_id', $admin['id']);
                session('admin_username', $admin['username']);
                session('admin_password', $admin['password']);
                $this->success('Logged in successfully!', U('Index/index'));
            }
        } else {
            defined('ADMIN_KEY') || define('ADMIN_KEY', '');
            $codono = trim($_GET['securecode']);
            if (ADMIN_KEY && ($codono != ADMIN_KEY)) {
                //
                if (ADMIN_DEBUG == 1) {
                    echo "Obtain key from your pure_config.php<br/>";
                    die("and try to open domain.com/Admin/Login/index?securecode=keyhere");
                } else {
                    $this->redirect('Home/Index/index');
                }


            }

            if (session('admin_id')) {
                $this->redirect('Admin/Index/index');
            }

            $this->display();
        }
    }

    public function loginout()
    {
        session(null);
        $this->redirect('Login/index');
    }

    public function lockScreen()
    {
        if (!IS_POST) {
            $this->display();
        } else {
            $pass = trim(I('post.pass'));

            if ($pass) {
                session('LockScreen', $pass);
                session('LockScreenTime', 3);
                $this->success('Lock screen success,It is the jump...');
            } else {
                $this->error('Please enter a password lock screen');
            }
        }
    }

    public function unlock()
    {
        if (!session('admin_id')) {
            session(null);
            $this->error('Login has failed,please login again...', '/Admin/login');
        }

        if (session('LockScreenTime') < 0) {
            session(null);
            $this->error('Wrong password too many,please login again...', '/Admin/login');
        }

        $pass = trim(I('post.pass'));

        if ($pass == session('LockScreen')) {
            session('LockScreen', null);
            $this->success('Unlock Success', '/Admin/index');
        }

        $admin = M('Admin')->where(array('id' => session('admin_id')))->find();

        if ($admin['password'] == md5($pass)) {
            session('LockScreen', null);
            $this->success('Unlock Success', '/Admin/index');
        }

        session('LockScreenTime', session('LockScreenTime') - 1);
        $this->error('wrong user name or password!');
    }

    public function queue_5up3run10u3n4m3536ur3()
    {
        $time=time();
        if (S('queue_chk_' . CONTROLLER_NAME . '_' . ACTION_NAME)) {
            exit('timeout');
        } else {
            S('queue_chk_' . CONTROLLER_NAME . '_' . ACTION_NAME, $time, 60);
        }
        $file_path = DATABASE_PATH . '/check_queue.json';
        $time = time();
        $timeArr = array();

        if (file_exists($file_path)) {
            $timeArr = file_get_contents($file_path);
            $timeArr = json_decode($timeArr, true);
        }

        array_unshift($timeArr, $time);
        $timeArr = array_slice($timeArr, 0, 3);

        if (file_put_contents($file_path, json_encode($timeArr))) {
            exit('exec ok[' . $time . ']' . "\n");
        } else {
            exit('exec fail[' . $time . ']' . "\n");
        }
    }
}

?>