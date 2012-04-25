<?php
/**
 * 캐쉬 유틸리티 메인 컨트롤러(Cache Utility Controller)
 *
 * @desc
 *  1. 이 유틸리티는 core 또는 common Controller 클래스에서 초기화됩니다.
 *
 * @author      jylee3@simplexi.com
 */
class utilCacheController
{
    const COMMON = -0x01;
    
    protected $sCafe24Id;
    protected $sCacheName;
    protected $sCacheType;
    protected $sCacheDataFile;
    protected $sCacheDataPath;
    protected $sCacheDataPath_Root;
    protected $sCacheDataType;

    protected $aAttributes;

    function __construct($sCacheType, $sCacheName, $sCafe24Id='', $sCacheDataType='Serialize')
    {
        if(!empty($sCafe24Id)) {
            $this->sCafe24Id = $sCafe24Id;
        } else {
            $this->sCafe24Id = (isset($_SERVER['mall_id'])) ? $_SERVER['mall_id'] : $_SERVER['builder_id'];
        }

        $this->sCacheType = $sCacheType;
        $this->sCacheName = $sCacheName;
        $this->sCacheDataType = $sCacheDataType;
    }

    /**
     * 캐쉬 속성 설정
     *
     * @param Array, String     $sAttributes    속성정보(Array, JSON String)
     */
    public function attribute($sAttributes='')
    {
        $this->_setAttrubutes($sAttributes);

        $this->_setCachePath();
        $this->sCacheDataFile = $this->_getCacheFileName();

        if(!file_exists($this->sCacheDataFile)) {
            $this->attr('StateCode', 220);
        } else if(!$this->is_valid()) {
            $this->attr('StateCode', 210);
        } else {
            $this->attr('StateCode', 200);
        }

        return $this;
    }

    /**
     * 클래스 멤버 변수 반환
     *
     * @param String    $sName
     *
     * @return Object
     */
    public function _getClassVar($sName)
    {
        return $this->${sName};
    }

    /**
     * 캐쉬 파일 이름 반환
     *
     * @param String    $sExt   확장자
     *
     * @return String
     */
    public function _getCacheFileName($sExt='')
    {
        if(empty($sExt))    $sExt = strtolower($this->sCacheDataType);

        $sFileName = sprintf("%s%s.%s", $this->sCacheDataPath, md5($this->attr('ValidKey')), $sExt);

        return strtolower($sFileName);
    }

    /**
     * 캐쉬 데이터 설정 및 반환
     *
     * @param Obejct    $oData
     *
     * @return Object, false(Boolean)
     */
    public function data($oData=null)
    {
        if(!is_null($oData)) {
            /**
             * 디렉토리가 존재하지 않는 경우 디렉토리 생성
             */
            if($this->attr('StateCode')==220) {
                $bResult = utilFile::recursive_mkdir($this->sCacheDataPath);

                // 디렉토리 생성에 실패한 경우 False 반환
                if(!$bResult)   return false;
            }

            utilCacheDataParser::data($this->sCacheDataFile, $this->sCacheDataType, $oData);

            $this->createValidate();
        }

        return utilCacheDataParser::data($this->sCacheDataFile, $this->sCacheDataType);
    }

    /**
     * 캐쉬 속성 설정 및 반환
     *
     * @param String    $sAttrName
     * @param Object    $oData
     *
     * @return Object, false(Boolean)
     */
    public function attr($sAttrName, $oData=null)
    {
        if(!is_null($oData)) {
            if(!array_key_exists($sAttrName, $this->aAttributes)) {
                return false;
            }

            $this->aAttributes[$sAttrName] = $oData;
        }

        return $this->aAttributes[$sAttrName];
    }

    /**
     * 캐쉬 만료기간 검증
     *
     * @return Boolean
     */
    public function is_expired()
    {
        if($this->attr('ExpireTime')>0) {
            if($this->attr('StateCode')==0 || $this->attr('StateCode')==200) {
                $iFileTime = filemtime($this->sCacheDataFile);

                if($iFileTime!==false) {
                    $iCurrentTime = time();
                    $iExpiredTime = $iFileTime + $this->attr('ExpireTime');

                    if($iExpiredTime>=$iCurrentTime) {
                        return true;
                    }
                }
            }

            return false;
        }

        return true;
    }

