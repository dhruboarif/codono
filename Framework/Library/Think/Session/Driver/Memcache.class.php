<?php

namespace Think\Session\Driver;

class Memcache
{
    protected $lifeTime = 3600;
    protected $sessionName = '';
    protected $handle = null;

    /**
     * turn onSession
     * @access public
     * @param string $savePath
     * @param mixed $sessName
     */
    public function open($savePath, $sessName)
    {
        $this->lifeTime = C('SESSION_EXPIRE') ? C('SESSION_EXPIRE') : $this->lifeTime;
        // $this->sessionName  = $sessName;
        $options = array(
            'timeout' => C('SESSION_TIMEOUT') ? C('SESSION_TIMEOUT') : 1,
            'persistent' => C('SESSION_PERSISTENT') ? C('SESSION_PERSISTENT') : 0
        );
        $this->handle = new \Memcache;
        $hosts = explode(',', C('MEMCACHE_HOST'));
        $ports = explode(',', C('MEMCACHE_PORT'));
        foreach ($hosts as $i => $host) {
            $port = isset($ports[$i]) ? $ports[$i] : $ports[0];
            $this->handle->addServer($host, $port, true, 1, $options['timeout']);
        }
        return true;
    }

    /**
     * shut downSession
     * @access public
     */
    public function close()
    {
        $this->gc(ini_get('session.gc_maxlifetime'));
        $this->handle->close();
        $this->handle = null;
        return true;
    }

    /**
     * ReadSession
     * @access public
     * @param string $sessID
     */
    public function read($sessID)
    {
        return $this->handle->get($this->sessionName . $sessID);
    }

    /**
     * WriteSession
     * @access public
     * @param string $sessID
     * @param String $sessData
     */
    public function write($sessID, $sessData)
    {
        return $this->handle->set($this->sessionName . $sessID, $sessData, 0, $this->lifeTime);
    }

    /**
     * deleteSession
     * @access public
     * @param string $sessID
     */
    public function destroy($sessID)
    {
        return $this->handle->delete($this->sessionName . $sessID);
    }

    /**
     * Session Garbage Collection
     * @access public
     * @param string $sessMaxLifeTime
     */
    public function gc($sessMaxLifeTime)
    {
        return true;
    }
}
