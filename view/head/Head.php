<?php
/**
 * User: Tibi
 * Date: 2015.11.23.
 * Time: 11:01
 */

namespace decoy\view\head;


/**
 * Class Head
 * @package decoy\view\head
 */
class Head
{
	/**
	 * @var string
	 */
	private $content = '';

	/**
	 * @param $content
	 */
	protected function append($content){
		$this->content.=$content;
	}

	/**
	 * @param $content
	 */
	protected function prepend($content){
		$this->content = $content.$this->content;
	}

	/**
	 * @return string
	 */
	public function __toString(){
		return $this->content;
	}
}