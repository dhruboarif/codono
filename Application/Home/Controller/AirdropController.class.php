<?php

namespace Home\Controller;

class AirdropController extends HomeController
{
    public function index()
    {
        $where['active'] = array('neq', 0);
        $Model = M('Dividend');
        $count = $Model->where($where)->count();
        $Page = new \Think\Page($count, 5);
        $show = $Page->show();

        $list = $Model->where($where)->order('sort asc,endtime desc,addtime desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

        $is_featured = $Model->where(array("is_featured" => 1))->order("addtime desc")->limit(1)->find();


        if ($is_featured) {

            $is_featured['coinname'] = C('coin')[$is_featured['coinname']]['title'];
            $is_featured['coinjian'] = C('coin')[$is_featured['coinjian']]['title'];
            $is_featured['content'] = mb_substr(clear_html($is_featured['content']), 0, 350, 'utf-8');


            $end_ms = $is_featured['endtime'] ;
            $begin_ms = $is_featured['addtime'];

            $is_featured['beginTime'] = date("Y-m-d H:i:s", $begin_ms);
            $is_featured['endTime'] = date("Y-m-d H:i:s", $end_ms);

            $is_featured['headtitle'] = "Running";

            if ($begin_ms > time()) {
                $is_featured['headtitle'] = "Upcoming";//Not started
            }

            if ($end_ms < time()) {
                $is_featured['headtitle'] = "Ended";//Ended
            }

        }


        $list_running = array();//Running
        $list_upcoming = array();//Upcoming
        $list_ended = array(); //Ended


        foreach ($list as $k => $v) {
            $list[$k]['img'] = M('Coin')->where(array('name' => $v['coinname']))->getField('img');

            

            $list[$k]['coinname'] = C('coin')[$v['coinname']]['title'];
            $list[$k]['coinjian'] = C('coin')[$v['coinjian']]['title'];
            $list[$k]['num'] = format_num( $v['num']);
            $list[$k]['content'] = mb_substr(clear_html($v['content']), 0, 350, 'utf-8');


            $end_ms = $v['endtime'];
            $begin_ms = $v['addtime'];


            $list[$k]['beginTime'] = date("Y-m-d H:i:s", $begin_ms);
            $list[$k]['endTime'] = date("Y-m-d H:i:s", $end_ms);

            $list[$k]['headtitle'] = L('RUNNING');

            if ($begin_ms > time()) {
                $list[$k]['headtitle'] = L('UPCOMING');//upcoming
            }


            if ($end_ms < time()) {
                $list[$k]['headtitle'] = L('ENDED');//ended
            }

            switch ($list[$k]['headtitle']) {
                case L('UPCOMING'):
                    $list_upcoming[] = $list[$k];
                    break;
                case L('RUNNING'):
                    $list_running[] = $list[$k];
                    break;
                case L('ENDED'):
                    $list_ended[] = $list[$k];
                    break;
            }


        }

        $this->assign('is_featured', $is_featured);
        $this->assign('list_upcoming', $list_upcoming);
        $this->assign('list_running', $list_running);
        $this->assign('list_ended', $list_ended);
        $this->assign('page', $show);
        $this->display();
    }
}

?>