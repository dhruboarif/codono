<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: support@codono.com
// +----------------------------------------------------------------------
namespace Think\Cache\Driver;

use Think\Cache;

defined('THINK_PATH') or exit();

/**
 * MemcacheCache Drives
 */
class Memcache extends Cache
{

    /**
     * Architecturefunction
     * @param array $options Cache parameters
     * @access public
     */
    function __construct($options = array())
    {
        if (!extension_loaded('memcache')) {
            E(L('_NOT_SUPPORT_') . ':memcache');
        }

        $options = array_merge(array(
            'host' => C('MEMCACHE_HOST') ?: '127.0.0.1',
            'port' => C('MEMCACHE_PORT') ?: 11211,
            'timeout' => C('DATA_CACHE_TIMEOUT') ?: false,
            'persistent' => false,
        ), $options);

        $this->options = $options;
        $this->options['expire'] = isset($options['expire']) ? $options['expire'] : C('DATA_CACHE_TIME');
        $this->options['prefix'] = isset($options['prefix']) ? $options['prefix'] : C('DATA_CACHE_PREFIX');
        $this->options['length'] = isset($options['length']) ? $options['length'] : 0;
        $func = $options['persistent'] ? 'pconnect' : 'connect';
        $this->handler = new \Memcache;
        $options['timeout'] === false ?
            $this->handler->$func($options['host'], $options['port']) :
            $this->handler->$func($options['host'], $options['port'], $options['timeout']);
    }

    /**
     * Read Cache
     * @access public
     * @param string $name Cache variable name
     * @return mixed
     */
    public function get($name)
    {
        N('cache_read', 1);
        return $this->handler->get($this->options['prefix'] . $name);
    }

    /**
     * Write Cache
     * @access public
     * @param string $name Cache variable name
     * @param mixed $value Storing data
     * @param integer $expire Active Time (sec)
     * @return boolean
     */
    public function set($name, $value, $expire = null)
    {
        N('cache_write', 1);
        if (is_null($expire)) {
            $expire = $this->options['expire'];
        }
        $name = $this->options['prefix'] . $name;
        if ($this->handler->set($name, $value, 0, $expire)) {
            if ($this->options['length'] > 0) {
                // recordingCachequeue
                $this->queue($name);
            }
            return true;
        }
        return false;
    }

    /**
     * Delete Cache
     * @access public
     * @param string $name Cache variable name
     * @return boolean
     */
    public function rm($name, $ttl = false)
    {
        $name = $this->options['prefix'] . $name;
        return $ttl === false ?
            $this->handler->delete($name) :
            $this->handler->delete($name, $ttl);
    }

    /**
     * clear cache
     * @access public
     * @return boolean
     */
    public function clear()
    {
        return $this->handler->flush();
    }
}
