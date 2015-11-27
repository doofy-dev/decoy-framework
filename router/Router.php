<?php
/**
 * User: Tibi
 * Date: 2015.11.18.
 * Time: 9:37
 */

namespace decoy\router;

/**
 * Class Router
 * @package decoy\router
 */
class Router
{
	/**
	 * @var Route[]
	 */
	private $routes;

	/**
	 * Router constructor.
	 */
	public function __construct()
	{
		$this->routes = array();
	}

	/**
	 * @param Route $route
	 */
	public function addRoute($key,$route){
		$this->routes[$key] =  $route;
	}

	/**
	 * @param array $route
	 */
	public function assignRoute($route){
		if(is_array($route))
			$this->routes = array_merge($this->routes, $this->traverse($route));
	}

	/**
	 * @param array $node
	 * @param Route $trunk
	 * @return Route[]
	 */
	private function traverse(array $node, $trunk = null){
		$r=array();
		foreach($node as $key=>$n){
//			echo $key.'<br>';
			$nodeRoute = new Route($n);
			if($trunk!=null)
				$nodeRoute->merge($trunk);
			$r[$key] = $nodeRoute;
			if(array_key_exists('child_route',$n)){
				$r=array_merge($this->traverse($n['child_route'],$nodeRoute),$r);
			}
		}
		return $r;
	}

	/**
	 * @TODO: check 404
	 * @param string $uri
	 * @return Route|null
	 */
	public function getRouteForURI($uri){
		$final = null;
		$bestMatch = 0;
		foreach ($this->routes as $route) {
			$m=$route->match($uri);
//			echo $route->getRoute().' '.($m>=0?'true':'false').'<br>';
			if($bestMatch<$m){
				$final = $route;
				$bestMatch = $m;
			}
		}
		if($final==null||($final!=null && $final->getRoute()=='/'&&$uri!="/")){
			return null;
		}
		if($final->getParams()!=null) {
			if (array_key_exists('controller', $final->getParams()) && $final->getParams()['controller'] != '')
				$final->setController($final->getParams()['controller']);
			if (array_key_exists('action', $final->getParams()) && $final->getParams()['action'] != '')
				$final->setAction($final->getParams()['action']);
		}
		if($final->getDefault()!=null){
			if($final->getAction()=='')
				$final->setAction($final->getDefault()->getAction());

			if($final->getController()=='')
				$final->setController($final->getDefault()->getController());
		}

		return $final;
	}

	/**
	 * @param string $key
	 * @return Route|null
	 */
	public function getRoute($key){
		if(array_key_exists($key,$this->routes))
			return $this->routes[$key];
		return null;
	}

	/**
	 * @return array of routes
	 */
	public function toArray(){
		$r = array();
		foreach($this->routes as $key=>$route){
			$r[$key]=$route->toArray();
		}
		return $r;
	}

}