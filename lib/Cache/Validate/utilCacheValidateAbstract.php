<?php
/**
 * 캐쉬 종류별 검증 추상화 클래스(호출안함/참고)
 *
 * @author     jylee3@simplexi.com
 */
abstract class utilCacheValidateAbstract
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