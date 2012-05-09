<?php defined('SYSPATH') or die('No direct script access.');

/**
 * A authentication controller  
 * @package   Canvas
 * @category  Auth
 * @author    Henry Tseng
 * @copyright (c) 2012 Canvas Digital LLC
 */
class Controller_Auth extends Controller {
	
	/**
	 * Constructor
	 * @param Request $request
	 * @param Response $response
	 */
	public function __construct(Request $request, Response $response) {
		parent::__construct($request, $response);
	}
	
	public function before() {
		parent::before();
	}
	
	public function after() {
		parent::after();
	}
	
	public function action_login() {
		$auth = Auth::get_instance();
		
		if($_POST) {
			$post = Validation::factory($_POST);
			$post->rule('username', 'not_empty');
			$post->rule('password', 'not_empty');
			
			if($post->check()) {
				$auth->login($post['username'], $post['password']);
				$this->request->redirect('auth/login');
			}
		}
		
		$view = View::factory('auth/login');
		$this->response->body($view);
	}
	
	public function action_logout() {
		$auth = Auth::get_instance();
		$auth->logout();
		
		$view = View::factory('auth/logout');
		$this->response->body($view);
	}
}
