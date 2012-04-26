<?php
final class DB
{
	/**
     * DB Class
     * by Arone Lee
     * @var PDOStatement
     */
	private static $conn = array();

	private $host;

	public function __construct() {	}

	public function setConnect($prefix = COMMON_DB_TYPE, $host = COMMON_DB_HOST, $user= COMMON_DB_USER , $passwd = COMMON_DB_PASSWD , $db_name = COMMON_DB_NAME , $port = COMMON_DB_PORT)
	{
		try {
			if(array_key_exists($host, self::$conn) === false) {
				self::$conn[$this->host] = new PDO($prefix.':dbname='.$db_name.';host='.$host.';port='.$port, $user, $passwd);
                $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			}
		} catch(PDOException $e) {
			throw new Exception($e->getMessage());
		}
		return $this;
	}

    public function setAttribute($attribute, $value)
    {
        self::$conn[$this->host]->setAttribute ($attribute, $value);
    }

	/* pgsql last index, param sequence name	*/
	public function getPgsqlLastIdx($seq_name)
	{
		return self::$conn[$this->host]->lastInsertId($seq_name);
	}

	public function query($sql)
	{
		return self::$conn[$this->host]->query($sql);
	}

	private function execute($sql)
	{
	    /* @var $data PDOStatement */
		$data = self::$conn[$this->host]->prepare($sql);

		$data->execute();
		return $data;
	}

	/**
	*  Insert Query
	*/
	public function setInsertQuery($sql)
	{
		/* @var $oPDO PDO */
		$oPDO = self::$conn[$this->host];
		$mResult = $oPDO->exec($sql);
		return $mResult;
	}

	/**
	*  Update Query
	*/
	public function setUpdateQuery($sql)
	{
		/* @var $oPDO PDO */
		$oPDO = self::$conn[$this->host];
		$mResult = $oPDO->exec($sql);
		return $mResult;
	}

	/**
	*  First record of select result
	*/
	public function getValue($sql)
	{
		$data = $this->execute($sql);
		$row = $data->fetchColumn();
		return $row;
	}

	/**
	*  First record of select result
	*/
	public function getRow($sql)
	{
		$data = $this->execute($sql);
		$row = $row = $data->fetch(PDO::FETCH_ASSOC);
		return $row;
	}
 
	/**
	*  total record of select result
	*/
	public function getRows($sql)
	{
		$row = array();
		$data = $this->execute($sql);
		$row = $data->fetchAll(PDO::FETCH_ASSOC);
		return $row;
	}

	public function Disconnect()
	{
		self::$conn[$this->host]->closeCursor();
	}
}
?>