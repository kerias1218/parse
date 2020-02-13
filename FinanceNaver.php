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
    const END_PAGE = 2;
    const PAGE_SLEEP = 1; // second

    private $prevUrl = "";
    private $result = [];
    private $stock = [];
    private $html;

    public function __construct($item, $delimiter) {
        $this->dismantle($item, $delimiter);
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

            sleep(self::PAGE_SLEEP);
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
            "<td.*?>.*?<img.*?alt=\"(?<upordown>.*?)\">.*?(?<diff>.*?)</td>.*?".
            "<td.*?>(?<open>.*?)</td>.*?".
            "<td.*?>(?<high>.*?)</td>.*?".
            "<td.*?>(?<low>.*?)</td>.*?".
            "<td.*?>(?<volume>.*?)</td>".
            "!is";

        preg_match_all($pcre, $this->html, $matchAll);


        $sub = [];
        $sub['date'] = array_map("strip_tags", $matchAll['date']);
        $sub['close'] = array_map("strip_tags", $matchAll['close']);
        $sub['upordown'] = array_map("strip_tags", $matchAll['upordown']);
        $sub['diff'] = array_map("trim", array_map("strip_tags", $matchAll['diff']));
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