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
    public $base_path;
    protected $list_view = 'mgmt/basic_list';
    protected $form_view = 'mgmt/basic_form';
    protected $entity_view = 'mgmt/basic_entity';
    
    public function __construct(){
        parent::__construct();
	
	$this->load->helper(array('url','form'));
	$this->load->model('core/'.$this->model);
	
	$path_parts = explode('\\',str_replace(FCPATH,'',__FILE__));
	$path_parts = array_slice($path_parts,2,-1);
	$this->controller_path = site_url(implode('/',$path_parts));
	$this->base_path = $this->controller_path;
	
	if(!$this->ignore_acl){
	    $this->check_access();
	}
	
	$this->schema = $this->{$this->model}->get_schema();
	$this->set('primary_key',$this->{$this->model}->primary_key);
	$this->set('display_key',$this->{$this->model}->display_key);
	$this->set('entity_type',$this->{$this->model}->name);
	$this->set('schema',$this->schema);
	$this->set('external_schema',$this->{$this->model}->get_external_schema());
	
	$this->set('title','SkriptaNet - MGMT');
	$this->set('nav_links',array());
    }
    
    /**
     * Show list view for model
     * @return mixed
     */
    public function index(){
	//get project data from a model
	$this->set('title',$this->{$this->model}->name.'s');
	$this->set('nav_links',array(
	    $this->controller_path.'/create' => 'file',
	));
	
	$this->set('results',$this->{$this->model}->get());
	$this->load_view($this->list_view);
    }
    
    /**
     * Show the object
     */
    public function view($id = null){
	$entity = $this->{$this->model}->get_by_id($id);
	$this->set('entity',$entity);
	$this->set('title',$this->{$this->model}->name.' Details');
	$this->set('nav_links',array(
	    $this->controller_path.'/edit/'.$entity['id'] => 'edit',
	    $this->controller_path => 'home',
	));
	
	/**
	 * Internal Schema
	 */
	$parsed_fields = array();
	$parsed_links = array();
	$timestamps = array();
	
	$id_field = array_shift($this->schema);
	
	foreach($this->schema as $field)
	{
	    if($field->type == fieldSchema::TYPE_TIMESTAMP){
		$timestamps[$field->label] = date('Y-m-d', strtotime($entity[$field->name]));
		continue;
	    }
	    
	    if(empty($field->db_links)){
		$value = str_replace("\n","<br />",$entity[$field->name]);
		$parsed_fields[$field->label] = $value;
		continue;
	    }
	    
	    $parsed_values = array();
	    foreach($field->db_links as $table => $foreign_key)
	    {
		$model_name = substr($table,-1) == 's' ? substr($table,0,-1) : $table;
		$model_name .= '_model';
		$this->load->model("core/{$model_name}",$model_name);

		$parsed_values = array_merge(
		    $parsed_values, 
		    $this->{$model_name}->get_linked_entities(
			$entity,
			$field->name,
			$foreign_key,
			$this->base_path
		    )
		);
	    }

	    $value = implode("<br />", $parsed_values);
	    $parsed_links[$field->label] = $value;
	}
	
	/**
	 * External Schema
	 */
	$external_schema = $this->{$this->model}->get_external_schema();
	foreach($external_schema as $field => $link){
	    $model_name = $field.'_model';
	    $this->load->model("core/{$model_name}",$model_name);

	    foreach($link as $key => $foreign_key)
	    {
		$parsed_values = $this->{$model_name}->get_linked_entities(
		    $entity,
		    $key,
		    $foreign_key,
		    $this->base_path
		);
		
		$entity_type = $this->{$model_name}->name;
		$field_label = "Assoc. {$entity_type}(s)";
		$parsed_links[$field_label] = implode('<br />',$parsed_values);
	    }
	}
	
	$row_data = '<div class="row attributes">';
	//$row_data .= '<div class="col-sm-3"><b>'.$id_field->label.':</b> '.$entity[$id_field->name].'</div>';
	foreach($timestamps as $field_name => $field_value){
	    $row_data .= '<div class="col-xs-6"><b>'.$field_name.'</b>: '.$field_value.'</div>';
	}
	$row_data .= '</div>';
	
	$first_row = array(
	    "{$id_field->label}: {$entity[$id_field->name]}" => $row_data,
	);
	
	$this->set('parsed_fields',array_merge($first_row,$parsed_fields,$parsed_links,$timestamps));
	$this->load_view($this->entity_view);
    }
    
    /**
     * Create new entity
     */
    public function create(){
	$this->_validate();
	
	$this->set('title','Create '.$this->{$this->model}->name);
	$this->set('nav_links',array(
	    $this->controller_path => 'home',
	));
	
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
	$this->set('title','Edit '.$this->{$this->model}->name);
	$this->set('nav_links',array(
	    $this->controller_path.'/view/'.$entity['id'] => 'eye-open',
	    $this->controller_path => 'home',
	));
	
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
	    $this->set('user_data',$session_data['user_data']);
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
    
    public function get_model_name(){
	return $this->model;
    }
}