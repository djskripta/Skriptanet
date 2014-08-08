<?php
require_once BASE_PATH.'application/models/core_model.php';
class User_model extends Core_model{    
    public function __construct(){
        $this->table = 'users';
	$this->schema = array();
	
	$field = new fieldSchema('id', 'int(11)', false, 'PRI', null, 'auto_increment');
	$this->schema[] = $field;
	
	$field = new fieldSchema('identity_id', 'int(11)', false, 'MUL');
	$this->schema[] = $field;
	
	$field = new fieldSchema('email', 'varchar(255)', false, 'MUL');
	$field->addValidator('email');
	$field->addValidator('unique');
	$this->schema[] = $field;
	
	$field = new fieldSchema('password', 'varchar(255)');
	$field->addValidator('password');
	$field->addParser('blowfish_encrypt');
	$this->schema[] = $field;
	
	$field = new fieldSchema('permissions', 'varchar(255)', false, null, 0);
	$field->addValidator('valid_permissions');
	$field->addParser('permission_bit');
	$this->schema[] = $field;
	
	$this->indexes = array(
	    'identity_id' => array('identity_id'),
	    'email' => array('email'),
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
    
    public function check_access($page){
        //make sure the user has access to the page
    }
    
    /**
     * Try to log the user in.. their username can be a username or an email
     * 
     * @param string $username
     * @param string $password
     * 
     * @return callbackResponseStruct indication of status, error messages, and data
     */
    public function log_in($username, $password){
        //verify the username or email against the password hash
        
        $callback_response = new callbackResponseStruct();
        $callback_response->status = 1;
        
        return $callback_response;
    }
    
    private function _callback_email($field, $data){
	
    }
    
    private function _callback_unique($field, $data){
	
    }
    
    private function _callback_password($field, $data){
	
    }
    
    private function _callback_valid_permissions($field, $data){
	
    }
    
    private function _callback_blowfish_encrypt($field, &$data){
	
    }
    
    private function _callback_permission_bit($field, &$data){
	
    }
}
?>

