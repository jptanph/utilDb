<?php
/**
 * Model PDO Driver - Common
 *
 * @package model
 * @subpackage pdo
 *
 * @author jylee3@simplexi.com
 * @version 1.0
 */
abstract class utilDbDriverCommon extends PDO
{
    public $dbPrefix    = '';
    public $swapPre     = '';

    public function __construct($dsn, $username, $passwd, $options)
    {
        parent::__construct($dsn, $username, $passwd, $options);
    }

    /**
     * 쿼리 실행 및 결과 반환(ROW|ROWS 타입)
     *
     * @param	String	$sSql				실행 쿼리
     * @param	String  $sType				결과 종류
     *
     * @return	Mixed
     */
    public function query($sSql, $sType='rows')
    {
        $sSql = T_dbQueryConvert($sSql);

        $sType = strtolower($sType);

        $dbResult = parent::query($sSql);

        if ($dbResult!==false) {
            $mResult = ($sType=='row')? $dbResult->fetch(PDO::FETCH_ASSOC) : $dbResult->fetchAll(PDO::FETCH_ASSOC);

            unset($dbResult);

            $mResult = T_dbDataConvert($mResult);
            return $mResult;
        } else {
            return false;
        }
    }

    /**
     * 쿼리 실행 및 결과 반환(Exec 타입)
     *
     * @param	String	$sSql				실행 쿼리
     *
     * @return	Mixed
     *
     * @see PDO::exec()
     */
    public function exec($sSql)
    {
        $sSql = T_dbQueryConvert($sSql);

        return parent::exec($sSql);
    }

    /**
     * SQL Escape
     *
     * @param	String	$sSql
     *
     * @return	String
     */
    public function escapeString($sSql)
    {
        return addslashes($sSql);
    }
}
