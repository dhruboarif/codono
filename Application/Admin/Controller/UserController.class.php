<?php
namespace Admin\Controller;

class UserController extends AdminController
{
	public function index($name = NULL, $field = NULL, $status = NULL)
	{
		$where=" 1 ";
		if ($field && $name) {
			
			if($field=="email"){
				$where="`email` LIKE '%".$name."%'";
			}else{
				$where = "`".$field."` = '".$name."'";
			}

		}

		if ($status) {
			if($status>2){
				switch($status){
					case "3":
						$where = $where." and `awardstatus`=1 ";
						break;
					case "4":
						$where = $where." and `awardstatus`=0 ";
						break;
					case "5":
						$where = $where." and `idcardauth`=1 ";
						break;
					case "6":
						$where = $where." and `idcardauth`=2 ";
						break;
					case "7": //Not submitted
						$where = $where." and `idcardauth`=0 ";
						break;
					case "8": //Rejected
						$where = $where." and `idcardauth`=3 ";
						break;
				}

			}else{

				$where = $where." and `status`=".($status-1);
			}
		}
//var_dump($where);exit;
		$count = M('User')->where($where)->count();
		$Page = new \Think\Page($count, 15);
		$show = $Page->show();
		$list = M('User')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

		foreach ($list as $k => $v) {
			$list[$k]['invit_1'] = M('User')->where(array('id' => $v['invit_1']))->getField('username');
			$list[$k]['invit_2'] = M('User')->where(array('id' => $v['invit_2']))->getField('username');
			$list[$k]['invit_3'] = M('User')->where(array('id' => $v['invit_3']))->getField('username');
		}

		$this->assign('list', $list);
		$this->assign('page', $show);
		$this->display();
	}

	public function edit($id = NULL)
	{
		if (empty($_POST)) {
			if (empty($id)) {
				$this->data = null;
			}
			else {
				$user = M('User')->where(array('id' => trim($id)))->find();
				$this->data = $user;
			}
			
			$imgstr = "";
			if($user['idcardimg1']){
				$img_arr = array();
				$img_arr = explode("_",$user['idcardimg1']);

				foreach($img_arr as $k=>$v){
					$imgstr = $imgstr.'<a href="/Upload/idcard/'.$v.'" target="_blank"><img src="/Upload/idcard/'.$v.'"  style="width:200px;height:100px;" /></a>';
				}

				unset($img_arr);
			}
			
			$this->assign('userimg', $imgstr);

			$this->display();
		}
		else {
			if (APP_DEMO) {
				$this->error(L('SYSTEM_IN_DEMO_MODE'));
			}

			switch($_POST['awardid']){
				case 0:
					$_POST['awardname']="No prizes";
					break;
				case 1:
					$_POST['awardname']="Apple computer";
					break;
				case 2:
					$_POST['awardname']="Huawei cell phone";
					break;
				case 3:
					$_POST['awardname']="$1000";
					break;
				case 4:
					$_POST['awardname']="Millet bracelet";
					break;
				case 5:
					$_POST['awardname']="$100";
					break;
				case 6:
					$_POST['awardname']="$10";
					break;
				case 7:
					$_POST['awardname']="$1";
					break;
				case 8:
					$_POST['awardname']="No prizes";
					break;
				case 9:
					$_POST['awardname']="$1";
					break;
				case 10:
					$_POST['awardname']="No prizes";
					break;
				default:
					$_POST['awardid']=0;
					$_POST['awardname']="No prizes";
			}
			
			
			unset($_POST['password']);
			if ($_POST['passwd']) {
				
				$_POST['password'] = md5($_POST['passwd']);
				unset($_POST['passwd']);
			}
			else {
				unset($_POST['passwd']);
			}

			if ($_POST['paypassword']) {
				$_POST['paypassword'] = md5($_POST['paypassword']);
			}
			else {
				unset($_POST['paypassword']);
			}
		//	$_POST['username'] = strtotime($_POST['usrname']);
			unset($_POST['usrname']);
			$_POST['cellphonetime'] = strtotime($_POST['cellphonetime']);

            $flag = false;
            if (isset($_POST['id'])) {
                $rs = M('User')->save($_POST);
            } else {
                $mo = M();
                $mo->execute('set autocommit=0');
                $mo->execute('lock tables codono_user write , codono_user_coin write ');
                $rs[] = $mo->table('codono_user')->add($_POST);
                $rs[] = $mo->table('codono_user_coin')->add(array('userid' => $rs[0]));
                $flag = true;
            }

			if ($rs) {
                if ($flag) {
                    $mo->execute('commit');
                    $mo->execute('unlock tables');
                }
                session('reguserId', $rs);
				$this->success(L('SAVED_SUCCESSFULLY'));
			}
			else {
                if ($flag) {
                    $mo->execute('rollback');
                }
				$this->error(L('COULD_NOT_SAVE'));
			}
		}
	}

	public function status($id = NULL, $type = NULL, $moble = 'User',$awardid=0)
	{

		if (APP_DEMO) {
			$this->error(L('SYSTEM_IN_DEMO_MODE'));
		}

		if (empty($id)) {
			$this->error('Select Members!');
		}

		if (empty($type)) {
			$this->error(L('INCORRECT_REQ'));
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
			if (M($moble)->where($where)->delete()) {
                $_where = array(
                    'userid' => $where['id'],
                );
                M('UserCoin')->where($_where)->delete();
				$this->success(L('SUCCESSFULLY_DONE'));
			}
			else {
				$this->error(L('OPERATION_FAILED'));
			}

			break;
			
		case 'idauth': 
			$data = array('idcardauth' => 1, 'addtime' => time());
			break;
			
		case 'notidauth': 
			$data = array('idcardauth' => 0);
			break;
			
		case 'award';
		
			switch($awardid){
				case 0:
					$awardname="No prizes";
					break;
				case 1:
					$awardname="Apple computer";
					break;
				case 2:
					$awardname="Huawei cell phone";
					break;
				case 3:
					$awardname="1000USD in cash";
					break;
				case 4:
					$awardname="Millet bracelet";
					break;
				case 5:
					$awardname="100USD in cash";
					break;
				case 6:
					$awardname="10USD in cash";
					break;
				case 7:
					$awardname="1USD in cash";
					break;
				case 8:
					$awardname="No prizes";
					break;
				case 9:
					$awardname="1USD in cash";
					break;
				case 10:
					$awardname="No prizes";
					break;
				default:
					$awardid=0;
					$awardname="No prizes";
			}
			$data = array('awardstatus' => 0, 'awardid' => $awardid,'awardname'=>$awardname);
			
			break;
		
		default:
			$this->error(L('OPERATION_FAILED'));
		}

		if (M($moble)->where($where)->save($data)) {
			$this->success(L('SUCCESSFULLY_DONE'));
		}
		else {
			$this->error(L('OPERATION_FAILED'));
		}
	}

	public function admin($name = NULL, $field = NULL, $status = NULL)
	{
		$DbFields = M('Admin')->getDbFields();

		if (!in_array('email', $DbFields)) {
			M()->execute('ALTER TABLE `codono_admin` ADD COLUMN `email` VARCHAR(200)  NOT NULL   COMMENT \'\' AFTER `id`;');
		}

		$where = array();

		if ($field && $name) {
			if ($field == 'username') {
				$where['userid'] = M('User')->where(array('username' => $name))->getField('id');
			}
			else {
				$where[$field] = $name;
			}
		}

		if ($status) {
			$where['status'] = $status - 1;
		}

		$count = M('Admin')->where($where)->count();
		$Page = new \Think\Page($count, 15);
		$show = $Page->show();
		$list = M('Admin')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
		$this->assign('list', $list);
		$this->assign('page', $show);
		$this->display();
	}

