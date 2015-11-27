<?php
/**
 * User: Tibi
 * Date: 2015.11.20.
 * Time: 8:50
 */

namespace decoy\utils\httpBody;


/**
 * Class JsonBody
 * @package decoy\utils\httpBody
 */
class JsonBody extends HttpBody
{

	/**
	 * JsonBody constructor.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->content = json_decode($this->input,true);
	}
}