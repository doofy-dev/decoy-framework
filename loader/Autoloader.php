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
		$root = dirname(dirname(dirname(dirname(__DIR__))));
		$path = $root.'/src/'.$className.'.php';
		if(file_exists($path) && !class_exists($className))
			require $path;

//		foreach(Autoloader::$invokables as $alias => $namespace){
//			if($namespace==$className){
//				$path =  $root. '/src/'.$className.'.php';
//				if (file_exists($path) && !class_exists($className))
//					require $path;
//			}
//		}
//		foreach(Autoloader::$modules as $module){
//			if(strpos($className,$module)!==false){
//				$dir =  $root. '/src/' . $className;
//				if (file_exists($dir . '.php') && !class_exists($className))
//					require $dir . '.php';
//			}
//		}

	}
}