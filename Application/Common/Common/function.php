<?php
use Think\SuperEmail;


if (!function_exists('array_column')) {
    function array_column(array $input, $columnKey, $indexKey = NULL)
    {
        $result = array();

        if (NULL === $indexKey) {
            if (NULL === $columnKey) {
                $result = array_values($input);
            } else {
                foreach ($input as $row) {
                    $result[] = $row[$columnKey];
                }
            }
        } else if (NULL === $columnKey) {
            foreach ($input as $row) {
                $result[$row[$indexKey]] = $row;
            }
        } else {
            foreach ($input as $row) {
                $result[$row[$indexKey]] = $row[$columnKey];
            }
        }

        return $result;
    }
}
function actionconvert($json){
//$json='{"coin":{"name":"btc","value":"1.5"},"market":{"name":"btc_usd","buy":"5","sell":"6"}}';

$res=json_decode($json);
$verb="";
if($res->coin->value>0){
	$verb.=$res->coin->value.' '. $res->coin->name .', '; 
}
if($res->market->buy>0 ){
	$verb.=$res->market->buy.' '. $res->market->name .' buy trades, '; 
}
if($res->market->sell>0 ){
	$verb.=$res->market->sell.' '. $res->market->name .' sell trades '; 
}
return rtrim($verb,", ");

}
function backrun($command){

require getcwd().'/Application/Common/Ext/vendor/autoload.php';
$process = new BackgroundProcess($command);
$process->run();
}
//Template Based Email
function tmail($to_email,$subject,$content)
{
		if(!$to_email || !$subject || !$content){
			$return=array('status'=>0,'message'=>"Ensure you have filled all fields, to,subject,content");
			return json_encode($return);
		}
		$logo=SITE_URL.'/Upload/public/'.C('web_logo');

		$template=file_get_contents('./Public/email-content.html');
		
		$vars = array(
		'{$root}'=>SITE_URL,
		'{$logo}'=>$logo,
		'{$content}'       => $content,
		'{$subject}'        => $subject,
		);
		$body= strtr($template, $vars);
		
         return SuperEmail::sendemail($to_email, $subject, $body);
}
function format_num($num,$decimal=8){
	return number_format($num,$decimal,'.','');
}

//$action= e =encrypt or d =decrypt
function cryptString( $string, $action = 'e' ) {
    //You can change it once in life time .. If you change it you need to change all passwords for ethereum nodes too
    $secret_key = 'G4356OJEGC';
    $secret_iv = 'I5GUCEB0IG';
	//Again Never change these keys , If you do your previous Passwords for blockchain accounts wont work..
    $output = false;
    $encrypt_method = "AES-256-CBC";
    $key = hash( 'sha256', $secret_key );
    $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );

    if( $action == 'e' ) {
        $output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
    }
    else if( $action == 'd' ){
        $output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
    }

    return $output;
}
function authgame($name)
{
    if (!check($name, 'w')) {
        return 0;
        exit();
    }

    if (M('VersionGame')->where(array('name' => $name, 'status' => 1))->find()) {
        return 1;
    } else {
        return 0;
    }
}

function getUrl($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 3);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, '');
    $data = curl_exec($ch);
    return $data;
}

function topup($cellphone = NULL, $num = NULL, $orderid = NULL)
{
    if (empty($cellphone)) {
        return NULL;
    }

    if (empty($num)) {
        return NULL;
    }

    if (empty($orderid)) {
        return NULL;
    }

    header('Content-type:text/html;charset=utf-8');
    $appkey = C('topup_appkey');
    $openid = C('topup_openid');
    $recharge = new \Common\Ext\Recharge($appkey, $openid);
    $telRechargeRes = $recharge->telcz($cellphone, $num, $orderid);

    if ($telRechargeRes['error_code'] == '0') {
        return 1;
    } else {
        return NULL;
    }
}

function mlog($text)
{
    $text = addtime(time()) . ' ' . $text . "\n";
    file_put_contents(APP_PATH . '/../Public/Log/mlog_file.log', $text, FILE_APPEND);
}

function clog($filename, $text)
{
	if(is_array($text)){$text= json_encode($text, true);}
    $text = addtime(time()) . ' ' . $text . "\n";
	$filename=date('d-m-Y')."_".$filename;
    file_put_contents(APP_PATH . '/../Public/Log/' . $filename . '.log', $text, FILE_APPEND);
}

function aaa($item, $pattern, $fun)
{
    $pattern = str_replace('###', $item['id'], $pattern);
    $view = new \Think\View();
    $view->assign($item);
    $pattern = $view->fetch('', $pattern);
    return $fun($pattern);
}

function authUrl($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 3);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, '');
    $data = curl_exec($ch);
    return $data;
}

function userid($username = NULL, $type = 'username')
{
    if ($username && $type) {
        $userid = (APP_DEBUG ? NULL : S('userid' . $username . $type));

        if (!$userid) {
            $userid = M('User')->where(array($type => $username))->getField('id');
            S('userid' . $username . $type, $userid);
        }
    } else {
        $userid = session('userId');
    }

    return $userid ? $userid : NULL;
}
function usertype()
{

	$userid=userid();
    if ((int)$userid>0) {
		
        $usertype = (APP_DEBUG ? NULL : S('usertype' . $userid));

        if (!$usertype) {
			
            $status = M('User')->where(array('id' => $userid))->getField('usertype');
			$usertype=$status;
            S('usertype' . $userid , $status);
			
        }
    } else {
        $usertype = 0;
    }
	return $usertype;

}
function kyced()
{
	if(ENFORCE_KYC!=1)
	{
		return true;
	}	
	$userid=userid();
    if ((int)$userid>0) {
		
        $kyced = (APP_DEBUG ? NULL : S('kyced' . $userid));

        if (!$kyced) {
			
            $status = M('User')->where(array('id' => $userid))->getField('idcardauth');
			$kyced=$status;
            S('kyced' . $userid , $status);
			
        }
    } else {
        $kyced = null;
    }
	if($kyced==1){
		return 1;
	}else{
		return 0;
	}
}

function check_kyc()
{

    $userid=userid();
    if ((int)$userid>0) {

        $kyced = (APP_DEBUG ? NULL : S('kyced' . $userid));

        if (!$kyced) {

            $status = M('User')->where(array('id' => $userid))->getField('idcardauth');
            $kyced=$status;
            S('kyced' . $userid , $status);

        }
    } else {
        $kyced = null;
    }
    if($kyced==1){
        return 1;
    }else{
        return 0;
    }
}

function username($id = NULL, $type = 'id')
{
    if ($id && $type) {
        $username = (APP_DEBUG ? NULL : S('username' . $id . $type));

        if (!$username) {
            $username = M('User')->where(array($type => $id))->getField('username');
            S('username' . $id . $type, $username);
        }
    } else {
        $username = session('userName');
    }

    return $username ? $username : NULL;
}

