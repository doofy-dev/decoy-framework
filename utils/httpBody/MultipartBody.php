<?php
/**
 * Created by PhpStorm.
 * User: Tibi
 * Date: 2015.11.20.
 * Time: 8:51
 */

namespace decoy\utils\httpBody;


class MultipartBody extends HttpBody
{
	public function __construct()
	{
		parent::__construct();
		$this->parse();
	}

	/**
	 *Parsing the sent input
	 */
	private function parse(){
		$this->content = $_POST;
		$this->files = $_FILES;
	}
}