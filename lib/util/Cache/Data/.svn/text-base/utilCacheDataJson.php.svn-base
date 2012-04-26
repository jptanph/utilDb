<?php
/**
 * 캐쉬 JSON 데이터 타입 클래스
 *
 * @author      jylee3@simplexi.com
 */
class utilCacheDataJson/* extends utilCacheDataAbstract*/
{
    public function data($sCacheDataFile, $oData=null)
    {
        if(!is_null($oData)) {
            $sFileContents = json_encode($oData);

            file_put_contents($sCacheDataFile, $sFileContents);
        }

        $oResult = file_get_contents($sCacheDataFile);

        return json_decode($oResult);
    }
}