<?php
/**
 * User: Tibi
 * Date: 2015.11.19.
 * Time: 11:16
 */

/**
 * Registering error events
 */
//$OldErrorHandler=set_error_handler("ErrorHandler");
register_shutdown_function("Shutdown");
/**
 *	If there was a 50* error, we want to catch it
 */
function Shutdown(){
	\decoy\base\ErrorController::$errors[]=error_get_last();
}

/**
 * PHP error handler function
 * This will catch almost everything and appends it to the $GLOBALS['error_list'] array
 * @param $errno
 * @param $errstr
 * @param $errfile
 * @param $errline
 * @return bool
 */
function ErrorHandler($errno, $errstr, $errfile, $errline){
	/**@TODO: NEED TO TEST
	if (!(error_reporting() & $errno)) {
			$error["type"]='E_WARNING';
		 return;
	 }*/

	$error=array("type"=>$errno,"message"=>$errstr,"file"=>$errfile,"line"=>$errline);

	switch ($errno){
		case E_USER_ERROR:
			$error["type"]='E_USER_ERROR';
			break;
		case E_WARNING:
			$error["type"]='E_WARNING';
			break;
		case E_USER_WARNING:
			$error["type"]='E_USER_WARNING';
			break;
		case E_USER_NOTICE:
			$error["type"]='E_USER_NOTICE';
			break;
		default:
			$error["type"]='UNKNOWN';
			break;
	}

	\decoy\base\ErrorController::$errors[]=$error;
	return true;
}