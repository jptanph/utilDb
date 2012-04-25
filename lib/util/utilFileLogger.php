<?php
/**
 * file logger library
 */
class utilFileLogger
{
    /**
     * log for created / modified file
     *
     * @param $sFilename file path
     * @param $sType  i:최초 업로드, u:수정, d:삭제
     * @param $bComplete
     */
    public function log($sFilename, $sType, $bComplete=true)
    {
        $aData = $this->getLoggingData($sFilename, $sType, $bComplete);
        $this->writeLog($aData);
    }

    public function getLoggingData($sFilename, $sType, $bComplete=true)
    {
        $sTime = date('D M d H:i:s Y');

        $sRemoteIp = $_SERVER['REMOTE_ADDR'];

        $iFilesize = filesize($sFilename);
        if(empty($iFilesize)) $iFilesize = 0;

        $sUserid = PLUGIN_USER_ID;

        $sComplete = $bComplete ? "c" : "i";

        return array(
            "time" => $sTime,
            "remote_ip" => $sRemoteIp,
            "filesize" => $iFilesize,
            "filename" => $sFilename,
            "type" => $sType,
            "userid" => $sUserid,
            "complete" => $sComplete
        );
    }

    /**
     * write log (xweblog)
     *
     * @param $sUserid
     * @param $sRemoteIp
     * @param $sFilename
     * @param $bComplete
     * @return unknown_type
     */
    public function writeLog($aData)
    {
        $sLog =
            $aData["time"]." ".
            $aData["remote_ip"]." ".
            $aData["filesize"]." ".
            $aData["filename"]." ".
            $aData["type"]." ".
            $aData["userid"]." ".
            $aData["complete"]."\n";

        if(! file_exists(COMMON_LOG_SYSTEM)) {
            return;
        }

        $oFile = fopen(COMMON_LOG_SYSTEM, "a");
        if($oFile == false) {
            return;
        }

        fwrite($oFile, $sLog);
        fclose($oFile);
    }
}