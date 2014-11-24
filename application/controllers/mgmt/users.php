<?php
require_once(dirname(__FILE__).'/mgmt_base.php');

/* 
 * @company SkriptaNet
 * @author Nikita Nikitin
 */

class Users extends Mgmt_base {
    protected $model = 'user_model';
    
    public function __construct(){
        parent::__construct();
	$this->load->model('core/'.$this->model); //should already have been done by this point
    }
    
    /**
     * Perform authentication checks
     * @return mixed
     */
    public function index(){
	$data = array();
	
	//get project data from a model
	$data['results'] = $this->{$this->model}->get();
	
	$this->set_data($data);
	$this->load_view('mgmt/users');
    }
    
    /**
     * Create new project
     */
    public function create(){
	$schema = $this->{$this->model}->get_schema();
	
	$post_data = array();
	foreach($schema as $field){
	    if(isset($_POST[$field->name])){
		$post_data[$field->name] = $_POST[$field->name];
	    }
	}
	
	$this->load->helper(array('form', 'url'));
	$this->load->library('form_validation');
	
	$errors = array();
	
	if(!empty($_POST)){
	    $this->{$this->model}->set_data($post_data);
	    $errors = $this->{$this->model}->validate();

	    foreach($errors as $field => $error){
		$this->form_validation->set_message($field, $error);
	    }
	}
	
	$this->set_data(array('model' => $this->model, 'schema' => $schema, 'errors' => $errors));
	if(empty($_POST) || !empty($errors)){
	    $this->load_view('mgmt/basic_form');
	    return;
	}
	
	//Insert
	$meta = new coreMetaStruct();
	print $this->{$this->model}->upsert($meta).'..';
	print 'No Errors';
    }
}