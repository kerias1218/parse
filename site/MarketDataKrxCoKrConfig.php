<?php
/**
 * Created by PhpStorm.
 * User: naya
 * Date: 2020/02/12
 * Time: 12:58 PM
 */

namespace Naya;

use Carbon\Carbon;

class MarketDataKrxCoKrConfig
{
    private $data = [];
    private $searchData;

    public function __construct($searchDate) {
        $ca =  Carbon::now();
        $this->otpUrl = 'http://marketdata.krx.co.kr/contents/COM/GenerateOTP.jspx?name=fileDown&filetype=csv&'.
            'url=MKD/13/1302/13020101/mkd13020101&market_gubun=ALL&sect_tp_cd=ALL&schdate='.$searchDate.
            '&pagePath=%2Fcontents%2FMKD%2F13%2F1302%2F13020101%2FMKD13020101.jsp';
        $this->otpRefer = 'http://marketdata.krx.co.kr/contents/MKD/13/1302/13020101/MKD13020101.jsp';
        $this->downCsvUrl = 'http://file.krx.co.kr/download.jspx';
        $this->downCsvRefer = 'http://marketdata.krx.co.kr/contents/MKD/13/1302/13020101/MKD13020101.jsp';

        $this->dir = __DIR__.'/../../../../base_data/';
        //$this->saveFileName = $ca->format("Y-m-d");
        $this->saveFileName = $searchDate;
        $this->ext = '.csv';

        $this->searchDate = $searchDate;
    }

    public function __set($key, $value) {
        $this->data[$key] = $value;
    }

    public function __get($key) {
        if(array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }
    }

    public function getParam() {
        return $this->data;
    }

}