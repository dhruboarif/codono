<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2012 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: support@codono.com
// +----------------------------------------------------------------------
// $Id: Model.class.php 2779 2012-02-24 02:56:57Z liu21st $

/**
 * ThinkPHP CLImodeModelModel Class
 * @category   Think
 * @package  Think
 * @subpackage  Core
 * @author    liu21st <liu21st@gmail.com>
 */
class Model
{

    // Current dataStorehouseoperatingObjects
    protected $db = null;
    // Primary keyname
    protected $pk = 'id';
    // data sheetPrefix
    protected $tablePrefix = '';
    // Model Name
    protected $name = '';
    // databasename
    protected $dbName = '';
    // data Sheet Name(Does not containTable Prefix)
    protected $tableName = '';
    // actualdata Sheet Name(containTable Prefix)
    protected $trueTableName = '';
    // most近Error Messages
    protected $error = '';
    // Fieldinformation
    protected $fields = array();
    // datainformation
    protected $data = array();
    // Query Expressionparameter
    protected $options = array();
    protected $_validate = array();  // automaticverificationdefinition
    protected $_auto = array();  // automaticcarry outdefinition
    // whetherautomaticDetectdata sheetFieldinformation
    protected $autoCheckFields = true;
    // whetherBatchdeal withverification
    protected $patchValidate = false;

    /**
     * Architecturefunction
     * ObtainDBExamples of object classes Field inspection
     * @param string $name Model Name
     * @param string $tablePrefix Table Prefix
     * @param mixed $connection Database connection information
     * @access public
     */
    public function __construct($name = '', $tablePrefix = '', $connection = '')
    {
        // modelinitialization
        $this->_initialize();
        // ObtainModel Name
        if (!empty($name)) {
            if (strpos($name, '.')) { // stand by DatabaseName.model Nameof definition
                list($this->dbName, $this->name) = explode('.', $name);
            } else {
                $this->name = $name;
            }
        } elseif (empty($this->name)) {
            $this->name = $this->getModelName();
        }
        if (!empty($tablePrefix)) {
            $this->tablePrefix = $tablePrefix;
        }
        // Set upTable Prefix
        $this->tablePrefix = $this->tablePrefix ? $this->tablePrefix : C('DB_PREFIX');
        // databaseinitializationoperating
        // ObtaindatabaseoperatingObjects
        // currentModel independentofDatabase connection information
        $this->db(0, empty($this->connection) ? $connection : $this->connection);
        // FieldDetect
        if (!empty($this->name) && $this->autoCheckFields) $this->_checkTableInfo();
    }

    /**
     * automaticDetectdata sheetinformation
     * @access protected
     * @return void
     */
    protected function _checkTableInfo()
    {
        // if notModelclass automaticrecordingdata sheetinformation
        // Only the firstcarried outrecording
        if (empty($this->fields)) {
            // in casedata sheetFieldNodefinitionthenautomaticObtain
            if (C('DB_FIELDS_CACHE')) {
                $db = $this->dbName ? $this->dbName : C('DB_NAME');
                $this->fields = F('_fields/' . $db . '.' . $this->name);
                if (!$this->fields) $this->flush();
            } else {
                // Every timeReaddata sheetinformation
                $this->flush();
            }
        }
    }

    /**
     * ObtainFieldinformationandCache
     * @access public
     * @return void
     */
    public function flush()
    {
        // Cachedoes not existthenQuery datatableinformation
        $fields = $this->db->getFields($this->getTableName());
        if (!$fields) { // UnableObtainFieldinformation
            return false;
        }
        $this->fields = array_keys($fields);
        $this->fields['_autoinc'] = false;
        foreach ($fields as $key => $val) {
            // recordingFieldTypes of
            $type[$key] = $val['type'];
            if ($val['primary']) {
                $this->fields['_pk'] = $key;
                if ($val['autoinc']) $this->fields['_autoinc'] = true;
            }
        }
        // recordingFieldTypes ofinformation
        if (C('DB_FIELDTYPE_CHECK')) $this->fields['_type'] = $type;

        // 2008-3-7 Increase the cache control switch
        if (C('DB_FIELDS_CACHE')) {
            // permanentCachedata sheetinformation
            $db = $this->dbName ? $this->dbName : C('DB_NAME');
            F('_fields/' . $db . '.' . $this->name, $this->fields);
        }
    }

    /**
     * Value of the data object
     * @access public
     * @param string $name name
     * @param mixed $value value
     * @return void
     */
    public function __set($name, $value)
    {
        // Set upData ObjectsAttributes
        $this->data[$name] = $value;
    }

