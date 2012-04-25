<?php
/**
 * 캐쉬 데이터 타입별 파서 클래스 호출 및 데이터 처리 인터페이스 클래스
 *
 * @author      jylee3@simplexi.com
 */
class utilCacheDataParser
{
    protected static $_INSTANCE = array();

    protected static function getClassLoad($sCacheDataType)
    {
        $sClassName = 'utilCacheData'.ucfirst(strtolower($sCacheDataType));

        if(class_exists($sClassName)) {
            utilCacheDataParser::$_INSTANCE[$sCacheDataType] = new ${sClassName}();
        } else {
            new exceptionDmc($sClassName.' is Class Not Exits.');
        }

        return utilCacheDataParser::$_INSTANCE[$sCacheDataType];
    }

    /**
     * 데이터 파서에 의한 데이터 요청 및 저장 호출
     *
     * @param String $sCacheDataFile        캐쉬 데이터 파일 경로
     * @param String $sCacheDataType        캐쉬 데이터 타입
     * @param Object $oData                 캐쉬 데이터(데이터 저장시)
     *
     * @return Object                       캐쉬 데이터
     */
    public static function data($sCacheDataFile, $sCacheDataType, $oData=null)
    {
        return self::getClassLoad($sCacheDataType)->data($sCacheDataFile, $oData);
    }
}