<?php
/**
 * User: Tibi
 * Date: 2015.11.17.
 * Time: 13:04
 */

namespace decoy;


use decoy\base\BaseController;
use decoy\base\ErrorController;
use decoy\log\Logger;
use decoy\router\Route;
use decoy\router\Router;
use decoy\utils\httpBody\FormBody;
use decoy\utils\httpBody\HttpBody;
use decoy\utils\httpBody\JsonBody;
use decoy\utils\httpBody\MultipartBody;
use decoy\utils\httpBody\PlainBody;
use decoy\utils\httpHeader\HtmlResponse;
use decoy\utils\httpHeader\HttpHeader;
use decoy\utils\httpHeader\JsonResponse;
use decoy\utils\Translator;
use decoy\view\ViewModel;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Symfony\Component\Yaml\Parser;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use decoy\loader\Autoloader;

require_once __DIR__ . '/ErrorHandler.php';

/**
 * Class Application
 * @package decoy
 */
class Application
{
	/**
	 * Contains global settings
	 * @var array
	 */
	private $config;

	/**
	 * @var array mixed
	 */
	private $moduleSettings;

	/**
	 * @var EntityManager
	 */
	private $entityManager;

	/**
	 * Request headers
	 * @var HttpHeader
	 */
	private $requestHeader;
	/**
	 * Request body
	 * @var HttpBody
	 */
	private $requestBody;

	/**
	 * Type of the response
	 * @var HtmlResponse|JsonResponse
	 */
	private $response;


	/**
	 * View rendering switch
	 * @var bool
	 */
	private $enableRender=true;

	/**
	 * Router interface
	 * @var Router
	 */
	private $router;

	/**
	 * Currently active/matched route
	 * @var router\Route
	 */
	private $currentRoute;

	/**
	 * List of executable classes
	 * @var array
	 */
	private $invokable;

	/**
	 * List of view files
	 * @var
	 */
	private $views;
	/**
	 * @var \Composer\Autoload\ClassLoader
	 */
	private $loader;
	/**
	 * @var Parser
	 */
	private $parser;
	/**
	 * @var array disable rendering for defined controllers
	 */
	private $disableRender = array();

	/**
	 * @var BaseController
	 */
	public $caller = null;

	/**
	 * @var Translator
	 */
	private $translator;

	/**
	 * Application constructor.
	 * Parsing all registered modules and finds a route. After that it will execute the
	 * requested module.
	 * @param $loader - autoloader instance
	 * @param $configurationFilePath
	 */
	public function __construct($loader, $configurationFilePath)
	{
		$this->translator = new Translator();
		$this->response = new HtmlResponse();
		$this->loader = $loader;
		$this->parser = new Parser();
		$this->config = $this->parser->parse(file_get_contents($configurationFilePath));
		$this->modules = array();
		$this->routes = array();
		$this->router = new Router();
		if (array_key_exists('use_database', $this->config))
			if ($this->config['use_database'] && array_key_exists('Database', $this->config)) {
				$isDevMode = true;
				$config = Setup::createConfiguration($isDevMode);
				$driver = new AnnotationDriver(new AnnotationReader(), array(dirname(dirname(dirname(__DIR__))) . "/src/Application/Entity"));
				AnnotationRegistry::registerLoader('class_exists');
				$config->setMetadataDriverImpl($driver);

				$conn = $this->config['Database'];
				$this->entityManager = EntityManager::create($conn, $config);
			}
		foreach ($this->config['Modules'] as $module) {
//			$this->translator->addFolder('src/'.$module.'/language');
			$path = 'src/' . $module . '/config/config.yml';
			$langPath = dirname(dirname(dirname(__DIR__))) . '/src/' . $module . '/language';
			if (file_exists($langPath))
				$this->translator->addFolder($langPath);
			if (file_exists($path)) {

				$this->moduleSettings[$module] = $this->parser->parse(file_get_contents($path));
				$this->router->assignRoute($this->moduleSettings[$module]['routes']);
				if (array_key_exists('invokable', $this->moduleSettings[$module]))
					$this->getInvokablesFromModule($this->moduleSettings[$module]['invokable']);
				if (array_key_exists('view', $this->moduleSettings[$module]))
					$this->getViewsFromModule($this->moduleSettings[$module]['view']);
			}
		}
		$this->translator->load();
		new Autoloader($this->invokable, $this->config['Modules']);
		$this->router->addRoute('error_page', new Route(array(
				'controller' => (array_key_exists('error_page_controller', $this->config) ?
						$this->config['error_page_controller'] : '\decoy\base\ErrorController'),
				'route' => 'error'
		)));

		$this->requestHeader = new HttpHeader(false);
		$this->requestHeader->parse($_SERVER);

		$this->currentRoute = $this->router->getRouteForURI($_SERVER['REQUEST_URI']);
		$this->parseRequestBody();
		try {
			$this->execute();
		} catch (\Exception $e) {
			Logger::Log('log/error.txt', $e->getMessage());
			\decoy\base\ErrorController::$errors[] = $e->getMessage();
			$this->toRoute('error_page');
		}

		if (count(\decoy\base\ErrorController::$errors) > 0 && $this->config['debug'] == true) {
			Logger::Log('log/warn.txt', json_encode(\decoy\base\ErrorController::$errors, JSON_PRETTY_PRINT));
		}
	}

