<?php
/**
 * Created by PhpStorm.
 * User: Tibi
 * Date: 2015.11.20.
 * Time: 8:59
 */

namespace decoy\utils\httpBody;


class PlainBody extends HttpBody
{
	public function __construct()
	{
		parent::__construct();
		$this->content = $this->input;
	}
}