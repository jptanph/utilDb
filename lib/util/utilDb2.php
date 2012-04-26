<?php

/**
* @author YongHun Jeong <yhjeong@simplexi.com>
* @version 1.1
*
* utilDbclass
*/

require_once (SERVER_DOCUMENT_ROOT .DS. 'utilDb/lib/util/utilSplClass.php');
require_once (SERVER_DOCUMENT_ROOT .DS. 'utilDb/lib/util/Db/utilDbAdapter.php');
require_once (SERVER_DOCUMENT_ROOT .DS. 'utilDb/lib/util/Db/utilDbDriverCommon.php');
require_once (SERVER_DOCUMENT_ROOT .DS. 'utilDb/lib/util/Db/utilDbDriverInterface.php');
require_once (SERVER_DOCUMENT_ROOT .DS. 'utilDb/lib/util/Db/utilDbDriverMysql.php');
require_once (SERVER_DOCUMENT_ROOT .DS. 'utilDb/lib/util/Db/utilDbModel.php');
require_once (SERVER_DOCUMENT_ROOT .DS. 'utilDb/lib/util/Db/utilDbUtility.php');

class utilDb2 extends utilDbModel
{
	private $str;
	
	/**
     * ???? ??
     * @return Bool true|false
     */
    final public function transactionStart()
    {
        return $this->dbDriver->beginTransaction();
    }

    /**
     * ???? ??
     * @return Bool true|false
     */
    final public function transactionCommit()
    {
        return $this->dbDriver->commit();
    }

    /**
     * ???? ??
     * @return Bool true|false
     */
    final public function transactionRollback()
    {
        return $this->dbDriver->rollBack();
    }

 	/**
     * query ??
     * PDO? SQL Error? ??? ?? LOG? ????.
     * ???? coreModel::query() ?? ???.
     *
     * @param Strig $sSql sql
     * @param String $sType row,rows,exec [?d]('?d / row?d / rows?d / exec?d' ???? Debug Log? ?? ???.)
     * @param Bool $bIsCache ?? ??
     */
    final public function query($sSql, $sType=null, $bIsCache=false)
    {
        $sSql = trim($sSql);
        $aType = parse_url($sType);
        $sType = ($aType['path']) ? $aType['path'] : null;

        if(is_null($sType) === true) $sType = $this->_getType($sSql);

        $mResult = parent::query($sSql, $sType);

        return $mResult;
    }

    /**
     * framework ?? ???? ?? ??
     * @param unknown_type $sSql
     */
    private function _getType($sSql)
    {
        $sQueryType = $this->_getQueryType($sSql);

        switch ($sQueryType) {
            case 'select' :
                $sResult = UTIL_DB_RESULT_ROWS;
                break;
            default :
                $sResult = UTIL_DB_RESULT_EXEC;
                break;
        }

        return $sResult;
    }

    /**
     * Query ?? ??
     * @param string $sSql ?? Query ???
     * @return mixed SELECT, UPDATE, DELETE, INSERT ? 1 ??. ???? ???? ??? false ??
     * */
    private function _getQueryType($sSql)
    {
        preg_match("/^\s*(SELECT|UPDATE|DELETE|INSERT)[\s]/i", $sSql, $aMatches);
        if (count($aMatches) > 1) return strtolower($aMatches[1]);
        else return false;
    }

    /**
    * update query wrapper
    */
    public function update($table, $aData, $sWhere="")
    {
        $sSql = $this->getUpdateQuery($table, $aData, $sWhere);

        if($sSql !== false) {
            return $this->query($sSql);
        } else return false;
    }

    /**
    * update query wrapper
    */
    public function insert( $table, $aData )
    {
        $sSql = $this->getInsertQuery($table, $aData);
        if($sSql !== false) {
            return $this->query($sSql);
        } else return false;
    }

	/**
	* generate sql insert statement.
	* Implementation: 
	*		$aData = array(
	*			'field1' => 'value1',
	*			'field2' => 'value2'			
	*		);
	*		$sql = utilDbQuery::getInsertQuery('table_name', $aData);
	*
	* @param string $table => table name
	* @param array $aData => data to be inserted
	* @return string result.
	*/
	public function getInsertQuery($table, $aData)
    {
        return $this->getInsertQueryLoop($table, array($aData));
    }
	
	/**
	* generate sql insert statement.
	* Implementation: 
	*		$aInsert[] = array('id' => '50', 'name' => 'boots');
	*		$aInsert[] = array('id' => '51', 'name' => 'arden');
	*		$aInsert[] = array('id' => '52', 'name' => 'sincere');
	*		$aInsert[] = array('id' => '53', 'name' => 'ardval');
	*		$sql = utilDbQuery::getInsertQueryLoop('table_name', $aInsert);
	*
	* @param string $table => table name
	* @param array $aData => data to be inserted
	* @return string result.
	*/
	public function getInsertQueryLoop($table, $aData)
    {
        if(!count($aData)) return false;

        $i = 0;
        $aInsert = array();
        $aField = array();
        foreach($aData as $sKey => $aValue) {

	        $aInsertData = array();
	        foreach($aValue as $field => $value)
	        {
	            if($i==0) $aField[] = $field;
	            $aInsertData[] = self::checkValue($value);
	        }
	        $aInsert[] = '('.implode(',', $aInsertData).')';
			$i++;
        }
		
		$this->str	= "INSERT INTO ".$table." (".implode(',', $aField).") VALUES ".implode(',', $aInsert);
		
        return $this;
    }

	/**
	* generate sql insert statement.
	* Implementation: 
	*		$aUpdate = array(
	*			'name' => 'updated'
	*		);
	*		$sWhere = "id = '52'";
	*		$sql = utilDbQuery::getUpdateQuery($sTable, $aUpdate, $sWhere);
	*
	* @param string $table => table name
	* @param array $aData => data to be updated
	* @param string $sWhere(optional) => string condition
	* @return string result.
	*/
	public function getUpdateQuery($table, $aData, $sWhere="")
    {
        $str = "";
        if(!count($aData)) return false;

        foreach($aData as $field => $value)
        {
            $str .= $field." = ".$this->checkValue($value).",";
        }
        return "UPDATE ".$table." SET ".substr($str, 0, -1)." ".($sWhere ? " WHERE ".$sWhere : "");
    }

	
	private function checkValue($value){

        if($value == "null" || strtolower($value) == "now()")
		{
            return $value;
        }
		
        switch (strtolower(gettype($value)))
		{
            case 'string':
                settype($value, 'string');
                $value = "'".mysql_escape_string($value)."'";
                break;
            
			case 'integer':
                settype($value, 'integer');
                break;
            case 'double' :
            
			case 'float' :
                settype($value, 'float');
                break;
            
			case 'boolean':
                settype($value, 'boolean');
                break;
            
			case 'array':
                $value = "'".mysql_escape_string(implode(',', $value))."'";
                break;
            
			case 'null' :
                $value = 'null';
                break;
        
		}
        return $value;
    }

	final public function sys_query($sSql, $sType=null)
    {
        $mResult = $this->dbDriver->query($sSql, $sType);

        return $mResult;
    }
	
	final public function selectAll( $sTblName , $aTblFields = NULL , $sWhereClause = NULL )
	{
		$sSql = "SELECT * FROM $sTblName";
		return $this->query($sSql);
	}
	
	// Debug method chain
	final public function debug()
	{
		return $this->str;
	}
}








