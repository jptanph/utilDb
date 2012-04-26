<?php
/**
 * 캐쉬 SQL 처리 타입 검증  클래스
 *
 * @author     jylee3@simplexi.com
 */
class utilCacheValidateSql/* extends utilCacheValidateAbstract*/
{
    protected static $oModelBaseInstance = array();
    protected $controller;
    protected $aSqlData = null;

    function __construct(&$utilCacheController)
    {
        $this->controller = $utilCacheController;
    }

    public function valid()
    {
        $oValidData = $this->_createValidData();

        $oVaildFileData = $this->_vaildFileData();

        if($oValidData==$oVaildFileData) {
            return true;
        } else {
            return false;
        }
    }

    public function create()
    {
        $aVaildData = $this->_createValidData();

        return $this->_vaildFileData($aVaildData);
    }

    protected function _getValidData()
    {
        return $this->_vaildFileData();
    }

    protected function _createValidData()
    {
        if(is_null($this->aSqlData)) {
            $aSqlArray = $this->controller->attr('ValidSql');

                    foreach($aSqlArray as $aSql) {
                if(is_array($aSql)) {
                    $sDatabase = ucfirst(strtolower($aSql[0]));
                    $sSql = $aSql[1];
                } else {
                    $sDatabase = 'Common';
                    $sSql = $aSql;
                }

                if(!utilCacheValidateSql::$oModelBaseInstance[$sDatabase]) {
                    $sModelBaseClass = 'model'.$sDatabase.'Base';

                    utilCacheValidateSql::$oModelBaseInstance[$sDatabase] = new ${sModelBaseClass}();
                }

                $oModelInstance = utilCacheValidateSql::$oModelBaseInstance[$sDatabase];

                if(method_exists($oModelInstance, 'executeQueryDup')) {
                    $this->aSqlData[] = $oModelInstance->executeQueryDup(trim($sSql), "rows");
                } else {
                    $this->aSqlData[] = $oModelInstance->executeQuery(trim($sSql), "rows");
                }
            }
        }

        return $this->aSqlData;
    }

    protected function _vaildFileData($oData=null)
    {
        $sFileName = $this->controller->_getCacheFileName('sql.serialize');

        return utilCacheDataParser::data($sFileName, 'Serialize', $oData);
    }
}