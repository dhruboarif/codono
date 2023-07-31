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

namespace Think\Db;

use Think\Config;
use Think\Debug;
use Think\Log;
use PDO;

abstract class Driver
{
    // PDOOperation Example
    protected $PDOStatement = null;
    // currentoperatingbelong tomodel Name
    protected $model = '_think_';
    // currentSQLinstruction
    protected $queryStr = '';
    protected $modelSql = array();
    // At lastinsertID
    protected $lastInsID = null;
    // returnorinfluencesrecordingnumber
    protected $numRows = 0;
    // Affairsinstructionnumber
    protected $transTimes = 0;
    // Error Messages
    protected $error = '';
    // Database ConnectivityID Support for multipleconnection
    protected $linkID = array();
    // currentconnectionID
    protected $_linkID = null;
    // Database ConnectivityparameterConfiguration
    protected $config = array(
        'type' => '',     // databaseTypes of
        'hostname' => '127.0.0.1', // serveraddress
        'database' => '',          // DatabaseName
        'username' => '',      // username
        'password' => '',          // password
        'hostport' => '',        // port
        'dsn' => '', //
        'params' => array(), // Database Connectivityparameter
        'charset' => 'utf8',      // databasecodingdefaultuseutf8
        'prefix' => '',    // databaseTable Prefix
        'debug' => false, // Database Debuggingmode
        'deploy' => 0, // databasedeploythe way:0 centralized(singleserver),1 distributed(Master-slaveserver)
        'rw_separate' => false,       // databaseRead and writewhetherSeparate Master-slave effective
        'master_num' => 1, // After the separate read and write Primary serverQuantity
        'slave_no' => '', // DesignationFromserverNo.
        'db_like_fields' => '',
    );
    // databaseexpression
    protected $exp = array('eq' => '=', 'neq' => '<>', 'gt' => '>', 'egt' => '>=', 'lt' => '<', 'elt' => '<=', 'notlike' => 'NOT LIKE', 'like' => 'LIKE', 'in' => 'IN', 'notin' => 'NOT IN', 'not in' => 'NOT IN', 'between' => 'BETWEEN', 'not between' => 'NOT BETWEEN', 'notbetween' => 'NOT BETWEEN');
    // Query Expression
    protected $selectSql = 'SELECT%DISTINCT% %FIELD% FROM %TABLE%%FORCE%%JOIN%%WHERE%%GROUP%%HAVING%%ORDER%%LIMIT% %UNION%%LOCK%%COMMENT%';
    // Inquirefrequency
    protected $queryTimes = 0;
    // carried outfrequency
    protected $executeTimes = 0;
    // PDOConnection parameters
    protected $options = array(
        PDO::ATTR_CASE => PDO::CASE_LOWER,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,
        PDO::ATTR_STRINGIFY_FETCHES => false,
    );
    protected $bind = array(); // Parameter binding

    /**
     * Architecturefunction ReadDatabase configuration information
     * @access public
     * @param array $config Database configuration array
     */
    public function __construct($config = '')
    {
        if (!empty($config)) {
            $this->config = array_merge($this->config, $config);
            if (is_array($this->config['params'])) {
                $this->options = $this->config['params'] + $this->options;
            }
        }
    }

    /**
     * Database connection method
     * @access public
     */
    public function connect($config = '', $linkNum = 0, $autoConnection = false)
    {
        if (!isset($this->linkID[$linkNum])) {
            if (empty($config)) $config = $this->config;
            try {
                if (empty($config['dsn'])) {
                    $config['dsn'] = $this->parseDsn($config);
                }
                if (version_compare(PHP_VERSION, '5.3.6', '<=')) {
                    // Disable mold
                    $this->options[PDO::ATTR_EMULATE_PREPARES] = false;
                }
                $this->linkID[$linkNum] = new PDO($config['dsn'], $config['username'], $config['password'], $this->options);
            } catch (\PDOException $e) {
                //                var_dump($e->getMessage()); //MYSQL is down
                die('We are upgrading the system! , Please refresh in sometime' . header("refresh:3;url=/"));
                if ($autoConnection) {
                    trace($e->getMessage(), '', 'ERR');
                    return $this->connect($autoConnection, $linkNum);
                } elseif ($config['debug']) {
                    E($e->getMessage());
                }
            }
        }
        return $this->linkID[$linkNum];
    }

    /**
     * Resolvepdoconnecteddsninformation
     * @access public
     * @param array $config Connection Information
     * @return string
     */
    protected function parseDsn($config)
    {
    }

    /**
     * Free result
     * @access public
     */
    public function free()
    {
        $this->PDOStatement = null;
    }

