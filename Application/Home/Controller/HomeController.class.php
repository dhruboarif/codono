<?php

namespace Home\Controller;

class HomeController extends \Think\Controller
{
    protected function _initialize()
    {
        defined('APP_DEMO') || define('APP_DEMO', 0);


        if (!session('userId')) {
            session('userId', 0);
        } else if (CONTROLLER_NAME != 'Login') {
            $user = D('user')->where('id = ' . session('userId'))->find();

            if (!$user['paypassword']) {
                redirect('/Login/paypassword');
            }

            if (!$user['truename'] && KYC_OPTIONAL==0) {
                //redirect('/User/nameauth');
            }


            if ($user['token'] != session('token_user')) {
                //log in
                session(null);
                session('codono_already', 1);
                redirect('/');
            }

        }

        // Increase access to currency type function

        //C('coin_menu_codono',array('USD','BTC','ETH'));


        if (userid()) {
            $userCoin_top = M('UserCoin')->where(array('userid' => userid()))->find();
            $userCoin_top['usd'] = round($userCoin_top['usd'], 2);
            $userCoin_top['usdd'] = round($userCoin_top['usdd'], 2);
            $userCoin_top['allusd'] = round($userCoin_top['usd'] + $userCoin_top['usdd'], 2);
            $this->assign('userCoin_top', $userCoin_top);
        }

        if (isset($_GET['invite'])) {
            session('invit', $_GET['invite']);
        }

        $config = (APP_DEBUG ? null : S('home_config'));

        if (!$config) {
            $config = M('Config')->where(array('id' => 1))->find();

            S('home_config', $config);
        }

        if(isset($_GET['codono'])){
        if (ADMIN_KEY==$_GET['codono']) {
            session('web_close', 1);
        }}

        if (!session('web_close')) {
            if (!$config['web_close']) {
                exit($config['web_close_cause']);
            }
        }

        C($config);
        C('contact_qq', explode('|', C('contact_qq')));
        C('contact_qqun', explode('|', C('contact_qqun')));
        C('contact_bank', explode('|', C('contact_bank')));

        $coin = (APP_DEBUG ? null : S('home_coin'));

        if (!$coin) {
            $coin = M('Coin')->where(array('status' => 1))->select();
            S('home_coin', $coin);
        }

        $coinList = array();

        foreach ($coin as $k => $v) {
            $coinList['coin'][$v['name']] = $v;

            if ($v['name'] != 'usd') {
                $coinList['coin_list'][$v['name']] = $v;
            }

            if ($v['type'] == 'rmb') {
                $coinList['rmb_list'][$v['name']] = $v;
            } else {
                $coinList['xnb_list'][$v['name']] = $v;
            }

            if ($v['type'] == 'rgb') {
                $coinList['rgb_list'][$v['name']] = $v;
            }

            if ($v['type'] == 'qbb') {
                $coinList['qbb_list'][$v['name']] = $v;
            }
			if ($v['type'] == 'eth') {
                $coinList['eth_list'][$v['name']] = $v;
            }
			if ($v['type'] == 'blockio') {
                $coinList['blockio_list'][$v['name']] = $v;
            }
			if ($v['type'] == 'cryptonote') {
                $coinList['cryptonote_list'][$v['name']] = $v;
            }
			if ($v['type'] == 'coinpay') {
                $coinList['coinpay_list'][$v['name']] = $v;
            }
			if ($v['type'] == 'waves') {
                $coinList['waves_list'][$v['name']] = $v;
            }
        }


        C($coinList);

        $market = (APP_DEBUG ? null : S('home_market'));


        $market_type = array();
        $coin_on = array();

        if (!$market) {
            $market = M('Market')->where(array('status' => 1))->select();
            S('home_market', $market);
        }

        foreach ($market as $k => $v) {
            $v['new_price'] = round($v['new_price'], $v['round']);
            $v['buy_price'] = round($v['buy_price'], $v['round']);
            $v['sell_price'] = round($v['sell_price'], $v['round']);
            $v['min_price'] = round($v['min_price'], $v['round']);
            $v['max_price'] = round($v['max_price'], $v['round']);
            $v['xnb'] = explode('_', $v['name'])[0];
            $v['rmb'] = explode('_', $v['name'])[1];
            $v['xnbimg'] = C('coin')[$v['xnb']]['img'];
            $v['rmbimg'] = C('coin')[$v['rmb']]['img'];
            $v['volume'] = $v['volume'] * 1;
            $v['change'] = $v['change'] * 1;
            $v['title'] = C('coin')[$v['xnb']]['title'] . '(' . strtoupper($v['xnb']) . '/' . strtoupper($v['rmb']) . ')';
            $v['navtitle'] = C('coin')[$v['xnb']]['title'] . '(' . strtoupper($v['xnb']) . ')';

            if (!$v['begintrade']) {
                $v['begintrade'] = "00:00:00";
            }
            if (!$v['endtrade']) {
                $v['endtrade'] = "23:59:59";
            }


            $market_type[$v['xnb']] = $v['name'];
            $coin_on[] = $v['xnb'];
            $marketList['market'][$v['name']] = $v;
        }

        C('market_type', $market_type);
        C('coin_on', $coin_on);

        C($marketList);
        $C = C();

        foreach ($C as $k => $v) {
            $C[strtolower($k)] = $v;
        }

        $this->assign('C', $C);

        if (!S('navigation_aa')) {
            $tables = M()->query('show tables');
            $tableMap = array();

            foreach ($tables as $table) {
                $tableMap[reset($table)] = 1;
            }

            if (!isset($tableMap['codono_navigation'])) {
                M()->execute("\r\n" . '                    CREATE TABLE `codono_navigation` (' . "\r\n" . '                        `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT \'Incrementid\',' . "\r\n" . '                        `name` VARCHAR(255) NOT NULL COMMENT \'name\',' . "\r\n" . '                         `title` VARCHAR(255) NOT NULL COMMENT \'name\',' . "\r\n" . '                        `url` VARCHAR(255) NOT NULL COMMENT \'url\',' . "\r\n" . '                        `sort` INT(11) UNSIGNED NOT NULL COMMENT \'Sequence\',' . "\r\n" . '                        `addtime` INT(11) UNSIGNED NOT NULL COMMENT \'add time\',' . "\r\n" . '                        `endtime` INT(11) UNSIGNED NOT NULL COMMENT \'Edit time\',' . "\r\n" . '                        `status` TINYINT(4)  NOT NULL COMMENT \'status\',' . "\r\n" . '                        PRIMARY KEY (`id`)' . "\r\n\r\n" . '                  )' . "\r\n" . 'COLLATE=\'gbk_chinese_ci\'' . "\r\n" . 'ENGINE=MyISAM' . "\r\n" . 'AUTO_INCREMENT=1' . "\r\n" . ';' . "\r\n\r\n\r\n\r\n" . 'INSERT INTO `codono_navigation` (`name`,`title`, `url`, `sort`, `status`) VALUES (\'finance\',\'Financial Center\', \'Finance/index\', 1, 1);' . "\r\n" . 'INSERT INTO `codono_navigation` (`name`,`title`, `url`, `sort`, `status`) VALUES (\'user\',\'Security center\', \'User/index\', 2, 1);' . "\r\n" . 'INSERT INTO `codono_navigation` (`name`, `title`,`url`, `sort`, `status`) VALUES (\'game\',\'application Center\', \'Issue/index\', 3, 1);' . "\r\n" . 'INSERT INTO `codono_navigation` (`name`, `title`,`url`, `sort`, `status`) VALUES (\'article\',\'Help\', \'Article/index\', 4, 1);' . "\r\n\r\n\r\n" . '                ');
            }

            S('navigation_aa', 1);
        }


        //echo C('reg_award').C('reg_award_coin').C('reg_award_num');
        //die();


        if (!S('navigation')) {
            $this->navigation = M('Navigation')->where(array('status' => 1))->order('sort asc')->select();
            S('navigation', $this->navigation);
        } else {
            $this->navigation = S('navigation');
        }

        $footerArticleType = (APP_DEBUG ? null : S('footer_indexArticleType'));

        if (!$footerArticleType) {
            $footerArticleType = M('ArticleType')->where(array('status' => 1, 'footer' => 1, 'shang' => ''))->order('sort asc ,id desc')->limit(3)->select();
            S('footer_indexArticleType', $footerArticleType);
        }

        $this->assign('footerArticleType', $footerArticleType);
        $footerArticle = (APP_DEBUG ? null : S('footer_indexArticle'));

        if (!$footerArticle) {
            foreach ($footerArticleType as $k => $v) {
                $footerArticle[$v['name']] = M('ArticleType')->where(array('shang' => $v['name'], 'footer' => 1, 'status' => 1))->order('id asc')->limit(4)->select();
            }

            S('footer_indexArticle', $footerArticle);
        }


        $this->assign('footerArticle', $footerArticle);

    }

    public function _empty()
    {
        send_http_status(404);
        $this->error();
        echo L('Module does not exist!');
        die();

    }
	public function addnotification($to_email,$subject,$content){
		M('Notification')->add(array('to_email' => $to_email, 'subject' => $subject,'content' => $content));
		
	}
	public function deposit_notify($userid,$deposit_address,$coinname,$txid,$deposited_amount,$time){
		$deposit_time=date('Y-m-d H:i',$time).'('.date_default_timezone_get().')';
		
		$user = M('User')->where(array('id' => $userid))->find();
		$to_email=$user['email'];
		$subject="Deposit Success Alerts ".$deposit_time;
		$content="Hello,<br/>Your ".SHORT_NAME." acccount has recharged ".$deposited_amount." ".$coinname ."<br/>
		<i><small>If this activity is not your own operation, please contact us immediately. </small>";

		M('Notification')->add(array('to_email' => $to_email, 'subject' => $subject,'content' => $content));
	}
}

?>