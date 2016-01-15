<?php
/**
 * User: Tibi
 * Date: 2015.11.24.
 * Time: 15:11
 */

namespace decoy\utils;
use Sepia\FileHandler;
use Sepia\PoParser;

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

	private $poFiles = array(
		'en_US' => array(
			__DIR__.'/../language/en_US.po'
		),
		'hu_HU' => array(
			__DIR__ . '/../language/hu_HU.po'
		),
	);

	private $translations = array();

	/**
	 * Translator constructor.
	 */
	public function __construct()
	{
	}


	public function addPoFile($language,$file){
		if(!array_key_exists($language,$this->poFiles))
			$this->poFiles[$language]=array();
		$this->poFiles[$language][]=$file;
	}

	public function load($lang=null){
		$this->translations = array();
		$language = $lang==null? $this->locale : $lang;
		if (!array_key_exists($language, $this->poFiles)) {
			foreach($this->poFiles[$language] as $file){
				$fHandler = new FileHandler('language/' . $this->locale . '.po');
				$poParser = new PoParser($fHandler);
				$this->translations = array_merge_recursive($this->translations, $poParser->parse());
			}
		}
	}

	/**
	 * @param $string
	 * @param array $params
	 * @return string
	 */
	public function translate($string, array $params = null){
		if(array_key_exists($string,$this->translations)){
			if($params==null)
				return implode($this->translations["The requested url has a bad route configuration!"]['msgstr']);
			else
				return vsprintf(implode($this->translations["The requested url has a bad route configuration!"]['msgstr']),$params);
		}
		return $string;
	}

	/**
	 * @param $locale
	 */
	public function setLocale($locale){
		$this->locale = $locale;
		$this->load($this->locale);
	}

	public function getLocale(){
		return $this->locale;
	}

	public function getTranslations()
	{
		return $this->translations;
	}

	/**
	 * @return array
	 */
	public function getPoFiles()
	{
		return $this->poFiles;
	}

	/**
	 * @return array
	 */
	public function getPaths()
	{
		return $this->paths;
	}

	/**
	 * @param $folder
	 */
	public function addFolder($folder){
		$files = scandir($folder);
		foreach ($files as $file) {
			if(preg_match('/.po$/g',$file)===true){
				$matches=array();
				echo "testing $file<br>";
				preg_match('/([a-z]{2}_[A-Z]{2})/g',$file,$matches);
				if(count($matches)>0){
					echo "adding path $matches[0]<br>";
					$this->addPoFile($matches[0],$folder.'/'.$file);
				}
			}
		}
	}

}