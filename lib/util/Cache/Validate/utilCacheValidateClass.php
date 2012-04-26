<?php
/**
 * 캐쉬 데이터 타입 검증  클래스
 *
 * @author     jylee3@simplexi.com
 */
class utilCacheValidateClass/* extends utilCacheValidateAbstract*/
{
    protected $controller;

    function __construct(&$utilCacheController)
    {
        $this->controller = $utilCacheController;
    }

    public function valid()
    {
        $oValidData = $this->_getVaildData();
        $oVaildFileData = $this->_getVaildFileData();

        if($oValidData==$oVaildFileData) {
            return true;
        } else {
            return false;
        }
    }

    public function create()
    {
        $aVaildData = $this->_getVaildData();

        return $this->_getVaildFileData($aVaildData);
    }

    protected function _getVaildData()
    {
        return $this->controller->attr('ValidData');
    }

    protected function _getVaildFileData($oData=null)
    {
        $sFileName = $this->controller->_getCacheFileName('dat.serialize');

        return utilCacheDataParser::data($sFileName, 'Serialize', $oData);
    }
}