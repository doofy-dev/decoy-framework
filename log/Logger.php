<?php
/**
 * User: Tibi
 * Date: 2015.11.19.
 * Time: 11:23
 */

namespace decoy\log;


/**
 * Class Logger
 * @package decoy\log
 */
class Logger
{
	/**
	 * @param $file
	 * @param $message
	 */
	public static function Log($file, $message)
	{
		$path = dirname(dirname(dirname(dirname(__DIR__)))).'/'.$file;
		if(!file_exists(dirname($path)))
			mkdir(dirname($path),0755,true);
		$date = new \DateTime();
		file_put_contents($path,"\r\n".'['.$date->format('Y-m-d H:i:s').'] '.$message,FILE_APPEND);
	}

}