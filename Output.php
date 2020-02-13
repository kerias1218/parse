<?php
/**
 * Created by PhpStorm.
 * User: naya
 * Date: 2020/02/12
 * Time: 12:50 PM
 */

namespace Naya;

class Output
{
    private $st;

    public function __construct($strategy) {
        $this->st = $strategy;
    }

    public function output() {
        $this->st->save();
    }
}