function check_dirfile()
{
    die();
    define('INSTALL_APP_PATH', realpath('./') . '/');
    $items = array(
        array('dir', 'Writable', 'ok', './Database'),
        array('dir', 'Writable', 'ok', './Database/Backup'),
        array('dir', 'Writable', 'ok', './Database/Cloud'),
        array('dir', 'Writable', 'ok', './Database/Temp'),
        array('dir', 'Writable', 'ok', './Database/Update'),
        array('dir', 'Writable', 'ok', './Runtime'),
        array('dir', 'Writable', 'ok', './Runtime/Logs'),
        array('dir', 'Writable', 'ok', './Runtime/Cache'),
        array('dir', 'Writable', 'ok', './Runtime/Temp'),
        array('dir', 'Writable', 'ok', './Runtime/Data'),
        array('dir', 'Writable', 'ok', './Upload/ad'),
        array('dir', 'Writable', 'ok', './Upload/ad'),
        array('dir', 'Writable', 'ok', './Upload/bank'),
        array('dir', 'Writable', 'ok', './Upload/coin'),
        array('dir', 'Writable', 'ok', './Upload/face'),
        array('dir', 'Writable', 'ok', './Upload/footer'),
        array('dir', 'Writable', 'ok', './Upload/game'),
        array('dir', 'Writable', 'ok', './Upload/link'),
        array('dir', 'Writable', 'ok', './Upload/public'),
        array('dir', 'Writable', 'ok', './Upload/shop')
    );

    foreach ($items as &$val) {
        if ('dir' == $val[0]) {
            if (!is_writable(INSTALL_APP_PATH . $val[3])) {
                if (is_dir($items[1])) {
                    $val[1] = 'Readable';
                    $val[2] = 'remove';
                    session('error', true);
                } else {
                    $val[1] = 'Does not exist or can not be written';
                    $val[2] = 'remove';
                    session('error', true);
                }
            }
        } else if (file_exists(INSTALL_APP_PATH . $val[3])) {
            if (!is_writable(INSTALL_APP_PATH . $val[3])) {
                $val[1] = 'File exists but can not be written';
                $val[2] = 'remove';
                session('error', true);
            }
        } else if (!is_writable(dirname(INSTALL_APP_PATH . $val[3]))) {
            $val[1] = 'Does not exist or can not be written';
            $val[2] = 'remove';
            session('error', true);
        }
    }

    return $items;
}

function op_t($text, $addslanshes = false)
{
    $text = nl2br($text);
    $text = real_strip_tags($text);

    if ($addslanshes) {
        $text = addslashes($text);
    }

    $text = trim($text);
    return $text;
}

function text($text, $addslanshes = false)
{
    return op_t($text, $addslanshes);
}

function html($text)
{
    return op_h($text);
}

function op_h($text, $type = 'html')
{
    $text_tags = '';
    $link_tags = '<a>';
    $image_tags = '<img>';
    $font_tags = '<i><b><u><s><em><strong><font><big><small><sup><sub><bdo><h1><h2><h3><h4><h5><h6>';
    $base_tags = $font_tags . '<p><br><hr><a><img><map><area><pre><code><q><blockquote><acronym><cite><ins><del><center><strike>';
    $form_tags = $base_tags . '<form><input><textarea><button><select><optgroup><option><label><fieldset><legend>';
    $html_tags = $base_tags . '<ul><ol><li><dl><dd><dt><table><caption><td><th><tr><thead><tbody><tfoot><col><colgroup><div><span><object><embed><param>';
    $all_tags = $form_tags . $html_tags . '<!DOCTYPE><meta><html><head><title><body><base><basefont><script><noscript><applet><object><param><style><frame><frameset><noframes><iframe>';
    $text = real_strip_tags($text, $$type . '_tags');

    if ($type != 'all') {
        while (preg_match('/(<[^><]+)(ondblclick|onclick|onload|onerror|unload|onmouseover|onmouseup|onmouseout|onmousedown|onkeydown|onkeypress|onkeyup|onblur|onchange|onfocus|action|background[^-]|codebase|dynsrc|lowsrc)([^><]*)/i', $text, $mat)) {
            $text = str_ireplace($mat[0], $mat[1] . $mat[3], $text);
        }

        while (preg_match('/(<[^><]+)(window\\.|javascript:|js:|about:|file:|document\\.|vbs:|cookie)([^><]*)/i', $text, $mat)) {
            $text = str_ireplace($mat[0], $mat[1] . $mat[3], $text);
        }
    }

    return $text;
}

function real_strip_tags($str, $allowable_tags = '')
{
    return strip_tags($str, $allowable_tags);
}

function clean_cache($dirname = './Runtime/')
{
    $dirs = array($dirname);

    foreach ($dirs as $value) {
        rmdirr($value);
    }

    @(mkdir($dirname, 511, true));
}

function getSubByKey($pArray, $pKey = '', $pCondition = '')
{
    $result = array();

    if (is_array($pArray)) {
        foreach ($pArray as $temp_array) {
            if (is_object($temp_array)) {
                $temp_array = (array)$temp_array;
            }

            if ((('' != $pCondition) && ($temp_array[$pCondition[0]] == $pCondition[1])) || ('' == $pCondition)) {
                $result[] = ('' == $pKey ? $temp_array : isset($temp_array[$pKey]) ? $temp_array[$pKey] : '');
            }
        }

        return $result;
    } else {
        return false;
    }
}

function debug($value, $type = 'DEBUG', $verbose = false, $encoding = 'UTF-8')
{
    if (ADMIN_DEBUG || 1) {
        if (!IS_CLI) {
            Common\Ext\FirePHP::getInstance(true)->log($value, $type);
        }
    }
}
function lognow($message="NO MESSAGE PROVIDED",$type="WARN"){
    \Think\Log::record($message,$type);
}
function EosClient($host, $port)
{
    return new \Common\Ext\EosClient($host, $port);
}
function EthMaster($host, $port,$coinbase,$coinbasePassword, $ContractAddress=false)
{	
    return new \Common\Ext\EthMaster($host, $port,$coinbase,$coinbasePassword, $ContractAddress);
}
function CoinClient($username, $password, $ip, $port, $timeout = 3, $headers = array(), $suppress_errors = false)
{	
    return new \Common\Ext\CoinClient($username, $password, $ip, $port, $timeout, $headers, $suppress_errors);
}
function WavesClient($username, $password, $ip, $port,$decimal, $timeout = 3, $headers = array(), $suppress_errors = false)
{
    return new \Common\Ext\WavesPlatform($ip, $port, $password, $username,$decimal);
}
function BlockIO($username, $password, $ip, $port, $timeout = 3, $headers = array(), $suppress_errors = false)
{
$apiKey = $username;
$pin = $password;
$version = 2; // the API version
return new \Common\Ext\BlockIo($apiKey, $pin, $version);
}
function CryptoNote($ip, $port)
{
return new \Common\Ext\CryptoNote($ip, $port);
}

function paymentwall($userid,$email,$mycz){
require_once('Paymentwall.class.php');
Paymentwall_Config::getInstance()->set(array(
    'api_type' => Paymentwall_Config::API_VC,
	//'public_key' => 't_19e54454e95a03804c1bd4ff000624',
	//'private_key' => 't_7538e7e80881ae38006a3edca83978'
	 'public_key' => 'X',
    'private_key' => 'X'
));
$widget = new Paymentwall_Widget(
    "$userid", 
    'p10_1', 
  array(),
    array(
        'email' => $email, 
        'history[registration_date]' => 'registered_date_of_user',
        'ps' => 'all' // Replace the value of 'ps' with specific payment system short code for Widget API uni
    )
);
return $widget->getUrl();
}

