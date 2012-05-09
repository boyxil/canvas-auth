<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
	// PHP hash_algos lists available algorithms
	'security' => array(
		'hash' => 'sha256',
		'salt' => 'Ejkfsdinw323289329010fsdjswi8jkvscvnm83n3+!',
	),
	
	// Authentication handler
	'handlers' => array(
		'mysql' => array(
			// Name of class to act as handler
			'class' => 'Canvas_Auth_Handler_Mysql',
			
			// Database configuration
			'database_config' => 'default',
			
			// Name of the users table
			'table_name' => 'users',
			
			// Fields data mappings for username and password
			'field_mappings' => array(
				'user_id' => 'id',
				'username' => 'username',
				'password' => 'password',
				'is_enabled' => 'is_enabled',
			),
			
			// additional fields to retrieve
			'data_fields' => array(
				'email',
			),
		),
		'file' => array(
			'class' => 'Canvas_Auth_Handler_File',
			
			// Fields data mappings for username and password
			'field_mappings' => array(
					'user_id' => 'id',
					'username' => 'username',
					'password' => 'password',
					'is_enabled' => 'is_enabled',
			),
			
			'user_map' => array(
				array(
					'id' => '1',
					'username' => 'admin',
					'password' => '1cf8f181a9e9a2d4edda63be27de5d4a2f827acef9ebe7b5431c7e030a79c0bb',
					'is_enabled' => '1',
				),
			),
		),
	),
	
	// Session data
	'session' => array(
		'key' => 'cnvs',
	),
	
	// Callbacks
	'callback' => array(
		'login_success' => array(),
		'login_fail' => array(),
		'logout' => array(),
	),
);
