<?php
require_once(APPPATH.'controllers/base_controller.php');
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Mgmt_base extends Base_Controller {
    protected $model;
    
    public function __construct(){
        parent::__construct();
	$this->check_access();
	$this->load->model('core/'.$this->model);
    }
    
    /**
     * Perform authentication checks
     * @return mixed
     */
    public function check_access(){
	return true;
    }
}