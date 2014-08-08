<?php
/**
 * Every object is an entity
 * This model sets the ground rules
 */

class Core_model extends CI_Model{
    public $database = 'moneytree';
    protected $table;
    protected $schema;
    protected $indexes;
    
    private $global_parsers = array();
    
    public function __construct()
    {
        parent::__construct();	
	$this->load->database('core');
	
	$field = new fieldSchema('created', fieldSchema::TYPE_TIMESTAMP);
	$field->addParser('created_timestamp');
	$this->schema[] = $field;
	
	$field = new fieldSchema('updated', fieldSchema::TYPE_TIMESTAMP);
	$field->addParser('updated_timestamp');
	$field->attributes = 'ON UPDATE CURRENT_TIMESTAMP';
	$this->schema[] = $field;
	
	$this->update_schema();
    }
    
    public function update_schema()
    {	
	/**
	 * Parse the fields
	 */
	$fields = array();
	foreach($this->schema as $item){
	    $fields[ucfirst($item->name)] = $this->_get_field_string_from_schema($item);
	}
	
	/**
	 * Generate the creation query..
	 * ..this can be re-used for multiple purposes
	 */
	$create_query = "CREATE TABLE {$this->table} (";
	$create_query .= implode(',',$fields);
	$create_query .= ")";
	
	$this->ensure_table_exists($create_query);
	$this->ensure_column_definitions();
	$this->ensure_indexes($create_query);
    }
    
    /**
     * Make sure the table exists
     * 
     * @param string $create_query - query for table creation
     */
    private function ensure_table_exists($create_query)
    {
	$rs = $this->db->query("SHOW TABLES LIKE '{$this->table}'");
	if($rs->num_rows == 0) $this->db->query($create_query);
    }
    
    /**
     * Make sure all field definitions are up to date
     */
    private function ensure_column_definitions(){
	$existing_schema = array();
	$deleted_fields = array();
        $rs = $this->db->query("SHOW COLUMNS FROM {$this->table}");
	foreach($rs->result_array() as $column){
	    $deleted_fields[$column['Field']] = 1;
	    $existing_schema[$column['Field']] = $column;
	}
	
	unset($rs);
	$new_fields = array();
	$altered_fields = array();
	foreach($this->schema as $field){
	    //Do we need a new field?
	    if(!isset($existing_schema[$field->name])){
		$new_fields[] = $this->_get_field_string_from_schema($field);
		continue;
	    }
	    //Has the field been altered?
	    unset($deleted_fields[$field->name]);
	    foreach($existing_schema[$field->name] as $attribute => $value){
		if($attribute == 'Field') continue;
		if(empty($value)) $value = null;
		
		$attribute = strtolower($attribute);
		if($value == $field->{$attribute}) continue;
		
		$altered_fields[] = $field->name." ".$this->_get_field_string_from_schema($field);
	    }
	}
	
	//Do we need to change the schema?
	if(!empty($new_fields) || !empty($altered_fields)){
	    $query = "ALTER TABLE {$this->table}\n";
	    if(!empty($new_fields)){
		$query .= "ADD COLUMN ".implode(",\n"."ADD COLUMN ", $new_fields)."\n";
		unset($new_fields);
	    }
	    if(!empty($altered_fields)){
		$query .= "CHANGE ".implode(",\n"."CHANGE ", $altered_fields)."\n";
		unset($altered_fields);
	    }
	}
    }
    
