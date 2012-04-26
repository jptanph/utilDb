<?php
/**
 * Model PDO Driver - Interface
 *
 * @package model
 * @subpackage pdo
 *
 * @author jylee3@simplexi.com
 * @version 1.0
 */
interface utilDbDriverInterface
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
    public function encrypt($mData, $sKey, $sIV='');

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
    public function decrypt($sColumnName, $sKey, $sIV='', $bIsValue=false);

    /**
     * SQL Escape
     *
     * @param	String	$sSql
     *
     * @return	String
     */
    public function escapeString($sSql);
}
