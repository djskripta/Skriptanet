<?php

/* 
 * @company SkriptaNet
 * @author Nikita Nikitin
 */

class MY_Form_validation extends CI_Form_validation {
    protected $_field_data = array();
    
    public function __construct()
    {
	log_message('debug', '*** Hello from MY_Session ***');
        $this->CI =& get_instance();
        $this->CI->load->library("cimongo/cimongo");
        parent::__construct();
    }
    
    /**
     * Set a custom error message
     * @param type $field
     * @param type $message
     */
    public function set_error($field, $message)
    {
	$this->_field_data[$field]['error'] = $message;
	$this->_error_array[$field] = $message;
    }
    
    /**
     * is_unique
     *
     */
    public function is_unique($str, $field)
    {
    	list($table, $field)=explode('.', $field);
    	$query = $this->CI->cimongo->limit(1)->get_where($table, array($field => $str));
    
    	return $query->num_rows() === 0;
    }
}