    /**
     * Gets the value of the data object
     * @access public
     * @param string $name name
     * @return mixed
     */
    public function __get($name)
    {
        return isset($this->data[$name]) ? $this->data[$name] : null;
    }

    /**
     * DetectData ObjectsThe value
     * @access public
     * @param string $name name
     * @return boolean
     */
    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    /**
     * The destruction of the value of the data object
     * @access public
     * @param string $name name
     * @return void
     */
    public function __unset($name)
    {
        unset($this->data[$name]);
    }

    /**
     * Use__callWay to achieveSome specialofModelmethod
     * @access public
     * @param string $method Method name
     * @param array $args Call parameters
     * @return mixed
     */
    public function __call($method, $args)
    {
        if (in_array(strtolower($method), array('table', 'where', 'order', 'limit', 'page', 'alias', 'having', 'group', 'lock', 'distinct'), true)) {
            // Coherent operationofachieve
            $this->options[strtolower($method)] = $args[0];
            return $this;
        } elseif (in_array(strtolower($method), array('count', 'sum', 'min', 'max', 'avg'), true)) {
            // statisticsInquireofachieve
            $field = isset($args[0]) ? $args[0] : '*';
            return $this->getField(strtoupper($method) . '(' . $field . ') AS tp_' . $method);
        } elseif (strtolower(substr($method, 0, 5)) == 'getby') {
            // according toAFieldObtainrecording
            $field = parse_name(substr($method, 5));
            $where[$field] = $args[0];
            return $this->where($where)->find();
        } else {
            throw_exception(__CLASS__ . ':' . $method . L('_METHOD_NOT_EXIST_'));
            return;
        }
    }

    // Callbackmethod initializationmodel
    protected function _initialize()
    {
    }

    /**
     * CorrectStorageToDatabasedataEnterRowdeal with
     * @access protected
     * @param mixed $data Data to be operated
     * @return boolean
     */
    protected function _facade($data)
    {
        // an examinationnon-dataField
        if (!empty($this->fields)) {
            foreach ($data as $key => $val) {
                if (!in_array($key, $this->fields, true)) {
                    unset($data[$key]);
                } elseif (C('DB_FIELDTYPE_CHECK') && is_scalar($val)) {
                    // FieldTypes ofan examination
                    $this->_parseType($data, $key);
                }
            }
        }
        return $data;
    }

    /**
     * New data
     * @access public
     * @param mixed $data data
     * @param array $options expression
     * @return mixed
     */
    public function add($data = '', $options = array())
    {
        if (empty($data)) {
            // Notransferdata,Get the currentData ObjectsThe value
            if (!empty($this->data)) {
                $data = $this->data;
            } else {
                $this->error = L('_DATA_TYPE_INVALID_');
                return false;
            }
        }
        // Analysis of expression
        $options = $this->_parseOptions($options);
        // datadeal with
        $data = $this->_facade($data);
        // data inputTodatabase
        $result = $this->db->insert($data, $options);
        if (false !== $result) {
            $insertId = $this->getLastInsID();
            if ($insertId) {
                // IncrementPrimary keyreturninsertID
                return $insertId;
            }
        }
        return $result;
    }

    /**
     * save data
     * @access public
     * @param mixed $data data
     * @param array $options expression
     * @return boolean
     */
    public function save($data = '', $options = array())
    {
        if (empty($data)) {
            // Notransferdata,Get the currentData ObjectsThe value
            if (!empty($this->data)) {
                $data = $this->data;
            } else {
                $this->error = L('_DATA_TYPE_INVALID_');
                return false;
            }
        }
        // datadeal with
        $data = $this->_facade($data);
        // Analysis of expression
        $options = $this->_parseOptions($options);
        if (!isset($options['where'])) {
            // in caseexistPrimary keydata thenautomaticAs aUpdatecondition
            if (isset($data[$this->getPk()])) {
                $pk = $this->getPk();
                $where[$pk] = $data[$pk];
                $options['where'] = $where;
                unset($data[$pk]);
            } else {
                // in caseNoany Update condition then not carrying out
                $this->error = L('_OPERATION_WRONG_');
                return false;
            }
        }
        $result = $this->db->update($data, $options);
        return $result;
    }

