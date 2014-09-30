<?php
class Api_model extends CI_Model {
    private $response = array();
    
    public function __construct(){
        parent::__construct();
        $this->load->helper('common');
        $request_method = $_SERVER['REQUEST_METHOD'];
        $params = $_{$request_method};       
        
        $this->perform_action(
            $request_method, 
            $this->url->segment(1), 
            $params
        );
        
        exit;
    }
    
    public function auth(){
        $this->load->model('core/oauth2_model');
        $token = $this->oauth2_model->get_token();
        
        return $token;
    }
    
    private function perform_action($method, $action, $params){
        if(!isset($params['token'])){
            $this->throw_exception('Null or Invalid Token');
        }
        //look up model based on action
        if(!file_exists(APPPATH."models/core/{$action}_{$model}.php")){
            $this->throw_exception('Invalid API Action');
        }
        
        $this->oauth2_model->check_token();
        $model_name = "{$action}_{$model}";
        $this->load->model("core/{$model_name}");
        
        //enforce permissions for action
        if(!$this->{$model_name}->get_is_accessible($action, $params)){
            throw_exception('Action not Accessible');
        }
        
        //now call the action
    }
    
    private function throw_exception($error = 'There is an error'){
        print 'Error: <b>'.$error.'</b>'."<br />\n";
        exit;
    }
}
?>