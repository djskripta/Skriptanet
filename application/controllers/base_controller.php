<?php
/**
 * The base controller extended by all app controllers
 * All common behaviors and global vars should be defined here
 */
 
class Base_controller extends CI_Controller {
    private $data;
    private $access;
    
    public function __construct(){
        parent::__construct();
    }
    
    /**
     * 
     * @param string $view_path
     * @param string $template
     * 
     * @todo Integrate with Smarty
     */
    public function load_view($view_path, $template = null){
        //buffer the view
        ob_start();
        $this->load->view($view_path, $this->data);
        $content = ob_get_clean();
        
        //insert the view into the template
        if(!is_null($template)){
            ob_start();
            $this->load->view('templates/'.$template, $this->data);
            $template_content = ob_get_clean();

            print str_replace('[%content%]',$content,$template_content);
            return;
        }
        
        print $content;
    }
    
    /**
     * Perform all access control checks
     */
    public function _acl(){
        
    }
}
?>