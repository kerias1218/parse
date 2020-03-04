<?php
/**
 * Created by PhpStorm.
 * User: naya
 * Date: 2020/02/12
 * Time: 12:58 PM
 */

namespace Naya;

class SaveFile
{
    private $stObj;
    const DIR = "./base_data/";

    public function __construct($obj) {
        $this->stObj = $obj;
    }

    public function save() {
        // TODO: Implement save() method.

        $data = $this->stObj->getResult();

        $data['ds1Json'];
        $data['block1Json'];

        $separator = "^|^";

        $line = $data['code'].$separator.$data['ds1Json'].$separator.$data['block1Json'].PHP_EOL;
        $fo = fopen(self::DIR."stock_summary_info.txt", "a");
        fwrite($fo, $line);
        fclose($fo);

    }
}