function IPNpaymentwall(){
require_once('Paymentwall.class.php');
Paymentwall_Config::getInstance()->set(array(
    'api_type' => Paymentwall_Config::API_VC,
	 'public_key' => '7x',
    'private_key' => 'x'
));
$pingback = new Paymentwall_Pingback($_GET, $_SERVER['REMOTE_ADDR']);
$data['status']=0;
$data['message']='Nothing Yet';
$data['ipn_resp']=$pingback;
if ($pingback->validate()) {
    $virtualCurrency = $pingback->getVirtualCurrencyAmount();
    if ($pingback->isDeliverable()) {
        // deliver the virtual currency
    } else if ($pingback->isCancelable()) {
        // withdraw the virtual currency
    } else if ($pingback->isUnderReview()) {
        // set "pending" status to order
    }
    $data['status']=1;$data['message']='OK';
	// Paymentwall expects response to be OK, otherwise the pingback will be resent
} else {
    //echo $pingback->getErrorSummary();
	$data['status']=0;$data['message']=$pingback->getErrorSummary(); 
}
return json_encode($data);
}
function CoinPay($username, $password, $ip, $port, $timeout = 3, $headers = array(), $suppress_errors = false)
{

$version = 2; // the API version
return new \Common\Ext\CoinpaymentsAPI($password, $username, 'json');
}

function createQRcode($save_path, $qr_data = 'PHP QR Code :)', $qr_level = 'L', $qr_size = 4, $save_prefix = 'qrcode')
{
    if (!isset($save_path)) {
        return '';
    }

    $PNG_TEMP_DIR = &$save_path;
    vendor('PHPQRcode.class#phpqrcode');

    if (!file_exists($PNG_TEMP_DIR)) {
        mkdir($PNG_TEMP_DIR);
    }

    $filename = $PNG_TEMP_DIR . 'test.png';
    $errorCorrectionLevel = 'L';

    if (isset($qr_level) && in_array($qr_level, array('L', 'M', 'Q', 'H'))) {
        $errorCorrectionLevel = &$qr_level;
    }

    $matrixPointSize = 4;

    if (isset($qr_size)) {
        $matrixPointSize = &min(max((int)$qr_size, 1), 10);
    }

    if (isset($qr_data)) {
        if (trim($qr_data) == '') {
            exit('data cannot be empty!');
        }

        $filename = $PNG_TEMP_DIR . $save_prefix . md5($qr_data . '|' . $errorCorrectionLevel . '|' . $matrixPointSize) . '.png';
        QRcode::png($qr_data, $filename, $errorCorrectionLevel, $matrixPointSize, 2, true);
    } else {
        QRcode::png('PHP QR Code :)', $filename, $errorCorrectionLevel, $matrixPointSize, 2, true);
    }

    if (file_exists($PNG_TEMP_DIR . basename($filename))) {
        return basename($filename);
    } else {
        return false;
    }
}

function NumToStr($num)
{
    if (!$num) {
        return $num;
    }

    if ($num == 0) {
        return 0;
    }

    $num = round($num, 8);
    $min = 0.0001;

    if ($num <= $min) {
        $times = 0;

        while ($num <= $min) {
            $num *= 10;
            $times++;

            if (10 < $times) {
                break;
            }
        }

        $arr = explode('.', $num);
        $arr[1] = str_repeat('0', $times) . $arr[1];
        return $arr[0] . '.' . $arr[1] . '';
    }

    return ($num * 1) . '';
}

function Num($num)
{
    if (!$num) {
        return $num;
    }

    if ($num == 0) {
        return 0;
    }

    $num = round($num, 8);
    $min = 0.0001;

    if ($num <= $min) {
        $times = 0;

        while ($num <= $min) {
            $num *= 10;
            $times++;

            if (10 < $times) {
                break;
            }
        }

        $arr = explode('.', $num);
        $arr[1] = str_repeat('0', $times) . $arr[1];
        return $arr[0] . '.' . $arr[1] . '';
    }

    return ($num * 1) . '';
}

function check_verify($code, $id = 1, $recap = 0)
{

    if (RECAPTCHA == 1 && $recap == 1) {
        $secret = RECAPTCHA_SECRET;
        $url = "https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$code";
        $ch = curl_init();
        // Disable SSL verification
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // Will return the response, if false it print the response
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Set the url
        curl_setopt($ch, CURLOPT_URL, $url);
        // Execute
        $result = json_decode(curl_exec($ch));
        $success = $result->success;
        return $success;
    } else {
        $code = strtoupper($code);
        $verify = new \Think\Verify();
        return $verify->check($code, $id);
    }

}

function check_recaptcha($code, $id = 1)
{


}


function get_city_ip($ip = NULL)
{
    if (empty($ip)) {
        $ip = get_client_ip();
    }

    $Ip = new Org\Net\IpLocation();
    $area = $Ip->getlocation($ip);
    $str = $area['country'] . $area['area'];
    $str = mb_convert_encoding($str, 'UTF-8', 'GBK');

    if (($ip == '127.0.0.1') || ($str == false) || ($str == 'IANA reserved addresses for local loopback')) {
        $str = 'LocalIP';
    }

    return $str;
}

function send_post($url, $post_data)
{
    $postdata = http_build_query($post_data);
    $options = array(
        'http' => array('method' => 'POST', 'header' => 'Content-type:application/x-www-form-urlencoded', 'content' => $postdata, 'timeout' => 15 * 60)
    );
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    return $result;
}

function request_by_curl($remote_server, $post_string)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $remote_server);
    curl_setopt($ch, CURLOPT_POSTFIELDS, 'mypost=' . $post_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, '');
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

function tradeno()
{
    return substr(str_shuffle('ABCDEFGHIJKLMNPQRSTUVWXYZ'), 0, 2) . substr(str_shuffle(str_repeat('123456789', 4)), 0, 6);
}

function tradenoa()
{
    return substr(str_shuffle('ABCDEFGHIJKLMNPQRSTUVWXYZ'), 0, 6);
}

function tradenob()
{
    return substr(str_shuffle(str_repeat('123456789', 4)), 0, 2);
}

function get_user($id, $type = NULL, $field = 'id')
{
    $key = md5('get_user' . $id . $type . $field);
    $data = S($key);

    if (!$data) {
        $data = M('User')->where(array($field => $id))->find();
        S($key, $data);
    }

    if ($type) {
        $rs = $data[$type];
    } else {
        $rs = $data;
    }

    return $rs;
}

