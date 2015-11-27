<?php
/**
 * User: Tibi
 * Date: 2015.11.17.
 * Time: 13:57
 */

namespace decoy\base;


use decoy\Application;
use decoy\utils\httpHeader\JsonResponse;
use decoy\view\ViewModel;

/**
 * Class RestFulController
 * @package decoy\base
 */
class RestFulController extends BaseController
{

	/**
	 * @return null
	 */
	protected function _call(){
		$this->forward()->setResponse(new JsonResponse());
		return $this->callCurrentOrDefault($this->getApplication()->getCurrentRoute()->getAction());
	}

	/**
	 * @param null $method
	 * @return null
	 */
	public function callCurrentOrDefault($method=null)
	{
		$action = null;
		if ($method != null && method_exists($this, $method))
			return $this->$method();
		else{
			$url = explode('/',$_SERVER['REQUEST_URI']);
			$id = intval(end($url));
			$hasID = $id>0;
			$requestBody = $this->forward()->getRequestBody();
			if($this->getRequest()->isGet()){
				if($hasID)
					return $this->_get($id);
				return $this->_list();
			}elseif($this->getRequest()->isPost()){
				return $this->_create($requestBody);
			}elseif($this->getRequest()->isPut()){
				if($hasID)
					return $this->_update($id,$requestBody);
				return $this->_replace($requestBody);
			}elseif($this->getRequest()->isCopy()){
				if($hasID)
					return $this->_clone($id);
				return $this->_duplicate();
			}
			elseif($this->getRequest()->isDelete()){
				if($hasID)
					return $this->_delete($id);
				return $this->_clear();
			}
		}
		return null;
	}

	/**
	 *
	 */
	protected function init(){
	}

	//GET without URL parameter
	/**
	 * @return null
	 */
	public function _list(){return null;}
	//GET with URL parameter
	/**
	 * @param $id
	 * @return null
	 */
	public function _get($id){return null;}

	//POST
	/**
	 * @param $data
	 * @return null
	 */
	public function _create($data){return null;}

	//PUT without URL parameter
	/**
	 * @param $data
	 * @return null
	 */
	public function _replace($data){return null;}
	//PUT with URL parameter
	/**
	 * @param $id
	 * @param $data
	 * @return null
	 */
	public function _update($id, $data){return null;}

	//DELETE without URL parameter
	/**
	 * @return null
	 */
	public function _clear(){return null;}
	//DELETE with URL parameter
	/**
	 * @param $id
	 * @return null
	 */
	public function _delete($id){return null;}

	//Copy with url parameter
	/**
	 * @param $id
	 * @return null
	 */
	public function _clone($id){return null;}
	//Copy all
	/**
	 * @return null
	 */
	public function _duplicate(){return null;}

}