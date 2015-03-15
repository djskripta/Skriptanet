<?php
require_once BASE_PATH.'application/models/core_model.php';
class User_model extends Core_model{
    public function __construct(){
	$this->name = 'User';
        $this->table = 'users';
	$this->display_key = 'email';
	$this->schema = array();
	
	$field = new fieldSchema('id', 'int(11)', false, 'PRI', null, 'auto_increment');
	$field->label = 'ID';
	$this->schema[] = $field;
	
	$field = new fieldSchema('identity_id', 'int(11)', false, 'MUL');
	$field->label = 'Identity ID';
	$this->schema[] = $field;
	
	$field = new fieldSchema('email', 'varchar(255)', false, 'MUL');
	$field->label = 'Email';
	$field->addValidator('this::email');
	$field->addValidator('this::unique');
	$this->schema[] = $field;
	
	$field = new fieldSchema('password', 'varchar(255)');
	$field->label = 'Password';
	$field->field_type = 'password';
	$field->ignore_blank_on_update = true;
	$field->addValidator('this::password');
	$field->addParser('this::blowfish_encrypt');
	$this->schema[] = $field;
	
	$field = new fieldSchema('permissions', 'varchar(255)', false, null, 0);
	$field->label = 'Permissions';
	$field->addValidator('this::valid_permissions');
	$field->addParser('this::permission_bit');
	$this->schema[] = $field;
	
	$this->indexes = array(
	    'identity_id' => array('identity_id'),
	    'email' => array('email'),
	);
	
	$this->external_schema['project'] = array('id' => 'user_id');
        
        parent::__construct();
    }
    
    public function upsert(coreMetaStruct $meta){
        parent::upsert($meta);
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
	$this->load->helper('crypt');
	
	$user = $this->user_model->get(array('email' => $username));
	if(empty($user)){
	    return "This username is not registered";
	}
	
	$user = array_shift($user);	
        $res = blowfish_compare($password, $user['password']);
	if(!$res){
	    return "Invalid Password";
	}
	
	$this->load->library('session');
	$this->session->set_userdata('user_data',$user);
	return true;
	
        $callback_response = new callbackResponseStruct();
        $callback_response->status = 1;
        
        return $callback_response;
    }
    
    public function _callback_email($field, $data){
	
    }
    
    public function _callback_password($field, $data){
	if($this->update_mode && empty($data[$field])) return;
    }
    
    public function _callback_valid_permissions($field, $data){
	
    }
    
    public function _callback_blowfish_encrypt($field, $data){
	$this->load->helper('crypt_helper');
	return blowfish_encode($data[$field]);
    }
    
    public function _callback_permission_bit($field, $data){
	return $data[$field];
    }
}
?>

