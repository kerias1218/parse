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

class KrInvestingCom implements IParse
{
    CONST HOST = 'https://kr.investing.com';

    private $result;

    public function __construct($code) {
        $this->result['code'] = $code;
    }

    public function parsing() {
        // TODO: Implement parsing() method.

        $url = 'https://kr.investing.com/search/?q='.$this->result['code'];
        $page = $this->getPage($url);

        $match = $this->parseUrl($page);

        if(!$match) {
            $this->result['status'] = 0;
            return;
        }

        sleep(2);

        $page2 = $this->getPage(self::HOST.trim($match[1]));

        $match2 = $this->extract($page2);
        if(!$match2) {
            $this->result['status'] = 0;
            return;
        }

        $this->result['status'] = 1;
        $this->result['isin'] = str_replace("&nbsp;","",$match2[1]);

    }

    private function parseUrl($page) {
        preg_match("!<a class=\"js-inner-all-results-quote-item row\" href=\"(.*?)\"!is", $page, $match);
        return $match;
    }

    private function extract($page2) {
        preg_match("!ISIN:.*?<span.*?>(.*?)</span>!is", $page2, $match2);

        return $match2;
    }

    private function getPage($url) {
        $curl = new Curl($url, '');
        return trim($curl->getPage());
    }

    public function getResult() {
        return $this->result;
    }

}