function ismobile()
{
    if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
        return true;
    }

    if (isset($_SERVER['HTTP_CLIENT']) && ('PhoneClient' == $_SERVER['HTTP_CLIENT'])) {
        return true;
    }

    if (isset($_SERVER['HTTP_VIA'])) {
        return stristr($_SERVER['HTTP_VIA'], 'wap') ? true : false;
    }

    if (isset($_SERVER['HTTP_USER_AGENT'])) {
        $clientkeywords = array('nokia', 'sony', 'ericsson', 'mot', 'samsung', 'htc', 'sgh', 'lg', 'sharp', 'sie-', 'philips', 'panasonic', 'alcatel', 'lenovo', 'iphone', 'ipod', 'blackberry', 'meizu', 'android', 'netfront', 'symbian', 'ucweb', 'windowsce', 'palm', 'operamini', 'operamobi', 'openwave', 'nexusone', 'cldc', 'midp', 'wap', 'mobile');

        if (preg_match('/(' . implode('|', $clientkeywords) . ')/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
            return true;
        }
    }

    if (isset($_SERVER['HTTP_ACCEPT'])) {
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && ((strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false) || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
            return true;
        }
    }

    return false;
}

function send_cellphone($cellphone, $content)
{

    if (MOBILE_CODE == 0) {
        return 1;
    }

    session_start();
    $statusStr = array(
        0 => 'Delivered',
        1 => 'Unknown',
        2 => 'Absent Subscriber - Temporary',
        3 => 'Absent Subscriber - Permanent',
        4 => 'Call barred by user',
        5 => 'Portability Error',
        6 => 'Anti-Spam Rejection',
        7 => 'Handset Busy',
        8 => 'Network Error',
        9 => 'Illegal Number',
        10 => 'Invalid Message',
        11 => 'Unroutable',
        12 => 'Destination Un-Reachable',
        13 => 'Subscriber Age Restriction',
        14 => 'Number Blocked by Carrier',
        15 => 'Pre-Paid - Insufficent funds',
        99 => 'General Error',
    );
    $content = "[" . SHORT_NAME . "]" . $content;
    $url = "https://rest.nexmo.com/sms/json?api_key=" . C('cellphone_user') . "&api_secret=" . C('cellphone_pwd') . "&from=".SHORT_NAME."&to=$cellphone&text=" . urlencode($content);
//  Initiate curl
    $ch = curl_init();
// Disable SSL verification
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	// Will return the response, if false it print the response
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	// Set the url
    curl_setopt($ch, CURLOPT_URL, $url);
	// Execute
    $result = curl_exec($ch);
	// Closing
    curl_close($ch);

	// Will dump a beauty json :3
    $array_result = json_decode($result, true);

    $status = $array_result['messages'][0]['status'];

    if ($status != 0) {
        $data['info'] = $array_result['messages'][0]['error-text'];
        return 0;
    } else {
        return 1;
    }
}

function addtime($time = NULL, $type = NULL)
{
    if (empty($time)) {
        return '---';
    }

    if (($time < 2545545) && (1893430861 < $time)) {
        return '---';
    }

    if (empty($type)) {
        $type = 'Y-m-d H:i:s';
    }

    return date($type, $time);
}

function check($data, $rule = NULL, $ext = NULL)
{
    $data = trim(str_replace(PHP_EOL, '', $data));

    if (empty($data)) {
        return false;
    }

    $validate['require'] = '/.+/';
    $validate['url'] = '/^http(s?):\\/\\/(?:[A-za-z0-9-]+\\.)+[A-za-z]{2,4}(?:[\\/\\?#][\\/=\\?%\\-&~`@[\\]\':+!\\.#\\w]*)?$/';
    $validate['currency'] = '/^\\d+(\\.\\d+)?$/';
    $validate['number'] = '/^\\d+$/';
    $validate['zip'] = '/^\\d{6}$/';
    $validate['usd'] = '/^(([1-9]{1}\\d*)|([0]{1}))(\\.(\\d){1,2})?$/';
    $validate['integer'] = '/^[\\+]?\\d+$/';
    $validate['double'] = '/^[\\+]?\\d+(\\.\\d+)?$/';
    $validate['english'] = '/^[A-Za-z ]+$/';
    $validate['idcard'] = '/^[A-Za-z0-9- ]+$/';
    $validate['truename'] = "/^[\\p{L} .'-]+$/";
    $validate['username'] = '/^[a-zA-Z]{1}[0-9a-zA-Z_]{4,15}$/';
    $validate['email'] = '/^\\w+([-+.]\\w+)*@\\w+([-.]\\w+)*\\.\\w+([-.]\\w+)*$/';
    $validate['cellphone'] = '/^[0-9]{6,15}+$/';
	$validate['mostregex'] = '/^[a-zA-Z0-9_ \\@\\#\\$\\%\\^\\&\\*\\(\\)\\!\\,\\.\\?\\-\\+\\|\\=]{1,200}$/';
	$validate['general']='/^[a-zA-Z0-9\\\/\-:\.#()\[\], ]{0,200}$/';
    $validate['password'] = '/^[a-zA-Z0-9_\\@\\#\\$\\%\\^\\&\\*\\(\\)\\!\\,\\.\\?\\-\\+\\|\\=]{5,16}$/';
    $validate['xnb'] = '/^[a-zA-Z]$/';
	$validate['market'] = '/^[a-zA-Z_]{3,16}$/';
    if (isset($validate[strtolower($rule)])) {
        $rule = $validate[strtolower($rule)];
        return preg_match($rule, $data);
    }

    $Ap = ' 0-9a-zA-Z\\@\\#\\$\\%\\^\\&\\*\\(\\)\\!\\,\\.\\?\\-\\+\\|\\=';
    $Cp = ' 0-9a-zA-Z\\@\\#\\$\\%\\^\\&\\*\\(\\)\\!\\,\\.\\?\\-\\+\\|\\=';
    $Dp = '0-9';
    $Wp = 'a-zA-Z';
    $Np = 'a-z';
    $Tp = '@#$%^&*()-+=';
    $_p = '_';
    $pattern = '/^[';
    $OArr = str_split(strtolower($rule));
    in_array('a', $OArr) && ($pattern .= $Ap);
    in_array('c', $OArr) && ($pattern .= $Cp);
    in_array('d', $OArr) && ($pattern .= $Dp);
    in_array('w', $OArr) && ($pattern .= $Wp);
    in_array('n', $OArr) && ($pattern .= $Np);
    in_array('t', $OArr) && ($pattern .= $Tp);
    in_array('_', $OArr) && ($pattern .= $_p);
    isset($ext) && ($pattern .= $ext);
    $pattern .= ']+$/u';
    return preg_match($pattern, $data);
}

function xcheck_arr($checkArr)
{
    if (!is_array($checkArr)) {
        return false;
    }
    $check_ok = true;
    while ($value = current($checkArr)) {
        $result = key($checkArr);
        if (!empty($result)) {
            $check_ok &= checkStr($value, $result);
        }
        next($checkArr);
    }
    reset($checkArr);
    return $check_ok;
}

function check_arr($rs)
{
    if (!is_array($rs)) {
        return false;
    }
    return true;
}

function xxcheck_arr($rs)
{
    foreach ($rs as $key => $value) {
        if (!$key) {
            return false;
        }
    }

    return true;
}

function maxArrayKey($arr, $key)
{
    $a = 0;

    foreach ($arr as $k => $v) {
        $a = max($v[$key], $a);
    }

    return $a;
}

function arr2str($arr, $sep = ',')
{
    return implode($sep, $arr);
}

function str2arr($str, $sep = ',')
{
    return explode($sep, $str);
}

function url($link = '', $param = '', $default = '')
{
    return $default ? $default : U($link, $param);
}

