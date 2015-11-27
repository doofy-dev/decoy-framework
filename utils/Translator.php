<?php
/**
 * User: Tibi
 * Date: 2015.11.24.
 * Time: 15:11
 */

namespace decoy\utils;


/**
 * Class Translator
 * @package decoy\utils
 */
class Translator
{
	/**
	 * @var array
	 */
	private $paths = array();
	/**
	 * @var string
	 */
	private $locale = "hu_HU";

	/**
	 * Translator constructor.
	 */
	public function __construct()
	{
		$this->setLocale($this->locale);
	}

	/**
	 * @param $string
	 * @return string
	 */
	public function translate($string){
		echo _($string);
		return _($string);
	}

	/**
	 * @param $locale
	 */
	public function setLocale($locale){
		$this->locale = $locale;
		putenv("LANG=".$locale);
		setlocale(LC_ALL, $locale);
	}

	/**
	 * @param $folder
	 */
	public function addFolder($folder){
		$this->paths[] = $folder;
		$this->bindDomain($folder);
	}

	/**
	 * @param $folder
	 * @param string $charset
	 */
	private function bindDomain($folder, $charset="UTF-8"){
		bindtextdomain($this->locale, dirname($folder));
		textdomain($this->locale);
		bind_textdomain_codeset($this->locale, $charset);
	}
}