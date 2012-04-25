<?php
/**
 * 클래스 초기화형 캐쉬 클래스(사용안함)
 *
 * @example
 *      class contController {
 *          function __construct()
 *          {
 *              $this->_init();
 *          }
 *
 *          function _init()
 *          {
 *              utilCacheClass::initCache('contController');
 *
 *              $this->_getCache_SystemInfo()
 *          }
 *
 *          function _getCache_SystemInfo() // 메소드명이 _getCache_로 시작되면 해당 함수는 자동으로 캐쉬처리 됩니다.
 *          {
 *              return null;
 *          }
 *      }
 *
 * @author      jylee3@simplexi.com
 */
class utilCacheClass
{
    protected static $__ClassCacheList = array();

    /**
     * 클래스 초기화형 캐쉬
     *
     * @param Object $sClassName
     */
    public static function initCache($sClassName)
    {
        if(!function_exists('classkit_method_rename')
            || !function_exists('classkit_method_add'))    return false;

        if(in_array(strtolower($sClassName), self::$__ClassCacheList)!==false)  return false;

        if(get_parent_class($sClassName)!==false) {
            self::initCache(get_parent_class($sClassName));
        }

        $aMethods = get_class_methods($sClassName);

        if(get_parent_class($sClassName)===false) {
            /**
             * 내부 함수 : 디버그 인스턴스 반환
             * $this->__getDebugInstance();
             */
            if(!method_exists($sClassName, '__getDebugInstance')) {
                classkit_method_add($sClassName, '__getDebugInstance', '',
                    '
                    if(!isset($this->__oDebugInstance)) {
                        $this->__oDebugInstance = new utilCacheDebug(false, \'win\', get_class($this));
                    }

                    return $this->__oDebugInstance;
                    ');
            }

            /**
             * 내부 함수 : 캐쉬 만료시간 반환
             * $this->__getCacheExpiredTime();
             */
            if(!method_exists($sClassName, '__getCacheExpiredTime')) {
                classkit_method_add($sClassName, '__getCacheExpiredTime', '',
                    '
                    if(isset($this->__iCacheExpiredTime)) {
                        return $this->__iCacheExpiredTime;
                    } else {
                        return 86400;
                    }
                    ');
            }

            /**
             * 내부 함수 : 캐쉬 타입 반환
             * $this->__getCacheDataType();
             */
            if(!method_exists($sClassName, '__getCacheDataType')) {
                classkit_method_add($sClassName, '__getCacheDataType', '',
                    '
                    if(isset($this->__sCacheDataType)) {
                        return $this->__sCacheDataType;
                    } else {
                        return \'Serialize\';
                    }
                    ');
            }

            /**
             * 내부 함수 : utilCache 인스턴스 반환
             * $this->__getCacheInstance();
             */
            if(!method_exists($sClassName, '__getCacheInstance')) {
                classkit_method_add($sClassName, '__getCacheInstance', '',
                    '
                    if(!isset($this->__oCacheDataTypeInstance)) {
                        $this->__oCacheDataTypeInstance = utilCache::getClassCache(get_class($this), null, $this->__getCacheDataType());
                    }

                    return $this->__oCacheDataTypeInstance;
                    ');
            }

            /**
             * 내부 함수 - 캐쉬 삭제
             * $this->__delCache()
             */
            if(!method_exists($sClassName, '__delCache')) {
                classkit_method_add($sClassName, '__delCache', '',
                    '
                    return $this->__getCacheInstance()->clear();
                    ');
            }
        }

        foreach($aMethods as $sMethod) {
            if(stripos($sMethod, '_getCache_')!==false && stripos($sMethod, '__getCache_')===false) {
                $sNewMethod =  strtolower('_'.$sMethod);

                classkit_method_rename($sClassName, $sMethod, $sNewMethod);
                classkit_method_add($sClassName, $sMethod, '',
                    '
                    $args = func_get_args();
                    if(!$args)  $args = null;

                    $aLog = array();

                    $aLog[] = \'Class ::: \'.get_class($this).\', Method ::: '.$sMethod.', Argument ::: \'.http_build_query($args, \'\', \'&\');

                    $this->__getCacheInstance()->attribute(\'{"ValidKey":"\'.str_ireplace(\'getCache_\', \'\', '.$sMethod.').\'","ValidData":"\'.base64_encode(serialize($args)).\'","ExpireTime":\'.$this->__getCacheExpiredTime().\'}\');

                    $iClassCacheStart = microtime();

//                    $this->__getDebugInstance()->start();

                    $aLog[] = \'캐쉬 기능이  활성화 (Cache) :::: ExpireTime : \'.$this->__getCacheExpiredTime().\'초\';

                    if($this->__getCacheInstance()->is_valid()!==false) {
                        $oCacheResult = $this->__getCacheInstance()->data();

                        $aLog[] = \'캐쉬 파일 호출 (Cache) :: \'.$this->__getCacheInstance()->_getClassVar(\'sCacheDataFile\');
                    } else {
                        $oCacheResult = call_user_func_array(array($this, \''.$sNewMethod.'\'), $args);
                        $this->__getCacheInstance()->data($oCacheResult);

                        $aLog[] = \'캐쉬 파일 생성 (Cache) :: \'.$this->__getCacheInstance()->_getClassVar(\'sCacheDataFile\');
                    }

                    $aLog[] = \'utilCache : \'.(microtime()-$iClassCacheStart);

//                    $this->__getDebugInstance()->setLog($aLog);

                    return $oCacheResult;
                    '
                );
            }
        }

        self::$__ClassCacheList[] = strtolower($sClassName);
    }
}