<?php
/**
 * 캐쉬 Serialize 데이터 타입 클래스
 *
 * @author      jylee3@simplexi.com
 */
class utilCacheDataSerialize/* extends utilCacheDataAbstract*/
{
    public function data($sCacheDataFile, $oData=null)
    {
        if(!is_null($oData)) {
            $sFileContents = serialize($oData);

            file_put_contents($sCacheDataFile, $sFileContents);
        }

        $oResult = file_get_contents($sCacheDataFile);

        return unserialize($oResult);
    }
}