    /**
     * Execute the query Return data sets
     * @access public
     * @param string $str sqlinstruction
     * @param boolean $fetchSql Just do not get executedSQL
     * @return mixed
     */
    public function query($str, $fetchSql = false)
    {
        $this->initConnect(false);
        if (!$this->_linkID) return false;
        $this->queryStr = $str;
        if (!empty($this->bind)) {
            $that = $this;
            $this->queryStr = strtr($this->queryStr, array_map(function ($val) use ($that) {
                return '\'' . $that->escapeString($val) . '\'';
            }, $this->bind));
        }
        if ($fetchSql) {
            return $this->queryStr;
        }
        //Previous releaseofsearch result
        if (!empty($this->PDOStatement)) $this->free();
        $this->queryTimes++;
        N('db_query', 1); // compatibleCode
        // debuggingStart
        $this->debug(true);
        $this->PDOStatement = $this->_linkID->prepare($str);
        if (false === $this->PDOStatement) {
            $this->error();
            return false;
        }
        foreach ($this->bind as $key => $val) {
            if (is_array($val)) {
                $this->PDOStatement->bindValue($key, $val[0], $val[1]);
            } else {
                $this->PDOStatement->bindValue($key, $val);
            }
        }
        $this->bind = array();
        try {
            $result = $this->PDOStatement->execute();
            // debuggingEnd
            $this->debug(false);
            if (false === $result) {
                $this->error();
                return false;
            } else {
                return $this->getResult();
            }
        } catch (\PDOException $e) {
            $this->error();
            return false;
        }
    }

    /**
     * Execute the statement
     * @access public
     * @param string $str sqlinstruction
     * @param boolean $fetchSql Just do not get executedSQL
     * @return mixed
     */
    public function execute($str, $fetchSql = false)
    {
        $this->initConnect(true);
        if (!$this->_linkID) return false;
        $this->queryStr = $str;
        if (!empty($this->bind)) {
            $that = $this;
            $this->queryStr = strtr($this->queryStr, array_map(function ($val) use ($that) {
                return '\'' . $that->escapeString($val) . '\'';
            }, $this->bind));
        }
        if ($fetchSql) {
            return $this->queryStr;
        }
        //Previous releaseofsearch result
        if (!empty($this->PDOStatement)) $this->free();
        $this->executeTimes++;
        N('db_write', 1); // compatibleCode
        // recordingStartcarried outtime
        $this->debug(true);
        $this->PDOStatement = $this->_linkID->prepare($str);
        if (false === $this->PDOStatement) {
            $this->error();
            return false;
        }
        foreach ($this->bind as $key => $val) {
            if (is_array($val)) {
                $this->PDOStatement->bindValue($key, $val[0], $val[1]);
            } else {
                $this->PDOStatement->bindValue($key, $val);
            }
        }
        $this->bind = array();
        try {
            $result = $this->PDOStatement->execute();
            // debuggingEnd
            $this->debug(false);
            if (false === $result) {
                $this->error();
                return false;
            } else {
                $this->numRows = $this->PDOStatement->rowCount();
                if (preg_match("/^\s*(INSERT\s+INTO|REPLACE\s+INTO)\s+/i", $str)) {
                    $this->lastInsID = $this->_linkID->lastInsertId();
                }
                return $this->numRows;
            }
        } catch (\PDOException $e) {
            $this->error();
            return false;
        }
    }

    /**
     * Start affairs
     * @access public
     * @return void
     */
    public function startTrans()
    {
        $this->initConnect(true);
        if (!$this->_linkID) return false;
        //datarollback stand by
        if ($this->transTimes == 0) {
            $this->_linkID->beginTransaction();
        }
        $this->transTimes++;
        return;
    }

    /**
     * Fornon-Auto Commitstatusthe followingofInquiresubmit
     * @access public
     * @return boolean
     */
    public function commit()
    {
        if ($this->transTimes > 0) {
            $result = $this->_linkID->commit();
            $this->transTimes = 0;
            if (!$result) {
                $this->error();
                return false;
            }
        }
        return true;
    }

    /**
     * Transaction rollback
     * @access public
     * @return boolean
     */
    public function rollback()
    {
        if ($this->transTimes > 0) {
            $result = $this->_linkID->rollback();
            $this->transTimes = 0;
            if (!$result) {
                $this->error();
                return false;
            }
        }
        return true;
    }

    /**
     * obtainallofQuery data
     * @access private
     * @return array
     */
    private function getResult()
    {
        //Return data sets
        $result = $this->PDOStatement->fetchAll(PDO::FETCH_ASSOC);
        $this->numRows = count($result);
        return $result;
    }

    /**
     * Get queries
     * @access public
     * @param boolean $execute Are all of the query
     * @return integer
     */
    public function getQueryTimes($execute = false)
    {
        return $execute ? $this->queryTimes + $this->executeTimes : $this->queryTimes;
    }

    /**
     * Get the number of executions
     * @access public
     * @return integer
     */
    public function getExecuteTimes()
    {
        return $this->executeTimes;
    }