	public function adminEdit()
	{
		if (empty($_POST)) {
			if (empty($_GET['id'])) {
				$this->data = null;
			}
			else {
				$this->data = M('Admin')->where(array('id' => trim($_GET['id'])))->find();
			}

			$this->display();
		}
		else {
			if (APP_DEMO) {
				$this->error(L('SYSTEM_IN_DEMO_MODE'));
			}

			$input = I('post.');

			if (!check($input['username'], 'username')) {
				$this->error(L('INVALID_USERNAME'));
			}

			if ($input['nickname'] && !check($input['nickname'], 'A')) {
				$this->error(L('INVALID_NICKNAME'));
			}

			if ($input['password'] && !check($input['password'], 'password')) {
				$this->error(L('INVALID_PASSWORD'));
			}

			if ($input['moble'] && !check($input['moble'], 'moble')) {
				$this->error(L('INVALID_PHONE_FORMAT'));
			}

			if ($input['email'] && !check($input['email'], 'email')) {
				$this->error(L('INVALID_EMAIL'));
			}

			if ($input['password']) {
				$input['password'] = md5($input['password']);
			}
			else {
				unset($input['password']);
			}

			if ($_POST['id']) {
				$rs = M('Admin')->save($input);
			}
			else {
				$_POST['addtime'] = time();
				$rs = M('Admin')->add($input);
			}

			if ($rs) {
				$this->success(L('SAVED_SUCCESSFULLY'));
			}
			else {
				$this->error(L('COULD_NOT_SAVE'));
			}
		}
	}

	public function adminStatus($id = NULL, $type = NULL, $moble = 'Admin')
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
			if (M($moble)->where($where)->delete()) {
				$this->success(L('SUCCESSFULLY_DONE'));
			}
			else {
				$this->error(L('OPERATION_FAILED'));
			}

			break;

