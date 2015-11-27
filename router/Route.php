<?php
/**
 * User: Tibi
 * Date: 2015.11.18.
 * Time: 9:41
 */

namespace decoy\router;

/**
 * Class Route
 * @package decoy\router
 */
class Route
{
	/**
	 * @var string
	 */
	private $route;
	/**
	 * @var string
	 */
	private $action;
	/**
	 * @var array
	 */
	private $constraints;
	/**
	 * @var string
	 */
	private $controller;
	/**
	 * @var array
	 */
	private $http_accept;
	/**
	 * @var \decoy\router\Route
	 */
	private $default;

	/**
	 * Route constructor.
	 * @param array $route
	 */
	public function __construct(array $route)
	{
		if (array_key_exists('default', $route))
			$this->default = new Route($route['default']);
		$this->controller = array_key_exists('controller', $route) ? $route['controller'] : '';
		$this->action = array_key_exists('action', $route) ? $route['action'] : '';
		$this->route = array_key_exists('route', $route) ? $route['route'] : '';
		$this->http_accept = array_key_exists('http_accept', $route) ? explode("|", $route['http_accept']) : null;
		$this->constraints = array_key_exists('constraint', $route) ? $route['constraint'] : array();
	}

	/**
	 * @return string
	 */
	public function getRoute()
	{
		return $this->route;
	}

	/**
	 * @param string $route
	 */
	public function setRoute($route)
	{
		$this->route = $route;
	}

	/**
	 * @return string
	 */
	public function getAction()
	{
		return $this->action;
	}

	/**
	 * @param string $action
	 */
	public function setAction($action)
	{
		$this->action = $action;
	}

	/**
	 * @return string
	 */
	public function getController()
	{
		return $this->controller;
	}

	/**
	 * @param string $controller
	 */
	public function setController($controller)
	{
		$this->controller = $controller;
	}

	/**
	 * @return array
	 */
	public function getHttpAccept()
	{
		return $this->http_accept;
	}

	/**
	 * @param array $http_accept
	 */
	public function setHttpAccept($http_accept)
	{
		$this->http_accept = $http_accept;
	}

	/**
	 * @return array
	 */
	public function getParams()
	{
		return $this->constraints;
	}

	/**
	 * @param array $params
	 */
	public function setParams($params)
	{
		$this->constraints = $params;
	}

	/**
	 * @return Route
	 */
	public function getDefault()
	{
		return $this->default;
	}

	/**
	 * @param Route $default
	 */
	public function setDefault($default)
	{
		$this->default = $default;
	}


	/**
	 * @return array
	 */
	public function toArray()
	{
		$res = array();
		foreach ($this as $k => $v) {
			if ($k != 'default')
				$res[$k] = $v;
			elseif ($v != null)
				$res[$k] = $v->toArray();
			else
				$res[$k] = array();
		}
		return $res;
	}

	/**
	 * @param Route $other
	 */
	public function merge(Route $other)
	{
		$temp = preg_replace('/\[\/\]$/', '/', $other->route);

		$hasSlash = false;
		if (strlen($temp) > 0)
			$hasSlash = $temp[strlen($temp) - 1] == '/';

		$this->route = $temp . ($hasSlash ? '' : '/') . $this->route;
		if ($other->default != null && $this->default != null)
			$this->default->merge($other->default);
		elseif ($other->default != null && $this->default == null)
			$this->default = $other->default;
		if ($this->controller == null)
			$this->controller = $other->controller;
		if ($this->http_accept != null && $other->http_accept != null)
			$this->http_accept = array_merge($other->http_accept, $this->http_accept);
		elseif ($this->http_accept == null && $other->http_accept != null)
			$this->http_accept = $other->http_accept;

		if ($this->constraints != null && $other->constraints != null)
			$this->constraints = array_merge($other->constraints, $this->constraints);
		elseif ($this->constraints == null && $other->constraints != null)
			$this->constraints = $other->constraints;
	}

	/**
	 * @param string $uri
	 * @return bool
	 */
	public function match($uri)
	{
		$actualRoute = $this->route;
		$forCount = $this->route;
		$actualRoute = preg_replace('/\[(\/|.)(.*?)\]/i', '($1$2)?', $actualRoute);
		if ($this->constraints != null)
			foreach ($this->constraints as $key => $constraint) {
				$actualRoute = str_replace(':' . $key, $constraint, $actualRoute);
				$forCount = str_replace(':' . $key, '', $forCount);
			}


//		$actualRoute = str_replace('[/]', '/*', $actualRoute);
		$actualRoute = '/' . str_replace('/', '\/', $actualRoute) . '/i';
//		echo $actualRoute.' - ';
		//		$actualRoute = str_replace(']',')',$actualRoute);
		if (preg_match($actualRoute, $uri)) {
			if ($this->constraints != null)
				foreach ($this->constraints as $key => $constraint)
					$this->setParamFromURI($uri, $key);
			return strlen($forCount);
		}
		return -1;
	}

	/**
	 * @param $uri
	 * @param $param
	 */
	private function setParamFromURI($uri, $param)
	{
		$explodeReal = explode('/', str_replace('.', '/', $uri));
		$cleaned = preg_replace('/(\(|\)|\?|\[|\]|\$)/im', '', $this->route);
		$explodeLocal = explode('/', str_replace('.', '/', $cleaned));

		$string = '';
		for ($i = 0; $i < count($explodeLocal) && $i < count($explodeLocal); $i++) {
			if (array_key_exists($i, $explodeReal)) {
				if ($explodeLocal[$i] == ':' . $param) {
					$string = $explodeReal[$i];
					break;
				}
			}
		}
		if ($this->default != null && $this->default->constraints!=null)
			if ($string == '' && array_key_exists($param, $this->default->constraints)) {
				$string = $this->default->constraints[$param];
			}
		$this->constraints[$param] = $string;
	}

}