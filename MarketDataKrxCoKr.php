<?php
/**
 * Created by PhpStorm.
 * User: naya
 * Date: 2020/02/12
 * Time: 12:58 PM
 */

/**
 *
 * http://marketdata.krx.co.kr/contents/MKD/13/1302/13020101/MKD13020101.jsp
*/

namespace Naya;

use Carbon\Carbon;


class MarketDataKrxCoKr implements IParse
{
    use CommonTrait;

    const PATH_CSV = __DIR__."/../../../data2/";
    const EXT = ".csv";
    private $result = [];

    public function __construct() {

    }

    public function parsing() {
        $this->downCsv();
    }

    public function getResult() {
        return $this->result;
    }

    private function downCsv() {

        /**
         * CSV download click
        */

        for($i=0; $i<10; $i++) {

            $this->generateOtp();
            $this->getCsv();

            if ($this->checkCsv()) {
                $this->result['status'] = "true";
                break;
            }
            else {
                $this->result['status'] = "false";
            }

            sleep(10);
        }
    }

    private function onepageData() {

        /**
         *
         * 페이지 네이션 9페이지 까지 가져오기
         *
         * 1. http://marketdata.krx.co.kr/contents/COM/Nowtime.jspx?_=1581903657758 (GET)
         *
         * 2. get OPT (GET)
         * http://marketdata.krx.co.kr/contents/COM/GenerateOTP.jspx?bld=MKD/13/1302/13020101/mkd13020101&name=form&_=1581903657759
         *
         * 3. http://marketdata.krx.co.kr/contents/MKD/99/MKD99000001.jspx (POST)
         *
         */

        $this->nowTime();
        $this->getOpt();
        $this->getStockData();
    }

    private function nowTime() {
        $url = "http://marketdata.krx.co.kr/contents/COM/Nowtime.jspx?_=".time();
        $refer = "http://marketdata.krx.co.kr/mdi";
        $curl = new Curl($url, $refer);
        $this->result['nowTime'] = json_decode($curl->getPage(), true);

    }

    private function getOpt() {
        $bld = "MKD/13/1302/13020101/mkd13020101";
        $url = "http://marketdata.krx.co.kr/contents/COM/GenerateOTP.jspx?bld=".urlencode($bld)."&name=form&_=".time();
        $refer = "http://marketdata.krx.co.kr/mdi";
        $curl = new Curl($url, $refer);
        $this->result['opt'] = trim($curl->getPage());

    }

    private function generateOtp() {
        $url = "http://marketdata.krx.co.kr/contents/COM/GenerateOTP.jspx?name=fileDown&filetype=csv&url=MKD/13/1302/13020101/mkd13020101&market_gubun=ALL&sect_tp_cd=ALL&schdate=20200219&pagePath=%2Fcontents%2FMKD%2F13%2F1302%2F13020101%2FMKD13020101.jsp";
        $refer = "http://marketdata.krx.co.kr/contents/MKD/13/1302/13020101/MKD13020101.jsp";
        $curl = new Curl($url, $refer);

        $this->result['opt'] = trim($curl->getPage());
    }

    private function getCsv() {
        $url = "http://file.krx.co.kr/download.jspx";
        $refer = "http://marketdata.krx.co.kr/contents/MKD/13/1302/13020101/MKD13020101.jsp";
        $post = [
            'code' => $this->result['opt']
        ];

        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Upgrade-Insecure-Requests' => '1',
        ];

        $curl = new Curl($url, $refer);
        $this->result['data'] = $curl->postPage($post, $headers, false);

    }

    public function saveFile() {
        if( $this->result['status'] == "false" ) throw new \Exception("csv 데이터 가져오지 못했습니다.");

        $dir = self::PATH_CSV.$this->getDateYmd().self::EXT;
        $fo = fopen($dir,"w");
        fwrite($fo, $this->result['data']);
        fclose($fo);
    }

    private function checkCsv() {
        $total =  count( explode("\n", $this->result['data']));
        if($total > 1000) return true;
        else return false;
    }


    /**
     * page 별 data 가져오기
    */
    private function getStockData() {

        $tmpArr = [];
        for($page=1; $page<=2; $page++) {

            $url = "http://marketdata.krx.co.kr/contents/MKD/99/MKD99000001.jspx";
            $refer = "http://marketdata.krx.co.kr/mdi";
            $post = [
                'market_gubun' => 'ALL',
                'sect_tp_cd' => 'ALL',
                'schdate' => '20200205',
                'pagePath' => urlencode('/contents/MKD/13/1302/13020101/MKD13020101.jsp'),
                'code' => $this->result['opt'],
                'curPage' => $page,
            ];

            $headers = [];

            $curl = new Curl($url, $refer);
            $tmp = json_decode($curl->postPage($post, $headers, false), true);
            $tmpArr['page_'.$page] = $tmp['시가총액 상하위'];

            /**
             *
             * 파일열고 저장 루틴
             *
             *
             **/

            sleep(3);
        }

        $this->result['data'] = $tmpArr;

        echo '<pre>';
        print_r($this->result);
        exit;
    }

    private function dismantle($item, $delimiter) {
        $tmpArr = explode($delimiter, $item);
        $this->stock['code'] = $tmpArr[0];
        $this->stock['name'] = $tmpArr[1];
    }

}