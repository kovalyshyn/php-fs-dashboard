<?php

class NewsController extends BaseController
{

	public function __construct()
	{
		$this->beforeFilter('csrf', array('on'=>'post'));
	}

	public function getIndex()
	{
		return View::make('layouts.news')
			->with('news', News::orderBy('created_at', 'DESC')->get());
	}

	public function postCreate()
	{
		$validator = Validator::make(Input::all(), News::$rules );

		if ($validator->passes()) 
		{
			$news = new News;
			$news->name = Input::get('name');
			$news->the_news = Input::get('the_news');
			$news->save();

			return Redirect::to('news')
				->with('message', 'The news posted!');
		}

		return Redirect::to('news')
			->with('message', 'Something went wrong ')
			->withErrors($validator)
			->withInput();
		
	}

	public function postModify()
	{
		$news = News::find(Input::get('id'));

		if ($news) {
			return View::make('adm.news')
				->with('news', $news);
		}

		return Redirect::to('news')
			->with('message', 'Something went wrong ');
	}

	public function postUpdate()
	{
		$validator = Validator::make(Input::all(), News::$rules );
		$news = News::find(Input::get('id'));

		if ($validator->passes()) 
		{
			if ($news) {
				$news->name = Input::get('name');
				$news->the_news = Input::get('the_news');
				$news->save();

				return Redirect::to('news')
					->with('message', 'The news updated!');
			}
		}

		return Redirect::to('news')
			->with('message', 'Something went wrong ')
			->withErrors($validator);
		
	}

	public function postDestroy()
	{
		$news = News::find(Input::get('id'));

		if ($news) {
			$news->delete();
			return Redirect::to('news')
				->with('message', 'The news deleted!');
		}

		return Redirect::to('news')
			->with('message', 'Something went wrong ');
	}

}

?>