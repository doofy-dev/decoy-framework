<?php
/**
 * Created by PhpStorm.
 * User: Tibi
 * Date: 2015.11.20.
 * Time: 11:43
 */

namespace decoy\utils;


/**
 * Class EventManager
 * @package decoy\utils
 */
class EventManager
{
	/**
	 * @var array
	 */
	private $event;

	/**
	 * EventManager constructor.
	 */
	public function __construct()
	{
		$this->event=array();
	}

	/**
	 * @param $EventName
	 * @param $EventData
	 */
	public function assignEvent($EventName, $EventData){
		if($this->event[$EventName]==null)
			$this->event[$EventName]=array();
		$this->event[$EventName][]=$EventData;
	}

	/**
	 * @param $EventName
	 * @return null
	 */
	public function getEvent($EventName){
		return (array_key_exists($EventName,$this->event)?$this->event[$EventName]:null);
	}
}