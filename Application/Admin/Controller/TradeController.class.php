<?php

namespace Admin\Controller;

class TradeController extends AdminController
{
    public function index($field = NULL, $name = NULL, $market = NULL, $status = NULL, $type = 0)
    {
        //$this->checkUpdata();
        $where = array();

        if ($field && $name) {
            if ($field == 'username') {
                $where['userid'] = M('User')->where(array('username' => $name))->getField('id');
            } elseif($field == 'liq')  {
                $where['userid'] =0;
            } else {
                $where[$field] = $name;
            }
        }

        if ($market) {
            $where['market'] = $market;
        }

        if ($status) {
            $where['status'] = $status;
        }

        if ($status == 0 && $status != null) {
            $where['status'] = 0;
        }
        if ($type == 1 || $type == 2) {
            $where['type'] = $type;
        }


        $count = M('Trade')->where($where)->count();

        $codono_getSum = M('Trade')->where($where)->sum('mum');

        $Page = new \Think\Page($count, 15);
        $show = $Page->show();
        $list = M('Trade')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

        foreach ($list as $k => $v) {
            $list[$k]['username'] = M('User')->where(array('id' => $v['userid']))->getField('username');
        }
        $this->assign('list', $list);
        $this->assign('codono_count', $count);
        $this->assign('codono_getSum', $codono_getSum);
        $this->assign('page', $show);
        $this->display();
    }

    public function reject($id = NULL)
    {
        $rs = D('Trade')->reject($id);

        if ($rs[0]) {
            $this->success($rs[1]);
        } else {
            $this->error($rs[1]);
        }
    }

    public function log($field = NULL, $name = NULL, $market = NULL, $type = NULL)
    {
        $where = array();

        if ($field && $name) {
            if ($field == 'username') {
                $where['userid'] = M('User')->where(array('username' => $name))->getField('id');
            } else if ($field == 'peername') {
                $where['peerid'] = M('User')->where(array('username' => $name))->getField('id');
            } else {
                $where[$field] = $name;
            }
        }


        if ($type == 1 || $type == 2) {
            $where['type'] = $type;
        }


        if ($market) {
            $where['market'] = $market;
        }

        $count = M('TradeLog')->where($where)->count();
        $codono_getSum = M('TradeLog')->where($where)->sum('mum');
        $Page = new \Think\Page($count, 15);
        $show = $Page->show();
        $list = M('TradeLog')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

        foreach ($list as $k => $v) {
            $list[$k]['username'] = M('User')->where(array('id' => $v['userid']))->getField('username');
            $list[$k]['peername'] = M('User')->where(array('id' => $v['peerid']))->getField('username');
        }


        $this->assign('codono_count', $count);
        $this->assign('codono_getSum', $codono_getSum);

        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }

    public function chat($field = NULL, $name = NULL)
    {
        $where = array();

        if ($field && $name) {
            if ($field == 'username') {
                $where['userid'] = M('User')->where(array('username' => $name))->getField('id');
            } else {
                $where[$field] = $name;
            }
        }

        $count = M('Chat')->where($where)->count();
        $Page = new \Think\Page($count, 15);
        $show = $Page->show();
        $list = M('Chat')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

        foreach ($list as $k => $v) {
            $list[$k]['username'] = M('User')->where(array('id' => $v['userid']))->getField('username');
        }

        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }

