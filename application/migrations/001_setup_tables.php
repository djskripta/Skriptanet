<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Setup_tables extends CI_Migration {

	public function up(){
		/**
		 * Load models for all our objects
		 * Get their schemas (or updates to schemas).. also ensure proper indexes
		 * Create the tables
		 */
		$this->dbforge->add_field(array(
			'blog_id' => array(
				'type' => 'INT',
				'constraint' => 5,
				'unsigned' => TRUE,
				'auto_increment' => TRUE
			),
			'blog_title' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
			),
			'blog_description' => array(
				'type' => 'TEXT',
				'null' => TRUE,
			),
		));

		$this->dbforge->create_table('blog');
		
		$this->load->library('cimongo/Cimongo');
		$this->cimongo->switch_db('skriptanet');
	}

	public function down(){
		$this->dbforge->drop_table('blog');
	}
}