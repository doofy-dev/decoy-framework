<?php
/**
 * User: Tibi
 * Date: 2015.11.20.
 * Time: 8:59
 */

namespace decoy\utils\httpBody;


/**
 * Class PlainBody
 * @package decoy\utils\httpBody
 */
class PlainBody extends HttpBody
{
	/**
	 * PlainBody constructor.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->content = $this->input;
	}
}