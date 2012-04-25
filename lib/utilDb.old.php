<?php

/**
* @author YongHun Jeong <yhjeong@simplexi.com>
* @version 1.1
*
* utilDbclass
*/

require_once (SERVER_DOCUMENT_ROOT .DS.'lib/util/utilSplClass.php');

require_once (SERVER_DOCUMENT_ROOT .DS.'lib/util/Db/utilDbAdapter.php');

require_once (SERVER_DOCUMENT_ROOT .DS.'lib/util/Db/utilDbDriverCommon.php');

require_once (SERVER_DOCUMENT_ROOT .DS.'lib/util/Db/utilDbDriverInterface.php');

require_once (SERVER_DOCUMENT_ROOT .DS.'lib/util/Db/utilDbDriverMysql.php');

require_once (SERVER_DOCUMENT_ROOT .DS.'lib/util/Db/utilDbModel.php');

require_once (SERVER_DOCUMENT_ROOT .DS.'lib/util/Db/utilDbUtility.php');

class utilDb extends utilDbModel
{
	/**
     * 트랜잭션 시작
     * @return Bool true|false
     */
    final public function transactionStart()
    {
        return $this->dbDriver->beginTransaction();
    }

    /**
     * 트랜잭션 커밋
     * @return Bool true|false
     */
    final public function transactionCommit()
    {
        return $this->dbDriver->commit();
    }

    /**
     * 트랜잭션 롤백
     * @return Bool true|false
     */
    final public function transactionRollback()
    {
        return $this->dbDriver->rollBack();
    }

 	/**
     * query 실행
     * PDO의 SQL Error가 발생할 경우 LOG를 남깁니다.
     * 사용법은 coreModel::query() 참조 하세요.
     *
     * @param Strig $sSql sql
     * @param String $sType row,rows,exec [?d]('?d / row?d / rows?d / exec?d' 사용하면 Debug Log가 기록 됩니다.)
     * @param Bool $bIsCache 캐쉬 여부
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
     * framework 에서 지원하는 상수 반환
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
     * Query 종류 반환
     * @param string $sSql 원본 Query 문자열
     * @return mixed SELECT, UPDATE, DELETE, INSERT 중 1 반환. 아무것도 해당되지 않으면 false 반환
     * */
    private function _getQueryType($sSql)
    {
        preg_match("/^\s*(SELECT|UPDATE|DELETE|INSERT)[\s]/i", $sSql, $aMatches);
        if (count($aMatches) > 1) return strtolower($aMatches[1]);
        else return false;
    }
}