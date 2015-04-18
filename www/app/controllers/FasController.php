<?php

class FasController extends BaseController
{
	
	public function __construct()
	{
		$this->beforeFilter('csrf', array('on'=>'post'));
	}

	public function getIndex()
	{
		if(Auth::check()) {
			return View::make('adm.fas')
				->with('dest', FAS::orderBy('global_prefix', 'ASC')->get());
		}
	}

	public function postCreate()
	{
		$validator = Validator::make(Input::all(), FAS::$rules );

		if ($validator->passes()) 
		{

			if (Input::hasFile('recording_file')) {
	            $file = Input::file('recording_file');
	            $file_name = time().'.mp3';
	            $file->move(public_path() . '/mp3', $file_name);
        	}

			$dest = new FAS;
			$dest->name = Input::get('name');
			$dest->global_prefix = Input::get('global_prefix');
			$dest->number_length = Input::get('number_length');
			$dest->before_ansfer = Input::get('before_ansfer');
			$dest->before_ansfer_from = (!Input::get('before_ansfer_from') ? '0' : Input::get('before_ansfer_from') ) ;
			$dest->before_ansfer_to = (!Input::get('before_ansfer_to') ? '0' : Input::get('before_ansfer_to') );
			$dest->after_ansfer = Input::get('after_ansfer');
			$dest->tone_stream = Input::get('tone_stream');
			$dest->tone_stream_duration = (!Input::get('tone_stream_duration') ? '0' : Input::get('tone_stream_duration') );
			$dest->random_pdd = (!Input::get('random_pdd') ? false : true);
			$dest->recording_file = (!Input::hasFile('recording_file') ? '' : $file_name);
			$dest->active = (!Input::get('active') ? '0' : '1');
			$dest->save();

			return Redirect::to('adm/fas')
				->with('dest', 'The destinations created!');
		}

		return Redirect::to('adm/fas')
			->with('message', 'Something went wrong ')
			->withErrors($validator)
			->withInput();
		
	}

	public function postModify()
	{
		$dest = FAS::find(Input::get('id'));

		if ($dest) {
			$cdr = CDR::where('destination_id', '=', Input::get('id'))->where('context', '=', 'fas')->orderBy('start_stamp', 'desc')->take(100)->get();
			return View::make('adm.fas_modify')
				->with('dest', $dest)
				->with('cdr', $cdr);
		}

		return Redirect::to('adm/fas')
			->with('message', 'Something went wrong ');
	}

	public function postUpdate()
	{
		if (Input::get('rate')) {
			if (Input::get('rate_user')) {
				FAS::where('id', '=', Input::get('id'))
				->update(array("rate_user" => Input::get('rate_user')));
			}
			if (Input::get('rate_agent')) {
				FAS::where('id', '=', Input::get('id'))
				->update(array("rate_agent" => Input::get('rate_agent')));
			}
			if (Input::get('rate_admin')) {
				FAS::where('id', '=', Input::get('id'))
				->update(array("rate_admin" => Input::get('rate_admin')));
			}
			$dest = FAS::find(Input::get('id'));
			if ($dest) {
			return Redirect::to('adm/fas')
					->with('message', 'The destination updated!');
			}
		}

		$validator = Validator::make(Input::all(), FAS::$rules );
		$dest = FAS::find(Input::get('id'));

		if ($validator->passes()) 
		{
			if ($dest) {
				
				if (Input::hasFile('recording_file')) {
		            $file = Input::file('recording_file');
		            $file_name = time().'.mp3';
		            $file->move(public_path() . '/mp3', $file_name);
	        	}
				$dest->name = Input::get('name');
				$dest->global_prefix = Input::get('global_prefix');
				$dest->number_length = Input::get('number_length');
				$dest->before_ansfer = Input::get('before_ansfer');
				$dest->before_ansfer_from = (!Input::get('before_ansfer_from') ? '0' : Input::get('before_ansfer_from') ) ;
				$dest->before_ansfer_to = (!Input::get('before_ansfer_to') ? '0' : Input::get('before_ansfer_to') );
				$dest->after_ansfer = Input::get('after_ansfer');
				$dest->tone_stream = Input::get('tone_stream');
				$dest->tone_stream_duration = (!Input::get('tone_stream_duration') ? '0' : Input::get('tone_stream_duration') );
				$dest->random_pdd = (!Input::get('random_pdd') ? false : true);
				$dest->recording_file = (!Input::hasFile('recording_file') ? $dest->recording_file : $file_name);
				$dest->active = (!Input::get('active') ? '0' : '1');
				$dest->save();

				return Redirect::to('adm/fas')
					->with('message', 'The destination updated!');
			}
		}

		return Redirect::to('adm/fas')
			->with('message', 'Something went wrong ')
			->withErrors($validator);
		
	}

	public function postDestroy()
	{
		$dest = FAS::find(Input::get('id'));

		if ($dest) {
			$dest->delete();
			return Redirect::to('adm/fas')
				->with('message', 'The destination deleted!');
		}

		return Redirect::to('adm/fas')
			->with('message', 'Something went wrong ');
	}

	public function getCallflow()
	{
		return View::make('layouts.callflow_fas');
	}

}

?>