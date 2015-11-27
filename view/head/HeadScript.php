<?php
/**
 * User: Tibi
 * Date: 2015.11.23.
 * Time: 11:00
 */

namespace decoy\view\head;


/**
 * Class HeadScript
 * @package decoy\view\head
 */
class HeadScript extends Head
{

	/**
	 * @param $path
	 * @param string $type
	 * @return $this
	 */
	public function appendFile($path, $type="text/javascript"){
		$this->append("<script type='$type' src='$path'></script>");
		return $this;
	}

	/**
	 * @param $path
	 * @param string $type
	 * @return $this
	 */
	public function prependFile($path, $type="text/javascript"){
		$this->prepend("<script type='$type' src='$path'></script>");
		return $this;
	}
}