<?php
/**
 * Created by PhpStorm.
 * User: Tibi
 * Date: 2015.11.17.
 * Time: 13:57
 */

namespace decoy\base;


use decoy\Application;
use decoy\utils\httpHeader\JsonResponse;
use decoy\view\ViewModel;

class RestFulController extends BaseController
{

	protected function _call(){
		$this->forward()->setResponse(new JsonResponse());
		return $this->callCurrentOrDefault($this->getApplication()->getCurrentRoute()->getAction());
	}
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

	protected function init(){
	}

	//GET without URL parameter
	public function _list(){return null;}
	//GET with URL parameter
	public function _get($id){return null;}

	//POST
	public function _create($data){return null;}

	//PUT without URL parameter
	public function _replace($data){return null;}
	//PUT with URL parameter
	public function _update($id, $data){return null;}

	//DELETE without URL parameter
	public function _clear(){return null;}
	//DELETE with URL parameter
	public function _delete($id){return null;}

	//Copy with url parameter
	public function _clone($id){return null;}
	//Copy all
	public function _duplicate(){return null;}

}