function rmdirr($dirname)
{
    if (!file_exists($dirname)) {
        return false;
    }

    if (is_file($dirname) || is_link($dirname)) {
        return unlink($dirname);
    }

    $dir = dir($dirname);

    if ($dir) {
        while (false !== $entry = $dir->read()) {
            if (($entry == '.') || ($entry == '..')) {
                continue;
            }

            rmdirr($dirname . DIRECTORY_SEPARATOR . $entry);
        }
    }

    $dir->close();
    return rmdir($dirname);
}

function list_to_tree($list, $pk = 'id', $pid = 'pid', $child = '_child', $root = 0)
{
    $tree = array();

    if (is_array($list)) {
        $refer = array();

        foreach ($list as $key => $data) {
            $refer[$data[$pk]] = &$list[$key];
        }

        foreach ($list as $key => $data) {
            $parentId = $data[$pid];

            if ($root == $parentId) {
                $tree[] = &$list[$key];
            } else if (isset($refer[$parentId])) {
                $parent = &$refer[$parentId];
                $parent[$child][] = &$list[$key];
            }
        }
    }

    return $tree;
}

function tree_to_list($tree, $child = '_child', $order = 'id', &$list = array())
{
    if (is_array($tree)) {
        $refer = array();

        foreach ($tree as $key => $value) {
            $reffer = $value;

            if (isset($reffer[$child])) {
                unset($reffer[$child]);
                tree_to_list($value[$child], $child, $order, $list);
            }

            $list[] = $reffer;
        }

        $list = list_sort_by($list, $order, $sortby = 'asc');
    }

    return $list;
}

function list_sort_by($list, $field, $sortby = 'asc')
{
    if (is_array($list)) {
        $refer = $resultSet = array();

        foreach ($list as $i => $data) {
            $refer[$i] = &$data[$field];
        }

        switch ($sortby) {
            case 'asc':
                asort($refer);
                break;

            case 'desc':
                arsort($refer);
                break;

            case 'nat':
                natcasesort($refer);
        }

        foreach ($refer as $key => $val) {
            $resultSet[] = &$list[$key];
        }

        return $resultSet;
    }

    return false;
}

function list_search($list, $condition)
{
    if (is_string($condition)) {
        parse_str($condition, $condition);
    }

    $resultSet = array();

    foreach ($list as $key => $data) {
        $find = false;

        foreach ($condition as $field => $value) {
            if (isset($data[$field])) {
                if (0 === strpos($value, '/')) {
                    $find = preg_match($value, $data[$field]);
                } else if ($data[$field] == $value) {
                    $find = true;
                }
            }
        }

        if ($find) {
            $resultSet[] = &$list[$key];
        }
    }

    return $resultSet;
}

function d_f($name, $value, $path = DATA_PATH)
{
    if (APP_MODE == 'sae') {
        return false;
    }

    static $_cache = array();
    $filename = $path . $name . '.php';

    if ('' !== $value) {
        if (is_null($value)) {
        } else {
            $dir = dirname($filename);

            if (!is_dir($dir)) {
                mkdir($dir, 493, true);
            }

            $_cache[$name] = $value;
            $content = strip_whitespace('<?php' . "\t" . 'return ' . var_export($value, true) . ';?>') . PHP_EOL;
            return file_put_contents($filename, $content, FILE_APPEND);
        }
    }

    if (isset($_cache[$name])) {
        return $_cache[$name];
    }

    if (is_file($filename)) {
        $value = include $filename;
        $_cache[$name] = $value;
    } else {
        $value = false;
    }

    return $value;
}

function DownloadFile($fileName)
{
    ob_end_clean();
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Length: ' . filesize($fileName));
    header('Content-Disposition: attachment; filename=' . basename($fileName));
    readfile($fileName);
}

function download_file($file, $o_name = '')
{
    if (is_file($file)) {
        $length = filesize($file);
        $type = mime_content_type($file);
        $showname = ltrim(strrchr($file, '/'), '/');

        if ($o_name) {
            $showname = $o_name;
        }

        header('Content-Description: File Transfer');
        header('Content-type: ' . $type);
        header('Content-Length:' . $length);

        if (preg_match('/MSIE/', $_SERVER['HTTP_USER_AGENT'])) {
            header('Content-Disposition: attachment; filename="' . rawurlencode($showname) . '"');
        } else {
            header('Content-Disposition: attachment; filename="' . $showname . '"');
        }

        readfile($file);
        exit();
    } else {
        exit('file does not exist');
    }
}

function wb_substr($str, $len = 140, $dots = 1, $ext = '')
{
    $str = htmlspecialchars_decode(strip_tags(htmlspecialchars($str)));
    $strlenth = 0;
    $output = '';

    preg_match_all("/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/", $str, $match);


    foreach ($match[0] as $v) {
        preg_match('/[' . "\xe0" . '-' . "\xef" . '][' . "\x80" . '-' . "\xbf" . ']{2}/', $v, $matchs);

        if (!empty($matchs[0])) {
            $strlenth += 1;
        } else if (is_numeric($v)) {
            $strlenth += 0.54500000000000004;
        } else {
            $strlenth += 0.47499999999999998;
        }

        if ($len < $strlenth) {
            $output .= $ext;
            break;
        }

        $output .= $v;
    }

    if (($len < $strlenth) && $dots) {
        $output .= '...';
    }

    return $output;
}

function msubstr($str, $start = 0, $length, $charset = 'utf-8', $suffix = true)
{
    if (function_exists('mb_substr')) {
        $slice = mb_substr($str, $start, $length, $charset);
    } else if (function_exists('iconv_substr')) {
        $slice = iconv_substr($str, $start, $length, $charset);

        if (false === $slice) {
            $slice = '';
        }
    } else {
        $re['utf-8'] = '/[' . "\x1" . '-]|[' . "\xc2" . '-' . "\xdf" . '][' . "\x80" . '-' . "\xbf" . ']|[' . "\xe0" . '-' . "\xef" . '][' . "\x80" . '-' . "\xbf" . ']{2}|[' . "\xf0" . '-' . "\xff" . '][' . "\x80" . '-' . "\xbf" . ']{3}/';
        $re['gb2312'] = '/[' . "\x1" . '-]|[' . "\xb0" . '-' . "\xf7" . '][' . "\xa0" . '-' . "\xfe" . ']/';
        $re['gbk'] = '/[' . "\x1" . '-]|[' . "\x81" . '-' . "\xfe" . '][@-' . "\xfe" . ']/';
        $re['big5'] = '/[' . "\x1" . '-]|[' . "\x81" . '-' . "\xfe" . ']([@-~]|' . "\xa1" . '-' . "\xfe" . '])/';
        preg_match_all($re[$charset], $str, $match);
        $slice = join('', array_slice($match[0], $start, $length));
    }

    return $suffix ? $slice . '...' : $slice;
}