    /**
     * delete data
     * @access public
     * @param mixed $options expression
     * @return mixed
     */
    public function delete($options = array())
    {
        if (empty($options) && empty($this->options['where'])) {
            // in casedeleteconditionforair thendeleteCurrent dataObjectsThecorrespondingrecording
            if (!empty($this->data) && isset($this->data[$this->getPk()]))
                return $this->delete($this->data[$this->getPk()]);
            else
                return false;
        }
        if (is_numeric($options) || is_string($options)) {
            // according toPrimary keyDelete Record
            $pk = $this->getPk();
            if (strpos($options, ',')) {
                $where[$pk] = array('IN', $options);
            } else {
                $where[$pk] = $options;
                $pkValue = $options;
            }
            $options = array();
            $options['where'] = $where;
        }
        // Analysis of expression
        $options = $this->_parseOptions($options);
        $result = $this->db->delete($options);
        // returnDelete RecordThe number of
        return $result;
    }

    /**
     * Query data sets
     * @access public
     * @param array $options Expression argument
     * @return mixed
     */
    public function select($options = array())
    {
        if (is_string($options) || is_numeric($options)) {
            // according toPrimary keyInquire
            $pk = $this->getPk();
            if (strpos($options, ',')) {
                $where[$pk] = array('IN', $options);
            } else {
                $where[$pk] = $options;
            }
            $options = array();
            $options['where'] = $where;
        }
        // Analysis of expression
        $options = $this->_parseOptions($options);
        $resultSet = $this->db->select($options);
        if (false === $resultSet) {
            return false;
        }
        if (empty($resultSet)) { // search resultforair
            return null;
        }
        return $resultSet;
    }

    /**
     * Analysis of expression
     * @access proteced
     * @param array $options Expression argument
     * @return array
     */
    protected function _parseOptions($options = array())
    {
        if (is_array($options))
            $options = array_merge($this->options, $options);
        // InquireAfter theClearsqlexpressionAssembly To avoid affecting the nextInquire
        $this->options = array();
        if (!isset($options['table']))
            // automaticObtainTable name
            $options['table'] = $this->getTableName();
        if (!empty($options['alias'])) {
            $options['table'] .= ' ' . $options['alias'];
        }
        // FieldTypes ofverification
        if (C('DB_FIELDTYPE_CHECK')) {
            if (isset($options['where']) && is_array($options['where'])) {
                // CorrectArrayQuery conditionsEnterRowFieldTypes ofan examination
                foreach ($options['where'] as $key => $val) {
                    if (in_array($key, $this->fields, true) && is_scalar($val)) {
                        $this->_parseType($options['where'], $key);
                    }
                }
            }
        }
        return $options;
    }

    /**
     * Data type detection
     * @access protected
     * @param mixed $data data
     * @param string $key Field Name
     * @return void
     */
    protected function _parseType(&$data, $key)
    {
        $fieldType = strtolower($this->fields['_type'][$key]);
        if (false !== strpos($fieldType, 'int')) {
            $data[$key] = intval($data[$key]);
        } elseif (false !== strpos($fieldType, 'float') || false !== strpos($fieldType, 'double')) {
            $data[$key] = floatval($data[$key]);
        } elseif (false !== strpos($fieldType, 'bool')) {
            $data[$key] = (bool)$data[$key];
        }
    }

    /**
     * Query data
     * @access public
     * @param mixed $options Expression argument
     * @return mixed
     */
    public function find($options = array())
    {
        if (is_numeric($options) || is_string($options)) {
            $where[$this->getPk()] = $options;
            $options = array();
            $options['where'] = $where;
        }
        // alwaysSeek一Records
        $options['limit'] = 1;
        // Analysis of expression
        $options = $this->_parseOptions($options);
        $resultSet = $this->db->select($options);
        if (false === $resultSet) {
            return false;
        }
        if (empty($resultSet)) {// search resultforair
            return null;
        }
        $this->data = $resultSet[0];
        return $this->data;
    }

    /**
     * Set uprecordingofAField Values
     * stand byusedatabaseFieldwithmethod
     * @access public
     * @param string|array $field Field Name
     * @param string|array $value Field Values
     * @return boolean
     */
    public function setField($field, $value)
    {
        if (is_array($field)) {
            $data = $field;
        } else {
            $data[$field] = $value;
        }
        return $this->save($data);
    }

    /**
     * Field value growth
     * @access public
     * @param string $field Field Name
     * @param integer $step Increase value
     * @return boolean
     */
    public function setInc($field, $step = 1)
    {
        return $this->setField($field, array('exp', $field . '+' . $step));
    }

    /**
     * Field values decrease
     * @access public
     * @param string $field Field Name
     * @param integer $step Reduce value
     * @return boolean
     */
    public function setDec($field, $step = 1)
    {
        return $this->setField($field, array('exp', $field . '-' . $step));
    }

