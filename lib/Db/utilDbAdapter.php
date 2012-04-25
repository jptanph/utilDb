<?php
/**
 * coreModel Database Instance Adapter
 *
 * 요청된 데이터베이스 객체를 반환하며 관리합니다.
 *
 * @package model
 * @subpackage utility
 *
 * @author jylee3@simplexi.com
 * @version 1.0
 */
class utilDbAdapter
{
    protected static $dbInstance = array();
    protected static $acInstance = array();
    protected static $helpInstance = array();

    /**
     * Getting... Database Driver Instance
     *
     * @param	String	$sDsn
     * @param	String	$sUser
     * @param	String	$sPassword
     * @param	Array	$aOptions
     * @param	String	$sDbType
     * @param   array   $aDsn
     *
     * @return coreModelDbDriverCommon
     */
    public static function dbInstance($sDsn, $sUser, $sPassword, $aOptions=array(), $sDbType='MySQL', $aDsn=null)
    {
        $sInstanceKey = self::getDbInstanceKey($aDsn);

        if (!isset(self::$dbInstance[$sInstanceKey])) {

            $sModelDbDriverName = 'utilDbDriver'.ucfirst(strtolower($sDbType));

            $reflectionClass = new ReflectionClass($sModelDbDriverName);

            self::$dbInstance[$sInstanceKey] = $reflectionClass->newInstance($sDsn, $sUser, $sPassword, $aOptions);
        }
        return self::$dbInstance[$sInstanceKey];
    }

    protected static function getDbInstanceKey($aDSN)
    {
        return md5(serialize($aDSN));
    }
}
