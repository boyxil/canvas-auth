<?php

/**
 * A config file based authentication handler.  
 * @package   Canvas
 * @category  Auth
 * @author    Henry Tseng
 * @copyright (c) 2012 Canvas Digital LLC
 */
class Canvas_Auth_Handler_File extends Canvas_Auth_Handler {
	public $name = 'file';
	public $default_fields = array(
			'user_id' => 'user_id',
			'username' => 'username',
			'password' => 'password',
			'is_enabled' => 'is_enabled',
	);
	public $user_map;
	
	/**
	 * Constructor
	 * @param array $config
	 */
	public function __construct($config) {
		parent::__construct($config);
		
		if(isset($this->_authconfig['handlers'][$this->name]['user_map'])) {
			$this->set_user_map($this->_authconfig['handlers'][$this->name]['user_map']);
		}
	}
	
	public function set_user_map($map) {
		$this->user_map = $map;
	}
	
	public function get_userdata($user_id) {
		foreach($this->user_map as $userdata) {
			if($userdata[$this->field_mappings['user_id']] == $user_id) {
				return $this->_map_results($userdata);
			}
		}
		return null;
	}
	
	public function check($username, $password) {
		foreach($this->user_map as $userdata) {
			if($userdata[$this->field_mappings['username']] == $username && $userdata[$this->field_mappings['password']] == $password) {
				return $this->_map_results($userdata);
			}
		}
		return null;
	}
	
	public function on_login_success($userdata) {
		
	}
	
	public function on_login_fail($userdata) {
		
	}
	
	public function on_logout($userdata) {
		
	}
	
	public function enable($user_id) {
		
	}
	
	public function disable($user_id) {
		
	}
	
}