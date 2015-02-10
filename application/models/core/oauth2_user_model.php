<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once BASE_PATH.'application/models/core_model.php';

class Oauth2_user_model extends Core_model
{    
    public function __construct()
    {
        $this->table = 'oauth_clients';
        $this->schema = array();
        
        $field = new fieldSchema('client_id', 'VARCHAR(80)', false);
        $field->addValidator('this::auth_credential');
	$this->schema[] = $field;
        
        $field = new fieldSchema('client_secret', 'VARCHAR(80)', false);
        $field->addValidator('this::auth_credential');
	$this->schema[] = $field;
        
        $field = new fieldSchema('redirect_uri ', 'VARCHAR(200)', false);
	$this->schema[] = $field;
        
        $field = new fieldSchema('grant_types ', 'VARCHAR(80)');
	$this->schema[] = $field;
        
        $field = new fieldSchema('scope ', 'VARCHAR(100)');
	$this->schema[] = $field;
        
        $field = new fieldSchema('user_id ', 'VARCHAR(80)');
	$this->schema[] = $field;
        
        $this->schema_constraints = array(
            'clients_client_id_pk' => 'PRIMARY KEY (client_id)'
        );
        
        parent::__construct();
    }
    
    public function _callback_auth_credential($field, $data){
        return preg_match('/^[\s\w]{4,16}/ui',$data[$field]);
    }
}