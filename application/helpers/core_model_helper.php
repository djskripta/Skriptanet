<?php
/* 
 * @company SkriptaNet
 * @author Nikita Nikitin
 */

/**
 * This class is a dependency of the core model
 * For fetching the object by id and performing checks against available data
 */
class coreMetaStruct{
    public $db_validators = array();
}

class callbackResponseStruct{
    public $status = 1;
    public $errors = array();
    public $data; //can be any kind of struct or an array (arrays for lazy developers)
}

/**
 * Used to track properties and callbacks for database fields
 */
class fieldSchema{
    const TYPE_PRIMARY_KEY = 'primary_key';
    const TYPE_INT = 'int(11)';
    const TYPE_VARCHAR = 'varchar(255)';
    const TYPE_TIMESTAMP = 'datetime';
    
    public $name;
    public $type = 'varchar(255)';
    public $field_type = 'text';
    public $editable = true;
    public $ignore_blank_on_update = false;
    public $null;
    public $key;
    public $default;
    public $extra;
    public $attributes = '';
    public $validation = array();
    public $parsers = array();
    public $triggers = array();
    public $db_links = array();
    
    /**
     * Form Stuff
     */
    public $label;
    
    public function __construct(
	$name, 
	$type, 
	$null = false, 
	$key = null, 
	$default = null, 
	$extra = null
    ){
	$this->name = $name;
	$this->type = $type;
	$this->null = $null;
	$this->key = $key;
	$this->default = $default;
	$this->extra = $extra;
    }
    
    public function addValidator($callback, $params = array()){
	$this->validation[] = array($callback, $params);
    }
    
    public function addParser($callback, $params = array()){
	$this->parsers[] = array($callback, $params);
    }
    
    public function addTrigger($callback, $params = array()){
	$this->triggers[] = array($callback, $params);
    }
    
    public function addDbLink($table, $key){
	$this->db_links[$table] = $key;
    }
}