<?php

namespace Home\Controller;

class PromoteController extends HomeController
{
    public function index($id = NULL)
    {
        $where = "invit_1<>'' and invit_1>0";
        $list = M('User')->field(array('count(*)' => 'pnum', 'invit_1' => 'uid'))->where($where)->group('invit_1')->order('pnum desc')->limit(10)->select();
        foreach ($list as $k => $v) {
            $list[$k]['username'] = M('User')->where(array('id' => $v['uid']))->getField('username');
        }

        //select invit,sum(fee) as jiner from codono_invit  where  group by invit ORDER BY jiner desc
        $where = "type like '%Top award%' and userid in (select id from codono_user)";
        $list_jiner = M('Invit')->field(array('sum(fee)' => 'jiner', 'userid'))->where($where)->group('userid')->order('jiner desc')->limit(10)->select();
        foreach ($list_jiner as $k => $v) {
            $list_jiner[$k]['username'] = M('User')->where(array('id' => $v['userid']))->getField('username');
        }

        $this->assign('list_jiner', $list_jiner);
        $this->assign('list', $list);
        $this->display();
    }


}

?>