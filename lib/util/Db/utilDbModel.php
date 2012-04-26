<?php
/**
 * Model Core Class
 *
 * @package model
 *
 * @author jylee3@simplexi.com
 * @version 1.0
 */
class utilDbModel
{
    /**
     * Model Utility
     * @var utilDbUtility
     */
    protected $util;

    /**
     * Database PDO Driver
     * @var utilDbDriverCommon
     */
    protected $dbDriver;


    /**
     * 모델 생성자
     *
     * @param	Array	$aDSN				DSN Info
     */
    public function __construct()
    {

        $aDSN = array(
            'driver' => COMMON_DB_TYPE,
            'host' => COMMON_DB_HOST,
            'id' => COMMON_DB_USER,
            'dbname' => COMMON_DB_NAME,
            'passwd' => COMMON_DB_PASSWD,
            'port' => COMMON_DB_PORT
        );

        $this->util = utilDbUtility::getInstance($aDSN);

        $this->dbDriver = utilDbAdapter::dbInstance(
            $this->util->getDsn(),
            $this->util->getUsername(),
            $this->util->getPassword(),
            array(),
            $this->util->getDriver(),
            $aDSN
        );

        /* Debug Mode Setting - Warning Message Display */
        if (IS_TEST === true) $this->dbDriver->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

        $this->__initBefore();

        $this->init();

    }

    /**
     * 초기화 템플릿 메소드 실행전 호출
     * @warning 해당 메소드를 오버라이딩 하실 경우 코어에 대한 확실한 구조 파악이 요구됩니다.
     */
    protected function __initBefore() {}

    /**
     * 데이터베이스 초기화
     *
     * 이 메소드를 재정의하여 데이터베이스 초기화에 할당할 수 있습니다.
     *
     * @return	void
     */
    protected function init() {}

    /**
     * 쿼리 실행
     *
     * <code>
     *
     * $this->query('SELECT * FROM TABLE');
     *
     * $this->query('SELECT * FROM TABLE', UTIL_DB_RESULT_ROWS);
     *
     * </code>
     *
     * @link	http://wiki.simplexi.com:8080/pages/viewpage.action?pageId=12475899
     *
     * @param	String	$sSql				Execute Query
     * @param	String	$sType				exec|row|rows, UTIL_DB_RESULT_EXEC|UTIL_DB_RESULT_ROW|UTIL_DB_RESULT_ROWS
     * @return	Mixed or false(Boolean)		Mixed : 성공 및 쿼리 결과, false : 쿼리 실패 또는 오류
     *
     */
    protected function query($sSql, $sType=UTIL_DB_RESULT_ROWS)
    {
        $sType = strtolower($sType);
        if (strcasecmp($sType, UTIL_DB_RESULT_EXEC)==0 || $this->isSelect($sSql)===false) {
            $mResult = $this->dbDriver->exec($sSql);
            return $mResult;
        } else {
            $mResult = $this->dbDriver->query($sSql, $sType);
            return $mResult;
        }
    }

    /**
     * Is... Select Query
     *
     * <code>
     * $bIsSelect = $this->isSelect('SELECT * FROM TABLE');
     * </code>
     *
     * @param	String	$sSql
     *
     * @return	Boolean
     */
    public function isSelect($sSql)
    {
        return (stripos($sSql, 'select')===false && stripos($sSql, 'call')===false)? false : true;
	}


    /**
     * Last Insert ID 반환
     *
     * @param	String	$sName	Sequence Object for the name
     *                          일부 데이터베이스에서는 값을 반드시 지정해야  정상적으로 반환됩니다.
     * 							MySQL은 일반적으로 값을 지정하지 않아도 자동 반환됩니다.
     * 							PGSQL은 일반적으로 시퀀스 이름을 지정해야 반환됩니다.
     *
     * @return	Mixed	string representing the row ID of the last row that was inserted into the database
     * 					string representing the last value retrieved from the specified sequence object
     *					If the PDO driver does not support this capability, PDO::lastInsertId() triggers an IM001 SQLSTATE
     */
    public function lastInsertId($sName=null)
    {
        return $this->dbDriver->lastInsertId($sName);
    }

    /**
     * SQL Escape Statement
     *
     * <code>
     * // I\'m Sorry.
     * $sSql = $this->escape("I'm Sorry.");
     * </code
     *
     * @param	String	$sSql
     *
     * @return	String
     */
    public function escape($sSql)
    {
        return $this->dbDriver->escapeString($sSql);
    }
}
