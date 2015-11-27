<?php
/**
 * User: Tibi
 * Date: 2015.11.17.
 * Time: 14:27
 */

namespace decoy\utils\httpHeader;


use decoy\base\BaseController;
use decoy\log\Logger;

/**
 * Class HtmlResponse
 * @package decoy\utils\httpHeader
 */
class HtmlResponse extends HttpHeader
{
	/**
	 * @param BaseController $value
	 * @return string
	 * @throws \Exception
	 */
	public function output(BaseController $value){
		$this->setVariable('CONTENT_TYPE','text/html');
		$this->setHeaders();
		$result = $value->getResult();
		if(gettype($result)=='array')
			$value->getTemplate()->addVariables($result);
		else
		$value->getTemplate()->addVariable('content',$result);

		return $value->getTemplate()->render();
	}
}