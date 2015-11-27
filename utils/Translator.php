<?php
/**
 * Created by PhpStorm.
 * User: Tibi
 * Date: 2015.11.24.
 * Time: 15:11
 */

namespace decoy\utils;


class Translator
{
	private $paths = array();
	private $locale = "hu_HU";
	public function __construct()
	{
		$this->setLocale($this->locale);
	}

	public function translate($string){
		echo _($string);
		return _($string);
	}
	public function setLocale($locale){
		$this->locale = $locale;
		putenv("LANG=".$locale);
		setlocale(LC_ALL, $locale);
	}
	public function addFolder($folder){
		$this->paths[] = $folder;
		$this->bindDomain($folder);
	}
	private function bindDomain($folder, $charset="UTF-8"){
		bindtextdomain($this->locale, dirname($folder));
		textdomain($this->locale);
		bind_textdomain_codeset($this->locale, $charset);
	}
}