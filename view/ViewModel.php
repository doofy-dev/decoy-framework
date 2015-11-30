<?php
/**
 * User: Tibi
 * Date: 2015.11.18.
 * Time: 7:53
 */

namespace decoy\view;


/**
 * Class ViewModel
 * @package decoy\view
 */
use decoy\utils\Translator;
use decoy\view\head\HeadLink;
use decoy\view\head\HeadMeta;
use decoy\view\head\HeadScript;

/**
 * Class ViewModel
 * @package decoy\view
 */
class ViewModel
{
	/**
	 * @var string
	 */
	private $template;
	/**
	 * @var string
	 */
	private static $doctype = 'html';
	/**
	 * @var array of the available template files
	 */
	private static $availableTemplates;
	/**
	 * @var array of the global configuration
	 */
	private static $config;

	/**
	 * @var HeadMeta
	 */
	private static $headMeta;
	/**
	 * @var Translator
	 */
	private static $translator;
	/**
	 * @var HeadScript
	 */
	private static $headScript;

	/**
	 * @var HeadLink
	 */
	private static $headLink;

	/**
	 * @var array of the global configuration in view
	 */
	public $globalConfig;

	/**
	 * ViewModel constructor.
	 * @param string $template default: application/index
	 */
	public function __construct($template = 'application/index')
	{
		$this->template = $template;
	}

	/**
	 * Updating the available template paths
	 * @param array $available
	 */
	public function setAvailable(array $available)
	{
		ViewModel::$availableTemplates = $available;
//		ViewModel::$translator = $translator;
	}

	/**
	 * Updating the available configurations
	 * @param array $config
	 */
	public function setConfig(array $config)
	{
		ViewModel::$config = $config;
	}

	/**
	 * @param $key
	 * @param $value
	 */
	public function addVariable($key, $value)
	{
		$this->{$key} = $value;
	}

	/**
	 * @param array $vars
	 */
	public function addVariables(array $vars)
	{
		foreach ($vars as $key => $val)
			$this->{$key} = $val;
	}

	/**
	 * sets the layout
	 * @param string $name
	 */
	public function setLayout($name)
	{
		$this->template = $name;
	}

	/**
	 * @return HeadMeta
	 */
	public function headMeta()
	{
		if (ViewModel::$headMeta == null)
			ViewModel::$headMeta = new HeadMeta();
		return ViewModel::$headMeta;
	}

	/**
	 * @return HeadScript
	 */
	public function headScript()
	{
		if (ViewModel::$headScript == null)
			ViewModel::$headScript = new HeadScript();
		return ViewModel::$headScript;
	}

	/**
	 * @return HeadLink
	 */
	public function headLink()
	{
		if (ViewModel::$headLink == null)
			ViewModel::$headLink = new HeadLink();
		return ViewModel::$headLink;
	}

	/**
	 * @param $string
	 * @return string
	 */
	public function translate($string){
		return ViewModel::$translator->translate($string);
	}

	/**
	 * @return string
	 */
	public function doctype()
	{
		return "<!DOCTYPE " . ViewModel::$doctype . ">";
	}

	/**
	 * @param $doctype
	 */
	public function setDoctype($doctype)
	{
		ViewModel::$doctype = $doctype;
	}

	/**
	 *
	 */
	function backtrace()
	{
		$bt = debug_backtrace();
		$result = "";
		$result .= "<br /><br />Backtrace (most recent call last):<br /><br />\n";
		for ($i = 0; $i <= count($bt) - 1; $i++) {
			if (!isset($bt[$i]["file"]))
				$result .= "[PHP core called function]<br />";
			else
				$result .= "File: " . $bt[$i]["file"] . "<br />";

			if (isset($bt[$i]["line"]))
				$result .= "&nbsp;&nbsp;&nbsp;&nbsp;line " . $bt[$i]["line"] . "<br />";
			$result .= "&nbsp;&nbsp;&nbsp;&nbsp;function called: " . $bt[$i]["function"];

			if ($bt[$i]["args"]) {
				$result .= "<br />&nbsp;&nbsp;&nbsp;&nbsp;args: ";
				for ($j = 0; $j <= count($bt[$i]["args"]) - 1; $j++) {
					if (is_array($bt[$i]["args"][$j])) {
						$result .= json_encode($bt[$i]["args"][$j]);
					} else {
						$item = $bt[$i]["args"][$j];
						if (
							$this->canBeString($item)
						) {
							$result.=$item;
						}else{
							$result.=json_encode($bt[$i]['args']);
						}
					}


					if ($j != count($bt[$i]["args"]) - 1)
						$result .= ", ";
				}
			}
			$result .= "<br /><br />";
		}
	}

	/**
	 * @param $value
	 * @return bool
	 */
	private function canBeString($value)
	{
		if (is_object($value) and method_exists($value, '__toString')) return true;

		if (is_null($value)) return true;

		return is_scalar($value);
	}

	/**
	 * @param $key
	 * @return null
	 */
	public function globalConfig($key){
		if(array_key_exists($key,ViewModel::$config))
			return ViewModel::$config[$key];
		return null;
	}
	/**
	 * Renders the layout
	 * @return string
	 * @throws \Exception if the template was not found
	 */
	public function render()
	{
		$path = $this->template;
		if (array_key_exists($path, ViewModel::$availableTemplates)) {
			$path = 'src/' . ViewModel::$availableTemplates[$this->template];
		}
		if (file_exists($path)) {
			ob_start();
			include($path);
			$output = ob_get_contents();
			ob_end_clean();
			return $output;
		} else {
			throw new \Exception("The requested template (" . $path . ") not found!");
		}
	}

	/**
	 * @return string
	 * @throws \Exception
	 */
	public function __toString()
	{
		return $this->render();
	}
}