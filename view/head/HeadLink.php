<?php
/**
 * User: Tibi
 * Date: 2015.11.23.
 * Time: 10:59
 */

namespace decoy\view\head;

/**
 * Class HeadLink
 * @package decoy\view\head
 */
class HeadLink extends Head
{
	/**
	 * @param $path
	 * @param string $type
	 * @return $this
	 */
	public function appendStylesheet($path, $type='text/css'){
		$this->append("<link href='$path' rel='stylesheet' media='screen' type='$type'>");
		return $this;
	}

	/**
	 * @param $path
	 * @param string $type
	 * @return $this
	 */
	public function prependStylesheet($path, $type='text/css'){
		$this->append("<link href='$path' rel='stylesheet' media='screen' type='$type'>");
		return $this;
	}
}