<?php

class ReportsController extends BaseController {

	public function getIndex()
	{
		return Redirect::to('news');
	}

	public function getBillings()
	{
		return View::make('reports.billings');
	}

	public function postBillings()
	{
		$s = CDR::orderBy('start_stamp')->get();

		//$s = CDR::where('parent_id', '=', Auth::user()->id)
		//	->orWhere('user_id', '=', Auth::user()->id)
		//	->get();

		//$s = CDR::where('user_id', '=', Auth::user()->id)->get();

		return View::make('reports.billings')
			->with('cdr', $s);
	}

}