    /**
     * Close the database
     * @access public
     */
    public function close()
    {
        $this->_linkID = null;
    }

    /**
     * Database Error Messages
     * And displays the currentSQLStatement
     * @access public
     * @return string
     */
    public function error()
    {
        if ($this->PDOStatement) {
            $error = $this->PDOStatement->errorInfo();
            $this->error = $error[1] . ':' . $error[2];
        } else {
            $this->error = '';
        }
        if ('' != $this->queryStr) {
            $this->error .= "\n [ SQLStatement ] : " . $this->queryStr;
        }
        // recordingerrorJournal
        trace($this->error, '', 'ERR');
        if ($this->config['debug']) {// OpenDatabase Debuggingmode
            E($this->error);
        } else {
            return $this->error;
        }
    }

    /**
     * Set the lock mechanism
     * @access protected
     * @return string
     */
    protected function parseLock($lock = false)
    {
        return $lock ? ' FOR UPDATE ' : '';
    }

    /**
     * setanalysis
     * @access protected
     * @param array $data
     * @return string
     */
    protected function parseSet($data)
    {
        foreach ($data as $key => $val) {
            if (is_array($val) && 'exp' == $val[0]) {
                $set[] = $this->parseKey($key) . '=' . $val[1];
            } elseif (is_null($val)) {
                $set[] = $this->parseKey($key) . '=NULL';
            } elseif (is_scalar($val)) {// Filter nonscalardata
                if (0 === strpos($val, ':') && in_array($val, array_keys($this->bind))) {
                    $set[] = $this->parseKey($key) . '=' . $this->escapeString($val);
                } else {
                    $name = count($this->bind);
                    $set[] = $this->parseKey($key) . '=:' . $name;
                    $this->bindParam($name, $val);
                }
            }
        }
        return ' SET ' . implode(',', $set);
    }

    /**
     * Parameter binding
     * @access protected
     * @param string $name BindingParam Name
     * @param mixed $value Bind values
     * @return void
     */
    protected function bindParam($name, $value)
    {
        $this->bind[':' . $name] = $value;
    }

    /**
     * Analysis of field names
     * @access protected
     * @param string $key
     * @return string
     */
    protected function parseKey(&$key)
    {
        return $key;
    }

    /**
     * valueanalysis
     * @access protected
     * @param mixed $value
     * @return string
     */
    protected function parseValue($value)
    {
        if (is_string($value)) {
            $value = strpos($value, ':') === 0 && in_array($value, array_keys($this->bind)) ? $this->escapeString($value) : '\'' . $this->escapeString($value) . '\'';
        } elseif (isset($value[0]) && is_string($value[0]) && strtolower($value[0]) == 'exp') {
            $value = $this->escapeString($value[1]);
        } elseif (is_array($value)) {
            $value = array_map(array($this, 'parseValue'), $value);
        } elseif (is_bool($value)) {
            $value = $value ? '1' : '0';
        } elseif (is_null($value)) {
            $value = 'null';
        }
        return $value;
    }

    /**
     * fieldanalysis
     * @access protected
     * @param mixed $fields
     * @return string
     */
    protected function parseField($fields)
    {
        if (is_string($fields) && '' !== $fields) {
            $fields = explode(',', $fields);
        }
        if (is_array($fields)) {
            // perfectArraythe waypassField Nameofstand by
            // stand by 'field1'=>'field2' SuchFieldAlias definition
            $array = array();
            foreach ($fields as $key => $field) {
                if (!is_numeric($key))
                    $array[] = $this->parseKey($key) . ' AS ' . $this->parseKey($field);
                else
                    $array[] = $this->parseKey($field);
            }
            $fieldsStr = implode(',', $array);
        } else {
            $fieldsStr = '*';
        }
        //TODO ifInquireCompleteField,And isjoinThe way,Then put to checkoftable加MoreSlug,so as not toFieldIscover
        return $fieldsStr;
    }

    /**
     * tableanalysis
     * @access protected
     * @param mixed $table
     * @return string
     */
    protected function parseTable($tables)
    {
        if (is_array($tables)) {// stand byAlias definition
            $array = array();
            foreach ($tables as $table => $alias) {
                if (!is_numeric($table))
                    $array[] = $this->parseKey($table) . ' ' . $this->parseKey($alias);
                else
                    $array[] = $this->parseKey($alias);
            }
            $tables = $array;
        } elseif (is_string($tables)) {
            $tables = explode(',', $tables);
            array_walk($tables, array(&$this, 'parseKey'));
        }
        return implode(',', $tables);
    }

