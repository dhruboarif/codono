<?php

namespace Admin\Controller;

class InvestController extends AdminController
{
     public function index($p = 1, $r = 15, $str_addtime = '', $end_addtime = '', $order = '', $status = '', $type = '', $field = '', $name = '',$coinname='')
    {
		$parameter['p'] = $p;
        $parameter['status'] = $status;
        $parameter['order'] = $order;
        $parameter['type'] = $type;
        $parameter['name'] = $name;
		
		$map = array();
		        if (empty($order)) {
            $order = 'id_desc';
        }

        $order_arr = explode('_', $order);
        if ($status) {
            $map['status'] = $status;
        }
        if (count($order_arr) != 2) {
            $order = 'id_desc';
            $order_arr = explode('_', $order);
        }
		 if ($field && $name) {
                $map[$field] = $name;
        }
		if ($coinname) {
                $map['coinname'] = $coinname;
        }
		if ($status) {
                $map['status'] = $status;
        }
        $order_set = $order_arr[0] . ' ' . $order_arr[1];

		$data = M('Investbox')->where($map)->order($order_set)->select();
        $count = M('Investbox')->where($map)->order($order_set)->count();
        $builder = new BuilderList();
        $builder->title('Invest Box:');
        $builder->titleList('Investments', U('Invest/list'));
        $builder->button('add', 'Add', U('Invest/edit'));
        $builder->keyId();
        $builder->keyText('coinname', 'Coin');
        $builder->keyText('percentage', '%');
        $builder->keyText('period', 'Period in days');
        $builder->keyText('minvest', 'Minvest');
        $builder->keyText('maxvest', 'Maxvest');
        $builder->keyText('creatorid', 'creatorid');

        $builder->keyStatus('status', 'Status', array('Submitted', 'Approved', 'Reject', 'Completed','Upcomings'));
		$coinname_arr = array('' => 'Coin');
        $coinname_arr = array_merge($coinname_arr, D('Coin')->get_all_name_list());
        $builder->search('coinname', 'select', $coinname_arr);
        $builder->search('field', 'select', array( 'creatorid' => 'creatorid'));
        $builder->search('status', 'select',  array('Submitted', 'Approved', 'Reject', 'Completed','Upcomings'));
        $builder->search('name', 'text', 'Enter search content');
        $builder->keyDoAction('Invest/edit?id=###', 'Edit', 'Option');
        $builder->data($data);
        $builder->pagination($count, $r, $parameter);
        $builder->display();
    }
	public function dicerolls($p = 1, $r = 15, $str_addtime = '', $end_addtime = '', $order = '', $status = '', $type = '', $field = '', $name = '',$coinname='')
    {
		$parameter['p'] = $p;
        $parameter['status'] = $status;
        $parameter['order'] = $order;
        $parameter['type'] = $type;
        $parameter['name'] = $name;
		
		$map = array();
		        if (empty($order)) {
            $order = 'id_desc';
        }

        $order_arr = explode('_', $order);

        if (count($order_arr) != 2) {
            $order = 'id_desc';
            $order_arr = explode('_', $order);
        }
		 if ($field && $name) {
                $map[$field] = $name;
        }
		if ($coinname) {
                $map['coinname'] = $coinname;
        }
		if ($status) {
                $map['status'] = $status;
        }
        $order_set = $order_arr[0] . ' ' . $order_arr[1];

		$data = M('Dice')->where($map)->order($order_set)->select();
		$count = M('Dice')->where($map)->count();
         $builder = new BuilderList();
        $builder->title('Dice Rolls : Status >>> 1=win,2=lost');
        $builder->titleList('DiceRolls', U('Invest/dicerolls'));
        $builder->keyId();
        $builder->keyText('coinname', 'Coin');
        $builder->keyText('call', 'call');
        $builder->keyText('number', 'Number');
		$builder->keyText('userid', 'Userid');

        $builder->keyStatus('result', 'result', array('NA', 'Won', 'Lost'));
        $builder->keyText('amount', 'Amount');
        $builder->keyText('winamount', 'winamount');
		$builder->keyText('addtime', 'addtime');
        $builder->search('field', 'select', array('result' => 'result', 'userid' => 'userid'));
        $builder->search('name', 'text', 'Enter search content');
        $builder->data($data);
        $builder->pagination($count, $r, $parameter);
        $builder->display();
    }

