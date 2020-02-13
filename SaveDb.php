<?php
/**
 * Created by PhpStorm.
 * User: naya
 * Date: 2020/02/12
 * Time: 12:58 PM
 */

namespace Naya;

class SaveDb implements IOutput
{
    private $stObj;
    const DIR = "";

    public function __construct($obj) {
        $this->stObj = $obj;
    }

    public function save() {
        // TODO: Implement save() method.

        $data = $this->stObj->getResult();

        foreach($data as $k=>$arr) {

        }

    }
}