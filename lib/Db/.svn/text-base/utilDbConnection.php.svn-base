<?php
/**
* @author Arden Vallente <vallente@simplexi.com>
* @version 1.0
* 
* Create PDO connection
*/
class utilDbConnection extends PDO
{
	public static function getInstance()
	{
		return new self();
	}

	/*
	* Create PDO connection
	* @return void 
	*/
	public function __construct()
	{
		$aDsn = $this->setDsnPdo();

		try {
			parent::__construct($aDsn['dsn'], $aDsn['user'], $aDsn['passwd']);
		} catch(PDOException $e){
			echo 'Connection failed: ' . $e->getMessage();
		}
	}

	/*
	* Return array for PDO connection
	* @return array 
	*/
	private function setDsnPdo()
    {   
        return array(
            'dsn'       => COMMON_DB_TYPE.':dbname='.COMMON_DB_NAME.';host='.COMMON_DB_HOST,
            'user'      => COMMON_DB_USER,
            'passwd'    => COMMON_DB_PASSWD
        );
    }
}