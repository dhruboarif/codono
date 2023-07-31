<?php

namespace Home\Controller;

class IndexController extends HomeController
{
	public function test(){
		echo "connected";
		
	}
    public function index()
    {
        $this->assign('prompt_text', D('Text')->get_content('index_info'));
        $this->assign('warning_text', D('Text')->get_content('index_warning'));
        $indexAdver = (APP_DEBUG ? null : S('index_indexAdver'));

        if (!$indexAdver) {
            $indexAdver = M('Adver')->where(array('status' => 1))->order('id asc')->select();
            S('index_indexAdver', $indexAdver);
        }
	
        $this->assign('indexAdver', $indexAdver);


        switch (C('index_html')) {
            case "a":
                //in case a stencil

                $indexArticle = (APP_DEBUG ? null : S('index_indexArticle'));

                $indexArticleType = array(
                    "gonggao" => "aaa",
                    "taolun" => "Codono Exchange",
                    "hangye" => "bbb"
                );

                if (!$indexArticle) {
                    foreach ($indexArticleType as $k => $v) {
                        $indexArticle[$k] = M('Article')->where(array('type' => $v, 'status' => 1, 'index' => 1))->order('id desc')->limit(4)->select();

                        foreach ($indexArticle[$k] as $kk => $vv) {
                            $indexArticle[$k][$kk]['content'] = mb_substr(clear_html($vv['content']), 0, 40, 'utf-8');
                            if ($indexArticle[$k][$kk]['img']) {
                                $indexArticle[$k][$kk]['img'] = "/upload/article/" . $indexArticle[$k][$kk]['img'];
                            } else {
                                $indexArticle[$k][$kk]['img'] = "/Public/static/default/defaultImg.jpg";
                            }
                        }
                    }

                    S('index_indexArticle', $indexArticle);
                }
                break;

            default:
                $indexArticleType = (APP_DEBUG ? null : S('index_indexArticleType'));

                if (!$indexArticleType) {
                    $indexArticleType = M('ArticleType')->where(array('status' => 1, 'index' => 1))->order('sort asc ,id desc')->limit(3)->select();
                    S('index_indexArticleType', $indexArticleType);
                }
                $indexArticle = (APP_DEBUG ? null : S('index_indexArticle'));

                if (!$indexArticle) {
                    foreach ($indexArticleType as $k => $v) {
                        $indexArticle[$k] = M('Article')->where(array('type' => $v['name'], 'status' => 1, 'index' => 1))->order('id desc')->limit(6)->select();
                    }

                    S('index_indexArticle', $indexArticle);
                }
        }
        $this->assign('indexArticleType', $indexArticleType);
        $this->assign('indexArticle', $indexArticle);


        $indexLink = (APP_DEBUG ? null : S('index_indexLink'));

        if (!$indexLink) {
            $indexLink = M('Link')->where(array('status' => 1))->order('sort asc ,id desc')->select();
        }
		
		/* ICO Widget Show*/
		
		 $where['status'] = array('neq', 0);
		 $where['homepage']=array ('eq',1);
        $Model = M('Issue');
        $count = $Model->where($where)->count();
        $Page = new \Think\Page($count, 3);
        $Page->show();
        $list = $Model->where($where)->order('tuijian asc,paixu desc,addtime desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

        $list_jinxing = array();//Running
		
		foreach ($list as $k => $v) {

            $list[$k]['bili'] = round(($v['deal'] / $v['num']) * 100, 2);
            $list[$k]['endtime'] = date("Y-m-d H:i:s", strtotime($v['time']) + $v['tian'] * 3600 * 24);

            $list[$k]['coinname'] = C('coin')[$v['coinname']]['title'];
            $list[$k]['buycoin'] = C('coin')[$v['buycoin']]['title'];
            $list[$k]['bili'] = round(($v['deal'] / $v['num']) * 100, 2);
            $list[$k]['content'] = mb_substr(clear_html($v['content']), 0, 350, 'utf-8');


            $end_ms = strtotime($v['time']) + $v['tian'] * 3600 * 24;
            $begin_ms = strtotime($v['time']);


            $list[$k]['beginTime'] = date("Y-m-d H:i:s", $begin_ms);
            $list[$k]['endTime'] = date("Y-m-d H:i:s", $end_ms);

            $list[$k]['zhuangtai'] = L('RUNNING');

            if ($begin_ms > time()) {
                $list[$k]['zhuangtai'] = L('UPCOMING');//upcoming
            }


            if ($list[$k]['num'] <= $list[$k]['deal']) {
                $list[$k]['zhuangtai'] = L('ENDED');//ended
            }

            if ($end_ms < time()) {
                $list[$k]['zhuangtai'] = L('ENDED');//ended
            }

            switch ($list[$k]['zhuangtai']) {
                  case L('RUNNING'):
                    $list_jinxing[] = $list[$k];
                    break;
				default:
				$list_jinxing[] = $list[$k];
            }


        }

        $this->assign('list_running', $list_jinxing);
		/* Issue widget Show end */
		
		
		
		
        $codono_getCoreConfig = codono_getCoreConfig();
        if (!$codono_getCoreConfig) {
            $this->error(L('Incorrect Core Config'));
        }

        $this->assign('codono_jiaoyiqu', $codono_getCoreConfig['codono_indexcat']);

        $this->assign('indexLink', $indexLink);

        $ajaxMenu = new AjaxController();
        $indexMenu = $ajaxMenu->getJsonMenu('');
        $this->assign('indexMenu', $indexMenu);

		#var_dump($indexAdver, $indexArticleType, $indexArticle, $indexLink);

        //print_r(C('index_html'));

    $this->display();
    }

    public function monesay($monesay = NULL)
    {
    }


    public function install()
    {
    }

    public function fragment()
    {
        $ajax = new AjaxController();
        $data = $ajax->allcoin('');
        $this->assign('data', $data);
        $this->display('Index/d/fragment');
    }

    public function newPrice()
    {
        ini_set('display_errors', 'on');
        error_reporting(E_ALL);
        //var_dump(C('market'));
        $data = $this->allCoinPrice();
        //var_dump($data);
        // exit;
        $last_data = S('ajax_all_coin_last');
        $_result = array();
        if (empty($last_data)) {
            foreach (C('market') as $k => $v) {
                $_result[$v['id'] . '-' . strtoupper($v['xnb'])] = $data[$k][1] . '-0.0';
            }
        } else {
            foreach (C('market') as $k => $v) {
                $_result[$v['id'] . '-' . strtoupper($v['xnb'])] = $data[$k][1] . '-' . ($data[$k][1] - $last_data[$k][1]);
            }
        }

        S('ajax_all_coin_last', $data);

        $data = json_encode(
            array(
                'result' => $_result,
            )
        );
        exit($data);

        //exit('{"result":{"25-BTC":"4099.0-0.0","1-LTC":"26.43--0.22650056625141082","26-DZI":"1.72-0.0","6-DOGE":"0.00151-0.0"},"totalPage":5}');
    }


    protected function allCoinPrice()
    {
        $data = (APP_DEBUG ? null : S('allCoinPrice'));
       if(!$data) {
           // market Transaction Record
           $marketLogs = array();
           foreach (C('market') as $k => $v) {
               $tradeLog = M('TradeLog')->where(array('status' => 1, 'market' => $k))->order('id desc')->limit(50)->select();
               $_data = array();
               foreach ($tradeLog as $_k => $_v) {
                   $_data['tradelog'][$_k]['addtime'] = date('m-d H:i:s', $_v['addtime']);
                   $_data['tradelog'][$_k]['type'] = $_v['type'];
                   $_data['tradelog'][$_k]['price'] = $_v['price'] * 1;
                   $_data['tradelog'][$_k]['num'] = round($_v['num'], 6);
                   $_data['tradelog'][$_k]['mum'] = round($_v['mum'], 2);
               }
               $marketLogs[$k] = $_data;
           }

           $themarketLogs = array();
           if ($marketLogs) {
               $last24 = time() - 86400;
               $_date = date('m-d H:i:s', $last24);
               foreach (C('market') as $k => $v) {
                   $tradeLog = isset($marketLogs[$k]['tradelog']) ? $marketLogs[$k]['tradelog'] : null;
                   if ($tradeLog) {
                       $sum = 0;
                       foreach ($tradeLog as $_k => $_v) {
                           if ($_v['addtime'] < $_date) {
                               continue;
                           }
                           $sum += $_v['mum'];
                       }
                       $themarketLogs[$k] = $sum;
                   }
               }
           }

           foreach (C('market') as $k => $v) {
               $data[$k][0] = $v['title'];
               $data[$k][1] = round($v['new_price'], $v['round']);
               $data[$k][2] = round($v['buy_price'], $v['round']);
               $data[$k][3] = round($v['sell_price'], $v['round']);
               $data[$k][4] = isset($themarketLogs[$k]) ? $themarketLogs[$k] : 0;//round($v['volume'] * $v['new_price'], 2) * 1;
               $data[$k][5] = '';
               $data[$k][6] = round($v['volume'], 2) * 1;
               $data[$k][7] = round($v['change'], 2);
               $data[$k][8] = $v['name'];
               $data[$k][9] = $v['xnbimg'];
               $data[$k][10] = '';
           }
           S('allCoinPrice', $data);
       }

        return $data;
    }

}

?>