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

## naver blog
```angular2html
[파싱/parse/파서] 주식 정보 데이터 추

vi stock_parsing.php
vi stock_isin3.php
vi stock_summary.php
```
