<?php
/**
 * Created by PhpStorm.
 * User: naya
 * Date: 2020/02/12
 * Time: 12:58 PM
 */

namespace Naya;

class FinanceNaver implements IParse
{
    use CommonTrait;

    const URL = "https://finance.naver.com/item/sise_day.nhn?code={code}&page=";
    const START_PAGE = 1;
    const END_PAGE = 3;
    const PAGE_SLEEP = 300000; // 0.5초

    private $prevUrl = "";
    private $result = [];
    private $stock = [];
    private $html;

    public function __construct($item, $delimiter) {
        $this->dismantle($item, $delimiter);
        // 파일 읽고 배열에 저장
    }

    private function dismantle($item, $delimiter) {
        $tmpArr = explode($delimiter, $item);
        $this->stock['code'] = $tmpArr[0];
        $this->stock['name'] = $tmpArr[1];
    }

    public function parsing() {
        // TODO: Implement parsing() method.

        $data = [];
        for ($i=self::START_PAGE; $i<=self::END_PAGE; $i++) {

            $url = $this->makeUrl($i);
            $refer = $this->makeReferer($url, $i);
            $this->getHtml($url, $refer);
            $sub = $this->htmlAnalyze();

            $data[$this->stock['code']][] = array_map(null, $sub['date'], $sub['close'], $sub['upordown'], $sub['diff'], $sub['open'], $sub['high'],$sub['low'],$sub['volume']);
            $this->log($i);

            usleep(self::PAGE_SLEEP);
        }

        $this->result = $data;
    }

    private function log($i) {
        echo $this->stock['code']." ".$this->stock['name']."\t".$i." page parsing....".PHP_EOL;
    }

    private function makeUrl($i) {
        $url = self::URL.$i;
        $url = str_replace("{code}",$this->stock['code'], $url);
        return $url;
    }

    private function makeReferer($url, $i) {
        return ($i==1)?"":$this->makeUrl(--$i);
    }

    private function getHtml($url, $refer) {
        $curl = new Curl($url, $refer);
        $this->html = iconv("cp949", "utf-8", $curl->getPage());
    }

    private function htmlAnalyze() {
        // 날짜 종가 전일비 시가 고가 저가 거래량
        $pcre = "!<tr onmouseover=.*?>.*?".
            "<td.*?>(?<date>.*?)</td>.*?".
            "<td.*?>(?<close>.*?)</td>.*?".
            "<td.*?>(?<upordown_diff>.*?)</td>.*?".
            "<td.*?>(?<open>.*?)</td>.*?".
            "<td.*?>(?<high>.*?)</td>.*?".
            "<td.*?>(?<low>.*?)</td>.*?".
            "<td.*?>(?<volume>.*?)</td>".
            "!is";

        preg_match_all($pcre, $this->html, $matchAll);

        $aa = array_map(function($item) {
            $r = [];
            if(preg_match("!<img.*?alt=\"(?<upordown>.*?)\">!is", $item, $match)) {
                preg_match("!<img.*?alt=\"(?<upordown>.*?)\">(?<diff>.*?)</span>!is", $item, $match);

                $r['upordown'] = $match['upordown'];
                $r['diff'] = trim(strip_tags($match['diff']));
            }
            else {
                $r['upordown'] = "-";
                $r['diff'] = "0";
            }
            return $r;
        }, $matchAll['upordown_diff']);

        $sub = [];
        $sub['upordown'] = [];
        $sub['diff'] = [];
        foreach($aa as $k=>$arr) {

            array_push($sub['upordown'], $arr['upordown']);
            array_push($sub['diff'], $arr['diff']);

        }


        $sub['date'] = array_map("strip_tags", $matchAll['date']);
        $sub['close'] = array_map("strip_tags", $matchAll['close']);
        $sub['open'] = array_map("strip_tags", $matchAll['open']);
        $sub['high'] = array_map("strip_tags", $matchAll['high']);
        $sub['low'] = array_map("strip_tags", $matchAll['low']);
        $sub['volume'] = array_map("strip_tags", $matchAll['volume']);

        return $sub;
    }

    public function getResult() {

        return $this->result;
    }

}