    public function chatStatus($id = NULL, $type = NULL, $model = 'Chat')
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
                    $this->success(L('SUCCESSFULLY_DONE'));
                } else {
                    $this->error(L('OPERATION_FAILED'));
                }

                break;

            default:
                $this->error(L('OPERATION_FAILED'));
        }

        if (M($model)->where($where)->save($data)) {
            $this->success(L('SUCCESSFULLY_DONE'));
        } else {
            $this->error(L('OPERATION_FAILED'));
        }
    }

    public function comment($field = NULL, $name = NULL, $coinname = NULL)
    {
        $where = array();

        if ($field && $name) {
            if ($field == 'username') {
                $where['userid'] = M('User')->where(array('username' => $name))->getField('id');
            } else {
                $where[$field] = $name;
            }
        }

        if ($coinname) {
            $where['coinname'] = $coinname;
        }

        $count = M('CoinComment')->where($where)->count();
        $Page = new \Think\Page($count, 15);
        $show = $Page->show();
        $list = M('CoinComment')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

        foreach ($list as $k => $v) {
            $list[$k]['username'] = M('User')->where(array('id' => $v['userid']))->getField('username');
        }

        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }

    public function commentStatus($id = NULL, $type = NULL, $model = 'CoinComment')
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
                    $this->success(L('SUCCESSFULLY_DONE'));
                } else {
                    $this->error(L('OPERATION_FAILED'));
                }

                break;

            default:
                $this->error(L('OPERATION_FAILED'));
        }

        if (M($model)->where($where)->save($data)) {
            $this->success(L('SUCCESSFULLY_DONE'));
        } else {
            $this->error(L('OPERATION_FAILED'));
        }
    }

    public function market($field = NULL, $name = NULL)
    {
        $where = array();

        if ($field && $name) {
            if ($field == 'username') {
                $where['userid'] = M('User')->where(array('username' => $name))->getField('id');
            } else {
                $where[$field] = $name;
            }
        }

        $count = M('Market')->where($where)->count();
        $Page = new \Think\Page($count, 15);
        $show = $Page->show();
        $list = M('Market')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

        foreach ($list as $k => $v) {
            if ($v['begintrade']) {
                $begintrade_codono_var = substr($v['begintrade'], 0, 5);
            } else {
                $begintrade_codono_var = "00:00";
            }
            if ($v['endtrade']) {
                $endtrade_codono_var = substr($v['endtrade'], 0, 5);
            } else {
                $endtrade_codono_var = "23:59";
            }


            $list[$k]['tradetimecodono'] = $begintrade_codono_var . "-" . $endtrade_codono_var;
        }

        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }

    public function marketEdit($id = NULL)
    {


        $codono_getCoreConfig = codono_getCoreConfig();
        if (!$codono_getCoreConfig) {
            $this->error('Incorrect Core Config');
        }


        if (empty($_POST)) {
            if (empty($id)) {
                $this->data = array();

                $beginshi = "00";
                $beginfen = "00";
                $endshi = "23";
                $endfen = "59";

            } else {
                $market_codono = M('Market')->where(array('id' => $id))->find();
                $this->data = $market_codono;

                if ($market_codono['begintrade']) {
                    $beginshi = explode(":", $market_codono['begintrade'])[0];
                    $beginfen = explode(":", $market_codono['begintrade'])[1];
                } else {
                    $beginshi = "00";
                    $beginfen = "00";
                }

                if ($market_codono['endtrade']) {
                    $endshi = explode(":", $market_codono['endtrade'])[0];
                    $endfen = explode(":", $market_codono['endtrade'])[1];
                } else {
                    $endshi = "23";
                    $endfen = "59";
                }

            }

            $this->assign('codono_getCoreConfig', $codono_getCoreConfig['codono_indexcat']);
            $this->assign('beginshi', $beginshi);
            $this->assign('beginfen', $beginfen);
            $this->assign('endshi', $endshi);
            $this->assign('endfen', $endfen);
            $this->display();
        } else {
            if (APP_DEMO) {
                $this->error(L('SYSTEM_IN_DEMO_MODE'));
            }

            $round = array(0, 1, 2, 3, 4, 5, 6,7,8,9,10);

            if (!in_array($_POST['round'], $round)) {
                $this->error('Decimal format error!');
            }

            if ($_POST['id']) {
                $rs = M('Market')->save($_POST);
            } else {
                $_POST['name'] = $_POST['sellname'] . '_' . $_POST['buyname'];
                unset($_POST['buyname']);
                unset($_POST['sellname']);

                if (M('Market')->where(array('name' => $_POST['name']))->find()) {
                    $this->error('Market exists!');
                }

                $rs = M('Market')->add($_POST);
            }

            if ($rs) {
                $this->success(L('SUCCESSFULLY_DONE'));
            } else {

                $this->error(L('OPERATION_FAILED'));
            }
        }
    }

    public function marketStatus($id = NULL, $type = NULL, $model = 'Market')
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
                    $this->success(L('SUCCESSFULLY_DONE'));
                } else {
                    $this->error(L('OPERATION_FAILED'));
                }

                break;

            default:
                $this->error(L('OPERATION_FAILED'));
        }

        if (M($model)->where($where)->save($data)) {
            $this->success(L('SUCCESSFULLY_DONE'));
        } else {
            $this->error(L('OPERATION_FAILED'));
        }
    }

    public function invit($field = NULL, $name = NULL)
    {
        $where = array();

        if ($field && $name) {
            if ($field == 'username') {
                $where['userid'] = M('User')->where(array('username' => $name))->getField('id');
            } else {
                $where[$field] = $name;
            }
        }

        $count = M('Invit')->where($where)->count();
        $Page = new \Think\Page($count, 15);
        $show = $Page->show();
        $list = M('Invit')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

        foreach ($list as $k => $v) {
            $list[$k]['username'] = M('User')->where(array('id' => $v['userid']))->getField('username');
            $list[$k]['invit'] = M('User')->where(array('id' => $v['invit']))->getField('username');
        }

        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }

    public function checkUpdata()
    {
        if (!S(MODULE_NAME . CONTROLLER_NAME . 'checkUpdata')) {
            $list = M('Menu')->where(array(
                'url' => 'Trade/index',
                'pid' => array('neq', 0)
            ))->select();

            if ($list[1]) {
                M('Menu')->where(array('id' => $list[1]['id']))->delete();
            } else if (!$list) {
                M('Menu')->add(array('url' => 'Trade/index', 'title' => 'Trades', 'pid' => 5, 'sort' => 1, 'hide' => 0, 'group' => 'transaction', 'ico_name' => 'stats'));
            } else {
                M('Menu')->where(array(
                    'url' => 'Trade/index',
                    'pid' => array('neq', 0)
                ))->save(array('title' => 'Trades', 'pid' => 5, 'sort' => 1, 'hide' => 0, 'group' => 'transaction', 'ico_name' => 'stats'));
            }

            $list = M('Menu')->where(array(
                'url' => 'Trade/log',
                'pid' => array('neq', 0)
            ))->select();

            if ($list[1]) {
                M('Menu')->where(array('id' => $list[1]['id']))->delete();
            } else if (!$list) {
                M('Menu')->add(array('url' => 'Trade/log', 'title' => 'Transaction Record', 'pid' => 5, 'sort' => 2, 'hide' => 0, 'group' => 'transaction', 'ico_name' => 'stats'));
            } else {
                M('Menu')->where(array(
                    'url' => 'Trade/log',
                    'pid' => array('neq', 0)
                ))->save(array('title' => 'Transaction Record', 'pid' => 5, 'sort' => 2, 'hide' => 0, 'group' => 'transaction', 'ico_name' => 'stats'));
            }

            $list = M('Menu')->where(array(
                'url' => 'Trade/chat',
                'pid' => array('neq', 0)
            ))->select();

            if ($list[1]) {
                M('Menu')->where(array('id' => $list[1]['id']))->delete();
            } else if (!$list) {
                M('Menu')->add(array('url' => 'Trade/chat', 'title' => 'Trading Chat', 'pid' => 5, 'sort' => 3, 'hide' => 0, 'group' => 'transaction', 'ico_name' => 'stats'));
            } else {
                M('Menu')->where(array(
                    'url' => 'Trade/chat',
                    'pid' => array('neq', 0)
                ))->save(array('title' => 'Trading Chat', 'pid' => 5, 'sort' => 3, 'hide' => 0, 'group' => 'transaction', 'ico_name' => 'stats'));
            }

            $list = M('Menu')->where(array(
                'url' => 'Trade/comment',
                'pid' => array('neq', 0)
            ))->select();

            if ($list[1]) {
                M('Menu')->where(array('id' => $list[1]['id']))->delete();
            } else if (!$list) {
                M('Menu')->add(array('url' => 'Trade/comment', 'title' => 'Currency Comments', 'pid' => 5, 'sort' => 4, 'hide' => 0, 'group' => 'transaction', 'ico_name' => 'stats'));
            } else {
                M('Menu')->where(array(
                    'url' => 'Trade/comment',
                    'pid' => array('neq', 0)
                ))->save(array('title' => 'Currency Comments', 'pid' => 5, 'sort' => 4, 'hide' => 0, 'group' => 'transaction', 'ico_name' => 'stats'));
            }

            $list = M('Menu')->where(array(
                'url' => 'Trade/market',
                'pid' => array('neq', 0)
            ))->select();

            if ($list[1]) {
                M('Menu')->where(array('id' => $list[1]['id']))->delete();
            } else if (!$list) {
                M('Menu')->add(array('url' => 'Trade/market', 'title' => 'market place', 'pid' => 5, 'sort' => 5, 'hide' => 0, 'group' => 'transaction', 'ico_name' => 'stats'));
            } else {
                M('Menu')->where(array(
                    'url' => 'Trade/market',
                    'pid' => array('neq', 0)
                ))->save(array('title' => 'market place', 'pid' => 5, 'sort' => 5, 'hide' => 0, 'group' => 'transaction', 'ico_name' => 'stats'));
            }

            $list = M('Menu')->where(array(
                'url' => 'Trade/invit',
                'pid' => array('neq', 0)
            ))->select();

            if ($list[1]) {
                M('Menu')->where(array('id' => $list[1]['id']))->delete();
            } else if (!$list) {
                M('Menu')->add(array('url' => 'Trade/invit', 'title' => 'TRADING RECOMMENDATIONS', 'pid' => 5, 'sort' => 6, 'hide' => 0, 'group' => 'transaction', 'ico_name' => 'stats'));
            } else {
                M('Menu')->where(array(
                    'url' => 'Trade/invit',
                    'pid' => array('neq', 0)
                ))->save(array('title' => 'TRADING RECOMMENDATIONS', 'pid' => 5, 'sort' => 6, 'hide' => 0, 'group' => 'transaction', 'ico_name' => 'stats'));
            }

            if (M('Menu')->where(array('url' => 'Chat/index'))->delete()) {
                M('AuthRule')->where(array('status' => 1))->delete();
            }

            if (M('Menu')->where(array('url' => 'Tradelog/index'))->delete()) {
                M('AuthRule')->where(array('status' => 1))->delete();
            }

            S(MODULE_NAME . CONTROLLER_NAME . 'checkUpdata', 1);
        }
    }
}

?>