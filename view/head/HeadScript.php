<?php
/**
 * Created by PhpStorm.
 * User: Tibi
 * Date: 2015.11.23.
 * Time: 11:00
 */

namespace decoy\view\head;


class HeadScript extends Head
{

	public function appendFile($path, $type="text/javascript"){
		$this->append("<script type='$type' src='$path'></script>");
		return $this;
	}
	public function prependFile($path, $type="text/javascript"){
		$this->prepend("<script type='$type' src='$path'></script>");
		return $this;
	}
}