<?php
require_once(dirname(__FILE__).'/mgmt_base.php');

/* 
 * @company SkriptaNet
 * @author Nikita Nikitin
 */

class Projects extends Mgmt_base {
    protected $model = 'project_model';
    
    public function __construct(){
        parent::__construct();
	$this->controller_path .= '/'.strtolower(__CLASS__);
    }
}