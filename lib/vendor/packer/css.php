<?php
/**
 * CSS minify 버젼 생성
 */

class CSSMin
{
    /**
    * construct
    */
    private function __consturct(){}

    /**
    * CSS 컨텐츠 압축
    * 
    * @param string $v (CSS 소스)
    * @return string
    */
    public static function minify($v)
    {
        $v = trim($v);
        $v = str_replace("\r\n", "\n", $v);
        $search = '/@charset[\s]+(?:("|\').+?\1[\s]*;?|.+?;)/i';
        $replace = '';
        $v = preg_replace($search, $replace, $v);
        $search = array("/\/\*[\d\D]*?\*\/|\t+/", "/\s+/", "/\}\s+/");
        $replace = array(null, " ", "}\n");
        $v = preg_replace($search, $replace, $v);
        $search = array("/\\;\s/", "/\s+\{\\s+/", "/\\:\s+\\#/", "/,\s+/i", "/\\:\s+\\\'/i", "/\\:\s+([0-9]+|[A-F]+)/i");
        $replace = array(";", "{", ":#", ",", ":\'", ":$1");
        $v = preg_replace($search, $replace, $v);
        $v = str_replace("\n", null, $v);

        return $v;
    }
}