		default:
			$this->error(L('OPERATION_FAILED'));
		}

		if (M($moble)->where($where)->save($data)) {
			$this->success(L('SUCCESSFULLY_DONE'));
		}
		else {
			$this->error(L('OPERATION_FAILED'));
		}
	}

	public function auth()
	{
		//$list = $this->lists('AuthGroup', array('module' => 'admin'), 'id asc');
		$authGroup = M('AuthGroup');
		$condition['module'] = 'admin';
		$list = $authGroup->order('id asc')->where($condition)->select();
		$list = int_to_string($list);
		$this->assign('_list', $list);
		$this->assign('_use_tip', true);
		$this->meta_title = 'Authority management';
		$this->display();
	}

	public function authEdit()
	{
		if (empty($_POST)) {
			if (empty($_GET['id'])) {
				$this->data = null;
			}
			else {
				$this->data = M('AuthGroup')->where(array(
                    'module' => 'admin',
                    'type' => 1,//Common\Model\AuthGroupModel::TYPE_ADMIN
                ))->find((int) $_GET['id']);
			}

			$this->display();
		}
		else {
			if (APP_DEMO) {
				$this->error(L('SYSTEM_IN_DEMO_MODE'));
			}

			if (isset($_POST['rules'])) {
				sort($_POST['rules']);
				$_POST['rules'] = implode(',', array_unique($_POST['rules']));
			}

			$_POST['module'] = 'admin';
			$_POST['type'] = 1;//Common\Model\AuthGroupModel::TYPE_ADMIN;
			$AuthGroup = D('AuthGroup');
			$data = $AuthGroup->create();

			if ($data) {
				if (empty($data['id'])) {
					$r = $AuthGroup->add();
				}
				else {
					$r = $AuthGroup->save();
				}

				if ($r === false) {
					$this->error('operation failed' . $AuthGroup->getError());
				}
				else {
					$this->success('Successful operation!');
				}
			}
			else {
				$this->error('operation failed' . $AuthGroup->getError());
			}
		}
	}

	public function authStatus($id = NULL, $type = NULL, $moble = 'AuthGroup')
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
			if (M($moble)->where($where)->delete()) {
				$this->success(L('SUCCESSFULLY_DONE'));
			}
			else {
				$this->error(L('OPERATION_FAILED'));
			}

			break;

		default:
			$this->error(L('OPERATION_FAILED'));
		}

		if (M($moble)->where($where)->save($data)) {
			$this->success(L('SUCCESSFULLY_DONE'));
		}
		else {
			$this->error(L('OPERATION_FAILED'));
		}
	}

	public function authStart()
	{
		if (M('AuthRule')->where(array('status' => 1))->delete()) {
			$this->success(L('SUCCESSFULLY_DONE'));
		}
		else {
			$this->error(L('OPERATION_FAILED'));
		}
	}

	public function apikeys($userid)
	{
		if (!check($userid, 'd')) {
            $this->error(L('Invalid userid'));
        }
		$uinfo = M('User')->field('id,username,apikey,token')->where(array('id' => $userid))->find();
		
		if ($uinfo['id'] !=$userid) {
		  $this->error(L('Invalid userid'));
        }
		$apikey=$uinfo['apikey'];
		
		if ($uinfo['apikey'] == NULL) {
                $apikey = md5(md5(rand(0, 10000) . md5(time()), md5(uniqid())));
                M('User')->where(array('id' => $userid))->setField('apikey', $apikey);
        }
		
		$data['status']=1;
		
		$data['username']=$uinfo['username'];
		$data['id']=$userid;
		$data['apikey']=$apikey;
		exit(json_encode($data));
	}
	public function authAccess()
	{
		$this->updateRules();
		$auth_group = M('AuthGroup')->where(array(
			'status' => array('egt', '0'),
			'module' => 'admin',
			'type'   => 1,//Common\Model\AuthGroupModel::TYPE_ADMIN
			))->getfield('id,id,title,rules');
		$node_list = $this->returnNodes();
		$map = array(
            'module' => 'admin',
            'type' => 2,//Common\Model\AuthRuleModel::RULE_MAIN,
            'status' => 1
        );
		$main_rules = M('AuthRule')->where($map)->getField('name,id');
		$map = array(
            'module' => 'admin',
            'type' => 1,//Common\Model\AuthRuleModel::RULE_URL,
            'status' => 1
        );
		$child_rules = M('AuthRule')->where($map)->getField('name,id');
		$this->assign('main_rules', $main_rules);
		$this->assign('auth_rules', $child_rules);
		$this->assign('node_list', $node_list);
		$this->assign('auth_group', $auth_group);
		$this->assign('this_group', $auth_group[(int) $_GET['group_id']]);
		$this->meta_title = 'Access authorization';
		$this->display();
	}

	protected function updateRules()
	{
		$nodes = $this->returnNodes(false);
		$AuthRule = M('AuthRule');
		$map = array(
			'module' => 'admin',
			'type'   => array('in', '1,2')
			);
		$rules = $AuthRule->where($map)->order('name')->select();
		$data = array();

		foreach ($nodes as $value) {
			$temp['name'] = $value['url'];
			$temp['title'] = $value['title'];
			$temp['module'] = 'admin';

			if (0 < $value['pid']) {
				$temp['type'] = 1;//Common\Model\AuthRuleModel::RULE_URL;
			}
			else {
				$temp['type'] = 2;//Common\Model\AuthRuleModel::RULE_MAIN;
			}

			$temp['status'] = 1;
			$data[strtolower($temp['name'] . $temp['module'] . $temp['type'])] = $temp;
		}

		$update = array();
		$ids = array();

		foreach ($rules as $index => $rule) {
			$key = strtolower($rule['name'] . $rule['module'] . $rule['type']);

			if (isset($data[$key])) {
				$data[$key]['id'] = $rule['id'];
				$update[] = $data[$key];
				unset($data[$key]);
				unset($rules[$index]);
				unset($rule['condition']);
				$diff[$rule['id']] = $rule;
			}
			else if ($rule['status'] == 1) {
				$ids[] = $rule['id'];
			}
		}

		if (count($update)) {
			foreach ($update as $k => $row) {
				if ($row != $diff[$row['id']]) {
					$AuthRule->where(array('id' => $row['id']))->save($row);
				}
			}
		}

		if (count($ids)) {
			$AuthRule->where(array(
				'id' => array('IN', implode(',', $ids))
				))->save(array('status' => -1));
		}

		if (count($data)) {
			$AuthRule->addAll(array_values($data));
		}

		if ($AuthRule->getDbError()) {
			trace('[' . 'Admin\\Controller\\UserController::updateRules' . ']:' . $AuthRule->getDbError());
			return false;
		}
		else {
			return true;
		}
	}

	public function authAccessUp()
	{
		if (isset($_POST['rules'])) {
			sort($_POST['rules']);
			$_POST['rules'] = implode(',', array_unique($_POST['rules']));
		}

		$_POST['module'] = 'admin';
		$_POST['type'] = 1;//Common\Model\AuthGroupModel::TYPE_ADMIN;
		$AuthGroup = D('AuthGroup');
		$data = $AuthGroup->create();

		if ($data) {
			if (empty($data['id'])) {
				$r = $AuthGroup->add();
			}
			else {
				$r = $AuthGroup->save();
			}

			if ($r === false) {
				$this->error('operation failed' . $AuthGroup->getError());
			}
			else {
				$this->success('Successful operation!');
			}
		}
		else {
			$this->error('operation failed' . $AuthGroup->getError());
		}
	}

	public function authUser($group_id)
	{
		if (empty($group_id)) {
			$this->error('Parameter error');
		}

		$auth_group = M('AuthGroup')->where(array(
			'status' => array('egt', '0'),
			'module' => 'admin',
			'type'   => 1,//Common\Model\AuthGroupModel::TYPE_ADMIN
			))->getfield('id,id,title,rules');
		$prefix = C('DB_PREFIX');
/* 		$l_table = $prefix . 'ucenter_member';//Common\Model\AuthGroupModel::MEMBER;
		$r_table = $prefix . 'auth_group_access';//Common\Model\AuthGroupModel::AUTH_GROUP_ACCESS;
		$model = M()->table($l_table . ' m')->join($r_table . ' a ON m.id=a.uid');
		$_REQUEST = array();
		$list = $this->lists($model, array(
			'a.group_id' => $group_id,
			'm.status'   => array('egt', 0)
			), 'm.id asc', null, 'm.id,m.username,m.nickname,m.last_login_time,m.last_login_ip,m.status'); */

			
		$l_table = $prefix . 'auth_group_access';//Common\Model\AuthGroupModel::MEMBER;
		$r_table = $prefix . 'admin';//Common\Model\AuthGroupModel::AUTH_GROUP_ACCESS;
		$model = M()->table($l_table . ' a')->join($r_table . ' m ON m.id=a.uid');
		$_REQUEST = array();
		$list = $this->lists($model, array(
			'a.group_id' => $group_id,
			//'m.status'   => array('egt', 0)
			), 'a.uid desc', null, 'm.id,m.username,m.nickname,m.last_login_time,m.last_login_ip,m.status');
			
			
        int_to_string($list);
		
		//var_dump($list);
		
		$this->assign('_list', $list);
		$this->assign('auth_group', $auth_group);
		$this->assign('this_group', $auth_group[(int) $_GET['group_id']]);
		$this->meta_title = 'Members of the authorized';
		$this->display();
	}

	public function authUserAdd()
	{
		$uid = I('uid');

		if (empty($uid)) {
			$this->error('Please enter backend userid');
		}

		if (!check($uid, 'd')) {
			$user = M('Admin')->where(array('username' => $uid))->find();

			if (!$user) {
				$user = M('Admin')->where(array('nickname' => $uid))->find();
			}

			if (!$user) {
				$user = M('Admin')->where(array('moble' => $uid))->find();
			}

			if (!$user) {
				$this->error('User does not exist(id username nickname The phone number can be)');
			}

			$uid = $user['id'];
		}

		$gid = I('group_id');

		if ($res = M('AuthGroupAccess')->where(array('uid' => $uid))->find()) {
			if ($res['group_id'] == $gid) {
				$this->error('already exists,Do not repeat Add');
			} else {
				$res = M('AuthGroup')->where(array('id' => $gid))->find();

				if (!$res) {
					$this->error('The current group does not exist');
				}

				//$this->error('already exists[' . $res['title'] . ']group,Add unrepeatable');
			}
		}

		$AuthGroup = D('AuthGroup');

		if (is_numeric($uid)) {
			if (is_administrator($uid)) {
				$this->error('The user is the super administrator');
			}

			if (!M('Admin')->where(array('id' => $uid))->find()) {
				$this->error('Admin user does not exist');
			}
		}

		if ($gid && !$AuthGroup->checkGroupId($gid)) {
			$this->error($AuthGroup->error);
		}

		if ($AuthGroup->addToGroup($uid, $gid)) {
			$this->success('Successful operation');
		}
		else {
			$this->error($AuthGroup->getError());
		}
	}

	public function authUserRemove()
	{
		$uid = I('uid');
		$gid = I('group_id');

		if ($uid == UID) {
			$this->error('Authorization is not allowed to lift itself');
		}

		if (empty($uid) || empty($gid)) {
			$this->error('Parameter is incorrect');
		}

		$AuthGroup = D('AuthGroup');

		if (!$AuthGroup->find($gid)) {
			$this->error('User group does not exist');
		}

		if ($AuthGroup->removeFromGroup($uid, $gid)) {
			$this->success('Successful operation');
		}
		else {
			$this->error('operation failed');
		}
	}

	public function log($name = NULL, $field = NULL, $status = NULL)
	{
		$where = array();

		if ($field && $name) {
			if ($field == 'username') {
				$where['userid'] = M('User')->where(array('username' => $name))->getField('id');
			}
			else {
				$where[$field] = $name;
			}
		}

		if ($status) {
			$where['status'] = $status - 1;
		}

		$count = M('UserLog')->where($where)->count();
		$Page = new \Think\Page($count, 15);
		$show = $Page->show();
		$list = M('UserLog')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

		foreach ($list as $k => $v) {
			$list[$k]['username'] = M('User')->where(array('id' => $v['userid']))->getField('username');
		}

		$this->assign('list', $list);
		$this->assign('page', $show);
		$this->display();
	}

	public function logEdit($id = NULL)
	{
		if (empty($_POST)) {
			if (empty($id)) {
				$this->data = null;
			}
			else {
				$this->data = M('UserLog')->where(array('id' => trim($id)))->find();
			}

			$this->display();
		}
		else {
			if (APP_DEMO) {
				$this->error(L('SYSTEM_IN_DEMO_MODE'));
			}

			$_POST['addtime'] = strtotime($_POST['addtime']);

            if ($id) {
                unset($_POST['id']);
                if (M()->table('codono_user_log')->where(array('id'=>$id))->save($_POST)) {
                    $this->success(L('SAVED_SUCCESSFULLY'));
                } else {
                    $this->error(L('COULD_NOT_SAVE'));
                }
            } else {
                if (M()->table('codono_user_log')->add($_POST)) {
                    $this->success(L('ADDED_SUCCESSFULLY'));
                } else {
                    $this->error(L('FAILED_TO_ADD'));
                }
            }

		}
	}

	public function logStatus($id = NULL, $type = NULL, $moble = 'UserLog')
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
			if (M($moble)->where($where)->delete()) {
				$this->success(L('SUCCESSFULLY_DONE'));
			}
			else {
				$this->error(L('OPERATION_FAILED'));
			}

			break;

		default:
			$this->error(L('OPERATION_FAILED'));
		}

		if (M($moble)->where($where)->save($data)) {
			$this->success(L('SUCCESSFULLY_DONE'));
		}
		else {
			$this->error(L('OPERATION_FAILED'));
		}
	}

	public function wallet($name = NULL, $field = NULL, $coinname = NULL, $status = NULL)
	{
		$where = array();

		if ($field && $name) {
			if ($field == 'username') {
				$where['userid'] = M('User')->where(array('username' => $name))->getField('id');
			}
			else {
				$where[$field] = $name;
			}
		}

		if ($status) {
			$where['status'] = $status - 1;
		}

		if ($coinname) {
			$where['coinname'] = trim($coinname);
		}

		$count = M('UserWallet')->where($where)->count();
		$Page = new \Think\Page($count, 15);
		$show = $Page->show();
		$list = M('UserWallet')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

		foreach ($list as $k => $v) {
			$list[$k]['username'] = M('User')->where(array('id' => $v['userid']))->getField('username');
		}

		$this->assign('list', $list);
		$this->assign('page', $show);
		$this->display();
	}

	public function walletEdit($id = NULL)
	{
		if (empty($_POST)) {
			if (empty($id)) {
				$this->data = null;
			}
			else {
				$this->data = M('UserWallet')->where(array('id' => trim($id)))->find();
			}

			$this->display();
		}
		else {
			if (APP_DEMO) {
				$this->error(L('SYSTEM_IN_DEMO_MODE'));
			}

			$_POST['addtime'] = strtotime($_POST['addtime']);

            if ($id) {
                unset($_POST['id']);
                if (M()->table('codono_user_wallet')->where(array('id' => $id))->save($_POST)) {
                    $this->success(L('SAVED_SUCCESSFULLY'));
                } else {
                    $this->error(L('COULD_NOT_SAVE'));
                }
            } else {
                if (M()->table('codono_user_wallet')->add($_POST)) {
                    $this->success(L('ADDED_SUCCESSFULLY'));
                } else {
                    $this->error(L('FAILED_TO_ADD'));
                }
            }
		}
	}

	public function walletStatus($id = NULL, $type = NULL, $moble = 'UserWallet')
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
			if (M($moble)->where($where)->delete()) {
				$this->success(L('SUCCESSFULLY_DONE'));
			}
			else {
				$this->error(L('OPERATION_FAILED'));
			}

			break;

		default:
			$this->error(L('OPERATION_FAILED'));
		}

		if (M($moble)->where($where)->save($data)) {
			$this->success(L('SUCCESSFULLY_DONE'));
		}
		else {
			$this->error(L('OPERATION_FAILED'));
		}
	}

	public function bank($name = NULL, $field = NULL, $status = NULL)
	{
		$where = array();

		if ($field && $name) {
			if ($field == 'username') {
				$where['userid'] = M('User')->where(array('username' => $name))->getField('id');
			}
			else {
				$where[$field] = $name;
			}
		}

		if ($status) {
			$where['status'] = $status - 1;
		}

		$count = M('UserBank')->where($where)->count();
		$Page = new \Think\Page($count, 15);
		$show = $Page->show();
		$list = M('UserBank')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

		foreach ($list as $k => $v) {
			$list[$k]['username'] = M('User')->where(array('id' => $v['userid']))->getField('username');
		}

		$this->assign('list', $list);
		$this->assign('page', $show);
		$this->display();
	}

	public function bankEdit($id = NULL)
	{
		if (empty($_POST)) {
			if (empty($id)) {
				$this->data = null;
			}
			else {
				$this->data = M('UserBank')->where(array('id' => trim($id)))->find();
			}

			$this->display();
		}
		else {
			if (APP_DEMO) {
				$this->error(L('SYSTEM_IN_DEMO_MODE'));
			}

			$_POST['addtime'] = strtotime($_POST['addtime']);

            if ($id) {
                if (M()->table('codono_user_bank')->where(array('id' => $id))->save($_POST)) {
                    $this->success(L('SAVED_SUCCESSFULLY'));
                }
                else {
                    $this->error(L('COULD_NOT_SAVE'));
                }
            } else {
                if (M()->table('codono_user_bank')->add($_POST)) {
                    $this->success(L('ADDED_SUCCESSFULLY'));
                }
                else {
                    $this->error(L('FAILED_TO_ADD'));
                }
            }
		}
	}

	public function bankStatus($id = NULL, $type = NULL, $moble = 'UserBank')
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
			if (M($moble)->where($where)->delete()) {
				$this->success(L('SUCCESSFULLY_DONE'));
			}
			else {
				$this->error(L('OPERATION_FAILED'));
			}

			break;

		default:
			$this->error(L('OPERATION_FAILED'));
		}

		if (M($moble)->where($where)->save($data)) {
			$this->success(L('SUCCESSFULLY_DONE'));
		}
		else {
			$this->error(L('OPERATION_FAILED'));
		}
	}

	public function coin($name = NULL, $field = NULL)
	{
		$where = array();

		if ($field && $name) {
			if ($field == 'username') {
				$where['userid'] = M('User')->where(array('username' => $name))->getField('id');
			}
			else {
				$where[$field] = $name;
			}
		}

		$count = M('UserCoin')->where($where)->count();
		$Page = new \Think\Page($count, 15);
		$show = $Page->show();
		$list = M('UserCoin')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

		foreach ($list as $k => $v) {
			$list[$k]['username'] = M('User')->where(array('id' => $v['userid']))->getField('username');
		}

		$this->assign('list', $list);
		$this->assign('page', $show);
		$this->display();
	}

	public function coinEdit($id = NULL)
	{
		if (empty($_POST)) {
			if (empty($id)) {
				$this->data = null;
			}
			else {
				$this->data = M('UserCoin')->where(array('id' => trim($id)))->find();
			}

			$this->display();
		}
		else {
			if (APP_DEMO) {
				$this->error(L('SYSTEM_IN_DEMO_MODE'));
			}
			
            if ($id) {
                if (M('UserCoin')->save($_POST)) {
                    $this->success(L('SAVED_SUCCESSFULLY'));
                }
                else {
                    $this->error(L('COULD_NOT_SAVE'));
                }
            } else {
                if (M()->table('codono_user_coin')->add($_POST)) {
                    $this->success(L('ADDED_SUCCESSFULLY'));
                }
                else {
                    $this->error(L('FAILED_TO_ADD'));
                }
            }
		}
	}

	public function coinLog($userid = NULL, $coinname = NULL)
	{
		$data['userid'] = $userid;
		$data['username'] = M('User')->where(array('id' => $userid))->getField('username');
		$data['coinname'] = $coinname;
		$data['zhengcheng'] = M('UserCoin')->where(array('userid' => $userid))->getField($coinname);
		$data['dongjie'] = M('UserCoin')->where(array('userid' => $userid))->getField($coinname . 'd');
		$data['zongji'] = $data['zhengcheng'] + $data['dongjie'];
		$data['chongzhiusd'] = M('Mycz')->where(array(
			'userid' => $userid,
			'status' => array('neq', '0')
			))->sum('num');
		$data['tixianusd'] = M('Mytx')->where(array('userid' => $userid, 'status' => 1))->sum('num');
		$data['tixianusdd'] = M('Mytx')->where(array('userid' => $userid, 'status' => 0))->sum('num');

		if ($coinname != 'usd') {
			$data['chongzhi'] = M('Myzr')->where(array(
				'userid' => $userid,
				'status' => array('neq', '0')
				))->sum('num');
			$data['tixian'] = M('Myzc')->where(array('userid' => $userid, 'status' => 1))->sum('num');
		}

		$this->assign('data', $data);
		$this->display();
	}

	public function goods($name = NULL, $field = NULL, $status = NULL)
	{
		$where = array();

		if ($field && $name) {
			if ($field == 'username') {
				$where['userid'] = M('User')->where(array('username' => $name))->getField('id');
			}
			else {
				$where[$field] = $name;
			}
		}

		if ($status) {
			$where['status'] = $status - 1;
		}

		$count = M('UserGoods')->where($where)->count();
		$Page = new \Think\Page($count, 15);
		$show = $Page->show();
		$list = M('UserGoods')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

		foreach ($list as $k => $v) {
			$list[$k]['username'] = M('User')->where(array('id' => $v['userid']))->getField('username');
		}

		$this->assign('list', $list);
		$this->assign('page', $show);
		$this->display();
	}

	public function goodsEdit($id = NULL)
	{
		if (empty($_POST)) {
			if (empty($id)) {
				$this->data = null;
			}
			else {
				$this->data = M('UserGoods')->where(array('id' => trim($id)))->find();
			}

			$this->display();
		}
		else {
			if (APP_DEMO) {
				$this->error(L('SYSTEM_IN_DEMO_MODE'));
			}

			$_POST['addtime'] = strtotime($_POST['addtime']);

            if ($id) {
                unset($_POST['id']);
                if (M()->table('codono_user_goods')->where(array('id'=>$id))->save($_POST)) {
                    $this->success(L('SAVED_SUCCESSFULLY'));
                }
                else {
                    $this->error(L('COULD_NOT_SAVE'));
                }
            } else {
                if (M()->table('codono_user_goods')->add($_POST)) {
                    $this->success(L('ADDED_SUCCESSFULLY'));
                }
                else {
                    $this->error(L('FAILED_TO_ADD'));
                }
            }
		}
	}

	public function goodsStatus($id = NULL, $type = NULL, $moble = 'UserGoods')
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
			if (M($moble)->where($where)->delete()) {
				$this->success(L('SUCCESSFULLY_DONE'));
			}
			else {
				$this->error(L('OPERATION_FAILED'));
			}

			break;

		default:
			$this->error(L('OPERATION_FAILED'));
		}

		if (M($moble)->where($where)->save($data)) {
			$this->success(L('SUCCESSFULLY_DONE'));
		}
		else {
			$this->error(L('OPERATION_FAILED'));
		}
	}

	public function setpwd()
	{
		if (IS_POST) {
			defined('APP_DEMO') || define('APP_DEMO', 0);

			if (APP_DEMO) {
				$this->error(L('SYSTEM_IN_DEMO_MODE'));
			}

			$oldpassword = $_POST['oldpassword'];
			$newpassword = $_POST['newpassword'];
			$repassword = $_POST['repassword'];

			if (!check($oldpassword, 'password')) {
				$this->error(L('INVALID_OLD_PWD'));
			}

			if (md5($oldpassword) != session('admin_password')) {
				$this->error(L('INCORRECT_OLD_PWD'));
			}

			if (!check($newpassword, 'password')) {
				$this->error(L('INVALID_NEW_PWD'));
			}

			if ($newpassword != $repassword) {
				$this->error(L('INCORRECT_NEW_PWD'));
			}

			if (D('Admin')->where(array('id' => session('admin_id')))->save(array('password' => md5($newpassword)))) {
				$this->success(L('LOGIN_PWD_CHANGED'), U('Login/loginout'));
			}
			else {
				$this->error(L('NO_CHANGES_TO_PWD'));
			}
		}

		$this->display();
	}

	
	public function award($name = NULL, $field = NULL, $status = NULL)
	{
/* 		$where = array();

		if ($field && $name) {
			if ($field == 'username') {
				$where['userid'] = M('User')->where(array('username' => $name))->getField('id');
			}
			else {
				$where[$field] = $name;
			}
		}

		if ($status) {
			$where['status'] = $status - 1;
		} */

		$where="";
		if ($field && $name) {
			//$where[$field] = $name;
			if($field=="awardid" &&($name==7 || $name==9)){
				$where = " (`awardid`=7 or `awardid`=9) ";
			}else{
				$where = "`".$field."`='".$name."'";
			}
		}

		if($status){
			$where = $where." and `status`=".($status-1);
		}
		
		
		
		
		
		
		$count = M('UserAward')->where($where)->count();
		$Page = new \Think\Page($count, 15);
		$show = $Page->show();
		$list = M('UserAward')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

		foreach ($list as $k => $v) {
			$list[$k]['username'] = M('User')->where(array('id' => $v['userid']))->getField('username');
		}

		$this->assign('list', $list);
		$this->assign('page', $show);
		$this->display();
	}

	public function awardStatus($id = NULL, $type = NULL,$status=NUll, $moble = 'UserAward')
	{
		if (APP_DEMO) {
			$this->error(L('SYSTEM_IN_DEMO_MODE'));
		}

		if (empty($id)) {
			$this->error('Please select the records to operate!');
		}

		if (empty($type)) {
			$this->error(L('INCORRECT_REQ'));
		}

		if (strpos(',', $id)) {
			$id = implode(',', $id);
		}

		$where['id'] = array('in', $id);

		switch (strtolower($type)) {
		case 'dealaward':
			if(empty($status)){
				$this->error("Parameter error!");
			}
			$data=array('status' => $status,'dealtime'=>time());
			break;
		case 'del':
			if (M($moble)->where($where)->delete()) {
				$this->success(L('SUCCESSFULLY_DONE'));
			}
			else {
				$this->error(L('OPERATION_FAILED'));
			}

			break;

		default:
			$this->error('operation failed');
		}
		
		if (M($moble)->where($where)->save($data)) {
			$this->success(L('SUCCESSFULLY_DONE'));
		}
		else {
			$this->error(L('OPERATION_FAILED'));
		}
		
		
	}
	
	public function checkUpdata()
	{
		if (!S(MODULE_NAME . CONTROLLER_NAME . 'checkUpdata')) {
			$list = M('Menu')->where(array(
				'url' => 'User/index',
				'pid' => array('neq', 0)
				))->select();

			if ($list[1]) {
				M('Menu')->where(array('id' => $list[1]['id']))->delete();
			}
			else if (!$list) {
				M('Menu')->add(array('url' => 'User/index', 'title' => 'User Management', 'pid' => 3, 'sort' => 1, 'hide' => 0, 'group' => 'user', 'ico_name' => 'user'));
			}
			else {
				M('Menu')->where(array(
					'url' => 'User/index',
					'pid' => array('neq', 0)
					))->save(array('title' => 'User Management', 'pid' => 3, 'sort' => 1, 'hide' => 0, 'group' => 'user', 'ico_name' => 'user'));
			}

			$list = M('Menu')->where(array(
				'url' => 'User/admin',
				'pid' => array('neq', 0)
				))->select();

			if ($list[1]) {
				M('Menu')->where(array('id' => $list[1]['id']))->delete();
			}
			else if (!$list) {
				M('Menu')->add(array('url' => 'User/admin', 'title' => 'Administrator Management', 'pid' => 3, 'sort' => 2, 'hide' => 0, 'group' => 'user', 'ico_name' => 'user'));
			}
			else {
				M('Menu')->where(array(
					'url' => 'User/admin',
					'pid' => array('neq', 0)
					))->save(array('title' => 'Admin', 'pid' => 3, 'sort' => 2, 'hide' => 0, 'group' => 'user', 'ico_name' => 'user'));
			}

			$list = M('Menu')->where(array(
				'url' => 'User/auth',
				'pid' => array('neq', 0)
				))->select();

			if ($list[1]) {
				M('Menu')->where(array('id' => $list[1]['id']))->delete();
			}
			else if (!$list) {
				M('Menu')->add(array('url' => 'User/auth', 'title' => 'Permissions list', 'pid' => 3, 'sort' => 3, 'hide' => 0, 'group' => 'user', 'ico_name' => 'user'));
			}
			else {
				M('Menu')->where(array(
					'url' => 'User/auth',
					'pid' => array('neq', 0)
					))->save(array('title' => 'Permissions list', 'pid' => 3, 'sort' => 3, 'hide' => 0, 'group' => 'user', 'ico_name' => 'user'));
			}

			$list = M('Menu')->where(array(
				'url' => 'User/log',
				'pid' => array('neq', 0)
				))->select();

			if ($list[1]) {
				M('Menu')->where(array('id' => $list[1]['id']))->delete();
			}
			else if (!$list) {
				M('Menu')->add(array('url' => 'User/log', 'title' => 'Logins', 'pid' => 3, 'sort' => 4, 'hide' => 0, 'group' => 'user', 'ico_name' => 'user'));
			}
			else {
				M('Menu')->where(array(
					'url' => 'User/log',
					'pid' => array('neq', 0)
					))->save(array('title' => 'Logins', 'pid' => 3, 'sort' => 4, 'hide' => 0, 'group' => 'user', 'ico_name' => 'user'));
			}

			$list = M('Menu')->where(array(
				'url' => 'User/wallet',
				'pid' => array('neq', 0)
				))->select();

			if ($list[1]) {
				M('Menu')->where(array('id' => $list[1]['id']))->delete();
			}
			else if (!$list) {
				M('Menu')->add(array('url' => 'User/wallet', 'title' => 'Users wallet', 'pid' => 3, 'sort' => 5, 'hide' => 0, 'group' => 'user', 'ico_name' => 'user'));
			}
			else {
				M('Menu')->where(array(
					'url' => 'User/wallet',
					'pid' => array('neq', 0)
					))->save(array('title' => 'Users wallet', 'pid' => 3, 'sort' => 5, 'hide' => 0, 'group' => 'user', 'ico_name' => 'user'));
			}

			$list = M('Menu')->where(array(
				'url' => 'User/bank',
				'pid' => array('neq', 0)
				))->select();

			if ($list[1]) {
				M('Menu')->where(array('id' => $list[1]['id']))->delete();
			}
			else if (!$list) {
				M('Menu')->add(array('url' => 'User/bank', 'title' => 'Withdraw Address', 'pid' => 3, 'sort' => 6, 'hide' => 0, 'group' => 'user', 'ico_name' => 'user'));
			}
			else {
				M('Menu')->where(array(
					'url' => 'User/bank',
					'pid' => array('neq', 0)
					))->save(array('title' => 'Withdraw Address', 'pid' => 3, 'sort' => 6, 'hide' => 0, 'group' => 'user', 'ico_name' => 'user'));
			}

			$list = M('Menu')->where(array(
				'url' => 'User/coin',
				'pid' => array('neq', 0)
				))->select();

			if ($list[1]) {
				M('Menu')->where(array('id' => $list[1]['id']))->delete();
			}
			else if (!$list) {
				M('Menu')->add(array('url' => 'User/coin', 'title' => 'Users property', 'pid' => 3, 'sort' => 7, 'hide' => 0, 'group' => 'user', 'ico_name' => 'user'));
			}
			else {
				M('Menu')->where(array(
					'url' => 'User/coin',
					'pid' => array('neq', 0)
					))->save(array('title' => 'Users property', 'pid' => 3, 'sort' => 7, 'hide' => 0, 'group' => 'user', 'ico_name' => 'user'));
			}

			$list = M('Menu')->where(array(
				'url' => 'User/goods',
				'pid' => array('neq', 0)
				))->select();

			if ($list[1]) {
				M('Menu')->where(array('id' => $list[1]['id']))->delete();
			}
			else if (!$list) {
				M('Menu')->add(array('url' => 'User/goods', 'title' => 'contact address', 'pid' => 3, 'sort' => 8, 'hide' => 0, 'group' => 'user', 'ico_name' => 'user'));
			}
			else {
				M('Menu')->where(array(
					'url' => 'User/goods',
					'pid' => array('neq', 0)
					))->save(array('title' => 'contact address', 'pid' => 3, 'sort' => 8, 'hide' => 0, 'group' => 'user', 'ico_name' => 'user'));
			}

			$list = M('Menu')->where(array(
				'url' => 'User/edit',
				'pid' => array('neq', 0)
				))->select();

			if ($list[1]) {
				M('Menu')->where(array('id' => $list[1]['id']))->delete();
			}
			else {
				$pid = M('Menu')->where(array(
					'url' => 'User/index',
					'pid' => array('neq', 0)
					))->getField('id');

				if (!$list) {
					M('Menu')->add(array('url' => 'User/edit', 'title' => 'Edit Add', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => 'user', 'ico_name' => 'home'));
				}
				else {
					M('Menu')->where(array(
						'url' => 'User/edit',
						'pid' => array('neq', 0)
						))->save(array('title' => 'Edit Add', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => 'user', 'ico_name' => 'home'));
				}
			}

			$list = M('Menu')->where(array(
				'url' => 'User/status',
				'pid' => array('neq', 0)
				))->select();

			if ($list[1]) {
				M('Menu')->where(array('id' => $list[1]['id']))->delete();
			}
			else {
				$pid = M('Menu')->where(array(
					'url' => 'User/index',
					'pid' => array('neq', 0)
					))->getField('id');

				if (!$list) {
					M('Menu')->add(array('url' => 'User/status', 'title' => 'Modify status', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => 'user', 'ico_name' => 'home'));
				}
				else {
					M('Menu')->where(array(
						'url' => 'User/status',
						'pid' => array('neq', 0)
						))->save(array('title' => 'Modify status', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => 'user', 'ico_name' => 'home'));
				}
			}

			$list = M('Menu')->where(array(
				'url' => 'User/adminEdit',
				'pid' => array('neq', 0)
				))->select();

			if ($list[1]) {
				M('Menu')->where(array('id' => $list[1]['id']))->delete();
			}
			else {
				$pid = M('Menu')->where(array(
					'url' => 'User/admin',
					'pid' => array('neq', 0)
					))->getField('id');

				if (!$list) {
					M('Menu')->add(array('url' => 'User/adminEdit', 'title' => 'Edit Add', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => 'user', 'ico_name' => 'home'));
				}
				else {
					M('Menu')->where(array(
						'url' => 'User/adminEdit',
						'pid' => array('neq', 0)
						))->save(array('title' => 'Edit Add', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => 'user', 'ico_name' => 'home'));
				}
			}

			$list = M('Menu')->where(array(
				'url' => 'User/adminStatus',
				'pid' => array('neq', 0)
				))->select();

			if ($list[1]) {
				M('Menu')->where(array('id' => $list[1]['id']))->delete();
			}
			else {
				$pid = M('Menu')->where(array(
					'url' => 'User/admin',
					'pid' => array('neq', 0)
					))->getField('id');

				if (!$list) {
					M('Menu')->add(array('url' => 'User/adminStatus', 'title' => 'Modify status', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => 'user', 'ico_name' => 'home'));
				}
				else {
					M('Menu')->where(array(
						'url' => 'User/adminStatus',
						'pid' => array('neq', 0)
						))->save(array('title' => 'Modify status', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => 'user', 'ico_name' => 'home'));
				}
			}

			$list = M('Menu')->where(array(
				'url' => 'User/authEdit',
				'pid' => array('neq', 0)
				))->select();

			if ($list[1]) {
				M('Menu')->where(array('id' => $list[1]['id']))->delete();
			}
			else {
				$pid = M('Menu')->where(array(
					'url' => 'User/auth',
					'pid' => array('neq', 0)
					))->getField('id');

				if (!$list) {
					M('Menu')->add(array('url' => 'User/authEdit', 'title' => 'Edit Add', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => 'user', 'ico_name' => 'home'));
				}
				else {
					M('Menu')->where(array(
						'url' => 'User/authEdit',
						'pid' => array('neq', 0)
						))->save(array('title' => 'Edit Add', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => 'user', 'ico_name' => 'home'));
				}
			}

			$list = M('Menu')->where(array(
				'url' => 'User/authStatus',
				'pid' => array('neq', 0)
				))->select();

			if ($list[1]) {
				M('Menu')->where(array('id' => $list[1]['id']))->delete();
			}
			else {
				$pid = M('Menu')->where(array(
					'url' => 'User/auth',
					'pid' => array('neq', 0)
					))->getField('id');

				if (!$list) {
					M('Menu')->add(array('url' => 'User/authStatus', 'title' => 'Modify status', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => 'user', 'ico_name' => 'home'));
				}
				else {
					M('Menu')->where(array(
						'url' => 'User/authStatus',
						'pid' => array('neq', 0)
						))->save(array('title' => 'Modify status', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => 'user', 'ico_name' => 'home'));
				}
			}

			$list = M('Menu')->where(array(
				'url' => 'User/authStart',
				'pid' => array('neq', 0)
				))->select();

			if ($list[1]) {
				M('Menu')->where(array('id' => $list[1]['id']))->delete();
			}
			else {
				$pid = M('Menu')->where(array(
					'url' => 'User/auth',
					'pid' => array('neq', 0)
					))->getField('id');

				if (!$list) {
					M('Menu')->add(array('url' => 'User/authStart', 'title' => 'Permission to re-initialize', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => 'user', 'ico_name' => 'home'));
				}
				else {
					M('Menu')->where(array(
						'url' => 'User/authStart',
						'pid' => array('neq', 0)
						))->save(array('title' => 'Permission to re-initialize', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => 'user', 'ico_name' => 'home'));
				}
			}

			$list = M('Menu')->where(array(
				'url' => 'User/authAccess',
				'pid' => array('neq', 0)
				))->select();

			if ($list[1]) {
				M('Menu')->where(array('id' => $list[1]['id']))->delete();
			}
			else {
				$pid = M('Menu')->where(array(
					'url' => 'User/auth',
					'pid' => array('neq', 0)
					))->getField('id');

				if (!$list) {
					M('Menu')->add(array('url' => 'User/authAccess', 'title' => 'Access authorization', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => 'user', 'ico_name' => 'home'));
				}
				else {
					M('Menu')->where(array(
						'url' => 'User/authAccess',
						'pid' => array('neq', 0)
						))->save(array('title' => 'Access authorization', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => 'user', 'ico_name' => 'home'));
				}
			}

			$list = M('Menu')->where(array(
				'url' => 'User/authAccessUp',
				'pid' => array('neq', 0)
				))->select();

			if ($list[1]) {
				M('Menu')->where(array('id' => $list[1]['id']))->delete();
			}
			else {
				$pid = M('Menu')->where(array(
					'url' => 'User/auth',
					'pid' => array('neq', 0)
					))->getField('id');

				if (!$list) {
					M('Menu')->add(array('url' => 'User/authAccessUp', 'title' => 'Access unauthorized modification', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => 'user', 'ico_name' => 'home'));
				}
				else {
					M('Menu')->where(array(
						'url' => 'User/authAccessUp',
						'pid' => array('neq', 0)
						))->save(array('title' => 'Access unauthorized modification', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => 'user', 'ico_name' => 'home'));
				}
			}

			$list = M('Menu')->where(array(
				'url' => 'User/authUser',
				'pid' => array('neq', 0)
				))->select();

			if ($list[1]) {
				M('Menu')->where(array('id' => $list[1]['id']))->delete();
			}
			else {
				$pid = M('Menu')->where(array(
					'url' => 'User/auth',
					'pid' => array('neq', 0)
					))->getField('id');

				if (!$list) {
					M('Menu')->add(array('url' => 'User/authUser', 'title' => 'Members of the authorized', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => 'user', 'ico_name' => 'home'));
				}
				else {
					M('Menu')->where(array(
						'url' => 'User/authUser',
						'pid' => array('neq', 0)
						))->save(array('title' => 'Members of the authorized', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => 'user', 'ico_name' => 'home'));
				}
			}

			$list = M('Menu')->where(array(
				'url' => 'User/authUserAdd',
				'pid' => array('neq', 0)
				))->select();

			if ($list[1]) {
				M('Menu')->where(array('id' => $list[1]['id']))->delete();
			}
			else {
				$pid = M('Menu')->where(array(
					'url' => 'User/auth',
					'pid' => array('neq', 0)
					))->getField('id');

				if (!$list) {
					M('Menu')->add(array('url' => 'User/authUserAdd', 'title' => 'Members of the authorized increase', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => 'user', 'ico_name' => 'home'));
				}
				else {
					M('Menu')->where(array(
						'url' => 'User/authUserAdd',
						'pid' => array('neq', 0)
						))->save(array('title' => 'Members of the authorized increase', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => 'user', 'ico_name' => 'home'));
				}
			}

			$list = M('Menu')->where(array(
				'url' => 'User/authUserRemove',
				'pid' => array('neq', 0)
				))->select();

			if ($list[1]) {
				M('Menu')->where(array('id' => $list[1]['id']))->delete();
			}
			else {
				$pid = M('Menu')->where(array(
					'url' => 'User/auth',
					'pid' => array('neq', 0)
					))->getField('id');

				if (!$list) {
					M('Menu')->add(array('url' => 'User/authUserRemove', 'title' => 'Members of the authorized lifted', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => 'user', 'ico_name' => 'home'));
				}
				else {
					M('Menu')->where(array(
						'url' => 'User/authUserRemove',
						'pid' => array('neq', 0)
						))->save(array('title' => 'Members of the authorized lifted', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => 'user', 'ico_name' => 'home'));
				}
			}

			$list = M('Menu')->where(array(
				'url' => 'User/logEdit',
				'pid' => array('neq', 0)
				))->select();

			if ($list[1]) {
				M('Menu')->where(array('id' => $list[1]['id']))->delete();
			}
			else {
				$pid = M('Menu')->where(array(
					'url' => 'User/log',
					'pid' => array('neq', 0)
					))->getField('id');

				if (!$list) {
					M('Menu')->add(array('url' => 'User/logEdit', 'title' => 'Edit Add', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => 'user', 'ico_name' => 'home'));
				}
				else {
					M('Menu')->where(array(
						'url' => 'User/logEdit',
						'pid' => array('neq', 0)
						))->save(array('title' => 'Edit Add', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => 'user', 'ico_name' => 'home'));
				}
			}

			$list = M('Menu')->where(array(
				'url' => 'User/logStatus',
				'pid' => array('neq', 0)
				))->select();

			if ($list[1]) {
				M('Menu')->where(array('id' => $list[1]['id']))->delete();
			}
			else {
				$pid = M('Menu')->where(array(
					'url' => 'User/log',
					'pid' => array('neq', 0)
					))->getField('id');

				if (!$list) {
					M('Menu')->add(array('url' => 'User/logStatus', 'title' => 'Modify status', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => 'user', 'ico_name' => 'home'));
				}
				else {
					M('Menu')->where(array(
						'url' => 'User/logStatus',
						'pid' => array('neq', 0)
						))->save(array('title' => 'Modify status', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => 'user', 'ico_name' => 'home'));
				}
			}

			$list = M('Menu')->where(array(
				'url' => 'User/walletEdit',
				'pid' => array('neq', 0)
				))->select();

			if ($list[1]) {
				M('Menu')->where(array('id' => $list[1]['id']))->delete();
			}
			else {
				$pid = M('Menu')->where(array(
					'url' => 'User/wallet',
					'pid' => array('neq', 0)
					))->getField('id');

				if (!$list) {
					M('Menu')->add(array('url' => 'User/walletEdit', 'title' => 'Edit Add', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => 'user', 'ico_name' => 'home'));
				}
				else {
					M('Menu')->where(array(
						'url' => 'User/walletEdit',
						'pid' => array('neq', 0)
						))->save(array('title' => 'Edit Add', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => 'user', 'ico_name' => 'home'));
				}
			}

			$list = M('Menu')->where(array(
				'url' => 'User/walletStatus',
				'pid' => array('neq', 0)
				))->select();

			if ($list[1]) {
				M('Menu')->where(array('id' => $list[1]['id']))->delete();
			}
			else {
				$pid = M('Menu')->where(array(
					'url' => 'User/wallet',
					'pid' => array('neq', 0)
					))->getField('id');

				if (!$list) {
					M('Menu')->add(array('url' => 'User/walletStatus', 'title' => 'Modify status', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => 'user', 'ico_name' => 'home'));
				}
				else {
					M('Menu')->where(array(
						'url' => 'User/walletStatus',
						'pid' => array('neq', 0)
						))->save(array('title' => 'Modify status', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => 'user', 'ico_name' => 'home'));
				}
			}

			$list = M('Menu')->where(array(
				'url' => 'User/bankEdit',
				'pid' => array('neq', 0)
				))->select();

			if ($list[1]) {
				M('Menu')->where(array('id' => $list[1]['id']))->delete();
			}
			else {
				$pid = M('Menu')->where(array(
					'url' => 'User/bank',
					'pid' => array('neq', 0)
					))->getField('id');

				if (!$list) {
					M('Menu')->add(array('url' => 'User/bankEdit', 'title' => 'Edit Add', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => 'user', 'ico_name' => 'home'));
				}
				else {
					M('Menu')->where(array(
						'url' => 'User/bankEdit',
						'pid' => array('neq', 0)
						))->save(array('title' => 'Edit Add', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => 'user', 'ico_name' => 'home'));
				}
			}

			$list = M('Menu')->where(array(
				'url' => 'User/bankStatus',
				'pid' => array('neq', 0)
				))->select();

			if ($list[1]) {
				M('Menu')->where(array('id' => $list[1]['id']))->delete();
			}
			else {
				$pid = M('Menu')->where(array(
					'url' => 'User/bank',
					'pid' => array('neq', 0)
					))->getField('id');

				if (!$list) {
					M('Menu')->add(array('url' => 'User/bankStatus', 'title' => 'Modify status', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => 'user', 'ico_name' => 'home'));
				}
				else {
					M('Menu')->where(array(
						'url' => 'User/bankStatus',
						'pid' => array('neq', 0)
						))->save(array('title' => 'Modify status', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => 'user', 'ico_name' => 'home'));
				}
			}

			$list = M('Menu')->where(array(
				'url' => 'User/coinEdit',
				'pid' => array('neq', 0)
				))->select();

			if ($list[1]) {
				M('Menu')->where(array('id' => $list[1]['id']))->delete();
			}
			else {
				$pid = M('Menu')->where(array(
					'url' => 'User/coin',
					'pid' => array('neq', 0)
					))->getField('id');

				if (!$list) {
					M('Menu')->add(array('url' => 'User/coinEdit', 'title' => 'Edit Add', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => 'user', 'ico_name' => 'home'));
				}
				else {
					M('Menu')->where(array(
						'url' => 'User/coinEdit',
						'pid' => array('neq', 0)
						))->save(array('title' => 'Edit Add', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => 'user', 'ico_name' => 'home'));
				}
			}

			$list = M('Menu')->where(array(
				'url' => 'User/coinLog',
				'pid' => array('neq', 0)
				))->select();

			if ($list[1]) {
				M('Menu')->where(array('id' => $list[1]['id']))->delete();
			}
			else {
				$pid = M('Menu')->where(array(
					'url' => 'User/coin',
					'pid' => array('neq', 0)
					))->getField('id');

				if (!$list) {
					M('Menu')->add(array('url' => 'User/coinLog', 'title' => 'Property statistics', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => 'user', 'ico_name' => 'home'));
				}
				else {
					M('Menu')->where(array(
						'url' => 'User/coinLog',
						'pid' => array('neq', 0)
						))->save(array('title' => 'Property statistics', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => 'user', 'ico_name' => 'home'));
				}
			}

			$list = M('Menu')->where(array(
				'url' => 'User/goodsEdit',
				'pid' => array('neq', 0)
				))->select();

			if ($list[1]) {
				M('Menu')->where(array('id' => $list[1]['id']))->delete();
			}
			else {
				$pid = M('Menu')->where(array(
					'url' => 'User/goods',
					'pid' => array('neq', 0)
					))->getField('id');

				if (!$list) {
					M('Menu')->add(array('url' => 'User/goodsEdit', 'title' => 'Edit Add', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => 'user', 'ico_name' => 'home'));
				}
				else {
					M('Menu')->where(array(
						'url' => 'User/goodsEdit',
						'pid' => array('neq', 0)
						))->save(array('title' => 'Edit Add', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => 'user', 'ico_name' => 'home'));
				}
			}

			$list = M('Menu')->where(array(
				'url' => 'User/goodsStatus',
				'pid' => array('neq', 0)
				))->select();

			if ($list[1]) {
				M('Menu')->where(array('id' => $list[1]['id']))->delete();
			}
			else {
				$pid = M('Menu')->where(array(
					'url' => 'User/goods',
					'pid' => array('neq', 0)
					))->getField('id');

				if (!$list) {
					M('Menu')->add(array('url' => 'User/goodsStatus', 'title' => 'Modify status', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => 'user', 'ico_name' => 'home'));
				}
				else {
					M('Menu')->where(array(
						'url' => 'User/goodsStatus',
						'pid' => array('neq', 0)
						))->save(array('title' => 'Modify status', 'pid' => $pid, 'sort' => 1, 'hide' => 1, 'group' => 'user', 'ico_name' => 'home'));
				}
			}

			$list = M('Menu')->where(array(
				'url' => 'User/setpwd',
				'pid' => array('neq', 0)
				))->select();

			if ($list[1]) {
				M('Menu')->where(array('id' => $list[1]['id']))->delete();
			}
			else if (!$list) {
				M('Menu')->add(array('url' => 'User/setpwd', 'title' => 'Change the administrator password', 'pid' => 3, 'sort' => 0, 'hide' => 1, 'group' => 'user', 'ico_name' => 'home'));
			}
			else {
				M('Menu')->where(array(
					'url' => 'User/setpwd',
					'pid' => array('neq', 0)
					))->save(array('title' => 'Change the administrator password', 'pid' => 3, 'sort' => 0, 'hide' => 1, 'group' => 'user', 'ico_name' => 'home'));
			}

			if (M('Menu')->where(array('url' => 'AuthManager/index'))->delete()) {
				M('AuthRule')->where(array('status' => 1))->delete();
			}

			if (M('Menu')->where(array('url' => 'User/adminUser'))->delete()) {
				M('AuthRule')->where(array('status' => 1))->delete();
			}

			if (M('Menu')->where(array('url' => 'AdminUser/index'))->delete()) {
				M('AuthRule')->where(array('status' => 1))->delete();
			}

			if (M('Menu')->where(array('url' => 'Userlog/index'))->delete()) {
				M('AuthRule')->where(array('status' => 1))->delete();
			}

			if (M('Menu')->where(array('url' => 'Userwallet/index'))->delete()) {
				M('AuthRule')->where(array('status' => 1))->delete();
			}

			if (M('Menu')->where(array('url' => 'Userbank/index'))->delete()) {
				M('AuthRule')->where(array('status' => 1))->delete();
			}

			if (M('Menu')->where(array('url' => 'Usercoin/index'))->delete()) {
				M('AuthRule')->where(array('status' => 1))->delete();
			}

			if (M('Menu')->where(array('url' => 'Usergoods/index'))->delete()) {
				M('AuthRule')->where(array('status' => 1))->delete();
			}

			S(MODULE_NAME . CONTROLLER_NAME . 'checkUpdata', 1);
		}
	}
}

?>