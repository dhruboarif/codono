<?php

namespace Admin\Controller;

class FinanceController extends AdminController
{
    public function index($field = NULL, $name = NULL)
    {
        //$this->checkUpdata();
        $where = array();

        if ($field && $name) {
            if ($field == 'username') {
                $where['userid'] = M('User')->where(array('username' => $name))->getField('id');
            } else {
                $where[$field] = $name;
            }
        }

        $count = M('Finance')->where($where)->count();
        $Page = new \Think\Page($count, 15);
        $show = $Page->show();
        $list = M('Finance')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $name_list = array('mycz' => 'Fiat Recharge', 'mytx' => 'Fiat Withdrawal', 'trade' => 'Trade', 'tradelog' => 'Successful Tx', 'issue' => 'ICO Subscriptions');
        $nameid_list = array('mycz' => U('Mycz/index'), 'mytx' => U('Mytx/index'), 'trade' => U('Trade/index'), 'tradelog' => U('Tradelog/index'), 'issue' => U('Issue/index'));

        foreach ($list as $k => $v) {
            $list[$k]['username'] = M('User')->where(array('id' => $v['userid']))->getField('username');
            $list[$k]['num_a'] = Num($v['num_a']);
            $list[$k]['num_b'] = Num($v['num_b']);
            $list[$k]['num'] = Num($v['num']);
            $list[$k]['fee'] = Num($v['fee']);
            $list[$k]['type'] = ($v['type'] == 1 ? 'income' : 'spend');
            $list[$k]['name'] = ($name_list[$v['name']] ? $name_list[$v['name']] : $v['name']);
            $list[$k]['nameid'] = ($name_list[$v['name']] ? $nameid_list[$v['name']] . '?id=' . $v['nameid'] : '');
            $list[$k]['mum_a'] = Num($v['mum_a']);
            $list[$k]['mum_b'] = Num($v['mum_b']);
            $list[$k]['mum'] = Num($v['mum']);
            $list[$k]['addtime'] = addtime($v['addtime']);
        }

        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }


    public function mycz($field = NULL, $name = NULL, $status = NULL)
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

        $count = M('Mycz')->where($where)->count();
        $Page = new \Think\Page($count, 15);
        $show = $Page->show();
        $list = M('Mycz')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

