<?php
$DocROOT = $_SERVER[DOCUMENT_ROOT];
require_once($DocROOT."/lib/util/Cache/utilCacheController.php");

/**
 * 캐쉬 호출 및 처리 클래스
 *
 * @author      jylee3@simplexi.com
 */
class utilCache
{
    /**
     * 기본 캐쉬 컨트롤러 인스턴스 반환
     *
     * @param String    $sCacheName
     * @param String    $sCafe24Id
     * @param String    $sCacheDataType
     *
     * @return utilCacheController
     */
    public static function getCache($sCacheName, $sCafe24Id='', $sCacheDataType='Serialize')
    {
        return self::_getCache("Normal", $sCacheName, $sCafe24Id, $sCacheDataType);
    }

    /**
     * 데이터베이스 캐쉬 컨트롤러 인스턴스 반환
     *
     * @param String    $sCacheName
     * @param String    $sCafe24Id
     * @param String    $sCacheDataType
     *
     * @return utilCacheController
     */
    public static function getDBCache($sCacheName, $sCafe24Id='', $sCacheDataType='Serialize')
    {
        return self::_getCache("Database", $sCacheName, $sCafe24Id, $sCacheDataType);
    }

    /**
     * 쿼리 캐쉬 컨트롤러 인스턴스 반환
     *
     * @param String    $sCacheName
     * @param String    $sCafe24Id
     * @param String    $sCacheDataType
     *
     * @return utilCacheController
     */
    public static function getSqlCache($sCacheName, $sCafe24Id='', $sCacheDataType='Serialize')
    {
        return self::_getCache("Sql", $sCacheName, $sCafe24Id, $sCacheDataType);
    }

    /**
     * 클래스 캐쉬 컨트롤러 인스턴스 반환
     *
     * @param String    $sCacheName
     * @param String    $sCafe24Id
     * @param String    $sCacheDataType
     *
     * @return utilCacheController
     */
    public static function getClassCache($sCacheName, $sCafe24Id='', $sCacheDataType='Serialize')
    {
        return self::_getCache("Class", $sCacheName, $sCafe24Id, $sCacheDataType);
    }

    /**
     * 데이터 캐쉬 컨트롤러 인스턴스 반환
     *
     * @param String    $sCacheName
     * @param String    $sCafe24Id
     * @param String    $sCacheDataType
     *
     * @return utilCacheController
     */
    public static function getDataCache($sCacheName, $sCafe24Id='', $sCacheDataType='Serialize')
    {
        return self::_getCache("Data", $sCacheName, $sCafe24Id, $sCacheDataType);
    }

    /**
     * 전체 속성 초기화/설정
     * @param utilCacheController   $utilCacheController
     * @param String    $sAttributes
     *
     * @return utilCacheController
     */
    public static function initAttr(utilCacheController &$utilCacheController, $sAttributes='')
    {
        return $utilCacheController->attribute($sAttributes);
    }

    /**
     * 캐쉬 데이터 반환
     *
     * @param utilCacheController   $utilCacheController
     *
     * @return Object
     */
    public static function getData(utilCacheController &$utilCacheController)
    {
        return $utilCacheController->data();
    }

    /**
     * 캐쉬 데이터 설정
     *
     * @param utilCacheController   $utilCacheController
     * @param Object    $oData
     *
     * @return Object
     */
    public static function setData(utilCacheController &$utilCacheController, $oData=null)
    {
        return $utilCacheController->data($oData);
    }

    /**
     * 각 속성  반환
     *
     * @param utilCacheController   $utilCacheController
     * @param String    $sAttrName
     *
     * @return Object
     */
    public static function getAttr(utilCacheController &$utilCacheController, $sAttrName)
    {
        return $utilCacheController->attr($sAttrName);
    }

    /**
     * 각 속성 설정
     *
     * @param utilCacheController   $utilCacheController
     * @param String    $sAttrName
     * @param Object    $oData
     *
     * @return Object
     */
    public static function setAttr(utilCacheController &$utilCacheController, $sAttrName, $oData=null)
    {
        return $utilCacheController->attr($sAttrName, $oData);
    }

    /**
     * 캐쉬 만료여부 반환
     *
     * @param utilCacheController   $utilCacheController
     *
     * @return Boolean
     */
    public static function isExpired(utilCacheController &$utilCacheController)
    {
        return $utilCacheController->is_expired();
    }

    /**
     * 캐쉬 유효성 여부 반환
     *
     * @param utilCacheController   $utilCacheController
     *
     * @return Boolean
     */
    public static function isValid(utilCacheController &$utilCacheController)
    {
        return $utilCacheController->is_valid();
    }

    /**
     * 캐쉬 초기화
     *
     * @param utilCacheController   $utilCacheController
     */
    public static function clearCache(utilCacheController &$utilCacheController)
    {
        return $utilCacheController->clear();
    }

    private static function _getCache($sCacheType, $sCacheName, $sCafe24Id, $sCacheDataType)
    {
        return new utilCacheController($sCacheType,
            $sCacheName,
            $sCafe24Id,
            $sCacheDataType);
    }
}