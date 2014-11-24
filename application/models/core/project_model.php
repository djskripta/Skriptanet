<?php
require_once BASE_PATH.'application/models/core_model.php';
class Project_model extends Core_model{    
    public function __construct(){
        $this->table = 'projects';
	$this->schema = array();
	
	$field = new fieldSchema('id', 'int(11)', false, 'PRI', null, 'auto_increment');
	$field->label = 'ID';
	$this->schema[] = $field;
	
	$field = new fieldSchema('user_id', 'int(11)', false, 'MUL');
	$field->label = 'Project Owner';
	$field->addDbLink('users','id');
	$this->schema[] = $field;
	
	$field = new fieldSchema('name', 'varchar(255)');
	$field->label = 'Project Name';
	$this->schema[] = $field;
	
	$field = new fieldSchema('label', 'varchar(255)');
	$field->label = 'URL Slug';
	$this->schema[] = $field;
	
	$field = new fieldSchema('description', 'text');
	$field->label = 'Description';
	$this->schema[] = $field;
	
	$field = new fieldSchema('parent_id', 'int(11)');
	$field->label = 'Parent Project';
	$field->addValidator('this::test');
	$this->schema[] = $field;
	
	$this->indexes = array(
	    'parent_id' => array('parent_id'),
	    'name' => array('name','user_id'),
	    'label' => array('label'),
	);
	
	$this->unique_indexes = array('name','label');
	$this->schema_constraints = array(
            'projects_id_fk' => 'FOREIGN KEY (parent_id) REFERENCES projects (id)',
	    'users_id_fk' => 'FOREIGN KEY (user_id) REFERENCES users (id)'
        );
        
        parent::__construct();
    }
    
    public function upsert($data, coreMetaStruct $meta){
        parent::upsert($data, $meta);
    }
    
    public function _filter(&$data) {
        parent::_filter($data);
    }
    
    public function _map() {
        parent::_map();
    }
    
    public function _callback_test($field, $data){
	return 'Error.. Bad Bar in the Foo';
    }
}
?>

