<?php
/**
 * Created by PhpStorm.
 * User: naya
 * Date: 2020/02/12
 * Time: 12:58 PM
 */

namespace Naya;

class SampleSite implements IParse
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