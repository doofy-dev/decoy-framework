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
		if(!file_exists(realpath($file)))
			mkdir(realpath($file),0755,true);
		$date = new \DateTime();
		file_put_contents($file,"\r\n".'['.$date->format('Y-m-d H:i:s').'] '.$message,FILE_APPEND);
	}

	/**
	 * @param $path
	 */
	public static function mkdirs($path)
	{
		$expl = explode('/(\/|\\)/i', $path);
		$curr = '';
		for ($i = 0; $i < count($expl) - 1; $i++) {
			if (!file_exists($expl[$i]))
				mkdir($curr . '/' . $expl[$i], 0755);
			$curr .= '/' . $expl[$i];
		}
	}
}