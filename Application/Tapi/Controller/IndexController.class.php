<?php

namespace Tapi\Controller;

class IndexController extends CommonController
{
    public function index()
    {
		echo $this->userid();
        $array = array('status' => 1, 'message' => 'connected to Index API');
        echo json_encode($array);
    }
	public function latestnews(){
		//codono_text name=index_info  get content
		$News = M('Text')->where(array('name' => 'game_bazaar','status'=>1))->field(array('name','title','content'))->find();
		
   $this->ajaxShow($News);		
		
		
	}

    public function up()
    {
		//Not required to run Can only be run once !
        $User = M('User')->getDbFields(); 
		if (!in_array('token', $User)) {
            echo 'add token field to user table;';
            $end = end($User);
            M()->execute('ALTER TABLE `codono_user` ADD COLUMN `token` VARCHAR(50) NULL AFTER `' . $end . '`');
        } else {
            echo 'Token Field Exists';
        }
    }

    public function initinfo()
    {
        $info = array();
        $info['WITHDRAW_NOTICE'] = L('YOUR_WITHDRAWAL_MESSAGE');
        $info['CHARGE_NOTICE'] = L('YOUR_RECHARGE_MESSAGE');
        $info['WEB_NAME'] = C('WEB_NAME');
        $info['WEB_TITLE'] = C('WEB_TITLE');
        $info['WEB_ICP'] = C('WEB_ICP');
        $info['INDEX_IMG'] = '';
        $News = M('Article')->where(array('type' => 'news'))->select();

        foreach ($News as $val) {
            $title = (50 < mb_strlen($val['title']) ? mb_substr($val['title'], 0, 50, 'utf-8') . '...' : $val['title']);
            $info['News'][] = array('id' => $val['id'], 'title' => $title);
        }

        $info['charge_account'] = array(
            'alipay' => array('bank' => 'Alipay', 'name' => "\t" . 'Account Name Here', 'card_num' => "\t" . '123456@alipay.com'),
            'bank' => array('bank' => 'Bank of China', 'name' => "\t" . 'Account Name Here', 'card_num' => '8888 8888 8888')
        );
        $myczType = M('MyczType')->where(array('status' => 1))->select();

        foreach ($myczType as $k => $v) {
            $myczTypeList[] = array('type' => $v['name'], 'title' => $v['title']);
        }

        $info['myczTypeList'] = $myczTypeList;
        $this->ajaxShow($info);
    }

    public function marketInfo()
    {

        $info = array();
        foreach (C('market') as $val) {

            $info['market'][] = array('id' => $val['id'], 'ticker' =>$val['name'],'fee_buy' =>format_num($val['fee_buy']),'fee_sell' =>format_num($val['fee_sell']),'name' => $val['title'], 'icon' => SITE_URL. '/Upload/coin/' . $val['xnbimg'], 'new_price' =>format_num( $val['new_price']) , 'buy_price' => format_num($val['buy_price']) , 'sell_price' => format_num($val['sell_price']) , 'min_price' => format_num($val['min_price']) , 'max_price' => format_num($val['max_price']) , 'change' => round($val['change'] , 2) , 'volume' => $val['volume'] );
        }
        $this->ajaxShow($info);
    }
	 public function singlemarketInfo()
    {

    $ticker = I('get.market', '', 'text');	
    $info = array();
        foreach (C('market') as $val) {
		if($val['name']!=$ticker){continue;}
            $info = array('id' => $val['id'], 'ticker' =>$val['name'],'fee_buy' =>$val['fee_buy'],'fee_sell' =>$val['fee_sell'],'name' => $val['title'], 'icon' => SITE_URL.'/Upload/coin/' . $val['xnbimg'], 'new_price' => $val['new_price'] , 'buy_price' => $val['buy_price'] , 'sell_price' => $val['sell_price'] , 'min_price' => $val['min_price'] , 'max_price' => $val['max_price'] , 'change' => round($val['change'] , 2) * (1 == rand(0, 1) ? 1 : -1), 'volume' => $val['volume'] );
        }
        $this->ajaxShow($info);
    }
	public function singlecoin(){
	$id = I('post.id', '', 'text');	
	$info = array();
		foreach (C('market') as $val) 
		{
			var_dump($val);
			if($val['rmb']!='usd'){continue;}
			if($val['id']!=$id){continue;}
        		$info = 
		array(
		'id'=>$val['id'],
		'name'=>$val['xnb'],
		'icon'=> SITE_URL. 'Upload/coin/' . $val['xnbimg'],
		'price_'.$val['rmb']=>(double)$val['new_price'] ,
		'fee_buy' =>$val['fee_buy'],
		'fee_sell' =>$val['fee_sell'],
		'new_price' => (double)$val['new_price'] ,
		'buy_price' => (double)$val['buy_price'] ,
		'sell_price' => (double)$val['sell_price'] ,
		'min_price' => (double)$val['min_price'] ,
		'max_price' => (double)$val['max_price'] ,
		'percent_change_24h'=>(double)round($val['change'] , 2),
		'24h_volume_'.$val['rmb'] => (double)$val['volume'] );
        }
        $this->ajaxShow($info);
		
	}
	public function ticker(){
		 $info = array();
		 /*
		 echo "<pre>";
		 var_dump(C('market'));
		 echo "</pre>";
		 */
        foreach (C('market') as $val) {
			if($val['rmb']!='usd'){continue;}
        		$info[] = 
		array(
		'id'=>$val['id'],
		'name'=>$val['xnb'],
		'icon'=> SITE_URL. 'Upload/coin/' . $val['xnbimg'],
		'price_'.$val['rmb']=>(double)$val['new_price'] ,
		'new_price' => (double)$val['new_price'] ,
		'buy_price' => (double)$val['buy_price'] ,
		'sell_price' => (double)$val['sell_price'] ,
		'min_price' => (double)$val['min_price'] ,
		'max_price' => (double)$val['max_price'] ,
		'percent_change_24h'=>(double)round($val['change'] , 2),
		'24h_volume_'.$val['rmb'] => (double)$val['volume'] );
        }
        $this->ajaxShow($info);
		
	}
	public function plainticker(){
		 $info = array();
		 /*
		 echo "<pre>";
		 var_dump(C('market'));
		 echo "</pre>";
		 */
        foreach (C('market') as $val) {
			if($val['rmb']!='usd'){continue;}
        		$info[] = 
		array(
		'id'=>$val['id'],
		'name'=>$val['xnb'],
		'icon'=> SITE_URL. 'Upload/coin/' . $val['xnbimg'],
		'price_'.$val['rmb']=>(double)$val['new_price'] ,
		'new_price' => (double)$val['new_price'] ,
		'buy_price' => (double)$val['buy_price'] ,
		'sell_price' => (double)$val['sell_price'] ,
		'min_price' => (double)$val['min_price'] ,
		'max_price' => (double)$val['max_price'] ,
		'percent_change_24h'=>(double)round($val['change'] , 2),
		'24h_volume_'.$val['rmb'] => (double)$val['volume'] );
        }
        //$this->ajaxShow($info);
 header('Content-type: application/json');
 echo(json_encode($info));exit;
		
	}
}

?>