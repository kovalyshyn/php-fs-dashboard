<?php

class UsersController extends BaseController
{
	protected $layout = "index";

	public function __construct()
	{
		$this->beforeFilter('csrf', array('on'=>'post'));
		$this->beforeFilter('auth', array('only'=>array('getDashboard')));
	}

	public function getRegister() {
    	$this->layout->content = View::make('index');
	}

	public function getLogin() {
    	$this->layout->content = View::make('index');
	}

	public function getDashboard() {
    	$this->layout->content = View::make('news');
	}

	public function postSignin() {
		if (Auth::attempt(array('email'=>Input::get('email'), 'password'=>Input::get('password')))) {
    return Redirect::to('news')->with('message', 'You are now logged in!');
} else {
    return Redirect::to('/')
        ->with('message', 'Your username/password combination was incorrect')
        ->withInput();
}
             
	}

}

?>