	/**
	 * Redirecting the execution route.
	 * This will not change the URL.
	 * All the not fully executed instance will finish.
	 * If you dont't want that, return after this method was called.
	 * @param string $route
	 */
	public function toRoute($route)
	{
		if ($this->caller != null)
			$this->disableRender[] = $this->caller->identifier;
		$r = $this->router->getRoute($route);
		if ($r != null) {
			$this->currentRoute = $r;
			try {
				$this->execute();
			} catch (\Exception $e) {
				Logger::Log('log/error.txt', $e->getMessage());
				\decoy\base\ErrorController::$errors[] = $e->getMessage();
				$this->toRoute('error_page');
			}
		}
		$this->caller = null;
	}

	/**
	 * Redirecting the site to another URL
	 * All the not saved instance state will lost!
	 * @param $url
	 */
	public function toUrl($url)
	{
		header('location: ' . $url);
	}

	/**
	 * @param HtmlResponse|JsonResponse $response
	 */
	public function setResponse($response)
	{
		$this->response = $response;
	}

	/**
	 *Executing the current route
	 */
	private function execute()
	{
		if ($this->currentRoute == null) {
			$this->router->getRoute('error_page')->setAction('_notFound');
			$this->toRoute('error_page');
			return;
		}
		$controllerName = $this->currentRoute->getController();
		if ($controllerName == '') {
			if ($this->currentRoute->getParams() != null && array_key_exists('controller', $this->currentRoute->getParams()))
				$controllerName = $this->currentRoute->getParams()['controller'];
			elseif ($this->currentRoute->getDefault() != null)
				$controllerName = $this->currentRoute->getDefault()->getController();
			else {
				throw new \Exception($this->translator->translate("The requested url has a bad route configuration!"));
			}
		}

		if (array_key_exists($controllerName, $this->invokable))
			$controllerName = $this->invokable[$controllerName];
		elseif (strpos($controllerName, 'decoy\base') === false)
			throw new \Exception('The requested Controller: \'' . $controllerName . '\' is not registered!');
		$c = new $controllerName($this);
		if ($c->getResult() && !in_array($c->identifier, $this->disableRender) && $this->enableRender) {
			echo $this->response->output($c);
		}
	}

	/**
	 * @param $controller
	 * @param array $settings
	 * @return string
	 * @throws \Exception
	 */
	public function dispatch($controller, array $settings)
	{
		$routeTemp = $this->currentRoute;
		$responseTemp = $this->response;
		$instance = null;
		$this->currentRoute = new Route($settings);
		if (class_exists($controller))
			$instance = new $controller($this);
		if ($instance == null && array_key_exists($controller, $this->invokable)) {
			$i = $this->invokable[$controller];
			$instance = new $i($this);
		}
		if ($instance == null)
			throw new \Exception("Class: " . $controller . " does not exist!");

		$this->currentRoute = $routeTemp;
		$res = $this->response;
		$this->response = $responseTemp;
		return $res->output($instance);
	}

