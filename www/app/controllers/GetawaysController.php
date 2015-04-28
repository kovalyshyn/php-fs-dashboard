<?php

class GetawaysController extends BaseController
{
	public function __construct()
	{
		$this->beforeFilter('csrf', array('on'=>'post'));
	}

	public function getIndex()
	{
		if(Auth::check()) {
			if(Auth::user()->type == '0') {
				$s = Getaway::orderBy('destinations')
				->paginate(20);
			}
	    	elseif(Auth::user()->type == '1') {
	      		$s = Getaway::where('parent_id', '=', Auth::user()->id)
					->orWhere('user_id', '=', Auth::user()->id)
					->orderBy('destinations')
					->paginate(20);
	    	}
	    	else {
				$s = Getaway::where('user_id', '=', Auth::user()->id)
				->orderBy('destinations')
				->paginate(20);
	    	}

			return View::make('layouts.getaways')
				->with('getaways', $s);
		}
	}

	public function postIndex()
	{
		if(Auth::check()) {
			if(Auth::user()->type == '0') {
				$s = Getaway::where('destinations', '=', Input::get('destinations'))
					->orderBy('destinations')->paginate(20);
			}
	    	elseif(Auth::user()->type == '1') {
	      		$s = Getaway::where('destinations', '=', Input::get('destinations'))
	      			->where('parent_id', '=', Auth::user()->id)
					->orWhere('user_id', '=', Auth::user()->id)
					->paginate(20);
	    	}
	    	else {
				$s = Getaway::where('destinations', '=', Input::get('destinations'))
					->where('user_id', '=', Auth::user()->id)->paginate(20);
	    	}

			return View::make('layouts.getaways')
				->with('getaways', $s);
		}
	}

	public function postCreate()
	{
		$validator = Validator::make(Input::all(), Getaway::$rules );

		if ($validator->passes()) 
		{
			$gw = new Getaway;
			$gw->ip = Input::get('ip');
			$gw->port = Input::get('port');
			$gw->limit = (!Input::get('limit') ? '0' : Input::get('limit'));
			$gw->delay = (!Input::get('delay') ? '0' : Input::get('delay'));
			$gw->minutes = (!Input::get('minutes') ? '60' : Input::get('minutes'));
			$gw->concurrent = (!Input::get('concurrent') ? '1' : Input::get('concurrent'));
			$gw->user_id = Input::get('user_id');
			$gw->parent_id = Input::get('parent_id');
			$gw->active = (!Input::get('active') ? '0' : '1');
			$gw->destinations = Input::get('destinations');
			$gw->mask = Input::get('mask');
			$gw->imei = Input::get('imei');
			$gw->sip_profile = 'openvpn';
			$gw->type_id = Input::get('type_id');
			$gw->save();

			return Redirect::to('getaways')
				->with('gw', 'The Getaway created!');
		}


		return Redirect::to('gw/'.Input::get('type_id'))
			->with('message', 'Something went wrong ')
			->withErrors($validator)
			->withInput();
		
	}

	public function postUpdate()
	{
		$validator = Validator::make(Input::all(), Getaway::$rules );
		$gw = Getaway::find(Input::get('id'));

		if ($validator->passes()) 
		{
			if ($gw) {
				$gw->ip = Input::get('ip');
				$gw->port = Input::get('port');
				$gw->limit = (!Input::get('limit') ? '0' : Input::get('limit'));
				$gw->delay = (!Input::get('delay') ? '0' : Input::get('delay'));
				$gw->minutes = (!Input::get('minutes') ? '60' : Input::get('minutes'));
				$gw->mask = Input::get('mask');
				$gw->imei = ($gw->type_id=='1' ? Input::get('id') : Input::get('imei') );
				$gw->sip_profile = Input::get('sip_profile');
				if (Input::get('user_id')) {
					$gw->user_id = Input::get('user_id');
					$gw->parent_id = SwitchUser::find(Input::get('user_id'))->parent_id;
				}
				if (Input::get('bridge_string')) {
                                        $gw->bridge_string = Input::get('bridge_string');
                                }
				if (Input::get('call_timeout')) {
                                        $gw->call_timeout = Input::get('call_timeout');
                                }
				$gw->concurrent = (!Input::get('concurrent') ? '1' : Input::get('concurrent'));
				$gw->active = (!Input::get('active') ? '0' : '1');
				$gw->destinations = Input::get('destinations');
				$gw->delay_rnd = (!Input::get('delay_rnd') ? '0' : '1');
				$gw->delay_all = (!Input::get('delay_all') ? '0' : '1');
				$gw->delay_from = (!Input::get('delay_from') ? '0' : Input::get('delay_from'));
				$gw->delay_to = (!Input::get('delay_to') ? '0' : Input::get('delay_to'));
				$gw->save();
				return Redirect::to('getaways')
					->with('message', 'The getaway updated!');
			}
		}

		return Redirect::to('getaway/'.$gw->id)
			->with('message', 'Something went wrong ')
			->withErrors($validator);
		
	}

