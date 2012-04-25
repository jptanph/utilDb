<?php
/**
 * 캐쉬 검증 호출 클래스(컨트롤러에 통합. 사용 안함)
 *
 * @author      jylee3@simplexi.com
 */
class utilCacheValidate
{
    /**
     * 검증 반환
     *
     * @param utilCacheController $utilCacheController
     *
     * @return Boolean
     */
    public static function isValidate(utilCacheController &$utilCacheController)
    {
        return self::getClassLoad($utilCacheController)->valid();
    }

    /**
     * 검증 생성
     *
     * @param utilCacheController $utilCacheController
     */
    public static function createValidate(utilCacheController &$utilCacheController)
    {
        return self::getClassLoad($utilCacheController)->create();
    }

    protected static function getClassLoad(&$utilCacheController)
    {
        /**
         * 캐쉬 타입
         *
         * Database, Data, File
         *
         * @var String
         */
        $sCacheType = $utilCacheController->_getClassVar('sCacheType');

        $sClassName = 'utilCacheValidate'.ucfirst(strtolower($sCacheType));

        if(class_exists($sClassName)) {
            return new ${sClassName}($utilCacheController);
        } else {
            new exceptionDmc($sClassName.' is Class Not Exits.');
        }
    }
}