	/**
	 * Setting up the request body parser
	 */
	private function parseRequestBody()
	{
		$content_type = $this->getRequestHeader()->getContentType();
		if (strpos($content_type, 'application/json') !== false) {
			$this->requestBody = new JsonBody();
		} elseif (strpos($content_type, 'application/x-www-form-urlencoded') !== false) {
			$this->requestBody = new FormBody();
		} elseif (strpos($content_type, 'text/plain') !== false) {
			$this->requestBody = new PlainBody();
		} elseif (strpos($content_type, 'multipart/form-data') !== false) {
			$this->requestBody = new MultipartBody();
		} else {
			$this->requestBody = new HttpBody();
		}
	}


	/**
	 * Get all invokable from a module, then assign it to a global list.
	 * @param array $invokable
	 */
	private function getInvokablesFromModule(array $invokable)
	{
		foreach ($invokable as $key => $path) {
			$this->invokable[$key] = $path;
		}
	}

	/**
	 * Get all view from a module, then assign it to a global list.
	 * @param array $views
	 */
	private function getViewsFromModule(array $views)
	{
		foreach ($views as $key => $path)
			$this->views[$key] = $path;
	}

	/**
	 * @return array
	 */
	public function getGlobalSettings()
	{
		return $this->config;
	}

	/**
	 * @return Translator
	 */
	public function getTranslator()
	{
		return $this->translator;
	}

	/**
	 * @param Translator $translator
	 */
	public function setTranslator($translator)
	{
		$this->translator = $translator;
	}

	/**
	 * @return array
	 */
	public function getRoutes()
	{
		return $this->router->toArray();
	}

	/**
	 * @return router\Route
	 */
	public function getCurrentRoute()
	{
		return $this->currentRoute;
	}

	/**
	 * Disabling ViewRenderers render method
	 */
	function disableRender()
	{
		$this->enableRender = false;
	}

	/**
	 * @param string $key
	 * @return mixed
	 */
	public function getParam($key)
	{
		if (array_key_exists($key, $this->currentRoute->getParams()))
			return $this->currentRoute->getParams()[$key];
		return null;
	}

	/**
	 * @return array
	 */
	public function getParams()
	{
		return $this->currentRoute->getParams();
	}

	/**
	 * @return array
	 */
	public function getModuleSettings()
	{
		return $this->moduleSettings;
	}

	/**
	 * @return EntityManager
	 */
	public function getEntityManager()
	{
		return $this->entityManager;
	}

	/**
	 * @return array
	 */
	public function getConfig()
	{
		return $this->config;
	}

	/**
	 * @param array $config
	 */
	public function setConfig($config)
	{
		$this->config = $config;
	}

	/**
	 * @return HttpHeader
	 */
	public function getRequestHeader()
	{
		return $this->requestHeader;
	}

	/**
	 * @param HttpHeader $requestHeader
	 */
	public function setRequestHeader($requestHeader)
	{
		$this->requestHeader = $requestHeader;
	}

	/**
	 * @return HttpHeader
	 */
	public function getResponse()
	{
		return $this->response;
	}

	/**
	 * @return Router
	 */
	public function getRouter()
	{
		return $this->router;
	}

	/**
	 * @param Router $router
	 */
	public function setRouter($router)
	{
		$this->router = $router;
	}

	/**
	 * @return array
	 */
	public function getInvokable()
	{
		return $this->invokable;
	}

	/**
	 * @param array $invokable
	 */
	public function setInvokable($invokable)
	{
		$this->invokable = $invokable;
	}

	/**
	 * @return mixed
	 */
	public function getViews()
	{
		return $this->views;
	}

	/**
	 * @param mixed $views
	 */
	public function setViews($views)
	{
		$this->views = $views;
	}

	/**
	 * @return HttpBody
	 */
	public function getRequestBody()
	{
		return $this->requestBody;
	}

}