    /**
     * Obtain一RecordsofAField Values
     * @access public
     * @param string $field Field Name
     * @param string $spea Field data symbol interval
     * @return mixed
     */
    public function getField($field, $sepa = null)
    {
        $options['field'] = $field;
        $options = $this->_parseOptions($options);
        if (strpos($field, ',')) { // manyField
            $resultSet = $this->db->select($options);
            if (!empty($resultSet)) {
                $_field = explode(',', $field);
                $field = array_keys($resultSet[0]);
                $move = $_field[0] == $_field[1] ? false : true;
                $key = array_shift($field);
                $key2 = array_shift($field);
                $cols = array();
                $count = count($_field);
                foreach ($resultSet as $result) {
                    $name = $result[$key];
                    if ($move) { // deleteThe keyrecording
                        unset($result[$key]);
                    }
                    if (2 == $count) {
                        $cols[$name] = $result[$key2];
                    } else {
                        $cols[$name] = is_null($sepa) ? $result : implode($sepa, $result);
                    }
                }
                return $cols;
            }
        } else {   // Seek一Records
            $options['limit'] = 1;
            $result = $this->db->select($options);
            if (!empty($result)) {
                return reset($result[0]);
            }
        }
        return null;
    }

    /**
     * Create data objects But not saved to the database
     * @access public
     * @param mixed $data Creating a Data
     * @return mixed
     */
    public function create($data = '')
    {
        // in caseNoBy valuedefaulttakePOSTdata
        if (empty($data)) {
            $data = $_POST;
        } elseif (is_object($data)) {
            $data = get_object_vars($data);
        }
        // verify the data
        if (empty($data) || !is_array($data)) {
            $this->error = L('_DATA_TYPE_INVALID_');
            return false;
        }

        // verificationcarry outFormData Objects
        if ($this->autoCheckFields) { // OpenFieldDetect The filtering illegalFielddata
            $vo = array();
            foreach ($this->fields as $key => $name) {
                if (substr($key, 0, 1) == '_') continue;
                $val = isset($data[$name]) ? $data[$name] : null;
                //Guarantee assignmenteffective
                if (!is_null($val)) {
                    $vo[$name] = (MAGIC_QUOTES_GPC && is_string($val)) ? stripslashes($val) : $val;
                }
            }
        } else {
            $vo = $data;
        }

        // 赋valueCurrent dataObjects
        $this->data = $vo;
        // returncreateofdataFor othertransfer
        return $vo;
    }

    /**
     * SQLInquire
     * @access public
     * @param mixed $sql SQLinstruction
     * @return mixed
     */
    public function query($sql)
    {
        if (!empty($sql)) {
            if (strpos($sql, '__TABLE__'))
                $sql = str_replace('__TABLE__', $this->getTableName(), $sql);
            return $this->db->query($sql);
        } else {
            return false;
        }
    }

    /**
     * carried outSQLStatement
     * @access public
     * @param string $sql SQLinstruction
     * @return false | integer
     */
    public function execute($sql)
    {
        if (!empty($sql)) {
            if (strpos($sql, '__TABLE__'))
                $sql = str_replace('__TABLE__', $this->getTableName(), $sql);
            return $this->db->execute($sql);
        } else {
            return false;
        }
    }

    /**
     * SwitchcurrentofDatabase Connectivity
     * @access public
     * @param integer $linkNum connectionNo.
     * @param mixed $config Database connection information
     * @param array $params Model parameters
     * @return Model
     */
    public function db($linkNum, $config = '', $params = array())
    {
        static $_db = array();
        if (!isset($_db[$linkNum])) {
            // CreatenewofExamples
            if (!empty($config) && false === strpos($config, '/')) { // stand byReadConfiguration parameters
                $config = C($config);
            }
            $_db[$linkNum] = Db::getInstance($config);
        } elseif (NULL === $config) {
            $_db[$linkNum]->close(); // Close the databaseconnection
            unset($_db[$linkNum]);
            return;
        }
        if (!empty($params)) {
            if (is_string($params)) parse_str($params, $params);
            foreach ($params as $name => $value) {
                $this->setProperty($name, $value);
            }
        }
        // SwitchDatabase Connectivity
        $this->db = $_db[$linkNum];
        return $this;
    }

    /**
     * getcurrentofData Objectsname
     * @access public
     * @return string
     */
    public function getModelName()
    {
        if (empty($this->name))
            $this->name = substr(get_class($this), 0, -5);
        return $this->name;
    }

