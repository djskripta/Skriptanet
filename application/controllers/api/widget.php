<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Widget extends CI_Controller {
    
    public function __construct(){
        parent::__construct();
    }
    
    public function index(){
        //process restful operations
    }
    
    public function test(){
        $this->load->model('core/oauth2_model');
        $this->load->model('core/oauth2_user_model');
        //$this->oauth2_model->update_schema();
        //$this->oauth2_model->set_user_data();
        
        $post_data = 'grant_type=client_credentials';
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://localhost/moneytree/api/widget/test_auth');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, "testclient:testpass");
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_POST, count($post_data));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $output = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
        
        print $output."<br />\n";
        $output = json_decode($output,true);
        
        $access_token = $output['access_token'];
        $access_token = '50fb4280e51528e6d852743c9f5fca07202711ba';
        
        //now try to authenticate with the token
        $this->load->helper('common');
        $res = curl_get('http://localhost/moneytree/api/widget/test_token_auth?access_token='.$access_token);
        print $res;
        
        return;
    }
    
    public function test_auth(){
        $this->load->model('core/oauth2_model');
        $token_data = $this->oauth2_model->get_token();
        print $token_data;
    }
    
    public function test_token_auth(){
        $this->load->model('core/oauth2_model');
        return $this->oauth2_model->check_token() ? '1' : '0';
    }
}