function shortFirstLetter($str, $start = 0, $length = 1, $charset = 'utf-8', $suffix = true)
{
    if (function_exists('mb_substr')) {
        $slice = mb_substr($str, $start, $length, $charset);
    } else if (function_exists('iconv_substr')) {
        $slice = iconv_substr($str, $start, $length, $charset);

        if (false === $slice) {
            $slice = '';
        }
    } else {
        $re['utf-8'] = '/[' . "\x1" . '-]|[' . "\xc2" . '-' . "\xdf" . '][' . "\x80" . '-' . "\xbf" . ']|[' . "\xe0" . '-' . "\xef" . '][' . "\x80" . '-' . "\xbf" . ']{2}|[' . "\xf0" . '-' . "\xff" . '][' . "\x80" . '-' . "\xbf" . ']{3}/';
        $re['gb2312'] = '/[' . "\x1" . '-]|[' . "\xb0" . '-' . "\xf7" . '][' . "\xa0" . '-' . "\xfe" . ']/';
        $re['gbk'] = '/[' . "\x1" . '-]|[' . "\x81" . '-' . "\xfe" . '][@-' . "\xfe" . ']/';
        $re['big5'] = '/[' . "\x1" . '-]|[' . "\x81" . '-' . "\xfe" . ']([@-~]|' . "\xa1" . '-' . "\xfe" . '])/';
        preg_match_all($re[$charset], $str, $match);
        $slice = join('', array_slice($match[0], $start, $length));
    }

    return ucfirst($slice);
}

function highlight_map($str, $keyword)
{
    return str_replace($keyword, '<em class=\'keywords\'>' . $keyword . '</em>', $str);
}

function del_file($file)
{
    $file = file_iconv($file);
    @(unlink($file));
}

function status_text($model, $key)
{
    if ($model == 'Nav') {
        $text = array('invalid', 'effective');
    }

    return $text[$key];
}

function user_auth_sign($user)
{
    ksort($user);
    $code = http_build_query($user);
    $sign = sha1($code);
    return $sign;
}

function get_link($link_id = NULL, $field = 'url')
{
    $link = '';

    if (empty($link_id)) {
        return $link;
    }

    $link = D('Url')->getById($link_id);

    if (empty($field)) {
        return $link;
    } else {
        return $link[$field];
    }
}

function get_cover($cover_id, $field = NULL)
{
    if (empty($cover_id)) {
        return false;
    }

    $picture = D('Picture')->where(array('status' => 1))->getById($cover_id);

    if ($field == 'path') {
        if (!empty($picture['url'])) {
            $picture['path'] = $picture['url'];
        } else {
            $picture['path'] = __ROOT__ . $picture['path'];
        }
    }

    return empty($field) ? $picture : $picture[$field];
}

function get_admin_name()
{
    $user = session(C('USER_AUTH_KEY'));
    return $user['admin_name'];
}

function is_login()
{
    $user = session(C('USER_AUTH_KEY'));

    if (empty($user)) {
        return 0;
    } else {
        return session(C('USER_AUTH_SIGN_KEY')) == user_auth_sign($user) ? $user['admin_id'] : 0;
    }
}

function is_administrator($uid = NULL)
{
    $uid = (is_null($uid) ? is_login() : $uid);
    return $uid && (intval($uid) === C('USER_ADMINISTRATOR'));
}

function show_tree($tree, $template)
{
    $view = new View();
    $view->assign('tree', $tree);
    return $view->fetch($template);
}

function int_to_string(&$data, $map = array(
    'status' => array(1 => 'Normal', -1 => 'Delete', 0 => 'Disable', 2 => 'Unreviewed', 3 => 'Draft')
))
{
    if (($data === false) || ($data === NULL)) {
        return $data;
    }

    $data = (array)$data;

    foreach ($data as $key => $row) {
        foreach ($map as $col => $pair) {
            if (isset($row[$col]) && isset($pair[$row[$col]])) {
                $data[$key][$col . '_text'] = $pair[$row[$col]];
            }
        }
    }

    return $data;
}

function hook($hook, $params = array())
{
    return \Think\Hook::listen($hook, $params);
}

function get_addon_class($name)
{
    $type = (strpos($name, '_') !== false ? 'lower' : 'upper');

    if ('upper' == $type) {
        $dir = \Think\Loader::parseName(lcfirst($name));
        $name = ucfirst($name);
    } else {
        $dir = $name;
        $name = \Think\Loader::parseName($name, 1);
    }

    $class = 'addons\\' . $dir . '\\' . $name;
    return $class;
}

function get_addon_config($name)
{
    $class = get_addon_class($name);

    if (class_exists($class)) {
        $addon = new $class();
        return $addon->getConfig();
    } else {
        return array();
    }
}

function addons_url($url, $param = array())
{
    $url = parse_url($url);
    $case = C('URL_CASE_INSENSITIVE');
    $addons = ($case ? parse_name($url['scheme']) : $url['scheme']);
    $controller = ($case ? parse_name($url['host']) : $url['host']);
    $action = trim($case ? strtolower($url['path']) : $url['path'], '/');

    if (isset($url['query'])) {
        parse_str($url['query'], $query);
        $param = array_merge($query, $param);
    }

    $params = array('_addons' => $addons, '_controller' => $controller, '_action' => $action);
    $params = array_merge($params, $param);
    return U('Addons/execute', $params);
}

function get_addonlist_field($data, $grid, $addon)
{
    foreach ($grid['field'] as $field) {
        $array = explode('|', $field);
        $temp = $data[$array[0]];

        if (isset($array[1])) {
            $temp = call_user_func($array[1], $temp);
        }

        $data2[$array[0]] = $temp;
    }

    if (!empty($grid['format'])) {
        $value = preg_replace_callback('/\\[([a-z_]+)\\]/', function ($match) use ($data2) {
            return $data2[$match[1]];
        }, $grid['format']);
    } else {
        $value = implode(' ', $data2);
    }

    if (!empty($grid['href'])) {
        $links = explode(',', $grid['href']);

        foreach ($links as $link) {
            $array = explode('|', $link);
            $href = $array[0];

            if (preg_match('/^\\[([a-z_]+)\\]$/', $href, $matches)) {
                $val[] = $data2[$matches[1]];
            } else {
                $show = (isset($array[1]) ? $array[1] : $value);
                $href = str_replace(array('[DELETE]', '[EDIT]', '[ADDON]'), array('del?ids=[id]&name=[ADDON]', 'edit?id=[id]&name=[ADDON]', $addon), $href);
                $href = preg_replace_callback('/\\[([a-z_]+)\\]/', function ($match) use ($data) {
                    return $data[$match[1]];
                }, $href);
                $val[] = '<a href="' . U($href) . '">' . $show . '</a>';
            }
        }

        $value = implode(' ', $val);
    }

    return $value;
}

function get_config_type($type = 0)
{
    $list = C('CONFIG_TYPE_LIST');
    return $list[$type];
}

function get_config_group($group = 0)
{
    $list = C('CONFIG_GROUP_LIST');
    return $group ? $list[$group] : '';
}

function parse_config_attr($string)
{
    $array = preg_split('/[,;\\r\\n]+/', trim($string, ',;' . "\r\n"));

    if (strpos($string, ':')) {
        $value = array();

        foreach ($array as $val) {
            list($k, $v) = explode(':', $val);
            $value[$k] = $v;
        }
    } else {
        $value = $array;
    }

    return $value;
}

