<?php

namespace Home\Controller;

class TopupController extends HomeController
{
    public function index()
    {
        if (empty($_POST)) {
            if (!userid()) {
                redirect('/#login');
            }

            $this->assign('prompt_text', D('Text')->get_content('game_topup'));
            $user_coin = M('UserCoin')->where(array('userid' => userid()))->find();
            $user_coin['usd'] = round($user_coin['usd'], 2);
            $this->assign('user_coin', $user_coin);
            $this->assign('topup_num', D('Topup')->get_type());
            $this->assign('topup_type', D('Topup')->get_coin());
            $where['userid'] = userid();
            $where['status'] = array('neq', -1);
            $count = M('Topup')->where($where)->count();
            $Page = new \Think\Page($count, 10);
            $show = $Page->show();
            $list = M('Topup')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

            foreach ($list as $k => $v) {
                $list[$k]['type'] = C('coin')[$v['type']]['title'];
            }

            $this->assign('list', $list);
            $this->assign('page', $show);
            $this->display();
        } else {
            $cellphone = $_POST['cellphone'];
            $num = $_POST['num'];
            $type = $_POST['type'];
            $paypassword = $_POST['paypassword'];

            if (!check($cellphone, 'cellphone')) {
                $this->error('Wrong format of phone number!');
            }

            if (!check($num, 'd')) {
                $this->error('Recharge amount malformed!');
            }

            if (!check($type, 'n')) {
                $this->error('Recharge way malformed!');
            }

            if (!check($paypassword, 'password')) {
                $this->error('Fund Pwd is malformed!');
            }

            if (!D('Topup')->get_type($num)) {
                $this->error('Recharge amount does not exist!');
            }

            $topup_type = D('Topup')->get_coin();

            if (!$topup_type[$type]) {
                $this->error('There is no way to recharge!');
            }

            if (!userid()) {
                $this->error('please log in first!');
            }

            $user = M('User')->where(array('id' => userid()))->find();

            if (!$user) {
                $this->error('User does not exist!');
            }

            if (!$user['status']) {
                session(null);
                $this->error('Users frozen!');
            }

            if ($user['paypassword'] != md5($paypassword)) {
                $this->error('Fund Pwd error!');
            }

            $mum = round($num / $topup_type[$type][1], 8);

            if ($mum < 0) {
                $this->error('Payment Amount error!');
            }

            $mo = M();
            $mo->execute('set autocommit=0');
            $mo->execute('lock tables  codono_user_coin write  , codono_topup write ');
            $rs = array();
            $user_coin = $mo->table('codono_user_coin')->where(array('userid' => userid()))->find();

            if (!$user_coin) {
                session(null);
                $this->error('User error property,please login again!');
            }

            if ($user_coin[$type] < $mum) {
                $this->error(L('Available') . $topup_type[$type][0] . 'Insufficient balance,Total to pay' . $mum . ' ' . $topup_type[$type][0]);
            }

            $rs[] = $mo->table('codono_user_coin')->where(array('userid' => userid()))->setDec($type, $mum);
            $rs[] = $topup_id = $mo->table('codono_topup')->add(array('userid' => userid(), 'cellphone' => $cellphone, 'num' => $num, 'type' => $type, 'mum' => $mum, 'addtime' => time(), 'status' => 0));

            if (C('topup_zidong')) {
                if (topup($cellphone, $num, md5($topup_id))) {
                    $rs[] = $mo->table('codono_topup')->where(array('id' => $topup_id))->save(array('endtime' => time(), 'status' => 1));
                }
            }

            if (check_arr($rs)) {
                $mo->execute('commit');
                $mo->execute('unlock tables');
                $this->success(L('SUCCESSFULLY_DONE'));
            } else {
                $mo->execute('rollback');
                $this->error('operation failed!');
            }
        }
    }

    public function uninstall()
    {

    }

}