    /**
     * whereanalysis
     * @access protected
     * @param mixed $where
     * @return string
     */
    protected function parseWhere($where)
    {
        $whereStr = '';
        if (is_string($where)) {
            // directuseStringcondition
            $whereStr = $where;
        } else { // useArrayexpression
            $operate = isset($where['_logic']) ? strtoupper($where['_logic']) : '';
            if (in_array($operate, array('AND', 'OR', 'XOR'))) {
                // definitionlogicOperationrule E.g OR XOR AND NOT
                $operate = ' ' . $operate . ' ';
                unset($where['_logic']);
            } else {
                // defaultEnterRow AND Operation
                $operate = ' AND ';
            }
            foreach ($where as $key => $val) {
                if (is_numeric($key)) {
                    $key = '_complex';
                }
                if (0 === strpos($key, '_')) {
                    // ResolvespecialConditional expression
                    $whereStr .= $this->parseThinkWhere($key, $val);
                } else {
                    // InquireFieldofSecurity filtering
                    // if(!preg_match('/^[A-Z_\|\&\-.a-z0-9\(\)\,]+$/',trim($key))){
                    //     E(L('_EXPRESS_ERROR_').':'.$key);
                    // }
                    // manyconditionstand by
                    $multi = is_array($val) && isset($val['_multi']);
                    $key = trim($key);
                    if (strpos($key, '|')) { // stand by name|title|nickname the waydefinitionInquireField
                        $array = explode('|', $key);
                        $str = array();
                        foreach ($array as $m => $k) {
                            $v = $multi ? $val[$m] : $val;
                            $str[] = $this->parseWhereItem($this->parseKey($k), $v);
                        }
                        $whereStr .= '( ' . implode(' OR ', $str) . ' )';
                    } elseif (strpos($key, '&')) {
                        $array = explode('&', $key);
                        $str = array();
                        foreach ($array as $m => $k) {
                            $v = $multi ? $val[$m] : $val;
                            $str[] = '(' . $this->parseWhereItem($this->parseKey($k), $v) . ')';
                        }
                        $whereStr .= '( ' . implode(' AND ', $str) . ' )';
                    } else {
                        $whereStr .= $this->parseWhereItem($this->parseKey($key), $val);
                    }
                }
                $whereStr .= $operate;
            }
            $whereStr = substr($whereStr, 0, -strlen($operate));
        }
        return empty($whereStr) ? '' : ' WHERE ' . $whereStr;
    }

    // whereSub-unit analysis
    protected function parseWhereItem($key, $val)
    {
        $whereStr = '';
        if (is_array($val)) {
            if (is_string($val[0])) {
                $exp = strtolower($val[0]);
                if (preg_match('/^(eq|neq|gt|egt|lt|elt)$/', $exp)) { // CompareOperation
                    $whereStr .= $key . ' ' . $this->exp[$exp] . ' ' . $this->parseValue($val[1]);
                } elseif (preg_match('/^(notlike|like)$/', $exp)) {// blurrySeek
                    if (is_array($val[1])) {
                        $likeLogic = isset($val[2]) ? strtoupper($val[2]) : 'OR';
                        if (in_array($likeLogic, array('AND', 'OR', 'XOR'))) {
                            $like = array();
                            foreach ($val[1] as $item) {
                                $like[] = $key . ' ' . $this->exp[$exp] . ' ' . $this->parseValue($item);
                            }
                            $whereStr .= '(' . implode(' ' . $likeLogic . ' ', $like) . ')';
                        }
                    } else {
                        $whereStr .= $key . ' ' . $this->exp[$exp] . ' ' . $this->parseValue($val[1]);
                    }
                } elseif ('bind' == $exp) { // useexpression
                    $whereStr .= $key . ' = :' . $val[1];
                } elseif ('exp' == $exp) { // useexpression
                    $whereStr .= $key . ' ' . $val[1];
                } elseif (preg_match('/^(notin|not in|in)$/', $exp)) { // IN Operation
                    if (isset($val[2]) && 'exp' == $val[2]) {
                        $whereStr .= $key . ' ' . $this->exp[$exp] . ' ' . $val[1];
                    } else {
                        if (is_string($val[1])) {
                            $val[1] = explode(',', $val[1]);
                        }
                        $zone = implode(',', $this->parseValue($val[1]));
                        $whereStr .= $key . ' ' . $this->exp[$exp] . ' (' . $zone . ')';
                    }
                } elseif (preg_match('/^(notbetween|not between|between)$/', $exp)) { // BETWEENOperation
                    $data = is_string($val[1]) ? explode(',', $val[1]) : $val[1];
                    $whereStr .= $key . ' ' . $this->exp[$exp] . ' ' . $this->parseValue($data[0]) . ' AND ' . $this->parseValue($data[1]);
                } else {
                    E(L('_EXPRESS_ERROR_') . ':' . $val[0]);
                }
            } else {
                $count = count($val);
                $rule = isset($val[$count - 1]) ? (is_array($val[$count - 1]) ? strtoupper($val[$count - 1][0]) : strtoupper($val[$count - 1])) : '';
                if (in_array($rule, array('AND', 'OR', 'XOR'))) {
                    $count = $count - 1;
                } else {
                    $rule = 'AND';
                }
                for ($i = 0; $i < $count; $i++) {
                    $data = is_array($val[$i]) ? $val[$i][1] : $val[$i];
                    if ('exp' == strtolower($val[$i][0])) {
                        $whereStr .= $key . ' ' . $data . ' ' . $rule . ' ';
                    } else {
                        $whereStr .= $this->parseWhereItem($key, $val[$i]) . ' ' . $rule . ' ';
                    }
                }
                $whereStr = '( ' . substr($whereStr, 0, -4) . ' )';
            }
        } else {
            //CorrectStringTypes ofFielduseFuzzy matching
            $likeFields = $this->config['db_like_fields'];
            if ($likeFields && preg_match('/^(' . $likeFields . ')$/i', $key)) {
                $whereStr .= $key . ' LIKE ' . $this->parseValue('%' . $val . '%');
            } else {
                $whereStr .= $key . ' = ' . $this->parseValue($val);
            }
        }
        return $whereStr;
    }

