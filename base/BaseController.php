<?php
/**
 * User: Tibi
 * Date: 2015.11.17.
 * Time: 13:58
 */

namespace decoy\base;


use decoy\log\Logger;
use decoy\utils\httpHeader\HtmlResponse;
use decoy\utils\httpHeader\HttpHeader;
use decoy\utils\httpHeader\JsonResponse;
use decoy\view\head\HeadMeta;
use decoy\view\ViewModel;

/**
 * Class BaseController
 * @package decoy\base
 */
class BaseController
{
	/**
	 * @var HttpHeader
	 */
	protected $response;
	/**
	 * @var \decoy\Application
	 */
	private $application;

	/**
	 * @var ViewModel
	 */
	private $template;

	/**
	 * @var mixed
	 */
	private $result;

	/**
	 * @var string
	 */
	public $identifier;
	/**
	 * @var \decoy\utils\Translator
	 */
	public $translator;

	/**
	 * BaseController constructor.
	 * @param \decoy\Application $application
	 * @throws \Exception if method was not found
	 */
	public function __construct($application)
	{
		$this->identifier = uniqid();
		$this->application = $application;
		$this->translator = $application->getTranslator();
		$accepted = $this->forward()->getCurrentRoute()->getHttpAccept();
		if(count($accepted)>0 &&
				!in_array($this->forward()->getRequestHeader()->getVariable('REQUEST_METHOD'),$accepted)) {
			$this->forward()->getRouter()->getRoute('error_page')->setAction('_notSupported');
			$this->forward()->toRoute('error_page');
			return;
		}


		$this->template = new ViewModel('application/layout');
		$this->_Bootstrap();
		$this->template->setAvailable($this->getApplication()->getViews(),$this->translator);
			$this->template->setConfig($this->getApplication()->getConfig());
			$this->result = $this->_call();
			$this->_Delegate();
	}

	/**
	 * @return mixed
	 */
	public function getResult(){return $this->result;}
	/**
	 * @return ViewModel
	 */
	public function getTemplate()
	{
		return $this->template;
	}

	/**
	 * @return mixed
	 * @throws \Exception
	 */
	protected function _call(){
		$action = $this->getCurrentOrDefault($this->getApplication()->getCurrentRoute()->getAction());
		if ( $action == null) {
			throw new \Exception('The requested method "' . $this->getApplication()->getCurrentRoute()->getAction() . '" in controller\alias "' . $this->getApplication()->getCurrentRoute()->getController() . '" does not exists!');
		}
		return $this->$action();
	}

	/**
	 * @return \decoy\Application
	 */
	public function forward(){
		$this->application->caller = $this;
		return $this->application;
	}


	/**
	 * @param string $layout
	 */
	public function setTemplate($layout)
	{
		$this->template = new ViewModel($layout);
	}
	/**
	 * @param ViewModel $view
	 * @return mixed
	 */
	public function renderView($view){
		$view->setAvailable($this->getApplication()->getViews());
		return $view->render();
	}

	/**
	 * @return HttpHeader
	 */
	public function getRequest(){
		return $this->application->getRequestHeader();
	}

	/**
	 * @return \Doctrine\ORM\EntityManager
	 */
	public function getEntityManager(){
		return $this->application->getEntityManager();
	}

	/**
	 * @return \decoy\Application
	 */
	public function getApplication()
	{
		$this->application->caller = $this;
		return $this->application;
	}

	/**
	 * @param string $method
	 * @return mixed
	 */
	public function getCurrentOrDefault($method)
	{
		$requested = $method;
		$default = 'index';
		if($this->getApplication()->getCurrentRoute()->getDefault()!=null)
			$default = $this->getApplication()->getCurrentRoute()->getDefault()->getAction();
		if ($requested != '' && method_exists($this, $requested))
			return $requested;
		elseif ($default != '' && method_exists($this, $default))
			return $default;
		return null;
	}

	/**
	 * @return array
	 */
	public function getUrlParameters(){
		return $this->forward()->getCurrentRoute()->getParams();
	}

	/**
	 * @param $key
	 * @return null
	 */
	public function getUrlParameter($key){
		if(array_key_exists($key,$this->forward()->getCurrentRoute()->getParams()))
			return $this->forward()->getCurrentRoute()->getParams()[$key];
		return null;
	}
	/**
	 *
	 */
	protected function init(){}

	/**
	 *
	 */
	public function _Bootstrap(){}

	/**
	 *
	 */
	public function _Delegate(){}


}