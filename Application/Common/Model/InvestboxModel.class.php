<?php

namespace Common\Model;

class InvestboxModel extends \Think\Model
{
    protected $keyS = 'Invest';
	private function investinfo($id){
		
		$investbox = M('investbox')->where(array('id'=>$id))->find();
		return $investbox;
	}
    public function withdraw($id = NULL)
    {
        if (!check($id, 'd')) {
            return array('0', 'Parameter error');
        }
        $ibl = M('InvestboxLog')->where(array('id' => $id,'userid'=>userid()))->find();
		$userid=userid();
        if (!$ibl) {
            return array('0', 'Investment does not exist');
        }

        if ($ibl['status'] != 1) {
            return array('0', 'Investment can not be undone');
        }
		$mo = M();
        $rs = array();
	
         if ($ibl['status'] == 1) {
            $refund = format_num($ibl['amount'], 8);

			$invest_info=$this->investinfo($ibl['boxid']);
			 if(!$invest_info)
			 {
				 return array('0', 'No such investment plan exists!');
			 }
			$coinname=strval(strtolower($invest_info['coinname']));
			$coinnamed=strval(strtolower($invest_info['coinname'].'d'));
			
             $query="SELECT `$coinname`,`$coinnamed` FROM `codono_user_coin` WHERE `userid` = $userid";
             $res_bal=$mo->query($query);
             $user_coin_bal = $res_bal[0];


            $mum_a=bcsub($user_coin_bal[$coinname],$refund,8);
			$mum_b=$user_coin_bal[$coinnamed];
			$num=bcadd($user_coin_bal[$coinname],$user_coin_bal[$coinnamed],8);
			$mum=bcadd($num,$refund,8);
			 
		$mo->execute('set autocommit=0');
        $mo->execute('lock tables codono_user_coin write  , codono_investbox_log write ,codono_finance write');
            if (0 < $refund) {
                $rs[] = $mo->table('codono_user_coin')->where(array('userid' => $ibl['userid']))->setInc($coinname, $refund);
            }
            $move_stamp='0_'.$ibl['docid'];
            $rs[] = $mo->table('codono_investbox_log')->where(array('id' => $ibl['id']))->save(array('status'=> 0,'withdrawn'=>time(),'credited'=>$refund));
            $rs[] = $mo->table('codono_finance')->add(array('userid' => userid(), 'coinname' => $coinname, 'num_a' => $user_coin_bal[$coinname], 'num_b' => $refund, 'num' => $num , 'fee' => $refund, 'type' => 2, 'name' => 'investbox', 'nameid' => $ibl['id'], 'remark' => 'InvestBoxInvest',  'move' => $move_stamp, 'addtime' => time(), 'status' => 1,'mum'=>$mum,'mum_a'=>$mum_a,'mum_b'=>$mum_b));
        } else {
            $mo->execute('rollback');
            return array('0', 'Invalid status of investment !');
        }

        if (check_arr($rs)) {
            $mo->execute('commit');
            $mo->execute('unlock tables');
            return array('1', 'Investment has been withdrawn!');
        } else {
            $mo->execute('rollback');
            $mo->execute('unlock tables');
            return array('0', 'Investment coult not be withdrawn!|' . implode('|', $rs));
        }
    }

}

?>