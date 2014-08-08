<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once 'base_controller.php';
class Welcome extends Base_controller {

    /*
     * SkriptaNet Index Controller
     */
    public function index(){
	$this->load_view('pages/index','basic_page');
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */