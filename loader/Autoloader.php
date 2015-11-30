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
	public static $invokables;
	public static $modules;

	/**
	 * Autoloader constructor.
	 * @param array $invokables
	 */
	public function __construct(array $invokables, array $modules)
	{
		Autoloader::$invokables=$invokables;
		Autoloader::$modules=$modules;
		spl_autoload_register('self::load');
	}

	/**
	 * @param $className
	 */
	public static function load($className){
		$ns = explode('\\',$className);
		$root = dirname(dirname(dirname(dirname(__DIR__))));
		foreach(Autoloader::$invokables as $alias => $namespace){
			if($namespace==$className){
				$dir =  $root. '/src/' . $ns[0] . '/' . $ns[1] . '/' . $ns[2];
				if (file_exists($dir . '.php') && !class_exists($className))
					require $dir . '.php';
			}
		}
		foreach(Autoloader::$modules as $module){
			if(strpos($className,$module)!==false){
				$dir =  $root. '/src/' . $ns[0] . '/' . $ns[1] . '/' . $ns[2];
				if (file_exists($dir . '.php') && !class_exists($className))
					require $dir . '.php';
			}
		}

	}
}