    /**
     * Special Conditions
     * @access protected
     * @param string $key
     * @param mixed $val
     * @return string
     */
    protected function parseThinkWhere($key, $val)
    {
        $whereStr = '';
        switch ($key) {
            case '_string':
                // StringmodeQuery conditions
                $whereStr = $val;
                break;
            case '_complex':
                // complexQuery conditions
                $whereStr = substr($this->parseWhere($val), 6);
                break;
            case '_query':
                // StringmodeQuery conditions
                parse_str($val, $where);
                if (isset($where['_logic'])) {
                    $op = ' ' . strtoupper($where['_logic']) . ' ';
                    unset($where['_logic']);
                } else {
                    $op = ' AND ';
                }
                $array = array();
                foreach ($where as $field => $data)
                    $array[] = $this->parseKey($field) . ' = ' . $this->parseValue($data);
                $whereStr = implode($op, $array);
                break;
        }
        return '( ' . $whereStr . ' )';
    }

    /**
     * limitanalysis
     * @access protected
     * @param mixed $lmit
     * @return string
     */
    protected function parseLimit($limit)
    {
        return !empty($limit) ? ' LIMIT ' . $limit . ' ' : '';
    }

    /**
     * joinanalysis
     * @access protected
     * @param mixed $join
     * @return string
     */
    protected function parseJoin($join)
    {
        $joinStr = '';
        if (!empty($join)) {
            $joinStr = ' ' . implode(' ', $join) . ' ';
        }
        return $joinStr;
    }

    /**
     * orderanalysis
     * @access protected
     * @param mixed $order
     * @return string
     */
    protected function parseOrder($order)
    {
        if (is_array($order)) {
            $array = array();
            foreach ($order as $key => $val) {
                if (is_numeric($key)) {
                    $array[] = $this->parseKey($val);
                } else {
                    $array[] = $this->parseKey($key) . ' ' . $val;
                }
            }
            $order = implode(',', $array);
        }
        return !empty($order) ? ' ORDER BY ' . $order : '';
    }

    /**
     * groupanalysis
     * @access protected
     * @param mixed $group
     * @return string
     */
    protected function parseGroup($group)
    {
        return !empty($group) ? ' GROUP BY ' . $group : '';
    }

    /**
     * havinganalysis
     * @access protected
     * @param string $having
     * @return string
     */
    protected function parseHaving($having)
    {
        return !empty($having) ? ' HAVING ' . $having : '';
    }

    /**
     * commentanalysis
     * @access protected
     * @param string $comment
     * @return string
     */
    protected function parseComment($comment)
    {
        return !empty($comment) ? ' /* ' . $comment . ' */' : '';
    }

    /**
     * distinctanalysis
     * @access protected
     * @param mixed $distinct
     * @return string
     */
    protected function parseDistinct($distinct)
    {
        return !empty($distinct) ? ' DISTINCT ' : '';
    }

    /**
     * unionanalysis
     * @access protected
     * @param mixed $union
     * @return string
     */
    protected function parseUnion($union)
    {
        if (empty($union)) return '';
        if (isset($union['_all'])) {
            $str = 'UNION ALL ';
            unset($union['_all']);
        } else {
            $str = 'UNION ';
        }
        foreach ($union as $u) {
            $sql[] = $str . (is_array($u) ? $this->buildSelectSql($u) : $u);
        }
        return implode(' ', $sql);
    }

    /**
     * Parameter binding analysis
     * @access protected
     * @param array $bind
     * @return array
     */
    protected function parseBind($bind)
    {
        $this->bind = array_merge($this->bind, $bind);
    }

