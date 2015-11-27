<?php
/**
 * User: Tibi
 * Date: 2015.11.20.
 * Time: 11:39
 */

namespace decoy\base;
use decoy\log\Logger;
use decoy\router\Route;
use decoy\utils\httpHeader\HtmlResponse;
use decoy\view\ViewModel;

/**
 * Class ErrorController
 * @package decoy\base
 */
class ErrorController extends BaseController
{
	/**
	 * @var array
	 */
	public static $errors=array();
	/**
	 * Bootstrapping the class
	 */
	public function _Bootstrap()
	{
		$this->forward()->setResponse(new HtmlResponse());
		$this->forward()->getCurrentRoute()->setDefault(new Route(array('action'=>'_error')));
	}
	/**
	 * If there was an error, this method will display it
	 */
	public function _error(){
		$model = new ViewModel('application/error');
		$model->addVariable('error_type','error');
		$model->addVariable('error',ErrorController::$errors);
		return $model;
	}

	/**
	 * @return ViewModel
	 */
	public function _notSupported()
	{
		$model = new ViewModel('application/error');
		$model->addVariable('error_type','notSupported');
		return $model;
	}

	/**
	 * @return ViewModel
	 */
	public function _notFound(){
		$model = new ViewModel('application/error');
		$model->addVariable('error_type','404');
		return $model;
	}
}