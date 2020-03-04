<?php

namespace Naya;

	/**
	 * APICall Class
	 *
	 */
	class APICall
	{
		private $APIHost;

		public function __construct($APIHost)
		{
			$this->APIHost   = $APIHost;
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

        public function sendRuleHttpRequest($PostData) {

            $headers = [];
            $headers[] = "X-TourLine-Client-Id: ".$PostData['client_id'];
            $headers[] = "X-TourLine-Client-Secret: ".$PostData['client_secret'];

            unset($PostData['client_id']);
            unset($PostData['client_secret']);

            $connection= curl_init();
            curl_setopt($connection, CURLOPT_URL,$this->APIHost);
            curl_setopt($connection, CURLOPT_CONNECTTIMEOUT, 30);
            curl_setopt($connection, CURLOPT_TIMEOUT, 30);
            curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($connection, CURLOPT_POST, true );
            curl_setopt($connection, CURLOPT_POSTFIELDS, http_build_query($PostData));
            curl_setopt($connection, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($connection, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($connection);

            curl_close($connection);
            return $response;
        }

	}

    class APIMultiCall
    {
        private $APIHost;

        public function __construct($APIHostArr)
        {
            $this->APIHost   = $APIHostArr;
        }

        function sendHttpMultiRequest($PostDataArr){


            $connection = curl_multi_init();
            $curl_array = array();
            foreach($this->APIHost as $i => $url)
            {


                //curl_setopt($curl_array[$i], CURLOPT_POSTFIELDS, $PostDataArr[$i]);
                if($url == 'https://apac.universal-api.travelport.com/B2BGateway/connect/uAPI/'){

                    $header = array(
                     "Content-Type: text/xml;charset=UTF-8",
                     "Accept: gzip,deflate",
                     "Cache-Control: no-cache",
                     "Pragma: no-cache",
                     "SOAPAction: \"\"",
                     "Authorization: Basic ".$PostDataArr[$i]['IdPw'],
                     "Content-length: ".strlen($PostDataArr[$i]['SoapRequest'])
                    );

                    $curl_array[$i] = curl_init($url.$PostDataArr[$i]['ServiceName']);
                    curl_setopt($curl_array[$i], CURLOPT_CONNECTTIMEOUT, 30);
                    curl_setopt($curl_array[$i], CURLOPT_TIMEOUT, 60);
                    curl_setopt($curl_array[$i], CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($curl_array[$i], CURLOPT_SSL_VERIFYHOST, false);
                    curl_setopt($curl_array[$i], CURLOPT_POST, true );
                    curl_setopt($curl_array[$i], CURLOPT_POSTFIELDS, $PostDataArr[$i]['SoapRequest']);
                    curl_setopt($curl_array[$i], CURLOPT_HTTPHEADER, $header);

                }
                else if($url == 'http://tourline.worldspan.kr/ServiceTourline.asmx/LFSThread') {
                    $curl_array[$i] = curl_init($url);
                    curl_setopt($curl_array[$i], CURLOPT_CONNECTTIMEOUT, 30);
                    curl_setopt($curl_array[$i], CURLOPT_TIMEOUT, 60);
                    curl_setopt($curl_array[$i], CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($curl_array[$i], CURLOPT_SSL_VERIFYHOST, false);
                    curl_setopt($curl_array[$i], CURLOPT_POST, true );
                    curl_setopt($curl_array[$i], CURLOPT_POSTFIELDS, http_build_query($PostDataArr[$i]));
                    curl_setopt($curl_array[$i], CURLOPT_RETURNTRANSFER, true);
                    //curl_setopt($curl_array[$i], CURLOPT_VERBOSE, 1);
                    //curl_setopt($curl_array[$i], CURLOPT_HEADER, 1);

                }
                else{
                    $curl_array[$i] = curl_init($url);
                    curl_setopt($curl_array[$i], CURLOPT_TIMEOUT, 60);
                    curl_setopt($curl_array[$i], CURLOPT_ENCODING, 'gzip,deflate');
                    curl_setopt($curl_array[$i], CURLOPT_POST, 1);
                    curl_setopt($curl_array[$i], CURLOPT_REFERER, $_SERVER["HTTP_HOST"]);
                    curl_setopt($curl_array[$i], CURLOPT_POSTFIELDS, http_build_query($PostDataArr[$i]));
                    curl_setopt($curl_array[$i], CURLOPT_HTTPHEADER,array('Accept-Encoding: gzip, deflate'));
                }

                curl_setopt($curl_array[$i], CURLOPT_RETURNTRANSFER, true);
                curl_multi_add_handle($connection, $curl_array[$i]);
            }
            $running = NULL;
            do {
                usleep(10000);
                $status = curl_multi_exec($connection,$running);

            } while($running > 0);

            $response = array();
            foreach($this->APIHost as $i => $url)
            {
                $response[$i] = curl_multi_getcontent($curl_array[$i]);

            }

            foreach($this->APIHost as $i => $url){
                curl_multi_remove_handle($connection, $curl_array[$i]);
            }


            curl_multi_close($connection);

            return $response;
        }
    }

	class APIHotelCall
	{
		private $APIHost;

		public function __construct($APIHost)
		{
			$this->APIHost   = $APIHost;
		}

		public function sendHttpRequest($PostData)
		{
			$connection = curl_init();
			curl_setopt($connection, CURLOPT_URL, $this->APIHost);
			curl_setopt($connection, CURLOPT_TIMEOUT, 60);
			curl_setopt($connection, CURLOPT_POST, 1);
			curl_setopt($connection, CURLOPT_REFERER, $_SERVER["HTTP_HOST"]);
			curl_setopt($connection, CURLOPT_POSTFIELDS, http_build_query($PostData));
			curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
			$response = curl_exec($connection);
			curl_close($connection);
			return $response;
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


	/**
	 * XML2Array Class
	 *
	 */
	class XML2Array
	{
		private $multiArrayValue = array();

		public function __construct(){}

		public function multiArrayKeyValue($needle, $haystack) {
			$this->multiArrayValue = array();
			if(is_array($needle) && count($needle) > 0){
				foreach($needle as $k => $v){
					$this->multiArrayValue[$v] = array();
					$this->_multiArrayKeyValue($v,$haystack);
				}
			}else{
				$this->_multiArrayKeyValue($needle,$haystack);
			}
			return $this->multiArrayValue;
		}

		private function _multiArrayKeyValue($needle, $haystack) {
			if(count($haystack) > 0){
				foreach ($haystack as $key=>$value) {
					if ($needle===$key) {
						if(isset($this->multiArrayValue[$needle])){
							array_push($this->multiArrayValue[$needle],$value);
						}else{
							array_push($this->multiArrayValue,$value);
						}
					}
					if (is_array($value)) {
						if($this->_multiArrayKeyValue($needle, $value)) {
							return $this->_multiArrayKeyValue($needle, $value);
						}
					}
				}
			}
			return false;
		}

		public function multiArrayKeyExists($needle, $haystack) {
			if(count($haystack) > 0){
				foreach ($haystack as $key=>$value) {
					if ($needle===$key) {
						return $value;
					}
					if (is_array($value)) {
						if($this->multiArrayKeyExists($needle, $value)) {
							return $this->multiArrayKeyExists($needle, $value);
						}
					}
				}
			}
			return false;
		}

		public function xml2Array($contents, $get_attributes = 1, $priority = 'tag')
        {
            if (!function_exists('xml_parser_create'))
            {
                return array ();
            }
            $parser = xml_parser_create('');
            xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8");
            xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
            xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
            xml_parse_into_struct($parser, trim($contents), $xml_values);
            xml_parser_free($parser);
            if (!$xml_values) return;
            $xml_array = array ();
            $parents = array ();
            $opened_tags = array ();
            $arr = array ();
            $current = & $xml_array;
            $repeated_tag_index = array ();
            foreach ($xml_values as $data)
            {
                unset ($attributes, $value);
                extract($data);
                $result = array ();
                $attributes_data = array ();
                if (isset ($value))
                {
                    if ($priority == 'tag')
                        $result = $value;
                    else
                        $result['value'] = $value;
                }
                if (isset ($attributes) and $get_attributes)
                {
                    foreach ($attributes as $attr => $val)
                    {
                        if ($priority == 'tag')
                            $attributes_data[$attr] = $val;
                        else
                            $result['attr'][$attr] = $val;
                    }
                }
                if ($type == "open")
                {
                    $parent[$level -1] = & $current;
                    if (!is_array($current) or (!in_array($tag, array_keys($current))))
                    {
                        $current[$tag] = (is_array($result)) ? "" : $result;
                        if ($attributes_data)
                            $current[$tag . '_attr'] = $attributes_data;
                        $repeated_tag_index[$tag . '_' . $level] = 1;
                        $current = & $current[$tag];
                    }
                    else
                    {
                        if (isset ($current[$tag][0]))
                        {
							if ($attributes_data){
							$current[$tag][($repeated_tag_index[$tag . '_' . $level]) . '_attr'] = $attributes_data;
							}

                            $current[$tag][$repeated_tag_index[$tag . '_' . $level]] = (is_array($result)) ? "" : $result;
                            $repeated_tag_index[$tag . '_' . $level]++;
                        }
                        else
                        {
                            $current[$tag] = array (
                                $current[$tag],
                                ((is_array($result)) ? "" : $result)
                            );
                            $repeated_tag_index[$tag . '_' . $level] = 2;
                            if (isset ($current[$tag . '_attr']))
                            {
                                $current[$tag]['0_attr'] = $current[$tag . '_attr'];
                                unset ($current[$tag . '_attr']);
								if ($attributes_data){
									$current[$tag]['1_attr'] = $attributes_data;
								}
                            }
                        }
                        $last_item_index = $repeated_tag_index[$tag . '_' . $level] - 1;
                        $current = & $current[$tag][$last_item_index];
                    }
                }
                elseif ($type == "complete")
                {
                    if (!isset ($current[$tag]))
                    {
                        $current[$tag] = (is_array($result)) ? "" : $result;
                        $repeated_tag_index[$tag . '_' . $level] = 1;
                        if ($priority == 'tag' and $attributes_data){
                            $current[$tag . '_attr'] = $attributes_data;
                        }
                    }
                    else
                    {
                        if (isset ($current[$tag][0]) and is_array($current[$tag]))
                        {
                            $current[$tag][$repeated_tag_index[$tag . '_' . $level]] = (is_array($result)) ? "" : $result;
                            if ($priority == 'tag' and $get_attributes and $attributes_data)
                            {
                                $current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
                            }
                            $repeated_tag_index[$tag . '_' . $level]++;
                        }
                        else
                        {
                            $current[$tag] = array (
                                $current[$tag],
                                ((is_array($result)) ? "" : $result)
                            );
                            $repeated_tag_index[$tag . '_' . $level] = 1;
                            if ($priority == 'tag' and $get_attributes)
                            {
                                if (isset ($current[$tag . '_attr']))
                                {
                                    $current[$tag]['0_attr'] = $current[$tag . '_attr'];
                                    unset ($current[$tag . '_attr']);
                                }
                                if ($attributes_data)
                                {
                                    $current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
                                }
                            }
                            $repeated_tag_index[$tag . '_' . $level]++; //0 and 1 index is already taken
                        }
                    }
                }
                elseif ($type == 'close')
                {
                    $current = & $parent[$level -1];
                }
            }
            return ($xml_array);
		}

        public function Newxml2Array($contents, $get_attributes = 1, $priority = 'tag')
		{
			if (!function_exists('xml_parser_create'))
			{
				return array ();
			}
			$parser = xml_parser_create('');
			xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8");
			xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
			xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
			xml_parse_into_struct($parser, trim($contents), $xml_values);
			xml_parser_free($parser);
            //print_r($xml_values);exit;
            $temp = array();

            foreach($xml_values as $val){
                if($val['type'] == 'open' OR $val['type'] == 'complete'){
                    $key = 'key'.$val['level'];

                    if($val['level'] == 1){
                        $$key = $val['tag'];
                        $temp[$$key] = array();
                        if(!empty($val['attributes'])){
                            array_push($temp[$$key] , $val['attributes']);
                        }
                    }else if($val['level'] == 2){
                        $$key = $val['tag'];

                        if(empty($temp[$key1][$$key])) $temp[$key1][$$key] = array();
                        if(!empty($val['attributes'])){
                            array_push($temp[$key1][$$key],$val['attributes']);
                        }
                    }else if($val['level'] == 3){
                        $$key = $val['tag'];
                        if(empty($temp[$key1][$key2][$$key])) $temp[$key1][$key2][$$key] = array();
                        if(!empty($val['attributes'])){
                            array_push($temp[$key1][$key2][$$key],$val['attributes']);
                        }
                    }else if($val['level'] == 4){
                        $$key = $val['tag'];
                        if(empty($temp[$key1][$key2][$key3][$$key])) $temp[$key1][$key2][$key3][$$key] = array();
                        if(!empty($val['attributes'])){
                            array_push($temp[$key1][$key2][$key3][$$key],$val['attributes']);
                        }
                    }else if($val['level'] == 5){
                        $$key = $val['tag'];

                        if(empty($temp[$key1][$key2][$key3][$key4][$$key])) $temp[$key1][$key2][$key3][$key4][$$key] = array();
                        if(!empty($val['attributes'])){
                            array_push($temp[$key1][$key2][$key3][$key4][$$key],$val['attributes']);
                        }
                    }else if($val['level'] == 6){
                        $$key = $val['tag'];
                        if(empty($temp[$key1][$key2][$key3][$key4][$key5][$$key])) $temp[$key1][$key2][$key3][$key4][$key5][$$key] = array();
                        if(!empty($val['attributes'])){
                            array_push($temp[$key1][$key2][$key3][$key4][$key5][$$key],$val['attributes']);
                        }
                    }else if($val['level'] == 7){
                        $$key = $val['tag'];
                        if(empty($temp[$key1][$key2][$key3][$key4][$key5][$key6][$$key])) $temp[$key1][$key2][$key3][$key4][$key5][$key6][$$key] = array();
                        if(!empty($val['attributes'])){
                            array_push($temp[$key1][$key2][$key3][$key4][$key5][$key6][$$key],$val['attributes']);
                        }
                    }else if($val['level'] == 8){
                        $$key = $val['tag'];
                        if(empty($temp[$key1][$key2][$key3][$key4][$key5][$key6][$key7][$$key])) $temp[$key1][$key2][$key3][$key4][$key5][$key6][$key7][$$key] = array();
                        if(!empty($val['attributes'])){
                            array_push($temp[$key1][$key2][$key3][$key4][$key5][$key6][$key7][$$key],$val['attributes']);
                        }
                    }
                }
            }

            return $temp;

        }
	}

    class Array2xml
    {
        var $xml;
        function createXML($array,$encoding='utf-8') {
            $this->xml='<?xml version="1.0" encoding="'.$encoding.'"?>';
            $this->xml.=$this->_array2xml($array);
        }
        function getXml() {
            return $this->xml;
        }
        function _array2xml($array)
        {
            $xml='';
            foreach($array as $key=>$val){
                if(is_numeric($key)){
                    //$key="item id=\"$key\"";
                }else{
                    //去掉空格，只取空格之前文字为key
                    list($key,)=explode(' ',$key);
                }
                $xml.="<$key>";
                $xml.=is_array($val)?$this->_array2xml($val):$val;
                //去掉空格，只取空格之前文字为key
                list($key,)=explode(' ',$key);
                $xml.="</$key>";
            }
            return $xml;
        }
    }

/*
 * This class is written based entirely on the work found below
 * www.techbytes.co.in/blogs/2006/01/15/consuming-rss-with-php-the-simple-way/
 * All credit should be given to the original author
 *
 * Example:

	$this->load->library('rssparser');
	$this->rssparser->set_feed_url('http://example.com/feed');
	$this->rssparser->set_cache_life(30);
	$rss = $this->rssparser->getFeed(6);  // Get six items from the feed

	// Using a callback function to parse addictional XML fields

	$this->load->library('rssparser', array($this, 'parseFile')); // parseFile method of current class

	function parseFile($data, $item)
	{
		$data['summary'] = (string)$item->summary;
		return $data;
	}
*/

class RSSParser {

	public $feed_uri            = NULL;                     // Feed URI
	public $data                = FALSE;                    // Associative array containing all the feed items
	public $channel_data        = array();                  // Store RSS Channel Data in an array
	public $feed_unavailable    = NULL;                     // Boolean variable which indicates whether an RSS feed was unavailable
	public $cache_life          = 0;                        // Cache lifetime
	public $cache_dir           = NULL;                     // Cache directory
	public $write_cache_flag    = FALSE;                    // Flag to write to cache
	public $callback            = FALSE;                    // Callback to read custom data


	function __construct($callback = FALSE)
	{
		$this->cache_dir = $_SERVER["DOCUMENT_ROOT"].'/cache/';
		if ($callback)
		{
			$this->callback = $callback;
		}
	}

	// --------------------------------------------------------------------

	function parse()
	{
		// Are we caching?
		if ($this->cache_life != 0)
		{
			$filename = $this->cache_dir.'rss_Parse_'.md5($this->feed_uri);

			// Is there a cache file ?
			if (file_exists($filename))
			{
				// Has it expired?
				$timedif = (time() - filemtime($filename));

				if ($timedif < ( $this->cache_life * 60))
				{
					$rawFeed = file_get_contents($filename);
				}
				else
				{
					// So raise the falg
					$this->write_cache_flag = true;
				}
			}
			else
			{
				// Raise the flag to write the cache
				$this->write_cache_flag = true;
			}
		}

		// Reset
		$this->data = array();
		$this->channel_data = array();

		// Parse the document
		if (!isset($rawFeed))
		{
			$rawFeed = file_get_contents($this->feed_uri);
		}

		$xml = new SimpleXmlElement($rawFeed);

		if ($xml->channel)
		{
			// Assign the channel data
			$this->channel_data['title'] = $xml->channel->title;
			$this->channel_data['description'] = $xml->channel->description;

			// Build the item array
			foreach ($xml->channel->item as $item)
			{
				$data = array();
				$data['title'] = (string)$item->title;
				$data['description'] = (string)$item->description;
				$data['pubDate'] = (string)$item->pubDate;
				$data['link'] = (string)$item->link;
				$dc = $item->children('http://purl.org/dc/elements/1.1/');
				$data['author'] = (string)$dc->creator;

				if ($this->callback)
				{
					$data = call_user_func($this->callback, $data, $item);
				}

				$this->data[] = $data;
			}
		}
		else
		{
			// Assign the channel data
			$this->channel_data['title'] = $xml->title;
			$this->channel_data['description'] = $xml->subtitle;

			// Build the item array
			foreach ($xml->entry as $item)
			{
				$data = array();
				$data['id'] = (string)$item->id;
				$data['title'] = (string)$item->title;
				$data['description'] = (string)$item->content;
				$data['pubDate'] = (string)$item->published;
				$data['link'] = (string)$item->link['href'];
				$dc = $item->children('http://purl.org/dc/elements/1.1/');
				$data['author'] = (string)$dc->creator;

				if ($this->callback)
				{
					$data = call_user_func($this->callback, $data, $item);
				}

				$this->data[] = $data;
			}
		}

		// Do we need to write the cache file?
		if ($this->write_cache_flag)
		{
			if (!$fp = @fopen($filename, 'wb'))
			{
				echo "RSSParser error";
				log_message('error', "Unable to write cache file: ".$filename);
				return;
			}

			flock($fp, LOCK_EX);
			fwrite($fp, $rawFeed);
			flock($fp, LOCK_UN);
			fclose($fp);
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	function set_cache_life($period = NULL)
	{
		$this->cache_life = $period;
		return $this;
	}

	// --------------------------------------------------------------------

	function set_feed_url($url = NULL)
	{
		$this->feed_uri = $url;
		return $this;
	}

	// --------------------------------------------------------------------

	/* Return the feeds one at a time: when there are no more feeds return false
	 * @param No of items to return from the feed
	 * @return Associative array of items
	*/
	function getFeed($num)
	{
		$this->parse();

		$c = 0;
		$return = array();

		foreach ($this->data AS $item)
		{
			$return[] = $item;
			$c++;

			if ($c == $num)
			{
				break;
			}
		}
		return $return;
	}

	// --------------------------------------------------------------------

	/* Return channel data for the feed */
	function & getChannelData()
	{
		$flag = false;

		if (!empty($this->channel_data))
		{
			return $this->channel_data;
		}
		else
		{
			return $flag;
		}
	}

	// --------------------------------------------------------------------

	/* Were we unable to retreive the feeds ?  */
	function errorInResponse()
	{
		return $this->feed_unavailable;
	}

	// --------------------------------------------------------------------

	/* Initialize the feed data */
	function clear()
	{
		$this->feed_uri     = NULL;
		$this->data         = FALSE;
		$this->channel_data = array();
		$this->cache_life   = 0;
		$this->callback     = FALSE;

		return $this;
	}
}

/* End of file RSSParser.php */
/* Location: ./application/libraries/RSSParser.php */
