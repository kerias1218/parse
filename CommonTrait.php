<?php
/**
 * Created by PhpStorm.
 * User: naya
 * Date: 2020/02/12
 * Time: 3:10 PM
 */

namespace Naya;

trait CommonTrait
{

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
}