<?php

namespace Admin\Controller;

class BankController extends AdminController
{
    public function index()
    {

        $UserBankType = M('UserBankType')->order('id desc')->select();
		$builder = new BuilderList();
        
		
		 $builder->title('Banks');
	$builder->titleList('Banks List', U('Bank/index'));
	$builder->button('add', 'Add', U('Bank/edit'));
	$builder->setSearchPostUrl(U('Bank/index'));
	$builder->search('order', 'select', array('id_desc' => 'ID desc', 'id_asc' => 'ID asc'));
	$builder->search('status', 'select', array('All Status', 'Disabled', 'Enabled'));
	$builder->search('type', 'select', $UserBankType);
	$builder->search('field', 'select', array('name' => 'Name'));
	$builder->search('name', 'text', 'Enter search content');
    $builder->keyText('id', 'id');
        $builder->keyText('name', 'Name');
        $builder->keyText('title', 'Title');
		$builder->keyText('url', 'URL');
        $builder->keyText('sort', 'Sort');
        $builder->keyStatus('status', 'Status', array('Disabled', 'Enabled'));
        $builder->keyDoAction('Bank/edit?id=###', 'Edit', 'Option');
        $builder->data($UserBankType);
        $builder->pagination($count, $r, $parameter);
        $builder->display();
    }

    public function edit($id = NULL)
    {
        if (!empty($_POST)) {

            if (APP_DEMO) {
                $this->error(L('SYSTEM_IN_DEMO_MODE'));
            }

            if (!check($_POST['name'], 'a')) {
            }
			if (!check($_POST['title'], 'a')) {
            }

            if (!check($_POST['sort'], 'd')) {
                $this->error('Sort malformed');
            }

            if (check($_POST['id'], 'd')) {
                $rs = M('UserBankType')->save($_POST);
            } else {
                $rs = M('UserBankType')->add($_POST);
            }
			
            if ($rs) {
                $this->success('Successful operation');
            } else {
                $this->error('operation failed');
            }
        } else {
            $builder = new BuilderEdit();
            $builder->title('Banks');
            $builder->titleList('Banks List', U('Bank/Index'));

            if ($id) {
                $builder->keyReadOnly('id', 'ID');
                $builder->keyHidden('id', 'Bank ID');
                $data = M('UserBankType')->where(array('id' => $id))->find();
                $data['addtime'] = addtime($data['addtime']);
                $data['endtime'] = addtime($data['endtime']);
                $builder->data($data);
            }

            $builder->keyText('name', 'Bank Name', 'Bank Name');
			$builder->keyText('title', 'Bank title', 'Bank title');
			$builder->keyText('url', 'Bank URL', 'Bank URL');
			$builder->keySelect('type', 'Type', 'Bank Or Crypto', array('crypto', 'bank'));
            $builder->keyText('sort', 'Sort');
            $builder->keyStatus();
            $builder->savePostUrl(U('Bank/edit'));
            $builder->display();
        }
    }

}

?>