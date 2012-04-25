<?php
/**
 * 캐쉬 기본 타입 검증  클래스
 *
 * @author     jylee3@simplexi.com
 */
class utilCacheValidateNormal/* extends utilCacheValidateAbstract*/
{
    protected $controller;

    function __construct(&$utilCacheController)
    {
        $this->controller = $utilCacheController;
    }

    /**
     * 검증 결과 반환
     *
     * @return Boolean
     */
    public function valid()
    {
        return true;
    }

    /**
     * 검증 데이터 생성
     *
     * @return Boolean
     */
    public function create()
    {
        return true;
    }
}