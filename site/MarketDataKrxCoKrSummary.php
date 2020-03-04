<?php
/**
 * Created by PhpStorm.
 * User: naya
 * Date: 2020/03/03
 * Time: 12:58 PM
 */

/**
 *
 * http://marketdata.krx.co.kr/contents/MKD/13/1302/13020101/MKD13020101.jsp
 * 주식 > 종목정보 > 개요일반
 * 만들어야함
 *
 *
 */

namespace Naya;

use Carbon\Carbon;

class MarketDataKrxCoKrSummary implements IParse
{
    use CommonTrait;

    private $config;
    private $result = [];
    private $ext;

    public function __construct(MarketDataKrxCoKrSummaryConfig $config) {
        $this->config = $config;
    }

    /**
     * algorithm
    */

    public function parsing() {
        // TODO: Implement parsing() method.
        $this->generateOtp();
        if(!$this->result['opt']) {
            //throw new \Exception("OTP 추출 못했습니다.");
            $this->result['status'] = 0;
            return;
        }
        $this->getInfoData();

        sleep(2);

        $this->generateOtp2();
        if(!$this->result['opt2']) {
            //throw new \Exception("OTP2 추출 못했습니다.");
            $this->result['status'] = 0;
            return;
        }
        $this->getInfoData2();

    }

    private function generateOtp() {
        $curl = new Curl($this->config->otpUrl, $this->config->otpRefer);
        $this->result['opt'] = trim($curl->getPage());
    }

    private function generateOtp2() {
        $curl = new Curl($this->config->otpUrl2, $this->config->otpRefer2);
        $this->result['opt2'] = trim($curl->getPage());
    }

    private function getInfoData() {

        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Upgrade-Insecure-Requests' => '1',
        ];

        $post = [
            'isu_cd' => $this->config->isin,
            'code' => $this->result['opt'],
            'pagePath' => $this->config->pagePath,
            'bldcode' => $this->config->bldcode,
        ];

        $curl = new Curl($this->config->url, $this->config->refer);
        $json = $curl->postPage($post, $headers, false);


        $this->result['ds1Json'] = $json;
        $this->result['data'][] = json_decode($json, TRUE);
    }

    private function getInfoData2() {

        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Upgrade-Insecure-Requests' => '1',
        ];

        $post = [
            'isu_cd' => $this->config->isin,
            'code' => $this->result['opt2'],
            'pagePath' => $this->config->pagePath2,
        ];

        $curl = new Curl($this->config->url2, $this->config->refer2);
        $json = $curl->postPage($post, $headers, false);

        $this->result['status'] = 1;
        $this->result['block1Json'] = $json;
        $this->result['data'][] = json_decode($json, TRUE);
    }

    public function getResult() {
        return $this->result;
    }

}