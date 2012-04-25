<?php
/**
 * Decrypt Class
 * by Jed Woo, 2010
 */

define ('MCRYPT_RAND', 2);
define ('MCRYPT_RIJNDAEL_128', "rijndael-128");
define ('MCRYPT_MODE_CBC', "cbc");

class utilDesCbc
{
	protected $sEncryptKey;

    protected $sEncryptType;

    protected $sEncryptMode;

    protected $sIv;

    public static function getInstance($sEncryptKey, $sIv)
    {
        return new self($sEncryptKey, $sIv);
    }

    public function __construct($sEncrypt, $sIv)
    {
        $this->sEncryptKey      = $sEncrypt;
        $this->sIv              = $sIv;
        $this->sEncryptType     = MCRYPT_RIJNDAEL_128;
        $this->sEncryptMode     = MCRYPT_MODE_CBC;
    }

    public function encrypt($sString)
    {
        return trim(
                    bin2hex(
                        mcrypt_encrypt(
                            $this->sEncryptType,
                            $this->sEncryptKey,
                            $sString,
                            $this->sEncryptMode,
                            $this->sIv
                        )
                    )
               );
    }

    public function decrypt($sString)
    {
        return rtrim(
                    mcrypt_decrypt(
                            $this->sEncryptType,
                            $this->sEncryptKey,
                            pack("H*",$sString),
                            $this->sEncryptMode,
                            $this->sIv
                    ),
                    "\0\4"
               );
    }

    /**
     * api key
     *
     * @param string $sKey
     *
     * @return string
     */
    public static function getApiKey($sKey)
    {
        $sKey = hash('sha256', $sKey);
        $sKey = md5($sKey);
        return $sKey;
    }

    /**
     * api iv
     *
     * @param string $sKey
     *
     * @return string
     */
    public static function getApiIv($sKey)
    {
        $sKey = self::getApiKey($sKey);
        $sIv = '';
        for ($i = 0; $i < 16; $i++) {
            $sIv .= $sKey[$i];
        }
        return $sIv;
    }
}
?>