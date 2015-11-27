<?php
/**
 * User: Tibi
 * Date: 2015.11.20.
 * Time: 8:50
 */

namespace decoy\utils\httpBody;


/**
 * Class HttpBody
 * @package decoy\utils\httpBody
 */
class HttpBody
{
	/**
	 * @var array mixed
	 */
	public $content;
	/**
	 * @var string php://input
	 */
	public $input;
	/**
	 * @var array of files
	 */
	public $files;

	/**
	 * HttpBody constructor.
	 */
	public function __construct()
	{
		$this->input = file_get_contents('php://input');
	}

	/**
	 * @return array
	 */
	public function getBody(){return $this->content;}

	/**
	 * @return array
	 */
	public function getFiles(){return $this->files;}
}