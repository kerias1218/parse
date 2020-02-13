<?php
/**
 * Created by PhpStorm.
 * User: naya
 * Date: 2020/02/12
 * Time: 12:50 PM
 */

namespace Naya;


class Parse
{
    private $st;

    public function __construct($strategy) {
        $this->st = $strategy;
    }

    public function crawling() {
        $this->st->parsing();
    }
}

