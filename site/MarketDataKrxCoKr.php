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

    private $config;
    private $result = [];
    private $csvCount;
    private $ext;

    public function __construct(MarketDataKrxCoKrConfig $config) {
        $this->config = $config;
    }

    /**
     * algorithm
    */

    public function parsing() {
        // TODO: Implement parsing() method.
        if( ! is_dir($this->config->dir) ) throw new \Exception("저장 경로가 없습니다. :\n".$this->config->dir);
        $this->downCsv();
    }


    public function getResult() {
        return $this->result;
    }

    /**
     * CSV download button click
     */

    private function downCsv() {

        for($i=0; $i<5; $i++) {
            $this->consoleStart();
            $this->generateOtp();
            $this->getCsv();

            $this->result['status'] = ($this->checkCsv())?"true":"false";

            if ($this->result['status'] == "true") {
                break;
            }
            else $this->result['status'] = "false";

            sleep(10);
        }

    }

    private function consoleStart() {
        echo $this->config->searchDate." 추출중...".PHP_EOL;
    }


    public function generateOtp() {
        $curl = new Curl($this->config->otpUrl, $this->config->otpRefer);
        $this->result['opt'] = trim($curl->getPage());
    }

    private function getCsv() {
        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Upgrade-Insecure-Requests' => '1',
        ];

        $post = [
            'code' => $this->result['opt'],
        ];

        $curl = new Curl($this->config->downCsvUrl, $this->config->downCsvRefer);
        $this->result['data'] = $curl->postPage($post, $headers, false);

    }

    private function checkCsv() {
        $total =  count( explode("\n", $this->result['data']));
        $this->csvCount = $total;

        $this->makeExt();

        if($total >= 1) return true;
        else return false;
    }

    private function makeExt() {
        if( $this->csvCount == 1 ) $this->ext = '.nodata';
        else $this->ext = $this->config->ext;
    }

    public function saveFile() {
        if( $this->result['status'] == "false" ) throw new \Exception("csv 데이터 가져오지 못했습니다.");

        $dir = $this->config->dir.$this->config->saveFileName.$this->ext;
        $fo = fopen($dir,"w");
        fwrite($fo, $this->result['data']);
        fclose($fo);

        echo $this->config->searchDate." 추출 완료 >> ".$this->config->saveFileName.$this->ext.PHP_EOL;

    }



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

    private function onepageData() {

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