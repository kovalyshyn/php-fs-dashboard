<?php

class SwitchUsersController extends BaseController
{

	public function __construct()
	{
		$this->beforeFilter('csrf', array('on'=>'post'));
	}

	public function getIndex()
	{
		if(Auth::check()) {
			if(Auth::user()->type == '0') {
				$s = SwitchUser::all();
			}
	    	elseif(Auth::user()->type == '1') {
	      		$s = SwitchUser::where('parent_id', '=', Auth::user()->id)->get();
	    	}
	    	else {
				$s = SwitchUser::where('id', '=', Auth::user()->id)->get();
	    	}

			return View::make('adm.index')
				->with('users', $s);
		}
	}

	public function postCreate()
	{
		$validator = Validator::make(Input::all(), SwitchUser::$rules );

		if ($validator->passes()) 
		{
			$user = new SwitchUser;
			$user->name = Input::get('name');
			$user->email = Input::get('email');
			$user->type = Input::get('type');
			$user->parent_id = Input::get('parent_id');
			$user->password = Hash::make(Input::get('password'));
			$user->save();

			return Redirect::to('adm/users')
				->with('message', 'Created new switch User');
		}

		return Redirect::to('adm/users')
			->with('message', 'Something went wrong ')
			->withErrors($validator)
			->withInput();
		
	}

	public function postUpdate()
	{
		$validator = Validator::make(Input::all(), SwitchUser::$uprules );
		$user = SwitchUser::find(Input::get('id'));

		if ($validator->passes()) 
		{
			if ($user) {
				$user->name = Input::get('name');
				$user->email = Input::get('email');
				if(Input::get('password')) {
					$user->password = Hash::make(Input::get('password'));
				}
				if(Input::get('parent_id')) {
					$user->parent_id = Input::get('parent_id');
				}
				if(Input::get('type')) {
					$user->type = (Input::get('type') == 'A' ? '0' : Input::get('type') );
				}
				$user->save();

				return Redirect::to('adm/user/'.Input::get('id'))
					->with('message', 'User profile updated!');
			}
		}

		return Redirect::to('adm/user/'.Input::get('id'))
			->with('message', 'Something went wrong ')
			->withErrors($validator)
			->withInput();
		
	}

	public function postDestroy()
	{
		$user = SwitchUser::find(Input::get('id'));

		if ($user) {
			$user->delete();
			return Redirect::to('adm/users')
				->with('message', 'switch User deleted!');
		}

		return Redirect::to('adm/users')
			->with('message', 'Something went wrong ');
	}

}

?>