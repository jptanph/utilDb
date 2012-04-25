<?php
/**
 * Model PDO Driver - MySQL
 *
 * @package model
 * @subpackage pdo
 *
 * @author jylee3@simplexi.com
 * @version 1.0
 */
class utilDbDriverMysql extends utilDbDriverCommon implements utilDbDriverInterface
{
    /**
     * Information Encrypt Function
     *
     * @param	Mixed	$mData
     * @param	String	$sKey
     * @param	String	$sIV
     *
     * @return	String
     */
    public function encrypt($mData, $sKey, $sIV='')
    {
        return "hex(aes_encrypt('".$mData."','".$sKey."'))";
    }

    /**
     * Information Decrypt Function
     *
     * @param	Mixed		$sColumnName
     * @param	String		$sKey
     * @param	String		$sIV
     * @param	Booelean	$bIsValue
     *
     * @return	String
     */
    public function decrypt($sColumnName, $sKey, $sIV='', $bIsValue=false)
    {
        $sHexFunction = ($bIsValue===false)? "unhex(".trim($sColumnName).")" : "unhex('".trim($sColumnName)."')";

        return "aes_decrypt(".$sHexFunction.",'".$sKey."')";
    }

    /**
     * SQL Escape
     *
     * @param	String	$sSql
     *
     * @return	String
     */
    public function escapeString($sSql)
    {
        return (function_exists('mysql_escape_string'))? mysql_escape_string($sSql) : parent::escapeString($sSql);
    }
}
