<?php
/**
 * Created by PhpStorm.
 * User: naya
 * Date: 2020/03/03
 * Time: 3:22 PM
 *
 * stock ISIN save
 *
 * https://kr.investing.com/search/?q=570023
 * https://kr.investing.com/search/?q=289080
 */

namespace Naya;

use Naya\IParse;
use Naya\XML2Array;


class DataGoKr implements IParse
{
    use CommonTrait;
    private $config;
    private $xml;
    private $result = [];

    public function __construct($config) {
        $this->config = $config;
    }

    public function parsing() {
        // TODO: Implement parsing() method.

        $curl = new CURL($this->config::URL,'');
        $this->xml =  $curl->getPage($this->config->getParams());

        $this->dismantle();
    }

    private function dismantle() {

        $arr = $this->xmlToArray($this->xml);
        if( $arr['header']['resultCode'] != '00' ) {
            $this->result['status'] = 0;
            $this->result['data'] = '';
            $this->result['oriXml'] = $this->xml;
            $this->result['ori'] = $arr;

        }
        else {
            $this->result['status'] = 1;
            $this->result['data'] = $arr['body']['items']['item'];
            $this->result['oriXml'] = $this->xml;
            $this->result['ori'] = $arr;
        }
    }

    public function getResult() {
        return $this->result;
    }
}