<?php
/**
 * Sql Query Hook
 */
function T_dbQueryConvert(&$sSql)
{
    return sqlInjectionAttackStringEncode($sSql);
}

/**
 * Sql Injection Attack String Encoding
 */
function sqlInjectionAttackStringEncode(&$sSql)
{
    $f = create_function('&$aVal', '
        switch($aVal[0]) {
            case \'--\':
                $sResult = \'LS0=\';
                break;
            case \'/*\':
                $sResult = \'Lyo=\';
                break;
            case \'@@\':
                $sResult = \'QEA=\';
                break;
            case \'#\':
                $sResult = \'Iw==\';
                break;
        }
        return $sResult;
    ');

    return preg_replace_callback('/\-\-|\/\*|\@\@|\#/', $f, $sSql);
}

/**
 * Sql Query Result Hook
 */
function T_dbDataConvert(&$mData)
{
    if( $mData === false ) return false;
    return sqlInjectionAttackStringDecode($mData);

}

/**
 * Sql Injection Attack String Decoding
 */
function sqlInjectionAttackStringDecode(&$mData)
{
    $fReplace = create_function('&$aVal', '
        switch($aVal[0]) {
            case \'LS0=\':
                $sResult = \'--\';
                break;
            case \'Lyo=\':
                $sResult = \'/*\';
                break;
            case \'QEA=\':
                $sResult = \'@@\';
                break;
            case \'Iw==\':
                $sResult = \'#\';
                break;
        }
        return $sResult;
    ');

    $sDecodeRegex = '/LS0\=|Lyo\=|QEA\=|Iw\=\=/';

    if(empty($mData[0]) === false && is_array($mData[0]) === true) {
        foreach($mData as $iKey => &$aRows) {
            $mResult[$iKey] = preg_replace_callback($sDecodeRegex, $fReplace, $aRows);
        }
    } else if(is_array($mData) === true) {
        $mResult = preg_replace_callback($sDecodeRegex, $fReplace, $mData);
    }
	
    return $mResult;
}

?>