#!/usr/bin/php
<?php


/*
namespace Naya;

use Naya\Parse;
use Naya\FinanceNaver;
use Carbon\Carbon;
*/


require __DIR__.'/vendor/autoload.php';

use Naya\FinanceNaver;
use Naya\Parse;
use Naya\SaveFile;
use Naya\Output;


$lists = array_map("trim", file(__DIR__."/stock_code.txt"));

/**
 * 파싱할 사이트 각각 다름 (전략패턴 사용)
 *   - 다른 사이트 파싱할 일이 있으면 FinanceNaver 같은 객체 만들면 됨
 * FinanceNaver 객체 만든후 Parse 에 주입
 *
 * 같은 방법으로 출력 -> 파일로.
 * 만약 DB 로 저장한다라고 한다면 new SaveFile 와 같이 new SaveDB 객체만들어서 $financeNaver 주입하면 됨
 *
*/

foreach ($lists as $item) {

    //echo $item.PHP_EOL;

    $financeNaver = new FinanceNaver($item,",");
    $par = new Parse($financeNaver);
    $par->crawling();

    $out = new Output(new SaveFile($financeNaver));
    $out->output();

    sleep(2);
}
