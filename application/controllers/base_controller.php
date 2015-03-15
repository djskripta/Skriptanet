<?php
/**
 * The base controller extended by all app controllers
 * All common behaviors and global vars should be defined here
 */
 
class Base_controller extends CI_Controller {
    private $data;
    private $access;
    private $nav_links;
    
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
    public function load_view($view_path, $template = 'basic_page'){
        //buffer the view
        ob_start();
        $this->load->view($view_path, $this->data);
        $content = ob_get_clean();
        
        //insert the view into the template
        if(!is_null($template)){	    
	    $username = !isset($this->data['user_data']) ? '' : $this->data['user_data']['email'];
	    
	    $replacements = array(
		'content' => $content,
		'username' => $username,
		'title' => $this->data['title'],
		'nav_links' => $this->data['nav_links'],
	    );
	    
	    $this->load->view('templates/'.$template, $replacements);
            return;
        }
        
        print $content;
    }
    
    /**
     * Perform all access control checks
     */
    public function _acl(){
        
    }
    
    /**
     * Set a data attribute
     * @todo Allow for multi-dimensional keys
     * @param string $key
     * @param string $value
     */
    public function set($key,$value){
	$this->data[$key] = $value;
    }
    
    public function set_data($data){
	$this->data = $data;
    }
}
?>