function parse_field_attr($string)
{
    if (0 === strpos($string, ':')) {
        return eval(substr($string, 1) . ';');
    }

    $array = preg_split('/[,;\\r\\n]+/', trim($string, ',;' . "\r\n"));

    if (strpos($string, ':')) {
        $value = array();

        foreach ($array as $val) {
            list($k, $v) = explode(':', $val);
            $value[$k] = $v;
        }
    } else {
        $value = $array;
    }

    return $value;
}

function api($name, $vars = array())
{
    $array = explode('/', $name);
    $method = array_pop($array);
    $classname = array_pop($array);
    $module = ($array ? array_pop($array) : 'Common');
    $callback = $module . '\\Api\\' . $classname . 'Api::' . $method;

    if (is_string($vars)) {
        parse_str($vars, $vars);
    }

    return call_user_func_array($callback, $vars);
}

function think_encrypt($data, $key = '', $expire = 0)
{
    $key = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
    $data = base64_encode($data);
    $x = 0;
    $len = strlen($data);
    $l = strlen($key);
    $char = '';
    $i = 0;

    for (; $i < $len; $i++) {
        if ($x == $l) {
            $x = 0;
        }

        $char .= substr($key, $x, 1);
        $x++;
    }

    $str = sprintf('%010d', $expire ? $expire + time() : 0);
    $i = 0;

    for (; $i < $len; $i++) {
        $str .= chr(ord(substr($data, $i, 1)) + (ord(substr($char, $i, 1)) % 256));
    }

    return str_replace(array('+', '/', '='), array('-', '_', ''), base64_encode($str));
}

function think_decrypt($data, $key = '')
{
    $key = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
    $data = str_replace(array('-', '_'), array('+', '/'), $data);
    $mod4 = strlen($data) % 4;

    if ($mod4) {
        $data .= substr('====', $mod4);
    }

    $data = base64_decode($data);
    $expire = substr($data, 0, 10);
    $data = substr($data, 10);

    if ((0 < $expire) && ($expire < time())) {
        return '';
    }

    $x = 0;
    $len = strlen($data);
    $l = strlen($key);
    $char = $str = '';
    $i = 0;

    for (; $i < $len; $i++) {
        if ($x == $l) {
            $x = 0;
        }

        $char .= substr($key, $x, 1);
        $x++;
    }

    $i = 0;

    for (; $i < $len; $i++) {
        if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1))) {
            $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
        } else {
            $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
        }
    }

    return base64_decode($str);
}

function data_auth_sign($data)
{
    if (!is_array($data)) {
        $data = (array)$data;
    }

    ksort($data);
    $code = http_build_query($data);
    $sign = sha1($code);
    return $sign;
}

function format_bytes($size, $delimiter = '')
{
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    $i = 0;

    for (; $i < 5; $i++) {
        $size /= 1024;
    }

    return round($size, 2) . $delimiter . $units[$i];
}

function set_redirect_url($url)
{
    cookie('redirect_url', $url);
}

function get_redirect_url()
{
    $url = cookie('redirect_url');
    return empty($url) ? __APP__ : $url;
}

function time_format($time = NULL, $format = 'Y-m-d H:i')
{
    $time = ($time === NULL ? NOW_TIME : intval($time));
    return date($format, $time);
}

function create_dir_or_files($files)
{
    foreach ($files as $key => $value) {
        if ((substr($value, -1) == '/') && !is_dir($value)) {
            mkdir($value);
        } else {
            @(file_put_contents($value, ''));
        }
    }
}

function get_table_name($model_id = NULL)
{
    if (empty($model_id)) {
        return false;
    }

    $Model = M('Model');
    $name = '';
    $info = $Model->getById($model_id);

    if ($info['extend'] != 0) {
        $name = $Model->getFieldById($info['extend'], 'name') . '_';
    }

    $name .= $info['name'];
    return $name;
}

function get_model_attribute($model_id, $group = true)
{
    static $list;

    if (empty($model_id) || !is_numeric($model_id)) {
        return '';
    }

    if (empty($list)) {
        $list = S('attribute_list');
    }

    if (!isset($list[$model_id])) {
        $map = array('model_id' => $model_id);
        $extend = M('Model')->getFieldById($model_id, 'extend');

        if ($extend) {
            $map = array(
                'model_id' => array(
                    'in',
                    array($model_id, $extend)
                )
            );
        }

        $info = M('Attribute')->where($map)->select();
        $list[$model_id] = $info;
    }

    $attr = array();

    foreach ($list[$model_id] as $value) {
        $attr[$value['id']] = $value;
    }

    if ($group) {
        $sort = M('Model')->getFieldById($model_id, 'field_sort');

        if (empty($sort)) {
            $group = array(1 => array_merge($attr));
        } else {
            $group = json_decode($sort, true);
            $keys = array_keys($group);

            foreach ($group as &$value) {
                foreach ($value as $key => $val) {
                    $value[$key] = $attr[$val];
                    unset($attr[$val]);
                }
            }

            if (!empty($attr)) {
                $group[$keys[0]] = array_merge($group[$keys[0]], $attr);
            }
        }

        $attr = $group;
    }

    return $attr;
}

function get_table_field($value = NULL, $condition = 'id', $field = NULL, $table = NULL)
{
    if (empty($value) || empty($table)) {
        return false;
    }

    $map[$condition] = $value;
    $info = M(ucfirst($table))->where($map);

    if (empty($field)) {
        $info = $info->field(true)->find();
    } else {
        $info = $info->getField($field);
    }

    return $info;
}

function get_tag($id, $link = true)
{
    $tags = D('Article')->getFieldById($id, 'tags');

    if ($link && $tags) {
        $tags = explode(',', $tags);
        $link = array();

        foreach ($tags as $value) {
            $link[] = '<a href="' . U('/') . '?tag=' . $value . '">' . $value . '</a>';
        }

        return join($link, ',');
    } else {
        return $tags ? $tags : 'none';
    }
}

function addon_model($addon, $model)
{
    $dir = \Think\Loader::parseName(lcfirst($addon));
    $class = 'addons\\' . $dir . '\\model\\' . ucfirst($model);
    $model_path = ONETHINK_ADDON_PATH . $dir . '/model/';
    $model_filename = \Think\Loader::parseName(lcfirst($model));
    $class_file = $model_path . $model_filename . '.php';

    if (!class_exists($class)) {
        if (is_file($class_file)) {
            \Think\Loader::import($model_filename, $model_path);
        } else {
            E('Addon ' . $addon . ' Model ' . $model . ' Not Found');
        }
    }

    return new $class($model);
}

function check_server()
{
    //Do nothing
}


