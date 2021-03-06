<?php
/**
* @author Arden Vallente <vallente@simplexi.com>
* @version 1.0
* 
* Generate queries such as modify and insert
*/
class utilDbQuery
{
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
	public static function getInsertQuery($table, $aData)
    {
        return self::getInsertQueryLoop($table, array($aData));
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
	public static function getInsertQueryLoop($table, $aData)
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
        return "INSERT INTO ".$table." (".implode(',', $aField).") VALUES ".implode(',', $aInsert);
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
	public static function getUpdateQuery($table, $aData, $sWhere="")
    {
        $str = "";
        if(!count($aData)) return;

        foreach($aData as $field => $value)
        {
            $str .= $field." = ".self::checkValue($value).",";
        }
        return "UPDATE ".$table." SET ".substr($str, 0, -1)." ".($sWhere ? " WHERE ".$sWhere : "");
    }

	
	private static function checkValue($value){

        if($value == "null" || strtolower($value) == "now()"){
            return $value;
        }
        switch (strtolower(gettype($value))){
            case 'string':
                settype($value, 'string');
                $value = "'". addslashes($value)."'";
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
                $value = "'". addslashes(implode(',', $value))."'";
                break;
            case 'null' :
                $value = 'null';
                break;
        }
        return $value;
    }
}
