<?php
/**
 * Created by PhpStorm.
 * User: Tibi
 * Date: 2015.11.23.
 * Time: 15:24
 */

namespace decoy\loader;


class Autoloader
{
	/**
	 * Autoloader constructor.
	 */
	public function __construct()
	{
		spl_autoload_register('self::load');
	}

	public static function load($className){
		$ns = explode('\\',$className);
		$dir = dirname(dirname(dirname(dirname(__DIR__)))).'/src/'.$ns[0].'/src/'.$ns[1].'/'.$ns[2];
		if(file_exists($dir.'.php'))
			include $dir.'.php';
	}
}