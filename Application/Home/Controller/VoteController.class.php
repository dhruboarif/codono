<?php

namespace Home\Controller;

class VoteController extends HomeController
{
	const DOWN_VOTE_ALLOWED = 1;  // Maxmium allowed invest box by a creator
    public function index()
    {


        $coin_list = M('VoteType')->select();
		$list=array();
        if (is_array($coin_list)) {
            foreach ($coin_list as $k => $v) {
                $vv = $v;



                $list[$vv['coinname']]['name'] = $vv['coinname'];
                $list[$vv['coinname']]['title'] = $vv['title'];
                $list[$vv['coinname']]['zhichi'] = M('Vote')->where(array('coinname' => $vv['coinname'], 'type' => 1))->count() + $vv['zhichi'];
                $list[$vv['coinname']]['fandui'] = M('Vote')->where(array('coinname' => $vv['coinname'], 'type' => 2))->count() + $vv['fandui'];
                $list[$vv['coinname']]['zongji'] = $list[$vv['coinname']]['zhichi'] - $list[$vv['coinname']]['fandui'];
                $list[$vv['coinname']]['bili'] = round(($list[$vv['coinname']]['zhichi'] / $list[$vv['coinname']]['zongji']) * 100, 2);
                $list[$vv['coinname']]['votecoin'] = C('coin')[$vv['votecoin']]['title'];
                $list[$vv['coinname']]['assumnum'] = $vv['assumnum'];
				$list[$vv['coinname']]['img'] = $vv['img'];
                $list[$vv['coinname']]['id'] = $vv['id'];
            }


            $sort = array(
                'direction' => 'SORT_DESC',
                'field' => 'zongji',
            );
            $arrSort = array();
            foreach ($list AS $uniqid => $row) {
                foreach ($row AS $key => $value) {
                    $arrSort[$key][$uniqid] = $value;
                }
            }


            if ($sort['direction']) {
                array_multisort($arrSort[$sort['field']], constant($sort['direction']), $list);
            }


            $this->assign('list', $list);
        }
		$this->assign('is_down_vote_allowed',self::DOWN_VOTE_ALLOWED);
        $this->assign('prompt_text', D('Text')->get_content('game_vote'));
        $this->display();
    }

    public function up($type = NULL, $coinname = NULL, $votecoin = NULL, $id = 0)
    {
        if (!userid()) {
            $this->error(L('PLEASE_LOGIN'));
        }
		if($type==2 && self::DOWN_VOTE_ALLOWED==0){
	        $this->error(L('You can not down vote'));
		}
        if (($type != 1) && ($type != 2)) {
            $this->error(L('INCORRECT_REQ'));
        }

        if (!is_array(D('Coin')->get_all_name_list())) {
            $this->error('Parameter error2!');
        }

        $curVote = M('VoteType')->where(array('coinname' => $coinname, 'id' => $id))->find();

        if ($curVote) {
            $curUserB = M('UserCoin')->where(array('userid' => userid()))->getField($curVote['votecoin']);

            //var_dump($curUserB);

            if (floatval($curUserB) < floatval($curVote['assumnum'])) {
                $this->error(L('Polling stations needed') . $votecoin . L('Insufficient number'));
            }

        } else {
            $this->error(L('Voting type does not exist'));
        }
        //$this->error(L('testing'));
        //if (M('Vote')->where(array('userid' => userid(), 'coinname' => $coinname))->find()) {
        //$this->error(L('You have voted before, can not be operated again!'));
        //}


        if (1 > 3) {
            //$this->error(L('You have voted before, can not be operated again!'));
        } //else if(1==1) {
        else if (M('Vote')->add(array('userid' => userid(), 'coinname' => $coinname, 'title' => $curVote['title'], 'type' => $type, 'addtime' => time(), 'status' => 1))) {
//            $zhichi = M('Vote')->where(array('coinname' => $coinname, 'type' => 1))->count();
//            $fandui = M('Vote')->where(array('coinname' => $coinname, 'type' => 2))->count();
//            $meta = array(
//                'zhichi' => $zhichi,
//                'fandui' => $fandui,
//                'zongji' => $zhichi + $fandui,
//                'bili' => round(($zhichi / $fandui) * 100, 2),
//            );
//            M('VoteType')->where(array('coinname' => $coinname))->save($meta);

            M('UserCoin')->where(array('userid' => userid()))->setDec($curVote['votecoin'], $curVote['assumnum']);


            $this->success(L('Voting success!'));
        } else {
            $this->error(L('Voting failed!'));
        }
    }

    public function uninstall()
    {

    }
	public function log($ls = 15)
    {
        if (!userid()) {
            redirect('/#login');
        }
		
        $where['status'] = array('egt', 0);
        $where['userid'] = userid();
        $Vote = M('Vote');
        $count = $Vote->where($where)->count();
        $Page = new \Think\Page($count, $ls);
        $show = $Page->show();
        $list = $Vote->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }

}

?>