    /**
     * Make sure all of the indexes are up to date
     * 
     * @param string $create_query - query for table creation
     */
    private function ensure_indexes($create_query){
	$existing_indexes = array();
	$deleted_indexes = array();
	$rs = $this->db->query("SHOW INDEXES FROM {$this->table}");
	foreach($rs->result_array() as $index){
	    if($index['Key_name'] == 'PRIMARY') continue;
	    $existing_indexes[$index['Key_name']][] = $index['Column_name'];
	    $deleted_indexes[$index['Key_name']] = 1;
	}
	
	$altered_indexes = 0;
	foreach($this->indexes as $name => $columns){
	    if(!isset($existing_indexes[$name])){
		$altered_indexes++;
		continue;
	    }
	    
	    unset($deleted_indexes[$name]);
	    if($existing_indexes[$name] === $columns) continue;
	    $altered_indexes++;
	}
	
	if(!empty($deleted_indexes)){
	    foreach($deleted_indexes as $name => $unused){
		$this->db->query("DROP INDEX {$name} ON {$this->table}");
	    }
	}
	
	if($altered_indexes > 0){
	    //make a temp table with the new structure
	    $create_query = str_replace($this->table,$this->table.'_temp',$create_query);
	    $this->db->query($create_query);
	    
	    //add the Indexes
	    $indexes = array();
	    foreach($this->indexes as $name => $columns){
		$indexes[] = "{$name} ('{$columns}')";
	    }
	    
	    $query = "ALTER TABLE {$this->table}_tmp\n";
	    $query .= "ADD INDEX ".implode(",\n"."ADD INDEX ", $indexes)."\n";
	    $this->db->query($query);
	    
	    //copy the data to the new table
	    $this->copy_table_data($this->table, $this->table.'_tmp');
	    
	    //Drop the old table
	    $this->db->query("
		RENAME TABLE 
		{$this->table} TO {$this->table}_old, 
		{$this->table}_tmp TO {$this->table}
	    ");
	    
	    $this->db->query("DROP TABLE {$this->table}_old");
	}
    }
    
    /**
     * Copy data from one table to another
     */
    private function copy_table_data($source, $destination){
	$limit = 100;
	$offset = 0;
	do{
	    $rows = $this->db->query("
		SELECT * FROM {$source}
		LIMIT {$offset},{$limit}
	    ")->result_array();

	    foreach($rows as $row){
		$keys = implode(',',array_keys($row));
		$values = "'".implode("','",$row)."'";
		$db->query("INSERT INTO {$destination} ({$keys}) VALUES ($values)");
	    }
	} while(!empty($rows));
    }
    
    private function _get_field_string_from_schema(fieldSchema $item)
    {
	$field_data = array();
	$field_data[] = $item->type;
	$field_data[] = $item->name;
	
	if($item->null === false) $field_data[] = 'NOT NULL';
	if(!is_null($item->extra)) $field_data[] = $item->extra;
	if(!is_null($item->default)) $field_data[] = 'DEFAULT '.$item->default;
	if($item->key === 'PRI') $field_data[] = 'primary key';
	
	return implode(' ',$field_data);
    }
    
    /**
     * Set the data to be processed
     */
    public function setData(array $data){
	$this->data = $data;
    }
    
    private function _perform_callback($callback, $field_name, $params){
	array_unshift($params,$this->data[$field_name],$this->data);
	return call_user_func_array($callback, $params);
    }
    
    /**
     * Upsert an instance of an entity
     * 
     * @param mixed $data - Typically a struct passed in by entity-specific models
     * @param coreMetaStruct $meta
     */
    public function upsert(coreMetaStruct $meta){
        $this->_filter();
        
        //ensure that the data matches the schema (attempt to convert delinquent values)
        
        //upsert the data based on meta upsert conditions (default to id)
        
        //return the id of the upserted entity
    }
    
    /**
     * Perform Error Checking (Validation)
     * 
     * @param array $data
     * @return array $errors
     */
    public function validate(){
	$errors = array();
	foreach($this->schema as $field){
	    foreach($field->validation as $callback){
		$field_error = $this->_perform_callback($callback[0], $field->name, $callback[1]);
		if(!empty($field_error)){
		    $errors[$field->name][] = $field_error;
		}
	    }
	}
	
	return $errors;
    }
    
    /**
     * Clean up the inputed data
     * ..Typically extended by entity-specific models
     * 
     * @param mixed $data
     */
    public function _filter(){
        //follow any defined filter callbacks
        foreach($this->schema as $field){
	    $parsers = $this->global_parsers + $field->parsers;
	    foreach($parsers as $callback){
		$this->_perform_callback($callback[0], $field->name, $callback[1]);
	    }
	}
    }
    
    /**
     * Map to any apis
     */
    public function _map(){
        //look up and perform mapping actions
    }
}

/**
 * This class is a dependency of the core model
 */
class coreMetaStruct{
    public $name;
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
    public $type;
    public $null;
    public $key;
    public $default;
    public $extra;
    public $attributes = '';
    public $validation = array();
    public $parsers = array();
    public $triggers = array();
    
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
}