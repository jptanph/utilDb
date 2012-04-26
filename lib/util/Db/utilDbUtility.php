<?php
/**
 * Model Utility Class
 *
 * @package model
 * @subpackage utility
 *
 * @author jylee3@simplexi.com
 * @version 1.0
 */
class utilDbUtility
{
    const MODEL_PREFIX_DBDRIVER    = 'driver';
    const MODEL_PREFIX_HOST        = 'host';
    const MODEL_PREFIX_PORT        = 'port';
    const MODEL_PREFIX_DBNAME      = 'dbname';
    const MODEL_PREFIX_USERNAME    = 'id';
    const MODEL_PREFIX_PASSWORD    = 'passwd';

    /**
     * 모델 유틸리티 인스턴스 반환
     *
     * @param	String	$sDatabaseName
     *
     * @return utilDbUtility
     */
    public static function getInstance($aDns)
    {
        return utilSplClass::load('utilDbUtility', array($aDns));
    }

    /**
     * 데이터베이스 설정
     * @var Array
     */
    protected $dbConfig;

    /**
     * 모듈 유틸리티 생성자
     *
     * @param	String	$sDatabaseName
     */
    public function __construct($aDns)
    {
        $this->dbConfig = $aDns;
    }

    /**
     * DSN 반환
     *
     * @return	String
     */
    public function getDsn()
    {
        return sprintf('%s:host=%s;port=%s;dbname=%s',
            $this->dbConfig[utilDbUtility::MODEL_PREFIX_DBDRIVER],
            $this->dbConfig[utilDbUtility::MODEL_PREFIX_HOST],
            $this->dbConfig[utilDbUtility::MODEL_PREFIX_PORT],
            $this->dbConfig[utilDbUtility::MODEL_PREFIX_DBNAME]);
    }

    /**
     * 데이터베이스 이름 반환
     *
     * @param	$sDBType	String		master(default), slave
     *
     * @return	String
     */
    public function getDbName()
    {
        return $this->dbConfig[utilDbUtility::MODEL_PREFIX_DBNAME];
    }

    /**
     * 데이터베이스 종류 반환
     *
     * @return	String
     */
    public function getDriver()
    {
        return $this->dbConfig[utilDbUtility::MODEL_PREFIX_DBDRIVER];
    }

    /**
     * 계정 이름 반환
     *
     * @param	$sDBType	String		master(default), slave
     *
     * @return	String
     */
    public function getUsername()
    {
        return $this->dbConfig[utilDbUtility::MODEL_PREFIX_USERNAME];
    }

    /**
     * 비밀번호 반환
     *
     * @param	$sDBType	String		master(default), slave
     *
     * @return	String
     */
    public function getPassword()
    {
        return $this->dbConfig[utilDbUtility::MODEL_PREFIX_PASSWORD];
    }
}
