<?php
/**
 * User: Tibi
 * Date: 2015.11.23.
 * Time: 15:24
 */

namespace decoy\loader;


/**
 * Class Autoloader
 * @package decoy\loader
 */
class Autoloader
{
	/**
	 * Autoloader constructor.
	 */
	public function __construct()
	{
		spl_autoload_register('self::load');
	}

	/**
	 * @param $className
	 */
	public static function load($className){
		$ns = explode('\\',$className);
		if($ns[1]!="Entity" && $ns[1]!="Repository")
			$dir = dirname(dirname(dirname(dirname(__DIR__)))).'/src/'.$ns[0].'/src/'.$ns[1].'/'.$ns[2];
		else
			$dir = dirname(dirname(dirname(dirname(__DIR__)))).'/src/'.$ns[0].'/'.$ns[1].'/'.$ns[2];
		if(file_exists($dir.'.php'))
			include $dir.'.php';
	}
}