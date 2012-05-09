Canvas Auth
===========

Overview
--------

A multiton based authentication module with interchangeable authorization contexts based on the state design pattern.  

canvas-auth is designed to be lightweight, flexible and modular.  

Authorization credentials can be mapped differently through configuration.  

Example controllers and views are included as a demonstration implementation.  

Coming down the pipeline will be integration with an Access-Control-Level (ACL) module to support a permissions model.  

--------------------

Requeriments
------------

* Kohana 3
* Kohana Database Query Builder (optional)
* Configuration 

Usage
-----

```php
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
```

Credits
-------

  * Kohana, https://github.com/kohana
  * Canvas Digital, http://www.heycanvas.com/
  * Henry Tseng, https://github.com/henrycanvas