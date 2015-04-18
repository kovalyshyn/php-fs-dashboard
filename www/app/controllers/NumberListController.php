<?php

class NumberListController extends BaseController
{

    public function __construct()
    {
        $this->beforeFilter('csrf', array('on'=>'post'));
    }

    public function getIndex()
    {
        if(Auth::user()->type == '0') {
            return View::make('layouts.numbers')
                ->with('destinations', Destinations::all());
        } else {
            return View::make('layouts.numbers')
                ->with('destinations', Destinations::Where('user_id', '=', Auth::user()->id)->get());
        }
    }

    public function getShow($id = null)
    {
        if(Auth::user()->type == '0') {
            $dest = Destinations::find($id);
            $destinations = Destinations::all();
        } else {
            $dest = Destinations::find($id);
            $destinations = Destinations::Where('user_id', '=', Auth::user()->id)->get();
        }
        if ($dest) {
            return View::make('layouts.blacklist')
                ->with('destinations', $destinations)
                ->with('dest', $dest)
                ->with('blacklist', NumberList::Where('destinations', '=', $id)->orderBy('added', 'desc')->paginate(50));
        }
        return Redirect::to('blacklist')
            ->with('message', 'No such destination! ');
        
    }

    public function postUpdate()
    {

        $dest = Destinations::find(Input::get('id'));

        if ($dest) {
            $dest->progress_before_answer = (!Input::get('progress_before_answer') ? '0' : Input::get('progress_before_answer'));
            $dest->progress_without_answer = (!Input::get('progress_without_answer') ? '0' : Input::get('progress_without_answer'));
            $dest->repeat_calls = (!Input::get('repeat_calls') ? '0' : Input::get('repeat_calls'));
            $dest->repeat_calls_minutes = (!Input::get('repeat_calls_minutes') ? '0' : Input::get('repeat_calls_minutes'));
            $dest->progress_no_answer = (!Input::get('progress_no_answer') ?  false : true);
            $dest->numA = (!Input::get('numA') ?  false : true);
            $dest->numB = (!Input::get('numB') ?  false : true);
            $dest->save();

            return Redirect::to('blacklist/show/'.Input::get('id'));

        }

        return Redirect::to('blacklist')
            ->with('message', 'Something went wrong ');
        
    }

    public function postModify()
    {

        $id_delete = Input::get('delIDs');
        if(is_array($id_delete))
        {
            foreach ($id_delete as $i => $value) {
                $bl = NumberList::find($id_delete[$i]);
                if ($bl) {
                    $bl->delete();
                }
            }
        }

        return Redirect::to('blacklist/show/'.Input::get('dest_id'));
        
    }

    public function getPurge($id = null)
    {
        $bl = NumberList::Where('destinations', '=', $id);
        $bl->delete();
        return Redirect::to('blacklist/show/'.$id);
        
    }

}

?>