    public function investlist($p = 1, $r = 15, $str_addtime = '', $end_addtime = '', $order = '', $status = '', $type = '', $field = '', $name = '')
    {
		$map = array();
		        if (empty($order)) {
            $order = 'id_desc';
        }

        $order_arr = explode('_', $order);

        if (count($order_arr) != 2) {
            $order = 'id_desc';
            $order_arr = explode('_', $order);
        }

        $order_set = $order_arr[0] . ' ' . $order_arr[1];
		$parameter['p'] = $p;
        $parameter['status'] = $status;
        $parameter['order'] = $order;
        $parameter['type'] = $type;
        $parameter['name'] = $name;
		
		$data = M('InvestboxLog')->where($map)->order($order_set)->select();
        $count = M('InvestboxLog')->where($map)->count();
		$builder = new BuilderList();
        $builder->title('Investments: Status');
        $builder->titleList('InvestBoxs', U('Invest/Index'));
        $builder->keyId();
        $builder->keyText('boxid', 'Boxid');
        $builder->keyText('docid', 'Docid');
        $builder->keyText('amount', 'amount');
        $builder->keyText('begintime', 'Begin');
		$builder->keyText('endtime', 'End');
        $builder->keyText('withdrawn', 'Withdrawn');
        $builder->keyText('maturity', 'Maturity');
		$builder->keyText('credited', 'Credited');
		$builder->keyText('userid', 'Userid');
        $builder->keyStatus('status', 'Status', array('Premature Withdrawn', 'Active', 'Reject', 'Completed'));
        $builder->data($data);
        $builder->pagination($count, $r, $parameter);
        $builder->display();
    }

    public function edit($id = NULL)
    {
        if (!empty($_POST)) {
            
            if(!$_POST['id']){
                $action['coin'] = array('name' => $_POST['actionxcoinname'], 'value' => $_POST['actionxcoinvalue']);
                $action['market'] = array('name' => $_POST['actionxmarketname'], 'buy' => $_POST['actionxmarketbuy'], 'sell' => $_POST['actionxmarketsell']);
                $array = array(
                    'coinname' => $_POST['coinname'],
                    'percentage' => $_POST['percentage'],
                    'period' => $_POST['period'],
                    'minvest' => $_POST['minvest'],
                    'maxvest' => $_POST['maxvest'],
                    'creatorid' => $_POST['creatorid'],
                    'status' => $_POST['status'],
                    'action' => (string)json_encode($action),
                );

                $rs = M('Investbox')->add($array);

            }else {
                $action['coin'] = array('name' => $_POST['actionxcoinname'], 'value' => $_POST['actionxcoinvalue']);
                $action['market'] = array('name' => $_POST['actionxmarketname'], 'buy' => $_POST['actionxmarketbuy'], 'sell' => $_POST['actionxmarketsell']);
                $array = array(
                    'id' => $_POST['id'],
                    'coinname' => $_POST['coinname'],
                    'percentage' => $_POST['percentage'],
                    'period' => $_POST['period'],
                    'minvest' => $_POST['minvest'],
                    'maxvest' => $_POST['maxvest'],
                    'creatorid' => $_POST['creatorid'],
                    'status' => $_POST['status'],
                    'action' => (string)json_encode($action),
                );

                $rs = M('Investbox')->save($array);
            }

            if ($rs) {
				S('investbox_list', NULL);
                $this->success('Successful operation');
            } else {
				$this->error('No changes were made !!');
            }
        } else {

            if ($id) {
                $data = M('Investbox')->where(array('id' => $id))->find();
				$this->assign($data);
                $action=json_decode($data['action']);
                $actionx['coin']['name']=$action->coin->name;
                $actionx['coin']['value']=$action->coin->value;
                $actionx['market']['name']=$action->market->name;
                $actionx['market']['buy']=$action->market->buy;
                $actionx['market']['sell']=$action->market->sell;

                $this->assign('actionx',$actionx);
            }
            $coin_list = D('Coin')->get_all_name_list();
			$status_array=array('0'=>'Submitted','1'=>'Approved','2'=>'Reject','3'=>'Completed','4'=>'Upcoming');

			$this->assign($coin_list);
			$this->assign($status_array);
            $this->display();
        }
    }


}

?>