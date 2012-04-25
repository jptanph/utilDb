<?php
/**
 * 유틸리티 - JavaScript
 *
 * @package util
 *
 * @author jylee3@simplexi.com
 * @version 1.0
 */
class utilJavascript
{

    #======================
    # Core
    #======================

    public function __construct() {}

    /**
     * 자바스크립트 실행코드 반환
     *
     * <code>
     * $utilJavascript = utilJavascript::getInstance();
     * $sJavascriptCode = $utilJavascript->javascript("alert('message')");
     * </code>
     *
     * @param	String	$sJavascriptCode	자바스크립트 코드
     *
     * @return	String	자바스크립트를 포함한 HTML 코드
     */
    public function javascript($sJavascriptCode)
    {
        return
            "
            <script type='text/javascript'>
                ".$sJavascriptCode."
            </script>
            ";
    }

    /**
     * document.write 자바스크립트 실행코드 반환
     *
     * <code>
     * $utilJavascript = utilJavascript::getInstance();
     * $sJavascriptCode = $utilJavascript->documentWrite('write message', true);
     * </code>
     *
     * @param	Mixed	$mJavascriptCode	자바스크립트 코드
     * @param	Boolean $bIsString			문자열 여부, 문자열이면 true, 문자열이 아닐 경우 false(기본값)
     *
     * @return	String	자바스크립트를 포함한 HTML 코드
     */
    public function documentWrite($mJavascriptCode, $bIsString=false)
    {
        $sDocumentWrite = ($bIsString===false)? "document.write(%s);" : "document.write('%s');";
        if (is_array($mJavascriptCode)) {
            $sJavascriptCode = '';
            foreach ($mJavascriptCode as $sJavascript) {
                $sJavascriptCode .= sprintf($sDocumentWrite,
                    ($bIsString===false)? sprintf($sJavascript) : sprintf(str_replace(array("\n", "'"), array('\\n', '\''), $sJavascript)));
            }
        } else {
            $sJavascriptCode = sprintf($sDocumentWrite,
                ($bIsString===false)? sprintf($mJavascriptCode) : sprintf(str_replace(array("\n", "'"), array('\\n', '\''), $mJavascriptCode)));
        }

        return $this->javascript($sJavascriptCode);
    }

    /**
     * histtory.back() 자바스크립트 실행코드 반환
     *
     * <code>
     * $utilJavascript = utilJavascript::getInstance();
     * $sJavascriptCode = $utilJavascript->historyBack('이전 페이지로 이동합니다.');
     * </code>
     *
     * @param	String	$sMsg	출력 메세지, 메세지를 지정하지 않을경우 경고창이 출력되지 않음
     *
     * @return	String	자바스크립트를 포함한 HTML 코드
     */
    public function historyBack($sMsg='')
    {
        $sJavascriptCode = "history.back();";

        if (!empty($sMsg))    $sJavascriptCode = "alert('".str_replace(array("\n", "'"), array('\\n', '\''), $sMsg)."');".$sJavascriptCode;

        return $this->javascript($sJavascriptCode);
    }

    /**
     * window.close() 자바스크립트 실행코드 반환
     *
     * <code>
     * $utilJavascript = utilJavascript::getInstance();
     * $sJavascriptCode = $utilJavascript->windowClose();
     * </code>
     *
     * @param	Boolean	$bIsSelf	창을 닫을 웹브라우저의 self 참조 여부, 기본값(false)
     *
     * @return	String	자바스크립트를 포함한 HTML 코드
     */
    public function windowClose($bIsSelf=false)
    {
        $sJavascriptCode = ($bIsSelf===false)? "opener = window;\nwindow.close();" : "self.close();";

        return $this->javascript($sJavascriptCode);
    }

    /**
     * location.replace() 자바스크립트 실행코드 반환
     *
     * <code>
     * $utilJavascript = utilJavascript::getInstance();
     * $sJavascriptCode = $utilJavascript->locationReplace('www.naver.com', '네이버로 이동합니다.');
     * </code>
     *
     * @param	String	$sReplaceUrl	Replace URL
     * @param	String	$sMsg			출력 메세지, 메세지를 지정하지 않을경우 경고창이 출력되지 랂음
     * @param	String	$sPosition		Replace 대상, 지정하지 않을경우 location 객체
     *
     * @return	String	자바스크립트를 포함한 HTML 코드
     */
    public function locationReplace($sReplaceUrl, $sMsg='', $sPosition='')
    {
        $sAlertMsg = (empty($sMsg))? '' : "alert('".str_replace(array("\n", "'"), array('\\n', '\''), $sMsg)."');";

        $sLocationReplaceUrl = (empty($sPosition))?
        	"location.replace('".$sReplaceUrl."');" : $sPosition.".location.replace('".$sReplaceUrl."');";

        $sJavascriptCode = $sAlertMsg.$sLocationReplaceUrl;

        return $this->javascript($sJavascriptCode);
    }

    #======================
    # Handler
    #======================

    /**
     * alert() 코드생성
     * @param String $sMsg 메시지
     * @return String 자바스크립트 코드
     */
    private function _alert($sMsg)
    {
        $sJavascriptCode = "alert('".str_replace(array("\n", "'"), array('\\n', '\''), $sMsg)."');";
        return $this->javascript($sJavascriptCode);
    }

    /**
     * history.back() 실행
     * @param String $sMsg 메시지
     */
    public function back($sMsg=null)
    {
        $sJavascriptCode = $this->historyBack($sMsg);
        $this->_print($sJavascriptCode);
    }

//    /**
//     * opener.window.close() 실행
//     * @param String $sMsg 메시지
//     * @param Bool $bIsSelf [ true:self.close() | flase:opener.window.close() ]
//     */
//    public function windowClose($sMsg=null, $bIsSelf=false)
//    {
//        $sAlert = null;
//        if(is_null($sMsg) == false) {
//            $sAlert = $this->_alert($sMsg);
//        }
//        $sJavascriptCode = $sAlert.$this->windowClose($bIsSelf);
//        $this->_print($sJavascriptCode);
//    }

    /**
     * location.replace() 실행
     * @param String $sReplaceUrl 페이지 이동할 URL
     * @param String $sMsg 메시지
     * @param String $sPosition Replace 대상(self | parent | etc..)
     */
    public function move($sReplaceUrl, $sMsg=null, $sPosition=null)
    {
        $sJavascriptCode = $sAlert.$this->locationReplace($sReplaceUrl, $sMsg, $sPosition);
        $this->_print($sJavascriptCode);
    }

    /**
     * 최종 출력
     * @param String $sJavascript 자바스크립트 코드
     */
    private function _print($sJavascript)
    {
        $sString = '
            <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
            <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
            <title>message confirm</title>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
            </head>
            <body>';
        $sString .= $sJavascript;
        $sString .= '
            </body>
            </html>';

        exit($sString);
    }
}