function clear_html($str)
{
    $str = preg_replace("/<style .*?<\/style>/is", "", $str);
    $str = preg_replace("/<script .*?<\/script>/is", "", $str);
    $str = preg_replace("/<br \s*\/?\/>/i", "\n", $str);
    $str = preg_replace("/<\/?p>/i", "\n\n", $str);
    $str = preg_replace("/<\/?td>/i", "\n", $str);
    $str = preg_replace("/<\/?div>/i", "\n", $str);
    $str = preg_replace("/<\/?blockquote>/i", "\n", $str);
    $str = preg_replace("/<\/?li>/i", "\n", $str);
    $str = preg_replace("/\&nbsp\;/i", " ", $str);
    $str = preg_replace("/\&nbsp/i", " ", $str);
    $str = preg_replace("/\&amp\;/i", "&", $str);
    $str = preg_replace("/\&amp/i", "&", $str);
    $str = preg_replace("/\&lt\;/i", "<", $str);
    $str = preg_replace("/\&lt/i", "<", $str);
    $str = preg_replace("/\&ldquo\;/i", '"', $str);
    $str = preg_replace("/\&ldquo/i", '"', $str);
    $str = preg_replace("/\&lsquo\;/i", "'", $str);
    $str = preg_replace("/\&lsquo/i", "'", $str);
    $str = preg_replace("/\&rsquo\;/i", "'", $str);
    $str = preg_replace("/\&rsquo/i", "'", $str);
    $str = preg_replace("/\&gt\;/i", ">", $str);
    $str = preg_replace("/\&gt/i", ">", $str);
    $str = preg_replace("/\&rdquo\;/i", '"', $str);
    $str = preg_replace("/\&rdquo/i", '"', $str);
    $str = strip_tags($str);
    $str = html_entity_decode($str, ENT_QUOTES);
    $str = preg_replace("/\&\#.*?\;/i", "", $str);
    return $str;
}


function codono_getCoreConfig()
{
    $file_path = DATABASE_PATH . '/c' . 'o' . 'd' . 'o' . 'n' . 'o' . '.' . 'j' . 's' . 'o' . 'n';
    if (file_exists($file_path)) {
        $codono_CoreConfig = preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents($file_path));
        $codono_CoreConfig = json_decode($codono_CoreConfig, true);
		return $codono_CoreConfig;
    } else {
        return false;
    }
}


function W_log($log)
{
    $logpath = RUNTIME_PATH . "/Logs/codono/log.html";
    $log_f = fopen($logpath, "a+");
    fputs($log_f, $log . "\r\n");
    fclose($log_f);
}


function check_codono($arr)
{

    if (is_array($arr)) {
        foreach ($arr as $key => $value) {
            if (!is_array($value)) {
                if (check_data($value)) {
                    return $value;
                }
            } else {
                check_codono($value);
            }
        }
    } else {
        if (check_data($arr)) {
            return $arr;
        }
    }
    return false;
}


function check_data($str)
{

    if (mb_strlen($str, "utf-8") > 100) {
        W_log("<br>IP: " . $_SERVER["REMOTE_ADDR"] . "<br>Time: " . strftime("%Y-%m-%d %H:%M:%S") . "<br>Page:" . $_SERVER["PHP_SELF"] . "<br>Method: " . $_SERVER["REQUEST_METHOD"] . "<br>Data: " . $str);
    }

    $args_arr = array(
        'xss' => "[\\'\\\"\\;\\*\\<\\>].*\\bon[a-zA-Z]{3,15}[\\s\\r\\n\\v\\f]*\\=|\\b(?:expression)\\(|\\<script[\\s\\\\\\/]|\\<\\!\\[cdata\\[|\\b(?:eval|alert|prompt|msgbox)\\s*\\(|url\\((?:\\#|data|javascript)",

        'sql' => "[^\\{\\s]{1}(\\s|\\b)+(?:select\\b|update\\b|insert(?:(\\/\\*.*?\\*\\/)|(\\s)|(\\+))+into\\b).+?(?:from\\b|set\\b)|[^\\{\\s]{1}(\\s|\\b)+(?:create|delete|drop|truncate|rename|desc)(?:(\\/\\*.*?\\*\\/)|(\\s)|(\\+))+(?:table\\b|from\\b|database\\b)|into(?:(\\/\\*.*?\\*\\/)|\\s|\\+)+(?:dump|out)file\\b|\\bsleep\\([\\s]*[\\d]+[\\s]*\\)|benchmark\\(([^\\,]*)\\,([^\\,]*)\\)|(?:declare|set|select)\\b.*@|union\\b.*(?:select|all)\\b|(?:select|update|insert|create|delete|drop|grant|truncate|rename|exec|desc|from|table|database|set|where)\\b.*(charset|ascii|bin|char|uncompress|concat|concat_ws|conv|export_set|hex|instr|left|load_file|locate|mid|sub|substring|oct|reverse|right|unhex)\\(|(?:master\\.\\.sysdatabases|msysaccessobjects|msysqueries|sysmodules|mysql\\.db|sys\\.database_name|information_schema\\.|sysobjects|sp_makewebtask|xp_cmdshell|sp_oamethod|sp_addextendedproc|sp_oacreate|xp_regread|sys\\.dbms_export_extension)",

        'other' => "\\.\\.[\\\\\\/].*\\%00([^0-9a-fA-F]|$)|%00[\\'\\\"\\.]");


    foreach ($args_arr as $key => $value) {
        if (preg_match("/" . $value . "/is", $str) == 1 || preg_match("/" . $value . "/is", urlencode($str)) == 1) {
            W_log("<br>IP: " . $_SERVER["REMOTE_ADDR"] . "<br>Time: " . strftime("%Y-%m-%d %H:%M:%S") . "<br>Page:" . $_SERVER["PHP_SELF"] . "<br>Method: " . $_SERVER["REQUEST_METHOD"] . "<br>Submit Data:: " . $str);
            echo "***************************************";
            return false;
            exit();
        }
    }
    return true;
}
function ErrorBox($error_here){
	$error_message= '<div class="alert alert-danger no-border">
									<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Close</span></button>
									<span class="text-semibold">Oh snap!</span>  '.$error_here.'</div>';
	echo $error_message;
}
function slugify($text)
{
  // replace non letter or digits by -
  $text = preg_replace('~[^\pL\d]+~u', '-', $text);

  // transliterate
  $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

  // remove unwanted characters
  $text = preg_replace('~[^-\w]+~', '', $text);

  // trim
  $text = trim($text, '-');

  // remove duplicate -
  $text = preg_replace('~-+~', '-', $text);

  // lowercase
  $text = strtolower($text);

  if (empty($text)) {
    return 'n-a';
  }

  return $text;
}
function discount($amount,$userid=NULL)
{
	if($userid==NULL){
	$userid=userid();
	}
	if(!$userid || $userid<0 ||!is_numeric($userid) ){
		return $amount;
	}
	$info=M('User')->where(array('id'=>$userid))->field('usertype')->find();
	$discount=0;
	if($info['usertype'])
	foreach(SUBSCRIPTION_PLANS as $plan){
			if($plan['id']==$info['usertype']){
			$discount=$plan['discount'];
			}
	}
	$amount=($amount*(100-$discount))/100;
	return $amount;
}
function systemexeption($message="We are upgrading the system! , Please refresh in sometime",$adminnote=false, $redirect=false){
			$string = file_get_contents('./Public/inline-error.html');
			$string = str_replace('$error', $message, $string);
            $string = str_replace('SITE_URL', SITE_URL, $string);
            $string = str_replace('SITE_NAME', SHORT_NAME, $string);
			if($adminnote){
				$adminnote=str_replace(PHP_EOL, "CLIENTIP[".get_client_ip()."]", $adminnote);
				clog('exception',$adminnote);
			}
			if($redirect){die($string.header("refresh:3;url=/"));}
			else{die($string);}
}
?>