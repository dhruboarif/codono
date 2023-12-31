<?php
// +----------------------------------------------------------------------
// | TOPThink [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://topthink.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: luofei614 <weibo.com/luofei614>
// +----------------------------------------------------------------------

namespace Think\Log\Driver;

class Sae
{

    protected $config = array(
        'log_time_format' => ' c ',
    );

    // InstantiationandIncoming parameters
    public function __construct($config = array())
    {
        $this->config = array_merge($this->config, $config);
    }

    /**
     * Log write Interface
     * @access public
     * @param string $log Log information
     * @param string $destination Written to the target
     * @return void
     */
    public function write($log, $destination = '')
    {
        static $is_debug = null;
        $now = date($this->config['log_time_format']);
        $logstr = "[{$now}] " . $_SERVER['REMOTE_ADDR'] . ' ' . $_SERVER['REQUEST_URI'] . "\r\n{$log}\r\n";
        if (is_null($is_debug)) {
            preg_replace('@(\w+)\=([^;]*)@e', '$appSettings[\'\\1\']="\\2";', $_SERVER['HTTP_APPCOOKIE']);
            $is_debug = in_array($_SERVER['HTTP_APPVERSION'], explode(',', $appSettings['debug'])) ? true : false;
        }
        if ($is_debug) {
            sae_set_display_errors(false);//LoggingWill notJournalprintcome out
        }
        sae_debug($logstr);
        if ($is_debug) {
            sae_set_display_errors(true);
        }

    }
}
