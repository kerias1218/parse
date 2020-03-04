<?php
/**
 * Created by PhpStorm.
 * User: naya
 * Date: 2020/02/12
 * Time: 3:21 PM
 */

namespace Naya;


class Curl
{

    private $host;
    private $refer;
    private $agent;

    public function __construct($host, $refer='')
    {
        $this->host = htmlspecialchars_decode($host);
        $this->refer = htmlspecialchars_decode($refer);
        $this->agent = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:72.0) Gecko/20100101 Firefox/72.0";
        //$this->agent = "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.1; WOW64; Trident/5.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; .NET4.0C; .NET4.0E)";
    }

    public function setReferer($refer) {
        $this->refer = $refer;
    }

    public function setAgent($agent) {
        $this->agent = $agent;
    }




    public function sendHttpRequest($PostData)
    {
        if($this->APIHost == 'https://apac.universal-api.travelport.com/B2BGateway/connect/uAPI/'){
            $header = array(
                "Content-Type: text/xml;charset=UTF-8",
                "Accept: gzip,deflate",
                "Cache-Control: no-cache",
                "Pragma: no-cache",
                "SOAPAction: \"\"",
                "Authorization: Basic ".$PostData['IdPw'],
                "Content-length: ".strlen($PostData['SoapRequest'])
            );

            $connection= curl_init();
            curl_setopt($connection, CURLOPT_URL,$this->APIHost.$PostData['ServiceName']);
            curl_setopt($connection, CURLOPT_CONNECTTIMEOUT, 30);
            curl_setopt($connection, CURLOPT_TIMEOUT, 30);
            curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($connection, CURLOPT_POST, true );
            curl_setopt($connection, CURLOPT_POSTFIELDS, $PostData['SoapRequest']);
            curl_setopt($connection, CURLOPT_HTTPHEADER, $header);
            curl_setopt($connection, CURLOPT_RETURNTRANSFER, true);
        }else{
            $connection = curl_init();
            curl_setopt($connection, CURLOPT_URL, $this->APIHost);
            curl_setopt($connection, CURLOPT_TIMEOUT, 60);
            curl_setopt($connection, CURLOPT_POST, 1);
            curl_setopt($connection, CURLOPT_REFERER, $_SERVER["HTTP_HOST"]);
            curl_setopt($connection, CURLOPT_POSTFIELDS, http_build_query($PostData));
            curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);


        }

        $response = curl_exec($connection);
        curl_close($connection);
        return $response;
    }

    public function getPage($param='') {

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->host.'?'.$param);
        if($this->refer) curl_setopt($curl, CURLOPT_REFERER, $this->refer);
        curl_setopt($curl, CURLOPT_TIMEOUT, 3);
        curl_setopt($curl, CURLOPT_USERAGENT, $this->agent);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        $page = curl_exec($curl);
        curl_close($curl);

        return $page;
    }

    public function postPage(array $post, array $headers=[], $header=0) {

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->host);

        if($header) curl_setopt($curl, CURLOPT_HEADER, true);
        if($headers) curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        if($this->refer) curl_setopt($curl, CURLOPT_REFERER, $this->refer);

        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_USERAGENT, $this->agent);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $page = curl_exec($curl);
        curl_close($curl);

        return $page;

    }


    function cUrlGetData($post_fields = null, $headers = null) {


        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $this->APIHost);
        curl_setopt($ch, CURLOPT_REFERER, $this->refer);
        if ($post_fields && !empty($post_fields)) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        }
        if ($headers && !empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $data = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

        return $data;
    }

    public function sendHttpRequesthotel($PostData)
    {
        $connection = curl_init();
        curl_setopt($connection, CURLOPT_URL, $this->APIHost);
        curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, false); //不验证证书
        curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, false); //不验证证书
        curl_setopt($connection, CURLOPT_HTTPHEADER,array('Accept-Encoding: gzip, deflate'));
        curl_setopt($connection, CURLOPT_ENCODING, 'gzip,deflate');
        curl_setopt($connection, CURLOPT_TIMEOUT, 60);
        curl_setopt($connection, CURLOPT_POST, 1);
        curl_setopt($connection, CURLOPT_REFERER, $_SERVER["HTTP_HOST"]);
        curl_setopt($connection, CURLOPT_POSTFIELDS, $PostData);
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($connection);

        curl_close($connection);
        return $response;
    }

}