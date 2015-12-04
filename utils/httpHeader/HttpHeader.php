<?php
/**
 * User: Tibi
 * Date: 2015.11.18.
 * Time: 15:06
 */

namespace decoy\utils\httpHeader;


/**
 * Class HttpHeader
 * @package decoy\utils\httpHeader
 */
class HttpHeader
{
	/**
	 * @var array
	 */
	private $content;

	/**
	 * @var
	 */
	private $body;
	/**
	 * @var
	 */
	private $files;

	/**
	 * HttpHeader constructor.
	 * @param boolean $autoParse
	 */
	public function __construct($autoParse=true)
	{
		$this->parse(getallheaders());
	}

	/**
	 * @param $key
	 * @param $value
	 */
	public function setVariable($key, $value){
		$this->content[$key] = $value;
	}

	/**
	 * @param $key
	 * @return null|array|string
	 */
	public function getVariable($key){
		if(array_key_exists($key,$this->content))
			return $this->content[$key];
		return null;
	}

	/**
	 * @param $header
	 */
	public function parse($header){
		$this->content = $header;
	}

	/**
	 * @return bool
	 */
	public function isPost(){
		return $this->content['REQUEST_METHOD']=='POST';
	}

	/**
	 * @return bool
	 */
	public function isGet(){
		return $this->content['REQUEST_METHOD']=='GET';
	}

	/**
	 * @return bool
	 */
	public function isDelete(){
		return $this->content['REQUEST_METHOD']=='DELETE';
	}

	/**
	 * @return bool
	 */
	public function isPut(){
		return $this->content['REQUEST_METHOD']=='PUT';
	}

	/**
	 * @return bool
	 */
	public function isCopy(){
		return $this->content['REQUEST_METHOD']=='COPY';
	}

	/**
	 * @return string
	 */
	public function getContentType(){
		if(array_key_exists('CONTENT_TYPE',$this->content))
			return $this->content['CONTENT_TYPE'];
		return 'text/plain';
	}

	/**
	 * @param integer $code
	 */
	public function responseCode($code){
		http_response_code($code);
	}

	/**
	 * Setting headers
	 */
	public function setHeaders(){
		if($this->content!=null && !headers_sent())
			foreach($this->content as $key=>$value)
				header(str_replace('_','-',$key).': '.$value);
	}
}