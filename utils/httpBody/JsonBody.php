<?php
/**
 * Created by PhpStorm.
 * User: Tibi
 * Date: 2015.11.20.
 * Time: 8:50
 */

namespace decoy\utils\httpBody;


class JsonBody extends HttpBody
{

	public function __construct()
	{
		parent::__construct();
		$this->content = json_decode($this->input,true);
	}
}