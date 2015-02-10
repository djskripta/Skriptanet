<?php
require_once(APPPATH.'controllers/base_controller.php');
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Mgmt_base extends Base_Controller {
    protected $ignore_acl = false;
    protected $model;
    public $controller_path;
    protected $list_view = 'mgmt/basic_list';
    protected $form_view = 'mgmt/basic_form';
    private $session_data = array();
    
    public function __construct(){
        parent::__construct();
	
	$this->load->helper(array('url','form'));
	$this->load->model('core/'.$this->model);
	
	$path_parts = explode('\\',str_replace(FCPATH,'',__FILE__));
	$path_parts = array_slice($path_parts,2,-1);
	$this->controller_path = site_url(implode('/',$path_parts));
	
	if(!$this->ignore_acl){
	    $this->check_access();
	}
	
	$this->schema = $this->{$this->model}->get_schema();
	$this->set('primary_key',$this->{$this->model}->primary_key);
	$this->set('entity_type',$this->{$this->model}->name);
    }
    
    /**
     * Show list view for model
     * @return mixed
     */
    public function index(){
	//get project data from a model
	$this->set('results',$this->{$this->model}->get());
	$this->load_view($this->list_view);
    }
    
    /**
     * Create new entity
     */
    public function create(){
	$this->_validate();
	
	if(empty($_POST) || !empty(validation_errors())){
	    $this->load_view($this->form_view);
	    return;
	}
	
	//Insert
	$meta = new coreMetaStruct();
	$id = $this->{$this->model}->upsert($meta);
	redirect($this->controller_path);
    }
    
    /**
     * Edit an entity
     */
    public function edit($id){
	$this->{$this->model}->set_id($id);
	$entity = $this->{$this->model}->get_by_id();
	
	$this->_validate($id);
	
	$this->set('entity',$entity);
	if(empty($_POST) || !empty(validation_errors())){
	    $this->load_view($this->form_view);
	    return;
	}
	
	//Update
	$meta = new coreMetaStruct();
	$id = $this->{$this->model}->upsert($meta);
	redirect($this->controller_path);
    }
    
    private function _validate($id = null){
	$this->load->library('form_validation');
	
	$post_data = array();
	foreach($this->schema as $field){
	    if(isset($_POST[$field->name])){
		$post_data[$field->name] = $_POST[$field->name];
		$this->form_validation->set_rules($field->name);
	    }
	}
	
	$errors = array();
	$update = false;
	
	if(!empty($_POST)){
	    $this->{$this->model}->set_data($post_data);
	    
	    if(!is_null($id)){
		$this->{$this->model}->set_id($id);
		$update = true;
	    }
	    $errors = $this->{$this->model}->validate($update);

	    $this->form_validation->run();
	    foreach($errors as $field => $error){
		$this->form_validation->set_error($field, implode('<br />',$error));
	    }
	}
    }
    
    /**
     * Perform authentication checks
     * @return mixed
     */
    public function check_access(){
	$this->load->library('session');
	$session_data = $this->session->all_userdata();
	
	if(!empty($session_data['user_data'])){
	    return true;
	}
	
	$this->session->set_flashdata('rurl',current_url());
	redirect($this->controller_path.'/login');	
	
//	$this->load->library('cimongo/Cimongo');
//	$this->cimongo->switch_db('skriptanet');
//	$this->cimongo->insert('session',array('session_id' => '0'));
	
	//redirect();
	return true;
    }
}