<?php
require_once BASE_PATH.'application/models/core_model.php';
class Session_model extends Core_model{
    public function __construct(){
	$this->name = 'Session';
        $this->table = 'session';
	$this->schema = array();
        
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
	
    }
}
?>

