<?php

/**
 * A MySQL based authentication handler.  
 * @package   Canvas
 * @category  Auth
 * @author    Henry Tseng
 * @copyright (c) 2012 Canvas Digital LLC
 */
class Canvas_Auth_Handler_Mysql extends Canvas_Auth_Handler {
	public $name = 'mysql';
	public $_default_fields = array(
		'user_id' => 'user_id',
		'username' => 'username',
		'password' => 'password',
		'is_enabled' => 'is_enabled',
	);
	public $_default_table_name = 'users';
	public $data_fields;
	public $field_list;
	
	protected $_table_name;
	protected $_database_config;
	
	/**
	 * Constructor
	 * @param array $config
	 */
	public function __construct($config) {
		parent::__construct($config);
		
		$database_config = Kohana::$config->load('database');
		$this->_table_name = isset($this->_authconfig['handlers'][$this->name]['table_name']) ? $this->_authconfig['handlers'][$this->name]['table_name'] : $this->_default_table_name;
		$this->_database_config = isset($this->_authconfig['handlers'][$this->name]['database_config']) ? $this->_authconfig['handlers'][$this->name]['database_config'] : 'default';
		
		// Additional data fields
		$this->data_fields = $this->_authconfig['handlers'][$this->name]['data_fields'];
		$this->field_list = array_merge(array_values($this->field_mappings), $this->data_fields);
	}
	
	public function get_userdata($user_id) {
		$query = call_user_func_array(array('DB', 'select'), $this->field_list);
		$query->from($this->_table_name);
		$query->where($this->field_mappings['user_id'], '=', $user_id);
		$query->where($this->field_mappings['is_enabled'], '=', '1');
		
		$results = $query->execute($this->_database_config);
		return $this->_map_results($results[0]);
	}
	
	public function check($username, $password) {
		$query = call_user_func_array(array('DB', 'select'), $this->field_list);
		$query->from($this->_table_name);
		$query->where($this->field_mappings['username'], '=', $username);
		$query->where($this->field_mappings['password'], '=', $password);
		$query->where($this->field_mappings['is_enabled'], '=', '1');
		
		$results = $query->execute($this->_database_config);
		return $this->_map_results($results[0]);
	}
	
	public function on_login_success($userdata) { }
	
	public function on_login_fail($userdata) { }
	
	public function on_logout($userdata) { }
	
	public function enable($user_id) {
		$query = DB::update($this->_table_name);
		$query->set(array(
			$this->field_mappings['is_enabled'] => '1'
		));
		$query->where($this->field_mappings['user_id'], '=', $user_id);
		$result = $query->execute($this->_database_config);
	}
	
	public function disable($user_id) {
		$query = DB::update($this->_table_name);
		$query->set(array(
				$this->field_mappings['is_enabled'] => '0'
		));
		$query->where($this->field_mappings['user_id'], '=', $user_id);
		$result = $query->execute($this->_database_config);
	}
	
}