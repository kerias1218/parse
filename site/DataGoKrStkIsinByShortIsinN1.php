<?php
/**
 * Created by PhpStorm.
 * User: naya
 * Date: 2020/03/03
 * Time: 6:04 PM
 *
 * stock_isin3.php 설정파일
 */



namespace Naya;


class DataGoKrStkIsinByShortIsinN1
{
    CONST URL = 'http://api.seibro.or.kr/openapi/service/StockSvc/getStkIsinByShortIsinN1';
    CONST KEY = 'wMsqd7ZuygHoZCf5QwIKqtZLnSdV%2BJD61jxLWs%2Bq3FBSNzkGdgPJ4xcx8ltizTtYI2NjfFJRVvF5nUSRDcaEBQ%3D%3D';
    CONST NUM_OF_ROWS = 1;
    CONST PAGE_NO = 1;

    private $shortIsin;
    private $params;

    public function __construct($code) {
        $this->shortIsin = $code;
        $this->makeParams();
    }

    public function getShortIsin() {
        return $this->shortIsin;
    }

    private function makeParams() {
        $param = [];
        $param['serviceKey'] = self::KEY;
        $param['pageNo'] = self::PAGE_NO;
        $param['numOfRows'] = self::NUM_OF_ROWS;
        $param['shortIsin'] = $this->shortIsin;

        $tmp = '';
        foreach($param as $k=>$val) {
            $tmp .= "$k=$val&";
        }
        $this->params = $tmp;
    }

    public function getParams() {
        return $this->params;
    }

}