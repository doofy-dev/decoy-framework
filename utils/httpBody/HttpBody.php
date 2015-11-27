<?php
/**
 * Created by PhpStorm.
 * User: Tibi
 * Date: 2015.11.20.
 * Time: 8:50
 */

namespace decoy\utils\httpBody;


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
	public function __construct()
	{
		$this->input = file_get_contents('php://input');
	}

	public function getBody(){return $this->content;}
	public function getFiles(){return $this->files;}
}