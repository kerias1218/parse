<?php
/**
 * Created by PhpStorm.
 * User: naya
 * Date: 2020/02/12
 * Time: 3:10 PM
 */

namespace Naya;

use Carbon\Carbon;

trait CommonTrait
{

    public function getDateYmd() {
        $ca =  Carbon::now();
        return $ca->format("Y-m-d");
    }

    public function now($format='Y-m-d') {
        $ca = Carbon::now();
        $now = $ca->format($format);
    }

    public function test() {
        echo "I am CommonTrait  test() method";
    }

    public function array_change_key_case_recursive($arr) {

        return array_map(function($item){
            if(is_array($item))
                $item = $this->array_change_key_case_recursive($item);
            return $item;
        },array_change_key_case($arr));

    }

    public function xmlToArray($xmlData) {
        $xml = simplexml_load_string($xmlData);
        $json = json_encode($xml);
        return json_decode($json, TRUE);

    }
}