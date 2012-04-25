<?php
/**
 * 유틸리티 - CUrl Request
 *
 * <code>
 * 
 * $utilCurl = utilCurl::getInstance();
 * $utilCurl->init(array('url' => 'http://xe2015test.cafe24.com'));
 * $utilCurl->exec();
 * 
 * </code>
 *
 * @package     util
 *
 * @author      ul jung
 * @version	1.0
 */
class utilCurl
{
//    /**
//     * CUrl 인스턴스 반환
//     *
//     * @return	utilCurl
//     */
//    public static function getInstance()
//    {
//        return utilSplClass::load('utilCurl');
//    }

    private $curl;
    private $aCurlOption;

    /**
     * CUrl 초기화
     *
     * <code>
     * $aResult = utilCurl::getInstance()->init(array('url' => 'http://xe2015test.cafe24.com'))->exec();
     * </code>
     *
     * @param	Array	$aParam
     *             	    $aParam['url']		// CURL URL OPTION
     *                  $aParam['host']		// CURL HOST OPTION
     * 	       			$aParam['header']	// CURL HEADER OPTION
     *       			$aParam['method']	// CURL METHOD OPTION	GET|POST|DELET|PUT|HEAD
     *       			$aParam['referer']	// CURL	REFERER OPTION
     *       			$aParam['cookie']	// CURL COOKIE OPTION
     *       			$aParam['timeout'] 	// CYRK TIME OUT OPTION
     *
     * @return	utilCurl
     */
    public function init($aParam)
    {
        $this->curl     = curl_init();
        $this->setBase();
        $this->setMethod($aParam['method']);
        $this->setHeader($aParam);
        $this->setReferer($aParam['referer']);
        $this->setAgent();
        $this->setCookie($aParam['cookie']);
        $this->setPost($aParam);
        $this->setUrl($aParam['url']);
        //$this->setLogin($aParam['login'], $aParam['password']);
        $this->setTimeout($aParam['timeout']);


        foreach ($this->aCurlOption as $sKey => $mValue) {
            curl_setopt($this->curl, $sKey, $mValue);
        }

        return $this;
    }

    /**
     * Make curl request
     *
     * @return Array  'header','body','curl_error','http_code','last_url'
     */
    public function exec()
    {
        $oResponse = curl_exec($this->curl);
        $oError = curl_error($this->curl);


        $aResult = array();

        if ($oError != "") {
            $aResult['curl_error'] = $oError;
            return $aResult;
        }

        $iHeaderSize            = curl_getinfo($this->curl,CURLINFO_HEADER_SIZE);
        $aResult['header']      = substr($oResponse, 0, $iHeaderSize);
        $aResult['body']        = substr($oResponse, $iHeaderSize );
        $aResult['http_code']   = curl_getinfo($this->curl,CURLINFO_HTTP_CODE);
        $aResult['last_url']    = curl_getinfo($this->curl,CURLINFO_EFFECTIVE_URL);

        curl_close($this->curl);

        return $aResult;
    }


    private function setBase()
    {
        $this->aCurlOption = array(
            CURLOPT_RETURNTRANSFER  => 1,
            //CURLOPT_VERBOSE         => 1,
            CURLOPT_HEADER          => 1,
            CURLOPT_FOLLOWLOCATION  => 1,
            CURLOPT_SSL_VERIFYPEER  => 0,
            CURLOPT_SSL_VERIFYHOST  => 0
        );
    }

    private function setMethod($sMethod)
    {
        $sMethod = empty($sMethod) ? 'GET' : $sMethod;

        if ($sMethod == "HEAD") {
            $this->aCurlOption[CURLOPT_NOBODY] = 1;
        }
    }

    private function setReferer($sReferer)
    {
        if ($sReferer) {
            $this->aCurlOption[CURLOPT_REFERER] = $sReferer;
        }
    }

    private function setUrl($sUrl)
    {
        $this->aCurlOption[CURLOPT_URL] = $sUrl;
    }


    private function setAgent()
    {
        $sAgent = "Mozilla/5.0 (Windows; U;\r\nWindows NT 5.1; ru; rv:1.8.0.9) Gecko/20061206 Firefox/1.5.0.9";
        $this->aCurlOption[CURLOPT_USERAGENT] = $sAgent;
    }

    private function setHeader($aParam)
    {
        $aHeader = array(
            "Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5",
            "Accept-Language: ru-ru,ru;q=0.7,en-us;q=0.5,en;q=0.3",
            "Accept-Charset: windows-1251,utf-8;q=0.7,*;q=0.7",
            "Keep-Alive: 300"
        );

        if (isset($aParam['host']))    $aHeader[] = "Host: ".$aParam['host'];
        if (isset($aParam['header']))  $aHeader[] = $aParam['header'];

        $this->aCurlOption[CURLOPT_HTTPHEADER] = $aHeader;
    }

    private function setCookie($sCookie)
    {
        if ($sCookie['cookie']) {
            $this->aCurlOption[CURLOPT_COOKIE] = $sCookie;
        }
    }

    private function setPost($aParam)
    {
        if ( $aParam['method'] == "POST") {
            $this->aCurlOption[CURLOPT_POST]        = true;
            $this->aCurlOption[CURLOPT_POSTFIELDS]  = $aParam['post_fields'];
        }
    }

    /*
    private function setLogin($sLogin, $sPwd)
    {
        if (($sLogin) && ($sPwd)) {
            $this->aCurlOption[CURLOPT_USERPWD] = $sLogin.':'.$sPwd;
        }
    }
    */

    private function setTimeout($iTimeout)
    {
        $iTimeout = ($iTimeout) ? $iTimeout : 5;
        $this->aCurlOption[CURLOPT_TIMEOUT] = $iTimeout;
    }
}