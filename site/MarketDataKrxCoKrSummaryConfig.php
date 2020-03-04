<?php
/**
 * Created by PhpStorm.
 * User: naya
 * Date: 2020/02/12
 * Time: 12:58 PM
 */

namespace Naya;

use Carbon\Carbon;

class MarketDataKrxCoKrSummaryConfig
{
    private $data = [];

    public function __construct($code, $isin) {
        $ca =  Carbon::now();

        $this->otpUrl = 'http://marketdata.krx.co.kr/contents/COM/GenerateOTP.jspx?bld=MKD/04/0402/04020100/mkd04020100t2_02&name=tablesubmit';
        $this->otpRefer = 'http://marketdata.krx.co.kr/mdi';
        $this->url = 'http://marketdata.krx.co.kr/contents/MKD/99/MKD99000001.jspx';
        $this->refer = 'http://marketdata.krx.co.kr/mdi';
        $this->pagePath = '/contents/MKD/04/0402/04020100/MKD04020100T2.jsp';
        $this->bldcode = 'MKD/04/0402/04020100/mkd04020100t2_02';

        $this->otpUrl2 = 'http://marketdata.krx.co.kr/contents/COM/GenerateOTP.jspx?bld=MKD/04/0402/04020100/mkd04020100t2_01&name=form';
        $this->otpRefer2 = 'http://marketdata.krx.co.kr/mdi';
        $this->url2 = 'http://marketdata.krx.co.kr/contents/MKD/99/MKD99000001.jspx';
        $this->refer2 = 'http://marketdata.krx.co.kr/mdi';
        $this->pagePath2 = '/contents/MKD/04/0402/04020100/MKD04020100T2.jsp';
        $this->bldcode2 = '';

        $this->agent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.100 Safari/537.36';

        $this->dir = __DIR__.'/../../../../base_data/';
        //$this->saveFileName = $ca->format("Y-m-d");
        $this->saveFileName = $code;
        $this->ext = '.csv';

        $this->code = $code;
        $this->isin = $isin;

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