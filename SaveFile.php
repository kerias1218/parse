<?php
/**
 * Created by PhpStorm.
 * User: naya
 * Date: 2020/02/12
 * Time: 12:58 PM
 */

namespace Naya;

class SaveFile implements IOutput
{
    private $stObj;
    const DIR = "./data/";

    public function __construct($obj) {
        $this->stObj = $obj;
    }

    public function save() {
        // TODO: Implement save() method.

        $data = $this->stObj->getResult();

        foreach($data as $k=>$arr) {
            $fineName = self::DIR."{$k}.data";

            $fo = fopen($fineName, "a");
            
            foreach($arr as $k2=>$arr2) {
                array_map(function($item) use($fo) {
                    $line = implode("^",$item).PHP_EOL;
                    fwrite($fo, $line);
                }, $arr2);

            }
            fclose($fo);
        }

    }
}