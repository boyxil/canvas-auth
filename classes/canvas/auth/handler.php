<?php

/**
 * An authentication handler is a context used to for authorization.  All authentication handlers must 
 * implement this interface.  
 * @package   Canvas
 * @category  Auth
 * @author    Henry Tseng
 * @copyright (c) 2012 Canvas Digital LLC
 */
abstract class Canvas_Auth_Handler {
	public $name = 'abstract';
	public $default_fields = array();
	public $field_mappings;
	
	protected $_authconfig;
	
	public function __construct($config) {
		$this->_authconfig = $config;
		
		// Create table field mappings
		$this->field_mappings = array_merge($this->default_fields, $this->_authconfig['handlers'][$this->name]['field_mappings']);
	}
	
	/**
	 * Reverse map results back to field mappings.  
	 * @param array $results, An array of user data 
	 * @param array $mappings, An optional custom mapping object
	 */
	public function _map_results($results, $mappings=null) {
		$field_mappings = ($mappings==null) ? $this->field_mappings : $mappings;
		foreach($field_mappings as $key=>$field) {
			if($key != $field) {
				$results[$key] = $results[$field];
				unset($results[$field]);
			}
		}
	
		return $results;
	}
	
	abstract public function get_userdata($user_id);
	
	abstract public function check($username, $password);
	
	abstract public function on_login_success($userdata);
	
	abstract public function on_login_fail($userdata);
	
	abstract public function on_logout($userdata);
	
	abstract public function enable($user_id);
	
	abstract public function disable($user_id);
	
}