    /**
     * 캐쉬 유효성 검증
     *
     * @return Boolean
     */
    public function is_valid()
    {
        if($this->attr('StateCode')==0 || $this->attr('StateCode')==200) {
            if(!$this->is_expired())    return false;

            return $this->isValidate();
        } else {
            return false;
        }
    }

    /**
     * 캐쉬 삭제(디렉토리 기준)
     */
    public function clear()
    {
        utilFile::rmDir($this->sCacheDataPath_Root);
    }

    protected function _setAttrubutes($sAttributes)
    {
        $this->aAttributes = array(
                'StateCode'=>0,                 // 상태 코드
                                                //   0 : 초기
                                                // 150 : 캐쉬 취소(기능로직에서 제외)
                                                // 200 : 캐쉬 파일 사용
                                                // 210 : 캐쉬 파일 사용중이나, 스케쥴에 의한 유효성 확인 실패
                                                // 220 : 캐쉬 디렉토리 없음
                                                // 230 : 캐쉬 파일 없음

                'ExpireTime'=>0,                // 유효시간
                'FilePathLevel'=>0,             // 파일 경로 생성 레벨

                'ValidKey'=>'',                 // 검증 키(공통), 파일 경로 생성 레벨에 따른 디렉토리 구조
                'ValidData'=>'',                // 검증 데이터
                'ValidSql'=>'',                 // SQL 데이터(2차원 배열 또는 배열)
            );

        if(!empty($sAttributes)) {
            if(is_array($sAttributes)) {
                $aAttributes = $sAttributes;
            } else {
                $aAttributes = json_decode($sAttributes, true);
            }

            $this->aAttributes = array_merge($this->aAttributes, $aAttributes);
        }
    }

    /**
     * 캐쉬 경로 반환
     *
     * @return Boolean
     */
    protected function _setCachePath()
    {
        $sValidKey = $this->attr('ValidKey');
        $iFilePathLevel = $this->attr('FilePathLevel');        
        
        /**
         * 공통 캐쉬 영역 확인
         */
        if($this->sCafe24Id!=self::COMMON) {    // Cafe24 ID가 -1 값이 아니면 각 아이디 단위로 캐쉬 구성
            if($GLOBALS['SERVERICE_NAME'] == 'BUILDER') {
                $sPath = DIR_USER_CACHE_DMC.'/_cache/'.$this->sCacheType.'/'.$this->sCacheName.'/'.md5($sValidKey).'/';
            } else {
                $sPath = RESOURCE_PWD.'/dmc/_cache/'.$this->sCacheType.'/'.$this->sCafe24Id.'/'.$this->sCacheName.'/'.md5($sValidKey).'/';
            }
        } else {                                // Cafe24 ID가 -1이면 공통  단위로 캐쉬 구성
            if($GLOBALS['SERVERICE_NAME'] == 'BUILDER') {
                $sPath = DIR_COMMON_CACHE_ROOT.'/_cache/'.$this->sCacheType.'/'.$this->sCacheName.'/'.md5($sValidKey).'/';
            } else {
                $sPath = RESOURCE_PWD.'/dmc/_cache/'.$this->sCacheType.'/common/'.$this->sCacheName.'/'.md5($sValidKey).'/';
            }
        }
        
        $this->sCacheDataPath_Root = strtolower($sPath);

        for ($i=0; $i<$iFilePathLevel; $i++) {
            $sPath .= substr($sValidKey, 0, ($i+1)).'/';
        }

        $this->sCacheDataPath = strtolower($sPath);
    }

    /**
     * 검증 반환
     *
     * @return Boolean
     */
    protected function isValidate()
    {
        return $this->getValidateClassLoad()->valid();
    }

    /**
     * 검증 생성
     *
     * @return Boolean
     */
    protected function createValidate()
    {
        return $this->getValidateClassLoad()->create();
    }

    /**
     * 검증 클래스 로드
     */
    protected function getValidateClassLoad()
    {
        $sClassName = 'utilCacheValidate'.ucfirst(strtolower($this->sCacheType));

        if(class_exists($sClassName)) {
            return new ${sClassName}($this);
        } else {
            new exceptionDmc($sClassName.' is Class Not Exits.');
        }
    }
}