    /**
     * Get the completedata Sheet Name
     * @access public
     * @return string
     */
    public function getTableName()
    {
        if (empty($this->trueTableName)) {
            $tableName = !empty($this->tablePrefix) ? $this->tablePrefix : '';
            if (!empty($this->tableName)) {
                $tableName .= $this->tableName;
            } else {
                $tableName .= parse_name($this->name);
            }
            $this->trueTableName = strtolower($tableName);
        }
        return (!empty($this->dbName) ? $this->dbName . '.' : '') . $this->trueTableName;
    }

    /**
     * Start affairs
     * @access public
     * @return void
     */
    public function startTrans()
    {
        $this->commit();
        $this->db->startTrans();
        return;
    }

    /**
     * Commit the transaction
     * @access public
     * @return boolean
     */
    public function commit()
    {
        return $this->db->commit();
    }

    /**
     * Transaction rollback
     * @access public
     * @return boolean
     */
    public function rollback()
    {
        return $this->db->rollback();
    }

    /**
     * Return modelError Messages
     * @access public
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Return dataStorehouseofError Messages
     * @access public
     * @return string
     */
    public function getDbError()
    {
        return $this->db->getError();
    }

    /**
     * Returns the last insertedID
     * @access public
     * @return string
     */
    public function getLastInsID()
    {
        return $this->db->getLastInsID();
    }

    /**
     * Returns the last executionsqlStatement
     * @access public
     * @return string
     */
    public function getLastSql()
    {
        return $this->db->getLastSql();
    }

    // In view ofgetLastSqlMore common increase_sql Slug
    public function _sql()
    {
        return $this->getLastSql();
    }

    /**
     * Gets the name of the primary key
     * @access public
     * @return string
     */
    public function getPk()
    {
        return isset($this->fields['_pk']) ? $this->fields['_pk'] : $this->pk;
    }

    /**
     * Obtaindata sheetFieldinformation
     * @access public
     * @return array
     */
    public function getDbFields()
    {
        if ($this->fields) {
            $fields = $this->fields;
            unset($fields['_autoinc'], $fields['_pk'], $fields['_type']);
            return $fields;
        }
        return false;
    }

    /**
     * Specify the query field Support field to exclude
     * @access public
     * @param mixed $field
     * @param boolean $except Whether to exclude
     * @return Model
     */
    public function field($field, $except = false)
    {
        if ($except) {// Fieldexclude
            if (is_string($field)) {
                $field = explode(',', $field);
            }
            $fields = $this->getDbFields();
            $field = $fields ? array_diff($fields, $field) : $field;
        }
        $this->options['field'] = $field;
        return $this;
    }

    /**
     * Set value data object
     * @access public
     * @param mixed $data data
     * @return Model
     */
    public function data($data)
    {
        if (is_object($data)) {
            $data = get_object_vars($data);
        } elseif (is_string($data)) {
            parse_str($data, $data);
        } elseif (!is_array($data)) {
            throw_exception(L('_DATA_TYPE_INVALID_'));
        }
        $this->data = $data;
        return $this;
    }

    /**
     * InquireSQLAssembly join
     * @access public
     * @param mixed $join
     * @return Model
     */
    public function join($join)
    {
        if (is_array($join))
            $this->options['join'] = $join;
        else
            $this->options['join'][] = $join;
        return $this;
    }

    /**
     * InquireSQLAssembly union
     * @access public
     * @param array $union
     * @return Model
     */
    public function union($union)
    {
        if (empty($union)) return $this;
        // Changeunionexpression
        if ($union instanceof Model) {
            $options = $union->getProperty('options');
            if (!isset($options['table'])) {
                // automaticObtainTable name
                $options['table'] = $union->getTableName();
            }
            if (!isset($options['field'])) {
                $options['field'] = $this->options['field'];
            }
        } elseif (is_object($union)) {
            $options = get_object_vars($union);
        } elseif (!is_array($union)) {
            throw_exception(L('_DATA_TYPE_INVALID_'));
        }
        $this->options['union'][] = $options;
        return $this;
    }

    /**
     * Property value model
     * @access public
     * @param string $name name
     * @param mixed $value value
     * @return Model
     */
    public function setProperty($name, $value)
    {
        if (property_exists($this, $name))
            $this->$name = $value;
        return $this;
    }

    /**
     * Gets the property value model
     * @access public
     * @param string $name name
     * @return mixed
     */
    public function getProperty($name)
    {
        if (property_exists($this, $name))
            return $this->$name;
        else
            return NULL;
    }
}