    /**
     * indexanalysis,CanoperatingChainDesignationneedEnforcementuseofindex
     * @access protected
     * @param mixed $index
     * @return string
     */
    protected function parseForce($index)
    {
        if (empty($index)) return '';
        if (is_array($index)) $index = join(",", $index);
        return sprintf(" FORCE INDEX ( %s ) ", $index);
    }

    /**
     * ON DUPLICATE KEY UPDATE analysis
     * @access protected
     * @param mixed $duplicate
     * @return string
     */
    protected function parseDuplicate($duplicate)
    {
        return '';
    }

    /**
     * Insert Record
     * @access public
     * @param mixed $data data
     * @param array $options Parameter expression
     * @param boolean $replace whetherreplace
     * @return false | integer
     */
    public function insert($data, $options = array(), $replace = false)
    {
        $values = $fields = array();
        $this->model = $options['model'];
        $this->parseBind(!empty($options['bind']) ? $options['bind'] : array());
        foreach ($data as $key => $val) {
            if (is_array($val) && 'exp' == $val[0]) {
                $fields[] = $this->parseKey($key);
                $values[] = $val[1];
            } elseif (is_null($val)) {
                $fields[] = $this->parseKey($key);
                $values[] = 'NULL';
            } elseif (is_scalar($val)) { // Filter nonscalardata
                $fields[] = $this->parseKey($key);
                if (0 === strpos($val, ':') && in_array($val, array_keys($this->bind))) {
                    $values[] = $this->parseValue($val);
                } else {
                    $name = count($this->bind);
                    $values[] = ':' . $name;
                    $this->bindParam($name, $val);
                }
            }
        }
        // compatibledigitalAfferentthe way
        $replace = (is_numeric($replace) && $replace > 0) ? true : $replace;
        $sql = (true === $replace ? 'REPLACE' : 'INSERT') . ' INTO ' . $this->parseTable($options['table']) . ' (' . implode(',', $fields) . ') VALUES (' . implode(',', $values) . ')' . $this->parseDuplicate($replace);
        $sql .= $this->parseComment(!empty($options['comment']) ? $options['comment'] : '');
        return $this->execute($sql, !empty($options['fetch_sql']) ? true : false);
    }


    /**
     * Bulk Insert record
     * @access public
     * @param mixed $dataSet data set
     * @param array $options Parameter expression
     * @param boolean $replace whetherreplace
     * @return false | integer
     */
    public function insertAll($dataSet, $options = array(), $replace = false)
    {
        $values = array();
        $this->model = $options['model'];
        if (!is_array($dataSet[0])) return false;
        $this->parseBind(!empty($options['bind']) ? $options['bind'] : array());
        $fields = array_map(array($this, 'parseKey'), array_keys($dataSet[0]));
        foreach ($dataSet as $data) {
            $value = array();
            foreach ($data as $key => $val) {
                if (is_array($val) && 'exp' == $val[0]) {
                    $value[] = $val[1];
                } elseif (is_null($val)) {
                    $value[] = 'NULL';
                } elseif (is_scalar($val)) {
                    if (0 === strpos($val, ':') && in_array($val, array_keys($this->bind))) {
                        $value[] = $this->parseValue($val);
                    } else {
                        $name = count($this->bind);
                        $value[] = ':' . $name;
                        $this->bindParam($name, $val);
                    }
                }
            }
            $values[] = 'SELECT ' . implode(',', $value);
        }
        $sql = 'INSERT INTO ' . $this->parseTable($options['table']) . ' (' . implode(',', $fields) . ') ' . implode(' UNION ALL ', $values);
        $sql .= $this->parseComment(!empty($options['comment']) ? $options['comment'] : '');
        return $this->execute($sql, !empty($options['fetch_sql']) ? true : false);
    }

    /**
     * bySelectRecord inserted
     * @access public
     * @param string $fields wantinsertofdata sheetField Name
     * @param string $table wantinsertofdata Sheet Name
     * @param array $option Query data parameters
     * @return false | integer
     */
    public function selectInsert($fields, $table, $options = array())
    {
        $this->model = $options['model'];
        $this->parseBind(!empty($options['bind']) ? $options['bind'] : array());
        if (is_string($fields)) $fields = explode(',', $fields);
        array_walk($fields, array($this, 'parseKey'));
        $sql = 'INSERT INTO ' . $this->parseTable($table) . ' (' . implode(',', $fields) . ') ';
        $sql .= $this->buildSelectSql($options);
        return $this->execute($sql, !empty($options['fetch_sql']) ? true : false);
    }

