<?php
require_once(dirname(__FILE__).'/mgmt_base.php');

/* 
 * @company SkriptaNet
 * @author Nikita Nikitin
 */

class Login extends Mgmt_base {
    protected $model = 'user_model';
    
    public function __construct(){
	$this->ignore_acl = true;
        parent::__construct();
	$this->controller_path .= '/'.strtolower(__CLASS__);
    }
    
    public function index(){
	$this->_validate();
	
	if(empty($_POST) || !empty(validation_errors())){
	    $this->load_view('mgmt/login');
	    return;
	}
	
	$this->load_view('mgmt/login');
    }
    
    public function _validate(){
	$this->load->library('session');
	$rurl = $this->session->flashdata('rurl');
	$this->session->set_flashdata('rurl',$rurl);
	
	if(empty($_POST)){
	    return;
	}
	
	$this->load->model('core/user_model');
	$user = isset($_POST['username']) ? $_POST['username'] : '';
	$pass = isset($_POST['password']) ? $_POST['password'] : '';
	
	$res = $this->user_model->log_in(
	    $user,
	    $pass
	);
	
	if($res === true){
	    redirect($rurl);
	}
    }
}