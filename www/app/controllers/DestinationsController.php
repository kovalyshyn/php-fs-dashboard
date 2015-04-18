<?php

class DestinationsController extends BaseController
{
	
	public function __construct()
	{
		$this->beforeFilter('csrf', array('on'=>'post'));
	}

	public function getIndex()
	{
		return View::make('adm.destinations')
			->with('dest', Destinations::orderBy('user_id')->paginate(20));

	}

	public function postCreate()
	{
		$validator = Validator::make(Input::all(), Destinations::$rules );

		if ($validator->passes()) 
		{
			$dest = new Destinations;
			$dest->name = Input::get('name');
			$dest->global_prefix = Input::get('global_prefix');
			$dest->local_prefix = 0;//Input::get('local_prefix');
			$dest->number_length = Input::get('number_length');
			$dest->user_id = Input::get('user_id');
			$dest->show_getaways = 1;
			$dest->active = (!Input::get('active') ? '0' : '1');
			$dest->save();

			return Redirect::to('destinations')
				->with('dest', 'The destinations created!');
		}

		return Redirect::to('destinations')
			->with('message', 'Something went wrong ')
			->withErrors($validator)
			->withInput();
		
	}

	public function postModify()
	{
		$dest = Destinations::find(Input::get('id'));

		if ($dest) {
			return View::make('adm.destinations_modify')
				->with('dest', $dest);
		}

		return Redirect::to('destinations')
			->with('message', 'Something went wrong ');
	}

	public function postUpdate()
	{
		if (Input::get('gw')) {
			if (Input::get('limit')) {
				Getaway::where('destinations', '=', Input::get('id'))
				->update(array("limit" => Input::get('limit')));
			}
			if (Input::get('delay')) {
				Getaway::where('destinations', '=', Input::get('id'))
				->update(array("delay" => Input::get('delay'), "delay_rnd" => '0'));
			}
			if (Input::get('minutes')) {
				Getaway::where('destinations', '=', Input::get('id'))
				->update(array("minutes" => Input::get('minutes')));
			}
			$dest = Destinations::find(Input::get('id'));
			if ($dest) {
			return View::make('adm.destinations_modify')
				->with('dest', $dest);
			}
		}
		if (Input::get('rate')) {
			if (Input::get('rate_user')) {
				Destinations::where('id', '=', Input::get('id'))
				->update(array("rate_user" => Input::get('rate_user')));
			}
			if (Input::get('rate_agent')) {
				Destinations::where('id', '=', Input::get('id'))
				->update(array("rate_agent" => Input::get('rate_agent')));
			}
			if (Input::get('rate_admin')) {
				Destinations::where('id', '=', Input::get('id'))
				->update(array("rate_admin" => Input::get('rate_admin')));
			}
			$dest = Destinations::find(Input::get('id'));
			if ($dest) {
			return View::make('adm.destinations_modify')
				->with('dest', $dest);
			}
		}

		$validator = Validator::make(Input::all(), Destinations::$rules );
		$dest = Destinations::find(Input::get('id'));

		if ($validator->passes()) 
		{
			if ($dest) {
				$dest->name = Input::get('name');
				$dest->global_prefix = Input::get('global_prefix');
				$dest->local_prefix = 0;//Input::get('local_prefix');
				$dest->number_length = Input::get('number_length');
				$dest->agent_prefix = Input::get('agent_prefix');
				$dest->del_prefix = (!Input::get('del_prefix') ? '0' : '1');
				$dest->active = (!Input::get('active') ? '0' : '1');
				$dest->ussd_balance = Input::get('ussd_balance');
				$dest->ussd_balance_pattern = Input::get('ussd_balance_pattern');
				$dest->user_id = Input::get('user_id');
				$dest->show_getaways = (!Input::get('show_getaways') ? '1' : Input::get('show_getaways'));
				$dest->save();

				return Redirect::to('destinations')
					->with('message', 'The destination updated!');
			}
		}

		return Redirect::to('destinations')
			->with('message', 'Something went wrong ')
			->withErrors($validator);
		
	}

	public function postDestroy()
	{
		$dest = Destinations::find(Input::get('id'));

		if ($dest) {
			$dest->delete();
			return Redirect::to('destinations')
				->with('message', 'The destination deleted!');
		}

		return Redirect::to('destinations')
			->with('message', 'Something went wrong ');
	}

}

?>
