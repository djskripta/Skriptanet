<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once BASE_PATH.'application/models/core_model.php';
require_once APPPATH.'third_party/OAuth2/Autoloader.php';
OAuth2\Autoloader::register();

class Oauth2_model extends Core_model
{
    private $server;
    
    public function __construct()
    {
        //init oauth vars
        $this->config->load('database');
        $dsn = "mysql:dbname={$this->config->item('database')};host={$this->config->item('hostname')}";
        $username = $this->config->item('username');
        $password = $this->config->item('password');
        
        $storage = new OAuth2\Storage\Pdo(array(
            'dsn' => $dsn, 
            'username' => $username, 
            'password' => $password
        ));

        //init Oauth2 server
        $this->server = new OAuth2\Server($storage, array(
            'allow_implicit' => true,
        ));
        
        $this->server->addGrantType(new OAuth2\GrantType\ClientCredentials($storage));
        $this->server->addGrantType(new OAuth2\GrantType\AuthorizationCode($storage));
    }
    
    public function update_schema() {
        //oauth_clients
        $schema = array();
        $schema[] = new fieldSchema('client_id', 'VARCHAR(80)', false);
        $schema[] = new fieldSchema('client_secret', 'VARCHAR(80)', false);
        $schema[] = new fieldSchema('redirect_uri', 'VARCHAR(200)', false);
        $schema[] = new fieldSchema('grant_types', 'VARCHAR(80)');
        $schema[] = new fieldSchema('scope', 'VARCHAR(100)');
        $schema[] = new fieldSchema('user_id', 'VARCHAR(80)');
        
        /**
         * @TODO: make base controller handle constraints
         * http://bshaffer.github.io/oauth2-server-php-docs/cookbook/
         */
        $schema_constraints = array(
            'clients_client_id_pk' => 'PRIMARY KEY (client_id)'
        );
        
        $this->_update_single_schema('oauth_clients', $schema, $schema_constraints);
        
        //oauth_access_tokens
        $schema = array();
        $schema[] = new fieldSchema('access_token', 'VARCHAR(40)', false);
        $schema[] = new fieldSchema('client_id', 'VARCHAR(80)', false);
        $schema[] = new fieldSchema('user_id', 'VARCHAR(255)', false);
        $schema[] = new fieldSchema('expires', 'TIMESTAMP', false);
        $schema[] = new fieldSchema('scope', 'VARCHAR(200)', false);
        
        $schema_constraints = array(
            'access_token_pk' => 'PRIMARY KEY (access_token)'
        );
        
        $this->_update_single_schema('oauth_access_tokens', $schema, $schema_constraints);
        
        //oauth_authorization_codes
        $schema = array();
        $schema[] = new fieldSchema('authorization_code', 'VARCHAR(40)', false);
        $schema[] = new fieldSchema('client_id', 'VARCHAR(80)', false);
        $schema[] = new fieldSchema('user_id', 'VARCHAR(255)', false);
        $schema[] = new fieldSchema('redirect_url', 'VARCHAR(200)', false);
        $schema[] = new fieldSchema('expires', 'TIMESTAMP', false);
        $schema[] = new fieldSchema('scope', 'VARCHAR(200)', false);
        
        $schema_constraints = array(
            'auth_code_pk' => 'PRIMARY KEY (authorization_code)'
        );
        
        $this->_update_single_schema('oauth_authorization_codes', $schema, $schema_constraints);
        
        //oauth_refresh_tokens
        $schema = array();
        $schema[] = new fieldSchema('refresh_token', 'VARCHAR(40)', false);
        $schema[] = new fieldSchema('client_id', 'VARCHAR(80)', false);
        $schema[] = new fieldSchema('user_id', 'VARCHAR(255)', false);
        $schema[] = new fieldSchema('expires', 'TIMESTAMP', false);
        $schema[] = new fieldSchema('scope', 'VARCHAR(200)', false);
        
        $schema_constraints = array(
            'refresh_token_pk' => 'PRIMARY KEY (refresh_token)'
        );
        
        $this->_update_single_schema('oauth_refresh_tokens', $schema, $schema_constraints);
        
        //oauth_users
        $schema = array();
        $schema[] = new fieldSchema('username', 'VARCHAR(255)', false);
        $schema[] = new fieldSchema('password', 'VARCHAR(200)', false);
        $schema[] = new fieldSchema('first_name', 'VARCHAR(255)', false);
        $schema[] = new fieldSchema('last_name', 'VARCHAR(255)', false);
        
        $schema_constraints = array(
            'username_pk' => 'PRIMARY KEY (username)'
        );
        
        $this->_update_single_schema('oauth_users', $schema, $schema_constraints);
        
        //oauth_scopes
        $schema = array();
        $schema[] = new fieldSchema('scope', 'TEXT');
        $schema[] = new fieldSchema('is_default', 'BOOLEAN');
        $this->_update_single_schema('oauth_scopes', $schema);
        
        //oauth_jwt
        $schema = array();
        $schema[] = new fieldSchema('client_id', 'VARCHAR(80)', false);
        $schema[] = new fieldSchema('subject', 'VARCHAR(80)', false);
        $schema[] = new fieldSchema('public_key', 'VARCHAR(200)', false);
        
        $schema_constraints = array(
            'jwt_client_id_pk' => 'PRIMARY KEY (client_id)'
        );
        
        $this->_update_single_schema('oauth_jwt', $schema, $schema_constraints);
    }
    
    private function _update_single_schema($table, $schema, $schema_constraints = array())
    {
        $this->table = $table;
        $this->schema = $schema;
        $this->schema_constraints = $schema_constraints;
        parent::update_schema(false);
    }
    
    public function set_user_data($user_data = array())
    {
        $user_data = array(
            'client_id' => 'testclient',
            'client_secret' => 'testpass',
            'redirect_uri' => 'http://www.google.com',
        );
        
        $this->load->model('core/oauth2_user_model');
        $this->oauth2_user_model->setData($user_data);
        $errors = $this->oauth2_user_model->validate();
        
        if(empty($errors)){
            $user_id = $this->oauth2_user_model->insert();
            print $user_id;
        } else {
            print_r($errors);
        }
    }
    
    public function get_token()
    {
        $this->load->helper('common');
        
        $response = ob_get_response(array(
            $this->server->handleTokenRequest(OAuth2\Request::createFromGlobals()),
            'send'
        ));
        
        $result = json_decode($response,true);
        if(!$result['access_token'] || empty($result['access_token'])){
            throw new core_exception('Invalid Credentials');
        }
        
        return $response;
    }
    
    public function check_token()
    {
        if (!$this->server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $this->load->helper('common');
            $res = ob_get_response(array($this->server->getResponse(),'send'));
            throw new core_exception('Token Auth Failed: '.$res);
        }
        
        print json_encode(array(
            'success' => true, "message" => "Foo Bar"
        ));
        
        return true;
    }
}