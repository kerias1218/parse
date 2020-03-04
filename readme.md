## Html parse 

![스크린샷 2020-02-13 오후 3 40 58](https://user-images.githubusercontent.com/17056359/74408320-74732780-4e77-11ea-89f8-4717ef3ebb5f.png)

전략객체를 사용하여 finance.naver.com 외에 다른 html 파싱해야 될경우
객체만 교체하면 됩니다.

또한 출력시 기본 파일 출력 / DB insert 시 SaveDB 객체 만들어서
교체 해주면 됩니다.

## Target Site
``` https://finance.naver.com/item/sise_day.nhn?code=003070&page=1```

## Installation

##### With Composer

```composer require naya/php-parse```



```vi stock_parsing.php```
```
#!/usr/bin/php
<?php

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
```


## class 파일 찾지 못할때

```composer dumpautoload -o```

## packagist

```https://packagist.org/packages/naya/php-parse```

## git 이용법

```
git add .
git commit -m "message"
git tag v1.0.6
git push origin v1.0.6
```

## vi stock_parsing.php
```
#!/usr/bin/php
<?php

require __DIR__.'/vendor/autoload.php';

use Carbon\Carbon;
use Naya\FinanceNaver;
use Naya\Parse;
use Naya\SaveFile;
use Naya\Output;
use Naya\MarketDataKrxCoKr;
use Naya\MarketDataKrxCoKrConfig;

if(count($argv) <2) {
    echo "usage: php stock_parsing.php [date]".PHP_EOL;
    echo "".PHP_EOL;
    echo "date: 20200302 format".PHP_EOL;
    echo "ex) php stock_parsing.php 2020228".PHP_EOL;
    exit;
}

if( !preg_match("![0-9]{8}!is", $argv[1]) ) throw new \Exception("20200228 형식으로 입력해주세요.");

$searchDate = trim($argv[1]);   // "20200228"; Ymd format

$ca =  Carbon::now();
$dt = \Carbon\Carbon::createFromFormat('Ymd', $searchDate);

if( $dt->dayOfWeek == \Carbon\Carbon::SATURDAY || $dt->dayOfWeek == \Carbon\Carbon::SUNDAY ) {
    echo $searchDate.' 날짜는 토요일 또는 일요일 입니다. 계속 하시겠습니까? (y/n)'.PHP_EOL;
    $input = fgetc(STDIN);
    if($input == 'n') exit;
}

$param = new MarketDataKrxCoKrConfig($searchDate);

$krx = new MarketDataKrxCoKr($param);
$par = new Parse($krx);
$par->crawling();
$krx->saveFile();

```

##vi stock_summary.php
```
#!/usr/bin/php
<?php

require __DIR__.'/vendor/autoload.php';

use Naya\Parse;
use Naya\MarketDataKrxCoKrSummary;
use Naya\MarketDataKrxCoKrSummaryConfig;
use Naya\SaveDb;
use Doctrine\DBAL\DriverManager;

$params = array(
    'dbname' => '',
    'user' => '',
    'password' => '',
    'host' => 'localhost',
    'driver' => 'pdo_mysql',
);

$fp = file("./data/isin.txt");  // 002680,한탑,KR7002680007
for($i=0; $i<count($fp); $i++) {
    $val = trim($fp[$i]);
    $arr = explode(",", $val);

    $code = trim($arr[0]);
    $title = trim($arr[1]);
    $isin = trim($arr[2]);

    $summary = new MarketDataKrxCoKrSummary(new MarketDataKrxCoKrSummaryConfig($code, $isin));
    $par = new Parse($summary);
    $par->crawling();

    $result = $summary->getResult();

    if($result['status']) {
        $db = new SaveDb($summary, DriverManager::getConnection($params));
        $db->save();
    }
    else {
        $i--;
    }

    echo "$i => $code $title".PHP_EOL;
    sleep(2);
}
```