<?php
/**
 * 캐쉬 파일 타입 검증  클래스
 *
 * @author     jylee3@simplexi.com
 */
class utilCacheValidateFile/* extends utilCacheValidateAbstract*/
{
    protected $controller;

    function __construct(&$utilCacheController)
    {
        $this->controller = $utilCacheController;
    }

    public function valid()
    {
        $sVaildFile = $this->_getVaildFile();

        $aValidData = $this->_getVaildData($sVaildFile);
        $aVaildFileData = $this->_vaildFileData();

        if($aValidData==$aVaildFileData) {
            return true;
        } else {
            return false;
        }
    }

    public function create($args)
    {
        $sVaildFile = $this->_getVaildFile();

        $aValidData = $this->_getVaildData($sVaildFile);

        if($aValidData!==false) {
            return $this->_vaildFileData($aValidData);
        } else {
            return false;
        }
    }

    protected function _getVaildFile()
    {
        return $this->controller->attr('ValidKey');
    }

    protected function _getVaildData($sVaildFile)
    {
        if(is_file($sVaildFile)) {
            $iFileSize = sprintf("%u", filesize($sVaildFile));
            $iFileTime = filemtime($sVaildFile);
            $sMD5 = md5($sVaildFile);
            
            return array('FileSize'=>$iFileSize,
                'FileTime'=>$iFileTime,
                'MD5'=>$sMD5);
        } else {
            return false;
        }
    }

    protected function _vaildFileData($oData=null)
    {
        $sFileName = $this->controller->_getCacheFileName('file');

        return utilCacheDataParser::data($sFileName, 'Serialize', $oData);
    }
}