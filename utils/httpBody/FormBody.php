<?php
/**
 * Created by PhpStorm.
 * User: Tibi
 * Date: 2015.11.20.
 * Time: 8:51
 */

namespace decoy\utils\httpBody;


/**
 * Class FormBody
 * @package decoy\utils\httpBody
 */
class FormBody extends HttpBody
{
	/**
	 * FormBody constructor.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->parse();
	}

	/**
	 *Parsing the sent input
	 */
	private function parse(){
		$inputs = explode('&',$this->input);
		foreach($inputs as $input){
			$exp = explode('=',$input);
			if(count($exp)==2)
				$this->content[urldecode($exp[0])] = urldecode($exp[1]);
		}
	}
}