    /**
     * update record
     * @access public
     * @param mixed $data data
     * @param array $options expression
     * @return false | integer
     */
    public function update($data, $options)
    {
        $this->model = $options['model'];
        $this->parseBind(!empty($options['bind']) ? $options['bind'] : array());
        $table = $this->parseTable($options['table']);
        $sql = 'UPDATE ' . $table . $this->parseSet($data);
        if (strpos($table, ',')) {// Multi-table update supportJOINoperating
            $sql .= $this->parseJoin(!empty($options['join']) ? $options['join'] : '');
        }
        $sql .= $this->parseWhere(!empty($options['where']) ? $options['where'] : '');
        if (!strpos($table, ',')) {
            //  Single table update supportorderwithlmit
            $sql .= $this->parseOrder(!empty($options['order']) ? $options['order'] : '')
                . $this->parseLimit(!empty($options['limit']) ? $options['limit'] : '');
        }
        $sql .= $this->parseComment(!empty($options['comment']) ? $options['comment'] : '');
        return $this->execute($sql, !empty($options['fetch_sql']) ? true : false);
    }

    /**
     * Delete Record
     * @access public
     * @param array $options expression
     * @return false | integer
     */
    public function delete($options = array())
    {
        $this->model = $options['model'];
        $this->parseBind(!empty($options['bind']) ? $options['bind'] : array());
        $table = $this->parseTable($options['table']);
        $sql = 'DELETE FROM ' . $table;
        if (strpos($table, ',')) {// Multi-table delete supportUSINGwithJOINoperating
            if (!empty($options['using'])) {
                $sql .= ' USING ' . $this->parseTable($options['using']) . ' ';
            }
            $sql .= $this->parseJoin(!empty($options['join']) ? $options['join'] : '');
        }
        $sql .= $this->parseWhere(!empty($options['where']) ? $options['where'] : '');
        if (!strpos($table, ',')) {
            // To delete a single table supportorderwithlimit
            $sql .= $this->parseOrder(!empty($options['order']) ? $options['order'] : '')
                . $this->parseLimit(!empty($options['limit']) ? $options['limit'] : '');
        }
        $sql .= $this->parseComment(!empty($options['comment']) ? $options['comment'] : '');
        return $this->execute($sql, !empty($options['fetch_sql']) ? true : false);
    }

    /**
     * Find Record
     * @access public
     * @param array $options expression
     * @return mixed
     */
    public function select($options = array())
    {
        $this->model = $options['model'];
        $this->parseBind(!empty($options['bind']) ? $options['bind'] : array());
        $sql = $this->buildSelectSql($options);
        $result = $this->query($sql, !empty($options['fetch_sql']) ? true : false);
        return $result;
    }

    /**
     * Generating a querySQL
     * @access public
     * @param array $options expression
     * @return string
     */
    public function buildSelectSql($options = array())
    {
        if (isset($options['page'])) {
            // according toPagesComputelimit
            list($page, $listRows) = $options['page'];
            $page = $page > 0 ? $page : 1;
            $listRows = $listRows > 0 ? $listRows : (is_numeric($options['limit']) ? $options['limit'] : 20);
            $offset = $listRows * ($page - 1);
            $options['limit'] = $offset . ',' . $listRows;
        }
        $sql = $this->parseSql($this->selectSql, $options);
        return $sql;
    }

    /**
     * replaceSQLStatement expression
     * @access public
     * @param array $options expression
     * @return string
     */
    public function parseSql($sql, $options = array())
    {
        $sql = str_replace(
            array('%TABLE%', '%DISTINCT%', '%FIELD%', '%JOIN%', '%WHERE%', '%GROUP%', '%HAVING%', '%ORDER%', '%LIMIT%', '%UNION%', '%LOCK%', '%COMMENT%', '%FORCE%'),
            array(
                $this->parseTable($options['table']),
                $this->parseDistinct(isset($options['distinct']) ? $options['distinct'] : false),
                $this->parseField(!empty($options['field']) ? $options['field'] : '*'),
                $this->parseJoin(!empty($options['join']) ? $options['join'] : ''),
                $this->parseWhere(!empty($options['where']) ? $options['where'] : ''),
                $this->parseGroup(!empty($options['group']) ? $options['group'] : ''),
                $this->parseHaving(!empty($options['having']) ? $options['having'] : ''),
                $this->parseOrder(!empty($options['order']) ? $options['order'] : ''),
                $this->parseLimit(!empty($options['limit']) ? $options['limit'] : ''),
                $this->parseUnion(!empty($options['union']) ? $options['union'] : ''),
                $this->parseLock(isset($options['lock']) ? $options['lock'] : false),
                $this->parseComment(!empty($options['comment']) ? $options['comment'] : ''),
                $this->parseForce(!empty($options['force']) ? $options['force'] : '')
            ), $sql);
        return $sql;
    }

    /**
     * ObtainLastInquireofsqlStatement
     * @param string $model model Name
     * @access public
     * @return string
     */
    public function getLastSql($model = '')
    {
        return $model ? $this->modelSql[$model] : $this->queryStr;
    }

    /**
     * Acquired recently insertedID
     * @access public
     * @return string
     */
    public function getLastInsID()
    {
        return $this->lastInsID;
    }