        foreach ($list as $k => $v) {
            $list[$k]['username'] = M('User')->where(array('id' => $v['userid']))->getField('username');
            $list[$k]['type'] = M('MyczType')->where(array('name' => $v['type']))->getField('title');
        }

        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }

    public function myczStatus($id = NULL, $type = NULL, $model = 'Mycz')
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
                $this->error('operation failed1!');
        }

        if (M($model)->where($where)->save($data)) {
            $this->success(L('SUCCESSFULLY_DONE'));
        } else {
            $this->error('operation failed2!');
        }
    }


    public function myzrConfirm()
    {
        $id = intval($_GET['id']);

        if (empty($id)) {
            $this->error('please chooseData to be operated!');
        }

        $myzr = M('Myzr')->where(array('id' => $id))->find();

        if (($myzr['status'] != 0)) {
            $this->error('Has been processed, prohibit the operation again!');
        }

        $mo = M();
        $mo->execute('set autocommit=0');
        $mo->execute('lock tables codono_user_coin write,codono_myzr write,codono_finance write,codono_invit write,codono_user write');
        $rs = array();

        $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $myzr['userid']))->setInc($myzr['coinname'], $myzr['num']);
        $rs[] = $mo->table('codono_myzr')->where(array('id' => $myzr['id']))->save(array('status' => 1, 'mum' => $myzr['num'], 'endtime' => time()));

        $cz_mes = "Treatment success";

        if (check_arr($rs)) {
            $mo->execute('commit');
            $mo->execute('unlock tables');
            $this->success($cz_mes);
        } else {
            $mo->execute('rollback');
            $this->error(L('OPERATION_FAILED'));
        }
    }


    public function myczConfirm()
    {
        $id = intval($_GET['id']);

        if (empty($id)) {
            $this->error('Choose correct deposit request!');
        }

        $mycz = M('Mycz')->where(array('id' => $id))->find();

        if (($mycz['status'] != 0) && ($mycz['status'] != 3) && ($mycz['status'] != 4)) {
            $this->error('Status of this payment does not allow it to be processed!');
        }

        $mo = M();
        $mo->execute('set autocommit=0');
        $mo->execute('lock tables codono_user_coin write,codono_mycz write,codono_finance write,codono_invit write,codono_user write');
        $rs = array();
        $finance = $mo->table('codono_finance')->where(array('userid' => $mycz['userid']))->order('id desc')->find();
        $finance_num_user_coin = $mo->table('codono_user_coin')->where(array('userid' => $mycz['userid']))->find();
        $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $mycz['userid']))->setInc('usd', $mycz['num']);
        $rs[] = $mo->table('codono_mycz')->where(array('id' => $mycz['id']))->save(array('status' => 2, 'mum' => $mycz['num'], 'endtime' => time()));
        $finance_mum_user_coin = $mo->table('codono_user_coin')->where(array('userid' => $mycz['userid']))->find();
        $finance_hash = md5($mycz['userid'] . $finance_num_user_coin['usd'] . $finance_num_user_coin['usdd'] . $mycz['num'] . $finance_mum_user_coin['usd'] . $finance_mum_user_coin['usdd'] . CODONOLIC . 'auth.codono.com');
        $finance_num = $finance_num_user_coin['usd'] + $finance_num_user_coin['usdd'];

        if ($finance['mum'] < $finance_num) {
            $finance_status = (1 < ($finance_num - $finance['mum']) ? 0 : 1);
        } else {
            $finance_status = (1 < ($finance['mum'] - $finance_num) ? 0 : 1);
        }

        $rs[] = $mo->table('codono_finance')->add(array('userid' => $mycz['userid'], 'coinname' => 'usd', 'num_a' => $finance_num_user_coin['usd'], 'num_b' => $finance_num_user_coin['usdd'], 'num' => $finance_num_user_coin['usd'] + $finance_num_user_coin['usdd'], 'fee' => $mycz['num'], 'type' => 1, 'name' => 'mycz', 'nameid' => $mycz['id'], 'remark' => 'Fiat Recharge-Artificial arrival', 'mum_a' => $finance_mum_user_coin['usd'], 'mum_b' => $finance_mum_user_coin['usdd'], 'mum' => $finance_mum_user_coin['usd'] + $finance_mum_user_coin['usdd'], 'move' => $finance_hash, 'addtime' => time(), 'status' => $finance_status));

        $cz_mes = "Success recharge[" . $mycz['num'] . "].";

        $cur_user_info = $mo->table('codono_user')->where(array('id' => $mycz['userid']))->find();
        //invit_1  invit_2  invit_3  WithmumPrevail  The amount is credited into account
        //Promotion commission, a promotion, a lifetime on commission    Recharge amount of offline bonus0.6%Three dividends.    generation0.3%      II0.2%      Three generations0.1%
        $cz_jiner = $mycz['num'];
        if ($cur_user_info['invit_1'] && $cur_user_info['invit_1'] > 0 && 1 == 2) {
            //The presence of a promoter
            $invit_1_jiner = round(($cz_jiner / 100) * 0.3, 6);

            if ($invit_1_jiner) {
                //deal withbeforeinformation
                $finance_1 = $mo->table('codono_finance')->where(array('userid' => $cur_user_info['invit_1']))->order('id desc')->find();
                $finance_num_user_coin_1 = $mo->table('codono_user_coin')->where(array('userid' => $cur_user_info['invit_1']))->find();

                //Startdeal with
                $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $cur_user_info['invit_1']))->setInc('usd', $invit_1_jiner);
                $rs[] = $mo->table('codono_invit')->add(array('userid' => $cur_user_info['invit_1'], 'invit' => $mycz['userid'], 'name' => 'usd', 'type' => 'Generation of recharge bonus', 'num' => $cz_jiner, 'mum' => $cz_jiner, 'fee' => $invit_1_jiner, 'addtime' => time(), 'status' => 1));

                //deal withRear
                $finance_mum_user_coin_1 = $mo->table('codono_user_coin')->where(array('userid' => $cur_user_info['invit_1']))->find();
                $finance_hash_1 = md5($cur_user_info['invit_1'] . $finance_num_user_coin_1['usd'] . $finance_num_user_coin_1['usdd'] . $invit_1_jiner . $finance_mum_user_coin_1['usd'] . $finance_mum_user_coin_1['usdd'] . CODONOLIC . 'auth.codono.com');
                $finance_num_1 = $finance_num_user_coin_1['usd'] + $finance_num_user_coin_1['usdd'];

                if ($finance_1['mum'] < $finance_num_1) {
                    $finance_status_1 = (1 < ($finance_num_1 - $finance_1['mum']) ? 0 : 1);
                } else {
                    $finance_status_1 = (1 < ($finance_1['mum'] - $finance_num_1) ? 0 : 1);
                }

                $rs[] = $mo->table('codono_finance')->add(array('userid' => $cur_user_info['invit_1'], 'coinname' => 'usd', 'num_a' => $finance_num_user_coin_1['usd'], 'num_b' => $finance_num_user_coin_1['usdd'], 'num' => $finance_num_user_coin_1['usd'] + $finance_num_user_coin_1['usdd'], 'fee' => $invit_1_jiner, 'type' => 1, 'name' => 'mycz', 'nameid' => $cur_user_info['invit_1'], 'remark' => 'Fiat Recharge-Generation of recharge bonus-RechargeID' . $mycz['userid'] . ',Order form' . $mycz['tradeno'] . ',Money' . $cz_jiner . ' ,reward' . $invit_1_jiner . ' ', 'mum_a' => $finance_mum_user_coin_1['usd'], 'mum_b' => $finance_mum_user_coin_1['usdd'], 'mum' => $finance_mum_user_coin_1['usd'] + $finance_mum_user_coin_1['usdd'], 'move' => $finance_hash_1, 'addtime' => time(), 'status' => $finance_status_1));

                //deal withEndTips
                $cz_mes = $cz_mes . "Generation Referral Rewards [" . $invit_1_jiner . "].";
            }


        }

        if ($cur_user_info['invit_2'] && $cur_user_info['invit_2'] > 0 && 1 == 2) {
            //The presence of two promoter
            $invit_2_jiner = round(($cz_jiner / 100) * 0.2, 6);
            if ($invit_2_jiner) {

                //deal withbeforeinformation
                $finance_2 = $mo->table('codono_finance')->where(array('userid' => $cur_user_info['invit_2']))->order('id desc')->find();
                $finance_num_user_coin_2 = $mo->table('codono_user_coin')->where(array('userid' => $cur_user_info['invit_2']))->find();

                //Startdeal with

                $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $cur_user_info['invit_2']))->setInc('usd', $invit_2_jiner);
                $rs[] = $mo->table('codono_invit')->add(array('userid' => $cur_user_info['invit_2'], 'invit' => $mycz['userid'], 'name' => 'usd', 'type' => 'II top award', 'num' => $cz_jiner, 'mum' => $cz_jiner, 'fee' => $invit_2_jiner, 'addtime' => time(), 'status' => 1));

                //deal withRear
                $finance_mum_user_coin_2 = $mo->table('codono_user_coin')->where(array('userid' => $cur_user_info['invit_2']))->find();
                $finance_hash_2 = md5($cur_user_info['invit_2'] . $finance_num_user_coin_2['usd'] . $finance_num_user_coin_2['usdd'] . $invit_2_jiner . $finance_mum_user_coin_2['usd'] . $finance_mum_user_coin_2['usdd'] . CODONOLIC . 'auth.codono.com');
                $finance_num_2 = $finance_num_user_coin_2['usd'] + $finance_num_user_coin_2['usdd'];

                if ($finance_2['mum'] < $finance_num_2) {
                    $finance_status_2 = (1 < ($finance_num_2 - $finance_2['mum']) ? 0 : 1);
                } else {
                    $finance_status_2 = (1 < ($finance_2['mum'] - $finance_num_2) ? 0 : 1);
                }

                $rs[] = $mo->table('codono_finance')->add(array('userid' => $cur_user_info['invit_2'], 'coinname' => 'usd', 'num_a' => $finance_num_user_coin_2['usd'], 'num_b' => $finance_num_user_coin_2['usdd'], 'num' => $finance_num_user_coin_2['usd'] + $finance_num_user_coin_2['usdd'], 'fee' => $invit_2_jiner, 'type' => 1, 'name' => 'mycz', 'nameid' => $cur_user_info['invit_2'], 'remark' => 'Fiat Recharge-II top award-RechargeID' . $mycz['userid'] . ',Order form' . $mycz['tradeno'] . ',Money' . $cz_jiner . ' usd,reward' . $invit_2_jiner . ' usd', 'mum_a' => $finance_mum_user_coin_2['usd'], 'mum_b' => $finance_mum_user_coin_2['usdd'], 'mum' => $finance_mum_user_coin_2['usd'] + $finance_mum_user_coin_2['usdd'], 'move' => $finance_hash_2, 'addtime' => time(), 'status' => $finance_status_2));

                //deal withEndTips

                $cz_mes = $cz_mes . "II Recommended awards [" . $invit_2_jiner . "] usd.";

            }

        }

        if ($cur_user_info['invit_3'] && $cur_user_info['invit_3'] > 0 && 1 == 2) {
            //The presence of three promoter
            $invit_3_jiner = round(($cz_jiner / 100) * 0.1, 6);
            if ($invit_3_jiner) {

                //deal withbeforeinformation
                $finance_3 = $mo->table('codono_finance')->where(array('userid' => $cur_user_info['invit_3']))->order('id desc')->find();
                $finance_num_user_coin_3 = $mo->table('codono_user_coin')->where(array('userid' => $cur_user_info['invit_3']))->find();

                //Startdeal with

                $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $cur_user_info['invit_3']))->setInc('usd', $invit_3_jiner);
                $rs[] = $mo->table('codono_invit')->add(array('userid' => $cur_user_info['invit_3'], 'invit' => $mycz['userid'], 'name' => 'usd', 'type' => 'Three generations of top awards', 'num' => $cz_jiner, 'mum' => $cz_jiner, 'fee' => $invit_3_jiner, 'addtime' => time(), 'status' => 1));

                //deal withRear
                $finance_mum_user_coin_3 = $mo->table('codono_user_coin')->where(array('userid' => $cur_user_info['invit_3']))->find();
                $finance_hash_3 = md5($cur_user_info['invit_3'] . $finance_num_user_coin_3['usd'] . $finance_num_user_coin_3['usdd'] . $invit_3_jiner . $finance_mum_user_coin_3['usd'] . $finance_mum_user_coin_3['usdd'] . CODONOLIC . 'auth.codono.com');
                $finance_num_3 = $finance_num_user_coin_3['usd'] + $finance_num_user_coin_3['usdd'];

                if ($finance_3['mum'] < $finance_num_3) {
                    $finance_status_3 = (1 < ($finance_num_3 - $finance_3['mum']) ? 0 : 1);
                } else {
                    $finance_status_3 = (1 < ($finance_3['mum'] - $finance_num_3) ? 0 : 1);
                }

                $rs[] = $mo->table('codono_finance')->add(array('userid' => $cur_user_info['invit_3'], 'coinname' => 'usd', 'num_a' => $finance_num_user_coin_3['usd'], 'num_b' => $finance_num_user_coin_3['usdd'], 'num' => $finance_num_user_coin_3['usd'] + $finance_num_user_coin_3['usdd'], 'fee' => $invit_3_jiner, 'type' => 1, 'name' => 'mycz', 'nameid' => $cur_user_info['invit_3'], 'remark' => 'Fiat Recharge-Three generations of top awards-RechargeID' . $mycz['userid'] . ',Order form' . $mycz['tradeno'] . ',Money' . $cz_jiner . ' USD,reward' . $invit_3_jiner . ' USD', 'mum_a' => $finance_mum_user_coin_3['usd'], 'mum_b' => $finance_mum_user_coin_3['usdd'], 'mum' => $finance_mum_user_coin_3['usd'] + $finance_mum_user_coin_3['usdd'], 'move' => $finance_hash_3, 'addtime' => time(), 'status' => $finance_status_3));

                //deal withEndTips
                $cz_mes = $cz_mes . "Three generations Referral Rewards[" . $invit_3_jiner . "] USD.";
            }

        }


		if (check_arr($rs)) {
            $mo->execute('commit');
            $mo->execute('unlock tables');
            $this->success($cz_mes);
        } else {
            $mo->execute('rollback');
            $this->error(L('OPERATION_FAILED'));
        }
    }
    public function myczType()
    {
        $where = array();
        $count = M('MyczType')->where($where)->count();
        $Page = new \Think\Page($count, 15);
        $show = $Page->show();
        $list = M('MyczType')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }
	public function fiatwithdrawal($id = NULL)
    {
		$id = intval($_GET['id']);
		$note="Please place memo and confirm!";	
        if (empty($id)) {
            $note="Select Correct Transaction!";
        }

        $mytx = M('Mytx')->where(array('id' => $id))->find();
		
        if (!$mytx) {
            $note="No such withdrawal request!";
        }


        if (($mytx['status'] == 1) && ($mytx['status'] == 2)) {
            $note="Withdrawal has been processed already!";
        }
		$username = M('User')->where(array('id' => $mytx['userid']))->getField('username');
		$kyc = M('User')->where(array('id' => $mytx['userid']))->getField('idcardauth');
		$this->assign('username', $username);
		$this->assign('kyc', $kyc);
		$this->assign('info', $mytx);
		$this->assign('note', $note);
        $this->display();
    }
	
	public function fiatwithdrawalConfirm()
    {
		$id = intval($_POST['id']);
		$memo= trim($_POST['memo']);
		
		$note="Please place memo and confirm!";	
        if (empty($id)) {
            $this->error("No such withdrawal request!");
        }
		if (!$memo) {
            $this->error("Please Enter Memo!");
        }

        $myzc = M('Mytx')->where(array('id' => $id))->find();
		$savetx=M('Mytx')->where(array('id' => $id))->save(array('memo'=>$memo,'status' => 1));
        if (!$savetx) {
             $this->error("Could not update memo and Status!");
        }
		else{
             $this->success("Memo is saved and status is updated!");
			 $this->redirect('Admin/Finance/mytx');
        }
    }

	public function manualwithdrawal($id = NULL)
    {
		$id = intval($_GET['id']);
		$note="Please place txid and confirm!";	
        if (empty($id)) {
            $note="please choose data to be operated!";
        }

        $myzc = M('Myzc')->where(array('id' => $id))->find();

        if (!$myzc) {
            $note="No such withdrawal request!";
        }


        if (($myzc['status'] != 0) && ($myzc['status'] != 3)) {
            $note="Withdrawal has been processed already!";
        }
		$username = M('User')->where(array('id' => $myzc['userid']))->getField('username');
		$this->assign('username', $username);
		$this->assign('info', $myzc);
		$this->assign('note', $note);
        $this->display();
    }
	public function manualwithdrawalConfirm()
    {
		$id = intval($_POST['id']);
		$txid = trim($_POST['txid']);
		
		$note="Please place txid and confirm!";	
        if (empty($id)) {
            $this->error("Please choose data to be operated!");
        }
		if (!$txid) {
            $this->error("Please Enter Txid !");
        }

        $myzc = M('Myzc')->where(array('id' => $id))->find();
		$savetx=M('Myzc')->where(array('id' => $id))->save(array('txid'=>$txid,'status' => 1));
        if (!$savetx) {
             $this->error("Could not update Txid and Status!");
        }
		else{
             $this->success("TXid is saved and status is updated!");
			 $this->redirect('Admin/Finance/myzc');
        }
    }

    public function myczTypeEdit($id = NULL)
    {
        if (empty($_POST)) {
            if ($id) {
                $this->data = M('MyczType')->where(array('id' => trim($id)))->find();
            } else {
                $this->data = null;
            }

            $this->display();
        } else {
            if (APP_DEMO) {
                $this->error(L('SYSTEM_IN_DEMO_MODE'));
            }

            if ($_POST['id']) {
                $rs = M('MyczType')->save($_POST);
            } else {
                $rs = M('MyczType')->add($_POST);
            }

            if ($rs) {
                $this->success(L('SUCCESSFULLY_DONE'));
            } else {
                $this->error(L('OPERATION_FAILED'));
            }
        }
    }

    public function myczTypeImage()
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

    public function myczTypeStatus($id = NULL, $type = NULL, $model = 'MyczType')
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
                $this->error('operation failed1!');
        }

        if (M($model)->where($where)->save($data)) {
            $this->success(L('SUCCESSFULLY_DONE'));
        } else {
            $this->error('operation failed2!');
        }
    }


    public function mytx($field = NULL, $name = NULL, $status = NULL)
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

        $count = M('Mytx')->where($where)->count();
        $Page = new \Think\Page($count, 15);
        $show = $Page->show();
        $list = M('Mytx')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

        foreach ($list as $k => $v) {
            $list[$k]['username'] = M('User')->where(array('id' => $v['userid']))->getField('username');
        }

        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }


    public function mytxStatus($id = NULL, $type = NULL, $model = 'Mytx')
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
                $this->error('operation failed1!');
        }

        if (M($model)->where($where)->save($data)) {
            $this->success(L('SUCCESSFULLY_DONE'));
        } else {
            $this->error('operation failed2!');
        }
    }

    public function mytxChuli()
    {
        $id = $_GET['id'];

        if (empty($id)) {
            $this->error('please choose data to be operated!');
        }

        if (M('Mytx')->where(array('id' => $id))->save(array('status' => 3))) {
            $this->success(L('SUCCESSFULLY_DONE'));
        } else {
            $this->error(L('OPERATION_FAILED'));
        }
    }

    public function mytxReject()
    {
        $id = $_GET['id'];

        if (empty($id)) {
            $this->error('please choose Data to be operated!');
        }

        $mytx = M('Mytx')->where(array('id' => trim($_GET['id'])))->find();
        $mo = M();
        $mo->execute('set autocommit=0');
        $mo->execute('lock tables codono_user_coin write,codono_mytx write,codono_finance write');
        $rs = array();
        $finance = $mo->table('codono_finance')->where(array('userid' => $mytx['userid']))->order('id desc')->find();
        $finance_num_user_coin = $mo->table('codono_user_coin')->where(array('userid' => $mytx['userid']))->find();
        $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $mytx['userid']))->setInc('usd', $mytx['num']);
        $rs[] = $mo->table('codono_mytx')->where(array('id' => $mytx['id']))->setField('status', 2);
        $finance_mum_user_coin = $mo->table('codono_user_coin')->where(array('userid' => $mytx['userid']))->find();
        $finance_hash = md5($mytx['userid'] . $finance_num_user_coin['usd'] . $finance_num_user_coin['usdd'] . $mytx['num'] . $finance_mum_user_coin['usd'] . $finance_mum_user_coin['usdd'] . CODONOLIC . 'auth.codono.com');
        $finance_num = $finance_num_user_coin['usd'] + $finance_num_user_coin['usdd'];

        if ($finance['mum'] < $finance_num) {
            $finance_status = (1 < ($finance_num - $finance['mum']) ? 0 : 1);
        } else {
            $finance_status = (1 < ($finance['mum'] - $finance_num) ? 0 : 1);
        }

        $rs[] = $mo->table('codono_finance')->add(array('userid' => $mytx['userid'], 'coinname' => 'usd', 'num_a' => $finance_num_user_coin['usd'], 'num_b' => $finance_num_user_coin['usdd'], 'num' => $finance_num_user_coin['usd'] + $finance_num_user_coin['usdd'], 'fee' => $mytx['num'], 'type' => 1, 'name' => 'mytx', 'nameid' => $mytx['id'], 'remark' => 'Fiat Withdrawal-Undo withdrawals', 'mum_a' => $finance_mum_user_coin['usd'], 'mum_b' => $finance_mum_user_coin['usdd'], 'mum' => $finance_mum_user_coin['usd'] + $finance_mum_user_coin['usdd'], 'move' => $finance_hash, 'addtime' => time(), 'status' => $finance_status));

        if (check_arr($rs)) {
            $mo->execute('commit');
            $mo->execute('unlock tables');
            $this->success(L('SUCCESSFULLY_DONE'));
        } else {
            $mo->execute('rollback');
            $this->error(L('OPERATION_FAILED'));
        }
    }

    public function mytxConfirm()
    {
        $id = $_GET['id'];

        if (empty($id)) {
            $this->error('please choose data to be operated!');
        }

        if (M('Mytx')->where(array('id' => $id))->save(array('status' => 1))) {
            $this->success(L('SUCCESSFULLY_DONE'));
        } else {
            $this->error(L('OPERATION_FAILED'));
        }
    }

    public function mytxExcel()
    {
        if (IS_POST) {
            $id = implode(',', $_POST['id']);
        } else {
            $id = $_GET['id'];
        }

        if (empty($id)) {
            $this->error('please choose data to be operated!');
        }

        $where['id'] = array('in', $id);
        $list = M('Mytx')->where($where)->select();

        foreach ($list as $k => $v) {
            $list[$k]['userid'] = M('User')->where(array('id' => $v['userid']))->getField('username');
            $list[$k]['addtime'] = addtime($v['addtime']);

            if ($list[$k]['status'] == 0) {
                $list[$k]['status'] = 'Untreated';
            } else if ($list[$k]['status'] == 1) {
                $list[$k]['status'] = 'Already transfer money';
            } else if ($list[$k]['status'] == 2) {
                $list[$k]['status'] = 'Revoked';
            } else {
                $list[$k]['status'] = 'error';
            }

            $list[$k]['bankcard'] = ' ' . $v['bankcard'] . ' ';
        }

        $zd = M('Mytx')->getDbFields();
        $xlsName = 'cade';
        $xls = array();

        foreach ($zd as $k => $v) {
            $xls[$k][0] = $v;
            $xls[$k][1] = $v;
        }

        $xls[0][2] = 'Numbering';
        $xls[1][2] = 'username';
        $xls[2][2] = 'Withdrawal Amount';
        $xls[3][2] = 'Fees';
        $xls[4][2] = 'Amount arrival';
        $xls[5][2] = 'Full name';
        $xls[6][2] = 'Bank Notes';
        $xls[7][2] = 'Bank name';
        $xls[8][2] = 'Opening provinces';
        $xls[9][2] = 'Cities account';
        $xls[10][2] = 'Opening address';
        $xls[11][2] = 'Bank card number';
        $xls[12][2] = ' ';
        $xls[13][2] = 'Withdraw Time';
        $xls[14][2] = 'Export Time';
        $xls[15][2] = 'Withdraw state';
        $this->exportExcel($xlsName, $xls, $list);
    }


    public function codono_financeExcel()
    {
        if (IS_POST) {
            $id = implode(',', $_POST['id']);
        } else {
            $id = intval($_GET['id']);
        }

        if (empty($id)) {
            $this->error('please choose data to be operated!');
        }

        $where['id'] = array('in', $id);
        $list = M('Finance')->where($where)->select();

        $name_list = array('mycz' => 'Fiat Recharge', 'mytx' => 'Fiat Withdrawal', 'trade' => 'Trade', 'tradelog' => 'Successful Tx', 'issue' => 'ICO Subscriptions');

        foreach ($list as $k => $v) {
            $list[$k]['userid'] = M('User')->where(array('id' => $v['userid']))->getField('username');
            $list[$k]['addtime'] = addtime($v['addtime']);

            $list[$k]['caozuoqian'] = "normal : " . $v['num_a'] . "freeze : " . $v['num_b'] . "total : " . $v['num'];
            $list[$k]['caozuohou'] = "normal : " . $v['mum_a'] . "freeze : " . $v['mum_b'] . "total : " . $v['mum'];

            $list[$k]['name'] = ($name_list[$v['name']] ? $name_list[$v['name']] : $v['name']);

            if ($list[$k]['type'] == 1) {
                $list[$k]['type'] = 'income';
            } else if ($list[$k]['type'] == 2) {
                $list[$k]['type'] = 'spend';
            }
            if ($list[$k]['status'] == 0) {
                $list[$k]['status'] = 'abnormal';
            } else if ($list[$k]['status'] == 1) {
                $list[$k]['status'] = 'normal';
            }


            unset($list[$k]['remark']);
            unset($list[$k]['nameid']);
            unset($list[$k]['move']);
            unset($list[$k]['num_a']);
            unset($list[$k]['mum_a']);
            unset($list[$k]['num_b']);
            unset($list[$k]['mum_b']);
            unset($list[$k]['num']);
            unset($list[$k]['mum']);

        }

        //$zd = M('Finance')->getDbFields();
        $xlsName = 'finance';
        $xls = array();

        $xls[0][0] = "id";
        $xls[0][2] = 'Numbering';
        $xls[1][0] = "userid";
        $xls[1][2] = 'username';
        $xls[2][0] = "coinname";
        $xls[2][2] = 'Currency operations';
        $xls[3][0] = "fee";
        $xls[3][2] = 'The number of operations';
        $xls[4][0] = "type";
        $xls[4][2] = 'Action Type';
        $xls[5][0] = "name";
        $xls[5][2] = 'Instructions';
        $xls[6][0] = "addtime";
        $xls[6][2] = 'Operating time';
        $xls[7][0] = "caozuoqian";
        $xls[7][2] = 'Before operation';
        $xls[8][0] = "caozuohou";
        $xls[8][2] = 'After the operation';
        $xls[9][0] = "status";
        $xls[9][2] = 'Status';
        $this->exportExcel($xlsName, $xls, $list);
    }


    public function codono_financeAllExcel()
    {
        $list = M('Finance')->select();

        $name_list = array('mycz' => 'Fiat Recharge', 'mytx' => 'Fiat Withdrawal', 'trade' => 'Trade', 'tradelog' => 'Successful Tx', 'issue' => 'ICO Subscriptions');

        foreach ($list as $k => $v) {
            $list[$k]['userid'] = M('User')->where(array('id' => $v['userid']))->getField('username');
            $list[$k]['addtime'] = addtime($v['addtime']);

            $list[$k]['caozuoqian'] = "normal : " . $v['num_a'] . "freeze : " . $v['num_b'] . "total : " . $v['num'];
            $list[$k]['caozuohou'] = "normal : " . $v['mum_a'] . "freeze : " . $v['mum_b'] . "total : " . $v['mum'];

            $list[$k]['name'] = ($name_list[$v['name']] ? $name_list[$v['name']] : $v['name']);

            if ($list[$k]['type'] == 1) {
                $list[$k]['type'] = 'income';
            } else if ($list[$k]['type'] == 2) {
                $list[$k]['type'] = 'spend';
            }
            if ($list[$k]['status'] == 0) {
                $list[$k]['status'] = 'abnormal';
            } else if ($list[$k]['status'] == 1) {
                $list[$k]['status'] = 'normal';
            }


            unset($list[$k]['remark']);
            unset($list[$k]['nameid']);
            unset($list[$k]['move']);
            unset($list[$k]['num_a']);
            unset($list[$k]['mum_a']);
            unset($list[$k]['num_b']);
            unset($list[$k]['mum_b']);
            unset($list[$k]['num']);
            unset($list[$k]['mum']);

        }

        //$zd = M('Finance')->getDbFields();
        $xlsName = 'finance';
        $xls = array();

        $xls[0][0] = "id";
        $xls[0][2] = 'Numbering';
        $xls[1][0] = "userid";
        $xls[1][2] = 'username';
        $xls[2][0] = "coinname";
        $xls[2][2] = 'Currency operations';
        $xls[3][0] = "fee";
        $xls[3][2] = 'The number of operations';
        $xls[4][0] = "type";
        $xls[4][2] = 'Action Type';
        $xls[5][0] = "name";
        $xls[5][2] = 'Instructions';
        $xls[6][0] = "addtime";
        $xls[6][2] = 'Operating time';
        $xls[7][0] = "caozuoqian";
        $xls[7][2] = 'Before operation';
        $xls[8][0] = "caozuohou";
        $xls[8][2] = 'After the operation';
        $xls[9][0] = "status";
        $xls[9][2] = 'Status';
        $this->exportExcel($xlsName, $xls, $list);
    }


    public function mytxConfig()
    {
        if (empty($_POST)) {
            $this->display();
        } else if (M('Config')->where(array('id' => 1))->save($_POST)) {
            $this->success('Changes Saved!');
        } else {
            $this->error('No changes were made!');
        }
    }

    public function myzr($field = NULL, $name = NULL, $coinname = NULL)
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

        $count = M('Myzr')->where($where)->count();
        $Page = new \Think\Page($count, 15);
        $show = $Page->show();
        $list = M('Myzr')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

        foreach ($list as $k => $v) {
            $list[$k]['usernamea'] = M('User')->where(array('id' => $v['userid']))->getField('username');
        }

        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }

    public function myzc($field = NULL, $name = NULL, $coinname = NULL)
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

        $count = M('Myzc')->where($where)->count();
        $Page = new \Think\Page($count, 15);
        $show = $Page->show();
        $list = M('Myzc')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

        foreach ($list as $k => $v) {
            $list[$k]['usernamea'] = M('User')->where(array('id' => $v['userid']))->getField('username');
        }

        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }

        public function myzcConfirm($id = NULL)
		{
        $id = intval($_GET['id']);
		if (APP_DEMO) {
            $this->error(L('SYSTEM_IN_DEMO_MODE'));
        }

        $myzc = M('Myzc')->where(array('id' => trim($id)))->find();

        if (!$myzc) {
            $this->error('Withdraw wrong!');
        }

        if ($myzc['status']) {
            $this->error('We have been treated!');
        }

        $username = M('User')->where(array('id' => $myzc['userid']))->getField('username');
		
        $coin = $myzc['coinname'];
        $dj_username = C('coin')[$coin]['dj_yh'];
        $dj_password = C('coin')[$coin]['dj_mm'];
        $dj_address = C('coin')[$coin]['dj_zj'];
        $dj_port = C('coin')[$coin]['dj_dk'];
		$dj_decimal = C('coin')[$coin]['cs_qk'];
		$main_address = C('coin')[$coin]['codono_coinaddress'];
        if (C('coin')[$coin]['type'] == 'blockio') {
		 $block_io = BlockIO($dj_username, $dj_password, $dj_address, $dj_port, 5, array(), 1);
         $json = $block_io->get_balance();
		
        if (!isset($json->status) || $json->status!='success') {
                            $this->error(L('Wallet link failure! blockio'));
		}
			
        $Coin = M('Coin')->where(array('name' => $myzc['coinname']))->find();
	
        $fee_user = M('UserCoin')->where(array($coin . 'b' => $Coin['zc_user']))->find();
        $user_coin = M('UserCoin')->where(array('userid' => $myzc['userid']))->find();
        $zhannei = M('UserCoin')->where(array($coin . 'b' => $myzc['username']))->find();
        $mo = M();
        $mo->execute('set autocommit=0');
        $mo->execute('lock tables  codono_user_coin write  , codono_myzc write  , codono_myzr write , codono_myzc_fee write');
        $rs = array();

        if ($zhannei) {
            $rs[] = $mo->table('codono_myzr')->add(array('userid' => $zhannei['userid'], 'username' => $myzc['username'], 'coinname' => $coin, 'txid' => md5($myzc['username'] . $user_coin[$coin . 'b'] . time()), 'num' => $myzc['num'], 'fee' => $myzc['fee'], 'mum' => $myzc['mum'], 'addtime' => time(), 'status' => 1));
            $rs[] = $r = $mo->table('codono_user_coin')->where(array('userid' => $zhannei['userid']))->setInc($coin, $myzc['mum']);
        }

        if (!$fee_user['userid']) {
            $fee_user['userid'] = 0;
        }

        if (0 < $myzc['fee']) {
            $rs[] = $mo->table('codono_myzc_fee')->add(array('userid' => $fee_user['userid'], 'username' => $Coin['zc_user'], 'coinname' => $coin, 'num' => $myzc['num'], 'fee' => $myzc['fee'], 'mum' => $myzc['mum'], 'type' => 2, 'addtime' => time(), 'status' => 1));

            if ($mo->table('codono_user_coin')->where(array($coin . 'b' => $Coin['zc_user']))->find()) {
                //$rs[] = $mo->table('codono_user_coin')->where(array($coin . 'b' => $Coin['zc_user']))->setInc($coin, $myzc['fee']);
                debug(array('lastsql' => $mo->table('codono_user_coin')->getLastSql()), 'Additional costs');
            } else {
                $rs[] = $mo->table('codono_user_coin')->add(array($coin . 'b' => $Coin['zc_user'], $coin => $myzc['fee']));
            }
        }

        $rs[] = M('Myzc')->where(array('id' => trim($id)))->save(array('status' => 1));

        if (check_arr($rs)) {
            $mo->execute('commit');
            $mo->execute('unlock tables');
			$valid_res = $block_io->validateaddress($myzc['username']);
			  if (!$valid_res) {
                    $this->error($myzc['username'] . L(' It is not a valid address wallet!'));
                }

                
                if ($json->data->available_balance < (double)$myzc['mum']) {
                    $this->error(L('Wallet balance of less than'));
                }
			
			$send_amt= round($myzc['mum'], 8);
			$sendrs = $block_io->withdraw(array('amounts' => $send_amt, 'to_addresses' => $myzc['username']));
            //$sendrs = $CoinClient->sendtoaddress($myzc['username'], (double)$myzc['mum']);

              if ($sendrs) {
                            if (isset($sendrs->status) && ($sendrs->status == 'success')) {
                                $flag = 1;
                            }
                        } else {
                            $flag = 0;
                        }

            if (!$flag) {
                $mo->execute('rollback');
                $mo->execute('unlock tables');
                $this->error('wallet server Withdraw currency failure!');
            } else {
                $this->success('Transfer success!');
            }
        } else {
            $mo->execute('rollback');
            $mo->execute('unlock tables');
            $this->error('Roll-out failure!' . implode('|', $rs) . $myzc['fee']);
        }
			
		}
		if (C('coin')[$coin]['type'] == 'eth') {
		    
		$heyue = $dj_username; //Contract Address
		$dj_password = cryptString(C('coin')[$coin]['dj_mm'],'d');
		
		$EthCommon = new \Org\Util\EthCommon($dj_address, $dj_port, "2.0");
        $EthPayLocal = new \Org\Util\EthPayLocal($dj_address, $dj_port, "2.0", $heyue,$dj_password);
        $ContractAddress=$heyue;
		$master=EthMaster($dj_address, $dj_port,$main_address,$dj_password, $ContractAddress);
		
        $Coin = M('Coin')->where(array('name' => $myzc['coinname']))->find();
        $fee_user = M('UserCoin')->where(array($coin . 'b' => $Coin['zc_user']))->find();
        $user_coin = M('UserCoin')->where(array('userid' => $myzc['userid']))->find();
        $zhannei = M('UserCoin')->where(array($coin . 'b' => $myzc['username']))->find();
        $mo = M();
        $mo->execute('set autocommit=0');
        $mo->execute('lock tables  codono_user_coin write  , codono_myzc write  , codono_myzr write , codono_myzc_fee write');
        $rs = array();

        if ($zhannei) {
            $rs[] = $mo->table('codono_myzr')->add(array('userid' => $zhannei['userid'], 'username' => $myzc['username'], 'coinname' => $coin, 'txid' => md5($myzc['username'] . $user_coin[$coin . 'b'] . time()), 'num' => $myzc['num'], 'fee' => $myzc['fee'], 'mum' => $myzc['mum'], 'addtime' => time(), 'status' => 1));
            $rs[] = $r = $mo->table('codono_user_coin')->where(array('userid' => $zhannei['userid']))->setInc($coin, $myzc['mum']);
        }

        if (!$fee_user['userid']) {
            $fee_user['userid'] = 0;
        }

        if (0 < $myzc['fee']) {
            $rs[] = $mo->table('codono_myzc_fee')->add(array('userid' => $fee_user['userid'], 'username' => $Coin['zc_user'], 'coinname' => $coin, 'num' => $myzc['num'], 'fee' => $myzc['fee'], 'mum' => $myzc['mum'], 'type' => 2, 'addtime' => time(), 'status' => 1));

            if ($mo->table('codono_user_coin')->where(array($coin . 'b' => $Coin['zc_user']))->find()) {
                //$rs[] = $mo->table('codono_user_coin')->where(array($coin . 'b' => $Coin['zc_user']))->setInc($coin, $myzc['fee']);
                debug(array('lastsql' => $mo->table('codono_user_coin')->getLastSql()), 'Additional costs');
            } else {
                $rs[] = $mo->table('codono_user_coin')->add(array($coin . 'b' => $Coin['zc_user'], $coin => $myzc['fee']));
            }
        }

        $rs[] = M('Myzc')->where(array('id' => trim($id)))->save(array('status' => 1));

        if (check_arr($rs)) {
            $mo->execute('commit');
            $mo->execute('unlock tables');
            //$sendrs = $CoinClient->sendtoaddress($myzc['username'], (double)$myzc['mum']);
			if ($heyue) {
            
                        //Contract Address transfer out
						$zhuan['fromaddress'] = $main_address;
                        $zhuan['toaddress'] = strtolower($myzc['username']);
                        $zhuan['token'] = $heyue;
                        $zhuan['type'] = $coin;
                        $zhuan['amount'] = (double)$myzc['mum'];
						$zhuan['password'] =$dj_password;
						
                        //$sendrs = $EthPayLocal->eth_ercsendTransaction($zhuan);
						$sendrs=$master->transferToken($zhuan['toaddress'], $zhuan['amount'], $zhuan['token'],$dj_decimal);
                    } else {
                        //eth
		
		
						$zhuan['fromaddress'] = $main_address;
                        $zhuan['toaddress'] = strtolower($myzc['username']);
                        $zhuan['amount'] = (double)$myzc['mum'];
						$zhuan['password'] =$dj_password;
                        $sendrs=$master->transferFromCoinbase($zhuan['toaddress'],(double)$myzc['mum']);
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
                $mo->execute('rollback');
                $mo->execute('unlock tables');
                $this->error('wallet server Withdraw currency failure!');
            } else {
				M('Myzc')->where(array('id' => $id))->save(array('txid'=>$sendrs));
                if ($hash) M()->execute("UPDATE `codono_myzc` SET  `hash` =  '$hash' WHERE id = '$id' ");
                $this->success('Transfer success!');
            }
        } else {
            $mo->execute('rollback');
            $mo->execute('unlock tables');
            $this->error('Roll-out failure!' . implode('|', $rs) . $myzc['fee']);
        }
	}
	              //CoinPayments  Ends
	if (C('coin')[$coin]['type'] == 'coinpay') {
		    
		$heyue = $dj_username; //Contract Address
		$dj_password = cryptString(C('coin')[$coin]['dj_mm'],'d');
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
                 if(strlen($myzc['username'])>8){
					 $valid_res =1;
				 }else{
					 $valid_res =0;
				 }

                if (!$valid_res) {
                    $this->error($myzc['username'] . L(' It is not a valid address wallet!'));
                }

                $balances = $cps_api->GetAllCoinBalances();
				
                if ($balances['result'][$coinpay_coin]['balancef'] <  $myzc['num']) {
                    //$this->error(L('Can not be withdrawn due to system'));
					debug($coin.' Balance is lower than  '. $myzc['num'].' at time:'.time() .'<br/>');	
					$can_withdraw=0;
                }
		
        $Coin = M('Coin')->where(array('name' => $myzc['coinname']))->find();
        $fee_user = M('UserCoin')->where(array($coin . 'b' => $Coin['zc_user']))->find();
        $user_coin = M('UserCoin')->where(array('userid' => $myzc['userid']))->find();
        $zhannei = M('UserCoin')->where(array($coin . 'b' => $myzc['username']))->find();
        $mo = M();
        $mo->execute('set autocommit=0');
        $mo->execute('lock tables  codono_user_coin write  , codono_myzc write  , codono_myzr write , codono_myzc_fee write');
        $rs = array();

        if ($zhannei) {
            $rs[] = $mo->table('codono_myzr')->add(array('userid' => $zhannei['userid'], 'username' => $myzc['username'], 'coinname' => $coin, 'txid' => md5($myzc['username'] . $user_coin[$coin . 'b'] . time()), 'num' => $myzc['num'], 'fee' => $myzc['fee'], 'mum' => $myzc['mum'], 'addtime' => time(), 'status' => 1));
            $rs[] = $r = $mo->table('codono_user_coin')->where(array('userid' => $zhannei['userid']))->setInc($coin, $myzc['mum']);
        }

        if (!$fee_user['userid']) {
            $fee_user['userid'] = 0;
        }

        if (0 < $myzc['fee']) {
            $rs[] = $mo->table('codono_myzc_fee')->add(array('userid' => $fee_user['userid'], 'username' => $Coin['zc_user'], 'coinname' => $coin, 'num' => $myzc['num'], 'fee' => $myzc['fee'], 'mum' => $myzc['mum'], 'type' => 2, 'addtime' => time(), 'status' => 1));

            if ($mo->table('codono_user_coin')->where(array($coin . 'b' => $Coin['zc_user']))->find()) {
                //$rs[] = $mo->table('codono_user_coin')->where(array($coin . 'b' => $Coin['zc_user']))->setInc($coin, $myzc['fee']);
                debug(array('lastsql' => $mo->table('codono_user_coin')->getLastSql()), 'Additional costs');
            } else {
                $rs[] = $mo->table('codono_user_coin')->add(array($coin . 'b' => $Coin['zc_user'], $coin => $myzc['fee']));
            }
        }

        $rs[] = M('Myzc')->where(array('id' => trim($id)))->save(array('status' => 1));

        if (check_arr($rs)) {
            $mo->execute('commit');
            $mo->execute('unlock tables');
            //$sendrs = $CoinClient->sendtoaddress($myzc['username'], (double)$myzc['mum']);
			$buyer_email = M('User')->where(array('id' => $myzc['userid']))->getField('email');
			$dest_tag=$myzc['dest_tag'];
			$withdrawals = ['amount' =>(double)$myzc['mum'],
						'add_tx_fee' => 0,
						'auto_confirm'=>1, //Auto confirm 1 or 0
						'currency' => $coinpay_coin,
						'address' => $myzc['username'],
						'ipn_url' => SITE_URL.'/IPN/confirm',
						'note'=>$buyer_email];
						if($dest_tag!=0 && $dest_tag!=NULL){
						$withdrawals['dest_tag']=$dest_tag;
						}

						$the_withdrawal = $cps_api->CreateWithdrawal($withdrawals);
						
						
                        if ($the_withdrawal["error"] != "ok") {
							$the_status=false;
                $mo->execute('rollback');
                $mo->execute('unlock tables');
                           $this->error('Your withdrawal could not be done !'.$the_withdrawal["error"]);
						   
                        } else {
							$the_status=true;
							$cp_withdrawal_id=$the_withdrawal["result"]["id"] ;
							M('Myzc')->where(array('id' => $id))->save(array('hash' => $cp_withdrawal_id));	
                            $this->success('Successful Withdrawal!');
                        }
        } else {
            $mo->execute('rollback');
            $mo->execute('unlock tables');
            $this->error('Roll-out failure!' . implode('|', $rs) . $myzc['fee']);
        }
	}
		if (C('coin')[$coin]['type'] == 'cryptonote') {
		$cryptonote = CryptoNote($dj_address, $dj_port);
					$open_wallet = $cryptonote->open_wallet($dj_username,$dj_password);
					
					$json=json_decode($cryptonote->get_height());
                    if (!isset($json->height) || $json->error!=0) {
						$status=1;
                        $this->error('CryptoNote Connection Failed ');
                    }

        $Coin = M('Coin')->where(array('name' => $myzc['coinname']))->find();
	
        $fee_user = M('UserCoin')->where(array($coin . 'b' => $Coin['zc_user']))->find();
        $user_coin = M('UserCoin')->where(array('userid' => $myzc['userid']))->find();
        $zhannei = M('UserCoin')->where(array($coin . 'b' => $myzc['username']))->find();
        $mo = M();
        $mo->execute('set autocommit=0');
        $mo->execute('lock tables  codono_user_coin write  , codono_myzc write  , codono_myzr write , codono_myzc_fee write');
        $rs = array();

        if ($zhannei) {
            $rs[] = $mo->table('codono_myzr')->add(array('userid' => $zhannei['userid'], 'username' => $myzc['username'], 'coinname' => $coin, 'txid' => md5($myzc['username'] . $user_coin[$coin . 'b'] . time()), 'num' => $myzc['num'], 'fee' => $myzc['fee'], 'mum' => $myzc['mum'], 'addtime' => time(), 'status' => 1));
            $rs[] = $r = $mo->table('codono_user_coin')->where(array('userid' => $zhannei['userid']))->setInc($coin, $myzc['mum']);
        }

        if (!$fee_user['userid']) {
            $fee_user['userid'] = 0;
        }

        if (0 < $myzc['fee']) {
            $rs[] = $mo->table('codono_myzc_fee')->add(array('userid' => $fee_user['userid'], 'username' => $Coin['zc_user'], 'coinname' => $coin, 'num' => $myzc['num'], 'fee' => $myzc['fee'], 'mum' => $myzc['mum'], 'type' => 2, 'addtime' => time(), 'status' => 1));

            if ($mo->table('codono_user_coin')->where(array($coin . 'b' => $Coin['zc_user']))->find()) {
                //$rs[] = $mo->table('codono_user_coin')->where(array($coin . 'b' => $Coin['zc_user']))->setInc($coin, $myzc['fee']);
                debug(array('lastsql' => $mo->table('codono_user_coin')->getLastSql()), 'Additional costs');
            } else {
                $rs[] = $mo->table('codono_user_coin')->add(array($coin . 'b' => $Coin['zc_user'], $coin => $myzc['fee']));
            }
        }

        $rs[] = M('Myzc')->where(array('id' => trim($id)))->save(array('status' => 1));

        if (check_arr($rs)) {
            $mo->execute('commit');
            $mo->execute('unlock tables');
			$bal_info = json_decode($cryptonote->getBalance());
			$crypto_balance=$cryptonote->deAmount($bal_info->unlocked_balance);    
                if ($crypto_balance < (double)$myzc['mum']) {
                    $this->error('Wallet balance is '.$crypto_balance.' of less than required '.$myzc['mum']);
                }
			
			$send_amt= round($myzc['mum'], 8);
			$sendrs = json_decode($cryptonote->transfer($send_amt, $myzc['username']));        

              if ($sendrs->error==0) {
                            if (isset($sendrs->tx_hash) && isset($sendrs->tx_key)) {
                                $flag = 1;
                            }
                        } else {
                            $flag = 0;
                        }

            if (!$flag) {
                $mo->execute('rollback');
                $mo->execute('unlock tables');
                $this->error('wallet server Withdraw currency failure!');
            } else {
                $this->success('Transfer success!');
            }
        } else {
            $mo->execute('rollback');
            $mo->execute('unlock tables');
            $this->error('Roll-out failure!' . implode('|', $rs) . $myzc['fee']);
        }
			
		}
		if (C('coin')[$coin]['type'] == 'waves') {
		$waves = WavesClient($dj_username, $dj_password, $dj_address, $dj_port,$dj_decimal, 5, array(), 1);
            $json = json_decode($waves->status(),true);
			$coinpay_coin=strtoupper($coin);
			

            if (!isset($json['blockchainHeight']) || $json['blockchainHeight']<=0) {
            	$this->error($coin.'Wallet link failure!');
            }

			
        $Coin = M('Coin')->where(array('name' => $myzc['coinname']))->find();
	
        $fee_user = M('UserCoin')->where(array($coin . 'b' => $Coin['zc_user']))->find();
        $user_coin = M('UserCoin')->where(array('userid' => $myzc['userid']))->find();
        $zhannei = M('UserCoin')->where(array($coin . 'b' => $myzc['username']))->find();
        $mo = M();
        $mo->execute('set autocommit=0');
        $mo->execute('lock tables  codono_user_coin write  , codono_myzc write  , codono_myzr write , codono_myzc_fee write');
        $rs = array();

        if ($zhannei) {
            $rs[] = $mo->table('codono_myzr')->add(array('userid' => $zhannei['userid'], 'username' => $myzc['username'], 'coinname' => $coin, 'txid' => md5($myzc['username'] . $user_coin[$coin . 'b'] . time()), 'num' => $myzc['num'], 'fee' => $myzc['fee'], 'mum' => $myzc['mum'], 'addtime' => time(), 'status' => 1));
            $rs[] = $r = $mo->table('codono_user_coin')->where(array('userid' => $zhannei['userid']))->setInc($coin, $myzc['mum']);
        }

        if (!$fee_user['userid']) {
            $fee_user['userid'] = 0;
        }

        if (0 < $myzc['fee']) {
            $rs[] = $mo->table('codono_myzc_fee')->add(array('userid' => $fee_user['userid'], 'username' => $Coin['zc_user'], 'coinname' => $coin, 'num' => $myzc['num'], 'fee' => $myzc['fee'], 'mum' => $myzc['mum'], 'type' => 2, 'addtime' => time(), 'status' => 1));

            if ($mo->table('codono_user_coin')->where(array($coin . 'b' => $Coin['zc_user']))->find()) {
                //$rs[] = $mo->table('codono_user_coin')->where(array($coin . 'b' => $Coin['zc_user']))->setInc($coin, $myzc['fee']);
                debug(array('lastsql' => $mo->table('codono_user_coin')->getLastSql()), 'Additional costs');
            } else {
                $rs[] = $mo->table('codono_user_coin')->add(array($coin . 'b' => $Coin['zc_user'], $coin => $myzc['fee']));
            }
        }

        $rs[] = M('Myzc')->where(array('id' => trim($id)))->save(array('status' => 1));

        if (check_arr($rs)) {
            $mo->execute('commit');
            $mo->execute('unlock tables');
			   if(strlen($myzc['username'])>30){
					 $valid_res =1;
				 }else{
					 $valid_res =0;
				 }
				 
			  if ($valid_res=0) {
                    $this->error($myzc['username'] . L(' It is not a valid address wallet!'));
                }

                
                if ($json->data->available_balance < (double)$myzc['mum']) {
                    $this->error(L('Wallet balance of less than'));
                }
				
				$balances = json_decode($waves->Balance($main_address,$dj_username),true);
				$wave_main_balance=$waves->deAmount($balances['balance'],$dj_decimal);
                if ($wave_main_balance < $myzc['mum']) {
					$this->error('Wallet balance is '.$wave_main_balance.' Needed: '.$myzc['mum']);
                }

			
			$send_amt= round($myzc['mum'], 8);
$wavesend_response = $waves->Send($main_address,$myzc['username'],$send_amt,$dj_username);
			$the_withdrawal = json_decode($wavesend_response,true);

                            if ($the_withdrawal["error"]) {
							$flag=0;
							$error_message=$the_withdrawal["message"];
							} else {
                            
                                $flag = 1;
                            }
                        
            if ($flag!=1) {
                $mo->execute('rollback');
                $mo->execute('unlock tables');
                $this->error('Failed!'.$error_message);
            } else {
				$mo->execute('commit');
            	$mo->execute('unlock tables');
				M('Myzc')->where(array('id' => $id))->save(array('txid'=>$the_withdrawal['id'])); 
                $this->success('Transfer success!');
            }
        } else {
            $mo->execute('rollback');
            $mo->execute('unlock tables');
            $this->error('Roll-out failure!' . implode('|', $rs) . $myzc['fee']);
        }
			
		}
		if (C('coin')[$coin]['type'] == 'qbb') {
		
		$CoinClient = CoinClient($dj_username, $dj_password, $dj_address, $dj_port, 5, array(), 1);
        $json = $CoinClient->getinfo();
        
        if (!isset($json['version']) || !$json['version']) {
            $this->error('Wallet link failure! 3');
        }

        $Coin = M('Coin')->where(array('name' => $myzc['coinname']))->find();
        $fee_user = M('UserCoin')->where(array($coin . 'b' => $Coin['zc_user']))->find();
        $user_coin = M('UserCoin')->where(array('userid' => $myzc['userid']))->find();
        $zhannei = M('UserCoin')->where(array($coin . 'b' => $myzc['username']))->find();
        $mo = M();
        $mo->execute('set autocommit=0');
        $mo->execute('lock tables  codono_user_coin write  , codono_myzc write  , codono_myzr write , codono_myzc_fee write');
        $rs = array();

        if ($zhannei) {
            $rs[] = $mo->table('codono_myzr')->add(array('userid' => $zhannei['userid'], 'username' => $myzc['username'], 'coinname' => $coin, 'txid' => md5($myzc['username'] . $user_coin[$coin . 'b'] . time()), 'num' => $myzc['num'], 'fee' => $myzc['fee'], 'mum' => $myzc['mum'], 'addtime' => time(), 'status' => 1));
            $rs[] = $r = $mo->table('codono_user_coin')->where(array('userid' => $zhannei['userid']))->setInc($coin, $myzc['mum']);
        }

        if (!$fee_user['userid']) {
            $fee_user['userid'] = 0;
        }

        if (0 < $myzc['fee']) {
            $rs[] = $mo->table('codono_myzc_fee')->add(array('userid' => $fee_user['userid'], 'username' => $Coin['zc_user'], 'coinname' => $coin, 'num' => $myzc['num'], 'fee' => $myzc['fee'], 'mum' => $myzc['mum'], 'type' => 2, 'addtime' => time(), 'status' => 1));

            if ($mo->table('codono_user_coin')->where(array($coin . 'b' => $Coin['zc_user']))->find()) {
                //$rs[] = $mo->table('codono_user_coin')->where(array($coin . 'b' => $Coin['zc_user']))->setInc($coin, $myzc['fee']);
                debug(array('lastsql' => $mo->table('codono_user_coin')->getLastSql()), 'Additional costs');
            } else {
                $rs[] = $mo->table('codono_user_coin')->add(array($coin . 'b' => $Coin['zc_user'], $coin => $myzc['fee']));
            }
        }

        $rs[] = M('Myzc')->where(array('id' => trim($id)))->save(array('status' => 1));

        if (check_arr($rs)) {
            $mo->execute('commit');
            $mo->execute('unlock tables');
            $sendrs = $CoinClient->sendtoaddress($myzc['username'], (double)$myzc['mum']);

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
            } else {
                M('Myzc')->where(array('id' => id))->save(array('txid'=>$sendrs));
                $this->success('Transfer success!');
            }
        } else {
            $mo->execute('rollback');
            $mo->execute('unlock tables');
            $this->error('Roll-out failure!' . implode('|', $rs) . $myzc['fee']);
        }
	}
 }


}

?>