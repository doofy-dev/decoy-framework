<?php
/**
 * Created by PhpStorm.
 * User: Tibi
 * Date: 2015.11.23.
 * Time: 11:01
 */

namespace decoy\view\head;


class Head
{
	private $content = '';

	protected function append($content){
		$this->content.=$content;
	}
	protected function prepend($content){
		$this->content = $content.$this->content;
	}
	public function __toString(){
		return $this->content;
	}
}