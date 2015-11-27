<?php
/**
 * Created by PhpStorm.
 * User: Tibi
 * Date: 2015.11.17.
 * Time: 14:27
 */

namespace decoy\utils\httpHeader;


use decoy\base\BaseController;

/**
 * Class JsonResponse
 * @package decoy\utils\httpHeader
 */
class JsonResponse extends HttpHeader
{
	/**
	 * @param BaseController $value
	 * @return string
	 */
	public function output(BaseController $value){
		$this->setVariable('CONTENT_TYPE','application/json');
		$this->setHeaders();
		return json_encode($value->getResult());
	}
}