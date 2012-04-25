<?php
/**
 * SPL Class Tool
 *
 * @package util
 *
 * @author jylee3@simplexi.com
 * @version 1.0
 */
class utilSplClass
{
    /**
     * SPL 클래스 인스턴스 저장소
     */
    protected static $instance = array();

    /**
     * SPL 클래스 로더
     *
     * <code>
     * $utilString = utilSplClass::load('utilString');
     *
     * or
     *
     * $utilXml = utilSplClass::load('utilXml', array('1.0', 'UTF-8'));
     * </code>
     *
     * @param String $sClassName	클래스 이름
     * @param String $aArgument		생성자 아규먼트
     *
     * @return Mixed
     */
    public static function load($sClassName, $aArgument=array())
    {
        $sInstanceKey = self::getInstanceKey($sClassName, $aArgument);

        if (!isset(self::$instance[$sInstanceKey])) {
            $reflectionClass = new ReflectionClass($sClassName);

            self::$instance[$sInstanceKey] = empty($aArgument)? $reflectionClass->newInstanceArgs() : $reflectionClass->newInstanceArgs($aArgument);
        }

        return self::$instance[$sInstanceKey];
    }

    /**
     * SPL Class Has Method
     *
     * <code>
     * $bResult = utilSplClass::hasMethod($this, 'get'.$sMethodName);
     * </code>
     *
     * @param	Mixed	$mClass
     * @param	String	$sMethodName
     *
     * @return	Boolean
     */
    public static function hasMethod($mClass, $sMethodName)
    {
        $sClassName = (is_object($mClass))? get_class($mClass) : $mClass;

        $reflectionClass = new ReflectionClass($sClassName);

        return $reflectionClass->hasMethod($sMethodName);
    }

    /**
     * 클래스 인스턴스 키 생성
     *
     * @param String	$sClassName		클래스 이름
     * @param Array		$aArgument		생성자 아규먼트
     */
    protected static function getInstanceKey($sClassName, $aArgument)
    {
        $sKeySerial = $sClassName.'::'.serialize($aArgument);

        return md5($sKeySerial);
    }
}
