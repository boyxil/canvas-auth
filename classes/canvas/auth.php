<?php defined('SYSPATH') or die('No direct script access.');

/**
 * A multiton based authentication module with interchangeable authorization contexts based on the state design pattern
 * @package   Canvas
 * @category  Auth
 * @author    Henry Tseng
 * @copyright (c) 2012 Canvas Digital LLC
 */
class Canvas_Auth {
	const DEFAULT_HANDLER = 'file';
	
	protected static $_instances = array();
	
	public $name;
	public $handler;
	protected $_authconfig;
	protected $_session;
	protected $_userdata;
	protected $_sessiondata;
	
	/**
	 * Get an instance of the auth module
	 * @return Auth
	 */
	public static function get_instance($name=Auth::DEFAULT_HANDLER, $config=null) {
		if(!isset(Auth::$_instances[$name])) {
			$config = ($config==null) ? Kohana::$config->load('auth') : $config;
			$instance = new Auth($config);
			$instance->name = $name;
			$instance->handler = Auth::get_handler($name, $config);
			$instance->_init();
			Auth::$_instances[$name] = $instance;
			
		} else {
			$instance = Auth::$_instances[$name];
		}
		return $instance;
	}
	
	/**
	 * Retrieve a handler based on name
	 * @param string $name
	 */
	public static function get_handler($name, $config) {
		$handler_name = $config['handlers'][$name]['class'];
		$handler = new $handler_name($config);
		if(!is_a($handler, 'Canvas_Auth_Handler')) {
			throw(new Canvas_Auth_Exception('Authentication handler context must inherit abstract Canvas_Auth_Handler.'));
		}
		return $handler;
	}
	
	/**
	 * Constructor
	 * @param array $config
	 */
	public function __construct($config) {
		$this->_authconfig = $config;
		$this->_session = Session::instance('native');
	}
	
	/**
	 * Initialize userdata
	 */
	protected function _init() {
		$this->_retrieve_userdata();
	}
	
	/**
	 * Login using userdata and identifier and a passcode
	 * @param array $credentials, An array hash containing user credentials required for the login process 
	 * @param bool $remember
	 * @return boolean
	 */
	public function login($username, $password) {
		if(!isset($username) || !isset($password)) {
			$this->_do_login_fail();
			return false;
		}
		
		$userdata = $this->handler->check($username, $this->hash($password));
		if($userdata) {
			$this->_set_sessiondata($userdata);
			$this->_bind_userdata($userdata);
			
			$this->_do_login_success();
			return true;
			
		} else {
			$this->_do_login_fail();
			return false;
		}
	}
	
	protected function _do_login_fail() {
		$this->_session->delete($this->_authconfig['session']['key']);
		$this->handler->on_login_fail($this->_userdata);
		$this->_execute_callbacks($this->_authconfig['callback']['login_fail']);
	}
	
	protected function _do_login_success() {
		$this->handler->on_login_success($this->_userdata);
		$this->_execute_callbacks($this->_authconfig['callback']['login_success']);
	}
	
	protected function _do_logout() {
		$this->handler->on_logout($this->_userdata);
		$this->_execute_callbacks($this->_authconfig['callback']['logout']);
	}
	
	protected function _execute_callbacks($list) {
		if(is_array($list)) {
			foreach($list as $callback) {
				try {
					call_user_func_array($callback, array());
				} catch(Exception $e) {
					
				}
			}
		}
	}
	
	/**
	 * Logout
	 * @param bool $destroy, Completely remove session data
	 * @param bool $is_persistent_session, Keep using existing session
	 */
	public function logout($destroy = false, $is_persistent_session = true) {
		if($destroy === true) {
			$this->_session->destroy();
		} else {
			$this->_session->delete($this->_authconfig['session']['key']);
			if(!$is_persistent_session) {
				$this->_session->regenerate();
			}
		}
		
		$this->_do_logout();
		$this->_userdata = null;
		$this->_sessiondata = null;
	}
	
	/**
	 * Set session user data
	 */
	protected function _set_sessiondata($userdata) {
		$data = $userdata;
		unset($data['password']);
		
		$this->_sessiondata = $data;
		$this->_session->set($this->_authconfig['session']['key'], $data);
	}
	
	/**
	 * Explicitly bind user data
	 */
	protected function _bind_userdata($userdata) {
		$this->_userdata =& $userdata;
	}
	
	/**
	 * Retrieve user data from database
	 */
	protected function _retrieve_userdata() {
		$this->_sessiondata = $this->_session->get($this->_authconfig['session']['key'], null);
		$this->_userdata = null;
		
		if($this->is_authenticated()) {
			$userdata = null;
			if(isset($this->_sessiondata['user_id'])) {
				$userdata = $this->handler->get_userdata($this->_sessiondata['user_id']);
			}
			
			if($userdata) {
				$this->_userdata = $userdata;
			} else {
				$this->_sessiondata = null;
				$this->_userdata = null;
			}
		}
	}
	
	/**
	 * Retrieve user data stored in session.  Minimally an array hash with:
	 * <ul>
	 * <li>id - The session id</li>
	 * <li>user_id - A unique identifier corresponding to the user account</li>
	 * <li>username - A unique username</li>
	 * <li>is_enabled - A flag corresponding to enabled/disabled account status</li>
	 * </ul>
	 * @return array
	 */
	public function get_user() {
		if($this->_sessiondata) {
			return array_merge(array(
				'session_id' => $this->_session->id(),
			), $this->_sessiondata);
		} else {
			return array(
				'session_id' => $this->_session->id(),
			);
		}
	}
	
	/**
	 * Check whether or not user is authenticated
	 * @return boolean
	 */
	public function is_authenticated() {
		return ($this->_sessiondata !== null);
	}
	
	/**
	 * Hash a string according to the salt value stored in the configuration files 
	 * @param string $text, A string to hash
	 * @throws Canvas_Auth_Exception
	 * @return string
	 */
	public function hash($text) {
		if(!$this->_authconfig['security']['salt'])
			throw new Canvas_Auth_Exception('A valid hash key must be set in your auth config.');
		
		$hashed_pwd = hash_hmac($this->_authconfig['security']['hash'], $text, $this->_authconfig['security']['salt']); 
		return $hashed_pwd;
	}
	
	/**
	 * Enable user account
	 * @param string $user_id
	 */
	public function enable($user_id) {
		$this->handler->enable($user_id);
	}
	
	/**
	 * Disable user account
	 * @param string $user_id
	 */
	public function disable($user_id) {
		$this->handler->disable($user_id);
	}
	
}
