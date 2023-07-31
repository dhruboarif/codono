<?php

namespace Home\Controller;

class PoolController extends HomeController
{
    public function __construct()
    {
        parent::__construct();
        $this->title = L('Market Trading');
        exit();
    }

    public function index()
    {
        if (!userid()) {
            redirect('/#login');
        }

        if (IS_POST) {
            $input = I('post.');

            if (!check($input['num'], 'd')) {
                $this->error(L('Quantity wrong format!'));
            }

            if ($input['num'] < 1) {
                $this->error(L('Quantity wrong!'));
            }

            if (!check($input['id'], 'd')) {
                $this->error(L('Mining machine type format error!'));
            }

            $user = $this->User(0, 0);

            if (!$user['id']) {
                $this->error(L('PLEASE_LOGIN'));
            }

            if (md5($input['paypassword']) != $user['paypassword']) {
                $this->error(L('Trading password is wrong!'));
            }

            $pool = M('Pool')->where(array('id' => $input['id']))->find();

            if (!$pool) {
                $this->error(L('Minerals wrong type!'));
            }

            if ($pool['status'] != 1) {
                $this->error(L('Minerals currently not open to buy!'));
            }

            $mum = round($pool['price'] * $input['num'], 6);

            if ($user['coin'][C('rmb_mr')] < $mum) {
                $this->error(L('Enough available balance of USD'));
            }

            $poolLog = M('PoolLog')->where(array('userid' => $user['id'], 'name' => $pool['name']))->sum('num');

            if ($pool['limit']) {
                if ($pool['limit'] < ($poolLog + $input['num'])) {
                    $this->error(L('The total purchase amount exceeds the limit!'));
                }
            }

            $mo = M();
            $mo->execute('set autocommit=0');
            $mo->execute('lock tables  codono_user write , codono_pool_log  write ,codono_user_coin write');
            $rs = array();
            $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $user['id']))->setDec(C('rmb_mr'), $mum);
            $rs[] = $mo->table('codono_pool_log')->add(array('userid' => $user['id'], 'coinname' => $pool['coinname'], 'name' => $pool['name'], 'ico' => $pool['ico'], 'price' => $pool['price'], 'num' => $input['num'], 'tian' => $pool['tian'], 'power' => $pool['power'], 'endtime' => time(), 'addtime' => time(), 'status' => 0));

            if (check_arr($rs)) {
                $mo->execute('commit');
                $mo->execute('unlock tables');
                $this->success(L('Buy success!'));
            } else {
                $mo->execute('rollback');
                $this->error(L('Failed purchase!'));
            }
        } else {
            $this->get_text();
            $list = M('Pool')->where(array('status' => 1))->select();
            $this->assign('list', $list);
            $this->display();
        }
    }

    public function log()
    {
        if (!userid()) {
            redirect('/#login');
        }

        $user = $this->User();
        $input = I('get.');
        $where['status'] = array('egt', 0);
        $where['userid'] = $user['id'];
        import('ORG.Util.Page');
        $Model = M('PoolLog');
        $count = $Model->where($where)->count();
        $Page = new \Think\Page($count, 15);
        $show = $Page->show();
        $list = $Model->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }

    public function startpool()
    {
        if (!userid()) {
            redirect('/#login');
        }

        if (IS_POST) {
            $input = I('post.');

            if (!check($input['id'], 'd')) {
                $this->error(L('Please select mining machine to work!'));
            }

            $poolLog = M('PoolLog')->where(array('id' => $input['id']))->find();

            if (!$poolLog) {
                $this->error(L('INCORRECT_REQ'));
            }

            if ($poolLog['status']) {
                $this->error(L('Access error!'));
            }

            $user = $this->User(0, 0);

            if (!$user['id']) {
                $this->error(L('PLEASE_LOGIN'));
            }

            if ($poolLog['userid'] != $user['id']) {
                $this->error(L('Unauthorized access'));
            }

            $mum = round($poolLog['price'] * $poolLog['num'], 6);
            $mo = M();
            $mo->execute('set autocommit=0');
            $mo->execute('lock tables codono_pool_log write');
            $rs = array();
            $rs[] = $mo->table('codono_pool_log')->where(array('id' => $poolLog['id']))->save(array('endtime' => time(), 'status' => 1));

            if (check_arr($rs)) {
                $mo->execute('commit');
                $mo->execute('unlock tables');
                $this->success(L('Mining machine has started to work!'));
            } else {
                $mo->execute('rollback');
                $this->error(APP_DEBUG ? implode('|', $rs) : L('Mining machine failed to work!'));
            }
        }
    }

    public function receiving()
    {
        if (!userid()) {
            redirect('/#login');
        }

        if (IS_POST) {
            $input = I('post.');

            if (!check($input['id'], 'd')) {
                $this->error(L('Please choose to receive ore mining machine!'));
            }

            $poolLog = M('PoolLog')->where(array('id' => $input['id']))->find();

            if (!$poolLog) {
                $this->error(L('INCORRECT_REQ'));
            }

            if ($poolLog['tian'] <= $poolLog['use']) {
                $this->error(L('Unauthorized access!'));
            }

            $tm = $poolLog['endtime'] + (60 * 60 * C('pool_jian'));

            if (time() < $tm) {
            }

            $user = $this->User(0, 0);

            if (!$user['id']) {
                $this->error(L('PLEASE_LOGIN'));
            }

            if ($poolLog['userid'] != $user['id']) {
                $this->error(L('Unauthorized access'));
            }

            $mo = M();
            $mo->execute('set autocommit=0');
            $mo->execute('lock tables codono_user_coin write ,  codono_pool_log  write ');
            $rs = array();
            $num = round($poolLog['num'] * C('pool_suan') * $poolLog['power'], 6);
            $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $user['id']))->setInc($poolLog['coinname'], $num);
            $rs[] = $mo->table('codono_pool_log')->where(array('id' => $poolLog['id']))->save(array('use' => $poolLog['use'] + 1, 'endtime' => time()));

            if ($poolLog['tian'] <= $poolLog['use'] + 1) {
                $rs[] = $mo->table('codono_pool_log')->where(array('id' => $poolLog['id']))->save(array('status' => 2));
            }

            if (check_arr($rs)) {
                $mo->execute('commit');
                $mo->execute('unlock tables');
                $this->success(L('Closed mine success! obtain') . $num . L('A currency'));
            } else {
                $mo->execute('rollback');
                $this->error(L('Failed to close the mine!'));
            }
        }
    }
}

?>