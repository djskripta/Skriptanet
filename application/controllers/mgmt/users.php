<?php
require_once(dirname(__FILE__).'/mgmt_base.php');

/* 
 * @company SkriptaNet
 * @author Nikita Nikitin
 */

class Users extends Mgmt_base {
    protected $model = 'user_model';
    //protected $ignore_acl = true;
    
    public function __construct(){
        parent::__construct();
	$this->controller_path .= '/'.strtolower(__CLASS__);
    }
}