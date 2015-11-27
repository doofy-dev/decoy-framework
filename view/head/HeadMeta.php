<?php
/**
 * User: Tibi
 * Date: 2015.11.23.
 * Time: 11:00
 */

namespace decoy\view\head;


/**
 * Class HeadMeta
 * @package decoy\view\head
 */
class HeadMeta extends Head
{
	/**
	 * @param $name
	 * @param $content
	 * @return $this
	 */
	public function appendName($name, $content){
		$this->append("<meta name='$name' content='$content'>");
		return $this;
	}

	/**
	 * @param $name
	 * @param $content
	 * @return $this
	 */
	public function prependName($name, $content){
		$this->prepend("<meta name='$name' content='$content'>");
		return $this;
	}

	/**
	 * @param $equiv
	 * @param $content
	 * @return $this
	 */
	public function appendHttpEquiv($equiv, $content){
		$this->append("<meta http-equiv='$equiv' content='$content'>");
		return $this;
	}

	/**
	 * @param $equiv
	 * @param $content
	 * @return $this
	 */
	public function prependHttpEquiv($equiv, $content){
		$this->prepend("<meta http-equiv='$equiv' content='$content'>");
		return $this;
	}

	/**
	 * @param $content
	 * @return $this
	 */
	public function appendCharset($content){
		$this->append("<meta charset='$content'>");
		return $this;
	}

	/**
	 * @param $content
	 * @return $this
	 */
	public function prependCharset($content){
		$this->prepend("<meta charset='$content'>");
		return $this;
	}
}