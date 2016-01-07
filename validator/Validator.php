<?php

namespace decoy\validator;

/**
 * Class Validator
 * @package decoy\validator
 */
/**
 * Class Validator
 * @package decoy\validator
 */
class Validator
{
    /**
     * @var mixed
     */
    protected $value;
    /**
     * @var string
     */
    protected $type;

    /**
     * @var array
     */
    protected $expressions =  array(
        'float'=>'/^([+\-])?\d+((.){0,1}\d+)$/g',
        'integer'=>'/^([+\-])?\d+$/g',
        'string'=>'/(.*?)/g',
        'email'=>'/^[a-zA-Z0-9.!#$%&\'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/g',
        'boolean'=>'/^(true|false|0|1)$/g',
        'hex'=>'/^#?([a-f0-9]{6}|[a-f0-9]{3})$/g',
        'H:i'=>'/^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/g',
        'H:i:s'=>'/^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/g',
        'h:m'=>'/^([0-9]|0[0-9]|1[0-2]):[0-5][0-9](\s)?(AM|PM)$/g',
        'yyyy/mm/dd'=>'/^[1-2][0-9]{3}\/([0-9]|1[0-2])\/([0-9]|1[0-9]|2[0-9]|3[0-1])$/g',
        'mm/dd/yyyy'=>'/^([0-9]|1[0-2])\/([0-9]|1[0-9]|2[0-9]|3[0-1])\/[1-2][0-9]{3}$/g',
        'dd/mm/yyyy'=>'/^([0-9]|1[0-9]|2[0-9]|3[0-1])\/([0-9]|1[0-2])\/[1-2][0-9]{3}$/g',

        'yyyy/mm/dd H:i:s'=>'/^[1-2][0-9]{3}\/([0-9]|1[0-2])\/([0-9]|1[0-9]|2[0-9]|3[0-1])\s([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/g',
    );

    /**
     * Validator constructor.
     */
    public function __construct()
    {

    }

    /**
     * @param $key
     * @param $expression
     */
    public function loadExpression($key, $expression){
        $this->expressions[$key] = $expression;
    }

    /**
     * @param array $expressions
     */
    public function loadExpressions(array $expressions){
        $this->expressions = array_merge($this->expressions,$expressions);
    }

    /**
     * @param $value
     * @param $type
     * @return bool
     */
    public function validate($value, $type){
        preg_match($value,$this->expressions[$type],$match);
        return count($match)==1;
    }

}