    /**
     * Obtainmost近ofError Messages
     * @access public
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * SQLInstruction security filtering
     * @access public
     * @param string $str SQLString
     * @return string
     */
    public function escapeString($str)
    {
        return addslashes($str);
    }

    /**
     * Set the current operating model
     * @access public
     * @param string $model model Name
     * @return void
     */
    public function setModel($model)
    {
        $this->model = $model;
    }

    /**
     * Database Debugging The current recordSQL
     * @access protected
     * @param boolean $start Debugging start tag true Start false End
     */
    protected function debug($start)
    {
        if ($this->config['debug']) {// OpenDatabase Debuggingmode
            if ($start) {
                G('queryStartTime');
            } else {
                $this->modelSql[$this->model] = $this->queryStr;
                //$this->model  =   '_think_';
                // recordingoperatingEndtime
                G('queryEndTime');
                trace($this->queryStr . ' [ RunTime:' . G('queryStartTime', 'queryEndTime') . 's ]', '', 'SQL');
            }
        }
    }

    /**
     * Initialize the database connection
     * @access protected
     * @param boolean $master Primary server
     * @return void
     */
    protected function initConnect($master = true)
    {
        if (!empty($this->config['deploy']))
            // usedistributeddatabase
            $this->_linkID = $this->multiConnect($master);
        else
            // defaultsingledatabase
            if (!$this->_linkID) $this->_linkID = $this->connect();
    }

    /**
     * Distributed server connection
     * @access protected
     * @param boolean $master Primary server
     * @return void
     */
    protected function multiConnect($master = false)
    {
        // distributedDatabase ConfigurationResolve
        $_config['username'] = explode(',', $this->config['username']);
        $_config['password'] = explode(',', $this->config['password']);
        $_config['hostname'] = explode(',', $this->config['hostname']);
        $_config['hostport'] = explode(',', $this->config['hostport']);
        $_config['database'] = explode(',', $this->config['database']);
        $_config['dsn'] = explode(',', $this->config['dsn']);
        $_config['charset'] = explode(',', $this->config['charset']);

        $m = floor(mt_rand(0, $this->config['master_num'] - 1));
        // databaseRead and writewhetherSeparate
        if ($this->config['rw_separate']) {
            // Master-slaveformulauseSeparate read and write
            if ($master)
                // Primary serverWrite
                $r = $m;
            else {
                if (is_numeric($this->config['slave_no'])) {// Designationserverread
                    $r = $this->config['slave_no'];
                } else {
                    // readoperatingconnectionFromserver
                    $r = floor(mt_rand($this->config['master_num'], count($_config['hostname']) - 1));   // Eachrandomconnecteddatabase
                }
            }
        } else {
            // Read and writeoperatingDoes not distinguish betweenserver
            $r = floor(mt_rand(0, count($_config['hostname']) - 1));   // Eachrandomconnecteddatabase
        }

        if ($m != $r) {
            $db_master = array(
                'username' => isset($_config['username'][$m]) ? $_config['username'][$m] : $_config['username'][0],
                'password' => isset($_config['password'][$m]) ? $_config['password'][$m] : $_config['password'][0],
                'hostname' => isset($_config['hostname'][$m]) ? $_config['hostname'][$m] : $_config['hostname'][0],
                'hostport' => isset($_config['hostport'][$m]) ? $_config['hostport'][$m] : $_config['hostport'][0],
                'database' => isset($_config['database'][$m]) ? $_config['database'][$m] : $_config['database'][0],
                'dsn' => isset($_config['dsn'][$m]) ? $_config['dsn'][$m] : $_config['dsn'][0],
                'charset' => isset($_config['charset'][$m]) ? $_config['charset'][$m] : $_config['charset'][0],
            );
        }
        $db_config = array(
            'username' => isset($_config['username'][$r]) ? $_config['username'][$r] : $_config['username'][0],
            'password' => isset($_config['password'][$r]) ? $_config['password'][$r] : $_config['password'][0],
            'hostname' => isset($_config['hostname'][$r]) ? $_config['hostname'][$r] : $_config['hostname'][0],
            'hostport' => isset($_config['hostport'][$r]) ? $_config['hostport'][$r] : $_config['hostport'][0],
            'database' => isset($_config['database'][$r]) ? $_config['database'][$r] : $_config['database'][0],
            'dsn' => isset($_config['dsn'][$r]) ? $_config['dsn'][$r] : $_config['dsn'][0],
            'charset' => isset($_config['charset'][$r]) ? $_config['charset'][$r] : $_config['charset'][0],
        );
        return $this->connect($db_config, $r, $r == $m ? false : $db_master);
    }

    /**
     * Destructor
     * @access public
     */
    public function __destruct()
    {
        // freedInquire
        if ($this->PDOStatement) {
            $this->free();
        }
        // shut downconnection
        $this->close();
    }
}