	public function postDestroy()
	{
		$gw = Getaway::find(Input::get('id'));

		if ($gw) {
			$gw->delete();
			return Redirect::to('getaways')
				->with('message', 'The getaway deleted!');
		}

		return Redirect::to('getaways')
			->with('message', 'Something went wrong ');
	}

	public function getCallflow()
	{
		if(Auth::check()) {
			if(Auth::user()->type == '0') {
				$s = Getaway::orderBy('destinations')->get();
			}
	    	elseif(Auth::user()->type == '1') {
	      		$s = Getaway::where('parent_id', '=', Auth::user()->id)
					->orWhere('user_id', '=', Auth::user()->id)
					->get();
	    	}
	    	else {
				$s = Getaway::where('user_id', '=', Auth::user()->id)->get();
	    	}

			return View::make('layouts.callflow_main')
				->with('getaways', $s);
		}
	}

	public function getShow($id = null)
	{
		$gw = Getaway::find($id);
		if ($gw) {
			$cdr = CDR::where('gw_id', '=', $id)->Where('billsec', '>', '0')->orderBy('start_stamp', 'desc')->paginate(50);
			if(Auth::user()->type == '0') {
				$cdr = CDR::where('gw_id', '=', $id)->orderBy('start_stamp', 'desc')->paginate(50);
			}
			return View::make('layouts.getaway')
			->with('getaway', $gw)
			->with('cdr', $cdr);
		}
		return Redirect::to('layouts.getaways')
			->with('message', 'No such gateway!');
	}

	public function getCfg($id = null)
	{
		$dom = new DOMDocument();
		$dom->load('imei');
		$root = $dom->documentElement; 

		foreach ($root->getElementsByTagName('user_name') as $s) {
			$cdata=$dom->createCDATASection($id);
			$s->replaceChild($cdata, $s->childNodes->item(0));
		}

		foreach ($root->getElementsByTagName('login') as $s) {
			$cdata=$dom->createCDATASection($id);
			$s->replaceChild($cdata, $s->childNodes->item(0));
		}

		$headers = array(
              'Content-Type: text/xml'
            );
		
		return Response::make( $dom->saveXML(), 200, $headers );
	}

	public function getEnable($id = null, $destinations = null)
	{
		$gw = Getaway::find($id);
		if ($gw) {
			$gw->active = '1';
			$gw->save();
		}
		if ($destinations) {
			$dest = Destinations::find($destinations);
			if ($dest) {
			return View::make('adm.destinations_modify')
				->with('dest', $dest);
			}
		}
		return Redirect::to('getaways');
	}

	public function getDisable($id = null, $destinations = null)
	{
		$gw = Getaway::find($id);
		if ($gw) {
			$gw->active = '0';
			$gw->save();
		}
		if ($destinations) {
			$dest = Destinations::find($destinations);
			if ($dest) {
			return View::make('adm.destinations_modify')
				->with('dest', $dest);
			}
		}
		return Redirect::to('getaways');
	}

	public function postUp($destinations = null)
	{
		$id_checked = Input::get('IDs');
		if(is_array($id_checked))
		{
			foreach ($id_checked as $i => $value) {
				$gw = Getaway::find($id_checked[$i]);
				if ($gw) {
					$gw->active = ($gw->active == '1' ? '0' : '1');
					$gw->save();
				}
			}
		}
		if ($destinations) {
			$dest = Destinations::find($destinations);
			if ($dest) {
			return View::make('adm.destinations_modify')
				->with('dest', $dest);
			}
		}
		return Redirect::to('getaways');
	}

}

?>
